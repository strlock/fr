<?php

namespace MPHB\Addons\RequestPayment\Emails\Customer;

/**
 * @since 2.0.0
 */
class RequestCustomPaymentEmail extends BaseCustomerEmail
{
    protected function initLabel()
    {
        $this->label = esc_html__('Payment Request Email', 'mphb-request-payment');
    }

    protected function initDescription()
    {
        $this->description = esc_html__('Email to customer regarding fixed or percentage payment request.', 'mphb-request-payment');
    }

    public function getDefaultSubject()
    {
        return esc_html__('%site_title% - Payment request for booking #%booking_id%', 'mphb-request-payment');
    }

    public function getDefaultMessageHeaderText()
    {
        return esc_html__('Payment Request', 'mphb-request-payment');
    }
}
