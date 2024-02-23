<?php

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Utilities\OrderUtil;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WCV_Dependencies' ) ) {
    require_once 'class-dependencies.php';
}

/**
 * WC Detection
 * */
if ( ! function_exists( 'wcv_is_woocommerce_activated' ) ) {
    /**
     * Check if WooCommerce is activated
     */
    function wcv_is_woocommerce_activated() {

        return WCV_Dependencies::woocommerce_active_check();
    }
}

/*
*
*  Get User Role
*/
if ( ! function_exists( 'wcv_get_user_role' ) ) {
    /**
     * Get user roole from user id
     *
     * @param int $user_id User ID.
     */
    function wcv_get_user_role( $user_id ) {

        global $wp_roles;
        $user  = new WP_User( $user_id );
        $roles = $user->roles;
        $role  = array_shift( $roles );

        return isset( $wp_roles->role_names[ $role ] ) ? $role : false;
    }
}


/**
 * This function gets the vendor name used throughout the interface on the front and backend
 *
 * @param boolean $singluar  Singluar or not.
 * @param boolean $upper_case Upper case or not.
 */
function wcv_get_vendor_name( $singluar = true, $upper_case = true ) {

    $vendor_singular = get_option( 'wcvendors_vendor_singular', __( 'Vendor', 'wc-vendors' ) );
    $vendor_plural   = get_option( 'wcvendors_vendor_plural', __( 'Vendors', 'wc-vendors' ) );

    $vendor_label = $singluar ?
        __( $vendor_singular, 'wc-vendors' ) : // phpcs:ignore
        __( $vendor_plural, 'wc-vendors' ); // phpcs:ignore
    $vendor_label = $upper_case ? ucfirst( $vendor_label ) : lcfirst( $vendor_label );

    $vendor_label = apply_filters_deprecated(
        'wcv_vendor_display_name',
        array( $vendor_label, $vendor_singular, $vendor_plural, $singluar, $upper_case ),
        '2.3.0',
        'wcvendors_vendor_display_name'
    );

    return apply_filters(
        'wcvendors_vendor_display_name',
        $vendor_label,
        $vendor_singular,
        $vendor_plural,
        $singluar,
        $upper_case
    );
}

/**
 * Output a single select page drop down.
 *
 * @param string $id    ID.
 * @param string $value Value.
 * @param string $css_class Class.
 * @param string $css   CSS.
 */
function wcv_single_select_page( $id, $value, $css_class = '', $css = '' ) {

    $dropdown_args = array(
        'name'             => $id,
        'id'               => $id,
        'sort_column'      => 'menu_order',
        'sort_order'       => 'ASC',
        'show_option_none' => ' ',
        'class'            => $css_class,
        'echo'             => false,
        'selected'         => $value,
    );

    $new_attributes  = ' data-placeholder="' . esc_attr__( 'Select a page&hellip;', 'wc-vendors' ) . '"';
    $new_attributes .= ' style="' . esc_attr( $css ) . '" class="' . $css_class . '" id="';

    echo wp_kses(
        str_replace(
            ' id=',
            $new_attributes,
            wp_dropdown_pages( $dropdown_args )
        ),
        wcv_allowed_html_tags()
    );
}

/**
 * Get the WC Vendors Screen ids.
 *
 * @return array
 */
function wcv_get_screen_ids() {

    return apply_filters(
        'wcv_get_screen_ids',
        array(
            'wc-vendors_page_wcv-settings',
            'wc-vendors_page_wcv-commissions',
            'wc-vendors_page_wcv-extensions',
        )
    );
}

/**
 * Filterable navigation items classes for Vendor Dashboard.
 *
 * @param string $item_id Navigation item ID.
 *
 * @return string
 */
function wcv_get_dashboard_nav_item_classes( $item_id ) {

    $classes = array( 'button' );

    $classes = apply_filters_deprecated( 'wcv_dashboard_nav_item_classes', array( $classes, $item_id ), '2.3.0', 'wcvendors_dashboard_nav_item_classes' );
    $classes = apply_filters( 'wcvendors_dashboard_nav_item_classes', $classes, $item_id );

    return implode( ' ', array_map( 'sanitize_html_class', $classes ) );
}

