<?php

namespace MPHB\Addons\MailChimp\Entities;

/**
 * MailChimp <b>e-commerce</b> customer.
 */
class MailchimpCustomer extends BookingEntity
{
    /**
     * @var bool FALSE - handle as transactional customers, TRUE - subscribe
     * customer.
     */
    public $optIn = false;

    /**
     * @param int $bookingId
     * @param \MPHB\Entities\Booking $booking Optional.
     * @param array $args
     * @param bool $args['opt_in'] Optional. FALSE by default.
     */
    public function __construct($bookingId, $booking = null, $args = [])
    {
        parent::__construct($bookingId, $booking);

        if (isset($args['opt_in'])) {
            $this->optIn = $args['opt_in'];
        }
    }

    /**
     * @return array
     *
     * @throws \Exception If can't find the booking.
     */
    public function toArray()
    {
        $booking = $this->getBooking();

        if (is_null($booking)) {
            throw new \Exception(sprintf(esc_html__('Can\'t find the booking #%d to create MailChimp customer.', 'mphb-mailchimp'), $this->bookingId));
        }

        $customer = $booking->getCustomer();
        $countriesBundle = mphb()->settings()->main()->getCountriesBundle();

        $customerInfo = [
            'id'            => $this->id,
            'email_address' => $customer->getEmail(),
            'opt_in_status' => $this->optIn, // false - transactional, true - subscribed
            'first_name'    => $customer->getFirstName(),
            'last_name'     => $customer->getLastName(),
            'address'       => [
                'address1'      => $customer->getAddress1(),
                'city'          => $customer->getCity(),
                'postal_code'   => $customer->getZip(),
                'country'       => $countriesBundle->getCountryLabel($customer->getCountry()),
                'country_code'  => $customer->getCountry()
            ]
        ];

        return $customerInfo;
    }
}
