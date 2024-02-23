<?php
/**
 * Database manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_DB' ) ) {

	/**
	 * Define Jet_Smart_Filters_DB class
	 */
	class Jet_Smart_Filters_DB {

		/**
		 * Tables data
		 */
		public static function tables() {

			global $wpdb;
			$prefix = $wpdb->prefix . 'jet_smart_filters_';

			return array(
				'indexer' => array(
					'name'  => $prefix . 'indexer',
					'query' => "
						id INT UNSIGNED NOT NULL auto_increment,
						type VARCHAR(50),
						item_id INT UNSIGNED,
						item_query VARCHAR(50),
						item_key VARCHAR(50),
						item_value VARCHAR(50),
						PRIMARY KEY (id)
					"
				)
			);
		}

		/**
		 * Get table data
		 */
		public static function get_table( $table_name ) {

			$tables = self::tables();

			return isset( $tables[$table_name] ) ? $tables[$table_name] : false;
		}

		/**
		 * Get table name
		 */
		public static function get_table_full_name( $table_name ) {

			$tables = self::tables();

			return isset( $tables[$table_name]['name'] ) ? $tables[$table_name]['name'] : false;
		}

		/**
		 * Create all tables on activation
		 */
		public static function create_all_tables() {

			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();

			foreach ( array_keys( self::tables() ) as $table_name ) {
				self::create_table( $table_name );
			}
		}

		/**
		 * Drop all tables on deactivation
		 */
		public static function drop_all_tables() {

			foreach ( array_keys( self::tables() ) as $table_name ) {
				self::drop( $table_name );
			}
		}

		/**
		 * Check if table is exists
		 */
		public static function is_table_exists( $table_name ) {

			global $wpdb;
			$table_full_name = self::get_table_full_name( $table_name );

			if ( ! $table_full_name ) {
				return false;
			}

			return ( strtolower( $table_full_name ) === strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_full_name'" ) ) );
		}

		/**
		 * Create table if allowed
		 */
		public static function create_table( $table_name ) {

			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();

			if ( ! self::is_table_exists( $table_name ) ) {
				$table = self::get_table( $table_name );

				$sql = "CREATE TABLE " . $table['name'] . " (
					" . $table['query'] . "
				) $charset_collate;";

				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

				dbDelta( $sql );
			}
		}

		/**
		 * Clear table data
		 */
		public static function clear_table( $table_name ) {

			global $wpdb;
			$table_full_name = self::get_table_full_name( $table_name );

			if ( ! $table_full_name ) {
				return false;
			}

			$wpdb->query( "TRUNCATE TABLE {$table_full_name}" );
		}

		/**
		 * Drop table data
		 */
		public static function drop( $table_name ) {

			global $wpdb;
			$table_full_name = self::get_table_full_name( $table_name );

			if ( ! $table_full_name ) {
				return false;
			}

			$wpdb->query( "DROP TABLE IF EXISTS {$table_full_name}" );
		}
	}
}
