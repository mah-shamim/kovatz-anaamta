<?php
namespace Jet_Engine\Modules\Maps_Listings\Filters\Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Simplified render class (do not use base JE render)
 */
class Location_Distance_Render {

	public function get_type() {
		return 'location-distance';
	}

	public function render( $settings, $base_class ) {

		jet_smart_filters()->set_filters_used();

		$group = false;

		if ( empty( $settings['filter_id'] ) ) {
			return;
		}

		$filter_ids = $settings['filter_id'];

		if ( ! is_array( $filter_ids ) ) {
			$filter_ids = array( $filter_ids );
		}

		if ( 1 < count( $filter_ids ) ) {
			$group = true;
		}

		$placeholder         = ! empty( $settings['placeholder'] ) ? $settings['placeholder'] : '';
		$geolocation_verbose = ! empty( $settings['geolocation_placeholder'] ) ? $settings['geolocation_placeholder'] : __( 'Your current location', 'jet-engine' );

		if ( 'submit' === $settings['apply_on'] && in_array( $settings['apply_type'], ['ajax', 'mixed'] ) ) {
			$apply_type = $settings['apply_type'] . '-reload';
		} else {
			$apply_type = $settings['apply_type'];
		}

		$query_id   = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$show_label = ! empty( $settings['show_label'] ) ? filter_var( $settings['show_label'], FILTER_VALIDATE_BOOLEAN ) : false;

		if ( $group ) {
			echo '<div class="jet-filters-group">';
		}

		foreach ( $filter_ids as $filter_id ) {

			$filter_id = apply_filters( 'jet-engine/render_filter_template/filter_id', $filter_id );

			jet_smart_filters()->admin_bar_register_item( $filter_id );

			printf(
				'<div class="%1$s jet-filter">',
				apply_filters( 'jet-engine/render_filter_template/base_class', $base_class, $filter_id )
			);

			$provider             = ! empty( $settings['content_provider'] ) ? $settings['content_provider'] : '';
			$additional_providers = jet_smart_filters()->utils->get_additional_providers( $settings );

			$filter_template_args =  array(
				'filter_id'            => $filter_id,
				'content_provider'     => $provider,
				'additional_providers' => $additional_providers,
				'placeholder'          => $placeholder,
				'geolocation_verbose'  => $geolocation_verbose,
				'apply_type'           => $apply_type,
				'query_id'             => $query_id,
				'show_label'           => $show_label,
				'display_options'      => array(),
			);

			$filter_template_args['distance_units'] = ! empty( $settings['distance_units'] ) ? $settings['distance_units'] : 'km';

			$distance_list = array();

			foreach ( $settings['distance_list'] as $item ) {
				if ( is_array( $item ) && ! empty( $item['distance'] ) ) {
					$distance_list[] = absint( $item['distance'] );
				} elseif ( ! is_array( $item ) ) {
					$distance_list[] = $item;
				}
			}

			$filter_template_args['distance_list'] = array_filter( $distance_list );

			include jet_smart_filters()->get_template( 'common/filter-label.php' );

			jet_smart_filters()->filter_types->render_filter_template( 
				$this->get_type(),
				$filter_template_args 
			);

			echo '</div>';

		}

		if ( $group ) {
			echo '</div>';
		}

	}

}
