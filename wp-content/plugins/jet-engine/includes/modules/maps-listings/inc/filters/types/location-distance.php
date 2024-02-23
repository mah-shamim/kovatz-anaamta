<?php
namespace Jet_Engine\Modules\Maps_Listings\Filters\Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Location_Distance class
 */
class Location_Distance extends \Jet_Smart_Filters_Filter_Base {

	private $css_rendered = false;

	/**
	 * Get provider name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Location & Distance', 'jet-engine' );
	}

	/**
	 * Get provider ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'location-distance';
	}

	/**
	 * Get icon URL
	 * 
	 * @return string
	 */
	public function get_icon_url() {

		return jet_engine()->plugin_url( 'assets/img/jet-smart-filters-types/location-distance.png' );
	}

	/**
	 * Get provider wrapper selector
	 *
	 * @return string
	 */
	public function get_scripts() {
		return array( 'jet-maps-listings-location-distance' );
	}

	public function get_template( $args = array() ) {
		return jet_engine()->modules->modules_path( 'maps-listings/inc/filters/types/location-distance-template.php' );
	}

	public function render_styles() {
		?>
		<style type="text/css">
			.jsf-location-distance {
				display: flex;
				gap: 10px;
			}
			.jsf-location-distance__location {
				flex: 0 0 80%;
				position: relative;
				display: flex;
				justify-content: stretch;
			}
			.jsf-location-distance__location-input {
				flex: 0 0 100%;
			}
			.jsf-location-distance__location-icon {
				width: 20px;
				height:20px;
				opacity: .5;
			}
			.jsf-location-distance__location-icon path {
				fill: currentColor;
			}
			.jsf-location-distance__location-clear,
			.jsf-location-distance__location-locate,
			.jsf-location-distance__location-loading {
				display: none;
				position: absolute;
				top: 50%;
				right: 0;
				margin: -12px 10px 0 0;
				width: 24px;
				height: 24px;
				align-items: center;
				justify-content: center;
				cursor: pointer;
			}
			.jsf-show-clear .jsf-location-distance__location-clear {
				display: flex;
			}
			.jsf-show-locate .jsf-location-distance__location-locate {
				display: flex;
			}
			.jsf-show-loading .jsf-location-distance__location-loading {
				animation: spin 1s infinite linear;
				display: flex;
			}
			.jsf-location-distance__location-clear:hover .jsf-location-distance__location-icon,
			.jsf-location-distance__location-locate:hover .jsf-location-distance__location-icon {
				opacity: 1;
			}
			.jsf-location-distance__tooltip {
				background: #23282D;
				box-shadow: 0px 1px 4px rgba(35,40,45,0.24);
				border-radius: 3px;
				padding: 5px 10px;
				font-size: 12px;
				line-height: 15px;
				color: #fff;
				bottom: 100%;
				position: absolute;
				margin: 0 0 15px -75px;
				text-align: center;
				pointer-events: none;
				opacity: 0;
				visibility: hidden;
				width: 150px;
				left: 50%;
				box-sizing: border-box;
				transition: all 150ms linear;
				z-index: 9999;
			}
			.jsf-location-distance__tooltip:after {
				top: 100%;
				left: 50%;
				margin: 0 0 0 -4px;
				width: 0;
				height: 0;
				border-style: solid;
				border-width: 4px 4px 0 4px;
				border-color: #23282D transparent transparent transparent;
				content: "";
				position: absolute;
			}
			.jsf-location-distance__location-locate:hover .jsf-location-distance__tooltip {
				visibility: visible;
				opacity: 1;
				margin-bottom: 10px;
			}
			.jsf-location-distance__location-dropdown {
				display: none;
				position: absolute;
				left: 0;
				right:0;
				top: 100%;
				background: #fff;
				box-shadow: 0 5px 15px rgba(0,0,0,.01);
				z-index: 999;
			}
			.jsf-location-distance__location-dropdown-item {
				padding: 8px 15px;
				font-size: .75em;
				cursor: pointer;
			}
			.jsf-location-distance__location-dropdown-item:hover {
				background: rgba(0,0,0,.1);
			}
			.jsf-location-distance__location-dropdown.is-active {
				display: block;
			}
			.jsf-location-distance__distance {
				flex: 1 1 auto;
				-webkit-appearance: none;
			}
			@keyframes spin {
				0% {
					transform: rotate(0deg);
				}
				100% {
					transform: rotate(360deg);
				}
			}

		</style>
		<?php
	}

	/**
	 * Prepare filter template argumnets
	 *
	 * @param  [type] $args [description]
	 *
	 * @return [type]       [description]
	 */
	public function prepare_args( $args ) {

		$filter_id            = $args['filter_id'];
		$content_provider     = isset( $args['content_provider'] ) ? $args['content_provider'] : false;
		$additional_providers = isset( $args['additional_providers'] ) ? $args['additional_providers'] : false;
		$distance_units       = isset( $args['distance_units'] ) ? $args['distance_units'] : 'km';
		$distance_list        = isset( $args['distance_list'] ) ? $args['distance_list'] : [ 10, 25, 50, 100, 250 ];
		$apply_type           = isset( $args['apply_type'] ) ? $args['apply_type'] : 'ajax';
		$placeholder          = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
		$geolocation_verbose  = isset( $args['geolocation_verbose'] ) ? $args['geolocation_verbose'] : __( 'Your current location', 'jet-engine' );

		if ( ! $filter_id ) {
			return false;
		}

		if ( ! $this->css_rendered ) {
			$this->render_styles();
			$this->css_rendered = true;
		}

		$current_value = array();
		$current_query = jet_smart_filters()->query->get_query_args();

		if ( ! empty( $current_query['geo_query'] )
			&& ! empty( $current_query['jet_smart_filters'] ) 
			&& $args['content_provider'] . '/' . $args['query_id'] === $current_query['jet_smart_filters']
		) {
			$current_value = $current_query['geo_query'];
		}

		return array(
			'options'              => false,
			'query_type'           => 'geo_query',
			'distance_units'       => $distance_units,
			'distance_list'        => $distance_list,
			'placeholder'          => $placeholder,
			'geolocation_verbose'  => $geolocation_verbose,
			'query_var'            => false,
			'query_var_suffix'     => jet_smart_filters()->filter_types->get_filter_query_var_suffix( $filter_id ),
			'content_provider'     => $content_provider,
			'additional_providers' => $additional_providers,
			'filter_id'            => $filter_id,
			'apply_type'           => $apply_type,
			'current_value'        => $current_value,
		);

	}

}
