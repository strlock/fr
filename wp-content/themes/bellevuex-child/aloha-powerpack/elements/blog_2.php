<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Plugin;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_Blog_FR extends Widget_Base {
        var $elementorPostImageKey = 'post_image_elementor';
        
        public function get_style_depends(){
            $modified = filemtime(THEMO_PATH.'css/blog2.css');
            wp_register_style( $this->get_name(), THEMO_URL . 'css/blog2.css', array(), $modified );
            return [$this->get_name()];
        }
        public function getImageKey(){
            return $this->elementorPostImageKey;
        }
	public function get_name() {
		return 'themo-blog';
	}

	public function get_title() {
		return __( 'Blog', ALOHA_DOMAIN );
	}

        private function setupLink($list, $linkKey){
            $this->remove_render_attribute($linkKey); //reset
            if (isset($list[$linkKey])) {
                $this->add_render_attribute($linkKey, 'href', esc_url($list[$linkKey]['url']), true);
                if (!empty($list[$linkKey]['is_external'])) {
                    $this->add_render_attribute($linkKey, 'target', '_blank', true);
                }
                if (!empty($list[$linkKey]['nofollow'])) {
                    $this->add_render_attribute($linkKey, 'rel', 'nofollow', true);
                }
            }
        }
	public function get_icon() {
		return 'th-editor-icon-blog';
	}

	public function get_categories() {
		return [ 'themo-elements' ];
	}

	public function get_help_url() {
		return ALOHA_WIDGETS_HELP_URL_PREFIX . $this->get_name();
	}
	
        public function get_script_depends() {
            return [];
        }
        
    private function get_blog_categories_list() {
		$categories = array('all' => __('All Categories', ALOHA_DOMAIN));
		$get_categories = get_categories( array(
			'orderby' => 'name',
			'order'   => 'ASC'
		) );

		foreach( $get_categories as $category ) {
			$id = $category->term_id;
			$name = $category->name;
			$categories[$id] = $name;
		}

		return $categories;
	}

	protected function register_controls() {
            $this->start_controls_section(
                'thmv_section_data',
                [
                    'label' => __('Data', ALOHA_DOMAIN),
                ]
        );

        $this->add_control(
                'thmv_data_switcher',
                [
                    'label' => __('Use data source', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'label_on' => __('Yes', ALOHA_DOMAIN),
                    'label_off' => __('No', ALOHA_DOMAIN),
                    'return_value' => 'yes',
                    'default'=>'yes'
                ]
        );
        $this->add_control(
                'post_categories',
                [
                    'label' => __('Categories', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SELECT2,
                    'label_block' => true,
                    'multiple' => true,
                    'default' => 'all',
                    'options' => $this->get_blog_categories_list(),
                    'condition' => [
                        'thmv_data_switcher' => 'yes',
                    ],
                ]
        );

        $this->add_control(
            'post_columns',
            [
                'label' => __( 'Columns', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => '3-col',
                'options' => [
                    '2-col' => __( '2 Columns', ALOHA_DOMAIN ),
                    '3-col' => __( '3 Columns', ALOHA_DOMAIN ),
                    '4-col' => __( '4 Columns', ALOHA_DOMAIN ),
                    '5-col' => __( '5 Columns', ALOHA_DOMAIN ),
                ],
                'condition' => [
                    'thmv_style' => [ 'style_1','style_3'],
                ],
            ]
        );
       
        $this->add_control(
            'post_count',
            [
                'label' => __( 'Posts', ALOHA_DOMAIN ),
                'type' => Controls_Manager::NUMBER,
                'default' => 10,
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                        'thmv_data_switcher' => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'thmv_link_text',
            [
                'label' => __( 'Link text', ALOHA_DOMAIN ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __( 'Read More', ALOHA_DOMAIN ),
                'placeholder' => __( 'Read More', ALOHA_DOMAIN ),
                'condition' => [
                        'thmv_data_switcher' => 'yes',
                ],
            ]
        );
         

        $this->add_control(
            'pagination',
            [
                'label' => __( 'Pagination', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'label_off',
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'condition' => [
                        'thmv_data_switcher' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'pagination_msg',
            [
                'type'    => Controls_Manager::RAW_HTML,
                'raw' => __( '<small>(not supported on the Frontpage)</small>', ALOHA_DOMAIN ),
                'content_classes' => 'your-class',
                'separator' => 'none',
                'condition' => [
                        'thmv_data_switcher' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'themo_automatic_post_excerpts',
            [
                'label' => __( 'Automatic Post Excerpts', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'default' => get_theme_mod(ALOHA_SETTING_BUTTON_BLOG_EXCERPTS_ID, ALOHA_SETTING_BUTTON_BLOG_EXCERPTS_DEFAULT),
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'return_value' => 'on',
                'condition' => [
                        'thmv_data_switcher' => 'yes',
                ],
            ]
        );
        $this->add_control(
            'thmv_section_hide_data_heading',
            [
                'label' => __( 'Hide', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                        'thmv_data_switcher' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'thmv_hide_image',
            [
                'label' => __( 'Image', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'selectors' => [
                    '{{WRAPPER}} .thmv-grid-img' => 'display:none;',
                    '{{WRAPPER}} .mas-blog-post img' => 'display:none;',
                ],
                'condition' => [
                        'thmv_data_switcher' => 'yes',
                ],

            ]
        );

        $this->add_control(
            'thmv_hide_title',
            [
                'label' => __( 'Title', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'selectors' => [
                    '{{WRAPPER}} h3' => 'display:none;',
                ],
                'condition' => [
                        'thmv_data_switcher' => 'yes',
                ],

            ]
        );

        $this->add_control(
            'thmv_hide_excerpt',
            [
                'label' => __( 'Excerpt', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'selectors' => [
                    '{{WRAPPER}} .entry-content' => 'display:none;',
                ],
                'condition' => [
                        'thmv_data_switcher' => 'yes',
                ],

            ]
        );

        $this->add_control(
            'thmv_hide_author',
            [
                'label' => __( 'Author', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'selectors' => [
                    '{{WRAPPER}} .thmv-author ' => 'display:none;',
                ],
                'condition' => [
                        'thmv_data_switcher' => 'yes',
                ],

            ]
        );

        $this->add_control(
            'thmv_hide_date',
            [
                'label' => __( 'Date', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'selectors' => [
                    '{{WRAPPER}} .thmv-date' => 'display:none;',
                ],
                'condition' => [
                        'thmv_data_switcher' => 'yes',
                ],

            ]
        );

        $this->add_control(
            'thmv_hide_category',
            [
                'label' => __( 'Category', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'selectors' => [
                    '{{WRAPPER}} .post-meta' => 'display:none;',
                ],
                'condition' => [
                    'thmv_data_switcher' => 'yes',
                    'thmv_style' => [ 'style_3'],

                ],
            ]
        );

        $this->add_control(
            'thmv_hide_comments',
            [
                'label' => __( 'Comments', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'selectors' => [
                    '{{WRAPPER}} .show-comments' => 'display:none;',
                ],
                'condition' => [
                    'thmv_data_switcher' => 'yes',
                    'thmv_style' => [ 'style_3']
                ],
            ]
        );

        $this->add_control(
            'thmv_hide_read_more',
            [
                'label' => __( 'Read more', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'selectors' => [
                    '{{WRAPPER}} .thmv-learn-btn' => 'display:none;',
                    '{{WRAPPER}} .entry-content a' => 'display:none;',
                ],
                'condition' => [
                        'thmv_data_switcher' => 'yes',
                ],

            ]
        );
        $this->end_controls_section();
        
        $this->start_controls_section(
                'thmv_section_listing',
                [
                    'label' => __('Posts', ALOHA_DOMAIN),
                    'condition' => [
                        'thmv_data_switcher' => '',
                    ],
                ]
        );
        $listing = new Repeater();
        $listing->add_control(
                'thmv_title',
                [
                    'label' => __('Title', ALOHA_DOMAIN),
                    'type' => Controls_Manager::TEXT,
                    'default' => __('Studio Suite', ALOHA_DOMAIN),
                    'placeholder' => __('Studio Suite', ALOHA_DOMAIN),
                    'label_block' => true,
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
        );
        
        $listing->add_control(
                'thmv_description',
                [
                    'label' => __('Description', ALOHA_DOMAIN),
                    'type' => Controls_Manager::TEXTAREA,
                    'label_block' => true,
                    'default' => 'The Studio Suite is warm and welcoming, with a large, gorgeous bathroom that includes a full-size whirlpool tub.',
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
        );
        $date = date(get_option('date_format'));
        $listing->add_control(
                $this->getImageKey(),
                [
                    'label' => __('Image', ALOHA_DOMAIN),
                    'type' => Controls_Manager::MEDIA,
                    'default' => [
                        'url' => Utils::get_placeholder_image_src(),
                    ],
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
        );
        $listing->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'name' => $this->getImageKey(),
                    'default' => 'large',
                    'separator' => 'none',
                ]
        );
        $listing->add_control(
                'thmv_align_image_right',
                [
                    'label' => __('Image on the right', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => '',
                    'label_on' => __('Yes', ALOHA_DOMAIN),
                    'label_off' => __('No', ALOHA_DOMAIN),
                    'return_value' => 'yes',
                ]
        );
        $listing->add_control(
                'thmv_author',
                [
                    'label' => __('Author', ALOHA_DOMAIN),
                    'type' => Controls_Manager::TEXT,
                    'default' => 'Ryan',
                    'placeholder' => 'Ryan',
                    'dynamic' => [
                        'active' => true,
                    ],
                    'separator' => 'before',
                ]
        );
        $listing->add_control(
                'thmv_date',
                [
                    'label' => __('Date', ALOHA_DOMAIN),
                    'type' => Controls_Manager::TEXT,
                    'default' => __($date, ALOHA_DOMAIN),
                    'placeholder' => __($date, ALOHA_DOMAIN),
                    'dynamic' => [
                        'active' => true,
                    ],
                    'separator' => 'before',
                ]
        );
        $listing->add_control(
                'thmv_link_text',
                [
                    'label' => __('Text', 'elementor'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'default' => __('Read More', 'elementor'),
                    'placeholder' => __('Read More', 'elementor'),
                ]
        );

        $listing->add_control(
                'thmv_link',
                [
                    'label' => __('Link', ALOHA_DOMAIN),
                    'type' => Controls_Manager::URL,
                    'placeholder' => 'http://your-link.com',
                    'default' => [
                        'url' => '',
                    ],
                    'dynamic' => [
                        'active' => true,
                    ],
                ]
        );
        $this->add_control(
                'listings',
                [
                    'label' => __('Posts', ALOHA_DOMAIN),
                    'type' => Controls_Manager::REPEATER,
                    'fields' => $listing->get_controls(),
                    'title_field' => '{{{ thmv_title }}}',
                ]
        );
        $this->end_controls_section();

        /* STYLE - Layout */
        $this->start_controls_section(
            'thmv_section_layout',
            [
                'label' => __( 'Layout', ALOHA_DOMAIN ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'thmv_style',
            [
                'label' => __( 'Style', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'style_3',
                'options' => [
                    'style_1' => __( 'Style 1', ALOHA_DOMAIN ),
                    'style_2' => __( 'Style 2', ALOHA_DOMAIN ),
                    'style_3' => __( 'Style 3', ALOHA_DOMAIN ),
                ],
            ]
        );

        /*$this->add_responsive_control(
            'thmv_wrapper_text_align',
            [
                'label' => __( 'Content Align', ALOHA_DOMAIN ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', ALOHA_DOMAIN ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', ALOHA_DOMAIN ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', ALOHA_DOMAIN ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .thmv-wrapper-content' => 'text-align: {{VALUE}}',
                ],
            ]
        );*/

        $this->end_controls_section();

        /* STYLE - Image */
        $this->start_controls_section(
            'thmv_section_image_style',
            [
                'label' => __( 'Image', ALOHA_DOMAIN ),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'thmv_hide_image' => '',
                ],
            ]
        );

        
        $this->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'name' => $this->getImageKey(),
                    'default' => 'large',
                    'separator' => 'none',
                     'condition' => [
                        'thmv_data_switcher' => 'yes',
                         'thmv_style' => ['style_1','style_2']
                    ],
                ]
        );
        $this->add_control(
            'post_image_size',
            [
                'label' => __( 'Size', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'th_img_sm_standard',
                'options' => [
                    'th_img_sm_standard' => __( 'Standard', ALOHA_DOMAIN ),
                    'th_img_sm_landscape' => __( 'Landscape', ALOHA_DOMAIN ),
                    'th_img_sm_portrait' => __( 'Portrait', ALOHA_DOMAIN ),
                    'th_img_sm_square' => __( 'Square', ALOHA_DOMAIN ),
                    'th_img_lg' => __( 'Large', ALOHA_DOMAIN ),
                ],
                'condition' => [
                    'thmv_data_switcher' => 'yes',
                    'thmv_style' => ['style_3']
                ],
            ]
        );
        $this->add_control(
                'thmv_align_image',
                [
                    'label' => __('Image alignment', ALOHA_DOMAIN),
                    'type' => Controls_Manager::CHOOSE,
                    'label_block' => false,
                    'options' => [
                        'left' => [
                            'title' => __('Left', ALOHA_DOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'alternate' => [
                            'title' => __('Alternate', ALOHA_DOMAIN),
                            'icon' => 'fa fa-times',
                        ],
                        'right' => [
                            'title' => __('Right', ALOHA_DOMAIN),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'default' => '',
                    'condition' => [
                        'thmv_data_switcher' => 'yes',
                        'thmv_style' => ['style_2']
                    ],
                ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'thmv_section_content_style',
            [
                'label' => __( 'Content', ALOHA_DOMAIN ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );


        /* STYLE - Title */
        $this->add_control(
            'thmv_section_title_heading',
            [
                'label' => __( 'Title', ALOHA_DOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'thmv_hide_title' => '',
                ],
            ]
        );

        $this->add_control(
                'title_size',
                [
                    'label' => __('HTML Tag', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'h1' => __('H1', ALOHA_DOMAIN),
                        'h2' => __('H2', ALOHA_DOMAIN),
                        'h3' => __('H3', ALOHA_DOMAIN),
                        'h4' => __('H4', ALOHA_DOMAIN),
                        'h5' => __('H5', ALOHA_DOMAIN),
                        'h6' => __('H6', ALOHA_DOMAIN),
                    ],
                    'default' => 'h3',
                    'separator' => 'none',
                ]
        );
        
		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .post-title a' => 'color: {{VALUE}};',
				],
                'condition' => [
                    'thmv_hide_title' => '',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => __( 'Typography', ALOHA_DOMAIN ),
				'selector' => '{{WRAPPER}} .post-title a',
                'condition' => [
                    'thmv_hide_title' => '',
                ],
			]
		);

        /* STYLE - Excerpt */
        $this->add_control(
            'thmv_section_excerpt',
            [
                'label' => __( 'Excerpt', ALOHA_DOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'thmv_hide_excerpt' => '',
                ],
            ]
        );

        $this->add_control(
            'excerpt_color',
            [
                'label' => __( 'Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .entry-content p' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'thmv_hide_excerpt' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'excerpt_typography',
                'label' => __( 'Typography', ALOHA_DOMAIN ),
                'selector' => '{{WRAPPER}} .entry-content p',
                'condition' => [
                    'thmv_hide_excerpt' => '',
                ],
            ]
        );

        /* STYLE - Meta */
        $this->add_control(
            'thmv_section_meta',
            [
                'label' => __( 'Meta', ALOHA_DOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );
        
		$this->add_control(
			'author_color',
			[
				'label' => __( 'Author Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .thmv-author a' => 'color: {{VALUE}};',
				],
                'condition' => [
                    'thmv_hide_author' => '',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'author_typography',
				'label' => __( 'Author Typography', ALOHA_DOMAIN ),
				'selector' => '{{WRAPPER}} .thmv-author',
                'condition' => [
                    'thmv_hide_author' => '',
                ],
			]
		);

        $this->add_control(
            'date_color',
            [
                'label' => __( 'Date Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .thmv-date' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'thmv_hide_date' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'date_typography',
                'label' => __( 'Date Typography', ALOHA_DOMAIN ),
                'selector' => '{{WRAPPER}} .thmv-date',
                'condition' => [
                    'thmv_hide_date' => '',
                ],
            ]
        );
        $this->add_control(
                'divider_color',
                [
                    'label' => __('Divider Color', ALOHA_DOMAIN),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .thmv-separator' => 'border-color: {{VALUE}};',
                    ],
                    'condition' => [
                        'thmv_hide_author' => '',
                        'thmv_style' => ['style_1']
                    ],
                ]
        );
        $this->add_control(
            'category_color',
            [
                'label' => __( 'Category Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementors' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'thmv_hide_category' => '',
                    'thmv_style' => [ 'style_3']
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'category_typography',
                'label' => __( 'Category Typography', ALOHA_DOMAIN ),

                'selector' => '{{WRAPPER}} .post-elementors',
                'condition' => [
                    'thmv_hide_category' => '',
                    'thmv_style' => [ 'style_3']
                ],
            ]
        );

        $this->add_control(
            'category_comments',
            [
                'label' => __( 'Comments Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementors' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'thmv_hide_comments' => '',
                    'thmv_style' => [ 'style_3']
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'comments_typography',
                'label' => __( 'Comments Typography', ALOHA_DOMAIN ),

                'selector' => '{{WRAPPER}} .post-elementors',
                'condition' => [
                    'thmv_hide_comments' => '',
                    'thmv_style' => [ 'style_3']
                ],
            ]
        );


        /* STYLE - Read More */
        $this->add_control(
            'thmv_section_read_more',
            [
                'label' => __( 'Read more', ALOHA_DOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'thmv_hide_read_more' => '',
                ],
            ]
        );

		$this->add_control(
			'read_more_color',
			[
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .thmv-learn-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .thmv-learn-btn svg path' => 'fill: {{VALUE}};',
				],
                'condition' => [
                    'thmv_hide_read_more' => '',
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'read_more_typography',
				'label' => __( 'Typography', ALOHA_DOMAIN ),

				'selector' => '{{WRAPPER}} .thmv-learn-btn',
                'condition' => [
                    'thmv_hide_read_more' => '',
                ],
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
            'section_style_border',
            [
                'label' => __( 'Appearance', ALOHA_DOMAIN ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'thmv_style' => [ 'style_3']
                ],
            ]
        );

        $this->add_responsive_control(
            'blog_section_padding',
            [
                'label' => __( 'Padding', ALOHA_DOMAIN ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .mas-blog-post .post-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'blog_border',
            [
                'label' => __( 'Borders', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __( 'Show', ALOHA_DOMAIN ),
                'label_off' => __( 'Hide', ALOHA_DOMAIN ),
                'selectors' => [
                    '{{WRAPPER}} .mas-blog-post .post-inner' => 'border-width:1px',

                ],
            ]
        );

        $this->add_responsive_control(
			'blog_content_border_radius',
			[
				'label' => __( 'Border Radius', ALOHA_DOMAIN ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'selectors' => [
					'{{WRAPPER}} .mas-blog-post .post-inner' => 'border-radius:{{VALUE}}px;',
                    '{{WRAPPER}} .mas-blog-post.format-video .post-inner, {{WRAPPER}} .mas-blog-post.format-image .post-inner,
                    {{WRAPPER}} .mas-blog-post.format-gallery .post-inner, {{WRAPPER}} .mas-blog-post.has-post-thumbnail .post-inner' => 'border-radius:0 0 {{VALUE}}px {{VALUE}}px;',
                    '{{WRAPPER}} .mas-blog-post .th-pkg-img img, {{WRAPPER}} .mas-blog-post.format-gallery .flexslider.gallery ul li a img,
                    {{WRAPPER}} .mas-blog-post.format-gallery .flexslider.gallery ul li img, {{WRAPPER}} .mas-blog-post a img.wp-post-image' => 'border-radius: {{VALUE}}px {{VALUE}}px 0 0;',
				],
                'dynamic' => [
                    'active' => true,
                ],
			]
		);

        $this->end_controls_section();

	}
        
    private function getImageFromPost($ID, $settings) {
        
        $imageKey = $this->getImageKey();
        $settings[$imageKey] = '';
        $th_imageId = get_post_thumbnail_id($ID);
        if ($th_imageId) {
           $settings[$imageKey] = ['id'=>$th_imageId];
           return Group_Control_Image_Size::get_attachment_image_html($settings, $imageKey);
        }
        
        return false;
        
    }
    private function getImageSizeInfo($settings, $imageKey) {

        $imgSize = $settings[$imageKey . '_size'];
        $dim = $settings[$imageKey . '_custom_dimension'];
        $image = isset($settings[$imageKey]) ? $settings[$imageKey] : false;
        $imageSizeInfo = array($this->getImageKey() => $image, $this->getImageKey() . '_size' => $imgSize, $this->getImageKey() . '_custom_dimension' => $dim);

        return $imageSizeInfo;
    }
    private function getDescription() {

        $excerpt = strip_tags(get_the_content());
        $dots = '&hellip;';
        $tempExcerpt = str_replace('...', $dots, strip_tags($excerpt));
//
//        if ... exist then remove them and extra read more
        $dotsPos = strpos($tempExcerpt, '&hellip;');
        
        if($dotsPos!==false){
            $tempExcerpt = substr($tempExcerpt, 0, $dotsPos).$dots;
            $excerpt = $tempExcerpt;
        }
        return $excerpt; //maybe keep bold, italics
    }
        private function getPosts($settings){
            $args = array(
                'post_type' => array('post'),
                'post_status' => array('publish'),
                'fields' => 'ids'
            );

            if ($settings['post_categories'] != 'all') {
                if (is_array($settings['post_categories'])) {
                    if (in_array('all', $settings['post_categories'])) {
                        $settings['post_categories'] = array_diff($settings['post_categories'], array('all'));
                    }

                    $categories = implode(', ', $settings['post_categories']);
                } else {
                    $categories = array($settings['post_categories']);
                }
                $args['cat'] = $categories;
            }

            if ($settings['post_count']) {
                $args['posts_per_page'] = $settings['post_count'];
            }

            if (isset($settings['pagination']) && $settings['pagination'] == 'yes') {

                if (get_query_var('paged')) {
                    $paged = get_query_var('paged');
                } elseif (get_query_var('page')) {
                    $paged = get_query_var('page');
                } else {
                    $paged = 1;
                }

                if (isset($settings['post_count'])) {
                    $th_offset = ( $paged - 1 ) * $settings['post_count'];
                } else {
                    $default_posts_per_page = get_option('posts_per_page');
                    $th_offset = ( $paged - 1 ) * $default_posts_per_page;
                }

                $args['paged'] = $paged;
                //$args['offset'] = $th_offset;
            }

            
            $posts = new \WP_Query($args);

            //if there are filtered posts but the result is empty, unset.
            if ( isset($args['cat']) && is_array($args['cat']) && (count($args['cat']) > 0) && !$posts->have_posts() ) {

                unset($args['cat']);
                $posts = new \WP_Query( $args );
            }
          
            return $posts;
        }
	protected function render() {
        $settings = $this->get_settings_for_display();
        $dataSource = $settings['thmv_data_switcher'] == "yes" ? true : false;
        // The Query
        $use_bittersweet_pagination = false;
        if (is_front_page()) {
            $use_bittersweet_pagination = true;
        }
        
        if ($dataSource) {  
            $widget_wp_query = $this->getPosts($settings);

           if (!$widget_wp_query || !count($widget_wp_query->posts)) {
                echo '<div class="alert">';
                _e('Sorry, no results were found.', 'th-widget-pack');
                echo '</div>';
                return;
            }
           global $wp_query;
           $temp_query = $wp_query;
           $wp_query   = $widget_wp_query; 
           $args = ['post__in'=> $wp_query->posts];
           if(isset($settings['post_count'])){
               $args['posts_per_page'] = $settings['post_count'];
           }
           $posts = get_posts($args);
           
        } else {
            
            if($settings['thmv_style']==='style_3'){
                 echo '<div class="alert">';
                _e('Sorry, this style only works with the data source.', ALOHA_DOMAIN);
                echo '</div>';
                return;
            }
            
            if (!isset($settings['listings']) || !count($settings['listings'])) {
                return;
            }

            $posts = $settings['listings'];
        }
        ?>

            <?php
            switch( $settings['thmv_style'] ) {
                case "style_1":
                case "style_2":    
                    $columns  = isset($settings['post_columns'])  &&  !empty($settings['post_columns']) ? 'thmv-col-'.(INT)$settings['post_columns']: '';
                    $readmoreText = isset($settings['thmv_link_text'])  &&  !empty($settings['thmv_link_text']) ? $settings['thmv_link_text']: '';
                    $hideImage = isset($settings['thmv_hide_image'])  &&  !empty($settings['thmv_hide_image']) ? $settings['thmv_hide_image']: '';
                    $hideDate =  isset($settings['thmv_hide_date'])  &&  !empty($settings['thmv_hide_date']) ? $settings['thmv_hide_date']: '';
                    $hideAuthor =  isset($settings['thmv_hide_author'])  &&  !empty($settings['thmv_hide_author']) ? $settings['thmv_hide_author']: '';
                    $style = (INT)str_replace('style_', '', $settings['thmv_style']);
                    $dateFormat = $style===2 ? 'd/m/Y' : get_option( 'date_format' );
                    $imageAlignment = 'image-alignment-' . $settings['thmv_align_image'];
                    ?>
                    <div class="thmv-blog-post thmv-post-styl-<?=$style?> <?=$columns?> <?=$imageAlignment?>">
                        <?php foreach($posts as $post) { 
                            if($dataSource){
                                setup_postdata( $GLOBALS['post'] =& $post );
                                
                                $postID = get_the_ID();
                                $title = get_the_title();
                                $image = $hideImage ? false : $this->getImageFromPost($postID, $settings);
                                $desc = $this->getDescription();
                                //$author_id = get_the_author_meta( 'ID' );
                                $date = get_the_date($dateFormat);
                                $link = get_permalink();
                                $authorLink = get_the_author_link();
                                $tempLinkArr = ['thmv_link'=>['url'=>$link]];
                                $this->setupLink($tempLinkArr, 'thmv_link');
                            }
                            else {
                                $title = $post['thmv_title'];
                                $image = Group_Control_Image_Size::get_attachment_image_html($post, $this->getImageKey());
                                $desc = $post['thmv_description'];
                                //$author_id = get_the_author_meta( 'ID' );
                                $date = $post['thmv_date'];
                                
                                $link = $post['thmv_link'];
                                $authorLink = $post['thmv_author'];
                                $this->setupLink($post, 'thmv_link');
                                $readmoreText = $post['thmv_link_text'];
                                $showImgesRightSide = isset($post['thmv_align_image_right']) && $post['thmv_align_image_right'] == 'yes';
                                $imageAlignment =  $showImgesRightSide ? 'image-column-right ' : '';

                            }
                            

                            
                            ?>
                        <div class="thmv-column <?= $imageAlignment ?>">
                           <?php if($image):?>
                            <div class="thmv-grid-img">
                                <a <?php echo $this->get_render_attribute_string('thmv_link'); ?>><?=$image?></a>
                            </div>
                            <?php endif; ?>
                            <div class="thmv-info mas-blog-post">
                                <?php if((!$hideAuthor && $authorLink) || (!$hideDate && $date) ): ?>
                                <div class="thmv-subheading">
                                     <?php if(!$hideAuthor && $authorLink):?>
                                    <span class="thmv-author"><?=$authorLink?><?=(!$hideDate ? ' - ': '') ?></span>
                                     <?php endif; ?>
                                    <?php if(!$hideDate):?>
                                    <span class="thmv-date"><?=$date?></span>
                                    <?php endif; ?>
                                    <?php if($style===1):?>
                                    <hr class="thmv-separator">
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                                <<?php echo esc_attr($settings['title_size']); ?> class="post-title"><a <?php echo $this->get_render_attribute_string('thmv_link'); ?>><?= $title?></a></<?php echo esc_attr($settings['title_size']); ?>>
                                <div class="entry-content"><p><?=$desc?></p></div>
                                <a class="thmv-learn-btn thmv-w-100" <?php echo $this->get_render_attribute_string('thmv_link'); ?>><?=esc_html__($readmoreText)?>
                                     <?php if($style===1):?>
                                    <svg width="19" height="10" viewBox="0 0 19 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18.4596 5.45962C18.7135 5.20578 18.7135 4.79422 18.4596 4.54038L14.323 0.403807C14.0692 0.149967 13.6576 0.149966 13.4038 0.403807C13.15 0.657648 13.15 1.06921 13.4038 1.32305L17.0808 5L13.4038 8.67696C13.15 8.9308 13.15 9.34235 13.4038 9.59619C13.6576 9.85004 14.0692 9.85004 14.323 9.5962L18.4596 5.45962ZM-5.68248e-08 5.65L18 5.65L18 4.35L5.68248e-08 4.35L-5.68248e-08 5.65Z" fill="#191B18"/>
                                    </svg>
                                     <?php endif; ?>
                                    <?php if($style===2):?>
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M-0.000156792 8.92L-0.000156705 6.92L11.9998 6.92L6.49984 1.42L7.91984 -3.46194e-07L15.8398 7.92L7.91984 15.84L6.49984 14.42L11.9998 8.92L-0.000156792 8.92Z" fill="#171818"></path>
                                    </svg>
                                    <?php endif; ?>
                                </a>
                            </div>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                    <?php if ( isset( $settings['pagination'] ) &&  $settings['pagination'] == 'yes' && $widget_wp_query->max_num_pages > 1 ) { ?>
                            <div class="row">
                                <nav class="post-nav">
                                    <ul class="pager">
                                        <?php
                                        if( $use_bittersweet_pagination ) {
                                            th_bittersweet_pagination($widget_wp_query->max_num_pages);
                                        } else { ?>
                                            <li class="previous"><?php next_posts_link( esc_html__( '&larr; Older posts', ALOHA_DOMAIN ), $widget_wp_query->max_num_pages); ?></li>
                                            <li class="next"><?php previous_posts_link( esc_html__( 'Newer posts &rarr;', ALOHA_DOMAIN ) ); ?></li>
                                        <?php }?>
                                    </ul>
                                </nav>
                            </div>
                        <?php } ?>
                    <!--- Post-style-1 start end--->
                <?php
                    break;

                case "style_3":
                    if ( isset( $settings['post_image_size'] ) &&  $settings['post_image_size'] > "" ) {
                        global $image_size, $masonry_template_key;
                        $image_size = $settings['post_image_size'];
                        $masonry_template_key = '-masonry';
                    }
                        
                    $automatic_post_excerpts = $settings['themo_automatic_post_excerpts']; //used by the blog templates
                       
                    
                    $th_section_class = "th-masonry-blog";
                    $th_post_classes = "col-sm-6 col-md-4";

                    if ( isset( $settings['post_columns'] ) &&  $settings['post_columns'] > "" ) {
                        switch ( $settings['post_columns'] ) {
                            case '2-col':
                                $th_section_class .= " th-blog-2-col";
                                $th_post_classes = "col-sm-6";
                                break;
                            case '4-col':
                                $th_section_class .= " th-blog-4-col";
                                $th_post_classes = "col-sm-6 col-md-4";
                                break;
                            case '5-col':
                                $th_section_class .= " th-blog-5-col";
                                $th_post_classes = "col-sm-6 col-md-4";
                                break;
                            default:
                                $th_section_class .= " th-blog-3-col";
                                $th_post_classes = "col-sm-6 col-md-4";
                        }
                    }
                    ?>
                    <section class="<?php echo esc_attr( $th_section_class ); ?>">

                        <div class="mas-blog row">
                            <div class="mas-blog-post-sizer <?php echo esc_attr($th_post_classes); ?>"></div>
                            <?php foreach($posts as $post) { 
                            setup_postdata( $GLOBALS['post'] =& $post );
                            ?>
                                <?php $format = get_post_format() ? get_post_format() : 'standard';?>
                                <div <?php $th_post_classes = "mas-blog-post " . esc_attr( $th_post_classes ); post_class( esc_attr( $th_post_classes ) ); ?>>
                                    <?php 
                                    if(file_exists(THEMO_PATH . 'elements/blog_templates/content-'.$format.'.php')){
                                        include THEMO_PATH . 'elements/blog_templates/content-'.$format.'.php';
                                    }
                                    else {
                                        include THEMO_PATH . 'elements/blog_templates/content-standard.php';
                                    }
                                    ?>
                                </div>

                            <?php } ?>

                        </div>
                        <?php if ( isset( $settings['pagination'] ) &&  $settings['pagination'] == 'yes' && $widget_wp_query->max_num_pages > 1 ) { ?>
                            <div class="row">
                                <nav class="post-nav">
                                    <ul class="pager">
                                        <?php
                                        if( $use_bittersweet_pagination ) {
//                                            th_bittersweet_pagination($widget_wp_query->max_num_pages);
                                        } else { ?>
                                            <li class="previous"><?php next_posts_link( esc_html__( '&larr; Older posts', ALOHA_DOMAIN ), $widget_wp_query->max_num_pages); ?></li>
                                            <li class="next"><?php previous_posts_link( esc_html__( 'Newer posts &rarr;', ALOHA_DOMAIN ) ); ?></li>
                                        <?php }?>
                                    </ul>
                                </nav>
                            </div>
                        <?php } ?>
                    </section>

                    <?php
                    break;
            }
		
            // Reset postdata
            if ($dataSource) {
                // Reset main query object
               $wp_query = NULL;
               $wp_query = $temp_query;
               wp_reset_postdata();

            }

	}

	protected function content_template() {}
}

Plugin::instance()->widgets_manager->register( new Themo_Widget_Blog_FR() );
