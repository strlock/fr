<?php

namespace MPHB\Addons\MailChimp\Utils;

use MPHB\PostTypes\PaymentCPT\Statuses as PaymentStatuses;

class BookingUtils
{
    /**
     * @param \MPHB\Entities\Booking $booking
     * @return float
     */
    public static function getToPayPrice($booking)
    {
        $total = $booking->getTotalPrice();
        $paid  = static::getPaidPrice($booking);

        return $total - $paid;
    }

    /**
     * @param \MPHB\Entities\Booking $booking
     * @return float
     */
    public static function getPaidPrice($booking)
    {
        $payments = static::getPayments($booking);

        $paid = array_reduce($payments, function ($paid, $payment) {
            if ($payment->getStatus() == PaymentStatuses::STATUS_COMPLETED) {
                $paid += $payment->getAmount();
            }

            return $paid;
        }, 0.0);

        return $paid;
    }

    /**
     * @param int|\MPHB\Entities\Booking $booking
     * @return MPHB\Entities\Payment[]
     */
    public static function getPayments($booking)
    {
        $bookingId = is_object($booking) ? $booking->getId() : $booking;
        $payments  = mphb()->getPaymentRepository()->findAll(['booking_id' => $bookingId]);

        return $payments;
    }

    /**
     * @param \MPHB\Entities\Booking $booking
     * @param bool $includeFees Optional. TRUE by default.
     * @return float
     */
    public static function getTaxesTotal($booking, $includeFees = true)
    {
        $prices = $booking->getLastPriceBreakdown();

        if (empty($prices) || empty($prices['rooms'])) {
            return 0;
        }

        $taxTotal = 0.0;

        foreach ($prices['rooms'] as $roomPrices) {
            // Add fees
            if ($includeFees && isset($roomPrices['fees'])) {
                $taxTotal += floatval($roomPrices['fees']['total']);
            }

            // Add taxes
            if (isset($roomPrices['taxes'])) {
                foreach ($roomPrices['taxes'] as $taxes) {
                    $taxTotal += floatval($taxes['total']);
                }
            }
        }

        return $taxTotal;
    }

    /**
     * @param \MPHB\Entities\Booking $booking
     * @return float
     */
    public static function getDiscountTotal($booking)
    {
        $prices = $booking->getLastPriceBreakdown();

        if (empty($prices) || !isset($prices['coupon'])) {
            return 0;
        }

        $discountTotal = floatval($prices['coupon']['discount']);

        return $discountTotal;
    }
}
