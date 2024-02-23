<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * WC Vendors TEST SANDBOX Payment Gateway
 *
 * Provides a test Payment Gateway, mainly for WC Vendors testing purposes.
 *
 * @version     1.0.0
 * @package     WooCommerce/Classes/Payment
 * @author      WC Vendors
 */
class WC_Gateway_WCV_Gateway_Test extends WC_Payment_Gateway {
    /**
     * The instructions for the customer about what to do when placing an order with this gateway.
     *
     * @var string
     */
    public $instructions = '';

    /**
     * Constructor for the gateway.
     */
    public function __construct() {
        $this->id                 = 'wcvendors_test_gateway';
        $this->icon               = apply_filters( 'woocommerce_cheque_icon', '' );
        $this->has_fields         = false;
        $this->method_title       = __( 'WC Vendors Test Gateway', 'wc-vendors' );
        $this->method_description = __( 'This gateway will set orders to processing upon receipt allowing you to test transactions in your store.  Some WooCommerce included gateways have problems with this - you should use this gateway for all of your non-PayPal testing.', 'wc-vendors' );

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Define user set variables.
        $this->title        = $this->get_option( 'title' );
        $this->description  = $this->get_option( 'description' );
        $this->instructions = $this->get_option( 'instructions', $this->description );

        // Actions.
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_thankyou_wcvendors_test_gateway', array( $this, 'thankyou_page' ) );

        // Customer Emails.
        add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
    }

    /**
     * Initialize Gateway Settings Form Fields.
     *
     * @return void
     */
    public function init_form_fields() {

        $this->form_fields = array(
            'enabled'      => array(
                'title'   => __( 'Enable/Disable', 'wc-vendors' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable WC Vendors Test Gateway Payment', 'wc-vendors' ),
                'default' => 'no',
            ),
            'title'        => array(
                'title'       => __( 'Title', 'wc-vendors' ),
                'type'        => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'wc-vendors' ),
                'default'     => __( 'WC Vendors Test Gateway', 'wc-vendors' ),
                'desc_tip'    => true,
            ),
            'description'  => array(
                'title'       => __( 'Description', 'wc-vendors' ),
                'type'        => 'textarea',
                'description' => __( 'Payment method description that the customer will see on your checkout.', 'wc-vendors' ),
                'default'     => __( 'This is a test gateway -- not to be used on live sites for live transactions. <a href="http://www.wcvendors.com/" target="top">Click here to visit WCVendors.com</a>.', 'wc-vendors' ),
                'desc_tip'    => true,
            ),
            'instructions' => array(
                'title'       => __( 'Instructions', 'wc-vendors' ),
                'type'        => 'textarea',
                'description' => __( 'Success!  Your test order is now marked as processing and any vendors will be sent an email as long as you have the Notify Vendors email enabled under WooCommerce--Settings--Emails.', 'wc-vendors' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
        );
    }

    /**
     * Output for the order received page.
     */
    public function thankyou_page() {
        if ( $this->instructions ) {
            echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) );
        }
    }

    /**
     * Add content to the WC emails.
     *
     * @access public
     * @param WC_Order $order The order object.
     * @param bool     $sent_to_admin Whether the email is sent to admin or not.
     * @param bool     $plain_text The plain text version of the email.
     */
    public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

        $payment_method = $order->get_payment_method();

        if ( $this->instructions && ! $sent_to_admin && 'wcvendors_test_gateway' === $payment_method && $order->has_status( 'processing' ) ) {
            echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) ) . PHP_EOL;
        }
    }

    /**
     * Process the payment and return the result
     *
     * @param int $order_id The order ID.
     * @return array
     */
    public function process_payment( $order_id ) {

        $order = wc_get_order( $order_id );

        // Mark as processing.
        if ( $order->get_total() > 0 ) {
            $order->update_status( 'wc-processing', __( 'Test gateway transaction complete.  Order processing.', 'wc-vendors' ) );
        } else {
            $order->payment_complete();
        }

        // Reduce stock levels.
        wc_reduce_stock_levels( $order_id );

        // Remove cart.
        WC()->cart->empty_cart();

        // Return thankyou redirect.
        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url( $order ),
        );
    }
}
