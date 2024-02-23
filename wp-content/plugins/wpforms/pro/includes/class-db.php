<?php

/**
 * DB class
 *
 * This handy class originated from Pippin's Easy Digital Downloads.
 * https://github.com/easydigitaldownloads/easy-digital-downloads/blob/master/includes/class-edd-db.php
 *
 * Sub-classes should define $table_name, $version, and $primary_key in __construct() method.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.1.6
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
abstract class WPForms_DB {

	/**
	 * Database table name.
	 *
	 * @since 1.1.6
	 *
	 * @var string
	 */
	public $table_name;

	/**
	 * Database version.
	 *
	 * @since 1.1.6
	 *
	 * @var string
	 */
	public $version;

	/**
	 * Primary key (unique field) for the database table.
	 *
	 * @since 1.1.6
	 *
	 * @var string
	 */
	public $primary_key;

	/**
	 * Database type type identifier.
	 *
	 * @since 1.5.1
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Retrieves the list of columns for the database table.
	 * Sub-classes should define an array of columns here.
	 *
	 * @since 1.1.6
	 *
	 * @return array List of columns.
	 */
	public function get_columns() {
		return array();
	}

	/**
	 * Retrieves column defaults.
	 * Sub-classes can define default for any/all of columns defined in the get_columns() method.
	 *
	 * @since 1.1.6
	 *
	 * @return array All defined column defaults.
	 */
	public function get_column_defaults() {
		return array();
	}

	/**
	 * Retrieves a row from the database based on a given row ID.
	 *
	 * @since 1.1.6
	 *
	 * @param int $row_id Row ID.
	 *
	 * @return null|object
	 */
	public function get( $row_id ) {

		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$this->table_name} WHERE {$this->primary_key} = %s LIMIT 1;",
				$row_id
			)
		);
	}

	/**
	 * Retrieves a row based on column and row ID.
	 *
	 * @since 1.1.6
	 *
	 * @param string $column Column name.
	 * @param int|string $row_id Row ID.
	 *
	 * @return object|null|bool Database query result, object or null on failure.
	 */
	public function get_by( $column, $row_id ) {

		global $wpdb;

		if ( ! array_key_exists( $column, $this->get_columns() ) || empty( $row_id ) ) {
			return false;
		}

		return $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $this->table_name WHERE $column = '%s' LIMIT 1;",
				$row_id
			)
		);
	}

	/**
	 * Retrieves a value based on column name and row ID.
	 *
	 * @since 1.1.6
	 *
	 * @param string $column Column name.
	 * @param int|string $row_id Row ID.
	 *
	 * @return string|null Database query result (as string), or null on failure.
	 */
	public function get_column( $column, $row_id ) {

		global $wpdb;

		if ( ! array_key_exists( $column, $this->get_columns() ) || empty( $row_id ) ) {
			return false;
		}

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $column FROM $this->table_name WHERE $this->primary_key = '%s' LIMIT 1;",
				$row_id
			)
		);
	}

	/**
	 * Retrieves one column value based on another given column and matching value.
	 *
	 * @since 1.1.6
	 *
	 * @param string $column Column name.
	 * @param string $column_where Column to match against in the WHERE clause.
	 * @param string $column_value Value to match to the column in the WHERE clause.
	 *
	 * @return string|null Database query result (as string), or null on failure.
	 */
	public function get_column_by( $column, $column_where, $column_value ) {

		global $wpdb;

		if ( empty( $column ) || empty( $column_where ) || empty( $column_value ) || ! array_key_exists( $column, $this->get_columns() ) ) {
			return false;
		}

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT $column FROM $this->table_name WHERE $column_where = %s LIMIT 1;",
				$column_value
			)
		);
	}

	/**
	 * Inserts a new record into the database.
	 *
	 * Please note: inserting a record flushes the cache.
	 *
	 * @since 1.1.6
	 *
	 * @param array $data Column data.
	 * @param string $type Optional. Data type context.
	 *
	 * @return int ID for the newly inserted record.
	 */
	public function add( $data, $type = '' ) {

		global $wpdb;

		// Set default values.
		$data = wp_parse_args( $data, $this->get_column_defaults() );

		do_action( 'wpforms_pre_insert_' . $type, $data );

		// Initialise column format array.
		$column_formats = $this->get_columns();

		// Force fields to lower case.
		$data = array_change_key_case( $data );

		// White list columns.
		$data = array_intersect_key( $data, $column_formats );

		// Reorder $column_formats to match the order of columns given in $data.
		$data_keys      = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		$wpdb->insert( $this->table_name, $data, $column_formats );

		do_action( 'wpforms_post_insert_' . $type, $wpdb->insert_id, $data );

		return $wpdb->insert_id;
	}

	/**
	 * Inserts a new record into the database. This runs the add method.
	 *
	 * Please note: inserting a record flushes the cache.
	 *
	 * @since 1.1.6
	 *
	 * @param array $data Column data.
	 *
	 * @return int ID for the newly inserted record.
	 */
	public function insert( $data ) {

		return $this->add( $data );
	}

	/**
	 * Updates an existing record in the database.
	 *
	 * Please note: updating a record flushes the cache.
	 *
	 * @since 1.1.6
	 *
	 * @param string $row_id Row ID for the record being updated.
	 * @param array $data Optional. Array of columns and associated data to update. Default empty array.
	 * @param string $where Optional. Column to match against in the WHERE clause. If empty, $primary_key
	 *                       will be used. Default empty.
	 * @param string $type Optional. Data type context, e.g. 'affiliate', 'creative', etc. Default empty.
	 *
	 * @return bool False if the record could not be updated, true otherwise.
	 */
	public function update( $row_id, $data = array(), $where = '', $type = '' ) {

		global $wpdb;

		// Row ID must be positive integer.
		$row_id = absint( $row_id );

		if ( empty( $row_id ) ) {
			return false;
		}

		if ( empty( $where ) ) {
			$where = $this->primary_key;
		}

		// Initialise column format array.
		$column_formats = $this->get_columns();

		// Force fields to lower case.
		$data = array_change_key_case( $data );

		// White list columns.
		$data = array_intersect_key( $data, $column_formats );

		// Reorder $column_formats to match the order of columns given in $data.
		$data_keys      = array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		if ( false === $wpdb->update( $this->table_name, $data, array( $where => $row_id ), $column_formats ) ) {
			return false;
		}

		do_action( 'wpforms_post_update_' . $type, $data );

		return true;
	}

	/**
	 * Deletes a record from the database.
	 *
	 * Please note: successfully deleting a record flushes the cache.
	 *
	 * @since 1.1.6
	 *
	 * @param int|string $row_id Row ID.
	 *
	 * @return bool False if the record could not be deleted, true otherwise.
	 */
	public function delete( $row_id = 0 ) {

		global $wpdb;

		// Row ID must be positive integer.
		$row_id = absint( $row_id );

		if ( empty( $row_id ) ) {
			return false;
		}

		if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM $this->table_name WHERE $this->primary_key = %d", $row_id ) ) ) {
			return false;
		}

		do_action( 'wpforms_post_delete', $row_id );
		do_action( 'wpforms_post_delete_' . $this->type, $row_id );

		return true;
	}

	/**
	 * Deletes a record from the database by column.
	 *
	 * Please note: successfully deleting a record flushes the cache.
	 *
	 * @since 1.1.6
	 *
	 * @param string $column
	 * @param int|string $row_id Row ID.
	 *
	 * @return bool False if the record could not be deleted, true otherwise.
	 */
	public function delete_by( $column, $row_id ) {

		global $wpdb;

		if ( empty( $column ) || empty( $row_id ) || ! array_key_exists( $column, $this->get_columns() ) ) {
			return false;
		}

		if ( false === $wpdb->query( $wpdb->prepare( "DELETE FROM $this->table_name WHERE $column = %s", $row_id ) ) ) {
			return false;
		}

		do_action( 'wpforms_post_delete', $row_id );
		do_action( 'wpforms_post_delete_' . $this->type, $row_id );

		return true;
	}


	/**
	 * Check if the given table exists.
	 *
	 * @since 1.1.6
	 *
	 * @param  string $table The table name.
	 *
	 * @return string|null If the table name exists.
	 */
	public function table_exists( $table ) {

		global $wpdb;

		$table = sanitize_text_field( $table );

		return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) === $table;
	}
}
