<?php
/**
 * Class YITH_WCBK_Admin
 * Admin Class
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Admin' ) ) {
	/**
	 * YITH_WCBK_Admin class.
	 *
	 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Admin {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBK_Admin
		 */
		private static $instance;

		/**
		 * The panel
		 *
		 * @var YIT_Plugin_Panel_WooCommerce $panel
		 */
		private $panel;

		/**
		 * The panel page
		 *
		 * @var string
		 */
		private $panel_page = 'yith_wcbk_panel';

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Admin
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCBK_Admin constructor.
		 */
		private function __construct() {
			add_filter( 'admin_body_class', array( $this, 'add_classes_to_body' ) );

			add_action( 'admin_menu', array( $this, 'register_panel' ), 5 );

			add_action( 'yith_wcbk_print_global_availability_rules_tab', array( $this, 'print_global_availability_rules_tab' ) );
			add_action( 'yith_wcbk_print_global_price_rules_tab', array( $this, 'print_global_price_rules_tab' ) );
			add_action( 'yith_wcbk_print_integrations_tab', array( $this, 'print_integrations_tab' ) );
			add_action( 'yith_wcbk_print_google_calendar_tab', array( $this, 'print_google_calendar_tab' ) );
			add_action( 'yith_wcbk_print_logs_tab', array( $this, 'print_logs_tab' ) );

			add_filter( 'yith_plugin_fw_panel_wc_extra_row_classes', array( $this, 'add_class_to_fields_having_after_html' ), 10, 2 );
			add_action( 'yith_plugin_fw_get_field_after', array( $this, 'print_field_after_html' ), 10, 1 );

			add_filter( 'plugin_action_links_' . plugin_basename( YITH_WCBK_DIR . '/' . basename( YITH_WCBK_FILE ) ), array( $this, 'action_links' ) );
			add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 3 );

			YITH_WCBK_Product_Post_Type_Admin::get_instance();
			YITH_WCBK_Service_Tax_Admin::get_instance();
			YITH_WCBK_Admin_Assets::get_instance();
			YITH_WCBK_Tools::get_instance();

			YITH_WCBK_Booking_Calendar::get_instance();

			YITH_WCBK_Legacy_Elements::get_instance();

			$this->notices();
		}

		/**
		 * Add classes in body
		 *
		 * @param string $classes The classes.
		 *
		 * @return string
		 */
		public function add_classes_to_body( $classes ) {
			$classes .= ' yith-booking-admin';

			return $classes;
		}

		/**
		 * Action Links
		 * add the action links to plugin admin page
		 *
		 * @param array $links Plugin links.
		 *
		 * @return  array
		 * @use     plugin_action_links_{$plugin_file_name}
		 */
		public function action_links( $links ) {
			return yith_add_action_links( $links, $this->panel_page, defined( 'YITH_WCBK_PREMIUM' ), YITH_WCBK_SLUG );
		}

		/**
		 * Adds action links to plugin admin page
		 *
		 * @param array    $row_meta_args Row meta args.
		 * @param string[] $plugin_meta   An array of the plugin's metadata, including the version, author, author URI, and plugin URI.
		 * @param string   $plugin_file   Path to the plugin file relative to the plugins directory.
		 *
		 * @return array
		 */
		public function plugin_row_meta( $row_meta_args, $plugin_meta, $plugin_file ) {
			if ( YITH_WCBK_INIT === $plugin_file ) {
				$row_meta_args['slug']       = YITH_WCBK_SLUG;
				$row_meta_args['is_premium'] = defined( 'YITH_WCBK_PREMIUM' );
			}

			return $row_meta_args;
		}

		/**
		 * Print the Global availability rules tab
		 */
		public function print_global_availability_rules_tab() {
			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-global-availability-rules.php';
		}

		/**
		 * Print the Global price rules tab
		 */
		public function print_global_price_rules_tab() {
			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-global-price-rules.php';
		}

		/**
		 * Print the Integrations tab
		 */
		public function print_integrations_tab() {
			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-integrations.php';
		}

		/**
		 * Print the Google Calendar tab
		 */
		public function print_google_calendar_tab() {
			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-google-calendar.php';
		}

		/**
		 * Print the Google Calendar tab
		 */
		public function print_logs_tab() {
			$logger = yith_wcbk_logger();

			if ( ! empty( $_REQUEST['yith-wcbk-logs-action'] ) ) {
				switch ( $_REQUEST['yith-wcbk-logs-action'] ) {
					case 'delete-logs':
						if ( isset( $_REQUEST['yith-wcbk-logs-nonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_REQUEST['yith-wcbk-logs-nonce'] ) ), 'yith_wcbk_delete_logs' ) ) {
							$logger->delete_logs();
							wp_safe_redirect( remove_query_arg( array( 'yith-wcbk-logs-action', 'yith-wcbk-logs-nonce' ) ) );
							exit;
						}
						break;
				}
			}

			include YITH_WCBK_VIEWS_PATH . 'settings-tabs/html-logs.php';
		}

		/**
		 * Print an HTML after the field, if set.
		 *
		 * @param array $field The field.
		 *
		 * @since 3.0.0
		 */
		public function print_field_after_html( $field ) {
			if ( ! empty( $field['yith-wcbk-after-html'] ) ) {
				echo '<span class="yith-wcbk-plugin-fw-field__after-html">' . $field['yith-wcbk-after-html'] . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		/**
		 * Add a CSS class to fields having an "after-html" set.
		 *
		 * @param array $classes The CSS classes.
		 * @param array $field   The field.
		 *
		 * @since 3.0.0
		 */
		public function add_class_to_fields_having_after_html( $classes, $field ) {
			if ( ! empty( $field['yith-wcbk-after-html'] ) ) {
				$classes[] = 'yith-wcbk-plugin-fw-field--with-after-html';
			}

			return $classes;
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @return   void
		 * @use      YIT_Plugin_Panel_WooCommerce class
		 * @see      plugin-fw/lib/yit-plugin-panel-woocommerce.php
		 */
		public function register_panel() {
			if ( ! empty( $this->panel ) ) {
				return;
			}

			$admin_tabs = array(
				'dashboard'     => _x( 'Dashboard', 'Settings tab name', 'yith-booking-for-woocommerce' ),
				'settings'      => _x( 'Settings', 'Settings tab name', 'yith-booking-for-woocommerce' ),
				'configuration' => _x( 'Configuration', 'Settings tab name', 'yith-booking-for-woocommerce' ),
				'tools'         => _x( 'Tools', 'Settings tab name', 'yith-booking-for-woocommerce' ),
				'integrations'  => _x( 'Integrations', 'Settings tab name', 'yith-booking-for-woocommerce' ),
			);

			$admin_tabs = apply_filters( 'yith_wcbk_settings_admin_tabs', $admin_tabs );

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'page_title'       => 'Booking and Appointment for WooCommerce',
				'menu_title'       => 'Booking',
				'class'            => yith_set_wrapper_class(),
				'capability'       => 'manage_options',
				'parent'           => '',
				'parent_page'      => 'yith_plugin_panel',
				'page'             => $this->panel_page,
				'admin-tabs'       => $admin_tabs,
				'options-path'     => YITH_WCBK_DIR . '/plugin-options',
				'plugin_slug'      => YITH_WCBK_SLUG,
				'help_tab'         => array(
					'hc_url' => 'https://support.yithemes.com/hc/en-us/categories/360003475718-YITH-BOOKING-AND-APPOINTMENT-FOR-WOOCOMMERCE',
				),
			);

			$args = apply_filters( 'yith_wcbk_plugin_panel_args', $args );

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}

		/**
		 * Retrieve the panel page.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public function get_panel_page() {
			return $this->panel_page;
		}

		/**
		 * Admin notices instance.
		 *
		 * @return YITH_WCBK_Admin_Notices
		 * @since 3.0.0
		 */
		public function notices() {
			return YITH_WCBK_Admin_Notices::get_instance();
		}
	}
}

/**
 * Unique access to instance of YITH_WCBK_Admin class
 *
 * @return YITH_WCBK_Admin
 */
function yith_wcbk_admin() {
	return YITH_WCBK_Admin::get_instance();
}
