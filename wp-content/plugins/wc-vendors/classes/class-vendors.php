<?php

/**
 * Vendor functions
 *
 * @author  Matt Gates <http://mgates.me>, WC Vendors <http://wcvendors.com>
 * @package WCVendors
 *
 * @version 2.4.8
 */

/**
 * WC Vendors Vendor class
 *
 * @version 2.4.8
 * @since   2.4.8 - HPOS Compatibility
 */
class WCV_Vendors {
    /**
     * Constructor
     *
     * @since 2.4.8 Added hook to create child orders via API.
     */
    public function __construct() {
        add_action( 'woocommerce_checkout_order_processed', array( __CLASS__, 'create_child_orders' ), 10, 1 );
        add_action( 'woocommerce_new_order', array( __CLASS__, 'create_child_orders' ), 10, 1 );
        add_action( 'woocommerce_rest_insert_shop_order_object', array( __CLASS__, 'create_child_orders_api' ), 10, 1 );
        add_filter( 'woocommerce_order_data_store_cpt_get_orders_query', array( $this, 'cpt_order_query_vars' ), 10, 2 );
        add_filter( 'woocommerce_order_query_args', array( $this, 'order_query_args' ), 10 );
        add_filter( 'init', array( $this, 'add_rewrite_rules' ), 0 );

        add_action( 'rest_api_init', array( $this, 'register_rest_fields' ) );
        add_action( 'woocommerce_order_status_changed', array( $this, 'update_sub_order_status' ), 10, 3 );

        if ( wcv_cot_enabled() ) {
            add_action( 'woocommerce_delete_order', array( $this, 'delete_child_orders' ), 10, 1 );
            add_action( 'woocommerce_trash_order', array( $this, 'trash_sub_orders' ), 10 );
            add_action( 'woocommerce_untrash_order', array( $this, 'untrash_sub_orders' ), 10, 2 );
        } else {
            add_action( 'deleted_post', array( $this, 'handle_delete_post' ), 10, 1 );
            add_action( 'trashed_post', array( $this, 'handle_trash_post' ), 10 );
            add_action( 'untrashed_post', array( $this, 'handle_untrash_post' ), 10, 2 );
        }

        $this->add_filters_to_prevent_email_sending();
    }

    /**
     * Add filters to WooCommerce email enabled
     *
     * @return void
     * @version 2.4.9.2
     * @since   2.4.9.2
     */
    public function add_filters_to_prevent_email_sending() {
        $filters = array(
			'woocommerce_email_enabled_new_order',
			'woocommerce_email_enabled_failed_order',
			'woocommerce_email_enabled_cancelled_order',
			'woocommerce_email_enabled_customer_completed_order',
			'woocommerce_email_enabled_customer_invoice',
			'woocommerce_email_enabled_customer_note',
			'woocommerce_email_enabled_customer_on_hold_order',
			'woocommerce_email_enabled_customer_processing_order',
			'woocommerce_email_enabled_customer_refunded_order',
            'woocommerce_email_enabled_vendor_notify_cancelled_order',
            'woocommerce_email_enabled_vendor_notify_order',
            'woocommerce_email_enabled_admin_notify_shipped',
		);

        /**
         * Allow other plugins to add their own filters to prevent emails from being sent.
         *
         * @param array $filters The filters to add to in order to disable emails.
         */
        $filters = apply_filters( 'wcvendors_email_enabled_filters', $filters );

        foreach ( $filters as $filter_name ) {
			add_filter( $filter_name, array( $this, 'disable_emails_for_sub_orders' ), 10, 2 );
		}
    }

    /**
     * Disable email for sub orders
     *
     * @param bool     $is_enabled Whether the email is enabled or not.
     * @param WC_Order $order The order object.
     *
     * @return bool $is_enabled Whether the email is enabled or not.
     * @version 2.4.9.2
     * @since   2.4.9.2 - Added
     */
    public function disable_emails_for_sub_orders( $is_enabled, $order ) {
        /**
         * Allows to filter the order types to disable emails for
         *
         * @param array $order_types The order types to disable emails for.
         * @return array $order_types The filtered order types to disable emails for.
         */
        $disable_for_order_types = apply_filters(
            'wcvendors_disable_email_for_order_types',
            array( 'shop_order_vendor' )
        );

        if ( in_array( $order->get_type(), $disable_for_order_types, true ) ) {
            return false;
        }

        return $is_enabled;
    }

    /**
     * Retrieve all products for a vendor
     *
     * @param int $vendor_id The vendor Id.
     *
     * @return array
     */
    public static function get_vendor_products( $vendor_id ) {

        $args = array(
            'limit'  => -1,
            'author' => $vendor_id,
            'status' => 'publish',
        );

        $args = apply_filters_deprecated(
            'pv_get_vendor_products_args',
            array( $args ),
            '2.4.9',
            'wcvendors_get_vendor_products_args'
        );
        $args = apply_filters( 'wcvendors_get_vendor_products_args', $args );

        return wc_get_products( $args );
    }

    /**
     * Get default commission rate for a vendor
     *
     * @param int $vendor_id The vendor Id.
     * @return number
     * @version 2.4.8
     * @since   2.0.0
     */
    public static function get_default_commission( $vendor_id ) {
        return get_user_meta( $vendor_id, 'pv_custom_commission_rate', true );
    }

