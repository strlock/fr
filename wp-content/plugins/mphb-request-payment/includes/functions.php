<?php

use MPHB\Addons\RequestPayment\Utils\BookingUtils;
use MPHB\Addons\RequestPayment\Utils\RequestUtils;
use MPHB\Addons\RequestPayment\Plugin as RequestPaymentPlugin;
use MPHB\Utils\ValidateUtils;

/**
 * @return MPHB\Addons\RequestPayment\Plugin
 */
function MPHBRP()
{
    return RequestPaymentPlugin::getInstance();
}

/**
 * @param \MPHB\Entities\Booking $booking
 */
function maybe_request_auto_payment($booking)
{
    // Check price and time
    $haveToPay = (BookingUtils::getUnpaidPrice($booking) > 0);
    $isTime = RequestUtils::isTimeForAutoPayment($booking);

    if ($haveToPay && $isTime) {
        RequestUtils::sendFullAmountPaymentRequest($booking, true);
    } else if (!$haveToPay) {
        // All paid, no need to check this booking anymore
        update_post_meta($booking->getId(), '_ready_for_payment_request', false);
    }
}

/**
 * @since 2.0.0
 *
 * @param string $input
 * @return int
 */
function mphbrp_parse_id($input)
{
    return (int)ValidateUtils::validateInt($input, 0);
}

/**
 * @since 2.0.0
 *
 * @param array args Optional.
 *     @param float args['min']
 *     @param float args['max']
 */
function mphbrp_parse_float($input, $args = [])
{
    $value = filter_var($input, FILTER_VALIDATE_FLOAT, ['options' => ['default' => 0]]);

    if (isset($args['min'])) {
        $value = max($value, $args['min']);
    }

    if (isset($args['max'])) {
        $value = min($value, $args['max']);
    }

    return $value;
}

/**
 * @since 2.0.0
 *
 * @param string $templateSlug Template slug, like <i>"edit-booking/payment-request-link"</i>.
 * @param array $templateArgs Optional.
 * @return string
 */
function mphbrp_render_template($templateSlug, $templateArgs = array())
{
    $template = "{$templateSlug}.php";
    $templateFile = MPHBRP()->pathTo("templates/{$template}");

    /**
     * @since 2.0.0
     *
     * @param string $templateFile Absolute path to the template.
     * @param string $templateSlug Template slug, like "edit-booking/payment-request-link".
     * @param array  $templateArgs
     */
    $templateFile = apply_filters('mphb_get_template_part', $templateFile, $templateSlug, $templateArgs);

    if (!file_exists($templateFile)) {
        return '';
    }

    ob_start();

    if (!empty($templateArgs)) {
        extract($templateArgs);
    }

    require $templateFile;

    $templateContent = ob_get_clean();

    return $templateContent;
}
