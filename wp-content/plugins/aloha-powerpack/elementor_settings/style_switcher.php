<?php

use Elementor\Core\Kits\Documents\Tabs\Tab_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Aloha_Settings_Style_Switcher extends Tab_Base {

    var $switch_kit = false;

    public function get_id() {
        return 'aloha-settings-style-switcher';
    }

    public function get_title() {
        $prefix = aloha_get_elementor_tab_prefix();
        return esc_html__($prefix . ' - Style Switcher', ALOHA_DOMAIN);
    }
    public function get_help_url() {
        return ALOHA_WIDGETS_HELP_URL_PREFIX . $this->get_id();
    }
    public function get_group() {
        return 'settings';
    }

    public function get_icon() {
        return 'eicon-tools';
    }

    protected function register_tab_controls() {

        $this->start_controls_section(
                'section_aloha_style_switcher',
                [
                    'label' => $this->get_title(),
                    'tab' => $this->get_id(),
                ]
        );
        $kits = $this->get_kits();
        $this->add_control(
                'aloha-active-kit',
                [
                    'label' => esc_html__('Active Kit', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SELECT,
                    'default' => '',
                    'options' => $kits,
                    'description' => esc_html__('Your current settings will be backed up. Please let the browser reload when it asks to do so.', ALOHA_DOMAIN),
                ]
        );

        if (file_exists(aloha_get_backup_file())) {
            $this->add_control(
                    'aloha-restore-kit',
                    [
                        'type' => Controls_Manager::BUTTON,
                        'text' => esc_html__('Restore Settings', ALOHA_DOMAIN),
                        'button_type' => 'default',
                        'description' => esc_html__('Restores the settings to what you had before applying these styles.', ALOHA_DOMAIN),
                        'separator' => 'before',
                        'show_label' => false,
                        'event' => 'aloha_restore_backup_event',
                    ]
            );
        } else {
            $this->add_control(
                    'aloha-restore-message',
                    [
                        'type' => Controls_Manager::RAW_HTML,
                        'raw' => __('The button to restore settings will appear here after a style has been applied.'),
                        'separator' => 'before',
                    ]
            );
        }
        $this->end_controls_section();
    }

    private function get_kits() {
        //check if we made an import, if yes, then we must have a backup too
        $files = glob(ALOHA_KITS_PATH . '/*manifest.json');
        $current_kits = ['' => __('Default', ALOHA_DOMAIN)];
        foreach ($files as $file) {
            $json_content = file_get_contents($file);
            $file_data = json_decode($json_content);
            $style_file = $file_data->name;
            $style_name = $file_data->title;
            if (file_exists(ALOHA_KITS_PATH . '/' . $style_file . '.json')) {
                $current_kits[$style_file] = $style_name;
            }
        }

        return $current_kits;
    }

}