if ( ! function_exists( 'wcv_vendor_drop_down_options' ) ) {
    /**
     * Generate a drop down with the vendor name based on the Dsiplay name setting used in the admin
     *
     * @param array $users     Users.
     * @param int   $vendor_id Vendor ID.
     * @since 2.1.10
     * @return string
     */
    function wcv_vendor_drop_down_options( $users, $vendor_id ) {
        $output = '';
        foreach ( (array) $users as $user ) {
            $shop_name    = WCV_Vendors::get_vendor_sold_by( $user->ID );
            $display_name = empty( $shop_name ) ? $user->display_name : $shop_name;
            $select       = selected( $user->ID, $vendor_id, false );
            $output      .= "<option value='$user->ID' $select>$display_name</option>";
        }
        $output = apply_filters_deprecated( 'wcv_vendor_drop_down_options', array( $output ), '2.3.0', 'wcvendors_vendor_drop_down_options' );
        return apply_filters( 'wcvendors_vendor_drop_down_options', $output );
    }
}


/**
 * Set the primary role of the specified user to vendor while retaining all other roles after
 *
 * @param $user WP_User
 *
 * @since 2.1.10
 * @version 2.1.10
 */

if ( ! function_exists( 'wcv_set_primary_vendor_role' ) ) {
    /**
     * Set primary role to vendor.
     *
     * @param WP_User|int $user The ID of the user or the user object.
     * @param string      $role The role to set, default 'vendor'.
     * @return void
     * @version 2.4.7
     * @since   2.4.7 - Added default role and allow ID or WP_User object.
     */
    function wcv_set_primary_vendor_role( $user, $role = 'vendor' ) {
        if ( is_int( $user ) ) {
            $user = get_user_by( 'id', $user );
        }
        // Get existing roles.
        $existing_roles = $user->roles;
        // Remove all existing roles.
        foreach ( $existing_roles as $existing_role ) {
            $user->remove_role( $existing_role );
        }
        // Add default role/vendor first.
        $user->add_role( $role );
        unset( $existing_roles[ $role ] ); // Remove assigned role from existing roles. Avoid adding it to the end if it's already there.
        // Re-add all other roles.
        foreach ( $existing_roles as $existing_role ) {
            $user->add_role( $existing_role );
        }
    }
}

if ( ! function_exists( 'wcv_is_show_reversed_order' ) ) {

    /**
     * Check show reversed order
     *
     * @since 2.4.0
     * @return bool
     */
    function wcv_is_show_reversed_order() {

        return wc_string_to_bool( get_option( 'wcvendors_dashboard_orders_show_reversed_orders', 'no' ) );
    }
}


if ( ! function_exists( 'wcv_hpos_enabled' ) ) {
    /**
     * Check if WooCommerce Custom Orders Table is enabled.
     *
     * @return bool
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    function wcv_hpos_enabled() {
        return OrderUtil::custom_orders_table_usage_is_enabled();
    }
}

if ( ! function_exists( 'wcv_cot_enabled' ) ) {
    /**
     * Check if custom order tables option is enabled
     *
     * @return boolean
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    function wcv_cot_enabled() {
        $cot_enabled = get_option( CustomOrdersTableController::CUSTOM_ORDERS_TABLE_USAGE_ENABLED_OPTION );
        return wc_string_to_bool( $cot_enabled );
    }
}

if ( ! function_exists( 'wcv_get_order' ) ) {
    /**
     * Get vendor order
     *
     * @param int|WC_Order_Vendor $order Order ID.
     * @return WC_Order_Vendor
     * @version 2.4.8
     * @since   2.4.8
     */
    function wcv_get_order( $order ) {
        if ( is_a( $order, WC_Order_Vendor::ORDER_TYPE ) ) {
            return $order;
        }

        if ( is_a( $order, 'WC_Order' ) ) {
            return new WC_Order_Vendor( $order->get_id() );
        }

        return new WC_Order_Vendor( $order );
    }
}

if ( ! function_exists( 'wcv_allowed_html_tags' ) ) {
    /**
     * Allow specific HTML tags.
     *
     * To be used with wp_kses_post() or wp_kses() to allow additional HTML tags that are not allowed by default.
     *
     * @return array
     * @version 2.4.8
     * @since   2.4.8 -  Added
     */
    function wcv_allowed_html_tags() {
        $html_allowed_tags   = wp_kses_allowed_html( 'post' );
        $wcv_additional_tags = array(
			'a'      => array(
				'href'  => array(),
				'title' => array(),
			),
			'label'  => array(
				'for'   => array(),
                'class' => array(),
                'id'    => array(),
			),
			'div'    => array(
				'class' => array(),
				'id'    => array(),
				'style' => array(),
			),
			'br'     => array(),
			'strong' => array(),
			'small'  => array(),
			'select' => array(
				'class' => array(),
				'id'    => array(),
				'name'  => array(),
				'value' => array(),
				'style' => array(),
			),
			'option' => array(
				'value'    => array(),
				'selected' => array(),
			),
			'ul'     => array(
				'class' => array(),
				'id'    => array(),
				'style' => array(),
			),
            'ol'     => array(
				'class' => array(),
				'id'    => array(),
				'style' => array(),
			),
            'form'   => array(
                'action' => array(),
                'method' => array(),
                'class'  => array(),
                'id'     => array(),
            ),
            'input'  => array(
                'type'        => array(),
                'name'        => array(),
                'value'       => array(),
                'class'       => array(),
                'id'          => array(),
                'placeholder' => array(),
            ),
            'span'   => array(
                'class' => array(),
                'id'    => array(),
                'style' => array(),
            ),
		);
        return apply_filters(
            'wcvendors_allowed_html_tags',
            wp_parse_args( $wcv_additional_tags, $html_allowed_tags )
        );
    }
}

