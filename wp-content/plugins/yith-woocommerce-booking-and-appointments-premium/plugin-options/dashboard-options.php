<?php
/**
 * Dashboard options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

$options = array(
	'dashboard' => array(
		'dashboard-tabs' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => array(
				'dashboard-all-bookings'      => array(
					'title' => _x( 'All Bookings', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				),
				'dashboard-bookings-calendar' => array(
					'title' => _x( 'Calendar', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				),
			),
		),
	),
);

return apply_filters( 'yith_wcbk_panel_dashboard_options', $options );
