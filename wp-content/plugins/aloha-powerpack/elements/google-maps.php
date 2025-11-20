<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_GoogleMaps extends Widget_Base {

	public function get_name() {
		return 'themo-google-maps';
	}

	public function get_title() {
		return __( 'Google Maps', ALOHA_DOMAIN );
	}

	public function get_icon() {
		return 'th-editor-icon-google-maps';
	}

	public function get_categories() {
		return [ 'themo-elements' ];
	}
        public function get_script_depends(){
            //only when not in the editor, otherwise get_settings throws error
            if (!\Elementor\Plugin::$instance->preview->is_preview_mode()) {
                $settings = $this->get_settings_for_display();
                //legacy field "api", if present, see if we need to use it
                if (isset($settings['api']) && !empty($settings['api'])) {
                    $this->aloha_maps_key_import($settings['api']);
                }
            }


        $modifiedJS = filemtime(THEMO_PATH . 'js/themo-google-maps.js');
            wp_register_script( 'themo-google-map', THEMO_URL . 'js/themo-google-maps.js', array('jquery'), $modifiedJS, true);
            wp_localize_script('themo-google-map', 'TH_MAP_KEY', array(
                'map_key' => $this->aloha_get_google_maps_key()
            ));
            return ['themo-google-map'];        
        }
	public function get_help_url() {
		return ALOHA_WIDGETS_HELP_URL_PREFIX . $this->get_name();
	}
        public function get_style_depends() {
            $modified = filemtime(THEMO_PATH . 'css/'.$this->get_name().'.css');
            wp_register_style($this->get_name(), THEMO_URL . 'css/'.$this->get_name().'.css', array(), $modified);
            return [$this->get_name()];
        }
        
    private function aloha_maps_key_import($legacy_key) {
        $current_key = $this->aloha_get_google_maps_key();
        $elementor_instance = aloha_hfe_get_elementor_instance();
        if (!empty($legacy_key) && empty($current_key) && $elementor_instance) {
            $kit = $elementor_instance->kits_manager->get_active_kit();
            $kit->update_settings([ALOHA_MAPS_OPTION => $legacy_key]);
        }
    }

    private function aloha_get_google_maps_key() {
        $elementor_instance = aloha_hfe_get_elementor_instance();
        if ($elementor_instance) {
            $kit = $elementor_instance->kits_manager->get_active_kit();
            $api_key = $kit->get_settings(ALOHA_MAPS_OPTION);
            return $api_key;
        }
        return '';
    }

    protected function register_controls() {
		$this->start_controls_section(
			'section_map',
			[
				'label' => __( 'Map', ALOHA_DOMAIN ),
			]
		);

		$default_latitude = 49.293753;
		$default_logitude = -123.053398;
		// $this->add_control(
		// 	'address',
		// 	[
		// 		'label' => __( 'Map Address', ALOHA_DOMAIN ),
		// 		'type' => Controls_Manager::TEXT,
		// 		'placeholder' => $default_address,
		// 		'default' => $default_address,
		// 		'label_block' => true,
		// 	]
		// );

		$this->add_control(
			'latitude',
			[
				'label' => __( 'Map Address : Latitude', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => $default_latitude,
				'default' => $default_latitude,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'longitude',
			[
				'label' => __( 'Map Address : Longitude', ALOHA_DOMAIN ),
                'description' => __( '<a href="http://www.latlong.net/" target="_blank">Find your Latitude & Longitude</a>', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => $default_logitude,
				'default' => $default_logitude,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'zoom',
			[
				'label' => __( 'Zoom Level', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 12,
				],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 20,
					],
				],
				'dynamic' => [
                    'active' => true,
                ],
			]
		);

        $this->add_control(
            'style',
            [
                'label' => __( 'Style', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'standard' => __( 'Standard', ALOHA_DOMAIN ),
                    'ultra_light' => __( 'Ultra Light', ALOHA_DOMAIN ),
                    'light_dream' => __( 'Light Dream', ALOHA_DOMAIN ),
                    'shades_of_gray' => __( 'Shades of Gray', ALOHA_DOMAIN ),
                    'subtle_grayscale' => __( 'Subtle Grayscale', ALOHA_DOMAIN ),
                    'retro' => __( 'Retro', ALOHA_DOMAIN ),
                    'apple_esque' => __( 'Apple-esque', ALOHA_DOMAIN ),
                    'blue_essence' => __( 'Blue Essence', ALOHA_DOMAIN ),
                ],
                'default' => 'standard',
            ]
        );

		$this->add_responsive_control(
			'height',
			[
				'label' => __( 'Height', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 400,
				],
				'range' => [
					'px' => [
						'min' => 40,
						'max' => 1440,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .th-map' => 'height: {{SIZE}}{{UNIT}};',
				],
				'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$this->add_control(
			'prevent_scroll',
			[
				'label' => __( 'Prevent Scroll', ALOHA_DOMAIN ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Yes', ALOHA_DOMAIN ),
				'label_off' => __( 'No', ALOHA_DOMAIN ),
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
			'section_text_block',
			[
				'label' => __( 'Text Block', ALOHA_DOMAIN ),
			]
		);

		$this->add_control(
			'title',
			[
				'label' => __( 'Title', ALOHA_DOMAIN ),
				'default' => __( 'Company Co.', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'separator' => 'before',
				'dynamic' => [
					'active' => true,
				],
				'label_block' => true,
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
			'business_address',
			[
				'label' => __( 'Business Address', ALOHA_DOMAIN ),
				'default' => __( "1366 Main Street\nVancouver Canada\nV8V 3K6", ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'hours',
			[
				'label' => __( 'Hours', ALOHA_DOMAIN ),
				'default' => __( "Monday to Friday: 10am - 6pm\nSaturday: 11am - 4pm\nSunday: Closed", ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXTAREA,
				'separator' => 'before',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'link_1_text',
			[
				'label' => __( 'Link 1 Text', ALOHA_DOMAIN ),
				'default' => __( 'Call Us', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'separator' => 'before',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'link_1_url',
			[
				'label' => __( 'Link 1 URL', ALOHA_DOMAIN ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'http://your-link.com', ALOHA_DOMAIN ),
                'default' => [
                    'url' => 'tel:222-2222',
                ],
                'dynamic' => [
                    'active' => true,
                ],
			]
		);

		$this->add_control(
			'link_2_text',
			[
				'label' => __( 'Link 2 Text', ALOHA_DOMAIN ),
                'default' => __( 'Email Us', ALOHA_DOMAIN ),
				'type' => Controls_Manager::TEXT,
				'separator' => 'before',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'link_2_url',
			[
				'label' => __( 'Link 2 URL', ALOHA_DOMAIN ),
				'type' => Controls_Manager::URL,
				'placeholder' => __( 'http://your-link.com', ALOHA_DOMAIN ),
                'default' => [
                    'url' => 'mailto:info@companyco.com',
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
                    '{{WRAPPER}} .map-info' => '{{VALUE}}',
                ],
                'selectors_dictionary' => [
                    'left' => 'left: 15px; right: auto;',
                    'center' => 'left: 50%; transform: translate(-50%, 0);',
                    'right' => 'left: auto; right: 15px;',
                ],
                'default' => 'left',
            ]
        );

		$this->end_controls_section();

        $this->start_controls_section(
            'section_style_background',
            [
                'label' => __( 'Content', ALOHA_DOMAIN ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'section_content_title_heading',
            [
                'label' => __( 'Title', 'elementor' ),
                'type' => Controls_Manager::HEADING,
            ]
        );

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .map-info > h3' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __( 'Typography', 'elementor' ),
                'name' => 'section_content_title_typography',
                'selector' => '{{WRAPPER}} .map-info > .th-map-title',
            ]
        );

        $this->add_control(
            'section_content_address_heading',
            [
                'label' => __( 'Business Address', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'address_color',
			[
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .map-info .th-gmap-address p' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __( 'Typography', 'elementor' ),
                'name' => 'section_content_address_typography',
                'selector' => '{{WRAPPER}} .map-info .th-gmap-address p',
            ]
        );

        $this->add_control(
            'section_content_hours_heading',
            [
                'label' => __( 'Hours', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'hours_color',
			[
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .map-info .th-gmap-hoursop p' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __( 'Typography', 'elementor' ),
                'name' => 'section_content_hours_typography',
                'selector' => '{{WRAPPER}} .map-info .th-gmap-hoursop p',
            ]
        );

        $this->add_control(
            'section_content_links_heading',
            [
                'label' => __( 'Link', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'link_colour',
            [
                'label' => __( 'Link Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,

                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .map-info .th-gmap-links a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'label' => __( 'Typography', 'elementor' ),
                'name' => 'section_content_link_typography',
                'selector' => '{{WRAPPER}} .map-info .th-gmap-links a',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_css_map',
            [
                'label' => __( 'Map', ALOHA_DOMAIN ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'label'	=> __( 'CSS Filters', 'elementor' ),
				'selector' => '{{WRAPPER}} .th-map',
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_gmap_border',
            [
                'label' => __( 'Appearance', ALOHA_DOMAIN ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'blog_section_padding',
            [
                'label' => __( 'Padding', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .th-gmap-wrap .map-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
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
                    '{{WRAPPER}} .th-gmap-wrap .map-info' => 'top: {{SIZE}}{{UNIT}}',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        // Add colour bg here

        $this->add_control(
            'bg_colour',
            [
                'label' => __( 'Background', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .map-info' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_border_content',
            [
                'label' => __( 'Border', ALOHA_DOMAIN ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'map_border',
                'selector' => '{{WRAPPER}} .th-gmap-wrap .map-info',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'map_border_radius',
            [
                'label' => __( 'Border Radius', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .th-gmap-wrap .map-info' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'map_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .th-gmap-wrap .map-info',
            ]
        );
        
        $this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();     
                
                if(!isset($settings['api'])){
                    $settings['api'] = $this->aloha_get_google_maps_key();
                }
                
		global $th_map_id;
		$map_id = 'th-map-' .  ++$th_map_id;

		if ( 0 === absint( $settings['zoom']['size'] ) ) $settings['zoom']['size'] = 12;
		if ( '' === $settings['latitude'] ) $settings['latitude'] = 49.293753;
		if ( '' === $settings['longitude'] ) $settings['longitude'] = -123.053398;

		// styles

        switch ($settings['style']) {
            case 'ultra_light':
                $th_map_style =  '[{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}]';
            break;
            case 'subtle_grayscale':
                $th_map_style = '[{"featureType":"administrative","elementType":"all","stylers":[{"saturation":"-100"}]},{"featureType":"administrative.province","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","elementType":"all","stylers":[{"saturation":-100},{"lightness":"50"},{"visibility":"simplified"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":"-100"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"lightness":"30"}]},{"featureType":"road.local","elementType":"all","stylers":[{"lightness":"40"}]},{"featureType":"transit","elementType":"all","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]},{"featureType":"water","elementType":"labels","stylers":[{"lightness":-25},{"saturation":-100}]}]';
            break;
            case 'shades_of_gray':
                $th_map_style = '[{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]}]';
            break;
            case 'light_dream':
                $th_map_style = '[{"featureType":"landscape","stylers":[{"hue":"#FFBB00"},{"saturation":43.400000000000006},{"lightness":37.599999999999994},{"gamma":1}]},{"featureType":"road.highway","stylers":[{"hue":"#FFC200"},{"saturation":-61.8},{"lightness":45.599999999999994},{"gamma":1}]},{"featureType":"road.arterial","stylers":[{"hue":"#FF0300"},{"saturation":-100},{"lightness":51.19999999999999},{"gamma":1}]},{"featureType":"road.local","stylers":[{"hue":"#FF0300"},{"saturation":-100},{"lightness":52},{"gamma":1}]},{"featureType":"water","stylers":[{"hue":"#0078FF"},{"saturation":-13.200000000000003},{"lightness":2.4000000000000057},{"gamma":1}]},{"featureType":"poi","stylers":[{"hue":"#00FF6A"},{"saturation":-1.0989010989011234},{"lightness":11.200000000000017},{"gamma":1}]}]';
            break;
            case 'retro':
                $th_map_style = '[{"elementType":"geometry","stylers":[{"color":"#ebe3cd"}]},{"elementType":"labels.text.fill","stylers":[{"color":"#523735"}]},{"elementType":"labels.text.stroke","stylers":[{"color":"#f5f1e6"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#c9b2a6"}]},{"featureType":"administrative.land_parcel","elementType":"geometry.stroke","stylers":[{"color":"#dcd2be"}]},{"featureType":"administrative.land_parcel","elementType":"labels.text.fill","stylers":[{"color":"#ae9e90"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#93817c"}]},{"featureType":"poi.park","elementType":"geometry.fill","stylers":[{"color":"#a5b076"}]},{"featureType":"poi.park","elementType":"labels.text.fill","stylers":[{"color":"#447530"}]},{"featureType":"road","elementType":"geometry","stylers":[{"color":"#f5f1e6"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#fdfcf8"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"color":"#f8c967"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#e9bc62"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry","stylers":[{"color":"#e98d58"}]},{"featureType":"road.highway.controlled_access","elementType":"geometry.stroke","stylers":[{"color":"#db8555"}]},{"featureType":"road.local","elementType":"labels.text.fill","stylers":[{"color":"#806b63"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"transit.line","elementType":"labels.text.fill","stylers":[{"color":"#8f7d77"}]},{"featureType":"transit.line","elementType":"labels.text.stroke","stylers":[{"color":"#ebe3cd"}]},{"featureType":"transit.station","elementType":"geometry","stylers":[{"color":"#dfd2ae"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#b9d3c2"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#92998d"}]}]';
            break;
            case 'blue_essence':
                $th_map_style = '[{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#e0efef"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"hue":"#1900ff"},{"color":"#c0e8e8"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"visibility":"on"},{"lightness":700}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#7dcdcd"}]}]';
            break;
            case 'apple_esque':
                $th_map_style = '[{"featureType":"landscape.man_made","elementType":"geometry","stylers":[{"color":"#f7f1df"}]},{"featureType":"landscape.natural","elementType":"geometry","stylers":[{"color":"#d0e3b4"}]},{"featureType":"landscape.natural.terrain","elementType":"geometry","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.medical","elementType":"geometry","stylers":[{"color":"#fbd3da"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#bde6ab"}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffe15f"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#efd151"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"color":"black"}]},{"featureType":"transit.station.airport","elementType":"geometry.fill","stylers":[{"color":"#cfb2db"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#a2daf2"}]}]';
            break;
            default:
                $th_map_style = false;
        }

        // Link 1
        if ( empty( $settings['link_1_url']['url'] ) ) { $settings['link_1_url']['url'] = '#'; };

        if ( ! empty( $settings['link_1_url']['url'] ) ) {
            $this->add_render_attribute( 'link-1', 'href', esc_url( $settings['link_1_url']['url'] ) );

            if ( ! empty( $settings['link_1_url']['is_external'] ) ) {
                $this->add_render_attribute( 'link-1', 'target', '_blank' );
            }
        }

        // Link 2
        if ( empty( $settings['link_2_url']['url'] ) ) { $settings['link_2_url']['url'] = '#'; };

        if ( ! empty( $settings['link_2_url']['url'] ) ) {
            $this->add_render_attribute( 'link-2', 'href', esc_url( $settings['link_2_url']['url'] ) );

            if ( ! empty( $settings['link_2_url']['is_external'] ) ) {
                $this->add_render_attribute( 'link-2', 'target', '_blank' );
            }
        }
		?>
        <div class="container th-gmap-wrap">
            <div class="map-info">
                <<?php echo esc_attr($settings['title_size']); ?> class="th-map-title"><?php echo esc_html( $settings['title'] ) ?></<?php echo esc_attr($settings['title_size']); ?>>
                <?php if(!empty($settings['business_address'])){
                    echo "<div class='th-gmap-address'>";
                    echo wpautop( wp_kses_post( $settings['business_address'] ) );
                    echo "</div>";
                }; ?>

                <?php if(!empty($settings['hours'])){
                    echo "<div class='th-gmap-hoursop'>";
                    echo wpautop( wp_kses_post( $settings['hours'] ) );
                    echo "</div>";
                }; ?>

                <?php if(!empty($settings['link_1_text']) || !empty($settings['link_2_text'])){ ?>
                <div class="th-gmap-links">
                    <?php if ( ! empty( $settings['link_1_text'] ) ) : ?>
                        <a <?php echo $this->get_render_attribute_string( 'link-1' ); ?>><?php echo esc_html( $settings['link_1_text'] ) ?></a>
                    <?php endif; ?>

                    <?php if ( ! empty( $settings['link_2_text'] ) ) : ?>
                        <a <?php echo $this->get_render_attribute_string( 'link-2' ); ?>><?php echo esc_html( $settings['link_2_text'] ) ?></a>
                    <?php endif;  ?>
                </div>
                <?php }; ?>

            </div>
        </div>

		<div class="th-map" id="<?php echo $map_id ?>" data-map-latitude="<?php echo esc_attr( $settings['latitude'] ) ?>" data-map-longitude="<?php echo esc_attr( $settings['longitude'] ) ?>" data-map-zoom="<?php echo esc_attr( $settings['zoom']['size'] ) ?>" data-map-scroll="<?php echo ( $settings['prevent_scroll'] == 'yes' ? "false" : "true" ); ?>" data-map-style='<?php if( isset( $th_map_style ) ) echo $th_map_style; ?>'></div>


		<?php
	}

	protected function content_template() {}

	
}

Plugin::instance()->widgets_manager->register( new Themo_Widget_GoogleMaps() );