    /**
     * Get vendors from an order including all user meta and vendor items filtered and grouped
     *
     * @param WC_Order $order The order object.
     *
     * @return array|WP_Error $vendors Array of vendor details or WP_Error if invalid order.
     * @version 2.0.0
     * @since   2.4.8 - Added HPOS Compatibility.
     */
    public static function get_vendors_from_order( $order ) {

        $vendors = array();

        if ( ! is_a( $order, 'WC_Order' ) ) {
            return new WP_Error(
                'invalid_order',
                __( 'Cannot get vendors from an invalid order object.', 'wcvendors' )
            );
        }

        // Only loop through order items if there isn't an error.
        $order_items = $order->get_items();

        if ( count( $order_items ) > 0 ) {

            foreach ( $order_items as $order_item ) {
                if ( ! is_a( $order_item, 'WC_Order_Item_Product' ) ) {
                    continue;
                }

                $vendor_id = self::get_vendor_from_product( $order_item->get_product_id() );

                if ( ! self::is_vendor( $vendor_id ) ) {
                    continue;
                }

                if ( array_key_exists( $vendor_id, $vendors ) ) {
                    $vendors[ $vendor_id ]['line_items'][ $order_item->get_id() ] = $order_item;
                } else {
                    $vendor_details        = array(
                        'vendor'     => get_userdata( $vendor_id ),
                        'line_items' => array(
                            $order_item->get_id() => $order_item,
						),
                    );
                    $vendors[ $vendor_id ] = $vendor_details;
                }
            }
        }

        // legacy filter left in place.
        $vendors = apply_filters_deprecated(
            'pv_vendors_from_order',
            array( $vendors, $order ),
            WCV_VERSION,
            'wcvendors_get_vendors_from_order'
        );

        return apply_filters( 'wcvendors_get_vendors_from_order', $vendors, $order );
    }

