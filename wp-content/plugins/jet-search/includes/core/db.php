<?php
/**
 * Database manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Search_DB' ) ) {

	/**
	 * Define Jet_Search_DB class
	 */
	class Jet_Search_DB {

		public $query_cache = array();

		/**
		 * Return table name by key
		 *
		 * @param  string $table table key.
		 * @return string
		 */
		public static function tables( $table = null, $return = 'all' ) {

			global $wpdb;

			$prefix = 'jet_';

			$tables = array(
				'search_suggestions' => array(
					'name'        => $wpdb->prefix . $prefix . 'search_suggestions',
					'export_name' => $prefix . 'search_suggestions',
					'query'       => "
						id int NOT NULL AUTO_INCREMENT,
						name text,
						weight bigint,
						parent text,
						term text,
						PRIMARY KEY  (id)
					",
				),
				'search_suggestions_sessions' => array(
					'name'        => $wpdb->prefix . $prefix . 'search_suggestions_sessions',
					'export_name' => $prefix . 'search_suggestions_sessions',
					'query'       => "
						id int NOT NULL AUTO_INCREMENT,
						token VARCHAR(255),
						created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						PRIMARY KEY  (id)
					",
				)
			);

			if ( ! $table && 'all' === $return ) {
				return $tables;
			}

			switch ( $return ) {
				case 'all':
					return isset( $tables[ $table ] ) ? $tables[ $table ] : false;

				case 'name':
					return isset( $tables[ $table ] ) ? $tables[ $table ]['name'] : false;

				case 'query':
					return isset( $tables[ $table ] ) ? $tables[ $table ]['query'] : false;
			}

			return false;

		}

		/**
		 * Create all tables on activation
		 *
		 * @return [type] [description]
		 */
		public static function create_all_tables() {

			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();

			foreach ( self::tables() as $table ) {

				$table_name  = $table['name'];
				$table_query = $table['query'];

				if ( ! self::table_exists( $table_name ) ) {

					$sql = "CREATE TABLE $table_name (
						$table_query
					) $charset_collate;";

					require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

					dbDelta( $sql );
				}

			}
		}

		/**
		 * Insert or update row into table
		 *
		 * @param  array  $data [description]
		 * @return [type]       [description]
		 */
		public function update( $table = null, $data = array(), $format = array() ) {

			$prepared_data = array();

			foreach ( $data as $key => $value ) {
				if ( is_array( $value ) ) {
					$prepared_data[ $key ] = maybe_serialize( $value );
				} else {
					$prepared_data[ $key ] = $value;
				}
			}

			global $wpdb;

			$table_name = $this->tables( $table, 'name' );
			$result     = false;

			if ( ! isset( $prepared_data['id'] ) ) {

				$inserted = $wpdb->insert( $table_name, $prepared_data, $format );

				if ( $inserted ) {
					$result = $wpdb->insert_id;
				}

			} else {

				$where        = array( 'id' => $prepared_data['id'] );
				$where_format = array( '%d' );
				$wpdb->update( $table_name, $prepared_data, $where, $format, $where_format );
				$result = $prepared_data['id'];

			}

			return $result;
		}

		public function delete( $table = null, $where = array(), $format = array() ) {
			global $wpdb;
			$table_name = $this->tables( $table, 'name' );
			$wpdb->delete( $table_name, $where, $format );
		}

		/**
		 * Check if table is exists
		 *
		 * @param  string  $table Table name.
		 * @return boolean
		 */
		public function is_table_exists( $table = null ) {

			$table_name = $this->tables( $table, 'name' );

			if ( ! $table_name ) {
				return false;
			}

			return self::table_exists( $table_name );

		}

		/**
		 * Check if given table is exists
		 *
		 * @param  [type] $table_name [description]
		 * @return [type]             [description]
		 */
		public static function table_exists( $table_name ) {

			global $wpdb;

			if ( NULL != $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) ) {
				$result = ( strtolower( $table_name ) === strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) ) );
			} else {
				$result = false;
			}

			return $result;

		}

		/**
		 * Create table if allowed
		 * @return [type] [description]
		 */
		public function create_table( $table = null ) {

			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();
			$table_data      = $this->tables( $table );

			if ( ! $table_data ) {
				return;
			}

			$table_name  = $table_data['name'];
			$table_query = $table_data['query'];

			$sql = "CREATE TABLE $table_name (
				$table_query
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			dbDelta( $sql );

		}

		/**
		 * Returns total rows count in requested table
		 *
		 * @param  string   $table  Table name.
		 * @return int
		 */
		public function count( $table = null, $args = array() ) {

			global $wpdb;

			$table_name = $this->tables( $table, 'name' );
			$query      = "SELECT COUNT(*) FROM $table_name";
			$query     .= $this->get_where_statement( $args );

			return $wpdb->get_var( $query );

		}

		/**
		 * Build where statement for SQL query
		 *
		 * @return [type] [description]
		 */
		public function get_where_statement( $args = array() ) {

			if ( empty( $args ) ) {
				return;
			}

			$query = ' WHERE ';
			$glue  = '';

			foreach ( $args as $key => $value ) {

				$query .= $glue;

				if ( ! is_array( $value ) ) {
					$query .= sprintf( '`%1$s` = \'%2$s\'', esc_sql( $key ), esc_sql( $value ) );
				} else {
					$value  = array_map( 'esc_sql', $value );
					$query .= sprintf( '`%1$s` IN (%2$s)', esc_sql( $key ), implode( ',' , $value ) );
				}

				$glue   = ' AND ';

			}

			return $query;

		}

		/**
		 * Query data from passed table by passed args
		 *
		 * @param  string   $table  Table name.
		 * @param  array    $args   Args array.
		 * @param  callable $filter Callback to filter results.
		 * @return array
		 */
		public function query( $table = null, $args = array(), $filter = null, $from_cache = true ) {

			$result = array();

			if ( $from_cache && isset( $this->query_cache[ $table ] ) ) {
				$result = $this->query_cached( $table, $args, $filter );
			} elseif ( $from_cache ) {
				$result = $this->query_raw( $table, $args, $filter );
			} else {
				global $wpdb;

				$table_name = $this->tables( $table, 'name' );
				$query      = "SELECT * FROM $table_name";
				$query     .= $this->get_where_statement( $args );
				$query     .= " ORDER BY id DESC";
				$raw        = $wpdb->get_results( $query, ARRAY_A );

				if ( ! $raw ) {
					$raw = array();
				}

				if ( ! $filter ) {
					$result = $raw;
				} else {
					$result = array_map( $filter, $raw );
				}
			}

			return $result;

		}

		/**
		 * Query raw results from table
		 *
		 * @param  [type] $table  [description]
		 * @param  array  $args   [description]
		 * @param  [type] $filter [description]
		 * @return [type]         [description]
		 */
		public function query_raw( $table = null, $args = array(), $filter = null ) {

			global $wpdb;

			$table_name = $this->tables( $table, 'name' );
			$raw        = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id DESC", ARRAY_A );

			if ( ! $raw ) {
				$raw = array();
			}

			$this->query_cache[ $table ] = $raw;

			return $this->query_cached( $table, $args, $filter );

		}

		/**
		 * Query by cached results
		 *
		 * @param  array  $args   [description]
		 * @param  [type] $filter [description]
		 * @return [type]         [description]
		 */
		public function query_cached( $table = null, $args = array(), $filter = null ) {

			if ( empty( $this->query_cache[ $table ] ) ) {
				return array();
			}

			$result = array();

			foreach ( $this->query_cache[ $table ] as $row ) {

				if ( empty( $args ) ) {

					if ( ! $filter ) {
						$result[] = $row;
					} else {
						$result[] = call_user_func( $filter, $row );
					}

					continue;

				}

				$match = true;

				foreach ( $args as $key => $value ) {

					if ( ! isset( $row[ $key ] ) ) {
						$match = false;
						break;
					} elseif ( ! is_array( $value ) && $row[ $key ] != $value ) {
						$match = false;
						break;
					} elseif ( is_array( $value ) && ! in_array( $row[ $key ], $value ) ) {
						$match = false;
						break;
					}

				}

				if ( $match ) {
					if ( ! $filter ) {
						$result[] = $row;
					} else {
						$result[] = call_user_func( $filter, $row );
					}
				}
			}

			return $result;

		}

	}

}
