<?php
namespace Jet_Engine\Forms\Block_Types;

/**
 * Booking Form block type.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Booking_Form block type class
 */
class Booking_Form extends \Jet_Engine_Blocks_Views_Type_Base {

	/**
	 * Returns block name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'booking-form';
	}

	/**
	 * Return attributes array
	 *
	 * @return array
	 */
	public function get_attributes() {
		return apply_filters( 'jet-engine/blocks-views/booking-form/attributes', array(
			'_form_id' => array(
				'type'    => 'string',
				'default' => '',
			),
			'fields_layout' => array(
				'type'    => 'string',
				'default' => 'column',
			),
			'fields_label_tag' => array(
				'type'    => 'string',
				'default' => 'div',
			),
			'submit_type' => array(
				'type'    => 'string',
				'default' => 'reload',
			),
			'cache_form' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'rows_divider' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'required_mark' => array(
				'type'    => 'string',
				'default' => '*',
			),
		) );
	}

	/**
	 * Returns CSS selector for nested element
	 *
	 * @param  string|array $el
	 * @return string
	 */
	public function css_selector( $el = null ) {
		if ( ! is_array( $el ) ) {
			return sprintf( '{{WRAPPER}} .jet-form%s', $el );
		} else {

			$res = array();

			foreach ( $el as $selector ) {
				$res[] = sprintf( '{{WRAPPER}} .jet-form%s', $selector );
			}

			return implode( ', ', $res );
		}
	}

