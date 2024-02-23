<?php
namespace Jet_Engine\Query_Builder;
/**
 * Glossaries data controller class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Options_Data class
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
		'status' => 'query',
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
	 * Sanitizr post type request
	 *
	 * @return void
	 */
	public function sanitize_item_request() {
		return true;
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
			'status'      => 'query',
			'labels'      => array(),
			'args'        => array(),
			'meta_fields' => array(),
		);

		$name = ! empty( $request['name'] ) ? sanitize_text_field( $request['name'] ) : 'Untitled query';

		$labels = array(
			'name' => $name,
		);

		// Sanitize arguments
		$args          = array();
		$request_args  = ! empty( $request['args'] ) ? $request['args'] : array();
		$q_type        = ! empty( $request_args['query_type'] ) ? $request_args['query_type'] : false;
		$allowed_types = array_keys( Manager::instance()->editor->get_types() );

		if ( in_array( $q_type, $allowed_types ) ) {
			$dynamic = '__dynamic_';
			$args['query_type'] = $q_type;
			$args[ $q_type ] = isset( $request_args[ $q_type ] ) ? $request_args[ $q_type ] : array();
			$args[ $dynamic . $q_type ] = isset( $request_args[ $dynamic . $q_type ] ) ? $request_args[ $dynamic . $q_type ] : array();
		}

		$bool_args = array(
			'show_preview',
			'cache_query',
		);

		foreach ( $bool_args as $key ) {
			$value = isset( $request_args[ $key ] ) ? $request_args[ $key ] : false;
			$args[ $key ] = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
		}

		$regular_args = array(
			'preview_page',
			'preview_page_title',
			'preview_query_string',
			'query_id',
			'description',
		);

		foreach ( $regular_args as $key ) {
			$args[ $key ] = isset( $request_args[ $key ] ) ? $request_args[ $key ] : null;
		}

		$result['slug']   = null;
		$result['labels'] = $labels;
		$result['args']   = $args;

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

		$result       = array();
		$args         = maybe_unserialize( $item['args'] );
		$labels       = maybe_unserialize( $item['labels'] );
		$args['name'] = $labels['name'];
		$result       = array_merge( $item, $args );

		// Set default value for `cache_query` setting if setting is not existing.
		if ( ! isset( $result['cache_query'] ) ) {
			$result['cache_query'] = true;
		}

		unset( $result['args'] );
		unset( $result['labels'] );
		unset( $result['status'] );
		unset( $result['slug'] );
		unset( $result['meta_fields'] );

		return $result;

	}

	/**
	 * Filter post type for edit
	 *
	 * @return array
	 */
	public function filter_item_for_edit( $item ) {
		return $this->filter_item_for_register( $item );
	}

}
