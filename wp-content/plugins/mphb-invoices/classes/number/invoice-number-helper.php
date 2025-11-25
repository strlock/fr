<?php

namespace MPHB\Addons\Invoice\Number;

use MPHB\Addons\Invoice\Settings;
use MPHB\Entities\Booking;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @since 1.4.0
 */
class InvoiceNumberHelper
{
    const INVOICE_ID_META = '_mphb_invoice_id';

    /**
     * See includes/actions-and-filters.php.
     */
    public static function addInvoiceIdToNewBooking(Booking $booking): void
    {
        $invoiceId = static::getNextBookingInvoiceId();

        add_post_meta($booking->getId(), static::INVOICE_ID_META, $invoiceId, true);
    }

    /**
     * @since 1.4.1
     */
    public static function fixInvoiceIdForBooking(int $bookingId): void
    {
        $invoiceId = static::getNextBookingInvoiceId();

        update_post_meta($bookingId, static::INVOICE_ID_META, $invoiceId);
    }

    public static function getBookingInvoiceId(int $bookingId): int
    {
        $invoiceNumber = get_post_meta($bookingId, static::INVOICE_ID_META, true);

        if ($invoiceNumber !== '') {
            $invoiceNumber = absint($invoiceNumber);
        } else {
            $invoiceNumber = $bookingId;
        }

        return $invoiceNumber;
    }

    public static function getBookingInvoiceIdPadded(int $bookingId): string
    {
        $invoiceNumber = static::getBookingInvoiceId($bookingId);
        $minLength = Settings::getInvoiceNumberMinLength();

        return str_pad((string)$invoiceNumber, $minLength, '0', STR_PAD_LEFT);
    }

    /**
     * @global \wpdb $wpdb
     */
    public static function getNextBookingInvoiceId(): int
    {
        global $wpdb;

        $startNumber = absint(Settings::getInvoiceStartNumber());

        $lastNumber = $wpdb->get_var(
            "SELECT MAX(CAST(`meta_value` AS UNSIGNED))"
            . " FROM `{$wpdb->postmeta}`"
            . " WHERE `meta_key` = '" . static::INVOICE_ID_META . "'"
        );
        $lastNumber = !is_null($lastNumber) ? absint($lastNumber) : 0;

        $nextNumber = max($startNumber, $lastNumber + 1);

        return $nextNumber;
    }
}
