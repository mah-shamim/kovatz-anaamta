<?php

/**
 * Phone number field.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
class WPForms_Field_Phone extends WPForms_Field {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Define field type information.
		$this->name  = esc_html__( 'Phone', 'wpforms' );
		$this->type  = 'phone';
		$this->icon  = 'fa-phone';
		$this->order = 50;
		$this->group = 'fancy';

		// Define additional field properties.
		add_filter( 'wpforms_field_properties_phone', array( $this, 'field_properties' ), 5, 3 );

		// Form frontend CSS enqueues.
		add_action( 'wpforms_frontend_css', array( $this, 'enqueue_frontend_css' ) );

		// Form frontend JS enqueues.
		add_action( 'wpforms_frontend_js', array( $this, 'enqueue_frontend_js' ) );
	}

	/**
	 * Define additional field properties.
	 *
	 * @since 1.3.8
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function field_properties( $properties, $field, $form_data ) {

		// Input primary: add validation rule and class for smart phone field.
		if ( 'smart' === $field['format'] ) {
			$properties['inputs']['primary']['class'][]                        = 'wpforms-smart-phone-field';
			$properties['inputs']['primary']['data']['rule-smart-phone-field'] = 'true';
		}

		// Input primary: add input mask and class for US formatted numbers.
		if ( 'us' === $field['format'] ) {
			$properties['inputs']['primary']['class'][]           = 'wpforms-masked-input';
			$properties['inputs']['primary']['data']['inputmask'] = "'mask': '(999) 999-9999'";
		}

		// Input primary: RTL support for input masks.
		if ( is_rtl() ) {
			$properties['inputs']['primary']['attr']['dir'] = 'rtl';
		}

		return $properties;
	}

	/**
	 * Form frontend CSS enqueues.
	 *
	 * @since 1.5.2
	 */
	public function enqueue_frontend_css() {

		$min = \wpforms_get_min_suffix();

		// International Telephone Input library CSS.
		wp_enqueue_style(
			'wpforms-smart-phone-field',
			WPFORMS_PLUGIN_URL . "pro/assets/css/vendor/intl-tel-input{$min}.css",
			array(),
			'15.0.0'
		);
	}

	/**
	 * Form frontend JS enqueues.
	 *
	 * @since 1.5.2
	 */
	public function enqueue_frontend_js() {

		$min = \wpforms_get_min_suffix();

		// Load International Telephone Input library - https://github.com/jackocnr/intl-tel-input.
		wp_enqueue_script(
			'wpforms-smart-phone-field',
			WPFORMS_PLUGIN_URL . "pro/assets/js/vendor/jquery.intl-tel-input{$min}.js",
			array( 'jquery' ),
			'15.0.0',
			true
		);
	}

	/**
	 * Field options panel inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field
	 */
	public function field_options( $field ) {
		/*
		 * Basic field options.
		 */

		// Options open markup.
		$args = array(
			'markup' => 'open',
		);
		$this->field_option( 'basic-options', $field, $args );

		// Label.
		$this->field_option( 'label', $field );

		// Format.
		$lbl  = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'format',
				'value'   => esc_html__( 'Format', 'wpforms' ),
				'tooltip' => esc_html__( 'Select format for the phone form field', 'wpforms' ),
			),
			false
		);
		$fld  = $this->field_element(
			'select',
			$field,
			array(
				'slug'    => 'format',
				'value'   => ! empty( $field['format'] ) ? esc_attr( $field['format'] ) : 'smart',
				'options' => array(
					'smart'         => esc_html__( 'Smart', 'wpforms' ),
					'us'            => esc_html__( 'US', 'wpforms' ),
					'international' => esc_html__( 'International', 'wpforms' ),
				),
			),
			false
		);
		$args = array(
			'slug'    => 'format',
			'content' => $lbl . $fld,
		);
		$this->field_element( 'row', $field, $args );

		// Description.
		$this->field_option( 'description', $field );

		// Required toggle.
		$this->field_option( 'required', $field );

		// Options close markup.
		$args = array(
			'markup' => 'close',
		);
		$this->field_option( 'basic-options', $field, $args );

		/*
		 * Advanced field options.
		 */

		// Options open markup.
		$args = array(
			'markup' => 'open',
		);
		$this->field_option( 'advanced-options', $field, $args );

		// Size.
		$this->field_option( 'size', $field );

		// Placeholder.
		$this->field_option( 'placeholder', $field );

		// Hide Label.
		$this->field_option( 'label_hide', $field );

		// Default value.
		$this->field_option( 'default_value', $field );

		// Custom CSS classes.
		$this->field_option( 'css', $field );

		// Options close markup.
		$args = array(
			'markup' => 'close',
		);
		$this->field_option( 'advanced-options', $field, $args );
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field
	 */
	public function field_preview( $field ) {

		// Define data.
		$placeholder = ! empty( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '';

		// Label.
		$this->field_preview_option( 'label', $field );

		// Primary input.
		echo '<input type="text" placeholder="' . $placeholder . '" class="primary-input" disabled>';

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field      Field data and settings.
	 * @param array $deprecated Deprecated field attributes. Use field properties.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		// Define data.
		$primary = $field['properties']['inputs']['primary'];

		// Allow input type to be changed for this particular field.
		$type = apply_filters( 'wpforms_phone_field_input_type', 'tel' );

		// Primary field.
		printf(
			'<input type="%s" %s %s>',
			esc_attr( $type ),
			wpforms_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
			$primary['required']
		);
	}
}

new WPForms_Field_Phone();
