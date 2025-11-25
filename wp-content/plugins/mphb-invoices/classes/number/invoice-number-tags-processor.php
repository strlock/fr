<?php

namespace MPHB\Addons\Invoice\Number;

use MPHB\Utils\DateUtils;
use DateTime;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @since 1.4.0
 */
class InvoiceNumberTagsProcessor
{
    /**
     * @var array [tag_name => tag_description]
     */
    private $tags;

    private $bookingId = 0;

    /**
     * @var DateTime|null
     */
    private $cachedDateTime = null;

    public static function getTags(): array
    {
        $tags = [
            'BOOKING_ID' => esc_html__('Booking ID', 'mphb-invoices'),
            'INVOICE_ID' => esc_html__('Sequential Invoice ID', 'mphb-invoices'),
            'D'          => esc_html__('Day of the month without leading zeros', 'mphb-invoices'),
            'DD'         => esc_html__('Day of the month, 2 digits with leading zeros', 'mphb-invoices'),
            'M'          => esc_html__('Numeric representation of a month, without leading zeros', 'mphb-invoices'),
            'MM'         => esc_html__('Numeric representation of a month, with leading zeros', 'mphb-invoices'),
            'YY'         => esc_html__('A two digit representation of a year', 'mphb-invoices'),
            'YYYY'       => esc_html__('A full numeric representation of a year, at least 4 digits', 'mphb-invoices'),
            'H'          => esc_html__('24-hour format of an hour without leading zeros', 'mphb-invoices'),
            'HH'         => esc_html__('24-hour format of an hour with leading zeros', 'mphb-invoices'),
            'N'          => esc_html__('Minutes with leading zeros', 'mphb-invoices'),
            'S'          => esc_html__('Seconds with leading zeros', 'mphb-invoices'),
        ];

        /**
         * @since 1.4.0
         *
         * @param array $tags [tag_name => tag_description]
         */
        $tags = apply_filters('mphb_invoice_number_tags', $tags);

        return $tags;
    }

    private function setupTags(): void
    {
        if (empty($this->tags)) {
            $this->tags = static::getTags();
        }
    }

    public function setBookingId(int $bookingId): void
    {
        $this->bookingId = $bookingId;
    }

    public function replaceTags(string $content): string
    {
        $this->clearCache();
        $this->setupTags();

        $content = preg_replace_callback($this->getTagsRegexPattern(), [$this, 'replaceTag'], $content);

        return $content;
    }

    private function getTagsRegexPattern(): string
    {
        return '/%' . implode('%|%', array_keys($this->tags)) . '%/i'; // Case-insensitive
    }

    /**
     * @access private
     */
    public function replaceTag(array $match): string
    {
        $tag = strtoupper(str_replace('%', '', $match[0]));

        $replaceText = '';

        switch ($tag) {
            case 'BOOKING_ID':
                $replaceText = (string)$this->bookingId;
                break;

            case 'INVOICE_ID':
                $replaceText = InvoiceNumberHelper::getBookingInvoiceIdPadded($this->bookingId);
                break;

            case 'D':
                $replaceText = $this->getCurrentDateTime()->format('j');
                break;

            case 'DD':
                $replaceText = $this->getCurrentDateTime()->format('d');
                break;

            case 'M':
                $replaceText = $this->getCurrentDateTime()->format('n');
                break;

            case 'MM':
                $replaceText = $this->getCurrentDateTime()->format('m');
                break;

            case 'YY':
                $replaceText = $this->getCurrentDateTime()->format('y');
                break;

            case 'YYYY':
                $replaceText = $this->getCurrentDateTime()->format('Y');
                break;

            case 'H':
                $replaceText = $this->getCurrentDateTime()->format('G');
                break;

            case 'HH':
                $replaceText = $this->getCurrentDateTime()->format('H');
                break;

            case 'N':
                $replaceText = $this->getCurrentDateTime()->format('i');
                break;

            case 'S':
                $replaceText = $this->getCurrentDateTime()->format('s');
                break;
        }

        /**
         * @since 1.4.0
         *
         * @param string $replaceText
         * @param string $tag
         * @param int    $bookingId
         */
        $replaceText = apply_filters('mphb_invoice_number_replace_tag', $replaceText, $tag, $this->bookingId);

        return $replaceText;
    }

    private function getCurrentDateTime(): DateTime
    {
        if (is_null($this->cachedDateTime)) {
            if (method_exists('MPHB\Utils\DateUtils', 'getSiteTimeZone')) {
                $this->cachedDateTime = new DateTime('now', DateUtils::getSiteTimeZone());
            } else {
                // Update your core plugin
                $this->cachedDateTime = new DateTime('now');
            }
        }

        return $this->cachedDateTime;
    }

    private function clearCache(): void
    {
        if (!is_null($this->cachedDateTime)) {
            unset($this->cachedDateTime);
        }
    }

    public static function getTagsDescription(): string
    {
        $output = '';

        $output .= '<details style="max-width: 25em;">';
            $output .= '<summary>' . esc_html__('Available Tags', 'mphb-invoices') . '</summary>';

            $output .= '<div class="mphb-tags-wrapper mphb-email-tags-wrapper">';
                $output .= '<table class="striped mphb-tags mphb-email-tags">';
                    foreach (static::getTags() as $tagName => $tagDescription) {
                        $output .= '<tr>';
                            $output .= '<td>' . esc_html($tagDescription) . '</td>';
                            $output .= '<td><em>%' . esc_html($tagName) . '%</em></td>';
                        $output .= '</tr>';
                    }
                $output .= '</table>';
            $output .= '</div>';
        $output .= '</details>';

        return $output;
    }
}
