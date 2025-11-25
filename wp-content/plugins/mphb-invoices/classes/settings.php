<?php

namespace MPHB\Addons\Invoice;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @since 1.4.0
 *
 * @todo Add other parameters.
 */
class Settings
{
    const DEFAULT_INVOICE_NUMBER_MASK  = '';
    const DEFAULT_INVOICE_START_NUMBER = '1';

    public static function getInvoiceNumberMask(): string
    {
        return get_option('mphb_invoice_number_mask', static::DEFAULT_INVOICE_NUMBER_MASK);
    }

    /**
     * @return string The return type is string, with leading zeros.
     */
    public static function getInvoiceStartNumber(): string
    {
        return get_option('mphb_invoice_number_start', static::DEFAULT_INVOICE_START_NUMBER);
    }

    public static function getInvoiceNumberMinLength(): int
    {
        return strlen(static::getInvoiceStartNumber());
    }
}
