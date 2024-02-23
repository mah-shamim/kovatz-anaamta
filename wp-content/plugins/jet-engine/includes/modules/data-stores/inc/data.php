<?php
namespace Jet_Engine\Modules\Data_Stores;

/**
 * Define Data class
 */
class Data extends \Jet_Engine_Base_Data {

	/**
	 * Table name
	 *
	 * @var string
	 */
	public $table = 'post_types';

	/**
	 * Query arguments
	 *
	 * @var array
	 */
	public $query_args = array(
		'status' => 'data-store',
	);

	/**
	 * Table format
	 *
	 * @var string
	 */
	public $table_format = array( '%s', '%s', '%s', '%s', '%s' );

	/**
	 * Returns blacklisted post types slugs
	 *
	 * @return array
	 */
	public function items_blacklist() {
		return array();
	}

	/**
	 * Returns blacklisted post types slugs
	 *
	 * @return array
	 */
	public function meta_blacklist() {
		return array();
	}

	/**
	 * Prepare post data from request to write into database
	 *
	 * @return array
	 */
	public function sanitize_item_from_request() {

		$request = $this->request;

		$result = array(
			'slug'        => '',
			'status'      => 'data-store',
			'labels'      => array(),
			'args'        => array(),
			'meta_fields' => array(),
		);

		$slug = ! empty( $request['slug'] ) ? $this->sanitize_slug( $request['slug'] ) : false;
		$name = ! empty( $request['name'] ) ? esc_html( $request['name'] ) : false;

		if ( ! $slug ) {
			return false;
		}

		$labels = array(
			'name' => $name,
		);

		$args        = array();
		$ensure_bool = array(
			'can_count',
			'is_user',
			'store_on_view',
		);

		foreach ( $ensure_bool as $key ) {
			$val = ! empty( $request[ $key ] ) ? $request[ $key ] : false;
			$args[ $key ] = filter_var( $val, FILTER_VALIDATE_BOOLEAN );
		}

		$regular_args = array(
			'type' => 'cookies',
			'on_post_type' => '',
			'size' => 0,
		);

		foreach ( $regular_args as $key => $default ) {
			$args[ $key ] = ! empty( $request[ $key ] ) ? $request[ $key ] : $default;
		}

		$result['slug']        = $slug;
		$result['labels']      = $labels;
		$result['args']        = $args;
		$result['meta_fields'] = null;

		return $result;

	}

	/**
	 * Sanitize meta fields
	 *
	 * @param  [type] $meta_fields [description]
	 * @return [type]              [description]
	 */
	public function sanitize_meta_fields( $meta_fields ) {
		return $meta_fields;
	}

	/**
	 * Filter post type for register
	 *
	 * @return array
	 */
	public function filter_item_for_register( $item ) {

		$result = array();

		$args   = maybe_unserialize( $item['args'] );
		$labels = maybe_unserialize( $item['labels'] );

		if ( ! empty( $labels['name'] ) ) {
			$labels['name'] = wp_specialchars_decode( $labels['name'], ENT_QUOTES );
		}

		$result = array_merge( $item, $labels, $args );

		unset( $result['args'] );
		unset( $result['labels'] );
		unset( $result['status'] );
		unset( $result['meta_fields'] );

		return $result;
	}

	/**
	 * Filter post type for edit
	 *
	 * @return array
	 */
	public function filter_item_for_edit( $item ) {
		return $item;
	}

}
