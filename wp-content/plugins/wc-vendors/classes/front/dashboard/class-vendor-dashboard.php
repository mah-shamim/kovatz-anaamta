<?php

use const PluginPackage\VERSION;

/**
 * Vendor Dashboard Class.
 *
 * @version 1.0.0
 * @since   1.0.0
 */
class WCV_Vendor_Dashboard {

    /**
     * __construct()
     */
    public function __construct() {
        if ( is_admin() ) {
            return;
        }

        add_shortcode( 'wcv_shop_settings', array( $this, 'display_vendor_settings' ) );
        add_shortcode( 'wcv_vendor_dashboard', array( $this, 'display_vendor_products' ) );
        add_shortcode( 'wcv_vendor_dashboard_nav', array( $this, 'display_dashboard_nav' ) );

        add_action( 'template_redirect', array( $this, 'check_access' ) );
        add_action( 'template_redirect', array( $this, 'save_vendor_settings' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'template_redirect', array( $this, 'lock_inactive_vendor_from_dashboard' ) );
    }

    /**
     * Enqueue styles and scripts.
     */
    public function enqueue_scripts() {

        global $post;
        if ( ! is_a( $post, 'WP_Post' ) ) {
            return;
        }
        $has_required_shortcodes = has_shortcode( $post->post_content, 'wcv_vendor_dashboard' )
            || has_shortcode( $post->post_content, 'wcv_orders' )
            || has_shortcode( $post->post_content, 'wcv_vendor_dashboard_nav' )
            || has_shortcode( $post->post_content, 'wcv_vendorslist' );

        if ( $has_required_shortcodes ) {
            wp_enqueue_style( 'dashicons' );
            wp_enqueue_style(
                'wcv_frontend_style',
                WCV_ASSETS_URL . 'css/wcv-frontend.css',
                array(),
                WCV_VERSION
            );
        }

        wp_enqueue_style(
            'wcv_vendor_store_style',
            WCV_ASSETS_URL . 'css/wcv-store.css',
            array(),
            WCV_VERSION
        );

        wp_enqueue_script(
            'wcv_vendor_store_script',
            WCV_ASSETS_URL . 'js/wcv-store-setting.js',
            array( 'jquery' ),
            WCV_VERSION,
            true
        );
    }

    /**
     * Save the vendor shop settings from the dashboard
     *
     * @version 2.4.3 - Added PayPal masspay
     */
    public function save_vendor_settings() {
        $user_id = get_current_user_id();

        if ( ! empty( $_GET['wc_pv_mark_shipped'] ) ) {

            if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'wc_pv_mark_shipped' ) ) {
                wc_add_notice( __( 'Failed to mark order as shipped. Please refresh the page and try again.', 'wc-vendors' ), 'error' );
                return;
            }

            $order_id = sanitize_text_field( wp_unslash( $_GET['wc_pv_mark_shipped'] ) );
            $order    = wc_get_order( $order_id );

            $marked_shipped = wcv_mark_vendor_shipped( $order, $user_id );

            if ( $marked_shipped ) {
                wc_add_notice( __( 'Order marked shipped.', 'wc-vendors' ) );
            } else {
                wc_add_notice( __( 'Failed to mark this order as shipped. Please contact administrator for assistance.', 'wc-vendors' ), 'error' );
            }

            return;
        }

