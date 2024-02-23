<?php
/**
 * Class YITH_WCBK_Product_Post_Type_Admin
 * handle the Booking product post type in Admin
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Product_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_WCBK_Product_Post_Type_Admin
	 *
	 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Product_Post_Type_Admin {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Booking product type
		 *
		 * @var string
		 * @static
		 */
		public static $prod_type = 'booking';

		/**
		 * Product meta array.
		 *
		 * @var array product meta array
		 * @deprecated 3.0.0
		 */
		public $product_meta_array = array();

		/**
		 * YITH_WCBK_Product_Post_Type_Admin constructor.
		 */
		protected function __construct() {

			// Add Booking product to WC product type selector.
			add_filter( 'product_type_selector', array( $this, 'product_type_selector' ) );
			add_filter( 'product_type_options', array( $this, 'product_type_options' ) );

			// add tabs for product booking.
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'product_booking_tabs' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'add_product_data_panels' ) );

			// save product meta.
			add_action( 'woocommerce_admin_process_product_object', array( $this, 'set_product_meta_before_saving' ) );
			add_action( 'woocommerce_process_product_meta_booking', array( $this, 'regenerate_product_data_after_saving' ) );

			// Remove Booking Services Metabox for products.
			add_action( 'add_meta_boxes', array( $this, 'manage_meta_boxes' ) );

			// Export action.
			add_filter( 'post_row_actions', array( $this, 'customize_booking_product_row_actions' ), 10, 2 );
		}

		/**
		 * Customize Booking Product Actions
		 *
		 * @param array   $actions An array of row action links. Defaults are
		 *                         'Edit', 'Quick Edit', 'Restore, 'Trash',
		 *                         'Delete Permanently', 'Preview', and 'View'.
		 * @param WP_Post $post    The post object.
		 *
		 * @return array
		 * @author      Leanza Francesco <leanzafrancesco@gmail.com>
		 * @since       2.0.0
		 */
		public function customize_booking_product_row_actions( $actions, $post ) {
			global $the_product;

			if ( 'product' !== $post->post_type ) {
				return $actions;
			}

			if ( empty( $the_product ) || $the_product->get_id() !== $post->ID ) {
				$the_product = wc_get_product( $post );
			}

			if ( yith_wcbk_is_booking_product( $the_product ) ) {
				/**
				 * Booking product.
				 *
				 * @var WC_Product_Booking $the_product
				 */

				$export_future_url = wp_nonce_url(
					add_query_arg(
						array(
							'yith_wcbk_exporter_action' => 'export_future_ics',
							'product_id'                => $the_product->get_id(),
							'key'                       => $the_product->get_external_calendars_key(),
						)
					),
					'export',
					'yith_wcbk_exporter_nonce'
				);

				$booking_actions = array(
					'yith_wcbk_export_future_ics' => array(
						'label' => __( 'Export Future ICS', 'yith-booking-for-woocommerce' ),
						'url'   => $export_future_url,
					),
					'yith_wcbk_view_calendar'     => array(
						'label' => __( 'Booking Calendar', 'yith-booking-for-woocommerce' ),
						'url'   => $the_product->get_admin_calendar_url(),
					),
				);

				foreach ( $booking_actions as $key => $action ) {
					$actions[ $key ] = "<a href='{$action['url']}'>{$action['label']}</a>";
				}
			}

			return $actions;
		}

		/**
		 * Remove Booking Services Metabox for products
		 *
		 * @param string $post_type Post type.
		 */
		public function manage_meta_boxes( $post_type ) {
			remove_meta_box( YITH_WCBK_Post_Types::SERVICE_TAX . 'div', 'product', 'side' );
		}

		/**
		 * Add data panels to products
		 */
		public function add_product_data_panels() {
			/**
			 * Product object.
			 *
			 * @var WC_Product $product_object
			 */
			global $post, $product_object;

			$tabs = array(
				'settings'     => 'yith_booking_settings_tab',
				'costs'        => 'yith_booking_costs_tab',
				'availability' => 'yith_booking_availability_tab',
				'people'       => 'yith_booking_people_tab',
				'services'     => 'yith_booking_services_tab',
				'sync'         => 'yith_booking_sync_tab',
			);

			$post_id         = $post->ID;
			$prod_type       = self::$prod_type;
			$booking_product = $product_object->is_type( self::$prod_type ) ? $product_object : false;

			foreach ( $tabs as $key => $tab_id ) {
				echo '<div id="' . esc_attr( $tab_id ) . '" class="panel woocommerce_options_panel">';
				yith_wcbk_get_view( 'product-tabs/html-' . $key . '-tab.php', compact( 'post_id', 'prod_type', 'booking_product', 'product_object', 'post' ) );
				echo '</div>';
			}
		}

		/**
		 * Add tabs for booking products
		 *
		 * @param array $tabs Tabs.
		 *
		 * @return array
		 */
		public function product_booking_tabs( $tabs ) {
			$new_tabs = array(
				'yith_booking_settings'     => array(
					'label'    => __( 'Booking Settings', 'yith-booking-for-woocommerce' ),
					'target'   => 'yith_booking_settings_tab',
					'class'    => array( 'show_if_' . self::$prod_type ),
					'priority' => 11,
				),
				'yith_booking_costs'        => array(
					'label'    => __( 'Booking Costs', 'yith-booking-for-woocommerce' ),
					'target'   => 'yith_booking_costs_tab',
					'class'    => array( 'show_if_' . self::$prod_type ),
					'priority' => 11,
				),
				'yith_booking_availability' => array(
					'label'    => __( 'Booking Availability', 'yith-booking-for-woocommerce' ),
					'target'   => 'yith_booking_availability_tab',
					'class'    => array( 'show_if_' . self::$prod_type ),
					'priority' => 11,
				),
				'yith_booking_people'       => array(
					'label'    => __( 'Booking People', 'yith-booking-for-woocommerce' ),
					'target'   => 'yith_booking_people_tab',
					'class'    => array( 'show_if_' . self::$prod_type ),
					'priority' => 11,
				),
				'yith_booking_services'     => array(
					'label'    => __( 'Booking Services', 'yith-booking-for-woocommerce' ),
					'target'   => 'yith_booking_services_tab',
					'class'    => array( 'show_if_' . self::$prod_type ),
					'priority' => 11,
				),
				'yith_booking_sync'         => array(
					'label'    => __( 'Booking Sync', 'yith-booking-for-woocommerce' ),
					'target'   => 'yith_booking_sync_tab',
					'class'    => array( 'show_if_' . self::$prod_type ),
					'priority' => 11,
				),
			);

			$tabs = array_merge( $tabs, $new_tabs );

			return apply_filters( 'yith_wcbk_product_booking_tabs', $tabs );
		}

		/**
		 * Add Booking Product type in product type selector
		 *
		 * @param array $types Product types.
		 *
		 * @return array
		 */
		public function product_type_selector( $types ) {
			$types[ self::$prod_type ] = _x( 'Bookable Product', 'Admin: type of product', 'yith-booking-for-woocommerce' );

			return $types;
		}

		/**
		 * Show "virtual" checkbox for Booking products
		 *
		 * @param array $options Options.
		 *
		 * @return array
		 * @since 2.0.3
		 */
		public function product_type_options( $options ) {
			$options['virtual']['wrapper_class'] .= ' show_if_' . self::$prod_type;

			return $options;
		}


		/**
		 * Set the product meta before saving the product
		 *
		 * @param WC_Product|WC_Product_Booking $product The product.
		 */
		public function set_product_meta_before_saving( $product ) {
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if ( $product->is_type( self::$prod_type ) ) {
				try {
					/**
					 * The Booking Product Data Store.
					 *
					 * @var YITH_WCBK_Product_Booking_Data_Store_CPT $data_store
					 */
					$data_store        = WC_Data_Store::load( 'product-booking' );
					$meta_key_to_props = $data_store->get_booking_meta_key_to_props();

					// TODO: check for un-slashing and sanitizing here.

					foreach ( $meta_key_to_props as $key => $prop ) {
						$setter = "set_{$prop}";
						if ( is_callable( array( $product, $setter ) ) ) {
							if ( $data_store->is_boolean_prop( $prop ) ) {
								$product->$setter( isset( $_POST[ $key ] ) && 'yes' === $_POST[ $key ] );
							} elseif ( $data_store->is_array_prop( $prop ) ) {
								$product->$setter( $_POST[ $key ] ?? array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
							} elseif ( isset( $_POST[ $key ] ) ) {
								$product->$setter( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
							}
						}
					}

					// Terms - Services.
					$product->set_service_ids( $_POST['_yith_booking_services'] ?? array() ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash

				} catch ( Exception $e ) {
					$message = sprintf( 'Error when trying to set product meta before saving. Exception: %s', $e->getMessage() );
					yith_wcbk_add_log( $message, YITH_WCBK_Logger_Types::ERROR, YITH_WCBK_Logger_Groups::GENERAL );
				}
			}
			// phpcs:enable
		}

		/**
		 * Regenerate product data after saving
		 *
		 * @param int $product_id Product ID.
		 */
		public function regenerate_product_data_after_saving( $product_id ) {
			yith_wcbk_regenerate_product_data( $product_id );
		}

		/**
		 * Return true if the product is Booking Product
		 *
		 * @param bool|int|WP_Post|WC_Product $product The product.
		 *
		 * @return bool
		 */
		public static function is_booking( $product = false ) {
			$product_id = false;
			if ( $product instanceof WC_Product ) {
				return $product->is_type( self::$prod_type );
			} elseif ( false === $product ) {
				$product = $GLOBALS['product'];
				if ( $product && $product instanceof WC_Product ) {
					return $product->is_type( self::$prod_type );
				}
				$product = $GLOBALS['post'];
				if ( ! $product || ! $product instanceof WP_Post ) {
					return false;
				}
				$product_id = $product->ID;
			} elseif ( is_numeric( $product ) ) {
				$product_id = absint( $product );
			} elseif ( $product instanceof WP_Post ) {
				$product_id = $product->ID;
			}

			if ( ! $product_id ) {
				return false;
			}

			$terms        = get_the_terms( $product_id, 'product_type' );
			$product_type = ! empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';

			return self::$prod_type === $product_type;
		}
	}
}
