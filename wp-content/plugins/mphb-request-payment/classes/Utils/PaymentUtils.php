<?php

namespace MPHB\Addons\RequestPayment\Utils;

use MPHB\Addons\RequestPayment\Entities\Request;
use MPHB\Entities\Payment;
use MPHB\PostTypes\PaymentCPT\Statuses as PaymentStatuses;

class PaymentUtils
{
    /**
     * @param Payment[] $payments
     * @return float
     */
    public static function getPaidPrice($payments)
    {
        $paid = array_reduce($payments, function ($paid, $payment) {
            if ($payment->getStatus() == PaymentStatuses::STATUS_COMPLETED) {
                $paid += $payment->getAmount();
            }

            return $paid;
        }, 0.0);

        return $paid;
    }

    /**
     * @param Payment|int $payment
     * @return string
     */
    public static function getGatewayTitle($payment)
    {
        if (is_int($payment)) {
            $payment = MPHB()->getPaymentRepository()->findById($payment);
        }

        if (is_object($payment)) {
            $gatewayId = $payment->getGatewayId();
            $gatewayTitle = MPHB()->gatewayManager()->getGateway($gatewayId)->getTitle();
        } else {
            $gatewayTitle = '';
        }

        return $gatewayTitle;
    }

    /**
     * @since 2.0.0
     *
     * @param Payment $payment
     * @param Request $request
     */
    public static function saveRequestMetaData($payment, $request)
    {
        add_post_meta($payment->getId(), 'mphbrp_request_id', $request->getId(), true);
        add_post_meta($payment->getId(), 'mphbrp_request_type', $request->getType(), true);
    }

    /**
     * @param Payment $payment
     */
    public static function waitForTransition($payment)
    {
        update_post_meta($payment->getId(), '_listen_request_payment_transitions', true);
    }

    /**
     * @param Payment $payment
     */
    public static function stopListenTransitions($payment)
    {
        update_post_meta($payment->getId(), '_listen_request_payment_transitions', false);
    }

    /**
     * @param Payment $payment
     */
    public static function isWaitingForTransition($payment)
    {
        return (bool)get_post_meta($payment->getId(), '_listen_request_payment_transitions', true);
    }
}
