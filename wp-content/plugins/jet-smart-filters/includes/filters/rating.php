<?php
/**
 * Rating filter class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Rating_Filter' ) ) {
	/**
	 * Define Jet_Smart_Filters_Rating_Filter class
	 */
	class Jet_Smart_Filters_Rating_Filter extends Jet_Smart_Filters_Filter_Base {
		/**
		 * Get provider name
		 */
		public function get_name() {

			return __( 'Rating', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID
		 */
		public function get_id() {

			return 'rating';
		}

		/**
		 * Get icon URL
		 */
		public function get_icon_url() {

			return jet_smart_filters()->plugin_url( 'admin/assets/img/filter-types/rating.png' );
		}

		/**
		 * Get provider wrapper selector
		 */
		public function get_scripts() {

			return false;
		}

		/**
		 * Prepare filter template argumnets
		 */
		public function prepare_args( $args ) {

			$filter_id            = $args['filter_id'];
			$widget_id            = isset( $args['__widget_id'] ) ? $args['__widget_id'] : false;
			$content_provider     = isset( $args['content_provider'] ) ? $args['content_provider'] : false;
			$additional_providers = isset( $args['additional_providers'] ) ? $args['additional_providers'] : false;
			$button_text          = isset( $args['button_text'] ) ? $args['button_text'] : false;
			$rating_icon          = isset( $args['rating_icon'] ) ? $args['rating_icon'] : false;
			$apply_type           = isset( $args['apply_type'] ) ? $args['apply_type'] : false;

			if ( ! $filter_id ) {
				return false;
			}

			$options      = get_post_meta( $filter_id, '_rating_options', true );
			$options      = ! empty( $options ) ? range( 1, intval( $options ) ) : array();
			$query_type   = 'meta_query';
			$query_var    = get_post_meta( $filter_id, '_query_var', true );

			return array(
				'options'              => $options,
				'query_type'           => $query_type,
				'query_var'            => $query_var,
				'query_var_suffix'     => jet_smart_filters()->filter_types->get_filter_query_var_suffix( $filter_id ),
				'content_provider'     => $content_provider,
				'additional_providers' => $additional_providers,
				'apply_type'           => $apply_type,
				'filter_id'            => $filter_id,
				'button_text'          => $button_text,
				'rating_icon'          => $rating_icon,
				'__widget_id'          => $widget_id,
				'accessibility_label'  => $this->get_accessibility_label( $filter_id )
			);
		}
	}
}
