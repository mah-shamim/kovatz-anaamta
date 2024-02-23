<?php
/**
 * Class YITH_WCBK_Product_Booking_Data_Store_CPT
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * YITH Booking Product Data Store: Stored in CPT.
 *
 * @since  2.1
 * @author Leanza Francesco <leanzafrancesco@gmail.com>
 */
class YITH_WCBK_Product_Booking_Data_Store_CPT extends WC_Product_Data_Store_CPT implements WC_Object_Data_Store_Interface {
	// phpcs:disable WordPress.Arrays.MultipleStatementAlignment

	/**
	 * Meta keys and how they transfer to CRUD props.
	 *
	 * @var array
	 */
	protected $booking_meta_key_to_props = array(
		// ------ Booking Settings --------------------------------------------------
		'_yith_booking_duration_type'                               => 'duration_type',
		'_yith_booking_duration'                                    => 'duration',
		'_yith_booking_duration_unit'                               => 'duration_unit',
		'_yith_booking_enable_calendar_range_picker'                => 'enable_calendar_range_picker',
		'_yith_booking_default_start_date'                          => 'default_start_date',
		'_yith_booking_default_start_date_custom'                   => 'default_start_date_custom',
		'_yith_booking_default_start_time'                          => 'default_start_time',
		'_yith_booking_all_day'                                     => 'full_day',
		'_yith_booking_location'                                    => 'location',
		'_yith_booking_location_lat'                                => 'location_latitude',
		'_yith_booking_location_lng'                                => 'location_longitude',

		// ------ Booking Availability --------------------------------------------------
		'_yith_booking_max_per_block'                               => 'max_bookings_per_unit',
		'_yith_booking_minimum_duration'                            => 'minimum_duration',
		'_yith_booking_maximum_duration'                            => 'maximum_duration',
		'_yith_booking_request_confirmation'                        => 'confirmation_required',
		'_yith_booking_can_be_cancelled'                            => 'cancellation_available',
		'_yith_booking_cancelled_duration'                          => 'cancellation_available_up_to',
		'_yith_booking_cancelled_unit'                              => 'cancellation_available_up_to_unit',
		'_yith_booking_checkin'                                     => 'check_in',
		'_yith_booking_checkout'                                    => 'check_out',
		'_yith_booking_allowed_start_days'                          => 'allowed_start_days',
		'_yith_booking_daily_start_time'                            => 'daily_start_time',
		'_yith_booking_buffer'                                      => 'buffer',
		'_yith_booking_time_increment_based_on_duration'            => 'time_increment_based_on_duration',
		'_yith_booking_time_increment_including_buffer'             => 'time_increment_including_buffer',
		'_yith_booking_allow_after'                                 => 'minimum_advance_reservation',
		'_yith_booking_allow_after_unit'                            => 'minimum_advance_reservation_unit',
		'_yith_booking_allow_until'                                 => 'maximum_advance_reservation',
		'_yith_booking_allow_until_unit'                            => 'maximum_advance_reservation_unit',
		'_yith_booking_availability_range'                          => 'availability_rules',
		'_yith_booking_default_availabilities'                      => 'default_availabilities',

		// ------ Booking Prices --------------------------------------------------
		'_yith_booking_block_cost'                                  => 'base_price',
		'_yith_booking_multiply_base_price_by_number_of_people'     => 'multiply_base_price_by_number_of_people',
		'_yith_booking_extra_price_per_person'                      => 'extra_price_per_person',
		'_yith_booking_extra_price_per_person_greater_than'         => 'extra_price_per_person_greater_than',
		'_yith_booking_weekly_discount'                             => 'weekly_discount',
		'_yith_booking_monthly_discount'                            => 'monthly_discount',
		'_yith_booking_last_minute_discount'                        => 'last_minute_discount',
		'_yith_booking_last_minute_discount_days_before_arrival'    => 'last_minute_discount_days_before_arrival',
		'_yith_booking_base_cost'                                   => 'fixed_base_fee',
		'_yith_booking_multiply_fixed_base_fee_by_number_of_people' => 'multiply_fixed_base_fee_by_number_of_people',
		'_yith_booking_extra_costs'                                 => 'extra_costs',
		'_yith_booking_costs_range'                                 => 'price_rules',

		// ------ Booking People --------------------------------------------------
		'_yith_booking_has_persons'                                 => 'enable_people',
		'_yith_booking_min_persons'                                 => 'minimum_number_of_people',
		'_yith_booking_max_persons'                                 => 'maximum_number_of_people',
		'_yith_booking_count_persons_as_bookings'                   => 'count_people_as_separate_bookings',
		'_yith_booking_enable_person_types'                         => 'enable_people_types',
		'_yith_booking_person_types'                                => 'people_types',

		// ------ Booking Services --------------------------------------------------
		// '_yith_booking_services'                         => 'service_ids' |  This is stored as term

		// ------ Booking Sync --------------------------------------------------
		'_yith_booking_external_calendars'                          => 'external_calendars',
		'_yith_booking_external_calendars_key'                      => 'external_calendars_key',
		'_yith_booking_external_calendars_last_sync'                => 'external_calendars_last_sync',
	);

