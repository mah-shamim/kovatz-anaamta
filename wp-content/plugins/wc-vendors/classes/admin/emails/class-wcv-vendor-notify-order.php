<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WCVendors_Vendor_Notify_Order' ) ) :

    /**
     * Notify Admin Shipped
     *
     * An email sent to the admin when the vendor marks the order shipped.
     *
     * @class       WCVendors_Vendor_Notify_Order
     * @version     2.0.0
     * @package     Classes/Admin/Emails
     * @author      WC Vendors
     * @extends     WC_Email
     */
    class WCVendors_Vendor_Notify_Order extends WC_Email {

        /**
         * List of vendors.
         *
         * @var array
         * @version 2.4.8
         * @since   2.4.8 - Added.
         */
        public $vendors = array();

        /**
         * The vendor Id.
         *
         * @var int
         * @version 2.4.8
         * @since   2.4.8 - Added.
         */
        public $vendor_id = 0;

        /**
         * The order items.
         *
         * @var array
         * @version 2.4.8
         * @since   2.4.8 - Added.
         */
        public $order_items;

        /**
         * Totals display.
         *
         * @var string
         * @version 2.4.8
         * @since   2.4.8
         */
        public $totals_display;

        /**
         * Constructor.
         */
        public function __construct() {

            $this->id    = 'vendor_notify_order';
            $this->title = sprintf(
                // translators: %s is the name used to refer to a vendor.
                __( '%s notify order', 'wc-vendors' ),
                wcv_get_vendor_name()
            );
            $this->description = sprintf(
                // translators: %s is the name used to refer to a vendor.
                __( 'Notification is sent to %s when an order is paid.', 'wc-vendors' ),
                wcv_get_vendor_name( true, false )
            );
            $this->template_html  = 'emails/vendor-notify-order.php';
            $this->template_plain = 'emails/plain/vendor-notify-order.php';
            $this->template_base  = dirname( dirname( dirname( __DIR__ ) ) ) . '/templates/';
            $this->placeholders   = array(
                '{site_title}'   => $this->get_blogname(),
                '{order_date}'   => '',
                '{order_number}' => '',
            );
            $this->recipient      = '';

            // Triggers.
            add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'trigger' ), 10, 2 );
            add_action( 'woocommerce_order_status_pending_to_completed_notification', array( $this, 'trigger' ), 10, 2 );
            add_action( 'woocommerce_order_status_failed_to_processing_notification', array( $this, 'trigger' ), 10, 2 );
            add_action( 'woocommerce_order_status_failed_to_completed_notification', array( $this, 'trigger' ), 10, 2 );
            add_action( 'woocommerce_order_status_on-hold_to_processing_notification', array( $this, 'trigger' ), 10, 2 );
            add_action( 'woocommerce_order_status_on-hold_to_completed_notification', array( $this, 'trigger' ), 10, 2 );

            // Call parent constructor.
            parent::__construct();
        }

        /**
         * Get email subject.
         *
         * @since  2.0.0
         * @return string
         */
        public function get_default_subject() {
            return __( '[{site_title}] New Customer Order ({order_number}) - {order_date}', 'wc-vendors' );
        }

        /**
         * Get email heading.
         *
         * @since  2.0.0
         * @return string
         */
        public function get_default_heading() {
            return __( 'New Customer Order', 'wc-vendors' );
        }

        /**
         * Trigger the sending of this email.
         *
         * @param int      $order_id The order ID.
         * @param WC_Order $order    Order object.
         */
        public function trigger( $order_id, $order = false ) {

            $this->setup_locale();

            if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
                $order = wc_get_order( $order_id );
            }

            if ( 0 !== $order->get_parent_id() ) {
                return;
            }

            $this->vendors = WCV_Vendors::get_vendors_from_order( $order );

            if ( is_a( $order, 'WC_Order' ) ) {
                $this->object                         = $order;
                $this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
                $this->placeholders['{order_number}'] = $this->object->get_order_number();
            }

            if ( $this->is_enabled() && ! empty( $this->vendors ) ) {

                foreach ( $this->vendors as $vendor_id => $vendor_details ) {

                    $this->recipient      = $vendor_details['vendor']->user_email;
                    $this->order_items    = $vendor_details['line_items'];
                    $this->vendor_id      = $vendor_id;
                    $this->totals_display = $this->get_option( 'totals_display' );

                    // Remove the customer name from the addresses.
                    add_filter( 'woocommerce_order_formatted_billing_address', array( $this, 'filter_customer_name' ) );
                    add_filter(
                        'woocommerce_order_formatted_shipping_address',
                        array(
                            $this,
                            'filter_customer_name',
                        )
                    );
                    $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
                    remove_filter(
                        'woocommerce_order_formatted_billing_address',
                        array(
                            $this,
                            'filter_customer_name',
                        )
                    );
                    remove_filter(
                        'woocommerce_order_formatted_shipping_address',
                        array(
                            $this,
                            'filter_customer_name',
                        )
                    );
                }
            }

            $this->restore_locale();
        }

        /**
         * Get content html.
         *
         * @access  public
         * @return string
         * @version 2.1.3
         */
        public function get_content_html() {

            return apply_filters(
                'wcv_vendor_notify_order_get_content_html',
                wc_get_template_html(
                    $this->template_html,
                    array(
                        'order'          => $this->object,
                        'vendor_id'      => $this->vendor_id,
                        'vendor_items'   => $this->order_items,
                        'email_heading'  => $this->get_heading(),
                        'totals_display' => $this->totals_display,
                        'sent_to_admin'  => false,
                        'sent_to_vendor' => true,
                        'plain_text'     => false,
                        'email'          => $this,
                    ),
                    'woocommerce',
                    $this->template_base
                ),
                $this
            );
        }

        /**
         * Get content plain.
         *
         * @access  public
         * @return string
         * @version 2.1.3
         */
        public function get_content_plain() {

            return apply_filters(
                'wcv_vendor_notify_order_get_content_plain',
                wc_get_template_html(
                    $this->template_plain,
                    array(
                        'order'          => $this->object,
                        'vendor_id'      => $this->vendor_id,
                        'vendor_items'   => $this->order_items,
                        'email_heading'  => $this->get_heading(),
                        'sent_to_admin'  => false,
                        'sent_to_vendor' => true,
                        'totals_display' => $this->totals_display,
                        'plain_text'     => true,
                        'email'          => $this,
                    ),
                    'woocommerce',
                    $this->template_base
                ),
                $this
            );
        }

        /**
         * Initialize settings form fields.
         */
        public function init_form_fields() {

            $this->form_fields = array(
                'enabled'        => array(
                    'title'   => __( 'Enable/Disable', 'wc-vendors' ),
                    'type'    => 'checkbox',
                    'label'   => __( 'Enable this email notification', 'wc-vendors' ),
                    'default' => 'yes',
                ),
                'subject'        => array(
                    'title'       => __( 'Subject', 'wc-vendors' ),
                    'type'        => 'text',
                    'desc_tip'    => true,
                    /* translators: %s: list of placeholders */
                    'description' => sprintf( __( 'Available placeholders: %s', 'wc-vendors' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
                    'placeholder' => $this->get_default_subject(),
                    'default'     => '',
                ),
                'heading'        => array(
                    'title'       => __( 'Email heading', 'wc-vendors' ),
                    'type'        => 'text',
                    'desc_tip'    => true,
                    /* translators: %s: list of placeholders */
                    'description' => sprintf( __( 'Available placeholders: %s', 'wc-vendors' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
                    'placeholder' => $this->get_default_heading(),
                    'default'     => '',
                ),
                'totals_display' => array(
                    'title'       => __( 'Totals Display', 'wc-vendors' ),
                    'type'        => 'select',
                    'description' => __( 'Choose how to display the product totals. Including commission or without or no totals at all.', 'wc-vendors' ),
                    'default'     => 'both',
                    'class'       => 'wc-enhanced-select',
                    'options'     => array(
                        'both'       => __( 'Both', 'wc-vendors' ),
                        'commission' => __( 'Commission', 'wc-vendors' ),
                        'product'    => __( 'Product', 'wc-vendors' ),
                        'none'       => __( 'None', 'wc-vendors' ),
                    ),
                    'desc_tip'    => true,
                ),
                'payment_method' => array(
                    'title'   => __( 'Payment method', 'wc-vendors' ),
                    'type'    => 'checkbox',
                    'label'   => __( 'Include the payment method in the email', 'wc-vendors' ),
                    'default' => 'no',
                ),
                'email_type'     => array(
                    'title'       => __( 'Email type', 'wc-vendors' ),
                    'type'        => 'select',
                    'description' => __( 'Choose which format of email to send.', 'wc-vendors' ),
                    'default'     => 'html',
                    'class'       => 'email_type wc-enhanced-select',
                    'options'     => $this->get_email_type_options(),
                    'desc_tip'    => true,
                ),
            );
        }

		/**
		 * Filter customer name from address.
		 *
		 * @param array $address Address.
		 */
		public function filter_customer_name( $address ) {

            unset( $address['first_name'] );
            unset( $address['last_name'] );

            return $address;
        }
    }

endif;
