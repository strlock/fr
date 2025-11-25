<?php

namespace MPHB\Addons\RequestPayment\Emails;

class EmailTemplater extends \MPHB\Emails\Templaters\EmailTemplater
{
    /** @var bool */
    protected $bookingGroupEnabled = false;

    /** @var \MPHB\Entities\Booking|null */
    private $booking = null;

    /** @var \MPHB\Entities\Payment|null */
    private $payment = null;

    /**
     * @since 2.0.0
     *
     * @var array
     */
    protected $tagAtts = array();

    public function setTagGroups($groups) {
        parent::setTagGroups($groups);

        $this->bookingGroupEnabled = (isset($groups['booking']) && $groups['booking']);
    }

    public function setupTags()
    {
        parent::setupTags();

        // Add new tags only if group "booking" was enabled
        if ($this->bookingGroupEnabled) {
            $this->tags = MPHBRP()->tags()->addPrivateTags($this->tags);
        }
    }

    public function replaceTag($regexMatch)
    {
        $tag = str_replace('%', '', $regexMatch[0]);

        // Replace own tags first (with additional atts)
        $replacement = MPHBRP()->tags()->replaceTag('', $tag, $this->booking, $this->tagAtts);

        // Replace other tags
        if (empty($replacement)) {
            $replacement = parent::replaceTag($regexMatch);
        }

        return $replacement;
    }

    /**
     * @param \MPHB\Entities\Booking $booking
     */
    public function setupBooking($booking)
    {
        $this->booking = $booking;

        // Setup booking in parent (private field)
        parent::setupBooking($booking);
    }

    /**
     * @param \MPHB\Entities\Payment $payment
     */
    public function setupPayment($payment)
    {
        $this->payment = $payment;

        // Setup payment in parent (private field)
        parent::setupPayment($payment);
    }

    /**
     * @since 2.0.0
     *
     * @param array $tagAtts
     */
    public function setupTagAtts($tagAtts)
    {
        $this->tagAtts = $tagAtts;
    }
}
