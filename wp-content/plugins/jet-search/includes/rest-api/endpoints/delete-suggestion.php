<?php
class Jet_Search_Rest_Delete_Suggestion extends Jet_Search_Rest_Base_Route {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'delete-suggestion';
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

		if ( empty( $suggestion ) || ! $suggestion['id'] ) {
			return rest_ensure_response( array(
				'success' => false,
				'data'    => esc_html__( 'Error! The suggestion could not be deleted.', 'jet-search' ),
			) );
		}

		unset( $suggestion['_locale'] );

		global $wpdb;

		$table_name    = 'search_suggestions';
		$suggestion_id = esc_sql( (int)$suggestion['id'] );
		$where         = array( 'id' => $suggestion_id );

		jet_search()->db->delete( $table_name, $where );

		$prefix     = 'jet_';
		$table_name = $wpdb->prefix . $prefix . 'search_suggestions';
		$query      = "SELECT * FROM {$table_name}";

		$suggestions = $wpdb->get_results( $query, ARRAY_A );

		if ( $suggestions ) {
			foreach ( $suggestions as $suggestion_item ) {
				if ( $suggestion_item['id'] !== $suggestion_id ) {
					$this->remove_deleted_parent( $suggestion_item, $suggestion_id );
				}
			}
		}

		$success_text = sprintf( esc_html__( 'Success! Suggestion: %s has been deleted', 'jet-search' ), $suggestion['name'] );

		return rest_ensure_response( array(
			'success' => true,
			'data'    => $success_text,
		) );
	}

	/**
	 * Remove deleted suggestion from suggestions parents
	 *
	 * @return void
	 */
	public function remove_deleted_parent( $item, $deleted_id ) {
		if ( "0" != $item['parent'] ) {

			if ( $item['parent'] === $deleted_id ) {
				$item['parent'] = 0;

				global $wpdb;

				$prefix       = 'jet_';
				$table_name   = $wpdb->prefix . $prefix . 'search_suggestions';
				$where        = array( 'id' => $item['id'] );
				$format       = array( '%s' );
				$where_format = array( '%d' );

				$wpdb->update( $table_name, $item, $where, $format, $where_format );
			} else {
				$item['parent'] = maybe_serialize( $item['parent'] );
			}
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
