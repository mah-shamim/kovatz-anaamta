<?php
namespace Jet_Engine\Modules\Maps_Listings\Providers;

class Leaflet extends Base {

	public function __construct() {
		add_filter( 
			'jet-engine/maps-listing/render/default-settings',
			array( $this, 'register_provider_settings' )
		);

		add_filter( 
			'jet-engine/blocks-views/maps-listing/attributes',
			array( $this, 'register_provider_block_attrs' )
		);

		add_filter(
			'jet-engine/maps-listings/data-settings',
			array( $this, 'apply_provider_settings' ),
			10, 2
		);

		parent::__construct();
	}

	/**
	 * Returns provider system slug
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'leaflet';
	}

	/**
	 * Returns provider human-readable name
	 *
	 * @return [type] [description]
	 */
	public function get_label() {
		return __( 'Leaflet Maps', 'jet-engine' );
	}

	public function public_assets( $query, $settings, $render ) {

		// Ensure registered `jet-plugins` script.
		if ( ! wp_script_is( 'jet-plugins', 'registered' )  ) {
			jet_engine()->frontend->register_jet_plugins_js();
		}

		$marker_clustering = isset( $settings['marker_clustering'] ) ? filter_var( $settings['marker_clustering'], FILTER_VALIDATE_BOOLEAN ) : true;

		if ( $marker_clustering ) {
			wp_enqueue_style(
				'jet-leaflet-markercluster',
				jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/lib/leaflet-markercluster/markercluster.css' ),
				array(),
				jet_engine()->get_version()
			);

			wp_enqueue_style(
				'jet-leaflet-markerclusterdefault',
				jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/lib/leaflet-markercluster/markerclusterdefault.css' ),
				array(),
				jet_engine()->get_version()
			);
		}

		wp_enqueue_style(
			'jet-leaflet-map',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/lib/leaflet/leaflet.css' ),
			array(),
			jet_engine()->get_version()
		);

		wp_enqueue_script(
			'jet-leaflet-map',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/lib/leaflet/leaflet.js' ),
			array(),
			jet_engine()->get_version(),
			true 
		);

		if ( $marker_clustering ) {
			wp_enqueue_script(
				'jet-leaflet-markercluster',
				jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/lib/leaflet-markercluster/leaflet.markercluster.js' ),
				array(),
				jet_engine()->get_version(),
				true 
			);
		}

		wp_enqueue_script(
			'jet-leaflet-map-provider',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/js/public/leaflet-maps.js' ),
			array('jet-plugins'),
			jet_engine()->get_version(),
			true 
		);

	}

	public function register_provider_block_attrs( $attrs ) {

		if ( $this->is_active() ) {
			$attrs['scrollwheel'] = array(
				'type'    => 'boolean',
				'default' => true,
			);
		}
		
		return $attrs;
	}

	public function register_provider_settings( $settings ) {

		if ( $this->is_active() ) {
			$settings['scrollwheel'] = true;
		}
		
		return $settings;
	}

	public function apply_provider_settings( $data = array(), $settings = array() ) {
	
		if ( $this->is_active() && isset( $settings['scrollwheel'] ) ) {
			$data['advanced']['scrollwheel'] = filter_var( $settings['scrollwheel'], FILTER_VALIDATE_BOOLEAN );
		} elseif ( $this->is_active() && ! isset( $settings['scrollwheel'] ) ) {
			$data['advanced']['scrollwheel'] = false;
		}

		return $data;
	}

	public function get_script_handles() {
		return array(
			'jet-leaflet-map',
			'jet-leaflet-markercluster',
			'jet-leaflet-map-provider',
		);
	}

	public function provider_settings() {

		if ( ! $this->is_active() ) {
			return array();
		}

		return array(
			'section_general' => array(
				'scrollwheel' => array(
					'label'        => __( 'Mouse Wheel Zoom', 'jet-engine' ),
					'type'         => 'switcher',
					'label_on'     => __( 'On', 'jet-engine' ),
					'label_off'    => __( 'Off', 'jet-engine' ),
					'return_value' => 'true',
					'default'      => 'true',
				),
			),
		);
	}

}
