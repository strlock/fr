<?php

namespace MPHB\Addons\Invoice\PDF;

use Dompdf\Dompdf;
use Dompdf\Options;
use MPHB\Addons\Invoice\Number\InvoiceNumberTagsProcessor;
use MPHB\Addons\Invoice\Settings;
use MPHB\Entities\Booking;
use MPHB\PostTypes\PaymentCPT\Statuses as PaymentStatuses;
use MPHB\Utils\DateUtils;
use MPHB\Views\BookingView;

class PDFHelper {

	const NO_VALUE_PLACEHOLDER = '';

    // these currencies have zero width in generated pdf so we need to set it explicitly
    private $not_supported_currencies = array(
		'AED' => array( // United Arab Emirates dirham
            'symbol' => '&#x62f;.&#x625;',
            'html' => '<span class="mphb-special-symbol" style="width: 1.1em; margin-bottom: -.1em;">&#x62f;.&#x625;</span>'
        ),
		'AFN' => array( // Afghan afghani
            'symbol' => '&#x60b;',
            'html' => '<span class="mphb-special-symbol" style="font-family: \'Currencies\'; width: .5em; margin-bottom: -.1em;">&#x60b;</span>'
        ),
		'BDT' => array( // Bangladeshi taka
            'symbol' => '&#2547;&nbsp;',
            'html' => '<span class="mphb-special-symbol" style="font-family: \'Currencies\'; margin-bottom: -.28em;">&#2547;&nbsp;</span>'
        ),
		'BHD' => array( // Bahraini dinar
            'symbol' => '.&#x62f;.&#x628;',
            'html' => '<span class="mphb-special-symbol">.&#x62f;.&#x628;</span>'
        ),
		'BTC' => array( // Bitcoin
            'symbol' => '&#3647;',
            'html' => '<span class="mphb-special-symbol">&#3647;</span>'
        ),
		'CRC' => array( // Costa Rican col&oacute;n
            'symbol' => '&#x20a1;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.25em;">&#x20a1;</span>'
        ),
		'DZD' => array( // Algerian dinar
            'symbol' => '&#x62f;.&#x62c;',
            'html' => '<span class="mphb-special-symbol">&#x62f;.&#x62c;</span>'
        ),
		'GEL' => array( // Georgian lari
            'symbol' => '&#x20be;',
            'html' => '<span class="mphb-special-symbol" style="font-family: \'Currencies\'; margin-bottom: -.25em;">&#x20be;</span>'
        ),
		'GHS' => array( // Ghana cedi
            'symbol' => '&#x20b5;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.2em;">&#x20b5;</span>'
        ),
		'ILS' => array( // Israeli new shekel
            'symbol' => '&#8362;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.23em;">&#8362;</span>'
        ),
		'INR' => array( // Indian rupee
            'symbol' => '&#8377;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.23em;">&#8377;</span>'
        ),
		'IQD' => array( // Iraqi dinar
            'symbol' => '&#x639;.&#x62f;',
            'html' => '<span class="mphb-special-symbol">&#x639;.&#x62f;</span>'
        ),
		'IRR' => array( // Iranian rial
            'symbol' => '&#xfdfc;',
            'html' => '<span class="mphb-special-symbol" style="font-family: \'Currencies\'; width: 1.2em; margin-bottom: -.23em;">&#xfdfc;</span>'
        ),
		'IRT' => array( // Iranian toman
            'symbol' => '&#x62A;&#x648;&#x645;&#x627;&#x646;',
            'html' => '<span class="mphb-special-symbol"  style="margin-bottom: -.15em;">&#x62A;&#x648;&#x645;&#x627;&#x646;</span>'
        ),
		'JOD' => array( // Jordanian dinar
            'symbol' => '&#x62f;.&#x627;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.25em;">&#x62f;.&#x627;</span>'
        ),
		'KHR' => array( // Cambodian riel
            'symbol' => '&#x17db;',
            'html' => '<span class="mphb-special-symbol" style="font-family: \'Currencies\'; font-size: 1.6em; width: .4em; margin-bottom: -.3em;">&#x17db;</span>'
        ),
		'KPW' => array( // North Korean won
            'symbol' => '&#x20a9;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.21em;">&#x20a9;</span>'
        ),
		'KRW' => array( // South Korean won
            'symbol' => '&#8361;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.21em;">&#8361;</span>'
        ),
		'KWD' => array( // Kuwaiti dinar
            'symbol' => '&#x62f;.&#x643;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.21em;">&#x62f;.&#x643;</span>'
        ),
		'LAK' => array( // Lao kip
            'symbol' => '&#8365;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.25em;">&#8365;</span>'
        ),
		'LBP' => array( // Lebanese pound
            'symbol' => '&#x644;.&#x644;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.18em;">&#x644;.&#x644;</span>'
        ),
		'LKR' => array( // Sri Lankan rupee
            'symbol' => '&#xdbb;&#xdd4;',
			// original symbol is not available in pdf library fonts and in Currencies so we use alternative symbol Rs
            'html' => '<span class="mphb-special-symbol">&#x20a8;</span>'
        ),
		'LYD' => array( // Libyan dinar
            'symbol' => '&#x644;.&#x62f;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.18em;">&#x644;.&#x62f;</span>'
        ),
		'MAD' => array( // Moroccan dirham
            'symbol' => '&#x62f;.&#x645;.',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.18em;">&#x62f;.&#x645;.</span>'
        ),
		'MNT' => array( // Mongolian t&ouml;gr&ouml;g
            'symbol' => '&#x20ae;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.25em;">&#x20ae;</span>'
        ),
		'MUR' => array( // Mauritian rupee
            'symbol' => '&#x20a8;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.22em;">&#x20a8;</span>'
        ),
		'MVR' => array( // Maldivian rufiyaa
            'symbol' => '.&#x783;',
			// original symbol is not available in pdf library fonts and in Currencies so we use alternative symbol Rf
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.25em;">Rf</span>'
        ),
		'NPR' => array( // Nepalese rupee
            'symbol' => '&#8360;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.22em;">&#8360;</span>'
        ),
		'OMR' => array( // Omani rial
            'symbol' => '&#x631;.&#x639;.',
            'html' => '<span class="mphb-special-symbol">&#x631;.&#x639;.</span>'
        ),
		'PHP' => array( // Philippine peso
            'symbol' => '&#8369;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.2em;">&#8369;</span>'
        ),
		'PKR' => array( // Pakistani rupee
            'symbol' => '&#8360;',
            'html' => '<span class="mphb-special-symbol">&#8360;</span>'
        ),
		'PYG' => array( // Paraguayan guaran&iacute;
            'symbol' => '&#8370;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.22em;">&#8370;</span>'
        ),
		'QAR' => array( // Qatari riyal
            'symbol' => '&#x631;.&#x642;',
            'html' => '<span class="mphb-special-symbol">&#x631;.&#x642;</span>'
        ),
		'RUB' => array( // Russian ruble
            'symbol' => '&#8381;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.23em;">&#8381;</span>'
        ),
		'SAR' => array( // Saudi riyal
            'symbol' => '&#x631;.&#x633;',
            'html' => '<span class="mphb-special-symbol">&#x631;.&#x633;</span>'
        ),
		'SCR' => array( // Seychellois rupee
            'symbol' => '&#x20a8;',
            'html' => '<span class="mphb-special-symbol">&#x20a8;</span>'
        ),
		'SDG' => array( // Sudanese pound
            'symbol' => '&#x62c;.&#x633;.',
            'html' => '<span class="mphb-special-symbol">&#x62c;.&#x633;.</span>'
        ),
		'SYP' => array( // Syrian pound
            'symbol' => '&#x644;.&#x633;',
            'html' => '<span class="mphb-special-symbol">&#x644;.&#x633;</span>'
        ),
		'THB' => array( // Thai baht
            'symbol' => '&#3647;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.2em;">&#3647;</span>'
        ),
		'TND' => array( // Tunisian dinar
            'symbol' => '&#x62f;.&#x62a;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.15em;">&#x62f;.&#x62a;</span>'
        ),
		'TRY' => array( // Turkish lira
            'symbol' => '&#8378;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.2em;">&#8378;</span>'
        ),
		'UAH' => array( // Ukrainian hryvnia
            'symbol' => '&#8372;',
            'html' => '<span class="mphb-special-symbol" style="margin-bottom: -.21em;">&#8372;</span>'
        ),
		'YER' => array( // Yemeni rial
            'symbol' => '&#xfdfc;',
            'html' => '<span class="mphb-special-symbol" style="width: .8em;">&#xfdfc;</span>'
        )
	);

