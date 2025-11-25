<?php

namespace MPHB\Addons\MPHB_Multi_Currency;

if ( !defined( 'ABSPATH' ) ) exit;

use MPHB\Admin\Fields\FieldFactory;


class Main_Settings_Group extends \MPHB\Admin\Groups\SettingsGroup {

    public function __construct( $name, $label, $page, $description = '' ) {

		parent::__construct( $name, $label, $page, $description );


        $menu_locations = get_nav_menu_locations();
        $menus = array();

        foreach( $menu_locations as $menu_location_name => $menu_id ) {

            if ( !empty($menu_id) && 0 < $menu_id ) {

                $menus[ $menu_id ] = wp_get_nav_menu_name( $menu_location_name );
            }
        }
        
        $menus = array_unique( $menus );

        if ( !empty($menus) ) {

            $mphbmc_menu_currency_switcher_settings = MPHB_Multi_Currency::get_menu_currency_switcher_settings();
        
            $menu_currency_switcher_settings_table = FieldFactory::create( 'mphbmc_menu_currency_switcher', array(
                'type'		  => 'complex',
                'label'		  => __( 'Menu currency switcher', 'mphb-multi-currency' ),
                'add_label'	  => __( 'Add menu currency switcher', 'mphb-multi-currency' ),
                'default'	  => array(),
                'fields'	  => array(
                    FieldFactory::create( 'mphbmc_currency_switcher_menu', array(
                        'type'		 => 'select',
                        'label'		 => __( 'Menu', 'mphb-multi-currency' ),
                        'list'		 => $menus,
                        'default'	 => ''
                    ) ),
                    FieldFactory::create( 'mphbmc_currency_switcher_position_in_menu', array(
                        'type'		 => 'radio',
                        'label'		 => __( 'Location', 'mphb-multi-currency' ),
                        'list'		 => array(
                            'first'	 => __( 'First menu item', 'mphb-multi-currency' ),
                            'last'	 => __( 'Last menu item', 'mphb-multi-currency' ),
                        ),
                        'default'	 => 'last'
                    ) ),
                    FieldFactory::create( 'mphbmc_menu_currency_switcher_type', array(
                        'type'		 => 'radio',
                        'label'		 => __( 'Type', 'mphb-multi-currency' ),
                        'list'		 => array(
                            'dropdown' => __( 'Dropdown', 'mphb-multi-currency' ),
                            'list'	 => __( 'Separate menu items', 'mphb-multi-currency' ),
                        ),
                        'default'	 => 'dropdown'
                    ) ),
                )
            ), $mphbmc_menu_currency_switcher_settings );

            $this->addField( $menu_currency_switcher_settings_table );
        }


        $mphbmc_exchange_rates = MPHB_Multi_Currency::get_currency_exchange_rates();
        
        $currency_exchange_rates_table = FieldFactory::create( 'mphbmc_exchange_rates', array(
            'type'		  => 'complex',
			'label'		  => __( 'Currency exchange rates', 'mphb-multi-currency' ),
			'add_label'	  => __( 'Add currency rate', 'mphb-multi-currency' ),
			'default'	  => array(
                array(
                    'mphbmc_currency_code' => MPHB()->settings()->currency()->getCurrencyCode(),
                    'mphbmc_currency_position' => MPHB()->settings()->currency()->getCurrencyPosition(),
                    'mphbmc_currency_rate' => 1,
                    'mphbmc_number_of_decimals' => 2
                )
            ),
			'fields'	  => array(
                FieldFactory::create( 'mphbmc_currency_code', array(
                    'type'		 => 'select',
                    'label'		 => __( 'Currency', 'motopress-hotel-booking' ),
                    'list'		 => MPHB()->settings()->currency()->getBundle()->getLabels(),
                    'default'	 => 'USD'
                ) ),
                FieldFactory::create( 'mphbmc_currency_position', array(
                    'type'		 => 'select',
                    'label'		 => __( 'Currency Position', 'motopress-hotel-booking' ),
                    'list'		 => MPHB()->settings()->currency()->getBundle()->getPositions(),
                    'default'	 => 'before'
                ) ),
                FieldFactory::create( 'mphbmc_currency_rate', array(
                    'type'		 => 'number',
                    'min'		 => 0.000001,
                    'step'		 => 0.000001,
                    'default'	 => 1,
                    'label'		 => __( 'Rate', 'mphb-multi-currency' ),
                    'classes'    => 'mphb-multi-currency_rate'
                ) ),
                FieldFactory::create( 'mphbmc_number_of_decimals', array(
                    'type'		 => 'number',
                    'min'		 => 0,
                    'step'		 => 1,
                    'default'	 => 2,
                    'label'		 => __( 'Number of decimals', 'mphb-multi-currency' )
                ) ),
            )
        ), $mphbmc_exchange_rates );

        $this->addField( $currency_exchange_rates_table );
	}

	public function save() {

		parent::save();

        $mphbmc_exchange_rates = get_option( 'mphbmc_exchange_rates', array() );

        // make sure we have right default currency in rates list
        $is_default_currency_rate_presented = false;
        $default_currency_code = MPHB()->settings()->currency()->getCurrencyCode();

        foreach( $mphbmc_exchange_rates as $index => $currency_rate_data ) {

            if ( $default_currency_code == $currency_rate_data[ 'mphbmc_currency_code' ] ) {

                $mphbmc_exchange_rates[ $index ][ 'mphbmc_currency_rate' ] = 1;
                $mphbmc_exchange_rates[ $index ][ 'mphbmc_currency_position' ] = MPHB()->settings()->currency()->getCurrencyPosition();
                $mphbmc_exchange_rates[ $index ][ 'mphbmc_number_of_decimals' ] = MPHB()->settings()->currency()->getPriceDecimalsCount();
                $is_default_currency_rate_presented = true;
                break;
            }
        }

        if ( !$is_default_currency_rate_presented ) {

            array_unshift( $mphbmc_exchange_rates, array(
                'mphbmc_currency_code' => MPHB()->settings()->currency()->getCurrencyCode(),
                'mphbmc_currency_position' => MPHB()->settings()->currency()->getCurrencyPosition(),
                'mphbmc_currency_rate' => 1,
                'mphbmc_number_of_decimals' => MPHB()->settings()->currency()->getPriceDecimalsCount()
            ) );
        }

        // remove duplicates in currency exchange rates
        $used_currency_codes = wp_list_pluck( $mphbmc_exchange_rates, 'mphbmc_currency_code' );
        $unique_used_currency_codes = array_unique( $used_currency_codes );

        $unique_exchange_rates = array();

        foreach( $mphbmc_exchange_rates as $index => $currency_rate_data ) {

            if ( isset( $unique_used_currency_codes[ $index ] ) ) {

                $unique_exchange_rates[ $index ] = $currency_rate_data;
            }
        }

        update_option( 'mphbmc_exchange_rates', $unique_exchange_rates );

        
        // remove duplicates of menu switchers
        $mphbmc_menu_currency_switcher_settings = get_option( 'mphbmc_menu_currency_switcher', array() );

        $used_menus = wp_list_pluck( $mphbmc_menu_currency_switcher_settings, 'mphbmc_currency_switcher_menu' );
        $unique_used_menus = array_unique( $used_menus );

        $unique_switcher_settings = array();

        foreach( $mphbmc_menu_currency_switcher_settings as $index => $settings ) {

            if ( isset( $unique_used_menus[ $index ] ) ) {

                $unique_switcher_settings[ $index ] = $settings;
            }
        }

        update_option( 'mphbmc_menu_currency_switcher', $unique_switcher_settings );
	}
}
