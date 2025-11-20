<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_RoomInfo extends Widget_Base {

	public function get_name() {
		return 'themo-room-info';
	}

	public function get_title() {
		return __( 'Room Info Bar', ALOHA_DOMAIN );
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
            'section_price',
            [
                'label' => __( 'Price', ALOHA_DOMAIN ),
            ]
        );

        $this->add_control(
            'price',
            [
                'label' => __( 'Price', ALOHA_DOMAIN ),
                'type' => Controls_Manager::TEXT,
                'default' => __( '$299', ALOHA_DOMAIN ),
                'placeholder' => __( '$299', ALOHA_DOMAIN ),
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $this->add_control(
            'price_text',
            [
                'label' => __( 'Price Text', ALOHA_DOMAIN ),
                'type' => Controls_Manager::TEXT,
                'default' => __( '/person', ALOHA_DOMAIN ),
                'placeholder' => __( '/person', ALOHA_DOMAIN ),
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $this->end_controls_section();

	    $this->start_controls_section(
			'section_items',
			[
				'label' => __( 'Items', ALOHA_DOMAIN ),
			]
        );
        
        $repeater = new Repeater();


        $repeater->add_control(
            'new_icon', [
            'fa4compatibility' => 'icon',
            'label' => __( 'Icon', ALOHA_DOMAIN ),
            'type' => Controls_Manager::ICONS,
            'label_block' => true,
            'default' => [
                'value' => 'fas fa-star',
                'library' => 'fa-solid',
            ],
            ]
        );    

        $repeater->add_control(
            'text', [
            'label' => __( 'Text', ALOHA_DOMAIN ),
            'type' => Controls_Manager::TEXT,
            'placeholder' => 'Feature',
            'label_block' => true,
            'default' => 'Feature',
            'dynamic' => [
                'active' => true,
            ]
            ]
        );    

		$this->add_control(
			'items',
			[
				'label' => __( 'Items', ALOHA_DOMAIN ),
				'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        // 'icon' => __( 'th-trip travelpack-compass', ALOHA_DOMAIN ),
                        'new_icon' => [
                            'value' => 'th-trip travelpack-compass',
							'library' => 'th-trip',  
                        ],
                        'text' => __( '4.5 Miles', ALOHA_DOMAIN ),
                    ],
                    [
                        // 'icon' => __( 'th-trip travelpack-clock-time', ALOHA_DOMAIN ),
                        'new_icon' => [
                            'value' => 'th-trip travelpack-clock-time',
							'library' => 'th-linea',  
                        ],
                        'text' => __( '3 Hours', ALOHA_DOMAIN ),
                    ],
                    [
                        // 'icon' => __( 'th-trip th-prsn travelpack-person-plus', ALOHA_DOMAIN ),
                        'new_icon' => [
                            'value' => 'th-trip th-prsn travelpack-person-plus',
							'library' => 'th-trip',  
                        ],
                        'text' => __( '3+ People', ALOHA_DOMAIN ),
                    ],

                ],
				'fields' => $repeater->get_controls(),
				'title_field' => '<i class="{{ new_icon.value }}"></i> {{{ text }}}',
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
            'section_button',
            [
                'label' => __( 'Button', ALOHA_DOMAIN ),
            ]
        );

        $this->add_control(
            'button_1_text',
            [
                'label' => __( 'Button Text', ALOHA_DOMAIN ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Book Room', ALOHA_DOMAIN ),
                'placeholder' => __( 'Book Room', ALOHA_DOMAIN ),
                'separator' => 'before',
                'dynamic' => [
                    'active' => true,
                ]
            ]
        );

        $this->add_control(
            'button_1_style',
            [
                'label' => __( 'Button Style', ALOHA_DOMAIN ),
                'type' => Controls_Manager::SELECT,
                'default' => 'ghost-dark',
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
            'button_1_link',
            [
                'label' => __( 'Link', ALOHA_DOMAIN ),
                'type' => Controls_Manager::URL,
                'placeholder' => __( '#bookroom', ALOHA_DOMAIN ),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->end_controls_section();

		$this->start_controls_section(
			'section_style_colors',
			[
				'label' => __( 'Content', ALOHA_DOMAIN ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
            'section_price_heading',
            [
                'label' => __( 'Price', 'elementor' ),
                'type' => Controls_Manager::HEADING,
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __( 'Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '#1b1b1b',
                'selectors' => [
                    '{{WRAPPER}} .th-tour-nav-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'selector' => '{{WRAPPER}} .th-tour-nav-price',

            ]
        );

        $this->add_control(
            'section_price_text_heading',
            [
                'label' => __( 'Price Text', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'price_text_color',
            [
                'label' => __( 'Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '#1b1b1b',
                'selectors' => [
                    '{{WRAPPER}} .th-tour-nav-price span' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'price_text_typography',
                'selector' => '{{WRAPPER}} .th-tour-nav-price span',

            ]
        );

        $this->add_control(
            'section_price_icon_heading',
            [
                'label' => __( 'Icon', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .th-tour-nav-item i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .th-tour-nav-item svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .th-tour-nav-item svg path' => 'fill: {{VALUE}};',
				],
                'default' => '#1b1b1b',
			]
		);

        $this->add_responsive_control(
            'icon_size',
            [
                'label' => __( 'Size', 'elementor' ),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .th-tour-nav-item i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .th-tour-nav-item svg' => 'height:auto; width: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_control(
            'section_icon_text_heading',
            [
                'label' => __( 'Text', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'text',
			[
				'label' => __( 'Color', ALOHA_DOMAIN ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .th-tour-nav-item span' => 'color: {{VALUE}};',
				],
                'default' => '#1b1b1b',
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'price_icon_typography',
                'selector' => '{{WRAPPER}} .th-tour-nav-item span',

            ]
        );

        $this->add_control(
            'section_button_text_heading',
            [
                'label' => __( 'Button', 'elementor' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __( 'Text Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .th-tour-nav-btn .btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_text_typography',
                'selector' => '{{WRAPPER}} .th-tour-nav-btn .btn',

            ]
        );

        $this->add_responsive_control(
            'button_text_padding',
            [
                'label' => __( 'Padding', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .th-tour-nav-btn .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );




		$this->end_controls_section();

        $this->start_controls_section(
            'section_padding_content',
            [
                'label' => __( 'Padding', ALOHA_DOMAIN ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'section_padding',
            [
                'label' => __( 'Padding', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .th-tour-nav' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_background_content',
            [
                'label' => __( 'Background', ALOHA_DOMAIN ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'section_background',
            [
                'label' => __( 'Color', ALOHA_DOMAIN ),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .th-tour-nav' => 'background-color: {{VALUE}};',
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
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .th-tour-nav',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'card_border_radius',
            [
                'label' => __( 'Border Radius', 'elementor' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .th-tour-nav' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'card_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .th-tour-nav',
            ]
        );

        $this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

        $items = $this->get_settings_for_display( 'items' );

        if ( empty( $settings['button_1_link']['url'] ) ) { $settings['button_1_link']['url'] = '#'; };

        $this->add_render_attribute( 'btn-1-link', 'class', 'btn-1' );
        $this->add_render_attribute( 'btn-1-link', 'class', 'btn' );
        $this->add_render_attribute( 'btn-1-link', 'class', 'th-btn' );
        $this->add_render_attribute( 'btn-1-link', 'class', 'btn-' . esc_attr( $settings['button_1_style'] ) );

        if ( ! empty( $settings['button_1_link']['url'] ) ) {
            $this->add_render_attribute( 'btn-1-link', 'href', esc_url( $settings['button_1_link']['url'] ) );

            if ( ! empty( $settings['button_1_link']['is_external'] ) ) {
                $this->add_render_attribute( 'btn-1-link', 'target', '_blank' );
            }
        }

		?>
		<div class="th-tour-nav">

            <?php if ( ! empty( $settings['price'] ) ) : ?>
            <div class="th-tour-nav-price">
                <?php echo esc_html( $settings['price'] ) ?><?php if ( ! empty( $settings['price_text'] ) ) : ?><span><?php echo esc_html( $settings['price_text'] ) ?></span><?php endif;?>
            </div>
            <?php endif;?>

            <?php if ( ! empty( $settings['button_1_text'] )  ) : ?>
                <div class="th-tour-nav-btn">
                <a <?php echo $this->get_render_attribute_string( 'btn-1-link' ); ?>>
                    <?php if ( ! empty( $settings['button_1_text'] ) ) : ?>
                        <?php echo esc_html( $settings['button_1_text'] ); ?>
                    <?php endif; ?>
                </a>
                </div>
            <?php endif; ?>

			<div class="th-tour-nav-items">
				<?php
				$counter = 1; ?>
                <?php foreach ( $items as $item ) : ?>
					<span class="th-tour-nav-item">
                        <?php
                        // new icon render
						$migrated = isset( $item['__fa4_migrated']['new_icon'] );
						$is_new = empty( $item['icon'] );
						if ( $is_new || $migrated ) {
							\Elementor\Icons_Manager::render_icon( $item['new_icon'], [ 'aria-hidden' => 'true' ] ); 
						} else {
							?><i class="<?php echo $item['icon']; ?>" aria-hidden="true"></i><?php
                        }
                        ?>
						<span><?php echo esc_html( $item['text'] ); ?></span>
					</span>
                    <?php
                    $counter++;
                endforeach; ?>
			</div>
		</div>
		<?php
	}

	protected function content_template() {
		?>
        <#  var button_1_link_url = '#';
        if ( settings.button_1_link.url ) { var button_1_link_url = settings.button_1_link.url }
        #>

        <div class="th-tour-nav">
            <# if ( settings.price ) { #>
            <div class="th-tour-nav-price">
                {{{ settings.price }}}<# if ( settings.price ) { #><span>{{{ settings.price_text }}}</span><# } #>
            </div>
            <# } #>

            <# if ( button_1_link_url ) { #>
                <div class="th-tour-nav-btn">
                    <a class="btn th-btn btn-{{ settings.button_1_style }}" href="{{ button_1_link_url }}">
                        {{{ settings.button_1_text }}}

                    </a>
                </div>
            <# } #>

            <div class="th-tour-nav-items">
            <# if ( settings.items ) {
                    _.each(settings.items, function( item ) { 
                        item.iconHTML = elementor.helpers.renderIcon( view, item.new_icon, { 'aria-hidden': true }, 'i' , 'object' ); 
                        item.migrated = elementor.helpers.isIconMigrated( item, 'new_icon' );
                        #>
                    <span class="th-tour-nav-item">
                        <# if ( item.iconHTML.rendered && ( ! item.icon || item.migrated ) ) { #>
					        {{{ item.iconHTML.value }}}
				        <# } else { #>
					        <i class="{{ item.icon }}" aria-hidden="true"></i>
				        <# } #>
                        <span>{{{ item.text }}}</span>
                    </span>

                <#  } );
                } #>
            </div>
        </div>
        <?php
	}

	
}

Plugin::instance()->widgets_manager->register( new Themo_Widget_RoomInfo() );