    /**
     * Get vendor IDs from order
     *
     * @param WC_Order $order The order object.
     * @return int[]
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public static function get_vendor_ids_from_order( $order ) {
        return (array) $order->get_meta( 'wcv_vendor_ids' );
    }

    /**
     * Get sub order ids from order object
     *
     * @param WC_Order $order The order object.
     * @return WC_Order[]
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public static function get_order_sub_orders( $order ) {
        $sub_order_ids = (array) $order->get_meta( 'wcv_sub_orders' );

        $sub_order_ids = array_filter( $sub_order_ids );

        return wcv_get_vendor_orders(
            array(
                'type'    => WC_Order_Vendor::ORDER_TYPE,
				'include' => $sub_order_ids,
            )
        );
    }


    /**
     * Get vendor dues from order.
     *
     * @param WC_Order $order The order object.
     * @param bool     $group Whether to group the dues by vendor.
     *
     * @return array
     */
    public static function get_vendor_dues_from_order( $order, $group = true ) {

        $receiver       = array();
        $shipping_given = 0;
        $tax_given      = 0;

        WCV_Shipping::$pps_shipping_costs = array();

        foreach ( $order->get_items() as $key => $order_item ) {

            $product_id = ! empty( $order_item['variation_id'] ) ? $order_item['variation_id'] : $order_item['product_id'];
            $author     = self::get_vendor_from_product( $product_id );

            if ( ! self::is_vendor( $author ) ) {
                continue;
            }

            $give_tax               = 'yes' === get_option( 'wcvendors_vendor_give_taxes', 'no' ) ? true : false;
            $give_shipping          = 'yes' === get_option( 'wcvendors_vendor_give_shipping', 'no' ) ? true : false;
            $give_tax_override      = get_user_meta( $author, 'wcv_give_vendor_tax', true );
            $give_shipping_override = get_user_meta( $author, 'wcv_give_vendor_shipping', true );
            $is_vendor              = self::is_vendor( $author );
            $commission             = $is_vendor ? WCV_Commission::calculate_commission( $order_item['line_subtotal'], $product_id, $order, $order_item['qty'], $order_item ) : 0;
            $tax                    = ! empty( $order_item['line_tax'] ) ? (float) $order_item['line_tax'] : 0;
            $order_id               = $order->get_id();

            // Check if shipping is enabled.
            if ( 'no' === get_option( 'woocommerce_calc_shipping' ) ) {
                $shipping     = 0;
                $shipping_tax = 0;
            } else {
                $shipping_costs = WCV_Shipping::get_shipping_due( $order_id, $order_item, $author, $product_id );
                $shipping       = $shipping_costs['amount'];
                $shipping_tax   = $shipping_costs['tax'];
            }

            $_product = new WC_Product( $order_item['product_id'] );

            // Add line item tax and shipping taxes together.
            $total_tax = ( $_product->is_taxable() ) ? (float) $tax + (float) $shipping_tax : 0;

            // Tax override on a per vendor basis.
            if ( $give_tax_override ) {
                $give_tax = true;
            }
            // Shipping override.
            if ( $give_shipping_override ) {
                $give_shipping = true;
            }

            if ( $is_vendor ) {

                $shipping_given += $give_shipping ? $shipping : 0;
                $tax_given      += $give_tax ? $total_tax : 0;

                $give  = 0;
                $give += ! empty( $receiver[ $author ]['total'] ) ? $receiver[ $author ]['total'] : 0;
                $give += $give_shipping ? $shipping : 0;
                $give += $commission;
                $give += $give_tax ? $total_tax : 0;

                if ( $group ) {

                    $receiver[ $author ] = array(
                        'vendor_id'  => (int) $author,
                        'commission' => ! empty( $receiver[ $author ]['commission'] ) ? $receiver[ $author ]['commission'] + $commission : $commission,
                        'shipping'   => $give_shipping ? ( ! empty( $receiver[ $author ]['shipping'] ) ? $receiver[ $author ]['shipping'] + $shipping : $shipping ) : 0,
                        'tax'        => $give_tax ? ( ! empty( $receiver[ $author ]['tax'] ) ? $receiver[ $author ]['tax'] + $total_tax : $total_tax ) : 0,
                        'qty'        => ! empty( $receiver[ $author ]['qty'] ) ? $receiver[ $author ]['qty'] + $order_item['qty'] : $order_item['qty'],
                        'total'      => $give,
                    );

                } else {

                    $receiver[ $author ][ $key ] = array(
                        'vendor_id'  => (int) $author,
                        'product_id' => $product_id,
                        'commission' => $commission,
                        'shipping'   => $give_shipping ? $shipping : 0,
                        'tax'        => $give_tax ? $total_tax : 0,
                        'qty'        => $order_item['qty'],
                        'total'      => ( $give_shipping ? $shipping : 0 ) + $commission + ( $give_tax ? $total_tax : 0 ),
                    );

                }
            }

            $admin_comm = $order_item['line_subtotal'] - $commission;

            if ( $group ) {
                $receiver[1] = array(
                    'vendor_id'  => 1,
                    'qty'        => ! empty( $receiver[1]['qty'] ) ? $receiver[1]['qty'] + $order_item['qty'] : $order_item['qty'],
                    'commission' => ! empty( $receiver[1]['commission'] ) ? $receiver[1]['commission'] + $admin_comm : $admin_comm,
                    'total'      => ! empty( $receiver[1] ) ? $receiver[1]['total'] + $admin_comm : $admin_comm,
                );
            } else {
                $receiver[1][ $key ] = array(
                    'vendor_id'  => 1,
                    'product_id' => $product_id,
                    'commission' => $admin_comm,
                    'shipping'   => 0,
                    'tax'        => 0,
                    'qty'        => $order_item['qty'],
                    'total'      => $admin_comm,
                );
            }
        }

        // Add remainders on end to admin.
        $discount = $order->get_total_discount();
        $shipping = round( ( $order->get_shipping_total() - $shipping_given ), 2 );
        $tax      = round( $order->get_total_tax() - $tax_given, 2 );
        $total    = ( $tax + $shipping ) - $discount;

        if ( ! empty( $receiver ) ) {
            if ( $group ) {
                $r_total                   = round( $receiver[1]['total'], 2 );
                $receiver[1]['commission'] = round( $receiver[1]['commission'], 2 ) - round( $discount, 2 );
                $receiver[1]['shipping']   = $shipping;
                $receiver[1]['tax']        = $tax;
                $receiver[1]['total']      = $r_total + round( $total, 2 );
            } else {
                $r_total                           = round( $receiver[1][ $key ]['total'], 2 );
                $receiver[1][ $key ]['commission'] = round( $receiver[1][ $key ]['commission'], 2 ) - round( $discount, 2 );
                $receiver[1][ $key ]['shipping']   = ( $order->get_shipping_total() - $shipping_given );
                $receiver[1][ $key ]['tax']        = $tax;
                $receiver[1][ $key ]['total']      = $r_total + round( $total, 2 );
            }
        }

        // Reset the array keys.
        $receiver = apply_filters_deprecated(
            'wcv_vendor_dues',
            array( $receiver, $order, $group ),
            '2.3.0',
            'wcvendors_vendor_dues'
        );

        return apply_filters( 'wcvendors_vendor_dues', $receiver, $order, $group );
    }


    /**
     * Return the PayPal address for a vendor.
     *
     * If no PayPal is set, it returns the vendor's email.
     *
     * @param int $vendor_id The vendor ID.
     *
     * @return string
     */
    public static function get_vendor_paypal( $vendor_id ) {

        $paypal = get_user_meta( $vendor_id, $meta_key = 'pv_paypal', true );
        $paypal = ! empty( $paypal ) ? $paypal : get_the_author_meta( 'user_email', $vendor_id );

        return $paypal;
    }