	public function printPdf( $id, $return_attachment = false ) {

		$variables = $this->getRenderVariables( $id );

		/*
		 * To override template in a theme
		 * {theme}\hotel-booking\invoice_template.html
		 */
		$template_file = locate_template( MPHB()->getTemplatePath() . 'invoice_template.html' );
		if ( ! $template_file ) {
			$template_file = MPHB_INVOICE_PLUGIN_DIR . 'templates/invoice_template.html';
		}
		$rendered_template = $this->renderTemplate( $template_file, $variables );
		$filename          = 'invoice-' . $id . '-' . date( get_option( 'date_format' ) ) . '-' . date( get_option( 'time_format' ) ) . '.pdf';
		$filename          = str_replace( ':', '-', $filename );
		$filename          = preg_replace( '/[^a-z0-9\_\-\.]/i', '', $filename );
		$options           = new Options();
		$options->set( 'isRemoteEnabled', true );
		$dompdf = new Dompdf( $options );
		$dompdf->loadHtml( $rendered_template );
		$dompdf->setPaper( 'A4', 'portrait' );
		$dompdf->render();

		$canvas          = $dompdf->getCanvas();
		$footer          = $canvas->open_object();
		$page_numeration = sprintf( __( 'Page %1$s of %2$s', 'mphb-invoices' ), '{PAGE_NUM}', '{PAGE_COUNT}' );
		$canvas->page_text( 35, 810, $page_numeration, 'sans-serif', 7 );
		$canvas->close_object();
		$canvas->add_object( $footer, 'all' );

		if ( $return_attachment ) {
			$dir = mphb_uploads_dir();
			@file_put_contents( $dir . $filename, $dompdf->output() );
			return $dir . $filename;
		}

		$dompdf->stream( $filename, array( 'Attachment' => 0 ) );
		die();
	}

