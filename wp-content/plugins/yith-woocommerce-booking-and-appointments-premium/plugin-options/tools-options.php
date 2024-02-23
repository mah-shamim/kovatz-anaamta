<?php
/**
 * Tools options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit();

$tab_options = array(
	'tools' => array(
		'tools-tabs' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => array(
				'tools-tools' => array(
					'title' => _x( 'Tools', 'Settings tab name', 'yith-booking-for-woocommerce' ),
				),
				'tools-logs'  => array(
					'title' => _x( 'Logs', 'Settings tab name', 'yith-booking-for-woocommerce' ),
				),
			),
		),
	),
);

return apply_filters( 'yith_wcbk_panel_tools_options', $tab_options );
