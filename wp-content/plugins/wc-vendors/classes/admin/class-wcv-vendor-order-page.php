<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * WCV Vendor Order Page
 *
 * @version 2.4.8
 * @since   2.4.8 - Added HPOS compatibility.
 * @since   2.4.8 - Refactored from WCV_Vendor_Dashboard to WCV_Vendor_Order_Page
 *
 * @package WCVendors
 * @extends WP_List_Table
 */
class WCV_Vendor_Order_Page extends WP_List_Table {

    /**
     * The current vendor id
     *
     * @var integer
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public $vendor_id = 0;

    /**
     * The index
     *
     * @var int
     */
    public $index;

    /**
     * Can_view_comments
     *
     * @since    1.0.0
     * @access   public
     * @var      string $can_view_comments permission check for view comments
     */
    public $can_view_comments;


    /**
     * Can_add_comments
     *
     * @since    1.0.0
     * @access   public
     * @var      string $can_add_comments permission check for add comments
     */
    public $can_add_comments;


    /**
     * __construct function.
     *
     * @access public
     */
    public function __construct() {

        global $status, $page;

        $this->index = 0;

        $this->vendor_id = get_current_user_id();

        // Set parent defaults.
        parent::__construct(
            array(
                'singular' => __( 'order', 'wc-vendors' ),
                'plural'   => __( 'orders', 'wc-vendors' ),
                'ajax'     => false,
            )
        );

        $this->can_view_comments = wc_string_to_bool( get_option( 'wcvendors_capability_order_read_notes', 'no' ) );
        $this->can_add_comments  = wc_string_to_bool( get_option( 'wcvendors_capability_order_update_notes', 'no' ) );
    }


