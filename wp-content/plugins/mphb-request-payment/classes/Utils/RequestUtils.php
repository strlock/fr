<?php

namespace MPHB\Addons\RequestPayment\Utils;

use MPHB\Addons\RequestPayment\Entities\Request;
use MPHB\Addons\RequestPayment\Settings;
use MPHB\Addons\RequestPayment\Utils\BookingUtils;
use MPHB\Entities\Booking;
use MPHB\Utils\DateUtils;
use DateTime;

class RequestUtils
{
    /**
     * @since 2.0.0
     *
     * @var array [%booking_id% => %price%]. See calcPriceForBooking().
     */
    protected static $cachedPrices = array();

    /**
     * @since 2.0.0
     *
     * @param Request $request
     * @param Booking $booking
     * @param boolean $ignoreCache Optional. False by default.
     * @return float
     */
    public static function calcPriceForRequest($request, $booking, $ignoreCache = false)
    {
        if (!$ignoreCache
            && array_key_exists($booking->getId(), static::$cachedPrices)
        ) {
            return static::$cachedPrices[$booking->getId()];
        }

        // Calculate new toPayPrice
        $totalPrice  = $booking->getTotalPrice();
        $unpaidPrice = BookingUtils::getUnpaidPrice($booking);
        $toPayPrice  = 0;

        switch ($request->getType()) {
            case Request::REQUEST_TYPE_DEPOSIT:
                $toPayPrice = BookingUtils::calcDepositPrice($booking);
                break;

            case Request::REQUEST_TYPE_PERCENT:
                $toPayPrice = round($totalPrice * ($request->getAmount() / 100), 2);
                break;

            case Request::REQUEST_TYPE_FIXED:
                $toPayPrice = $request->getAmount();
                break;

            case Request::REQUEST_TYPE_FULL:
            default:
                $toPayPrice = $unpaidPrice;
                break;
        }

        // Validate price
        if (!$request->isCustomRequest()) {
            // Full & Deposit
            $toPayPrice = mphb_limit($toPayPrice, 0, $unpaidPrice);
        } else {
            // Percentabe & Fixed: $toPayPrice may exceed the $unpaidPrice price
        }

        // Save price
        static::$cachedPrices[$booking->getId()] = $toPayPrice;

        return $toPayPrice;
    }

    /**
     * @since 2.0.0
     *
     * @param Booking $booking
     * @param Request $request
     * @return string
     */
    public static function getCheckoutUrl($booking, $request)
    {
        // Already with "key" arg
        $checkoutUrl = BookingUtils::getCheckoutUrl($booking);

        // Add "type" and "amount" args
        switch ($request->getType()) {
            case Request::REQUEST_TYPE_FULL:
                break;

            case Request::REQUEST_TYPE_DEPOSIT:
                $checkoutUrl = add_query_arg('type', 'deposit', $checkoutUrl);
                break;

            case Request::REQUEST_TYPE_PERCENT:
            case Request::REQUEST_TYPE_FIXED:
                $checkoutUrl = add_query_arg('type', $request->getType(), $checkoutUrl);
                $checkoutUrl = add_query_arg('amount', $request->getAmount(), $checkoutUrl);
                break;
        }

        return $checkoutUrl;
    }

    /**
     * @since 2.0.0
     *
     * Simple function for tag %booking_payment_request_deposit_link% that
     * doesn't require the Request object.
     *
     * @see \MPHB\Addons\RequestPayment\Emails\NewTags::replaceTag()
     *
     * @param Booking $booking
     * @return string
     */
    public static function getCheckoutUrlForDeposit($booking)
    {
        $checkoutUrl = BookingUtils::getCheckoutUrl($booking);
        $checkoutUrl = add_query_arg('type', 'deposit', $checkoutUrl);

        return $checkoutUrl;
    }

    /**
     * @since 2.0.0
     *
     * @return int[]
     *
     * @global \wpdb $wpdb
     */
    public static function findNewBookingIdsForAutoPayments()
    {
        global $wpdb;

        $bookingType = MPHB()->postTypes()->booking()->getPostType();
        $lastSearchedId = Settings::getLastSkippedBookingId();
        $availableStatuses = BookingUtils::getAvailableStatusesForAutoPayments();
        $availableStatusesString = "'" . implode("', '", $availableStatuses) . "'";

        $query = "SELECT posts.ID AS ids"
            . " FROM {$wpdb->posts} AS posts"
            . " LEFT JOIN {$wpdb->postmeta} AS ready_meta"
                . " ON posts.ID = ready_meta.post_id AND ready_meta.meta_key = '_ready_for_payment_request'"
            . " LEFT JOIN {$wpdb->postmeta} AS disable_meta"
                . " ON posts.ID = disable_meta.post_id AND (disable_meta.meta_key = '_disable_payment_request' OR disable_meta.meta_key = '_payment_request_sent')"
            . " WHERE posts.post_type = '{$bookingType}' AND posts.ID > {$lastSearchedId} AND posts.post_status IN ({$availableStatusesString})"
                . " AND (ready_meta.post_id IS NULL OR ready_meta.meta_value = '1')"
                . " AND (disable_meta.post_id IS NULL OR disable_meta.meta_value != '1')"
            . " GROUP BY ids";

        $bookingIds = $wpdb->get_col($query);
        $bookingIds = array_map('absint', $bookingIds);

        return $bookingIds;
    }