        if ( isset( $_POST['update_tracking'] ) ) {

            if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'track-shipment' ) ) {
                wc_add_notice( __( 'Tracking information not updated. Please refresh the page and try again.', 'wc-vendors' ), 'error' );
                return;
            }

            $order_id   = (int) $_POST['order_id'];
            $product_id = (int) $_POST['product_id'];

            $tracking_provider        = wc_clean( sanitize_text_field( wp_unslash( $_POST['tracking_provider'] ) ) );
            $custom_tracking_provider = wc_clean( sanitize_text_field( wp_unslash( $_POST['custom_tracking_provider_name'] ) ) );
            $custom_tracking_link     = wc_clean( sanitize_text_field( wp_unslash( $_POST['custom_tracking_url'] ) ) );
            $tracking_number          = wc_clean( sanitize_text_field( wp_unslash( $_POST['tracking_number'] ) ) );
            $date_shipped             = wc_clean( strtotime( sanitize_text_field( wp_unslash( $_POST['date_shipped'] ) ) ) );

            $order         = wc_get_order( $order_id );
            $products      = $order->get_items();
            $order_item_id = 0;

            $order_tracking_details = (array) $order->get_meta( '_wcv_tracking_details', true );
            $shippers               = array_filter( (array) $order->get_meta( 'wc_pv_shipped', true ) );

            foreach ( $products as $key => $value ) {
                if ( $value['product_id'] === $product_id || $value['variation_id'] === $product_id ) {
                    $order_item_id = $key;
                    break;
                }
            }

            if ( $order_item_id ) {
                wc_delete_order_item_meta( $order_item_id, __( 'Tracking number', 'wc-vendors' ) );
                wc_add_order_item_meta( $order_item_id, __( 'Tracking number', 'wc-vendors' ), $tracking_number );
            }

            // Add order tracking information.
            $vendor_tracking_data = array(
                '_tracking_provider'        => $tracking_provider,
                '_custom_tracking_provider' => $custom_tracking_provider,
                '_tracking_number'          => $tracking_number,
                '_custom_tracking_link'     => $custom_tracking_link,
                '_date_shipped'             => $date_shipped,
            );

            $order_tracking_details[ $user_id ] = $vendor_tracking_data;

            $order->update_meta_data( '_wcv_tracking_details', $order_tracking_details, true );
            $order->save();

            // If the vendor has not shipped already.
            if ( ! in_array( $user_id, $shippers, true ) ) {
                wcv_mark_vendor_shipped( $order, $user_id );
            }

            $message = __( 'Success. Your tracking number has been updated.', 'wc-vendors' );
            wc_add_notice( $message, 'success' );
        }

        if ( empty( $_POST['vendor_application_submit'] ) ) {
            return false;
        }

        if ( isset( $_POST['wc-product-vendor-nonce'] ) ) {
            $commission_payout_method = isset( $_POST['wcv_commission_payout_method'] ) ? sanitize_text_field( $_POST['wcv_commission_payout_method'] ) : '';
            if ( ! in_array( $commission_payout_method, array( 'paypal', 'bank' ), true ) ) {
                $commission_payout_method = 'paypal';
            }

            if ( ! wp_verify_nonce( $_POST['wc-product-vendor-nonce'], 'save-shop-settings' ) ) {
                wc_add_notice( __( 'Failed to update your settings. Please refresh the page and try again.', 'wc-vendors' ), 'error' );
                return false;
            }

            if ( isset( $_POST['pv_paypal'] ) && '' !== $_POST['pv_paypal'] ) {
                if ( ! is_email( $_POST['pv_paypal'] ) ) {
                    wc_add_notice( __( 'Your PayPal address is not a valid email address.', 'wc-vendors' ), 'error' );
                } else {
                    update_user_meta( $user_id, 'pv_paypal', $_POST['pv_paypal'] );
                }
            } else {
                update_user_meta( $user_id, 'pv_paypal', '' );
            }

            if ( ! empty( $_POST['pv_shop_name'] ) ) {
                $users = get_users(
                    array(
                        'meta_key'   => 'pv_shop_slug',
                        'meta_value' => sanitize_title( $_POST['pv_shop_name'] ),
                    )
                );
                if ( ! empty( $users ) && $users[0]->ID !== $user_id ) {
                    wc_add_notice( __( 'That shop name is already taken. Your shop name must be unique.', 'wc-vendors' ), 'error' );
                } else {
                    update_user_meta( $user_id, 'pv_shop_name', $_POST['pv_shop_name'] );
                    update_user_meta( $user_id, 'pv_shop_slug', sanitize_title( $_POST['pv_shop_name'] ) );
                }
            }

            if ( isset( $_POST['pv_shop_description'] ) ) {
                update_user_meta( $user_id, 'pv_shop_description', $_POST['pv_shop_description'] );
            } else {
                update_user_meta( $user_id, 'pv_shop_description', '' );
            }

            if ( isset( $_POST['pv_seller_info'] ) ) {
                update_user_meta( $user_id, 'pv_seller_info', $_POST['pv_seller_info'] );
            }

            // Commission payout method.
            if ( $commission_payout_method ) {
                update_user_meta( $user_id, 'wcv_commission_payout_method', sanitize_text_field( $_POST['wcv_commission_payout_method'] ) );
            } else {
                delete_user_meta( $user_id, 'wcv_commission_payout_method' );
            }

            if ( 'paypal' === $commission_payout_method ) {
                // PayPal Masspay wallet.
                if ( isset( $_POST['wcv_paypal_masspay_wallet'] ) ) {
                    update_user_meta( $user_id, 'wcv_paypal_masspay_wallet', sanitize_text_field( $_POST['wcv_paypal_masspay_wallet'] ) );
                } else {
                    delete_user_meta( $user_id, 'wcv_paypal_masspay_wallet' );
                }

                // PayPal Masspay venmo.
                if ( isset( $_POST['wcv_paypal_masspay_venmo_id'] ) ) {
                    update_user_meta( $user_id, 'wcv_paypal_masspay_venmo_id', sanitize_text_field( $_POST['wcv_paypal_masspay_venmo_id'] ) );
                } else {
                    delete_user_meta( $user_id, 'wcv_paypal_masspay_venmo_id' );
                }
            }

            if ( 'bank' === $commission_payout_method ) {
                // Bank details.
                if ( isset( $_POST['wcv_bank_account_name'] ) ) {
                    update_user_meta( $user_id, 'wcv_bank_account_name', $_POST['wcv_bank_account_name'] );
                } else {
                    delete_user_meta( $user_id, 'wcv_bank_account_name' );
                }

                if ( isset( $_POST['wcv_bank_account_number'] ) ) {
                    update_user_meta( $user_id, 'wcv_bank_account_number', $_POST['wcv_bank_account_number'] );
                } else {
                    delete_user_meta( $user_id, 'wcv_bank_account_number' );
                }

                if ( isset( $_POST['wcv_bank_name'] ) ) {
                    update_user_meta( $user_id, 'wcv_bank_name', $_POST['wcv_bank_name'] );
                } else {
                    delete_user_meta( $user_id, 'wcv_bank_name' );
                }

                if ( isset( $_POST['wcv_bank_routing_number'] ) ) {
                    update_user_meta( $user_id, 'wcv_bank_routing_number', $_POST['wcv_bank_routing_number'] );
                } else {
                    delete_user_meta( $user_id, 'wcv_bank_routing_number' );
                }

                if ( isset( $_POST['wcv_bank_iban'] ) ) {
                    update_user_meta( $user_id, 'wcv_bank_iban', $_POST['wcv_bank_iban'] );
                } else {
                    delete_user_meta( $user_id, 'wcv_bank_iban' );
                }

                if ( isset( $_POST['wcv_bank_bic_swift'] ) ) {
                    update_user_meta( $user_id, 'wcv_bank_bic_swift', $_POST['wcv_bank_bic_swift'] );
                } else {
                    delete_user_meta( $user_id, 'wcv_bank_bic_swift' );
                }
            }

            do_action( 'wcvendors_shop_settings_saved', $user_id );

            if ( ! wc_notice_count() ) {
                wc_add_notice( __( 'Settings saved.', 'wc-vendors' ), 'success' );
            }
        }
    }

    /**
     * Check if vendor has access to the dashboard
     *
     * @return void
     */
    public function check_access() {
        $vendor_dashboard_page = get_option( 'wcvendors_vendor_dashboard_page_id' );
        $shop_settings_page    = get_option( 'wcvendors_shop_settings_page_id' );

        if ( $vendor_dashboard_page && is_page( $vendor_dashboard_page ) || $shop_settings_page && is_page( $shop_settings_page ) ) {
            if ( ! is_user_logged_in() ) {
                wp_safe_redirect( get_permalink( wc_get_page_id( 'myaccount' ) ), 303 );
                exit;
            }
        }
    }

    /**
     * [wcv_vendor_dashboard] shortcode
     *
     * @param array $atts The shortcode attributes.
     *
     * @return string
     */
    public function display_vendor_products( $atts ) {
        global $wpdb;
        ob_start();

        global $start_date, $end_date;

        // WC 3.6+ - Cart and other frontend functions are not included for REST requests.
        include_once WC()->plugin_path() . '/includes/wc-notice-functions.php';

        $dates      = WCV_Queries::orders_within_range();
        $start_date = $dates['after'];
        $end_date   = $dates['before'];

        $can_view_orders = wc_string_to_bool( get_option( 'wcvendors_capability_orders_enabled', 'no' ) );

        if ( ! $this->can_view_vendor_page() ) {
            wc_get_template(
                'denied.php',
                array(),
                'wc-vendors/dashboard/',
                WCV_PLUGIN_DIR . 'templates/dashboard/'
            );
            return ob_get_clean();
        }

        $atts = shortcode_atts(
            array(
                'user_id'    => get_current_user_id(),
                'datepicker' => true,
            ),
            $atts
        );

        $user_id              = $atts['user_id'];
        $datepicker           = $atts['datepicker'];
        $show_reversed_orders = wcv_is_show_reversed_order();
        $vendor_products      = WCV_Queries::get_commission_products( $user_id );
        $product_ids          = array();
        foreach ( $vendor_products as $vendor_product ) {
            $product_ids[] = $vendor_product->get_id();
        }

        $orders_sql = "SELECT COUNT(order_id) FROM {$wpdb->prefix}pv_commission WHERE vendor_id = %d AND time >= %s AND time <= %s";

        if ( ! $show_reversed_orders ) {
            $orders_sql .= " AND status != 'reversed'";
        }

        $total_orders = $wpdb->get_var(
            $wpdb->prepare(
                $orders_sql, // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                $user_id,
                gmdate( 'Y-m-d H:i:s', $start_date ),
                gmdate( 'Y-m-d H:i:s', $end_date )
            )
        );

        $per_page       = apply_filters( 'wcvendors_dashboard_orders_per_page', get_option( 'wcvendors_free_orders_per_page', 10 ) );
        $paged          = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
        $vendor_summary = $this->format_product_details( $vendor_products );
        $order_summary  = WCV_Queries::get_orders_for_products(
            $product_ids,
            $user_id,
            array(
				'limit' => $per_page,
				'paged' => $paged,
            )
        );

        $shipping_providers = new WCV_Shipping_Providers();

        $providers      = $shipping_providers->get_providers();
        $provider_array = $shipping_providers->get_provider_url_list();

        $can_view_address = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_shipping', 'yes' ) );

        do_action( 'wcvendors_before_dashboard' );

        if ( function_exists( 'wc_print_notices' ) ) {
            wc_print_notices();
        }

        wc_get_template(
            'navigation.php',
            array(
                'items' => $this->get_nav_items(),
            ),
            'wc-vendors/dashboard/',
            WCV_PLUGIN_DIR . 'templates/dashboard/'
        );

        if ( wc_string_to_bool( get_option( 'wcvendors_capability_frontend_reports', 'yes' ) ) ) {

            wc_get_template(
                'reports.php',
                array(
                    'start_date'      => $start_date,
                    'end_date'        => $end_date,
                    'vendor_products' => $vendor_products,
                    'vendor_summary'  => $vendor_summary,
                    'datepicker'      => $datepicker,
                    'can_view_orders' => $can_view_orders,
                ),
                'wc-vendors/dashboard/',
                WCV_PLUGIN_DIR . 'templates/dashboard/'
            );
        }

        wc_get_template(
            'orders.php',
            array(
                'start_date'       => $start_date,
                'end_date'         => $end_date,
                'vendor_products'  => $vendor_products,
                'order_summary'    => $order_summary,
                'datepicker'       => $datepicker,
                'providers'        => $providers,
                'provider_array'   => $provider_array,
                'can_view_orders'  => $can_view_orders,
                'can_view_address' => $can_view_address,
                'show_reversed'    => $show_reversed_orders,
                'total_pages'      => ceil( $total_orders / $per_page ),
                'paged'            => $paged,
            ),
            'wc-vendors/dashboard/',
            WCV_PLUGIN_DIR . 'templates/dashboard/'
        );
        do_action( 'wcvendors_after_dashboard' );

        wc_enqueue_js( WCV_Vendor_dashboard::wc_st_js( $provider_array ) );

        return ob_get_clean();
    }

    /**
     * Filterable dashboard navigation items.
     *
     * @return array
     */
    public function get_nav_items() {

        $items = array(
            'shop_page'     => array(
                'url'   => urldecode( WCV_Vendors::get_vendor_shop_page( wp_get_current_user()->user_login ) ),
                'label' => esc_html__( 'View Your Store', 'wc-vendors' ),
            ),
            'settings_page' => array(
                'url'   => get_permalink( get_option( 'wcvendors_shop_settings_page_id' ) ),
                'label' => esc_html__( 'Store Settings', 'wc-vendors' ),
            ),
        );

        $can_submit = wc_string_to_bool( get_option( 'wcvendors_capability_products_enabled', 'no' ) );

        if ( $can_submit ) {
            $items['submit_link'] = array(
                'url'    => admin_url( 'post-new.php?post_type=product' ),
                'label'  => esc_html__( 'Add New Product', 'wc-vendors' ),
                'target' => '_top',
            );
            $items['edit_link']   = array(
                'url'    => admin_url( 'edit.php?post_type=product' ),
                'label'  => esc_html__( 'Edit Products', 'wc-vendors' ),
                'target' => '_top',
            );
        }

        $items = apply_filters_deprecated(
            'wcv_dashboard_nav_items',
            array( $items ),
            '2.3.0',
            'wcvendors_dashboard_nav_items'
        );
        return apply_filters( 'wcvendors_dashboard_nav_items', $items );
    }

    /**
     * [wcv_vendor_dashboard_nav] shortcode.
     *
     * @return string
     */
    public function display_dashboard_nav() {

        ob_start();

        wc_get_template(
            'navigation.php',
            array(
                'items' => $this->get_nav_items(),
            ),
            'wc-vendors/dashboard/',
            WCV_PLUGIN_DIR . 'templates/dashboard/'
        );

        return ob_get_clean();
    }

    /**
     * [pv_recent_vendor_sales] shortcode
     *
     * @param array $atts The shortcode attributes.
     *
     * @return string
     */
    public function display_vendor_settings( $atts ) {
        global $woocommerce;

        ob_start();

        if ( ! $this->can_view_vendor_page() ) {
            return ob_get_clean();
        }

        $atts = shortcode_atts(
            array(
                'user_id'          => get_current_user_id(),
                'paypal_address'   => true,
                'shop_description' => true,
            ),
            $atts
        );

        $user_id          = $atts['user_id'];
        $paypal_address   = $atts['paypal_address'];
        $shop_description = $atts['shop_description'];

        $description = get_user_meta( $user_id, 'pv_shop_description', true );
        $seller_info = get_user_meta( $user_id, 'pv_seller_info', true );
        $has_html    = get_user_meta( $user_id, 'pv_shop_html_enabled', true );
        $shop_page   = WCV_Vendors::get_vendor_shop_page( wp_get_current_user()->user_login );
        $global_html = wc_string_to_bool( get_option( 'wcvendors_display_shop_description_html', 'no' ) );

        wc_get_template(
            'settings.php',
            array(
                'description'      => $description,
                'global_html'      => $global_html,
                'has_html'         => $has_html,
                'paypal_address'   => $paypal_address,
                'seller_info'      => $seller_info,
                'shop_description' => $shop_description,
                'shop_page'        => $shop_page,
                'user_id'          => $user_id,
            ),
            'wc-vendors/dashboard/settings/',
            WCV_PLUGIN_DIR . 'templates/dashboard/settings/'
        );

        return ob_get_clean();
    }

    /**
     * Can the user view this page.
     *
     * @version 2.2.1
     *
     * @return bool
     */
    public static function can_view_vendor_page() {
        if ( ! is_user_logged_in() || ! WCV_Vendors::is_vendor( get_current_user_id() ) ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Format products for easier displaying
     *
     * @param array $products The list of products.
     *
     * @return bool|array
     */
    public function format_product_details( $products ) {
        if ( empty( $products ) ) {
            return false;
        }

        $orders_page_id     = get_option( 'wcvendors_product_orders_page_id' );
        $orders_page        = get_permalink( $orders_page_id );
        $default_commission = get_option( 'wcvendors_vendor_commission_rate' );
        $total_cost         = 0;
        $total_qty          = $total_cost;
        $data               = array(
            'products'   => array(),
            'total_qty'  => '',
            'total_cost' => '',
        );

        foreach ( $products as $product ) {
            $ids[] = $product->get_id();
        }

        $orders = WCV_Queries::sum_orders_for_products( $ids, array( 'vendor_id' => get_current_user_id() ) );

        if ( $orders ) {
            foreach ( $orders as $order_item ) {
            if ( $order_item->qty < 1 ) {
                continue;
            }

            $commission_rate = WCV_Commission::get_commission_rate( $order_item->product_id );
            $_product        = wc_get_product( $order_item->product_id );
            $parent_id       = $_product->get_parent_id();
            $id              = ! empty( $parent_id ) ? $parent_id : $order_item->product_id;

            $data['products'][ $id ] = array(
                'id'              => $id,
                'title'           => $_product->get_title(),
                'qty'             => ! empty( $data['products'][ $id ] ) ? $data['products'][ $id ]['qty'] + $order_item->qty : $order_item->qty,
                'cost'            => ! empty( $data['products'][ $id ] ) ? $data['products'][ $id ]['cost'] + $order_item->line_total : $order_item->line_total,
                'view_orders_url' => esc_url( add_query_arg( 'orders_for_product', $id, $orders_page ) ),
                'commission_rate' => $commission_rate,
            );

            $total_qty  += $order_item->qty;
            $total_cost += $order_item->line_total;

            }
        }

        $data['total_qty']  = $total_qty;
        $data['total_cost'] = $total_cost;

        // Sort by product title.
        if ( ! empty( $data['products'] ) ) {
            usort( $data['products'], array( $this, 'sort_by_title' ) );
        }

        return $data;
    }

    /**
     * Sort an array by 'title'
     *
     * @param array $a Item a.
     * @param array $b Item b.
     *
     * @return int
     */
    private function sort_by_title( array $a, array $b ) {
        return strcasecmp( $a['title'], $b['title'] );
    }

    /**
     *  Load the javascript for the WC Shipment Tracking form
     *
     * @param array $provider_array The array of providers.
     */
    public static function wc_st_js( $provider_array ) {
        $js = "
        jQuery(function() {

            var providers = jQuery.parseJSON( '" . wp_json_encode( $provider_array ) . "' );

            jQuery('#tracking_number').prop('readonly',true);
            jQuery('#date_shipped').prop('readonly',true);

            function updatelink( tracking, provider ) {

            var postcode = '32';
            postcode = encodeURIComponent(postcode);

            link = providers[provider];
            link = link.replace('%251%24s', tracking);
            link = link.replace('%252%24s', postcode);
            link = decodeURIComponent(link);
            return link;
            }

            jQuery('.tracking_provider, #tracking_number').unbind().change(function(){

            var form = jQuery(this).parent().parent().attr('id');

            var tracking = jQuery('#' + form + ' input#tracking_number').val();
            var provider = jQuery('#' + form + ' #tracking_provider').val();

            if ( providers[ provider ]) {
                link = updatelink(tracking, provider);
                jQuery('#' + form + ' #tracking_number').prop('readonly',false);
                jQuery('#' + form + ' #date_shipped').prop('readonly',false);
                jQuery('#' + form + ' .custom_tracking_url_field, #' + form + ' .custom_tracking_provider_name_field').hide();
            } else {
                jQuery('#' + form + ' .custom_tracking_url_field, #' + form + ' .custom_tracking_provider_name_field').show();
                link = jQuery('#' + form + ' input#custom_tracking_link').val();
            }

            if (link) {
                jQuery('#' + form + ' p.preview_tracking_link a').attr('href', link);
                jQuery('#' + form + ' p.preview_tracking_link').show();
            } else {
                jQuery('#' + form + ' p.preview_tracking_link').hide();
            }

            });

            jQuery('#custom_tracking_provider_name').unbind().click(function(){

            var form = jQuery(this).parent().parent().attr('id');

            jQuery('#' + form + ' #tracking_number').prop('readonly',false);
            jQuery('#' + form + ' #date_shipped').prop('readonly',false);

            });

        });
        ";

        return $js;
    }

    /**
     * Add custom wcvendors pro css classes
     *
     * @since    1.0.0
     * @access   public
     *
     * @param array $classes - body css classes.
     *
     * @return array $classes - body css classes.
     */
    public function body_class( $classes ) {

        $dashboard_page = get_option( 'wcvendors_vendor_dashboard_page_id' );
        $orders_page    = get_option( 'wcvendors_product_orders_page_id' );
        $shop_settings  = get_option( 'wcvendors_shop_settings_page_id' );
        $terms_page     = get_option( 'wcvendors_vendor_terms_page_id' );

        if ( is_page( $dashboard_page ) ) {
            $classes[] = 'wcvendors wcv-vendor-dashboard-page';
        }

        if ( is_page( $orders_page ) ) {
            $classes[] = 'wcvendors wcv-orders-page';
        }

        if ( is_page( $shop_settings ) ) {
            $classes[] = 'wcvendors wcv-shop-settings-page';
        }

        if ( is_page( $terms_page ) ) {
            $classes[] = 'wcvendors wcv-terms-page';
        }

        return $classes;
    }

    /**
     * Lock inactive vendors from accessing the dashboard
     *
     * @since 2.4.8
     * @version 2.4.8
     */
	public function lock_inactive_vendor_from_dashboard() {

        if ( ! is_user_logged_in() ) {
            return;
        }

		$current_user = wp_get_current_user();
        $user_id      = $current_user->ID;
        $user_roles   = $current_user->roles;

        if ( ! in_array( 'vendor', $user_roles, true ) ) {
            return;
        }

		$page_id                = get_queried_object_id();
		$pro_dashboard_page_id  = (array) get_option( 'wcvendors_dashboard_page_id', array() );
		$pro_dashboard_page_id  = array_map( 'intval', $pro_dashboard_page_id );
		$free_dashboard_page_id = (int) get_option( 'wcvendors_vendor_dashboard_page_id' );

		if ( in_array( $page_id, $pro_dashboard_page_id, true ) || $page_id === $free_dashboard_page_id ) {
			$vendor_status = get_user_meta( $user_id, '_wcv_vendor_status', true );
			if ( 'active' !== $vendor_status && in_array( 'vendor', $user_roles, true ) ) {
				wp_safe_redirect( get_permalink( wc_get_page_id( 'myaccount' ) ) );
				wc_add_notice( __( 'Your store is currently inactive. Please contact support if you feel this is a mistake.', 'wc-vendors' ), 'notice' );
				exit;
			}
		}
	}
}
