<?php
namespace Jet_Engine\Modules\Maps_Listings\Providers;

use Jet_Engine\Modules\Maps_Listings\Module;

class Google extends Base {

	/**
	 * Returns provider system slug
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'google';
	}

	/**
	 * Returns provider human-readable name
	 *
	 * @return [type] [description]
	 */
	public function get_label() {
		return __( 'Google Maps', 'jet-engine' );
	}

	public function map_api_deps() {
		
		$api_disabled = Module::instance()->settings->get( 'disable_api_file' );
		$deps         = array();

		if ( ! $api_disabled ) {
			$deps[] = 'jet-engine-google-maps-api';
		}

		return $deps;

	}

	public function register_public_assets() {
		
		$api_disabled = Module::instance()->settings->get( 'disable_api_file' );

		if ( ! $api_disabled ) {

			$query_args = apply_filters( 'jet-engine/maps-listing/map-providers/google/api-url/query-args', array(
				'key'      => Module::instance()->settings->get( 'api_key' ),
				'language' => substr( get_bloginfo( 'language' ), 0, 2 ),
				'callback' => 'Function.prototype', // fixed js error: Loading the Google Maps JavaScript API without a callback is not supported
			) );

			wp_register_script(
				'jet-engine-google-maps-api',
				add_query_arg(
					$query_args,
					'https://maps.googleapis.com/maps/api/js'
				),
				array(),
				false,
				true
			);

		}

		wp_register_script(
			'jet-markerclustererplus',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/lib/markerclustererplus/markerclustererplus.min.js' ),
			$this->map_api_deps(),
			jet_engine()->get_version(),
			true
		);

	}

	public function public_assets( $query, $settings, $render ) {

		wp_enqueue_script( 'jet-engine-google-maps-api' );

		$marker_clustering = isset( $settings['marker_clustering'] ) ? filter_var( $settings['marker_clustering'], FILTER_VALIDATE_BOOLEAN ) : true;

		if ( $marker_clustering ) {
			wp_enqueue_script( 'jet-markerclustererplus' );
		}

		wp_enqueue_script(
			'jet-google-map-provider',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/js/public/google-maps.js' ),
			$this->map_api_deps(),
			jet_engine()->get_version(),
			true 
		);

	}

	public function get_script_handles() {
		return array(
			'jet-engine-google-maps-api',
			'jet-markerclustererplus',
			'jet-google-map-provider',
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
			v-if="'google' === settings.map_provider"
		>
			<cx-vui-input
				label="<?php _e( 'API Key', 'jet-engine' ); ?>"
				description="<?php _e( 'Google maps API key. Video tutorial about creating Google Maps API key <a href=\'https://www.youtube.com/watch?v=t2O2a2YiLJA\' target=\'_blank\'>here</a>. <br>Please make sure <b>Geocoding API</b> is enabled for your API key (or use sparate key for Geocoding API).', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				size="fullwidth"
				@on-input-change="updateSetting( $event.target.value, 'api_key' )"
				:value="settings.api_key"
			></cx-vui-input>
			<cx-vui-switcher
				label="<?php _e( 'Disable Google Maps API JS file', 'jet-engine' ); ?>"
					description="<?php _e( 'Disable Google Maps API JS file, if it already included by another plugin or theme', 'jet-engine' ); ?>"
				:wrapper-css="[ 'equalwidth' ]"
				@input="updateSetting( $event, 'disable_api_file' )"
				:value="settings.disable_api_file"
			></cx-vui-switcher>
		</template>
		<?php
	}

	public function provider_settings() {
		return array(
			'section_general' => array(
				'custom_style' => array(
					'label'       => __( 'Custom Map Style', 'jet-engine' ),
					'type'        => 'textarea',
					'default'     => '',
					'description' => __( 'Find a free map styles at <a href="https://snazzymaps.com/explore" target="_blank" rel="nofollow">Snazzy Maps</a>. Use plain code or link to the file with config.', 'jet-engine' ),
					'has_html'    => true,
					'label_block' => true,
					'dynamic'     => array(
						'active' => true,
					),
				),
				'zoom_control' => array(
					'separator'   => 'before',
					'label'       => __( 'Zoom & Pan Control', 'jet-engine' ),
					'type'        => 'select',
					'description' => __( 'Controls how the API handles gestures on the map. More details <a href="https://developers.google.com/maps/documentation/javascript/interaction#gestureHandling" target="_blank">here</a>', 'jet-engine' ),
					'default'     => 'auto',
					'has_html'    => true,
					'options'     => array(
						'auto'        => __( 'Auto', 'jet-engine' ),
						'greedy'      => __( 'Greedy', 'jet-engine' ),
						'cooperative' => __( 'Cooperative', 'jet-engine' ),
						'none'        => __( 'None', 'jet-engine' ),
					),
				),
				'zoom_controls' => array(
					'label'        => __( 'Zoom Controls', 'jet-engine' ),
					'type'         => 'switcher',
					'label_on'     => __( 'Show', 'jet-engine' ),
					'label_off'    => __( 'Hide', 'jet-engine' ),
					'return_value' => 'true',
					'default'      => 'true',
				),
				'fullscreen_control' => array(
					'label'        => __( 'Fullscreen Control', 'jet-engine' ),
					'type'         => 'switcher',
					'label_on'     => __( 'Show', 'jet-engine' ),
					'label_off'    => __( 'Hide', 'jet-engine' ),
					'return_value' => 'true',
					'default'      => 'true',
				),
				'street_view_controls' => array(
					'label'        => __( 'Street View Controls', 'jet-engine' ),
					'type'         => 'switcher',
					'label_on'     => __( 'Show', 'jet-engine' ),
					'label_off'    => __( 'Hide', 'jet-engine' ),
					'return_value' => 'true',
					'default'      => 'true',
				),
				'map_type_controls' => array(
					'label'        => __( 'Map Type Controls (Map/Satellite)', 'jet-engine' ),
					'type'         => 'switcher',
					'label_on'     => __( 'Show', 'jet-engine' ),
					'label_off'    => __( 'Hide', 'jet-engine' ),
					'return_value' => 'true',
					'default'      => 'true',
				)
			),
			'section_popup_settings' => array(
				'popup_pin' => array(
					'label'        => esc_html__( 'Add popup pin', 'jet-engine' ),
					'type'         => 'switcher',
					'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
					'label_off'    => esc_html__( 'No', 'jet-engine' ),
					'return_value' => 'yes',
					'default'      => '',
				),
			),
		);
	}

	public function prepare_render_settings( $settings = array() ) {
		if ( ! empty( $settings['custom_style'] ) && filter_var( $settings['custom_style'], FILTER_VALIDATE_URL ) ) {
		
			$transient = 'jet_map_styles_' . md5( $settings['custom_style'] );
			$styles = get_transient( $transient );

			if ( ! $styles ) {
				$styles = wp_remote_retrieve_body( wp_remote_get( $settings['custom_style'] ) );
				set_transient( $styles, 3 * DAY_IN_SECONDS );
			}

			$decoded = json_decode( $styles );

			if ( $decoded ) {
				$settings['custom_style'] = $styles;
			}

		}

		return $settings;
	}

}
