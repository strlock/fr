<?php

namespace MPHB\Addons\MPHB_Multi_Currency;

if ( !defined( 'ABSPATH' ) ) exit;

use MPHB\Admin\Tabs\SettingsSubTab;

class Plugin_Settings_Handler {

    public function __construct() {

        if ( !is_admin() ) return;

        add_action( 'mphb_generate_extension_settings', function( \MPHB\Admin\Tabs\SettingsTab $mphb_settings_tab ) {

            $this->generate_settings_subtab( $mphb_settings_tab );
        }, 10, 1 );
    }

    private function generate_settings_subtab( \MPHB\Admin\Tabs\SettingsTab $settings_tab ):void {

        $settings_subtab = new SettingsSubTab( 'multi_currency', esc_html__( 'Multi-Currency', 'mphb-multi-currency'),
            $settings_tab->getPageName(), $settings_tab->getName() );

        $settings_subtab->addGroup( new Main_Settings_Group( 'mphbmc_main_settings', '', $settings_subtab->getOptionGroupName() ) );

        if ( MPHB_Multi_Currency::is_edd_license_enabled() ) {

            $license_group = new License_Settings_Group( 'mphbmc_license',
                esc_html__( 'License', 'mphb-multi-currency'), $settings_subtab->getOptionGroupName());

            $settings_subtab->addGroup( $license_group );
        }

        $settings_tab->addSubTab( $settings_subtab );
    }
}
