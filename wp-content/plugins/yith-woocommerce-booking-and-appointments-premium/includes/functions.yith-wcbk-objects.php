<?php
/**
 * Objects Functions
 *
 * @author  YITH
 * @package YITH\Booking\Functions
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! function_exists( 'yith_wcbk_get_search_form' ) ) {
	/**
	 * Get the booking search form object.
	 *
	 * @param int $id The ID.
	 *
	 * @return YITH_WCBK_Search_Form|bool
	 * @uses   YITH_WCBK_Search_Form
	 */
	function yith_wcbk_get_search_form( $id ) {
		$_search_form = false;
		if ( is_numeric( $id ) ) {
			$_search_form = new YITH_WCBK_Search_Form( $id );
		}

		if ( ! $_search_form instanceof YITH_WCBK_Search_Form || ! $_search_form->is_valid() ) {
			$_search_form = false;
		}

		return apply_filters( 'yith_wcbk_get_search_form', $_search_form );
	}
}

if ( ! function_exists( 'yith_wcbk_product_extra_costs_array_reduce' ) ) {
	/**
	 * Extra costs reduce.
	 *
	 * @param array                        $result     The result array.
	 * @param YITH_WCBK_Product_Extra_Cost $extra_cost The extra cost.
	 *
	 * @return array
	 */
	function yith_wcbk_product_extra_costs_array_reduce( $result, $extra_cost ) {
		if ( $extra_cost->is_valid() ) {
			$result[ $extra_cost->get_identifier() ] = $extra_cost;
		}

		return $result;
	}
}


if ( ! function_exists( 'yith_wcbk_simple_object_to_array' ) ) {
	/**
	 * Simple object to array
	 *
	 * @param YITH_WCBK_Simple_Object|array $object Object.
	 *
	 * @return array
	 * @since 2.1
	 */
	function yith_wcbk_simple_object_to_array( $object ) {
		return $object instanceof YITH_WCBK_Simple_Object ? $object->to_array() : $object;
	}
}

if ( ! function_exists( 'yith_wcbk_simple_object_to_array_deep' ) ) {
	/**
	 * Simple object to array Deep
	 *
	 * @param YITH_WCBK_Simple_Object|array|mixed $object The object.
	 *
	 * @return array|mixed
	 * @since 3.0.0
	 */
	function yith_wcbk_simple_object_to_array_deep( $object ) {
		if ( is_a( $object, 'YITH_WCBK_Simple_Object' ) ) {
			$object = $object->to_array();
		} elseif ( is_array( $object ) ) {
			$object = array_map( 'yith_wcbk_simple_object_to_array_deep', $object );
		}

		return $object;
	}
}

if ( ! function_exists( 'yith_wcbk_simple_objects_to_array' ) ) {
	/**
	 * Simple object array to array of array
	 *
	 * @param YITH_WCBK_Simple_Object[] $objects The objects.
	 *
	 * @return array
	 * @since 2.1
	 */
	function yith_wcbk_simple_objects_to_array( $objects ) {
		return is_array( $objects ) ? array_map( 'yith_wcbk_simple_object_to_array', $objects ) : array();
	}
}

if ( ! function_exists( 'yith_wcbk_exclude_availability_rules_with_time' ) ) {
	/**
	 * Filter availabilities by day
	 *
	 * @param YITH_WCBK_Availability_Rule[] $rules Availability rules.
	 *
	 * @return YITH_WCBK_Availability_Rule[]
	 * @since 3.0.0
	 */
	function yith_wcbk_exclude_availability_rules_with_time( array $rules ): array {
		return array_filter(
			$rules,
			function ( $rule ) {
				$availabilities = $rule->get_availabilities();
				$has_time       = ! ! array_filter(
					$availabilities,
					function ( $availability ) {
						return ! $availability->is_full_day();
					}
				);

				return ! $has_time;
			}
		);
	}
}

if ( ! function_exists( 'yith_wcbk_unique_day_availabilities' ) ) {
	/**
	 * Filter availabilities with unique days and sort them.
	 *
	 * @param YITH_WCBK_Availability[] $availabilities Availabilities.
	 *
	 * @return YITH_WCBK_Availability[]
	 * @since 3.0.0
	 */
	function yith_wcbk_unique_day_availabilities( $availabilities ) {
		$days        = array_map(
			function ( $availability ) {
				return $availability->get_day();
			},
			$availabilities
		);
		$unique_days = array_unique( $days );

		$availabilities = array_values( array_intersect_key( $availabilities, $unique_days ) );
		uasort(
			$availabilities,
			function ( $a, $b ) {
				return $a->get_day() <=> $b->get_day();
			}
		);

		return $availabilities;
	}
}

if ( ! function_exists( 'yith_wcbk_remove_time_slots_from_availabilities' ) ) {
	/**
	 * Remove time-slots from availabilities.
	 *
	 * @param YITH_WCBK_Availability[] $availabilities Availabilities.
	 *
	 * @return YITH_WCBK_Availability[]
	 * @since 3.0.0
	 */
	function yith_wcbk_remove_time_slots_from_availabilities( $availabilities ) {
		return array_map(
			function ( $availability ) {
				$availability->set_time_slots( array() );

				return $availability;
			},
			$availabilities
		);
	}
}

if ( ! function_exists( 'yith_wcbk_validate_availabilities' ) ) {
	/**
	 * Validate availabilities.
	 *
	 * @param YITH_WCBK_Availability[] $availabilities Availabilities.
	 * @param array                    $args           Arguments.
	 *
	 * @return YITH_WCBK_Availability[]
	 * @since 3.0.0
	 */
	function yith_wcbk_validate_availabilities( $availabilities, $args ) {
		$defaults = array(
			'remove_time_slots'   => false,
			'force_first_all_day' => false,
		);
		$args     = wp_parse_args( $args, $defaults );

		$availabilities = yith_wcbk_unique_day_availabilities( $availabilities );
		if ( $args['remove_time_slots'] ) {
			$availabilities = yith_wcbk_remove_time_slots_from_availabilities( $availabilities );
		}

		if ( ! $availabilities ) {
			$all_day_availability = new YITH_WCBK_Availability();
			$all_day_availability->set_day( 'all' );
			$all_day_availability->set_bookable( true );
			$availabilities = array( $all_day_availability );
		}

		if ( ! ! $availabilities && $args['force_first_all_day'] ) {
			$first_availability = current( $availabilities );
			if ( ! $first_availability->is_all_days() ) {
				$all_day_availability = new YITH_WCBK_Availability();
				$all_day_availability->set_day( 'all' );
				$all_day_availability->set_bookable( ! $first_availability->is_bookable() );
				$availabilities = array( $all_day_availability ) + $availabilities;
			}
		}

		return $availabilities;
	}
}