	// phpcs:enable

	/**
	 * Boolean props
	 *
	 * @var array
	 */
	private $booking_boolean_props = array(
		'enable_calendar_range_picker',
		'full_day',
		'confirmation_required',
		'cancellation_available',
		'time_increment_based_on_duration',
		'time_increment_including_buffer',
		'multiply_base_price_by_number_of_people',
		'multiply_fixed_base_fee_by_number_of_people',
		'enable_people',
		'count_people_as_separate_bookings',
		'enable_people_types',
	);

	/**
	 * Array props
	 *
	 * @var array
	 */
	private $booking_array_props = array(
		'allowed_start_days',
		'availability_rules',
		'people_types',
		'price_rules',
		'external_calendars',
		'extra_costs',
		'default_availabilities',
	);

	/**
	 * Simple object props
	 *
	 * @var array
	 */
	private $booking_simple_object_array_props = array(
		'availability_rules',
		'price_rules',
		'extra_costs',
		'default_availabilities',
	);

	/**
	 * YITH_WCBK_Product_Booking_Data_Store_CPT constructor.
	 */
	public function __construct() {
		if ( is_callable( 'parent::__construct' ) ) {
			parent::__construct();
		}

		$this->internal_meta_keys = array_merge( $this->internal_meta_keys, array_keys( $this->booking_meta_key_to_props ) );
	}

	/**
	 * Force meta values on save.
	 *
	 * @param WC_Product_Booking $product The booking product.
	 */
	protected function force_meta_values( &$product ) {
		$product->set_regular_price( '' );
		$product->set_sale_price( '' );
		$product->set_manage_stock( false );
		$product->set_stock_status( 'instock' );

		$this->sync_booking_price( $product );
	}

	/**
	 * Method to create a new product in the database.
	 *
	 * @param WC_Product_Booking $product The booking product.
	 */
	public function create( &$product ) {
		parent::create( $product );
		$this->force_meta_values( $product );
	}

	/**
	 * Method to update a product in the database.
	 *
	 * @param WC_Product_Booking $product The booking product.
	 */
	public function update( &$product ) {
		parent::update( $product );
		$this->force_meta_values( $product );
	}

	/**
	 * Helper method that updates all the post meta for a product based on it's settings in the WC_Product class.
	 *
	 * @param WC_Product $product The booking product.
	 * @param bool       $force   Force all props to be written even if not changed. This is used during creation.
	 *
	 * @since 3.0.0
	 */
	public function update_post_meta( &$product, $force = false ) {
		parent::update_post_meta( $product, $force );

		$props_to_update = $force ? $this->booking_meta_key_to_props : $this->get_props_to_update( $product, $this->booking_meta_key_to_props );

		foreach ( $props_to_update as $meta_key => $prop ) {
			if ( is_callable( array( $product, "get_$prop" ) ) ) {
				$value = $product->{"get_$prop"}( 'edit' );
				if ( $this->is_boolean_prop( $prop ) ) {
					$updated = update_post_meta( $product->get_id(), $meta_key, wc_bool_to_string( $value ) );
				} elseif ( $this->is_simple_object_array_prop( $prop ) ) {
					$updated = update_post_meta( $product->get_id(), $meta_key, yith_wcbk_simple_objects_to_array( $value ) );
				} else {
					$updated = update_post_meta( $product->get_id(), $meta_key, $value );
				}

				if ( $updated ) {
					$this->updated_props[] = $prop;
				}
			}
		}
	}

	/**
	 * Read product data. Can be overridden by child classes to load other props.
	 *
	 * @param WC_Product $product The booking product.
	 */
	public function read_product_data( &$product ) {
		parent::read_product_data( $product );

		$props_to_set = array();

		// Convert "multiply costs by persons" in two different fields.
		if ( metadata_exists( 'post', $product->get_id(), '_yith_booking_multiply_costs_by_persons' ) ) {
			$value = get_post_meta( $product->get_id(), '_yith_booking_multiply_costs_by_persons', true );
			update_post_meta( $product->get_id(), '_yith_booking_multiply_base_price_by_number_of_people', $value );
			update_post_meta( $product->get_id(), '_yith_booking_multiply_fixed_base_fee_by_number_of_people', $value );
			delete_post_meta( $product->get_id(), '_yith_booking_multiply_costs_by_persons' );
		}

		foreach ( $this->booking_meta_key_to_props as $meta_key => $prop ) {
			if ( metadata_exists( 'post', $product->get_id(), $meta_key ) ) {
				$value = get_post_meta( $product->get_id(), $meta_key, true );

				$props_to_set[ $prop ] = $this->is_boolean_prop( $prop ) ? wc_string_to_bool( $value ) : $value;
			}
		}

		$props_to_set['service_ids'] = $this->get_term_ids( $product, YITH_WCBK_Post_Types::SERVICE_TAX );

		$product->set_props( $props_to_set );
	}

