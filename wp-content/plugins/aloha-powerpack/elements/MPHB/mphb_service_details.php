<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_MPHB_Service_Details extends Widget_Base {

    public function get_name() {
        return 'themo-mphb-service-details';
    }

    public function get_title() {
        return __( 'Accommodation Service Details', ALOHA_DOMAIN );
    }

    public function get_icon() {
        return 'th-editor-icon-service-details';
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
    
    public function get_script_depends() {
        return [];
    }
    public function get_style_depends() {
        return [];
    }
    
    protected function register_controls() {
        $this->start_controls_section(
            'section_shortcode',
            [
                'label' => __( 'Service Details', ALOHA_DOMAIN ),
            ]
        );

        $this->add_control(
            'important_note',
            [
                //'label' => __( 'Note', ALOHA_DOMAIN ),
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __( '<p style="line-height: 17px;">This widget is designed to work inside of a Service Post Type. 
                            You can access yours under Dashboard / Accommodations / Services / Edit /</p>', ALOHA_DOMAIN ),
                'content_classes' => 'themo-elem-html-control',
            ]
        );

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_color',
            [
                'label' => __( 'Typography & Color', ALOHA_DOMAIN ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __( 'Price Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .themo_mphb_service_details span.mphb-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => __( 'Price', ALOHA_DOMAIN ),
                'selector' => '{{WRAPPER}} .themo_mphb_service_details span.mphb-price',

            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __( 'Text Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .themo_mphb_service_details' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'label' => __( 'Text', ALOHA_DOMAIN ),
                'selector' => '{{WRAPPER}} .themo_mphb_service_details',

            ]
        );



        $this->end_controls_section();

    }

    protected function render() {

        global $post;

        $settings = $this->get_settings_for_display();

        if ( is_singular( 'mphb_room_service') && function_exists('mphb_tmpl_the_service_price')) { // check if function exists and if we are on a room service single.
            ?>
            <div class="themo_mphb_service_details"><?php mphb_tmpl_the_service_price(); ?></div>
            <?php
        }else{ ?>
            <div class="themo_mphb_service_details"><?php _e( 'This widget is designed to work inside of a Service Post Type. You can access yours under Dashboard / Accommodations / Services / Edit /', ALOHA_DOMAIN ); ?></div>
        <?php }
    }

    public function render_plain_content() {
        // In plain mode, render without shortcode
        echo $this->get_settings( 'shortcode' );
    }

    protected function content_template() {}

}

Plugin::instance()->widgets_manager->register( new Themo_Widget_MPHB_Service_Details() );