    public static function isTimeForAutoPayment($booking)
    {
        // Check the dates
        $checkIn = $booking->getCheckInDate();
        $checkInTime = MPHB()->settings()->dateTime()->getCheckInTime(true);
        $checkIn->setTime($checkInTime[0], $checkInTime[1], $checkInTime[2]);

        $now = new DateTime('now');

        $daysToCheckIn = DateUtils::calcNights($now, $checkIn);
        $daysBeforeAutoRequest = Settings::getDaysBeforeCheckIn();

        if ($daysToCheckIn <= $daysBeforeAutoRequest) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @since 2.0.0
     *
     * @param Booking $booking
     * @param boolean $isAuto Optional. False by default.
     */
    public static function sendFullAmountPaymentRequest($booking, $isAuto = false)
    {
        MPHB()->emails()->getEmail('customer_request_payment')->trigger($booking, array('is_auto' => $isAuto));

        update_post_meta($booking->getId(), '_payment_request_sent', true);
        update_post_meta($booking->getId(), '_ready_for_payment_request', false);
    }

    /**
     * @since 2.0.0
     *
     * @param Booking $booking
     * @param Request $request
     */
    public static function sendCustomPaymentRequest($booking, $request)
    {
        $emailArgs = array(
            'tag_atts' => array(
                'request' => $request,
            ),
        );

        switch ($request->getType()) {
            case Request::REQUEST_TYPE_DEPOSIT:
                MPHB()->emails()->getEmail('customer_request_deposit_payment')->trigger($booking, $emailArgs);
                break;

            case Request::REQUEST_TYPE_PERCENT:
            case Request::REQUEST_TYPE_FIXED:
                MPHB()->emails()->getEmail('customer_request_custom_payment')->trigger($booking, $emailArgs);
                break;

            case Request::REQUEST_TYPE_FULL:
            default:
                MPHB()->emails()->getEmail('customer_request_payment')->trigger($booking, $emailArgs);
                break;
        }
    }

    /**
     * @since 2.0.0
     *
     * @param Booking $booking
     * @return Request[]
     */
    public static function findAvailableRequestsForBooking($booking)
    {
        $requests = array(
            new Request(0, array('type' => Request::REQUEST_TYPE_FULL)),
            new Request(0, array('type' => Request::REQUEST_TYPE_DEPOSIT)),
        );

        $customRules = BookingUtils::getCustomRequests($booking);

        foreach ($customRules as $postmetaId => $customRule) {
            $requests[] = new Request($postmetaId, $customRule);
        }

        return $requests;
    }

    /**
     * @param Booking $booking
     * @param array $searchArgs
     *     @param string    $searchArgs['type']
     *     @param int|float $searchArgs['amount']
     * @return Request|null
     */
    public static function findAvailableRequestForBooking($booking, $searchArgs = array())
    {
        $searchedType = isset($searchArgs['type']) ? $searchArgs['type'] : Request::REQUEST_TYPE_FULL;
        $searchedAmount = isset($searchArgs['amount']) ? $searchArgs['amount'] : 0;

        $availableRequests = static::findAvailableRequestsForBooking($booking);

        $requestTypesWithoutAmount = array(
            Request::REQUEST_TYPE_FULL,
            Request::REQUEST_TYPE_DEPOSIT,
        );

        foreach ($availableRequests as $request) {
            if ($request->getType() == $searchedType) {
                if ($request->getAmount() == $searchedAmount
                    || in_array($request->getType(), $requestTypesWithoutAmount)
                ) {
                    return $request;
                }
            }
        }

        // Nothing found
        return null;
    }

    /**
     * @since 2.0.0
     *
     * @param int $bookingId
     * @param int $requestId Request ID / booking postmeta ID.
     * @param string $requestType Optional. Deposit request by default.
     * @return Request|null
     *
     * @see RequestUtils::findAvailableRequestsForBooking()
     */
    public static function findRequest($bookingId, $requestId, $requestType = Request::REQUEST_TYPE_DEPOSIT)
    {
        if (in_array($requestType, array(Request::REQUEST_TYPE_FULL, Request::REQUEST_TYPE_DEPOSIT))) {
            return new Request(0, array('type' => $requestType));
        }

        // Check custom requests
        $customRequestsRaw = BookingUtils::getCustomRequests($bookingId);

        foreach ($customRequestsRaw as $customRequestId => $customRequest) {
            if ($customRequestId == $requestId && $customRequest['type'] == $requestType) {
                return new Request($customRequestId, $customRequest);
            }
        }

        return null;
    }

    /**
     * @since 2.0.0
     *
     * @param array $input
     *     @param array $input['request']
     *         @param string $input['request']['type']
     *         @param float  $input['request']['amount']
     *         @param string $input['request']['description'] Optional.
     * @return Request
     *
     * @see \MPHB\Addons\RequestPayment\Ajax::addPaymentRequest()
     */
    public static function validateRequest($input)
    {
        $requestType = mphb_clean($input['request']['type']);
        $requestAmount = mphbrp_parse_float($input['request']['amount'], 0);
        $requestDescription = (isset($input['request']['description']) ? mphb_clean($input['request']['description']) : '');

        if ( !in_array($requestType, array(Request::REQUEST_TYPE_PERCENT, Request::REQUEST_TYPE_FIXED)) ) {
            wp_send_json_error(array(
                'status'  => 'error',
                'message' => esc_html__('Invalid type.', 'mphb-request-payment'),
            ));
        }

        // Validate amount
        if ($requestAmount <= 0) {
            wp_send_json_error(array(
                'status'  => 'error',
                'message' => esc_html__('Please enter the amount.', 'mphb-request-payment'),
            ));
        }

        // Request OK
        return new Request(0, array(
            'type'        => $requestType,
            'amount'      => $requestAmount,
            'description' => $requestDescription,
        ));
    }
}
