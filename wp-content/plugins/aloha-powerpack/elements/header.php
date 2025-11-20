<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_Header extends Widget_Base {

	public function get_name() {
		return 'themo-header';
	}

	public function get_title() {
		return __( 'Header', ALOHA_DOMAIN );
	}

	public function get_icon() {
		return 'th-editor-icon-page-title';
	}

	public function get_categories() {
		return [ 'themo-elements' ];
	}
        
        public function get_script_depends() {
            return [];
        }
    
    public function get_style_depends() {
        $modified = filemtime(THEMO_PATH . 'css/'.$this->get_name().'.css');
        wp_register_style($this->get_name(), THEMO_URL . 'css/'.$this->get_name().'.css', array(), $modified);
        return [$this->get_name()];
    }    
    public function get_help_url() {
        return ALOHA_WIDGETS_HELP_URL_PREFIX . $this->get_name();
    }
    
	protected function register_controls() {

        $this->start_controls_section(
            'section_align',
            [
                'label' => __( 'Position', ALOHA_DOMAIN ),
            ]
        );

        $this->add_responsive_control(
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
                    '{{WRAPPER}} .th-header-wrap' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_responsive_control(
            'header_horizontal_position',
            [
                'label' => __( 'Horizontal Position', ALOHA_DOMAIN ),
                'type' => Controls_Manager::CHOOSE,
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
                    '{{WRAPPER}} .th-header-wrap' => '{{VALUE}}',
                ],
                'selectors_dictionary' => [
                    'left' => 'margin-right: auto; margin-left:0;',
                    'center' => 'margin: 0 auto',
                    'right' => 'margin-left: auto; margin-right:0;',
                ],
                'default' => 'center',
            ]
        );

        $this->add_responsive_control(
            'text_align',
            [
                'label' => __( 'Content Alignment', ALOHA_DOMAIN ),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'center',
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
                    '{{WRAPPER}} .th-header-wrap .elementor-icon-box-wrapper' => 'text-align: {{VALUE}};',
                ],
            ]
        );


        $this->end_controls_section();

		$this->start_controls_section(
			'section_icon',
			[
				'label' => __( 'Icon', ALOHA_DOMAIN ),
			]
		);

        $this->add_control(
            'new_icon',
            [
                'label' => __( 'Choose Icon', ALOHA_DOMAIN ),
                'fa4compatibility' => 'icon',
                'type' => Controls_Manager::ICONS,
                'label_block' => true,
                /*'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],*/
            ]
        );		


        $this->add_control(
            'view',
            [
                'label' => __( 'Style', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'default' => __( 'Default', ALOHA_DOMAIN ),
                    'stacked' => __( 'Filled', ALOHA_DOMAIN ),
                    'framed' => __( 'Framed', ALOHA_DOMAIN ),
                ],
                'default' => 'default',
                'prefix_class' => 'elementor-view-',
            ]

        );

        $this->add_control(
            'shape',
            [
                'label' => __( 'Shape', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'circle' => __( 'Circle', ALOHA_DOMAIN ),
                    'square' => __( 'Square', ALOHA_DOMAIN ),
                ],
                'default' => 'circle',
                'condition' => [
                    'view!' => 'default',
                ],
                'prefix_class' => 'elementor-shape-',
            ]
        );

        $this->add_control(
            'icon_size',
            [
                'label' => __( 'Icon Size', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'sm' => __( 'Small', ALOHA_DOMAIN ),
                    'md' => __( 'Medium', ALOHA_DOMAIN ),
                    'lg' => __( 'Large', ALOHA_DOMAIN ),
                    'xl' => __( 'Extra Large', ALOHA_DOMAIN ),
                ],
                'default' => 'lg',
            ]
        );

        $this->add_responsive_control(
            'position',
            [
                'label' => __( 'Position', ALOHA_DOMAIN ),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'top',
                'options' => [
                    'left' => [
                        'title' => __( 'Left', ALOHA_DOMAIN ),
                        'icon' => 'fa fa-align-left',
                    ],
                    'top' => [
                        'title' => __( 'Top', ALOHA_DOMAIN ),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __( 'Right', ALOHA_DOMAIN ),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'prefix_class' => 'elementor-position%s-',
                'toggle' => true,
            ]
        );
        
        $this->end_controls_section();

        $this->start_controls_section(
            'section_title',
            [
                'label' => __( 'Title & Description', ALOHA_DOMAIN ),
            ]
        );

        $this->add_control(
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
                'default' => 'h1',
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'title_divider',
            [
                'label' => __( 'Title Divider', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __( 'Show', ALOHA_DOMAIN ),
                'label_off' => __( 'Hide', ALOHA_DOMAIN ),
                'return_value' => 'yes',
                /*'condition' => [
                    'title_size' => 'h2',
                ],*/
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'title_text',
            [
                'label' => __( 'Title', ALOHA_DOMAIN ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Title Text', ALOHA_DOMAIN ),
                'placeholder' => __( 'Title Text', ALOHA_DOMAIN ),
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'description_text',
            [
                'label' => __( 'Description', ALOHA_DOMAIN ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __( 'Nulla eget tortor ac ipsum gravida sollicitudin vel aliquet ligula. Phasellus vitae nisi at risus euismod.', ALOHA_DOMAIN ),
                'placeholder' => __( 'Your Description', ALOHA_DOMAIN ),
                'title' => __( 'Input icon text here', ALOHA_DOMAIN ),
                'rows' => 10,
                'separator' => 'none',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );




        $this->add_responsive_control(
            'description_align',
            [
                'label' => __( 'Description Alignment', ALOHA_DOMAIN ),
                'type' => Controls_Manager::CHOOSE,
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
                'separator' => 'none',
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon-box-description' => 'text-align: {{VALUE}};',
                ],
            ]
        );



        $this->end_controls_section();

        $this->start_controls_section(
            'section_buttons',
            [
                'label' => __( 'Buttons', ALOHA_DOMAIN ),
            ]
        );

        $this->add_control(
            'button_1_heading',
            [
                'label' => __( 'Button 1', ALOHA_DOMAIN ),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'button_1_text',
            [
                'label' => __( 'Button Text', ALOHA_DOMAIN ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __( 'Button Text', ALOHA_DOMAIN ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'button_1_style',
            [
                'label' => __( 'Button Style', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'standard-primary',
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
            ]
        );


        $this->add_control(
            'button_1_image',
            [
                'label' => __( 'Button Graphic', ALOHA_DOMAIN ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    //'url' => Utils::get_placeholder_image_src(),
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'button_1_link',
            [
                'label' => __( 'Link', ALOHA_DOMAIN ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( '#buttonlink', ALOHA_DOMAIN ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );


        $this->add_control(
            'button_2_heading',
            [
                'label' => __( 'Button 2', ALOHA_DOMAIN ),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'button_2_text',
            [
                'label' => __( 'Button Text', ALOHA_DOMAIN ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __( 'Button Text', ALOHA_DOMAIN ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'button_2_style',
            [
                'label' => __( 'Button Style', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'standard-primary',
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
            ]
        );

        $this->add_control(
            'button_2_image',
            [
                'label' => __( 'Button Graphic', ALOHA_DOMAIN ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    //'url' => Utils::get_placeholder_image_src(),
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'button_2_link',
            [
                'label' => __( 'Link', ALOHA_DOMAIN ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( '#buttonlink', ALOHA_DOMAIN ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_responsive_control(
            'button_align',
            [
                'label' => __( 'Alignment Override', ALOHA_DOMAIN ),
                'type' => Controls_Manager::CHOOSE,
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
                'separator' => 'none',
                'selectors' => [
                    '{{WRAPPER}} .th-btn-wrap' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
			'section_style_icon',
			[
				'label' => __( 'Icon', ALOHA_DOMAIN ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'primary_color',
			[
				'label' => __( 'Primary Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
                    '{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-view-framed .elementor-icon, {{WRAPPER}}.elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                    '{{WRAPPER}}.elementor-view-framed .elementor-icon svg, {{WRAPPER}}.elementor-view-default .elementor-icon svg' => 'fill: {{VALUE}}',
				],
			]
		);


		$this->add_control(
			'secondary_color',
			[
				'label' => __( 'Secondary Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'condition' => [
					'view!' => 'default',
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-view-framed .elementor-icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}}.elementor-view-stacked .elementor-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);




        $this->add_control(
            'svg_title',
            [
                'label' => __( 'SVG Paths', ALOHA_DOMAIN ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'icon_primary_path',
            [
                'label' => __( 'Primary Path', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.elementor-view-framed .elementor-icon, {{WRAPPER}}.elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                    '{{WRAPPER}}.elementor-view-framed .elementor-icon svg path, {{WRAPPER}}.elementor-view-default .elementor-icon svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_secondary_path',
            [
                'label' => __( 'Secondary Path', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'condition' => [
                    'view!' => 'default',
                ],
                'selectors' => [
                    '{{WRAPPER}}.elementor-view-framed .elementor-icon' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.elementor-view-stacked .elementor-icon svg path' => 'fill: {{VALUE}};',
                ],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label' => __( 'Content', ALOHA_DOMAIN ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'heading_title',
			[
				'label' => __( 'Title', ALOHA_DOMAIN ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
            'title_color',
            [
                'label' => __( 'Title Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-title' => 'color: {{VALUE}};',
                ],
            ]
        );



		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-title',

			]
		);

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_shadow',
                'selector' => '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-title',
            ]
        );

        $this->add_control(
            'thmv_section_divider_heading',
            [
                'label' => __('Divider', 'elementor'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'title_divider' => 'yes'
                ],
            ]
        );


        $this->add_control(
            'divider_color',
            [
                'label' => __( 'Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .th-header-wrap .th-header-divider' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'title_divider' => 'yes',
                ],
            ]
        );
        $this->add_responsive_control(
            'thmv_divider_size',
            [
                'label' => __('Width', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 1140,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .th-header-wrap .th-header-divider' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'title_divider' => 'yes'
                ],
            ]
        );

        $this->add_responsive_control(
            'thmv_divider_height',
            [
                'label' => __('Height', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                        'step' => 1,
                    ],

                ],
                'selectors' => [
                    '{{WRAPPER}} .th-header-wrap .th-header-divider' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'title_divider' => 'yes'
                ],
            ]
        );

        $this->add_responsive_control(
            'thmv_divider_radius',
            [
                'label' => __('Corner Radius', 'elementor'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .th-header-wrap .th-header-divider' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'thmv_divider_spacing',
            [
                'label' => __('Spacing', 'elementor'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .th-header-wrap .th-header-divider' => 'margin-top: {{SIZE}}{{UNIT}}; margin-bottom: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'title_divider' => 'yes'
                ],
            ]
        );
		$this->add_control(
			'heading_description',
			[
				'label' => __( 'Description', ALOHA_DOMAIN ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
                    '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-description' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-description a' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-description',

            ]
        );

        $this->add_control(
            'heading_span',
            [
                'label' => __('Span', ALOHA_DOMAIN),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
                'description' => 'HELLO this is some text.'
            ]
        );
        $this->add_control(
            'span_note',
            [
                //'label' => __( 'Note', ALOHA_DOMAIN ),
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __( '<p style="line-height: 17px;">Span tags can be added to Title and Description for additional styling. E.G.: My title has &lt;span&gt;blue text&lt;/span&gt;.</p>', ALOHA_DOMAIN ),
                'content_classes' => 'themo-elem-html-control',
            ]
        );
        $this->add_control(
            'span_color',
            [
                'label' => __('Color', ALOHA_DOMAIN),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-title span' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-description span' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-description a span' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'span_typography',
                'selector' => '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-title span, 
                {{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-description span,
                {{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-description span a',

            ]
        );

        $this->add_control(
            'section_button_1_heading',
            [
                'label' => __( 'Button 1', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'button_1_text_color',
            [
                'label' => __( 'Text Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .th-btn-wrap .btn-1' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __( 'Typography', 'elementor' ),
                'name' => 'section_button_1_typography',
                'selector' => '{{WRAPPER}} .th-btn-wrap .btn-1',
            ]
        );

        $this->add_responsive_control(
            'section_button_1_padding',
            [
                'label' => __( 'Padding', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .th-btn-wrap .btn-1' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'section_button_2_heading',
            [
                'label' => __( 'Button 2', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'button_2_text_color',
            [
                'label' => __( 'Text Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .th-btn-wrap .btn-2' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __( 'Typography', 'elementor' ),
                'name' => 'section_button_2_typography',
                'selector' => '{{WRAPPER}} .th-btn-wrap .btn-2',
            ]
        );

        $this->add_responsive_control(
            'section_button_2_padding',
            [
                'label' => __( 'Padding', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .th-btn-wrap .btn-2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

	$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

        $elm_animation = false;
        if ( ! empty( $settings['hover_animation'] ) ) {
            $elm_animation = 'elementor-animation-' . esc_attr( $settings['hover_animation'] );
        }
        $this->add_render_attribute( 'icon', 'class', ['elementor-icon', $elm_animation] );

		$icon_tag = 'span';

		/*if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_render_attribute( 'link', 'href', esc_url( $settings['link']['url'] ) );
			$icon_tag = 'a';

			if ( ! empty( $settings['link']['is_external'] ) ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
			}
		}*/

        $this->add_render_attribute( 'th-icon-size', 'class', 'elementor-icon-box-icon' );
        $this->add_render_attribute( 'th-icon-size', 'class', 'th-icon-size-' . esc_attr( $settings['icon_size'] ) );

		$icon_attributes = $this->get_render_attribute_string( 'icon' );
		//$link_attributes = $this->get_render_attribute_string( 'link' );

        // BUTTON 1

        // Graphic Button
        $button_1_image = false;
        if ( isset( $settings['button_1_image']['id'] ) && $settings['button_1_image']['id'] > "" ) {
            $button_1_image = wp_get_attachment_image( $settings['button_1_image']['id'], "th_img_xs", false, array( 'class' => '' ) );
        }elseif ( ! empty( $settings['button_1_image']['url'] ) ) {
            $this->add_render_attribute( 'button_1_image', 'src', esc_url( $settings['button_1_image']['url'] ) );
            $this->add_render_attribute( 'button_1_image', 'alt', esc_attr( Control_Media::get_image_alt( $settings['button_1_image'] ) ) );
            $this->add_render_attribute( 'button_1_image', 'title', esc_attr( Control_Media::get_image_title( $settings['button_1_image'] ) ) );
            $button_1_image = '<img ' . $this->get_render_attribute_string( 'button_1_image' ) . '>';
        }

        // Graphic Button URL Styling
        if ( isset($button_1_image) && ! empty( $button_1_image ) ) {
            // image button
            $this->add_render_attribute( 'btn-1-link', 'class', 'btn-1' );
            $this->add_render_attribute( 'btn-1-link', 'class', 'th-btn' );
            $this->add_render_attribute( 'btn-1-link', 'class', 'btn-image' );
        }else{ // Bootstrap Button URL Styling
            $this->add_render_attribute( 'btn-1-link', 'class', 'btn-1' );
            $this->add_render_attribute( 'btn-1-link', 'class', 'btn' );
            $this->add_render_attribute( 'btn-1-link', 'class', 'th-btn' );
            $this->add_render_attribute( 'btn-1-link', 'class', 'btn-' . esc_attr( $settings['button_1_style'] ) );
        }

        // Button URL
        if ( empty( $settings['button_1_link']['url'] ) ) { $settings['button_1_link']['url'] = '#'; };

        if ( ! empty( $settings['button_1_link']['url'] ) ) {
            $this->add_render_attribute( 'btn-1-link', 'href', esc_url( $settings['button_1_link']['url'] ) );

            if ( ! empty( $settings['button_1_link']['is_external'] ) ) {
                $this->add_render_attribute( 'btn-1-link', 'target', '_blank' );
            }
        }

        // BUTTON 2

        // Graphic Button
        $button_2_image = false;
        if ( isset( $settings['button_2_image']['id'] ) && $settings['button_2_image']['id'] > "" ) {
            $button_2_image = wp_get_attachment_image( $settings['button_2_image']['id'], "th_img_xs", false, array( 'class' => '' ) );
        }elseif ( ! empty( $settings['button_2_image']['url'] ) ) {
            $this->add_render_attribute( 'button_2_image', 'src', esc_url( $settings['button_2_image']['url'] ) );
            $this->add_render_attribute( 'button_2_image', 'alt', esc_attr( Control_Media::get_image_alt( $settings['button_2_image'] ) ) );
            $this->add_render_attribute( 'button_2_image', 'title', esc_attr( Control_Media::get_image_title( $settings['button_2_image'] ) ) );
            $button_1_image = '<img ' . $this->get_render_attribute_string( 'button_2_image' ) . '>';
        }

        // Graphic Button URL Styling
        if ( isset($button_2_image) && ! empty( $button_2_image ) ) {
            // image button
            $this->add_render_attribute( 'btn-2-link', 'class', 'btn-2' );
            $this->add_render_attribute( 'btn-2-link', 'class', 'th-btn' );
            $this->add_render_attribute( 'btn-2-link', 'class', 'btn-image' );
        }else{ // Bootstrap Button URL Styling
            $this->add_render_attribute( 'btn-2-link', 'class', 'btn-2' );
            $this->add_render_attribute( 'btn-2-link', 'class', 'btn' );
            $this->add_render_attribute( 'btn-2-link', 'class', 'th-btn' );
            $this->add_render_attribute( 'btn-2-link', 'class', 'btn-' . esc_attr( $settings['button_2_style'] ) );
        }

        // Button URL
        if ( empty( $settings['button_2_link']['url'] ) ) { $settings['button_2_link']['url'] = '#'; };

        if ( ! empty( $settings['button_2_link']['url'] ) ) {
            $this->add_render_attribute( 'btn-2-link', 'href', esc_url( $settings['button_2_link']['url'] ) );

            if ( ! empty( $settings['button_2_link']['is_external'] ) ) {
                $this->add_render_attribute( 'btn-2-link', 'target', '_blank' );
            }
        }

        $this->add_render_attribute( 'th-header-class', 'class', 'elementor-icon-box-title' );

        // Divider & Alignment Class
        
        if ( isset($settings['title_divider']) && 'yes' == $settings['title_divider'] ) {
            $this->add_render_attribute( 'th_divider_span', 'class', 'th-header-divider' );
        }

		?>
		<div class="th-header-wrap">
        <div class="elementor-icon-box-wrapper <?php if ( ( isset($settings['icon'] ) && $settings['icon'] > "" ) || (is_array( $settings['new_icon'] ) && !empty($settings['new_icon']['value'])) ){ echo "th-show-icon"; } ?>">
            <?php if ( ( isset($settings['icon'] ) && $settings['icon'] > "" ) || (is_array( $settings['new_icon'] ) && !empty($settings['new_icon']['value'])) ){ ?>
                <div <?php echo $this->get_render_attribute_string( 'th-icon-size' ); ?>>
                    <<?php echo wp_kses_post(implode( ' ', [ $icon_tag, $icon_attributes ] )); ?>>
                        <?php
                        // new icon render
                        $migrated = isset( $settings['__fa4_migrated']['new_icon'] );
                        $is_new = empty( $settings['icon'] );
                        if ( $is_new || $migrated ) {
                            \Elementor\Icons_Manager::render_icon( $settings['new_icon'], [ 'aria-hidden' => 'true' ] ); 
                        } else {
                            ?><i class="<?php echo $settings['icon']; ?>" aria-hidden="true" fff></i><?php
                        }
                        ?>
                    </<?php echo esc_attr( $icon_tag ); ?>>
                </div>
                <?php } ?>
                <div class="elementor-icon-box-content">
                    <<?php echo esc_attr($settings['title_size']); ?> <?php echo $this->get_render_attribute_string( 'th-header-class' );?>>
                        <?php echo wp_kses_post( $settings['title_text'] ); ?>
                    </<?php echo esc_attr( $settings['title_size'] ); ?>>
                    <?php if ( isset($settings['title_divider']) && 'yes' == $settings['title_divider'] ) {?>
                        <span <?php echo $this->get_render_attribute_string( 'th_divider_span' ); ?>></span>
                    <?php } ?>
                    <p class="elementor-icon-box-description"><?php echo wp_kses_post( $settings['description_text'] ); ?></p>

                    <?php if ( ! empty( $settings['button_1_text'] ) || ! empty( $settings['button_2_text'] )  || ! empty($button_1_image) || ! empty( $button_2_image ) ) : ?>
                        <div class="th-btn-wrap">
                            <?php if ( isset($button_1_image) && ! empty( $button_1_image ) ) : ?>
                                <?php if ( ! empty( $settings['button_1_link']['url'] ) ) : ?>
                                    <a <?php echo $this->get_render_attribute_string( 'btn-1-link' ); ?>>
                                        <?php echo wp_kses_post( $button_1_image ); ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo wp_kses_post( $button_1_image ); ?>
                                <?php endif; ?>
                            <?php elseif ( ! empty( $settings['button_1_text'] )  ) : ?>
                                <a <?php echo $this->get_render_attribute_string( 'btn-1-link' ); ?>>
                                    <?php if ( ! empty( $settings['button_1_text'] ) ) : ?>
                                        <?php echo esc_html( $settings['button_1_text'] ); ?>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>

                            <?php if ( isset($button_2_image) && ! empty( $button_2_image ) ) : ?>
                                <?php if ( ! empty( $settings['button_2_link']['url'] ) ) : ?>
                                    <a <?php echo $this->get_render_attribute_string( 'btn-2-link' ); ?>>
                                        <?php echo wp_kses_post( $button_2_image ); ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo wp_kses_post( $button_2_image ); ?>
                                <?php endif; ?>
                            <?php elseif ( ! empty( $settings['button_2_text'] ) ) : ?>
                                <a <?php echo $this->get_render_attribute_string( 'btn-2-link' ); ?>>
                                    <?php if ( ! empty( $settings['button_2_text'] ) ) : ?>
                                        <?php echo esc_html( $settings['button_2_text'] ); ?>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

		<?php
	}

	protected function content_template() {
		?>

		<#
        iconHTML = elementor.helpers.renderIcon( view, settings.new_icon, { 'aria-hidden': true }, 'i' , 'object' ); 
        migrated = elementor.helpers.isIconMigrated( settings, 'new_icon' );
        var link = '',
        iconTag = 'span';
        icon_size = '';
        icon_show = '';
        var th_divder_span = '';
        var th_header_class = 'elementor-icon-box-title';

        // Divider & Alignment Class
        if ( settings.title_divider  && 'yes' == settings.title_divider  ) {
            var th_divder_span = "<span class='th-header-divider'></span>";
        }

        if ( settings.icon_size ) { var icon_size = 'th-icon-size-'+settings.icon_size }
        if ( settings.icon || settings.new_icon) { var icon_show = 'th-show-icon'}
                #>
        <div class="th-header-wrap">
            <div class="elementor-icon-box-wrapper {{ icon_show }}">
                <# if ( settings.icon || ( iconHTML.rendered && ( ! settings.icon || migrated ) ) ) { #>
                <div class="elementor-icon-box-icon {{ icon_size }}">
                    <{{{ iconTag }}} class="elementor-icon elementor-animation-{{ settings.hover_animation }}">
                        <# if ( iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
					        {{{ iconHTML.value }}}
				        <# } else { #>
					        <i class="{{ settings.icon }}" aria-hidden="true"></i>
				        <# } #>
                    </{{{ iconTag }}}>
                </div>
                <# } #>
                <div class="elementor-icon-box-content">
                    <{{{ settings.title_size }}} class="{{{th_header_class}}}">
                        {{{ settings.title_text }}}
                    </{{{ settings.title_size }}}>
                    {{{th_divder_span}}}
                    <p class="elementor-icon-box-description">{{{ settings.description_text }}}</p>

                    <#  var button_1_link_url = '#';
                        var button_1_text = '';

                        if ( settings.button_1_link.url ) { var button_1_link_url = settings.button_1_link.url }
                        if ( settings.button_1_text ) { var button_1_text = settings.button_1_text }


                        var button_2_link_url = '#';
                        var button_2_text = '';

                        if ( settings.button_2_link.url ) { var button_2_link_url = settings.button_2_link.url }
                        if ( settings.button_2_text ) { var button_2_text = settings.button_2_text }

                    #>

                        <# if ( button_1_text || button_2_text || settings.button_1_image || settings.button_2_image) { #>
                        <div class="th-btn-wrap">
                            <# if ( settings.button_1_image && '' !== settings.button_1_image.url ) { #>
                                <a class="btn-1 th-btn btn-image" href="{{ button_1_link_url }}">
                                    <img src="{{{ settings.button_1_image.url }}}" />
                                </a>
                            <# } else if ( button_1_text ) { #>
                                <a class="btn btn-1 th-btn btn-{{ settings.button_1_style }}" href="{{ button_1_link_url }}">
                                    {{{ settings.button_1_text }}}
                                </a>
                            <# } #>
                            <# if ( settings.button_2_image && '' !== settings.button_2_image.url ) { #>
                                <a class="btn-2 th-btn btn-image" href="{{ button_2_link_url }}">
                                    <img src="{{{ settings.button_2_image.url }}}" />
                                </a>
                            <# } else if ( button_2_text ) { #>
                                <a class="btn btn-2 th-btn btn-{{ settings.button_2_style }}" href="{{ button_2_link_url }}">
                                    {{{ settings.button_2_text }}}
                                </a>
                            <# } #>
                        </div>
                    <# } #>
                </div>
            </div>
        </div>

		<?php
	}

	
}

Plugin::instance()->widgets_manager->register( new Themo_Widget_Header() );
