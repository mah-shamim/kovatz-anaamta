<?php
/**
 * Cart & Orders options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

/**
 * The "Deposits" integration instance.
 *
 * @var YITH_WCBK_Deposits_Integration $deposits_integration
 */
$deposits_integration = yith_wcbk()->integrations->get_integration( 'deposits' );

$options = array(
	'settings-cart' => array(
		'cart-settings'                        => array(
			'title' => __( 'Cart & Checkout', 'yith-booking-for-woocommerce' ),
			'type'  => 'title',
			'desc'  => '',
		),
		'show-booking-of-in-cart-and-checkout' => array(
			'id'        => 'yith-wcbk-show-booking-of-in-cart-and-checkout',
			'name'      => __( 'Show "booking of" text before product name', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable to add a "booking of" label before the product name in Cart and Checkout.', 'yith-booking-for-woocommerce' ),
			'default'   => 'no',
		),
		'booking-of-label'                     => array(
			'id'                => 'yith-wcbk-label-booking-of', // This ID has to be 'yith-wcbk-label-booking-of'; see YITH_WCBK_Language for details.
			'name'              => __( '"Booking of" label', 'yith-booking-for-woocommerce' ),
			'type'              => 'yith-field',
			'yith-type'         => 'text',
			'desc'              => __( 'Enter a text to be shown before product name in cart and checkout pages.', 'yith-booking-for-woocommerce' ),
			'default'           => '',
			'custom_attributes' => 'placeholder="' . esc_attr( yith_wcbk_get_default_label( 'booking-of' ) ) . '"',
			'deps'              => array(
				'id'    => 'yith-wcbk-show-booking-of-in-cart-and-checkout',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),
		'show-totals-in-cart-and-checkout'     => array(
			'id'        => 'yith-wcbk-show-totals-in-cart-and-checkout',
			'name'      => __( 'Show totals in cart and checkout', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable to show totals in Cart and Checkout.', 'yith-booking-for-woocommerce' ),
			'default'   => 'no',
		),
		'cart-settings-end'                    => array(
			'type' => 'sectionend',
		),
		'deposits-integration-options'         => array(
			// translators: %s is the plugin name.
			'title' => sprintf( _x( '%s integration', 'Panel Section Title', 'yith-booking-for-woocommerce' ), esc_html( $deposits_integration->get_name() ) ),
			'type'  => 'title',
			'desc'  => $deposits_integration->is_enabled() ? '' : implode(
				' ',
				array(
					// translators:  %s is the plugin name including the version number.
					sprintf( esc_html__( 'In order to use this integration you have to install and activate %s or greater.', 'yith-booking-for-woocommerce' ), esc_html( $deposits_integration->get_name() . ' ' . $deposits_integration->get_min_version() ) ),
					"<a href='" . esc_url( $deposits_integration->get_landing_uri() ) . "'>" . esc_html_x( 'Learn more', 'Learn more link for plugin integrations', 'yith-booking-for-woocommerce' ) . '</a>',
				)
			),
		),
		'set-booking-paid-for-deposits'        => array(
			'id'              => 'yith-wcbk-set-booking-paid-for-deposits',
			'name'            => __( 'Set bookings as paid for deposit orders', 'yith-booking-for-woocommerce' ),
			'type'            => 'yith-field',
			'yith-type'       => 'radio',
			'options'         => array(
				'deposit' => esc_html__( 'When the deposit order is paid', 'yith-booking-for-woocommerce' ),
				'balance' => esc_html__( 'When the balance order is paid', 'yith-booking-for-woocommerce' ),
			),
			'default'         => 'deposit',
			'extra_row_class' => $deposits_integration->is_enabled() ? '' : 'yith-disabled',
			'desc'            => esc_html__( 'Choose when setting the booking as paid if it\'s purchased with a deposit.', 'yith-booking-for-woocommerce' ),
		),
		'deposits-integration-options-end'     => array(
			'type' => 'sectionend',
		),
	),
);

return apply_filters( 'yith_wcbk_panel_cart_and_checkout_options', $options );
