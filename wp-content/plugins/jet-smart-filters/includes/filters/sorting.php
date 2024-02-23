<?php
/**
 * Sorting filter class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Sorting_Filter' ) ) {
	/**
	 * Define Jet_Smart_Filters_Sorting_Filter class
	 */
	class Jet_Smart_Filters_Sorting_Filter {

		/**
		 * Get provider ID
		 */
		public function get_id() {

			return 'sorting';
		}

		/**
		 * Returns sorting data options
		 */
		public function sorting_options( $options_list = array() ) {

			$options  = array();
			$currentFromRequest = isset( $_REQUEST['_sort_standard'] )
				? json_decode( str_replace( '\"', '"', $_REQUEST['_sort_standard'] ), true )
				: false;

			foreach ( $options_list as $option_data ) {
				$option_value = $this->option_value( $option_data );
				$option = array(
					'title' => $option_data['title'],
					'value' => htmlspecialchars( json_encode( $option_value ) )
				);

				if ( $currentFromRequest === $option_value ) {
					$option['current'] = true;
				}

				if ( !empty( $option['title'] ) && !empty( $option['value'] ) ) {
					$options[] = $option;
				}
			}

			return $options;
		}

		/**
		 * Retrun orderby data options
		 */
		public function orderby_options() {

			$options_list = array(
				'none'          => __( 'No order (by ID)', 'jet-smart-filters' ),
				'rand'          => __( 'Random', 'jet-smart-filters' ),
				'author'        => __( 'Author', 'jet-smart-filters' ),
				'title'         => __( 'Title', 'jet-smart-filters' ),
				'name'          => __( 'Name (post slug)', 'jet-smart-filters' ),
				'date'          => __( 'Date', 'jet-smart-filters' ),
				'modified'      => __( 'Last modified date', 'jet-smart-filters' ),
				'comment_count' => __( 'Number of comments', 'jet-smart-filters' ),
			);

			if ( class_exists( 'WooCommerce' ) ) {
				$options_list += array(
					'price'          => __( 'Product price', 'jet-smart-filters' ),
					'sales_number'   => __( 'Product sales number', 'jet-smart-filters' ),
					'rating'         => __( 'Product rating', 'jet-smart-filters' ),
					'reviews_number' => __( 'Product reviews number', 'jet-smart-filters' ),
				);
			}

			$options_list['meta_value']     = __( 'Meta key', 'jet-smart-filters' );
			$options_list['meta_value_num'] = __( 'Meta key numeric', 'jet-smart-filters' );

			if ( class_exists( 'Jet_Engine' ) ) {
				$options_list['clause_value'] = __( 'Clause key', 'jet-smart-filters' );
			}

			return $options_list;
		}

		/**
		 * Returns option data value
		 */
		private function option_value( $option ) {

			$output = array();

			if ( in_array( $option['orderby'], ['none', 'rand'] ) ) {
				$option['order'] = '';
			}

			foreach ( ['orderby', 'order', 'meta_key'] as $key ) {
				if ( ! empty( $option[$key] ) ) {
					$output[$key] = $option[$key];
				}
			}

			return $output;
		}

		/**
		 * Sorting container data attributes
		 */
		public function container_data_atts( $settings = array() ) {

			$output = '';

			if ( 'submit' === $settings['apply_on'] && in_array( $settings['apply_type'], ['ajax', 'mixed'] ) ) {
				$apply_type = $settings['apply_type'] . '-reload';
			} else {
				$apply_type = $settings['apply_type'];
			}

			$data_atts = array(
				'data-smart-filter'         => 'sorting',
				'data-query-type'           => 'sort',
				'data-query-var'            => 'standard',
				'data-content-provider'     => ! empty( $settings['content_provider'] ) ? $settings['content_provider'] : '',
				'data-additional-providers' => jet_smart_filters()->utils->get_additional_providers( $settings ),
				'data-query-id'             => ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default',
				'data-apply-type'           => $apply_type,
			);

			foreach ( $data_atts as $key => $value ) {
				$output .= sprintf( ' %1$s="%2$s"', $key, $value );
			}

			return $output;
		}
	}
}
