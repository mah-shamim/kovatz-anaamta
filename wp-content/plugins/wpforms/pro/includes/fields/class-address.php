<?php

/**
 * Address text field.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
class WPForms_Field_Address extends WPForms_Field {

	/**
	 * Address schemes: 'us' or 'international' by default.
	 *
	 * @since 1.2.7
	 * @var array
	 */
	public $schemes;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Define field type information.
		$this->name  = esc_html__( 'Address', 'wpforms' );
		$this->type  = 'address';
		$this->icon  = 'fa-map-marker';
		$this->order = 70;
		$this->group = 'fancy';

		// Allow for additional or customizing address schemes.
		$this->schemes = apply_filters(
			'wpforms_address_schemes',
			array(
				'us'            => array(
					'label'          => esc_html__( 'US', 'wpforms' ),
					'address1_label' => esc_html__( 'Address Line 1', 'wpforms' ),
					'address2_label' => esc_html__( 'Address Line 2', 'wpforms' ),
					'city_label'     => esc_html__( 'City', 'wpforms' ),
					'postal_label'   => esc_html__( 'Zip Code', 'wpforms' ),
					'state_label'    => esc_html__( 'State', 'wpforms' ),
					'states'         => wpforms_us_states(),
				),
				'international' => array(
					'label'          => esc_html__( 'International', 'wpforms' ),
					'address1_label' => esc_html__( 'Address Line 1', 'wpforms' ),
					'address2_label' => esc_html__( 'Address Line 2', 'wpforms' ),
					'city_label'     => esc_html__( 'City', 'wpforms' ),
					'postal_label'   => esc_html__( 'Postal Code', 'wpforms' ),
					'state_label'    => esc_html__( 'State / Province / Region', 'wpforms' ),
					'states'         => '',
					'country_label'  => esc_html__( 'Country', 'wpforms' ),
					'countries'      => wpforms_countries(),
				),
			)
		);

		// Define additional field properties.
		add_filter( 'wpforms_field_properties_address', array( $this, 'field_properties' ), 5, 3 );
	}

	/**
	 * Define additional field properties.
	 *
	 * @since 1.4.1
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field data and settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function field_properties( $properties, $field, $form_data ) {

		// Determine scheme we should use moving forward.
		$scheme = 'us';
		if ( ! empty( $field['scheme'] ) ) {
			$scheme = esc_attr( $field['scheme'] );
		} elseif ( ! empty( $field['format'] ) ) {
			// <1.2.7 backwards compatibility.
			$scheme = esc_attr( $field['format'] );
		}

		// Expanded formats.
		// Remove primary for expanded formats.
		unset( $properties['inputs']['primary'] );

		$form_id  = absint( $form_data['id'] );
		$field_id = absint( $field['id'] );

		// Properties shared by both core schemes.
		$props      = array(
			'inputs' => array(
				'address1' => array(
					'attr'     => array(
						'name'        => "wpforms[fields][{$field_id}][address1]",
						'value'       => ! empty( $field['address1_default'] ) ? apply_filters( 'wpforms_process_smart_tags', $field['address1_default'], $form_data ) : '',
						'placeholder' => ! empty( $field['address1_placeholder'] ) ? $field['address1_placeholder'] : '',
					),
					'block'    => array(),
					'class'    => array(
						'wpforms-field-address-address1',
					),
					'data'     => array(),
					'id'       => "wpforms-{$form_id}-field_{$field_id}",
					'required' => ! empty( $field['required'] ) ? 'required' : '',
					'sublabel' => array(
						'hidden' => ! empty( $field['sublabel_hide'] ),
						'value'  => isset( $this->schemes[ $scheme ]['address1_label'] ) ? $this->schemes[ $scheme ]['address1_label'] : '',
					),
				),
				'address2' => array(
					'attr'     => array(
						'name'        => "wpforms[fields][{$field_id}][address2]",
						'value'       => ! empty( $field['address2_default'] ) ? apply_filters( 'wpforms_process_smart_tags', $field['address2_default'], $form_data ) : '',
						'placeholder' => ! empty( $field['address2_placeholder'] ) ? $field['address2_placeholder'] : '',
					),
					'block'    => array(),
					'class'    => array(
						'wpforms-field-address-address2',
					),
					'data'     => array(),
					'hidden'   => ! empty( $field['address2_hide'] ),
					'id'       => "wpforms-{$form_id}-field_{$field_id}-address2",
					'required' => '',
					'sublabel' => array(
						'hidden' => ! empty( $field['sublabel_hide'] ),
						'value'  => isset( $this->schemes[ $scheme ]['address2_label'] ) ? $this->schemes[ $scheme ]['address2_label'] : '',
					),
				),
				'city'     => array(
					'attr'     => array(
						'name'        => "wpforms[fields][{$field_id}][city]",
						'value'       => ! empty( $field['city_default'] ) ? apply_filters( 'wpforms_process_smart_tags', $field['city_default'], $form_data ) : '',
						'placeholder' => ! empty( $field['city_placeholder'] ) ? $field['city_placeholder'] : '',
					),
					'block'    => array(
						'wpforms-field-row-block',
						'wpforms-one-half',
						'wpforms-first',
					),
					'class'    => array(
						'wpforms-field-address-city',
					),
					'data'     => array(),
					'id'       => "wpforms-{$form_id}-field_{$field_id}-city",
					'required' => ! empty( $field['required'] ) ? 'required' : '',
					'sublabel' => array(
						'hidden' => ! empty( $field['sublabel_hide'] ),
						'value'  => isset( $this->schemes[ $scheme ]['city_label'] ) ? $this->schemes[ $scheme ]['city_label'] : '',
					),
				),
				'state'    => array(
					'attr'     => array(
						'name'        => "wpforms[fields][{$field_id}][state]",
						'value'       => ! empty( $field['state_default'] ) ? apply_filters( 'wpforms_process_smart_tags', $field['state_default'], $form_data ) : '',
						'placeholder' => ! empty( $field['state_placeholder'] ) ? $field['state_placeholder'] : '',
					),
					'block'    => array(
						'wpforms-field-row-block',
						'wpforms-one-half',
					),
					'class'    => array(
						'wpforms-field-address-state',
					),
					'data'     => array(),
					'id'       => "wpforms-{$form_id}-field_{$field_id}-state",
					'options'  => isset( $this->schemes[ $scheme ]['states'] ) ? $this->schemes[ $scheme ]['states'] : '',
					'required' => ! empty( $field['required'] ) ? 'required' : '',
					'sublabel' => array(
						'hidden' => ! empty( $field['sublabel_hide'] ),
						'value'  => isset( $this->schemes[ $scheme ]['state_label'] ) ? $this->schemes[ $scheme ]['state_label'] : '',
					),
				),
				'postal'   => array(
					'attr'     => array(
						'name'        => "wpforms[fields][{$field_id}][postal]",
						'value'       => ! empty( $field['postal_default'] ) ? apply_filters( 'wpforms_process_smart_tags', $field['postal_default'], $form_data ) : '',
						'placeholder' => ! empty( $field['postal_placeholder'] ) ? $field['postal_placeholder'] : '',
					),
					'block'    => array(
						'wpforms-field-row-block',
						'wpforms-one-half',
						'wpforms-first',
					),
					'class'    => array(
						'wpforms-field-address-postal',
					),
					'data'     => array(),
					'hidden'   => ! empty( $field['postal_hide'] ) || ! isset( $this->schemes[ $scheme ]['postal_label'] ) ? true : false,
					'id'       => "wpforms-{$form_id}-field_{$field_id}-postal",
					'required' => ! empty( $field['required'] ) ? 'required' : '',
					'sublabel' => array(
						'hidden' => ! empty( $field['sublabel_hide'] ),
						'value'  => isset( $this->schemes[ $scheme ]['postal_label'] ) ? $this->schemes[ $scheme ]['postal_label'] : '',
					),
				),
				'country'  => array(
					'attr'     => array(
						'name'        => "wpforms[fields][{$field_id}][country]",
						'value'       => ! empty( $field['country_default'] ) ? apply_filters( 'wpforms_process_smart_tags', $field['country_default'], $form_data ) : '',
						'placeholder' => ! empty( $field['country_placeholder'] ) ? $field['country_placeholder'] : '',
					),
					'block'    => array(
						'wpforms-field-row-block',
						'wpforms-one-half',
					),
					'class'    => array(
						'wpforms-field-address-country',
					),
					'data'     => array(),
					'hidden'   => ! empty( $field['country_hide'] ) || ! isset( $this->schemes[ $scheme ]['countries'] ) ? true : false,
					'id'       => "wpforms-{$form_id}-field_{$field_id}-country",
					'options'  => isset( $this->schemes[ $scheme ]['countries'] ) ? $this->schemes[ $scheme ]['countries'] : '',
					'required' => ! empty( $field['required'] ) ? 'required' : '',
					'sublabel' => array(
						'hidden' => ! empty( $field['sublabel_hide'] ),
						'value'  => isset( $this->schemes[ $scheme ]['country_label'] ) ? $this->schemes[ $scheme ]['country_label'] : '',
					),
				),
			),
		);
		$properties = array_merge_recursive( $properties, $props );

		// Input keys.
		$keys = array( 'address1', 'address2', 'city', 'state', 'postal', 'country' );

		// Add input error class if needed.
		foreach ( $keys as $key ) {
			if ( ! empty( $properties['error']['value'][ $key ] ) ) {
				$properties['inputs'][ $key ]['class'][] = 'wpforms-error';
			}
		}

		// Add input required class if needed.
		foreach ( $keys as $key ) {
			if ( ! empty( $properties['inputs'][ $key ]['required'] ) ) {
				$properties['inputs'][ $key ]['class'][] = 'wpforms-field-required';
			}
		}

		// Add Postal code input mask for US address.
		if ( 'us' === $scheme ) {
			$properties['inputs']['postal']['class'][]                  = 'wpforms-masked-input';
			$properties['inputs']['postal']['data']['inputmask-mask']   = '99999[-9999]';
			$properties['inputs']['postal']['data']['inputmask-greedy'] = 'false';
		}

		return $properties;
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
		$this->field_option(
			'basic-options',
			$field,
			array(
				'markup' => 'open',
			)
		);

		// Label.
		$this->field_option( 'label', $field );

		// Address Scheme - was "format" key prior to 1.2.7.
		$scheme = ! empty( $field['scheme'] ) ? esc_attr( $field['scheme'] ) : 'us';
		if ( empty( $scheme ) && ! empty( $field['format'] ) ) {
			$scheme = esc_attr( $field['format'] );
		}
		$tooltip = esc_html__( 'Select scheme format for the address field.', 'wpforms' );
		$options = array();
		foreach ( $this->schemes as $slug => $s ) {
			$options[ $slug ] = $s['label'];
		}
		$output  = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'scheme',
				'value'   => esc_html__( 'Scheme', 'wpforms' ),
				'tooltip' => $tooltip,
			),
			false
		);
		$output .= $this->field_element(
			'select',
			$field,
			array(
				'slug'    => 'scheme',
				'value'   => $scheme,
				'options' => $options,
			),
			false
		);
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'scheme',
				'content' => $output,
			)
		);

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

		// Size.
		$this->field_option( 'size', $field );

		// Address Line 1.
		$address1_placeholder = ! empty( $field['address1_placeholder'] ) ? esc_attr( $field['address1_placeholder'] ) : '';
		$address1_default     = ! empty( $field['address1_default'] ) ? esc_attr( $field['address1_default'] ) : '';
		printf( '<div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-address1" id="wpforms-field-option-row-%d-address1" data-subfield="address-1" data-field-id="%d">', $field['id'], $field['id'] );
			$this->field_element( 'label', $field, array(
				'slug'  => 'address1_placeholder',
				'value' => esc_html__( 'Address Line 1', 'wpforms' ),
			) );
			echo '<div class="placeholder">';
				printf( '<input type="text" class="placeholder" id="wpforms-field-option-%d-address1_placeholder" name="fields[%d][address1_placeholder]" value="%s">', $field['id'], $field['id'], $address1_placeholder );
				printf( '<label for="wpforms-field-option-%d-address1_placeholder" class="sub-label">%s</label>', $field['id'], esc_html__( 'Placeholder', 'wpforms' ) );
			echo '</div>';
			echo '<div class="default">';
				printf( '<input type="text" class="default" id="wpforms-field-option-%d-address1_default" name="fields[%d][address1_default]" value="%s">', $field['id'], $field['id'], $address1_default );
				printf( '<label for="wpforms-field-option-%d-address1_default" class="sub-label">%s</label>', $field['id'], esc_html__( 'Default Value', 'wpforms' ) );
			echo '</div>';
		echo '</div>';

		// Address Line 2.
		$address2_placeholder = ! empty( $field['address2_placeholder'] ) ? esc_attr( $field['address2_placeholder'] ) : '';
		$address2_default     = ! empty( $field['address2_default'] ) ? esc_attr( $field['address2_default'] ) : '';
		$address2_hide        = ! empty( $field['address2_hide'] ) ? true : false;
		printf( '<div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-address2" id="wpforms-field-option-row-%d-address2" data-subfield="address-2" data-field-id="%d">', $field['id'], $field['id'] );
			$this->field_element( 'label', $field, array(
				'slug'  => 'address2_placeholder',
				'value' => esc_html__( 'Address Line 2', 'wpforms' ),
			) );
			echo '<div class="placeholder">';
				printf( '<input type="text" class="placeholder" id="wpforms-field-option-%d-address2_placeholder" name="fields[%d][address2_placeholder]" value="%s">', $field['id'], $field['id'], $address2_placeholder );
				printf( '<label for="wpforms-field-option-%d-address2_placeholder" class="sub-label">%s</label>', $field['id'], esc_html__( 'Placeholder', 'wpforms' ) );
			echo '</div>';
			echo '<div class="default">';
				printf( '<input type="text" class="default" id="wpforms-field-option-%d-address2_default" name="fields[%d][address2_default]" value="%s">', $field['id'], $field['id'], $address2_default );
				printf( '<label for="wpforms-field-option-%d-address2_default" class="sub-label">%s</label>', $field['id'], esc_html__( 'Default Value', 'wpforms' ) );
			echo '</div>';
			echo '<div class="hide">';
				printf( ' <input type="checkbox" class="hide" name="fields[%d][address2_hide]" value="1" %s>', $field['id'], checked( true, $address2_hide, false ) );
			echo '</div>';
		echo '</div>';

		// City.
		$city_placeholder = ! empty( $field['city_placeholder'] ) ? esc_attr( $field['city_placeholder'] ) : '';
		$city_default     = ! empty( $field['city_default'] ) ? esc_attr( $field['city_default'] ) : '';
		printf( '<div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-city" id="wpforms-field-option-row-%d-city" data-subfield="city" data-field-id="%d">', $field['id'], $field['id'] );
			$this->field_element( 'label', $field, array(
				'slug'  => 'city_placeholder',
				'value' => esc_html__( 'City', 'wpforms' ),
			) );
			echo '<div class="placeholder">';
				printf( '<input type="text" class="placeholder" id="wpforms-field-option-%d-city_placeholder" name="fields[%d][city_placeholder]" value="%s">', $field['id'], $field['id'], $city_placeholder );
				printf( '<label for="wpforms-field-option-%d-city_placeholder" class="sub-label">%s</label>', $field['id'], esc_html__( 'Placeholder', 'wpforms' ) );
			echo '</div>';
			echo '<div class="default">';
				printf( '<input type="text" class="default" id="wpforms-field-option-%d-city_default" name="fields[%d][city_default]" value="%s">', $field['id'], $field['id'], $city_default );
				printf( '<label for="wpforms-field-option-%d-city_default" class="sub-label">%s</label>', $field['id'], esc_html__( 'Default Value', 'wpforms' ) );
			echo '</div>';
		echo '</div>';

		// State.
		$state_placeholder = ! empty( $field['state_placeholder'] ) ? esc_attr( $field['state_placeholder'] ) : '';
		$state_default     = ! empty( $field['state_default'] ) ? esc_attr( $field['state_default'] ) : '';
		printf( '<div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-state" id="wpforms-field-option-row-%d-state" data-subfield="state" data-field-id="%d">', $field['id'], $field['id'] );
			$this->field_element( 'label', $field, array(
				'slug'  => 'state_placeholder',
				'value' => esc_html__( 'State / Province / Region', 'wpforms' ),
			) );
			echo '<div class="placeholder">';
				printf( '<input type="text" class="placeholder" id="wpforms-field-option-%d-state_placeholder" name="fields[%d][state_placeholder]" value="%s">', $field['id'], $field['id'], $state_placeholder );
				printf( '<label for="wpforms-field-option-%d-state_placeholder" class="sub-label">%s</label>', $field['id'], esc_html__( 'Placeholder', 'wpforms' ) );
			echo '</div>';
			echo '<div class="default">';
				printf( '<input type="text" class="default" id="wpforms-field-option-%d-state_default" name="fields[%d][state_default]" value="%s">', $field['id'], $field['id'], $state_default );
				printf( '<label for="wpforms-field-option-%d-state_default" class="sub-label">%s</label>', $field['id'], esc_html__( 'Default Value', 'wpforms' ) );
			echo '</div>';
		echo '</div>';

		// ZIP/Postal.
		$postal_placeholder = ! empty( $field['postal_placeholder'] ) ? esc_attr( $field['postal_placeholder'] ) : '';
		$postal_default     = ! empty( $field['postal_default'] ) ? esc_attr( $field['postal_default'] ) : '';
		$postal_hide        = ! empty( $field['postal_hide'] );
		$postal_visibility  = ! isset( $this->schemes[ $scheme ]['postal_label'] ) ? 'wpforms-hidden' : '';
		printf( '<div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-postal %s" id="wpforms-field-option-row-%d-postal" data-subfield="postal" data-field-id="%d">', $postal_visibility, $field['id'], $field['id'] );
			$this->field_element( 'label', $field, array(
				'slug'  => 'postal_placeholder',
				'value' => esc_html__( 'ZIP / Postal', 'wpforms' ),
			) );
			echo '<div class="placeholder">';
				printf( '<input type="text" class="placeholder" id="wpforms-field-option-%d-postal_placeholder" name="fields[%d][postal_placeholder]" value="%s">', $field['id'], $field['id'], $postal_placeholder );
				printf( '<label for="wpforms-field-option-%d-postal_placeholder" class="sub-label">%s</label>', $field['id'], esc_html__( 'Placeholder', 'wpforms' ) );
			echo '</div>';
			echo '<div class="default">';
				printf( '<input type="text" class="default" id="wpforms-field-option-%d-postal_default" name="fields[%d][postal_default]" value="%s">', $field['id'], $field['id'], $postal_default );
				printf( '<label for="wpforms-field-option-%d-postal_default" class="sub-label">%s</label>', $field['id'], esc_html__( 'Default Value', 'wpforms' ) );
			echo '</div>';
			echo '<div class="hide">';
				printf( ' <input type="checkbox" class="hide" name="fields[%d][postal_hide]" value="1" %s>', $field['id'], checked( true, $postal_hide, false ) );
			echo '</div>';
		echo '</div>';

		// Country.
		$country_placeholder = ! empty( $field['country_placeholder'] ) ? esc_attr( $field['country_placeholder'] ) : '';
		$country_default     = ! empty( $field['country_default'] ) ? esc_attr( $field['country_default'] ) : '';
		$country_hide        = ! empty( $field['country_hide'] ) ? true : false;
		$country_visibility  = ! isset( $this->schemes[ $scheme ]['countries'] ) ? 'wpforms-hidden' : '';

		printf( '<div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-country %s" id="wpforms-field-option-row-%d-country" data-subfield="country" data-field-id="%d">', $country_visibility, $field['id'], $field['id'] );
			$this->field_element( 'label', $field, array(
				'slug'  => 'country_placeholder',
				'value' => esc_html__( 'Country', 'wpforms' ),
			) );
			echo '<div class="placeholder">';
				printf( '<input type="text" class="placeholder" id="wpforms-field-option-%d-country_placeholder" name="fields[%d][country_placeholder]" value="%s">', $field['id'], $field['id'], $country_placeholder );
				printf( '<label for="wpforms-field-option-%d-country_placeholder" class="sub-label">%s</label>', $field['id'], esc_html__( 'Placeholder', 'wpforms' ) );
			echo '</div>';
			echo '<div class="default">';
				printf( '<input type="text" class="default" id="wpforms-field-option-%d-country_default" name="fields[%d][country_default]" value="%s">', $field['id'], $field['id'], $country_default );
				printf( '<label for="wpforms-field-option-%d-country_default" class="sub-label">%s</label>', $field['id'], esc_html__( 'Default Value', 'wpforms' ) );
			echo '</div>';
			echo '<div class="hide">';
				printf( ' <input type="checkbox" class="hide" name="fields[%d][country_hide]" value="1" %s>', $field['id'], checked( '1', $country_hide, false ) );
			echo '</div>';
		echo '</div>';

		// Hide label.
		$this->field_option( 'label_hide', $field );

		// Hide sublabel.
		$this->field_option( 'sublabel_hide', $field );

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
	 * @since 1.0.0
	 *
	 * @param array $field
	 */
	public function field_preview( $field ) {

		// Define data.
		$address1_placeholder = ! empty( $field['address1_placeholder'] ) ? esc_attr( $field['address1_placeholder'] ) : '';
		$address2_placeholder = ! empty( $field['address2_placeholder'] ) ? esc_attr( $field['address2_placeholder'] ) : '';
		$address2_hide        = ! empty( $field['address2_hide'] ) ? 'wpforms-hide' : '';
		$city_placeholder     = ! empty( $field['city_placeholder'] ) ? esc_attr( $field['city_placeholder'] ) : '';
		$state_placeholder    = ! empty( $field['state_placeholder'] ) ? esc_attr( $field['state_placeholder'] ) : '';
		$state_default        = ! empty( $field['state_default'] ) ? esc_attr( $field['state_default'] ) : '';
		$postal_placeholder   = ! empty( $field['postal_placeholder'] ) ? esc_attr( $field['postal_placeholder'] ) : '';
		$postal_hide          = ! empty( $field['postal_hide'] ) ? 'wpforms-hide' : '';
		$country_placeholder  = ! empty( $field['country_placeholder'] ) ? esc_attr( $field['country_placeholder'] ) : '';
		$country_default      = ! empty( $field['country_default'] ) ? esc_attr( $field['country_default'] ) : '';
		$country_hide         = ! empty( $field['country_hide'] ) ? 'wpforms-hide' : '';
		$format               = ! empty( $field['format'] ) ? esc_attr( $field['format'] ) : 'us';
		$scheme_selected      = ! empty( $field['scheme'] ) ? esc_attr( $field['scheme'] ) : $format;

		// Label.
		$this->field_preview_option( 'label', $field );

		// Field elements.
		foreach ( $this->schemes as $slug => $scheme ) {

			$active = $slug !== $scheme_selected ? 'wpforms-hide' : '';

			printf( '<div class="wpforms-address-scheme wpforms-address-scheme-%s %s">', $slug, $active );

				// Row 1 - Address Line 1.
				echo '<div class="wpforms-field-row wpforms-address-1">';
					printf( '<input type="text" placeholder="%s" disabled>', $address1_placeholder );
					printf( '<label class="wpforms-sub-label">%s</label>', $scheme['address1_label'] );
				echo '</div>';

				// Row 2 - Address Line 2.
				printf( '<div class="wpforms-field-row wpforms-address-2 %s">', $address2_hide );
					printf( '<input type="text" placeholder="%s" disabled>', $address2_placeholder );
					printf( '<label class="wpforms-sub-label">%s</label>', $scheme['address2_label'] );
				echo '</div>';

				// Row 3 - City & State.
				echo '<div class="wpforms-field-row">';

					// City.
					echo '<div class="wpforms-city wpforms-one-half ">';
						printf( '<input type="text" placeholder="%s" disabled>', $city_placeholder );
						printf( '<label class="wpforms-sub-label">%s</label>', $scheme['city_label'] );
					echo '</div>';

					// State / Providence / Region.
					echo '<div class="wpforms-state wpforms-one-half last">';

						if ( isset( $scheme['states'] ) && empty( $scheme['states'] ) ) {

							// State text input.
							printf( '<input type="text" placeholder="%s" disabled>', $state_placeholder );

						} elseif ( ! empty( $scheme['states'] ) && is_array( $scheme['states'] ) ) {

							// State select.
							echo '<select disabled>';
							if ( ! empty( $state_placeholder ) ) {
								printf( '<option value="" class="placeholder" selected>%s</option>', $state_placeholder );
							}
							foreach ( $scheme['states'] as $key => $state ) {
								$select = false;
								if ( ! empty( $state_default ) && ( $key === $state_default || $state === $state_default ) ) {
									$select = true;
								}
								$selected = selected( $select, true, false );
								printf( '<option %s>%s</option>', $selected, $state );
							}
							echo '</select>';
						}

						printf( '<label class="wpforms-sub-label">%s</label>', $scheme['state_label'] );
					echo '</div>';

				echo '</div>';

				// Row 4 - Zip & Country.
				echo '<div class="wpforms-field-row">';

					// ZIP / Postal.
					printf( '<div class="wpforms-postal wpforms-one-half %s">', $postal_hide );
						printf( '<input type="text" placeholder="%s" disabled>', $postal_placeholder );
						printf( '<label class="wpforms-sub-label">%s</label>', $scheme['postal_label'] );
					echo '</div>';

					// Country.
					printf( '<div class="wpforms-country wpforms-one-half last %s">', $country_hide );

						if ( isset( $scheme['countries'] ) && empty( $scheme['countries'] ) ) {

							// Country text input.
							printf( '<input type="text" placeholder="%s" disabled>', $state_placeholder );
							printf( '<label class="wpforms-sub-label">%s</label>', $scheme['country_label'] );

						} elseif ( ! empty( $scheme['countries'] ) && is_array( $scheme['countries'] ) ) {

							// Country select.
							echo '<select disabled>';
							if ( ! empty( $country_placeholder ) ) {
								printf( '<option value="" class="placeholder" selected>%s</option>', $country_placeholder );
							}
							foreach ( $scheme['countries'] as $key => $country ) {
								$select = false;
								if ( ! empty( $country_default ) && ( $key === $country_default || $country === $country_default ) ) {
									$select = true;
								}
								$selected = selected( $select, true, false );
								printf( '<option %s>%s</option>', $selected, $country );
							}
							echo '</select>';
							printf( '<label class="wpforms-sub-label">%s</label>', $scheme['country_label'] );
						}

					echo '</div>';

				echo '</div>';

			echo '</div>';
		}

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field      Field data and settings.
	 * @param array $deprecated Deprecated field attributes. Use field properties instead.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		// Define data.
		$format   = ! empty( $field['format'] ) ? esc_attr( $field['format'] ) : 'us';
		$scheme   = ! empty( $field['scheme'] ) ? esc_attr( $field['scheme'] ) : $format;
		$address1 = ! empty( $field['properties']['inputs']['address1'] ) ? $field['properties']['inputs']['address1'] : array();
		$address2 = ! empty( $field['properties']['inputs']['address2'] ) ? $field['properties']['inputs']['address2'] : array();
		$city     = ! empty( $field['properties']['inputs']['city'] ) ? $field['properties']['inputs']['city'] : array();
		$state    = ! empty( $field['properties']['inputs']['state'] ) ? $field['properties']['inputs']['state'] : array();
		$postal   = ! empty( $field['properties']['inputs']['postal'] ) ? $field['properties']['inputs']['postal'] : array();
		$country  = ! empty( $field['properties']['inputs']['country'] ) ? $field['properties']['inputs']['country'] : array();

		// Row wrapper.
		echo '<div class="wpforms-field-row wpforms-field-' . sanitize_html_class( $field['size'] ) . '">';

			// Address Line 1.
			echo '<div ' . wpforms_html_attributes( false, $address1['block'] ) . '>';
				$this->field_display_sublabel( 'address1', 'before', $field );
				printf(
					'<input type="text" %s %s>',
					wpforms_html_attributes( $address1['id'], $address1['class'], $address1['data'], $address1['attr'] ),
					$address1['required']
				);
				$this->field_display_sublabel( 'address1', 'after', $field );
				$this->field_display_error( 'address1', $field );
			echo '</div>';

		echo '</div>';

		if ( empty( $address2['hidden'] ) ) {

			// Row wrapper.
			echo '<div class="wpforms-field-row wpforms-field-' . sanitize_html_class( $field['size'] ) . '">';

				// Address Line 2.
				echo '<div ' . wpforms_html_attributes( false, $address2['block'] ) . '>';
					$this->field_display_sublabel( 'address2', 'before', $field );
					printf(
						'<input type="text" %s %s>',
						wpforms_html_attributes( $address2['id'], $address2['class'], $address2['data'], $address2['attr'] ),
						$address2['required']
					);
					$this->field_display_sublabel( 'address2', 'after', $field );
					$this->field_display_error( 'address2', $field );
				echo '</div>';

			echo '</div>';
		}

		// Row wrapper.
		echo '<div class="wpforms-field-row wpforms-field-' . sanitize_html_class( $field['size'] ) . '">';

			// City.
			echo '<div ' . wpforms_html_attributes( false, $city['block'] ) . '>';
				$this->field_display_sublabel( 'city', 'before', $field );
				printf(
					'<input type="text" %s %s>',
					wpforms_html_attributes( $city['id'], $city['class'], $city['data'], $city['attr'] ),
					$city['required']
				);
				$this->field_display_sublabel( 'city', 'after', $field );
				$this->field_display_error( 'city', $field );
			echo '</div>';

			// State.
			if ( isset( $this->schemes[ $scheme ]['states'] ) && isset( $state['options'] ) ) {

				echo '<div ' . wpforms_html_attributes( false, $state['block'] ) . '>';
					$this->field_display_sublabel( 'state', 'before', $field );
					if ( empty( $state['options'] ) ) {
						printf(
							'<input type="text" %s %s>',
							wpforms_html_attributes( $state['id'], $state['class'], $state['data'], $state['attr'] ),
							$state['required']
						);
					} else {
						printf(
							'<select %s %s>',
							wpforms_html_attributes( $state['id'], $state['class'], $state['data'], $state['attr'] ),
							$state['required']
						);
							if ( ! empty( $state['attr']['placeholder'] ) && empty( $state['attr']['value'] ) ) {
								printf( '<option class="placeholder" value="" selected disabled>%s</option>', sanitize_text_field( $state['attr']['placeholder'] ) );
							}
							foreach ( $state['options'] as $state_key => $state_label ) {
								$select = false;
								if ( ! empty( $state['attr']['value'] ) && ( $state_key === $state['attr']['value'] || $state_label === $state['attr']['value'] ) ) {
									$select = true;
								}
								printf(
									'<option value="%s" %s>%s</option>',
									$state_key,
									selected( $select, true, false ),
									$state_label
								);
							}
						echo '</select>';
					}
					$this->field_display_sublabel( 'state', 'after', $field );
					$this->field_display_error( 'state', $field );
				echo '</div>';
			}

		echo '</div>';

		// Only render this row if we have at least one of the items.
		if ( empty( $country['hidden'] ) || empty( $postal['hidden'] ) ) {

			// Row wrapper.
			echo '<div class="wpforms-field-row wpforms-field-' . sanitize_html_class( $field['size'] ) . '">';

				// Postal.
				if ( empty( $postal['hidden'] ) ) {

					echo '<div ' . wpforms_html_attributes( false, $postal['block'] ) . '>';
						$this->field_display_sublabel( 'postal', 'before', $field );
						printf(
							'<input type="text" %s %s>',
							wpforms_html_attributes( $postal['id'], $postal['class'], $postal['data'], $postal['attr'] ),
							$postal['required']
						);
						$this->field_display_sublabel( 'postal', 'after', $field );
						$this->field_display_error( 'postal', $field );
					echo '</div>';
				}

				// Country.
				if ( isset( $country['options'] ) && empty( $country['hidden'] ) ) {

					echo '<div ' . wpforms_html_attributes( false, $country['block'] ) . '>';
						$this->field_display_sublabel( 'country', 'before', $field );
						if ( empty( $country['options'] ) ) {
							printf(
								'<input type="text" %s %s>',
								wpforms_html_attributes( $country['id'], $country['class'], $country['data'], $country['attr'] ),
								$country['required']
							);
						} else {
							printf( '<select %s %s>',
								wpforms_html_attributes( $country['id'], $country['class'], $country['data'], $country['attr'] ),
								$country['required']
							);
								if ( ! empty( $country['attr']['placeholder'] ) && empty( $country['attr']['value'] ) ) {
									printf( '<option class="placeholder" value="" selected disabled>%s</option>', sanitize_text_field( $country['attr']['placeholder'] ) );
								}
								foreach ( $country['options'] as $country_key => $country_label ) {
									$select = false;
									if ( ! empty( $country['attr']['value'] ) && ( $country_key === $country['attr']['value'] || $country_label === $country['attr']['value'] ) ) {
										$select = true;
									}
									printf(
										'<option value="%s" %s>%s</option>',
										$country_key,
										selected( $select, true, false ),
										$country_label
									);
								}
							echo '</select>';
						}
						$this->field_display_sublabel( 'country', 'after', $field );
						$this->field_display_error( 'country', $field );
					echo '</div>';
				}

			echo '</div>';
		}
	}

	/**
	 * Validates field on form submit.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Submitted field values.
	 * @param array $form_data    Form data and settings.
	 */
	public function validate( $field_id, $field_submit, $form_data ) {

		$form_id  = $form_data['id'];
		$required = wpforms_get_required_label();
		$scheme   = ! empty( $form_data['fields'][ $field_id ]['scheme'] ) ? $form_data['fields'][ $field_id ]['scheme'] : $form_data['fields'][ $field_id ]['format'];

		// Extended required validation needed for the different address fields.
		if ( ! empty( $form_data['fields'][ $field_id ]['required'] ) ) {

			// Require Address Line 1.
			if ( empty( $field_submit['address1'] ) ) {
				wpforms()->process->errors[ $form_id ][ $field_id ]['address1'] = $required;
			}

			// Require City.
			if ( empty( $field_submit['city'] ) ) {
				wpforms()->process->errors[ $form_id ][ $field_id ]['city'] = $required;
			}

			// Require ZIP/Postal.
			if ( empty( $form_data['fields'][ $field_id ]['postal_hide'] ) && isset( $this->schemes[ $scheme ]['postal_label'] ) && empty( $field_submit['postal'] ) ) {
				wpforms()->process->errors[ $form_id ][ $field_id ]['postal'] = $required;
			}

			// Required State.
			if ( isset( $this->schemes[ $scheme ]['states'] ) && empty( $field_submit['state'] ) ) {
				wpforms()->process->errors[ $form_id ][ $field_id ]['state'] = $required;
			}

			// Required Country.
			if ( empty( $form_data['fields'][ $field_id ]['country_hide'] ) && isset( $this->schemes[ $scheme ]['countries'] ) && empty( $field_submit['country'] ) ) {
				wpforms()->process->errors[ $form_id ][ $field_id ]['country'] = $required;
			}
		}
	}

	/**
	 * Formats field.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Submitted field values.
	 * @param array $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		$name     = ! empty( $form_data['fields'][ $field_id ]['label'] ) ? $form_data['fields'][ $field_id ]['label'] : '';
		$address1 = ! empty( $field_submit['address1'] ) ? $field_submit['address1'] : '';
		$address2 = ! empty( $field_submit['address2'] ) ? $field_submit['address2'] : '';
		$city     = ! empty( $field_submit['city'] ) ? $field_submit['city'] : '';
		$state    = ! empty( $field_submit['state'] ) ? $field_submit['state'] : '';
		$postal   = ! empty( $field_submit['postal'] ) ? $field_submit['postal'] : '';
		$country  = ! empty( $field_submit['country'] ) ? $field_submit['country'] : '';

		$value  = '';
		$value .= ! empty( $address1 ) ? "$address1\n" : '';
		$value .= ! empty( $address2 ) ? "$address2\n" : '';
		if ( ! empty( $city ) && ! empty( $state ) ) {
			$value .= "$city, $state\n";
		} elseif ( ! empty( $state ) ) {
			$value .= "$state\n";
		} elseif ( ! empty( $city ) ) {
			$value .= "$city\n";
		}
		$value .= ! empty( $postal ) ? "$postal\n" : '';
		$value .= ! empty( $country ) ? "$country\n" : '';
		$value  = wpforms_sanitize_textarea_field( $value );

		if ( empty( $city ) && empty( $address1 ) ) {
			$value = '';
		}

		wpforms()->process->fields[ $field_id ] = array(
			'name'     => sanitize_text_field( $name ),
			'value'    => $value,
			'id'       => absint( $field_id ),
			'type'     => $this->type,
			'address1' => sanitize_text_field( $address1 ),
			'address2' => sanitize_text_field( $address2 ),
			'city'     => sanitize_text_field( $city ),
			'state'    => sanitize_text_field( $state ),
			'postal'   => sanitize_text_field( $postal ),
			'country'  => sanitize_text_field( $country ),
		);
	}
}

new WPForms_Field_Address();
