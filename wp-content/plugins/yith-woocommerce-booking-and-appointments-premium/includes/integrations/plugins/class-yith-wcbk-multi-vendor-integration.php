<?php
/**
 * Class YITH_WCBK_Multi_Vendor_Integration
 * Multi Vendor integration
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Multi_Vendor_Integration
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.7
 */
class YITH_WCBK_Multi_Vendor_Integration extends YITH_WCBK_Integration {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * Vendor Service meta.
	 *
	 * @var string
	 */
	public static $vendor_service_meta = 'yith_shop_vendor';

	/**
	 * Vendors data.
	 *
	 * @var array
	 */
	public static $vendors_data = array();

	/**
	 * Filter vendor services enabled flag.
	 *
	 * @var bool
	 */
	public $filter_vendor_services_enabled = true;

	/**
	 * The vendor panel
	 *
	 * @var $panel YIT_Plugin_Panel_WooCommerce
	 */
	protected $panel;

	/**
	 * The Vendor panel page.
	 *
	 * @var string
	 */
	protected $panel_page = 'yith_wcbk_vendor_panel';

	/**
	 * Init
	 */
	protected function init() {
		if ( $this->is_component_active() ) {
			/* - - - A D M I N   B O O K I N G   M A N A G E M E N T - - - */
			add_action( 'yith_wcbk_multi-vendor_add_on_active_status_change', array( $this, 'add_remove_booking_capabilities_for_vendor' ) );
		} else {
			return;
		}

		/* - - - B O O K I N G S - - - */
		add_filter( 'manage_' . YITH_WCBK_Post_Types::BOOKING . '_posts_columns', array( $this, 'remove_vendor_column_in_booking_for_vendors' ), 20 );
		add_filter( 'yith_wck_booking_helper_count_booked_bookings_in_period_get_post_args', array( $this, 'suppress_vendor_filter' ), 10, 1 );

		if ( $this->is_enabled() ) {
			/* - - - PANEL - - - */
			add_action( 'admin_menu', array( $this, 'register_vendor_panel' ), 5 );
			add_action( 'yit_plugin_panel_asset_loading', array( $this, 'panel_assets_loading' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 99 );
			add_filter( 'yith_wcbk_booking_admin_screen_ids', array( $this, 'add_vendor_panel_to_admin_screen_ids' ), 10, 1 );

			/* - - - B O O K I N G S - - - */
			add_action( 'yith_wcbk_booking_created', array( $this, 'add_vendor_taxonomy_to_booking' ), 10, 3 );

			/* - - - O R D E R S - - - */
			add_filter( 'yith_wcbk_order_check_order_for_booking', array( $this, 'not_check_for_booking_in_parent_orders_with_suborders' ), 10, 3 );
			add_action( 'yith_wcmv_checkout_order_processed', array( yith_wcbk()->orders, 'check_order_for_booking' ), 999, 1 ); // check (sub)orders for booking.
			add_filter( 'yith_wcbk_order_bookings_related_to_order', array( $this, 'add_bookings_related_to_suborders' ), 10, 2 );
			add_filter( 'yith_wcbk_booking_details_order_id', array( $this, 'show_parent_order_id' ) );
			add_filter( 'yith_wcbk_email_booking_details_order_id', array( $this, 'show_parent_order_id_in_emails' ), 10, 5 );
			add_filter( 'yith_wcbk_pdf_booking_details_order_id', array( $this, 'show_parent_order_id_in_pdf' ), 10, 3 );

			/* - - - S E R V I C E S - - - */
			if ( is_admin() ) {
				add_action( 'pre_get_terms', array( $this, 'filter_vendor_services' ) );
				add_filter( 'wp_unique_term_slug', array( $this, 'unique_term_slug_for_vendors' ), 10, 3 );
				add_filter( 'pre_get_terms', array( $this, 'filter_services_by_vendor_or_admin_when_creating_services' ) );
			}
			add_action( 'yith_wcbk_service_fields_set', array( $this, 'set_vendor_in_services' ), 10, 1 );
			add_filter( 'yith_wcbk_service_tax_get_service_taxonomy_fields', array( $this, 'add_vendor_info_in_services' ) );
			add_action( 'after-' . YITH_WCBK_Post_Types::SERVICE_TAX . '-table', array( $this, 'add_vendor_filter_in_services' ) );
			add_filter( 'yith_wcbk_booking_services_list_additional_columns', array( $this, 'add_vendor_column_in_services' ) );
			add_filter( 'manage_' . YITH_WCBK_Post_Types::SERVICE_TAX . '_custom_column', array( $this, 'print_vendor_column_in_services' ), 10, 3 );
			add_filter( 'yith_wcmv_disable_post', array( $this, 'allow_editing_services' ), 20 );
			add_filter( 'yith_plugin_fw_panel_url', array( $this, 'add_post_type_to_services_url_in_panel_nav' ), 10, 3 );

			/* - - - C A L E N D A R - - - */
			add_filter( 'yith_wcbk_json_search_booking_products_args', array( $this, 'filter_args_to_return_vendor_booking_products_only' ), 10, 1 );
			add_filter( 'yith_wcbk_calendar_url_query_args', array( $this, 'calendar_url_query_args' ), 10, 1 );
			add_filter( 'yith_wcbk_admin_js_disable_wc_check_for_changes', array( $this, 'admin_js_disable_wc_check_for_changes' ), 10, 1 );
			$show_externals = 'yes' === get_option( 'yith-wcbk-external-calendars-show-externals-in-calendar', 'no' );
			if ( $show_externals ) {
				add_filter( 'yith_wcbk_calendar_booking_list_bookings', array( $this, 'filter_external_bookings_in_calendar' ) );
			}

			if ( is_admin() && ! ( YITH_WCBK()->is_request( 'ajax' ) ) ) {
				add_filter( 'yith_wcbk_pre_get_bookings_args', array( $this, 'filter_args_to_return_vendor_bookings_only' ), 10, 1 );
			}

			/* - - - E M A I L - - - */
			add_filter( 'woocommerce_email_classes', array( $this, 'add_email_classes' ), 20 );
		} else {
			// Hide Booking Products in Admin, if integration is not active.
			add_filter( 'product_type_selector', array( $this, 'remove_booking_in_product_type_selector_for_vendors' ), 999 );
			add_action( 'init', array( $this, 'remove_booking_data_panels_for_vendors' ), 999 );

		}
	}

	/**
	 * Retrieve the current Vendor.
	 *
	 * @return YITH_Vendor|false
	 * @since 2.1.28
	 */
	public function get_current_vendor() {
		if ( function_exists( 'yith_get_vendor' ) ) {
			$vendor = yith_get_vendor( 'current', 'user' );
			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				return $vendor;
			}
		}

		return false;
	}

