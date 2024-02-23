<?php
class Jet_Search_Rest_Add_Suggestion extends Jet_Search_Rest_Base_Route {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'add-suggestion';
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
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$suggestion = $request->get_params();

		if ( empty( $suggestion ) || ! $suggestion['name'] ) {
			return rest_ensure_response( array(
				'success' => false,
				'data'    => esc_html__( 'The suggestion could not be added.', 'jet-search' ),
			) );
		}

		unset( $suggestion['_locale'] );

		global $wpdb;

		$prefix          = 'jet_';
		$table_name      = $wpdb->prefix . $prefix . 'search_suggestions';
		$suggestion_name = esc_sql( $suggestion['name'] );

		$query = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE name = %s ", $suggestion_name );

		$get_request = $wpdb->get_row( $query, ARRAY_A );

		if ( NULL != $get_request ) {
			return rest_ensure_response( array(
				'success' => false,
				'data'    => sprintf(  esc_html__( 'The suggestion with name "%s" already exists.', 'jet-search' ), $suggestion['name'] ),
			) );
		} else {
			if ( is_array( $suggestion['parent'] ) ) {
				$suggestion['parent'] = $suggestion['parent'][0];
			}

			$wpdb->insert( $table_name, $suggestion, '%s' );

			if ($wpdb->insert_id) {
				$success_text = sprintf( esc_html__( 'Success! New suggestion: %s has been added', 'jet-search' ), $suggestion['name'] );
			} else {
				$success_text = esc_html__( 'Error!', 'jet-search' );
			}
		}

		return rest_ensure_response( array(
			'success' => true,
			'data'    => $success_text,
		) );
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
	 * Returns arguments config
	 *
	 * @return array
	 */
	public function get_args() {
		return array();
	}

}
