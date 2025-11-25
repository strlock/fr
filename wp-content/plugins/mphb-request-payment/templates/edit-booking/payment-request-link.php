<?php

/**
 * @since 2.0.0
 *
 * @var Booking $booking
 * @var Request $request
 */

use MPHB\Addons\RequestPayment\Entities\Request;
use MPHB\Addons\RequestPayment\Utils\RequestUtils;
use MPHB\Entities\Booking;

$requestClass = 'mphbrp-payment-request ' . ($request->isCustomRequest() ? 'custom-request' : 'default-request');
$checkoutUrl = RequestUtils::getCheckoutUrl($booking, $request);

?>

<div class="<?php echo esc_attr($requestClass); ?>" data-id="<?php echo esc_attr($request->getId()); ?>" data-type="<?php echo esc_attr($request->getType()); ?>">
    <p class="mphbrp-payment-request-link-wrapper">
        <strong>
        <?php
        switch ($request->getType()) {
            case Request::REQUEST_TYPE_FULL:
                esc_html_e('Balance Due', 'mphb-request-payment');
                break;

            case Request::REQUEST_TYPE_DEPOSIT:
                esc_html_e('Deposit', 'mphb-request-payment');
                break;

            case Request::REQUEST_TYPE_PERCENT:
                printf("%s%%", $request->getAmount());
                break;

            case Request::REQUEST_TYPE_FIXED:
                echo mphb_format_price($request->getAmount());
                break;
        }
        ?>
        </strong>
        <?php if ($request->hasDescription()) { ?>
            <br/><small><?php esc_html_e($request->getDescription()); ?></small>
        <?php } ?>
    </p>

    <p class="mphbrp-payment-request-controls" style="color:#aaa;">
        <a href="#" class="button-send"><?php esc_html_e('Send', 'mphb-request-payment'); ?></a> |
        <a href="<?php echo esc_url($checkoutUrl); ?>" class="mphbrp-payment-request-link" target="_blank">
            <?php esc_html_e('Open', 'mphb-request-payment'); ?></a> |
        <a href="#" class="button-copy"><?php esc_html_e('Copy', 'mphb-request-payment'); ?></a>

        <?php if ($request->isCustomRequest()) { ?>
            | <a href="#" class="button-delete"><?php esc_html_e('Delete', 'mphb-request-payment'); ?></a>
        <?php } ?>
    </p>
    <hr>
</div>
