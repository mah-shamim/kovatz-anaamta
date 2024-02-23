<?php
/**
 * Class YITH_WCBK_Search_Form_Helper
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Search_Form_Helper' ) ) {
	/**
	 * Search Form Helper
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Search_Form_Helper {
		use YITH_WCBK_Singleton_Trait;

		const RESULT_KEY_IN_BOOKING_DATA = 'bk-sf-res';

		/**
		 * The post type name
		 *
		 * @var string
		 */
		public $post_type_name;


		/**
		 * Constructor
		 */
		protected function __construct() {
			$this->post_type_name = YITH_WCBK_Post_Types::SEARCH_FORM;
		}

		/**
		 * Get all search forms by arguments
		 *
		 * @param array $args Arguments.
		 *
		 * @return WP_Post[]|int[]
		 */
		public function get_forms( $args = array() ) {
			$default_args = array(
				'post_type'      => $this->post_type_name,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'fields'         => 'ids',
			);

			$args = wp_parse_args( $args, $default_args );

			return get_posts( $args );
		}

		/**
		 * Get forms in array id -> name
		 *
		 * @return array
		 */
		public function get_forms_in_array() {
			$form_ids = $this->get_forms();
			$forms    = array();

			if ( $form_ids && is_array( $form_ids ) ) {
				foreach ( $form_ids as $form_id ) {
					$forms[ $form_id ] = get_the_title( $form_id );
				}
			}

			return $forms;
		}

		/**
		 * Get a search form by id
		 *
		 * @param int $form_id The search form ID.
		 *
		 * @return WP_Post|bool
		 */
		public function get_form( $form_id ) {
			$form = get_post( $form_id );

			if ( $form && $form->post_type === $this->post_type_name ) {
				return $form;
			}

			return false;
		}


		/**
		 * Get data of a search form by id
		 *
		 * @param int $form_id The search form ID.
		 *
		 * @return object|bool
		 * @deprecated 1.0.8
		 */
		public function get_form_data( $form_id ) {
			$form = yith_wcbk_get_search_form( $form_id );
			if ( $form && $form->is_valid() ) {
				$form_data = array(
					'post'    => $form->get_post_data(),
					'fields'  => $form->get_fields(),
					'styles'  => $form->get_styles(),
					'options' => $form->get_options(),
				);

				return (object) $form_data;
			}

			return false;
		}

		/**
		 * Get the style setting of the form
		 *
		 * @param int $form_id The search form ID.
		 *
		 * @return array|false
		 * @deprecated 1.0.8
		 */
		public function get_form_styles( $form_id ) {
			$form = yith_wcbk_get_search_form( $form_id );
			if ( $form && $form->is_valid() ) {
				return $form->get_styles();
			}

			return false;
		}


		/**
		 * Search booking products
		 *
		 * @param array $args Arguments.
		 *
		 * @return array|bool
		 * @since 1.0.8
		 */
		public function search_booking_products( $args ) {
			$from           = $args['from'] ?? '';
			$to             = $args['to'] ?? '';
			$persons        = $args['persons'] ?? false;
			$person_types   = $args['person_types'] ?? array();
			$services       = $args['services'] ?? array();
			$categories     = $args['categories'] ?? array();
			$tags           = $args['tags'] ?? array();
			$location       = $args['location'] ?? '';
			$location_range = absint( $args['location_range'] ?? 30 );
			$search         = $args['s'] ?? '';

			$location_coord = yith_wcbk()->maps->get_location_by_address( $location );

			if ( ! ! $person_types && is_array( $person_types ) ) {
				$persons = 0;
				foreach ( $person_types as $person_type_id => $person_type_number ) {
					$persons += absint( $person_type_number );
					if ( ! absint( $person_type_number ) ) {
						unset( $person_types[ $person_type_id ] );
					}
				}
			}

			$search_args = array(
				'yith_wcbk_search' => 1,
				'posts_per_page'   => - 1,
				'post_type'        => 'product',
				'post_status'      => 'publish',
				'fields'           => 'ids',
				'meta_query'       => array(
					'relation' => 'AND',
				),
				'tax_query'        => array(
					'relation' => 'AND',
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => YITH_WCBK_Product_Post_Type_Admin::$prod_type,
					),
				),
			);

			if ( $search ) {
				$search_args['s'] = $search;
			}

			if ( $persons > 0 ) {
				$search_args['meta_query'][] = array(
					'key'     => '_yith_booking_min_persons',
					'value'   => $persons,
					'compare' => '<=',
					'type'    => 'numeric',
				);

				$search_args['meta_query'][] = array(
					'relation' => 'OR',
					array(
						'key'     => '_yith_booking_max_persons',
						'value'   => $persons,
						'compare' => '>=',
						'type'    => 'numeric',
					),
					array(
						'key'     => '_yith_booking_max_persons',
						'value'   => 1,
						'compare' => '<',
						'type'    => 'numeric',
					),
				);
			}

			if ( ! ! $services && is_array( $services ) ) {
				$search_args['tax_query'][] = array(
					'taxonomy' => YITH_WCBK_Post_Types::SERVICE_TAX,
					'field'    => 'term_id',
					'terms'    => array_map( 'absint', $services ),
					'operator' => 'AND',
				);
			}

			if ( ! ! $categories ) {
				$categories                 = is_array( $categories ) ? $categories : explode( ',', $categories );
				$search_args['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => array_map( 'absint', $categories ),
				);
			}

			if ( ! ! $tags && is_array( $tags ) ) {
				$search_args['tax_query'][] = array(
					'taxonomy' => 'product_tag',
					'field'    => 'term_id',
					'terms'    => array_map( 'absint', $tags ),
					'operator' => 'AND',
				);
			}

			if ( ! ! $location && ! ! ( $location_coord ) ) {
				// Location approximation for database query.
				$earth_radius   = 6371;
				$location_range = min( $location_range, $earth_radius );
				$lat            = $location_coord['lat'];
				$lng            = $location_coord['lng'];
				$delta_lat      = rad2deg( $location_range / $earth_radius );
				$delta_lng      = rad2deg( asin( $location_range / $earth_radius ) / cos( deg2rad( $lat ) ) );
				$max_lat        = min( $lat + $delta_lat, 90 );
				$min_lat        = max( $lat - $delta_lat, - 90 );
				$max_lng        = min( $lng + $delta_lng, 180 );
				$min_lng        = max( $lng - $delta_lng, - 180 );

				$search_args['meta_query'][] = array(
					'relation' => 'AND',
					array(
						'key'     => '_yith_booking_location_lat',
						'value'   => array( $min_lat, $max_lat ),
						'compare' => 'BETWEEN',
						'type'    => 'DECIMAL(10,5)',
					),
					array(
						'key'     => '_yith_booking_location_lng',
						'value'   => array( $min_lng, $max_lng ),
						'compare' => 'BETWEEN',
						'type'    => 'DECIMAL(10,5)',
					),
				);
			}

			do_action( 'yith_wcbk_search_booking_products_before_get_results', $search_args, $args );

			$search_args = apply_filters( 'yith_wcbk_search_booking_products_search_args', $search_args, $args );

			$product_ids = apply_filters( 'yith_wcbk_search_booking_products_search_results', null, $search_args, $args );
			if ( is_null( $product_ids ) ) {
				$product_ids = get_posts( $search_args );
			}

			do_action( 'yith_wcbk_search_booking_products_after_get_results', $search_args, $args );

			// Remove unavailable bookings.
			if ( ! ! $product_ids && is_array( $product_ids ) ) {
				$availability_args = array(
					'from' => ! ! $from ? strtotime( $from ) : false,
					'to'   => ! ! $to ? strtotime( $to ) : false,
				);
				foreach ( $product_ids as $product_id ) {
					$the_product       = wc_get_product( $product_id );
					$remove_product_id = true;

					if ( $the_product && $the_product instanceof WC_Product_Booking ) {
						$remove_product_id = false;
						if ( ! ! $person_types && $the_product->has_people_types_enabled() ) {
							$persons = 0;
							foreach ( $the_product->get_people_types() as $current_person_type ) {
								$current_person_type_id = $current_person_type['id'];

								if ( yith_plugin_fw_is_true( $current_person_type['enabled'] ) ) {
									if ( isset( $person_types[ $current_person_type_id ] ) && '' !== $person_types[ $current_person_type_id ] ) {
										$requested_number = absint( $person_types[ $current_person_type_id ] );
										$min_number       = absint( $current_person_type['min'] );
										$max_number       = absint( $current_person_type['max'] );
										if ( $requested_number < $min_number || ( $max_number > 0 && $requested_number > $max_number ) ) {
											$remove_product_id = true;
										}
										$persons += $requested_number;
									}
								} else {
									if ( isset( $person_types[ $current_person_type_id ] ) && $person_types[ $current_person_type_id ] > 0 ) {
										$remove_product_id = true;
									}
								}
							}
							$availability_args['persons'] = $persons;
						}

						if ( ! $the_product->has_time() && ! apply_filters( 'yith_wcbk_search_booking_products_show_daily_bookings_with_at_least_one_day_available', false ) ) {
							if ( ! ! $from && ! $the_product->is_available( $availability_args ) ) {
								$remove_product_id = true;
							}
						} else {
							if ( ! ! $from ) {
								$_from                      = strtotime( $from );
								$_to                        = ! ! $to ? strtotime( $to ) : $_from;
								$_current_day               = $_from;
								$has_at_least_one_time_slot = false;
								$relative_minimum_duration  = $the_product->get_minimum_duration() * $the_product->get_duration();
								$duration_unit              = $the_product->get_duration_unit();

								while ( $_current_day <= $_to ) {
									if ( $the_product->has_time() ) {
										if ( $the_product->has_at_least_one_time_slot_available_on( $_current_day ) ) {
											$has_at_least_one_time_slot = true;
											break;
										}
									} else {
										$min_to = yith_wcbk_date_helper()->get_time_sum( $_current_day, $relative_minimum_duration, $duration_unit, true );
										if (
											$min_to <= $_to
											&& $the_product->is_available(
												array(
													'from' => $_current_day,
													'to'   => $min_to,
												)
											)
										) {
											$has_at_least_one_time_slot = true;
											break;
										}
									}

									$_current_day = strtotime( 'tomorrow', $_current_day );
								}

								if ( ! $has_at_least_one_time_slot ) {
									$remove_product_id = true;
								}
							}
						}

						if ( ! $remove_product_id && ! ! $location_coord ) {
							$product_location_coord = $the_product->get_location_coordinates();

							if ( ! ! $product_location_coord ) {
								$distance = yith_wcbk()->maps->calculate_distance( $location_coord, $product_location_coord );

								if ( false !== $distance && floatval( $location_range ) < floatval( $distance ) ) {
									$remove_product_id = true;
								}
							} else {
								$remove_product_id = true;
							}
						}
					}

					if ( $remove_product_id ) {
						$product_ids = array_diff( $product_ids, array( $product_id ) );
					}
				}
				unset( $the_product );
			}

			return apply_filters( 'yith_wcbk_search_booking_products', $product_ids, $args );
		}

		/**
		 * Get searched values from query string
		 * used for showing default values in booking form by searched values
		 *
		 * @param string $key The key.
		 *
		 * @return bool|false|string
		 */
		public static function get_searched_value_for_field( $key ) {
			$value = false;
			if ( isset( $_GET[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$value = wc_clean( wp_unslash( $_GET[ $key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

				if ( in_array( $key, array( 'from', 'to' ), true ) && is_numeric( $value ) ) {
					$value = gmdate( 'Y-m-d', $value );
				}
			}

			return apply_filters( 'yith_wcbk_searched_value_for_field', $value, $key );
		}
	}
}
