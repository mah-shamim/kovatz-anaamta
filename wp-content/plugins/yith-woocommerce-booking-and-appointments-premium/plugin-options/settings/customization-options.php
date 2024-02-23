<?php
/**
 * Labels options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

$default_labels = yith_wcbk()->language->get_default_labels();

$label_options = array();

$label_options['label-options'] = array(
	'title' => __( 'Labels', 'yith-booking-for-woocommerce' ),
	'type'  => 'title',
	'desc'  => '',
	'id'    => 'yith-wcbk-label-options',
);

$excluded_keys = array(
	'booking-of', // "Booking of" label is available in "Settings > Cart & Checkout" tab options
);

$default_colors = yith_wcbk_get_default_colors();

foreach ( $default_labels as $key => $label ) {
	if ( in_array( $key, $excluded_keys, true ) ) {
		continue;
	}
	$label_options[ $key ] = array(
		'id'                => 'yith-wcbk-label-' . $key,
		'name'              => yith_wcbk_get_default_label( $key ),
		'type'              => 'yith-field',
		'yith-type'         => 'text',
		'desc'              => implode(
			'<br />',
			array(
				// translators: %s is the label text.
				sprintf( __( 'Choose the text of "%s" label for bookable products.', 'yith-booking-for-woocommerce' ), yith_wcbk_get_default_label( $key ) ),
				__( 'Leave empty to get the default label.', 'yith-booking-for-woocommerce' ),
			)
		),
		'default'           => '',
		'custom_attributes' => 'placeholder="' . esc_attr( yith_wcbk_get_default_label( $key ) ) . '"',
	);
}

$label_options['label-options-end'] = array(
	'type' => 'sectionend',
);


$tab_options = array(
	'settings-customization' => array(
		'customization-options'              => array(
			'title' => __( 'Customization options', 'yith-booking-for-woocommerce' ),
			'type'  => 'title',
			'id'    => 'customization-options',
		),
		'costs-included-in-shown-price'      => array(
			'id'        => 'yith-wcbk-costs-included-in-shown-price',
			'name'      => __( 'The price shown will include', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'checkbox-array',
			'options'   => array(
				'base-price'     => __( 'Base Price', 'yith-booking-for-woocommerce' ),
				'fixed-base-fee' => __( 'Fixed base fee', 'yith-booking-for-woocommerce' ),
				'extra-costs'    => __( 'Extra costs', 'yith-booking-for-woocommerce' ),
				'services'       => __( 'Service costs', 'yith-booking-for-woocommerce' ),
			),
			'desc'      => implode(
				'<br />',
				array(
					__( 'Choose which costs will be used when calculating the price shown for bookable products in the Shop page.', 'yith-booking-for-woocommerce' ),
					__( 'Please note: you have to select at least one of these costs.', 'yith-booking-for-woocommerce' ),
				)
			),
			'default'   => array( 'base-price', 'fixed-base-fee', 'extra-costs', 'services' ),
		),
		'show-duration-unit-in-price'        => array(
			'id'        => 'yith-wcbk-show-duration-unit-in-price',
			'name'      => __( 'Show the price type (/day, /month, ...)', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'If enabled, the user will see if the price is for days, for months and so on...', 'yith-booking-for-woocommerce' ),
			'default'   => 'no',
		),
		'replace-days-with-weeks'            => array(
			'id'        => 'yith-wcbk-replace-days-with-weeks-in-price',
			'name'      => __( 'Replace "days" with "weeks"', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Enable to use the "week" formula for booking units multiple of 7 days. In this way, for example, if you set a fixed booking duration of 7 days you will see "price / week" instead of "price / 7 days".', 'yith-booking-for-woocommerce' ),
			'default'   => 'no',
		),
		'redirect-to-checkout-after-booking' => array(
			'id'        => 'yith-wcbk-redirect-to-checkout-after-booking',
			'name'      => __( 'Redirect users to checkout', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'If enabled, after clicking on the "book" button, the user will be automatically redirected to the Checkout page.', 'yith-booking-for-woocommerce' ),
			'default'   => 'no',
		),
		'hide-add-to-cart-button-in-loop'    => array(
			'id'        => 'yith-wcbk-hide-add-to-cart-button-in-loop',
			'name'      => __( 'Hide "Read more" button in Shop', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'Hide "Read more" button for bookable products in Shop pages.', 'yith-booking-for-woocommerce' ),
			'default'   => 'no',
		),
		'date-picker-format'                 => array(
			'id'        => 'yith-wcbk-date-picker-format',
			'name'      => __( 'Date Picker Format', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'date-format',
			'js'        => true,
			'desc'      => __( 'Choose the format for date-pickers in booking forms.', 'yith-booking-for-woocommerce' ),
			'default'   => 'yy-mm-dd',
		),
		'time-picker-format'                 => array(
			'id'        => 'yith-wcbk-time-picker-format',
			'name'      => __( 'Time Picker Format', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'date-format',
			'format'    => 'time',
			'desc'      => __( 'Choose the format for time-pickers in booking forms.', 'yith-booking-for-woocommerce' ),
			'default'   => wc_time_format(),
		),
		'customization-colors'               => array(
			'id'           => 'yith-wcbk-colors',
			'name'         => __( 'Colors', 'yith-booking-for-woocommerce' ),
			'type'         => 'yith-field',
			'yith-type'    => 'multi-colorpicker',
			'colorpickers' => array(
				array(
					'id'            => 'primary',
					'name'          => _x( 'Primary', 'Color variant', 'yith-booking-for-woocommerce' ),
					'alpha_enabled' => false,
					'std'           => $default_colors['primary'],
				),
				array(
					'id'            => 'primary-light',
					'name'          => _x( 'Primary Light', 'Color variant', 'yith-booking-for-woocommerce' ),
					'alpha_enabled' => false,
					'std'           => $default_colors['primary-light'],
				),
				array(
					'id'            => 'primary-contrast',
					'name'          => _x( 'Primary Contrast', 'Color variant', 'yith-booking-for-woocommerce' ),
					'alpha_enabled' => false,
					'std'           => $default_colors['primary-contrast'],
				),
				array(
					'id'            => 'border-color',
					'name'          => _x( 'Border', 'Color variant', 'yith-booking-for-woocommerce' ),
					'alpha_enabled' => false,
					'std'           => $default_colors['border-color'],
				),
				array(
					'id'            => 'border-color-focus',
					'name'          => _x( 'Border focus', 'Color variant', 'yith-booking-for-woocommerce' ),
					'alpha_enabled' => false,
					'std'           => $default_colors['border-color-focus'],
				),
				array(
					'id'            => 'shadow-color-focus',
					'name'          => _x( 'Shadow focus', 'Color variant', 'yith-booking-for-woocommerce' ),
					'alpha_enabled' => true,
					'std'           => $default_colors['shadow-color-focus'],
				),
				array(
					'id'            => 'underlined-bg',
					'name'          => _x( 'Underlined background', 'Color variant', 'yith-booking-for-woocommerce' ),
					'alpha_enabled' => false,
					'std'           => $default_colors['underlined-bg'],
				),
				array(
					'id'            => 'underlined-text',
					'name'          => _x( 'Underlined text', 'Color variant', 'yith-booking-for-woocommerce' ),
					'alpha_enabled' => false,
					'std'           => $default_colors['underlined-text'],
				),
			),
			'desc'         => __( 'Choose colors used in frontend elements, such as booking form fields, search form fields, and so on.', 'yith-booking-for-woocommerce' ),
			'default'      => $default_colors,
		),
		'customization-options-end'          => array(
			'title' => __( 'Customization options', 'yith-booking-for-woocommerce' ),
			'type'  => 'sectionend',
		),

	),
);

$tab_options['settings-customization'] = array_merge( $tab_options['settings-customization'], $label_options );

return apply_filters( 'yith_wcbk_panel_customization_options', $tab_options );
