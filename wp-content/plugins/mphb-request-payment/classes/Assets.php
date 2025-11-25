<?php

namespace MPHB\Addons\RequestPayment;

use MPHB\Addons\RequestPayment\Entities\Request;
use MPHB\Addons\RequestPayment\Utils\BookingUtils;
use MPHB\Addons\RequestPayment\Utils\RequestUtils;
use MPHB\Entities\Booking;

class Assets
{
    protected $isDebug = false;

    public function __construct()
    {
        $this->isDebug = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG);

        add_action('wp_enqueue_scripts', array($this, 'register'));
        add_action('admin_enqueue_scripts', array($this, 'register'));
    }

    public function register()
    {
        $frontScriptDependencies = array('mphb', 'mphb-jquery-serialize-json');

        if (MPHB()->gatewayManager()->getGateway('stripe')->isActive()) {
            $frontScriptDependencies[] = 'mphb-vendor-stripe-library';
        }

        if (MPHB()->gatewayManager()->getGateway('braintree')->isActive()) {
            $frontScriptDependencies[] = 'mphb-vendor-braintree-client-sdk';
        }

        wp_register_script('mphbrp-front', $this->scriptUrl('assets/scripts/frontend.min.js'), $frontScriptDependencies, MPHBRP()->getVersion(), true);

        wp_register_script('mphbrp-link-manager', $this->scriptUrl('assets/scripts/link-manager.min.js'), array('jquery'), MPHBRP()->getVersion(), true);

        wp_localize_script('mphbrp-link-manager', 'MPHBRP', array(
            '_data' => array(
                'ajaxUrl'  => admin_url('admin-ajax.php'),
                'nonces'   => array(
                    'disableAutoRequest'   => wp_create_nonce('mphbrp_disable_auto_request'),
                    'addPaymentRequest'    => wp_create_nonce('mphbrp_add_payment_request'),
                    'sendPaymentRequest'   => wp_create_nonce('mphbrp_send_payment_request'),
                    'deletePaymentRequest' => wp_create_nonce('mphbrp_delete_payment_request'),
                ),
                'page'     => array(
                    'bookingId' => BookingUtils::getEditingBookingId(),
                ),
                'messages' => array(
                    'unableToCopy' => esc_html__('Sorry, unable to copy.', 'mphb-request-payment'),
                    'copied'       => esc_html__('Link copied.', 'mphb-request-payment'),
                ),
            ),
        ));
    }

    public function enqueueFront()
    {
        wp_enqueue_script('mphbrp-front');
    }

    public function enqueueAdmin()
    {
    }

    /**
     * @since 2.0.0 added parameter $request.
     *
     * @param Booking $booking
     * @param Request $request Optional.
     */
    public function addCheckoutData($booking, $request = null)
    {
        if (!is_null($request)) {
            $toPay = RequestUtils::calcPriceForRequest($request, $booking);
        } else {
            $toPay = BookingUtils::getUnpaidPrice($booking);
        }

        // Add price
        MPHB()->getPublicScriptManager()->addCheckoutData(array('total' => $toPay));

        // Add gateways
        foreach (MPHB()->gatewayManager()->getListActive() as $gateway) {
            $checkoutData = $gateway->getCheckoutData($booking);

            // Don't use the deposit amount for requests, use the real "to pay"
            // price instead
            $checkoutData['amount'] = $toPay;

            MPHB()->getPublicScriptManager()->addGatewayData($gateway->getId(), $checkoutData);
        }
    }

    protected function scriptUrl($relativePath)
    {
        if ($this->isDebug) {
            $relativePath = str_replace(array('.min.js', '.min.css'), array('.js', '.css'), $relativePath);
        }

        return MPHBRP()->urlTo($relativePath);
    }
}
