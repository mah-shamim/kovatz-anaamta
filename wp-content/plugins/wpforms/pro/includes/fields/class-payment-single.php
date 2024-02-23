<?php

/**
 * Single item payment field.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
class WPForms_Field_Payment_Single extends WPForms_Field {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Define field type information.
		$this->name  = esc_html__( 'Single Item', 'wpforms' );
		$this->type  = 'payment-single';
		$this->icon  = 'fa-file-o';
		$this->order = 30;
		$this->group = 'payment';

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

		// Basic IDs.
		$form_id  = absint( $form_data['id'] );
		$field_id = absint( $field['id'] );

		// Set options container (<select>) properties.
		$properties['input_container'] = array(
			'class' => array( 'wpforms-payment-price' ),
			'data'  => array(),
			'id'    => "wpforms-{$form_id}-field_{$field_id}",
		);

		// User format data and class.
		$field_format = ! empty( $field['format'] ) ? $field['format'] : 'single';
		if ( 'user' === $field_format ) {
			$properties['inputs']['primary']['data']['rule-currency'] = '["$",false]';

			$properties['inputs']['primary']['class'][] = 'wpforms-payment-user-input';
		}

		$properties['inputs']['primary']['class'][] = 'wpforms-payment-price';

		// Check size.
		if ( ! empty( $field['size'] ) ) {
			$properties['inputs']['primary']['class'][] = 'wpforms-field-' . esc_attr( $field['size'] );
		}

		// Price.
		$field_value                                      = ! empty( $field['price'] ) ? wpforms_sanitize_amount( $field['price'] ) : '';
		$properties['inputs']['primary']['attr']['value'] = ! empty( $field_value ) ? wpforms_format_amount( $field_value, true ) : '';

		// Single item and hidden format should hide the input field.
		if ( ! empty( $field['format'] ) && 'hidden' === $field['format'] ) {
			$properties['container']['class'][] = 'wpforms-field-hidden';
		}

		return $properties;
	}

	/**
	 * @inheritdoc
	 */
	protected function get_field_populated_single_property_value( $raw_value, $input, $properties, $field ) {

		if ( ! is_string( $raw_value ) ) {
			return $properties;
		}

		// Allow to redefine the value for user-defined price only.
		$field_format = ! empty( $field['format'] ) ? $field['format'] : 'single';
		if ( 'user' !== $field_format ) {
			return $properties;
		}

		$get_value           = stripslashes( sanitize_text_field( $raw_value ) );
		$get_value           = ! empty( $get_value ) ? wpforms_sanitize_amount( $get_value ) : '';
		$get_value_formatted = ! empty( $get_value ) ? wpforms_format_amount( $get_value ) : '';

		// `primary` by default.
		if (
			! empty( $input ) &&
			isset( $properties['inputs'][ $input ] )
		) {
			$properties['inputs'][ $input ]['attr']['value'] = $get_value_formatted;
		}

		return $properties;
	}

	/**
	 * Field options panel inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field data and settings.
	 */
	public function field_options( $field ) {
		/*
		 * Basic field options.
		 */

		$this->field_option( 'basic-options', $field, array( 'markup' => 'open' ) );
		$this->field_option( 'label', $field );
		$this->field_option( 'description', $field );

		// Item Price.
		$price   = ! empty( $field['price'] ) ? wpforms_format_amount( wpforms_sanitize_amount( $field['price'] ) ) : '';
		$tooltip = esc_html__( 'Enter the price of the item, without a currency symbol.', 'wpforms' );
		$output  = $this->field_element( 'label', $field, array( 'slug' => 'price', 'value' => esc_html__( 'Item Price', 'wpforms' ), 'tooltip' => $tooltip ), false );
		$output .= $this->field_element(
			'text',
			$field,
			array(
				'slug'        => 'price',
				'value'       => $price,
				'class'       => 'wpforms-money-input',
				'placeholder' => wpforms_format_amount( 0 ),
			),
			false
		);
		$this->field_element( 'row', $field, array( 'slug' => 'price', 'content' => $output ) );

		// Item Format option.
		$format  = ! empty( $field['format'] ) ? esc_attr( $field['format'] ) : 'date-time';
		$tooltip = esc_html__( 'Select the item type.', 'wpforms' );
		$options = array(
			'single' => esc_html__( 'Single Item', 'wpforms' ),
			'user'   => esc_html__( 'User Defined', 'wpforms' ),
			'hidden' => esc_html__( 'Hidden', 'wpforms' ),
		);
		$output  = $this->field_element( 'label', $field, array( 'slug' => 'format', 'value' => esc_html__( 'Item Type', 'wpforms' ), 'tooltip' => $tooltip ), false );
		$output .= $this->field_element( 'select', $field, array( 'slug' => 'format', 'value' => $format, 'options' => $options ), false );
		$this->field_element( 'row', $field, array( 'slug' => 'format', 'content' => $output ) );

		$this->field_option( 'required', $field );
		$this->field_option( 'basic-options', $field, array( 'markup' => 'close' ) );

		/*
		 * Advanced field options.
		 */

		$this->field_option( 'advanced-options', $field, array( 'markup' => 'open' ) );
		$this->field_option( 'size', $field );
		$this->field_option( 'placeholder', $field );
		$this->field_option( 'label_hide', $field );
		$this->field_option( 'css', $field );
		$this->field_option( 'advanced-options', $field, array( 'markup' => 'close' ) );
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field data and settings.
	 */
	public function field_preview( $field ) {

		$price       = ! empty( $field['price'] ) ? wpforms_format_amount( wpforms_sanitize_amount( $field['price'] ), true ) : wpforms_format_amount( 0, true );
		$placeholder = ! empty( $field['placeholder'] ) ? $field['placeholder'] : wpforms_format_amount( 0 );
		$format      = ! empty( $field['format'] ) ? $field['format'] : 'single';
		$value       = ! empty( $field['price'] ) ? wpforms_format_amount( wpforms_sanitize_amount( $field['price'] ) ) : '';

		echo '<div class="format-selected-' . esc_attr( $format ) . ' format-selected">';

			$this->field_preview_option( 'label', $field );

			echo '<p class="item-price">';
				printf(
					/* translators: %s - price amount. */
					esc_html__( 'Price: %s', 'wpforms' ),
					'<span class="price">' . esc_html( $price ) . '</span>'
				);
			echo '</p>';

			printf(
				'<input type="text" placeholder="%s" class="primary-input" value="%s" disabled>',
				esc_attr( $placeholder ),
				esc_attr( $value )
			);

			$this->field_preview_option( 'description', $field );

			echo '<p class="item-price-hidden">';
				esc_html_e( 'Note: Item type is set to hidden and will not be visible when viewing the form.', 'wpforms' );
			echo '</p>';

		echo '</div>';
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Converted to a new format, where all the data are taken not from $deprecated, but field properties.
	 *
	 * @param array $field      Field data and settings.
	 * @param array $deprecated Deprecated field attributes.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		// Shortcut for easier access.
		$primary = $field['properties']['inputs']['primary'];

		$field_format = ! empty( $field['format'] ) ? $field['format'] : 'single';

		switch ( $field_format ) {
			case 'single':
			case 'hidden':
				if ( 'single' === $field_format ) {
					echo '<div class="wpforms-single-item-price">';
					printf(
						/* translators: %s - price amount. */
						esc_html__( 'Price: %s', 'wpforms' ),
						'<span class="wpforms-price">' . esc_html( wpforms_format_amount( wpforms_sanitize_amount( $field['price'] ), true ) ) . '</span>'
					);
					echo '</div>';
				}

				// Primary price field.
				printf(
					'<input type="hidden" %s>',
					wpforms_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] )
				);
				break;

			case 'user':
				printf(
					'<input type="text" %s>',
					wpforms_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] )
				);
				break;
		}
	}

	/**
	 * Validates field on form submit.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $field_id     Field ID.
	 * @param string $field_submit Field data submitted by a user.
	 * @param array  $form_data    Form data and settings.
	 */
	public function validate( $field_id, $field_submit, $form_data ) {

		// If field is required, check for data.
		if (
			empty( $field_submit ) &&
			! empty( $form_data['fields'][ $field_id ]['required'] )
		) {
			wpforms()->process->errors[ $form_data['id'] ][ $field_id ] = wpforms_get_required_label();

			return;
		}

		// If field format is not user provided, validate the amount posted.
		if (
			! empty( $field_submit ) &&
			'user' !== $form_data['fields'][ $field_id ]['format']
		) {
			$price  = wpforms_sanitize_amount( $form_data['fields'][ $field_id ]['price'] );
			$submit = wpforms_sanitize_amount( $field_submit );
			if ( $price !== $submit ) {
				wpforms()->process->errors[ $form_data['id'] ][ $field_id ] = esc_html__( 'Amount mismatch', 'wpforms' );
			}
		}
	}

	/**
	 * Formats and sanitizes field.
	 *
	 * @since 1.0.0
	 *
	 * @param int    $field_id     Field ID.
	 * @param string $field_submit Field data submitted by a user.
	 * @param array  $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		$field = $form_data['fields'][ $field_id ];
		$name  = ! empty( $field['label'] ) ? sanitize_text_field( $field['label'] ) : '';

		// Only trust the value if the field is user format.
		if ( 'user' === $field['format'] ) {
			$amount = wpforms_sanitize_amount( $field_submit );
		} else {
			$amount = wpforms_sanitize_amount( $field['price'] );
		}

		wpforms()->process->fields[ $field_id ] = array(
			'name'       => $name,
			'value'      => wpforms_format_amount( $amount, true ),
			'amount'     => wpforms_format_amount( $amount ),
			'amount_raw' => $amount,
			'currency'   => wpforms_setting( 'currency', 'USD' ),
			'id'         => absint( $field_id ),
			'type'       => $this->type,
		);
	}
}

new WPForms_Field_Payment_Single();
