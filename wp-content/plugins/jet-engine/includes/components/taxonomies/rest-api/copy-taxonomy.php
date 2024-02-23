<?php
/**
 * Copy tax endpoint
 */

class Jet_Engine_CPT_Rest_Copy_Taxonomy extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'copy-taxonomy';
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
		$id     = isset( $params['id'] ) ? intval( $params['id'] ) : false;

		if ( ! $id ) {

			jet_engine()->taxonomies->add_notice(
				'error',
				__( 'Item ID not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->taxonomies->get_notices(),
			) );

		}

		$tax_data = jet_engine()->taxonomies->data->get_item_for_edit( $id );

		if ( ! $tax_data ) {

			jet_engine()->taxonomies->add_notice(
				'error',
				__( 'Taxonomy not found', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->taxonomies->get_notices(),
			) );

		}

		if ( empty( $tax_data['labels'] ) ) {
			$tax_data['labels']['singular_name'] = '';
		}

		$name = $this->safe_get( $tax_data, 'general_settings', 'name' ) . ' (' . esc_html__( 'Copy', 'jet-engine' ) . ')';
		$slug = $this->prepare_slug( $this->safe_get( $tax_data, 'general_settings', 'slug' ) );

		jet_engine()->taxonomies->data->set_request( array(
			'name'                       => $name,
			'slug'                       => $slug,
			'object_type'                => $this->safe_get( $tax_data, 'general_settings', 'object_type' ),
			'show_edit_link'             => $this->safe_get( $tax_data, 'general_settings', 'show_edit_link' ),
			'hide_field_names'           => $this->safe_get( $tax_data, 'general_settings', 'hide_field_names' ),
			'singular_name'              => $this->safe_get( $tax_data, 'labels', 'singular_name' ),
			'menu_name'                  => $this->safe_get( $tax_data, 'labels', 'menu_name' ),
			'all_items'                  => $this->safe_get( $tax_data, 'labels', 'all_items' ),
			'edit_item'                  => $this->safe_get( $tax_data, 'labels', 'edit_item' ),
			'view_item'                  => $this->safe_get( $tax_data, 'labels', 'view_item' ),
			'update_item'                => $this->safe_get( $tax_data, 'labels', 'update_item' ),
			'add_new_item'               => $this->safe_get( $tax_data, 'labels', 'add_new_item' ),
			'new_item_name'              => $this->safe_get( $tax_data, 'labels', 'new_item_name' ),
			'parent_item'                => $this->safe_get( $tax_data, 'labels', 'parent_item' ),
			'parent_item_colon'          => $this->safe_get( $tax_data, 'labels', 'parent_item_colon' ),
			'search_items'               => $this->safe_get( $tax_data, 'labels', 'search_items' ),
			'popular_items'              => $this->safe_get( $tax_data, 'labels', 'popular_items' ),
			'separate_items_with_commas' => $this->safe_get( $tax_data, 'labels', 'separate_items_with_commas' ),
			'add_or_remove_items'        => $this->safe_get( $tax_data, 'labels', 'add_or_remove_items' ),
			'choose_from_most_used'      => $this->safe_get( $tax_data, 'labels', 'choose_from_most_used' ),
			'not_found'                  => $this->safe_get( $tax_data, 'labels', 'not_found' ),
			'back_to_items'              => $this->safe_get( $tax_data, 'labels', 'back_to_items' ),
			'public'                     => $this->safe_get( $tax_data, 'advanced_settings', 'public' ),
			'publicly_queryable'         => $this->safe_get( $tax_data, 'advanced_settings', 'publicly_queryable' ),
			'show_ui'                    => $this->safe_get( $tax_data, 'advanced_settings', 'show_ui' ),
			'show_in_menu'               => $this->safe_get( $tax_data, 'advanced_settings', 'show_in_menu' ),
			'show_in_nav_menus'          => $this->safe_get( $tax_data, 'advanced_settings', 'show_in_nav_menus' ),
			'show_in_rest'               => $this->safe_get( $tax_data, 'advanced_settings', 'show_in_rest' ),
			'query_var'                  => $this->safe_get( $tax_data, 'advanced_settings', 'query_var' ),
			'rewrite'                    => $this->safe_get( $tax_data, 'advanced_settings', 'rewrite' ),
			'with_front'                 => $this->safe_get( $tax_data, 'advanced_settings', 'with_front' ),
			'capability_type'            => $this->safe_get( $tax_data, 'advanced_settings', 'capability_type' ),
			'hierarchical'               => $this->safe_get( $tax_data, 'advanced_settings', 'hierarchical' ),
			'rewrite_slug'               => $this->safe_get( $tax_data, 'advanced_settings', 'rewrite_slug' ),
			'rewrite_hierarchical'       => $this->safe_get( $tax_data, 'advanced_settings', 'rewrite_hierarchical' ),
			'description'                => $this->safe_get( $tax_data, 'advanced_settings', 'description' ),
			'meta_fields'                => ! empty( $tax_data['meta_fields'] ) ? $tax_data['meta_fields'] : array(),
		) );

		$tax_id = jet_engine()->taxonomies->data->create_item( false );

		return rest_ensure_response( array(
			'success' => ! empty( $tax_id ),
			'item'    => array(
				'id'     => $tax_id,
				'slug'   => $slug,
				'labels' => array(
					'name' => $name,
				),
			),
			'notices' => jet_engine()->taxonomies->get_notices(),
		) );

	}

	/**
	 * Prepare slug.
	 *
	 * @param  string $slug
	 * @return string mixed
	 */
	public function prepare_slug( $slug ) {

		$slug = substr( $slug, 0, 29 ) . '_' . rand( 10, 99 );

		return $slug;
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
		return '(?P<id>[\d]+)';
	}

}
