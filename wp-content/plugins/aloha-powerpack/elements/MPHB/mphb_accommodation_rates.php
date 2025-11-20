<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_MPHB_Accommodation_Rates extends Widget_Base {

    public function get_name() {
        return 'themo-mphb-accommodation-rates';
    }

    public function get_title() {
        return __( 'Accommodation Rates', ALOHA_DOMAIN );
    }

    public function get_icon() {
        return 'th-editor-icon-rates';
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
                'label' => __( 'Accommodation Rates', ALOHA_DOMAIN ),
            ]
        );

        $this->add_control('type_id', array(
            'type'        => Controls_Manager::TEXT,
            'label'       => __('Accommodation Type ID', ALOHA_DOMAIN),
            'default'     => '',
            'label_block' => true,
            'dynamic' => [
                'active' => true,
            ]
        ));

        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_color',
            [
                'label' => __( 'Typography & Color', ALOHA_DOMAIN ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __( 'Text Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .themo_mphb_room_rates .mphb-room-rates-list li' => 'color: {{VALUE}};',
                ],

            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typography',
                'label' => __( 'Typography', ALOHA_DOMAIN ),
                'selector' => '{{WRAPPER}} .themo_mphb_room_rates .mphb-room-rates-list li',

            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __( 'Price Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .themo_mphb_room_rates .mphb-room-rates-list li .mphb-price' => 'color: {{VALUE}};',
                ],

                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => __( 'Typography', ALOHA_DOMAIN ),
                'selector' => '{{WRAPPER}} .themo_mphb_room_rates .mphb-room-rates-list li .mphb-price',

            ]
        );

        $this->add_control(
            'price_period_color',
            [
                'label' => __( 'Price Period Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .themo_mphb_room_rates .mphb-room-rates-list li .mphb-price-period' => 'color: {{VALUE}};',
                ],

                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'price_period_typography',
                'label' => __( 'Typography', ALOHA_DOMAIN ),
                'selector' => '{{WRAPPER}} .themo_mphb_room_rates .mphb-room-rates-list li .mphb-price-period',

            ]
        );

        $this->add_control(
            'price_period_clear',
            [
                'label' => __( 'Remove Plugin Styling', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'selectors' => [
                    '{{WRAPPER}} .themo_mphb_room_rates .mphb-room-rates-list li .mphb-price-period' => 'border-bottom: none;cursor: initial',

                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {


        global $post;

        $settings = $this->get_settings_for_display();

        // If Accommodation type id field is empty, try to get the id automatically.
        if ( !isset( $settings['type_id'] ) || empty( $settings['type_id']) ) {
            if(isset($post->ID )&& $post->ID > ""){
                $postID = $post->ID;
                $themo_post_type = get_post_type($postID);
                if(isset($themo_post_type) && $themo_post_type=='mphb_room_type'){
                    $settings['type_id'] = $postID;
                }
            }
        }

        if ( isset( $settings['type_id'] ) && ! empty( $settings['type_id']) && is_numeric($settings['type_id']) ) {

            $th_shortcode = '[mphb_rates id='.$settings['type_id'].']';
            $th_shortcode = sanitize_text_field( $th_shortcode );
            $th_shortcode = do_shortcode( shortcode_unautop( $th_shortcode ) );

            ?>
            <div class="elementor-shortcode themo_mphb_room_rates"><?php echo $th_shortcode; ?></div>
            <?php
        }
    }

    public function render_plain_content() {
        // In plain mode, render without shortcode
        echo $this->get_settings( 'shortcode' );
    }

    protected function content_template() {}

}

Plugin::instance()->widgets_manager->register( new Themo_Widget_MPHB_Accommodation_Rates() );
