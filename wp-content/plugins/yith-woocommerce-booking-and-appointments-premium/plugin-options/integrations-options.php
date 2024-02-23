<?php
/**
 * Integrations tab options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit();

$tab_options = array(
	'integrations' => array(
		'integrations-tab' => array(
			'type'           => 'custom_tab',
			'action'         => 'yith_wcbk_print_integrations_tab',
			'show_container' => true,
			'title'          => _x( 'Integrations', 'Settings tab title', 'yith-booking-for-woocommerce' ),
		),
	),
);

return apply_filters( 'yith_wcbk_panel_integrations_options', $tab_options );
