<?php

use Elementor\Core\Kits\Documents\Tabs\Tab_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Aloha_Settings_Blog extends Tab_Base {

    public function get_id() {
        return 'aloha-settings-blog';
    }

    public function get_title() {
        $prefix = aloha_get_elementor_tab_prefix();
        return esc_html__($prefix . ' - Blog', ALOHA_DOMAIN);
    }

    public function get_group() {
        return 'settings';
    }

    public function get_icon() {
        return 'eicon-tools';
    }

    protected function register_tab_controls() {

        $this->start_controls_section(
                'section_aloha_blog',
                [
                    'label' => $this->get_title(),
                    'tab' => $this->get_id(),
                ]
        );

        $themo_automatic_post_excerpts = get_theme_mod('themo_automatic_post_excerpts', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_automatic_post_excerpts',
                [
                    'label' => __('Enable Automatic Post Excerpts', ALOHA_DOMAIN),
                    'description' => esc_html__('This option affects the Blog widget and the blog templates', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_automatic_post_excerpts,
                    'return_value' => 'on'
                ]
        );
        $themo_blog_index_layout_show_header = get_theme_mod('themo_blog_index_layout_show_header', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_blog_index_layout_show_header',
                [
                    'label' => __('Blog Homepage Header', ALOHA_DOMAIN),
                    'description' => esc_html__('Show / Hide header for Blog Homepage', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_blog_index_layout_show_header,
                    'return_value' => 'on'
                ]
        );
        $this->add_control(
                'themo_blog_index_layout_header_float',
                [
                    'label' => esc_html__('Blog Homepage Header Position', ALOHA_DOMAIN),
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
                    'default' => get_theme_mod('themo_blog_index_layout_header_float', 'centered'),
                    'condition' => [
                        'themo_blog_index_layout_show_header' => 'on',
                    ],
                    'separator' => 'after',
                ]
        );
        $this->add_control(
                'themo_blog_index_layout_sidebar',
                [
                    'label' => esc_html__('Blog Homepage Sidebar Position', ALOHA_DOMAIN),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => __('Left', ALOHA_DOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'full' => [
                            'title' => __('None', ALOHA_DOMAIN),
                        ],
                        'right' => [
                            'title' => __('Right', ALOHA_DOMAIN),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'default' => get_theme_mod('themo_blog_index_layout_sidebar', 'centered'),
                ]
        );
        
        $themo_single_post_layout_show_header = get_theme_mod('themo_single_post_layout_show_header', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_single_post_layout_show_header',
                [
                    'label' => __('Blog Single Page Header', ALOHA_DOMAIN),
                    'description' => esc_html__('Show / Hide Page header for Blog Single', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_single_post_layout_show_header,
                    'return_value' => 'on'
                ]
        );
        $this->add_control(
                'themo_single_post_layout_header_float',
                [
                    'label' => esc_html__('Blog Single Page Header Position', ALOHA_DOMAIN),
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
                    'default' => get_theme_mod('themo_single_post_layout_header_float', 'centered'),
                    'condition' => [
                        'themo_single_post_layout_show_header' => 'on',
                    ],
                    'separator' => 'after',
                ]
        );
        $this->add_control(
                'themo_single_post_layout_sidebar',
                [
                    'label' => esc_html__('Blog Single Sidebar Position', ALOHA_DOMAIN),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => __('Left', ALOHA_DOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'full' => [
                            'title' => __('None', ALOHA_DOMAIN),
                        ],
                        'right' => [
                            'title' => __('Right', ALOHA_DOMAIN),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'default' => get_theme_mod('themo_single_post_layout_sidebar', 'right'),
                ]
        );
        $themo_default_layout_show_header = get_theme_mod('themo_default_layout_show_header', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_default_layout_show_header',
                [
                    'label' => __('Archives Header', ALOHA_DOMAIN),
                    'description' => esc_html__('Show / Hide header for Archives, 404, Search', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_default_layout_show_header,
                    'return_value' => 'on'
                ]
        );
        $this->add_control(
                'themo_default_layout_header_float',
                [
                    'label' => esc_html__('Archives Header Position', ALOHA_DOMAIN),
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
                    'default' => get_theme_mod('themo_default_layout_header_float', 'centered'),
                    'condition' => [
                        'themo_default_layout_show_header' => 'on',
                    ],
                    'separator' => 'after',
                ]
        );
        $this->add_control(
                'themo_default_layout_sidebar',
                [
                    'label' => esc_html__('Archives Sidebar Position', ALOHA_DOMAIN),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => __('Left', ALOHA_DOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'full' => [
                            'title' => __('None', ALOHA_DOMAIN),
                        ],
                        'right' => [
                            'title' => __('Right', ALOHA_DOMAIN),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'default' => get_theme_mod('themo_default_layout_sidebar', 'right'),
                ]
        );
        $themo_blog_index_layout_masonry = get_theme_mod('themo_blog_index_layout_masonry', 1) == 1 ? 'on' : '';
        $this->add_control(
                'themo_blog_index_layout_masonry',
                [
                    'label' => __('Masonry Style for Category Pages', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Enable', ALOHA_DOMAIN),
                    'label_off' => __('Disable', ALOHA_DOMAIN),
                    'default' => $themo_blog_index_layout_masonry,
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
            'themo_automatic_post_excerpts',
            'themo_blog_index_layout_show_header',
            'themo_blog_index_layout_header_float',
            'themo_blog_index_layout_sidebar',
            'themo_single_post_layout_show_header',
            'themo_single_post_layout_header_float',
            'themo_single_post_layout_sidebar',
            'themo_default_layout_show_header',
            'themo_default_layout_header_float',
            'themo_default_layout_sidebar',
            'themo_blog_index_layout_masonry',
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
