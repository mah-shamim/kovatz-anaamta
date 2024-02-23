<?php
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Internal\DataStores\Orders\DataSynchronizer;
use Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableDataStore;
use Automattic\WooCommerce\Internal\Features\FeaturesController;

/**
 * Order data synchronizer class.
 *
 * @version 2.4.8
 * @since   2.4.8 - Added class.
 */
class WCV_Order_Data_Synchronizer {
    /**
     * The number of orders to process at a time.
     *
     * @var integer
     * @version 2.4.8
     * @since   2.4.8
     */
    public $batch_size = 1000;

    /**
     * List of order statuses to process.
     *
     * @var array
     * @version 2.4.8
     * @since   2.4.8
     */
    public $order_statuses = array( 'wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed', 'wc-refunded' );

    /**
     * The logger object.
     *
     * @var WC_Logger
     * @version 2.4.8
     * @since   2.4.8
     */
    public $logger;

    /**
     * Instantiate the class.
     *
     * @param int $batch_size The number of orders to process at a time.
     *
     * @version 2.4.8
     * @since   2.4.8
     */
    public function __construct( $batch_size = 1000 ) {
        $this->batch_size = apply_filters(
            'wcvendors_data_synchronization_batch_size',
            $batch_size
        );
        $this->get_logger();
    }

    /**
     * Initialize the hooks
     *
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function init_hooks() {
        add_action( 'wcvendors_scheduled_sync_order_meta_data', array( $this, 'sync_orders' ) );
        add_action( 'wcvendors_scheduled_migrate_tracking_details', array( $this, 'migrate_tracking_details' ) );
        add_action( 'wcvendors_scheduled_migrate_vendor_id', array( $this, 'migrate_vendor_id' ) );
        add_action( 'wcvendors_scheduled_verify_customer_ids', array( $this, 'verify_customer_ids' ) );
        add_action( FeaturesController::FEATURE_ENABLED_CHANGED_ACTION, array( $this, 'handle_hpos_feature_changed' ), 99, 2 );

        add_action( 'updated_option', array( $this, 'process_updated_option' ), 9999, 3 );
        add_action( 'added_option', array( $this, 'process_added_option' ), 9999, 2 );

        add_action( 'woocommerce_new_order', array( $this, 'handle_updated_order' ), 999 );
        add_action( 'woocommerce_update_order', array( $this, 'handle_updated_order' ), 0 );
        add_action( 'admin_notices', array( $this, 'update_in_progress_notice' ) );
        add_action( 'admin_notices', array( $this, 'data_sync_progress_notice' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }

    /**
	 * Process an option change for specific keys.
	 *
	 * @param string $option_key The option key.
	 * @param string $old_value  The previous value.
	 * @param string $new_value  The new value.
	 *
	 * @return void
	 */
	public function process_updated_option( $option_key, $old_value, $new_value ) { //phpcs:ignore
        if ( DataSynchronizer::ORDERS_DATA_SYNC_ENABLED_OPTION !== $option_key ) {
            return;
        }

        $this->process_background_sync();
        $this->maybe_verify_customer_ids();
    }

    /**
	 * Process an option change when the key didn't exist before.
	 *
	 * @param string $option_key The option key.
	 * @param string $value      The new value.
	 *
	 * @return void
	 */
	public function process_added_option( $option_key, $value ) {
		$this->process_updated_option( $option_key, false, $value );
	}

    /**
     * Maybe reschedule customer id verification.
     *
     * @since 2.4.8
     * @since 2.4.8
     */
    public function maybe_verify_customer_ids() {
        $mismatched_orders = $this->get_mismatched_order_customer_ids();

        if ( count( $mismatched_orders ) < 1 ) {
            delete_option( 'wcv_verify_customer_ids_complete' );
            $this->log( 'WC Vendors: No mismatched orders found.' );
            return;
        }

        $this->log( sprintf( 'WC Vendors: Scheduled action to sync %d orders', count( $mismatched_orders ) ) );

        $this->maybe_reschedule_verify_customer_ids();
        unset( $mismatched_orders );
    }

