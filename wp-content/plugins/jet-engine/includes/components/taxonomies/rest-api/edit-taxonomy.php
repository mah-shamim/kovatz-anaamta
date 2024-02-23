<?php
/**
 * Add/Update tax endpoint
 */

class Jet_Engine_CPT_Rest_Edit_Taxonomy extends Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'edit-taxonomy';
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

		if ( empty( $params['id'] ) ) {

			jet_engine()->taxonomies->add_notice(
				'error',
				__( 'Item ID not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => jet_engine()->taxonomies->get_notices(),
			) );

		}

		$update_terms = ! empty( $params['update_terms'] ) ? $params['update_terms'] : false;
		$initial_slug = ! empty( $params['initial_slug'] ) ? $params['initial_slug'] : false;
		$new_slug     = $this->safe_get( $params, 'general_settings', 'slug' );

		jet_engine()->taxonomies->data->set_request( array(
			'id'                         => $params['id'],
			'name'                       => $this->safe_get( $params, 'general_settings', 'name' ),
			'slug'                       => $new_slug,
			'object_type'                => $this->safe_get( $params, 'general_settings', 'object_type' ),
			'show_edit_link'             => $this->safe_get( $params, 'general_settings', 'show_edit_link' ),
			'hide_field_names'           => $this->safe_get( $params, 'general_settings', 'hide_field_names' ),
			'singular_name'              => $this->safe_get( $params, 'labels', 'singular_name' ),
			'menu_name'                  => $this->safe_get( $params, 'labels', 'menu_name' ),
			'all_items'                  => $this->safe_get( $params, 'labels', 'all_items' ),
			'edit_item'                  => $this->safe_get( $params, 'labels', 'edit_item' ),
			'view_item'                  => $this->safe_get( $params, 'labels', 'view_item' ),
			'update_item'                => $this->safe_get( $params, 'labels', 'update_item' ),
			'add_new_item'               => $this->safe_get( $params, 'labels', 'add_new_item' ),
			'new_item_name'              => $this->safe_get( $params, 'labels', 'new_item_name' ),
			'parent_item'                => $this->safe_get( $params, 'labels', 'parent_item' ),
			'parent_item_colon'          => $this->safe_get( $params, 'labels', 'parent_item_colon' ),
			'search_items'               => $this->safe_get( $params, 'labels', 'search_items' ),
			'popular_items'              => $this->safe_get( $params, 'labels', 'popular_items' ),
			'separate_items_with_commas' => $this->safe_get( $params, 'labels', 'separate_items_with_commas' ),
			'add_or_remove_items'        => $this->safe_get( $params, 'labels', 'add_or_remove_items' ),
			'choose_from_most_used'      => $this->safe_get( $params, 'labels', 'choose_from_most_used' ),
			'not_found'                  => $this->safe_get( $params, 'labels', 'not_found' ),
			'back_to_items'              => $this->safe_get( $params, 'labels', 'back_to_items' ),
			'public'                     => $this->safe_get( $params, 'advanced_settings', 'public' ),
			'publicly_queryable'         => $this->safe_get( $params, 'advanced_settings', 'publicly_queryable' ),
			'show_ui'                    => $this->safe_get( $params, 'advanced_settings', 'show_ui' ),
			'show_in_menu'               => $this->safe_get( $params, 'advanced_settings', 'show_in_menu' ),
			'show_in_nav_menus'          => $this->safe_get( $params, 'advanced_settings', 'show_in_nav_menus' ),
			'show_in_rest'               => $this->safe_get( $params, 'advanced_settings', 'show_in_rest' ),
			'query_var'                  => $this->safe_get( $params, 'advanced_settings', 'query_var' ),
			'rewrite'                    => $this->safe_get( $params, 'advanced_settings', 'rewrite' ),
			'with_front'                 => $this->safe_get( $params, 'advanced_settings', 'with_front' ),
			'capability_type'            => $this->safe_get( $params, 'advanced_settings', 'capability_type' ),
			'hierarchical'               => $this->safe_get( $params, 'advanced_settings', 'hierarchical' ),
			'rewrite_slug'               => $this->safe_get( $params, 'advanced_settings', 'rewrite_slug' ),
			'rewrite_hierarchical'       => $this->safe_get( $params, 'advanced_settings', 'rewrite_hierarchical' ),
			'description'                => $this->safe_get( $params, 'advanced_settings', 'description' ),
			'meta_fields'                => ! empty( $params['meta_fields'] ) ? $params['meta_fields'] : array(),
		) );

		$updated = jet_engine()->taxonomies->data->edit_item( false );

		if ( $updated && $update_terms && $initial_slug && $new_slug !== $initial_slug ) {

			global $wpdb;

			$wpdb->update(
				$wpdb->term_taxonomy,
				array(
					'taxonomy' => $new_slug,
				),
				array(
					'taxonomy' => $initial_slug,
				)
			);
		}

		if ( $updated && $initial_slug && $new_slug !== $initial_slug ) {
			do_action( 'jet-engine/taxonomies/updated-taxonomy-slug', $new_slug, $initial_slug );
		}

		return rest_ensure_response( array(
			'success' => $updated,
			'notices' => jet_engine()->taxonomies->get_notices(),
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
		return '(?P<id>[\d]+)';
	}

}
