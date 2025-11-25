<?php

namespace MPHB\Addons\RequestPayment;

use MPHB\Addons\RequestPayment\Utils\BookingUtils;
use MPHB\Addons\RequestPayment\Utils\RequestUtils;
use MPHB\Utils\ValidateUtils;

class Ajax
{
    protected $nonceName = 'mphb_nonce';

    /** @since 2.0.0 */
    protected $basePrefix = 'mphb_';
    protected $actionPrefix = 'mphbrp_';

    /**
     * Add more handlers for action "get_billing_fields", that will work for a
     * default and ours checkout forms.
     */
    public function redefineActions()
    {
        // Redirect "wp_ajax_mphb_get_billing_fields" requests to current handler
        $action = "wp_ajax_{$this->basePrefix}get_billing_fields";
        $nopriv = "wp_ajax_nopriv_{$this->basePrefix}get_billing_fields";

        $mphbAjax = MPHB()->getAjax();

        remove_action($action, array($mphbAjax, 'get_billing_fields'));
        remove_action($nopriv, array($mphbAjax, 'get_billing_fields'));

        add_action($action, array($this, 'getBillingFields'));
        add_action($nopriv, array($this, 'getBillingFields'));

        // Redirect "wp_ajax_mphb_update_checkout_info" requests to current handler
        $action = "wp_ajax_{$this->basePrefix}update_checkout_info";
        $nopriv = "wp_ajax_nopriv_{$this->basePrefix}update_checkout_info";

        remove_action($action, array($mphbAjax, 'update_checkout_info'));
        remove_action($nopriv, array($mphbAjax, 'update_checkout_info'));

        add_action($action, array($this, 'updateCheckoutInfo'));
        add_action($nopriv, array($this, 'updateCheckoutInfo'));

        // Add handlers for the "Payment Requests" metabox (link management)
        add_action("wp_ajax_{$this->actionPrefix}disable_auto_request", array($this, 'disableAutoRequest'));
        add_action("wp_ajax_{$this->actionPrefix}add_payment_request", array($this, 'addPaymentRequest'));
        add_action("wp_ajax_{$this->actionPrefix}send_payment_request", array($this, 'sendPaymentRequest'));
        add_action("wp_ajax_{$this->actionPrefix}delete_payment_request", array($this, 'deletePaymentRequest'));
    }

    public function getBillingFields()
    {
        $action = "{$this->basePrefix}get_billing_fields";
        $input  = $_GET;

        // Maybe use a default handler
        if (!isset($input['formValues']['is_checkout_requested'])) {
            MPHB()->getAjax()->get_billing_fields();
        }

        $this->verifyNonce($action, $input);

        $gatewayId  = !empty($input['mphb_gateway_id']) ? mphb_clean($input['mphb_gateway_id']) : '';
        $bookingKey = !empty($input['formValues']['mphb_key']) ? mphb_clean($input['formValues']['mphb_key']) : '';
        $booking    = !empty($bookingKey) ? BookingUtils::findBookingByKey($bookingKey) : null;

        if (!array_key_exists($gatewayId, MPHB()->gatewayManager()->getListActive())) {
            wp_send_json_error(array(
                'message' => esc_html__('Selected payment method is not available. Refresh the page and try again.', 'mphb-request-payment')
            ));
        }

        if (is_null($booking)) {
            wp_send_json_error(array(
                'message' => esc_html__('Sorry, but no booking was found.', 'mphb-request-payment')
            ));
        }

        $gateway = MPHB()->gatewayManager()->getGateway($gatewayId);

        ob_start();
        $gateway->renderPaymentFields($booking);
        $fields = ob_get_clean();

        wp_send_json_success(array(
            'fields'           => $fields,
            'hasVisibleFields' => $gateway->hasVisiblePaymentFields()
        ));
    }

    public function updateCheckoutInfo()
    {
        $input  = $_GET;
        
        // Maybe use a default handler
        if (!isset($input['formValues']['is_checkout_requested'])) {
            MPHB()->getAjax()->update_checkout_info();
        }

        wp_send_json_success();
    }

