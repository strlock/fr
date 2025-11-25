<?php

namespace MPHB\Addons\RequestPayment\Utils;

use MPHB\Addons\RequestPayment\Emails\Admin\RequestPaidEmail;
use MPHB\Addons\RequestPayment\Emails\Customer\PaymentConfirmationEmail;
use MPHB\Addons\RequestPayment\Emails\Customer\RequestCustomPaymentEmail;
use MPHB\Addons\RequestPayment\Emails\Customer\RequestDepositPaymentEmail;
use MPHB\Addons\RequestPayment\Emails\Customer\RequestPaymentEmail;
use MPHB\Addons\RequestPayment\Emails\EmailTemplater;

class EmailUtils
{
    public static function addRequestPaymentEmail()
    {
        $requestPaymentTemplater = new EmailTemplater();
        $requestPaymentTemplater->setTagGroups(array('booking' => true, 'user_cancellation' => true));

        $requestPaymentEmail = new RequestPaymentEmail(array('id' => 'customer_request_payment'), $requestPaymentTemplater);

        MPHB()->emails()->addEmail($requestPaymentEmail);
    }

    /**
     * @since 2.0.0
     */
    public static function addRequestDepositPaymentEmail()
    {
        $requestDepositPaymentTemplater = new EmailTemplater();
        $requestDepositPaymentTemplater->setTagGroups(array('booking' => true, 'user_cancellation' => true));

        $requestDepositPaymentEmail = new RequestDepositPaymentEmail(array('id' => 'customer_request_deposit_payment'), $requestDepositPaymentTemplater);

        MPHB()->emails()->addEmail($requestDepositPaymentEmail);
    }

    /**
     * @since 2.0.0
     */
    public static function addRequestCustomPaymentEmail()
    {
        $requestCustomPaymentTemplater = new EmailTemplater();
        $requestCustomPaymentTemplater->setTagGroups(array('booking' => true, 'user_cancellation' => true));

        $requestCustomPaymentEmail = new RequestCustomPaymentEmail(array('id' => 'customer_request_custom_payment'), $requestCustomPaymentTemplater);

        MPHB()->emails()->addEmail($requestCustomPaymentEmail);
    }

    /**
     * @since 1.2.0
     */
    public static function addPaymentConfirmationEmail()
    {
        $paymentConfirmationTemplater = new EmailTemplater();
        $paymentConfirmationTemplater->setTagGroups(array('booking' => true, 'booking_details' => true, 'payment' => true));

        $paymentConfirmationEmail = new PaymentConfirmationEmail(array('id' => 'customer_payment_confirmation'), $paymentConfirmationTemplater);

        MPHB()->emails()->addEmail($paymentConfirmationEmail);
    }

    public static function addRequestPaidEmail()
    {
        $requestPaidTemplater = new EmailTemplater();
        $requestPaidTemplater->setTagGroups(array('booking' => true, 'payment' => true));

        $requestPaidEmail = new RequestPaidEmail(array('id' => 'admin_request_paid'), $requestPaidTemplater);

        MPHB()->emails()->addEmail($requestPaidEmail);
    }
}
