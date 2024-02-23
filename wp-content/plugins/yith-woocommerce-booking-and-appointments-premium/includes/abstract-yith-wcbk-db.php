<?php
/**
 * Class YITH_WCBK_DB
 * Handle DB
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_DB' ) ) {
	/**
	 * Class YITH_WCBK_DB
	 *
	 * @abstract
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	abstract class YITH_WCBK_DB {

		const BOOKING_NOTES_TABLE       = 'yith_wcbk_booking_notes';
		const LOGS_TABLE                = 'yith_wcbk_logs';
		const EXTERNAL_BOOKINGS_TABLE   = 'yith_wcbk_external_bookings';
		const BOOKING_META_LOOKUP_TABLE = 'yith_wcbk_booking_meta_lookup';

		/**
		 * Booking Notes table
		 *
		 * @var string
		 * @deprecated 3.5 | use YITH_WCBK_DB::BOOKING_NOTES_TABLE instead
		 */
		public static $booking_notes_table = 'yith_wcbk_booking_notes';

		/**
		 * Log table
		 *
		 * @var string
		 * @deprecated 3.5 | use YITH_WCBK_DB::LOGS_TABLE instead
		 */
		public static $log_table = 'yith_wcbk_logs';

		/**
		 * External Bookings table
		 *
		 * @var string
		 * @deprecated 3.5 | use YITH_WCBK_DB::EXTERNAL_BOOKINGS_TABLE instead
		 */
		public static $external_bookings_table = 'yith_wcbk_external_bookings';

		/**
		 * Install
		 *
		 * @deprecated 3.0.0
		 */
		public static function install() {
			self::create_db_tables();
		}

		/**
		 * Register custom tables within $wpdb object.
		 */
		public static function define_tables() {
			global $wpdb;

			// List of tables without prefixes.
			$tables = array(
				self::BOOKING_NOTES_TABLE       => self::BOOKING_NOTES_TABLE,
				self::LOGS_TABLE                => self::LOGS_TABLE,
				self::EXTERNAL_BOOKINGS_TABLE   => self::EXTERNAL_BOOKINGS_TABLE,
				self::BOOKING_META_LOOKUP_TABLE => self::BOOKING_META_LOOKUP_TABLE,
			);

			foreach ( $tables as $name => $table ) {
				$wpdb->$name    = $wpdb->prefix . $table;
				$wpdb->tables[] = $table;
			}
		}

		/**
		 * Create tables
		 *
		 * @noinspection SqlNoDataSourceInspection
		 */
		public static function create_db_tables() {
			global $wpdb;

			$wpdb->hide_errors();

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			$booking_notes_table       = $wpdb->prefix . self::BOOKING_NOTES_TABLE;
			$logs_table                = $wpdb->prefix . self::LOGS_TABLE;
			$external_bookings_table   = $wpdb->prefix . self::EXTERNAL_BOOKINGS_TABLE;
			$booking_meta_lookup_table = $wpdb->prefix . self::BOOKING_META_LOOKUP_TABLE;
			$collate                   = '';

			if ( $wpdb->has_cap( 'collation' ) ) {
				$collate = $wpdb->get_charset_collate();
			}

			$sql = "CREATE TABLE $booking_notes_table (
						`id` bigint(20) NOT NULL AUTO_INCREMENT,
						`booking_id` bigint(20) NOT NULL,
						`type` varchar(255) NOT NULL,
						`description` TEXT NOT NULL,
						`note_date` datetime NOT NULL,
						PRIMARY KEY (`id`)
                    ) $collate;
                    
					CREATE TABLE $logs_table (
						`id` bigint(20) NOT NULL AUTO_INCREMENT,
						`description` text NOT NULL,
						`type` varchar(255) NOT NULL DEFAULT '',
						`group` varchar(255) NOT NULL,
						`date` datetime NOT NULL,
						PRIMARY KEY (`id`)
                    ) $collate;

					CREATE TABLE $external_bookings_table (
						`id` bigint(20) NOT NULL AUTO_INCREMENT,
						`product_id` bigint(20),
						`from` bigint(20),
						`to` bigint(20),
						`description` text,
						`summary` text,
						`location` varchar(255),
						`uid` varchar(255),
						`calendar_name` varchar(255) DEFAULT '',
						`source` varchar(255),
						`date` datetime,
						PRIMARY KEY (`id`)
                    ) $collate;

					CREATE TABLE $booking_meta_lookup_table (
					  `booking_id` bigint(20) NOT NULL,
					  `product_id` bigint(20) NOT NULL,
					  `order_id` bigint(20) NOT NULL,
					  `user_id` bigint(20) NOT NULL,
					  `status` varchar(100) NOT NULL default 'bk-unpaid',
					  `from` datetime NOT NULL,
					  `to` datetime NOT NULL,
					  `persons` integer NOT NULL default 1,
					  PRIMARY KEY  (`booking_id`),
					  KEY `product_id` (`product_id`),
					  KEY `order_id` (`order_id`),
					  KEY `user_id` (`user_id`),
					  KEY `status` (`status`)
					) $collate;
                    ";

			dbDelta( $sql );
		}
	}
}