    /**
     * Run data sync and migration when the WooCommerce data sync is enabled.
     *
     * @return void
     *
     * @version 2.4.8
     * @since   2.4.8
     */
    public function verify_customer_ids() {
        global $wpdb;

        $mismatched_orders = $this->get_mismatched_order_customer_ids();

        if ( ! $mismatched_orders || count( $mismatched_orders ) < 1 ) {
            update_option( 'wcv_verify_customer_ids_complete', 'yes' );
            return;
        }

        $this->log( sprintf( 'WC Vendors: Started syncing batch of %s mismatched orders', count( $mismatched_orders ) ) );

        // Update CPT data table.
        if ( $this->cot_is_authoritative() ) {
            $this->log( 'WC Vendors: Syncing mismatched orders on COT' );

            foreach ( $mismatched_orders as $order_row ) {
                $data = array();

                $status       = $order_row->status;
                $order_status = 'wc-' !== substr( $status, 0, 3 ) ? 'wc-' . $status : $status;

                if ( $order_row->post_status !== $order_row->status || 'wc-' !== substr( $order_row->status, 0, 3 ) ) {
                    $data['post_status'] = $order_status;
                }

                if ( $order_row->customer_id !== $order_row->post_author ) {
                    $data['post_author'] = $order_row->customer_id;
                }

                $data = array_filter( $data );

                if ( empty( $data ) ) {
                    continue;
                }

                $wpdb->update(
                    $wpdb->posts,
                    $data,
                    array( 'ID' => $order_row->id )
                );
                update_post_meta( $order_row->id, '_customer_user', $order_row->customer_id );
                unset( $data );
                unset( $order_status );
            }
            $this->log( 'WC Vendors: Done syncing mismatched orders on COT' );

        } else {
            // Update COT table.
            $this->log( 'WC Vendors: Syncing mismatched orders on CPT' );

            foreach ( $mismatched_orders as $order_row ) {
                $data = array();

                $status       = $order_row->status;
                $order_status = 'wc-' !== substr( $status, 0, 3 ) ? 'wc-' . $status : $status;

                if ( $order_row->status !== $order_row->post_status || 'wc-' !== substr( $order_row->post_status, 0, 3 ) ) {
                    $data['status'] = $order_status;
                }

                if ( $order_row->customer_id !== $order_row->post_author ) {
                    $data['customer_id'] = $order_row->post_author;
                }

                $data = array_filter( $data );

                if ( empty( $data ) ) {
                    continue;
                }

                $wpdb->update(
                    $wpdb->prefix . 'wc_orders',
                    $data,
                    array(
                        'id' => $order_row->id,
                    )
                );

                unset( $data );
                unset( $order_status );
            }

            $this->log( 'WC Vendors: Done syncing mismatched orders on CPT' );
        }

        // Done processing.
        if ( count( $mismatched_orders ) < $this->batch_size ) {
            $this->log( 'WC Vendors: Done processing last batch.' );
            update_option( 'wcv_verify_customer_ids_complete', 'yes' );
        }

        $this->maybe_reschedule_verify_customer_ids();
    }

