<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_MPHB_Checkout_Form extends Widget_Base {

    public function get_name() {
        return 'themo-mphb-checkout-form';
    }

    public function get_title() {
        return __( 'Booking Confirmation Form', ALOHA_DOMAIN );
    }

    public function get_icon() {
        return 'th-editor-icon-checkout-form';
    }

    public function get_categories() {
        return [ 'themo-mbhb' ];
    }

    public function get_help_url() {
        return ALOHA_WIDGETS_HELP_URL_PREFIX . $this->get_name();
    }
    
    public function is_reload_preview_required() {
        return true;
    }
    public function get_style_depends() {
        $modified2 = filemtime(THEMO_PATH . 'css/'.ALOHA_ELEMENTOR_CALENDAR_AND_OTHER_FORMS_FILENAME.'.css');
        wp_register_style(ALOHA_ELEMENTOR_CALENDAR_AND_OTHER_FORMS_FILENAME, THEMO_URL . 'css/'.ALOHA_ELEMENTOR_CALENDAR_AND_OTHER_FORMS_FILENAME.'.css', array(), $modified2);         
    
        return [ALOHA_ELEMENTOR_CALENDAR_AND_OTHER_FORMS_FILENAME];
    }
    
    public function get_script_depends() {
        return [];
    }
    
    protected function register_controls() {
        $this->start_controls_section(
            'section_shortcode',
            [
                'label' => __( 'Checkout Form', ALOHA_DOMAIN ),
            ]
        );




        /*$this->add_control(
            'inline_form',
            [
                'label' => __( 'Formidable Form Style', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __( 'Default', ALOHA_DOMAIN ),
                    'stacked' => __( 'Fill', ALOHA_DOMAIN ),

                ],
            ]
        );*/

        /*$this->add_control(
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
        );*/


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
            'inline_form',
            [
                'label' => __( 'Form Style', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'stacked',
                'options' => [
                    'none' => __( 'Default', ALOHA_DOMAIN ),
                    'stacked' => __( 'Stretched', ALOHA_DOMAIN ),

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
            'hide_required_notices',
            [
                'label' => __( 'Hide Required Tips', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'selectors' => [
                    '{{WRAPPER}} .mphb_sc_checkout-wrapper .mphb-required-fields-tip' => 'display:none;',
                    '{{WRAPPER}} .mphb_sc_checkout-wrapper label abbr' => 'display:none;',
                ],
            ]
        );

        $this->add_control(
            'important_note',
            [
                //'label' => __( 'Note', ALOHA_DOMAIN ),
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __( '<p style="line-height: 17px;">This widget is designed to work inside the Checkout Page. See: Accommodation / Settings / General / Checkout Page. 
                              <p style="line-height: 17px; margin-top: 10px;">Use the booking form on the front-end to preview your styling changes.</p>', ALOHA_DOMAIN ),
                'content_classes' => 'themo-elem-html-control',
                'separator' => 'before'
            ]
        );

        /*$this->add_control(
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
                    '{{WRAPPER}} .th-fo-form' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );*/


        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_content',
            [
                'label' => __( 'Colors', ALOHA_DOMAIN ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'headings_1_color',
            [
                'label' => __( 'Heading 1', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} h3' => 'color: {{VALUE}};',
                ],


            ]
        );

        $this->add_control(
            'headings_2_color',
            [
                'label' => __( 'Heading 2', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} h4' => 'color: {{VALUE}};',
                ],


            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __( 'Text', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} p' => 'color: {{VALUE}};',
                    '{{WRAPPER}} li' => 'color: {{VALUE}};',
                    '{{WRAPPER}} th' => 'color: {{VALUE}};',
                    '{{WRAPPER}} td' => 'color: {{VALUE}};',
                ],


            ]
        );


        $this->add_control(
            'tip_color',
            [
                'label' => __( 'Required Tips', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .mphb-required-fields-tip' => 'color: {{VALUE}};',
                ],


            ]
        );


        $this->add_control(
            'label_color',
            [
                'label' => __( 'Labels', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .mphb_sc_checkout-wrapper label' => 'color: {{VALUE}};',
                ],

            ]
        );


        $this->end_controls_section();
    }

    protected function render() {

        $settings = $this->get_settings_for_display();

        //if ( isset( $settings['type_id'] ) && ! empty( $settings['type_id'] && is_numeric($settings['type_id'])) ) {


            $th_shortcode = '[mphb_checkout]';
            $th_shortcode = sanitize_text_field( $th_shortcode );
            $th_shortcode = do_shortcode( shortcode_unautop( $th_shortcode ) );


            if ( function_exists( 'get_theme_mod' ) ) {
                $themo_mphb_styling = get_theme_mod('themo_mphb_use_theme_styling', true);
                if ($themo_mphb_styling == true) {

                    // Add in special classes
                    // Hotel Booking Form
                    // Form Wrapper
                    $th_shortcode = str_replace(
                        'mphb_sc_checkout-wrapper',
                        'mphb_sc_checkout-wrapper frm_forms with_frm_style',
                        $th_shortcode
                    );

                    // Dropdowns Adults
                    $th_shortcode = str_replace(
                        'mphb-adults-chooser',
                        'mphb-adults-chooser frm_form_field ',
                        $th_shortcode
                    );
                    // Dropdowns Children
                    $th_shortcode = str_replace(
                        'mphb-children-chooser',
                        'mphb-children-chooser frm_form_field ',
                        $th_shortcode
                    );

                    // Dropdowns Guest Name
                    $th_shortcode = str_replace(
                        'mphb-guest-name-wrapper',
                        'mphb-guest-name-wrapper frm_form_field ',
                        $th_shortcode
                    );

                    // Additional Services
                    $th_shortcode = str_replace(
                        'mphb_checkout-services-list',
                        'mphb_checkout-services-list frm_form_field frm_checkbox frm_radio ',
                        $th_shortcode
                    );

                    // Payment Gateways
                    $th_shortcode = str_replace(
                        'mphb-gateways-list',
                        'mphb-gateways-list frm_form_field frm_checkbox frm_radio ',
                        $th_shortcode
                    );

                    // Book Now Button
                    $th_shortcode = str_replace(
                        'mphb_sc_checkout-submit-wrapper',
                        'mphb_sc_checkout-submit-wrapper frm_submit',
                        $th_shortcode
                    );

                    // Your Information Sections (name, email, phone, country, notes)

                    $th_shortcode = str_replace(
                        'mphb-customer-name',
                        'mphb-customer-name frm_form_field ',
                        $th_shortcode
                    );
                    $th_shortcode = str_replace(
                        'mphb-customer-last-name',
                        'mphb-customer-last-name frm_form_field ',
                        $th_shortcode
                    );
                    $th_shortcode = str_replace(
                        'mphb-customer-email',
                        'mphb-customer-email frm_form_field ',
                        $th_shortcode
                    );
                    $th_shortcode = str_replace(
                        'mphb-customer-phone',
                        'mphb-customer-phone frm_form_field ',
                        $th_shortcode
                    );
                    $th_shortcode = str_replace(
                        'mphb-customer-country',
                        'mphb-customer-country frm_form_field ',
                        $th_shortcode
                    );
                    $th_shortcode = str_replace(
                        'mphb-customer-note',
                        'mphb-customer-note frm_form_field ',
                        $th_shortcode
                    );

                    //btn-1 btn th-btn btn-standard-primary
                    $th_shortcode = str_replace(
                        'mphb-apply-coupon-code-button',
                        'mphb-apply-coupon-code-button btn th-btn btn-standard-primary',
                        $th_shortcode
                    );
                }
            }


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

            $this->add_render_attribute( 'th-form-class', 'class', 'th-fo-form');
            //$this->add_render_attribute( 'th-form-class', 'class', esc_attr( $th_cal_align_class ) );
            $this->add_render_attribute( 'th-form-class', 'class', esc_attr( $th_formidable_class ) );
            $this->add_render_attribute( 'th-form-class', 'class', esc_attr( $th_form_border_class ) );
            $this->add_render_attribute( 'th-form-class', 'class', 'th-btn-form' );
            $this->add_render_attribute( 'th-form-class', 'class', 'btn-' . esc_attr( $settings['button_1_style'] . '-form' ) );

            $themo_form_styling = false;
            if ( function_exists( 'get_theme_mod' ) ) {
                $themo_mphb_styling = get_theme_mod('themo_mphb_use_theme_styling', true);
                if ($themo_mphb_styling == true) {
                    $themo_form_styling = $this->get_render_attribute_string( 'th-form-class');
                }
            }
            ?>
            <div <?php echo $themo_form_styling; ?>><?php echo $th_shortcode; ?></div>
            <?php
        //}
    }

    public function render_plain_content() {
        // In plain mode, render without shortcode
        echo $this->get_settings( 'shortcode' );
    }

    protected function content_template() {}

}

Plugin::instance()->widgets_manager->register( new Themo_Widget_MPHB_Checkout_Form() );
