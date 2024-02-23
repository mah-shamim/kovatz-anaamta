<?php

/**
 * Date / Time field.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
class WPForms_Field_Date_Time extends WPForms_Field {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Define field type information.
		$this->name  = esc_html__( 'Date / Time', 'wpforms' );
		$this->type  = 'date-time';
		$this->icon  = 'fa-calendar-o';
		$this->order = 80;
		$this->group = 'fancy';

		// Set custom option wrapper classes.
		add_filter( 'wpforms_builder_field_option_class', array( $this, 'field_option_class' ), 10, 2 );

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
		$form_id        = absint( $form_data['id'] );
		$field_id       = absint( $field['id'] );
		$field_format   = ! empty( $field['format'] ) ? $field['format'] : 'date-time';
		$field_required = ! empty( $field['required'] ) ? 'required' : '';
		$field_size_cls = 'wpforms-field-' . ( ! empty( $field['size'] ) ? $field['size'] : 'medium' );

		$date_format      = ! empty( $field['date_format'] ) ? $field['date_format'] : 'm/d/Y';
		$date_placeholder = ! empty( $field['date_placeholder'] ) ? $field['date_placeholder'] : '';

		$time_placeholder = ! empty( $field['time_placeholder'] ) ? $field['time_placeholder'] : '';
		$time_format      = ! empty( $field['time_format'] ) ? $field['time_format'] : 'g:i A';
		$time_interval    = ! empty( $field['time_interval'] ) ? $field['time_interval'] : '30';

		if (
			! empty( $field['time_format'] ) &&
			( 'H:i' === $field['time_format'] || 'H:i A' === $field['time_format'] )
		) {
			$time_validation = 'time24h';
		} else {
			$time_validation = 'time12h';
		}

		// Backwards compatibility with old datepicker format.
		if ( 'mm/dd/yyyy' === $date_format ) {
			$date_format = 'm/d/Y';
		} elseif ( 'dd/mm/yyyy' === $date_format ) {
			$date_format = 'd/m/Y';
		} elseif ( 'mmmm d, yyyy' === $date_format ) {
			$date_format = 'F j, Y';
		}

		$default_date = array(
			'container' => array(
				'attr'  => array(),
				'class' => array(
					'wpforms-field-row-block',
					'wpforms-one-half',
					'wpforms-first',
				),
				'data'  => array(),
				'id'    => '',
			),
			'attr'      => array(
				'name'        => "wpforms[fields][{$field_id}][date]",
				'value'       => '',
				'placeholder' => $date_placeholder,
			),
			'sublabel'  => array(
				'hidden' => ! empty( $field['sublabel_hide'] ),
				'value'  => esc_html__( 'Date', 'wpforms' ),
			),
			'class'     => array(
				'wpforms-field-date-time-date',
				'wpforms-datepicker',
				! empty( $field_required ) ? 'wpforms-field-required' : '',
				! empty( wpforms()->process->errors[ $form_id ][ $field_id ]['date'] ) ? 'wpforms-error' : '',
			),
			'data'      => array(
				'date-format' => $date_format,
			),
			'id'        => "wpforms-{$form_id}-field_{$field_id}",
			'required'  => $field_required,
		);
		$default_time = array(
			'container' => array(
				'attr'  => array(),
				'class' => array(
					'wpforms-field-row-block',
					'wpforms-one-half',
				),
				'data'  => array(),
				'id'    => '',
			),
			'attr'      => array(
				'name'        => "wpforms[fields][{$field_id}][time]",
				'value'       => '',
				'placeholder' => $time_placeholder,
			),
			'sublabel'  => array(
				'hidden' => ! empty( $field['sublabel_hide'] ),
				'value'  => esc_html__( 'Time', 'wpforms' ),
			),
			'class'     => array(
				'wpforms-field-date-time-time',
				'wpforms-timepicker',
				! empty( $field_required ) ? 'wpforms-field-required' : '',
				! empty( wpforms()->process->errors[ $form_id ][ $field_id ]['time'] ) ? 'wpforms-error' : '',
			),
			'data'      => array(
				'rule-' . $time_validation => 'true',
				'time-format'              => $time_format,
				'step'                     => $time_interval,
			),
			'id'        => "wpforms-{$form_id}-field_{$field_id}-time",
			'required'  => $field_required,
		);

		switch ( $field_format ) {
			case 'date-time':
				$properties['input_container'] = array(
					'id'    => '',
					'class' => array(
						'wpforms-field-row',
						$field_size_cls,
					),
					'data'  => array(),
					'attr'  => array(),
				);

				$properties['inputs']['date'] = $default_date;

				$properties['inputs']['time'] = $default_time;
				break;

			case 'date':
				$properties['inputs']['date'] = $default_date;

				$properties['inputs']['date']['class'][] = $field_size_cls;

				break;

			case 'time':
				$properties['inputs']['time'] = $default_time;

				$properties['inputs']['time']['class'][] = $field_size_cls;
				break;
		}

		return $properties;
	}

	/**
	 * @inheritdoc
	 */
	protected function get_field_populated_single_property_value( $raw_value, $input, $properties, $field ) {

		$properties   = parent::get_field_populated_single_property_value( $raw_value, $input, $properties, $field );
		$date_type    = ! empty( $field['date_type'] ) ? $field['date_type'] : 'datepicker';
		$field_format = ! empty( $field['format'] ) ? $field['format'] : 'date-time';

		// Ordinary date/time fields, without dropdown, were already processed by this time.
		if (
			'time' === $field_format ||
			'dropdown' !== $date_type
		) {
			return $properties;
		}

		$subinput = explode( '_', $input );

		// Only date subfield supports this extra logic.
		if (
			empty( $subinput ) ||
			'date' !== $subinput[0] ||
			empty( $subinput[1] )
		) {
			return $properties;
		}

		$properties['inputs']['date']['default'][ sanitize_key( $subinput[1] ) ] = (int) $raw_value;

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
		 * Basic field options
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

		// Format option.
		$format        = ! empty( $field['format'] ) ? esc_attr( $field['format'] ) : 'date-time';
		$format_label  = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'format',
				'value'   => esc_html__( 'Format', 'wpforms' ),
				'tooltip' => esc_html__( 'Select format for the date field.', 'wpforms' ),
			),
			false
		);
		$format_select = $this->field_element(
			'select',
			$field,
			array(
				'slug'    => 'format',
				'value'   => $format,
				'options' => array(
					'date-time' => esc_html__( 'Date and Time', 'wpforms' ),
					'date'      => esc_html__( 'Date', 'wpforms' ),
					'time'      => esc_html__( 'Time', 'wpforms' ),
				),
			),
			false
		);
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'format',
				'content' => $format_label . $format_select,
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
		 * Advanced field options
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

		// Custom options.
		echo '<div class="format-selected-' . $format . ' format-selected">';

			// Date.
			$date_placeholder = ! empty( $field['date_placeholder'] ) ? $field['date_placeholder'] : '';
			$date_format      = ! empty( $field['date_format'] ) ? esc_attr( $field['date_format'] ) : 'm/d/Y';
			$date_type        = ! empty( $field['date_type'] ) ? esc_attr( $field['date_type'] ) : 'datepicker';
			// Backwards compatibility with old datepicker format.
			if ( 'mm/dd/yyyy' === $date_format ) {
				$date_format = 'm/d/Y';
			} elseif ( 'dd/mm/yyyy' === $date_format ) {
				$date_format = 'd/m/Y';
			} elseif ( 'mmmm d, yyyy' === $date_format ) {
				$date_format = 'F j, Y';
			}
			$date_formats = apply_filters(
				'wpforms_datetime_date_formats',
				array(
					'm/d/Y'  => 'm/d/Y',
					'd/m/Y'  => 'd/m/Y',
					'F j, Y' => 'F j, Y',
				)
			);
			printf(
				'<div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-date" id="wpforms-field-option-row-%d-date" data-subfield="date" data-field-id="%d">',
				esc_attr( $field['id'] ),
				esc_attr( $field['id'] )
			);
			$this->field_element(
				'label',
				$field,

				array(
					'slug'    => 'date_placeholder',
					'value'   => esc_html__( 'Date', 'wpforms' ),
					'tooltip' => esc_html__( 'Advanced date options.', 'wpforms' ),
				)
			);
			echo '<div class="placeholder">';
				printf(
					'<input type="text" class="placeholder" id="wpforms-field-option-%d-date_placeholder" name="fields[%d][date_placeholder]" value="%s">',
					esc_attr( $field['id'] ),
					esc_attr( $field['id'] ),
					esc_attr( $date_placeholder )
				);
				printf(
					'<label for="wpforms-field-option-%d-date_placeholder" class="sub-label">%s</label>',
					esc_attr( $field['id'] ),
					esc_html__( 'Placeholder', 'wpforms' )
				);
			echo '</div>';
			echo '<div class="format">';
				printf(
					'<select id="wpforms-field-option-%d-date_format" name="fields[%d][date_format]">',
					esc_attr( $field['id'] ),
					esc_attr( $field['id'] )
				);
				foreach ( $date_formats as $key => $value ) {
					if ( in_array( $key, array( 'm/d/Y', 'd/m/Y' ), true ) ) {
						printf(
							'<option value="%s" %s>%s (%s)</option>',
							$key,
							selected( $date_format, $key, false ),
							date( $value ),
							$key
						);
					} else {
						printf(
							'<option value="%s" class="datepicker-only" %s>%s</option>',
							$key,
							selected( $date_format, $key, false ),
							date( $value )
						);
					}
				}
				echo '</select>';
				printf(
					'<label for="wpforms-field-option-%d-date_format" class="sub-label">%s</label>',
					esc_attr( $field['id'] ),
					esc_html__( 'Format', 'wpforms' )
				);
			echo '</div>';
			echo '<div class="type">';
				printf(
					'<select id="wpforms-field-option-%d-date_type" name="fields[%d][date_type]">',
					esc_attr( $field['id'] ),
					esc_attr( $field['id'] )
				);
					printf(
						'<option value="datepicker" %s>%s</option>',
						selected( $date_type, 'datepicker', false ),
						esc_html__( 'Date Picker', 'wpforms' )
					);
					printf(
						'<option value="dropdown" %s>%s</option>',
						selected( $date_type, 'dropdown', false ),
						esc_html__( 'Date Dropdown', 'wpforms' )
					);
				echo '</select>';
				printf(
					'<label for="wpforms-field-option-%d-date_type" class="sub-label">%s</label>',
					esc_attr( $field['id'] ),
					esc_html__( 'Type', 'wpforms' )
				);
			echo '</div>';
		echo '</div>';

		// Time.
		$time_placeholder = ! empty( $field['time_placeholder'] ) ? $field['time_placeholder'] : '';
		$time_format      = ! empty( $field['time_format'] ) ? esc_attr( $field['time_format'] ) : 'g:i A';
		$time_formats     = array(
			'g:i A' => '12 H',
			'H:i'   => '24 H',
		);
		$time_interval    = ! empty( $field['time_interval'] ) ? esc_attr( $field['time_interval'] ) : '30';
		$time_intervals   = array(
			'15' => esc_html__( '15 minutes', 'wpforms' ),
			'30' => esc_html__( '30 minutes', 'wpforms' ),
			'60' => esc_html__( '1 hour', 'wpforms' ),
		);
		printf(
			'<div class="wpforms-clear wpforms-field-option-row wpforms-field-option-row-time" id="wpforms-field-option-row-%d-time" data-subfield="time" data-field-id="%d">',
			esc_attr( $field['id'] ),
			esc_attr( $field['id'] )
		);
			$this->field_element(
				'label',
				$field,
				array(
					'slug'    => 'time_placeholder',
					'value'   => esc_html__( 'Time', 'wpforms' ),
					'tooltip' => esc_html__( 'Advanced time options.', 'wpforms' ),
				)
			);
			echo '<div class="placeholder">';
				printf(
					'<input type="text"" class="placeholder" id="wpforms-field-option-%d-time_placeholder" name="fields[%d][time_placeholder]" value="%s">',
					esc_attr( $field['id'] ),
					esc_attr( $field['id'] ),
					esc_attr( $time_placeholder )
				);
				printf(
					'<label for="wpforms-field-option-%d-time_placeholder" class="sub-label">%s</label>',
					esc_attr( $field['id'] ),
					esc_html__( 'Placeholder', 'wpforms' )
				);
			echo '</div>';
			echo '<div class="format">';
					printf(
						'<select id="wpforms-field-option-%d-time_format" name="fields[%d][time_format]">',
						esc_attr( $field['id'] ),
						esc_attr( $field['id'] )
					);
						foreach ( $time_formats as $key => $value ) {
							printf(
								'<option value="%s" %s>%s</option>',
								$key,
								selected( $time_format, $key, false ),
								$value
							);
						}
					echo '</select>';
				printf(
					'<label for="wpforms-field-option-%d-time_format" class="sub-label">%s</label>',
					esc_attr( $field['id'] ),
					esc_html__( 'Format', 'wpforms' )
				);
			echo '</div>';
			echo '<div class="interval">';
				printf(
					'<select id="wpforms-field-option-%d-time_interval" name="fields[%d][time_interval]">',
					esc_attr( $field['id'] ),
					esc_attr( $field['id'] )
				);
				foreach ( $time_intervals as $key => $value ) {
					printf(
						'<option value="%s" %s>%s</option>',
						$key,
						selected( $time_interval, $key, false ),
						$value
					);
				}
				echo '</select>';
				printf(
					'<label for="wpforms-field-option-%d-time_interval" class="sub-label">%s</label>',
					esc_attr( $field['id'] ),
					esc_html__( 'Interval', 'wpforms' )
				);
			echo '</div>';
		echo '</div>';

		echo '</div>';

		// Hide label.
		$this->field_option( 'label_hide', $field );

		// Hide sub-labels.
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
	 * Add class to field options wrapper to indicate if field confirmation is enabled.
	 *
	 * @since 1.3.0
	 *
	 * @param string $class
	 * @param array  $field
	 *
	 * @return string
	 */
	public function field_option_class( $class, $field ) {

		if ( 'date-time' === $field['type'] ) {

			$date_type = ! empty( $field['date_type'] ) ? sanitize_html_class( $field['date_type'] ) : 'datepicker';
			$class     = "wpforms-date-type-$date_type";
		}

		return $class;
	}

	/**
	 * Field preview inside the builder.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field Field data and settings.
	 */
	public function field_preview( $field ) {

		$date_placeholder = ! empty( $field['date_placeholder'] ) ? esc_attr( $field['date_placeholder'] ) : '';
		$time_placeholder = ! empty( $field['time_placeholder'] ) ? esc_attr( $field['time_placeholder'] ) : '';
		$format           = ! empty( $field['format'] ) ? esc_attr( $field['format'] ) : 'date-time';
		$date_type        = ! empty( $field['date_type'] ) ? esc_attr( $field['date_type'] ) : 'datepicker';
		$date_format      = ! empty( $field['date_format'] ) ? esc_attr( $field['date_format'] ) : 'm/d/Y';

		if ( 'mm/dd/yyyy' === $date_format || 'm/d/Y' === $date_format ) {
			$date_first_select  = 'MM';
			$date_second_select = 'DD';
		} else {
			$date_first_select  = 'DD';
			$date_second_select = 'MM';
		}

		// Label.
		$this->field_preview_option( 'label', $field );

		echo '<div class="format-selected-' . $format . ' format-selected">';

			// Date.
			printf( '<div class="wpforms-date wpforms-date-type-%s">', $date_type );
				echo '<div class="wpforms-date-datepicker">';
					printf( '<input type="text" placeholder="%s" class="primary-input" disabled>', $date_placeholder );
					printf( '<label class="wpforms-sub-label">%s</label>', esc_html__( 'Date', 'wpforms' ) );
				echo '</div>';
				echo '<div class="wpforms-date-dropdown">';
					printf( '<select disabled class="first"><option>%s</option></select>', $date_first_select );
					echo '<span>/</span>';
					printf( '<select disabled class="second"><option>%s</option></select>', $date_second_select );
					echo '<span>/</span>';
					echo '<select disabled><option>YYYY</option></select>';
					printf( '<label class="wpforms-sub-label">%s</label>', esc_html__( 'Date', 'wpforms' ) );
				echo '</div>';
			echo '</div>';

			// Time.
			echo '<div class="wpforms-time">';
				printf( '<input type="text" placeholder="%s" class="primary-input" disabled>', $time_placeholder );
				printf( '<label class="wpforms-sub-label">%s</label>', esc_html__( 'Time', 'wpforms' ) );
			echo '</div>';
		echo '</div>';

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Converted to a new format, where all the data are taken not from $deprecated, but field properties.
	 *
	 * @param array $field      Field data and settings.
	 * @param array $deprecated Deprecated array of field attributes.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		$form_id    = $form_data['id'];
		$properties = $field['properties'];
		$container  = isset( $properties['input_container'] ) ? $properties['input_container'] : array();
		$date_prop  = isset( $field['properties']['inputs']['date'] ) ? $field['properties']['inputs']['date'] : array();
		$time_prop  = isset( $field['properties']['inputs']['time'] ) ? $field['properties']['inputs']['time'] : array();

		$field_required = ! empty( $field['required'] ) ? ' required' : '';
		$field_format   = ! empty( $field['format'] ) ? $field['format'] : 'date-time';

		$date_format = ! empty( $field['date_format'] ) ? $field['date_format'] : 'm/d/Y';
		$date_type   = ! empty( $field['date_type'] ) ? esc_attr( $field['date_type'] ) : 'datepicker';

		switch ( $field_format ) {
			case 'date-time':
				printf(
					'<div %s>',
					wpforms_html_attributes( $container['id'], $container['class'], $container['data'], $container['attr'] )
				);

				printf(
					'<div %s>',
					wpforms_html_attributes( $date_prop['container']['id'], $date_prop['container']['class'], $date_prop['container']['data'], $date_prop['container']['attr'] )
				);

				$this->field_display_sublabel( 'date', 'before', $field );

				if ( 'dropdown' === $date_type ) {

					$this->field_display_date_dropdowns( $date_format, $field, $field_required, $form_id );

				} else {

					printf(
						'<input type="text" %s %s>',
						wpforms_html_attributes( $date_prop['id'], $date_prop['class'], $date_prop['data'], $date_prop['attr'] ),
						$date_prop['required']
					);
				}


				$this->field_display_error( 'date', $field );
				$this->field_display_sublabel( 'date', 'after', $field );

				echo '</div>';

				printf(
					'<div %s>',
					wpforms_html_attributes( $time_prop['container']['id'], $time_prop['container']['class'], $time_prop['container']['data'], $time_prop['container']['attr'] )
				);

				$this->field_display_sublabel( 'time', 'before', $field );

				printf(
					'<input type="text" %s %s>',
					wpforms_html_attributes( $time_prop['id'], $time_prop['class'], $time_prop['data'], $time_prop['attr'] ),
					$time_prop['required']
				);

				$this->field_display_error( 'time', $field );
				$this->field_display_sublabel( 'time', 'after', $field );

				echo '</div>';

				echo '</div>';
				break;

			case 'date':
				if ( 'dropdown' === $date_type ) {

					$this->field_display_date_dropdowns( $date_format, $field, $field_required, $form_id );

				} else {

					printf(
						'<input type="text" %s %s>',
						wpforms_html_attributes( $date_prop['id'], $date_prop['class'], $date_prop['data'], $date_prop['attr'] ),
						$date_prop['required']
					);
				}
				break;

			case 'time':
			default:
				printf(
					'<input type="text" %s %s>',
					wpforms_html_attributes( $time_prop['id'], $time_prop['class'], $time_prop['data'], $time_prop['attr'] ),
					$time_prop['required']
				);
				break;
		}
	}

	/**
	 * Display the date field using dropdowns.
	 *
	 * @since 1.3.0
	 *
	 * @param string $format         Field format.
	 * @param array  $field          Field data and settings.
	 * @param string $field_required Is this field required or not, has a HTML attribute or empty.
	 * @param int    $form_id        Form ID.
	 */
	public function field_display_date_dropdowns( $format, $field, $field_required, $form_id ) {

		$format = ! empty( $format ) ? esc_attr( $format ) : 'm/d/Y';
		// Backwards compatibility with old datepicker format.
		if ( 'mm/dd/yyyy' === $format ) {
			$format = 'm/d/Y';
		} elseif ( 'dd/mm/yyyy' === $format ) {
			$format = 'd/m/Y';
		} elseif ( 'mmmm d, yyyy' === $format ) {
			$format = 'F j, Y';
		}

		$defaults  = ! empty( $field['properties']['inputs']['date']['default'] ) && is_array( $field['properties']['inputs']['date']['default'] ) ? $field['properties']['inputs']['date']['default'] : array();
		$current_d = ! empty( $defaults['d'] ) ? (int) $defaults['d'] : 0;
		$current_m = ! empty( $defaults['m'] ) ? (int) $defaults['m'] : 0;
		$current_y = ! empty( $defaults['y'] ) ? (int) $defaults['y'] : 0;

		$ranges = apply_filters(
			'wpforms_datetime_date_dropdowns',
			array(
				'months'       => range( 1, 12 ),
				'days'         => range( 1, 31 ),
				'years'        => range( date( 'Y' ), 1920 ),
				'months_label' => esc_html__( 'MM', 'wpforms' ),
				'days_label'   => esc_html__( 'DD', 'wpforms' ),
				'years_label'  => esc_html__( 'YYYY', 'wpforms' ),
			),
			$form_id,
			$field
		);

		if ( 'm/d/Y' === $format ) {

			// Month.
			$month_class  = 'wpforms-field-date-time-date-month';
			$month_class .= ! empty( $field_required ) ? ' wpforms-field-required' : '';
			$month_class .= ! empty( wpforms()->process->errors[ $form_id ][ $field['id'] ]['date'] ) ? ' wpforms-error' : '';

			printf(
				'<select name="wpforms[fields][%d][date][m]" id="%s" class="%s" %s>',
				esc_attr( $field['id'] ),
				esc_attr( "wpforms-field_{$field['id']}-month" ),
				esc_attr( $month_class ),
				$field_required
			);
				echo '<option class="placeholder" selected disabled>' . esc_html( $ranges['months_label'] ) . '</option>';
				foreach ( $ranges['months'] as $month ) {
					$month = (int) $month;
					printf(
						'<option value="%d" %s>%d</option>',
						$month,
						selected( $month, $current_m, false ),
						$month
					);
				}
			echo '</select>';

			echo '<span class="wpforms-field-date-time-date-sep">/</span>';

			// Day.
			$day_class  = 'wpforms-field-date-time-date-day';
			$day_class .= ! empty( $field_required ) ? ' wpforms-field-required' : '';
			$day_class .= ! empty( wpforms()->process->errors[ $form_id ][ $field['id'] ]['date'] ) ? ' wpforms-error' : '';
			printf(
				'<select name="wpforms[fields][%d][date][d]" id="%s" class="%s" %s>',
				(int) $field['id'],
				esc_attr( "wpforms-field_{$field['id']}-day" ),
				esc_attr( $day_class ),
				$field_required
			);
			echo '<option class="placeholder" selected disabled>' . esc_html( $ranges['days_label'] ) . '</option>';
			foreach ( $ranges['days'] as $day ) {
				$day = (int) $day;
				printf(
					'<option value="%d" %s>%d</option>',
					$day,
					selected( $day, $current_d, false ),
					$day
				);
			}
			echo '</select>';

		} else {

			// Day.
			$day_class  = 'wpforms-field-date-time-date-day';
			$day_class .= ! empty( $field_required ) ? ' wpforms-field-required' : '';
			$day_class .= ! empty( wpforms()->process->errors[ $form_id ][ $field['id'] ]['date'] ) ? ' wpforms-error' : '';
			printf(
				'<select name="wpforms[fields][%d][date][d]" id="%s" class="%s" %s>',
				(int) $field['id'],
				esc_attr( "wpforms-field_{$field['id']}-day" ),
				esc_attr( $day_class ),
				$field_required
			);
			echo '<option class="placeholder" selected disabled>' . esc_html( $ranges['days_label'] ) . '</option>';
			foreach ( $ranges['days'] as $day ) {
				$day = (int) $day;
				printf(
					'<option value="%d" %s>%d</option>',
					$day,
					selected( $day, $current_d, false ),
					$day
				);
			}
			echo '</select>';

			echo '<span class="wpforms-field-date-time-date-sep">/</span>';

			// Month.
			$month_class  = 'wpforms-field-date-time-date-month';
			$month_class .= ! empty( $field_required ) ? ' wpforms-field-required' : '';
			$month_class .= ! empty( wpforms()->process->errors[ $form_id ][ $field['id'] ]['date'] ) ? ' wpforms-error' : '';
			printf(
				'<select name="wpforms[fields][%d][date][m]" id="%s" class="%s" %s>',
				(int) $field['id'],
				esc_attr( "wpforms-field_{$field['id']}-month" ),
				esc_attr( $month_class ),
				$field_required
			);
			echo '<option class="placeholder" selected disabled>' . esc_html( $ranges['months_label'] ) . '</option>';
			foreach ( $ranges['months'] as $month ) {
				$month = (int) $month;
				printf(
					'<option value="%d" %s>%d</option>',
					$month,
					selected( $month, $current_m, false ),
					$month
				);
			}
			echo '</select>';
		}

		echo '<span class="wpforms-field-date-time-date-sep">/</span>';

		// Year.
		$year_class  = 'wpforms-field-date-time-date-year';
		$year_class .= ! empty( $field_required ) ? ' wpforms-field-required' : '';
		$year_class .= ! empty( wpforms()->process->errors[ $form_id ][ $field['id'] ]['date'] ) ? ' wpforms-error' : '';
		printf(
			'<select name="wpforms[fields][%d][date][y]" id="%s" class="%s" %s>',
			(int) $field['id'],
			esc_attr( "wpforms-field_{$field['id']}-year" ),
			esc_attr( $year_class ),
			$field_required
		);
		echo '<option class="placeholder" selected disabled>' . esc_html( $ranges['years_label'] ) . '</option>';
		foreach ( $ranges['years'] as $year ) {
			$year = (int) $year;
			printf(
				'<option value="%d" %s>%d</option>',
				$year,
				selected( $year, $current_y, false ),
				$year
			);
		}
		echo '</select>';
	}

	/**
	 * Validates field on form submit.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Submitted field value.
	 * @param array $form_data    Form data and settings.
	 */
	public function validate( $field_id, $field_submit, $form_data ) {

		// Extended validation needed for the different address fields.
		if ( ! empty( $form_data['fields'][ $field_id ]['required'] ) ) {

			$form_id  = $form_data['id'];
			$format   = $form_data['fields'][ $field_id ]['format'];
			$required = wpforms_get_required_label();

			if (
				! empty( $form_data['fields'][ $field_id ]['date_type'] ) &&
				'dropdown' === $form_data['fields'][ $field_id ]['date_type']
			) {
				if (
					( 'date' === $format || 'date-time' === $format ) &&
					( empty( $field_submit['date']['m'] ) || empty( $field_submit['date']['d'] ) || empty( $field_submit['date']['y'] ) )
				) {
					wpforms()->process->errors[ $form_id ][ $field_id ]['date'] = $required;
				}
			} else {
				if (
					( 'date' === $format || 'date-time' === $format ) &&
					empty( $field_submit['date'] )
				) {
					wpforms()->process->errors[ $form_id ][ $field_id ]['date'] = $required;
				}
			}

			if (
				( 'time' === $format || 'date-time' === $format ) &&
				empty( $field_submit['time'] )
			) {
				wpforms()->process->errors[ $form_id ][ $field_id ]['time'] = $required;
			}
		}
	}

	/**
	 * Formats field.
	 *
	 * @since 1.0.0
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Submitted field value.
	 * @param array $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		$name        = ! empty( $form_data['fields'][ $field_id ]['label'] ) ? $form_data['fields'][ $field_id ]['label'] : '';
		$format      = $form_data['fields'][ $field_id ]['format'];
		$date_format = $form_data['fields'][ $field_id ]['date_format'];
		$time_format = $form_data['fields'][ $field_id ]['time_format'];
		$value       = '';
		$date        = '';
		$time        = '';
		$unix        = '';

		if ( ! empty( $field_submit['date'] ) ) {
			if ( is_array( $field_submit['date'] ) ) {

				if (
					! empty( $field_submit['date']['m'] ) &&
					! empty( $field_submit['date']['d'] ) &&
					! empty( $field_submit['date']['y'] )
				) {
					if (
						'dd/mm/yyyy' === $date_format ||
						'd/m/Y' === $date_format
					) {
						$date = $field_submit['date']['d'] . '/' . $field_submit['date']['m'] . '/' . $field_submit['date']['y'];
					} else {
						$date = $field_submit['date']['m'] . '/' . $field_submit['date']['d'] . '/' . $field_submit['date']['y'];
					}
				} else {
					// So we are missing some of the values.
					// We can't process date further, as we won't be able to retrieve its unix time.
					wpforms()->process->fields[ $field_id ] = array(
						'name'  => sanitize_text_field( $name ),
						'value' => sanitize_text_field( $value ),
						'id'    => absint( $field_id ),
						'type'  => $this->type,
						'date'  => '',
						'time'  => '',
						'unix'  => false,
					);

					return;
				}
			} else {
				$date = $field_submit['date'];
			}
		}

		if ( ! empty( $field_submit['time'] ) ) {
			$time = $field_submit['time'];
		}

		if ( 'date-time' === $format && ! empty( $field_submit ) ) {
			$value = trim( "$date $time" );
		} elseif ( 'date' === $format ) {
			$value = $date;
		} elseif ( 'time' === $format ) {
			$value = $time;
		}

		// Always store the raw time in 12H format.
		if ( ( 'H:i A' === $time_format || 'H:i' === $time_format ) && ! empty( $time ) ) {
			$time = date( 'g:i A', strtotime( $time ) );
		}

		// Always store the date in m/d/Y format so it is strtotime() compatible.
		if (
			( 'dd/mm/yyyy' === $date_format || 'd/m/Y' === $date_format ) &&
			! empty( $date )
		) {
			list( $d, $m, $y ) = explode( '/', $date );

			$date = "$m/$d/$y";
		}

		// Calculate unix time if we have a date.
		if ( ! empty( $date ) ) {
			$unix = strtotime( trim( "$date $time" ) );
		}

		wpforms()->process->fields[ $field_id ] = array(
			'name'  => sanitize_text_field( $name ),
			'value' => sanitize_text_field( $value ),
			'id'    => absint( $field_id ),
			'type'  => $this->type,
			'date'  => sanitize_text_field( $date ),
			'time'  => sanitize_text_field( $time ),
			'unix'  => $unix,
		);
	}
}

new WPForms_Field_Date_Time();
