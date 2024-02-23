<?php
class Jet_Search_Rest_Form_Add_Suggestion extends Jet_Search_Rest_Base_Route {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'form-add-suggestion';
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
		$params = $request->get_params();

		if ( empty( $params ) || ! $params['data']['name'] ) {
			return;
		}

		global $wpdb;

		$prefix              = 'jet_';
		$table_name          = $wpdb->prefix . $prefix . 'search_suggestions';
		$sessions_table_name = $wpdb->prefix . $prefix . 'search_suggestions_sessions';

		$suggestion_name = esc_sql( $params['data']['name'] );

		$query = $wpdb->prepare( "SELECT * FROM {$table_name} WHERE name = %s ", $suggestion_name );

		$get_request = $wpdb->get_row( $query, ARRAY_A );

		if ( NULL != $get_request ) {
			$get_request['weight'] += 1;

			$where        = array( 'id' => $get_request['id'] );
			$format       = array( '%s' );
			$where_format = array( '%d' );

			$wpdb->update( $table_name, $get_request, $where, $format, $where_format );
		} else {
			$use_session = get_option( 'jet_search_suggestions_use_session' );

			if ( false != $use_session && 'true' === $use_session ) {
				if ( false === get_option( 'jet_search_suggestions_records_limit') ) {
					$records_limit = add_option( 'jet_search_suggestions_records_limit' , 5 );
					$records_limit = 5;
				} else {
					$records_limit = get_option( 'jet_search_suggestions_records_limit' );
				}
				$token         = jet_search_token_manager()->generate_token();
				$count_records = jet_search_token_manager()->check_token_records( $token );

				if ( $count_records >= $records_limit && 0 != $records_limit ) {
					return true;
				}

				$session_record = array(
					"token" => $token
				);

				jet_search_token_manager()->add_token( $session_record );
			}

			$suggestion = array(
				"name"   => $params['data']['name'],
				"weight" => 1,
				"parent" => 0,
				"term"   => NULL
			);

			$wpdb->insert( $table_name, $suggestion, '%s' );

		}
	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		$use_session = get_option( 'jet_search_suggestions_use_session' );

		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$referer         = parse_url( $_SERVER['HTTP_REFERER'] );
			$currentSiteHost = parse_url( $_SERVER['HTTP_HOST'] );
			$currentSite     = isset( $currentSiteHost['host'] ) ? parse_url( $_SERVER['HTTP_HOST'] ) : parse_url( '//' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] );

			if ( $referer['host'] !== $currentSite['host'] ) {
				die( "Sorry, you are not allowed to create entries from external sites." );
			}

			return true;
		}

		return false;
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
