<?php
/**
 * Class YITH_WCBK_Cart
 * handle add-to-cart processes for Booking products
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Cart' ) ) {
	/**
	 * Class YITH_WCBK_Cart
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Cart {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Cart constructor.
		 */
		private function __construct() {
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'woocommerce_add_cart_item_data' ), 10, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'woocommerce_get_cart_item_from_session' ), 99, 3 );
			add_filter( 'woocommerce_add_cart_item', array( $this, 'woocommerce_add_cart_item' ), 10, 2 );

			add_filter( 'woocommerce_cart_item_class', array( $this, 'add_cart_item_class_to_booking_products' ), 10, 3 );

			add_filter( 'woocommerce_get_item_data', array( $this, 'woocommerce_get_item_data' ), 10, 2 );

			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'prevent_add_to_cart_if_request_confirm' ), 10, 3 );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 10, 6 );

			add_action( 'woocommerce_check_cart_items', array( $this, 'check_booking_availability' ) );
			add_action( 'woocommerce_before_checkout_process', array( $this, 'check_booking_availability_before_checkout' ) );

			if ( 'yes' === get_option( 'yith-wcbk-redirect-to-checkout-after-booking', 'no' ) ) {
				add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'checkout_redirect_after_add_to_cart' ), 10, 2 );
				add_filter( 'wc_add_to_cart_message_html', array( $this, 'empty_added_to_cart_message' ), 10, 2 );
			}

			if ( 'yes' === get_option( 'yith-wcbk-show-booking-of-in-cart-and-checkout', 'no' ) ) {
				add_filter( 'woocommerce_cart_item_name', array( $this, 'cart_item_name' ), 10, 2 );
			}
		}

		/**
		 * Get the default booking data
		 *
		 * @return array
		 * @since 2.0.8
		 */
		public static function get_default_booking_data() {
			return array(
				'add-to-cart'                => 0,
				'from'                       => 'now',
				'to'                         => '',
				'duration'                   => 1,
				'persons'                    => 1,
				'person_types'               => array(),
				'booking_services'           => array(),
				'booking_service_quantities' => array(),
			);
		}

		/**
		 * Get booking data from Request Form
		 *
		 * @param array $request The Request.
		 *
		 * @return array
		 */
		public static function get_booking_data_from_request( $request = array() ) {
			$request     = empty( $request ) ? $_REQUEST : $request; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$request     = apply_filters( 'yith_wcbk_cart_booking_data_request', $request );
			$date_helper = yith_wcbk_date_helper();

			$booking_fields = self::get_default_booking_data();
			$booking_data   = array();
			foreach ( $booking_fields as $field => $default ) {
				$booking_data[ $field ] = ! empty( $request[ $field ] ) ? $request[ $field ] : $default;
			}

			$product_id = absint( $booking_data['add-to-cart'] );
			if ( ! $product_id && isset( $request['product_id'] ) ) {
				$product_id = absint( $request['product_id'] );

			}

			/**
			 * The booking product.
			 *
			 * @var WC_Product_Booking $product
			 */
			$product = wc_get_product( $product_id );
			if ( ! $product || ! YITH_WCBK_Product_Post_Type_Admin::is_booking( $product_id ) ) {
				return array();
			}

			if ( ! is_numeric( $booking_data['from'] ) ) {
				$booking_data['from'] = strtotime( $booking_data['from'] );
			}

			if ( empty( $request['to'] ) ) {
				$from                     = $booking_data['from'];
				$duration                 = absint( $booking_data['duration'] ) * $product->get_duration();
				$booking_data['to']       = $date_helper->get_time_sum( $from, $duration, $product->get_duration_unit() );
				$booking_data['duration'] = $duration;
				if ( $product->is_full_day() ) {
					$booking_data['to'] -= 1;
				}
			} else {
				if ( ! is_numeric( $booking_data['to'] ) ) {
					$booking_data['to'] = strtotime( $booking_data['to'] );
				}

				if ( $product->is_full_day() ) {
					$booking_data['to'] = $date_helper->get_time_sum( $booking_data['to'], 1, 'day' );
				}

				$booking_data['duration'] = $date_helper->get_time_diff( $booking_data['from'], $booking_data['to'], $product->get_duration_unit() );

				if ( $product->is_full_day() ) {
					$booking_data['to'] -= 1;
				}
			}

			if ( ! empty( $request['person_types'] ) ) {
				$request['person_types'] = yith_wcbk_booking_person_types_to_id_number_array( $request['person_types'] );
			}

			if ( $product->has_people() ) {
				if ( ! empty( $request['person_types'] ) ) {
					$persons = 0;
					foreach ( $request['person_types'] as $person_type_id => $number ) {
						$persons += absint( $number );
					}
					$booking_data['persons'] = $persons;
				}
			} else {
				if ( isset( $booking_data['persons'] ) ) {
					unset( $booking_data['persons'] );
				}
			}

			$services           = $product->get_service_ids();
			$selected_services  = array_filter( array_map( 'absint', $booking_data['booking_services'] ) );
			$service_quantities = $booking_data['booking_service_quantities'] ?? array();
			if ( $services && is_array( $services ) ) {
				$all_services = array();
				foreach ( $services as $service_id ) {
					$service = yith_get_booking_service( $service_id );
					if ( ! $service->is_valid() ) {
						continue;
					}

					if ( $service->is_quantity_enabled() ) {
						$selected_quantity = isset( $service_quantities[ $service_id ] ) ? absint( $service_quantities[ $service_id ] ) : 0;
						$quantity          = $service->validate_quantity( $selected_quantity );

						if ( $quantity < 1 ) {
							continue;
						}
					}

					if ( $service->is_optional() && ! in_array( $service_id, $selected_services, true ) ) {
						continue;
					}

					$all_services[] = $service_id;
				}

				$booking_data['booking_services'] = $all_services;
			}

			unset( $booking_data['add-to-cart'] );

			return apply_filters( 'yith_wcbk_cart_get_booking_data_from_request', $booking_data, $request );
		}

		/**
		 * Bookings that require admin confirmation cannot be added to the cart
		 *
		 * @param bool $passed_validation The validation.
		 * @param int  $product_id        The product ID.
		 * @param int  $quantity          The quantity.
		 *
		 * @return bool
		 */
		public function prevent_add_to_cart_if_request_confirm( $passed_validation, $product_id, $quantity ) {
			if ( YITH_WCBK_Product_Post_Type_Admin::is_booking( $product_id ) ) {
				/**
				 * The booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product = wc_get_product( $product_id );
				if ( ! $product || ( $product->is_confirmation_required() ) ) {
					return false;
				}
			}

			return $passed_validation;
		}

		/**
		 * Add to cart validation for Booking Products
		 *
		 * @param bool   $passed_validation The validation.
		 * @param int    $product_id        The product ID.
		 * @param int    $quantity          The quantity.
		 * @param string $variation_id      The variation ID.
		 * @param array  $variations        The variations.
		 * @param array  $cart_item_data    The cart item data.
		 *
		 * @return bool
		 */
		public function add_to_cart_validation( $passed_validation, $product_id, $quantity, $variation_id = '', $variations = array(), $cart_item_data = array() ) {
			if ( YITH_WCBK_Product_Post_Type_Admin::is_booking( $product_id ) ) {
				$product_id = apply_filters( 'yith_wcbk_booking_product_id_to_translate', $product_id );

				/**
				 * The booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product = wc_get_product( $product_id );

				if ( $product ) {
					// Get the request from cart_item_data; if it's not set, get it by $_REQUEST.
					$request      = ! empty( $cart_item_data['yith_booking_request'] ) ? $cart_item_data['yith_booking_request'] : false;
					$booking_data = self::get_booking_data_from_request( $request );
					$product_link = "<a href='{$product->get_permalink()}'>{$product->get_title()}</a>";
					if ( ! $booking_data ) {
						// translators: %s is the product link.
						wc_add_notice( sprintf( __( 'There was an error in the request, please try again: %s', 'yith-booking-for-woocommerce' ), $product_link ), 'error' );
						$passed_validation = false;
					} else {

						$availability_args = array(
							'from' => $booking_data['from'],
							'to'   => $booking_data['to'],
						);
						$r_persons         = absint( $booking_data['persons'] ?? 0 );
						if ( $product->has_people() ) {
							$availability_args['persons'] = max( 1, $r_persons );
						}

						$availability_args['return'] = 'array';
						$availability                = $product->is_available( $availability_args );
						if ( ! $availability['available'] ) {
							if ( $availability['non_available_reasons'] ) {
								$non_available_reasons = implode( ', ', $availability['non_available_reasons'] );
								// translators: 1. the product name; 2. list of reasons why the product is not available.
								wc_add_notice( sprintf( __( '%1$s is not available: %2$s', 'yith-booking-for-woocommerce' ), $product->get_title(), $non_available_reasons ), 'error' );
							} else {
								// translators: %s is the product name.
								wc_add_notice( sprintf( __( '%s is not available', 'yith-booking-for-woocommerce' ), $product->get_title() ), 'error' );
							}
							$passed_validation = false;
						}

						if ( $product->has_people() ) {
							$min_persons = $product->get_minimum_number_of_people();
							$max_persons = $product->get_maximum_number_of_people();
							if ( $r_persons < $min_persons ) {
								// translators: %s is the minimum number of people.
								wc_add_notice( sprintf( __( 'Minimum number of people: %s', 'yith-booking-for-woocommerce' ), $min_persons ), 'error' );
								$passed_validation = false;
							}

							if ( $max_persons > 0 && $r_persons > $max_persons ) {
								// translators: %s is the maximum number of people.
								wc_add_notice( sprintf( __( 'Maximum number of people: %s', 'yith-booking-for-woocommerce' ), $max_persons ), 'error' );
								$passed_validation = false;
							}
						}

						if ( $passed_validation && $product->has_people_types_enabled() ) {
							$people_types   = $product->get_enabled_people_types();
							$r_people_types = yith_wcbk_booking_person_types_to_id_number_array( $booking_data['person_types'] ?? array() );
							foreach ( $people_types as $people_type ) {
								$people_type_id   = $people_type['id'] ?? 0;
								$people_type_name = get_the_title( $people_type_id );
								$min_persons      = absint( $people_type['min'] ?? 0 );
								$max_persons      = absint( $people_type['max'] ?? 0 );
								$current_persons  = absint( $r_people_types[ $people_type_id ] ?? 0 );
								if ( $min_persons && $current_persons < $min_persons ) {
									// translators: 1. person type name; 2. minimum number of people.
									wc_add_notice( sprintf( __( 'Minimum number for "%1$s": %2$s', 'yith-booking-for-woocommerce' ), $people_type_name, $min_persons ), 'error' );
									$passed_validation = false;
								}

								if ( $max_persons > 0 && $current_persons > $max_persons ) {
									// translators: 1. person type name; 2. maximum number of people.
									wc_add_notice( sprintf( __( 'Maximum number for "%1$s": %2$s', 'yith-booking-for-woocommerce' ), $people_type_name, $max_persons ), 'error' );
									$passed_validation = false;
								}
							}
						}

						if ( $passed_validation && $product->get_max_bookings_per_unit() ) {
							// Check if there are booking products already added to the cart in the same dates.
							$from                      = $booking_data['from'];
							$to                        = $booking_data['to'];
							$count_persons_as_bookings = $product->has_count_people_as_separate_bookings_enabled();
							$include_externals         = $product->has_external_calendars();
							$max_booking_per_block     = $product->get_max_bookings_per_unit();
							$unit                      = $product->get_duration_unit();

							$bookings_added_to_cart_in_same_dates = $this->count_added_to_cart_bookings_in_period( compact( 'product_id', 'from', 'to', 'count_persons_as_bookings' ) );
							$booked_bookings_in_same_dates        = yith_wcbk_booking_helper()->count_max_booked_bookings_per_unit_in_period( compact( 'product_id', 'from', 'to', 'unit', 'include_externals', 'count_persons_as_bookings' ) );
							$_existing_bookings                   = $bookings_added_to_cart_in_same_dates + $booked_bookings_in_same_dates;
							$_booking_weight                      = ! ! $count_persons_as_bookings ? $r_persons : 1;

							if ( $_existing_bookings + $_booking_weight > $max_booking_per_block ) {
								$_remained      = $max_booking_per_block - $_existing_bookings;
								$_remained_text = '';
								if ( ! ! $_remained ) {
									if ( $product->has_people() && $count_persons_as_bookings ) {
										// translators: %s is the number of available people remained.
										$_remained_text = sprintf( __( 'Too many people selected (%s remaining)', 'yith-booking-for-woocommerce' ), $_remained );
									} else {
										// translators: %s is the number of available bookings.
										$_remained_text = sprintf( __( '(%s remaining)', 'yith-booking-for-woocommerce' ), $_remained );
									}
								}

								$notice = apply_filters(
									'yith_wcbk_no_add_to_cart_for_selected_data',
									sprintf(
										'<a href="%s" class="button wc-forward">%s</a> %s',
										wc_get_cart_url(),
										__( 'View cart', 'woocommerce' ),
										// translators: 1. the product name; 2. additional details about availability.
										sprintf( __( 'You cannot add another &quot;%1$s&quot; to your cart in the dates you selected. %2$s', 'yith-booking-for-woocommerce' ), $product->get_title(), $_remained_text )
									),
									wc_get_cart_url(),
									$product->get_title(),
									$_remained_text
								);
								wc_add_notice( $notice, 'error' );
								$passed_validation = false;

								yith_wcbk_do_deprecated_action( 'yith_wcbk_add_to_cart_for_selected_data', array(), '3.0', 'yith_wcbk_after_add_to_cart_validation' );
							}
						}

						do_action( 'yith_wcbk_after_add_to_cart_validation', $product, $passed_validation, $booking_data, $cart_item_data );
					}
				}
			}

			return $passed_validation;
		}

		/**
		 * Count Bookings added to cart in the same period passed by $args.
		 *
		 * @param array $args Arguments.
		 *
		 * @return int
		 * @since 1.0.7
		 */
		public function count_added_to_cart_bookings_in_period( $args = array() ) {
			$default_args = array(
				'product_id'                => 0,
				'from'                      => '',
				'to'                        => '',
				'count_persons_as_bookings' => false,
			);

			$args = wp_parse_args( $args, $default_args );

			$found_bookings = 0;

			if ( ! ! $args['product_id'] && ! ! $args['from'] && ! ! $args['to'] ) {
				$cart_contents = WC()->cart->cart_contents;
				if ( ! ! $cart_contents ) {
					foreach ( $cart_contents as $cart_item_key => $cart_item_data ) {
						if ( isset( $cart_item_data['product_id'] ) && absint( $cart_item_data['product_id'] ) === absint( $args['product_id'] ) ) {
							// Booking in cart with the same product_id.
							if ( isset( $cart_item_data['yith_booking_data']['from'] ) && isset( $cart_item_data['yith_booking_data']['to'] ) ) {
								if ( $cart_item_data['yith_booking_data']['from'] < $args['to'] && $cart_item_data['yith_booking_data']['to'] > $args['from'] ) {
									if ( $args['count_persons_as_bookings'] && ! empty( $cart_item_data['yith_booking_data']['persons'] ) ) {
										$found_bookings += max( 1, absint( $cart_item_data['yith_booking_data']['persons'] ) );
									} else {
										$found_bookings ++;
									}
								}
							}
						}
					}
				}
			}

			return $found_bookings;

		}

		/**
		 * Add Cart item data for booking products
		 *
		 * @param array $cart_item_data The cart item data.
		 * @param int   $product_id     The product ID.
		 *
		 * @return array
		 */
		public function woocommerce_add_cart_item_data( $cart_item_data, $product_id ) {
			$is_booking = YITH_WCBK_Product_Post_Type_Admin::is_booking( $product_id );
			if ( $is_booking && ! isset( $cart_item_data['yith_booking_data'] ) ) {

				// Get the request from cart_item_data; if it's not set, get it by $_REQUEST.
				$request      = ! empty( $cart_item_data['yith_booking_request'] ) ? $cart_item_data['yith_booking_request'] : false;
				$booking_data = self::get_booking_data_from_request( $request );

				if ( ! isset( $booking_data['_added-to-cart-timestamp'] ) ) {
					/**
					 * Add the timestamp to allow adding to cart more booking products with the same configuration.
					 *
					 * @since 1.0.10
					 */
					$booking_data['_added-to-cart-timestamp'] = time();
				}

				$cart_item_data['yith_booking_data'] = $booking_data;
			}

			return $cart_item_data;
		}

		/**
		 * Set correct price for Booking on add-to-cart item
		 *
		 * @param array  $cart_item_data The cart item data.
		 * @param string $cart_item_key  The cart item key.
		 *
		 * @return array
		 */
		public function woocommerce_add_cart_item( $cart_item_data, $cart_item_key ) {
			$product_id = $cart_item_data['product_id'] ?? 0;
			if ( YITH_WCBK_Product_Post_Type_Admin::is_booking( $product_id ) ) {
				/**
				 * The Booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product      = $cart_item_data['data'];
				$booking_data = $cart_item_data['yith_booking_data'];

				$price = $this->get_product_price( $product_id, $booking_data );

				$product->set_price( $price );
				$cart_item_data['data'] = $product;
			}

			return $cart_item_data;
		}

		/**
		 * Set invalid order awaiting payment in WC session
		 * when the customer add a product in cart, since when a new product is added to the cart
		 * the old value of order_awaiting_payment is invalid, because the customer is creating a new order
		 *
		 * @since      2.1.1
		 * @deprecated 3.0.0
		 */
		public function set_invalid_order_awaiting_payment_in_session() {
			$current_session_order_id = isset( WC()->session->order_awaiting_payment ) ? absint( WC()->session->order_awaiting_payment ) : 0;
			WC()->session->set( 'yith_wcbk_invalid_order_awaiting_payment', $current_session_order_id );
		}

		/**
		 * Set correct price for Booking.
		 *
		 * @param array  $session_data  The session data.
		 * @param array  $cart_item     The cart item.
		 * @param string $cart_item_key The cart item key.
		 *
		 * @return array
		 */
		public function woocommerce_get_cart_item_from_session( $session_data, $cart_item, $cart_item_key ) {
			$product_id = $cart_item['product_id'] ?? 0;
			if ( YITH_WCBK_Product_Post_Type_Admin::is_booking( $product_id ) ) {
				/**
				 * The Booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product      = $session_data['data'];
				$booking_data = $session_data['yith_booking_data'];

				$price = $this->get_product_price( $product_id, $booking_data );

				$product->set_price( $price );
				$session_data['data'] = $product;
			}

			return $session_data;
		}

		/**
		 * Check the booking availability before checkout
		 * and remove no longer available booking products
		 *
		 * @throws Exception When validation fails.
		 * @since 2.0.1
		 */
		public function check_booking_availability_before_checkout() {
			$cart   = WC()->cart;
			$errors = array();

			foreach ( $cart->get_cart() as $cart_item_key => $values ) {
				/**
				 * The Booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product      = $values['data'];
				$booking_data = ! empty( $values['yith_booking_data'] ) ? $values['yith_booking_data'] : false;

				if ( $product && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) && $booking_data ) {
					$booking_data['exclude_order_id'] = yith_wcbk_get_order_awaiting_payment();
					if ( ! $product->is_available( $booking_data ) ) {
						$cart->set_quantity( $cart_item_key, 0 );
						$product_link = "<a href='{$product->get_permalink()}'>{$product->get_name()}</a>";
						// translators: %s is the product name (with link).
						$notice_text = sprintf( __( '%s has been removed from your cart because it can no longer be booked.', 'yith-booking-for-woocommerce' ), $product_link );

						$errors[] = $notice_text;
					}
				}
			}

			if ( ! ! $errors ) {
				throw new Exception( implode( '<br />', $errors ) );
			}
		}

		/**
		 * Check the booking availability in cart.
		 *
		 * @since 2.1.1
		 */
		public function check_booking_availability() {
			$cart = WC()->cart;

			foreach ( $cart->get_cart() as $cart_item_key => $values ) {
				/**
				 * The Booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product      = $values['data'];
				$booking_data = ! empty( $values['yith_booking_data'] ) ? $values['yith_booking_data'] : false;

				if ( $product && $product->is_type( YITH_WCBK_Product_Post_Type_Admin::$prod_type ) && $booking_data ) {
					$booking_data['exclude_order_id'] = yith_wcbk_get_order_awaiting_payment();
					if ( ! $product->is_available( $booking_data ) ) {
						$cart->set_quantity( $cart_item_key, 0 );
						$product_link = "<a href='{$product->get_permalink()}'>{$product->get_name()}</a>";
						// translators: %s is the product name (with link).
						wc_add_notice( sprintf( __( '%s has been removed from your cart because it can no longer be booked.', 'yith-booking-for-woocommerce' ), $product_link ), 'error' );
					}
				}
			}
		}

		/**
		 * Filter item data
		 *
		 * @param array $item_data The item data.
		 * @param array $cart_item The cart item.
		 *
		 * @return array
		 */
		public function woocommerce_get_item_data( $item_data, $cart_item ) {
			$product_id = isset( $cart_item['product_id'] ) ? $cart_item['product_id'] : 0;
			if ( YITH_WCBK_Product_Post_Type_Admin::is_booking( $product_id ) ) {
				/**
				 * The Booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product      = wc_get_product( $product_id );
				$booking_data = $cart_item['yith_booking_data'];
				$from         = $booking_data['from'];
				$to           = $booking_data['to'];
				$duration     = $booking_data['duration'];

				$booking_item_data = array(
					'yith_booking_from'     => array(
						'key'     => yith_wcbk_get_booking_meta_label( 'from' ),
						'value'   => $from,
						'display' => $product->has_time() ? yith_wcbk_datetime( $from ) : yith_wcbk_date( $from ),
					),
					'yith_booking_to'       => array(
						'key'     => yith_wcbk_get_booking_meta_label( 'to' ),
						'value'   => $to,
						'display' => $product->has_time() ? yith_wcbk_datetime( $to ) : yith_wcbk_date( $to ),
					),
					'yith_booking_duration' => array(
						'key'     => yith_wcbk_get_booking_meta_label( 'duration' ),
						'value'   => $duration,
						'display' => yith_wcbk_format_duration( $duration, $product->get_duration_unit() ),
					),
				);

				if ( $product->has_people() ) {
					$persons = $booking_data['persons'] ?? 1;
					if ( ! empty( $booking_data['person_types'] ) ) {
						foreach ( $booking_data['person_types'] as $person_type_id => $person_type_number ) {
							if ( $person_type_number < 1 ) {
								continue;
							}
							$person_type_name = yith_wcbk()->person_type_helper->get_person_type_title( $person_type_id );

							$booking_item_data[ 'yith_booking_person_type_' . $person_type_id ] = array(
								'key'     => $person_type_name,
								'value'   => $person_type_number,
								'display' => $person_type_number,
							);
						}
					} else {
						$booking_item_data['yith_booking_persons'] = array(
							'key'     => yith_wcbk_get_booking_meta_label( 'persons' ),
							'value'   => $persons,
							'display' => $persons,
						);
					}
				}

				$services           = $product->get_service_ids();
				$selected_services  = isset( $booking_data['booking_services'] ) ? array_filter( array_map( 'absint', $booking_data['booking_services'] ) ) : array();
				$service_quantities = $booking_data['booking_service_quantities'] ?? array();
				if ( $services && is_array( $services ) ) {
					$my_services = array();
					foreach ( $services as $service_id ) {
						$service = yith_get_booking_service( $service_id );
						if ( ! $service->is_valid() ) {
							continue;
						}

						if ( $service->is_quantity_enabled() ) {
							$selected_quantity = isset( $service_quantities[ $service_id ] ) ? absint( $service_quantities[ $service_id ] ) : 0;
							$quantity          = $service->validate_quantity( $selected_quantity );
							if ( $quantity !== $selected_quantity ) {
								$booking_data['booking_service_quantities'][ $service_id ] = $quantity;
							}

							if ( $quantity < 1 ) {
								continue;
							}
						}

						if ( $service->is_optional() && ! in_array( $service_id, $selected_services, true ) ) {
							continue;
						}

						if ( ! $service->is_hidden() ) {
							$quantity      = $service_quantities[ $service_id ] ?? false;
							$my_services[] = $service->get_name_with_quantity( $quantity );
						}
					}
					if ( ! ! $my_services ) {
						$booking_item_data['yith_booking_services'] = array(
							'key'     => yith_wcbk_get_label( 'booking-services' ),
							'value'   => $my_services,
							'display' => yith_wcbk_booking_services_html( $my_services ),
						);
					}
				}

				if ( 'yes' === get_option( 'yith-wcbk-show-totals-in-cart-and-checkout', 'no' ) ) {

					$totals      = $product->calculate_totals( $booking_data );
					$totals_html = implode(
						"\n",
						array_map(
							function ( $total ) use ( $product ) {
								$price   = $total['display'] ?? ( yith_wcbk_get_formatted_price_to_display( $product, $total['value'] ) );
								$classes = '';
								if ( $total['value'] < 0 ) {
									$classes = 'yith-wcbk-cart-item-data--discount';
								}

								return '<span class="' . esc_attr( $classes ) . '">' . $total['label'] . ': ' . $price . '</span>';
							},
							$totals
						)
					);

					$booking_item_data['yith_booking_totals'] = array(
						'key'     => __( 'Totals', 'yith-booking-for-woocommerce' ),
						'value'   => $totals,
						'display' => $totals_html,
					);
				}

				$item_data = array_merge( $item_data, $booking_item_data );
			}

			return $item_data;
		}

		/**
		 * Get product price depending on booking data.
		 *
		 * @param int   $product_id   The product ID.
		 * @param array $booking_data The booking data.
		 *
		 * @return bool|float|string
		 */
		public function get_product_price( $product_id, $booking_data ) {
			$price = false;
			if ( YITH_WCBK_Product_Post_Type_Admin::is_booking( $product_id ) ) {
				if ( isset( $booking_data['person_types'] ) ) {
					$person_types = array();
					foreach ( $booking_data['person_types'] as $person_type_id => $person_type_number ) {
						$person_types[] = array(
							'id'     => $person_type_id,
							'number' => $person_type_number,
						);
					}
					$booking_data['person_types'] = $person_types;
				}
				/**
				 * The booking product.
				 *
				 * @var WC_Product_Booking $product
				 */
				$product = wc_get_product( $product_id );
				$price   = $product->calculate_price( $booking_data );
			} else {
				$product = wc_get_product( $product_id );
				if ( $product ) {
					$price = $product->get_price();
				}
			}

			return $price;
		}

		/**
		 * Redirect to the "Checkout" page after adding the product to the cart.
		 *
		 * @param string           $url     The redirect URL.
		 * @param WC_Product|false $product The added-to-cart product.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function checkout_redirect_after_add_to_cart( $url, $product = false ) {
			if ( $product && is_a( $product, 'WC_Product' ) && yith_wcbk_is_booking_product( $product ) ) {
				$url = wc_get_checkout_url();
			}

			return $url;
		}

		/**
		 * Empty added-to-cart message to prevent showing it for Booking products if "Redirect users to checkout" option is enabled.
		 *
		 * @param string $message  The HTML message.
		 * @param array  $products Key-value array of product ID-quantity.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function empty_added_to_cart_message( $message, $products ) {
			if ( count( $products ) === 1 ) {
				$product_id = current( array_keys( $products ) );
				if ( yith_wcbk_is_booking_product( $product_id ) ) {
					$message = '';
				}
			}

			return $message;
		}

		/**
		 * Filter cart item name
		 *
		 * @param string $name      The product name shown in cart.
		 * @param array  $cart_item The cart item.
		 *
		 * @return string
		 */
		public function cart_item_name( $name, $cart_item ) {
			/**
			 * The Booking product.
			 *
			 * @var WC_Product_Booking $product
			 */
			$product = $cart_item['data'];

			if ( is_a( $product, 'WC_Product' ) && yith_wcbk_is_booking_product( $product ) ) {
				$name = yith_wcbk_product_booking_of_name( $name );
			}

			return $name;
		}

		/**
		 * Add specific class to booking product cart items.
		 *
		 * @param string $class_name The CSS class.
		 * @param array  $cart_item  The cart item.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function add_cart_item_class_to_booking_products( $class_name, $cart_item ) {
			if ( isset( $cart_item['yith_booking_data'] ) ) {
				$class_name .= ' cart-item--booking';
			}

			return $class_name;
		}
	}
}
