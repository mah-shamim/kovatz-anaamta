<?php

/**
 * Rating field.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.4.4
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2018, WPForms LLC
 */
class WPForms_Rating_Text extends WPForms_Field {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.4.4
	 */
	public function init() {

		// Define field type information.
		$this->name  = esc_html__( 'Rating', 'wpforms' );
		$this->type  = 'rating';
		$this->icon  = 'fa-star';
		$this->order = 210;
		$this->group = 'fancy';

		// Define additional field properties.
		add_filter( 'wpforms_field_properties_rating', array( $this, 'field_properties' ), 5, 3 );

		// Customize value format for HTML emails.
		add_filter( 'wpforms_html_field_value', array( $this, 'html_email_value' ), 10, 4 );
	}

	/**
	 * Define additional field properties.
	 *
	 * @since 1.4.4
	 *
	 * @param array $properties Field properties.
	 * @param array $field      Field settings.
	 * @param array $form_data  Form data and settings.
	 *
	 * @return array
	 */
	public function field_properties( $properties, $field, $form_data ) {

		// Primary input: set screen reader text class.
		$properties['inputs']['primary']['class'][] = 'wpforms-screen-reader-element';

		// Rating scale.
		$properties['inputs']['primary']['rating']['scale'] = ! empty( $field['scale'] ) ? esc_attr( $field['scale'] ) : 5;

		// Rating icon color.
		$properties['inputs']['primary']['rating']['color'] = ! empty( $field['icon_color'] ) ? esc_attr( $field['icon_color'] ) : '#e27730';

		// Rating icons size.
		$properties['inputs']['primary']['rating']['size'] = '28'; // Default size.

		if ( ! empty( $field['icon_size'] ) && 'small' === $field['icon_size'] ) {
			$properties['inputs']['primary']['rating']['size'] = '18';
		} elseif ( ! empty( $field['icon_size'] ) && 'large' === $field['icon_size'] ) {
			$properties['inputs']['primary']['rating']['size'] = '38';
		}

		// Rating icon SVG image.
		$properties['inputs']['primary']['rating']['svg'] = '<svg width="" height="" style="" fill="" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z"/></svg>';

		if ( ! empty( $field['icon'] ) && 'heart' === $field['icon'] ) {
			$properties['inputs']['primary']['rating']['svg'] = '<svg width="" height="" style="" fill="" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M896 1664q-26 0-44-18l-624-602q-10-8-27.5-26t-55.5-65.5-68-97.5-53.5-121-23.5-138q0-220 127-344t351-124q62 0 126.5 21.5t120 58 95.5 68.5 76 68q36-36 76-68t95.5-68.5 120-58 126.5-21.5q224 0 351 124t127 344q0 221-229 450l-623 600q-18 18-44 18z"/></svg>';
		} elseif ( ! empty( $field['icon'] ) && 'thumb' === $field['icon'] ) {
			$properties['inputs']['primary']['rating']['svg'] = '<svg width="" height="" style="" fill="" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M320 1344q0-26-19-45t-45-19q-27 0-45.5 19t-18.5 45q0 27 18.5 45.5t45.5 18.5q26 0 45-18.5t19-45.5zm160-512v640q0 26-19 45t-45 19h-288q-26 0-45-19t-19-45v-640q0-26 19-45t45-19h288q26 0 45 19t19 45zm1184 0q0 86-55 149 15 44 15 76 3 76-43 137 17 56 0 117-15 57-54 94 9 112-49 181-64 76-197 78h-129q-66 0-144-15.5t-121.5-29-120.5-39.5q-123-43-158-44-26-1-45-19.5t-19-44.5v-641q0-25 18-43.5t43-20.5q24-2 76-59t101-121q68-87 101-120 18-18 31-48t17.5-48.5 13.5-60.5q7-39 12.5-61t19.5-52 34-50q19-19 45-19 46 0 82.5 10.5t60 26 40 40.5 24 45 12 50 5 45 .5 39q0 38-9.5 76t-19 60-27.5 56q-3 6-10 18t-11 22-8 24h277q78 0 135 57t57 135z"/></svg>';
		} elseif ( ! empty( $field['icon'] ) && 'smiley' === $field['icon'] ) {
			$properties['inputs']['primary']['rating']['svg'] = '<svg width="" height="" style="" fill="" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path  d="M1262 1075q-37 121-138 195t-228 74-228-74-138-195q-8-25 4-48.5t38-31.5q25-8 48.5 4t31.5 38q25 80 92.5 129.5t151.5 49.5 151.5-49.5 92.5-129.5q8-26 32-38t49-4 37 31.5 4 48.5zm-494-435q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm512 0q0 53-37.5 90.5t-90.5 37.5-90.5-37.5-37.5-90.5 37.5-90.5 90.5-37.5 90.5 37.5 37.5 90.5zm256 256q0-130-51-248.5t-136.5-204-204-136.5-248.5-51-248.5 51-204 136.5-136.5 204-51 248.5 51 248.5 136.5 204 204 136.5 248.5 51 248.5-51 204-136.5 136.5-204 51-248.5zm128 0q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z"/></svg>';
		}

		return $properties;
	}

