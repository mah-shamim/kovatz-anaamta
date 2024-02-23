<?php

/**
 * Payment Checkbox Field.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.5.1
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2018, WPForms LLC
 */
class WPForms_Field_Payment_Checkbox extends WPForms_Field {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.5.1
	 */
	public function init() {

		// Define field type information.
		$this->name     = esc_html__( 'Checkbox Items', 'wpforms' );
		$this->type     = 'payment-checkbox';
		$this->icon     = 'fa-check-square-o';
		$this->order    = 50;
		$this->group    = 'payment';
		$this->defaults = array(
			1 => array(
				'label'   => esc_html__( 'First Item', 'wpforms' ),
				'value'   => '10.00',
				'image'   => '',
				'default' => '',
			),
			2 => array(
				'label'   => esc_html__( 'Second Item', 'wpforms' ),
				'value'   => '25.00',
				'image'   => '',
				'default' => '',
			),
			3 => array(
				'label'   => esc_html__( 'Third Item', 'wpforms' ),
				'value'   => '50.00',
				'image'   => '',
				'default' => '',
			),
		);

		// Customize HTML field values.
		add_filter( 'wpforms_html_field_value', array( $this, 'field_html_value' ), 10, 4 );

		// Define additional field properties.
		add_filter( 'wpforms_field_properties_payment-checkbox', array( $this, 'field_properties' ), 5, 3 );
	}

	/**
	 * Return images, if any, for HTML supported values.
	 *
	 * @since 1.5.1
	 *
	 * @param string $value     Field value.
	 * @param array  $field     Field settings.
	 * @param array  $form_data Form data and settings.
	 * @param string $context   Value display context.
	 *
	 * @return string
	 */
	public function field_html_value( $value, $field, $form_data = array(), $context = '' ) {

		// Only use HTML formatting for checkbox fields, with image choices
		// enabled, and exclude the entry table display. Lastly, provides a
		// filter to disable fancy display.
		if (
			! empty( $field['value'] ) &&
			$this->type === $field['type'] &&
			! empty( $field['images'] ) &&
			'entry-table' !== $context &&
			apply_filters( 'wpforms_payment-checkbox_field_html_value_images', true, $context )
		) {

			$items  = array();
			$values = explode( "\n", $field['value'] );

			foreach ( $values as $key => $val ) {

				if ( ! empty( $field['images'][ $key ] ) ) {
					$items[] = sprintf(
						'<span style="max-width:200px;display:block;margin:0 0 5px 0;"><img src="%s" style="max-width:100%%;display:block;margin:0;"></span>%s',
						esc_url( $field['images'][ $key ] ),
						$val
					);
				} else {
					$items[] = $val;
				}
			}

			return implode( '<br><br>', $items );
		}

		return $value;
	}

