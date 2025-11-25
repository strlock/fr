<?php

namespace MPHB\Addons\RequestPayment\Utils;

use MPHB\Addons\RequestPayment\Entities\Request;
use MPHB\Addons\RequestPayment\Settings;
use MPHB\Entities\Booking;
use MPHB\PostTypes\BookingCPT\Statuses as BookingStatuses;

class BookingUtils
{
    /**
     * Workaround for safe searching by zero ID.
     *
     * @since 2.0.0
     *
     * @param int $id
     * @param boolean $force Optional. False by default.
     * @return Booking|null
     */
    public static function findById($id, $force = false)
    {
        if ($id != 0) {
            return MPHB()->getBookingRepository()->findById($id, $force);
        } else {
            return null;
        }
    }

    /**
     * @since 2.0.0
     *
     * @param int $bookingId
     * @param Request|array $request
     * @return int|false New postmeta ID or false.
     */
    public static function addCustomRequest($bookingId, $request)
    {
        if (!is_array($request)) {
            $request = $request->toArray('no-id');
        }

        $metaId = add_post_meta($bookingId, 'mphbrp_custom_rule', $request);

        return $metaId;
    }

    /**
     * @since 2.0.0
     *
     * @param int $bookingId
     * @param int $requestId
     * @return bool Whether the request was deleted or not.
     *
     * @global \wpdb $wpdb
     */
    public static function deleteCustomRequest($bookingId, $requestId)
    {
        global $wpdb;

        $rowsDeleted = $wpdb->delete(
            $wpdb->postmeta,
            array(
                'meta_id' => $requestId,
                'post_id' => $bookingId,
                'meta_key' => 'mphbrp_custom_rule',
            ),
            array(
                '%d',
                '%d',
                '%s'
            )
        );

        return ($rowsDeleted !== false && $rowsDeleted > 0);
    }

    /**
     * @since 2.0.0
     *
     * @param Booking|int $booking
     * @return array [%meta_id% => ['type', 'amount', 'description']]
     *
     * @global \wpdb $wpdb
     */
    public static function getCustomRequests($booking)
    {
        global $wpdb;

        $bookingId = is_object($booking) ? $booking->getId() : $booking;

        $selectQuery = $wpdb->prepare("SELECT `meta_id` AS `ID`, `meta_value` AS `value` FROM `{$wpdb->postmeta}` WHERE `post_id` = %d AND `meta_key` = 'mphbrp_custom_rule'", $bookingId);
        $postmetas = $wpdb->get_results($selectQuery, ARRAY_A);

        $customRules = array();

        foreach ($postmetas as $postmeta) {
            $customRules[$postmeta['ID']] = maybe_unserialize($postmeta['value']);
        }

        return $customRules;
    }

    /**
     * @since 2.0.0
     *
     * @return int
     */
    public static function getEditingBookingId()
    {
        $postId = 0;

        if (isset($_REQUEST['post_ID']) && is_numeric($_REQUEST['post_ID'])) {
            $postId = intval($_REQUEST['post_ID']); // On post update ($_POST)

        } else if (isset($_REQUEST['post']) && is_numeric($_REQUEST['post'])) {
            $postId = intval($_REQUEST['post']); // On post edit page ($_GET)
        }

        return $postId;
    }

    /**
     * @return Booking|null
     */
    public static function getEditingBooking()
    {
        $bookingId = static::getEditingBookingId();

        return static::findById($bookingId);
    }

    /**
     * @since 2.0.0
     *
     * @return string
     */
    public static function getCheckoutUrlForEditingBooking()
    {
        $editingBooking = static::getEditingBooking();

        if (!is_null($editingBooking)) {
            return static::getCheckoutUrl($editingBooking);
        } else {
            return Settings::getCheckoutPageUrl();
        }
    }

    /**
     * @since 2.0.0
     *
     * @param Booking $booking
     * @return string
     */
    public static function getCheckoutUrl($booking)
    {
        $bookingKey = static::getOrderKey($booking);

        $checkoutUrl = Settings::getCheckoutPageUrl();
        $checkoutUrl = add_query_arg('key', $bookingKey, $checkoutUrl);

        return $checkoutUrl;
    }

    /**
     * @param string $bookingKey
     * @return Booking|null
     */
    public static function findBookingByKey($bookingKey)
    {
        return MPHB()->getBookingRepository()->findByMeta('mphb_key', $bookingKey);
    }

    /**
     * Will generate and save new key if the booking does not have it.
     *
     * @param Booking $booking
     * @return string
     */
    public static function getOrderKey($booking)
    {
        $key = $booking->getKey();

        if (empty($key)) {
            // This will also automatically save the new key
            $key = $booking->generateKey();
        }

        return $key;
    }

    /**
     * Will generate and save new ID if the booking does not have it.
     *
     * @param Booking $booking
     * @return string
     */
    public static function getCheckoutId($booking)
    {
        $checkoutId = $booking->getCheckoutId();

        if (empty($checkoutId)) {
            $checkoutId = mphb_generate_uuid4();

            // Save new checkout ID
            update_post_meta($booking->getId(), '_mphb_checkout_id', $checkoutId);
        }

        return $checkoutId;
    }

