<?php

use MPHB\Addons\Invoice\Number\InvoiceNumberHelper;

if (!defined('ABSPATH')) {
    exit;
}

add_filter( 'post_row_actions', 'mphb_invoice_add_print_button', 10, 2 );

new MPHB\Addons\Invoice\MetaBoxes\InvoiceMetaBox('print_invoice', esc_html__('Invoice', 'mphb-invoices'),
    MPHB()->postTypes()->booking()->getPostType(), 'side');

// Print PDFs
add_action( 'admin_action_mphb-invoice', 'mphb_invoice_action_printpdf' );
add_action( 'init', 'mphb_invoice_analyze_request', 999 );

// Print invoice link
add_action( 'mphb_sc_booking_confirmation_bottom', 'mphb_invoice_add_secure_pdf_link');

// Add the invoice number (ID) to new bookings. Bookings created by an admin
// also trigger this action.
add_action( 'mphb_create_booking_by_user', [InvoiceNumberHelper::class, 'addInvoiceIdToNewBooking'], 10, 1);
