<?php

/**
 * Orders class
 *
 * @version 1.0.0
 * @since   1.0.0
 */
class WCV_Orders {

    /**
     * Whether the vendor can view orders.
     *
     * @var bool
     */
    public $can_view_orders;

    /**
     * Whether the vendor can export orders to CSV.
     *
     * @var bool
     */
    public $can_export_csv;

    /**
     * Whether the vendor can view customer emails.
     *
     * @var bool
     */
    public $can_view_emails;

    /**
     * Whether the vendor can view customer names.
     *
     * @var bool
     */
    public $can_view_name;

    /**
     * Whether the vendor can view customer shipping names.
     *
     * @var bool
     */
    public $can_view_shipping_name;

    /**
     * Whether the vendor can view customer shipping address.
     *
     * @var bool
     */
    public $can_view_address;

    /**
     * The product ID.
     *
     * @var int
     */
    public $product_id;

    /**
     * The list of orders.
     *
     * @var WC_Order[]
     */
    public $orders;

    /**
     * __construct()
     */
    public function __construct() {

        $this->can_view_orders        = wc_string_to_bool( get_option( 'wcvendors_capability_orders_enabled', 'no' ) );
        $this->can_export_csv         = wc_string_to_bool( get_option( 'wcvendors_capability_orders_export', 'no' ) );
        $this->can_view_emails        = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_email', 'no' ) );
        $this->can_view_name          = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_name', 'no' ) );
        $this->can_view_shipping_name = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_shipping_name', 'no' ) );
        $this->can_view_address       = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_shipping' ) );

        add_action( 'template_redirect', array( $this, 'check_access' ) );
        add_action( 'template_redirect', array( $this, 'process_export_orders' ) );
        add_shortcode( 'wcv_orders', array( $this, 'display_product_orders' ) );
    }