	/**
	 * Customize format for HTML email notifications and entry details.
	 *
	 * @since 1.4.4
	 *
	 * @param string $val       The value.
	 * @param array  $field     Field.
	 * @param array  $form_data Form data settings.
	 * @param string $context   Context usage.
	 *
	 * @return string
	 */
	public function html_email_value( $val, $field, $form_data = array(), $context = '' ) {

		if ( ! empty( $field['value'] ) && 'rating' === $field['type'] && apply_filters( 'wpforms_rating_field_emoji', true ) ) {

			// Determine emoji to use.
			switch ( $field['icon'] ) {
				case 'star':
					$emoji = '⭐';
					break;
				case 'heart':
					$emoji = '❤️';
					break;
				case 'thumb':
					$emoji = '👍';
					break;
				default:
					$emoji = '🙂';
					break;
			}

			if ( 'entry-table' === $context ) {
				// For the entry list table, lighten the scale display.
				return sprintf(
					'%s <span style="color:#ccc;">(%d/%d)</span>',
					str_repeat( $emoji, absint( $field['value'] ) ),
					absint( $field['value'] ),
					absint( $field['scale'] )
				);
			}

			return sprintf(
				'%s (%d/%d)',
				str_repeat( $emoji, absint( $field['value'] ) ),
				absint( $field['value'] ),
				absint( $field['scale'] )
			);
		}

		return $val;
	}

