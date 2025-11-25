<?php

namespace MPHB\Addons\MailChimp\Entities;

use MPHB\Addons\MailChimp\Utils\BookingUtils;

class MailchimpCart extends BookingEntity
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

        // Handle all customers from cart as transactional only
        $this->customer = new MailchimpCustomer($bookingId, $booking, ['opt_in' => false]);

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
            throw new \Exception(sprintf(esc_html__('Can\'t find the booking #%d to create MailChimp cart.', 'mphb-mailchimp'), $this->bookingId));
        } else if (empty($booking->getCustomer()->getEmail())) {
            throw new \Exception(sprintf(esc_html__('Booking #%d have no customer to connect with MailChimp.', 'mphb-mailchimp'), $this->bookingId));
        }

        $cartInfo = [
            'id'            => $this->id,
            'customer'      => $this->customer->toArray(),
            'checkout_url'  => $this->getDetailsUrl(),
            'currency_code' => mphb()->settings()->currency()->getCurrencyCode(),
            'order_total'   => $booking->getTotalPrice(),
            'tax_total'     => BookingUtils::getTaxesTotal($booking),
            'lines'         => $this->orderList->toArray()
        ];

        // Add campaign ID
        $campaignId = $this->getCampaignId();

        if (!empty($campaignId)) {
            $cartInfo['campaign_id'] = $campaignId;
        }

        return $cartInfo;
    }
}
