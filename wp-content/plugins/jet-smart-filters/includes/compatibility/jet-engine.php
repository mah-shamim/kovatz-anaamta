<?php
/**
 * Compatibility filters and actions
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Compatibility_JE class
 */
class Jet_Smart_Filters_Compatibility_JE {

	/**
	 * Constructor for the class
	 */
	function __construct() {

		if ( ! function_exists( 'jet_engine' ) ) {
			return;
		}

		// for CCT
		add_filter( 'jet-smart-filters/post-type/options-data-sources', array( $this, 'cct_data_sources' ) );
		add_filter( 'jet-smart-filters/post-type/meta-fields-settings', array( $this, 'cct_register_controls' ) );

		// Filter query macros
		add_action( 'jet-engine/register-macros', array( $this, 'register_macros' ) );
	}

	/**
	 * Register filter related macros for JetEngine
	 * @return void
	 */
	public function register_macros() {

		require_once jet_smart_filters()->plugin_path( 'includes/compatibility/jet-engine-macros-filter-query.php' );
		new Jet_Smart_Filters_Macros_Filter_Query();

	}

	public function cct_data_sources( $data_sources ) {

		if ( function_exists( 'jet_engine' ) && jet_engine()->modules->is_module_active( 'custom-content-types' ) ) {
			$data_sources['cct'] = __( 'JetEngine Custom Content Types', 'jet-smart-filters' );
		}

		return $data_sources;
	}

	public function cct_register_controls( $fields ) {

		if ( function_exists( 'jet_engine' ) && jet_engine()->modules->is_module_active( 'custom-content-types' ) ) {
			$fields = jet_smart_filters()->utils->array_insert_after( $fields, '_data_source', array(
				'_cct_notice' => array(
					'title'      => __( 'Coming soon', 'jet-smart-filters' ),
					'type'       => 'html',
					'fullwidth'  => true,
					'html'       => __( 'Support for the Visual filter will be added with future updates', 'jet-smart-filters' ),
					'conditions' => array(
						'_filter_type' => 'color-image',
						'_data_source' => 'cct',
					),
				),
			) );

			if ( jet_smart_filters()->is_classic_admin ) {
				$fields['_cct_notice']['type']        = 'text';
				$fields['_cct_notice']['input_type']  = 'hidden';
				$fields['_cct_notice']['description'] = $fields['_cct_notice']['html'];
				unset( $fields['_cct_notice']['html'] );
			}
		}

		$fields = jet_smart_filters()->utils->add_control_condition( $fields, '_color_image_type', '_cct_notice!', 'is_visible' );
		$fields = jet_smart_filters()->utils->add_control_condition( $fields, '_color_image_behavior', '_cct_notice!', 'is_visible' );
		$fields = jet_smart_filters()->utils->add_control_condition( $fields, '_source_color_image_input', '_cct_notice!', 'is_visible' );
		$fields = jet_smart_filters()->utils->add_control_condition( $fields, '_query_var', '_cct_notice!', 'is_visible' );

		return $fields;
	}

}
