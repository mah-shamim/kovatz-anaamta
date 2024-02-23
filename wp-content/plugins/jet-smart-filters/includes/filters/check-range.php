<?php
/**
 * Checkboxes filter class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Check_Range_Filter' ) ) {
	/**
	 * Define Jet_Smart_Filters_Check_Range_Filter class
	 */
	class Jet_Smart_Filters_Check_Range_Filter extends Jet_Smart_Filters_Filter_Base {
		/**
		 * Get provider name
		 */
		public function get_name() {

			return __( 'Check Range Filter', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID
		 */
		public function get_id() {

			return 'check-range';
		}

		/**
		 * Get icon URL
		 */
		public function get_icon_url() {

			return jet_smart_filters()->plugin_url( 'admin/assets/img/filter-types/check-range.png' );
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

			$filter_id                   = $args['filter_id'];
			$content_provider            = isset( $args['content_provider'] ) ? $args['content_provider'] : false;
			$additional_providers        = isset( $args['additional_providers'] ) ? $args['additional_providers'] : false;
			$apply_type                  = isset( $args['apply_type'] ) ? $args['apply_type'] : false;
			$search_enabled              = isset( $args['search_enabled'] ) ? $args['search_enabled'] : false;
			$search_placeholder          = isset( $args['search_placeholder'] ) ? $args['search_placeholder'] : __( 'Search...', 'jet-smart-filters' );
			$less_items_count            = isset( $args['less_items_count'] ) ? $args['less_items_count'] : false;
			$more_text                   = isset( $args['more_text'] ) ? $args['more_text'] : __( 'More', 'jet-smart-filters' );
			$less_text                   = isset( $args['less_text'] ) ? $args['less_text'] : __( 'Less', 'jet-smart-filters' );
			$scroll_height               = isset( $args['scroll_height'] ) ? $args['scroll_height'] : false;
			$dropdown_enabled            = isset( $args['dropdown_enabled'] ) ? filter_var( $args['dropdown_enabled'], FILTER_VALIDATE_BOOLEAN ) : false;
			$dropdown_placeholder        = isset( $args['dropdown_placeholder'] ) ? $args['dropdown_placeholder'] : __( 'Select some options', 'jet-smart-filters' );
			$dropdown_n_selected_enabled = isset( $args['dropdown_n_selected_enabled'] ) ? $args['dropdown_n_selected_enabled'] : false;
			$dropdown_n_selected_number  = isset( $args['dropdown_n_selected_number'] ) ? $args['dropdown_n_selected_number'] : false;
			$dropdown_n_selected_text    = isset( $args['dropdown_n_selected_text'] ) ? $args['dropdown_n_selected_text'] : false;

			if ( ! $filter_id ) {
				return false;
			}

			$source       = get_post_meta( $filter_id, '_data_source', true );
			$raw_options  = get_post_meta( $filter_id, '_source_manual_input_range', true );
			$prefix       = get_post_meta( $filter_id, '_values_prefix', true );
			$suffix       = get_post_meta( $filter_id, '_values_suffix', true );
			$query_type   = 'meta_query';
			$query_var    = get_post_meta( $filter_id, '_query_var', true );
			$options      = array();
			$decimal_num  = get_post_meta( $filter_id, '_values_decimal_num', true );
			$decimal_sep  = get_post_meta( $filter_id, '_values_decimal_sep', true );
			$thousand_sep = get_post_meta( $filter_id, '_values_thousand_sep', true );
			$format       = array(
				'decimal_num'   => $decimal_num ? absint( $decimal_num ) : 0,
				'decimal_sep'   => $decimal_sep ? $decimal_sep : '.',
				'thousands_sep' => $thousand_sep ? $thousand_sep : ''
			);

			if ( ! empty( $raw_options ) ) {
				foreach ( $raw_options as $option ) {

					$min = ! empty( $option['min'] ) ? $option['min'] : 0;
					$max = ! empty( $option['max'] ) || $option['max'] === '0' ? $option['max'] : 100;
					$key = $min . '_' . $max;
					$min = trim( $min );
					$max = trim( $max );

					$min = number_format(
						floatval($min),
						$format['decimal_num'],
						$format['decimal_sep'],
						$format['thousands_sep']
					);

					$max = number_format(
						floatval($max),
						$format['decimal_num'],
						$format['decimal_sep'],
						$format['thousands_sep']
					);

					$value = $prefix . $min . $suffix . ' — ' . $prefix . $max . $suffix;

					$options[ $key ] = $value;
				}
			}

			$options = apply_filters( 'jet-smart-filters/filters/filter-options', $options, $filter_id, $this );

			$result = array(
				'options'              => $options,
				'query_type'           => $query_type,
				'query_var'            => $query_var,
				'prefix'               => $prefix,
				'suffix'               => $suffix,
				'format'               => $format,
				'query_var_suffix'     => jet_smart_filters()->filter_types->get_filter_query_var_suffix( $filter_id ),
				'content_provider'     => $content_provider,
				'additional_providers' => $additional_providers,
				'apply_type'           => $apply_type,
				'filter_id'            => $filter_id,
				'search_enabled'       => $search_enabled,
				'search_placeholder'   => $search_placeholder,
				'less_items_count'     => $less_items_count,
				'more_text'            => $more_text,
				'less_text'            => $less_text,
				'scroll_height'        => $scroll_height,
				'dropdown_enabled'     => $dropdown_enabled,
				'dropdown_placeholder' => $dropdown_placeholder,
				'accessibility_label'  => $this->get_accessibility_label( $filter_id )
			);

			if ( $dropdown_n_selected_enabled ) {
				$result['dropdown_n_selected_enabled'] = $dropdown_n_selected_enabled;
				$result['dropdown_n_selected_number']  = $dropdown_n_selected_number;
				$result['dropdown_n_selected_text']    = $dropdown_n_selected_text;
			}

			return $result;
		}
	}
}
