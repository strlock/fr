<?php

namespace MPHB\Addons\MPHB_Multi_Currency;

if ( !defined( 'ABSPATH' ) ) exit;


class License_Settings_Group extends \MPHB\Admin\Groups\SettingsGroup {

	public function render() {

		parent::render();

		$license_key = MPHB_Multi_Currency::get_license_key();

		if ( $license_key ) {

			$license_data = MPHB_Multi_Currency::get_license_data_from_remote_server();
		}

		?>

		<i><?php echo wp_kses( __( "The License Key is required in order to get automatic plugin updates and support. You can manage your License Key in your personal account. <a href='https://motopress.zendesk.com/hc/en-us/articles/202812996-How-to-use-your-personal-MotoPress-account' target='_blank'>Learn more</a>.", 'mphb-multi-currency' ), ['a' => ['href' => [], 'title' => [], 'target' => []]] ); ?></i>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row" valign="top">
						<?php esc_html_e( 'License Key', 'mphb-multi-currency' ); ?>
					</th>
					<td>
						<input id="mphbmc_edd_license_key" name="mphbmc_edd_license_key" type="password"
							   class="regular-text" value="<?php echo esc_attr( $license_key ); ?>" autocomplete="new-password" />

						<?php if ( $license_key ) { ?>
							<i style="display:block;"><?php echo wp_kses_post( str_repeat( "&#8226;", 20 ) . substr( $license_key, -7 ) ); ?></i>
						<?php } ?>
					</td>
				</tr>
				<?php if ( isset( $license_data, $license_data->license ) ) { ?>
					<tr valign="top">
						<th scope="row" valign="top">
							<?php esc_html_e( 'Status', 'mphb-multi-currency' ); ?>
						</th>
						<td>
							<?php
							switch ( $license_data->license ) {

								case 'inactive':
								case 'site_inactive':

									esc_html_e( 'Inactive', 'mphb-multi-currency' );
									break;

								case 'valid':

									if ( 'lifetime' !== $license_data->expires ) {

										$date	 = ($license_data->expires) ? new \DateTime( $license_data->expires ) : false;
										$expires = ($date) ? ' ' . $date->format( 'd.m.Y' ) : '';

										echo esc_html( __( 'Valid until', 'mphb-multi-currency' ) . $expires );

									} else {

										esc_html_e( 'Valid (Lifetime)', 'mphb-multi-currency' );
									}
									break;

								case 'disabled':

									esc_html_e( 'Disabled', 'mphb-multi-currency' );
									break;

								case 'expired':

									esc_html_e( 'Expired', 'mphb-multi-currency' );
									break;

								case 'invalid':

									esc_html_e( 'Invalid', 'mphb-multi-currency' );
									break;

								case 'item_name_mismatch':

									echo wp_kses( __( "Your License Key does not match the installed plugin. <a href='https://motopress.zendesk.com/hc/en-us/articles/202957243-What-to-do-if-the-license-key-doesn-t-correspond-with-the-plugin-license' target='_blank'>How to fix this.</a>", 'mphb-multi-currency' ), ['a' => ['href' => [], 'title' => [], 'target' => []]] );
									break;

								case 'invalid_item_id':

									esc_html_e( 'Product ID is not valid', 'mphb-multi-currency' );
									break;
							}
							?>
						</td>
					</tr>
					
                    <?php 

                    if ( in_array( $license_data->license, array( 'inactive', 'site_inactive', 'valid', 'expired' ) ) ) { 
                    ?>

						<tr valign="top">
							<th scope="row" valign="top">
								<?php esc_html_e( 'Action', 'mphb-multi-currency' ); ?>
							</th>
							<td>
								<?php
								if ( $license_data->license === 'inactive' || $license_data->license === 'site_inactive' ) {

									wp_nonce_field( 'mphbmc_edd_nonce', 'mphbmc_edd_nonce' );
                                ?>

									<input type="submit" class="button-secondary" name="edd_license_activate"
										   value="<?php esc_attr_e( 'Activate License', 'mphb-multi-currency' ); ?>"/>

								<?php 
                                } elseif ( $license_data->license === 'valid' ) { 
                                    
                                    wp_nonce_field( 'mphbmc_edd_nonce', 'mphbmc_edd_nonce' ); 
                                ?>

									<input type="submit" class="button-secondary" name="edd_license_deactivate"
										   value="<?php esc_attr_e( 'Deactivate License', 'mphb-multi-currency' ); ?>"/>

								<?php 
                                } elseif ( $license_data->license === 'expired' ) {
                                ?>

									<a href="<?php echo esc_url( MPHB_Multi_Currency::get_plugin_source_server_url() ); ?>"
									   class="button-secondary"
									   target="_blank">
										   <?php esc_html_e( 'Renew License', 'mphb-multi-currency' ); ?>
									</a>

                                <?php
								}
								?>
							</td>
						</tr>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>

		<?php
	}

	public function save() {

		parent::save();

		if ( empty($_POST) ) return;

		// $queryArgs = array(
		// 	'page' => $this->getPage(),
		// 	'tab' => $this->getName()
		// );

		if ( isset($_POST[ 'mphbmc_edd_license_key' ]) ) {

            MPHB_Multi_Currency::set_license_key( trim( sanitize_text_field( wp_unslash( $_POST[ 'mphbmc_edd_license_key' ] ) ) ) );
		}

		if ( isset($_POST[ 'edd_license_activate' ]) ) {

            // get out if we didn't click the Activate button
			if ( !check_admin_referer( 'mphbmc_edd_nonce', 'mphbmc_edd_nonce' ) ) return;

			$license_data = $this->activate_license();

			if ( false === $license_data ) return false;

			// if ( !$license_data->success && $license_data->error === 'item_name_mismatch' ) {
			// 	$queryArgs[ 'item-name-mismatch' ] = 'true';
			// }
		}

		if ( isset($_POST[ 'edd_license_deactivate' ]) ) {

			// get out if we didn't click the Activate button
			if ( !check_admin_referer( 'mphbmc_edd_nonce', 'mphbmc_edd_nonce' ) ) return; 

			$license_data = $this->deactivate_license();

			if ( false === $license_data ) return false;
		}
	}

	private function activate_license() {

		$api_params = array(
			'edd_action' => 'activate_license',
			'license'	 => MPHB_Multi_Currency::get_license_key(),
			'item_id'	 => MPHB_Multi_Currency::get_product_id(),
			'url'		 => home_url(),
		);

		$activate_url = add_query_arg( $api_params, MPHB_Multi_Currency::get_plugin_source_server_url() );

		$response = wp_remote_get( $activate_url, array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) ) return false;

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $licenseData->license will be either "active" or "inactive"
        MPHB_Multi_Currency::set_license_status( sanitize_text_field( $license_data->license ) );

		return $license_data;
	}

	private function deactivate_license() {

		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'	 => MPHB_Multi_Currency::get_license_key(),
			'item_id'	 => MPHB_Multi_Currency::get_product_id(),
			'url'		 => home_url(),
		);

		$deactivate_url = add_query_arg( $api_params, MPHB_Multi_Currency::get_plugin_source_server_url() );

		$response = wp_remote_get( $deactivate_url, array( 'timeout' => 15, 'sslverify' => false ) );

		if ( is_wp_error( $response ) ) return false;

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		// $license_data->license will be either "deactivated" or "failed"
		if ( 'deactivated' == $license_data->license ) {

            MPHB_Multi_Currency::set_license_status( '' );
		}

		return $license_data;
	}
}