	/**
	 * Field options panel inside the builder.
	 *
	 * @since 1.4.4
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

		// Description.
		$this->field_option( 'description', $field );

		// Scale.
		$lbl = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'scale',
				'value'   => esc_html__( 'Scale', 'wpforms' ),
				'tooltip' => esc_html__( 'Select rating scale', 'wpforms' ),
			),
			false
		);
		$fld = $this->field_element(
			'select',
			$field,
			array(
				'slug'    => 'scale',
				'value'   => ! empty( $field['scale'] ) ? esc_attr( $field['scale'] ) : '5',
				'options' => array(
					'2'  => '2',
					'3'  => '3',
					'4'  => '4',
					'5'  => '5',
					'6'  => '6',
					'7'  => '7',
					'8'  => '8',
					'9'  => '9',
					'10' => '10',
				),
			),
			false
		);
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'scale',
				'content' => $lbl . $fld,
			)
		);

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

		// Icon.
		$lbl = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'icon',
				'value'   => esc_html__( 'Icon', 'wpforms' ),
				'tooltip' => esc_html__( 'Select icon to display', 'wpforms' ),
			),
			false
		);
		$fld = $this->field_element(
			'select',
			$field,
			array(
				'slug'    => 'icon',
				'value'   => ! empty( $field['icon'] ) ? esc_attr( $field['icon'] ) : 'star',
				'options' => array(
					'star'   => esc_html__( 'Star', 'wpforms' ),
					'heart'  => esc_html__( 'Heart', 'wpforms' ),
					'thumb'  => esc_html__( 'Thumb', 'wpforms' ),
					'smiley' => esc_html__( 'Smiley Face', 'wpforms' ),
				),
			),
			false
		);
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'icon',
				'content' => $lbl . $fld,
			)
		);

		// Icon size.
		$lbl = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'icon_size',
				'value'   => esc_html__( 'Icon Size', 'wpforms' ),
				'tooltip' => esc_html__( 'Select the size of the rating icon', 'wpforms' ),
			),
			false
		);
		$fld = $this->field_element(
			'select',
			$field,
			array(
				'slug'    => 'icon_size',
				'value'   => ! empty( $field['icon_size'] ) ? esc_attr( $field['icon_size'] ) : 'medium',
				'options' => array(
					'small'  => esc_html__( 'Small', 'wpforms' ),
					'medium' => esc_html__( 'Medium', 'wpforms' ),
					'large'  => esc_html__( 'Large', 'wpforms' ),
				),
			),
			false
		);
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'icon_size',
				'content' => $lbl . $fld,
			)
		);

		// Icon color picker.
		$lbl = $this->field_element(
			'label',
			$field,
			array(
				'slug'    => 'icon_color',
				'value'   => esc_html__( 'Icon Color', 'wpforms' ),
				'tooltip' => esc_html__( 'Select the color for the rating icon', 'wpforms' ),
			),
			false
		);
		$fld = $this->field_element(
			'text',
			$field,
			array(
				'slug'  => 'icon_color',
				'value' => ! empty( $field['icon_color'] ) ? esc_attr( $field['icon_color'] ) : '#e27730',
				'class' => 'wpforms-color-picker',
			),
			false
		);
		$this->field_element(
			'row',
			$field,
			array(
				'slug'    => 'icon_color',
				'content' => $lbl . $fld,
				'class'   => 'color-picker-row',
			)
		);

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
	 * @since 1.4.4
	 *
	 * @param array $field Field settings.
	 */
	public function field_preview( $field ) {

		// Define data.
		$scale         = ! empty( $field['scale'] ) ? esc_attr( $field['scale'] ) : 5;
		$icon          = ! empty( $field['icon'] ) ? esc_attr( $field['icon'] ) : 'star';
		$icon_size     = ! empty( $field['icon_size'] ) ? esc_attr( $field['icon_size'] ) : 'medium';
		$icon_color    = ! empty( $field['icon_color'] ) ? esc_attr( $field['icon_color'] ) : '#e27730';
		$icon_class    = '';
		$icon_size_css = '';

		// Set icon class.
		switch ( $icon ) {
			case 'star':
				$icon_class = 'fa-star';
				break;
			case 'heart':
				$icon_class = 'fa-heart';
				break;
			case 'thumb':
				$icon_class = 'fa-thumbs-up';
				break;
			case 'smiley':
				$icon_class = 'fa-smile-o';
				break;
		}

		// Set icon size.
		switch ( $icon_size ) {
			case 'small':
				$icon_size_css = '18';
				break;
			case 'medium':
				$icon_size_css = '28';
				break;
			case 'large':
				$icon_size_css = '38';
				break;
		}

		// Label.
		$this->field_preview_option( 'label', $field );

		// Primary input.
		for ( $i = 1; $i <= 10; $i++ ) {
			printf(
				'<i class="fa %s %s rating-icon" aria-hidden="true" style="margin-right:5px; color:%s; display:%s; font-size:%dpx;"></i>',
				$icon_class,
				$icon_size,
				$icon_color,
				$i <= $scale ? 'inline-block' : 'none',
				$icon_size_css
			);
		}

		// Description.
		$this->field_preview_option( 'description', $field );
	}