    /**
     * @since 2.0.0
     *
     * @param int $bookingId
     * @param boolean $isDisabled
     */
    public static function toogleAutoRequest($bookingId, $isDisabled)
    {
        update_post_meta($bookingId, '_disable_payment_request', $isDisabled);
    }

    /**
     * @param int $bookingId
     * @return bool
     */
    public static function isAutoRequestDisabledByUser($bookingId)
    {
        // "" (no post meta, or has empty value - not enabled) or "1" (enabled)
        $requestDisabled = get_post_meta($bookingId, '_disable_payment_request', true);

        return (bool)$requestDisabled;
    }

    /**
     * @since 2.0.0
     *
     * @param int $bookingId
     * @return boolean
     */
    public static function isAutoRequestDisabledByPlugin($bookingId)
    {
        return !static::isReadyForAutoRequest($bookingId);
    }

    /**
     * @since 2.0.0
     *
     * @param int $bookingId
     * @return boolean
     */
    public static function isAutoRequestDisabled($bookingId)
    {
        return static::isAutoRequestDisabledByUser($bookingId)
            || static::isAutoRequestDisabledByPlugin($bookingId);
    }

    /**
     * @since 2.0.0
     *
     * @return string[]
     */
    public static function getAvailableStatusesForAutoPayments()
    {
        return apply_filters(
            'mphbrp_booking_statuses_for_auto_payments',
            array(
                BookingStatuses::STATUS_CONFIRMED,
            )
        );
    }

    /**
     * Get available booking statuses for deposit and partial payments.
     *
     * @since 2.0.0
     *
     * @return string[]
     */
    public static function getAvailableStatusesForDeposits()
    {
        return apply_filters(
            'mphbrp_booking_statuses_for_deposits',
            array(
                BookingStatuses::STATUS_CONFIRMED,
                BookingStatuses::STATUS_PENDING,
            )
        );
    }

    /**
     * @since 2.0.0
     *
     * @return string[]
     */
    public static function getAvailableStatusesForEditing()
    {
        $autoPaymentStatuses = static::getAvailableStatusesForAutoPayments();
        $depositPaymentStatuses = static::getAvailableStatusesForDeposits();

        $allAvailableStatuses = array_merge($autoPaymentStatuses, $depositPaymentStatuses);
        $allAvailableStatuses = array_unique($allAvailableStatuses);

        return $allAvailableStatuses;
    }

    /**
     * @since 2.0.0
     *
     * @param string $bookingStatus
     * @return boolean
     */
    public static function isStatusAvailableForAutoPayments($bookingStatus)
    {
        return in_array($bookingStatus, static::getAvailableStatusesForAutoPayments());
    }

    /**
     * @since 2.0.0
     *
     * @param string $bookingStatus
     * @return boolean
     */
    public static function isStatusAvailableForEditing($bookingStatus)
    {
        return in_array($bookingStatus, static::getAvailableStatusesForEditing());
    }

    /**
     * @param int $bookingId
     * @return bool
     */
    public static function isAutoRequestSent($bookingId)
    {
        // "" (no post meta, or has empty value - not sent) or "1" (sent)
        $requestSent = get_post_meta($bookingId, '_payment_request_sent', true);

        return (bool)$requestSent;
    }

    /**
     * @param int $bookingId
     * @return bool
     */
    public static function isReadyForAutoRequest($bookingId)
    {
        // [] (no post meta), [""] (not ready) or ["1"] (ready)
        $metaValue = get_post_meta($bookingId, '_ready_for_payment_request');

        if (empty($metaValue)) {
            // No meta value, but != [""], so the booking is ready
            return true;
        } else {
            $requestReady = reset($metaValue);
            return (bool)($requestReady);
        }
    }

    /**
     * @param int|Booking $booking
     * @return MPHB\Entities\Payment[]
     */
    public static function getPayments($booking)
    {
        $bookingId = is_object($booking) ? $booking->getId() : $booking;
        $payments  = MPHB()->getPaymentRepository()->findAll(array('booking_id' => $bookingId));

        return $payments;
    }

    public static function getPaidPrice($booking)
    {
        $payments = static::getPayments($booking);
        $paid = PaymentUtils::getPaidPrice($payments);

        return $paid;
    }

    /**
     * @param Booking $booking
     * @return float
     */
    public static function getUnpaidPrice($booking)
    {
        $total = $booking->getTotalPrice();
        $paid  = static::getPaidPrice($booking);

        return $total - $paid;
    }

    /**
     * Ignores MPHB()->settings()->payment()->getAmountType() and just calculates
     * the deposit price.
     *
     * @since 2.0.0
     *
     * @param Booking $booking
     * @return float
     */
    public static function calcDepositPrice($booking)
    {
        $depositType = MPHB()->settings()->payment()->getDepositType();
        $depositAmount = (float)MPHB()->settings()->payment()->getDepositAmount();

        if ($depositType == 'percent') {
            $depositPrice = round($booking->getTotalPrice() * ($depositAmount / 100), 2);
        } else {
            $depositPrice = $depositAmount;
        }

        /**
         * @since 2.0.0
         *
         * @param float $deposit
         * @param Booking $booking
         */
        $depositPrice = apply_filters('mphbrp_booking_deposit_price', $depositPrice, $booking);

        return $depositPrice;
    }
}
