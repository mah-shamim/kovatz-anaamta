<?php
/**
 * Add/Update post type endpoint
 */

class Jet_Engine_CPT_Rest_Edit_BI_Post_Type extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'edit-built-in-post-type';
	}

	public function safe_get( $args = array(), $group = '', $key = '', $default = false ) {
		return isset( $args[ $group ][ $key ] ) ? $args[ $group ][ $key ] : $default;
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params = $request->get_params();

		if ( empty( $params['post_type'] ) ) {

			jet_engine()->cpt->add_notice(
				'error',
				__( 'Post type not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->cpt->get_notices(),
			) );

		}

		$post_type = esc_attr( $params['post_type'] );

		$request_data = array(
			'id'                    => $this->safe_get( $params, 'general_settings', 'id' ),
			'name'                  => $this->safe_get( $params, 'general_settings', 'name' ),
			'slug'                  => $this->safe_get( $params, 'general_settings', 'slug' ),
			'show_edit_link'        => $this->safe_get( $params, 'general_settings', 'show_edit_link' ),
			'hide_field_names'      => $this->safe_get( $params, 'general_settings', 'hide_field_names' ),
			'singular_name'         => $this->safe_get( $params, 'labels', 'singular_name' ),
			'menu_name'             => $this->safe_get( $params, 'labels', 'menu_name' ),
			'name_admin_bar'        => $this->safe_get( $params, 'labels', 'name_admin_bar' ),
			'add_new'               => $this->safe_get( $params, 'labels', 'add_new' ),
			'add_new_item'          => $this->safe_get( $params, 'labels', 'add_new_item' ),
			'new_item'              => $this->safe_get( $params, 'labels', 'new_item' ),
			'edit_item'             => $this->safe_get( $params, 'labels', 'edit_item' ),
			'view_item'             => $this->safe_get( $params, 'labels', 'view_item' ),
			'all_items'             => $this->safe_get( $params, 'labels', 'all_items' ),
			'search_items'          => $this->safe_get( $params, 'labels', 'search_items' ),
			'parent_item_colon'     => $this->safe_get( $params, 'labels', 'parent_item_colon' ),
			'not_found'             => $this->safe_get( $params, 'labels', 'not_found' ),
			'not_found_in_trash'    => $this->safe_get( $params, 'labels', 'not_found_in_trash' ),
			'featured_image'        => $this->safe_get( $params, 'labels', 'featured_image' ),
			'set_featured_image'    => $this->safe_get( $params, 'labels', 'set_featured_image' ),
			'remove_featured_image' => $this->safe_get( $params, 'labels', 'remove_featured_image' ),
			'use_featured_image'    => $this->safe_get( $params, 'labels', 'use_featured_image' ),
			'archives'              => $this->safe_get( $params, 'labels', 'archives' ),
			'insert_into_item'      => $this->safe_get( $params, 'labels', 'insert_into_item' ),
			'uploaded_to_this_item' => $this->safe_get( $params, 'labels', 'uploaded_to_this_item' ),
			'public'                => $this->safe_get( $params, 'advanced_settings', 'public' ),
			'exclude_from_search'   => $this->safe_get( $params, 'advanced_settings', 'exclude_from_search' ),
			'publicly_queryable'    => $this->safe_get( $params, 'advanced_settings', 'publicly_queryable' ),
			'show_ui'               => $this->safe_get( $params, 'advanced_settings', 'show_ui' ),
			'show_in_menu'          => $this->safe_get( $params, 'advanced_settings', 'show_in_menu' ),
			'show_in_nav_menus'     => $this->safe_get( $params, 'advanced_settings', 'show_in_nav_menus' ),
			'show_in_rest'          => $this->safe_get( $params, 'advanced_settings', 'show_in_rest' ),
			'query_var'             => $this->safe_get( $params, 'advanced_settings', 'query_var' ),
			'rewrite'               => $this->safe_get( $params, 'advanced_settings', 'rewrite' ),
			'map_meta_cap'          => $this->safe_get( $params, 'advanced_settings', 'map_meta_cap' ),
			'has_archive'           => $this->safe_get( $params, 'advanced_settings', 'has_archive' ),
			'hierarchical'          => $this->safe_get( $params, 'advanced_settings', 'hierarchical' ),
			'rewrite_slug'          => $this->safe_get( $params, 'advanced_settings', 'rewrite_slug' ),
			'capability_type'       => $this->safe_get( $params, 'advanced_settings', 'capability_type' ),
			'menu_position'         => $this->safe_get( $params, 'advanced_settings', 'menu_position' ),
			'menu_icon'             => $this->safe_get( $params, 'advanced_settings', 'menu_icon' ),
			'supports'              => $this->safe_get( $params, 'advanced_settings', 'supports', array() ),
			'admin_columns'         => ! empty( $params['admin_columns'] ) ? $params['admin_columns'] : array(),
			'admin_filters'         => ! empty( $params['admin_filters'] ) ? $params['admin_filters'] : array(),
			'meta_fields'           => ! empty( $params['meta_fields'] ) ? $params['meta_fields'] : array(),
		);

		$default_data = jet_engine()->cpt->data->get_default_built_in_post_type( $post_type );

		if ( ! $default_data ) {

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->cpt->get_notices(),
			) );

		}

		$to_update = array();

		$raw_default = array(
			'admin_columns' => array(),
			'admin_filters' => array(),
			'meta_fields'   => array(),
		);

		if ( isset( $default_data['advanced_settings']['rewrite'] ) && is_array( $default_data['advanced_settings']['rewrite'] ) ) {
			$default_data['advanced_settings']['rewrite_slug'] = $default_data['advanced_settings']['rewrite']['slug'];
			$default_data['advanced_settings']['rewrite']      = true;
		} else {
			$default_data['advanced_settings']['rewrite_slug'] = '';
		}

		$raw_default = array_merge(
			$raw_default,
			$default_data['general_settings'],
			$default_data['advanced_settings'],
			$default_data['labels']
		);

		// Changing post type slug not permitted
		unset( $request_data['slug'] );

		foreach ( $request_data as $key => $value ) {

			if ( 'id' === $key ) {
				continue;
			}

			if ( in_array( $key, array( 'admin_columns', 'admin_filters', 'meta_fields' ) ) && ! empty( $value ) ) {
				$to_update[ $key ] = $value;
				continue;
			}

			if ( ! isset( $raw_default[ $key ] ) && ! empty( $value ) ) {
				$to_update[ $key ] = $value;
				continue;
			}

			if ( $value != $raw_default[ $key ] ) {
				$to_update[ $key ] = $value;
			}

		}

		if ( ! empty( $to_update ) ) {

			$to_update['slug'] = $post_type;

			if ( ! empty( $request_data['id'] ) ) {
				$to_update['id'] = $request_data['id'];
			}

			jet_engine()->cpt->data->set_request( $to_update );
			$item_id = jet_engine()->cpt->data->edit_built_in_item( false );

			if ( $item_id ) {
				$updated = true;
			} else {
				$updated = false;
			}

		} else {
			jet_engine()->cpt->data->reset_built_in_post_type( $post_type );
			$updated = true;
			$item_id = false;
		}

		return rest_ensure_response( array(
			'success' => $updated,
			'item_id' => $item_id,
			'notices' => jet_engine()->cpt->get_notices(),
		) );

	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELTE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'POST';
	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get query param. Regex with query parameters
	 *
	 * @return string
	 */
	public function get_query_params() {
		return '(?P<post_type>[a-z\-\_\d]+)';
	}

}
