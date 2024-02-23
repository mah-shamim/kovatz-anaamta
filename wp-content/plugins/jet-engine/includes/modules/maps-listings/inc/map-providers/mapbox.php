<?php
namespace Jet_Engine\Modules\Maps_Listings\Providers;

use Jet_Engine\Modules\Maps_Listings\Module;

class Mapbox extends Base {

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
			$data['advanced']['scrollwheel'] = ! filter_var( $settings['scrollwheel'], FILTER_VALIDATE_BOOLEAN );
		}

		return $data;
	}

	/**
	 * Returns provider system slug
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'mapbox';
	}

	/**
	 * Returns provider human-readable name
	 *
	 * @return [type] [description]
	 */
	public function get_label() {
		return __( 'Mapbox', 'jet-engine' );
	}

	public function public_assets( $query, $settings, $render ) {

		$marker_clustering = isset( $settings['marker_clustering'] ) ? filter_var( $settings['marker_clustering'], FILTER_VALIDATE_BOOLEAN ) : true;

		wp_enqueue_style(
			'jet-mapbox',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/lib/mapbox/mapbox-gl.css' ),
			array(),
			jet_engine()->get_version()
		);

		wp_enqueue_script(
			'jet-mapbox',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/lib/mapbox/mapbox-gl.js' ),
			array(),
			jet_engine()->get_version(),
			true 
		);

		if ( $marker_clustering ) {
			wp_enqueue_script(
				'jet-mapbox-markerclusterer',
				jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/js/public/mapbox-markerclusterer.js' ),
				array(),
				jet_engine()->get_version(),
				true 
			);
		}

		wp_enqueue_script(
			'jet-mapbox-provider',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/js/public/mapbox-maps.js' ),
			array(),
			jet_engine()->get_version(),
			true 
		);

		wp_localize_script( 'jet-mapbox-provider', 'JetEngineMapboxData', array(
			'token' => Module::instance()->settings->get( 'mapbox_access_token' ),
		) );

	}

	public function get_script_handles() {
		return array(
			'jet-mapbox',
			'jet-mapbox-markerclusterer',
			'jet-mapbox-provider',
		);
	}

	/**
	 * Provider-specific settings fields template
	 *
	 * @return [type] [description]
	 */
	public function settings_fields() {
		?>
		<template
			v-if="'mapbox' === settings.map_provider"
		>
			<cx-vui-input
				label="<?php _e( 'Access token', 'jet-engine' ); ?>"
				description="<?php _e( 'Create access token at Mapbox <a href=\'https://www.mapbox.com/\' target=\'_blank\'>website</a>', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				@on-input-change="updateSetting( $event.target.value, 'mapbox_access_token' )"
				:value="settings.mapbox_access_token"
			></cx-vui-input>
		</template>
		<?php
	}

	public function provider_settings() {

		$settings = array(
			'custom_style' => array(
				'label'       => __( 'Custom Map Style', 'jet-engine' ),
				'type'        => 'text',
				'default'     => '',
				'description' => __( 'Styles map link. You can get in your Mapbox account', 'jet-engine' ),
				'label_block' => true,
			),
		);

		if ( $this->is_active() ) {
			$settings['scrollwheel'] = array(
				'label'        => __( 'Mouse Wheel Zoom', 'jet-engine' ),
				'type'         => 'switcher',
				'label_on'     => __( 'On', 'jet-engine' ),
				'label_off'    => __( 'Off', 'jet-engine' ),
				'return_value' => 'true',
				'default'      => 'true',
			);
		}

		return array(
			'section_general' => $settings,
		);
	}

}
