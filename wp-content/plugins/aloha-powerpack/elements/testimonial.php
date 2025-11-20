<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_Testimonial extends Widget_Base {

	public function get_name() {
		return 'themo-testimonial';
	}

	public function get_title() {
		return __( 'Testimonial', ALOHA_DOMAIN );
	}

	public function get_icon() {
		return 'th-editor-icon-testimonial';
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
			'section_testimonial',
			[
				'label' => __( 'Testimonial', ALOHA_DOMAIN ),
			]
		);

		$this->add_control(
			'testimonial_content',
			[
				'label' => __( 'Content', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'rows' => '10',
				'default' => __( '“Pellentesque vel purus vestibulum, commodo tellus iaculis, molestie nisi. Cras auctor, sapien eu ullamcorper tincidunt, eros felis congue arcu, id finibus libero neque ut tellus. Phasellus bibendum nibh tortor. Nam malesuada quam lorem, eu.”', ALOHA_DOMAIN ),
				'placeholder' => __( '“Pellentesque vel purus vestibulum, commodo tellus iaculis, molestie nisi. Cras auctor, sapien eu ullamcorper tincidunt, eros felis congue arcu, id finibus libero neque ut tellus. Phasellus bibendum nibh tortor. Nam malesuada quam lorem, eu.”', ALOHA_DOMAIN ),
			]
		);

        $this->add_control(
            'text_size',
            [
                'label' => __( 'Content Size', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'sm' => __( 'Small', ALOHA_DOMAIN ),
                    'md' => __( 'Medium', ALOHA_DOMAIN ),
                    'lg' => __( 'Large', ALOHA_DOMAIN ),
                ],
                'default' => 'md',
            ]
        );


        $this->add_control(
            'star_rating',
            [
                'label' => __( 'Star Rating', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'return_value' => 'yes',
            ]
        );


        $this->add_control(
            'rating',
            [
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 5,
                ],
                'range' => [
                    'px' => [
                        'min' => .5,
                        'max' => 5,
                        'step' => .5,
                    ],
                ],
                'condition' => [
                    'star_rating' => 'yes',
                ],
                'dynamic' => [
                    'active' => true,
                ],
                'separator' => 'none',
            ]
        );

        $this->add_control(
            'star_rating_position',
            [
                'label' => __( 'Star Rating Position', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'top' => __( 'Top', ALOHA_DOMAIN ),
                    'bottom' => __( 'Bottom', ALOHA_DOMAIN ),
                ],
                'default' => 'bottom',
                'condition' => [
                    'star_rating' => 'yes',
                ],
            ]
        );

		$this->add_control(
			'testimonial_image',
			[
				'label' => __( 'Add Image', ALOHA_DOMAIN ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'testimonial_name',
			[
				'label' => __( 'Name', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Doug Martin',
				'placeholder' => 'Doug Martin',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'testimonial_job',
			[
				'label' => __( 'Job', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Customer',
				'placeholder' => 'Customer',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'testimonial_image_position',
			[
				'label' => __( 'Image Position', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'default' => 'aside',
				'options' => [
					'aside' => __( 'Aside', ALOHA_DOMAIN ),
					'top' => __( 'Top', ALOHA_DOMAIN ),
				],
				'condition' => [
					'testimonial_image[url]!' => '',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'testimonial_alignment',
			[
				'label' => __( 'Alignment', ALOHA_DOMAIN ),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'    => [
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
                    '{{WRAPPER}} .elementor-testimonial-wrapper' => 'text-align: {{VALUE}}',
                ],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => __( 'View', ALOHA_DOMAIN ),
				'type' => Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();


        // Content
        $this->start_controls_section(
            'section_style_testimonial_content',
            [
                'label' => __( 'Content', ALOHA_DOMAIN ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'section_text_heading',
            [
                'label' => __( 'Text', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'content_content_color',
			[
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,

				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial-content' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __( 'Typography', 'elementor' ),
                'name' => 'section_text_typography',
                'selector' => '{{WRAPPER}} .elementor-testimonial-content',
            ]
        );

        $this->add_control(
            'section_name_heading',
            [
                'label' => __( 'Name', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'name_text_color',
			[
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,

				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial-name' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __( 'Typography', 'elementor' ),
                'name' => 'section_name_text_typography',
                'selector' => '{{WRAPPER}} .elementor-testimonial-name',
            ]
        );

        $this->add_control(
            'section_job_heading',
            [
                'label' => __( 'Job', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
			'job_text_color',
			[
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,

				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-testimonial-job' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __( 'Typography', 'elementor' ),
                'name' => 'section_job_typography',
                'selector' => '{{WRAPPER}} .elementor-testimonial-job',
            ]
        );

        $this->add_control(
            'section_star_heading',
            [
                'label' => __( 'Star Rating', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'star_rating_color',
            [
                'label' => __( 'Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,

                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .th-star-rating' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'section_image_heading',
            [
                'label' => __( 'Photo', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'label'	=> __( 'CSS Filters', 'elementor' ),
				'selector' => '{{WRAPPER}} .th-team-member-image',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {

	    $settings = $this->get_settings_for_display();
        $settings = $this->get_settings_for_display();


		$this->add_render_attribute( 'wrapper', 'class', 'elementor-testimonial-wrapper' );
        $this->add_render_attribute( 'wrapper', 'class', 'th-testimonial-w' );

        $this->add_render_attribute( 'wrapper', 'class', 'th-txt-'.esc_attr( $settings['text_size'] ) );

		if ( isset($settings['testimonial_alignment'] )) {
			$this->add_render_attribute( 'wrapper', 'class', 'elementor-testimonial-text-align-' . esc_attr( $settings['testimonial_alignment'] ) );
		}

		$this->add_render_attribute( 'meta', 'class', 'elementor-testimonial-meta' );

		/*if ( $settings['testimonial_image']['url'] ) {
			$this->add_render_attribute( 'meta', 'class', 'elementor-has-image' );
		}*/

        if ( isset( $settings['testimonial_image']['id']) && $settings['testimonial_image']['id'] > "" ) {

            if ( $settings['testimonial_image']['id'] ) $image = wp_get_attachment_image( $settings['testimonial_image']['id'], 'th_img_sm_square', false, array( 'class' => 'th-team-member-image' ) );

        }elseif ( ! empty( $settings['testimonial_image']['url'] ) ) {
            $this->add_render_attribute( 'image', 'src', esc_url( $settings['testimonial_image']['url'] ) );
            $this->add_render_attribute( 'image', 'alt', esc_attr( Control_Media::get_image_alt( $settings['testimonial_image'] ) ) );
            $this->add_render_attribute( 'image', 'title', esc_attr( Control_Media::get_image_title( $settings['testimonial_image'] ) ) );
            $this->add_render_attribute( 'image', 'class', 'th-team-member-image' );
            $image = '<img ' . $this->get_render_attribute_string( 'image' ) . '>';
        }

		if ( $settings['testimonial_image_position'] ) {
			$this->add_render_attribute( 'meta', 'class', 'elementor-testimonial-image-position-' . esc_attr( $settings['testimonial_image_position'] ) );
		}

		$has_content = ! ! $settings['testimonial_content'];

		$has_image = ! ! $settings['testimonial_image']['url'];

		$has_name = ! ! $settings['testimonial_name'];

		$has_job = ! ! $settings['testimonial_job'];

		if ( ! $has_content && ! $has_image && ! $has_name && ! $has_job ) {
			return;
		}

        if (isset( $settings['rating']) && isset($settings['rating']['size']) ) {
		    $th_rating = $settings['rating']['size'];
            $th_rating = $th_rating*10;
            $th_rating = sprintf("%02d", $th_rating);
            $this->add_render_attribute( 'star-rating', 'class', 'th-star-rating th-star-' . esc_attr( $th_rating ) );
        }
        if ( $settings['star_rating_position'] == 'top' ) {
            $this->add_render_attribute( 'star-rating', 'class', 'th-star-rating-top');
        }

		?>
        <?php if ($settings['star_rating'] == 'yes') :
            //$th_rating_class = $this->get_render_attribute_string( 'star-rating' );
            $th_rating_markup = "<div " . $this->get_render_attribute_string( 'star-rating' ) . ">\n";
            $th_rating_markup .= "<span class=\"th-star-1 fa\"></span>\n";
            $th_rating_markup .= "<span class=\"th-star-2 fa\"></span>\n";
            $th_rating_markup .= "<span class=\"th-star-3 fa\"></span>\n";
            $th_rating_markup .= "<span class=\"th-star-4 fa\"></span>\n";
            $th_rating_markup .= "<span class=\"th-star-5 fa\"></span>\n";
            $th_rating_markup .= "</div>";
         endif; ?>

		<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>

            <?php if($settings['star_rating'] == 'yes' && $settings['star_rating_position'] == 'top') : echo $th_rating_markup; endif; ?>

			<?php if ( $has_content ) : ?>
				<div class="elementor-testimonial-content"><?php echo wp_kses_post( $settings['testimonial_content'] ); ?></div>
			<?php endif; ?>

            <?php if($settings['star_rating'] == 'yes' && $settings['star_rating_position'] == 'bottom') : echo $th_rating_markup; endif; ?>

			<?php if ( $has_image || $has_name || $has_job ) : ?>

                    <div <?php echo $this->get_render_attribute_string( 'meta' ); ?>>

				<div class="elementor-testimonial-meta-inner">
                    <?php if ( isset( $image ) ) : ?>
                        <div class="elementor-testimonial-image">
                            <?php echo wp_kses_post($image); ?>
                        </div>
                    <?php endif; ?>

					<?php if ( $has_name || $has_job ) : ?>
					<div class="elementor-testimonial-details">
						<?php if ( $has_name ) : ?>
							<div class="elementor-testimonial-name"><?php echo esc_html( $settings['testimonial_name'] ); ?></div>
						<?php endif; ?>

						<?php if ( $has_job ) : ?>
							<div class="elementor-testimonial-job"><?php echo esc_html( $settings['testimonial_job'] ); ?></div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	<?php
	}

	protected function content_template() {}

}

Plugin::instance()->widgets_manager->register( new Themo_Widget_Testimonial() );