	/**
	 * Define additional field properties.
	 *
	 * @since 1.5.1
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function field_properties( $properties, $field, $form_data ) {

		// Define data.
		$form_id  = absint( $form_data['id'] );
		$field_id = absint( $field['id'] );
		$choices  = $field['choices'];

		// Remove primary input.
		unset( $properties['inputs']['primary'] );

		// Set input container (ul) properties.
		$properties['input_container'] = array(
			'class' => array(),
			'data'  => array(),
			'id'    => "wpforms-{$form_id}-field_{$field_id}",
		);

		// Set input properties.
		foreach ( $choices as $key => $choice ) {

			// Choice labels should not be left blank, but if they are we
			// provide a basic value.
			$label = $choice['label'];
			if ( '' === $label ) {
				if ( 1 === count( $choices ) ) {
					$label = esc_html__( 'Checked', 'wpforms' );
				} else {
					/* translators: %s - item number. */
					$label = sprintf( esc_html__( 'Item %s', 'wpforms' ), $key );
				}
			}

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
					'text'  => $label,
				),
				'attr'      => array(
					'name'  => "wpforms[fields][{$field_id}][]",
					'value' => $key,
				),
				'class'     => array( 'wpforms-payment-price' ),
				'data'      => array(
					'amount' => wpforms_format_amount( wpforms_sanitize_amount( $choice['value'] ) ),
				),
				'id'        => "wpforms-{$form_id}-field_{$field_id}_{$key}",
				'image'     => isset( $choice['image'] ) ? $choice['image'] : '',
				'required'  => ! empty( $field['required'] ) ? 'required' : '',
				'default'   => isset( $choice['default'] ),
			);
		}

		// Required class for pagebreak validation.
		if ( ! empty( $field['required'] ) ) {
			$properties['input_container']['class'][] = 'wpforms-field-required';
		}

		// Custom properties if image choices are enabled.
		if ( ! empty( $field['choices_images'] ) ) {

			$properties['input_container']['class'][] = 'wpforms-image-choices';
			$properties['input_container']['class'][] = 'wpforms-image-choices-' . sanitize_html_class( $field['choices_images_style'] );

			foreach ( $properties['inputs'] as $key => $inputs ) {
				$properties['inputs'][ $key ]['container']['class'][] = 'wpforms-image-choices-item';

				if ( in_array( $field['choices_images_style'], array( 'modern', 'classic' ), true ) ) {
					$properties['inputs'][ $key ]['class'][] = 'wpforms-screen-reader-element';
				}
			}
		}

		// Add selected class for choices with defaults.
		foreach ( $properties['inputs'] as $key => $inputs ) {
			if ( ! empty( $inputs['default'] ) ) {
				$properties['inputs'][ $key ]['container']['class'][] = 'wpforms-selected';
			}
		}

		return $properties;
	}

	/**
	 * @inheritdoc
	 */
	protected function get_field_populated_single_property_value( $raw_value, $input, $properties, $field ) {
		/*
		 * When the form is submitted we get only choice values from the Fallback.
		 * As payment-checkbox (checkboxes) field doesn't support 'show_values' option -
		 * we should transform that into label to check against using general logic in parent method.
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
	 * @since 1.5.1
	 *
	 * @param array $field Field settings.
	 */
	public function field_options( $field ) {
		/*
		 * Basic field options.
		 */

		// Options open markup.
		$this->field_option(
			'basic-options',
			$field,
			array(
				'markup' => 'open',
			)
		);

		// Label.
		$this->field_option( 'label', $field );

		// Choices option.
		$this->field_option( 'choices_payments', $field );

		// Choices Images.
		$this->field_option( 'choices_images', $field );

		// Description.
		$this->field_option( 'description', $field );

		// Required toggle.
		$this->field_option( 'required', $field );

		// Options close markup.
		$this->field_option(
			'basic-options',
			$field,
			array(
				'markup' => 'close',
			)
		);

		/*
		 * Advanced field options.
		 */

		// Options open markup.
		$this->field_option(
			'advanced-options',
			$field,
			array(
				'markup' => 'open',
			)
		);

		// Choices Images Style (theme).
		$this->field_option( 'choices_images_style', $field );

		// Input columns.
		$this->field_option( 'input_columns', $field );

		// Hide label.
		$this->field_option( 'label_hide', $field );

		// Custom CSS classes.
		$this->field_option( 'css', $field );

		// Options close markup.
		$this->field_option(
			'advanced-options',
			$field,
			array(
				'markup' => 'close',
			)
		);
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.5.1
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
	 * @since 1.5.1
	 *
	 * @param array $field      Field settings.
	 * @param array $deprecated Deprecated array.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		// Define data.
		$container = $field['properties']['input_container'];
		$choices   = $field['properties']['inputs'];

		printf(
			'<ul %s>',
			wpforms_html_attributes( $container['id'], $container['class'], $container['data'] )
		);

			foreach ( $choices as $key => $choice ) {

				printf(
					'<li %s>',
					wpforms_html_attributes( $choice['container']['id'], $choice['container']['class'], $choice['container']['data'], $choice['container']['attr'] )
				);

					if ( empty( $field['dynamic_choices'] ) && ! empty( $field['choices_images'] ) ) {

						// Image choices.
						printf(
							'<label %s>',
							wpforms_html_attributes( $choice['label']['id'], $choice['label']['class'], $choice['label']['data'], $choice['label']['attr'] )
						);

							if ( ! empty( $choice['image'] ) ) {
								printf(
									'<span class="wpforms-image-choices-image"><img src="%s" alt="%s"%s></span>',
									esc_url( $choice['image'] ),
									esc_attr( $choice['label']['text'] ),
									! empty( $choice['label']['text'] ) ? ' title="' . esc_attr( $choice['label']['text'] ) . '"' : ''
								);
							}

							if ( 'none' === $field['choices_images_style'] ) {
								echo '<br>';
							}

							printf(
								'<input type="checkbox" %s %s %s>',
								wpforms_html_attributes( $choice['id'], $choice['class'], $choice['data'], $choice['attr'] ),
								esc_attr( $choice['required'] ),
								checked( '1', $choice['default'], false )
							);

							echo '<span class="wpforms-image-choices-label">' . wp_kses_post( $choice['label']['text'] ) . '</span>';

						echo '</label>';

					} else {

						// Normal display.
						printf(
							'<input type="checkbox" %s %s %s>',
							wpforms_html_attributes( $choice['id'], $choice['class'], $choice['data'], $choice['attr'] ),
							esc_attr( $choice['required'] ),
							checked( '1', $choice['default'], false )
						);

						printf(
							'<label %s>%s</label>',
							wpforms_html_attributes( $choice['label']['id'], $choice['label']['class'], $choice['label']['data'], $choice['label']['attr'] ),
							wp_kses_post( $choice['label']['text'] )
						); // WPCS: XSS ok.
					}

				echo '</li>';
			}

		echo '</ul>';
	}

	/**
	 * Validates field on form submit.
	 *
	 * @since 1.5.1
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Array of selected choice IDs.
	 * @param array $form_data    Form data and settings.
	 */
	public function validate( $field_id, $field_submit, $form_data ) {

		$error = '';

		// Basic required check - If field is marked as required, check for entry data.
		if ( ! empty( $form_data['fields'][ $field_id ]['required'] ) && empty( $field_submit ) ) {
			$error = wpforms_get_required_label();
		}

		if ( ! empty( $field_submit ) ) {
			foreach ( (array) $field_submit as $checked_choice ) {
				// Validate that the option selected is real.
				if ( empty( $form_data['fields'][ $field_id ]['choices'][ (int) $checked_choice ] ) ) {
					$error = esc_html__( 'Invalid payment option.', 'wpforms' );
					break;
				}
			}
		}

		if ( ! empty( $error ) ) {
			wpforms()->process->errors[ $form_data['id'] ][ $field_id ] = $error;
		}
	}

	/**
	 * Formats and sanitizes field.
	 *
	 * @since 1.5.1
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Array of selected choice IDs.
	 * @param array $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		$field_submit  = array_values( (array) $field_submit );
		$field         = $form_data['fields'][ $field_id ];
		$name          = sanitize_text_field( $field['label'] );
		$amount        = 0;
		$images        = array();
		$choice_values = array();
		$choice_labels = array();
		$choice_keys   = array();

		if ( ! empty( $field_submit ) ) {
			foreach ( $field_submit as $choice_checked ) {
				foreach ( $field['choices'] as $choice_id => $choice ) {
					// Exit early.
					if ( (int) $choice_checked !== (int) $choice_id ) {
						continue;
					}

					$value = (float) wpforms_sanitize_amount( $choice['value'] );

					// Increase total amount.
					$amount += $value;

					$value        = wpforms_format_amount( $value, true );
					$choice_label = '';

					if ( ! empty( $choice['label'] ) ) {
						$choice_label = sanitize_text_field( $choice['label'] );
						$value        = $choice_label . ' - ' . $value;
					}

					$choice_labels[] = $choice_label;
					$choice_values[] = $value;
					$choice_keys[]   = $choice_id;
				}
			}

			if ( ! empty( $choice_keys ) && ! empty( $field['choices_images'] ) ) {
				foreach ( $choice_keys as $choice_key ) {
					$images[] = ! empty( $field['choices'][ $choice_key ]['image'] ) ? esc_url_raw( $field['choices'][ $choice_key ]['image'] ) : '';
				}
			}
		}

		wpforms()->process->fields[ $field_id ] = array(
			'name'         => $name,
			'value'        => implode( "\r\n", $choice_values ),
			'value_choice' => implode( "\r\n", $choice_labels ),
			'value_raw'    => implode( ',', array_map( 'absint', $field_submit ) ),
			'amount'       => wpforms_format_amount( $amount ),
			'amount_raw'   => $amount,
			'currency'     => wpforms_setting( 'currency', 'USD' ),
			'images'       => $images,
			'id'           => absint( $field_id ),
			'type'         => $this->type,
		);
	}
}

new WPForms_Field_Payment_Checkbox();
