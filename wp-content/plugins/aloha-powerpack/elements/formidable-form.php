<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_Formidable extends Widget_Base {

	public function get_name() {
		return 'themo-formidable-form';
	}

	public function get_title() {
		return __( 'Formidable Form', ALOHA_DOMAIN );
	}

	public function get_icon() {
		return 'th-editor-icon-forms';
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
 
        public function get_style_depends() {
            $modified2 = filemtime(THEMO_PATH . 'css/'.ALOHA_ELEMENTOR_CALENDAR_AND_OTHER_FORMS_FILENAME.'.css');
            wp_register_style(ALOHA_ELEMENTOR_CALENDAR_AND_OTHER_FORMS_FILENAME, THEMO_URL . 'css/'.ALOHA_ELEMENTOR_CALENDAR_AND_OTHER_FORMS_FILENAME.'.css', array(), $modified2);         
            return [ALOHA_ELEMENTOR_CALENDAR_AND_OTHER_FORMS_FILENAME];
        }
	protected function register_controls() {
		$this->start_controls_section(
			'section_shortcode',
			[
				'label' => __( 'Form shortcode', ALOHA_DOMAIN ),
			]
		);

        $this->add_control(
            'shortcode',
            [
                'label' => __( 'Shortcode', ALOHA_DOMAIN ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __( '[formidable id=3]', ALOHA_DOMAIN ),
                //'default' => __( '[formidable id=3]', ALOHA_DOMAIN ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
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
            ]
        );
        $this->add_control(
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
            ]
        );

        $this->add_control(
            'slide_text_align',
            [
                'label' => __( 'Align', ALOHA_DOMAIN ),
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

        $this->add_responsive_control(
            'content_max_width',
            [
                'label' => __( 'Content Width', 'elementor' ),
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
                    '{{WRAPPER}} .th-fo-form' => 'max-width: {{SIZE}}{{UNIT}};',
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
                'label' => __( 'Button', ALOHA_DOMAIN ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'button_1_style',
            [
                'label' => __( 'Style', ALOHA_DOMAIN ),
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
            'button_text_colour',
            [
                'label' => __( 'Text Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} form .frm_submit input[type=submit]' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_text_typography',
                'selector' => '{{WRAPPER}} form .frm_submit input[type=submit]',

            ]
        );

        $this->end_controls_section();
	}

	protected function render() {

        $settings = $this->get_settings_for_display();

        if ( isset( $settings['shortcode'] ) && ! empty( $settings['shortcode'] ) ) {
            $th_shortcode = sanitize_text_field( $settings['shortcode'] );
            $th_shortcode = do_shortcode( shortcode_unautop( $th_shortcode ) );

            $th_form_border_class = false;
            $th_formidable_class = 'th-form-default';
            if ( isset( $settings['inline_form'] ) && $settings['inline_form'] > "" ) :
                switch ( $settings['inline_form'] ) {
                    case 'stacked':
                        $th_formidable_class = 'th-form-stacked';
                        if ( isset( $settings['slide_shortcode_border'] ) && $settings['slide_shortcode_border'] != 'none' ) {
                            $th_form_border_class = $settings['slide_shortcode_border'];
                        }
                        break;
                    case 'inline':
                        $th_formidable_class = 'th-conversion';
                        break;
                }
            endif;

            $th_cal_align_class = false;
            if ( isset( $settings['slide_text_align'] ) && $settings['slide_text_align'] > "" ) {
                switch ( $settings['slide_text_align'] ) {
                    case 'left':
                        $th_cal_align_class = ' th-left';
                        break;
                    case 'center':
                        $th_cal_align_class = ' th-centered';
                        break;
                    case 'right':
                        $th_cal_align_class = ' th-right';
                        break;
                }
            }

            $this->add_render_attribute( 'th-form-class', 'class', 'th-fo-form');
            $this->add_render_attribute( 'th-form-class', 'class', esc_attr( $th_cal_align_class ) );
            $this->add_render_attribute( 'th-form-class', 'class', esc_attr( $th_formidable_class ) );
            $this->add_render_attribute( 'th-form-class', 'class', esc_attr( $th_form_border_class ) );
            $this->add_render_attribute( 'th-form-class', 'class', 'th-btn-form' );
            $this->add_render_attribute( 'th-form-class', 'class', 'btn-' . esc_attr( $settings['button_1_style'] . '-form' ) );


            ?>
            <div <?php echo $this->get_render_attribute_string( 'th-form-class'); ?>><?php echo $th_shortcode; ?></div>
            <?php
        }

	}

	/*public function render_plain_content() {
		// In plain mode, render without shortcode
		echo sanitize_text_field($this->get_settings( 'shortcode' ));
	}*/

	protected function content_template() {}

	
}

Plugin::instance()->widgets_manager->register( new Themo_Widget_Formidable() );