	public function addInvoicePdfAttachment( $attachments, $booking ) {

		$invoice_attachment = $this->printPdf( $booking->getId(), true );
		if ( $attachments == null || $attachments == '' ) {
			$attachments = array();
		}
		$attachments [] = $invoice_attachment;
		return $attachments;
	}

	public function getRenderVariables( $booking_id ) {

		$booking       = MPHB()->getBookingRepository()->findById( $booking_id );
		$logo_path     = '';
		$logo_image_id = get_option( 'mphb_invoice_company_logo', '' );
		$logo_base64   = '';

		if ( $logo_image_id != '' ) {
			$logo_image_url = wp_get_attachment_url( $logo_image_id );
			$uploads        = wp_upload_dir();
			$logo_path      = str_replace( $uploads['baseurl'], $uploads['basedir'], $logo_image_url );
			$img_type       = pathinfo( $logo_path, PATHINFO_EXTENSION );
			$img_data       = file_get_contents( $logo_path );
			$logo_base64    = 'data:image/' . $img_type . ';base64,' . base64_encode( $img_data );
		}

		$render_variables = array(
			'CLASS_HTML'                 => get_option( 'mphb_invoice_choose_font', 'open_sans' ),
			'OPTIONS_INVOICE_TITLE'      => get_option( 'mphb_invoice_title', '' ),
			'OPTIONS_HOTEL_TITLE'        => get_option( 'mphb_invoice_company_name', '' ),
			// 'OPTIONS_LOGO_IMAGE' => $logo_path,
			'OPTIONS_LOGO_IMAGE'         => $logo_base64,
			'OPTIONS_HOTEL_INFORMATION'  => str_replace( "\r\n", '<br>', get_option( 'mphb_invoice_company_information', '' ) ),
			'OPTIONS_BOTTOM_INFORMATION' => str_replace( "\r\n", '<br>', get_option( 'mphb_invoice_bottom_information', '' ) ),
			'INVOICE_NUMBER'             => $this->getInvoiceNumberForBooking($booking_id),
			'BOOKING_ID'                 => $booking->getId(),
			'BOOKING_DATE'               => get_the_date( get_option( 'date_format' ), $booking->getId() ),
			'BOOKING_CHECK_IN'           => \MPHB\Utils\DateUtils::formatDateWPFront( $booking->getCheckInDate() ),
			'BOOKING_CHECK_OUT'          => \MPHB\Utils\DateUtils::formatDateWPFront( $booking->getCheckOutDate() ),
			'BOOKING_CURRENT_DATE'       => \MPHB\Utils\DateUtils::formatDateWPFront( new \DateTime( 'now' ) ),
			'CUSTOMER_INFORMATION'       => $this->renderCustomerInformation( $booking ),
			'BOOKING_DETAILS'            => $this->renderPriceBreakdown( $booking ),
			'PAYMENT_DETAILS'            => $this->generatePaymentTable( $booking ),
			'CPT_PAYMENT_DETAILS'        => __( 'Payment Details', 'mphb-invoices' ),
			'CPT_BOOKING_DETAILS'        => __( 'Booking Details', 'mphb-invoices' ),
			'CPT_BOOKING'                => __( 'Booking', 'mphb-invoices' ),
			'CPT_DATE'                   => __( 'Date', 'mphb-invoices' ),
			'CPT_BOOKING_CHECK_IN_DATE'  => __( 'Check In', 'mphb-invoices' ),
			'CPT_BOOKING_CHECK_OUT_DATE' => __( 'Check Out', 'mphb-invoices' ),
			'CPT_INVOICE_NUMBER'         => __( 'Invoice Number', 'mphb-invoices' ),
			'CPT_INVOICE_DATE'           => __( 'Invoice Date', 'mphb-invoices' ),
		);

		return $render_variables;

	}

