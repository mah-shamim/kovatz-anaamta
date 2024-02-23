<?php
namespace Jet_Engine\Query_Builder\Queries\Traits;

trait Date_Query_Trait {

	/**
	 * Prepare Date Query arguments by initial arguments list
	 *
	 * @param   array $args
	 * @return array
	 */
	public function prepare_date_query_args( $args = array() ) {

		$raw        = $args['date_query'];
		$date_query = array();

		foreach ( $raw as $key => $query_row ) {

			if ( ! is_array( $query_row ) ) {
				// To prevent fatal error if `year` argument is empty.
				if ( 'year' === $key && empty( $query_row ) ) {
					continue;
				}

				$date_query[ $key ] = $query_row;
				continue;
			}

			// To prevent fatal error if `year` argument is empty.
			if ( empty( $query_row['year'] ) ) {
				unset( $query_row['year'] );
			}

			// To prevent database error in the Comments Query if all key fields are empty.
			if ( $this->is_not_empty_key_fields( $query_row ) ) {
				$date_query[ $key ] = $query_row;
			}
		}

		return $date_query;
	}

	public function is_not_empty_key_fields( $query_row ) {
		$check_keys = array(
			'year',
			'month',
			'day',
			'after',
			'before',

			'dayofyear',
			'dayofweek',
			'dayofweek_iso',
			'week',
			'hour',
			'minute',
			'second',
		);

		foreach ( $check_keys as $key ) {
			if ( ! \Jet_Engine_Tools::is_empty( $query_row, $key ) ) {
				return true;
			}
		}

		return false;
	}

}
