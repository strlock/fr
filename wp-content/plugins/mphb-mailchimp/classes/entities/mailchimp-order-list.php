<?php

namespace MPHB\Addons\MailChimp\Entities;

class MailchimpOrderList extends BookingEntity
{
    /**
     * @return array
     *
     * @throws \Exception If can't find the booking.
     */
    public function toArray()
    {
        $booking = $this->getBooking();

        if (is_null($booking)) {
            throw new \Exception(sprintf(esc_html__('Can\'t find the booking #%d to create MailChimp order list.', 'mphb-mailchimp'), $this->bookingId));
        }

        $itemsInfo = [];
        $priceInfo = $booking->getLastPriceBreakdown();

        $roomNo = 0;

        foreach ($booking->getReservedRooms() as $reservedRoom) {
            $roomTypeId = $reservedRoom->getRoomTypeId();

            if ($roomTypeId == 0) {
                continue;
            }

            $price = 0;
            $discount = 0;

            if (isset($priceInfo['rooms'][$roomNo])) {
                // Get real numbers
                $roomPrices = $priceInfo['rooms'][$roomNo];

                $price = floatval($roomPrices['discount_total']);
                $discount = floatval($roomPrices['total']) - $price;
            } else {
                // Get at least base numbers (without services, fees and taxes)

                // Check if the rate still exist when submitting old booking to
                // MailChimp. Hotel Booking plugin v3.7.0 and below does not check
                // the rate by itself
                $rate = mphb()->getRateRepository()->findById($reservedRoom->getRateId());

                if (!is_null($rate)) {
                    $price = $reservedRoom->calcRoomPrice($booking->getCheckInDate(), $booking->getCheckOutDate());
                }
            }

            $itemInfo = [
                'id'                 => (string)$reservedRoom->getId(),
                'product_id'         => (string)$roomTypeId,
                'product_variant_id' => (string)$reservedRoom->getRoomId(),
                'quantity'           => 1,
                // The "price" is required field. So at lease pass the base
                // price for the rate
                'price'              => $price
            ];

            if ($discount > 0) {
                $itemInfo['discount'] = $discount;
            }

            $itemsInfo[] = $itemInfo;

            $roomNo++;
        }

        return $itemsInfo;
    }
}
