<?php
/**
 * Compatibility manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Smart_Filters_Compatibility_Manager class
 */
class Jet_Smart_Filters_Compatibility_Manager {

	/**
	 * Constructor for the class
	 */
	function __construct() {

		if ( defined( 'WPML_ST_VERSION' ) ) {
			require jet_smart_filters()->plugin_path( 'includes/compatibility/wpml.php' );
			new Jet_Smart_Filters_Compatibility_WMPL();
		}

		if ( class_exists( 'WooCommerce' ) ) {
			require jet_smart_filters()->plugin_path( 'includes/compatibility/woocommerce.php' );
			new Jet_Smart_Filters_Compatibility_WC();
		}

		if ( function_exists( 'jet_engine' ) ) {
			require jet_smart_filters()->plugin_path( 'includes/compatibility/jet-engine.php' );
			new Jet_Smart_Filters_Compatibility_JE();
		}

		add_filter( 'jet-smart-filters/filters/localized-data',  array( $this, 'datepicker_texts' ) );

	}

	public function datepicker_texts( $args ) {

		$args['datePickerData'] = array(
			'closeText'       => esc_html__( 'Done', 'jet-smart-filters' ),
			'prevText'        => esc_html__( 'Prev', 'jet-smart-filters' ),
			'nextText'        => esc_html__( 'Next', 'jet-smart-filters' ),
			'currentText'     => esc_html__( 'Today', 'jet-smart-filters' ),
			'monthNames'      => array(
				esc_html__( 'January', 'jet-smart-filters' ),
				esc_html__( 'February', 'jet-smart-filters' ),
				esc_html__( 'March', 'jet-smart-filters' ),
				esc_html__( 'April', 'jet-smart-filters' ),
				esc_html__( 'May', 'jet-smart-filters' ),
				esc_html__( 'June', 'jet-smart-filters' ),
				esc_html__( 'July', 'jet-smart-filters' ),
				esc_html__( 'August', 'jet-smart-filters' ),
				esc_html__( 'September', 'jet-smart-filters' ),
				esc_html__( 'October', 'jet-smart-filters' ),
				esc_html__( 'November', 'jet-smart-filters' ),
				esc_html__( 'December', 'jet-smart-filters' ),
			),
			'monthNamesShort' => array(
				esc_html__( 'Jan', 'jet-smart-filters' ),
				esc_html__( 'Feb', 'jet-smart-filters' ),
				esc_html__( 'Mar', 'jet-smart-filters' ),
				esc_html__( 'Apr', 'jet-smart-filters' ),
				esc_html__( 'May', 'jet-smart-filters' ),
				esc_html__( 'Jun', 'jet-smart-filters' ),
				esc_html__( 'Jul', 'jet-smart-filters' ),
				esc_html__( 'Aug', 'jet-smart-filters' ),
				esc_html__( 'Sep', 'jet-smart-filters' ),
				esc_html__( 'Oct', 'jet-smart-filters' ),
				esc_html__( 'Nov', 'jet-smart-filters' ),
				esc_html__( 'Dec', 'jet-smart-filters' ),
			),
			'dayNames'        => array(
				esc_html__( 'Sunday', 'jet-smart-filters' ),
				esc_html__( 'Monday', 'jet-smart-filters' ),
				esc_html__( 'Tuesday', 'jet-smart-filters' ),
				esc_html__( 'Wednesday', 'jet-smart-filters' ),
				esc_html__( 'Thursday', 'jet-smart-filters' ),
				esc_html__( 'Friday', 'jet-smart-filters' ),
				esc_html__( 'Saturday', 'jet-smart-filters' )
			),
			'dayNamesShort'   => array(
				esc_html__( 'Sun', 'jet-smart-filters' ),
				esc_html__( 'Mon', 'jet-smart-filters' ),
				esc_html__( 'Tue', 'jet-smart-filters' ),
				esc_html__( 'Wed', 'jet-smart-filters' ),
				esc_html__( 'Thu', 'jet-smart-filters' ),
				esc_html__( 'Fri', 'jet-smart-filters' ),
				esc_html__( 'Sat', 'jet-smart-filters' )
			),
			'dayNamesMin'     => array(
				esc_html__( 'Su', 'jet-smart-filters' ),
				esc_html__( 'Mo', 'jet-smart-filters' ),
				esc_html__( 'Tu', 'jet-smart-filters' ),
				esc_html__( 'We', 'jet-smart-filters' ),
				esc_html__( 'Th', 'jet-smart-filters' ),
				esc_html__( 'Fr', 'jet-smart-filters' ),
				esc_html__( 'Sa', 'jet-smart-filters' ),
			),
			'weekHeader'      => esc_html__( 'Wk', 'jet-smart-filters' ),
		);

		return $args;
	}

}
