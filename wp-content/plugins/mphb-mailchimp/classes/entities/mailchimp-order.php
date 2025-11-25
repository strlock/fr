<?php

namespace MPHB\Addons\MailChimp\Entities;

use MPHB\Addons\MailChimp\Utils\BookingUtils;
use MPHB\PostTypes\BookingCPT\Statuses as BookingStatuses;

class MailchimpOrder extends BookingEntity
{
    /** @var \MPHB\Addons\MailChimp\Entities\MailchimpCustomer */
    public $customer = null;

    /** @var \MPHB\Addons\MailChimp\Entities\MailchimpOrderList */
    public $orderList = null;

    /**
     * @param int $bookingId
     * @param \MPHB\Entities\Booking $booking Optional.
     */
    public function __construct($bookingId, $booking = null)
    {
        parent::__construct($bookingId, $booking);

        $this->customer = new MailchimpCustomer($bookingId, $booking, ['opt_in' => mphbmc()->settings()->subscribeEcommerceCustomers()]);
        $this->orderList = new MailchimpOrderList($bookingId, $booking);
    }

    /**
     * @return array
     *
     * @throws \Exception If can't find the booking or if the customer is not set.
     */
    public function toArray()
    {
        $booking = $this->getBooking();

        if (is_null($booking)) {
            throw new \Exception(sprintf(esc_html__('Can\'t find the booking #%d to create MailChimp order.', 'mphb-mailchimp'), $this->bookingId));
        } else if (empty($booking->getCustomer()->getEmail())) {
            throw new \Exception(sprintf(esc_html__('Booking #%d have no customer to connect with MailChimp.', 'mphb-mailchimp'), $this->bookingId));
        }

        $customer = $booking->getCustomer();
        $countriesBundle = mphb()->settings()->main()->getCountriesBundle();

        $orderInfo = [
            'id'               => $this->id,
            'customer'         => $this->customer->toArray(),
            'landing_site'     => home_url(),
            'financial_status' => 'pending',
            'currency_code'    => mphb()->settings()->currency()->getCurrencyCode(),
            'order_total'      => $booking->getTotalPrice(),
            'order_url'        => $this->getDetailsUrl(),
            'discount_total'   => BookingUtils::getDiscountTotal($booking),
            'tax_total'        => BookingUtils::getTaxesTotal($booking),
            'processed_at_foreign' => get_post_time('c', true, $this->bookingId, true), // ISO 8601 format
            'updated_at_foreign'   => get_post_modified_time('c', true, $this->bookingId, true), // ISO 8601 format
            'lines'            => $this->orderList->toArray(),
            'billing_address'  => [
                'address1'       => $customer->getAddress1(),
                'city'           => $customer->getCity(),
                'postal_code'    => $customer->getZip(),
                'country'        => $countriesBundle->getCountryLabel($customer->getCountry()),
                'country_code'   => $customer->getCountry(),
                'phone'          => $customer->getPhone()
            ]
        ];

        // Set different status
        switch ($booking->getStatus()) {
            case BookingStatuses::STATUS_CONFIRMED:
                if (mphb_mc_booking_paid($booking)) {
                    $orderInfo['financial_status'] = 'paid';
                }
                break;

            case BookingStatuses::STATUS_CANCELLED:
            case BookingStatuses::STATUS_ABANDONED:
                $orderInfo['financial_status'] = 'cancelled';
                break;
        }

        // Add campaign ID
        $campaignId = $this->getCampaignId();

        if (!empty($campaignId)) {
            $orderInfo['campaign_id'] = $campaignId;
        }

        return $orderInfo;
    }
}
