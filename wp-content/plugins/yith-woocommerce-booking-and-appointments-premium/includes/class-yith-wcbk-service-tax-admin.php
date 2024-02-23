<?php
/**
 * Service Taxonomy class
 * Manage Service taxonomy
 *
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Service_Tax_Admin' ) ) {
	/**
	 * YITH_WCBK_Service_Tax_Admin
	 */
	class YITH_WCBK_Service_Tax_Admin {
		/**
		 * The instance of the class.
		 *
		 * @var YITH_WCBK_Service_Tax_Admin
		 */
		private static $instance;

		/**
		 * The service taxonomy name.
		 *
		 * @var string
		 * @deprecated 3.0.0 | use YITH_WCBK_Post_Types::$service_tax instead.
		 */
		public $taxonomy_name;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Service_Tax_Admin
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCBK_Service_Tax_Admin constructor.
		 */
		private function __construct() {
			$this->taxonomy_name = YITH_WCBK_Post_Types::SERVICE_TAX;
			$taxonomy            = YITH_WCBK_Post_Types::SERVICE_TAX;

			add_action( $taxonomy . '_add_form_fields', array( $this, 'add_taxonomy_fields' ), 1, 1 );
			add_action( $taxonomy . '_edit_form_fields', array( $this, 'edit_taxonomy_fields' ), 1, 1 );

			add_action( 'edited_' . $taxonomy, array( $this, 'save_taxonomy_fields' ), 10, 2 );
			add_action( 'created_' . $taxonomy, array( $this, 'save_taxonomy_fields' ), 10, 2 );

			add_filter( "manage_edit-{$taxonomy}_columns", array( $this, 'get_columns' ) );
			add_filter( "manage_{$taxonomy}_custom_column", array( $this, 'custom_columns' ), 12, 3 );

			add_filter( 'tag_row_actions', array( $this, 'remove_row_actions' ), 10, 2 );
		}

		/**
		 * Add fields to Service taxonomy [Add New Service Screen]
		 *
		 * @param string $taxonomy Current taxonomy.
		 */
		public function add_taxonomy_fields( $taxonomy ) {
			if ( apply_filters( 'yith_wcbk_add_taxonomy_fields_display', true, $taxonomy ) ) {
				include YITH_WCBK_VIEWS_PATH . 'taxonomies/service/html-add-service.php';
			}
		}

		/**
		 * Filter columns
		 *
		 * @param array $columns The columns.
		 *
		 * @return array The columns list
		 * @use   manage_{$this->screen->id}_columns filter
		 */
		public function get_columns( $columns ) {
			$to_remove = array( 'posts', 'slug', 'description' );

			foreach ( $to_remove as $column ) {
				if ( isset( $columns[ $column ] ) ) {
					unset( $columns[ $column ] );
				}
			}

			$columns['service_price'] = __( 'Price', 'yith-booking-for-woocommerce' );

			$additional_columns = apply_filters( 'yith_wcbk_booking_services_list_additional_columns', array() );
			$columns            = $columns + $additional_columns;

			$columns['actions'] = __( 'Actions', 'yith-booking-for-woocommerce' );

			return $columns;
		}

		/**
		 * Display custom columns for Service Taxonomy
		 *
		 * @param string $custom_column Filter value.
		 * @param string $column_name   Column name.
		 * @param int    $term_id       The term id.
		 *
		 * @internal param \The $columns columns
		 * @use      manage_{$this->screen->taxonomy}_custom_column filter
		 */
		public function custom_columns( $custom_column, $column_name, $term_id ) {
			$service = yith_get_booking_service( $term_id );
			switch ( $column_name ) {
				case 'service_price':
					$person_types_pricing = '';
					if ( $service->is_multiply_per_persons() ) {
						$person_types = yith_wcbk()->person_type_helper->get_person_type_ids();
						if ( ! ! $person_types ) {
							foreach ( $person_types as $person_type_id ) {
								$pt_price = $service->get_price_html( $person_type_id );
								$pt_title = get_the_title( $person_type_id );

								$person_types_pricing .= $pt_title . ': ' . $pt_price . '<br />';
							}
						}
					}

					if ( ! ! $person_types_pricing ) {
						$price_html    = $service->get_price_html();
						$custom_column = "<span class='tips' data-tip='$person_types_pricing'>$price_html</span>";
					} else {
						$custom_column = $service->get_price_html();
					}
					break;

				case 'actions':
					$actions = yith_plugin_fw_get_default_term_actions( $term_id );

					foreach ( $actions as $action ) {
						$custom_column .= yith_plugin_fw_get_component( $action, false );
					}

					break;
			}

			return $custom_column;
		}


		/**
		 * Edit fields to service taxonomy
		 *
		 * @param WP_Term $service_term The service term.
		 *
		 * @return void
		 * @author Leanza Francesco <leanzafrancesco@gmail.com>
		 */
		public function edit_taxonomy_fields( $service_term ) {
			if ( ! apply_filters( 'yith_wcbk_edit_taxonomy_fields_display', true, $service_term ) ) {
				return;
			}

			$service_id = $service_term->term_id;
			$service    = yith_get_booking_service( $service_id, $service_term );
			include YITH_WCBK_VIEWS_PATH . 'taxonomies/service/html-edit-service.php';
		}


		/**
		 * Save extra taxonomy fields for service taxonomy
		 *
		 * @param int $term_id The term ID.
		 *
		 * @return void
		 * @since  1.0
		 */
		public function save_taxonomy_fields( $term_id = 0 ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( ! isset( $_POST['yith_booking_service_data'] ) ) {
				return;
			}

			$service_data = wp_unslash( $_POST['yith_booking_service_data'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing
			$service_data = wp_parse_args( $service_data, YITH_WCBK_Service::get_default_meta_data() );

			if ( is_array( $service_data ) && ! ! ( $service_data ) ) {
				$service = yith_get_booking_service( $term_id );
				if ( $service->is_valid() ) {
					foreach ( $service_data as $key => $value ) {
						$service->set( $key, $value );
					}

					do_action( 'yith_wcbk_service_fields_set', $service, $service_data );
				}
			}

			do_action( 'yith_wcbk_service_tax_taxonomy_fields_saved', $term_id, $service_data );
		}

		/**
		 * Retrieve info for the taxonomy service
		 *
		 * @param string $service_arg_name Service name.
		 * @param string $service_args     Service args.
		 *
		 * @return array
		 * @deprecated 3.0.0
		 */
		public static function get_service_taxonomy_info( $service_arg_name = '', $service_args = '' ) {
			return array();
		}


		/**
		 * Retrieve field for the "service" taxonomy
		 * used to create settings
		 *
		 * @return array|mixed|string
		 * @since 2.1
		 */
		public static function get_service_taxonomy_fields() {
			$currency_html = '<span class="yith-wcbk-currency">' . get_woocommerce_currency_symbol() . '</span>';
			$service_array = array(
				'price'                  => array(
					'title'                => __( 'Price', 'yith-booking-for-woocommerce' ),
					'type'                 => 'text',
					'class'                => 'wc_input_price',
					'default'              => '',
					'desc'                 => __( 'Select the price for this service.', 'yith-booking-for-woocommerce' ),
					'yith-wcbk-after-html' => $currency_html,
				),
				'optional'               => array(
					'title'       => __( 'Set as optional - Let customers choose to add it or not', 'yith-booking-for-woocommerce' ),
					'short_title' => __( 'Set as optional', 'yith-booking-for-woocommerce' ),
					'type'        => 'checkbox',
					'default'     => 'no',
					'desc'        => __( 'Select if this service is optional (let customers choose to add it or not).', 'yith-booking-for-woocommerce' ),
				),
				'hidden'                 => array(
					'title'   => __( 'Hide to customers', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if you want to hide this service to customers.', 'yith-booking-for-woocommerce' ),
				),
				'hidden_in_search_forms' => array(
					'title'      => __( 'Hide in booking search forms', 'yith-booking-for-woocommerce' ),
					'type'       => 'checkbox',
					'default'    => 'no',
					'desc'       => __( 'Select if you want to hide this service to customers in Booking Search Forms.', 'yith-booking-for-woocommerce' ),
					'field_deps' => array(
						'id'    => 'hidden',
						'value' => 'no',
					),
				),
				'multiply_per_blocks'    => array(
					'title'   => __( 'Multiply cost by units selected', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if you want to multiply the cost of this service by the number of units selected.', 'yith-booking-for-woocommerce' ),
				),
				'multiply_per_persons'   => array(
					'title'   => __( 'Multiply cost by people', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if you want to multiply the cost of this service by the number of people selected.', 'yith-booking-for-woocommerce' ),
				),

			);

			/* Add service price for Peron Types */
			$person_types = yith_wcbk()->person_type_helper->get_person_types_array();
			foreach ( $person_types as $_id => $_title ) {
				$service_array[ 'price_for_pt_' . $_id ] = array(
					// translators: %s is the person type. Ex: Price for Children.
					'title'                => sprintf( _x( 'Price for %s', 'Price for person type', 'yith-booking-for-woocommerce' ), $_title ),
					'type'                 => 'text',
					'class'                => 'wc_input_price',
					'default'              => '',
					'name'                 => 'price_for_person_types[' . $_id . ']',
					// translators: %s is the person type.
					'desc'                 => sprintf( __( 'Select this service price for %s. Leave empty to use default price', 'yith-booking-for-woocommerce' ), $_title ),
					'person_type_id'       => $_id,
					'field_deps'           => array(
						'id'    => 'multiply_per_persons',
						'value' => 'yes',
					),
					'yith-wcbk-after-html' => $currency_html,
				);
			}

			$quantity_options = array(
				'quantity_enabled' => array(
					'title'   => __( 'Show quantity selector', 'yith-booking-for-woocommerce' ),
					'type'    => 'checkbox',
					'default' => 'no',
					'desc'    => __( 'Select if you want to allow a quantity selection for this service.', 'yith-booking-for-woocommerce' ),
				),
				'min_quantity'     => array(
					'title'             => __( 'Min quantity', 'yith-booking-for-woocommerce' ),
					'type'              => 'number',
					'default'           => '',
					'desc'              => __( 'Choose the minimum quantity for this service.', 'yith-booking-for-woocommerce' ),
					'custom_attributes' => 'min="0"',
					'field_deps'        => array(
						'id'    => 'quantity_enabled',
						'value' => 'yes',
					),
				),
				'max_quantity'     => array(
					'title'             => __( 'Max quantity', 'yith-booking-for-woocommerce' ),
					'type'              => 'number',
					'default'           => '',
					'desc'              => __( 'Choose the maximum quantity for this service. Leave empty for unlimited', 'yith-booking-for-woocommerce' ),
					'custom_attributes' => 'min="0"',
					'field_deps'        => array(
						'id'    => 'quantity_enabled',
						'value' => 'yes',
					),
				),
			);

			$service_array = $service_array + $quantity_options;

			return apply_filters( 'yith_wcbk_service_tax_get_service_taxonomy_fields', $service_array );
		}

		/**
		 * Remove Row Actions
		 *
		 * @param array   $actions An array of row action links. Defaults are
		 *                         'Edit', 'Quick Edit', 'Restore, 'Trash',
		 *                         'Delete Permanently', 'Preview', and 'View'.
		 * @param WP_Term $tag     The post object.
		 *
		 * @return array
		 * @since 3.0.0
		 */
		public function remove_row_actions( $actions, $tag ) {
			if ( YITH_WCBK_Post_Types::SERVICE_TAX === $tag->taxonomy ) {
				$actions = array();
			}

			return $actions;
		}
	}
}