	/**
	 * Field display on the form front-end.
	 *
	 * @since 1.4.4
	 *
	 * @param array $field      Field settings.
	 * @param array $deprecated Deprecated, don't use.
	 * @param array $form_data  Form data and settings.
	 */
	public function field_display( $field, $deprecated, $form_data ) {

		// Define data.
		$primary = $field['properties']['inputs']['primary'];
		$rating  = $primary['rating'];
		$svg     = $rating['svg'];
		$scale   = ! empty( $rating['scale'] ) ? absint( $rating['scale'] ) : 5;

		// Apply our customizations to the SVG.
		$svg = str_replace( 'width=""', 'width="' . absint( $rating['size'] ) . '"', $svg );
		$svg = str_replace( 'height=""', 'height="' . absint( $rating['size'] ) . '"', $svg );
		$svg = str_replace( 'style=""', 'style="height:' . absint( $rating['size'] ) . 'px;width:' . absint( $rating['size'] ) . 'px;"', $svg );
		$svg = str_replace( 'fill=""', 'fill="currentColor" color="' . wpforms_sanitize_hex_color( $rating['color'] ) . '"', $svg );

		echo '<div class="wpforms-field-rating-items">';

		// Generate each rating icon/element.
		for ( $i = 1; $i <= $scale; $i++ ) {

			printf(
				'<label class="wpforms-field-rating-item choice-%d" for="wpforms-%d-field_%d_%d">',
				$i,
				absint( $form_data['id'] ),
				$field['id'],
				$i
			);

				// Hidden label for screen readers.
				echo '<span class="wpforms-screen-reader-element">';
					/* translators: %1$s - rating value; %2$s - rating scale. */
					printf( esc_html__( 'Rate %1$d out of %2$d', 'wpforms' ), $i, $scale );
				echo '</span>';

				// Primary field.
				$primary['id'] = sprintf(
					'wpforms-%d-field_%d_%d',
					absint( $form_data['id'] ),
					$field['id'],
					$i
				);

				$primary['attr']['value'] = $i;

				if ( ! empty( $rating['default'] ) && $i === $rating['default'] ) {
					$primary['attr']['checked'] = 'checked';
				} else {
					$primary['attr']['checked'] = '';
				}

				printf(
					'<input type="radio" %s %s>',
					wpforms_html_attributes( $primary['id'], $primary['class'], $primary['data'], $primary['attr'] ),
					$primary['required']
				);

				// SVG image.
				echo $svg;

			echo '</label>';
		}

		echo '</div>';
	}

	/**
	 * @inheritdoc
	 */
	protected function get_field_populated_single_property_value( $raw_value, $input, $properties, $field ) {

		if ( ! is_string( $raw_value ) ) {
			return $properties;
		}

		$properties['inputs'][ $input ]['rating']['default'] = (int) $raw_value;

		return $properties;
	}

	/**
	 * Formats field.
	 *
	 * @since 1.4.4
	 *
	 * @param int   $field_id     Field ID.
	 * @param array $field_submit Submitted field value.
	 * @param array $form_data    Form data and settings.
	 */
	public function format( $field_id, $field_submit, $form_data ) {

		// Define data.
		$name  = ! empty( $form_data['fields'][ $field_id ]['label'] ) ? $form_data['fields'][ $field_id ]['label'] : '';
		$value = ! empty( $field_submit ) ? absint( $field_submit ) : '';
		$scale = absint( $form_data['fields'][ $field_id ]['scale'] );

		if ( $value > $scale ) {
			$value = '';
		}

		// Set final field details.
		wpforms()->process->fields[ $field_id ] = array(
			'name'  => sanitize_text_field( $name ),
			'value' => sanitize_text_field( $value ),
			'id'    => absint( $field_id ),
			'type'  => $this->type,
			'scale' => sanitize_text_field( $scale ),
			'icon'  => sanitize_text_field( $form_data['fields'][ $field_id ]['icon'] ),
		);
	}
}

new WPForms_Rating_Text();
