<?php
/**
 * Queries class
 *
 * @version 2.4.8
 * @since   2.4.8 - Add HPOS Compatibility
 */
class WCV_Queries {

    /**
     * Order ID.
     *
     * @param int $user_id The user ID.
     *
     * @return array
     */
    public static function get_commission_products( $user_id ) {
        global $wpdb;

        $dates                = self::orders_within_range();
        $vendor_products      = array();
        $show_reversed_orders = wcv_is_show_reversed_order();

        if ( ! $show_reversed_orders ) {
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT DISTINCT product_id FROM {$wpdb->prefix}pv_commission WHERE vendor_id = %s AND status != 'reversed'
                    AND time >= %s AND time <= %s",
                    $user_id,
                    $dates['after'],
                    $dates['before']
                )
            );
        } else {
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT DISTINCT product_id FROM {$wpdb->prefix}pv_commission WHERE vendor_id = %s
                    AND time >= %s AND time <= %s",
                    $user_id,
                    $dates['after'],
                    $dates['before']
                )
            );
        }

        $ids = array();
        foreach ( $results as $row ) {
            $ids[] = $row->product_id;
        }

        $product_types = array_keys( wc_get_product_types() );
        $product_types = array_merge( $product_types, array( 'variation' ) );
        $product_types = apply_filters( 'wcvendors_get_commission_products_type', $product_types );

        if ( ! empty( $ids ) ) {
            $vendor_products = wc_get_products(
                array(
                    'limit'   => -1,
                    'orderby' => 'date',
                    'order'   => 'DESC',
                    'include' => $ids,
                    'type'    => $product_types,
                )
            );
        }

        return $vendor_products;
    }

    /**
     * Get products for order.
     *
     * @param int $order_id The orderId.
     *
     * @return array
     */
    public static function get_products_for_order( $order_id ) {
        global $wpdb;

        $vendor_products      = array();
        $vendor_id            = get_current_user_id();
        $show_reversed_orders = wcv_is_show_reversed_order();

        if ( ! $show_reversed_orders ) {
            $result = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT product_id FROM {$wpdb->prefix}pv_commission WHERE order_id = %s 
                    AND status != 'reversed'
                    AND vendor_id = %s GROUP BY product_id",
                    $order_id,
                    $vendor_id
                )
            );
        } else {
            $result = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT product_id FROM {$wpdb->prefix}pv_commission WHERE order_id = %s
                    AND vendor_id = %s GROUP BY product_id",
                    $order_id,
                    $vendor_id
                )
            );
        }

        $results = apply_filters( 'wcvendors_get_vendor_products', $result );

        if ( empty( $results ) ) {
            return array();
        }

        foreach ( $results as $value ) {
            $vendor_products[] = $value->product_id;
        }

        return $vendor_products;
    }

    /**
     * All orders for a specific product
     *
     * @param array     $product_ids The product ids.
     * @param int|int[] $vendor_id   The vendor ID.
     * @param array     $args        The query args.
     *
     * @return WC_Order[]
     */
    public static function get_orders_for_products( array $product_ids, $vendor_id = 0, array $args = array() ) {
        if ( empty( $product_ids ) ) {
            return array();
        }
        $show_reversed_orders = wcv_is_show_reversed_order();
        $dates                = self::orders_within_range();

        $vendor_id = ( $vendor_id > 0 ) ? $vendor_id : get_current_user_id();

        $defaults = array(
            'type'      => WC_Order_Vendor::ORDER_TYPE,
            'status'    => apply_filters(
                'wcvendors_completed_statuses',
                array_keys( wc_get_order_statuses() )
            ),
            'orderby'   => 'date',
            'order'     => 'DESC',
            'vendor_id' => $vendor_id,
        );

        $args = wp_parse_args( $args, $defaults );

        $order_ids = self::get_order_ids_for_product_ids( $product_ids );

        if ( $args['vendor_id'] && WC_Order_Vendor::ORDER_TYPE === $args['type'] ) {
            $args['parent'] = $order_ids;
        } elseif ( ! $args['vendor_id'] && WC_Order_Vendor::ORDER_TYPE === $args['type'] ) {
            unset( $args['vendor_id'] );
            $args['include'] = $order_ids;
        }
        $args['post__in'] = $order_ids;

        if ( ! empty( array_filter( $dates ) ) ) {
            $args['date_created'] = "{$dates['after']}...{$dates['before']}";
        }

        if ( isset( $args['dates'] ) && is_array( $args['dates'] ) ) {
            $arg_date   = $args['dates'];
            $start_date = $arg_date['after'];
            $end_date   = $arg_date['before'];

            $args['date_created'] = "{$start_date}...{$end_date}";
        }

        if ( $show_reversed_orders ) {
            $args['status'][] = 'reversed';
        }

        $orders = wc_get_orders( $args );

        return $orders;
    }

    /**
     * Sum of orders for a specific product
     *
     * @param array $product_ids The product IDs.
     * @param array $args        The arguments.
     *
     * @return array
     */
    public static function sum_orders_for_products( array $product_ids, array $args = array() ) {
        global $wpdb;

        $dates = self::orders_within_range();

        $defaults = array(
            'status' => apply_filters( 'wcvendors_completed_statuses', array( 'completed', 'processing' ) ),
            'dates'  => array(
                'before' => $dates['before'],
                'after'  => $dates['after'],
            ),
        );

        foreach ( $product_ids as $id ) {
            $products = wc_get_products(
                array(
                    'limit'  => -1,
                    'parent' => $id,
                    'return' => 'ids',
                )
            );

            if ( ! empty( $products ) ) {
                foreach ( $products as $product_id ) {
                    $product_ids[] = $product_id;
                }
            }
        }

        $args = wp_parse_args( $args, $defaults );

        $sql = "
        SELECT COUNT(order_id) as total_orders,
                SUM(total_due + total_shipping + tax) as line_total,
                SUM(qty) as qty,
                product_id

        FROM {$wpdb->prefix}pv_commission

        WHERE   product_id IN ('" . implode( "','", $product_ids ) . "')
        AND     time >= '" . $args['dates']['after'] . "'
        AND     time <= '" . $args['dates']['before'] . "'
        AND     status != 'reversed'
        ";

        if ( ! empty( $args['vendor_id'] ) ) {
            $sql .= "
            AND vendor_id = {$args['vendor_id']}
        ";
        }

        $sql .= '
        GROUP BY product_id
        ORDER BY time DESC;
        ';

        $orders = $wpdb->get_results( $sql ); // phpcs:ignore

        return $orders;
    }

    /**
     * Sum of orders for a specific order
     *
     * @param array $order_ids  The order IDs.
     * @param array $args       The query args.
     * @param bool  $date_range Whether to query by date range.
     *
     * @return object
     */
    public static function sum_for_orders( array $order_ids, array $args = array(), $date_range = true ) {
        global $wpdb;

        $dates = ( $date_range ) ? self::orders_within_range() : array();

        $defaults = array(
            'status' => apply_filters( 'wcvendors_completed_statuses', array( 'completed', 'processing' ) ),
        );

        $args = wp_parse_args( $args, $defaults );

        $sql = "SELECT COUNT(order_id) as total_orders,
                SUM(total_due + total_shipping + tax) as line_total,
                SUM(qty) as qty,
                product_id FROM {$wpdb->prefix}pv_commission
                WHERE   order_id IN ('" . implode( "','", $order_ids ) . "')";

        if ( ! empty( $dates ) ) {
            $sql .= "
            AND     time >= '" . $dates['after'] . "'
            AND     time <= '" . $dates['before'] . "'
        ";
        }

        if ( ! empty( $args['vendor_id'] ) ) {
            $sql .= "
            AND vendor_id = {$args['vendor_id']}
        ";
        }

        $sql .= '
        GROUP BY order_id, product_id
        ORDER BY time DESC;
        ';

        $orders = $wpdb->get_results( $sql ); // phpcs:ignore

        return $orders;
    }

    /**
     * Get a list of order IDs for a specific product or multiple products
     *
     * @param array $product_ids The list of product IDs.
     * @param array $order_types The order types.
     * @param array $order_status The order status.
     * @return array|int
     * @version 2.4.8
     * @since   2.4.8 - Added
     */
    public static function get_order_ids_for_product_ids( $product_ids, $order_types = array(), $order_status = array() ) {
        global $wpdb;

        $order_types = wp_parse_args(
            $order_types,
            array( 'shop_order', WC_Order_Vendor::ORDER_TYPE ),
        );

        $order_status = wp_parse_args(
            $order_status,
            array( 'wc-completed', 'wc-processing' ),
        );

        $order_types          = implode( "','", $order_types );
        $order_status         = implode( "','", $order_status );
        $products_placeholder = implode( ',', array_fill( 0, count( $product_ids ), '%d' ) );
        // phpcs:disable
        if ( wcv_cot_enabled() ) {
            return $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT order_items.order_id
                    FROM {$wpdb->prefix}woocommerce_order_items as order_items
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta
                        ON order_items.order_item_id = order_item_meta.order_item_id
                    LEFT JOIN {$wpdb->prefix}wc_orders AS orders ON order_items.order_id = orders.id
                    WHERE orders.type IN ('" . $order_types . "')
                    AND orders.status IN ('" . $order_status . "')
                    AND order_items.order_item_type = 'line_item'
                    AND order_item_meta.meta_key IN ( '_product_id', '_variation_id' )
                    AND order_item_meta.meta_value IN ($products_placeholder)",
                    $product_ids
                )
            );
        }

        // Get order id from post meta.
        $order_ids =  $wpdb->get_col(
            $wpdb->prepare(
                "SELECT order_items.order_id
                FROM {$wpdb->prefix}woocommerce_order_items as order_items
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta
                    ON order_items.order_item_id = order_item_meta.order_item_id
                LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
                WHERE posts.post_type IN ('" . $order_types . "')
                AND posts.post_status IN ('" . $order_status . "')
                AND order_items.order_item_type = 'line_item'
                AND order_item_meta.meta_value IN ($products_placeholder)
                AND order_item_meta.meta_key IN ( '_product_id', '_variation_id' )",
                $product_ids
            )
        );
        return $order_ids;
        // phpcs:enable
    }

    /**
     * Orders for range filter function
     *
     * @return array
     */
    public static function orders_within_range() {
        global $start_date, $end_date;

        // Need to check if the session exists and if it doesn't create it.
        if ( null === WC()->session ) {
            $session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' );
            // Prefix session class with global namespace if not already namespaced.
            if ( false === strpos( $session_class, '\\' ) ) {
                $session_class = '\\' . $session_class;
            }
            WC()->session = new $session_class();
            WC()->session->init();
        }

        // phpcs:disable
        if ( ! empty( $_POST['start_date'] ) ) {
            WC()->session->set( 'wcv_order_start_date', strtotime( sanitize_text_field( wp_unslash( $_POST['start_date'] ) ) ) );
        }

        if ( ! empty( $_POST['end_date'] ) ) {
            WC()->session->set( 'wcv_order_end_date', strtotime( sanitize_text_field( wp_unslash( $_POST['end_date'] ) ) ) );
        }
        // phpcs:enable

        $start_date = WC()->session->get( 'wcv_order_start_date', strtotime( current_time( 'Y-M' ) . '-01' ) );
        $end_date   = WC()->session->get( 'wcv_order_end_date', strtotime( current_time( 'mysql' ) ) );

        $after  = gmdate( 'Y-m-d', $start_date );
        $before = gmdate( 'Y-m-d', strtotime( '+1 day', $end_date ) );

        return apply_filters(
            'wcvendors_orders_date_range',
            array(
                'after'  => $after,
                'before' => $before,
            )
        );
    }
}
