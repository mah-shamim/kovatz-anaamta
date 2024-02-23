<?php
namespace Jet_Engine\Modules\Maps_Listings;

class Lat_Lng {

	public $meta_key          = '_jet_maps_coord';
	public $field_groups      = array();
	public $done              = false;
	public $failures          = array();
	public $current_source    = null;

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'hook_preload' ) );
	}

	/**
	 * Set current source
	 *
	 * @param $source
	 */
	public function set_current_source( $source ) {
		$this->current_source = $source;
	}

	/**
	 * Get source instance
	 *
	 * @return false|Source\Base
	 */
	public function get_source_instance() {

		if ( ! $this->current_source ) {
			return false;
		}

		return Module::instance()->sources->get_source( $this->current_source );
	}

	/**
	 * Hook meta-fields preloading
	 *
	 * @return void
	 */
	public function hook_preload() {

		$preload = Module::instance()->settings->get( 'enable_preload_meta' );

		if ( ! $preload ) {
			return;
		}

		$preload_fields = Module::instance()->settings->get( 'preload_meta' );

		if ( empty( $preload_fields ) ) {
			return;
		}

		$preload_fields = explode( ',', $preload_fields );
		$preload_fields = array_map( 'trim', $preload_fields );

		$sources = Module::instance()->sources->get_sources();

		if ( empty( $sources ) ) {
			return;
		}

		foreach ( $sources as $source ) {
			$source->preload_hooks( $preload_fields );
		}

	}

	/**
	 * Get address value from post object and field name
	 *
	 * @param object $post  Post object.
	 * @param string $field Field name.
	 *
	 * @return mixed
	 */
	public function get_address_from_field( $post, $field ) {

		$source = $this->get_source_instance();

		if ( $source ) {
			return $source->get_field_value( $post, $field );
		}

		// For backward compatibility.
		return apply_filters( 'jet-engine/maps-listing/get-address-from-field', false, $post, $field );
	}

	/**
	 * Get address string from post object and field names array
	 *
	 * @param object $post   Post object.
	 * @param array  $fields Fields array.
	 *
	 * @return bool|string
	 */
	public function get_address_from_fields_group( $post = null, $fields = array() ) {

		$group = array();

		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return false;
		}

		foreach ( $fields as $field ) {
			if ( ! empty( $_POST[ $field ] ) ) {
				$group[] = $_POST[ $field ];
			} else {
				$group[] = $this->get_address_from_field( $post, $field );
			}
		}

		$group = array_filter( $group );

		if ( empty( $group ) ) {
			return false;
		} else {
			return implode( ', ', $group );
		}

	}

	/**
	 * Preload fields groups
	 */
	public function preload_groups( $post_id ) {

		if ( $this->done ) {
			return;
		}

		$group = false;
		$post  = false;

		$source = $this->get_source_instance();

		if ( $source ) {
			$group = $source->get_field_groups();
			$post  = $source->get_obj_by_id( $post_id );
		}

		if ( empty( $group ) || empty( $post ) ) {
			return;
		}

		foreach ( $group as $fields ) {

			$address = $this->get_address_from_fields_group( $post, $fields );

			if ( ! $address ) {
				continue;
			}

			$coord = $this->get( $post, $address, implode( '+', $fields ) );

		}

		$this->done = true;

	}

	/**
	 * Preload field address
	 *
	 * @param  int    $post_id
	 * @param  string $address
	 * @return void
	 */
	public function preload( $post_id, $address, $field = '' ) {

		if ( empty( $address ) ) {
			return;
		}

		$post   = false;
		$source = $this->get_source_instance();

		if ( $source ) {
			$post = $source->get_obj_by_id( $post_id );
		}

		$coord = $this->get( $post, $address, $field );
	}

	/**
	 * Returns remote coordinates by location
	 *
	 * @param  [type] $location [description]
	 * @return [type]           [description]
	 */
	public function get_remote( $location ) {

		$provider_id      = Module::instance()->settings->get( 'geocode_provider' );
		$geocode_provider = Module::instance()->providers->get_providers( 'geocode', $provider_id );

		$decoded_location = json_decode( htmlspecialchars_decode( $location ), true );

		if ( $decoded_location && $decoded_location['lat'] && $decoded_location['lng'] ) {
			return $decoded_location;
		}

		if ( ! $geocode_provider ) {
			return false;
		} else {
			return $geocode_provider->get_location_data( $location );
		}

	}

	/**
	 * Get not-post related coordinates
	 *
	 * @param  [type] $location [description]
	 * @return [type]           [description]
	 */
	public function get_from_transient( $location ) {

		$key   = md5( $location );
		$coord = get_transient( $key );

		if ( ! $coord ) {

			$coord = $this->get_remote( $location );

			if ( $coord ) {
				set_transient( $key, $coord, WEEK_IN_SECONDS );
			}

		}

		return is_array( $coord ) ? array_map( 'floatval', $coord ) : $coord;

	}

	/**
	 * Prints failures message
	 */
	public function failures_message() {

		if ( empty( $this->failures ) ) {
			return;
		}

		if ( 5 <= count( $this->failures ) ) {
			$message = __( 'We can`t get coordinates for multiple locations', 'jet-engine' );
		} else {

			$locations = array();

			foreach ( $this->failures as $key => $location ) {
				$locations[] = sprintf( '%1$s (%2$s)', $location, $key );
			}

			$message = __( 'We can`t get coordinates for locations: ', 'jet-engine' ) . implode( ', ', $locations );

		}

		$message .= __( '. Please check your API key (you can validate it in maps settings or check in Google Console), make sure Geocoding API is enabled.', 'jet-engine' );

		return sprintf( '<div style="border: 1px solid #f00; color: #f00;  padding: 20px; margin: 10px 0;">%s</div>', $message );

	}

	public function maybe_add_offset( $coordinates = array() ) {

		$add_offset = Module::instance()->settings->get( 'add_offset' );

		if ( ! $add_offset ) {
			return $coordinates;
		}

		$offset_rate = apply_filters( 'jet-engine/maps-listing/offset-rate', 100000 );

		$offset_lat = ( 10 - rand( 0, 20 ) ) / $offset_rate;
		$offset_lng = ( 10 - rand( 0, 20 ) ) / $offset_rate;

		if ( isset( $coordinates['lat'] ) ) {
			$coordinates['lat'] = floatval( $coordinates['lat'] ) + $offset_lat;
		}

		if ( isset( $coordinates['lng'] ) ) {
			$coordinates['lng'] = floatval( $coordinates['lng'] ) + $offset_lng;
		}

		return $coordinates;

	}

	/**
	 * Returns lat and lang for passed address
	 *
	 * @param  int|object $post     Post ID or object
	 * @param  string     $location Location
	 *
	 * @return array|bool
	 */
	public function get( $post, $location, $field_name = '' ) {

		if ( is_array( $location ) ) {
			return $this->maybe_add_offset( $location );
		}

		$location_hash = md5( $location );
		$source        = $this->get_source_instance();

		if ( ! $source ) {
			return false;
		}

		$meta = $source->get_field_coordinates( $post, $location, $field_name );

		if ( ! empty( $meta ) && $location_hash === $meta['key'] ) {
			return $this->maybe_add_offset( $meta['coord'] );
		}

		$coord = $this->get_remote( $location );

		if ( ! $coord ) {
			if ( $location ) {
				$this->add_failure( $post, $location );
			}
			return false;
		}

		if ( ! $field_name ) {
			$field_name = $this->meta_key;
		}

		$this->update_address_coord_field( $post, $field_name, $location_hash, $coord );

		return $this->maybe_add_offset( $coord );

	}

	public function add_failure( $post, $location ) {

		$key    = false;
		$source = $this->get_source_instance();

		if ( $source ) {
			$key = $source->get_failure_key( $post );
		}

		// For backward compatibility.
		if ( ! $key ) {
			$key = apply_filters( 'jet-engine/maps-listing/failure-message-key', $key, $post );
		}

		if ( ! $key ) {
			return;
		}

		$this->failures[ $key ] = $location;
	}

	public function update_address_coord_field( $post, $field_name, $location_hash, $coord ) {

		$value = array(
			'key'   => $location_hash,
			'coord' => $coord,
		);

		$source = $this->get_source_instance();

		if ( $source ) {
			$source->update_field_value( $post, $field_name, $value );
			return;
		}

		// For backward compatibility.
		do_action( 'jet-engine/maps-listings/update-address-coord-field', $post, $value, $this );
	}

}
