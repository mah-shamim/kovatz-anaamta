<?php
/**
 * Search filter class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Search_Filter' ) ) {
	/**
	 * Define Jet_Smart_Filters_Search_Filter class
	 */
	class Jet_Smart_Filters_Search_Filter extends Jet_Smart_Filters_Filter_Base {
		/**
		 * Get provider name
		 */
		public function get_name() {

			return __( 'Search', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID
		 */
		public function get_id() {

			return 'search';
		}

		/**
		 * Get icon URL
		 */
		public function get_icon_url() {

			return jet_smart_filters()->plugin_url( 'admin/assets/img/filter-types/search.png' );
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
			$content_provider     = isset( $args['content_provider'] ) ? $args['content_provider'] : false;
			$additional_providers = isset( $args['additional_providers'] ) ? $args['additional_providers'] : false;
			$apply_type           = isset( $args['apply_type'] ) ? $args['apply_type'] : false;
			$button_text          = isset( $args['button_text'] ) ? $args['button_text'] : false;
			$button_icon          = isset( $args['button_icon'] ) ? $args['button_icon'] : false;
			$button_icon_position = isset( $args['button_icon_position'] ) ? $args['button_icon_position'] : 'left';
			$min_letters_count    = isset( $args['min_letters_count'] ) && $apply_type === 'ajax-ontyping' ? $args['min_letters_count'] : false;
			$hide_apply_button    = isset( $args['hide_apply_button'] ) ? $args['hide_apply_button'] : true;

			if ( ! $filter_id ) {
				return false;
			}

			$placeholder = get_post_meta( $filter_id, '_s_placeholder', true );
			$search_by   = get_post_meta( $filter_id, '_s_by', true );

			if ( ! $search_by ) {
				$search_by = 'default';
			}

			if ( 'default' === $search_by ) {
				$query_type = '_s';
				$query_var  = 'query';
			} else {
				$query_type = 'meta_query';
				$query_var  = get_post_meta( $filter_id, '_query_var', true );
			}

			return array(
				'options'              => false,
				'query_type'           => $query_type,
				'query_var'            => $query_var,
				'query_var_suffix'     => jet_smart_filters()->filter_types->get_filter_query_var_suffix( $filter_id ),
				'placeholder'          => $placeholder,
				'content_provider'     => $content_provider,
				'additional_providers' => $additional_providers,
				'apply_type'           => $apply_type,
				'filter_id'            => $filter_id,
				'button_text'          => $button_text,
				'button_icon'          => $button_icon,
				'hide_apply_button'    => $hide_apply_button,
				'button_icon_position' => $button_icon_position,
				'min_letters_count'    => $min_letters_count,
				'accessibility_label'  => $this->get_accessibility_label( $filter_id )
			);
		}
	}
}
