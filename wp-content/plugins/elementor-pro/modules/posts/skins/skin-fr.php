<?php
namespace ElementorPro\Modules\Posts\Skins;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use ElementorPro\Core\Utils as ProUtils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Skin_FR extends Skin_Base {

	protected function _register_controls_actions() {
		parent::_register_controls_actions();

		add_action( 'elementor/element/posts/classic_section_design_layout/after_section_end', [ $this, 'register_additional_design_controls' ] );
	}

	public function get_id() {
		return 'fr';
	}

	public function get_title() {
		return esc_html__( 'FR', 'elementor-pro' );
	}

	public function register_additional_design_controls() {
		$this->start_controls_section(
			'section_design_box',
			[
				'label' => esc_html__( 'Box', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'elementor-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'border-style: solid; border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'box_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor-pro' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 200,
					],
					'em' => [
						'max' => 20,
					],
					'rem' => [
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label' => esc_html__( 'Padding', 'elementor-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__( 'Content Padding', 'elementor-pro' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'max' => 50,
					],
					'em' => [
						'max' => 5,
					],
					'rem' => [
						'max' => 5,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-post__text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'separator' => 'after',
			]
		);

		$this->start_controls_tabs( 'bg_effects_tabs' );

		$this->start_controls_tab( 'classic_style_normal',
			[
				'label' => esc_html__( 'Normal', 'elementor-pro' ),
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
				'selector' => '{{WRAPPER}} .elementor-post',
			]
		);

		$this->add_control(
			'box_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-post' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'classic_style_hover',
			[
				'label' => esc_html__( 'Hover', 'elementor-pro' ),
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow_hover',
				'selector' => '{{WRAPPER}} .elementor-post:hover',
			]
		);

		$this->add_control(
			'box_bg_color_hover',
			[
				'label' => esc_html__( 'Background Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-post:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'box_border_color_hover',
			[
				'label' => esc_html__( 'Border Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-post:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

    protected function render_excerpt() {
        add_filter( 'excerpt_more', [ $this, 'filter_excerpt_more' ], 20 );
        add_filter( 'excerpt_length', [ $this, 'filter_excerpt_length' ], 20 );

        if ( ! $this->get_instance_value( 'show_excerpt' ) ) {
            return;
        }

        add_filter( 'excerpt_more', [ $this, 'filter_excerpt_more' ], 20 );
        add_filter( 'excerpt_length', [ $this, 'filter_excerpt_length' ], 20 );

        ?>
        <div class="elementor-post__excerpt">
            <?php
                // Force the manually-generated Excerpt length as well if the user chose to enable 'apply_to_custom_excerpt'.
                the_content();
            ?>
        </div>
        <?php

        remove_filter( 'excerpt_length', [ $this, 'filter_excerpt_length' ], 20 );
        remove_filter( 'excerpt_more', [ $this, 'filter_excerpt_more' ], 20 );
    }

    protected function render_post_header() {
        ?>
    <article <?php post_class( [ 'elementor-post elementor-post-viewpoint elementor-grid-item' ] ); ?> role="listitem">
        <?php
    }

    protected function render_post() {
        $this->render_post_header();
        $this->render_thumbnail();
        $this->render_text_header();
        $this->render_title();
//        $this->render_meta_data();
        $this->render_excerpt();
        $this->render_read_more();
        $this->render_text_footer();
        $this->render_post_footer();
    }
}
