<?php

use Elementor\Core\Kits\Documents\Tabs\Tab_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Aloha_Settings_Misc extends Tab_Base {

    public function get_id() {
        return 'aloha-settings-misc';
    }

    public function get_title() {
        $prefix = aloha_get_elementor_tab_prefix();
        return esc_html__($prefix . ' - Misc.', ALOHA_DOMAIN);
    }

    public function get_group() {
        return 'settings';
    }

    public function get_icon() {
        return 'eicon-tools';
    }

    protected function register_tab_controls() {

        $this->start_controls_section(
                'section_aloha_misc',
                [
                    'label' => $this->get_title(),
                    'tab' => $this->get_id(),
                ]
        );
        
        $this->add_control(
                ALOHA_SETTING_BUTTON_STYLE_ID,
                [
                    'label' => esc_html__('Button Style', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SELECT,
                    'default' => get_theme_mod(ALOHA_SETTING_BUTTON_STYLE_ID, ALOHA_SETTING_BUTTON_STYLE_DEFAULT),
                    'options' => [
                        'sharp' => esc_attr__('Sharp', ALOHA_DOMAIN),
                        'square' => esc_attr__('Squared', ALOHA_DOMAIN),
                        'round' => esc_attr__('Rounded', ALOHA_DOMAIN),
                    ],
                ]
        );
        $preloader = get_theme_mod('themo_preloader', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_preloader',
                [
                    'label' => __('Preloader', ALOHA_DOMAIN),
                    'description' => esc_html__('Enables preloader site wide.', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $preloader,
                    'return_value' => 'on'
                ]
        );
        $themo_boxed_layout = get_theme_mod('themo_boxed_layout', false) ? 'on' : '';
        $this->add_control(
                'themo_boxed_layout',
                [
                    'label' => __('Boxed Layout', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_boxed_layout,
                    'return_value' => 'on'
                ]
        );

        $this->add_control(
                'th_boxed_bg_color',
                [
                    'label' => __('Background Color', ALOHA_DOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'alpha' => true,
                    'default' => get_theme_mod('th_boxed_bg_color', '#FFF'),
                    'selectors' => [
                        ':root' => '--aloha_body_background_color: {{VALUE}}',
                    ],
                    'condition' => [
                        'themo_boxed_layout' => 'on',
                    ],
                ]
        );

        $this->add_control(
                'th_boxed_bg_image',
                [
                    'label' => __('Background Image', ALOHA_DOMAIN),
                    'type' => Controls_Manager::MEDIA,
                    'default' => [
                        'url' => get_theme_mod('th_boxed_bg_image', ''),
                    ],
                    'condition' => [
                        'themo_boxed_layout' => 'on',
                    ],
                    'selectors' => [
                        'body' => 'background-image: {{VALUE}};',
                        'body' => 'background-attachment: fixed;',
                        'body' => 'background-size: cover;',
                    ],
                    'separator' => 'before',
                ]
        );
        $themo_retinajs = get_theme_mod('themo_retinajs', false) ? 'on' : '';
        $this->add_control(
                'themo_retinajs',
                [
                    'label' => esc_html__('High-resolution/Retina Image Support', ALOHA_DOMAIN),
                    'description' => esc_html__('Automatically serve up high-resolution images to devices that support them.', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_retinajs,
                    'return_value' => 'on',
                    'separator' => 'before',

                ]
        );
        $themo_retina_support = get_theme_mod('themo_retina_support', '') == 1 ? 'on' : '';
        $this->add_control(
                'themo_retina_support',
                [
                    'label' => esc_html__('High-resolution/Retina Image Generator', ALOHA_DOMAIN),
                    'description' => esc_html__('Automatically generate high-resolution/retina image sizes (@2x) when uploaded to your Media Library.', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_retina_support,
                    'return_value' => 'on'
                ]
        );

        $this->add_control(
                'themo_room_rewrite_slug',
                [
                    'label' => esc_html__('Room Custom Slug', ALOHA_DOMAIN),
                    'description' => esc_html__('Optionally change the permalink slug for the Room custom post type. e.g.: "rides" or "packages"', ALOHA_DOMAIN),
                    'type' => Controls_Manager::TEXT,
                    'default' => get_theme_mod('themo_room_rewrite_slug', ''),
                ]
        );

        $tribe_events_layout_show_header = get_theme_mod('tribe_events_layout_show_header', 1) == 1 ? 'on' : '';
        $this->add_control(
                'tribe_events_layout_show_header',
                [
                    'label' => esc_html__('Events Header', ALOHA_DOMAIN),
                    'description' => esc_html__('Show / Hide header for Events pages', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $tribe_events_layout_show_header,
                    'return_value' => 'on'
                ]
        );

        $this->add_control(
                'tribe_events_layout_header_float',
                [
                    'label' => esc_html__('Events Header Position', ALOHA_DOMAIN),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => __('Left', ALOHA_DOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'centered' => [
                            'title' => __('Centered', ALOHA_DOMAIN),
                            'icon' => 'fa fa-align-center',
                        ],
                        'right' => [
                            'title' => __('Right', ALOHA_DOMAIN),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'default' => get_theme_mod('tribe_events_layout_header_float', 'centered'),
                    'condition' => [
                        'tribe_events_layout_show_header' => 'on',
                    ],
                    'separator' => 'after',
                ]
        );
        $this->add_control(
                'tribe_events_layout_sidebar',
                [
                    'label' => esc_html__('Events Sidebar Position', ALOHA_DOMAIN),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => __('Left', ALOHA_DOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'full' => [
                            'title' => __('None', ALOHA_DOMAIN),
                            'icon' => '',
                        ],
                        'right' => [
                            'title' => __('Right', ALOHA_DOMAIN),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'default' => get_theme_mod('tribe_events_layout_sidebar', 'right'),
                ]
        );
        
        $this->add_control(
                'aloha_maps_key',
                [
                    'label' => __( 'Google Maps API Key', ALOHA_DOMAIN ),
		    'description' => __( '<a href="https://help.bellevuetheme.com/article/215-how-to-setup-a-google-api-key" target="_blank">Setup your Google Maps API Key</a>', ALOHA_DOMAIN ),
                    'type' => Controls_Manager::TEXTAREA,
                    'default' => '',
                    'separator' => 'before',

                ]
        );
        $this->end_controls_section();
    }

    public function on_save($data) {
        if (
                !isset($data['settings']['post_status']) ||
                Document::STATUS_PUBLISH !== $data['settings']['post_status'] ||
                // Should check for the current action to avoid infinite loop
                // when updating options like: "blogname" and "blogdescription".
                strpos(current_action(), 'update_option_') === 0
        ) {
            return;
        }

        $keysToAdd = [
            ALOHA_SETTING_BUTTON_STYLE_ID,
            'themo_preloader',
            'themo_boxed_layout',
            'th_boxed_bg_color',
            'th_boxed_bg_image',
            'themo_retinajs',
            'themo_retina_support',
            'themo_room_rewrite_slug',
            'tribe_events_layout_show_header',
            'tribe_events_layout_header_float',
            'tribe_events_layout_sidebar',
        ];
        foreach ($keysToAdd as $key) {
            if (array_key_exists($key, $data['settings'])) {
                $value = $data['settings'][$key];
                if ($value === 'on') {
                    $value = 1;
                }
                set_theme_mod($key, $value);
            }
        }
    }

}
