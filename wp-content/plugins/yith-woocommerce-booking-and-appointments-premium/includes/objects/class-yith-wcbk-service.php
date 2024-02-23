<?php
/**
 * Class YITH_WCBK_Service
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit();

if ( ! class_exists( 'YITH_WCBK_Service' ) ) {
	/**
	 * Class YITH_WCBK_Service
	 * the Service
	 *
	 * @property    string $name
	 * @property    string $slug
	 * @property    string $description
	 * @property    string $price
	 * @property    string $optional
	 * @property    string $hidden
	 * @property    string $hidden_in_search_forms
	 * @property    string $multiply_per_blocks
	 * @property    string $multiply_per_persons
	 * @property    array  $price_for_person_types
	 * @property    string $quantity_enabled
	 * @property    string $min_quantity
	 * @property    string $max_quantity
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Service {

		/**
		 * Boolean props
		 *
		 * @var string[]
		 */
		private static $boolean_props = array( 'quantity_enabled', 'optional', 'multiply_per_persons', 'multiply_per_blocks', 'hidden_in_search_forms' );

		/**
		 * The term ID.
		 *
		 * @var int
		 */
		public $id;

		/**
		 * The term object.
		 *
		 * @var WP_Term
		 */
		public $term;

		/**
		 * Taxonomy name.
		 *
		 * @var string
		 */
		public $taxonomy_name;

		/**
		 * YITH_WCBK_Service constructor.
		 *
		 * @param int          $term_id Term ID.
		 * @param WP_Term|null $term    Term object.
		 */
		public function __construct( $term_id, $term = null ) {
			$this->taxonomy_name = YITH_WCBK_Post_Types::SERVICE_TAX;
			$this->id            = absint( $term_id );

			$this->populate( $term );
		}

		/**
		 * __get function.
		 *
		 * @param string $key The key.
		 *
		 * @return mixed
		 */
		public function __get( $key ) {
			$value = apply_filters( 'yith_wcbk_booking_service_get', null, $key, $this );
			if ( is_null( $value ) ) {
				$value = get_term_meta( $this->id, $key, true );
			}

			if ( ! empty( $value ) ) {
				$this->$key = $value;
			}

			return $value;
		}

		/**
		 * __isset function.
		 *
		 * @param string $key The key.
		 *
		 * @return bool
		 */
		public function __isset( $key ) {
			return metadata_exists( 'term', $this->id, $key );
		}

		/**
		 * Set function.
		 *
		 * @param string $property Property.
		 * @param mixed  $value    Value.
		 *
		 * @return bool|int
		 */
		public function set( $property, $value ) {
			if ( 'price' === $property && $value ) {
				$value = wc_format_decimal( $value );
			}

			if ( 'price_for_person_types' === $property && is_array( $value ) ) {
				foreach ( $value as $k => $v ) {
					$value[ $k ] = wc_format_decimal( $v );
				}
			}

			if ( in_array( $property, self::$boolean_props, true ) ) {
				$value = wc_bool_to_string( $value );
			}

			$this->$property = $value;

			return update_term_meta( $this->id, $property, wc_clean( $value ) );
		}

		/**
		 * Get data of the service
		 *
		 * @param WP_Term $term The term.
		 */
		private function populate( $term = null ) {
			if ( empty( $term ) ) {
				$this->term = get_term( $this->id, $this->taxonomy_name );
			} else {
				$this->term = $term;
			}
			if ( $this->is_valid() ) {
				$this->name        = $this->term->name;
				$this->description = $this->term->description;
				$this->slug        = $this->term->slug;

				foreach ( $this->get_meta() as $key => $value ) {
					if ( in_array( $key, self::$boolean_props, true ) ) {
						$value = wc_bool_to_string( $value );
					}
					$this->$key = $value;
				}

				do_action( 'yith_wcbk_booking_service_loaded', $this );
			}
		}

		/**
		 * Get the ID.
		 *
		 * @return int
		 * @since 3.0.0
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Check if the service is valid.
		 *
		 * @return bool
		 */
		public function is_valid() {
			return ! empty( $this->term ) && ! empty( $this->id ) && $this->term->term_id === $this->id;
		}


		/**
		 * Check if the service is hidden
		 *
		 * @return bool
		 */
		public function is_hidden() {
			return 'yes' === $this->hidden;
		}

		/**
		 * Check if the service is hidden in search forms
		 *
		 * @return bool
		 */
		public function is_hidden_in_search_forms() {
			return 'yes' === $this->hidden_in_search_forms || $this->is_hidden();
		}

		/**
		 * Check if the service has multiply per blocks enabled
		 *
		 * @return bool
		 */
		public function is_multiply_per_blocks() {
			return 'yes' === $this->multiply_per_blocks;
		}

		/**
		 * Check if the service has multiply per persons enabled
		 *
		 * @return bool
		 */
		public function is_multiply_per_persons() {
			return 'yes' === $this->multiply_per_persons;
		}

		/**
		 * Check if the service is optional
		 *
		 * @return bool
		 */
		public function is_optional() {
			return 'yes' === $this->optional;
		}

		/**
		 * Check if the service has quantity enabled
		 *
		 * @return bool
		 * @since 2.0.5
		 */
		public function is_quantity_enabled() {
			return 'yes' === $this->quantity_enabled;
		}

		/**
		 * Get the min quantity
		 *
		 * @return int
		 */
		public function get_min_quantity() {
			return max( 0, absint( $this->min_quantity ) );
		}

		/**
		 * Get the max quantity
		 *
		 * @return int
		 */
		public function get_max_quantity() {
			return absint( $this->max_quantity );
		}

		/**
		 * Get the price of the current service
		 *
		 * @param int $person_type Person type ID.
		 *
		 * @return string
		 */
		public function get_price_for_person_type( $person_type ) {
			$price = '';
			if ( $person_type ) {
				$price_for_person_types = $this->price_for_person_types;
				if ( isset( $price_for_person_types[ $person_type ] ) ) {
					$price = $price_for_person_types[ $person_type ];
				}
			}

			return $price;
		}

		/**
		 * Get the price of the current service
		 *
		 * @param int $person_type Person type ID.
		 *
		 * @return string
		 */
		public function get_price( $person_type = 0 ) {
			$price = $this->price;
			if ( $person_type ) {
				$price_for_person_type = $this->get_price_for_person_type( $person_type );
				if ( '' !== $price_for_person_type ) {
					$price = $price_for_person_type;
				}
			}

			return apply_filters( 'yith_wcbk_service_price', floatval( $price ) );
		}

		/**
		 * Get the name of the service
		 *
		 * @return string
		 */
		public function get_name() {
			return apply_filters( 'yith_wcbk_get_service_name', $this->name, $this );
		}

		/**
		 * Get the service name including quantity
		 *
		 * @param bool|int $quantity Quantity.
		 *
		 * @return string
		 * @since 2.0.5
		 */
		public function get_name_with_quantity( $quantity = false ) {
			if ( $this->is_quantity_enabled() && false !== $quantity ) {
				$name = sprintf( '%s (x %s)', $this->get_name(), $quantity );
			} else {
				$name = $this->get_name();
			}

			return apply_filters( 'yith_wcbk_get_name_with_quantity', $name, $this );
		}

		/**
		 * Get the price HTML of the current service
		 *
		 * @param int $person_type Person type ID.
		 *
		 * @return string
		 */
		public function get_price_html( $person_type = 0 ) {
			return wc_price( $this->get_price( $person_type ) );
		}

		/**
		 * Fill the default metadata with the post meta stored in db
		 *
		 * @return array
		 */
		public function get_meta() {
			$meta = array();
			foreach ( self::get_default_meta_data() as $key => $value ) {
				$meta[ $key ] = $this->$key;
			}

			return $meta;
		}

		/**
		 * Return an array of all custom fields
		 *
		 * @return array
		 */
		public static function get_default_meta_data() {
			return array(
				'price'                  => '',
				'optional'               => 'no',
				'hidden'                 => 'no',
				'hidden_in_search_forms' => 'no',
				'multiply_per_blocks'    => 'no',
				'multiply_per_persons'   => 'no',
				'quantity_enabled'       => 'no',
				'min_quantity'           => '',
				'max_quantity'           => '',
				'price_for_person_types' => array(),
			);
		}

		/**
		 * Get pricing for the service
		 *
		 * @param WC_Product_Booking $product The product.
		 *
		 * @return array
		 */
		public function get_pricing( $product ) {
			$pricing         = array();
			$duration_period = yith_wcbk_format_duration( $product->get_duration(), $product->get_duration_unit(), 'period' );
			if ( $this->is_multiply_per_persons() && $product->has_people_types_enabled() ) {
				foreach ( $product->get_enabled_people_types() as $person_type ) {
					$person_type_id = absint( $person_type['id'] );
					$price          = apply_filters( 'yith_wcbk_booking_service_get_pricing_html_price', $this->get_price( $person_type_id ), $this, $product );
					if ( ! $price ) {
						$price_html = apply_filters( 'yith_wcbk_service_free_text', __( 'Free', 'yith-booking-for-woocommerce' ) );
					} else {
						$price_html = wc_price( yith_wcbk_get_price_to_display( $product, $price ) );
						if ( $this->is_multiply_per_blocks() ) {
							$price_html .= ' / ' . $duration_period;
						}
					}

					$label = yith_wcbk()->person_type_helper->get_person_type_title( $person_type_id );

					$pricing[ 'person-type-' . $person_type_id ] = array(
						'price'      => $price,
						'price_html' => $price_html,
						'display'    => $label . ' ' . $price_html,
					);
				}

				$html_prices        = wp_list_pluck( $pricing, 'price_html' );
				$unique_html_prices = array_unique( $html_prices );
				if ( 1 === count( $unique_html_prices ) ) {
					$singe_pricing = current( $pricing );
					$pricing       = array(
						'price' => array(
							'price'      => $singe_pricing['price'],
							'price_html' => $singe_pricing['price_html'],
							'display'    => $singe_pricing['price_html'],
						),
					);
				}
			} else {
				$price = apply_filters( 'yith_wcbk_booking_service_get_pricing_html_price', $this->get_price(), $this, $product );
				if ( ! $price ) {
					$price_html = apply_filters( 'yith_wcbk_service_free_text', __( 'Free', 'yith-booking-for-woocommerce' ) );
				} else {
					$price_html = wc_price( yith_wcbk_get_price_to_display( $product, $price ) );
					if ( $this->is_multiply_per_blocks() ) {
						$price_html .= ' / ' . $duration_period;
					}
				}
				$pricing['price'] = array(
					'price'      => $price,
					'price_html' => $price_html,
					'display'    => $price_html,
				);
			}

			return $pricing;
		}

		/**
		 * Get the pricing for the services
		 *
		 * @param WC_Product_Booking $product Booking product.
		 *
		 * @return string
		 */
		public function get_pricing_html( $product ) {
			$pricing         = $this->get_pricing( $product );
			$pricing_display = wp_list_pluck( $pricing, 'display' );

			return implode( '<br />', $pricing_display );
		}


		/**
		 * Get the service description
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public function get_description() {
			return apply_filters( 'yith_wcbk_booking_service_get_description', $this->description, $this );
		}

		/**
		 * Get the service description HTML.
		 *
		 * @return string
		 * @since 2.1.27
		 */
		public function get_description_html() {
			return apply_filters( 'yith_wcbk_booking_service_get_description_html', wp_kses_post( wpautop( wptexturize( $this->get_description() ) ) ), $this );
		}


		/**
		 * Get information to show in help_tip
		 *
		 * @param WC_Product_Booking $product Booking product.
		 *
		 * @return string
		 * @since 2.0.0
		 */
		public function get_info( $product ) {
			$info = '';

			if ( yith_wcbk()->settings->get( 'show-service-descriptions', 'no' ) === 'yes' ) {
				$description = $this->get_description_html();
				if ( $description ) {
					$info .= "<div class='yith-wcbk-booking-service__description'>{$description}</div>";
				}
			}

			if ( yith_wcbk()->settings->get( 'show-service-prices', 'no' ) === 'yes' ) {
				$pricing = $this->get_pricing_html( $product );

				$info .= "<div class='yith-wcbk-booking-service__pricing'>{$pricing}</div>";
			}

			return apply_filters( 'yith_wcbk_booking_service_get_info', $info, $this, $product );
		}

		/**
		 * Get info html.
		 *
		 * @param array $args Arguments.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_info_html( $args ) {
			$defaults        = array(
				'product'          => false,
				'show_description' => yith_wcbk()->settings->get( 'show-service-descriptions', 'no' ) === 'yes',
				'show_price'       => yith_wcbk()->settings->get( 'show-service-prices', 'no' ) === 'yes',
				'layout'           => yith_wcbk()->settings->get( 'service-info-layout', 'tooltip' ),
			);
			$args            = wp_parse_args( $args, $defaults );
			$args['service'] = $this;
			$info_html       = '';

			if ( $args['product'] ) {
				$info_html = wc_get_template_html(
					'single-product/add-to-cart/booking-form/services/service-info-' . $args['layout'] . '.php',
					$args,
					'',
					YITH_WCBK_TEMPLATE_PATH
				);
			}

			return $info_html;
		}

		/**
		 * Return a valid quantity
		 *
		 * @param int $qty Quantity.
		 *
		 * @return int
		 */
		public function validate_quantity( $qty ) {
			$qty = absint( $qty );
			$qty = max( $qty, $this->get_min_quantity() );
			if ( $this->get_max_quantity() ) {
				$qty = min( $qty, $this->get_max_quantity() );
			}

			return $qty;
		}
	}
}

if ( ! function_exists( 'yith_get_booking_service' ) ) {
	/**
	 * Get the booking service
	 *
	 * @param int|YITH_WCBK_Service $service Service ID or object.
	 * @param WP_Term               $term    The term.
	 *
	 * @return YITH_WCBK_Service
	 */
	function yith_get_booking_service( $service, $term = null ) {
		if ( $service instanceof YITH_WCBK_Service ) {
			$_service = $service;
		} else {
			$_service = new YITH_WCBK_Service( $service, $term );
		}

		return $_service;
	}
}
