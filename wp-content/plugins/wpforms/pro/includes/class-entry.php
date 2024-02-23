<?php

/**
 * Entry DB class.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
class WPForms_Entry_Handler extends WPForms_DB {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name  = $wpdb->prefix . 'wpforms_entries';
		$this->primary_key = 'entry_id';
		$this->type        = 'entries';
	}

	/**
	 * Get table columns.
	 *
	 * @since 1.0.0
	 */
	public function get_columns() {

		return array(
			'entry_id'      => '%d',
			'form_id'       => '%d',
			'post_id'       => '%d',
			'user_id'       => '%d',
			'status'        => '%s',
			'type'          => '%s',
			'viewed'        => '%d',
			'starred'       => '%d',
			'fields'        => '%s',
			'meta'          => '%s',
			'date'          => '%s',
			'date_modified' => '%s',
			'ip_address'    => '%s',
			'user_agent'    => '%s',
			'user_uuid'     => '%s',
		);
	}

	/**
	 * Default column values.
	 *
	 * @since 1.0.0
	 */
	public function get_column_defaults() {

		return array(
			'form_id'       => '',
			'post_id'       => '',
			'user_id'       => '',
			'status'        => '',
			'type'          => '',
			'fields'        => '',
			'meta'          => '',
			'date'          => date( 'Y-m-d H:i:s' ),
			'date_modified' => date( 'Y-m-d H:i:s' ),
			'ip_address'    => '',
			'user_agent'    => '',
			'user_uuid'     => '',
		);
	}

	/**
	 * Deletes an entry from the database, also removes entry meta.
	 *
	 * Please note: successfully deleting a record flushes the cache.
	 *
	 * @since 1.1.6
	 *
	 * @param int $row_id Entry ID.
	 *
	 * @return bool False if the record could not be deleted, true otherwise.
	 */
	public function delete( $row_id = 0 ) {

		$entry  = parent::delete( $row_id );
		$meta   = wpforms()->entry_meta->delete_by( 'entry_id', $row_id );
		$fields = wpforms()->entry_fields->delete_by( 'entry_id', $row_id );

		return ( $entry && $meta && $fields );
	}

	/**
	 * Get next entry.
	 *
	 * @since 1.1.5
	 *
	 * @param int $row_id  Entry ID.
	 * @param int $form_id Form ID.
	 *
	 * @return mixed object or null
	 */
	public function get_next( $row_id, $form_id ) {

		global $wpdb;

		if ( empty( $row_id ) || empty( $form_id ) ) {
			return false;
		}

		$next = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} WHERE `form_id` = %d AND {$this->primary_key} > %d ORDER BY {$this->primary_key} LIMIT 1;",
				absint( $form_id ),
				absint( $row_id )
			)
		);

		return $next;
	}

	/**
	 * Get previous entry.
	 *
	 * @since 1.1.5
	 *
	 * @param int $row_id  Entry ID.
	 * @param int $form_id Form ID.
	 *
	 * @return mixed object or null
	 */
	public function get_prev( $row_id, $form_id ) {

		global $wpdb;

		if ( empty( $row_id ) || empty( $form_id ) ) {
			return false;
		}

		$prev = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} WHERE `form_id` = %d AND {$this->primary_key} < %d ORDER BY {$this->primary_key} DESC LIMIT 1;",
				absint( $form_id ),
				absint( $row_id )
			)
		);

		return $prev;
	}

	/**
	 * Get last entry.
	 *
	 * @since 1.5.0
	 *
	 * @param int $form_id Form ID.
	 *
	 * @return mixed Object from DB values or null.
	 */
	public function get_last( $form_id ) {

		global $wpdb;

		if ( empty( $form_id ) ) {
			return false;
		}

		$last = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name}
				WHERE `form_id` = %d
				ORDER BY {$this->primary_key} DESC
				LIMIT 1;",
				absint( $form_id )
			)
		);

		return $last;
	}

	/**
	 * Mark all entries read for a form.
	 *
	 * @since 1.1.6
	 *
	 * @param int $form_id Form ID.
	 *
	 * @return bool
	 */
	public function mark_all_read( $form_id = 0 ) {

		global $wpdb;

		if ( empty( $form_id ) ) {
			return false;
		}

		if ( false === $wpdb->query( $wpdb->prepare( "UPDATE $this->table_name SET `viewed` = '1' WHERE `form_id` = %d", $form_id ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get next entries count.
	 *
	 * @since 1.5.0
	 *
	 * @param int $row_id  Entry ID.
	 * @param int $form_id Form ID.
	 *
	 * @return int
	 */
	public function get_next_count( $row_id, $form_id ) {

		global $wpdb;

		if ( empty( $form_id ) ) {
			return 0;
		}

		$prev_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT({$this->primary_key}) FROM {$this->table_name}
				WHERE `form_id` = %d AND {$this->primary_key} > %d
				ORDER BY {$this->primary_key} ASC;",
				absint( $form_id ),
				absint( $row_id )
			)
		);

		return absint( $prev_count );
	}

	/**
	 * Get previous entries count.
	 *
	 * @since 1.5.0 Changed return type to always be an integer.
	 * @since 1.1.5
	 *
	 * @param int $row_id  Entry ID.
	 * @param int $form_id Form ID.
	 *
	 * @return int
	 */
	public function get_prev_count( $row_id, $form_id ) {

		global $wpdb;

		if ( empty( $row_id ) || empty( $form_id ) ) {
			return 0;
		}

		$prev_count = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT({$this->primary_key}) FROM {$this->table_name}
				WHERE `form_id` = %d AND {$this->primary_key} < %d
				ORDER BY {$this->primary_key} ASC;",
				absint( $form_id ),
				absint( $row_id )
			)
		);

		return absint( $prev_count );
	}

	/**
	 * Get entries from the database.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args  Redefine query parameters by providing own arguments.
	 * @param bool  $count Whether to just count entries or get the list of them. True to just count.
	 *
	 * @return array|int
	 */
	public function get_entries( $args = array(), $count = false ) {

		global $wpdb;

		$defaults = array(
			'select'        => 'all',
			'number'        => 30,
			'offset'        => 0,
			'form_id'       => 0,
			'entry_id'      => 0,
			'is_filtered'   => false,
			'post_id'       => '',
			'user_id'       => '',
			'status'        => '',
			'type'          => '',
			'viewed'        => '',
			'starred'       => '',
			'user_uuid'     => '',
			'date'          => '',
			'date_modified' => '',
			'ip_address'    => '',
			'orderby'       => 'entry_id',
			'order'         => 'DESC',
		);

		$args = apply_filters(
			'wpforms_entry_handler_get_entries_args',
			wp_parse_args( $args, $defaults )
		);

		/*
		 * Modify the SELECT.
		 */
		$select = '*';

		$possible_select_values = apply_filters(
			'wpforms_entry_handler_get_entries_select',
			array(
				'all'       => '*',
				'entry_ids' => '`entry_id`',
			)
		);
		if ( array_key_exists( $args['select'], $possible_select_values ) ) {
			$select = esc_sql( $possible_select_values[ $args['select'] ] );
		}

		/*
		 * Modify the WHERE.
		 *
		 * Always define a default WHERE clause.
		 * MySQL/MariaDB optimizations are clever enough to strip this out later before actual execution.
		 * But having this default here in the code will make everything a bit better to read and understand.
		 */
		$where = array(
			'default' => '1=1',
		);

		// Allowed int arg items.
		$keys = array( 'entry_id', 'form_id', 'post_id', 'user_id', 'viewed', 'starred' );
		foreach ( $keys as $key ) {
			// Value `$args[ $key ]` can be a natural number and a numeric string.
			// We should skip empty string values, but continue working with '0'.
			if ( ! is_array( $args[ $key ] ) && ( ! is_numeric( $args[ $key ] ) || 0 === $args[ $key ] ) ) {
				continue;
			}

			if ( is_array( $args[ $key ] ) && ! empty( $args[ $key ] ) ) {
				$ids = implode( ',', array_map( 'intval', $args[ $key ] ) );
			} else {
				$ids = (int) $args[ $key ];
			}

			$where[ 'arg_' . $key ] = "`{$key}` IN ( {$ids} )";
		}

		// Allowed string arg items.
		$keys = array( 'status', 'type', 'user_uuid' );
		foreach ( $keys as $key ) {

			if ( '' !== $args[ $key ] ) {
				$where[ 'arg_' . $key ] = "`{$key}` = '" . esc_sql( $args[ $key ] ) . "'";
			}
		}

		// Process dates.
		$keys = array( 'date', 'date_modified' );
		foreach ( $keys as $key ) {
			if ( empty( $args[ $key ] ) ) {
				continue;
			}

			// We can pass array and treat it as a range from:to.
			if ( is_array( $args[ $key ] ) && count( $args[ $key ] ) === 2 ) {
				$date_start = wpforms_get_day_period_date( 'start_of_day', strtotime( $args[ $key ][0] ) );
				$date_end   = wpforms_get_day_period_date( 'end_of_day', strtotime( $args[ $key ][1] ) );

				if ( ! empty( $date_start ) && ! empty( $date_end ) ) {
					$where[ 'arg_' . $key . '_start' ] = "`{$key}` >= '{$date_start}'";
					$where[ 'arg_' . $key . '_end' ]   = "`{$key}` <= '{$date_end}'";
				}
			} elseif ( is_string( $args[ $key ] ) ) {
				/*
				 * If we pass the only string representation of a date -
				 * that means we want to get records of that day only.
				 * So we generate start and end MySQL dates for the specified day.
				 */
				$timestamp  = strtotime( $args[ $key ] );
				$date_start = wpforms_get_day_period_date( 'start_of_day', $timestamp );
				$date_end   = wpforms_get_day_period_date( 'end_of_day', $timestamp );

				if ( ! empty( $date_start ) && ! empty( $date_end ) ) {
					$where[ 'arg_' . $key . '_start' ] = "`{$key}` >= '{$date_start}'";
					$where[ 'arg_' . $key . '_end' ]   = "`{$key}` <= '{$date_end}'";
				}
			}
		}

		// Remove filtering by id if it is not a filtered query.
		if ( ! $args['is_filtered'] ) {
			unset( $where['arg_entry_id'] );
		}

		// Give developers an ability to modify WHERE (unset clauses, add new, etc).
		$where     = (array) apply_filters( 'wpforms_entry_handler_get_entries_where', $where, $args );
		$where_sql = implode( ' AND ', $where );

		/*
		 * Modify the ORDER BY.
		 */
		$args['orderby'] = ! array_key_exists( $args['orderby'], $this->get_columns() ) ? $this->primary_key : $args['orderby'];

		if ( 'ASC' === strtoupper( $args['order'] ) ) {
			$args['order'] = 'ASC';
		} else {
			$args['order'] = 'DESC';
		}

		/*
		 * Modify the OFFSET / NUMBER.
		 */
		$args['offset'] = absint( $args['offset'] );
		if ( $args['number'] < 1 ) {
			$args['number'] = PHP_INT_MAX;
		}
		$args['number'] = absint( $args['number'] );

		/*
		 * Retrieve the results.
		 */

		if ( true === $count ) {

			// @codingStandardsIgnoreStart
			$results = absint( $wpdb->get_var(
				"SELECT COUNT({$this->primary_key})
				FROM {$this->table_name}
				WHERE {$where_sql};"
			) );
			// @codingStandardsIgnoreEnd

		} else {

			// @codingStandardsIgnoreStart
			$results = $wpdb->get_results(
				"SELECT {$select}
				FROM {$this->table_name}
				WHERE {$where_sql}
				ORDER BY {$args['orderby']} {$args['order']}
				LIMIT {$args['offset']}, {$args['number']};"
			);
			// @codingStandardsIgnoreEnd
		}

		return $results;
	}

	/**
	 * Create custom entry database table.
	 *
	 * @since 1.0.0
	 */
	public function create_table() {

		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate .= "DEFAULT CHARACTER SET {$wpdb->charset}";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE {$wpdb->collate}";
		}

		$sql = "CREATE TABLE {$this->table_name} (
			entry_id bigint(20) NOT NULL AUTO_INCREMENT,
			form_id bigint(20) NOT NULL,
			post_id bigint(20) NOT NULL,
			user_id bigint(20) NOT NULL,
			status varchar(30) NOT NULL,
			type varchar(30) NOT NULL,
			viewed tinyint(1) DEFAULT 0,
			starred tinyint(1) DEFAULT 0,
			fields longtext NOT NULL,
			meta longtext NOT NULL,
			date datetime NOT NULL,
			date_modified datetime NOT NULL,
			ip_address varchar(128) NOT NULL,
			user_agent varchar(256) NOT NULL,
			user_uuid varchar(36) NOT NULL,
			PRIMARY KEY  (entry_id),
			KEY form_id (form_id)
		) {$charset_collate};";

		dbDelta( $sql );
	}
}
