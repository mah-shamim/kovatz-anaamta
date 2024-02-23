<?php
/**
 * Jet_Search_Token_Manager class
 *
 * @package   jet-search
 * @author    Zemez
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Search_Token_Manager' ) ) {

	/**
	 * Define Jet_Search_Token_Manager class
	 */
	class Jet_Search_Token_Manager {

        /**
		 * A reference to an instance of this class.
		 *
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

        private $enabled = false;

        /**
		 * Constructor for the class
		 */
        public function init() {
            //Schedule token cleanup event.
            add_action( 'wp',                     array( $this, 'schedule_token_cleanup' ) );
			add_action( 'clean_old_tokens_event', array( $this, 'clean_old_tokens' ) );
		}

        /**
		 * Clean old tokens by removing expired sessions from the database.
		 */

        public function clean_old_tokens() {
            global $wpdb;

            $table_name     = $wpdb->prefix . 'jet_search_suggestions_sessions';
            $current_time   = current_time('mysql');
            $time_threshold = date( 'Y-m-d 00:00:01', strtotime( $current_time ) );

            $wpdb->query( $wpdb->prepare( "DELETE FROM $table_name WHERE created_at < %s", $time_threshold ) );
        }

		/**
		 * Schedule regular cleanup of old tokens.
		 */
		public function schedule_token_cleanup() {

            $use_session = get_option( 'jet_search_suggestions_use_session' );

			if ( false != $use_session && 'true' === $use_session ) {
                global $wpdb;

                $tokens_table_name = $wpdb->prefix . 'jet_search_suggestions_sessions';

                if ( empty( $wpdb->get_var( "SHOW TABLES LIKE '$tokens_table_name'" ) ) ) {
                    jet_search()->db->create_all_tables();
                }

                if ( defined('DISABLE_WP_CRON') ) {
                    if ( DISABLE_WP_CRON ) {
                        $this->clean_old_tokens();

                        return;
                    }
                }

                $timestamp = strtotime('tomorrow midnight');

                if ( !wp_next_scheduled( 'clean_old_tokens_event' ) ) {
                    wp_schedule_event( $timestamp, 'daily', 'clean_old_tokens_event' );
                }
            }
		}

        public function add_token( $record ) {
            global $wpdb;

            $table_name = $wpdb->prefix . 'jet_search_suggestions_sessions';

            $wpdb->insert( $table_name, $record, '%s' );
        }

        /**
         * Generate a user token.
         * @return string
         */
        public function generate_token() {
            if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) ) {
                $ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else {
                $ip_address = $_SERVER['REMOTE_ADDR'];
            }

            $nonce_salt = defined('NONCE_SALT') ? NONCE_SALT : '';
            $token      = md5( $ip_address . $nonce_salt );

            return $token;
        }

        /**
         * Return the count of token records in the database.
         * @return int The count of token records
         */
        public function check_token_records( $token ) {
            global $wpdb;

            $prefix     = 'jet_';
            $table_name = $wpdb->prefix . $prefix . 'search_suggestions_sessions';

            $count_token_records = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE token = %s", $token ) );

            return $count_token_records;
        }

        /**
		 * Returns the instance.
		 *
		 * @access public
		 * @return object
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
    }
}

/**
 * Returns instance of Jet_Search_Token_Manager
 *
 * @return object
 */
function jet_search_token_manager() {
	return Jet_Search_Token_Manager::get_instance();
}

jet_search_token_manager()->init();