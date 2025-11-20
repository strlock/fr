<?php

use Elementor\Core\Kits\Documents\Tabs\Tab_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Aloha_Settings_Booking extends Tab_Base {

    public function get_id() {
        return 'aloha-settings-booking';
    }

    public function get_title() {
        $prefix = aloha_get_elementor_tab_prefix();
        return esc_html__($prefix . ' - Booking', ALOHA_DOMAIN);
    }

    public function get_group() {
        return 'settings';
    }

    public function get_icon() {
        return 'eicon-tools';
    }

    protected function register_tab_controls() {

        $this->start_controls_section(
                'section_aloha_booking',
                [
                    'label' => $this->get_title(),
                    'tab' => $this->get_id(),
                ]
        );

        $themo_mphb_use_theme_styling = get_theme_mod('themo_mphb_use_theme_styling', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_mphb_use_theme_styling',
                [
                    'label' => __('Calendar Styling', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_mphb_use_theme_styling,
                    'return_value' => 'on',
                    'selectors' => [
                        '.themo_mphb_availability_calendar .datepick, .datepick-popup .datepick.mphb-datepick-popup' => 'width: auto!important;',
                        '.datepick-popup .datepick.mphb-datepick-popup' => 'max-width: 600px;',
                    ],
                ]
        );
        $multicolor = get_theme_mod('themo_mphb_date_colors');

        $css_selector = ':root .mphb-calendar.mphb-datepick,'
                . ':root .mphb-calendar.mphb-datepick [class*="mphb-datepicker-"], '
                . ':root .datepick-popup .mphb-datepick-popup,'
                . ':root .datepick-popup [class*="mphb-datepicker-"].mphb-datepick-popup';

        $mphb_booked_date = '';
        if (isset($multicolor['mphb_booked_date'])) {
            $mphb_booked_date = $multicolor['mphb_booked_date'];
        }
        $this->add_control(
                'mphb_booked_date',
                [
                    'label' => __('Date Booked - Background', ALOHA_DOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'alpha' => true,
                    'default' => $mphb_booked_date,
                    'selectors' => [
                        $css_selector => ''
                        . '--mphb-booked-date-bg: {{VALUE}};'
                    ],
                    'condition' => [
                        'themo_mphb_use_theme_styling' => 'on',
                    ],
                ]
        );
        $this->add_control(
                'mphb_booked_date_color',
                [
                    'label' => __('Date Booked - Color', ALOHA_DOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'alpha' => true,
                    'selectors' => [
                        $css_selector => ''
                        . '--mphb-booked-date-color: {{VALUE}};'
                    ],
                    'condition' => [
                        'themo_mphb_use_theme_styling' => 'on',
                    ],
                    'separator' => 'after',
                ]
        );

        $this->add_control(
                'mphb_unavailable_date',
                [
                    'label' => __('Date Unavailable - Background', ALOHA_DOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'alpha' => true,
                    'selectors' => [
                        $css_selector => ''
                        . '--mphb-unselectable-date-bg: {{VALUE}};'
                        . '--mphb-not-available-date-bg: {{VALUE}};'
                        . '--mphb-out-of-season-date-bg: {{VALUE}};'
                    ],
                    'condition' => [
                        'themo_mphb_use_theme_styling' => 'on',
                    ],
                ]
        );
        $this->add_control(
                'mphb_unavailable_color',
                [
                    'label' => __('Date Unavailable - Color', ALOHA_DOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'alpha' => true,
                    'selectors' => [
                        $css_selector => ''
                        . '--mphb-unselectable-date-color: {{VALUE}};'
                        . '--mphb-not-available-date-color: {{VALUE}};'
                        . '--mphb-out-of-season-date-color: {{VALUE}};'
                    ],
                    'condition' => [
                        'themo_mphb_use_theme_styling' => 'on',
                    ],
                    'separator' => 'after',
                ]
        );

        $mphb_available_date = '';
        if (isset($multicolor['mphb_available_date'])) {
            $mphb_available_date = $multicolor['mphb_available_date'];
        }
        $this->add_control(
                'mphb_available_date',
                [
                    'label' => __('Date Available - Background', ALOHA_DOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'alpha' => true,
                    'default' => $mphb_available_date,
                    'selectors' => [
                        $css_selector => '--mphb-available-date-bg: {{VALUE}};',
                    ],
                    'condition' => [
                        'themo_mphb_use_theme_styling' => 'on',
                    ],
                ]
        );

        $this->add_control(
                'mphb_available_date_color',
                [
                    'label' => __('Date Available - Color', ALOHA_DOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'alpha' => true,
                    'selectors' => [
                        $css_selector => '--mphb-available-date-color: {{VALUE}};',
                    ],
                    'condition' => [
                        'themo_mphb_use_theme_styling' => 'on',
                    ],
                    'separator' => 'after',
                ]
        );
        $this->add_control(
                'mphb_selected_date_bg',
                [
                    'label' => __('Date Selected - Background', ALOHA_DOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'alpha' => true,
                    'selectors' => [
                        $css_selector => '--mphb-selected-date-bg: {{VALUE}};',
                    ],
                    'condition' => [
                        'themo_mphb_use_theme_styling' => 'on',
                    ],
                ]
        );

        $this->add_control(
                'mphb_selected_date_color',
                [
                    'label' => __('Date Selected - Color', ALOHA_DOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'alpha' => true,
                    'selectors' => [
                        $css_selector => '--mphb-selected-date-color: {{VALUE}};',
                    ],
                    'condition' => [
                        'themo_mphb_use_theme_styling' => 'on',
                    ],
                    'separator' => 'after',
                ]
        );

        $themo_mphp_category_show_header = get_theme_mod('themo_mphp_category_show_header', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_mphp_category_show_header',
                [
                    'label' => __('Category Header', ALOHA_DOMAIN),
                    'description' => __('Show / Hide header for Categories', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_mphp_category_show_header,
                    'return_value' => 'on'
                ]
        );

        $this->add_control(
                'themo_mphp_category_header_float',
                [
                    'label' => esc_html__('Category Header Position', ALOHA_DOMAIN),
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
                    'default' => get_theme_mod('themo_mphp_category_header_float', 'centered'),
                    'condition' => [
                        'themo_mphp_category_show_header' => 'on',
                    ],
                    'separator' => 'after',
                ]
        );
        $this->add_control(
                'themo_mphp_category_sidebar',
                [
                    'label' => esc_html__('Category Sidebar Position', ALOHA_DOMAIN),
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
                    'default' => get_theme_mod('themo_mphp_category_sidebar', 'right'),
                ]
        );

        $themo_mphp_category_masonry = get_theme_mod('themo_mphp_category_masonry', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_mphp_category_masonry',
                [
                    'label' => __('Category Masonry Style', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_mphp_category_masonry,
                    'return_value' => 'on'
                ]
        );
        $themo_mphp_tag_show_header = get_theme_mod('themo_mphp_tag_show_header', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_mphp_tag_show_header',
                [
                    'label' => __('Tag Header', ALOHA_DOMAIN),
                    'description' => __('Show / Hide header for Tags', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_mphp_tag_show_header,
                    'return_value' => 'on'
                ]
        );
        $this->add_control(
                'themo_mphp_tag_header_float',
                [
                    'label' => esc_html__('Category Header Position', ALOHA_DOMAIN),
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
                    'default' => get_theme_mod('themo_mphp_tag_header_float', 'centered'),
                    'condition' => [
                        'themo_mphp_tag_show_header' => 'on',
                    ],
                    'separator' => 'after',
                ]
        );
        $this->add_control(
                'themo_mphp_tag_sidebar',
                [
                    'label' => esc_html__('Tag Sidebar Position', ALOHA_DOMAIN),
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
                    'default' => get_theme_mod('themo_mphp_tag_sidebar', 'right'),
                ]
        );
        $themo_mphp_tag_masonry = get_theme_mod('themo_mphp_tag_masonry', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_mphp_tag_masonry',
                [
                    'label' => __('Tag Masonry Style', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_mphp_tag_masonry,
                    'return_value' => 'on'
                ]
        );
        $themo_mphp_amenities_show_header = get_theme_mod('themo_mphp_amenities_show_header', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_mphp_amenities_show_header',
                [
                    'label' => __('Amenity Header', ALOHA_DOMAIN),
                    'description' => __('Show / Hide header for Amenities', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_mphp_amenities_show_header,
                    'return_value' => 'on'
                ]
        );
        $this->add_control(
                'themo_mphp_amenities_header_float',
                [
                    'label' => esc_html__('Amenity Header Position', ALOHA_DOMAIN),
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
                    'default' => get_theme_mod('themo_mphp_amenities_header_float', 'centered'),
                    'condition' => [
                        'themo_mphp_amenities_show_header' => 'on',
                    ],
                    'separator' => 'after',
                ]
        );
        $this->add_control(
                'themo_mphp_amenities_sidebar',
                [
                    'label' => esc_html__('Amenity Sidebar Position', ALOHA_DOMAIN),
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
                    'default' => get_theme_mod('themo_mphp_amenities_sidebar', 'right'),
                ]
        );

        $themo_mphp_amenities_masonry = get_theme_mod('themo_mphp_amenities_masonry', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_mphp_amenities_masonry',
                [
                    'label' => __('Amenity Masonry Style', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_mphp_amenities_masonry,
                    'return_value' => 'on'
                ]
        );
        $themo_mphp_service_show_header = get_theme_mod('themo_mphp_service_show_header', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_mphp_service_show_header',
                [
                    'label' => __('Service Header', ALOHA_DOMAIN),
                    'description' => __('Show / Hide header for Services', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_mphp_service_show_header,
                    'return_value' => 'on'
                ]
        );
        $this->add_control(
                'themo_mphp_service_header_float',
                [
                    'label' => esc_html__('Amenity Header Position', ALOHA_DOMAIN),
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
                    'default' => get_theme_mod('themo_mphp_service_header_float', 'centered'),
                    'condition' => [
                        'themo_mphp_service_show_header' => 'on',
                    ],
                    'separator' => 'after',
                ]
        );
        $this->add_control(
                'themo_mphp_service_sidebar',
                [
                    'label' => esc_html__('Service Sidebar Position', ALOHA_DOMAIN),
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
                    'default' => get_theme_mod('themo_mphp_service_sidebar', 'right'),
                ]
        );
        $themo_mphp_service_masonry = get_theme_mod('themo_mphp_service_masonry', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_mphp_service_masonry',
                [
                    'label' => __('Service Masonry Style', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_mphp_service_masonry,
                    'return_value' => 'on'
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
            'themo_mphb_use_theme_styling',
            'themo_mphp_category_show_header',
            'themo_mphp_category_header_float',
            'themo_mphp_category_sidebar',
            'themo_mphp_category_masonry',
            'themo_mphp_tag_show_header',
            'themo_mphp_tag_header_float',
            'themo_mphp_tag_sidebar',
            'themo_mphp_tag_masonry',
            'themo_mphp_amenities_show_header',
            'themo_mphp_amenities_header_float',
            'themo_mphp_amenities_sidebar',
            'themo_mphp_amenities_masonry',
            'themo_mphp_service_show_header',
            'themo_mphp_service_header_float',
            'themo_mphp_service_sidebar',
            'themo_mphp_service_masonry',
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

        //save calendar colors as array of 'themo_mphb_date_colors'

        $themo_mphb_date_colors = [];
        if (array_key_exists('mphb_booked_date', $data['settings'])) {
            $themo_mphb_date_colors['mphb_booked_date'] = $data['settings']['mphb_booked_date'] ? $data['settings']['mphb_booked_date'] : '';
        }
        if (array_key_exists('mphb_available_date', $data['settings'])) {
            $themo_mphb_date_colors['mphb_available_date'] = $data['settings']['mphb_available_date'] ? $data['settings']['mphb_available_date'] : '';
        }
        if (count($themo_mphb_date_colors)) {
            set_theme_mod('themo_mphb_date_colors', $themo_mphb_date_colors);
        }
    }

}
