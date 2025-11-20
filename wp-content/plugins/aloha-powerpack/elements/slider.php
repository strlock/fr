<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_Slider extends Widget_Base {

	public function get_name() {
		return 'themo-slider';
	}

	public function get_title() {
		return __( 'Slider', ALOHA_DOMAIN );
	}

	public function get_icon() {
		return 'th-editor-icon-slider';
	}

	public function get_categories() {
		return [ 'themo-elements' ];
	}

	public function get_help_url() {
		return ALOHA_WIDGETS_HELP_URL_PREFIX . $this->get_name();
	}
	
	public static function get_button_sizes() {
		return [
			'xs' => __( 'Extra Small', ALOHA_DOMAIN ),
			'sm' => __( 'Small', ALOHA_DOMAIN ),
			'md' => __( 'Medium', ALOHA_DOMAIN ),
			'lg' => __( 'Large', ALOHA_DOMAIN ),
			'xl' => __( 'Extra Large', ALOHA_DOMAIN ),
		];
	}
        public function get_style_depends() {
            $modified2 = filemtime(THEMO_PATH . 'css/'.ALOHA_ELEMENTOR_CALENDAR_AND_OTHER_FORMS_FILENAME.'.css');
            wp_register_style(ALOHA_ELEMENTOR_CALENDAR_AND_OTHER_FORMS_FILENAME, THEMO_URL . 'css/'.ALOHA_ELEMENTOR_CALENDAR_AND_OTHER_FORMS_FILENAME.'.css', array(), $modified2);         
        
            $modified = filemtime(THEMO_PATH . 'css/'.$this->get_name().'.css');
            wp_register_style($this->get_name(), THEMO_URL . 'css/'.$this->get_name().'.css', array(), $modified);
            return [ALOHA_ELEMENTOR_CALENDAR_AND_OTHER_FORMS_FILENAME, $this->get_name()];
        }
        
        public function get_script_depends() {
            return [];
        }
    
	protected function register_controls() {
		$this->start_controls_section(
			'section_slides',
			[
				'label' => __( 'Slides', ALOHA_DOMAIN ),
			]
		);

		$th_repeater = new Repeater();

		$th_repeater->start_controls_tabs( 'slider_repeater' );

		$th_repeater->start_controls_tab( 'slide_background', [ 'label' => __( 'Background', ALOHA_DOMAIN ) ] );

		$th_repeater->add_control(
			'slide_bg_color',
			[
				'label' => __( 'Background Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'default' => '#4A4A4A',
				'selectors' => [
					'{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .slider-bg' => 'background-color: {{VALUE}};',
				],
			]
		);

		$th_repeater->add_control(
			'slide_bg_image',
			[
				'label' => __( 'Background Image', ALOHA_DOMAIN ),
				'type' => Controls_Manager::MEDIA,
                'dynamic' => [
					'active' => true,
				],
				'selectors' => [
					'{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .slider-bg' => 'background-image: url({{URL}});',
				],
			]
		);

		$th_repeater->add_control(
            'section_bg_heading',
            [
                'label' => __( 'Image', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$th_repeater->add_responsive_control(
			'slide_bg_repeat',
			[
				'label' => __( 'Background Repeat', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'default' => 'no-repeat',
				'options' => [
					'no-repeat' => __( 'No Repeat', ALOHA_DOMAIN ),
					'repeat' => __( 'Repeat All', ALOHA_DOMAIN ),
					'repeat-x' => __( 'Repeat Horizontally', ALOHA_DOMAIN ),
					'repeat-y' => __( 'Repeat Vertically ', ALOHA_DOMAIN ),
				],
				'selectors' => [
					'{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .slider-bg' => 'background-repeat: {{VALUE}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'slide_bg_image[url]',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$th_repeater->add_responsive_control(
			'slide_bg_attachment',
			[
				'label' => __( 'Background Attachment', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'default' => 'scroll',
				'options' => [
					'fixed' => __( 'Fixed', ALOHA_DOMAIN ),
					'scroll' => __( 'Scroll', ALOHA_DOMAIN ),
				],
				'selectors' => [
					'{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .slider-bg' => 'background-attachment: {{VALUE}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'slide_bg_image[url]',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$th_repeater->add_responsive_control(
			'slide_bg_position',
			[
				'label' => __( 'Background Position', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => [
					'left top' =>  __( 'Left Top', ALOHA_DOMAIN ),
					'left center' =>  __( 'Left Center', ALOHA_DOMAIN ),
					'left bottom' =>  __( 'Left Bottom', ALOHA_DOMAIN ),
					'center top' =>  __( 'Center Top', ALOHA_DOMAIN ),
					'center center' =>  __( 'Center Center', ALOHA_DOMAIN ),
					'center bottom' =>  __( 'Center Bottom', ALOHA_DOMAIN ),
					'right top' =>  __( 'Right Top', ALOHA_DOMAIN ),
					'right center' =>  __( 'Right Center', ALOHA_DOMAIN ),
					'right bottom' =>  __( 'Right Bottom', ALOHA_DOMAIN ),
				],
				'selectors' => [
					'{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .slider-bg' => 'background-position: {{VALUE}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'slide_bg_image[url]',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$th_repeater->add_responsive_control(
			'slide_bg_size',
			[
				'label' => __( 'Background Size', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => [
					'cover' => __( 'Cover', ALOHA_DOMAIN ),
					'auto' => __( 'Auto', ALOHA_DOMAIN ),
				],
				'selectors' => [
					'{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .slider-bg' => 'background-size: {{VALUE}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'slide_bg_image[url]',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$th_repeater->add_control(
			'slide_bg_overlay',
			[
				'label' => __( 'Background Overlay', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', ALOHA_DOMAIN ),
				'label_off' => __( 'No', ALOHA_DOMAIN ),
				'return_value' => 'yes',
				'default' => '',
				'separator' => 'before',
				'conditions' => [
					'terms' => [
						[
							'name' => 'slide_bg_image[url]',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
			]
		);

		$th_repeater->add_control(
			'slide_bg_overlay_color',
			[
				'label' => __( 'Overlay Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'default' => 'rgba(0, 0, 0, 0.5)',
				'selectors' => [
					'{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .has-image-bg.th-slider-overlay' => 'background-color: {{VALUE}};',
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'slide_bg_overlay',
							'operator' => '==',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$th_repeater->end_controls_tab();

		$th_repeater->start_controls_tab( 'slide_content', [ 'label' => __( 'Content', ALOHA_DOMAIN ) ] );

		$th_repeater->add_control(
			'slide_title',
			[
				'label' => __( 'Title', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Slide Title', ALOHA_DOMAIN ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);
                $th_repeater->add_control(
                    'title_size',
                    [
                        'label' => __( 'Title HTML Tag', ALOHA_DOMAIN ),
                        'type' => Controls_Manager::SELECT,
                        'options' => [
                            'h1' => __( 'H1', ALOHA_DOMAIN ),
                            'h2' => __( 'H2', ALOHA_DOMAIN ),
                            'h3' => __( 'H3', ALOHA_DOMAIN ),
                            'h4' => __( 'H4', ALOHA_DOMAIN ),
                            'h5' => __( 'H5', ALOHA_DOMAIN ),
                            'h6' => __( 'H6', ALOHA_DOMAIN ),
                        ],
                        'default' => 'h2',
                        'separator' => 'none',
                    ]
                );
                
		$th_repeater->add_control(
			'slide_text',
			[
				'label' => __( 'Content', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'Slide Content', ALOHA_DOMAIN ),
				'show_label' => false,
				'dynamic' => [
					'active' => true,
				],
			]
		);

        $th_repeater->add_control(
            'slide_button_text_1_show',
            [
                'label' => __( 'Button 1', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'return_value' => 'yes',
                'default' => 'yes',
                'separator' => 'before',
            ]
        );



		$th_repeater->add_control(
			'slide_button_text_1',
			[
				'label' => __( 'Button 1 Text', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Button Text', ALOHA_DOMAIN ),
				'dynamic' => [
					'active' => true,
				],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'slide_button_text_1_show',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
                'separator' => 'before',
			]
		);

        $th_repeater->add_control(
            'slide_button_style_1',
            [
                'label' => __( 'Button 1 Style', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'standard-light',
                'options' => [
                    'standard-primary' => __( 'Standard Primary', ALOHA_DOMAIN ),
                    'standard-accent' => __( 'Standard Accent', ALOHA_DOMAIN ),
                    'standard-light' => __( 'Standard Light', ALOHA_DOMAIN ),
                    'standard-dark' => __( 'Standard Dark', ALOHA_DOMAIN ),
                    'ghost-primary' => __( 'Ghost Primary', ALOHA_DOMAIN ),
                    'ghost-accent' => __( 'Ghost Accent', ALOHA_DOMAIN ),
                    'ghost-light' => __( 'Ghost Light', ALOHA_DOMAIN ),
                    'ghost-dark' => __( 'Ghost Dark', ALOHA_DOMAIN ),
                    'cta-primary' => __( 'CTA Primary', ALOHA_DOMAIN ),
                    'cta-accent' => __( 'CTA Accent', ALOHA_DOMAIN ),
                ],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'slide_button_text_1_show',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $th_repeater->add_control(
            'button_1_image',
            [
                'label' => __( 'Button Graphic', ALOHA_DOMAIN ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
					'active' => true,
				],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'slide_button_text_1_show',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

		$th_repeater->add_control(
			'slide_button_link_1',
			[
				'label' => __( 'Button 1 Link', ALOHA_DOMAIN ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'http://your-link.com', ALOHA_DOMAIN ),
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'slide_button_text_1_show',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
                'dynamic' => [
                    'active' => true,
                ],
			]
		);




        $th_repeater->add_control(
            'slide_button_text_2_show',
            [
                'label' => __( 'Button 2', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'return_value' => 'yes',
                'default' => '',
                'separator' => 'before',
            ]
        );


		$th_repeater->add_control(
			'slide_button_text_2',
			[
				'label' => __( 'Button 2 Text', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'More Info', ALOHA_DOMAIN ),
				'dynamic' => [
					'active' => true,
				],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'slide_button_text_2_show',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
			]
		);

		$th_repeater->add_control(
			'slide_button_style_2',
			[
				'label' => __( 'Button 2 Style', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'default' => 'standard-light',
				'options' => [
					'standard-primary' => __( 'Standard Primary', ALOHA_DOMAIN ),
					'standard-accent' => __( 'Standard Accent', ALOHA_DOMAIN ),
					'standard-light' => __( 'Standard Light', ALOHA_DOMAIN ),
					'standard-dark' => __( 'Standard Dark', ALOHA_DOMAIN ),
					'ghost-primary' => __( 'Ghost Primary', ALOHA_DOMAIN ),
					'ghost-accent' => __( 'Ghost Accent', ALOHA_DOMAIN ),
					'ghost-light' => __( 'Ghost Light', ALOHA_DOMAIN ),
					'ghost-dark' => __( 'Ghost Dark', ALOHA_DOMAIN ),
					'cta-primary' => __( 'CTA Primary', ALOHA_DOMAIN ),
					'cta-accent' => __( 'CTA Accent', ALOHA_DOMAIN ),
				],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'slide_button_text_2_show',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
			]
		);

        $th_repeater->add_control(
            'button_2_image',
            [
                'label' => __( 'Button Graphic', ALOHA_DOMAIN ),
                'type' => Controls_Manager::MEDIA,
                'dynamic' => [
					'active' => true,
				],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'slide_button_text_2_show',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

        $th_repeater->add_control(
            'slide_button_link_2',
            [
                'label' => __( 'Button 2 Link', ALOHA_DOMAIN ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'http://your-link.com', ALOHA_DOMAIN ),
                'dynamic' => [
					'active' => true,
				],
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'slide_button_text_2_show',
                            'operator' => '==',
                            'value' => 'yes',
                        ],
                    ],
                ],
            ]
        );

		$th_repeater->add_control(
			'slide_image',
			[
				'label' => __( 'Image', ALOHA_DOMAIN ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$th_repeater->add_control(
			'slide_image_url',
			[
				'label' => __( 'Image URL', ALOHA_DOMAIN ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'http://your-link.com', ALOHA_DOMAIN ),
				'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$th_repeater->add_control(
			'slide_shortcode',
			[
				'label' => __( 'Shortcode', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
			]
		);



        $th_repeater->add_control(
            'inline_form',
            [
                'label' => __( 'Formidable Form Style', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __( 'Default', ALOHA_DOMAIN ),
                    'inline' => __( 'Inline', ALOHA_DOMAIN ),
                    'stacked' => __( 'Stacked', ALOHA_DOMAIN ),

                ],
                'dynamic' => [
					'active' => true,
				],
            ]
        );

		$th_repeater->add_control(
			'slide_shortcode_border',
			[
				'label' => __( 'Form Background', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
                    'none' => __( 'None', ALOHA_DOMAIN ),
                    'th-form-bg th-light-bg' => __( 'Light', ALOHA_DOMAIN ),
					'th-form-bg th-dark-bg' => __( 'Dark', ALOHA_DOMAIN ),

				],
                'condition' => [
                    'inline_form' => 'stacked',
                ],
                'dynamic' => [
					'active' => true,
				],
			]
		);

		$th_repeater->add_control(
			'slide_tooltip',
			[
				'label' => __( 'Calendar Tooltip', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', ALOHA_DOMAIN ),
				'label_off' => __( 'No', ALOHA_DOMAIN ),
				'return_value' => 'yes',
			]
		);

		$th_repeater->add_control(
			'slide_tooltip_text',
			[
				'label' => __( 'Tooltip Text', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'condition' => [
					'slide_tooltip' => 'yes',
				],
                'default' => __( 'Calendar Toolip', ALOHA_DOMAIN ),
                'plcaeholder' => __( 'Calendar Toolip', ALOHA_DOMAIN ),
                'dynamic' => [
					'active' => true,
				],
			]
		);

            $th_repeater->add_control(
                'th_cal_size',
                [
                    'label' => __( 'Calendar Size', ALOHA_DOMAIN ),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'small',
                    'options' => [
                        'small' => __( 'Small', ALOHA_DOMAIN ),
                    ]
                ]
            );

		$th_repeater->end_controls_tab();

		$th_repeater->start_controls_tab( 'slide_style', [ 'label' => __( 'Style', ALOHA_DOMAIN ) ] );

        $th_repeater->add_responsive_control(
            'content_max_width',
            [
                'label' => __( 'Content Width', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ '%', 'px' ],
                'default' => [
                    'size' => '100',
                    'unit' => '%',
                ],
                'selectors' => [
                    '{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .slider-bg .th-slide-content' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );


        $th_repeater->add_responsive_control(
            'slide_horizontal_position',
            [
                'label' => __( 'Horizontal Position', ALOHA_DOMAIN ),
                'type' => Controls_Manager::CHOOSE,
                'label_block' => false,
                'options' => [
                    'left' => [
                        'title' => __( 'Left', ALOHA_DOMAIN ),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __( 'Center', ALOHA_DOMAIN ),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', ALOHA_DOMAIN ),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .th-slide-content' => '{{VALUE}}',
                ],
                'selectors_dictionary' => [
                    'left' => 'margin-right: auto; margin-left:0;',
                    'center' => 'margin: 0 auto',
                    'right' => 'margin-left: auto; margin-right:0;',
                ],
                'default' => 'center',
            ]
        );

        $th_repeater->add_responsive_control(
			'slide_vertical_position',
			[
				'label' => __( 'Vertical Position', ALOHA_DOMAIN ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'top' => [
						'title' => __( 'Top', ALOHA_DOMAIN ),
						'icon' => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', ALOHA_DOMAIN ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', ALOHA_DOMAIN ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .th-slide-inner' => 'align-items: {{VALUE}}',
				],
				'selectors_dictionary' => [
					'top' => 'flex-start',
					'middle' => 'center',
					'bottom' => 'flex-end',
				],
                'default' => 'middle',
                //'prefix_class' => 'th-slide-v-position-',
			]
		);

		$th_repeater->add_control(
			'slide_text_align',
			[
				'label' => __( 'Text Align', ALOHA_DOMAIN ),
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
                'default' => 'center',
			]
		);



		$th_repeater->add_control(
			'slide_title_color',
			[
				'label' => __( 'Title Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .slider-bg .slider-title' => 'color: {{VALUE}}'
				],
//                                'default' => '#FFFFFF',
			]
		);

		$th_repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'slide_title_typo',
				'label' => __( 'Title Typography', ALOHA_DOMAIN ),
				'selector' => '{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .slider-bg .slider-title',
			]
		);

		$th_repeater->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'slide_title_shadow',
				'label'	=> 'Text Shadow',
				'selector' => '{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .slider-bg .slider-title',
			]
		);
		

		$th_repeater->add_control(
			'slide_content_color',
			[
				'label' => __( 'Content Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .slider-bg .slider-subtitle p' => 'color: {{VALUE}}',
					'{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .wpbs-legend .wpbs-legend-item p' => 'color: {{VALUE}}',
				],
                'default' => '#FFFFFF',
			]
		);

		$th_repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'slide_content_typo',
				'label' => __( 'Content Typography', ALOHA_DOMAIN ),
				'selector' => '{{WRAPPER}} #main-flex-slider {{CURRENT_ITEM}} .slider-bg .slider-subtitle p',
			]
		);

		$th_repeater->end_controls_tab();

		$th_repeater->end_controls_tabs();

		$this->add_control(
			'slides',
			[
				'label' => __( 'Slides', ALOHA_DOMAIN ),
				'type' => Controls_Manager::REPEATER,
				'show_label' => true,
                'default' => [
                    [
                        'slide_title' => __( 'In in dictum metus, nec.', ALOHA_DOMAIN ),
                        'slide_text' => __( 'Donec ultrices libero id leo tempor, nec efficitur sem auctor. Duis dictum justo a risus ultricies.', ALOHA_DOMAIN ),
                        'slide_button_text_1' => __( 'Button Text', ALOHA_DOMAIN ),
                        'slide_bg_color' => __( '#CCC', ALOHA_DOMAIN ),
                        'slide_button_style_1' => __( 'ghost-light', ALOHA_DOMAIN ),
                    ],
                    [
                        'slide_title' => __( 'Sed diam nunc, pretium vitae.', ALOHA_DOMAIN ),
                        'slide_text' => __( 'Donec ultrices libero id leo tempor, nec efficitur sem auctor. Duis dictum justo a risus ultricies.', ALOHA_DOMAIN ),
                        'slide_bg_color' => __( '#4A4A4A', ALOHA_DOMAIN ),
                        'slide_button_text_1_show' => __( 'no', ALOHA_DOMAIN ),
                        'slide_shortcode' => __( '[booked-calendar]', ALOHA_DOMAIN ),

                    ],
                    [
                        'slide_title' => __( 'In pellentesque ultricies nulla dapibus.', ALOHA_DOMAIN ),
                        'slide_text' => __( 'Donec ultrices libero id leo tempor, nec efficitur sem auctor. Duis dictum justo a risus ultricies.', ALOHA_DOMAIN ),
                        'slide_bg_color' => __( '#7A85E8', ALOHA_DOMAIN ),
                        'inline_form' => __( 'inline', ALOHA_DOMAIN ),
                        'slide_button_text_1_show' => __( 'no', ALOHA_DOMAIN ),
                        'slide_shortcode' => __( '[formidable id="2"]', ALOHA_DOMAIN ),
                    ],

                ],
				'fields' => $th_repeater->get_controls(),
				'title_field' => '{{{ slide_title }}}',
			]
		);

		$this->add_responsive_control(
			'slides_height',
			[
				'label' => __( 'Height', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 1000,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 600,
					'unit' => 'px',
				],
				'size_units' => [ 'px', 'vh', 'em' ],
				'selectors' => [
					'{{WRAPPER}} #main-flex-slider .slider-bg' => 'min-height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
				'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$this->add_control(
			'slides_down_arrow',
			[
				'label' => __( 'Down Arrow', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', ALOHA_DOMAIN ),
				'label_off' => __( 'No', ALOHA_DOMAIN ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'slides_down_arrow_link',
			[
				'label' => __( 'Down Arrow URL anchor', ALOHA_DOMAIN ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( '#prices', ALOHA_DOMAIN ),
				'condition' => [
					'slides_down_arrow' => 'yes',
				],
				'dynamic' => [
                    'active' => true,
                ],
			]
		);

        $this->add_control(
            'slide_down_arrow_color',
            [
                'label' => __( 'Down Arrow Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} #main-flex-slider a.slider-scroll-down' => 'color: {{VALUE}}'
                ],
                'default' => '#FFFFFF',
                'condition' => [
                    'slides_down_arrow' => 'yes',
                ],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_slider_options',
			[
				'label' => __( 'Slider Options', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SECTION,
			]
		);

        $this->add_control(
            'autoplay',
            [
                'label' => __( 'Auto play', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'On', ALOHA_DOMAIN ),
                'label_off' => __( 'Off', ALOHA_DOMAIN ),
                'return_value' => 'On',
                'description' => __( 'Start slider automatically', ALOHA_DOMAIN ),
            ]
        );

		$this->add_control(
			'th_animation',
			[
				'label' => __( 'Transition Style', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'fade' => __( 'Fade', ALOHA_DOMAIN ),
					'slide' => __( 'Slide', ALOHA_DOMAIN ),
				],
				'description' => __( 'Controls the transition style, "fade" or "slide"', ALOHA_DOMAIN ),
			]
		);

		$this->add_control(
			'easing',
			[
				'label' => __( 'Easing', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'default' => 'swing',
				'options' => [
					'swing' => __( 'Swing', ALOHA_DOMAIN ),
					'linear' => __( 'Linear', ALOHA_DOMAIN ),
				],
				'description' => __( 'Determines the easing method used in jQuery transitions', ALOHA_DOMAIN ),
			]
		);

		$this->add_control(
			'animation_loop',
			[
				'label' => __( 'Infinite Loop', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', ALOHA_DOMAIN ),
				'label_off' => __( 'Off', ALOHA_DOMAIN ),
				'return_value' => 'On',
				'default' => 'On',
				'description' => __( 'Gives the slider a seamless infinite loop', ALOHA_DOMAIN ),
			]
		);

		$this->add_control(
			'smooth_height',
			[
				'label' => __( 'Smooth Height', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', ALOHA_DOMAIN ),
				'label_off' => __( 'Off', ALOHA_DOMAIN ),
				'return_value' => 'On',
				'default' => 'On',
				'description' => __( 'Animate the height of the slider smoothly for slides of varying height', ALOHA_DOMAIN ),
			]
		);

		$this->add_control(
			'slideshow_speed',
			[
				'label' => __( 'Slideshow Speed', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 4000,
                ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 15000,
					],
				],
				'description' => __( 'Set the speed of the slideshow cycling, in milliseconds (1 s = 1000 ms)', ALOHA_DOMAIN ),
				'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$this->add_control(
			'animation_speed',
			[
				'label' => __( 'Animation Speed', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 550,
                ],
                'range' => [
					'px' => [
						'min' => 0,
						'max' => 1200,
					],
				],
				'description' => __( 'Set the speed of animations, in milliseconds (1 s = 1000 ms)', ALOHA_DOMAIN ),
				'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$this->add_control(
			'randomize',
			[
				'label' => __( 'Randomize', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', ALOHA_DOMAIN ),
				'label_off' => __( 'Off', ALOHA_DOMAIN ),
				'return_value' => 'On',
				'description' => __( 'Randomize slide order, on load', ALOHA_DOMAIN ),
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label' => __( 'Pause On Hover', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', ALOHA_DOMAIN ),
				'label_off' => __( 'Off', ALOHA_DOMAIN ),
				'return_value' => 'On',
				'default' => 'On',
				'description' => __( 'Pause the slideshow when hovering over slider, then resume when no longer hovering', ALOHA_DOMAIN ),
			]
		);

		$this->add_control(
			'touch',
			[
				'label' => __( 'Touch', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', ALOHA_DOMAIN ),
				'label_off' => __( 'Off', ALOHA_DOMAIN ),
				'return_value' => 'On',
				'default' => 'On',
				'description' => __( 'Allow touch swipe navigation of the slider on enabled devices', ALOHA_DOMAIN ),
			]
		);

		$this->add_control(
			'direction',
			[
				'label' => __( 'Direction Nav', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', ALOHA_DOMAIN ),
				'label_off' => __( 'Off', ALOHA_DOMAIN ),
				'return_value' => 'On',
				'default' => 'On',
				'description' => __( 'Create previous/next arrow navigation', ALOHA_DOMAIN ),
			]
		);

		$this->add_control(
			'paging',
			[
				'label' => __( 'Paging Control', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', ALOHA_DOMAIN ),
				'label_off' => __( 'Off', ALOHA_DOMAIN ),
				'return_value' => 'On',
				'default' => 'On',
				'description' => __( 'Create navigation for paging control of each slide', ALOHA_DOMAIN ),
			]
		);

		$this->end_controls_section();



	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['slides'] ) ) {
			return;
		}

		$this->add_render_attribute( 'slider-bg', 'class', 'slider-bg' );
		$this->add_render_attribute( 'slider-bg', 'class', 'slide-cal-center' );

		$init_main_loop = 0;
		?>

		<div id="main-flex-slider" class="flexslider">
			<ul class="slides">
				<?php $th_counter=0; foreach( $settings['slides'] as $slide ) { ?>

                    <?php ++$th_counter; ?>

                    <?php

                    // Graphic Button 1
                    $button_1_image = false;
                    if ( isset( $slide['button_1_image']['id'] ) && $slide['button_1_image']['id'] > "" ) {
                        $button_1_image = wp_get_attachment_image( $slide['button_1_image']['id'], "th_img_xs", false, array( 'class' => '' ) );
                    }elseif ( ! empty( $slide['button_1_image']['url'] ) ) {
                        $this->add_render_attribute( 'button_1_image-'.$th_counter, 'src', esc_url( $slide['button_1_image']['url'] ) );
                        $this->add_render_attribute( 'button_1_image-'.$th_counter, 'alt', esc_attr( Control_Media::get_image_alt( $slide['button_1_image'] ) ) );
                        $this->add_render_attribute( 'button_1_image-'.$th_counter, 'title', esc_attr( Control_Media::get_image_title( $slide['button_1_image'] ) ) );
                        $button_1_image = '<img ' . $this->get_render_attribute_string( 'button_1_image'.$th_counter ) . '>';
                    }
                    // Graphic Button URL Styling 1
                    if ( isset($button_1_image) && ! empty( $button_1_image ) ) {
                        // image button
                        $this->add_render_attribute( 'btn-1-link-'.$th_counter, 'class', 'btn-1' );
                        $this->add_render_attribute( 'btn-1-link-'.$th_counter, 'class', 'th-btn' );
                        $this->add_render_attribute( 'btn-1-link-'.$th_counter, 'class', 'btn-image' );
                    }else{ // Bootstrap Button URL Styling
                        $this->add_render_attribute( 'btn-1-link-'.$th_counter, 'class', 'btn-1' );
                        $this->add_render_attribute( 'btn-1-link-'.$th_counter, 'class', 'btn' );
                        $this->add_render_attribute( 'btn-1-link-'.$th_counter, 'class', 'th-btn' );
                        $this->add_render_attribute( 'btn-1-link-'.$th_counter, 'class', 'btn-' . esc_attr( $slide['slide_button_style_1'] ) );
                    }

                    // Button URL 1
                    if ( empty( $slide['slide_button_link_1']['url'] ) ) { $slide['slide_button_link_1']['url'] = '#'; };

                    if ( ! empty( $slide['slide_button_link_1']['url'] ) ) {
                        $this->add_render_attribute( 'btn-1-link-'.$th_counter, 'href', esc_url( $slide['slide_button_link_1']['url'] ) );

                        if ( ! empty( $slide['slide_button_link_1']['is_external'] ) ) {
                            $this->add_render_attribute( 'btn-1-link-'.$th_counter, 'target', '_blank' );
                        }
                    }

                    // Graphic Button 2
                    $button_2_image = false;
                    if ( isset( $slide['button_2_image']['id'] ) && $slide['button_2_image']['id'] > "" ) {
                        $button_2_image = wp_get_attachment_image( $slide['button_2_image']['id'], "th_img_xs", false, array( 'class' => '' ) );
                    }elseif ( ! empty( $slide['button_2_image']['url'] ) ) {
                        $this->add_render_attribute( 'button_2_image-'.$th_counter, 'src', esc_url( $slide['button_2_image']['url'] ) );
                        $this->add_render_attribute( 'button_2_image-'.$th_counter, 'alt', esc_attr( Control_Media::get_image_alt( $slide['button_2_image'] ) ) );
                        $this->add_render_attribute( 'button_2_image-'.$th_counter, 'title', esc_attr( Control_Media::get_image_title( $slide['button_2_image'] ) ) );
                        $button_2_image = '<img ' . $this->get_render_attribute_string( 'button_2_image-'.$th_counter ) . '>';
                    }
                    // Graphic Button URL Styling 2
                    if ( isset($button_2_image) && ! empty( $button_2_image ) ) {
                        // image button
                        $this->add_render_attribute( 'btn-2-link-'.$th_counter, 'class', 'btn-2' );
                        $this->add_render_attribute( 'btn-2-link-'.$th_counter, 'class', 'th-btn' );
                        $this->add_render_attribute( 'btn-2-link-'.$th_counter, 'class', 'btn-image' );
                    }else{ // Bootstrap Button URL Styling
                        $this->add_render_attribute( 'btn-2-link-'.$th_counter, 'class', 'btn-2' );
                        $this->add_render_attribute( 'btn-2-link-'.$th_counter, 'class', 'btn' );
                        $this->add_render_attribute( 'btn-2-link-'.$th_counter, 'class', 'th-btn' );
                        $this->add_render_attribute( 'btn-2-link-'.$th_counter, 'class', 'btn-' . esc_attr( $slide['slide_button_style_2'] ) );
                    }

                    // Button URL 2
                    if ( empty( $slide['slide_button_link_2']['url'] ) ) { $slide['slide_button_link_2']['url'] = '#'; };

                    if ( ! empty( $slide['slide_button_link_2']['url'] ) ) {
                        $this->add_render_attribute( 'btn-2-link-'.$th_counter, 'href', esc_url( $slide['slide_button_link_2']['url'] ) );

                        if ( ! empty( $slide['slide_button_link_2']['is_external'] ) ) {
                            $this->add_render_attribute( 'btn-2-link-'.$th_counter, 'target', '_blank' );
                        }
                    }

                    ?>

					<?php if ( ! empty( $settings['button_size_1'] ) ) {
						$this->add_render_attribute( 'th-button-1', 'class', 'th-button-size-' . esc_attr( $settings['button_size_1'] ) );
					} ?>
					<?php if ( ! empty( $settings['button_size_2'] ) ) {
						$this->add_render_attribute( 'th-button-2', 'class', 'th-button-size-' . esc_attr( $settings['button_size_2'] ) );
					} ?>
                    <?php

                    $th_form_border_class = false;
                    $th_formidable_class = 'th-form-default';
                    if ( isset( $slide['inline_form'] ) && $slide['inline_form'] > "" ) :
                        switch ( $slide['inline_form'] ) {
                            case 'stacked':
                                $th_formidable_class = 'th-form-stacked';
                                if ( isset( $slide['slide_shortcode_border'] ) && $slide['slide_shortcode_border'] != 'none' ){
                                    $th_form_border_class = $slide['slide_shortcode_border'];
                                }
                                break;
                            case 'inline':
                                $th_formidable_class = 'th-conversion ';
                                break;
                        }
                    endif;

					$this->add_render_attribute( 'slider-bg', 'class', esc_attr( $th_form_border_class ) );
                    $this->add_render_attribute( 'slider-bg-overlay', 'class', 'th-slide-wrap' );

					if ( 'yes' === $slide['slide_bg_overlay'] ) {
                        $this->add_render_attribute( 'slider-bg-overlay', 'class', 'th-slider-overlay' );
                    }

                    if ($slide['slide_bg_image']['url'] ) {
                        $this->add_render_attribute( 'slider-bg-overlay', 'class', 'has-image-bg' );
                    }

                    $th_cal_align_class = false;
                    if ( isset( $slide['slide_text_align'] ) && $slide['slide_text_align'] > "" ) {
                        switch ( $slide['slide_text_align'] ) {
                            case 'left':
                                $th_cal_align_class =  ' th-left';
                                break;
                            case 'center':
                                $th_cal_align_class = ' th-centered';
                                break;
                            case 'right':
                                $th_cal_align_class = ' th-right';
                                break;
                        }
                    }
                    ?>

                    <li class="elementor-repeater-item-<?php echo esc_attr( $slide['_id'] ); ?>">
						<div <?php echo $this->get_render_attribute_string( 'slider-bg' ); ?>>
                            <div <?php echo $this->get_render_attribute_string( 'slider-bg-overlay' );?>>
                                <div class="th-slide-inner <?php echo esc_attr( $th_cal_align_class ); ?>">
                                    <div class="th-slide-content">
                                        <?php if ( ! empty( $slide['slide_title'] ) ) : ?>
                                            <<?php echo esc_attr($slide['title_size']); ?> class="slider-title"><?php echo esc_html( $slide['slide_title'] ) ?></<?php echo esc_attr($slide['title_size']); ?>>
                                        <?php endif;?>

                                        <?php if ( ! empty( $slide['slide_text'] ) ) : ?>
                                            <div class="slider-subtitle">
                                                <p><?php echo wp_kses_post( $slide['slide_text']) ?></p>
                                            </div>
                                        <?php endif;?>
                                        <?php if ( ! empty( $slide['slide_button_text_1'] ) || ! empty( $slide['slide_button_text_2'] ) || ! empty($button_1_image) || ! empty( $button_2_image )) : ?>
                                            <div class="th-btn-wrap">
                                                <?php if ( isset( $slide['slide_button_text_1_show'] ) && $slide['slide_button_text_1_show'] == 'yes' ) : ?>
                                                    <?php if ( isset($button_1_image) && ! empty( $button_1_image ) ) : ?>
                                                        <?php if ( ! empty( $slide['slide_button_link_1']['url'] ) ) : ?>
                                                            <a <?php echo $this->get_render_attribute_string( 'btn-1-link-'.$th_counter ); ?>>
                                                                <?php echo wp_kses_post( $button_1_image ); ?>
                                                            </a>
                                                        <?php else : ?>
                                                            <?php echo wp_kses_post( $button_1_image ); ?>
                                                        <?php endif; ?>
                                                    <?php elseif ( ! empty( $slide['slide_button_text_1'] ) ) : ?>
                                                        <a <?php echo $this->get_render_attribute_string( 'btn-1-link-'.$th_counter ); ?>>
                                                            <?php if ( ! empty( $slide['slide_button_text_1'] ) ) : ?>
                                                                <?php echo esc_html( $slide['slide_button_text_1'] ); ?>
                                                            <?php endif; ?>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if ( isset( $slide['slide_button_text_2_show'] ) && $slide['slide_button_text_2_show'] == 'yes' ) : ?>
                                                    <?php if ( isset($button_2_image) && ! empty( $button_2_image ) ) : ?>
                                                        <?php if ( ! empty( $slide['slide_button_link_2']['url'] ) ) : ?>
                                                            <a <?php echo $this->get_render_attribute_string( 'btn-2-link-'.$th_counter ); ?>>
                                                                <?php echo wp_kses_post( $button_2_image ); ?>
                                                            </a>
                                                        <?php else : ?>
                                                            <?php echo wp_kses_post( $button_2_image ); ?>
                                                        <?php endif; ?>
                                                    <?php elseif ( ! empty( $slide['slide_button_text_2'] ) ) : ?>
                                                        <a <?php echo $this->get_render_attribute_string( 'btn-2-link-'.$th_counter ); ?>>
                                                            <?php if ( ! empty( $slide['slide_button_text_2'] ) ) : ?>
                                                                <?php echo esc_html( $slide['slide_button_text_2'] ); ?>
                                                            <?php endif; ?>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ( $slide['slide_image']['id'] ) : ?>
                                            <div class="page-title-image ">
                                                <?php if ( ! empty( $slide['slide_image_url']['url'] ) ) : ?>
                                                    <?php $img_target = $slide['slide_image_url']['is_external'] ? ' target="_blank"' : ''; ?>
                                                    <?php echo '<a href="' . esc_url( $slide['slide_image_url']['url'] ) . '"' . wp_kses_post( $img_target ) . '>'; ?>
                                                <?php endif; ?>
                                                <?php echo wp_kses_post(wp_get_attachment_image( $slide['slide_image']['id'], 'large', false, array( 'class' => 'hero wp-post-image' ) )); ?>
                                                <?php if ( ! empty( $slide['slide_image_url']['url'] ) ) : ?>
                                                    <?php echo '</a>'; ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ( isset( $slide['slide_shortcode'] ) ) : ?>
                                            <?php $sth_show_tooltip = $slide['slide_tooltip'] == 'yes' ? true : false; ?>
                                            <?php $th_tooltip = $slide['slide_tooltip_text'] ? $slide['slide_tooltip_text'] : ''; ?>
                                            <?php $themo_flex_smoothheight = strpos( $slide['slide_shortcode'], 'booked-calendar' ) !== FALSE ? false : true; ?>

                                            <?php
                                                $th_shortcode = sanitize_text_field( $slide['slide_shortcode'] );
                                                $th_brackets = array( "[","]" );
                                                $th_shortcode_text = str_replace( $th_brackets, "", $th_shortcode );
                                                $th_shortcode_name = strtok( $th_shortcode_text,  ' ' );
                                                $th_cal_size =  ( isset( $slide['th_cal_size'] ) ? $slide['th_cal_size'] : false );
                                                $th_output = "";

                                                switch ( $th_shortcode_name ) {
                                                    case 'formidable':
                                                        $th_output .= '<div class="' . sanitize_html_class( $th_formidable_class ) . '">';
                                                        $th_output .= do_shortcode( $th_shortcode );
                                                        $th_output .= '</div>';
                                                        break;
                                                    case 'booked-calendar':
                                                        $th_output .= '<div class="th-book-cal-' . esc_attr( $th_cal_size ) . esc_attr( $th_cal_align_class ) .'">';
                                                        if( $sth_show_tooltip ){
                                                            $th_output .= '<div class="th-cal-tooltip">';
                                                            $th_output .= '<h3>' . esc_html( $th_tooltip ) . '</h3>';
                                                            $th_output .= '</div>';
                                                        }
                                                        $th_output .= do_shortcode( $th_shortcode );
                                                        $th_output .= '</div>';
                                                        break;
                                                    case 'wpbs':
                                                        $th_output .= '<div class="th-book-cal-' . esc_attr( $th_cal_size ) . esc_attr( $th_cal_align_class ) .'">';
                                                        if( $sth_show_tooltip ){
                                                            $th_output .= '<div class="th-cal-tooltip">';
                                                            $th_output .= '<h3>' . esc_html( $th_tooltip ) . '</h3>';
                                                            $th_output .= '</div>';
                                                        }
                                                        $th_output .= do_shortcode( $th_shortcode );
                                                        $th_output .= '</div>';
                                                        break;
                                                    default:
                                                        $th_output .= '<div>';
                                                        $th_output .= do_shortcode( $th_shortcode );
                                                        $th_output .= '</div>';
                                                }
                                                echo $th_output;
                                            ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
							</div>
						</div>
					</li>
				<?php } ?>
			</ul>

			<?php if ( $settings['slides_down_arrow'] == 'yes' && $settings['slides_down_arrow_link']['url'] ) : ?>
				<?php $down_target = $settings['slides_down_arrow_link']['is_external'] ? 'target="_blank"' : 'target="_self"'; ?>
				<a href="<?php echo esc_url( $settings['slides_down_arrow_link']['url'] ) ?>" <?php echo wp_kses_post( $down_target ) ?> class="slider-scroll-down th-icon th-i-down"></a>
			<?php endif; ?>
		</div>

		<script>
			jQuery( function ( $ ) {

				themo_start_flex_slider(
					'.flexslider',
                    <?php echo esc_attr( $settings['autoplay'] ) ? 'true' : 'false'; ?>,
					'<?php echo esc_attr( $settings['th_animation'] ); ?>',
					'<?php echo esc_attr( $settings['easing'] ); ?>',
					<?php echo esc_attr( $settings['animation_loop'] ) ? 'true' : 'false'; ?>,
					<?php echo esc_attr( $settings['smooth_height'] ) ? 'true' : 'false'; ?>,
					<?php echo ! esc_attr( $settings['slideshow_speed']['size'] ) ? '0' : $settings['slideshow_speed']['size']; ?>,
					<?php echo ! esc_attr( $settings['animation_speed']['size'] ) ? '0' : $settings['animation_speed']['size']; ?>,
					<?php echo esc_attr( $settings['randomize'] ) ? 'true' : 'false'; ?>,
					<?php echo esc_attr( $settings['pause_on_hover'] ) ? 'true' : 'false'; ?>,
					<?php echo esc_attr( $settings['touch'] ) ? 'true' : 'false'; ?>,
					<?php echo esc_attr( $settings['direction'] ) ? 'true' : 'false'; ?>,
					<?php echo esc_attr( $settings['paging'] ) ? 'true' : 'false'; ?>
				);
			} );
		</script>
		<?php
	}

	protected function content_template() {}

	
}

Plugin::instance()->widgets_manager->register( new Themo_Widget_Slider() );
