<?php

namespace MPHB\Addons\RequestPayment\Emails\Customer;

use MPHB\Addons\RequestPayment\Entities\Request;
use MPHB\Emails\Booking\Customer\BaseEmail;
use MPHB\Entities\Booking;

abstract class BaseCustomerEmail extends BaseEmail
{
    /** @var int|null User ID or null. */
    protected $author = null;

    /**
     * @param array $atts
     * @param string $atts['id'] ID of email.
     * @param string $atts['label'] Email label.
     * @param string $atts['description'] Optional. Email description.
     * @param string $atts['default_subject'] Optional. Default subject of email.
     * @param string $atts['default_header_text'] Optional. Default text in header.
     * @param \MPHB\Emails\Templaters\EmailTemplater $templater
     */
    public function __construct($atts, $templater)
    {
        parent::__construct($atts, $templater);

        // Don't generate our settings in "Admin Emails" and "Customer Emails"
        // tabs, use extension tab instead
        remove_action('mphb_generate_settings_customer_emails', array($this, 'generateSettingsFields'), 10, 1);

        add_action('mphb_generate_settings_request_emails', array($this, 'generateSettingsFields'), 10, 1);
    }

    /**
     * @return int|null User ID or null.
     */
    protected function getAuthor()
    {
        return $this->author;
    }

    /**
     * @since 2.0.0 added attribute $atts['tag_atts'].
     *
     * @param Booking $booking
     * @param array $atts Optional.
     *     @param boolean $atts['is_auto']
     *     @param array $atts['tag_atts']
     *         @param Request $atts['tag_atts']['request']
     */
    public function trigger($booking, $atts = array())
    {
        // Set up "Auto" author
        $resetAuthor = $this->author;

        if (isset($atts['is_auto']) && $atts['is_auto']) {
            $this->author = 0;
        }

        // Pass tag atts to templater
        if (isset($atts['tag_atts'])) {
            $this->templater->setupTagAtts($atts['tag_atts']);
        } else {
            $this->templater->setupTagAtts(array()); // Remove previous atts
        }

        parent::trigger($booking, $atts);

        $this->author = $resetAuthor;
    }
}
