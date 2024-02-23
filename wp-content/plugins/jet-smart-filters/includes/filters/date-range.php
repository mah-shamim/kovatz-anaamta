<?php
/**
 * Range filter class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Date_Range_Filter' ) ) {
	/**
	 * Define Jet_Smart_Filters_Date_Range_Filter class
	 */
	class Jet_Smart_Filters_Date_Range_Filter extends Jet_Smart_Filters_Filter_Base {
		/**
		 * Get provider name
		 */
		public function get_name() {

			return __( 'Date Range', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID156
		 */
		public function get_id() {

			return 'date-range';
		}

		/**
		 * Get icon URL
		 */
		public function get_icon_url() {

			return jet_smart_filters()->plugin_url( 'admin/assets/img/filter-types/date-range.png' );
		}

		/**
		 * Get provider wrapper selector
		 */
		public function get_scripts() {

			return array( 'jquery-ui-datepicker' );
		}

		/**
		 * Prepare filter template argumnets
		 */
		public function prepare_args( $args ) {

			$filter_id            = $args['filter_id'];
			$content_provider     = isset( $args['content_provider'] ) ? $args['content_provider'] : false;
			$additional_providers = isset( $args['additional_providers'] ) ? $args['additional_providers'] : false;
			$apply_type           = isset( $args['apply_type'] ) ? $args['apply_type'] : false;
			$button_text          = isset( $args['button_text'] ) ? $args['button_text'] : false;
			$button_icon          = isset( $args['button_icon'] ) ? $args['button_icon'] : false;
			$hide_button          = isset( $args['hide_button'] ) ? $args['hide_button'] : false;
			$button_icon_position = isset( $args['button_icon_position'] ) ? $args['button_icon_position'] : 'left';

			if ( ! $filter_id ) {
				return false;
			}

			$query_type  = get_post_meta( $filter_id, '_date_source', true );
			$query_var   = $query_type === 'meta_query' ? get_post_meta( $filter_id, '_query_var', true ) : '';
			$date_format = get_post_meta( $filter_id, '_date_format', true );
			$from        = get_post_meta( $filter_id, '_date_from_placeholder', true );
			$to          = get_post_meta( $filter_id, '_date_to_placeholder', true );

			return array(
				'options'              => false,
				'query_type'           => $query_type,
				'query_var'            => $query_var,
				'query_var_suffix'     => jet_smart_filters()->filter_types->get_filter_query_var_suffix( $filter_id ),
				'content_provider'     => $content_provider,
				'additional_providers' => $additional_providers,
				'apply_type'           => $apply_type,
				'filter_id'            => $filter_id,
				'hide_button'          => $hide_button,
				'button_text'          => $button_text,
				'button_icon'          => $button_icon,
				'button_icon_position' => $button_icon_position,
				'date_format'          => $date_format,
				'from_placeholder'     => $from,
				'to_placeholder'       => $to,
				'accessibility_label'  => $this->get_accessibility_label( $filter_id )
			);
		}
	}
}
