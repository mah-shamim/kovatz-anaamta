<?php

/**
 * Admin orders class
 *
 * All WooCommerce Order related functions for WC Vendors.
 *
 * @since 2.4.0
 * @package WCVendors\Admin
 */

/**
 * WC Vendors Admin Orders Class
 *
 * @version 2.4.0
 * @since   2.4.0
 */
class WCVendors_Admin_Orders {

    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize all actions and filters here.
     */
    public function init_hooks() {
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
        add_action( 'woocommerce_admin_order_data_after_shipping_address', array( $this, 'add_vendor_shipped_details' ), 10, 2 );
        add_action( 'woocommerce_admin_order_actions', array( $this, 'append_mark_shipped' ), 10, 2 );
        add_action( 'wp_ajax_wcvendors_mark_order_shipped', array( __CLASS__, 'mark_order_shipped' ) );
        add_action( 'wp_ajax_wcvendors_mark_order_vendor_shipped', array( __CLASS__, 'mark_order_vendor_shipped' ) );
        add_action( 'wp_ajax_wcvendors_mark_order_vendor_unshipped', array( __CLASS__, 'mark_order_vendor_unshipped' ) );

        // Bulk order actions for CPT.
        add_filter( 'bulk_actions-edit-shop_order', array( $this, 'add_bulk_order_shipped_action' ) );
        add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_bulk_actions' ), 10, 3 );

        // Bulk order actions for HPOS.
        add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'add_bulk_order_shipped_action' ) );
        add_action( 'handle_bulk_actions-woocommerce_page_wc-orders', array( $this, 'handle_bulk_actions' ), 10, 3 );

        add_action( 'woocommerce_order_actions', array( $this, 'add_order_shipped_action' ) );
        add_action( 'woocommerce_order_actions', array( $this, 'add_order_unshipped_action' ) );
        add_action( 'woocommerce_order_action_wcvendors_order_shipped', array( $this, 'handle_order_shipped' ), 10, 1 );
        add_action( 'woocommerce_order_action_wcvendors_order_unshipped', array( $this, 'handle_order_unshipped' ), 10, 1 );

        if ( wcv_hpos_enabled() ) {
            add_action( 'woocommerce_order_list_table_restrict_manage_orders', array( $this, 'add_vendor_orders_filter' ), 50 );
            add_filter( 'woocommerce_orders_table_query_clauses', array( $this, 'filter_orders_by_vendor_query' ), 10, 2 );
        } else {
            add_action( 'restrict_manage_posts', array( $this, 'add_vendor_orders_filter' ), 50 );
            add_action( 'pre_get_posts', array( $this, 'filter_orders_by_vendor_none_cot' ) );
        }
    }

    /**
     * Add the vendor shipped information to the order edit screen.
     *
     * @param WC_Order $order the order we are viewing.
     */
    public function add_vendor_shipped_details( $order ) {
        echo wp_kses_post( wcv_get_order_vendors_shipped_text( $order, true ) );
    }


    /**
     * Append the mark shipped action to the actions column on the orders screen
     *
     * @param array    $actions The order actions column.
     * @param WC_Order $order the order row we are currently on.
     */
    public function append_mark_shipped( $actions, $order ) {

        if ( $order->has_status( wcv_marked_shipped_order_status() ) && ! wcv_all_vendors_shipped( $order ) ) {
            $actions['wcvendors_mark_shipped'] = array(
                'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=wcvendors_mark_order_shipped&order_id=' . $order->get_id() ), 'wcvendors-mark-order-shipped' ),
                'name'   => __( 'Mark Shipped', 'wc-vendors' ) . wcv_get_order_vendors_shipped_text( $order ),
                'action' => 'wcvendors_mark_shipped',
            );
        }

        return $actions;
    }

    /**
     * Add action to bulk actions on order list
     *
     * @param   array $actions bulk actions.
     *
     * @return  array $actions Modified bulk actions
     * @since   2.4.0
     */
    public function add_bulk_order_shipped_action( $actions ) {
        $actions['wcvendors_bulk_order_shipped'] = __( 'Mark shipped', 'wc-vendors' );
        return $actions;
    }

    /**
     * Add action to calculate commissions on single order edit screen
     *
     * @param array $actions The order actions.
     *
     * @since 2.4.0
     */
    public function add_order_shipped_action( $actions ) {
        $actions['wcvendors_order_shipped'] = __( 'Mark shipped', 'wc-vendors' );
        return $actions;
    }

    /**
     * Add action to makr order unshipped on single order edit screen
     *
     * @param array $actions The order actions.
     *
     * @since 2.4.9
     * @return array $actions The order actions.
     */
    public function add_order_unshipped_action( $actions ) {
        $actions['wcvendors_order_unshipped'] = __( 'Mark unshipped', 'wc-vendors' );
        return $actions;
    }


    /**
     * Bulk action handler
     *
     * @param string $redirect_to The redirect URL.
     * @param string $action The action being taken.
     * @param array  $ids The order ids.
     */
    public function handle_bulk_actions( $redirect_to, $action, $ids ) {

        // Bail out if this is not a bulk mark shipped action.
        if ( 'wcvendors_bulk_order_shipped' !== $action ) {
            return $redirect_to;
        }

        $send_back_args = wcv_cot_enabled()
            ? array(
                'wcvendors_order_action' => 'orders_marked_shipped',
                'ids'                    => join( ',', $ids ),
                'error_count'            => 0,
            )
            : array(
                'post_type'              => 'shop_order',
                'wcvendors_order_action' => 'orders_marked_shipped',
                'ids'                    => join( ',', $ids ),
            );

        $changed = 0;

        $ids = apply_filters( 'wcvendors_bulk_order_action_ids', array_reverse( array_map( 'absint', $ids ) ), $action, 'order' );

        foreach ( $ids as $order_id ) {
            $order = wc_get_order( $order_id );

            if ( ! in_array( $order->get_status(), wcv_marked_shipped_order_status(), true ) ) {
                continue;
            }

            $vendor_ids = WCV_Vendors::get_vendor_ids_from_order( $order );

            foreach ( $vendor_ids as $vendor_id ) {
                wcv_mark_vendor_shipped( $order, $vendor_id );

                do_action( 'wcvendors_mark_order_shipped', $order, $vendor_id );
            }

            ++$changed;

            do_action( 'wcvendors_bulk_order_marked_shipped', $order_id );
        }

        $send_back_args['changed'] = $changed;

        $redirect_to = add_query_arg( $send_back_args, $redirect_to );

        if ( wcv_cot_enabled() ) {
            wp_safe_redirect( esc_url_raw( $redirect_to ) );
            exit();
        }

        return esc_url_raw( $redirect_to );
    }

    /**
     * Mark the order shipped from the order edit screen
     *
     * @param WC_Order $order the order we are viewing.
     *
     * @since 2.4.0
     * @return void
     */
    public function handle_order_shipped( $order ) {
        if ( ! is_a( $order, 'WC_Order' ) ) {
            $order = wc_get_order( $order );
        }
        wcv_mark_order_shipped( $order );
    }

    /**
     * Handle order unshipped
     *
     * @param WC_Order $order the order we are viewing.
     *
     * @since 2.4.9
     * @return void
     */
    public function handle_order_unshipped( $order ) {
        if ( ! is_a( $order, 'WC_Order' ) ) {
            $order = wc_get_order( $order );
        }

        if ( ! $order->has_status( wcv_marked_unshipped_order_status() ) ) {
            return;
        }

        wcv_mark_order_unshipped( $order );
    }

    /**
     * Mark an order shipped for all vendors.
     *
     * @since 2.4.0
     */
    public static function mark_order_shipped() {
        if ( current_user_can( 'edit_shop_orders' ) && check_admin_referer( 'wcvendors-mark-order-shipped' ) && $_GET['order_id'] ) {
            $order_id = absint( wp_unslash( $_GET['order_id'] ) );
            $order    = wc_get_order( $order_id );

            wcv_mark_order_shipped( $order );

            $redirect_to = self::get_order_edit_redirect_url( $order_id );

            wp_safe_redirect( $redirect_to );
            exit;
        }
    }

    /**
     * Get the order edit redirect url.
     *
     * @param int $order_id The order id.
     * @return string $redirect_url The url the user will be redirected to after editing an order.
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public static function get_order_edit_redirect_url( $order_id ) {
        $action           = isset( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $send_back_action = $action;

        switch ( $action ) {
            case 'wcvendors_mark_order_vendor_unshipped':
                $send_back_action = 'order_marked_unshipped';
                break;
            case 'wcvendors_mark_order_vendor_shipped':
                $send_back_action = 'order_marked_shipped';
                break;
        }

        $send_back_args = wcv_cot_enabled()
            ? array(
                'wcvendors_order_action' => $send_back_action,
                'order_id'               => $order_id,
                'id'                     => $order_id,
                'error_count'            => 0,
                'action'                 => 'edit',
            )
            : array(
                'post'                   => $order_id,
                'order_id'               => $order_id,
                'wcvendors_order_action' => $send_back_action,
                'action'                 => 'edit',

            );

        $admin_url = wcv_cot_enabled() ? 'admin.php?page=wc-orders' : 'post.php';

        $redirect_to = add_query_arg( $send_back_args, admin_url( $admin_url ) );

        /**
         * Filter the redirect url after marking an order shipped.
         *
         * @param string $redirect_to The redirect url.
         * @param int    $order_id    The id of the order currently being edited.
         */
        return apply_filters(
            'wcvendors_order_edit_redirect_url',
            $redirect_to,
            $order_id
        );
    }

    /**
     * Mark an order shipped for a particular vendor.
     *
     * @since 2.4.0
     */
    public static function mark_order_vendor_shipped() {
        $admin_url = wcv_cot_enabled() ? 'admin.php?page=wc-orders' : 'edit.php?post_type=shop_order';
        $admin_url = admin_url( $admin_url );

        if ( ! isset( $_GET['order_id'] ) || ! isset( $_GET['vendor_id'] ) ) {
            wp_safe_redirect( $admin_url );
            exit;
        }

        if ( ! current_user_can( 'edit_shop_orders' ) || ! check_admin_referer( 'wcvendors-mark-order-vendor-shipped' ) ) { // phpcs:ignore
            wp_safe_redirect( $admin_url );
            exit;
        }

        $order_id  = absint( wp_unslash( $_GET['order_id'] ) );
        $vendor_id = absint( wp_unslash( $_GET['vendor_id'] ) );

        $order = wc_get_order( $order_id );

        wcv_mark_vendor_shipped( $order, $vendor_id );

        $redirect_to = self::get_order_edit_redirect_url( $order_id );

        wp_safe_redirect( $redirect_to );
        exit;
    }

    /**
     * Mark an order unshipped for a particular vendor.
     *
     * @since 2.4.0
     */
    public static function mark_order_vendor_unshipped() {
        $admin_url = wcv_cot_enabled() ? 'admin.php?page=wc-orders' : 'edit.php?post_type=shop_order';
        $admin_url = admin_url( $admin_url );

        if ( ! isset( $_GET['order_id'] ) || ! isset( $_GET['vendor_id'] ) ) {
            wp_safe_redirect( $admin_url );
            exit;
        }

        if ( ! current_user_can( 'edit_shop_orders' ) || ! check_admin_referer( 'wcvendors-mark-order-vendor-unshipped' ) ) { // phpcs:ignore
            wp_safe_redirect( $admin_url );
            exit;
        }

        $order_id  = absint( wp_unslash( $_GET['order_id'] ) );
        $vendor_id = absint( wp_unslash( $_GET['vendor_id'] ) );

        $order = wc_get_order( $order_id );

        wcv_mark_vendor_unshipped( $order, $vendor_id );

        $redirect_to = self::get_order_edit_redirect_url( $order_id );

        wp_safe_redirect( $redirect_to );
        exit;
    }

    /**
     * Notices
     */

    /**
     * Show confirmation message that order has been marked shipped.
     *
     * @since 2.4.0
     */
    public function admin_notices() {
        global $post_type, $pagenow;

        //phpcs:disable WordPress.Security.NonceVerification.Recommended

        $is_cpt_order_page = ( 'edit.php' === $pagenow || 'post.php' === $pagenow ) && 'shop_order' === $post_type;
        $is_cot_order_page = ( 'admin.php' === $pagenow && 'wc-orders' === $_REQUEST['page'] );

        // Bail if not on required page.
        if ( ( ! $is_cpt_order_page && ! $is_cot_order_page ) || ! isset( $_REQUEST['wcvendors_order_action'] ) ) {
            return;
        }

        $action    = wc_clean( wp_unslash( $_REQUEST['wcvendors_order_action'] ) );
        $order_id  = isset( $_REQUEST['order_id'] ) ? absint( wp_unslash( $_REQUEST['order_id'] ) ) : '';
        $post_id   = isset( $_REQUEST['post'] ) ? absint( wp_unslash( $_REQUEST['post'] ) ) : '';
        $ids       = isset( $_REQUEST['ids'] ) ? absint( wp_unslash( $_REQUEST['ids'] ) ) : '';
        $vendor_id = isset( $_REQUEST['vendor_id'] ) ? absint( wp_unslash( $_REQUEST['vendor_id'] ) ) : '';

        switch ( $action ) {
            case 'order_marked_shipped':
                if ( $order_id ) {
                    $message = sprintf(
                        // translators: %1$d is the order id, %2$s is the name used to refer to vendors.
                        __( 'Order #%1$d marked shipped for %2$s.', 'wc-vendors' ),
                        $order_id,
                        wcv_get_vendor_name( true, false )
                    );
                    echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
                }
                break;
            case 'order_marked_unshipped':
                if ( $order_id ) {
                    $message = sprintf(
                        // translators: %1$d is the order id, %2$s is the name used to refer to vendors.
                        __( 'Order #%1$d marked unshipped for %2$s.', 'wc-vendors' ),
                        $order_id,
                        wcv_get_vendor_name( true, false )
                    );
                    echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
                }
                break;
            case 'orders_marked_shipped':
                if ( $ids ) {
                    $message = sprintf(
                        // translators: name used to refer to vendors.
                        __( 'Orders marked shipped for all %s.', 'wc-vendors' ),
                        wcv_get_vendor_name( false, false )
                    );
                    echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
                }
                break;
            case 'order_marked_vendor_shipped':
                if ( $post_id ) {
                    $vendor_name = WCV_Vendors::get_vendor_shop_name( $vendor_id );
                    $message     = sprintf(
                        // translators: %s: vendor name.
                        __( 'Order marked shipped for %s.', 'wc-vendors' ),
                        $vendor_name
                    );
                    echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
                }
                break;
            case 'order_marked_vendor_unshipped':
                if ( $post_id ) {
                    $vendor_name = WCV_Vendors::get_vendor_shop_name( $vendor_id );
                    $message     = sprintf(
                        // translators: %s: vendor name.
                        __( 'Order marked unshipped for %s.', 'wc-vendors' ),
                        $vendor_name
                    );
                    echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
                }
                break;
            default:
                break;
        }

        // phpcs:enable WordPress.Security.NonceVerification.Recommended
    }

    /**
     * Add custom filter select box.
     *
     * @since 2.4.8
     * @version 2.4.8
     * @return void
     */
	public function add_vendor_orders_filter() {
		$post_tpye = get_current_screen()->post_type;
		if ( 'shop_order' === $post_tpye ) {
			$vendor_id       = isset( $_GET['vendor_id'] ) ? sanitize_text_field( wp_unslash( $_GET['vendor_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$select_box_args = array(
                'id'          => 'vendor_id',
                'placeholder' => sprintf(
                    /* translators: %s: vendor name. */
                    __( 'Filter by %s', 'wc-vendors' ),
                    wcv_get_vendor_name()
                ),
            );
            if ( ! empty( $vendor_id ) ) {
                $select_box_args['selected'] = $vendor_id;
            }

            echo WCV_Product_Meta::vendor_selectbox( $select_box_args, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            wp_nonce_field( 'wcv_vendor_orders', 'wcv_vendor_orders' );
		}
	}

    /**
     * Filter orders by vendor.
     *
     * @param array                                                              $pieces The query pieces.
     * @param Automattic\WooCommerce\Internal\DataStores\Orders\OrdersTableQuery $order_query_class The order query class.
     *
     * @since 2.4.8
     * @version 2.4.8
     *
     * @return array $pieces The query pieces.
     */
    public function filter_orders_by_vendor_query( $pieces, $order_query_class ) {

        $nonce = isset( $_GET['wcv_vendor_orders'] ) ? sanitize_text_field( $_GET['wcv_vendor_orders'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wcv_vendor_orders' ) ) {
            return $pieces;
        }

        if ( ! self::is_shop_order_screen() ) {
            return $pieces;
        }

        $vendor_id = isset( $_GET['vendor_id'] ) ? sanitize_text_field( $_GET['vendor_id'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        if ( empty( $vendor_id ) ) {
            return $pieces;
        }

        $order_ids = self::get_vendor_order_ids( $vendor_id );

        if ( empty( $order_ids ) ) {
            $pieces['where'] = '1=0';
            return $pieces;
        }

        if ( ! empty( $order_ids ) ) {
            $pieces['where'] .= " AND {$order_query_class->get_table_name('orders')}.id IN (" . implode( ',', $order_ids ) . ')';
        }

        return $pieces;
    }

    /**
     * Filter orders by vendor for HPOS disabled.
     *
     * @since 2.4.8
     * @version 2.4.8
     *
     * @param WP_Query $query The query object.
     */
    public function filter_orders_by_vendor_none_cot( $query ) {

        $nonce = isset( $_GET['wcv_vendor_orders'] ) ? sanitize_text_field( $_GET['wcv_vendor_orders'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wcv_vendor_orders' ) ) {
            return $query;
        }

        if ( ! self::is_shop_order_screen() ) {
            return $query;
        }

        $vendor_id = isset( $_GET['vendor_id'] ) ? sanitize_text_field( $_GET['vendor_id'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

        if ( empty( $vendor_id ) ) {
            return $query;
        }

        $order_ids = self::get_vendor_order_ids( $vendor_id );

        if ( empty( $order_ids ) ) {
            $query->set( 'post__in', array( 0 ) );
            return $query;
        }

        if ( ! empty( $order_ids ) ) {
            $query->set( 'post__in', $order_ids );
        }

        return $query;
    }

    /**
     * Get vendor order ids by vendor id.
     *
     * @version 2.4.8
     * @since   2.4.8
     * @param int $vendor_id The vendor id.
     *
     * @return array $vendor_orders_ids The vendor order ids.
     */
    public static function get_vendor_order_ids( $vendor_id ) {
        global $wpdb;

        $vendor_product_ids = array();
        $vendor_orders_ids  = array();

        $results = $wpdb->get_results( // phpcs:ignore
            $wpdb->prepare(
                "SELECT DISTINCT product_id FROM {$wpdb->prefix}pv_commission WHERE vendor_id = %d AND status != 'reversed'",
                $vendor_id,
            )
        );

        if ( empty( $results ) ) {
            return $vendor_orders_ids;
        }

        foreach ( $results as $result ) {
            $vendor_product_ids[] = $result->product_id;
        }

        if ( ! empty( $vendor_product_ids ) ) {
            $vendor_orders_ids = WCV_Queries::get_order_ids_for_product_ids( $vendor_product_ids );
        }

        return apply_filters( 'wcvendors_admin_vendor_orders_ids', $vendor_orders_ids, $vendor_id );
    }

    /**
     * Check shop_order screen.
     *
     * @since 2.4.8
     * @version 2.4.8
     *
     * @return bool
     */
    public static function is_shop_order_screen() {
        $screen = null;

        if ( function_exists( 'get_current_screen' ) ) {
            $screen = get_current_screen();
        }

        if ( ! $screen ) {
            return false;
        }

        if ( 'shop_order' !== $screen->post_type ) {
            return false;
        }

        if ( ! is_admin() ) {
            return false;
        }

        return true;
    }
}
