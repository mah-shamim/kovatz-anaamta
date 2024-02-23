<?php

/**
 * Dropdown payment field.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.3.1
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
class WPForms_Field_Payment_Select extends WPForms_Field {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.3.1
	 */
	public function init() {

		// Define field type information.
		$this->name     = esc_html__( 'Dropdown Items', 'wpforms' );
		$this->type     = 'payment-select';
		$this->icon     = 'fa-caret-square-o-down';
		$this->order    = 70;
		$this->group    = 'payment';
		$this->defaults = array(
			1 => array(
				'label'   => esc_html__( 'First Item', 'wpforms' ),
				'value'   => '10.00',
				'default' => '',
			),
			2 => array(
				'label'   => esc_html__( 'Second Item', 'wpforms' ),
				'value'   => '25.00',
				'default' => '',
			),
			3 => array(
				'label'   => esc_html__( 'Third Item', 'wpforms' ),
				'value'   => '50.00',
				'default' => '',
			),
		);

		// Define additional field properties.
		add_filter( 'wpforms_field_properties_' . $this->type, array( $this, 'field_properties' ), 5, 3 );
	}

	/**
	 * Define additional field properties.
	 *
	 * @since 1.5.0
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function field_properties( $properties, $field, $form_data ) {

		// Remove primary input.
		unset( $properties['inputs']['primary'] );

		// Define data.
		$form_id  = absint( $form_data['id'] );
		$field_id = absint( $field['id'] );
		$choices  = $field['choices'];

		// Set options container (<select>) properties.
		$properties['input_container'] = array(
			'class' => array( 'wpforms-payment-price' ),
			'data'  => array(),
			'id'    => "wpforms-{$form_id}-field_{$field_id}",
			'attr'  => array(
				'name' => "wpforms[fields][{$field_id}]",
			),
		);

		// Set properties.
		foreach ( $choices as $key => $choice ) {

			$properties['inputs'][ $key ] = array(
				'container' => array(
					'attr'  => array(),
					'class' => array( "choice-{$key}" ),
					'data'  => array(),
					'id'    => '',
				),
				'label'     => array(
					'attr'  => array(
						'for' => "wpforms-{$form_id}-field_{$field_id}_{$key}",
					),
					'class' => array( 'wpforms-field-label-inline' ),
					'data'  => array(),
					'id'    => '',
					'text'  => $choice['label'],
				),
				'attr'      => array(
					'value' => $choice['value'],
					'data'  => array(
						'amount' => wpforms_format_amount( wpforms_sanitize_amount( $choice['value'] ) ),
					),
 				),
				'class'     => array(),
				'data'      => array(),
				'id'        => "wpforms-{$form_id}-field_{$field_id}_{$key}",
				'required'  => ! empty( $field['required'] ) ? 'required' : '',
				'default'   => isset( $choice['default'] ),
			);
		}

		// Add class that changes the field size.
		if ( ! empty( $field['size'] ) ) {
			$properties['input_container']['class'][] = 'wpforms-field-' . esc_attr( $field['size'] );
		}

		// Required class for pagebreak validation.
		if ( ! empty( $field['required'] ) ) {
			$properties['input_container']['class'][] = 'wpforms-field-required';
		}

		return $properties;
	}

	/**
	 * @inheritdoc
	 */
	protected function get_field_populated_single_property_value( $raw_value, $input, $properties, $field ) {
		/*
		 * When the form is submitted we get from Fallback only values (prices).
		 * As payment-dropdown field doesn't support 'show_values' option -
		 * we should transform value into label to check against using general logic in parent method.
		 */

		if (
			! is_string( $raw_value ) ||
			empty( $field['choices'] ) ||
			! is_array( $field['choices'] )
		) {
			return $properties;
		}

		// The form submits only the sum, so shortcut for Dynamic.
		if ( ! is_numeric( $raw_value ) ) {
			return parent::get_field_populated_single_property_value( $raw_value, $input, $properties, $field );
		}

		$get_value = wpforms_format_amount( wpforms_sanitize_amount( $raw_value ) );

		foreach ( $field['choices'] as $choice ) {
			if (
				isset( $choice['label'], $choice['value'] ) &&
				wpforms_format_amount( wpforms_sanitize_amount( $choice['value'] ) ) === $get_value
			) {
				$trans_value = $choice['label'];
				// Stop iterating over choices.
				break;
			}
		}

		if ( empty( $trans_value ) ) {
			return $properties;
		}

		return parent::get_field_populated_single_property_value( $trans_value, $input, $properties, $field );
	}

	/**
	 * Field options panel inside the builder.
	 *
	 * @since 1.3.1
	 *
	 * @param array $field Field settings.
	 */
	public function field_options( $field ) {
		/*
		 * Basic field options.
		 */

		// Options open markup.
		$this->field_option( 'basic-options', $field, array( 'markup' => 'open' ) );

		// Label.
		$this->field_option( 'label', $field );

		// Choices option.
		$this->field_option( 'choices_payments', $field );

		// Description.
		$this->field_option( 'description', $field );

		// Required toggle.
		$this->field_option( 'required', $field );

		// Options close markup.
		$this->field_option( 'basic-options', $field, array( 'markup' => 'close' ) );

		/*
		 * Advanced field options.
		 */

		// Options open markup.
		$this->field_option( 'advanced-options', $field, array( 'markup' => 'open' ) );

		// Size.
		$this->field_option( 'size', $field );

		// Placeholder.
		$this->field_option( 'placeholder', $field );

		// Hide label.
		$this->field_option( 'label_hide', $field );

		// Custom CSS classes.
		$this->field_option( 'css', $field );

		// Options close markup.
		$this->field_option( 'advanced-options', $field, array( 'markup' => 'close' ) );
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.3.1
	 *
	 * @param array $field Field settings.
	 */
	public function field_preview( $field ) {

		// Label.
		$this->field_preview_option( 'label', $field );

		// Choices.
		$this->field_preview_option( 'choices', $field );

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.3.1
	 * @since 1.5.0 Converted to a new format, where all the data are taken not from $deprecated, but field properties.
	 *
	 * @param array $field      Field data and settings.
	 * @param array $deprecated Deprecated array of field attributes.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		$container = $field['properties']['input_container'];

		$field_placeholder = ! empty( $field['placeholder'] ) ? $field['placeholder'] : '';
		if ( ! empty( $field['required'] ) ) {
			$container['attr']['required'] = 'required';
		}

		$choices     = $field['properties']['inputs'];
		$has_default = false;

		// Check to see if any of the options were selected by default.
		foreach ( $choices as $choice ) {
			if ( ! empty( $choice['default'] ) ) {
				$has_default = true;
				break;
			}
		}

		// Preselect default if no other choices were marked as default.
		printf(
			'<select %s>',
			wpforms_html_attributes( $container['id'], $container['class'], $container['data'], $container['attr'] )
		);

		// Optional placeholder.
		if ( ! empty( $field_placeholder ) ) {
			printf(
				'<option value="" class="placeholder" disabled %s>%s</option>',
				selected( false, $has_default, false ),
				esc_html( $field_placeholder )
			);
		}

		// Build the select options.
		foreach ( $choices as $key => $choice ) {
			$amount = wpforms_format_amount( wpforms_sanitize_amount( $choice['attr']['value'] ) );

			printf(
				'<option value="%s" data-amount="%s" %s>%s</option>',
				esc_attr( $key ),
				esc_attr( $amount ),
				selected( true, ! empty( $choice['default'] ), false ),
				esc_html( $choice['label']['text'] )
			);
		}

		echo '</select>';
	}

	/**
	 * Validates field on form submit.
	 *
	 * @since 1.3.1
	 *
	 * @param int    $field_id     Field ID.
	 * @param string $field_submit Submitted field value (selected option).
	 * @param array  $form_data    Form data and settings.
	 */
	public function validate( $field_id, $field_submit, $form_data ) {

		// Basic required check - If field is marked as required, check for entry data.
		if ( ! empty( $form_data['fields'][ $field_id ]['required'] ) && empty( $field_submit ) ) {

			wpforms()->process->errors[ $form_data['id'] ][ $field_id ] = wpforms_get_required_label();
		}

		// Validate that the option selected is real.
		if ( ! empty( $field_submit ) && empty( $form_data['fields'][ $field_id ]['choices'][ $field_submit ] ) ) {

			wpforms()->process->errors[ $form_data['id'] ][ $field_id ] = esc_html__( 'Invalid payment option', 'wpforms' );
		}
	}

	/**
	 * Formats and sanitizes field.
	 *
	 * @since 1.3.1
	 *
	 * @param int    $field_id     Field ID.
	 * @param string $field_submit Submitted field value (selected option).
	 * @param array  $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		$choice_label = '';
		$field        = $form_data['fields'][ $field_id ];
		$name         = ! empty( $field['label'] ) ? sanitize_text_field( $field['label'] ) : '';

		// Fetch the amount.
		if ( ! empty( $field['choices'][ $field_submit ]['value'] ) ) {
			$amount = wpforms_sanitize_amount( $field['choices'][ $field_submit ]['value'] );
		} else {
			$amount = 0;
		}

		$value = wpforms_format_amount( $amount, true );

		if ( empty( $field_submit ) ) {
			$value = '';
		} elseif ( ! empty( $field['choices'][ $field_submit ]['label'] ) ) {
			$choice_label = sanitize_text_field( $field['choices'][ $field_submit ]['label'] );
			$value        = $choice_label . ' - ' . $value;
		}

		wpforms()->process->fields[ $field_id ] = array(
			'name'         => $name,
			'value'        => $value,
			'value_choice' => $choice_label,
			'value_raw'    => sanitize_text_field( $field_submit ),
			'amount'       => wpforms_format_amount( $amount ),
			'amount_raw'   => $amount,
			'currency'     => wpforms_setting( 'currency', 'USD' ),
			'id'           => absint( $field_id ),
			'type'         => $this->type,
		);
	}
}

new WPForms_Field_Payment_Select();
