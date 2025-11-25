<?php

namespace MPHB\Addons\MailChimp\Listeners;

use MPHB\PostTypes\BookingCPT\Statuses as BookingStatuses;

class Subscription
{
    public function __construct()
    {
        $this->addAction();
    }

    protected function addAction()
    {
        add_action('mphb_create_booking_by_user', [$this, 'allowSubscription']);

        // Call before e-commerce action and after the tracking action
        add_action('mphb_booking_confirmed', [$this, 'subscribeCustomer']);
    }

    /**
     * @param \MPHB\Entities\Booking $booking
     */
    public function allowSubscription($booking)
    {
        if (isset($_POST['mphb_mc_confirm_subscription'])) {
            // The value is boolean, no need to read it or validate. Just save TRUE
            add_post_meta($booking->getId(), '_mphb_mc_customer_confirmed_subscription', true, true);

            if ($booking->getStatus() == BookingStatuses::STATUS_CONFIRMED) {
                // Required for Add New Booking page: "mphb_booking_confirmed"
                // triggers before "mphb_create_booking_by_user"
                $this->subscribeCustomer($booking);
            }
        }
    }

    /**
     * @param \MPHB\Entities\Booking $booking
     */
    public function subscribeCustomer($booking)
    {
        $alreadySubscribed = !empty(get_post_meta($booking->getId(), '_mphb_mc_customer_id', true));

        // Prevent resubscription when changing booking statuses manually
        if ($alreadySubscribed) {
            return;
        }

        $canSubscribe = !mphbmc()->settings()->askForSubscription() || (bool)get_post_meta($booking->getId(), '_mphb_mc_customer_confirmed_subscription', true);

        if (!$canSubscribe) {
            return;
        }

        $customer     = $booking->getCustomer();
        $interests    = mphbmc()->settings()->getInterestsToSubscribe();
        $replaceLists = mphbmc()->settings()->replaceLists();

        $customerId = mphbmc()->api()->mailchimp()->subscribeCustomer($customer, $interests, $replaceLists);

        if ($customerId) {
            add_post_meta($booking->getId(), '_mphb_mc_customer_id', $customerId, true);
            delete_post_meta($booking->getId(), '_mphb_mc_customer_confirmed_subscription');

            $booking->addLog(esc_html__('Customer subscription details have been updated in MailChimp successfully.', 'mphb-mailchimp'));
        } else if (!empty($interests)) {
            $booking->addLog(sprintf(esc_html__('Failed to update customer subscription details in MailChimp. API response: "%s"', 'mphb-mailchimp'), mphbmc()->api()->mailchimp()->getLastError()));
        }
    }
}
