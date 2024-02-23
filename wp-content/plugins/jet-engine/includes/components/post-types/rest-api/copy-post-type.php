<?php
/**
 * Copy post type endpoint
 */

class Jet_Engine_CPT_Rest_Copy_Post_Type extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'copy-post-type';
	}

	public function safe_get( $args = array(), $group = '', $key = '', $default = false ) {
		return isset( $args[ $group ][ $key ] ) ? $args[ $group ][ $key ] : $default;
	}

	/**
	 * API callback
	 *
	 * @param $request
	 * @return void|WP_Error|WP_HTTP_Response|WP_REST_Response
	 */
	public function callback( $request ) {

		$params = $request->get_params();
		$id     = isset( $params['id'] ) ? intval( $params['id'] ) : false;

		if ( ! $id ) {

			jet_engine()->cpt->add_notice(
				'error',
				__( 'Item ID not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->cpt->get_notices(),
			) );

		}

		$post_type_data = jet_engine()->cpt->data->get_item_for_edit( $id );

		if ( ! $post_type_data ) {

			jet_engine()->cpt->add_notice(
				'error',
				__( 'Post type not found', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->cpt->get_notices(),
			) );

		}

		if ( empty( $post_type_data['labels'] ) ) {
			$post_type_data['labels']['singular_name'] = '';
		}

		$name = $this->safe_get( $post_type_data, 'general_settings', 'name' ) . ' (' . esc_html__( 'Copy', 'jet-engine' ) . ')';
		$slug = $this->prepare_slug( $this->safe_get( $post_type_data, 'general_settings', 'slug' ) );

		jet_engine()->cpt->data->set_request( array(
			'name'                  => $name,
			'slug'                  => $slug,
			'show_edit_link'        => $this->safe_get( $post_type_data, 'general_settings', 'show_edit_link' ),
			'hide_field_names'      => $this->safe_get( $post_type_data, 'general_settings', 'hide_field_names' ),
			'singular_name'         => $this->safe_get( $post_type_data, 'labels', 'singular_name' ),
			'menu_name'             => $this->safe_get( $post_type_data, 'labels', 'menu_name' ),
			'name_admin_bar'        => $this->safe_get( $post_type_data, 'labels', 'name_admin_bar' ),
			'add_new'               => $this->safe_get( $post_type_data, 'labels', 'add_new' ),
			'add_new_item'          => $this->safe_get( $post_type_data, 'labels', 'add_new_item' ),
			'new_item'              => $this->safe_get( $post_type_data, 'labels', 'new_item' ),
			'edit_item'             => $this->safe_get( $post_type_data, 'labels', 'edit_item' ),
			'view_item'             => $this->safe_get( $post_type_data, 'labels', 'view_item' ),
			'all_items'             => $this->safe_get( $post_type_data, 'labels', 'all_items' ),
			'search_items'          => $this->safe_get( $post_type_data, 'labels', 'search_items' ),
			'parent_item_colon'     => $this->safe_get( $post_type_data, 'labels', 'parent_item_colon' ),
			'not_found'             => $this->safe_get( $post_type_data, 'labels', 'not_found' ),
			'not_found_in_trash'    => $this->safe_get( $post_type_data, 'labels', 'not_found_in_trash' ),
			'featured_image'        => $this->safe_get( $post_type_data, 'labels', 'featured_image' ),
			'set_featured_image'    => $this->safe_get( $post_type_data, 'labels', 'set_featured_image' ),
			'remove_featured_image' => $this->safe_get( $post_type_data, 'labels', 'remove_featured_image' ),
			'use_featured_image'    => $this->safe_get( $post_type_data, 'labels', 'use_featured_image' ),
			'archives'              => $this->safe_get( $post_type_data, 'labels', 'archives' ),
			'insert_into_item'      => $this->safe_get( $post_type_data, 'labels', 'insert_into_item' ),
			'uploaded_to_this_item' => $this->safe_get( $post_type_data, 'labels', 'uploaded_to_this_item' ),
			'public'                => $this->safe_get( $post_type_data, 'advanced_settings', 'public' ),
			'exclude_from_search'   => $this->safe_get( $post_type_data, 'advanced_settings', 'exclude_from_search' ),
			'publicly_queryable'    => $this->safe_get( $post_type_data, 'advanced_settings', 'publicly_queryable' ),
			'show_ui'               => $this->safe_get( $post_type_data, 'advanced_settings', 'show_ui' ),
			'show_in_menu'          => $this->safe_get( $post_type_data, 'advanced_settings', 'show_in_menu' ),
			'show_in_nav_menus'     => $this->safe_get( $post_type_data, 'advanced_settings', 'show_in_nav_menus' ),
			'show_in_rest'          => $this->safe_get( $post_type_data, 'advanced_settings', 'show_in_rest' ),
			'query_var'             => $this->safe_get( $post_type_data, 'advanced_settings', 'query_var' ),
			'rewrite'               => $this->safe_get( $post_type_data, 'advanced_settings', 'rewrite' ),
			'with_front'            => $this->safe_get( $post_type_data, 'advanced_settings', 'with_front' ),
			'map_meta_cap'          => $this->safe_get( $post_type_data, 'advanced_settings', 'map_meta_cap' ),
			'has_archive'           => $this->safe_get( $post_type_data, 'advanced_settings', 'has_archive' ),
			'hierarchical'          => $this->safe_get( $post_type_data, 'advanced_settings', 'hierarchical' ),
			'rewrite_slug'          => $this->safe_get( $post_type_data, 'advanced_settings', 'rewrite_slug' ),
			'capability_type'       => $this->safe_get( $post_type_data, 'advanced_settings', 'capability_type' ),
			'menu_position'         => $this->safe_get( $post_type_data, 'advanced_settings', 'menu_position' ),
			'menu_icon'             => $this->safe_get( $post_type_data, 'advanced_settings', 'menu_icon' ),
			'supports'              => $this->safe_get( $post_type_data, 'advanced_settings', 'supports', array() ),
			'admin_columns'         => ! empty( $post_type_data['admin_columns'] ) ? $post_type_data['admin_columns'] : array(),
			'admin_filters'         => ! empty( $post_type_data['admin_filters'] ) ? $post_type_data['admin_filters'] : array(),
			'meta_fields'           => ! empty( $post_type_data['meta_fields'] ) ? $post_type_data['meta_fields'] : array(),
		) );

		$post_type_id = jet_engine()->cpt->data->create_item( false );

		return rest_ensure_response( array(
			'success' => ! empty( $post_type_id ),
			'item'    => array(
				'id'     => $post_type_id,
				'slug'   => $slug,
				'labels' => array(
					'name' => $name,
				),
			),
			'notices' => jet_engine()->cpt->get_notices(),
		) );

	}

	/**
	 * Prepare slug.
	 *
	 * @param  string $slug
	 * @return string mixed
	 */
	public function prepare_slug( $slug ) {

		$slug = substr( $slug, 0, 17 ) . '_' . rand( 10, 99 );

		return $slug;
	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELETE
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
		return '(?P<id>[\d]+)';
	}

}