    /**
     * Check access
     */
    public function check_access() {

        global $post;

        $orders_page = get_option( 'wcvendors_product_orders_page_id' );

        // Only if the orders page is set should we check access.
        if ( $orders_page && is_page( $orders_page ) && is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'wcv_orders' ) && ! is_user_logged_in() ) {
            wp_safe_redirect( get_permalink( wc_get_page_id( 'myaccount' ) ), 303 );
            exit;
        }
    }


    /**
     *  Processs export orders csv request
     *
     * @since 1.9.4
     * @version 2.4.8 - Added nonce check, fix csv export not working
     */
    public function process_export_orders() {

        // phpcs:disable
        if ( empty( $_GET['orders_for_product'] ) ) {

            return sprintf(
                // translators: %s - Name used to refer to vendor.
                __( 'You haven\'t selected a product\'s orders to view! Please go back to the %s Dashboard and click Show Orders on the product you\'d like to view.', 'wc-vendors' ),
                wcv_get_vendor_name()
            );

        } else {
            $this->product_id = ! empty( $_GET['orders_for_product'] ) ? (int) $_GET['orders_for_product'] : false;

            $products = array( $this->product_id );

            $_product = wc_get_product( $this->product_id );

            if ( is_object( $_product ) ) {

                $children = $_product->get_children();

                if ( ! empty( $children ) ) {
                    $products = array_merge( $products, $children );
                    $products = array_unique( $products );
                }
            }

            $this->orders = WCV_Queries::get_orders_for_products( $products );
        }
        // phpcs:enable

        if ( ! $this->orders ) {
            return __( 'No orders.', 'wc-vendors' );
        }

        if ( ! wp_verify_nonce( isset( $_POST['export_orders_nonce'] ) ? $_POST['export_orders_nonce'] : '', 'export_orders' ) ) {
            return;
        }

        if ( $this->can_export_csv && ! empty( $_POST['export_orders'] ) ) {
            $this->download_csv();
        }
    }

    /**
     * Download CSV
     *
     * @return bool|void
     */
    public function download_csv() {

        if ( ! $this->orders ) {
            return false;
        }

        $order_details = self::format_order_details( $this->orders, $this->product_id );
        $items         = $order_details['items'];
        $body          = $order_details['body'];
        $product_id    = $order_details['product_id'];

        $headers = self::get_headers();

        // Export the CSV.
        require_once WCV_PLUGIN_DIR . 'classes/front/orders/class-export-csv.php';
        WCV_Export_CSV::output_csv( $this->product_id, $headers, $body, $items );
    }
        // phpcs:disable

    /**
     * Use views to display the Orders page
     *
     * @return string|void
     */
    public function display_product_orders() {
        $can_view_orders = wc_string_to_bool( get_option( 'wcvendors_capability_orders_enabled', 'no' ) );
        if ( ! WCV_Vendors::is_vendor( get_current_user_id() ) ) {
            ob_start();
            wc_get_template(
                'denied.php',
                array(),
                'wc-vendors/dashboard/',
                WCV_PLUGIN_DIR . 'templates/dashboard/'
            );

            return ob_get_clean();
        }

        if ( ! $can_view_orders ) {
            return __( 'You don\'t have permission to view orders.', 'wc-vendors' );
        }

        if ( empty( $_GET['orders_for_product'] ) ) {

            return sprintf(
                // translators: %s - Name used to refer to vendor.
                __( 'You haven\'t selected a product\'s orders to view! Please go back to the %s Dashboard and click Show Orders on the product you\'d like to view.', 'wc-vendors' ),
                wcv_get_vendor_name()
            );

        } else {
            global $wpdb;
            $show_reversed_order = wcv_is_show_reversed_order();
            $this->product_id    = ! empty( $_GET['orders_for_product'] ) ? (int) $_GET['orders_for_product'] : false;

            $products = array( $this->product_id );

            $_product  = wc_get_product( $this->product_id );
            $vendor_id = get_post_field( 'author', $this->product_id );

            if ( is_object( $_product ) ) {

                $children = $_product->get_children();

                if ( ! empty( $children ) ) {
                    $products = array_merge( $products, $children );
                    $products = array_unique( $products );
                }
            }

            $total_orders_sql = $wpdb->prepare(
                "SELECT COUNT(DISTINCT order_id) FROM {$wpdb->prefix}pv_commission WHERE product_id = %d",
                $this->product_id
            );
            if ( ! $show_reversed_order ) {
                $total_orders_sql = $wpdb->prepare(
                    "SELECT COUNT(DISTINCT order_id) FROM {$wpdb->prefix}pv_commission WHERE product_id = %d AND status != %s",
                    $this->product_id,
                    'reversed'
                );
            }
            $total_orders = $wpdb->get_var(
                $total_orders_sql // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
            );

            $per_page    = apply_filters( 'wcvendors_orders_per_page', get_option( 'wcvendors_free_orders_per_page', 10 ) );
            $total_pages = ceil( $total_orders / $per_page );
            $paged       = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

            $this->orders = WCV_Queries::get_orders_for_products(
                $products,
                $vendor_id,
                array(
					'limit' => $per_page,
					'paged' => $paged,
                )
            );
        }

        if ( ! $this->orders ) {
            return __( 'No orders.', 'wc-vendors' );
        }

        if ( ! empty( $_POST['submit_comment'] ) ) {
            require_once WCV_PLUGIN_DIR . 'classes/front/orders/class-submit-comment.php';
            $submit_comment = new WCV_Submit_Comment();
            $submit_comment->new_comment( $this->orders );
        }

        if ( isset( $_POST['mark_shipped'] ) ) {
            $order_id      = (int) $_POST['order_id'];
            $product_id    = (int) $_POST['product_id'];
            $shipped_order = wc_get_order( $order_id );
            wcv_mark_vendor_shipped( $shipped_order, get_post_field( 'post_author', $product_id ) );

            wc_add_notice( __( 'Order marked shipped', 'wc-vendors' ) );
        }

        $headers = self::get_headers();
        $all     = self::format_order_details( $this->orders, $this->product_id );

        wp_enqueue_script( 'wcvendors_frontend_script', WCV_ASSETS_URL . 'js/front-orders.js' );

        $shipping_providers = new WCV_Shipping_Providers();

        $providers      = $shipping_providers->get_providers();
        $provider_array = $shipping_providers->get_provider_url_list();

        // Show the Export CSV button.
        if ( $this->can_export_csv ) {
            wc_get_template( 'csv-export.php', array(), 'wc-vendors/orders/', WCV_PLUGIN_DIR . 'templates/orders/' );
        }

        wc_get_template(
            'orders.php',
            array(
                'headers'        => $headers,
                'body'           => $all['body'],
                'items'          => $all['items'],
                'product_id'     => $all['product_id'],
                'providers'      => $providers,
                'provider_array' => $provider_array,
                'total_pages'    => $total_pages,
                'paged'          => $paged,
            ),
            'wc-vendors/orders/',
            WCV_PLUGIN_DIR . 'templates/orders/'
        );
        // phpcs:enable
    }

    /**
     * Headers for the Orders page
     *
     * @return array
     */
    public function get_headers() {

        $headers = array(
            'order'   => __( 'Order', 'wc-vendors' ),
            'product' => __( 'Product Title', 'wc-vendors' ),
            'name'    => __( 'Full name', 'wc-vendors' ),
            'address' => __( 'Address', 'wc-vendors' ),
            'city'    => __( 'City', 'wc-vendors' ),
            'state'   => __( 'State', 'wc-vendors' ),
            'country' => __( 'Country', 'wc-vendors' ),
            'zip'     => __( 'Zip', 'wc-vendors' ),
            'email'   => __( 'Email address', 'wc-vendors' ),
            'date'    => __( 'Date', 'wc-vendors' ),
        );

        if ( ! $this->can_view_emails ) {
            unset( $headers['email'] );
        }

        if ( ! $this->can_view_name ) {
            unset( $headers['name'] );
        }

        if ( ! $this->can_view_address ) {
            unset( $headers['address'] );
            unset( $headers['city'] );
            unset( $headers['state'] );
            unset( $headers['zip'] );
        }

        return $headers;
    }


    /**
     * Format the orders with just the products we want
     *
     * @param array $orders The orders.
     * @param int   $product_id The product ID.
     *
     * @return array
     */
    public function format_order_details( $orders, $product_id ) {

        $items   = array();
        $body    = $items;
        $product = wc_get_product( $product_id )->get_title();

        foreach ( $orders as $i => $order ) {
            if ( is_bool( $order ) ) {
                continue;
            }

            $order_date = $order->get_date_created();

            $parent_order = wc_get_order( $order->get_parent_id() );
            $sub_orders   = (array) $parent_order->get_meta( 'wcv_sub_orders' );
            $product_ids  = (array) $order->get_meta( 'wcv_product_ids' );

            $shipping_first_name = $parent_order->get_shipping_first_name();
            $shipping_last_name  = $parent_order->get_shipping_last_name();
            $shipping_address_1  = $parent_order->get_shipping_address_1();
            $shipping_city       = $parent_order->get_shipping_city();
            $shipping_country    = $parent_order->get_shipping_country();
            $shipping_state      = $parent_order->get_shipping_state();
            $shipping_postcode   = $parent_order->get_shipping_postcode();
            $billing_email       = $parent_order->get_billing_email();

            $order_notes = $parent_order->get_customer_order_notes();

            $customer_order_notes = array();
            foreach ( $order_notes as $order_note ) {
                if ( in_array( $order->get_id(), $sub_orders, true ) && in_array( $product_id, $product_ids, true ) ) {
                    $customer_order_notes[] = $order_note;
                }
            }
            $order_number          = $order->get_order_number();
            $body[ $order_number ] = array(
                'order_number' => $parent_order->get_order_number(),
                'product'      => $product,
                'name'         => $shipping_first_name . ' ' . $shipping_last_name,
                'address'      => $shipping_address_1,
                'city'         => $shipping_city,
                'state'        => $shipping_state,
                'country'      => $shipping_country,
                'zip'          => $shipping_postcode,
                'email'        => $billing_email,
                'date'         => date_i18n( wc_date_format(), strtotime( $order_date ) ),
                'comments'     => $customer_order_notes,
            );

            if ( ! $this->can_view_emails ) {
                unset( $body[ $i ]['email'] );
            }

            if ( ! $this->can_view_name ) {
                unset( $body[ $i ]['name'] );
            }

            if ( ! $this->can_view_address ) {
                unset( $body[ $i ]['address'] );
                unset( $body[ $i ]['city'] );
                unset( $body[ $i ]['state'] );
                unset( $body[ $i ]['zip'] );
            }

            $items[ $order_number ]['total_qty'] = 0;
            $is_full_refunded                    = $order->get_total_refunded() === $order->get_total();
            foreach ( $order->get_items() as $line_id => $item ) {

                if ( $item['product_id'] !== $product_id && $item['variation_id'] !== $product_id ) {
                    continue;
                }

                $refund_total = $order->get_total_refunded_for_item( $item->get_id() );

                if ( $is_full_refunded ) {
                    $refund_total = $item['line_total'];
                }
                if ( ( $refund_total > 0 ) && $item->get_product_id() === $product_id || $item->get_variation_id() === $product_id ) {
                    $items[ $order_number ]['refund'] = array(
                        'total' => $refund_total,
                    );

                }

                $items[ $order_number ]['items'][]    = $item;
                $items[ $order_number ]['total_qty'] += $item['qty'];
            }
        }

        return array(
            'body'       => $body,
            'items'      => $items,
            'product_id' => $product_id,
        );
    }


    /**
     * Verify the current user can view orders for a product
     */
    public function verify_order_access() {

        if ( ! is_user_logged_in() || empty( $this->product_id ) ) {
            wp_safe_redirect( apply_filters( 'woocommerce_get_myaccount_page_id', get_permalink( wc_get_page_id( 'myaccount' ) ) ) );
            exit;
        }

        $product = wc_get_product( $this->product_id );

        if ( ! is_a( $product, 'WC_Product' ) || get_current_user_id() !== get_post_field( 'post_author', $product->get_id() ) ) {
            wp_safe_redirect(
                apply_filters(
                    'woocommerce_get_myaccount_page_id',
                    get_permalink( wc_get_page_id( 'myaccount' ) )
                )
            );
            exit;
        }
    }

    /**
     * Get the variation data for a product
     *
     * @param int $item_id The item ID.
     *
     * @since 1.9.4
     * @return string variation_data
     */
    public static function get_variation_data( $item_id ) {

        $_var_product     = new WC_Product_Variation( $item_id );
        $variation_data   = $_var_product->get_variation_attributes();
        $variation_detail = wc_get_formatted_variation( $variation_data, true );

        return $variation_detail;
    }
}