    /**
     * @since 2.0.0
     */
    public function disableAutoRequest()
    {
        $input = $_POST;

        $this->verifyNonce("{$this->actionPrefix}disable_auto_request", $input);

        $bookingId = mphbrp_parse_id($input['booking_id']);
        $isDisabled = ValidateUtils::validateBool($input['disabled']);

        if (!$bookingId) {
            wp_send_json_error(array(
                'status'  => 'error',
                'message' => esc_html__('Sorry, but no booking was found.', 'mphb-request-payment'),
            ));
        }

        BookingUtils::toogleAutoRequest($bookingId, $isDisabled);

        wp_send_json_success(array(
            'status' => 'success',
        ));
    }

    /**
     * @since 2.0.0
     */
    public function addPaymentRequest()
    {
        $input = $_POST;

        $this->verifyNonce("{$this->actionPrefix}add_payment_request", $input);

        // Get booking
        $bookingId = mphbrp_parse_id($input['booking_id']);
        $booking = BookingUtils::findById($bookingId);

        if (is_null($booking)) {
            wp_send_json_error(array(
                'status'  => 'error',
                'message' => esc_html__('Sorry, but no booking was found.', 'mphb-request-payment'),
            ));
        }

        // Validate and save request
        $request = RequestUtils::validateRequest($input); // Sends JSON errors
        $requestId = BookingUtils::addCustomRequest($booking->getId(), $request);

        if ($requestId !== false) {
            $request->setId($requestId);

            wp_send_json_success(array(
                'status' => 'success',
                'html'   => mphbrp_render_template('edit-booking/payment-request-link', array(
                    'booking' => $booking,
                    'request' => $request,
                )),
            ));
        } else {
            wp_send_json_error(array(
                'status'  => 'error',
                'message' => esc_html__('Failed to add a new record to the database.', 'mphb-request-payment'),
            ));
        }
    }

    /**
     * @since 2.0.0
     */
    public function sendPaymentRequest()
    {
        $input = $_POST;

        $this->verifyNonce("{$this->actionPrefix}send_payment_request", $input);

        $bookingId = mphbrp_parse_id($input['booking_id']);
        $requestId = mphbrp_parse_id($input['request']['id']);
        $requestType = mphb_clean($input['request']['type']);

        $booking = BookingUtils::findById($bookingId);
        $request = RequestUtils::findRequest($bookingId, $requestId, $requestType);

        if (is_null($booking)) {
            wp_send_json_error(array(
                'status'  => 'error',
                'message' => esc_html__('Sorry, but no booking was found.', 'mphb-request-payment'),
            ));

        } else if (is_null($request)) {
            wp_send_json_error(array(
                'status'  => 'error',
                'message' => esc_html__('The requested record does not exist.', 'mphb-request-payment'),
            ));

        } else {
            RequestUtils::sendCustomPaymentRequest($booking, $request);

            wp_send_json_success(array(
                'status'  => 'success',
                'message' => esc_html__('Request sent.', 'mphb-request-payment'),
            ));
        }
    }

    /**
     * @since 2.0.0
     */
    public function deletePaymentRequest()
    {
        $input = $_POST;

        $this->verifyNonce("{$this->actionPrefix}delete_payment_request", $input);

        $bookingId = mphbrp_parse_id($input['booking_id']);
        $requestId = mphbrp_parse_id($input['request']['id']);

        $isRequestDeleted = BookingUtils::deleteCustomRequest($bookingId, $requestId);

        if ($isRequestDeleted) {
            wp_send_json_success(array(
                'status' => 'success',
            ));
        } else {
            wp_send_json_error(array(
                'status'  => 'error',
                'message' => esc_html__('The requested record does not exist.', 'mphb-request-payment'),
            ));
        }
    }

    /**
     * @param string $action Prefixed action.
     * @param array $input
     */
    protected function verifyNonce($action, $input)
    {
        $nonce = isset($input[$this->nonceName]) ? $input[$this->nonceName] : '';

        if (!wp_verify_nonce($nonce, $action)) {
            wp_send_json_error(array(
                'status'  => 'error', // Required in link-manager.js
                'message' => esc_html__('Request did not pass security verification.', 'mphb-request-payment'),
            ));
        }
    }
}
