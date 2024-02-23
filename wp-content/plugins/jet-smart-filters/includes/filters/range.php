<?php
/**
 * Range filter class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Range_Filter' ) ) {
	/**
	 * Define Jet_Smart_Filters_Range_Filter class
	 */
	class Jet_Smart_Filters_Range_Filter extends Jet_Smart_Filters_Filter_Base {
		/**
		 * Get provider name
		 */
		public function get_name() {

			return __( 'Range', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID
		 */
		public function get_id() {

			return 'range';
		}

		/**
		 * Get icon URL
		 */
		public function get_icon_url() {

			return jet_smart_filters()->plugin_url( 'admin/assets/img/filter-types/range.png' );
		}

		/**
		 * Get provider wrapper selector
		 */
		public function get_scripts() {

			return false;
		}

		private function max_value_for_current_step( $max, $min, $step ) {

			if ( $step === 1 ) {
				return $max;
			}

			$steps_count = ceil( ( $max - $min ) / $step );

			return $steps_count * $step + $min;
		}

		/**
		 * Prepare filter template argumnets
		 */
		public function prepare_args( $args ) {

			$filter_id            = $args['filter_id'];
			$content_provider     = isset( $args['content_provider'] ) ? $args['content_provider'] : false;
			$additional_providers = isset( $args['additional_providers'] ) ? $args['additional_providers'] : false;
			$apply_type           = isset( $args['apply_type'] ) ? $args['apply_type'] : false;

			if ( ! $filter_id ) {
				return false;
			}

			$query_type                = 'meta_query';
			$query_var                 = get_post_meta( $filter_id, '_query_var', true );
			$inputs_enabled            = filter_var( get_post_meta( $filter_id, '_range_inputs_enabled', true ), FILTER_VALIDATE_BOOLEAN );
			$inputs_separators_enabled = filter_var( get_post_meta( $filter_id, '_range_inputs_separators_enabled', true ), FILTER_VALIDATE_BOOLEAN );
			$prefix                    = get_post_meta( $filter_id, '_values_prefix', true );
			$suffix                    = get_post_meta( $filter_id, '_values_suffix', true );
			$source_cb                 = get_post_meta( $filter_id, '_source_callback', true );
			$min                       = false;
			$max                       = false;
			$step                      = get_post_meta( $filter_id, '_source_step', true );
			$decimal_num               = get_post_meta( $filter_id, '_values_decimal_num', true );
			$decimal_sep               = get_post_meta( $filter_id, '_values_decimal_sep', true );
			$thousand_sep              = get_post_meta( $filter_id, '_values_thousand_sep', true );
			$format                    = array(
				'decimal_num'   => $decimal_num ? absint( $decimal_num ) : 0,
				'decimal_sep'   => $decimal_sep ? $decimal_sep : '.',
				'thousands_sep' => $thousand_sep ? $thousand_sep : ''
			);

			if ( ! $step ) {
				$step = 1;
			}

			if ( is_callable( $source_cb ) ) {
				$data = call_user_func( $source_cb, array( 'key' => $query_var ) );
				$min  = isset( $data['min'] ) ? $data['min'] : false;
				$max  = isset( $data['max'] ) ? $this->max_value_for_current_step( $data['max'], $min, $step ) : false;
			}

			if ( false === $min ) {
				$min = (float)get_post_meta( $filter_id, '_source_min', true );
			}

			if ( false === $max ) {
				$max = get_post_meta( $filter_id, '_source_max', true );

				if ( $max === '' ) {
					$max = 100;
				} else {
					$max = (float)$max;
				}
			}

			return array(
				'options'                   => false,
				'min'                       => $min,
				'max'                       => $max,
				'step'                      => $step,
				'format'                    => $format,
				'query_type'                => $query_type,
				'query_var'                 => $query_var,
				'query_var_suffix'          => jet_smart_filters()->filter_types->get_filter_query_var_suffix( $filter_id ),
				'content_provider'          => $content_provider,
				'additional_providers'      => $additional_providers,
				'apply_type'                => $apply_type,
				'inputs_enabled'            => $inputs_enabled,
				'inputs_separators_enabled' => $inputs_separators_enabled,
				'prefix'                    => jet_smart_filters_macros( $prefix ),
				'suffix'                    => jet_smart_filters_macros( $suffix ),
				'filter_id'                 => $filter_id,
				'accessibility_label'       => $this->get_accessibility_label( $filter_id )
			);
		}

		public function additional_filter_data_atts( $args ) {

			$additional_filter_data_atts = array();

			if ( ! empty( $args['format'] ) ) {
				$additional_filter_data_atts['data-format'] = $args['format'];
			}

			return $additional_filter_data_atts;
		}
	}
}
