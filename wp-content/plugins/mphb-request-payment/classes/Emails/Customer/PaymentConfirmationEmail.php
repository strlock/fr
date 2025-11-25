<?php

namespace MPHB\Addons\RequestPayment\Emails\Customer;

/**
 * @since 1.2.0
 */
class PaymentConfirmationEmail extends BaseCustomerEmail
{
    protected function initLabel()
    {
        $this->label = esc_html__('Payment Confirmation Email', 'mphb-request-payment');
    }

    protected function initDescription()
    {
        $this->description = esc_html__('Email that is sent to customer after they have made the requested payment.', 'mphb-request-payment');
    }

    public function getDefaultSubject()
    {
        return esc_html__('%site_title% - Payment received for booking #%booking_id%', 'mphb-request-payment');
    }

    public function getDefaultMessageHeaderText()
    {
        return esc_html__('Payment Received', 'mphb-request-payment');
    }
}