	/**
	 * Add style block options
	 *
	 * @return void
	 */
	public function add_style_manager_options() {

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_rows_style',
				'title' => esc_html__( 'Rows', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'rows_divider_height',
				'label' => __( 'Divider Height', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 20,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( '__divider' ) => 'height: {{VALUE}}{{UNIT}};',
				),
				'condition' => array(
					'rows_divider' => true,
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'rows_divider_color',
				'label' => __( 'Divider Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '__divider' ) => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'rows_divider' => true,
				),
				'separator' => 'after',
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'rows_gap',
				'label' => __( 'Rows Gap', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( '-row' ) => 'padding-top: calc( {{VALUE}}{{UNIT}}/2 ); padding-bottom: calc( {{VALUE}}{{UNIT}}/2 )',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'cols_gap',
				'label' => __( 'Columns Gap', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( '-row' ) => 'margin-left: calc( -{{VALUE}}{{UNIT}}/2 ); margin-right: calc( -{{VALUE}}{{UNIT}}/2 )',
					$this->css_selector( '-col' ) => 'padding-left: calc( {{VALUE}}{{UNIT}}/2 ); padding-right: calc( {{VALUE}}{{UNIT}}/2 )',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'labels_typography',
				'label'     => __( 'Labels Typography', 'jet-engine' ),
				'type'      => 'typography',
				'separator' => 'both',
				'css_selector' => array(
					$this->css_selector( '__label' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'labels_color',
				'label' => __( 'Labels Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '__label' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'labels_gap',
				'label'     => __( 'Labels Gap', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( '__label' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'labels_width',
				'label' => __( 'Labels Width', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => '%',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
					),
				),
				'attributes' => array(
					'default' => array(
						'value' => 30,
						'unit'  => '%',
					),
				),
				'css_selector' => array(
					$this->css_selector( '.layout-row .jet-form-col__start' ) => 'max-width: {{VALUE}}%; -ms-flex: 0 0 {{VALUE}}%; flex: 0 0 {{VALUE}}%;',
				),
				'condition' => array(
					'fields_layout' => 'row',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'desc_typography',
				'label'     => __( 'Descriptions Typography', 'jet-engine' ),
				'type'      => 'typography',
				'separator' => 'both',
				'css_selector' => array(
					$this->css_selector( '__desc' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'desc_color',
				'label' => __( 'Descriptions Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '__desc' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'           => 'desc_gap',
				'label'        => __( 'Descriptions Gap', 'jet-engine' ),
				'type'         => 'dimensions',
				'units'        => array( 'px' ),
				'separator'    => 'before',
				'css_selector' => array(
					$this->css_selector( '__desc' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'labels_h_alignment',
				'label'     => __( 'Horizontal Alignment', 'jet-engine' ),
				'type'      => 'choose',
				'separator' => 'before',
				'options'   => array(
					'left' => array(
						'shortcut' => __( 'Left', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignleft',
					),
					'center' => array(
						'shortcut' => __( 'Center', 'jet-engine' ),
						'icon'     => 'dashicons-editor-aligncenter',
					),
					'right' => array(
						'shortcut' => __( 'Right', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignright',
					),
				),
				'css_selector' => array(
					$this->css_selector( '__label' ) => 'text-align: {{VALUE}};',
					$this->css_selector( '__desc' ) => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'labels_v_alignment',
				'label'     => __( 'Vertical Alignment', 'jet-engine' ),
				'type'      => 'choose',
				'separator' => 'before',
				'options'   => array(
					'flex-start' => array(
						'shortcut' => __( 'Top', 'jet-engine' ),
						'icon'     => 'dashicons-arrow-up-alt',
					),
					'center' => array(
						'shortcut' => __( 'Middle', 'jet-engine' ),
						'icon'     => 'dashicons-align-center',
					),
					'flex-end' => array(
						'shortcut' => __( 'Bottom', 'jet-engine' ),
						'icon'     => 'dashicons-arrow-down-alt',
					),
				),
				'css_selector' => array(
					$this->css_selector( '-col' ) => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_fields_style',
				'title' => esc_html__( 'Fields', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'fields_typography',
				'label'     => __( 'Typography', 'jet-engine' ),
				'type'      => 'typography',
				'separator' => 'after',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__field:not(.checkradio-field):not(.range-field)' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'           => 'fields_color',
				'label'        => __( 'Color', 'jet-engine' ),
				'type'         => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__field:not(.checkradio-field):not(.range-field)' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'fields_placeholder_color',
				'label' => __( 'Placeholder Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' ::-webkit-input-placeholder' ) => 'color: {{VALUE}}',
					$this->css_selector( ' ::-moz-placeholder' )          => 'color: {{VALUE}}',
					$this->css_selector( ' :-ms-input-placeholder' )      => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'fields_background_color',
				'label' => __( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__field:not(.checkradio-field):not(.range-field)' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'fields_padding',
				'label'     => __( 'Padding', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__field:not(.checkradio-field):not(.range-field)' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'fields_margin',
				'label' => __( 'Margin', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px' ),
				'css_selector' => array(
					$this->css_selector( ' .jet-form__field:not(.checkradio-field):not(.range-field)' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'fields_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'separator'      => 'before',
				'disable_radius' => true,
				'css_selector' => array(
					$this->css_selector( ' .jet-form__field:not(.checkradio-field):not(.range-field)' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'fields_border_radius',
				'label'     => __( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__field:not(.checkradio-field):not(.range-field)' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		// Not supported
		//$this->controls_manager->add_control(
		//	array(
		//		'id'    => 'fields_box_shadow',
		//		'label' => __( 'Box Shadow', 'jet-engine' ),
		//		'type'  => 'box-shadow',
		//		'css_selector' => $this->css_selector( ' .jet-form__field:not(.checkradio-field):not(.range-field)' ),
		//	)
		//);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'fields_width',
				'label' => __( 'Fields width', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 50,
							'max'  => 1000,
						),
					),
					array(
						'value'     => '%',
						'intervals' => array(
							'step' => 1,
							'min'  => 1,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( ' .jet-form__field:not(.checkboxes-field):not(.radio-field):not(.range-field)' ) => 'max-width: {{VALUE}}{{UNIT}};width: {{VALUE}}{{UNIT}};flex: 0 1 {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'fields_textarea_height',
				'label' => __( 'Textarea Height', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 500,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( ' .jet-form__field.textarea-field' ) => 'height: {{VALUE}}px; min-height: {{VALUE}}px;',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'reset_appearance',
				'label'     => esc_html__( 'Reset Select Field Appearance', 'jet-engine' ),
				'help'      => esc_html__( 'Check this option to reset select field appearance CSS value. This will make select fields appearance the same for all browsers', 'jet-engine' ),
				'type'      => 'toggle',
				'unit'      => 'px',
				'separator' => 'before',
				'return_value' => array(
					'true'  => '-webkit-appearance: none;',
					'false' => '',
				),
				'css_selector' => array(
					$this->css_selector( ' .jet-form__field.select-field' ) => '{{VALUE}}',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_checkradio_fields_style',
				'title' => esc_html__( 'Checkbox and Radio Fields', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'      => 'checkradio_fields_layout',
				'label'   => __( 'Layout', 'jet-engine' ),
				'type'    => 'choose',
				'options' => array(
					'0 1 auto' => array(
						'shortcut' => __( 'Horizontal', 'jet-engine' ),
						'icon'     => 'dashicons-ellipsis',
					),
					'0 1 100%' => array(
						'shortcut' => __( 'Vertical', 'jet-engine' ),
						'icon'     => 'dashicons-menu',
					),
				),
				'css_selector' => array(
					$this->css_selector( ' .jet-form__fields-group' ) => 'display: flex; flex-wrap: wrap;',
					$this->css_selector( ' .checkradio-wrap' ) => 'flex: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'checkradio_fields_typography',
				'label'     => __( 'Typography', 'jet-engine' ),
				'type'      => 'typography',
				'separator' => 'both',
				'css_selector' => array(
					$this->css_selector( ' .checkradio-wrap' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'checkradio_fields_color',
				'label' => __( 'Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .checkradio-wrap' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'checkradio_fields_width',
				'label'     => __( 'Width', 'jet-engine' ),
				'type'      => 'range',
				'separator' => 'before',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 50,
							'max'  => 600,
						),
					),
					array(
						'value'     => '%',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( ' .jet-form__fields-group.checkradio-wrap' ) => 'max-width: {{VALUE}}{{UNIT}};flex: 0 1 {{VALUE}}{{UNIT}};width: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'checkradio_fields_col_width',
				'label'     => __( 'Column Width', 'jet-engine' ),
				'type'      => 'range',
				'separator' => 'before',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 50,
							'max'  => 600,
						),
					),
					array(
						'value'     => '%',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( ' .jet-form__field-wrap.checkradio-wrap' ) => 'flex: 0 1 {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'checkradio_fields_gap',
				'label' => __( 'Gap between control and label', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 50,
						),
					),
				),
				'css_selector' => array(
					'body:not(.rtl) ' . $this->css_selector( ' .jet-form__field.checkradio-field' ) => 'margin-right: {{VALUE}}px;',
					'body.rtl ' . $this->css_selector( ' .jet-form__field.checkradio-field' ) => 'margin-left: {{VALUE}}px;',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'checkradio_fields_background_color',
				'label'     => __( 'Background Color', 'jet-engine' ),
				'type'      => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .checkradio-wrap label' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'checkradio_fields_padding',
				'label'     => __( 'Padding', 'jet-engine' ),
				'type'      => 'dimensions',
				'separator' => 'before',
				'units' => array( 'px' ),
				'css_selector' => array(
					$this->css_selector( ' .checkradio-wrap label' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'checkradio_fields_margin',
				'label' => __( 'Margin', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px' ),
				'css_selector' => array(
					$this->css_selector( ' .checkradio-wrap label' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'checkradio_fields_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'separator'      => 'before',
				'disable_radius' => true,
				'css_selector'   => array(
					$this->css_selector( ' .checkradio-wrap label' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'checkradio_fields_border_radius',
				'label'     => __( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'separator' => 'before',
				'units'     => array( 'px', '%' ),
				'css_selector' => array(
					$this->css_selector( ' .checkradio-wrap label' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_calc_fields_style',
				'title' => esc_html__( 'Calculated Fields', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'calc_fields_typography',
				'label'     => __( 'Typography', 'jet-engine' ),
				'type'      => 'typography',
				'separator' => 'after',
				'css_selector' => array(
					$this->css_selector( '__calculated-field' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'calc_fields_color',
				'label' => __( 'Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '__calculated-field' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'calc_fields_prefix_color',
				'label'     => __( 'Prefix Color', 'jet-engine' ),
				'type'      => 'color-picker',
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( '__calculated-field-prefix' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'calc_fields_prefix_size',
				'label' => __( 'Prefix size', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 50,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( '__calculated-field-prefix' ) => 'font-size: {{VALUE}}px;',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'calc_fields_suffix_color',
				'label'     => __( 'Suffix Color', 'jet-engine' ),
				'type'      => 'color-picker',
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( '__calculated-field-suffix' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'calc_fields_suffix_size',
				'label' => __( 'Suffix size', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 50,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( '__calculated-field-suffix' ) => 'font-size: {{VALUE}}px;',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'calc_fields_background_color',
				'label'     => __( 'Background Color', 'jet-engine' ),
				'type'      => 'color-picker',
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( '__calculated-field' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'calc_fields_padding',
				'label'     => __( 'Padding', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( '__calculated-field' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'calc_fields_margin',
				'label' => __( 'Margin', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px' ),
				'css_selector' => array(
					$this->css_selector( '__calculated-field' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'calc_fields_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'disable_radius' => true,
				'separator'      => 'before',
				'css_selector'   => array(
					$this->css_selector( '__calculated-field' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'calc_fields_border_radius',
				'label'     => __( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( '__calculated-field' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_range_fields_style',
				'title' => esc_html__( 'Range Fields', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'range_max_width',
				'label' => esc_html__( 'Max Width', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 1,
							'max'  => 1000,
						),
					),
					array(
						'value'     => '%',
						'intervals' => array(
							'step' => 1,
							'min'  => 1,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( '__field-wrap.range-wrap' ) => 'max-width: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'range_slider_heading',
				'type'      => 'text',
				'content'   => esc_html__( 'Slider', 'jet-engine' ),
				'separator' => 'before',
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'track_height',
				'label' => esc_html__( 'Track Height', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 1,
							'max'  => 20,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( '__field.range-field[type="range"]::-webkit-slider-runnable-track' ) => 'height: {{VALUE}}{{UNIT}};',
					$this->css_selector( '__field.range-field[type="range"]::-moz-range-track' ) => 'height: {{VALUE}}{{UNIT}};',
					$this->css_selector( '__field.range-field[type="range"]::-ms-track' ) => 'height: {{VALUE}}{{UNIT}};',

					$this->css_selector( '__field.range-field[type="range"]::-webkit-slider-thumb' ) => 'margin-top: calc( {{VALUE}}{{UNIT}}/2 ); transform: translateY(-50%);',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'thumb_size',
				'label' => esc_html__( 'Thumb Size', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 1,
							'max'  => 50,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( '__field.range-field[type="range"]' ) => 'min-height: {{VALUE}}{{UNIT}};',

					$this->css_selector( '__field.range-field[type="range"]::-webkit-slider-thumb' ) => 'width: {{VALUE}}{{UNIT}}; height: {{VALUE}}{{UNIT}};',
					$this->css_selector( '__field.range-field[type="range"]::-moz-range-thumb' ) => 'width: {{VALUE}}{{UNIT}}; height: {{VALUE}}{{UNIT}};',
					$this->css_selector( '__field.range-field[type="range"]::-ms-thumb' ) => 'width: {{VALUE}}{{UNIT}}; height: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'track_border_radius',
				'label' => esc_html__( 'Track Border Radius', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%' ),
				'css_selector' => array(
					$this->css_selector( '__field.range-field[type="range"]::-webkit-slider-runnable-track' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					$this->css_selector( '__field.range-field[type="range"]::-moz-range-track' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					$this->css_selector( '__field.range-field[type="range"]::-ms-track' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'thumb_border_radius',
				'label' => esc_html__( 'Thumb Border Radius', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%' ),
				'css_selector' => array(
					$this->css_selector( '__field.range-field[type="range"]::-webkit-slider-thumb' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					$this->css_selector( '__field.range-field[type="range"]::-moz-range-thumb' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					$this->css_selector( '__field.range-field[type="range"]::-ms-thumb' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'track_bg_color',
				'label' => esc_html__( 'Track Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '__field.range-field[type="range"]::-webkit-slider-runnable-track' ) => 'background-color: {{VALUE}};',
					$this->css_selector( '__field.range-field[type="range"]::-moz-range-track' ) => 'background-color: {{VALUE}};',
					$this->css_selector( '__field.range-field[type="range"]::-ms-track' ) => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'thumb_bg_color',
				'label' => esc_html__( 'Thumb Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '__field.range-field[type="range"]::-webkit-slider-thumb' ) => 'background-color: {{VALUE}};',
					$this->css_selector( '__field.range-field[type="range"]::-moz-range-thumb' ) => 'background-color: {{VALUE}};',
					$this->css_selector( '__field.range-field[type="range"]::-ms-thumb' ) => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'range_value_heading',
				'type'      => 'text',
				'content'   => esc_html__( 'Value', 'jet-engine' ),
				'separator' => 'before',
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'range_value_typography',
				'label' => esc_html__( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css_selector' => array(
					$this->css_selector( '__field-value.range-value' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'range_value_color',
				'label' => esc_html__( 'Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '__field-value.range-value' ) => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'range_prefix_value_size',
				'label' => __( 'Prefix size', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 50,
						),
					),
					array(
						'value'     => 'em',
						'intervals' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 10,
						),
					),
					array(
						'value'     => 'rem',
						'intervals' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 10,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( '__field-value.range-value .jet-form__field-value-prefix' ) => 'font-size: {{VALUE}}px;',
				),
				'separator' => 'before',
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'range_prefix_value_color',
				'label' => __( 'Prefix Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '__field-value.range-value .jet-form__field-value-prefix' ) => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'range_suffix_value_size',
				'label' => __( 'Suffix size', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 50,
						),
					),
					array(
						'value'     => 'em',
						'intervals' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 10,
						),
					),
					array(
						'value'     => 'rem',
						'intervals' => array(
							'step' => 0.1,
							'min'  => 0,
							'max'  => 10,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( '__field-value.range-value .jet-form__field-value-suffix' ) => 'font-size: {{VALUE}}px;',
				),
				'separator' => 'before',
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'range_suffix_value_color',
				'label' => __( 'Suffix Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '__field-value.range-value .jet-form__field-value-suffix' ) => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_headings_style',
				'title' => esc_html__( 'Heading', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'field_heading_styles_heading',
				'type'      => 'text',
				'content'   => esc_html__( 'Label', 'jet-engine' ),
				'separator' => 'none',
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'field_heading_typography',
				'label' => __( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__heading' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'fields_heading_color',
				'label' => __( 'Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__heading' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'fields_heading_gap',
				'label'     => __( 'Gap', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__heading' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'field_heading_styles_desc',
				'content'   => esc_html__( 'Description', 'jet-engine' ),
				'type'      => 'text',
				'separator' => 'before',
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'field_desc_typography',
				'label' => __( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__heading-desc' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'fields_desc_color',
				'label' => __( 'Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__heading-desc' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'fields_heading_desc_gap',
				'label'     => __( 'Gap', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__heading-desc' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_repeater_style',
				'title' => esc_html__( 'Repeater', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'field_repeater_row_desc',
				'content'   => esc_html__( 'Repeater row', 'jet-engine' ),
				'type'      => 'text',
				'separator' => 'none',
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'booking_form_repeater_row_padding',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%', 'em' ),
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__row' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'field_repeater_new_desc',
				'content'   => esc_html__( 'New item button', 'jet-engine' ),
				'type'      => 'text',
				'separator' => 'before',
			)
		);

		$this->controls_manager->start_tabs(
			'style_controls',
			array(
				'id'        => 'tabs_booking_form_repeater_style',
				'separator' => 'after',
			)
		);

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'booking_form_repeater_normal',
				'title' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_repeater_bg_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__new' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_repeater_color',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__new' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'booking_form_repeater_hover',
				'title' => esc_html__( 'Hover', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_repeater_bg_color_hover',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__new:hover' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_repeater_color_hover',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__new:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_repeater_hover_border_color',
				'label' => esc_html__( 'Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__new:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->end_tabs();

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_repeater_typography',
				'label' => __( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__new' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_repeater_padding',
				'label'     => esc_html__( 'Padding', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%', 'em' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__new' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_repeater_margin',
				'label'     => esc_html__( 'Margin', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%', 'em' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__new' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'booking_form_repeater_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'separator'      => 'before',
				'disable_radius' => true,
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__new' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_repeater_border_radius',
				'label'     => __( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__field:not(.checkradio-field):not(.range-field)' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		// booking_form_repeater_box_shadow

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_repeater_alignment',
				'label'     => __( 'Alignment', 'jet-engine' ),
				'type'      => 'choose',
				'separator' => 'before',
				'options'   => array(
					'flex-start' => array(
						'shortcut' => esc_html__( 'Start', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignleft',
					),
					'center' => array(
						'shortcut' => esc_html__( 'Center', 'jet-engine' ),
						'icon'     => 'dashicons-editor-aligncenter',
					),
					'flex-end' => array(
						'shortcut' => esc_html__( 'End', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignright',
					),
				),
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__actions' ) => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'field_repeater_del_desc',
				'content'   => esc_html__( 'Remove item button', 'jet-engine' ),
				'type'      => 'text',
				'separator' => 'before',
			)
		);

		$this->controls_manager->start_tabs(
			'style_controls',
			array(
				'id'        => 'tabs_booking_form_repeater_del_style',
				'separator' => 'after',
			)
		);

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'booking_form_repeater_del_normal',
				'title' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_repeater_del_bg_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__remove' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_repeater_del_color',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__remove' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'booking_form_repeater_del_hover',
				'title' => esc_html__( 'Hover', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_repeater_del_bg_color_hover',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__remove:hover' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_repeater_del_color_hover',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__remove:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_repeater_del_hover_border_color',
				'label' => esc_html__( 'Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__remove:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->end_tabs();

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'booking_form_repeater_del_padding',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%', 'em' ),
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__remove' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_repeater_del_margin',
				'label'     => esc_html__( 'Margin', 'jet-engine' ),
				'type'      => 'dimensions',
				'separator' => 'before',
				'units'     => array( 'px', '%', 'em' ),
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__remove' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'booking_form_repeater_del_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'separator'      => 'before',
				'disable_radius' => true,
				'css_selector'   => array(
					$this->css_selector( ' .jet-form-repeater__remove' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_repeater_del_border_radius',
				'label'     => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__remove' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_repeater_del_size',
				'label' => esc_html__( 'Icon Size', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 12,
							'max'  => 90,
						),
					),
				),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__remove' ) => 'font-size: {{VALUE}}{{UNIT}};line-height: {{VALUE}}{{UNIT}};',
				),
			)
		);

		// booking_form_repeater_del_box_shadow

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_repeater_del_alignment',
				'label'     => esc_html__( 'Alignment', 'jet-engine' ),
				'type'      => 'choose',
				'separator' => 'before',
				'options' => array(
					'flex-start' => array(
						'shortcut' => __( 'Top', 'jet-engine' ),
						'icon'     => 'dashicons-arrow-up-alt',
					),
					'center' => array(
						'shortcut' => __( 'Middle', 'jet-engine' ),
						'icon'     => 'dashicons-align-center',
					),
					'flex-end' => array(
						'shortcut' => __( 'Bottom', 'jet-engine' ),
						'icon'     => 'dashicons-arrow-down-alt',
					),
				),
				'css_selector' => array(
					$this->css_selector( ' .jet-form-repeater__row-remove' ) => 'align-self: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_group_break_style',
				'title' => esc_html__( 'Groups Break', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'group_break_height',
				'label' => __( 'Height', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 20,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( ' .jet-form__group-break' ) => 'height: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'group_break_color',
				'label' => __( 'Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__group-break' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'group_break_gap_before',
				'label' => __( 'Gap Before', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
					),
				),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__group-break' ) => 'margin-top: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'group_break_gap_after',
				'label' => __( 'Gap After', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( ' .jet-form__group-break' ) => 'margin-bottom: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_required_style',
				'title' => esc_html__( 'Required mark', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'required_mark_color',
				'label' => __( 'Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '__required' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'required_size',
				'label'     => __( 'Size', 'jet-engine' ),
				'type'      => 'range',
				'separator' => 'before',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 50,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( '__required' ) => 'font-size: {{VALUE}}px;',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'form_submit_style',
				'title' => esc_html__( 'Submit', 'jet-engine' ),
			)
		);

		$this->controls_manager->start_tabs(
			'style_controls',
			array(
				'id' => 'tabs_booking_form_submit_style',
			)
		);

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'booking_form_submit_normal',
				'title' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_submit_bg_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__submit' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_submit_color',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__submit' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'booking_form_submit_hover',
				'title' => esc_html__( 'Hover', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_submit_bg_color_hover',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__submit:hover' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_submit_color_hover',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__submit:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_submit_hover_border_color',
				'label' => esc_html__( 'Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__submit:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->end_tabs();

		$this->controls_manager->add_control(
			array(
				'id'        => 'booking_form_submit_typography',
				'label'     => __( 'Typography', 'jet-engine' ),
				'type'      => 'typography',
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__submit' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_submit_padding',
				'label'     => esc_html__( 'Padding', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%', 'em' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__submit' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_submit_margin',
				'label'     => esc_html__( 'Margin', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%', 'em' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__submit' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'booking_form_submit_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'separator'      => 'before',
				'disable_radius' => true,
				'css_selector'   => array(
					$this->css_selector( ' .jet-form__submit' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_submit_border_radius',
				'label'     => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__submit' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		// booking_form_submit_box_shadow

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_submit_alignment',
				'label'     => esc_html__( 'Alignment', 'jet-engine' ),
				'type'      => 'choose',
				'separator' => 'before',
				'options' => array(
					'flex-start' => array(
						'shortcut' => esc_html__( 'Start', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignleft',
					),
					'center' => array(
						'shortcut' => esc_html__( 'Center', 'jet-engine' ),
						'icon'     => 'dashicons-editor-aligncenter',
					),
					'flex-end' => array(
						'shortcut' => esc_html__( 'End', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignright',
					),
					'stretch' => array(
						'shortcut' => esc_html__( 'Fullwidth', 'jet-engine' ),
						'icon'     => 'dashicons-editor-justify',
					),
				),
				'css_selector' => array(
					$this->css_selector( ' .jet-form__submit-wrap' ) => 'align-items: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'form_next_page_style',
				'title' => esc_html__( 'Next Page Button', 'jet-engine' ),
			)
		);

		$this->controls_manager->start_tabs(
			'style_controls',
			array(
				'id' => 'tabs_booking_form_next_page_style',
			)
		);

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'booking_form_next_page_normal',
				'title' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_next_page_bg_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__next-page' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_next_page_color',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__next-page' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'booking_form_next_page_hover',
				'title' => esc_html__( 'Hover', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_next_page_bg_color_hover',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__next-page:hover' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_next_page_color_hover',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__next-page:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_next_page_hover_border_color',
				'label' => esc_html__( 'Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__next-page:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->end_tabs();

		$this->controls_manager->add_control(
			array(
				'id'        => 'booking_form_next_page_typography',
				'label'     => __( 'Typography', 'jet-engine' ),
				'type'      => 'typography',
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__next-page' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_next_page_padding',
				'label'     => esc_html__( 'Padding', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%', 'em' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__next-page' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_next_page_margin',
				'label'     => esc_html__( 'Margin', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%', 'em' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__next-page' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'booking_form_next_page_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'separator'      => 'before',
				'disable_radius' => true,
				'css_selector'   => array(
					$this->css_selector( ' .jet-form__next-page' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_next_page_border_radius',
				'label'     => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__next-page' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		// booking_form_next_page_box_shadow

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'form_prev_page_style',
				'title' => esc_html__( 'Prev Page Button', 'jet-engine' ),
			)
		);

		$this->controls_manager->start_tabs(
			'style_controls',
			array(
				'id' => 'tabs_booking_form_prev_page_style',
			)
		);

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'booking_form_prev_page_normal',
				'title' => esc_html__( 'Normal', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_prev_page_bg_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__prev-page' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_prev_page_color',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__prev-page' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'booking_form_prev_page_hover',
				'title' => esc_html__( 'Hover', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_prev_page_bg_color_hover',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__prev-page:hover' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_prev_page_color_hover',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__prev-page:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_form_prev_page_hover_border_color',
				'label' => esc_html__( 'Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__prev-page:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->end_tabs();

		$this->controls_manager->add_control(
			array(
				'id'        => 'booking_form_prev_page_typography',
				'label'     => __( 'Typography', 'jet-engine' ),
				'type'      => 'typography',
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__prev-page' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_prev_page_padding',
				'label'     => esc_html__( 'Padding', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%', 'em' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__prev-page' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_prev_page_margin',
				'label'     => esc_html__( 'Margin', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%', 'em' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__prev-page' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'booking_form_prev_page_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'separator'      => 'before',
				'disable_radius' => true,
				'css_selector'   => array(
					$this->css_selector( ' .jet-form__prev-page' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_form_prev_page_border_radius',
				'label'     => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( ' .jet-form__prev-page' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		// booking_form_prev_page_box_shadow

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'form_messages_style',
				'title' => esc_html__( 'Messages', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_messages_typography',
				'label' => __( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css_selector' => array(
					$this->css_selector( '-message' ) => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'booking_messages_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'separator'      => 'before',
				'disable_radius' => true,
				'css_selector'   => array(
					$this->css_selector( '-message' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_messages_border_radius',
				'label'     => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( '-message' ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->start_tabs(
			'style_controls',
			array(
				'id' => 'tabs_booking_messages_style',
			)
		);

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'booking_messages_success',
				'title' => esc_html__( 'Success', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_messages_success_bg',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '-message.jet-form-message--success' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_messages_success_color',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '-message.jet-form-message--success' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_messages_success_border_color',
				'label' => esc_html__( 'Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '-message.jet-form-message--success' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		// booking_messages_box_shadow_success

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'booking_messages_error',
				'title' => esc_html__( 'Error', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_messages_error_bg',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '-message.jet-form-message--error' ) => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_messages_error_color',
				'label' => esc_html__( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '-message.jet-form-message--error' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'booking_messages_error_border_color',
				'label' => esc_html__( 'Border Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '-message.jet-form-message--error' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		// booking_messages_box_shadow_error

		$this->controls_manager->end_tab();

		$this->controls_manager->end_tabs();

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_messages_padding',
				'label'     => esc_html__( 'Padding', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%', 'em' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( '-message' ) => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'booking_messages_margin',
				'label'     => esc_html__( 'Margin', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%', 'em' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( '-message' ) => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'booking_messages_alignment',
				'label' => esc_html__( 'Alignment', 'jet-engine' ),
				'type'  => 'choose',
				'options' => array(
					'left' => array(
						'shortcut' => esc_html__( 'Left', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignleft',
					),
					'center' => array(
						'shortcut' => esc_html__( 'Center', 'jet-engine' ),
						'icon'     => 'dashicons-editor-aligncenter',
					),
					'right' => array(
						'shortcut' => esc_html__( 'Right', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignright',
					),
				),
				'css_selector' => array(
					$this->css_selector( '-message' ) => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'field_messages',
				'content'   => esc_html__( 'Field Messages', 'jet-engine' ),
				'type'      => 'text',
				'separator' => 'before',
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'field_messages_font_size',
				'label' => __( 'Font Size', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 9,
							'max'  => 50,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( array( '__field-error', ' .jet-engine-file-upload__errors' ) ) => 'font-size: {{VALUE}}px;',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'field_messages_color',
				'label' => esc_html__( 'Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( array( '__field-error', ' .jet-engine-file-upload__errors' ) ) => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'        => 'field_messages_margin',
				'label'     => esc_html__( 'Margin', 'jet-engine' ),
				'type'      => 'dimensions',
				'units'     => array( 'px', '%', 'em' ),
				'separator' => 'before',
				'css_selector' => array(
					$this->css_selector( array( '__field-error', ' .jet-engine-file-upload__errors' ) ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'field_messages_alignment',
				'label' => esc_html__( 'Alignment', 'jet-engine' ),
				'type'  => 'choose',
				'options' => array(
					'left' => array(
						'shortcut' => esc_html__( 'Left', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignleft',
					),
					'center' => array(
						'shortcut' => esc_html__( 'Center', 'jet-engine' ),
						'icon'     => 'dashicons-editor-aligncenter',
					),
					'right' => array(
						'shortcut' => esc_html__( 'Right', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignright',
					),
				),
				'css_selector' => array(
					$this->css_selector( array( '__field-error', ' .jet-engine-file-upload__errors' ) ) => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_section();

	}

	public function render_callback( $attributes = array() ) {

		jet_engine()->frontend->frontend_scripts();

		$render = $this->get_render_instance( $attributes );

		if ( $this->is_edit_mode() ) {
			$render->set_edit_mode( true );
		}

		$content = $render->get_content();

		// Ensure enqueue form script after getting content.
		wp_enqueue_script( 'jet-engine-frontend-forms' );

		$this->_root['class'][] = 'jet-form-block';

		if ( ! empty( $attributes['className'] ) ) {
			$this->_root['class'][] = $attributes['className'];
		}

		$this->_root['data-is-block'] = $this->get_block_name();

		return sprintf( '<div %1$s>%2$s</div>', $this->get_root_attr_string(), $content );
	}

}
