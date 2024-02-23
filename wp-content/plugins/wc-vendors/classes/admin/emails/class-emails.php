<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * WC Vendors Emails Class.
 *
 * @author Matt Gates <http://mgates.me>
 * @package     WC_Vendors
 */
class WCV_Emails {

    /**
     * Construct an instance of the class
     */
    public function __construct() {

        add_filter( 'woocommerce_email_classes', array( $this, 'email_classes' ) );
        add_filter( 'woocommerce_order_actions', array( $this, 'order_actions' ) );
        add_action( 'woocommerce_order_action_send_vendor_new_order', array( $this, 'order_actions_save' ) );

        // Deprecated.
        add_action( 'set_user_role', array( $this, 'application_status_email' ), 10, 2 );
        add_action( 'transition_post_status', array( $this, 'trigger_new_product' ), 10, 3 );

        // Low stock
        // These fatal error in WC3.3.3 @todo fix !
        add_filter( 'woocommerce_email_recipient_low_stock', array( $this, 'vendor_low_stock_email' ), 10, 2 );
        add_filter( 'woocommerce_email_recipient_no_stock', array( $this, 'vendor_no_stock_email' ), 10, 2 );
        add_filter( 'woocommerce_email_recipient_backorder', array( $this, 'vendor_backorder_stock_email' ), 10, 2 );

        // New emails
        // Triggers.
        add_action( 'wcvendors_vendor_ship', array( $this, 'vendor_shipped' ), 10, 3 );
        add_action( 'wcvendors_email_order_details', array( $this, 'vendor_order_details' ), 10, 8 );
        add_action( 'wcvendors_email_customer_details', array( $this, 'vendor_customer_details' ), 10, 3 );
        add_filter( 'woocommerce_order_needs_shipping_address', array( $this, 'add_customer_shipping_address' ), 10, 1 );

        // Trigger application emails as required.
        add_action( 'add_user_role', array( $this, 'vendor_application' ), 10, 2 );
        add_action( 'wcvendors_deny_vendor', array( $this, 'deny_application' ) );

        // WooCommerce Product Enquiry Compatibility.
        add_filter( 'product_enquiry_send_to', array( $this, 'product_enquiry_compatibility' ), 10, 2 );
    }

    /**
     * Deprecated
     *
     * @param string  $from Sender email address.
     * @param string  $to   Receiver email address.
     * @param WP_Post $post The post object.
     * @return void
     * @version 1.0.0
     * @since   1.0.0
     */
    public function trigger_new_product( $from, $to, $post ) {
        global $woocommerce;

        if ( $from !== $to && 'pending' === $post->post_status && WCV_Vendors::is_vendor( $post->post_author ) && 'product' === $post->post_type ) {
            $mails = $woocommerce->mailer()->get_emails();
            if ( ! empty( $mails ) ) {
                $mails['WC_Email_Notify_Admin']->trigger( $post->post_id, $post );
            }
        }
    }

