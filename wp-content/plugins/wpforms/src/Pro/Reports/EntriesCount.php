<?php

namespace WPForms\Pro\Reports;

/**
 * Generates form submissions reports.
 *
 * @package    WPForms\Pro\Reports
 * @author     WPForms
 * @since      1.5.4
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2019, WPForms LLC
 */
class EntriesCount {

	/**
	 * Constructor.
	 *
	 * @since 1.5.4
	 */
	public function __construct() {}

	/**
	 * Get entries count grouped by $param.
	 * Main point of entry to fetch form entry count data from DB.
	 * Caches the result.
	 *
	 * @since 1.5.4
	 *
	 * @param string $param        'date' or 'form'.
	 * @param int    $form_id      Form ID to fetch the data for.
	 * @param int    $days         Timespan (in days) to fetch the data for.
	 * @param string $date_end_str End date of the timespan (PHP DateTime supported string, see http://php.net/manual/en/datetime.formats.php).
	 *
	 * @return array
	 */
	public function get_by( $param, $form_id = 0, $days = 0, $date_end_str = 'yesterday' ) {

		$allowed_params = array( 'date', 'form' );

		if ( ! \in_array( $param, $allowed_params, true ) ) {
			return array();
		}

		try {
			$date_end = new \DateTime( $date_end_str );
		} catch ( \Exception $e ) {
			return array();
		}

		try {
			$date_start = new \DateTime( $date_end_str );
		} catch ( \Exception $e ) {
			return array();
		}

		$date_end = $date_end->setTime( 23, 59, 59 );

		$date_start = $date_start->modify( '-' . \absint( $days ) . 'days' );
		$date_start = $date_start->setTime( 0, 0, 0 );

		switch ( $param ) {
			case 'date':
				$result = $this->get_by_date_sql( $form_id, $date_start, $date_end );
				break;
			case 'form':
				$result = $this->get_by_form_sql( $form_id, $date_start, $date_end );
				break;
			default:
				$result = array();
		}

		return $result;
	}

	/**
	 * Get entries count grouped by date.
	 * In most cases it's better to use `get_by( 'date' )` instead.
	 * Doesn't cache the result.
	 *
	 * @since 1.5.4
	 *
	 * @param int       $form_id    Form ID to fetch the data for.
	 * @param \DateTime $date_start Start date for the search.
	 * @param \DateTime $date_end   End date for the search.
	 *
	 * @return array
	 */
	public function get_by_date_sql( $form_id = 0, $date_start = null, $date_end = null ) {

		global $wpdb;

		$table_name   = \wpforms()->entry->table_name;
		$format       = 'Y-m-d H:i:s';
		$placeholders = array();

		$sql = "SELECT CAST(date AS DATE) as day, COUNT(entry_id) as count
				FROM {$table_name}
				WHERE 1=1";

		if ( ! empty( $form_id ) ) {
			$sql .= ' AND form_id = %d';
			$placeholders[] = \absint( $form_id );
		}

		if ( ! empty( $date_start ) ) {
			$sql .= ' AND date >= %s';
			$placeholders[] = $date_start->format( $format );
		}

		if ( ! empty( $date_end ) ) {
			$sql .= ' AND date <= %s';
			$placeholders[] = $date_end->format( $format );
		}

		$sql .= ' GROUP BY day;';

		if ( ! empty( $placeholders ) ) {
			$sql = $wpdb->prepare( $sql, $placeholders );
		}

		$results = $wpdb->get_results( $sql, \OBJECT_K );

		if ( empty( $results ) ) {
			return array();
		}

		return (array) $results;
	}

	/**
	 * Get entries count grouped by form.
	 * In most cases it's better to use `get_by( 'form' )` instead.
	 * Doesn't cache the result.
	 *
	 * @since 1.5.4
	 *
	 * @param int       $form_id    Form ID to fetch the data for.
	 * @param \DateTime $date_start Start date for the search.
	 * @param \DateTime $date_end   End date for the search.
	 *
	 * @return array
	 */
	public function get_by_form_sql( $form_id = 0, $date_start = null, $date_end = null ) {

		global $wpdb;

		$table_name = \wpforms()->entry->table_name;
		$format     = 'Y-m-d H:i:s';

		$sql = "SELECT form_id, COUNT(entry_id) as count
				FROM {$table_name}
				WHERE 1=1";

		if ( ! empty( $form_id ) ) {
			$sql .= ' AND form_id = %d';
			$placeholders[] = \absint( $form_id );
		}

		if ( ! empty( $date_start ) ) {
			$sql .= ' AND date >= %s';
			$placeholders[] = $date_start->format( $format );
		}

		if ( ! empty( $date_end ) ) {
			$sql .= ' AND date <= %s';
			$placeholders[] = $date_end->format( $format );
		}

		$sql .= 'GROUP BY form_id ORDER BY count DESC;';

		if ( ! empty( $placeholders ) ) {
			$sql = $wpdb->prepare( $sql, $placeholders );
		}

		$results = $wpdb->get_results( $sql, \OBJECT_K );

		return (array) $this->fill_forms_list_form_data( $results );
	}

	/**
	 * Fill a forms list with the data needed for a frontend display.
	 *
	 * @since 1.5.4
	 *
	 * @param array $results DB results from `$wpdb->prepare()`.
	 *
	 * @return array
	 */
	public function fill_forms_list_form_data( $results ) {

		if ( ! \is_array( $results ) ) {
			return array();
		}

		$processed = array();

		foreach ( $results as $form_id => $result ) {

			$form = \wpforms()->form->get( $form_id );

			if ( empty( $form ) ) {
				continue;
			}

			$edit_url = \add_query_arg(
				array(
					'page'    => 'wpforms-entries',
					'view'    => 'list',
					'form_id' => \absint( $form->ID ),
				),
				\admin_url( 'admin.php' )
			);

			$processed[ $form->ID ] = array(
				'form_id'  => $form->ID,
				'count'    => isset( $results[ $form->ID ]->count ) ? \absint( $results[ $form->ID ]->count ) : 0,
				'title'    => $form->post_title,
				'edit_url' => $edit_url,
			);
		}

		return $processed;
	}
}