	/**
	 * Register specific panel for vendor.
	 */
	public function register_vendor_panel() {
		$vendor = $this->get_current_vendor();
		if ( $vendor ) {
			if ( ! empty( $this->panel ) ) {
				return;
			}

			$tabs = array(
				'vendor-all-bookings' => _x( 'Bookings', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				'vendor-calendar'     => _x( 'Calendar', 'Tab title in vendor plugin settings panel', 'yith-booking-for-woocommerce' ),
				'vendor-services'     => _x( 'Services', 'Tab title in vendor plugin settings panel', 'yith-booking-for-woocommerce' ),
			);

			$args = array(
				'create_menu_page' => true,
				'parent_slug'      => '',
				'class'            => yith_set_wrapper_class(),
				'page_title'       => 'Booking and Appointment for WooCommerce',
				'menu_title'       => 'Booking',
				'capability'       => 'edit_yith_bookings',
				'parent'           => '',
				'parent_page'      => '',
				'page'             => $this->panel_page,
				'admin-tabs'       => $tabs,
				'icon_url'         => 'dashicons-calendar',
				'position'         => 30,
				'options-path'     => YITH_WCBK_DIR . '/includes/integrations/plugins/multi-vendor/panel',
			);

			$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
		}
	}

	/**
	 * Is this the Vendor Membership panel?
	 *
	 * @param string $tab The tab.
	 *
	 * @return bool
	 */
	public function is_panel( $tab = '' ) {
		$screen    = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
		$screen_id = $screen ? $screen->id : false;

		if ( $screen_id && strpos( $screen_id, $this->panel_page ) !== false ) {
			if ( ! $tab ) {
				return true;
			} elseif ( isset( $_GET['tab'] ) && $tab === $_GET['tab'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return true;
			}
		}

		return false;
	}

	/**
	 * Load panel assets in vendor panel page
	 *
	 * @param bool $load Load flag.
	 *
	 * @return bool
	 */
	public function panel_assets_loading( $load ) {
		if ( $this->is_panel() ) {
			$load = true;
		}

		return $load;
	}

	/**
	 * Add Vendor panel to admin screen IDs to allow enqueuing admin styles and scripts.
	 *
	 * @param array $screen_ids Screen IDs.
	 *
	 * @return array
	 */
	public function add_vendor_panel_to_admin_screen_ids( $screen_ids ) {
		$screen_ids[] = 'toplevel_page_' . $this->panel_page;

		return $screen_ids;
	}

	/**
	 * Enqueue Admin Scripts and Styles
	 */
	public function admin_enqueue_scripts() {
		if ( $this->is_panel( 'vendor-calendar' ) ) {
			wp_enqueue_script( 'yith-wcbk-admin-booking-calendar' );
			wp_enqueue_style( 'yith-wcbk-admin-booking-calendar' );
		}
	}

	/**
	 * Filter 'get_post' args to return vendor booking products only.
	 *
	 * @param array $args The 'get_post' args.
	 *
	 * @return array
	 * @since 2.1.28
	 */
	public function filter_args_to_return_vendor_booking_products_only( $args ) {
		$vendor = $this->get_current_vendor();
		if ( $vendor ) {
			$args['tax_query']   = $args['tax_query'] ?? array(); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			$args['tax_query'][] = array(
				'taxonomy' => YITH_Vendors()->get_taxonomy_name(),
				'field'    => 'id',
				'terms'    => $vendor->id,
			);
		}

		return $args;
	}

	/**
	 * Filter Calendar URL query args.
	 *
	 * @param array $args The  args.
	 *
	 * @return array
	 * @since 3.0.2
	 */
	public function calendar_url_query_args( $args ) {
		$vendor = $this->get_current_vendor();
		if ( $vendor ) {
			$args = array(
				'page' => $this->panel_page,
				'tab'  => 'vendor-calendar',
			);
		}

		return $args;
	}

	/**
	 * Disable WC check for changes in JS.
	 *
	 * @param bool $disable Disable flag.
	 *
	 * @return bool
	 * @since 3.0.2
	 */
	public function admin_js_disable_wc_check_for_changes( $disable ) {
		if ( $this->is_panel( 'vendor-calendar' ) ) {
			$disable = true;
		}

		return $disable;
	}

	/**
	 * Filter args to return vendor bookings only in calendar
	 *
	 * @param array $args The args.
	 *
	 * @return array
	 * @since 3.0.0
	 */
	public function filter_args_to_return_vendor_bookings_only( $args ) {
		$vendor = $this->get_current_vendor();
		if ( $vendor ) {
			$args['data_query']   = $args['data_query'] ?? array();
			$args['data_query'][] = array(
				'data-type' => 'term',
				'taxonomy'  => YITH_Vendors()->get_taxonomy_name(),
				'terms'     => $vendor->id,
				'operator'  => 'IN',
				'field'     => 'id',
			);
		}

		return $args;
	}

	/**
	 * Suppress filters for booking post type to avoid issues when retrieving booking product availability through AJAX.
	 * This way when the plugin search for "bookings" it'll retrieve all bookings regardless the vendor
	 *
	 * @param array $args Arguments.
	 *
	 * @return array
	 * @see   YITH_WCMV_Addons_Compatibility::filter_vendor_post_types (since 3.3.7)
	 * @since 2.1.4
	 */
	public function suppress_vendor_filter( $args ) {
		$args['yith_wcmv_addons_suppress_filter'] = true;

		return $args;
	}

	/**
	 * Filter externals in calendar to show the vendor ones only
	 *
	 * @param YITH_WCBK_Booking[]|YITH_WCBK_Booking_External[] $bookings Bookings.
	 *
	 * @return YITH_WCBK_Booking[]|YITH_WCBK_Booking_External[]
	 */
	public function filter_external_bookings_in_calendar( $bookings ) {
		if ( function_exists( 'yith_get_vendor' ) ) {
			$vendor = yith_get_vendor( 'current', 'user' );
			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				$vendor_product_ids = array_map( 'absint', $vendor->get_products() );
				foreach ( $bookings as $key => $booking ) {
					if ( $booking->is_external() && ! in_array( $booking->get_product_id(), $vendor_product_ids, true ) ) {
						unset( $bookings[ $key ] );
					}
				}
			}
		}

		return $bookings;
	}

	/**
	 * Remove booking data panels in product for vendors if the integration is not active
	 */
	public function remove_booking_data_panels_for_vendors() {
		if ( function_exists( 'yith_get_vendor' ) ) {
			$vendor = yith_get_vendor( 'current', 'user' );
			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				$product_cpt = YITH_WCBK_Product_Post_Type_Admin::get_instance();
				remove_filter( 'woocommerce_product_data_tabs', array( $product_cpt, 'product_booking_tabs' ), 10 );
				remove_action( 'woocommerce_product_options_general_product_data', array( $product_cpt, 'add_options_to_general_product_data' ), 10 );
			}
		}
	}

