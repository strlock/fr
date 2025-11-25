<?php
/**
 * The Template for Payment Received Email.
 *
 * Email that is sent to Customer after the requested payment has been made.
 *
 * @version 1.0
 * @since 1.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<?php printf(esc_html__('Dear %1$s %2$s,', 'mphb-request-payment'), '%customer_first_name%', '%customer_last_name%'); ?><br /><br />
<?php esc_html_e("We're writing to confirm that we've received your payment.", 'mphb-request-payment'); ?>

<h4><?php esc_html_e('Details of payment', 'mphb-request-payment'); ?></h4>
<?php printf(esc_html__('Payment ID: #%s', 'mphb-request-payment'), '%payment_id%'); ?><br />
<?php printf(esc_html__('Amount: %s', 'mphb-request-payment'), '%payment_amount%'); ?><br />
<?php printf(esc_html__('Method: %s', 'mphb-request-payment'), '%payment_method%'); ?><br />

<h4><?php esc_html_e('Details of booking', 'mphb-request-payment'); ?></h4>
<?php printf(esc_html__('Booking ID: #%s', 'mphb-request-payment'), '%booking_id%'); ?><br />
<?php printf(esc_html__('Total Price: %s', 'mphb-request-payment'), '%booking_total_price%'); ?><br />
<?php printf(esc_html__('Balance Due: %s', 'mphb-request-payment'), '%booking_balance_due%'); ?><br /><br />

<a href="%view_booking_link%"><?php esc_html_e('View Booking', 'mphb-request-payment'); ?></a><br />
<br />
<?php esc_html_e('Thank you!', 'mphb-request-payment'); ?>