if ( ! function_exists( 'wcv_get_vendor_orders' ) ) {
    /**
     * Get a list of vendor orders.
     *
     * @param array $args The arguments to pass to WP_Query.
     * @return WC_Order_Vendor[]|array[WC_Order]
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    function wcv_get_vendor_orders( $args = array() ) {
        $args = wp_parse_args(
            $args,
            array(
                'type'  => WC_Order_Vendor::ORDER_TYPE,
                'limit' => -1,
            )
        );

        $args = apply_filters(
            'wcvendors_get_vendor_orders_args',
            $args
        );

        $orders = wc_get_orders( $args );

        $vendor_orders = array();

        $return_vendor_orders = is_string( $args['type'] ) && WC_Order_Vendor::ORDER_TYPE === $args['type'];

        if ( $return_vendor_orders ) {

            foreach ( $orders as $vendor_order ) {
                if ( 0 === $vendor_order->get_id() ) {
                    continue;
                }
                $vendor_orders[] = wcv_get_order( $vendor_order->get_id() );
            }
        }

        return apply_filters(
            'wcvendors_get_vendor_orders',
            $return_vendor_orders ? $vendor_orders : $orders,
            $args
        );
    }
}

if ( ! function_exists( 'wcvendors_schedule_display_notice' ) ) {
    /**
     * Schedule notice
     *
     * @param string $notice_key The notice key.
     * @param int    $days       The number of days to schedule the notice.
     *
     * @since 2.4.7
     */
    function wcvendors_schedule_display_notice( $notice_key, $days = 0 ) {
        if ( ! class_exists( 'ActionScheduler' ) ) {
            return;
        }

        $action_key = 'wcvendors_notice_scheduled_action';
        if ( as_next_scheduled_action( $action_key, array( $notice_key ), 'wcvendors' ) ) {
            as_unschedule_all_actions( $action_key, array( $notice_key ), 'wcvendors' );
        }
        $is_dismissed = get_option( 'wcvendors_dismissed_notice_' . $notice_key, 'no' );
        if ( 'yes' === $is_dismissed ) {
            return;
        }
        $notices = get_option( 'wcvendors_admin_notices', array() );
        if ( ! in_array( $notice_key, $notices, true ) ) {
            $notices[] = $notice_key;
            update_option( 'wcvendors_admin_notices', $notices );
        }
        as_schedule_single_action( time() + ( DAY_IN_SECONDS * $days ), $action_key, array( $notice_key ), 'wcvendors' );
        $is_display = 0 === $days ? 'yes' : 'no';
        add_option( 'wcvendors_display_notice_' . $notice_key, $is_display );
    }
}

if ( ! function_exists( 'wcv_switch_to_classic_cart_checkout' ) ) {

    /**
     * Switch to classic cart/checkout
     *
     * @since 2.4.8
     * @version 2.4.8
     * @return void
     */
    function wcv_switch_to_classic_cart_checkout() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'switch_cc_blocks' ) ) {
            return;
        }

        $cart_page     = get_post( wc_get_page_id( 'cart' ) );
        $checkout_page = get_post( wc_get_page_id( 'checkout' ) );

        if ( ! $cart_page || ! $checkout_page ) {
            return;
        }

        if ( ! has_block( 'woocommerce/cart', $cart_page ) && ! has_block( 'woocommerce/checkout', $checkout_page ) ) {
            wp_die();
        }

        if ( has_block( 'woocommerce/cart', $cart_page ) ) {
            wp_update_post(
                array(
                    'ID'           => $cart_page->ID,
                    'post_content' => '<!-- wp:woocommerce/classic-shortcode /-->',
                )
            );
        }

        if ( has_block( 'woocommerce/checkout', $checkout_page ) ) {
            wp_update_post(
                array(
                    'ID'           => $checkout_page->ID,
                    'post_content' => '<!-- wp:woocommerce/classic-shortcode {"shortcode":"checkout"} /-->	',
                )
            );
        }
        wp_send_json_success(
            array(
                'message' => __( 'Successfully switched to Classic Cart/Checkout.', 'wc-vendors' ),
            )
        );
        wp_die();
    }
}

