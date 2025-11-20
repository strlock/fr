<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_ServiceBlock extends Widget_Base {

	public function get_name() {
		return 'themo-service-block';
	}

	public function get_title() {
		return __( 'Service Block', ALOHA_DOMAIN );
	}

	public function get_icon() {
		return 'th-editor-icon-service-block';
	}

	public function get_categories() {
		return [ 'themo-elements' ];
	}

    public function get_help_url() {
        return ALOHA_WIDGETS_HELP_URL_PREFIX . $this->get_name();
    }
        public function get_style_depends() {
            $modified = filemtime(THEMO_PATH . 'css/'.$this->get_name().'.css');
            wp_register_style($this->get_name(), THEMO_URL . 'css/'.$this->get_name().'.css', array(), $modified);
            return [$this->get_name()];
        }
        
        public function get_script_depends() {
            return [];
        }

	protected function register_controls() {

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
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
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
                'default' => 'md',
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
                'default' => 'h3',
            ]
        );

        $this->add_control(
            'title_text',
            [
                'label' => __( 'Title', ALOHA_DOMAIN ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Title', ALOHA_DOMAIN ),
                'placeholder' => __( 'Title', ALOHA_DOMAIN ),
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
                'default' => __( 'Maecenas accumsan, elit id hendrerit convallis, lectus lacus fermentum nisi.', ALOHA_DOMAIN ),
                'placeholder' => __( 'Add a description', ALOHA_DOMAIN ),
                'title' => __( 'Input icon text here', ALOHA_DOMAIN ),
                'rows' => 10,
                'separator' => 'none',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );



        $this->end_controls_section();

        $this->start_controls_section(
            'section_link',
            [
                'label' => __( 'Link', ALOHA_DOMAIN ),
            ]
        );



        $this->add_control(
            'link',
            [
                'label' => __( 'Link to', ALOHA_DOMAIN ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( 'http://your-link.com', ALOHA_DOMAIN ),
                'separator' => 'before',
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );


        $this->end_controls_section();


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
                'selectors' => [
                    '{{WRAPPER}} .th-service-block-w' => 'max-width: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .th-service-block-w' => '{{VALUE}}',
                ],
                'selectors_dictionary' => [
                    'left' => 'margin-right: auto; margin-left:0;',
                    'center' => 'margin: 0 auto',
                    'right' => 'margin-left: auto; margin-right:0;',
                ],
            ]
        );

        $this->add_responsive_control(
            'text_align',
            [
                'label' => __( 'Content Alignment', ALOHA_DOMAIN ),
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
                'selectors' => [
                    '{{WRAPPER}} .th-service-block-w .elementor-icon-box-wrapper' => 'text-align: {{VALUE}};',
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
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,

				'default' => '',
				'selectors' => [
					'{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.elementor-view-framed .th-service-block-w .elementor-icon, 
					{{WRAPPER}}.elementor-view-framed .elementor-icon i, 
					{{WRAPPER}}.elementor-view-default .elementor-icon i' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				],
                'dynamic' => [
                    'active' => true,
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
					'{{WRAPPER}}.elementor-view-stacked .elementor-icon i' => 'color: {{VALUE}};',
				],
                'dynamic' => [
                    'active' => true,
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
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-title span' => 'color: {{VALUE}};',
				],

                'dynamic' => [
                    'active' => true,
                ],
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-title span',

                'label' => 'Typography',
			]
		);

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'section_content_title_shadow',
                'label' => 'Text Shadow',
                'selector' => '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-title span',
            ]
        );

        $this->add_responsive_control(
            'section_title_space_above',
            [
                'label' => __( 'Space Above', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-title' => 'margin-top: {{SIZE}}{{UNIT}}',
                ],
                'dynamic' => [
                    'active' => true,
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

                'dynamic' => [
                    'active' => true,
                ],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __( 'Typography', 'elementor' ),
                'name' => 'section_description_typography',
                'selector' => '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-description, {{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-description a',
            ]
        );

        $this->add_responsive_control(
            'section_description_space_above',
            [
                'label' => __( 'Space Above', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-description' => 'margin-top: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .elementor-icon-box-content .elementor-icon-box-description a' => 'margin-top: {{SIZE}}{{UNIT}}',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

        $elm_animation = false;
        if ( ! empty( $settings['hover_animation'] ) ) {
            $elm_animation = 'elementor-animation-' . esc_attr( $settings['hover_animation'] );
        }
        $this->add_render_attribute('icon', 'class', ['elementor-icon', $elm_animation] );

        $icon_tag = 'span';

        if ( ! empty( $settings['link']['url'] ) ) {
            $this->add_render_attribute( 'link', 'href', esc_url( $settings['link']['url'] ) );
            $icon_tag = 'a';

            if ( ! empty( $settings['link']['is_external'] ) ) {
                $this->add_render_attribute( 'link', 'target', '_blank' );
            }
        }

        $this->add_render_attribute( 'th-icon-size', 'class', 'elementor-icon-box-icon' );
        $this->add_render_attribute( 'th-icon-size', 'class', 'th-icon-size-'. esc_attr( $settings['icon_size'] ) );

		$icon_attributes = $this->get_render_attribute_string( 'icon' );
		$link_attributes = $this->get_render_attribute_string( 'link' );

		?>
		<div class="th-service-block-w">
            <div class="elementor-icon-box-wrapper <?php if ( ( isset($settings['icon'] ) && $settings['icon'] > "") || is_array( $settings['new_icon'] ) ){ echo "th-show-icon"; } ?>">
                <?php if ( ( isset($settings['icon'] ) && $settings['icon'] > "" ) || is_array( $settings['new_icon'] ) ){ ?>
                    <div <?php echo $this->get_render_attribute_string( 'th-icon-size' ); ?>>
                        <<?php echo wp_kses_post(implode( ' ', [ $icon_tag, $icon_attributes, $link_attributes ] )); ?>>
                            <?php
                            // new icon render
                            $migrated = isset( $settings['__fa4_migrated']['new_icon'] );
                            $is_new = empty( $settings['icon'] );
                            if ( $is_new || $migrated ) {
                                \Elementor\Icons_Manager::render_icon( $settings['new_icon'], [ 'aria-hidden' => 'true' ] );
                            } else {
                                ?><i class="<?php echo $settings['icon']; ?>" aria-hidden="true"></i><?php
                            }
                            ?>
                        </<?php echo esc_attr($icon_tag); ?>>
                    </div>
                <?php } ?>
                <div class="elementor-icon-box-content">
                    <<?php echo esc_attr($settings['title_size']); ?> class="elementor-icon-box-title">
                        <<?php echo wp_kses_post(implode( ' ', [ $icon_tag, $link_attributes ] )); ?>><?php echo esc_html( $settings['title_text'] ); ?></<?php echo esc_attr( $icon_tag ); ?>>
                    </<?php echo esc_attr( $settings['title_size'] ); ?>>
                    <p class="elementor-icon-box-description"><?php echo wp_kses_post( $settings['description_text'] ); ?></p>
                </div>
            </div>

        </div>

		<?php
	}

	protected function content_template() {
		?>
        <#
        var iconHTML = elementor.helpers.renderIcon( view, settings.new_icon, { 'aria-hidden': true }, 'i' , 'object' );
        migrated = elementor.helpers.isIconMigrated( settings, 'new_icon' );
        var link = settings.link.url ? 'href="' + settings.link.url + '"' : '',
        iconTag = link ? 'a' : 'span';
        icon_size = '';
        icon_show = '';
        if ( settings.icon_size ) { var icon_size = 'th-icon-size-'+settings.icon_size }
        if ( settings.icon || settings.new_icon) { var icon_show = 'th-show-icon'}

        #>
        <div class="th-service-block-w">
            <div class="elementor-icon-box-wrapper {{ icon_show }}">
                <div class="elementor-icon-box-icon {{ icon_size }}">
                    <{{{ iconTag + ' ' + link }}} class="elementor-icon elementor-animation-{{ settings.hover_animation }}">
                        <# if ( iconHTML.rendered && ( ! settings.icon || migrated ) ) { #>
					        {{{ iconHTML.value }}}
				        <# } else { #>
					        <i class="{{ settings.icon }}" aria-hidden="true"></i>
				        <# } #>
                    </{{{ iconTag }}}>
                </div>
                <div class="elementor-icon-box-content">
                    <{{{ settings.title_size }}} class="elementor-icon-box-title">
                        <{{{ iconTag + ' ' + link }}}>{{{ settings.title_text }}}</{{{ iconTag }}}>
                    </{{{ settings.title_size }}}>
                    <p class="elementor-icon-box-description">{{{ settings.description_text }}}</p>
                </div>
            </div>
        </div>

		<?php
	}

	
}

Plugin::instance()->widgets_manager->register( new Themo_Widget_ServiceBlock() );
