<?php
/**
 * Settings options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

$options = array(
	'settings' => array(
		'settings-tabs' => array(
			'type'     => 'multi_tab',
			'sub-tabs' => array(
				'settings-general-settings' => array(
					'title' => _x( 'General Settings', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				),
				'settings-booking-forms'    => array(
					'title' => _x( 'Booking Forms', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				),
				'settings-calendars'        => array(
					'title' => _x( 'Calendars', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				),
				'settings-cart'             => array(
					'title' => _x( 'Cart & Orders', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				),
				'settings-customization'    => array(
					'title' => _x( 'Customizations', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ),
				),
			),
		),
	),
);

return apply_filters( 'yith_wcbk_panel_settings_options', $options );