    /**
     * Column_default function.
     *
     * @access public
     *
     * @param object $item The item.
     * @param mixed  $column_name The column name.
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {

        global $wpdb;

        switch ( $column_name ) {
            case 'order_id':
                return $item->order_id;
            case 'customer':
                return $item->customer;
            case 'products':
                return $item->products;
            case 'total':
                return $item->total;
            case 'date':
                return $item->date;
            case 'status':
                return $item->status;
            default:
                return apply_filters( 'wcvendors_vendor_order_page_column_default', '', $item, $column_name );
        }
    }


    /**
     * Column_cb function.
     *
     * @access public
     *
     * @param mixed $item The item.
     *
     * @return string
     */
    public function column_cb( $item ) {

        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/
            'order_id',
            /*$2%s*/
            $item->order_id
        );
    }


    /**
     * Get_columns function.
     *
     * @access public
     * @return array
     */
    public function get_columns() {

        $columns = array(
            'cb'       => '<input type="checkbox" />',
            'order_id' => __( 'Order ID', 'wc-vendors' ),
            'customer' => __( 'Customer', 'wc-vendors' ),
            'products' => __( 'Products', 'wc-vendors' ),
            'total'    => __( 'Total', 'wc-vendors' ),
            'date'     => __( 'Date', 'wc-vendors' ),
            'status'   => __( 'Shipped', 'wc-vendors' ),
        );

        if ( ! $this->can_view_comments ) {
            unset( $columns['comments'] );
        }

        return apply_filters( 'wcvendors_vendor_order_page_get_columns', $columns );
    }


    /**
     * Get_sortable_columns function.
     *
     * @access public
     * @return array
     */
    public function get_sortable_columns() {

        $sortable_columns = array(
            'order_id' => array( 'order_id', false ),
            'total'    => array( 'total', false ),
            'status'   => array( 'status', false ),
        );

        return $sortable_columns;
    }


    /**
     * Get bulk actions
     *
     * @return array
     */
    public function get_bulk_actions() {

        $actions = array(
            'mark_shipped' => apply_filters( 'wcvendors_mark_shipped_label', __( 'Mark shipped', 'wc-vendors' ) ),
        );

        return $actions;
    }


    /**
     * Process bulk actions
     *
     * @return void
     */
    public function process_bulk_action() {

        // phpcs:disable
        if ( ! isset( $_GET['order_id'] ) ) {
            return;
        }

        if ( is_array( $_GET['order_id'] ) ) {

            $items = array_map( 'intval', $_GET['order_id'] );

            switch ( $this->current_action() ) {
                case 'mark_shipped':
                    $result = $this->mark_shipped( $items );

                    if ( $result ) {
                        echo '<div class="updated"><p>' . esc_attr( __( 'Orders marked shipped.', 'wc-vendors' ) ) . '</p></div>';
                    }
                    break;

                default:
                    // code...
                    break;
            }
        } elseif ( ! isset( $_GET['action'] ) ) {
            return;
        }
        // phpcs:enable
    }


    /**
     *  Mark orders as shipped
     *
     * @param array $ids IDs of orders to mark shipped.
     *
     * @version 2.0.0
     * @return void|bool
     */
    public function mark_shipped( $ids = array() ) {
        if ( ! empty( $ids ) ) {
            foreach ( $ids as $order_id ) {
                $sub_order = wcv_get_order( $order_id );

                $order = $sub_order->get_parent_id() > 0 ? wc_get_order( $sub_order->get_parent_id() ) : $sub_order;

                $vendor_ids = WCV_Vendors::get_vendor_ids_from_order( $order );

                if ( ! in_array( $this->vendor_id, $vendor_ids, true ) ) {
                    return;
                }

                $shippers = (array) $order->get_meta( 'wc_pv_shipped', true );

                if ( ! in_array( $this->vendor_id, $shippers, true ) ) {

                    $shippers[] = $this->vendor_id;

                    if ( ! empty( $mails ) ) {
                        WC()->mailer()->emails['WC_Email_Notify_Shipped']->trigger( $order_id, $this->vendor_id );
                    }
                    do_action( 'wcvendors_vendor_ship', $order_id, $this->vendor_id, $order );
                }

                $order->update_meta_data( 'wc_pv_shipped', $shippers );
                $order->save();
            }

            return true;
        }

        return false;
    }


    /**
     *  Get Orders to display in admin
     *
     * @return $orders
     */
    public function get_orders() {
        $orders          = array();
        $products        = array();
        $vendor_products = $this->get_vendor_products( $this->vendor_id );

        foreach ( $vendor_products as $product ) {
            $products[] = $product->get_id();
        }

        $vendor_product_orders = $this->get_orders_for_vendor_products( $products );

        $model_id = 0;

        if ( empty( $vendor_product_orders ) ) {
            return $orders;
        }

        foreach ( $vendor_product_orders as $current_order ) {

            // Check to see that the order hasn't been deleted or in the trash.
            if ( ! $current_order->get_status() || 'trash' === $current_order->get_status() ) {
                continue;
            }

            $parent_order = wc_get_order( $current_order->get_parent_id() );
            $valid_items  = WCV_Queries::get_products_for_order( $current_order->get_id() );
            $valid        = array();
            $items        = $current_order->get_items();

            foreach ( $items as $order_item_id => $item ) {
                $product_id = $item->get_variation_id() > 0 ? $item->get_variation_id() : $item->get_product_id();
                $author     = (int) get_post_field( 'post_author', $item->get_product_id() );

                if ( $author !== (int) $this->vendor_id ) {
                    continue;
                }

                if ( ! in_array( $order_item_id, $valid_items, true ) ) {
                    $valid[ $order_item_id ] = $item;
                }
            }

            $order_products_html = '';

            foreach ( $valid as $order_item_id => $item ) {

                $wc_product           = new WC_Product( $item['product_id'] );
                $order_products_html .= '<strong>' . $item['qty'] . ' x ' . $item['name'] . '</strong><br />';
                $item                 = $current_order->get_item( $order_item_id );
                $meta_data            = $item->get_meta_data();

                if ( ! empty( $meta_data ) ) {

                    $order_products_html .= '<table cellspacing="0" class="wcv_display_meta">';

                    foreach ( $meta_data as $meta ) {
                        $hidden_fields = apply_filters(
                            'woocommerce_hidden_order_itemmeta',
                            array(
                                '_qty',
                                '_tax_class',
                                '_product_id',
                                '_variation_id',
                                '_line_subtotal',
                                '_line_subtotal_tax',
                                '_line_total',
                                '_line_tax',
                                '_vendor_order_item_id',
                                '_vendor_commission',
                                    __( get_option( 'wcvendors_label_sold_by' ), 'wc-vendors' ), // phpcs:ignore
                            )
                        );

                        // Skip hidden core fields.
                        if ( in_array( $meta->key, $hidden_fields, true ) ) {
                            continue;
                        }

                        // Skip serialized meta.
                        if ( is_serialized( $meta->value ) ) {
                            continue;
                        }

                        // Get attribute data.
                        if ( taxonomy_exists( wc_sanitize_taxonomy_name( $meta->key ) ) ) {
                            $term        = get_term_by( 'slug', $meta->value, wc_sanitize_taxonomy_name( $meta->key ) );
                            $meta->key   = wc_attribute_label( wc_sanitize_taxonomy_name( $meta->key ) );
                            $meta->value = isset( $term->name ) ? $term->name : $meta->value;
                        } else {
                            $meta->key = apply_filters( 'woocommerce_attribute_label', wc_attribute_label( $meta->key, $wc_product ), $meta->key );
                        }

                        $order_products_html .= '<tr><th>' . wp_kses_post( rawurldecode( $meta->key ) ) . ':</th><td>' . rawurldecode( $meta->value ) . '</td></tr>';
                    }
                    $order_products_html .= '</table>';
                }
            }

            $order_id = $parent_order->get_id();
            $shippers = (array) $parent_order->get_meta( 'wc_pv_shipped', true );
            $shippers = array_filter( $shippers );
            $shippers = array_map( 'intval', $shippers );
            $shipped  = in_array( $this->vendor_id, $shippers, true ) ? __( 'Yes', 'wc-vendors' ) : __( 'No', 'wc-vendors' );

            $sum = WCV_Queries::sum_for_orders( array( $order_id ), array( 'vendor_id' => $this->vendor_id ), false );
            $sum = reset( $sum );

            $total = is_object( $sum ) && property_exists( $sum, 'total' ) ? $sum->total : $current_order->get_total();

            $comment_output = '';

            $show_billing_name     = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_name', 'no' ) );
            $show_shipping_name    = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_shipping_name', 'no' ) );
            $show_billing_address  = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_billing', 'no' ) );
            $show_shipping_address = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_shipping', 'no' ) );
            $order_date            = $current_order->get_date_created();

            $address = $parent_order->get_address( 'billing' );
            if ( ! $show_billing_name ) {
                unset( $address['first_name'] );
                unset( $address['last_name'] );
            }

            if ( ! $show_billing_address ) {
                unset( $address['company'] );
                unset( $address['address_1'] );
                unset( $address['address_2'] );
                unset( $address['city'] );
                unset( $address['state'] );
                unset( $address['postcode'] );
                unset( $address['country'] );
            }

            if ( ( get_option( 'woocommerce_ship_to_billing_address_only' ) === 'no' ) && ( $parent_order->get_formatted_shipping_address() ) ) {

                $address = $parent_order->get_address( 'shipping' );
                if ( ! $show_shipping_name ) {
                    unset( $address['first_name'] );
                    unset( $address['last_name'] );
                }

                if ( ! $show_shipping_address ) {
                    unset( $address['company'] );
                    unset( $address['address_1'] );
                    unset( $address['address_2'] );
                    unset( $address['city'] );
                    unset( $address['state'] );
                    unset( $address['postcode'] );
                    unset( $address['country'] );
                }
            }

            $customer = WC()->countries->get_formatted_address( $address );

            $order_items             = array();
            $order_items['order_id'] = $order_id;
            $order_items['customer'] = $customer;
            $order_items['products'] = $order_products_html;
            $order_items['total']    = wc_price( $total );
            $order_items['date']     = date_i18n( wc_date_format(), strtotime( $order_date ) );
            $order_items['status']   = $shipped;

            $orders[] = (object) $order_items;

            ++$model_id;
        }

        return $orders;
    }


    /**
     *  Get the vendor products sold
     *
     * @param int $vendor_id - the vendor id to get the products of.
     *
     * @return array
     */
    public function get_vendor_products( $vendor_id ) {

        $vendor_products = wc_get_products(
            array(
                'limit'   => -1,
                'orderby' => 'date',
                'type'    => array( 'simple', 'variable' ),
                'order'   => 'DESC',
                'author'  => $vendor_id,
            )
        );

        $vendor_products = apply_filters( 'wcvendors_get_vendor_products', $vendor_products );
        return $vendor_products;
    }


    /**
     * All orders for a specific product
     *
     * @param array $product_ids List of product IDs.
     * @param array $args args.
     *
     * @return false|array
     */
    public function get_orders_for_vendor_products( array $product_ids, array $args = array() ) {

        if ( empty( $product_ids ) ) {
            return false;
        }

        $now           = time();
        $now           = gmdate( 'Y-m-d', $now );
        $minus_30_days = gmdate( 'Y-m-d', strtotime( '-30 days', strtotime( $now ) ) );
        $start_date    = ! empty( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : $minus_30_days; // phpcs:ignore
        $end_date      = ! empty( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : $now;// phpcs:ignore
        $args['dates'] = array(
            'after'  => $start_date,
            'before' => $end_date,
        );

        $orders = WCV_Queries::get_orders_for_products( $product_ids, $this->vendor_id, $args );

        return apply_filters(
            'wcvendors_get_orders_for_vendor_products',
            $orders,
            $product_ids,
            $args
        );
    }

    /**
     * Extra table nav with date filter
     *
     * @param string $which Top or bottom.
     */
    public function extra_tablenav( $which ) {
        if ( 'top' === $which ) {
            $this->date_filter();
        }
    }

    /**
     * Date filter with range
     */
    public function date_filter() {
        $now           = time();
        $now           = gmdate( 'Y-m-d', $now );
        $minus_30_days = gmdate( 'Y-m-d', strtotime( '-30 days', strtotime( $now ) ) );
        $start_date    = ! empty( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : $minus_30_days; // phpcs:ignore
        $end_date      = ! empty( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : $now; // phpcs:ignore
        ?>
        <div class="alignleft actions">
            <label for="start_date" class="screen-reader-text"><?php esc_html_e( 'Start date', 'wc-vendors' ); ?></label>
            <input type="date" id="start_date" name="start_date" class="wcv_date_range" placeholder="<?php esc_html_e( 'Start date', 'wc-vendors' ); ?>" value="<?php echo esc_attr( $start_date ); ?>" />
            <label for="end_date" class="screen-reader-text"><?php esc_html_e( 'End date', 'wc-vendors' ); ?></label>
            <input type="date" id="end_date" name="end_date" class="wcv_date_range" placeholder="<?php esc_html_e( 'End date', 'wc-vendors' ); ?>" value="<?php echo esc_attr( $end_date ); ?>" />
            <input type="submit" name="filter_date" id="post-query-submit" class="button" value="<?php esc_html_e( 'Filter', 'wc-vendors' ); ?>" />
        </div>
        <?php
    }

    /**
     * Prepare_items function.
     *
     * @access public
     */
    public function prepare_items() {

        /**
         * Init column headers
         */
        $this->_column_headers = $this->get_column_info();

        /**
         * Process bulk actions
         */
        $this->process_bulk_action();

        /**
         * Get items
         */

        $this->items = $this->get_orders();
    }
}
