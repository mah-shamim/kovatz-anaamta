<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

abstract class Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	abstract public function get_id();

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	abstract public function get_name();

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	abstract public function check( $args = array() );

	/**
	 * Returns group for current operator
	 *
	 * @return [type] [description]
	 */
	public function get_group() {
		return false;
	}

	/**
	 * Check if is condition available for meta fields control
	 *
	 * @return boolean [description]
	 */
	public function is_for_fields() {
		return true;
	}

	/**
	 * Check if is condition available for meta value control
	 *
	 * @return boolean [description]
	 */
	public function need_value_detect() {
		return true;
	}

	/**
	 * This condition is required data type detection
	 *
	 * @return boolean [description]
	 */
	public function need_type_detect() {
		return false;
	}

	/**
	 * Returns current field value by arguments
	 *
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function get_current_value( $args = array() ) {

		$current_value = null;

		if ( ! empty( $args['field_raw'] ) ) {

			$context = ! empty( $args['context'] ) ? $args['context'] : 'default';

			if ( 'current_listing' === $context ) {

				$object_id = jet_engine()->listings->data->get_current_object_id();
				$object    = jet_engine()->listings->data->get_current_object();

				if ( $object && is_object( $object ) ) {
					switch ( get_class( $object ) ) {

						case 'WP_Comment':
							$current_value = get_comment_meta( $object_id, $args['field_raw'], true );
							break;

						case 'WP_Term':
							$current_value = get_term_meta( $object_id, $args['field_raw'], true );
							break;

						case 'WP_User':
							$current_value = get_user_meta( $object_id, $args['field_raw'], true );
							break;

						default:
							$current_value = get_post_meta( $object_id, $args['field_raw'], true );
							break;

					}

				}

			} else {
				$current_value = get_post_meta( get_the_ID(), $args['field_raw'], true );
			}

		} else {
			$current_value = jet_engine()->listings->macros->do_macros( $args['field'] );
		}

		return $current_value;

	}

	/**
	 * Convert Engine checkboxes values to plain array
	 */
	public function checkboxes_to_array( $array = array() ) {

		$result = array();

		foreach ( $array as $value => $bool ) {

			$bool = filter_var( $bool, FILTER_VALIDATE_BOOLEAN );

			if ( $bool ) {
				$result[] = $value;
			}
		}

		return $result;

	}

	/**
	 * Ad
	 * @param  [type] $current_value    [description]
	 * @param  [type] $value_to_compare [description]
	 * @param  [type] $data_type        [description]
	 * @return [type]                   [description]
	 */
	public function adjust_values_type( $current_value, $value_to_compare, $data_type ) {

		switch ( $data_type ) {
			case 'numeric':
				$current_value    = intval( $current_value );
				$value_to_compare = intval( $value_to_compare );
				break;

			case 'datetime':
			case 'date':

				if ( ! \Jet_Engine_Tools::is_valid_timestamp( $current_value ) ) {
					$current_value = strtotime( $current_value );
				}

				if ( ! \Jet_Engine_Tools::is_valid_timestamp( $value_to_compare ) ) {
					$value_to_compare = strtotime( $value_to_compare );
				}

				break;

			default:
				$current_value    = strval( $current_value );
				$value_to_compare = strval( $value_to_compare );
				break;
		}

		return array(
			'current' => $current_value,
			'compare' => $value_to_compare,
		);

	}

	/**
	 * Explode value string
	 *
	 * @return [type] [description]
	 */
	public function explode_string( $value = null ) {

		if ( \Jet_Engine_Tools::is_empty( $value ) ) {
			return array();
		}

		$value = explode( ',', $value );
		$value = array_map( 'trim', $value );

		return $value;

	}

	/**
	 * Returns condition specific repeater controls
	 */
	public function get_custom_controls() {
		return false;
	}

}