	/**
	 * For all stored terms in all taxonomies, save them to the DB.
	 *
	 * @param WC_Product_Booking $product Product object.
	 * @param bool               $force   Force update. Used during create.
	 *
	 * @since 3.0.0
	 */
	protected function update_terms( &$product, $force = false ) {
		parent::update_terms( $product, $force );

		$changes = $product->get_changes();

		if ( $force || array_key_exists( 'service_ids', $changes ) ) {
			wp_set_post_terms( $product->get_id(), $product->get_service_ids( 'edit' ), YITH_WCBK_Post_Types::SERVICE_TAX, false );
		}
	}

	/**
	 * Handle updated meta props after updating meta data.
	 *
	 * @param WC_Product_Booking $product Product Object.
	 *
	 * @since 3.0.0
	 */
	protected function handle_updated_props( &$product ) {
		if (
			in_array( 'location', $this->updated_props, true ) ||
			( $product->get_location( 'edit' ) && ( ! $product->get_location_latitude( 'edit' ) || ! $product->get_location_longitude( 'edit' ) ) )
		) {
			$location  = $product->get_location( 'edit' );
			$latitude  = '';
			$longitude = '';
			if ( $location ) {
				$coordinates = yith_wcbk()->maps->get_location_by_address( $location );
				if ( isset( $coordinates['lat'] ) && isset( $coordinates['lng'] ) ) {
					$latitude  = $coordinates['lat'];
					$longitude = $coordinates['lng'];
				}
			}

			update_post_meta( $product->get_id(), '_yith_booking_location_lat', $latitude );
			update_post_meta( $product->get_id(), '_yith_booking_location_lng', $longitude );
			$product->set_location_latitude( $latitude );
			$product->set_location_longitude( $longitude );
		}

		if ( in_array( 'external_calendars', $this->updated_props, true ) ) {
			yith_wcbk_booking_externals()->delete_externals_from_product_id( $product->get_id() );
			yith_wcbk_product_delete_external_calendars_last_sync( $product );
		}

		if ( in_array( 'duration_type', $this->updated_props, true ) && 'fixed' === $product->get_duration_type() ) {
			update_post_meta( $product->get_id(), '_yith_booking_maximum_duration', 1 );
			$product->set_maximum_duration( 1 );
		}

		parent::handle_updated_props( $product );
	}

	/**
	 * Check if a prop is boolean.
	 *
	 * @param string $prop The property.
	 *
	 * @return bool
	 */
	public function is_boolean_prop( $prop ) {
		return in_array( $prop, $this->booking_boolean_props, true );
	}

	/**
	 * Check if a prop is array
	 *
	 * @param string $prop The property.
	 *
	 * @return bool
	 */
	public function is_array_prop( $prop ) {
		return in_array( $prop, $this->booking_array_props, true );
	}

	/**
	 * Check if a prop is an array of simple objects
	 *
	 * @param string $prop The property.
	 *
	 * @return bool
	 */
	public function is_simple_object_array_prop( $prop ) {
		return in_array( $prop, $this->booking_simple_object_array_props, true );
	}

	/**
	 * Update the last sync for external calendars
	 *
	 * @param WC_Product_Booking $product   The booking product.
	 * @param int|null           $last_sync The last sync timestamp. Set null for current timestamp.
	 *
	 * @return bool|int
	 */
	public function update_external_calendars_last_sync( $product, $last_sync = null ) {
		$last_sync = ! is_null( $last_sync ) ? $last_sync : time();

		if ( $last_sync ) {
			$success = update_post_meta( $product->get_id(), '_yith_booking_external_calendars_last_sync', $last_sync );
		} else {
			$success = delete_post_meta( $product->get_id(), '_yith_booking_external_calendars_last_sync' );
		}

		return ! ! $success ? $last_sync : false;
	}

	/**
	 * Sync Booking product price
	 *
	 * @param int|WC_Product_Booking $product The booking product.
	 *
	 * @return bool
	 */
	public function sync_booking_price( $product ) {
		$product = wc_get_product( $product );
		if ( $product && $product->is_type( 'booking' ) ) {
			/**
			 * The Booking product
			 *
			 * @var WC_Product_Booking $product
			 */
			do_action( 'yith_wcbk_product_sync_price_before', $product );
			delete_post_meta( $product->get_id(), '_price' );
			$price = $product->get_price_to_store();
			if ( $price ) {
				update_post_meta( $product->get_id(), '_price', $price );
			}

			if ( is_callable( array( $this, 'update_lookup_table' ) ) ) {
				$this->update_lookup_table( $product->get_id(), 'wc_product_meta_lookup' );
			}

			yith_wcbk_maybe_debug( sprintf( 'Sync Product Price #%s', $product->get_id() ) );
			do_action( 'yith_wcbk_product_sync_price_after', $product );
		}

		return false;
	}

	/**
	 * Get booking meta key to props.
	 *
	 * @return array
	 */
	public function get_booking_meta_key_to_props() {
		return $this->booking_meta_key_to_props;
	}
}
