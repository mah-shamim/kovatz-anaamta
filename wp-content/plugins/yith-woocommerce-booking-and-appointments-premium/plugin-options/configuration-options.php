<?php
/**
 * Dashboard options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

$options = array(
	'configuration' => array(
		'configuration-tabs' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => array(
				'configuration-availability-rules' => array(
					'title' => _x( 'Availability rules', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				),
				'configuration-price-rules'        => array(
					'title' => _x( 'Price rules', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				),
				'configuration-services'           => array(
					'title' => _x( 'Services', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				),
				'configuration-people'             => array(
					'title' => _x( 'People', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				),
				'configuration-costs'              => array(
					'title' => _x( 'Costs', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				),
				'configuration-search-forms'       => array(
					'title' => _x( 'Search Forms', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				),
			),
		),
	),
);

return apply_filters( 'yith_wcbk_panel_configuration_options', $options );
