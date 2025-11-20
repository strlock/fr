<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_MPHB_Availability_Calendar extends Widget_Base {

    public function get_name() {
        return 'themo-mphb-availability-calendar';
    }

    public function get_title() {
        return __( 'Accommodation Availability Calendar', ALOHA_DOMAIN );
    }

    public function get_icon() {
        return 'th-editor-icon-calender-2';
    }

    public function get_help_url() {
        return ALOHA_WIDGETS_HELP_URL_PREFIX . $this->get_name();
    }
    
    public function get_categories() {
        return [ 'themo-mbhb' ];
    }

    public function is_reload_preview_required() {
        return true;
    }
    
    public function get_style_depends() {
        $themo_mphb_styling = get_theme_mod( 'themo_mphb_use_theme_styling', true );
        if ($themo_mphb_styling) {
            $modified = filemtime(THEMO_PATH . 'css/themo-mphb-booking-form.css');
            wp_register_style('themo-mphb-booking-form', THEMO_URL . 'css/themo-mphb-booking-form.css', array(), $modified);
            return ['themo-mphb-booking-form'];
        }

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
                'label' => __( 'Text', ALOHA_DOMAIN ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Book Today', ALOHA_DOMAIN ),
                'placeholder' => __( 'Book here', ALOHA_DOMAIN ),
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );
        $this->add_control(
                'title_size',
                [
                    'label' => __('Title HTML Tag', ALOHA_DOMAIN),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'h1' => __('H1', ALOHA_DOMAIN),
                        'h2' => __('H2', ALOHA_DOMAIN),
                        'h3' => __('H3', ALOHA_DOMAIN),
                        'h4' => __('H4', ALOHA_DOMAIN),
                        'h5' => __('H5', ALOHA_DOMAIN),
                        'h6' => __('H6', ALOHA_DOMAIN),
                    ],
                    'default' => 'h3',
                    'separator' => 'none',
                ]
        );
        $this->add_control(
            'tooltip_color',
            [
                'label' => __( 'Text Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,

                'default' => '#FFFFFF',
                'selectors' => [
                    '{{WRAPPER}} .th-cal-tooltip .th-tooltip-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tooltip_background',
            [
                'label' => __( 'Background Color', ALOHA_DOMAIN ),
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
            'section_shortcode',
            [
                'label' => __( 'Availability Calendar', ALOHA_DOMAIN ),
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

        $this->add_control('months_to_show', array(
            'type'        => Controls_Manager::TEXT,
            'label'       => __('Months to display', ALOHA_DOMAIN),
            'default'     => '3',
            'dynamic' => [
                'active' => true,
            ]
        ));

        $this->add_control(
            'content_max_width',
            [
                'label' => __( 'Calendar Width', ALOHA_DOMAIN ),
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
                'default' => [
                    'size' => '650',
                    'unit' => 'px',
                ],
                'size_units' => [ '%', 'px' ],
                'selectors' => [
                    '{{WRAPPER}} .themo_mphb_availability_calendar' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->add_control(
            'calendar_align',
            [
                'label' => __( 'Center Align', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => __( 'Yes', ALOHA_DOMAIN ),
                'label_off' => __( 'No', ALOHA_DOMAIN ),
                'selectors' => [
                    '{{WRAPPER}} .themo_mphb_availability_calendar' => 'margin: auto;',
                ],
            ]
        );



        $this->end_controls_section();
    }

    protected function render() {

        global $post;

        $settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'th-cal-tooltip', 'class', 'th-cal-tooltip' );

        // If Accommodation type id field is empty, try to get the id automatically.
        if ( !isset( $settings['type_id'] ) || empty( $settings['type_id']) ) {
            if(isset($post->ID )&& $post->ID > ""){
                $postID = $post->ID;
                $themo_post_type = get_post_type($postID);
                if(isset($themo_post_type) && $themo_post_type=='mphb_room_type'){
                    $settings['type_id'] = $postID;
                }else{
                    $themo_mphb_args = array(
                        'orderby' => 'rand',
                        'posts_per_page' => '1',
                        'post_type' => 'mphb_room_type'
                    );
                    $mphb_room_type_loop = new \WP_Query( $themo_mphb_args );
                    while ( $mphb_room_type_loop->have_posts() ) : $mphb_room_type_loop->the_post();
                        $mphb_room_type_id = get_the_ID();
                        $settings['type_id'] = $mphb_room_type_id;
                    endwhile;


                }
            }
        }


        if ( isset( $settings['type_id'] ) && ! empty( $settings['type_id']) && is_numeric($settings['type_id']) ) {

            if ( isset( $settings['months_to_show'] ) && ! empty( $settings['months_to_show'] ) && is_numeric($settings['months_to_show'])) {
                $th_monthstoshow = $settings['months_to_show'];
            }else{
                $th_monthstoshow=2;
            }

            $th_shortcode = '[mphb_availability_calendar id='.$settings['type_id'].' monthstoshow='.$th_monthstoshow.']';
            $th_shortcode = sanitize_text_field( $th_shortcode );
            $th_shortcode = do_shortcode( shortcode_unautop( $th_shortcode ) );

            ?>
            <div class="elementor-shortcode themo_mphb_availability_calendar">
                <?php if( $settings['tooltip_title'] ) : ?>
                    <div <?php echo $this->get_render_attribute_string( 'th-cal-tooltip'); ?>><<?php echo esc_attr($settings['title_size']); ?> class="th-tooltip-title"><?php echo esc_html( $settings['tooltip_title'] ); ?></<?php echo esc_attr($settings['title_size']); ?>></div>
                <?php endif; ?>
                <?php echo $th_shortcode; ?>
            </div>
            <?php
        }
    }

    public function render_plain_content() {
        // In plain mode, render without shortcode
        echo $this->get_settings( 'shortcode' );
    }

    protected function content_template() {}


}

Plugin::instance()->widgets_manager->register( new Themo_Widget_MPHB_Availability_Calendar() );
