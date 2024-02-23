<?php
/**
 * Date Period filter class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Date_Period_Filter' ) ) {
	/**
	 * Define Jet_Smart_Filters_Date_Period_Filter class
	 */
	class Jet_Smart_Filters_Date_Period_Filter extends Jet_Smart_Filters_Filter_Base {
		/**
		 * Get provider name
		 */
		public function get_name() {

			return __( 'Date Period', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID156
		 */
		public function get_id() {

			return 'date-period';
		}

		/**
		 * Get icon URL
		 */
		public function get_icon_url() {

			return jet_smart_filters()->plugin_url( 'admin/assets/img/filter-types/date-period.png' );
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
			$hide_button          = isset( $args['hide_button'] ) ? $args['hide_button'] : false;
			$button_icon_position = isset( $args['button_icon_position'] ) ? $args['button_icon_position'] : 'left';

			if ( ! $filter_id ) {
				return false;
			}

			$query_type             = get_post_meta( $filter_id, '_date_source', true );
			$query_var              = $query_type === 'meta_query' ? get_post_meta( $filter_id, '_query_var', true ) : '';
			$period_type            = get_post_meta( $filter_id, '_date_period_type', true );
			$datepicker_button_text = get_post_meta( $filter_id, '_date_period_datepicker_button_text', true );
			$start_end_enabled      = filter_var( get_post_meta( $filter_id, '_date_period_start_end_enabled', true ), FILTER_VALIDATE_BOOLEAN );
			$min_max_date_enabled   = filter_var( get_post_meta( $filter_id, '_min_max_date_period_enabled', true ), FILTER_VALIDATE_BOOLEAN );

			if ( $start_end_enabled ) {
				$date_format_start = get_post_meta( $filter_id, '_date_period_start_format', true );
				$date_separator    = get_post_meta( $filter_id, '_date_period_separator', true );
				$date_format_end   = get_post_meta( $filter_id, '_date_period_end_format', true );

				$date_format = htmlspecialchars( json_encode( array(
					'start'     => $date_format_start,
					'separator' => $date_separator,
					'end'       => $date_format_end,
				) ) );
			} else {
				$date_format = get_post_meta( $filter_id, '_date_period_format', true );
			}

			$result = array(
				'options'                => false,
				'query_type'             => $query_type,
				'query_var'              => $query_var,
				'query_var_suffix'       => jet_smart_filters()->filter_types->get_filter_query_var_suffix( $filter_id ),
				'content_provider'       => $content_provider,
				'additional_providers'   => $additional_providers,
				'apply_type'             => $apply_type,
				'filter_id'              => $filter_id,
				'hide_button'            => $hide_button,
				'button_text'            => $button_text,
				'button_icon'            => $button_icon,
				'button_icon_position'   => $button_icon_position,
				'period_type'            => $period_type,
				'datepicker_button_text' => $datepicker_button_text,
				'date_format'            => $date_format,
				'accessibility_label'    => $this->get_accessibility_label( $filter_id )
			);

			if ( $min_max_date_enabled ) {
				$min_date = get_post_meta( $filter_id, '_min_date_period', true );
				$max_date = get_post_meta( $filter_id, '_max_date_period', true );

				if ( $min_date ) {
					$result['min_date'] = $min_date;
				}

				if ( $max_date ) {
					$result['max_date'] = $max_date;
				}
			}

			return $result;
		}

		public function additional_filter_data_atts( $args ) {

			$additional_filter_data_atts = array();

			if ( ! empty( $args['period_type'] ) ) $additional_filter_data_atts['data-period-type'] = $args['period_type'];

			return $additional_filter_data_atts;
		}
	}
}
