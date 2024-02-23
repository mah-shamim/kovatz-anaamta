<?php
/**
 * Add/Update post type endpoint
 */

class Jet_Engine_CPT_Rest_Add_Post_Type extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'add-post-type';
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

		jet_engine()->cpt->data->set_request( array(
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
			'with_front'            => $this->safe_get( $params, 'advanced_settings', 'with_front' ),
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
		) );

		$post_type_id = jet_engine()->cpt->data->create_item( false );

		return rest_ensure_response( array(
			'success' => ! empty( $post_type_id ),
			'item_id' => $post_type_id,
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

}
