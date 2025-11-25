<?php

namespace MPHB\Addons\RequestPayment\MetaBoxes;

use MPHB\Addons\RequestPayment\Utils\BookingUtils;
use MPHB\Addons\RequestPayment\Utils\RequestUtils;
use MPHB\Admin\Fields\FieldFactory;
use MPHB\Admin\Groups\MetaBoxGroup;

/**
 * @since 2.0.0
 */
class LinkManagerMetaBox extends CustomMetaBox
{
    /**
     * @param MetaBoxGroup[] $metaBoxes
     * @param string $postType
     * @return MetaBoxGroup[]
     */
    public function registerInMphb($metaBoxes, $postType)
    {
        if ($postType != $this->postType) {
            return $metaBoxes;
        }

        $postStatus = '';

        if (isset($_POST['mphb_post_status'])) {
            // Catch new status on "Update Booking" action
            $postStatus = $_POST['mphb_post_status'];

        } else {
            $booking = BookingUtils::getEditingBooking();

            if (!is_null($booking)) {
                $postStatus = $booking->getStatus();
            }
        }

        // Don't show the metabox for cancelled and abandoned bookings
        if (BookingUtils::isStatusAvailableForEditing($postStatus)) {
            $metaBoxes[] = $this;
        }

        return $metaBoxes;
    }

    protected function registerFields()
    {
        $this->addFields(array(
            FieldFactory::create('mphbrp_custom_request_type', array(
                'type'        => 'radio',
                'label'       => esc_html__('Type', 'mphb-request-payment'),
                'list'        => array(
                    'percent'     => esc_html__('Percentage', 'mphb-request-payment'),
                    'fixed'       => esc_html__('Fixed', 'mphb-request-payment')
                ),
                'default'     => 'percent',
            )),
            FieldFactory::create('mphbrp_custom_request_amount', array(
                'type'        => 'number',
                'label'       => esc_html__('Amount', 'mphb-request-payment'),
                'min'         => 0,
                'step'        => 0.01,
                'size'        => 'price',
                'allow_empty' => true,
                'default'     => '',
            )),
            FieldFactory::create('mphbrp_custom_request_description', array(
                'type'        => 'textarea',
                'label'       => esc_html__('Description (for internal use)', 'mphb-request-payment'),
                'rows'        => 2,
            )),
        ));
    }

    public function render()
    {
        $booking = BookingUtils::getEditingBooking();

        if (is_null($booking)) {
            return;
        }

        // Show checkbox
        echo '<p id="mphbrp-disable-auto-request-control" class="mphb-ctrl-wrapper mphb-ctrl mphb-ctrl-checkbox" data-type="checkbox" data-inited="true">';
            echo '<input id="mphbrp-disable-auto-request" name="mphbrp_disable_auto_request" value="1"', checked(true, BookingUtils::isAutoRequestDisabledByUser($booking->getId()), false), ' type="checkbox" style="margin-top: 0">';
            echo '&nbsp;';
            echo '<label for="mphbrp-disable-auto-request">', esc_html__('Disable automatic payment requests for this booking', 'mphb-request-payment'), '</label>';
        echo '</p>';

        // Show all available request links
        $requests = RequestUtils::findAvailableRequestsForBooking($booking);

        echo '<div id="mphbrp-payment-requests">';
            echo '<hr>';
            foreach ($requests as $request) {
                echo mphbrp_render_template('edit-booking/payment-request-link', array(
                    'booking' => $booking,
                    'request' => $request,
                ));
            }
        echo '</div>';

        // Add button and form
        echo '<div id="mphb-add-payment-request-form">';
            echo '<div class="mphbrp-custom-fields mphb-hide">';
                parent::render(); // Render fields
            echo '</div>';

            echo '<p class="mphbrp-payment-request-controls">';
                echo '<button class="button button-secondary button-new">', esc_html__('New Request', 'mphb-request-payment'), '</button>';
                echo '<button class="button button-secondary button-add mphb-hide">', esc_html__('Add Request', 'mphb-request-payment'), '</button>';
                echo ' ';
                echo '<button class="button button-secondary button-cancel mphb-hide">', esc_html__('Cancel', 'mphb-request-payment'), '</button>';
            echo '</p>';
        echo '</div>';

        wp_enqueue_script('mphbrp-link-manager');
    }

    /**
     * @since 2.0.0
     */
    public function save()
    {
        // Don't save custom fields
    }
}