    /**
     * Application status email.
     *
	 * Trigger the application status email
	 *
     * @deprecated
     *
     * @param int    $user_id User ID. The user ID.
     * @param string $role    The current role.   Role.
     */
    public function application_status_email( $user_id, $role ) {

        global $woocommerce;

        // phpcs:disable
        if ( ! empty( $_POST['apply_for_vendor'] ) || ( ! empty( $_GET['action'] ) && ( $_GET['action'] == 'approve_vendor' || $_GET['action'] == 'deny_vendor' ) ) ) {

			if ( 'pending_vendor' === $role ) {
				$status = __( 'pending', 'wc-vendors' );
			} elseif ( 'vendor' === $role ) {
				$status = __( 'approved', 'wc-vendors' );
			} elseif ( ! empty( $_GET['action'] ) && 'deny_vendor' === $_GET['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$status = __( 'denied', 'wc-vendors' );
			}

            $mails = $woocommerce->mailer()->get_emails();

            if ( isset( $status ) && ! empty( $mails ) ) {
                $mails['WC_Email_Approve_Vendor']->trigger( $user_id, $status );
            }
        }
        // phpcs:enable
    }

    /**
     * Load WooCommerce email classes.
     *
     * @param   array $emails Current list of WooCommerce emails.
     * @version 2.0.13
     * @since   1.0.0
     *
     * @return array
     */
    public function email_classes( $emails ) {

        require_once WCV_PLUGIN_DIR . 'classes/admin/emails/class-wc-notify-admin.php';
        require_once WCV_PLUGIN_DIR . 'classes/admin/emails/class-wc-approve-vendor.php';
        require_once WCV_PLUGIN_DIR . 'classes/admin/emails/class-wc-notify-shipped.php';

        // Emails to depreciate.
        $emails['WC_Email_Approve_Vendor'] = new WC_Email_Approve_Vendor();
        $emails['WC_Email_Notify_Admin']   = new WC_Email_Notify_Admin();
        $emails['WC_Email_Notify_Shipped'] = new WC_Email_Notify_Shipped();

        // New emails introduced in @since 2.0.0.
        require_once WCV_PLUGIN_DIR . 'classes/admin/emails/class-wcv-customer-notify-shipped.php';
        require_once WCV_PLUGIN_DIR . 'classes/admin/emails/class-wcv-admin-notify-shipped.php';
        require_once WCV_PLUGIN_DIR . 'classes/admin/emails/class-wcv-admin-notify-product.php';
        require_once WCV_PLUGIN_DIR . 'classes/admin/emails/class-wcv-admin-notify-application.php';
        require_once WCV_PLUGIN_DIR . 'classes/admin/emails/class-wcv-admin-notify-approved.php';
        require_once WCV_PLUGIN_DIR . 'classes/admin/emails/class-wcv-vendor-notify-application.php';
        require_once WCV_PLUGIN_DIR . 'classes/admin/emails/class-wcv-vendor-notify-approved.php';
        require_once WCV_PLUGIN_DIR . 'classes/admin/emails/class-wcv-vendor-notify-denied.php';
        require_once WCV_PLUGIN_DIR . 'classes/admin/emails/class-wcv-vendor-notify-order.php';
        require_once WCV_PLUGIN_DIR . 'classes/admin/emails/class-wcv-vendor-notify-cancelled-order.php';

        $emails['WCVendors_Customer_Notify_Shipped']       = new WCVendors_Customer_Notify_Shipped();
        $emails['WCVendors_Admin_Notify_Shipped']          = new WCVendors_Admin_Notify_Shipped();
        $emails['WCVendors_Admin_Notify_Product']          = new WCVendors_Admin_Notify_Product();
        $emails['WCVendors_Admin_Notify_Application']      = new WCVendors_Admin_Notify_Application();
        $emails['WCVendors_Admin_Notify_Approved']         = new WCVendors_Admin_Notify_Approved();
        $emails['WCVendors_Vendor_Notify_Application']     = new WCVendors_Vendor_Notify_Application();
        $emails['WCVendors_Vendor_Notify_Approved']        = new WCVendors_Vendor_Notify_Approved();
        $emails['WCVendors_Vendor_Notify_Denied']          = new WCVendors_Vendor_Notify_Denied();
        $emails['WCVendors_Vendor_Notify_Order']           = new WCVendors_Vendor_Notify_Order();
        $emails['WCVendors_Vendor_Notify_Cancelled_Order'] = new WCVendors_Vendor_Notify_Cancelled_Order();

        return $emails;
    } // email_classes

    /**
     * Add the vendor email to the low stock emails.
     *
     * @param string     $emails  The email addresses to to send to.
     * @param WC_Product $product The product.
     * @return string
     */
    public function vendor_stock_email( $emails, $product ) {

        if ( ! is_a( $product, 'WC_Product' ) ) {
            return $emails;
        }

        $post = get_post( $product->get_id() );

        if ( WCV_Vendors::is_vendor( $post->post_author ) ) {
            $vendor_data  = get_userdata( $post->post_author );
            $vendor_email = trim( $vendor_data->user_email );
            $emails      .= ',' . $vendor_email;
        }

        return $emails;
    }

    /**
     *  Handle low stock emails for vendors
     *
     * @param string     $emails  The email addresses to send to.
     * @param WC_Product $product The product object.
     *
     * @since   2.1.10
     * @version 2.1.0
     *
     * @return string
     */
    public function vendor_low_stock_email( $emails, $product ) {
        if ( 'no' === get_option( 'wcvendors_notify_low_stock', 'yes' ) ) {
            return $emails;
        }
        return $this->vendor_stock_email( $emails, $product );
    }

    /**
     *  Handle no stock emails for vendors
     *
     * @param string     $emails  The email addresses to send to.
     * @param WC_Product $product The product object.
     *
     * @since 2.1.10
     * @version 2.1.0
     */
    public function vendor_no_stock_email( $emails, $product ) {
        if ( 'no' === get_option( 'wcvendors_notify_low_stock', 'yes' ) ) {
            return $emails;
        }
        return $this->vendor_stock_email( $emails, $product );
    }

    /**
     *  Handle backorder stock emails for vendors
     *
     * @param string     $emails  The email addresses to send to.
     * @param WC_Product $product The product object.
     *
     * @since 2.1.10
     * @version 2.1.0
     */
    public function vendor_backorder_stock_email( $emails, $product ) {
        if ( 'no' === get_option( 'wcvendors_notify_backorder_stock', 'yes' ) ) {
            return;
        }
        $this->vendor_stock_email( $emails, $product );
    }


    /**
     * Filter hook for order actions meta box
     *
     * @param array $order_actions The order actions.
     */
    public function order_actions( $order_actions ) {
        $order_actions['send_vendor_new_order'] = sprintf(
            // translators: %s: name used to refer to vendor.
            __( 'Resend %s new order notification', 'wc-vendors' ),
            wcv_get_vendor_name( true, false )
        );

        return $order_actions;
    }

    /**
     * Action hook : trigger the notify vendor email
     *
     * @param WC_Order $order The order email.
     */
    public function order_actions_save( $order ) {
        if ( ! is_a( $order, 'WC_Order' ) ) {
            $order = wc_get_order( $order );
        }
        WC()->mailer()->emails['WCVendors_Vendor_Notify_Order']->trigger( $order->get_id(), $order );
    }

    /**
     * Trigger the notify vendor shipped emails
     *
     * @version 2.2.2
     * @since   2.0.0
     *
     * @param int      $order_id The order Id.
     * @param int      $user_id  The user Id.
     * @param WC_Order $order The order object.
     */
    public function vendor_shipped( $order_id, $user_id, $order ) {
        if ( ! is_a( $order, 'WC_Order' ) ) {
            $order = wc_get_order( $order_id );
        }
        // Notify the admin.
        WC()->mailer()->emails['WCVendors_Admin_Notify_Shipped']->trigger( $order->get_id(), $user_id, $order );

        // Notify the customer.
        if ( apply_filters( 'wcvendors_vendor_shipped_customer_notification', true ) ) {
            WC()->mailer()->emails['WCVendors_Customer_Notify_Shipped']->trigger( $order->get_id(), $user_id, $order );
        }
    }

    /**
     * Trigger the vendor application emails
     *
     * @since 2.0.0
     * @version 2.1.7
     *
     * @param int    $user_id The user ID.
     * @param string $role    The user role.
     */
    public function vendor_application( $user_id, $role = '' ) {

        /**
         * If the role is not given, set it according to the vendor approval option in admin
         */
        if ( '' === $role ) {
            $manual = wc_string_to_bool( get_option( 'wcvendors_vendor_approve_registration', 'no' ) );
            $role   = apply_filters( 'wcvendors_pending_role', ( $manual ? 'pending_vendor' : 'vendor' ) );
        }

        if ( 'pending_vendor' === $role ) {
            $status = __( 'pending', 'wc-vendors' );
            WC()->mailer()->emails['WCVendors_Vendor_Notify_Application']->trigger( $user_id, $status );
            WC()->mailer()->emails['WCVendors_Admin_Notify_Application']->trigger( $user_id, $status );
        } elseif ( 'vendor' === $role ) {
            $status = __( 'approved', 'wc-vendors' );
            WC()->mailer()->emails['WCVendors_Vendor_Notify_Approved']->trigger( $user_id, $status );
            WC()->mailer()->emails['WCVendors_Admin_Notify_Approved']->trigger( $user_id, $status );
        }
    }

    /**
     * Trigger the deny application email
     *
     * @param WP_User $user The user object.
     *
     * @since 2.1.8
     */
    public function deny_application( $user ) {
        $user_id = $user->ID;
        WC()->mailer()->emails['WCVendors_Vendor_Notify_Denied']->trigger( $user_id );
    }

    /**
     * Show the order details table filtered for each vendor
     *
     * @param WC_Order $order           The order.
     * @param array    $vendor_items    The vendor's items.
     * @param mixed    $totals_display  Totals display.
     * @param int      $vendor_id       The vendor ID.
     * @param boolean  $sent_to_vendor  Whether the email should be sent to the vendor.
     * @param boolean  $sent_to_admin   Whether the email should be sent to admin.
     * @param boolean  $plain_text      Whether the email is plain text.
     * @param string   $email           The email.
     */
    public function vendor_order_details( $order, $vendor_items, $totals_display, $vendor_id, $sent_to_vendor = false, $sent_to_admin = false, $plain_text = false, $email = '' ) {

        if ( $plain_text ) {

            wc_get_template(
                'emails/plain/vendor-order-details.php',
                array(
                    'order'          => $order,
                    'vendor_id'      => $vendor_id,
                    'vendor_items'   => $vendor_items,
                    'sent_to_admin'  => $sent_to_admin,
                    'sent_to_vendor' => $sent_to_vendor,
                    'totals_display' => $totals_display,
                    'plain_text'     => $plain_text,
                    'email'          => $email,
                ),
                'woocommerce',
                WCV_TEMPLATE_BASE
            );

        } else {

            wc_get_template(
                'emails/vendor-order-details.php',
                array(
                    'order'          => $order,
                    'vendor_id'      => $vendor_id,
                    'vendor_items'   => $vendor_items,
                    'sent_to_admin'  => $sent_to_admin,
                    'sent_to_vendor' => $sent_to_vendor,
                    'totals_display' => $totals_display,
                    'plain_text'     => $plain_text,
                    'email'          => $email,
                ),
                'woocommerce',
                WCV_TEMPLATE_BASE
            );
        }
    }

    /**
     * Show the customer address details based on the capabilities for the vendor
     *
     * @param WC_Order $order         The order.
     * @param boolean  $sent_to_admin Whether the email should be sent to admin.
     * @param boolean  $plain_text    Whether the email is sent as plaintext.
     * @return void
     */
    public function vendor_customer_details( $order, $sent_to_admin, $plain_text ) {

        $show_customer_billing_name  = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_name', 'no' ) );
        $show_customer_shipping_name = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_shipping_name', 'no' ) );
        $show_customer_email         = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_email', 'no' ) );
        $show_customer_phone         = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_phone', 'no' ) );
        $show_billing_address        = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_billing', 'no' ) );
        $show_shipping_address       = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_shipping', 'no' ) );
        $customer_billing_name       = $show_customer_billing_name ? $order->get_formatted_billing_full_name() : '';
        $customer_shipping_name      = $show_customer_shipping_name ? $order->get_formatted_shipping_full_name() : '';

        if ( $plain_text ) {
            wc_get_template(
                'emails/plain/vendor-order-addresses.php',
                array(
                    'show_customer_email'         => $show_customer_email,
                    'show_customer_phone'         => $show_customer_phone,
                    'show_billing_address'        => $show_billing_address,
                    'show_shipping_address'       => $show_shipping_address,
                    'show_customer_billing_name'  => $show_customer_billing_name,
                    'customer_billing_name'       => $customer_billing_name,
                    'show_customer_shipping_name' => $show_customer_billing_name,
                    'customer_shipping_name'      => $customer_shipping_name,
                    'order'                       => $order,
                    'sent_to_admin'               => $sent_to_admin,
                ),
                'woocommerce',
                WCV_TEMPLATE_BASE
            );
        } else {
            wc_get_template(
                'emails/vendor-order-addresses.php',
                array(
                    'show_customer_email'         => $show_customer_email,
                    'show_customer_phone'         => $show_customer_phone,
                    'show_billing_address'        => $show_billing_address,
                    'show_shipping_address'       => $show_shipping_address,
                    'show_customer_billing_name'  => $show_customer_billing_name,
                    'customer_billing_name'       => $customer_billing_name,
                    'show_customer_shipping_name' => $show_customer_billing_name,
                    'customer_shipping_name'      => $customer_shipping_name,
                    'order'                       => $order,
                    'sent_to_admin'               => $sent_to_admin,
                ),
                'woocommerce',
                WCV_TEMPLATE_BASE
            );
        }
    }

    /**
     * WooCommerce Product Enquiry hook - Send email to vendor instead of admin
     *
     * @param string $send_to    The email address to send to.
     * @param int    $product_id The product ID.
     * @return string
     */
    public function product_enquiry_compatibility( $send_to, $product_id ) {
        $author_id = get_post( $product_id )->post_author;
        if ( WCV_Vendors::is_vendor( $author_id ) ) {
            $send_to = get_userdata( $author_id )->user_email;
        }

        return $send_to;
    }

    /**
     * Is order needs shipping address
     *
     * @param boolean $needs_shipping_address Whether the order needs a shipping address.
     * @return boolean
     *
     * @since 2.4.9
     * @version 2.4.9
     */
    public function add_customer_shipping_address( $needs_shipping_address ) {

        $show_shipping_address       = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_shipping', 'no' ) );
        $show_customer_shipping_name = wc_string_to_bool( get_option( 'wcvendors_capability_order_customer_shipping_name', 'no' ) );

        if ( $show_shipping_address || $show_customer_shipping_name ) {
            if ( is_admin() ) {
                $needs_shipping_address = true;
            }
        }

        return $needs_shipping_address;
    }
}
