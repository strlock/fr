<?php

namespace MPHB\Addons\MPHB_Multi_Currency;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Currency_Switcher_Widget extends \WP_Widget {

	public function __construct() {

		parent::__construct(
			'mphbmc_select_currency',
			__( 'Hotel Booking Currency Switcher', 'mphb-multi-currency' ),
			array(
				'description' => __( "Allows site visitors to see accommodations' prices in the preferable currency.", 'mphb-multi-currency' ),
			)
		);
	}

	public function widget( $widget_args, $widget_settings ) {

		if ( ! MPHB_Multi_Currency::is_currency_exchange_rates_on() ) {
			return;
		}

		wp_enqueue_script(
			'mphbmc-currency-switcher-widget',
			MPHB_Multi_Currency::get_plugin_url() . 'js/currency-switcher-widget.min.js',
			array( 'jquery' ),
			MPHB_Multi_Currency::get_plugin_version(),
			true
		);

		wp_localize_script(
			'mphbmc-currency-switcher-widget',
			'MPHBMCWidgetData',
			array(
				'baseUrlPath' => get_site_url( null, '/', 'relative' ),
			)
		);

		$title = '';

		if ( ! empty( $widget_settings['title'] ) ) {

			$title = ( empty( $widget_args['before_title'] ) ? '' : $widget_args['before_title'] ) .
				$widget_settings['title'] .
				( empty( $widget_args['after_title'] ) ? '' : $widget_args['after_title'] );
		}

		$currency_exchange_rates = MPHB_Multi_Currency::get_currency_exchange_rates();
		$selected_currency_code  = Convert_Price_Handler::get_current_selected_currency_code();

		$select = '<select name="mphbmc_currency_switcher">';

		foreach ( $currency_exchange_rates as $currency_data ) {

			$select .= '<option value="' . esc_attr( $currency_data['mphbmc_currency_code'] ) . '"' .
				( $selected_currency_code == $currency_data['mphbmc_currency_code'] ? ' selected="selected"' : '' ) . '>' .
				esc_html( MPHB()->settings()->currency()->getBundle()->getLabel( $currency_data['mphbmc_currency_code'] ) ) . '</option>';
		}
		$select .= '</select>';

		echo ( empty( $widget_args['before_widget'] ) ? '' : $widget_args['before_widget'] ) .
			$title . $select .
			( empty( $widget_args['after_widget'] ) ? '' : $widget_args['after_widget'] );
	}

	public function form( $current_widget_settings ) {

		$current_widget_settings = wp_parse_args(
			$current_widget_settings,
			array(
				'title' => '',
			)
		);

		extract( $current_widget_settings );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

	public function update( $new_widget_settings, $old_widget_settings ) {

		$widget_settings = array();

		$widget_settings['title'] = ! empty( $new_widget_settings['title'] ) ? strip_tags( $new_widget_settings['title'] ) : '';

		return $widget_settings;
	}
}
