<?php

namespace MPHB\Addons\RequestPayment\Emails;

use MPHB\Addons\RequestPayment\Entities\Request;
use MPHB\Addons\RequestPayment\Utils\BookingUtils;
use MPHB\Addons\RequestPayment\Utils\RequestUtils;
use MPHB\Entities\Booking;

class NewTags
{
    public function __construct()
    {
        $this->addActions();
    }

    protected function addActions()
    {
        // See MPHB\Emails\Templaters\EmailTemplater::_fill*Tags();
        add_filter('mphb_email_booking_tags', array($this, 'addLinkTags'));
        add_filter('mphb_email_booking_tags', array($this, 'addPriceTags'));

        add_filter('mphb_email_replace_tag', array($this, 'replaceTag'), 10, 3); // Skip 4th argument: payment
    }

    /**
     * @since 2.0.0
     *
     * @access protected
     *
     * @param array $tags [Tag index => [%Tag args%]]
     * @param array
     */
    public function addLinkTags($tags)
    {
        $linkTags = array(
            array(
                'name'             => 'booking_payment_request_link',
                'description'      => esc_html__('Booking Payment Request Link', 'mphb-request-payment'),
                'deprecated'       => false,
                'deprecated_title' => '',
                'inner_tags'       => array(),
            ),
            array(
                'name'             => 'booking_payment_request_deposit_link',
                'description'      => esc_html__('Booking Payment Request Deposit Link', 'mphb-request-payment'),
                'deprecated'       => false,
                'deprecated_title' => '',
                'inner_tags'       => array(),
            ),
        );

        $tags = $this->insertTagsAfter($tags, $linkTags, 'booking_edit_link');

        return $tags;
    }

    /**
     * @since 2.0.0
     *
     * @access protected
     *
     * @param array $tags [Tag index => [%Tag args%]]
     * @param array
     */
    public function addPriceTags($tags)
    {
        $priceTags = array(
            array(
                'name'             => 'booking_balance_due',
                'description'      => esc_html__('Booking Balance Due', 'mphb-request-payment'),
                'deprecated'       => false,
                'deprecated_title' => '',
                'inner_tags'       => array(),
            ),
            array(
                'name'             => 'booking_deposit_amount',
                'description'      => esc_html__('Booking Deposit Amount', 'mphb-request-payment'),
                'deprecated'       => false,
                'deprecated_title' => '',
                'inner_tags'       => array(),
            ),
        );

        $tags = $this->insertTagsAfter($tags, $priceTags, 'booking_total_price');

        return $tags;
    }

    /**
     * @since 2.0.0
     *
     * @param array $tags [Tag index => [%Tag args%]]
     * @param array
     */
    public function addPrivateTags($tags)
    {
        $privateTags = array(
            array(
                'name'             => 'booking_requested_amount',
                'description'      => esc_html__('Booking Requested Amount', 'mphb-request-payment'),
                'deprecated'       => false,
                'deprecated_title' => '',
                'inner_tags'       => array(),
            ),
        );

        $tags = $this->insertTagsAfter($tags, $privateTags, 'booking_deposit_amount');

        return $tags;
    }

    /**
     * @param array $tags
     * @param array $newTags
     * @param string $afterTag
     */
    protected function insertTagsAfter($tags, $newTags, $afterTag)
    {
        $tagNames = array_column($tags, 'name');
        $index    = array_search($afterTag, $tagNames);

        if ($index !== false) {
            return array_merge(
                array_slice($tags, 0, $index + 1, true),
                $newTags,
                array_slice($tags, $index + 1, count($tags), true)
            );
        } else {
            return array_merge($tags, $newTags);
        }
    }

    /**
     * @since 2.0.0 added parameter $tagArgs.
     *
     * @param string $replacement
     * @param string $tag
     * @param Booking $booking
     * @param array $tagArgs Optional.
     *     @param Request $tagArgs['request']
     * @return string
     */
    public function replaceTag($replacement, $tag, $booking = null, $tagArgs = array())
    {
        if (empty($replacement) && !is_null($booking)) { // Nothing to do here without booking

            $request = (isset($tagArgs['request']) ? $tagArgs['request'] : null);

            switch ($tag) {
                case 'booking_payment_request_link':
                    if (!is_null($request)) {
                        $replacement = RequestUtils::getCheckoutUrl($booking, $request);
                    } else {
                        $replacement = BookingUtils::getCheckoutUrl($booking);
                    }

                    break;

                case 'booking_payment_request_deposit_link':
                    $replacement = RequestUtils::getCheckoutUrlForDeposit($booking);
                    break;

                case 'booking_balance_due':
                    $unpaidPrice = BookingUtils::getUnpaidPrice($booking);
                    $replacement = mphb_format_price($unpaidPrice);
                    break;

                case 'booking_deposit_amount':
                    $depositPrice = BookingUtils::calcDepositPrice($booking);
                    $replacement = mphb_format_price($depositPrice);
                    break;

                case 'booking_requested_amount':
                    if (!is_null($request)) {
                        $requestPrice = RequestUtils::calcPriceForRequest($request, $booking);
                    } else {
                        $requestPrice = BookingUtils::getUnpaidPrice($booking);
                    }

                    $replacement = mphb_format_price($requestPrice);

                    break;
            }
        }

        return $replacement;
    }
}
