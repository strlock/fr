<?php

namespace MPHB\Addons\Invoice\Update\Patches;

use MPHB\Addons\Invoice\Number\InvoiceNumberHelper;
use MPHB\Libraries\WP_Background_Processing\WP_Async_Request;

/**
 * @since 1.4.1
 */
class PluginPatch_1_4_1 extends WP_Async_Request
{
    /**
     * @var string
     */
    protected $prefix = 'mphb_invoice';

    /**
     * @var string
     */
    protected $action = 'plugin_patch_1_4_1';

    /**
     * @global \wpdb $wpdb
     */
    protected function handle()
    {
        global $wpdb;

        $invoiceMetaKey = InvoiceNumberHelper::INVOICE_ID_META;

        $invoiceIdsCount = $wpdb->get_results(
            "SELECT `meta_value` AS `invoice_id`, COUNT(*) AS `count`"
                . " FROM `{$wpdb->postmeta}`"
                . " WHERE `meta_key` = '{$invoiceMetaKey}'"
                . " GROUP BY `meta_value`",
            ARRAY_A
        );

        $duplicateIds = array_filter(
            $invoiceIdsCount,
            function ($selectRow) {
                return $selectRow['count'] !== '1';
            }
        );

        foreach ($duplicateIds as $selectRow) {
            $bookingIds = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT `post_id` FROM `{$wpdb->postmeta}` WHERE `meta_key` = %s AND `meta_value` = %s;",
                    $invoiceMetaKey,
                    $selectRow['invoice_id']
                )
            );

            // Leave the first booking with its invoice ID
            array_shift($bookingIds);

            foreach ($bookingIds as $bookingId) {
                InvoiceNumberHelper::fixInvoiceIdForBooking((int)$bookingId);
            }
        }
    }
}
