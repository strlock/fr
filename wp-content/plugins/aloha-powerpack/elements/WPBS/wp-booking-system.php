<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_WP_Booking_System extends Widget_Base {

	public function get_name() {
		return 'themo-wp-boooking-system';
	}

	public function get_title() {
		return __( 'WP Booking Calendar', ALOHA_DOMAIN );
	}

	public function get_icon() {
		return 'th-editor-icon-calendar-1';
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
			'section_tooltip',
			[
				'label' => __( 'Tooltip Title', ALOHA_DOMAIN ),
			]
		);

		$this->add_control(
			'tooltip_title',
			[
				'label' => __( 'Tooltip Title', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Book Today', ALOHA_DOMAIN ),
				'placeholder' => __( 'Book here', ALOHA_DOMAIN ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'tooltip_background',
			[
				'label' => __( 'Tooltip Background', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,

				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .th-cal-tooltip' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .th-cal-tooltip:after' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_calendar',
			[
				'label' => __( 'Calendar', ALOHA_DOMAIN ),
			]
		);

		$this->add_control(
			'calendar_shortcode',
			[
				'label' => __( 'Shortcode', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'default' => __( '[wpbs id="1" form="no-form"]', ALOHA_DOMAIN ),
				'placeholder' => __( '[add_shortcode_here]', ALOHA_DOMAIN ),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'calendar_size',
			[
				'label' => __( 'Calendar Size', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'default' => 'small',
				'options' => [
					//'large' => __( 'Large', ALOHA_DOMAIN ),
					'small' => __( 'Small', ALOHA_DOMAIN ),
				],
			]
		);

		$this->add_control(
			'calendar_align',
			[
				'label' => __( 'Align Calendar', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SELECT,
				'default' => 'centered',
				'options' => [
					'left' => __('Left', ALOHA_DOMAIN),
					'centered' => __('Center', ALOHA_DOMAIN),
					'right' => __('Right', ALOHA_DOMAIN),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_tooltip',
			[
				'label' => __( 'Text Colors', ALOHA_DOMAIN ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);



        $this->add_control(
            'tooltip_color',
            [
                'label' => __( 'Tooltip Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,

                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .th-cal-tooltip h3' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'legend_color',
            [
                'label' => __( 'Legend Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,

                'default' => '#000',
                'selectors' => [
                    '{{WRAPPER}} .wpbs-legend .wpbs-legend-item p' => 'color: {{VALUE}};',
                ],
            ]
        );

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'th-cal-wrap', 'class', 'th-book-cal-' . esc_attr( $settings['calendar_size'] ) );
        $this->add_render_attribute( 'th-cal-wrap', 'class', 'th-' . esc_attr( $settings['calendar_align'] ) );
        $this->add_render_attribute( 'th-cal-tooltip', 'class', 'th-cal-tooltip' );

		?>
		<div <?php echo $this->get_render_attribute_string( 'th-cal-wrap'); ?>>
			<?php if( $settings['tooltip_title'] ) : ?>
				<div <?php echo $this->get_render_attribute_string( 'th-cal-tooltip'); ?>><h3><?php echo esc_html( $settings['tooltip_title'] ); ?></h3></div>
			<?php endif; ?>
			<?php echo do_shortcode( sanitize_text_field( $settings['calendar_shortcode'] ) ); ?>
		</div>
		<?php
	}

	protected function content_template() {}

}

Plugin::instance()->widgets_manager->register( new Themo_Widget_WP_Booking_System() );
