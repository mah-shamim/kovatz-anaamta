<?php

namespace Jet_Engine\Bricks_Views\Helpers;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Options_Converter {

	public static function convert( $args = [] ) {

		switch ( $args['type'] ) {
			case 'text':
			case 'textarea':
				$control = new Controls_Converter\Control_Text();
				break;
			case 'jet-query':
			case 'select':

				if ( ! empty( $args['groups'] ) ) {
					$args['options'] = self::convert_select_groups_to_options( $args['groups'] );
				}

				$control = new Controls_Converter\Control_Select();
				break;
			case 'switcher':
				$control = new Controls_Converter\Control_Checkbox();
				break;
			case 'repeater':
				$control = new Controls_Converter\Control_Repeater();
				break;
			case 'icons':
			case 'icon':
				$control = new Controls_Converter\Control_Icon();
				break;
			default:
				$control = new Controls_Converter\Control_Default();
		}

		return $control->parse_callback_arguments( $args );
	}

	public static function convert_select_groups_to_options( $groups, $index = null ) {

		if ( ! is_array( $groups ) ) {
			return false;
		}

		$result = array();

		foreach ( $groups as $key => $value ) {

			if ( $key === '' ) {
				continue;
			}

			if ( is_array( $value ) ) {
				$result = array_merge( $result, self::convert_select_groups_to_options( $value, is_numeric( $key ) ? $key : null ) );
				continue;
			}

			if ( $key === 'label' && is_numeric( $index ) ) {
				$result[ 'GroupTitle' . $index ] = $value;
				continue;
			}

			$result[ $key ] = $value;
		}

		return $result;
	}

	public static function filters_options_by_key( $options, $allowed ) {

		/*if ( array_key_exists( '', $options ) ) {
			unset( $options[''] );
		}*/

		return array_filter(
			$options,
			fn( $val, $key ) => isset($allowed[$key]) && $allowed[$key] === true,
			ARRAY_FILTER_USE_BOTH
		);
	}

	public static function changes_empty_key_in_options( $options ) {

		if ( array_key_exists( '', $options ) ) {
			$value = $options[''];
			unset( $options[''] );
			$new_key = strtolower($value);
			$new_key = str_replace(' ', '_', $new_key);

			return array_merge(
				[$new_key => $value],
				$options
			);
		}

		return $options;
	}

}