	/**
	 * @since 1.4.0
	 *
	 * @param int $booking_id
	 * @return string
	 */
	private function getInvoiceNumberForBooking($booking_id)
	{
		$tagsProcessor = new InvoiceNumberTagsProcessor();
		$tagsProcessor->setBookingId($booking_id);

		return $tagsProcessor->replaceTags(Settings::getInvoiceNumberMask());
	}

	public function renderTemplate( $template_file, $variables ) {

		$template = '';

		if ( ! empty( $template_file ) ) {

			$template = file_get_contents( $template_file );
		}

		// Remove an empty invoice number <tr>
		if ( empty( $variables[ 'INVOICE_NUMBER' ] ) ) {
			$template = preg_replace( '/<tr class="mphb-invoice-number">.*?<\\/tr>/s', '', $template );
		}

		foreach ( $variables as $key => $var ) {
			$template = str_replace( '{%' . $key . '%}', $var, $template );
		}
		
		foreach ( $this->not_supported_currencies as $currency_data ) {
			$pattern = '/<span(.*)class=(.*)mphb-price(.*)' . $currency_data[ 'symbol' ] . '(.*)<\/span>/i';
			$replace  = '<span${1}class=${2}mphb-price${3}' . $currency_data[ 'html' ] . '${4}</span>';
			$template = preg_replace( $pattern, $replace, $template );
		}

		return $template;
	}

	public function renderCustomerInformation( $booking ) {

		$customer = $booking->getCustomer();

		$html  = ( $customer->getName() !== '' ) ? '<strong>' . $customer->getName() . '</strong><br/>' : '';
		$html .= ( $customer->getEmail() !== '' ) ? $customer->getEmail() . '<br/>' : '';
		$html .= ( $customer->getPhone() !== '' ) ? $customer->getPhone() . '<br/>' : '';

		$html_arr = array(
			$customer->getZip(),
			$customer->getCountry(),
			$customer->getState(),
			$customer->getCity(),
			$customer->getAddress1(),
		);

		// compatibility with US adress standarts
		$html_arr = array_reverse( $html_arr );

		$html .= implode( ', ', array_filter( $html_arr ) );
		return $html;
	}

