<?php
/**
 * Class YITH_TRS_Transaction_Data_Store
 *
 * Data store
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @package YITH\Booking
 */

defined( 'ABSPATH' ) || exit();

/**
 * Class YITH_WCBK_Data_Store_WP
 *
 * @since 3.0.0
 */
class YITH_WCBK_Data_Store_WP {

	/**
	 * Meta type. Available values: 'post' and 'term'.
	 *
	 * @var string
	 */
	protected $meta_type = 'post';

	/**
	 * Array for mapping meta_keys to props
	 *
	 * @var array
	 */
	protected $meta_key_to_props = array();

	/**
	 * This only needs set if you are using a custom metadata type
	 *
	 * @var string
	 */
	protected $object_id_field_for_meta = '';

	/**
	 * This only needs set if you are using a custom table for meta
	 *
	 * @var string
	 */
	protected $table_for_meta = '';

	/**
	 * This only needs set if you are using a custom table for meta, so the meta id field will be custom
	 *
	 * @var array
	 */
	protected $meta_id_field_for_meta = '';

	/**
	 * Prefix key with _
	 *
	 * @param string $key The key.
	 *
	 * @return string
	 */
	protected function prefix_key( $key ) {
		return '_' === substr( $key, 0, 1 ) ? $key : '_' . $key;
	}

	/**
	 * Callback to exclude internal meta
	 *
	 * @param object $meta Meta object to check if it should be excluded or not.
	 *
	 * @return bool
	 */
	protected function exclude_internal_meta( $meta ) {
		return ! in_array( $meta->meta_key, $this->get_internal_meta_keys(), true );
	}

	/**
	 * Table structure is slightly different between meta types, this function will return what we need to know.
	 *
	 * @return array Array elements: table, object_id_field, meta_id_field
	 */
	protected function get_db_info() {
		global $wpdb;

		$meta_id_field   = 'meta_id';
		$table           = $wpdb->prefix . $this->meta_type . 'meta';
		$object_id_field = $this->meta_type . '_id';

		if ( ! empty( $this->object_id_field_for_meta ) ) {
			$object_id_field = $this->object_id_field_for_meta;
		}

		if ( ! empty( $this->table_for_meta ) ) {
			$table = $wpdb->prefix . $this->table_for_meta;
		}

		if ( ! empty( $this->meta_id_field_for_meta ) ) {
			$table = $this->meta_id_field_for_meta;
		}

		return array(
			'table'           => $table,
			'object_id_field' => $object_id_field,
			'meta_id_field'   => $meta_id_field,
		);
	}

	/**
	 * Returns an array of meta for an object.
	 *
	 * @param YITH_WCBK_Data $object The object.
	 *
	 * @return array
	 */
	public function read_meta( &$object ) {
		global $wpdb;
		$db_info = $this->get_db_info();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		$raw_meta_data = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT %s as meta_id, meta_key, meta_value
				FROM %s
				WHERE %s = %d
				ORDER BY %s',
				$db_info['meta_id_field'],
				$db_info['table'],
				$db_info['object_id_field'],
				$object->get_id(),
				$db_info['meta_id_field']
			)
		);

		$meta_data = array_filter( $raw_meta_data, array( $this, 'exclude_internal_meta' ) );

		return apply_filters( "yith_wcbk_data_store_wp_{$this->meta_type}_read_meta", $meta_data, $object, $this );
	}

	/**
	 * Deletes meta based on meta ID.
	 *
	 * @param YITH_WCBK_Data $object YITH_WCBK_Data object.
	 * @param stdClass       $meta   (containing at least ->id).
	 */
	public function delete_meta( &$object, $meta ) {
		delete_metadata_by_mid( $this->meta_type, $meta->id );
	}

	/**
	 * Add new piece of meta.
	 *
	 * @param YITH_WCBK_Data $object YITH_WCBK_Data object.
	 * @param stdClass       $meta   (containing ->key and ->value).
	 *
	 * @return int meta ID
	 */
	public function add_meta( &$object, $meta ) {
		return add_metadata( $this->meta_type, $object->get_id(), wp_slash( $meta->key ), is_string( $meta->value ) ? wp_slash( $meta->value ) : $meta->value, false );
	}

	/**
	 * Update meta.
	 *
	 * @param YITH_WCBK_Data $object YITH_WCBK_Data object.
	 * @param stdClass       $meta   (containing ->id, ->key and ->value).
	 */
	public function update_meta( &$object, $meta ) {
		update_metadata_by_mid( $this->meta_type, $meta->id, $meta->value, $meta->key );
	}

	/**
	 * Get the props to update
	 *
	 * @param YITH_WCBK_Data $object    The object.
	 * @param string         $meta_type The meta type.
	 *
	 * @return array
	 */
	protected function get_props_to_update( $object, $meta_type = 'post' ) {

		$props_to_update = array();
		$changed_props   = $object->get_changes();

		// Props should be updated if they are a part of the $changed array or don't exist yet.
		foreach ( $this->meta_key_to_props as $meta_key => $prop ) {
			if ( array_key_exists( $prop, $changed_props ) || ! metadata_exists( $meta_type, $object->get_id(), $meta_key ) ) {
				$props_to_update[ $meta_key ] = $prop;
			}
		}

		return $props_to_update;
	}

	/**
	 * Return list of internal meta keys.
	 *
	 * @return array
	 */
	public function get_internal_meta_keys() {
		return array_keys( $this->meta_key_to_props );
	}

	/**
	 * Get and store terms from a taxonomy.
	 *
	 * @param YITH_WCBK_Data|integer $object   YITH_WCBK_Data object or object ID.
	 * @param string                 $taxonomy Taxonomy name e.g. product_cat.
	 *
	 * @return array of terms
	 */
	protected function get_term_ids( $object, $taxonomy ) {
		if ( is_numeric( $object ) ) {
			$object_id = $object;
		} else {
			$object_id = $object->get_id();
		}
		$terms = get_the_terms( $object_id, $taxonomy );
		if ( false === $terms || is_wp_error( $terms ) ) {
			return array();
		}

		return wp_list_pluck( $terms, 'term_id' );
	}
}