    /**
     * Get orders with mismatched customer IDs.
     *
     * @return object[]
     * @version 2.4.8
     * @since   2.4.8
     */
    public function get_mismatched_order_customer_ids() {
        global $wpdb;

        $orders = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT cot.id, cpt.ID, cot.status, cot.customer_id, cpt.post_author, cpt.post_status, cpt.post_type, cot.type FROM {$wpdb->prefix}wc_orders cot
                INNER JOIN {$wpdb->posts} cpt
                ON cpt.ID=cot.id
                WHERE cot.id = cpt.ID
                AND cpt.post_author != cot.customer_id
                AND (( cpt.post_type = 'shop_order_vendor' AND cot.type = 'shop_order_vendor' ) OR ( cpt.post_type = 'shop_order' AND cot.type = 'shop_order' ))
                LIMIT %d",
                $this->batch_size
            )
        );

        return $orders;
    }

    /**
     * Run data sync and migration when the WooCommerce data sync is enabled.
     *
     * @param string $feature_id The name of the feature being enabled|disabled.
     * @param string $enabled Whether the feature is enabled or not.
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function handle_hpos_feature_changed( $feature_id, $enabled ) {
        if ( 'custom_order_tables' !== $feature_id || ! $enabled ) {
            return;
        }

        $this->log( 'WC_Vendors: Initialize data sync because data storage has changed.' );
        $this->process_background_sync();
    }

    /**
     * Process background data sync.
     *
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function process_background_sync() {
        $this->log( 'WC_Vendors: Force run order data sync after enabling WooCommerce data sync. ' );

        // Delete the options to force run the data sync.
        delete_option( 'wcv_tracking_details_migration_complete' );
        delete_option( 'wcv_hpos_data_sync_complete' );
        delete_option( 'wcv_vendor_id_migration_complete' );

        // Start the sync process.
        $this->sync_orders();
        $this->migrate_tracking_details();
        $this->migrate_vendor_id();
    }

    /**
     * Check if custom order tables are enabled.
     *
     * @return boolean
     * @version 2.4.8
     * @since   2.4.8
     */
    public function cot_is_authoritative() {
        return wc_string_to_bool( get_option( CustomOrdersTableController::CUSTOM_ORDERS_TABLE_USAGE_ENABLED_OPTION ) );
    }

    /**
     * Check if data sync is enabled
     *
     * @return boolean
     * @version 2.4.8
     * @since   2.4.8
     */
    public function data_sync_is_enabled(): bool {
        return 'yes' === get_option( DataSynchronizer::ORDERS_DATA_SYNC_ENABLED_OPTION );
    }

    /**
     * Handle updated order.
     *
     * @param int $order_id The ID of the created/updated order.
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function handle_updated_order( $order_id ) {
        global $wpdb;

        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            $this->log( 'WC_Vendors: The order does not exist. ' );
            return;
        }

        if ( 'shop_order' !== $order->get_type() ) {
            return;
        }

        $sub_orders = array_filter( $this->get_sub_orders( $order ) );

        if ( empty( $sub_orders ) ) {
            $this->log( 'WC_Vendors: No sub orders found for order #' . $order_id );
            return;
        }

        $status       = $order->get_status();
        $order_status = 'wc-' !== substr( $status, 0, 3 ) ? 'wc-' . $status : $status;

        $order_details = array(
			'id'             => $order->get_id(),
			'title'          => sprintf(
                // translators: %s is the order date.
                __( 'Order &ndash; %s', 'wc-vendors' ),
                ( $order->get_date_created() )->format(
                    _x( 'M d, Y @ h:i A', 'Order date parsed by DateTime::format', 'wc-vendors' )
                )
            ),
			'status'         => $order_status,
			'customer_id'    => $order->get_customer_id(),
			'transaction_id' => $order->get_transaction_id(),
			'payment_method' => $order->get_payment_method(),
			'method_title'   => $order->get_payment_method_title(),
		);

        // Remove the actions to prevent infinite loop.
        remove_action( 'woocommerce_new_order', array( $this, 'handle_updated_order' ), 999 );
        remove_action( 'woocommerce_update_order', array( $this, 'handle_updated_order' ), 0 );

        $status       = $order->get_status();
        $order_status = 'wc-' !== substr( $status, 0, 3 ) ? 'wc-' . $status : $status;

        if ( wcv_cot_enabled() && $this->data_sync_is_enabled() ) {

            $sub_orders_ids = array();

            foreach ( $sub_orders as $vendor_order ) {
                $vendor_order_id = $vendor_order->get_id();

                $sub_orders_ids[] = $vendor_order_id;

                $post_data = array();

                if ( $vendor_order->get_status() !== $order_status ) {
                    $post_data['post_status'] = $order_status;
                }

                $post_data['post_title']  = $order_details['title'];
                $post_data['post_name']   = sanitize_title( $order_details['title'] );
                $post_data['post_author'] = $order_details['customer_id'];

                $wpdb->update(
                    $wpdb->posts,
                    $post_data,
                    array(
						'ID' => $vendor_order_id,
                    )
                );

                $vendor_order_transaction = $vendor_order->get_transaction_id();
                $vendor_order_transaction = '' !== $vendor_order_transaction ? $vendor_order_transaction : $order_details['transaction_id'];

                update_post_meta( $vendor_order_id, '_customer_user', $order_details['customer_id'] );
                update_post_meta( $vendor_order_id, '_payment_method', $order_details['payment_method'] );
                update_post_meta( $vendor_order_id, '_transaction_id', $vendor_order_transaction );
                update_post_meta( $vendor_order_id, '_payment_method_title', $order_details['method_title'] );
                update_post_meta( $vendor_order_id, '_order_tax', $order->get_total_tax() );
                update_post_meta( $vendor_order_id, '_order_shipping_tax', $order->get_shipping_tax() );
                update_post_meta( $vendor_order_id, '_order_total', $order->get_total() );

                $vendor_order->set_billing_email( $order->get_billing_email() );
                $vendor_order->save();
            }

            update_post_meta( $order_id, 'wcv_sub_orders', $sub_orders_ids );
        } elseif ( ! wcv_cot_enabled() && $this->data_sync_is_enabled() ) {

            $order_table_name = OrdersTableDataStore::get_orders_table_name();
            $order_meta_table = OrdersTableDataStore::get_meta_table_name();
            $sub_order_ids    = array();

            foreach ( $sub_orders as $sub_order ) {
                $sub_order_ids[]             = $sub_order->get_id();
                $order_data                  = array(
                    'status'         => $order_status,
                    'customer_id'    => $order->get_customer_id(),
                    'transaction_id' => $order->get_transaction_id(),
                );
                $sub_order_transaction_id    = $sub_order->get_transaction_id();
                $parent_order_transaction_id = $order->get_transaction_id();

                if ( ! empty( $sub_order_transaction_id ) ) {
                    $order_data['transaction_id'] = $sub_order_transaction_id;
                } elseif ( strpos( $parent_order_transaction_id, ',' ) === false && ! empty( $parent_order_transaction_id ) ) {
                    $order_data['transaction_id'] = $parent_order_transaction_id;
                }

                $wpdb->update(
                    $order_table_name,
                    $order_data,
                    array(
                        'id' => $sub_order->get_id(),
                    )
                );

                $sub_order->set_status( $order_status );
                $sub_order->save();
                unset( $parent_order_transaction_id );
                unset( $order_data );
            }

            $updated = $wpdb->update(
                $order_meta_table,
                array(
                    'meta_key'   => 'wcv_sub_orders',
                    'meta_value' => maybe_serialize( $sub_order_ids ),
                ),
                array(
                    'order_id' => $order_id,
                    'meta_key' => 'wcv_sub_orders',
                )
            );

            if ( ! $updated ) {
                $wpdb->insert(
                    $order_meta_table,
                    array(
                        'order_id'   => $order_id,
						'meta_key'   => 'wcv_sub_orders',
						'meta_value' => maybe_serialize( $sub_order_ids ),
                    ),
                );
            }

            unset( $sub_order_ids );
            unset( $updated );
        }

        // Add the actions again.
        add_action( 'woocommerce_new_order', array( $this, 'handle_updated_order' ), 999 );
        add_action( 'woocommerce_update_order', array( $this, 'handle_updated_order' ), 0 );
    }

    /**
     * Sync order meta data.
     *
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function sync_orders() {
        $orders = $this->get_pending_orders();

        if ( count( $orders ) === 0 ) {
            $this->done();
            return;
        }

        $this->log( 'WC vendors: Found ' . count( $orders ) . ' orders to process.' );

        $this->process_orders( $orders );

        if ( count( $orders ) < $this->batch_size ) {
            unset( $orders );
            $this->done();
        }

        $this->maybe_schedule_hpos_data_sync();
    }

    /**
     * Update orders on custom order tables.
     *
     * @param WC_Order[] $orders The list of orders to process.
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function process_orders( $orders ) {
        $this->log( 'WC Vendors: Updating orders on custom order tables.' );

        foreach ( $orders as $order ) {
            $order = wc_get_order( $order );

            if ( ! $order ) {
                continue;
            }

            if ( 'shop_order' !== $order->get_type() ) {
                continue;
            }

            $this->log( 'WC Vendors: Adding meta data to order #' . $order->get_id() );

            $vendor_ids = $this->get_order_vendor_ids( $order );
            $sub_orders = $this->get_sub_orders( $order );

            $this->process_sub_orders( $order, $sub_orders );

            $sub_order_ids = array();
            foreach ( $sub_orders as $sub_order ) {
                $sub_order_ids[] = $sub_order->get_id();
            }

            $sub_order_ids = array_filter( $sub_order_ids );

            if ( ! empty( $vendor_ids ) ) {
                $order->add_meta_data( 'wcv_vendor_ids', $vendor_ids, true );
            }

            if ( ! empty( $child_orders ) ) {
                $order->add_meta_data( 'wcv_sub_orders', $child_orders, true );
            }

            if ( ! empty( $vendor_ids ) || ! empty( $child_orders ) ) {
                $order->save();
            }

            unset( $vendor_ids );
            unset( $sub_orders );
            unset( $sub_order_ids );
        }
    }

    /**
     * Process sub orders
     *
     * @param WC_Order          $order The order to process.
     * @param WC_Order_Vendor[] $sub_orders The list of sub orders.
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function process_sub_orders( $order, $sub_orders ) {
        $vendor_orders = $this->get_vendors_from_order( $order );

        $this->log( 'WC Vendors: Processing sub orders for order #' . $order->get_id() );

        $vendors_product_ids = array();
        foreach ( $vendor_orders as $vendor_id => $details ) {
            $order_item_product_ids = WCV_Vendors::get_order_item_product_ids( $details['line_items'] );

            $vendors_product_ids[ $vendor_id ] = $order_item_product_ids;
            unset( $order_item_product_ids );
        }

        unset( $vendor_orders );

        foreach ( $sub_orders as $sub_order ) {
            $this->log( 'Processing sub order: ' . $sub_order->get_id() );

            $sub_order_items = $sub_order->get_items();

            foreach ( $sub_order_items as $sub_order_item ) {
                $this->log( 'Found ' . count( $sub_order_items ) . ' items for sub order.' );
                foreach ( $vendors_product_ids as $vendor_id => $item_ids ) {
                    $wcv_product_ids = $vendors_product_ids[ $vendor_id ];

                    $this->log( 'Checking vendor item ids: ' . implode( ',', $item_ids ) );

                    if ( ! in_array( $sub_order_item->get_product_id(), $item_ids, true ) ) {
                        continue;
                    }

                    $this->log( 'WC Vendors: Adding order item ids to sub order #' . $sub_order->get_id() );

                    $sub_order->add_meta_data( 'wcv_vendor_id', $vendor_id, true );
                    $sub_order->add_meta_data( 'wcv_product_ids', $wcv_product_ids, true );

                    unset( $wcv_product_ids );
                }
            }

            $sub_order->save();

            unset( $sub_order_items );
        }

        unset( $sub_orders );
        unset( $vendors_product_ids );

        $this->log( 'WC Vendors: Done processing sub orders.' );
    }

    /**
     * Migrate tracking details from sub orders to parent orders.
     *
     * @return void
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public function migrate_tracking_details() {
        $this->log( 'WC Vendors: Migrating tracking details from sub orders to parent orders.' );
        $args = array(
            'type'       => array( 'shop_order' ),
            'limit'      => $this->batch_size,
            'status'     => $this->order_statuses,
            'meta_query' => array(
                array(
                    'key'     => '_wcv_tracking_details',
                    'compare' => 'NOT EXISTS',
                ),
            ),
        );

        $orders = wc_get_orders( $args );

        if ( ! $orders || count( $orders ) === 0 ) {
            $this->log( 'WC Vendors: No orders to process.' );
            update_option( 'wcv_tracking_details_migration_complete', 'yes' );
            return;
        }

        $this->log( 'WC Vendors: Found ' . count( $orders ) . ' orders to process.' );

        foreach ( $orders as $order ) {
            $sub_orders = $this->get_sub_orders( $order );

            $tracking_details = array();

            if ( count( $sub_orders ) <= 0 ) {
                continue;
            }

            $this->log( 'WC Vendors: Processing ' . count( $sub_orders ) . ' sub orders for order #' . $order->get_id() );

            foreach ( $sub_orders as $sub_order ) {
                // Copy sub order tracking to parent order.
                $vendor_order_tracking = array(
                    '_tracking_provider'        => $sub_order->get_meta( '_tracking_provider', true ),
                    '_custom_tracking_provider' => $sub_order->get_meta( '_custom_tracking_provider', true ),
                    '_tracking_number'          => $sub_order->get_meta( '_tracking_number', true ),
                    '_custom_tracking_link'     => $sub_order->get_meta( '_custom_tracking_link', true ),
                    '_date_shipped'             => $sub_order->get_meta( '_date_shipped', true ),
                );

                $vendor_order_tracking = array_filter( $vendor_order_tracking );

                if ( ! empty( $vendor_order_tracking ) ) {
                    $tracking_details[ $sub_order->get_vendor_id() ] = $vendor_order_tracking;
                }

                $this->log( 'WC Vendors: Done migrating tracking data for sub order# ' . $sub_order->get_id() );
            }

            // Save parent order tracking with the vendor order tracking details.
            $this->log( 'Add order tracking to parent order#' . $order->get_id() );
            $order->add_meta_data( '_wcv_tracking_details', $tracking_details, true );
            $order->save_meta_data();

            unset( $tracking_details );
            unset( $sub_orders );
        }

        if ( count( $orders ) < $this->batch_size ) {
            $this->log( 'WC Vendors: No orders to process.' );
            update_option( 'wcv_tracking_details_migration_complete', 'yes' );
            unset( $orders );
        }

        $this->maybe_schedule_tracking_details_migration();
    }

    /**
     * Migrate vendor id to new meta key.
     *
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function migrate_vendor_id() {
        $orders = wc_get_orders(
            array(
                'limit'      => $this->batch_size,
                'type'       => array( 'shop_order_vendor' ),
                'meta_query' => array(
                    array(
                        'key'     => '_vendor_id',
                        'compare' => 'EXISTS',
                    ),
                ),
            ),
        );

        if ( ! $orders || count( $orders ) === 0 ) {
            $this->log( 'WC Vendors: No orders to process.' );
            update_option( 'wcv_vendor_id_migration_complete', 'yes' );
            return;
        }

        foreach ( $orders as $order ) {
            $order_meta_data = $order->get_meta_data();
            $this->log( 'WC Vendors: Processing order #' . $order->get_id() . ' with ' . count( $order_meta_data ) . ' meta data.' );

            foreach ( $order_meta_data as $meta ) {
                $meta_data = $meta->get_data();

                if ( '_vendor_id' !== $meta_data['key'] ) {
                    continue;
                }

                // Copy _vendor_id to wcv_vendor_id meta key.
                $order->add_meta_data( 'wcv_vendor_id', $meta_data['value'], true );
                $order->delete_meta_data( '_vendor_id' );
                unset( $meta_data );
            }

            $this->log( 'Done copying _vendor_id to wcv_vendor_id for order #' . $order->get_id() );
            $order->save();

            unset( $order_meta_data );
        }

        if ( count( $orders ) < $this->batch_size ) {
            $this->log( 'WC Vendors: No orders to process.' );
            update_option( 'wcv_vendor_id_migration_complete', 'yes' );
        }

        $this->maybe_reschedule_vendor_id_migration();
    }

    /**
     * Save info when we are done processing.
     *
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function done() {
        $this->log( 'WC Vendors: Done processing all orders.' );

        update_option( 'wcv_hpos_data_sync_complete', 'yes' );
    }

    /**
     * Maybe schedule the next batch.
     *
     * @param string $option_name The name of the option to check.
     * @param string $hook_name   The name of the hook to schedule.
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function maybe_schedule_next_batch( $option_name, $hook_name ) {
        $complete = get_option( $option_name, 'no' );

        if ( wc_string_to_bool( $complete ) ) {
            return;
        }

        $this->log( 'WC Vendors: Scheduling next batch.' );

        WC()->queue()->schedule_single(
            current_time( 'mysql' ),
            $hook_name
        );
    }

    /**
     * Maybe schedule the tracking details migration.
     *
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function maybe_schedule_tracking_details_migration() {
        $this->maybe_schedule_next_batch(
            'wcv_tracking_details_migration_complete',
            'wcvendors_scheduled_migrate_tracking_details'
        );
    }

    /**
     * Maybe reschedule the HPOS data sync.
     *
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function maybe_schedule_hpos_data_sync() {
        $this->maybe_schedule_next_batch(
            'wcv_hpos_data_sync_complete',
            'wcvendors_scheduled_sync_order_meta_data'
        );
    }

    /**
     * Maybe reschedule vendor id migration.
     *
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function maybe_reschedule_vendor_id_migration() {
        $this->maybe_schedule_next_batch(
            'wcv_vendor_id_migration_complete',
            'wcvendors_scheduled_migrate_vendor_id'
        );
    }

    /**
     * Maybe reschedule customer id verification
     *
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function maybe_reschedule_verify_customer_ids() {
        $this->maybe_schedule_next_batch(
            'wcv_verify_customer_ids_complete',
            'wcvendors_scheduled_verify_customer_ids'
        );
    }

    /**
     * Getters
     * ==================================================
     */

    /**
     * Get the wc logger.
     *
     * @return object
     * @version 2.4.8
     * @since   2.4.8
     */
    private function get_logger() {
        $this->logger = wc_get_logger();
        return $this->logger;
    }

    /**
     * Get orders pending sync.
     *
     * @return WC_Order[]
     * @version 2.4.8
     * @since   2.4.8
     */
    public function get_pending_orders() {
        $args = array(
            'type'       => array( 'shop_order' ),
            'status'     => $this->order_statuses,
            'limit'      => $this->batch_size,
            'return'     => 'ids',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key'     => 'wcv_sub_orders',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key'     => 'wcv_vendor_ids',
                    'compare' => 'NOT EXISTS',
                ),
            ),
        );

        $orders = wc_get_orders( $args );

        return $orders;
    }
    /**
     * Get vendors from order.
     *
     * @param WC_Order $order The order.
     * @return array
     * @version 2.4.8
     * @since   2.4.8
     */
    public function get_vendors_from_order( $order ) {
        return WCV_Vendors::get_vendors_from_order( $order );
    }

    /**
     * Get vendor IDs for an order.
     *
     * @param WC_Order $order The order object.
     * @return array[int]
     * @version 2.4.8
     * @since   2.4.8
     */
    public function get_order_vendor_ids( $order ) {
        $vendor_orders = $this->get_vendors_from_order( $order );

        return array_keys( $vendor_orders );
    }

    /**
     * Get sub orders for an order.
     *
     * @param WC_Order $order The parent order object.
     * @return WC_Order_Vendor[]
     * @version 2.4.8
     * @since   2.4.8
     */
    public function get_sub_orders( $order ) {
        $orders = wcv_get_vendor_orders(
            array(
                'parent' => $order->get_id(),
            )
        );

        return $orders ? $orders : array();
    }

    /**
     * Utils
     * ============================================
     */

    /**
     * Log a message in the debug log
     *
     * @param string $message The message to be logged.
     * @param string $level   The error level. emergency, alert, critical, critical, error, warning, notice, debug, info.
     * @param array  $context The additional info for loggers.
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    private function log( $message, $level = 'debug', $context = array() ) {
        $this->logger->log( $level, $message, $context );
    }

    /**
     * Enqueue admin scripts
     *
     * @return void
     * @version 2.4.9.2
     * @since   2.4.9.2 - Added
     */
    public function admin_enqueue_scripts() {
        wp_enqueue_style(
            'wcvendors-progress-bar',
            WCV_ASSETS_URL . 'css/wcv-admin-progress-bar.css',
            array(),
            WCV_VERSION
        );
    }

    /**
     * Admin notices
     * =========================================================
     */


    /**
     * Show a notice if the HPOS data sync is not complete.
     *
     * @return void
     * @version 2.4.9.2
     * @since   2.4.9.2
     */
    public function update_in_progress_notice() {
        $is_dismissed = get_option( 'wcvendors_display_notice_hpos_sync_in_progress', 'no' );

        if ( wc_string_to_bool( $is_dismissed ) ) {
            return;
        }

        if ( isset( $_GET['show_update_progress_nonce'] ) ) { // phpcs:ignore
            return;
        }

        $wcv_hpos_sync_complete = wc_string_to_bool( get_option( 'wcv_hpos_data_sync_complete', 'no' ) );

        if ( $wcv_hpos_sync_complete ) {
            return;
        }

        $show_progress_url = add_query_arg(
            array(
                'show_update_progress_nonce' => wp_create_nonce( 'wcv_show_update_progress' ),
            ),
            admin_url( 'admin.php?page=wcv-settings' )
        );

        ?>
        <div id="message" class="notice is-dismissible">
            <p><strong><?php esc_html_e( 'WC Vendors Marketplace is updating product data in the background' ); ?></strong></p>
            <p><?php esc_html_e( 'This process will take some time. Order display and commission calculations may not be accurate until this finishes.', 'wc-vendors' ); ?></p>
            <p>
                <a href="<?php echo esc_url_raw( $show_progress_url ); ?>" class="button button-primary">
                    <?php esc_html_e( 'Click here to view progress', 'wc-vendors' ); ?>
                </a>
            </p>
        </div>
        <?php
    }

    /**
     * Display admin notice showing progress bar.
     *
     * @return void
     * @version 2.4.9.2
     * @since   2.4.9.2
     */
    public function data_sync_progress_notice() {

        if ( ! isset( $_GET['show_update_progress_nonce'] ) || ! wp_verify_nonce( $_GET['show_update_progress_nonce'], 'wcv_show_update_progress' ) ) {
            return;
        }

        $cot_enabled    = get_option( 'woocommerce_custom_orders_table_enabled', 'no' );
        $is_cot_enabled = wc_string_to_bool( $cot_enabled );

        global $wpdb;

        $count_pending_orders = 0;
        $count_all_orders     = 0;

        if ( ! $is_cot_enabled ) {
            $all_orders = $wpdb->get_col( "SELECT ID FROM `{$wpdb->prefix}posts` WHERE post_type = 'shop_order' LIMIT 18446744073709551615" );

            $count_all_orders = count( $all_orders );

            $count_pending_orders = (int) $wpdb->get_var(
                "SELECT COUNT( p.ID ) as count_orders FROM {$wpdb->prefix}posts p
                LEFT JOIN {$wpdb->prefix}postmeta m1 ON p.ID = m1.post_id and m1.meta_key = 'wcv_sub_orders'
                LEFT JOIN {$wpdb->prefix}postmeta m2 ON p.ID = m2.post_id and m2.meta_key = 'wcv_vendor_ids'
                WHERE p.post_type = 'shop_order'
                and ( m1.meta_key IS null or m2.meta_key IS null )
                LIMIT 18446744073709551615"
            );
        } else {
            $all_orders = $wpdb->get_col( "SELECT id FROM `{$wpdb->prefix}wc_orders` WHERE type = 'shop_order' LIMIT 18446744073709551615" );

            $count_all_orders = count( $all_orders );

            $count_pending_orders = (int) $wpdb->get_var(
                "SELECT COUNT( cot.id ) as count_orders FROM {$wpdb->prefix}wc_orders cot
                LEFT JOIN {$wpdb->prefix}wc_orders_meta m1 ON cot.id = m1.order_id and m1.meta_key = 'wcv_sub_orders'
                LEFT JOIN {$wpdb->prefix}wc_orders_meta m2 ON cot.id = m2.order_id and m2.meta_key = 'wcv_vendor_ids'
                WHERE cot.type = 'shop_order'
                and ( m1.meta_key IS null or m2.meta_key IS null )
                LIMIT 18446744073709551615"
            );
        }

        $count_processed_orders = $count_all_orders - $count_pending_orders;

        if ( $count_pending_orders === $count_all_orders ) {
            $complete_progress = 100;
        } else {
            $complete_progress = round( ( $count_processed_orders / $count_all_orders ) * 100, 2 );
        }

        if ( $complete_progress < 100 ) {

            $this->progress_bar(
                $complete_progress,
                sprintf(
                    '<h3>%s</h3>',
                    __( 'WC Vendors database update in progress', 'wc-vendors' )
                ),
                sprintf(
                    '<p>%s</p>',
                    sprintf(
                    // translators: %1$d is the number of processed orders, %2$d is the number of remaining orders.
                        __( 'Processed %1$d orders, %2$d still pending.', 'wc-vendors' ),
                        $count_processed_orders,
                        $count_pending_orders
                    )
                )
            );
        } else {
            $this->progress_bar( $complete_progress, __( 'Finished processing all orders', 'wc-vendors' ) );
            update_option( 'wcv_hpos_data_sync_complete', 'yes' );
            update_option( 'wcvendors_display_notice_hpos_sync_in_progress', 'yes' );
        }
    }

    /**
     * Display a progress bar inside an admin notice.
     *
     * @param int    $progress The progress value.
     * @param string $heading HTML content to be place as heading above progress bar.
     * @param string $extra_html HTML content to be displayed below progress bar.
     * @return void
     * @version 2.4.9.2
     * @since   2.4.9.2 - Added
     */
    public function progress_bar( $progress, $heading = '', $extra_html = '' ) {
        ?>
        <div id="message" class="notice wcvendors-message wc-connect-success">
            <?php echo wp_kses_post( $heading ); ?>
            <div class="wcv-progress-wrapper">
                <div class="wcv-progress-bar">
                    <span class="wcv-progress-bar-fill" style="width: <?php echo esc_attr( $progress ); ?>%"></span>
                </div>
            </div>
            <?php echo wp_kses_post( $extra_html ); ?>
        </div>

        <?php
    }
}
