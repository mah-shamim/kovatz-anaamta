<?php

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

/**
 * Elementor box styles control.
 *
 * A base control for creating box style control. Displays input fields to define
 * the boxes styles.
 *
 * @since 1.1.0
 */
class Jet_Woo_Group_Control_Box_Style extends Elementor\Group_Control_Base {

	/**
	 * Fields.
	 *
	 * Holds all the box style control fields.
	 *
	 * @since  1.1.0
	 * @access protected
	 * @static
	 *
	 * @var array Background control fields.
	 */
	protected static $fields;

	/**
	 * Get box style control type.
	 *
	 * Retrieve the control type, in this case `jet-woo-box-style`.
	 *
	 * @since  1.1.0
	 * @access public
	 * @static
	 *
	 * @return string Control type.
	 */
	public static function get_type() {
		return 'jet-woo-box-style';
	}

	/**
	 * Init fields.
	 *
	 * Initialize box style control fields.
	 *
	 * @since  1.1.0
	 * @access public
	 *
	 * @return array Control fields.
	 */
	protected function init_fields() {

		$fields = [];

		$fields['background'] = [
			'label'       => _x( 'Background Type', 'Background Control', 'jet-woo-builder' ),
			'type'        => Controls_Manager::CHOOSE,
			'options'     => [
				'color'    => [
					'title' => _x( 'Classic', 'Background Control', 'jet-woo-builder' ),
					'icon'  => 'fa fa-paint-brush',
				],
				'gradient' => [
					'title' => _x( 'Gradient', 'Background Control', 'jet-woo-builder' ),
					'icon'  => 'fa fa-barcode',
				],
			],
			'render_type' => 'ui',
		];

		$fields['color'] = [
			'label'     => _x( 'Color', 'Background Control', 'jet-woo-builder' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'title'     => _x( 'Background Color', 'Background Control', 'jet-woo-builder' ),
			'selectors' => [
				'{{SELECTOR}}' => 'background-color: {{VALUE}};',
			],
			'condition' => [
				'background' => [ 'color', 'gradient' ],
			],
		];

		$fields['color_stop'] = [
			'label'       => _x( 'Location', 'Background Control', 'jet-woo-builder' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => [ '%' ],
			'default'     => [
				'unit' => '%',
				'size' => 0,
			],
			'render_type' => 'ui',
			'condition'   => [
				'background' => [ 'gradient' ],
			],
			'of_type'     => 'gradient',
		];

		$fields['color_b'] = [
			'label'       => _x( 'Second Color', 'Background Control', 'jet-woo-builder' ),
			'type'        => Controls_Manager::COLOR,
			'default'     => '#f2295b',
			'render_type' => 'ui',
			'condition'   => [
				'background' => [ 'gradient' ],
			],
			'of_type'     => 'gradient',
		];

		$fields['color_b_stop'] = [
			'label'       => _x( 'Location', 'Background Control', 'jet-woo-builder' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => [ '%' ],
			'default'     => [
				'unit' => '%',
				'size' => 100,
			],
			'render_type' => 'ui',
			'condition'   => [
				'background' => [ 'gradient' ],
			],
			'of_type'     => 'gradient',
		];

		$fields['gradient_type'] = [
			'label'       => _x( 'Type', 'Background Control', 'jet-woo-builder' ),
			'type'        => Controls_Manager::SELECT,
			'options'     => [
				'linear' => _x( 'Linear', 'Background Control', 'jet-woo-builder' ),
				'radial' => _x( 'Radial', 'Background Control', 'jet-woo-builder' ),
			],
			'default'     => 'linear',
			'render_type' => 'ui',
			'condition'   => [
				'background' => [ 'gradient' ],
			],
			'of_type'     => 'gradient',
		];

		$fields['gradient_angle'] = [
			'label'      => _x( 'Angle', 'Background Control', 'jet-woo-builder' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'deg' ],
			'default'    => [
				'unit' => 'deg',
				'size' => 180,
			],
			'range'      => [
				'deg' => [
					'step' => 10,
				],
			],
			'selectors'  => [
				'{{SELECTOR}}' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
			],
			'condition'  => [
				'background'    => [ 'gradient' ],
				'gradient_type' => 'linear',
			],
			'of_type'    => 'gradient',
		];

		$fields['gradient_position'] = [
			'label'     => _x( 'Position', 'Background Control', 'jet-woo-builder' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'center center' => _x( 'Center Center', 'Background Control', 'jet-woo-builder' ),
				'center left'   => _x( 'Center Left', 'Background Control', 'jet-woo-builder' ),
				'center right'  => _x( 'Center Right', 'Background Control', 'jet-woo-builder' ),
				'top center'    => _x( 'Top Center', 'Background Control', 'jet-woo-builder' ),
				'top left'      => _x( 'Top Left', 'Background Control', 'jet-woo-builder' ),
				'top right'     => _x( 'Top Right', 'Background Control', 'jet-woo-builder' ),
				'bottom center' => _x( 'Bottom Center', 'Background Control', 'jet-woo-builder' ),
				'bottom left'   => _x( 'Bottom Left', 'Background Control', 'jet-woo-builder' ),
				'bottom right'  => _x( 'Bottom Right', 'Background Control', 'jet-woo-builder' ),
			],
			'default'   => 'center center',
			'selectors' => [
				'{{SELECTOR}}' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{color.VALUE}} {{color_stop.SIZE}}{{color_stop.UNIT}}, {{color_b.VALUE}} {{color_b_stop.SIZE}}{{color_b_stop.UNIT}})',
			],
			'condition' => [
				'background'    => [ 'gradient' ],
				'gradient_type' => 'radial',
			],
			'of_type'   => 'gradient',
		];

		$fields['box_font_color'] = [
			'label'     => __( 'Font Color', 'jet-woo-builder' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{SELECTOR}}'     => 'color: {{VALUE}}',
				'{{SELECTOR}} svg' => 'fill: {{VALUE}}',
			],
		];

		$fields['box_font_size'] = [
			'label'      => __( 'Font Size', 'jet-woo-builder' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em', 'rem', ],
			'responsive' => true,
			'range'      => [
				'px' => [
					'min' => 5,
					'max' => 500,
				],
			],
			'selectors'  => [
				'{{SELECTOR}}'        => 'font-size: {{SIZE}}{{UNIT}}',
				'{{SELECTOR}}:before' => 'font-size: {{SIZE}}{{UNIT}}',
			],
		];

		$fields['separate_box_sizes'] = [
			'label' => __( 'Separate Box Sizes', 'jet-woo-builder' ),
			'type'  => Controls_Manager::SWITCHER,
		];

		$fields['box_size'] = [
			'label'      => __( 'Box Size', 'jet-woo-builder' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em', '%' ],
			'responsive' => true,
			'selectors'  => [
				'{{SELECTOR}}' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				'separate_box_sizes' => '',
			],
		];

		$fields['box_width'] = [
			'label'      => __( 'Box Width', 'jet-woo-builder' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em', '%' ],
			'responsive' => true,
			'selectors'  => [
				'{{SELECTOR}}' => 'width: {{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				'separate_box_sizes!' => '',
			],
		];

		$fields['box_height'] = [
			'label'      => __( 'Box Height', 'jet-woo-builder' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em', '%' ],
			'responsive' => true,
			'selectors'  => [
				'{{SELECTOR}}' => 'height: {{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				'separate_box_sizes!' => '',
			],
		];

		$fields['box_border'] = [
			'label'     => _x( 'Border Type', 'Border Control', 'jet-woo-builder' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				''       => __( 'None', 'jet-woo-builder' ),
				'solid'  => _x( 'Solid', 'Border Control', 'jet-woo-builder' ),
				'double' => _x( 'Double', 'Border Control', 'jet-woo-builder' ),
				'dotted' => _x( 'Dotted', 'Border Control', 'jet-woo-builder' ),
				'dashed' => _x( 'Dashed', 'Border Control', 'jet-woo-builder' ),
			],
			'selectors' => [
				'{{SELECTOR}}' => 'border-style: {{VALUE}};',
			],
		];

		$fields['box_border_width'] = [
			'label'     => _x( 'Width', 'Border Control', 'jet-woo-builder' ),
			'type'      => Controls_Manager::DIMENSIONS,
			'selectors' => [
				'{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition' => [
				'box_border!' => '',
			],
		];

		$fields['box_border_color'] = [
			'label'     => _x( 'Color', 'Border Control', 'jet-woo-builder' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{SELECTOR}}' => 'border-color: {{VALUE}};',
			],
			'condition' => [
				'box_border!' => '',
			],
		];

		$fields['box_border_radius'] = [
			'label'      => __( 'Border Radius', 'jet-woo-builder' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%' ],
			'selectors'  => [
				'{{SELECTOR}}' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		];

		$fields['allow_box_shadow'] = [
			'label' => _x( 'Box Shadow', 'Box Shadow Control', 'jet-woo-builder' ),
			'type'  => Controls_Manager::SWITCHER,
		];

		$fields['box_shadow'] = [
			'label'     => _x( 'Box Shadow', 'Box Shadow Control', 'jet-woo-builder' ),
			'type'      => Controls_Manager::BOX_SHADOW,
			'condition' => [
				'allow_box_shadow!' => '',
			],
			'selectors' => [
				'{{SELECTOR}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
			],
		];

		$fields['box_shadow_position'] = [
			'label'       => _x( 'Position', 'Box Shadow Control', 'jet-woo-builder' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => ' ',
			'options'     => [
				' '     => _x( 'Outline', 'Box Shadow Control', 'jet-woo-builder' ),
				'inset' => _x( 'Inset', 'Box Shadow Control', 'jet-woo-builder' ),
			],
			'condition'   => [
				'allow_box_shadow!' => '',
			],
			'render_type' => 'ui',
		];

		return apply_filters( 'jet-woo-builder/controls/groups/group-control-box-style', $fields );

	}

	/**
	 * Prepare fields.
	 *
	 * Process box styles control fields before adding them to `add_control()`.
	 *
	 * @since  1.1.0
	 * @access protected
	 *
	 * @param array $fields Box style control fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {

		array_walk( $fields, function ( &$field, $field_name ) {

			if ( in_array( $field_name, [ 'popover_toggle' ] ) ) {
				return;
			}

			$condition = [
				'popover_toggle!' => '',
			];

			if ( isset( $field['condition'] ) ) {
				$field['condition'] = array_merge( $field['condition'], $condition );
			} else {
				$field['condition'] = $condition;
			}

		} );

		return parent::prepare_fields( $fields );

	}

}
