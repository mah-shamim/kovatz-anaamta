<?php
/**
 * Class YITH_WCBK_Post_Types
 * Post Types handler.
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Post_Types' ) ) {
	/**
	 * YITH_WCBK_Post_Types class.
	 */
	class YITH_WCBK_Post_Types {
		const BOOKING     = 'yith_booking';
		const PERSON_TYPE = 'ywcbk-person-type';
		const SEARCH_FORM = 'ywcbk-search-form';
		const EXTRA_COST  = 'ywcbk-extra-cost';
		const SERVICE_TAX = 'yith_booking_service';

		/**
		 * Booking Post Type
		 *
		 * @var string
		 * @deprecated 3.0.0 | use YITH_WCBK_Post_Types::BOOKING instead
		 */
		public static $booking = self::BOOKING;

		/**
		 * Person Type Post Type
		 *
		 * @var string
		 * @deprecated 3.0.0 | use YITH_WCBK_Post_Types::PERSON_TYPE instead
		 */
		public static $person_type = self::PERSON_TYPE;

		/**
		 * Search Form Post Type
		 *
		 * @var string
		 * @deprecated 3.0.0 | use YITH_WCBK_Post_Types::SEARCH_FORM instead
		 */
		public static $search_form = self::SEARCH_FORM;

		/**
		 * Extra Cost Post Type
		 *
		 * @var string
		 * @deprecated 3.0.0 | use YITH_WCBK_Post_Types::EXTRA_COST instead
		 */
		public static $extra_cost = self::EXTRA_COST;

		/**
		 * Service Tax
		 *
		 * @var string
		 * @deprecated 3.0.0 | use YITH_WCBK_Post_Types::SERVICE_TAX instead
		 */
		public static $service_tax = self::SERVICE_TAX;

		/**
		 * Let's init the post types, post statuses, taxonomies and data stores.
		 */
		public static function init() {
			add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
			add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
			add_action( 'init', array( __CLASS__, 'register_post_status' ), 9 );

			add_filter( 'woocommerce_data_stores', array( __CLASS__, 'register_data_stores' ), 10, 1 );

			add_action( 'plugins_loaded', array( __CLASS__, 'include_admin_handlers' ), 20 );

			add_filter( 'wp_untrash_post_status', array( __CLASS__, 'untrash_post_status' ), 10, 3 );
			add_action( 'delete_post', array( __CLASS__, 'delete_post' ), 10, 1 );
			add_action( 'deleted_post', array( __CLASS__, 'deleted_post' ), 10, 2 );
			add_action( 'trashed_post', array( __CLASS__, 'updated_trashed_post_status' ) );
			add_action( 'untrashed_post', array( __CLASS__, 'updated_trashed_post_status' ) );

			add_action( 'admin_action_yith-wcbk-add-new-post', array( __CLASS__, 'handle_new_post_creation' ) );

			add_filter( 'enter_title_here', array( __CLASS__, 'set_title_placeholder' ) );
			add_action( 'edit_form_after_title', array( __CLASS__, 'add_title_description' ) );
			add_action( 'post_submitbox_start', array( __CLASS__, 'add_save_button' ), 10, 1 );

			add_filter( 'post_updated_messages', array( __CLASS__, 'post_updated_messages' ) );
		}

		/**
		 * Include Admin Post Type and Taxonomy handlers.
		 */
		public static function include_admin_handlers() {
			require_once trailingslashit( YITH_WCBK_INCLUDES_PATH ) . 'admin/post-types/class-yith-wcbk-extra-cost-post-type-admin.php';
			require_once trailingslashit( YITH_WCBK_INCLUDES_PATH ) . 'admin/post-types/class-yith-wcbk-person-type-post-type-admin.php';
			require_once trailingslashit( YITH_WCBK_INCLUDES_PATH ) . 'admin/post-types/class-yith-wcbk-search-form-post-type-admin.php';
			require_once trailingslashit( YITH_WCBK_INCLUDES_PATH ) . 'admin/post-types/class-yith-wcbk-booking-post-type-admin.php';
		}

		/**
		 * Register core post types.
		 */
		public static function register_post_types() {
			if ( post_type_exists( self::BOOKING ) ) {
				return;
			}

			do_action( 'yith_wcbk_register_post_type' );

			// Booking -----------------------------------------------------------.
			$labels = array(
				'name'               => __( 'All Bookings', 'yith-booking-for-woocommerce' ),
				'singular_name'      => __( 'Booking', 'yith-booking-for-woocommerce' ),
				'add_new'            => __( 'Add Booking', 'yith-booking-for-woocommerce' ),
				'add_new_item'       => __( 'Add New Booking', 'yith-booking-for-woocommerce' ),
				'edit'               => __( 'Edit', 'yith-booking-for-woocommerce' ),
				'edit_item'          => __( 'Edit Booking', 'yith-booking-for-woocommerce' ),
				'new_item'           => __( 'New Booking', 'yith-booking-for-woocommerce' ),
				'view'               => __( 'View Booking', 'yith-booking-for-woocommerce' ),
				'view_item'          => __( 'View Booking', 'yith-booking-for-woocommerce' ),
				'search_items'       => __( 'Search Bookings', 'yith-booking-for-woocommerce' ),
				'not_found'          => __( 'No bookings found', 'yith-booking-for-woocommerce' ),
				'not_found_in_trash' => __( 'No bookings found in trash', 'yith-booking-for-woocommerce' ),
				'parent'             => __( 'Parent Bookings', 'yith-booking-for-woocommerce' ),
				'menu_name'          => _x( 'Bookings', 'Admin menu name', 'yith-booking-for-woocommerce' ),
				'all_items'          => __( 'All Bookings', 'yith-booking-for-woocommerce' ),
			);

			$booking_post_type_args = array(
				'label'               => __( 'Booking', 'yith-booking-for-woocommerce' ),
				'labels'              => $labels,
				'description'         => __( 'This is where bookings are stored.', 'yith-booking-for-woocommerce' ),
				'public'              => false,
				'show_ui'             => true,
				'capability_type'     => self::BOOKING,
				'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'        => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_in_menu'        => false,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => array( '' ),
				'has_archive'         => false,
				'menu_icon'           => 'dashicons-calendar',
			);

			register_post_type( self::BOOKING, $booking_post_type_args );

			// Person Type -----------------------------------------------------------.
			$labels = array(
				'menu_name'          => _x( 'People', 'Admin menu name', 'yith-booking-for-woocommerce' ),
				'all_items'          => __( 'People', 'yith-booking-for-woocommerce' ),
				'name'               => __( 'People', 'yith-booking-for-woocommerce' ),
				'singular_name'      => __( 'Person', 'yith-booking-for-woocommerce' ),
				'add_new'            => __( 'Add new type', 'yith-booking-for-woocommerce' ),
				'add_new_item'       => __( 'New type', 'yith-booking-for-woocommerce' ),
				'edit_item'          => __( 'Edit type', 'yith-booking-for-woocommerce' ),
				'view_item'          => __( 'View this type', 'yith-booking-for-woocommerce' ),
				'search_items'       => __( 'Search person type', 'yith-booking-for-woocommerce' ),
				'not_found'          => __( 'Type not found', 'yith-booking-for-woocommerce' ),
				'not_found_in_trash' => __( 'Type not found in trash', 'yith-booking-for-woocommerce' ),
			);

			$person_type_args = array(
				'labels'              => $labels,
				'public'              => false,
				'show_ui'             => true,
				'menu_position'       => 10,
				'exclude_from_search' => true,
				'capability_type'     => self::PERSON_TYPE,
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'supports'            => array( 'title', 'editor', 'thumbnail' ),
				'show_in_menu'        => false,
			);

			register_post_type( self::PERSON_TYPE, $person_type_args );

			// Search Forms -----------------------------------------------------------.
			$labels = array(
				'menu_name'          => _x( 'Search Forms', 'Admin menu name', 'yith-booking-for-woocommerce' ),
				'all_items'          => __( 'Search Forms', 'yith-booking-for-woocommerce' ),
				'name'               => __( 'Search Forms', 'yith-booking-for-woocommerce' ),
				'singular_name'      => __( 'Search form', 'yith-booking-for-woocommerce' ),
				'add_new'            => __( 'Add search form', 'yith-booking-for-woocommerce' ),
				'add_new_item'       => __( 'New search form', 'yith-booking-for-woocommerce' ),
				'edit_item'          => __( 'Edit search form', 'yith-booking-for-woocommerce' ),
				'view_item'          => __( 'View search form', 'yith-booking-for-woocommerce' ),
				'search_items'       => _x( 'Search', 'Search label for "search forms"', 'yith-booking-for-woocommerce' ),
				'not_found'          => __( 'Search form not found', 'yith-booking-for-woocommerce' ),
				'not_found_in_trash' => __( 'Search form not found in trash', 'yith-booking-for-woocommerce' ),
			);

			$search_form_args = array(
				'labels'              => $labels,
				'public'              => false,
				'show_ui'             => true,
				'menu_position'       => 10,
				'exclude_from_search' => true,
				'capability_type'     => self::SEARCH_FORM,
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'supports'            => array( 'title' ),
				'show_in_menu'        => false,
			);

			register_post_type( self::SEARCH_FORM, $search_form_args );

			// Extra Costs. -----------------------------------------------------------.
			$labels = array(
				'menu_name'          => _x( 'Extra Costs', 'Admin menu name', 'yith-booking-for-woocommerce' ),
				'all_items'          => __( 'Extra Costs', 'yith-booking-for-woocommerce' ),
				'name'               => __( 'Extra Costs', 'yith-booking-for-woocommerce' ),
				'singular_name'      => __( 'Extra Cost', 'yith-booking-for-woocommerce' ),
				'add_new'            => __( 'Add New Extra Cost', 'yith-booking-for-woocommerce' ),
				'add_new_item'       => __( 'New Extra Cost', 'yith-booking-for-woocommerce' ),
				'edit_item'          => __( 'Edit Extra Cost', 'yith-booking-for-woocommerce' ),
				'view_item'          => __( 'View this extra cost', 'yith-booking-for-woocommerce' ),
				'search_items'       => __( 'Search extra cost', 'yith-booking-for-woocommerce' ),
				'not_found'          => __( 'Extra cost not found', 'yith-booking-for-woocommerce' ),
				'not_found_in_trash' => __( 'Extra cost not found in trash', 'yith-booking-for-woocommerce' ),
			);

			$extra_cost_args = array(
				'labels'              => $labels,
				'public'              => false,
				'show_ui'             => true,
				'menu_position'       => 10,
				'exclude_from_search' => true,
				'capability_type'     => self::EXTRA_COST,
				'map_meta_cap'        => true,
				'rewrite'             => true,
				'has_archive'         => true,
				'hierarchical'        => false,
				'show_in_nav_menus'   => false,
				'supports'            => array( 'title', 'editor', 'thumbnail' ),
				'show_in_menu'        => false,
			);

			register_post_type( self::EXTRA_COST, $extra_cost_args );
		}

		/**
		 * Register our custom post statuses, used for order status.
		 */
		public static function register_post_status() {
			foreach ( yith_wcbk_get_booking_statuses() as $status_slug => $status_label ) {
				$status_slug = 'bk-' . $status_slug;
				$options     = array(
					'label'                     => $status_label,
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					// phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralSingle,WordPress.WP.I18n.NonSingularStringLiteralPlural,WordPress.WP.I18n.MissingTranslatorsComment
					'label_count'               => _n_noop( $status_label . ' <span class="count">(%s)</span>', $status_label . ' <span class="count">(%s)</span>', 'yith-booking-for-woocommerce' ),
				);

				register_post_status( $status_slug, $options );
			}
		}

		/**
		 * Register core taxonomies.
		 */
		public static function register_taxonomies() {
			if ( taxonomy_exists( self::SERVICE_TAX ) ) {
				return;
			}

			$objects = apply_filters( 'yith_wcbk_taxonomy_objects_booking_service', array( 'product', self::BOOKING ) );
			$args    = apply_filters(
				'yith_wcbk_taxonomy_args_booking_service',
				array(
					'hierarchical'      => true,
					'label'             => __( 'Booking Services', 'yith-booking-for-woocommerce' ),
					'labels'            => array(
						'name'                       => __( 'Booking Services', 'yith-booking-for-woocommerce' ),
						'singular_name'              => __( 'Booking Service', 'yith-booking-for-woocommerce' ),
						'menu_name'                  => _x( 'Booking Services', 'Admin menu name', 'yith-booking-for-woocommerce' ),
						'all_items'                  => __( 'All Booking Services', 'yith-booking-for-woocommerce' ),
						'edit_item'                  => __( 'Edit Booking Service', 'yith-booking-for-woocommerce' ),
						'view_item'                  => __( 'View Booking Service', 'yith-booking-for-woocommerce' ),
						'update_item'                => __( 'Update Booking Service', 'yith-booking-for-woocommerce' ),
						'add_new_item'               => __( 'Add New Booking Service', 'yith-booking-for-woocommerce' ),
						'new_item_name'              => __( 'New Booking Service Name', 'yith-booking-for-woocommerce' ),
						'parent_item'                => __( 'Parent Booking Service', 'yith-booking-for-woocommerce' ),
						'parent_item_colon'          => __( 'Parent Booking Service:', 'yith-booking-for-woocommerce' ),
						'search_items'               => __( 'Search Booking Services', 'yith-booking-for-woocommerce' ),
						'separate_items_with_commas' => __( 'Separate booking services with commas', 'yith-booking-for-woocommerce' ),
						'add_or_remove_items'        => __( 'Add or remove booking services', 'yith-booking-for-woocommerce' ),
						'choose_from_most_used'      => __( 'Choose among the most popular booking services', 'yith-booking-for-woocommerce' ),
						'not_found'                  => __( 'No booking service found.', 'yith-booking-for-woocommerce' ),
					),
					'show_ui'           => true,
					'query_var'         => true,
					'show_in_nav_menus' => false,
					'show_admin_column' => true,
					'capabilities'      => array(
						'manage_terms' => 'manage_' . self::SERVICE_TAX . 's',
						'edit_terms'   => 'edit_' . self::SERVICE_TAX . 's',
						'delete_terms' => 'delete' . self::SERVICE_TAX . 's',
						'assign_terms' => 'assign' . self::SERVICE_TAX . 's',
					),
					'rewrite'           => true,
				)
			);

			register_taxonomy( self::SERVICE_TAX, $objects, $args );
		}

		/**
		 * Add capabilities to Admin and Shop Manager
		 */
		public static function add_capabilities() {
			$admin            = get_role( 'administrator' );
			$shop_manager     = get_role( 'shop_manager' );
			$capability_types = array(
				self::BOOKING          => 'post',
				self::PERSON_TYPE      => 'post',
				self::SEARCH_FORM      => 'post',
				self::EXTRA_COST       => 'post',
				self::SERVICE_TAX      => 'tax',
				'yith_create_booking'  => 'single',
				'yith_manage_bookings' => 'single',
			);

			foreach ( $capability_types as $capability_type => $type ) {
				$caps = array();
				switch ( $type ) {
					case 'post':
						$caps = array(
							'edit_post'              => "edit_{$capability_type}",
							'delete_post'            => "delete_{$capability_type}",
							'edit_posts'             => "edit_{$capability_type}s",
							'edit_others_posts'      => "edit_others_{$capability_type}s",
							'publish_posts'          => "publish_{$capability_type}s",
							'read_private_posts'     => "read_private_{$capability_type}s",
							'delete_posts'           => "delete_{$capability_type}s",
							'delete_private_posts'   => "delete_private_{$capability_type}s",
							'delete_published_posts' => "delete_published_{$capability_type}s",
							'delete_others_posts'    => "delete_others_{$capability_type}s",
							'edit_private_posts'     => "edit_private_{$capability_type}s",
							'edit_published_posts'   => "edit_published_{$capability_type}s",
							'create_posts'           => "create_{$capability_type}s",
						);

						if ( self::BOOKING === $capability_type ) {
							unset( $caps['create_posts'] );
						}

						break;

					case 'tax':
						$caps = array(
							'manage_terms' => 'manage_' . $capability_type . 's',
							'edit_terms'   => 'edit_' . $capability_type . 's',
							'delete_terms' => 'delete' . $capability_type . 's',
							'assign_terms' => 'assign' . $capability_type . 's',
						);
						break;
					case 'single':
						$caps = array( $capability_type );
				}

				foreach ( $caps as $key => $cap ) {
					if ( $admin ) {
						$admin->add_cap( $cap );
					}

					if ( $shop_manager ) {
						$shop_manager->add_cap( $cap );
					}
				}
			}
		}

		/**
		 * Register data stores
		 *
		 * @param array $data_stores WooCommerce Data Stores.
		 *
		 * @return array
		 */
		public static function register_data_stores( $data_stores ) {
			$data_stores['product-booking'] = 'YITH_WCBK_Product_Booking_Data_Store_CPT';
			$data_stores['yith-booking']    = 'YITH_WCBK_Booking_Data_Store';

			return $data_stores;
		}

		/**
		 * Ensure statuses are correctly reassigned when restoring CPT.
		 *
		 * @param string $new_status      The new status of the post being restored.
		 * @param int    $post_id         The ID of the post being restored.
		 * @param string $previous_status The status of the post at the point where it was trashed.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public static function untrash_post_status( $new_status, $post_id, $previous_status ) {
			$post_types = array( self::BOOKING, self::SEARCH_FORM, self::EXTRA_COST, self::PERSON_TYPE );

			if ( in_array( get_post_type( $post_id ), $post_types, true ) ) {
				$new_status = $previous_status;
			}

			return $new_status;
		}

		/**
		 * Clean product cache when deleting booking object.
		 *
		 * @param int $id ID of post being deleted.
		 *
		 * @since 3.0.0
		 */
		public static function delete_post( $id ) {
			if ( ! $id ) {
				return;
			}

			$post_type = get_post_type( $id );

			switch ( $post_type ) {
				case self::BOOKING:
					$booking = yith_get_booking( $id );
					if ( $booking ) {
						yith_wcbk_regenerate_product_data( $booking->get_product_id() );
					}
					break;
			}
		}

		/**
		 * Removes deleted bookings from lookup table.
		 *
		 * @param int     $id   ID of post being deleted.
		 * @param WP_Post $post The post being deleted.
		 *
		 * @throws Exception If the data store loading fails.
		 * @since 3.0.0
		 */
		public static function deleted_post( $id, $post = false ) {
			if ( ! $id ) {
				return;
			}

			if ( ! $post ) {
				// The $post arg was added to 'deleted_post' action in WordPress 5.5.
				// TODO: remove this line when removing support for WordPress < 5.5.
				$post = get_post( $id );
			}

			if ( ! $post ) {
				return;
			}

			$post_type = $post->post_type;

			switch ( $post_type ) {
				case self::BOOKING:
					/**
					 * The Booking Data Store
					 *
					 * @var YITH_WCBK_Booking_Data_Store $data_store
					 */
					$data_store = WC_Data_Store::load( 'yith-booking' );
					$data_store->delete_from_lookup_table( $id, YITH_WCBK_DB::BOOKING_META_LOOKUP_TABLE );
					break;
			}
		}

		/**
		 * Update status for trashed/un-trashed bookings in lookup table.
		 *
		 * @param mixed $id ID of post being trashed/un-trashed.
		 *
		 * @throws Exception If the data store loading fails.
		 * @since 3.0.0
		 */
		public static function updated_trashed_post_status( $id ) {
			if ( ! $id ) {
				return;
			}

			$post_type = get_post_type( $id );

			switch ( $post_type ) {
				case self::BOOKING:
					/**
					 * The Booking Data Store
					 *
					 * @var YITH_WCBK_Booking_Data_Store $data_store
					 */
					$data_store = WC_Data_Store::load( 'yith-booking' );
					$data_store->update_booking_meta_lookup_table( $id );

					$booking = yith_get_booking( $id );
					if ( $booking ) {
						yith_wcbk_regenerate_product_data( $booking->get_product_id() );
					}
					break;
			}
		}

		/**
		 * Handle new post creation.
		 *
		 * @since 3.0.0
		 */
		public static function handle_new_post_creation() {
			if (
				isset( $_REQUEST['yith-wcbk-add-new-post-nonce'], $_REQUEST['post_type'] ) &&
				wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['yith-wcbk-add-new-post-nonce'] ) ), 'yith-wcbk-add-new-post' )
			) {
				$post_type  = sanitize_text_field( wp_unslash( $_REQUEST['post_type'] ) );
				$post_types = array( self::EXTRA_COST, self::PERSON_TYPE );

				if ( in_array( $post_type, $post_types, true ) ) {
					$post_type_object = get_post_type_object( $post_type );

					if ( $post_type_object && current_user_can( $post_type_object->cap->create_posts ) ) {
						$name        = sanitize_text_field( wp_unslash( $_REQUEST['name'] ?? '' ) );
						$description = sanitize_textarea_field( wp_unslash( $_REQUEST['description'] ?? '' ) );

						if ( $name ) {
							$post_id = wp_insert_post(
								array(
									'post_title'   => wp_slash( $name ),
									'post_type'    => $post_type,
									'post_status'  => 'publish',
									'post_content' => wp_slash( $description ),
								)
							);

							if ( $post_id ) {
								$redirect_url = add_query_arg( array( 'post_type' => $post_type ), admin_url( 'edit.php' ) );
								wp_safe_redirect( $redirect_url );
								exit;
							}
						}
					}
				}
			}

			wp_die( esc_html__( 'Something went wrong. Try again!', 'yith-booking-for-woocommerce' ) );
		}

		/**
		 * Set the "title" placeholder.
		 *
		 * @param string $placeholder Title placeholder.
		 *
		 * @return string
		 * @since 3.0.0
		 */
		public static function set_title_placeholder( $placeholder ) {
			global $post_type;

			$titles = array(
				self::PERSON_TYPE => __( 'Person type name', 'yith-booking-for-woocommerce' ),
				self::EXTRA_COST  => __( 'Extra cost name', 'yith-booking-for-woocommerce' ),
				self::SEARCH_FORM => __( 'Search form name', 'yith-booking-for-woocommerce' ),
			);

			return array_key_exists( $post_type, $titles ) ? $titles[ $post_type ] : $placeholder;
		}

		/**
		 * Add title description
		 *
		 * @since 3.0.0
		 */
		public static function add_title_description() {
			global $post_type;

			$descriptions = array(
				self::PERSON_TYPE => __( 'Enter a name to identify this person type.', 'yith-booking-for-woocommerce' ),
				self::EXTRA_COST  => __( 'Enter a name to identify this extra cost.', 'yith-booking-for-woocommerce' ),
				self::SEARCH_FORM => __( 'Enter a name to identify this search form.', 'yith-booking-for-woocommerce' ),
			);

			if ( array_key_exists( $post_type, $descriptions ) ) {
				?>
				<div id="yith-wcbk-cpt-title__wrapper">
					<div id="yith-wcbk-cpt-title__field"></div>
					<div id="yith-wcbk-cpt-title__description">
						<?php echo esc_html( $descriptions[ $post_type ] ); ?>
					</div>
				</div>

				<script type="text/javascript">
					( function () {
						document.getElementById( 'yith-wcbk-cpt-title__field' ).appendChild( document.getElementById( 'title' ) );
						document.getElementById( 'titlewrap' ).appendChild( document.getElementById( 'yith-wcbk-cpt-title__wrapper' ) );
					} )();
				</script>
				<?php
			}
		}

		/**
		 * Add Save button instead of the "publish" one.
		 *
		 * @param WP_Post $post The post.
		 *
		 * @since 3.0.0
		 */
		public static function add_save_button( $post ) {
			if ( in_array( $post->post_type, array( self::PERSON_TYPE, self::EXTRA_COST, self::SEARCH_FORM ), true ) ) {
				echo '<span id="yith-wcbk-cpt-publishing-actions" class="yith-plugin-ui">';
				echo '<span id="yith-wcbk-cpt-save" class="yith-plugin-fw__button--primary">' . esc_html( _x( 'Save', 'Save button', 'yith-booking-for-woocommerce' ) ) . '</span>';
				echo '</span>';
				echo '<style>#publishing-action { display : none; }</style>';
			}
		}

		/**
		 * Change messages when a post type is updated.
		 *
		 * @param array $messages Array of messages.
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public static function post_updated_messages( $messages ) {
			$messages[ self::BOOKING ] = array(
				1 => __( 'Booking updated.', 'yith-booking-for-woocommerce' ),
				4 => __( 'Booking updated.', 'yith-booking-for-woocommerce' ),
				7 => __( 'Booking saved.', 'yith-booking-for-woocommerce' ),
			);

			$messages[ self::PERSON_TYPE ] = array(
				1  => __( 'Person type updated.', 'yith-booking-for-woocommerce' ),
				4  => __( 'Person type updated.', 'yith-booking-for-woocommerce' ),
				6  => __( 'Person type published.', 'yith-booking-for-woocommerce' ),
				7  => __( 'Person type saved.', 'yith-booking-for-woocommerce' ),
				8  => __( 'Person type submitted.', 'yith-booking-for-woocommerce' ),
				10 => __( 'Person type draft updated.', 'yith-booking-for-woocommerce' ),
			);

			$messages[ self::EXTRA_COST ] = array(
				1  => __( 'Extra cost updated.', 'yith-booking-for-woocommerce' ),
				4  => __( 'Extra cost updated.', 'yith-booking-for-woocommerce' ),
				6  => __( 'Extra cost published.', 'yith-booking-for-woocommerce' ),
				7  => __( 'Extra cost saved.', 'yith-booking-for-woocommerce' ),
				8  => __( 'Extra cost submitted.', 'yith-booking-for-woocommerce' ),
				10 => __( 'Extra cost draft updated.', 'yith-booking-for-woocommerce' ),
			);

			$messages[ self::SEARCH_FORM ] = array(
				1  => __( 'Search form updated.', 'yith-booking-for-woocommerce' ),
				4  => __( 'Search form updated.', 'yith-booking-for-woocommerce' ),
				6  => __( 'Search form published.', 'yith-booking-for-woocommerce' ),
				7  => __( 'Search form saved.', 'yith-booking-for-woocommerce' ),
				8  => __( 'Search form submitted.', 'yith-booking-for-woocommerce' ),
				10 => __( 'Search form draft updated.', 'yith-booking-for-woocommerce' ),
			);

			return $messages;
		}
	}
}