    /**
     * Check if a vendor has an amount due for an order already
     *
     * @param int $vendor_id The vendor ID.
     * @param int $order_id  The order ID.
     *
     * @return string|null
     */
    public static function count_due_by_vendor( $vendor_id, $order_id ) {

        global $wpdb;

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}pv_commission WHERE vendor_id = %d AND order_id = %d AND status = %s",
                $vendor_id,
                $order_id,
                'due'
            )
        );

        return $count;
    }

    /**
     * All commission due for a specific vendor
     *
     * @param int $vendor_id The vendor ID.
     *
     * @return array
     */
    public static function get_due_orders_by_vendor( $vendor_id ) {

        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}pv_commission WHERE vendor_id = %d AND status = %s",
                $vendor_id,
                'due'
            )
        );

        return $results;
    }

    /**
     * Get vendor from product.
     *
     * @param int $product_id The product ID.
     *
     * @return int
     */
    public static function get_vendor_from_product( $product_id ) {
        $author = -1;

        // Make sure we are returning an author for products or product variations only.
        if ( 'product' === get_post_type( $product_id ) || 'product_variation' === get_post_type( $product_id ) ) {
            $parent = get_post_ancestors( $product_id );
            if ( $parent ) {
                $product_id = $parent[0];
            }

            $post   = get_post( $product_id );
            $author = $post ? $post->post_author : 1;

            $author = apply_filters_deprecated(
                'pv_product_author',
                array( $author, $product_id ),
                '2.4.9',
                'wcvendors_product_author'
            );

            $author = apply_filters( 'wcvendors_product_author', $author, $product_id );
        }

        return $author;
    }


    /**
     * Checks whether the ID provided is vendor capable or not
     *
     * @param int $user_id The User ID.
     *
     * @return bool
     */
    public static function is_vendor( $user_id ) {
        $user         = get_userdata( $user_id );
        $vendor_roles = apply_filters( 'wcvendors_vendor_roles', array( 'vendor' ) );
        $is_vendor    = false;

        if ( is_object( $user ) && is_array( $user->roles ) ) {

            foreach ( $vendor_roles as $role ) {
                if ( in_array( $role, $user->roles, true ) ) {
                    $is_vendor = true;
                    break;
                }
            }
        }

        $is_vendor = apply_filters_deprecated(
            'pv_is_vendor',
            array( $is_vendor, $user_id ),
            '2.4.9',
            'wcvendors_is_vendor'
        );

        return apply_filters( 'wcvendors_is_vendor', $is_vendor, $user_id );
    }


    /**
     * Grabs the vendor ID whether a username or an int is provided
     * and returns the vendor_id if it's actually a vendor
     *
     * @param string|int $input The username or user ID.
     *
     * @return int
     */
    public static function get_vendor_id( $input ) {
        if ( empty( $input ) ) {
            return false;
        }

        $users = get_users(
            array(
                'meta_key'   => 'pv_shop_slug',
                'meta_value' => sanitize_title( $input ),
            )
        );

        if ( ! empty( $users ) && 1 === count( $users ) ) {
            $vendor = $users[0];
        } else {
            $int_vendor = is_numeric( $input );
            $vendor     = ! empty( $int_vendor ) ? get_userdata( $input ) : get_user_by( 'login', $input );
        }

        if ( $vendor ) {
            $vendor_id = $vendor->ID;
            if ( self::is_vendor( $vendor_id ) ) {
            return $vendor_id;
            }
        }

        return false;
    }


    /**
     * Retrieve the shop page for a specific vendor
     *
     * @param int $vendor_id The vendor ID.
     *
     * @return string
     */
    public static function get_vendor_shop_page( $vendor_id ) {

        $vendor_id = self::get_vendor_id( $vendor_id );
        if ( ! $vendor_id ) {
            return '';
        }

        $slug   = get_user_meta( $vendor_id, 'pv_shop_slug', true );
        $vendor = ! $slug ? get_userdata( $vendor_id )->user_login : $slug;

        if ( get_option( 'permalink_structure' ) ) {
            $permalink = trailingslashit( get_option( 'wcvendors_vendor_shop_permalink' ) );

            return trailingslashit( home_url( sprintf( '/%s%s', $permalink, $vendor ) ) );
        } else {
            return esc_url( add_query_arg( array( 'vendor_shop' => $vendor ), get_post_type_archive_link( 'product' ) ) );
        }
    }


    /**
     * Retrieve the shop name for a specific vendor
     *
     * @param int $vendor_id The vendor Id.
     *
     * @return string
     */
    public static function get_vendor_shop_name( $vendor_id ) {

        $vendor_id = self::get_vendor_id( $vendor_id );
        $name      = $vendor_id ? get_user_meta( $vendor_id, 'pv_shop_name', true ) : false;
        $vendor    = get_userdata( $vendor_id );
        $shop_name = ( ! $name && $vendor ) ? $vendor->user_login : $name;

        return $shop_name;
    }


    /**
     * Check if is pending vendor.
     *
     * @param int $user_id The user Id.
     *
     * @return bool
     */
    public static function is_pending( $user_id ) {

        $user  = get_userdata( $user_id );
        $roles = $user->roles;

        return in_array( 'pending_vendor', $roles, true );
    }

    /**
     * Is this a vendor product.
     *
     * @param string $role The vendor's role.
     * @return boolean
     */
    public static function is_vendor_product( $role ) {
        return ( 'vendor' === $role ) ? true : false;
    }

    /**
     * Is this the vendors shop archive page or a single vendor product?
     *
     * @return boolean
     * @since   2.1.3
     * @version 2.1.3
     */
    public static function is_vendor_page() {

        global $post;

        $vendor_shop = urldecode( get_query_var( 'vendor_shop' ) );
        $vendor_id   = self::get_vendor_id( $vendor_shop );

        if ( ! $vendor_id && is_a( $post, 'WC_Product' ) ) {
            if ( self::is_vendor( $post->post_author ) ) {
                $vendor_id = $post->post_author;
            }
        }

        return $vendor_id ? true : false;
    }

    /**
     * Is this a vendor single product page
     *
     * @param int $vendor_id The vendor ID.
     * @return boolean
     */
    public static function is_vendor_product_page( $vendor_id ) {

        $vendor_product = self::is_vendor_product( wcv_get_user_role( $vendor_id ) );

        return $vendor_product ? true : false;
    }

    /**
     * Get vendor sold by display name
     *
     * @param int $vendor_id The user ID.
     *
     * @return string
     */
    public static function get_vendor_sold_by( $vendor_id ) {

        $vendor_display_name = get_option( 'wcvendors_display_shop_display_name' );
        $vendor              = get_userdata( $vendor_id );

        switch ( $vendor_display_name ) {
            case 'display_name':
                $display_name = $vendor->display_name;
                break;
            case 'user_login':
                $display_name = $vendor->user_login;
                break;
            case 'user_email':
                $display_name = $vendor->user_email;
                break;
            default:
                $display_name = self::get_vendor_shop_name( $vendor_id );
                break;
        }

        return $display_name;
    }

    /**
     * Split order into vendor orders (when applicable) after checkout
     *
     * @since 1.0.0
     *
     * @param int|WC_Order $order The order ID, or WC_Order object.
     *
     * @return void
     */
    public static function create_child_orders( $order ) {

        if ( ! is_a( $order, 'WC_Order' ) ) {
            $order = wc_get_order( $order );
        }

        if ( ! is_a( $order, 'WC_Order' ) ) {
            return;
        }

        $items        = $order->get_items();
        $vendor_items = array();

        foreach ( $items as $item_id => $item ) {
            if ( isset( $item['product_id'] ) && 0 !== $item['product_id'] ) {
                // check if product is from vendor.
                $product_author = get_post_field( 'post_author', $item['product_id'] );
                if ( self::is_vendor( $product_author ) ) {
                    $vendor_items[ $product_author ][ $item_id ] = array(
                        'item_id'      => $item_id,
                        'qty'          => $item['qty'],
                        'total'        => $item['line_total'],
                        'subtotal'     => $item['line_subtotal'],
                        'tax'          => $item['line_tax'],
                        'subtotal_tax' => $item['line_subtotal_tax'],
                        'tax_data'     => maybe_unserialize( $item['line_tax_data'] ),
                        'commission'   => WCV_Commission::calculate_commission(
                            $item['line_subtotal'],
                            $item['product_id'],
                            $order,
                            $item['qty'],
                            $item
                        ),
                    );
                }
            }
        }

        $vendor_ids   = array();
        $child_orders = array();

        $status       = $order->get_status();
        $order_status = 'wc-' !== substr( $status, 0, 3 ) ? 'wc-' . $status : $status;

        foreach ( $vendor_items as $vendor_id => $items ) {
            if ( ! empty( $items ) ) {

                $vendor_order = self::create_vendor_order(
                    array(
                        'parent_id'   => $order->get_id(),
                        'vendor_id'   => $vendor_id,
                        'customer_id' => $order->get_customer_id(),
                        'line_items'  => $items,
                        'status'      => $order_status,
                        'date'        => $order->get_date_created(),
                    )
                );

                $vendor_ids[]   = $vendor_id;
                $child_orders[] = $vendor_order->get_id();
            }
        }

        if ( ! empty( $vendor_ids ) ) {
            $order->add_meta_data( 'wcv_vendor_ids', $vendor_ids, true );
        }

        if ( ! empty( $child_orders ) ) {
            $order->add_meta_data( 'wcv_sub_orders', $child_orders, true );
        }

        if ( ! empty( $vendor_ids ) || ! empty( $child_orders ) ) {
            $order->save();
        }

        if ( wcv_cot_enabled() ) {
            add_post_meta( $order->get_id(), 'wcv_vendor_ids', $vendor_ids, true );
            add_post_meta( $order->get_id(), 'wcv_sub_orders', $child_orders, true );
        }
    }

    /**
     * Create child orders when order is created via the API
     *
     * @param WC_Order $order The order object.
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public static function create_child_orders_api( $order ) {
        self::create_child_orders( $order );
    }

    /**
     * Update sub order status when parent order status changes.
     *
     * @param int    $order_id   The parent order ID.
     * @param string $old_status The old order status.
     * @param string $new_status The new order status.
     * @return void
     * @version 2.4.9.2
     * @since   2.4.9.2 - Refactor remove_action
     * @since   2.4.8   - Added.
     */
    public function update_sub_order_status( $order_id, $old_status, $new_status ) {
        $order = wc_get_order( $order_id );

        if ( ! $order || 'shop_order' !== $order->get_type() ) {
            return;
        }

        $sub_order_ids = array_filter( (array) $order->get_meta( 'wcv_sub_orders' ) );

        if ( empty( $sub_order_ids ) ) {
            return;
        }

        remove_action( 'woocommerce_order_status_changed', array( $this, 'update_sub_order_status' ), 10 );

        foreach ( $sub_order_ids as $sub_order_id ) {
            if ( ! $sub_order_id ) {
                continue;
            }
            $sub_order = wcv_get_order( $sub_order_id );
            if ( $sub_order->get_status() === $old_status || $sub_order->get_status() !== $new_status ) {

                $sub_order->set_status( $new_status );
                $sub_order->save();
            }
        }

        add_action( 'woocommerce_order_status_changed', array( $this, 'update_sub_order_status' ), 10, 3 );
    }

    /**
     * Create a new vendor order programmatically
     *
     * Returns a new vendor_order object on success which can then be used to add additional data.
     *
     * @version 2.4.8
     * @since   2.4.8 - Added HPOS Compatibility.
     *
     * @param array $args The details of the order to be created.
     *
     * @return WC_Order_Vendor|WP_Error
     */
    public static function create_vendor_order( $args = array() ) {
        global $wpdb;

        $default_args = array(
            'vendor_id'       => null,
            'parent_id'       => 0,
            'vendor_order_id' => 0,
            'line_items'      => array(),
            'date'            => current_time( 'mysql', 0 ),
            'status'          => 'wc-completed',
            'customer_id'     => get_current_user_id(),
        );

        $args = wp_parse_args( $args, $default_args );

        $args = apply_filters_deprecated(
            'woocommerce_new_vendor_order_data',
            array( $args ),
            '2.4.8',
            'wcvendors_new_vendor_order_data'
        );

        $updating = $args['vendor_order_id'] > 0;

        $vendor_order = null;

        if ( $updating ) {
            $vendor_order = new WC_Order_Vendor( $args['vendor_order_id'], $args['vendor_id'] );
            $vendor_order->set_date_modified( $args['date'] );
        } else {
            $vendor_order = new WC_Order_Vendor();
            $vendor_order->set_date_created( $args['date'] );
        }

        $vendor_order->set_customer_id( $args['customer_id'] );
        $vendor_order->set_vendor_id( $args['vendor_id'] );
        $vendor_order->set_status( $args['status'] );
        $vendor_order->set_parent_id( $args['parent_id'] );

        $commission = 0;

        foreach ( $args['line_items'] as $item ) {
            $commission += $item['commission'];
        }

        $order_item_product_ids = self::get_order_item_product_ids( $args['line_items'] );
        if ( ! empty( $order_item_product_ids ) ) {
            $vendor_order->add_meta_data( 'wcv_product_ids', $order_item_product_ids, true );
        }

        $vendor_order->add_meta_data( 'wcv_vendor_id', $args['vendor_id'], true );
        $vendor_order->add_meta_data( 'wcv_commission', $commission, true );

        $vendor_order_id = $vendor_order->save();

        if ( is_wp_error( $vendor_order_id ) ) {
            return $vendor_order_id;
        }

        // Manually update the post author since the COT does not have post_author.
        if ( ! wcv_cot_enabled() ) {
            $wpdb->update(
                $wpdb->posts,
                array(
                    'post_author' => $args['customer_id'],
                    'post_status' => $args['status'],
                ),
                array( 'ID' => $vendor_order_id )
            );
        }

        if ( wcv_cot_enabled() ) {
            add_post_meta( $vendor_order_id, 'wcv_vendor_id', $args['vendor_id'], true );
            add_post_meta( $vendor_order_id, 'wcv_commission', $commission, true );
        }

        if ( ! $updating ) {

            // Get vendor order object.
            if ( is_null( $vendor_order ) ) {
                $vendor_order = wcv_get_order( $vendor_order_id );
            }

            // Order currency is the same used for the parent order.
            $order = $vendor_order->get_parent_order();
            $vendor_order->set_currency( $order->get_currency() );
            $vendor_order->set_customer_id( $order->get_customer_id() );
            $vendor_order->set_billing_email( $order->get_billing_email() );
            $vendor_order->set_payment_method( $order->get_payment_method() );
            $vendor_order->set_payment_method_title( $order->get_payment_method_title() );
            $vendor_order->set_transaction_id( $order->get_transaction_id() );

            if ( count( $args['line_items'] ) > 0 ) {
                $order_items = $order->get_items( array( 'line_item', 'fee', 'shipping' ) );

                foreach ( $args['line_items'] as $vendor_order_item_id => $vendor_order_item ) {

                    if ( isset( $order_items[ $vendor_order_item_id ] ) ) {
                        if ( empty( $vendor_order_item['qty'] ) && empty( $vendor_order_item['total'] ) && empty( $vendor_order_item['tax'] ) ) {
                            continue;
                        }

                        // Prevents errors when the order has no taxes.
                        if ( ! isset( $vendor_order_item['tax'] ) ) {
                            $vendor_order_item['tax'] = array();
                        }

                        switch ( $order_items[ $vendor_order_item_id ]['type'] ) {
                            case 'line_item':
                                $line_item_args = array(
                                    'totals' => array(
                                        'subtotal'     => $vendor_order_item['subtotal'],
                                        'total'        => $vendor_order_item['total'],
                                        'subtotal_tax' => $vendor_order_item['subtotal_tax'],
                                        'tax'          => $vendor_order_item['tax'],
                                        'tax_data'     => $vendor_order_item['tax_data'],
                                    ),
                                );
                                $line_item      = new WC_Order_Item_Product( $vendor_order_item_id );
                                $new_item_id    = $vendor_order->add_product( $line_item->get_product(), isset( $vendor_order_item['qty'] ) ? $vendor_order_item['qty'] : 0, $line_item_args );
                                wc_add_order_item_meta( $new_item_id, '_vendor_order_item_id', $vendor_order_item_id );
                                wc_add_order_item_meta( $new_item_id, '_vendor_commission', $vendor_order_item['commission'] );
                                break;
                            case 'shipping':
                                $shipping = new WC_Shipping_Rate();
                                $shipping->set_label( $order_items[ $vendor_order_item_id ]['name'] );
                                $shipping->set_id( $order_items[ $vendor_order_item_id ]['method_id'] );
                                $shipping->set_cost( $vendor_order_item['total'] );
                                $shipping->set_taxes( $vendor_order_item['tax'] );

                                $new_item_id = $vendor_order->add_shipping( $shipping );
                                wc_add_order_item_meta( $new_item_id, '_vendor_order_item_id', $vendor_order_item_id );
                                break;
                            case 'fee':
                                $fee            = new stdClass();
                                $fee->name      = $order_items[ $vendor_order_item_id ]['name'];
                                $fee->tax_class = $order_items[ $vendor_order_item_id ]['tax_class'];
                                $fee->taxable   = '0' !== $fee->tax_class;
                                $fee->amount    = $vendor_order_item['total'];
                                $fee->tax       = array_sum( $vendor_order_item['tax'] );
                                $fee->tax_data  = $vendor_order_item['tax'];

                                $new_item_id = $vendor_order->add_fee( $fee );
                                wc_add_order_item_meta( $new_item_id, '_vendor_order_item_id', $vendor_order_item_id );
                                break;
                        }
                    }
                }
                $vendor_order->update_taxes();
            }

            $vendor_order->calculate_totals( false );

            $vendor_order->save();

            do_action( 'woocommerce_vendor_order_created', $vendor_order_id, $args );
        }

        // Clear transients.
        wc_delete_shop_order_transients( $args['parent_id'] );

        return is_null( $vendor_order ) ? new WC_Order_Vendor( $vendor_order_id ) : $vendor_order;
    }

    /**
     * Get product IDs for the order items.
     *
     * @param WC_Order_Item_Product[] $line_items The list of line items.
     * @return int[]
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public static function get_order_item_product_ids( $line_items ) {
        $vendor_product_ids = array();
        foreach ( $line_items as $item ) {
            $product = new WC_Order_Item_Product( $item['item_id'] );

            $product_id = $product->get_variation_id() > 0 ? $product->get_variation_id() : $product->get_product_id();

            $vendor_product_ids[] = $product_id;
        }

        return $vendor_product_ids;
    }

    /**
     * Get vendor orders
     *
     * @param array $args The order query args.
     * @return WC_Order_Vendor[]
     */
    public static function get_vendor_orders( $args ) {
        return wcv_get_vendor_orders( $args );
    }

    /**
     * Find the parent product id if the variation has been deleted
     *
     * @since   1.9.13
     * @version 2.4.8
     *
     * @param int $order_id   The order ID.
     * @param int $product_id The product ID.
     * @access public
     */
    public static function find_parent_id_from_order( $order_id, $product_id ) {
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            return $product_id;
        }

        $order_items = $order->get_items();

        foreach ( $order_items as $order_item ) {
            $product = new WC_Order_Item_Product( $order_item->get_id() );

            $item_product_id   = $product->get_product_id();
            $item_variation_id = $product->get_variation_id();

            if ( $item_variation_id === $product_id ) {
                return $item_product_id;
            }
        }

        return $product_id;
    }

    /**
     * Delete sub orders when an order is deleted from posts table.
     *
     * @param int $post_id The post ID of the order.
     * @return void
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public function handle_delete_post( $post_id ) {
        if ( get_post_type( $post_id ) !== 'shop_order' ) {
            return;
        }

        $this->delete_child_orders( $post_id );
    }

    /**
     * Trash sub orders when an order is trashed from the posts table.
     *
     * @param int $post_id The post ID of the order.
     * @return void
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public function handle_trash_post( $post_id ) {
        if ( 'shop_order' !== get_post_type( $post_id ) ) {
            return;
        }

        $this->trash_sub_orders( $post_id );
    }

    /**
     * Untrash sub orders when an order is untrashed from the posts table.
     *
     * @param int    $post_id The post ID of the order.
     * @param string $previous_status The status of the order before it was trashed.
     * @return void
     * @version 2.4.8
     * @since   2.4.8 -
     */
    public function handle_untrash_post( $post_id, $previous_status ) {
        if ( 'shop_order' !== get_post_type( $post_id ) ) {
            return;
        }

        $this->untrash_sub_orders( $post_id, $previous_status );
    }

    /**
     * Delete child orders if the parent order is deleted
     *
     * @param int $order_id The post ID.
     *
     * @since 2.1.13
     * @access public
     */
    public function delete_child_orders( $order_id ) {
        $child_orders = $this->get_sub_orders( $order_id );

        if ( empty( $child_orders ) ) {
            return;
        }

        foreach ( $child_orders as $child_order ) {
            $child_order->delete( true );
        }
    }

    /**
     * Trash sub orders when the parent order is trashed.
     *
     * @param int $order_id The id of the order that was trashed.
     * @return void
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public function trash_sub_orders( $order_id ) {
        $sub_orders = $this->get_sub_orders( $order_id );

        if ( empty( $sub_orders ) ) {
            return;
        }

        foreach ( $sub_orders as $sub_order ) {
            $sub_order->delete();
        }
    }

    /**
     * Untrash sub orders when the parent order is untrashed.
     *
     * @param int    $order_id The id of the order that was untrashed.
     * @param string $previous_status The order status before it was trashed.
     * @return void
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public function untrash_sub_orders( $order_id, $previous_status ) {
        $sub_orders = $this->get_sub_orders( $order_id );

        if ( empty( $sub_orders ) ) {
            return;
        }

        foreach ( $sub_orders as $sub_order ) {
            $sub_order->set_status( $previous_status );
            $sub_order->save();
        }
    }

    /**
     * Get sub orders for an order given it's id.
     *
     * @param int $order_id The ID of the order.
     * @return WC_Order[]
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public function get_sub_orders( $order_id ) {
        $statuses   = array_keys( wc_get_order_statuses() );
        $statuses[] = 'trash';

        $child_order_query = array(
            'parent' => $order_id,
            'type'   => 'shop_order_vendor',
            'status' => $statuses,
        );

        $child_orders = wc_get_orders( $child_order_query );

        return apply_filters(
            'wcvendors_get_child_orders',
            $child_orders,
            $order_id
        );
    }

    /**
     * Handle custom order query vars.
     *
     * @param array $query - Args for WP_Query.
     * @param array $query_vars - Query vars from WC_Order_Query.
     * @return array modified $query
     */
    public function cpt_order_query_vars( $query, $query_vars ) {
        return array_merge( $query, $this->order_query_args( $query_vars ) );
    }

    /**
     * Add order query args.
     *
     * @param array $query_args The query args.
     * @return array
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public function order_query_args( $query_args ) {
        if ( isset( $query_args['vendor_id'] ) || isset( $query_args['wcv_vendor_id'] ) ) {
            $vendor_ids = isset( $query_args['vendor_id'] ) ? $query_args['vendor_id'] : $query_args['wcv_vendor_id'];

            $query_args['meta_query']['relation'] = 'OR';

            $query_args['meta_query'][] = array(
                'key'     => '_vendor_id',
                'compare' => is_array( $vendor_ids ) ? 'IN' : '=',
                'value'   => $vendor_ids,
            );

            $query_args['meta_query'][] = array(
				'key'     => 'wcv_vendor_id',
				'compare' => is_array( $vendor_ids ) ? 'IN' : '=',
				'value'   => $vendor_ids,
			);
            unset( $query_args['vendor_id'] );
            unset( $query_args['wcv_vendor_id'] );
            unset( $query_args['wcv_vendor_ids'] );
        }

        if ( isset( $query_args['product_id'] ) || isset( $query_args['wcv_product_ids'] ) ) {
            $product_ids                = isset( $query_args['product_id'] ) ? $query_args['product_id'] : $query_args['wcv_product_ids'];
            $query_args['meta_query'][] = array(
				'key'     => 'wcv_product_ids',
				'compare' => 'LIKE',
				'value'   => is_array( $product_ids ) ? maybe_serialize( $product_ids ) : $product_ids,
			);
            unset( $query_args['product_id'] );
            unset( $query_args['wcv_product_ids'] );
        }

        if ( isset( $query_args['wcv_sub_orders'] ) || isset( $query_args['sub_order'] ) ) {
            $sub_orders = isset( $query_args['wcv_sub_orders'] ) ? $query_args['wcv_sub_orders'] : $query_args['sub_order'];

            $query_args['meta_query'][] = array(
                'key'     => 'wcv_sub_orders',
                'compare' => 'LIKE',
                'value'   => is_array( $sub_orders ) ? maybe_serialize( $sub_orders ) : $sub_orders,
            );
            unset( $query_args['wcv_sub_orders'] );
            unset( $query_args['sub_order'] );
        }

        return $query_args;
    }

    /**
     * Moved to vendors class
     *
     * @version 2.2.0
     * @since 2.0.9
     */
    public static function add_rewrite_rules() {

        $permalink = untrailingslashit( get_option( 'wcvendors_vendor_shop_permalink' ) );

        // Remove beginning slash.
        if ( '/' === substr( $permalink, 0, 1 ) ) {
            $permalink = substr( $permalink, 1, strlen( $permalink ) );
        }

        add_rewrite_tag( '%vendor_shop%', '([^&]+)' );

        add_rewrite_rule( $permalink . '/page/([0-9]+)', 'index.php?pagename=' . $permalink . '&paged=$matches[1]', 'top' );
        add_rewrite_rule( $permalink . '/([^/]*)/page/([0-9]+)', 'index.php?post_type=product&vendor_shop=$matches[1]&paged=$matches[2]', 'top' );
        add_rewrite_rule( $permalink . '/([^/]*)', 'index.php?post_type=product&vendor_shop=$matches[1]', 'top' );
    }

    /**
     * Register custom user meta to be returned via API
     *
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function register_rest_fields() {
        register_rest_field(
            'user',
            'wcvendors_meta',
            array(
                'get_callback'    => array( $this, 'get_vendor_shop_details' ),
                'update_callback' => null,
                'schema'          => null,
            )
        );

        register_rest_field(
            'user',
            'is_vendor',
            array(
                'get_callback'    => function ( $user ) {
                    return self::is_vendor( $user['id'] );
                },
                'update_callback' => null,
                'schema'          => null,
            )
        );
    }

    /**
     * Get vendor seller info via API request
     *
     * Adds wcvendor_meta to user object on API request
     *
     * @param array $user       The retrieved WordPress user.
     *
     * @since   2.4.8
     * @version 2.4.8
     *
     * @return array
     */
    public function get_vendor_shop_details( $user ) {
        $vendor_id   = $user['id'];
        $shop_slug   = $vendor_id ? get_user_meta( $vendor_id, 'pv_shop_slug', true ) : '';
        $description = $vendor_id ? get_user_meta( $vendor_id, 'pv_shop_description', true ) : '';
        $seller_info = $vendor_id ? get_user_meta( $vendor_id, 'pv_seller_info', true ) : '';

        return array(
            'shop_name'   => self::get_vendor_shop_name( $vendor_id ),
            'shop_slug'   => $shop_slug,
            'description' => $description,
            'seller_info' => $seller_info,
        );
    }
}