	public function generatePaymentTable( $booking ) {

			$payments = MPHB()->getPaymentRepository()->findAll( array( 'booking_id' => $booking->getId() ) );

			$totalPaid = 0.0;
			ob_start();
		?>
			<table class="mphb-payments-table" style="width: 100%">
				<thead>
				<tr>
					<th><?php esc_html_e( 'Payment ID', 'motopress-hotel-booking' ); ?></th>
					<th><?php esc_html_e( 'Date', 'motopress-hotel-booking' ); ?></th>
					<th><?php esc_html_e( 'Payment Method', 'motopress-hotel-booking' ); ?></th>
					<th><?php esc_html_e( 'Status', 'motopress-hotel-booking' ); ?></th>
					<th><?php esc_html_e( 'Amount', 'motopress-hotel-booking' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php if ( empty( $payments ) ) { ?>
					<tr>
						<td>&#8212;</td>
						<td>&#8212;</td>
						<td>&#8212;</td>
						<td>&#8212;</td>
						<td>&#8212;</td>
					</tr>
				<?php } else { ?>
					<?php
					foreach ( $payments as $payment ) {
						if ( $payment->getStatus() == PaymentStatuses::STATUS_COMPLETED ) {
							$totalPaid += $payment->getAmount();
						}
						$gateway      = MPHB()->gatewayManager()->getGateway( $payment->getGatewayId() );
						$gatewayTitle = ! is_null( $gateway ) ? $gateway->getAdminTitle() : self::NO_VALUE_PLACEHOLDER;

						printf( '<tr class="%s">', esc_attr( 'mphb-payment mphb-payment-status-' . $payment->getStatus() ) );
						echo '<td>', esc_html( $payment->getId() ) , '</td>';
						echo '<td>', esc_html( DateUtils::formatDateWPFront( $payment->getDate() ) ), '</td>';
						echo '<td>', esc_html( $gatewayTitle ), '</td>';
						echo '<td>', esc_html( mphb_get_status_label( $payment->getStatus() ) ), '</td>';
						echo '<td>', wp_kses_post( mphb_format_price( $payment->getAmount() ) ), '</td>';
						echo '</tr>';
					}
					?>
				<?php } ?>
				</tbody>
				<tfoot>
				<tr>
					<th class="mphb-total-label" colspan="4"><?php esc_html_e( 'Total Paid', 'motopress-hotel-booking' ); ?></th>
					<th><?php echo wp_kses_post( mphb_format_price( $totalPaid ) ); ?></th>
				</tr>
				<tr>
					<th class="mphb-to-pay-label" colspan="4"><?php esc_html_e( 'To Pay', 'motopress-hotel-booking' ); ?></th>
					<th>
						<?php
						$needToPay = $booking->getTotalPrice() - $totalPaid;
						echo wp_kses_post( mphb_format_price( $needToPay ) );
						?>
					</th>
				</tr>
				</tfoot>
			</table>
		<?php

		return ob_get_clean();
	}

	/**
	 * @since 1.4.0
	 *
	 * @param Booking $booking
	 */
	public function renderPriceBreakdown( $booking ) {
		$priceBreakdown = $booking->getLastPriceBreakdown();

		if ( empty( $priceBreakdown ) ) {
			MPHB()->reservationRequest()->setupParameter( 'pricing_strategy', 'default' );
			$priceBreakdown = $booking->getPriceBreakdown();
			MPHB()->reservationRequest()->resetDefaults( array( 'pricing_strategy' ) );
		}

		return BookingView::generatePriceBreakdownArray(
			$priceBreakdown,
			array(
				'title_expandable' => false,
				'coupon_removable' => false,
				'force_unfold'     => true,
			)
		);
	}
}