	/**
	 * Remove vendor column in bookings for vendors.
	 *
	 * @param array $columns The columns.
	 *
	 * @return array
	 */
	public function remove_vendor_column_in_booking_for_vendors( $columns ) {
		if ( function_exists( 'yith_get_vendor' ) && function_exists( 'YITH_Vendors' ) && isset( $columns[ 'taxonomy-' . YITH_Vendors()->get_taxonomy_name() ] ) ) {
			$vendor = yith_get_vendor( 'current', 'user' );
			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				unset( $columns[ 'taxonomy-' . YITH_Vendors()->get_taxonomy_name() ] );
			}
		}

		return $columns;
	}

	/**
	 * Show parent order ID in emails.
	 *
	 * @param int               $order_id      Order ID.
	 * @param YITH_WCBK_Booking $booking       The Booking.
	 * @param bool              $sent_to_admin Sent to admin flag.
	 * @param string            $plain_text    Plain text.
	 * @param WC_Email          $email         The email.
	 *
	 * @return mixed
	 */
	public function show_parent_order_id_in_emails( $order_id, $booking, $sent_to_admin, $plain_text, $email ) {
		if ( ! $email instanceof YITH_WCBK_Email_Booking_Status ) {
			return $this->show_parent_order_id( $order_id );
		}

		return $order_id;
	}

	/**
	 * Show parent order ID in PDF.
	 *
	 * @param int               $order_id Order ID.
	 * @param YITH_WCBK_Booking $booking  The booking.
	 * @param bool              $is_admin Is-admin flag.
	 *
	 * @return mixed
	 */
	public function show_parent_order_id_in_pdf( $order_id, $booking, $is_admin ) {
		if ( ! $is_admin ) {
			return $this->show_parent_order_id( $order_id );
		}

		return $order_id;
	}

	/**
	 * Retrieve the parent order ID.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return int
	 */
	public function show_parent_order_id( $order_id ) {
		$parent_id = wp_get_post_parent_id( $order_id );

		return ! ! $parent_id ? $parent_id : $order_id;
	}

	/**
	 * Add booking related to suborders to display them in parent order details
	 *
	 * @param YITH_WCBK_Booking[] $bookings Bookings.
	 * @param WC_Order            $order    The order.
	 *
	 * @return array
	 */
	public function add_bookings_related_to_suborders( $bookings, $order ) {
		$suborder_ids = YITH_Vendors()->orders->get_suborder( $order->get_id() );

		if ( ! ! $bookings || ! is_array( $bookings ) ) {
			$bookings = array();
		}

		if ( ! ! $suborder_ids && is_array( $suborder_ids ) ) {
			foreach ( $suborder_ids as $suborder_id ) {
				$suborder_bookings = yith_wcbk()->booking_helper->get_bookings_by_order( $suborder_id );
				if ( ! ! $suborder_bookings && is_array( $suborder_bookings ) ) {
					$bookings = array_merge( $bookings, $suborder_bookings );
				}
			}
		}

		return $bookings;
	}

	/**
	 * Add email classes to WooCommerce ones.
	 *
	 * @param WC_Email[] $emails Emails.
	 *
	 * @return WC_Email[]
	 */
	public function add_email_classes( $emails ) {
		$emails['YITH_WCBK_Email_Vendor_New_Booking']    = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-vendor-new-booking.php';
		$emails['YITH_WCBK_Email_Booking_Status_Vendor'] = include YITH_WCBK_DIR . '/includes/emails/class-yith-wcbk-email-booking-status-vendor.php';

		return $emails;
	}

	/**
	 * Remove booking product type in product type selector for vendors
	 *
	 * @param array $types Types.
	 *
	 * @return array
	 */
	public function remove_booking_in_product_type_selector_for_vendors( $types ) {
		if ( function_exists( 'yith_get_vendor' ) && isset( $types[ YITH_WCBK_Product_Post_Type_Admin::$prod_type ] ) ) {
			$vendor = yith_get_vendor( 'current', 'user' );
			if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
				unset( $types[ YITH_WCBK_Product_Post_Type_Admin::$prod_type ] );
			}
		}

		return $types;
	}

	/**
	 * Disable check for bookings in orders with suborders
	 *
	 * @param bool  $check    Check flag.
	 * @param int   $order_id Order ID.
	 * @param array $posted   Posted arguments.
	 *
	 * @return bool
	 */
	public function not_check_for_booking_in_parent_orders_with_suborders( $check, $order_id, $posted ) {
		$has_suborders = ! ! get_post_meta( $order_id, 'has_sub_order', true );
		if ( $has_suborders ) {
			// parent order.
			return false;
		}

		return $check;
	}

	/**
	 * Add vendor taxonomy to booking when it's created
	 *
	 * @param YITH_WCBK_Booking $booking The booking.
	 */
	public function add_vendor_taxonomy_to_booking( $booking ) {
		if ( $booking->get_product_id() ) {
			$vendor = yith_get_vendor( $booking->get_product_id(), 'product' );

			if ( $vendor->is_valid() ) {
				wp_set_object_terms( $booking->get_id(), $vendor->term->slug, $vendor->term->taxonomy, false );
			}
		}
	}

	/**
	 * Sdd/remove booking capabilities for vendor based on "integration is active or not".
	 *
	 * @param string $activation_status The activation status('yes' or 'no').
	 */
	public function add_remove_booking_capabilities_for_vendor( $activation_status ) {
		$action      = 'yes' === $activation_status ? 'add' : 'remove';
		$vendor_role = get_role( YITH_Vendors()->get_role_name() );
		if ( $vendor_role ) {

			$booking_post_type = YITH_WCBK_Post_Types::BOOKING;
			$booking_caps      = array(
				'edit_post'            => "edit_{$booking_post_type}",
				'edit_posts'           => "edit_{$booking_post_type}s",
				'edit_others_posts'    => "edit_others_{$booking_post_type}s",
				'read_private_posts'   => "read_private_{$booking_post_type}s",
				'edit_private_posts'   => "edit_private_{$booking_post_type}s",
				'edit_published_posts' => "edit_published_{$booking_post_type}s",
			);

			$service_caps = array(
				'manage_terms' => 'manage_' . YITH_WCBK_Post_Types::SERVICE_TAX . 's',
				'edit_terms'   => 'edit_' . YITH_WCBK_Post_Types::SERVICE_TAX . 's',
				'delete_terms' => 'delete' . YITH_WCBK_Post_Types::SERVICE_TAX . 's',
				'assign_terms' => 'assign' . YITH_WCBK_Post_Types::SERVICE_TAX . 's',
			);

			$caps = array_merge( $booking_caps, $service_caps );

			foreach ( $caps as $key => $cap ) {
				if ( 'add' === $action ) {
					$vendor_role->add_cap( $cap );
				} elseif ( 'remove' === $action ) {
					$vendor_role->remove_cap( $cap );
				}
			}
		}
	}

	/**
	 * Filter services by vendor or admin to allow creating vendor services with the same name of admin services
	 *
	 * @param WP_Term_Query $term_query Term query.
	 */
	public function filter_services_by_vendor_or_admin_when_creating_services( $term_query ) {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if (
			isset( $_REQUEST['yith_booking_service_data'], $_REQUEST['yith_booking_service_data']['yith_shop_vendor'] )
			&& $this->filter_vendor_services_enabled && function_exists( 'yith_get_vendor' )
			&& isset( $term_query->query_vars['taxonomy'] ) && array( YITH_WCBK_Post_Types::SERVICE_TAX ) === $term_query->query_vars['taxonomy']
		) {
			$vendor_id = absint( $_REQUEST['yith_booking_service_data']['yith_shop_vendor'] );
			$vendor    = $vendor_id ? yith_get_vendor( $vendor_id ) : false;
			if ( $vendor && $vendor->is_valid() ) {
				$meta_query = array(
					array(
						'key'   => self::$vendor_service_meta,
						'value' => $vendor->id,
					),
				);
			} else {
				$meta_query = array(
					array(
						'relation' => 'OR',
						array(
							'key'   => self::$vendor_service_meta,
							'value' => '',
						),
						array(
							'key'     => self::$vendor_service_meta,
							'compare' => 'NOT EXISTS',
						),
					),
				);
			}
			if ( ! empty( $term_query->query_vars['meta_query'] ) && is_array( $term_query->query_vars['meta_query'] ) ) {
				$meta_query = array_merge( $meta_query, $term_query->query_vars['meta_query'] );
			}

			$term_query->query_vars['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}
		// phpcs:enable
	}

	/**
	 * Filter the vendor services.
	 *
	 * @param WP_Term_Query $term_query Term query.
	 */
	public function filter_vendor_services( $term_query ) {
		global $pagenow;
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if (
			$this->filter_vendor_services_enabled && function_exists( 'yith_get_vendor' )
			&& isset( $term_query->query_vars['taxonomy'] ) && array( YITH_WCBK_Post_Types::SERVICE_TAX ) === $term_query->query_vars['taxonomy']
		) {
			$vendor                             = yith_get_vendor( 'current', 'user' );
			$is_vendor                          = $vendor->is_valid() && $vendor->has_limited_access();
			$is_service_edit_page_filter_vendor = 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && YITH_WCBK_Post_Types::SERVICE_TAX === $_GET['taxonomy'] && ! empty( $_GET[ self::$vendor_service_meta ] );

			if ( $is_vendor || $is_service_edit_page_filter_vendor ) {
				if ( $is_vendor ) {
					$vendor_id = $vendor->id;
				} else {
					// $is_service_edit_page_filter_vendor
					$vendor_id = wc_clean( wp_unslash( $_GET[ self::$vendor_service_meta ] ) );
				}

				if ( 'mine' !== $vendor_id ) {
					$meta_query = array(
						array(
							'key'   => self::$vendor_service_meta,
							'value' => $vendor_id,
						),
					);
				} else {
					$meta_query = array(
						array(
							'relation' => 'OR',
							array(
								'key'   => self::$vendor_service_meta,
								'value' => '',
							),
							array(
								'key'     => self::$vendor_service_meta,
								'compare' => 'NOT EXISTS',
							),
						),
					);
				}

				if ( ! empty( $term_query->query_vars['meta_query'] ) && is_array( $term_query->query_vars['meta_query'] ) ) {
					$meta_query = array_merge( $meta_query, $term_query->query_vars['meta_query'] );
				}

				$term_query->query_vars['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			}
		}
		// phpcs:enable
	}

	/**
	 * Filter unique term slug to allow Vendor to add services with the same name of the admin services
	 *
	 * @param string  $slug          Slug.
	 * @param WP_Term $term          The term.
	 * @param string  $original_slug Original slug.
	 *
	 * @return string
	 * @since 1.0.14
	 */
	public function unique_term_slug_for_vendors( $slug, $term, $original_slug ) {
		if ( isset( $term->taxonomy ) && YITH_WCBK_Post_Types::SERVICE_TAX === $term->taxonomy ) {
			remove_filter( 'wp_unique_term_slug', array( $this, __FUNCTION__ ), 10 );
			$this->filter_vendor_services_enabled = false;

			$slug = wp_unique_term_slug( $original_slug, $term );

			add_filter( 'wp_unique_term_slug', array( $this, __FUNCTION__ ), 10, 3 );
			$this->filter_vendor_services_enabled = true;
		}

		return $slug;
	}

	/**
	 * Add Vendor ID in services
	 *
	 * @param YITH_WCBK_Service $service The service.
	 */
	public function set_vendor_in_services( $service ) {
		$vendor = yith_get_vendor( 'current', 'user' );
		if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
			$service->set( self::$vendor_service_meta, $vendor->id );
		}
	}

	/**
	 * Allow editing and seeing services for vendors
	 *
	 * @param bool $disable_post Disable post flag.
	 *
	 * @return bool
	 * @since 2.0.9
	 */
	public function allow_editing_services( $disable_post ) {
		global $pagenow;

		// phpcs:disable WordPress.Security.NonceVerification.Missing

		$is_edit_tag         = 'edit-tags.php' === $pagenow;
		$is_edit_action      = ! empty( $_POST['action'] ) && 'editedtag' === $_POST['action'];
		$is_booking_taxonomy = ! empty( $_POST['taxonomy'] ) && YITH_WCBK_Post_Types::SERVICE_TAX === $_POST['taxonomy'];

		if ( $is_edit_tag && $is_edit_action && $is_booking_taxonomy ) {
			$disable_post = false;
		}

		// phpcs:enable

		return $disable_post;
	}

	/**
	 * Add Post type param to Services URL, to allow vendors seeing that page.
	 *
	 * @param string $url  The Tab URL.
	 * @param string $page The page.
	 * @param string $tab  The tab.
	 *
	 * @return string
	 * @since 3.0.0
	 */
	public function add_post_type_to_services_url_in_panel_nav( $url, $page, $tab ) {
		if ( $page === $this->panel_page && 'vendor-services' === $tab ) {
			$url = add_query_arg(
				array(
					'post_type' => YITH_WCBK_Post_Types::BOOKING,
				),
				$url
			);
		}

		return $url;
	}

	/**
	 * Add Vendor info in sevices to show vendor dropdown
	 *
	 * @param array $info Service info.
	 *
	 * @return array
	 */
	public function add_vendor_info_in_services( $info ) {
		$vendor = yith_get_vendor( 'current', 'user' );
		if ( ! $vendor->is_valid() || ! $vendor->has_limited_access() ) {
			$vendors = self::get_vendors( array( 'fields' => 'id=>name' ) );

			if ( ! $vendors || ! is_array( $vendors ) ) {
				$vendors = array();
			}

			$vendors[''] = __( 'None', 'yith-booking-for-woocommerce' );
			asort( $vendors );

			$info[ self::$vendor_service_meta ] = array(
				'title'   => __( 'Vendor', 'yith-booking-for-woocommerce' ),
				'type'    => 'select',
				'default' => '',
				'options' => $vendors,
				'desc'    => '',
			);
		}

		return $info;
	}

	/**
	 * Add vendor filter form and dropdown in services
	 */
	public function add_vendor_filter_in_services() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$vendor = yith_get_vendor( 'current', 'user' );
		if ( ! $vendor->is_valid() || ! $vendor->has_limited_access() ) {
			$vendors = self::get_vendors( array( 'fields' => 'id=>name' ) );
			if ( ! $vendors || ! is_array( $vendors ) ) {
				$vendors = array();
			}

			$vendors['']     = __( 'All', 'yith-booking-for-woocommerce' );
			$vendors['mine'] = __( 'Mine', 'yith-booking-for-woocommerce' );

			asort( $vendors );

			$get_params = ! empty( $_GET ) ? $_GET : array();

			echo '<div class="yith-wcbk-services-filter-by-vendor-form yith-wcbk-move alignleft actions" data-after=".tablenav.top > .bulkactions">';
			echo '<form method="get">';
			foreach ( $get_params as $key => $value ) {
				if ( self::$vendor_service_meta === $key ) {
					continue;
				}
				echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" />';
			}

			$selected_vendor = isset( $_REQUEST[ self::$vendor_service_meta ] ) ? absint( $_REQUEST[ self::$vendor_service_meta ] ) : 0;

			echo '<select name="' . esc_attr( self::$vendor_service_meta ) . '">';
			foreach ( $vendors as $vendor_id => $vendor_name ) {
				echo '<option value="' . esc_attr( $vendor_id ) . '" ' . selected( $selected_vendor, absint( $vendor_id ) ) . '>' . esc_html( $vendor_name ) . '</option>';
			}
			echo '</select>';

			echo '<input type="submit" class="button" value="' . esc_html__( 'Filter by Vendor', 'yith-booking-for-woocommerce' ) . '">';

			echo '</form>';
			echo '</div>';
		}
		// phpcs:enable
	}

	/**
	 * Add Vendor column in services
	 *
	 * @param array $columns The columns.
	 *
	 * @return array The columns list
	 */
	public function add_vendor_column_in_services( $columns ) {
		$vendor = yith_get_vendor( 'current', 'user' );
		if ( ! $vendor->is_valid() || ! $vendor->has_limited_access() ) {
			$columns['service_vendor'] = __( 'Vendor', 'yith-booking-for-woocommerce' );
		}

		return $columns;
	}

	/**
	 * Print Vendor column in services
	 *
	 * @param string $custom_column Filtered value.
	 * @param string $column_name   Column name.
	 * @param int    $term_id       The term ID.
	 *
	 * @return string The column value.
	 */
	public function print_vendor_column_in_services( $custom_column, $column_name, $term_id ) {
		$service = yith_get_booking_service( $term_id );
		if ( 'service_vendor' === $column_name ) {
			$vendor_meta = self::$vendor_service_meta;
			$vendor_id   = absint( $service->$vendor_meta );
			if ( ! ! $vendor_id ) {
				$vendor = yith_get_vendor( $vendor_id );
				if ( $vendor->is_valid() ) {
					$link        = add_query_arg(
						array(
							self::$vendor_service_meta => $vendor->id,
						)
					);
					$vendor_name = $vendor->name;
					// translators: %s is the vendor name.
					$title = sprintf( _x( 'Filter by %s', 'Filter by Vendor name', 'yith-booking-for-woocommerce' ), $vendor_name );

					$custom_column .= sprintf(
						'<a href="%s" title="%s">%s</a>',
						esc_url( $link ),
						esc_attr( $title ),
						esc_html( $vendor_name )
					);
				}
			}
		}

		return $custom_column;
	}

	/**
	 * Get Vendors.
	 *
	 * @param array $args Arguments.
	 *
	 * @return array|int|WP_Error
	 */
	public static function get_vendors( $args = array() ) {
		$hash = ! ! $args ? md5( implode( ' ', $args ) ) : 0;
		if ( ! isset( self::$vendors_data[ $hash ] ) ) {

			$default_args = array(
				'fields'     => 'id',
				'hide_empty' => false,
			);

			$args             = wp_parse_args( $args, $default_args );
			$args['taxonomy'] = YITH_Vendor::$taxonomy;

			self::$vendors_data[ $hash ] = yith_wcbk()->wp->get_terms( $args );
		}

		return self::$vendors_data[ $hash ];
	}
}
