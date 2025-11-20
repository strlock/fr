<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_Image_Gallery extends Widget_Base {

	public function get_name() {
		return 'themo-image-gallery';
	}

	public function get_title() {
		return __( 'Image Gallery', ALOHA_DOMAIN );
	}

	public function get_icon() {
		return 'th-editor-icon-image-gallery';
	}

    public function get_categories() {
        return [ 'themo-elements' ];
    }
    public function get_style_depends() {
            $modified2 = filemtime(THEMO_PATH . 'css/themo-slider.css');
            wp_register_style('themo-slider', THEMO_URL . 'css/themo-slider.css', array(), $modified2);
            
            $modified = filemtime(THEMO_PATH . 'css/'.$this->get_name().'.css');
            wp_register_style($this->get_name(), THEMO_URL . 'css/'.$this->get_name().'.css', array(), $modified);
            return ['themo-slider', $this->get_name()];
    }
    
    public function get_script_depends() {
        return [];
    }
    
    
    public function get_help_url() {
		return ALOHA_WIDGETS_HELP_URL_PREFIX . $this->get_name();
	}
	
	protected function register_controls() {
		$this->start_controls_section(
			'section_gallery',
			[
				'label' => __( 'Image Gallery', ALOHA_DOMAIN ),
			]
		);

		$this->add_control(
			'wp_gallery',
			[
				'label' => __( 'Add Images', ALOHA_DOMAIN ),
				'type' => Controls_Manager::GALLERY,
				'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
                'name' => 'thumbnail',
                'default' => 'th_img_sm_square',
				'exclude' => [ 'custom','themo-logo','th_img_xs','th_img_lg','th_img_xl','th_img_xxl','themo_team','themo_brands','full'],
			]
		);

		$gallery_columns = range( 1, 6 );
		$gallery_columns = array_combine( $gallery_columns, $gallery_columns );

		$this->add_control(
			'gallery_columns',
			[
				'label' => __( 'Columns', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'default' => 3,
				'options' => $gallery_columns,
			]
		);

		$this->add_control(
			'gallery_link',
			[
				'label' => __( 'Link to', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'default' => 'file',
				'options' => [
					'file' => __( 'Media File', ALOHA_DOMAIN ),
					'attachment' => __( 'Attachment Page', ALOHA_DOMAIN ),
					'none' => __( 'None', ALOHA_DOMAIN ),
				],
			]
		);

		$this->add_control(
			'gallery_rand',
			[
				'label' => __( 'Ordering', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => __( 'Default', ALOHA_DOMAIN ),
					'rand' => __( 'Random', ALOHA_DOMAIN ),
				],
				'default' => '',
			]
		);

        $this->add_control(
            'image_stretch',
            [
                'label' => __( 'Image Stretch', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'yes',
                'options' => [
                    'no' => __( 'No', ALOHA_DOMAIN ),
                    'yes' => __( 'Yes', ALOHA_DOMAIN ),
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

		$this->start_controls_section(
			'section_caption',
			[
				'label' => __( 'Content', ALOHA_DOMAIN ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_responsive_control(
            'title_and_caption',
            [
                'label' => __( 'Titles & Captions', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'block' => __( 'Show', ALOHA_DOMAIN ),
                    'none' => __( 'Hide', ALOHA_DOMAIN ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .elementor-image-gallery .gallery .gallery-text' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => __( 'Alignment', ALOHA_DOMAIN ),
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
                    'justify' => [
                        'title' => __( 'Justified', ALOHA_DOMAIN ),
                        'icon' => 'fa fa-align-justify',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .image-title' => 'text-align: {{VALUE}};',
                    '{{WRAPPER}} .caption' => 'text-align: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'section_gallery_image',
            [
                'label' => __( 'Images', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'gallery_filter_switcher',
            [
                'label' => __( 'Hover Effect', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'selectors' => [
                    '{{WRAPPER}} .gallery a.img-thumbnail:hover img' => 'filter: none',
                    '{{WRAPPER}} .gallery a.img-thumbnail:hover' => 'opacity: 1',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'gallery_css_filters',
                'label'	=> __( 'CSS Filters', 'elementor' ),
                'selector' => '{{WRAPPER}} .gallery a.img-thumbnail img',
                'condition' => [
                    'gallery_filter_switcher' => 'yes',
                ],
            ]
        );



        /*$this->add_control(
            'gallery_hover_opacity',
            [
                'label' => __( 'Hover Opacity (%)', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'px',
                    'size' => 0.7,
                ],
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [

                ],
            ]
        );*/


		$this->add_control(
            'section_gallery_heading',
            [
                'label' => __( 'Title', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .image-title' => 'color: {{VALUE}};',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_title_typography',
				'selector' => '{{WRAPPER}} .image-title',


			]
		);

        $this->add_responsive_control(
            'gallery_display_title',
            [
                'label' => __( 'Display', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'block' => __( 'Show', ALOHA_DOMAIN ),
                    'none' => __( 'Hide', ALOHA_DOMAIN ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .image-title' => 'display: {{VALUE}};',
                ],

            ]
        );

		$this->add_control(
            'section_gallery_caption',
            [
                'label' => __( 'Caption', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',

            ]
        );

		$this->add_control(
			'caption_text_color',
			[
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .caption' => 'color: {{VALUE}};',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'caption_title_typography',
				'selector' => '{{WRAPPER}} .caption',


			]
		);

        $this->add_responsive_control(
            'gallery_display_caption',
            [
                'label' => __( 'Display', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'block' => __( 'Show', ALOHA_DOMAIN ),
                    'none' => __( 'Hide', ALOHA_DOMAIN ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .gallery-text .caption' => 'display: {{VALUE}};',
                ],

            ]
        );


		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( ! $settings['wp_gallery'] ) {
			return;
		}

        $gallery_class = false;
        if ( 'yes' === $settings['image_stretch'] ) {
            $gallery_class = 'th-image-stretch';
        }

		$ids = wp_list_pluck( $settings['wp_gallery'], 'id' );

		$this->add_render_attribute( 'shortcode', 'ids', esc_attr(implode( ',', $ids )) );
		$this->add_render_attribute( 'shortcode', 'size', esc_attr( $settings['thumbnail_size'] ) );

		if ( $settings['gallery_columns'] ) {
			$this->add_render_attribute( 'shortcode', 'columns', esc_attr( $settings['gallery_columns'] ) );
		}

		if ( $settings['gallery_link'] ) {
			$this->add_render_attribute( 'shortcode', 'link', esc_attr( $settings['gallery_link'] ) );
		}

		if ( ! empty( $settings['gallery_rand'] ) ) {
			$this->add_render_attribute( 'shortcode', 'orderby', esc_attr( $settings['gallery_rand'] ) );
		}
		?>
		<div class="elementor-image-gallery <?php echo esc_attr( $gallery_class ); ?>">
			<?php echo do_shortcode( '[gallery ' . sanitize_text_field( $this->get_render_attribute_string( 'shortcode' ) ) . ']' ); ?>
		</div>
		<?php
	}

	protected function content_template() {}
}

Plugin::instance()->widgets_manager->register( new Themo_Widget_Image_Gallery() );
