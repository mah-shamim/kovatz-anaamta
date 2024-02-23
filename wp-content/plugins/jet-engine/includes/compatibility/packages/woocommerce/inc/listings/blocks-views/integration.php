<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings\Blocks_Views;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Integration {

	public function __construct() {

		add_filter( 'jet-engine/blocks-views/block-types/attributes/dynamic-link', [ $this, 'register_add_to_cart_atts' ] );
		add_filter( 'jet-engine/blocks-views/custom-blocks-controls', [ $this, 'register_link_controls' ] );
		add_filter( 'jet-engine/listings/macros-list', [ $this, 'register_macros' ] );

		add_action( 'jet-engine/blocks-views/dynamic-link/style-controls', [ $this, 'register_dynamic_link_style_controls' ], 10, 2 );

	}

	public function register_macros( $macros_list ) {

		$macros_list['add_to_cart_text'] = [
			'label' => __( 'Add to Cart Text', 'jet-engine' ),
			'cb'    => [ $this, 'add_to_cart_text' ],
		];

		return $macros_list;

	}

	/**
	 * Add to cart text.
	 *
	 * Add to cart text callback function.
	 *
	 * @since  3.0.8
	 * @access public
	 *
	 * @return string
	 */
	public function add_to_cart_text() {

		global $product;

		if ( is_null( $product ) ) {
			$product = jet_engine()->listings->data->get_current_object();
		}

		if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
			return esc_attr__( 'Add to Cart', 'jet-engine' );
		}

		return esc_html( $product->add_to_cart_text() );

	}

	/**
	 * Register add to cart atts.
	 *
	 * Register add to cart source custom attributes.
	 *
	 * @since  3.0.8
	 * @access public
	 *
	 * @param array $atts Attributes list
	 *
	 * @return array
	 */
	public function register_add_to_cart_atts( $atts ) {

		$atts['dynamic_link_enable_quantity_input'] = [
			'type'    => 'boolean',
			'default' => false,
		];

		$atts['dynamic_link_add_to_cart_quantity'] = [
			'type'    => 'number',
			'default' => 1,
		];

		return $atts;

	}

	/**
	 * Register link controls.
	 *
	 * Register add to cart source custom controls.
	 *
	 * @since  3.0.8
	 * @access public
	 *
	 * @param array $controls Controls list.
	 *
	 * @return array
	 */
	public function register_link_controls( $controls = [] ) {

		$link_controls = ! empty( $controls['dynamic-link'] ) ? $controls['dynamic-link'] : [];

		$link_controls[] = [
			'type'      => 'switcher',
			'name'      => 'dynamic_link_enable_quantity_input',
			'label'     => __( 'Enable quantity input', 'jet-engine' ),
			'default'   => false,
			'condition' => [
				'dynamic_link_source' => [ 'add_to_cart' ],
			],
		];

		$link_controls[] = [
			'type'      => 'number',
			'name'      => 'dynamic_link_add_to_cart_quantity',
			'label'     => __( 'Quantity', 'jet-engine' ),
			'default'   => 1,
			'condition' => [
				'dynamic_link_source' => [ 'add_to_cart' ],
			],
		];

		$controls['dynamic-link'] = $link_controls;

		return $controls;

	}

	/**
	 * Register dynamic links style controls.
	 *
	 * Register add to cart source quantity input styles.
	 *
	 * @since  3.0.8
	 * @access public
	 *
	 * @param object $controls_manager JetStyleManager controls instance.
	 * @param object $block            Dynamic link block instance.
	 *
	 * @return void
	 */
	public function register_dynamic_link_style_controls( $controls_manager, $block ) {

		$controls_manager->start_section(
			'style_controls',
			[
				'id'        => 'dynamic_link_quantity_input_styles',
				'title'     => __( 'Quantity Input', 'jet-engine' ),
				'condition' => [
					'dynamic_link_enable_quantity_input' => [ 'yes', 'true', '1' ],
				],
			]
		);

		$controls_manager->add_responsive_control(
			[
				'id'           => 'dynamic_link_quantity_input_display_type',
				'label'        => __( 'Display Type', 'jet-engine' ),
				'type'         => 'select',
				'options'      => [
					[
						'label' => __( 'Inline', 'jet-engine' ),
						'value' => 'row',
					],
					[
						'label' => __( 'Block', 'jet-engine' ),
						'value' => 'column',
					],
				],
				'css_selector' => [
					$block->css_selector( ' .cart' ) => 'flex-direction: {{VALUE}};',
				],
			]
		);

		$controls_manager->add_control( [
			'id'           => 'dynamic_link_quantity_input_width',
			'type'         => 'range',
			'label'        => __( 'Width', 'jet-engine' ),
			'attributes'   => [
				'default' => [
					'value' => [
						'value' => 70,
						'unit'  => 'px',
					],
				],
			],
			'units'        => [
				[
					'value'     => 'px',
					'intervals' => [
						'min' => 50,
						'max' => 500,
					],
				],
				[
					'value'     => '%',
					'intervals' => [
						'min' => 10,
						'max' => 100,
					],
				],
			],
			'css_selector' => [
				$block->css_selector( ' .quantity' ) => 'width: {{VALUE}}{{UNIT}};',
			],
		] );

		$controls_manager->add_control( [
			'id'           => 'dynamic_link_quantity_input_typography',
			'type'         => 'typography',
			'css_selector' => [
				$block->css_selector( ' .quantity .qty' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
			],
		] );

		$controls_manager->start_tabs(
			'style_controls',
			[
				'id'        => 'dynamic_link_quantity_input_tabs',
				'separator' => 'after',
			]
		);

		$controls_manager->start_tab(
			'style_controls',
			[
				'id'    => 'dynamic_link_quantity_input_tab_normal',
				'title' => __( 'Normal', 'jet-engine' ),
			]
		);

		$controls_manager->add_control( [
			'id'           => 'dynamic_link_quantity_input_color',
			'type'         => 'color-picker',
			'label'        => __( 'Color', 'jet-engine' ),
			'css_selector' => [
				$block->css_selector( ' .quantity .qty' ) => 'color: {{VALUE}}',
			],
		] );

		$controls_manager->add_control( [
			'id'           => 'dynamic_link_quantity_input_background',
			'type'         => 'color-picker',
			'label'        => __( 'Background Color', 'jet-engine' ),
			'css_selector' => [
				$block->css_selector( ' .quantity .qty' ) => 'background-color: {{VALUE}}',
			],
		] );

		$controls_manager->end_tab();

		$controls_manager->start_tab(
			'style_controls',
			[
				'id'    => 'dynamic_link_quantity_input_tab_focus',
				'title' => __( 'Focus', 'jet-engine' ),
			]
		);

		$controls_manager->add_control( [
			'id'           => 'dynamic_link_quantity_input_color_focus',
			'type'         => 'color-picker',
			'label'        => __( 'Color', 'jet-engine' ),
			'css_selector' => [
				$block->css_selector( ' .quantity .qty:focus' ) => 'color: {{VALUE}}',
			],
		] );

		$controls_manager->add_control( [
			'id'           => 'dynamic_link_quantity_input_background_focus',
			'type'         => 'color-picker',
			'label'        => __( 'Background Color', 'jet-engine' ),
			'css_selector' => [
				$block->css_selector( ' .quantity .qty:focus' ) => 'background-color: {{VALUE}}',
			],
		] );

		$controls_manager->add_control( [
			'id'           => 'dynamic_link_quantity_input_border_color_focus',
			'type'         => 'color-picker',
			'label'        => __( 'Border Color', 'jet-engine' ),
			'css_selector' => [
				$block->css_selector( ' .quantity .qty:focus' ) => 'border-color: {{VALUE}}',
			],
		] );

		$controls_manager->end_tab();

		$controls_manager->end_tabs();

		$controls_manager->add_control( [
			'id'           => 'dynamic_link_quantity_input_border',
			'type'         => 'border',
			'label'        => __( 'Border', 'jet-engine' ),
			'css_selector' => [
				$block->css_selector( ' .quantity .qty' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
			],
		] );

		$controls_manager->add_control( [
			'id'           => 'dynamic_link_quantity_input_margin',
			'type'         => 'dimensions',
			'label'        => __( 'Margin', 'jet-engine' ),
			'units'        => [ 'px', '%' ],
			'css_selector' => [
				$block->css_selector( ' .quantity' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
			],
		] );

		$controls_manager->add_control( [
			'id'           => 'dynamic_link_quantity_input_padding',
			'type'         => 'dimensions',
			'label'        => __( 'Padding', 'jet-engine' ),
			'units'        => [ 'px', '%' ],
			'css_selector' => [
				$block->css_selector( ' .quantity .qty' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
			],
		] );

		$controls_manager->end_section();

	}

}