if ( ! function_exists( 'wcv_get_product_total_sales_by_order_status' ) ) {
    /**
     * Get total sales for a product by order status.
     *
     * @param array $product_ids The product IDs.
     * @param array $order_statuses The order statuses to include.
     *
     * @version 2.4.9
     * @since   2.4.9 - Added.
     * @return array
     */
    function wcv_get_product_total_sales_by_order_status( $product_ids, $order_statuses ) {
        global $wpdb;
        $product_ids                 = array_unique( array_map( 'absint', $product_ids ) );
        $product_placeholders        = array_fill( 0, count( $product_ids ), '%d' );
        $product_placeholders        = implode( ',', $product_placeholders );
        $order_statuses_placeholders = array_fill( 0, count( $order_statuses ), '%s' );
        $order_statuses_placeholders = implode( ',', $order_statuses_placeholders );
        $sql                         = '';
        if ( ! wcv_hpos_enabled() ) {
            $sql     = "SELECT order_item_meta.meta_value as product_id, SUM( order_item_meta_2.meta_value ) as total_sales FROM {$wpdb->prefix}woocommerce_order_items as order_items
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id
            INNER JOIN {$wpdb->prefix}posts AS posts ON order_items.order_id = posts.ID
            WHERE order_items.order_item_type = 'line_item'
            AND order_item_meta.meta_key IN ( '_product_id', '_variation_id' )
            AND order_item_meta_2.meta_key = '_qty'
            AND posts.post_type = 'shop_order' AND posts.post_status IN ( $order_statuses_placeholders )
            AND order_item_meta.meta_value IN ( $product_placeholders )
            GROUP BY order_item_meta.meta_value";
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    $sql, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                    array_merge( $order_statuses, $product_ids )
                )
            );
        } else {
            $sql     = "SELECT order_item_meta.meta_value as product_id, SUM( order_item_meta_2.meta_value ) as total_sales FROM {$wpdb->prefix}woocommerce_order_items as order_items
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
            INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id
            INNER JOIN {$wpdb->prefix}wc_orders AS orders ON order_items.order_id = orders.id
            WHERE order_items.order_item_type = 'line_item'
            AND order_item_meta.meta_key IN ( '_product_id', '_variation_id' )
            AND order_item_meta_2.meta_key = '_qty'
            AND orders.parent_order_id = 0 AND orders.status IN ( $order_statuses_placeholders )
            AND order_item_meta.meta_value IN ( $product_placeholders )
            GROUP BY order_item_meta.meta_value";
            $results = $wpdb->get_results(
                $wpdb->prepare(
                    $sql, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                    array_merge( $order_statuses, $product_ids )
                )
            );
        }

        if ( ! $results ) {
            return array();
        }

        return $results;
    }
}

if ( ! function_exists( 'wcvendors_add_vendor_status_meta_key' ) ) {

    /**
     * Add _vendors_status as user meta for vendor and pending vendor users
     *
     * @since 2.4.8
     * @version 2.4.9.2
     * @return void
     */
    function wcvendors_add_vendor_status_meta_key() {

        $users = get_users(
            array(
                'role__in'   => array( 'vendor', 'pending_vendor' ),
                'number'     => apply_filters( 'wcvendors_sync_vendor_status_limit', 100 ),
                'fields'     => 'ID',
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key'     => '_wcv_vendor_status',
                        'compare' => 'NOT EXISTS',
                    ),
                    array(
                        'key'     => '_wcv_vendor_status',
                        'value'   => '',
                        'compare' => '=',
                    ),
                ),
            )
        );

        if ( empty( $users ) ) {
            return;
        }

        foreach ( $users as $user_id ) {
            if ( wc_user_has_role( $user_id, 'vendor' ) ) {
                update_user_meta( $user_id, '_wcv_vendor_status', 'active' );
            } else {
                update_user_meta( $user_id, '_wcv_vendor_status', 'inactive' );
            }
        }

        wp_schedule_single_event( time() + 5, 'wcvendors_sync_vendor_status' );
    }
}
