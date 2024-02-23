<?php
/**
 * Forms options
 *
 * @package YITH\Booking\Options
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.

$options = array(
	'settings-booking-forms' => array(
		'booking-forms-options'                   => array(
			'title' => __( 'Forms options', 'yith-booking-for-woocommerce' ),
			'type'  => 'title',
			'desc'  => '',
		),
		'show-booking-form-to'                    => array(
			'id'        => 'yith-wcbk-show-booking-form-to',
			'name'      => __( 'Show booking form in bookable product page to', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'desc'      => __( 'Choose to show the booking form in the product page or hide it to guest users.', 'yith-booking-for-woocommerce' ),
			'options'   => array(
				'all'       => __( 'All users', 'yith-booking-for-woocommerce' ),
				'logged-in' => __( 'Only logged-in users', 'yith-booking-for-woocommerce' ),
			),
			'default'   => 'all',
		),
		'booking-form-position'                   => array(
			'id'        => 'yith-wcbk-booking-form-position',
			'name'      => __( 'Booking Form Position', 'yith-booking-for-woocommerce' ),
			'class'     => 'wc-enhanced-select',
			'type'      => 'yith-field',
			'yith-type' => 'select',
			'options'   => array(
				'default'            => __( 'Default', 'yith-booking-for-woocommerce' ),
				'before_summary'     => __( 'Before summary', 'yith-booking-for-woocommerce' ),
				'after_title'        => __( 'After title', 'yith-booking-for-woocommerce' ),
				'before_description' => __( 'Before description', 'yith-booking-for-woocommerce' ),
				'after_description'  => __( 'After description', 'yith-booking-for-woocommerce' ),
				'after_summary'      => __( 'After summary', 'yith-booking-for-woocommerce' ),
				'widget'             => __( 'Use Widget', 'yith-booking-for-woocommerce' ),
				'none'               => __( 'None', 'yith-booking-for-woocommerce' ),
			),
			'default'   => 'default',
			'desc'      => implode(
				'<br />',
				array(
					__( 'Choose the position of the booking form in Single Product Page.', 'yith-booking-for-woocommerce' ),
					__( 'In our live demo, we set it to "use widget". Then in "Appearance > Widgets" we put the "Bookable Product Form" widget into the product page sidebar.', 'yith-booking-for-woocommerce' ),
				)
			),
		),
		'date-range-picker-layout'                => array(
			'id'        => 'yith-wcbk-date-range-picker-layout',
			'name'      => __( 'Date range picker layout', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'desc'      => __( 'Choose to show the date pickers as a unique field or as two different fields. In our demo, we use the single line layout.', 'yith-booking-for-woocommerce' ),
			'options'   => array(
				'unique'    => __( 'Start and end date in a single line', 'yith-booking-for-woocommerce' ),
				'different' => __( 'Start and end date in separate lines', 'yith-booking-for-woocommerce' ),
			),
			'default'   => 'unique',
		),
		'calendar-range-picker-columns'           => array(
			'id'        => 'yith-wcbk-calendar-range-picker-columns',
			'name'      => __( 'Date range picker columns', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'desc'      => implode(
				'<br />',
				array(
					__( 'Choose the number of columns for calendar range picker fields shown in the Booking form.', 'yith-booking-for-woocommerce' ),
					__( 'In our live demo we use the single column layout.', 'yith-booking-for-woocommerce' ),
				)
			),
			'default'   => 1,
			'options'   => array(
				1 => __( 'Single column', 'yith-booking-for-woocommerce' ),
				2 => __( 'Two columns', 'yith-booking-for-woocommerce' ),
			),
			'deps'      => array(
				'id'    => 'yith-wcbk-date-range-picker-layout',
				'value' => 'different',
				'type'  => 'hide',
			),
		),
		'calendar-style'                          => array(
			'id'        => 'yith-wcbk-calendar-style',
			'name'      => __( 'Calendar style', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'default'   => 'dropdown',
			'options'   => array(
				'dropdown' => __( 'Show as dropdown', 'yith-booking-for-woocommerce' ),
				'inline'   => __( 'Show inline in the page', 'yith-booking-for-woocommerce' ),
			),
			'desc'      => implode(
				'<br />',
				array(
					__( 'Choose to show the calendar in a dropdown or by embedding it in the page.', 'yith-booking-for-woocommerce' ),
					__( 'Please note: this will affect the single date-pickers only; the "single-line" date range picker will always be displayed as a dropdown.', 'yith-booking-for-woocommerce' ),
				)
			),
		),
		'months-loaded-in-calendar'               => array(
			'id'                   => 'yith-wcbk-months-loaded-in-calendar',
			'name'                 => __( 'In calendar load', 'yith-booking-for-woocommerce' ),
			'type'                 => 'yith-field',
			'yith-type'            => 'number',
			'default'              => 3,
			'min'                  => 1,
			'max'                  => 12,
			'class'                => 'yith-wcbk-number-field-mini',
			'yith-wcbk-after-html' => __( 'months', 'yith-booking-for-woocommerce' ),
			'desc'                 => __( 'Choose the number of months loaded in calendar. Other ones will be loaded in AJAX to improve performance (Suggested: 3).', 'yith-booking-for-woocommerce' ),
		),
		'enable-people-selector'                  => array(
			'id'        => 'yith-wcbk-people-selector-enabled',
			'name'      => __( 'People types layout', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'options'   => array(
				'yes' => __( 'Show as dropdown', 'yith-booking-for-woocommerce' ),
				'no'  => __( 'Show people type fields', 'yith-booking-for-woocommerce' ),
			),
			'default'   => 'yes',
			'desc'      => __( 'Choose to show people types in a dropdown or by listing all people type fields in the Booking form.', 'yith-booking-for-woocommerce' ),
		),
		'person-type-columns'                     => array(
			'id'        => 'yith-wcbk-person-type-columns',
			'name'      => __( 'Columns for people fields', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'number',
			'desc'      => __( 'Choose the number of columns for people fields shown in the Booking form.', 'yith-booking-for-woocommerce' ),
			'default'   => '1',
			'min'       => 1,
			'deps'      => array(
				'id'    => 'yith-wcbk-people-selector-enabled',
				'value' => 'no',
				'type'  => 'hide',
			),
		),
		'check-min-max-duration-in-calendar'      => array(
			'id'        => 'yith-wcbk-check-min-max-duration-in-calendar',
			'name'      => __( 'Check min/max duration', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'If enabled, the plugin considers the minimum and maximum duration to show available dates in the calendar.', 'yith-booking-for-woocommerce' ),
			'default'   => 'yes',
		),
		'ajax-update-non-available-dates-on-load' => array(
			'id'        => 'yith-wcbk-ajax-update-non-available-dates-on-load',
			'name'      => __( 'Update non-available dates on loading (AJAX)', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => __( 'If enabled, the plugin will update non-available dates in the calendar on page loading. You should activate it only if you use plugins to cache product pages.', 'yith-booking-for-woocommerce' ),
			'default'   => 'no',
		),
		'disable-day-if-no-time-available'        => array(
			'id'        => 'yith-wcbk-disable-day-if-no-time-available',
			'name'      => __( 'Disable day if no time is available', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'onoff',
			'desc'      => implode(
				'<br />',
				array(
					__( 'If enabled, hide days in calendar if no time is available.', 'yith-booking-for-woocommerce' ),
					__( 'Please note: enabling this option the calendar will show up to 3 months for hourly bookings and up to 1 month for per-minute bookings.', 'yith-booking-for-woocommerce' ),
				)
			),
			'default'   => 'no',
		),
		'show-service-prices'                     => array(
			'id'            => 'yith-wcbk-show-service-prices',
			'name'          => __( 'Info to show before "book" button', 'yith-booking-for-woocommerce' ),
			'type'          => 'checkbox',
			'desc'          => __( 'Prices for services', 'yith-booking-for-woocommerce' ),
			'default'       => 'no',
			'checkboxgroup' => 'start',
		),
		'show-service-descriptions'               => array(
			'id'            => 'yith-wcbk-show-service-descriptions',
			'desc'          => __( 'Descriptions for services', 'yith-booking-for-woocommerce' ),
			'type'          => 'checkbox',
			'default'       => 'no',
			'checkboxgroup' => '',
		),
		'show-included-services'                  => array(
			'id'            => 'yith-wcbk-show-included-services',
			'type'          => 'checkbox',
			'desc'          => __( 'Included services', 'yith-booking-for-woocommerce' ),
			'default'       => 'yes',
			'checkboxgroup' => '',
		),
		'show-totals'                             => array(
			'id'            => 'yith-wcbk-show-totals',
			'type'          => 'checkbox',
			'desc'          => __( 'Totals', 'yith-booking-for-woocommerce' ),
			'default'       => 'no',
			'checkboxgroup' => 'end',
		),
		'service-info-layout'                     => array(
			'id'        => 'yith-wcbk-service-info-layout',
			'name'      => __( 'Service info layout', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'desc'      => __( 'Choose how to show service info (description and prices).', 'yith-booking-for-woocommerce' ),
			'options'   => array(
				'tooltip' => __( 'Show in tooltip', 'yith-booking-for-woocommerce' ),
				'inline'  => __( 'Show inline', 'yith-booking-for-woocommerce' ),
			),
			'default'   => 'tooltip',
			'deps'      => array(
				'id'    => 'yith-wcbk-show-service-descriptions',
				'value' => 'yes',
				'type'  => 'hide',
			),
		),
		'form-error-handling'                     => array(
			'id'        => 'yith-wcbk-form-error-handling',
			'name'      => __( 'In case of errors in the booking form', 'yith-booking-for-woocommerce' ),
			'type'      => 'yith-field',
			'yith-type' => 'radio',
			'desc'      => __( 'Choose the booking form behavior in case of errors.', 'yith-booking-for-woocommerce' ),
			'options'   => array(
				'on-form-update'  => __( 'Disable the "book" button and show messages when updating the form', 'yith-booking-for-woocommerce' ),
				'on-button-click' => __( 'Show error messages after clicking on the "book" button', 'yith-booking-for-woocommerce' ),
			),
			'default'   => 'on-form-update',
		),
		'booking-forms-options-end'               => array(
			'type' => 'sectionend',
		),
	),
);

return apply_filters( 'yith_wcbk_panel_forms_options', $options );
