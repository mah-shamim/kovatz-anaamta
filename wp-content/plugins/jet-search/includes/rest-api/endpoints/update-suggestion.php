<?php
class Jet_Search_Rest_Update_Suggestion extends Jet_Search_Rest_Base_Route {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'update-suggestion';
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

		unset( $suggestion['_locale'] );

		global $wpdb;

		$prefix          = 'jet_';
		$table_name      = $wpdb->prefix . $prefix . 'search_suggestions';
		$suggestion_id   = esc_sql( $suggestion['id'] );
		$suggestion_name = esc_sql( $suggestion['name'] );

		$query = $wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE id != %s AND name = %s",
			$suggestion_id,
			$suggestion_name
		);

		$get_request = $wpdb->get_row( $query, ARRAY_A );

		if ( NULL === $get_request ) {

			$query = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %s ", $suggestion_id );

			$get_request = $wpdb->get_row( $query, ARRAY_A );

			if ( NULL != $get_request ) {

				unset( $suggestion['child'] );

				$where                = array( 'id' => $suggestion_id );
				$format               = array( '%s' );
				$where_format         = array( '%d' );

				$wpdb->update( $table_name, $suggestion, $where, $format, $where_format );

				$success_text = sprintf( esc_html__( 'Success! Suggestion: "%s" has been updated', 'jet-search' ), $suggestion['name'] );

				return rest_ensure_response( array(
					'success' => true,
					'data'    => $success_text,
				) );
			} else {
				$success_text = sprintf( esc_html__( 'Fail! The suggestion with "%s" id has not found', 'jet-search' ), $suggestion['id'] );

				return rest_ensure_response( array(
					'success' => false,
					'data'    => $success_text,
				) );
			}
		} else {
			$success_text = sprintf( esc_html__( 'Fail! The suggestion with "%s" already exists.', 'jet-search' ), $suggestion['name'] );

			return rest_ensure_response( array(
				'success' => false,
				'data'    => $success_text,
			) );
		}
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
