<?php
/**
 * Class YITH_WCBK_Admin_Assets
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Admin_Assets' ) ) {
	/**
	 * Class YITH_WCBK_Admin_Assets
	 * register and enqueue styles and scripts in Admin
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Admin_Assets {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Admin_Assets constructor.
		 */
		protected function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'register_styles' ), 11 );
			add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ), 11 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 11 );

			add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ), 99, 1 );
		}


		/**
		 * Register Styles
		 */
		public function register_styles() {
			wp_register_style( 'yith-wcbk-admin-fields', YITH_WCBK_ASSETS_URL . '/css/admin/admin-fields.css', array( 'yith-wcbk', 'yith-plugin-fw-fields' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-admin-settings-sections', YITH_WCBK_ASSETS_URL . '/css/admin/admin-settings-sections.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-admin', YITH_WCBK_ASSETS_URL . '/css/admin/admin.css', array( 'yith-wcbk', 'yith-wcbk-admin-fields', 'yith-wcbk-admin-settings-sections' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-admin-rtl', YITH_WCBK_ASSETS_URL . '/css/admin/admin-rtl.css', array(), YITH_WCBK_VERSION );

			wp_register_style( 'yith-wcbk-admin-booking', YITH_WCBK_ASSETS_URL . '/css/admin/admin-booking.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-admin-booking-calendar', YITH_WCBK_ASSETS_URL . '/css/admin/admin-booking-calendar.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-admin-booking-search-form', YITH_WCBK_ASSETS_URL . '/css/admin/admin-booking-search-form.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-admin-integrations', YITH_WCBK_ASSETS_URL . '/css/admin/admin-integrations.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-admin-logs', YITH_WCBK_ASSETS_URL . '/css/admin/admin-logs.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-admin-service-taxonomy', YITH_WCBK_ASSETS_URL . '/css/admin/admin-service-taxonomy.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );
			wp_register_style( 'yith-wcbk-admin-orders', YITH_WCBK_ASSETS_URL . '/css/admin/admin-orders.css', array( 'yith-wcbk' ), YITH_WCBK_VERSION );
		}

		/**
		 * Register Scripts
		 */
		public function register_scripts() {
			$suffix     = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$bk         = YITH_WCBK_Common_Assets::get_bk_global_params( 'admin' );
			$wcbk_admin = array(
				'prod_type'                    => YITH_WCBK_Product_Post_Type_Admin::$prod_type,
				'loader_svg'                   => yith_wcbk_print_svg( 'loader', false ),
				'i18n_delete_log_confirmation' => esc_js( __( 'Are you sure you want to delete logs?', 'yith-booking-for-woocommerce' ) ),
				'i18n_untitled'                => __( 'Untitled', 'yith-booking-for-woocommerce' ),
				'i18n_leave_page_confirmation' => __( 'The changes you made will be lost if you navigate away from this page.', 'yith-booking-for-woocommerce' ),
				'i18n_copied'                  => __( 'Copied!', 'yith-booking-for-woocommerce' ),
				'i18n_durations'               => $bk['i18n_durations'], // kept for backward-compatibility.
				'i18n'                         => array(
					'create_booking'          => _x( 'Create Booking', 'Popup title', 'yith-booking-for-woocommerce' ),
					'themeInstallationFailed' => _x( 'Installation failed!', 'Theme', 'yith-booking-for-woocommerce' ),
					'themeInstall'            => _x( 'Install', 'Theme', 'yith-booking-for-woocommerce' ),
					'themeInstalled'          => _x( 'Installed!', 'Theme', 'yith-booking-for-woocommerce' ),
					'themeInstalling'         => _x( 'Installing...', 'Theme', 'yith-booking-for-woocommerce' ),
					'themeActivate'           => _x( 'Activate', 'Theme', 'yith-booking-for-woocommerce' ),
					'themeNetworkEnable'      => _x( 'Network enable', 'Theme', 'yith-booking-for-woocommerce' ),
					'themeNetworkEnabling'    => _x( 'Enabling...', 'Theme', 'yith-booking-for-woocommerce' ),
					'themeNetworkEnabled'     => _x( 'Enabled', 'Theme', 'yith-booking-for-woocommerce' ),
				),
				'nonces'                       => array(
					'get_booking_form' => wp_create_nonce( 'yith-wcbk-get-booking-form' ),
					'themeAction'      => wp_create_nonce( 'yith-wcbk-theme-action' ),
				),
				'disableWcCheckForChanges'     => apply_filters( 'yith_wcbk_admin_js_disable_wc_check_for_changes', $this->is( 'settings', 'dashboard', 'bookings-calendar' ) || $this->is( 'settings', 'tools', 'logs' ) ),
			);

			wp_register_script( 'yith-wcbk-admin', YITH_WCBK_ASSETS_URL . '/js/admin/admin' . $suffix . '.js', array( 'jquery', 'jquery-tiptip', 'yith-wcbk-datepicker', 'yith-plugin-fw-fields' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-admin-booking-availability-rules', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-availability-rules' . $suffix . '.js', array( 'jquery', 'yith-wcbk-datepicker', 'wp-util' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-admin-booking-price-rules', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-price-rules' . $suffix . '.js', array( 'jquery', 'yith-wcbk-datepicker', 'wp-util' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-admin-booking-calendar', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-calendar' . $suffix . '.js', array( 'jquery', 'jquery-blockui' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-admin-booking-create', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-create' . $suffix . '.js', array( 'jquery', 'yith-wcbk-booking-form', 'yith-wcbk-fields' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-admin-booking-edit-services', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-edit-services' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-admin-booking-meta-boxes', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-meta-boxes' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-admin-booking-product', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-product' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'yith-wcbk-datepicker', 'jquery-ui-sortable', 'google-maps' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-admin-booking-search-form', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-search-form' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-admin-booking-settings-sections', YITH_WCBK_ASSETS_URL . '/js/admin/admin-booking-settings-sections' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-admin-prevent-leave-on-changes', YITH_WCBK_ASSETS_URL . '/js/admin/admin-prevent-leave-on-changes' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );
			wp_register_script( 'yith-wcbk-admin-suggested-themes', YITH_WCBK_ASSETS_URL . '/js/admin/suggested-themes' . $suffix . '.js', array( 'jquery', 'updates' ), YITH_WCBK_VERSION, true );

			wp_register_script( 'yith-wcbk-enhanced-select', YITH_WCBK_ASSETS_URL . '/js/admin/enhanced-select' . $suffix . '.js', array( 'jquery' ), YITH_WCBK_VERSION, true );

			$google_maps_key = get_option( 'yith-wcbk-google-maps-api-key', '' );
			$google_maps_key = ! ! $google_maps_key ? "&key=$google_maps_key" : '';
			wp_register_script( 'google-maps', "//maps.google.com/maps/api/js?libraries=places$google_maps_key", false, '3', true );

			// Localize.
			wp_localize_script( 'yith-wcbk-admin', 'bk', $bk );
			wp_localize_script( 'yith-wcbk-admin', 'wcbk_admin', $wcbk_admin );
			wp_localize_script( 'yith-wcbk-admin-booking-settings-sections', 'wcbk_admin', $wcbk_admin );
			wp_localize_script( 'yith-wcbk-admin-booking-product', 'bk', $bk );
			wp_localize_script( 'yith-wcbk-admin-booking-product', 'wcbk_admin', $wcbk_admin );
			wp_localize_script( 'yith-wcbk-admin-booking-create', 'bk', $bk );
			wp_localize_script( 'yith-wcbk-admin-booking-create', 'wcbk_admin', $wcbk_admin );
			wp_localize_script( 'yith-wcbk-admin-prevent-leave-on-changes', 'wcbk_admin', $wcbk_admin );
			wp_localize_script( 'yith-wcbk-admin-suggested-themes', 'wcbk_admin', $wcbk_admin );
			wp_localize_script(
				'yith-wcbk-enhanced-select',
				'yith_wcbk_enhanced_select_params',
				array(
					'ajax_url'              => admin_url( 'admin-ajax.php' ),
					'search_bookings_nonce' => wp_create_nonce( 'search-bookings' ),
					'search_orders_nonce'   => wp_create_nonce( 'search-orders' ),
				)
			);
		}


		/**
		 * Enqueue scripts and styles
		 */
		public function enqueue() {
			global $wp_scripts;

			// Booking admin screen ids and Settings Panels.
			if ( $this->is( yith_wcbk_booking_admin_screen_ids() ) || $this->is( 'settings' ) ) {
				$jquery_version = $wp_scripts->registered['jquery-ui-core']->ver ?? '1.9.2';

				wp_enqueue_script( 'jquery-tiptip' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'yith-wcbk-admin' );
				wp_enqueue_script( 'yith-wcbk-enhanced-select' );

				wp_enqueue_style( 'yith-wcbk-admin' );
				wp_enqueue_style( 'yith-wcbk-datepicker' );
				wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );
			}

			// General Settings.
			if ( $this->is( 'settings', 'settings', 'general-settings' ) ) {
				wp_enqueue_script( 'yith-wcbk-admin-suggested-themes' );
			}

			// Calendar.
			if ( $this->is( 'settings', 'dashboard', 'bookings-calendar' ) ) {
				wp_enqueue_script( 'yith-wcbk-admin-booking-calendar' );

				wp_enqueue_style( 'yith-wcbk-admin-booking-calendar' );
			}

			// Booking WP List.
			if ( $this->is( 'edit-' . YITH_WCBK_Post_Types::BOOKING ) ) {
				// Booking Create scripts and styles.
				wp_enqueue_script( 'yith-wcbk-admin-booking-create' );
				wp_enqueue_script( 'yith-wcbk-booking-form' );
				wp_enqueue_style( 'yith-wcbk-booking-form' );
			}

			// Booking object.
			if ( $this->is( YITH_WCBK_Post_Types::BOOKING ) ) {
				global $post;
				$post_id = ! ! $post && isset( $post->ID ) ? $post->ID : '';

				$params = array(
					'post_id'                   => $post_id,
					'add_booking_note_nonce'    => wp_create_nonce( 'add-booking-note' ),
					'delete_booking_note_nonce' => wp_create_nonce( 'delete-booking-note' ),
					'i18n_delete_note'          => __( 'Are you sure you want to delete this note? This action cannot be undone.', 'yith-booking-for-woocommerce' ),
				);

				wp_localize_script( 'yith-wcbk-admin-booking-meta-boxes', 'wcbk_admin_booking_meta_boxes', $params );
				wp_enqueue_script( 'yith-wcbk-admin-booking-meta-boxes' );

				wp_enqueue_style( 'yith-wcbk-admin-booking' );
			}

			// Service List.
			if ( $this->is( 'edit-' . YITH_WCBK_Post_Types::SERVICE_TAX ) ) {
				wp_enqueue_script( 'yith-wcbk-admin-booking-edit-services' );

				wp_enqueue_style( 'yith-wcbk-admin-service-taxonomy' );
			}

			// Search Form.
			if ( $this->is( YITH_WCBK_Post_Types::SEARCH_FORM ) ) {
				wp_enqueue_script( 'yith-wcbk-admin-booking-search-form' );

				wp_enqueue_style( 'yith-wcbk-admin-booking-search-form' );
			}

			// Integrations TAB.
			if ( $this->is( 'settings', 'integrations' ) ) {
				wp_enqueue_style( 'yith-wcbk-admin-integrations' );

			}

			// Logs TAB.
			if ( $this->is( 'settings', 'tools', 'logs' ) ) {
				wp_enqueue_style( 'yith-wcbk-admin-logs' );
			}

			// Edit Product.
			if ( $this->is( 'product' ) ) {
				wp_enqueue_script( 'google-maps' );
				wp_enqueue_script( 'yith-wcbk-admin-booking-availability-rules' );
				wp_enqueue_script( 'yith-wcbk-admin-booking-price-rules' );
				wp_enqueue_script( 'yith-wcbk-admin-booking-product' );
				wp_enqueue_script( 'yith-wcbk-admin-booking-settings-sections' );
			}

			// Edit Order.
			if ( $this->is( 'shop_order' ) ) {
				wp_enqueue_style( 'yith-wcbk-admin-orders' );
			}

			// Settings Availability Tab.
			if ( $this->is( 'settings', 'configuration', 'availability-rules' ) ) {
				wp_enqueue_script( 'yith-wcbk-admin-booking-availability-rules' );
				wp_enqueue_script( 'yith-wcbk-admin-booking-settings-sections' );
				wp_enqueue_script( 'yith-wcbk-admin-prevent-leave-on-changes' );
			}

			// Settings Costs Tab.
			if ( $this->is( 'settings', 'configuration', 'price-rules' ) ) {
				wp_enqueue_script( 'yith-wcbk-admin-booking-price-rules' );
				wp_enqueue_script( 'yith-wcbk-admin-booking-settings-sections' );
				wp_enqueue_script( 'yith-wcbk-admin-prevent-leave-on-changes' );
			}

			if ( is_rtl() ) {
				wp_enqueue_style( 'yith-wcbk-admin-rtl' );
			}
		}

		/**
		 * Add custom screen ids to standard WC
		 *
		 * @access public
		 *
		 * @param array $screen_ids Screen IDs.
		 *
		 * @return array
		 */
		public function add_screen_ids( $screen_ids ) {
			$screen_ids[] = 'yith_booking_page_yith-wcbk-booking-calendar';
			$screen_ids[] = YITH_WCBK_Post_Types::BOOKING;
			$screen_ids[] = 'edit-' . YITH_WCBK_Post_Types::BOOKING;
			$screen_ids[] = 'edit-' . YITH_WCBK_Post_Types::SERVICE_TAX;

			return $screen_ids;
		}

		/**
		 * Which is the current page?
		 *
		 * @param array|string $id      The screen ID, or 'settings'.
		 * @param string       $tab     The tab.
		 * @param string       $sub_tab The sub-tab.
		 *
		 * @return bool
		 */
		private function is( $id, $tab = '', $sub_tab = '' ) {
			$panel_page = 'yith_wcbk_panel';
			$screen     = get_current_screen();
			$is_page    = false;

			switch ( $id ) {
				case 'settings':
					if ( strpos( $screen->id, 'page_' . $panel_page ) > 0 ) {
						if ( ! ! $tab ) {
							$is_page = isset( $_GET['tab'] ) && $_GET['tab'] === $tab; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

							if ( ! ! $sub_tab ) {
								$is_page = $is_page && isset( $_GET['sub_tab'] ) && "{$tab}-{$sub_tab}" === $_GET['sub_tab']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
							}
						} else {
							$is_page = true;
						}
					}
					break;
				default:
					if ( is_array( $id ) ) {
						$is_page = in_array( $screen->id, $id, true );
					} elseif ( $id === $screen->id ) {
						$is_page = true;
					}
					break;
			}

			return $is_page;
		}
	}
}
