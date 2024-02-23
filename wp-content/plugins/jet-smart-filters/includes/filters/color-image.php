<?php
/**
 * Color/Image filter class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Color_Image_Filter' ) ) {
	/**
	 * Define Jet_Smart_Filters_Color_Image_Filter class
	 */
	class Jet_Smart_Filters_Color_Image_Filter extends Jet_Smart_Filters_Filter_Base {
		/**
		 * Get provider name
		 *
		 * @return string
		 */
		public function get_name() {

			return __( 'Visual', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID
		 */
		public function get_id() {

			return 'color-image';
		}

		/**
		 * Get icon URL
		 */
		public function get_icon_url() {

			return jet_smart_filters()->plugin_url( 'admin/assets/img/filter-types/color-image.png' );
		}

		/**
		 * Get provider wrapper selector
		 */
		public function get_scripts() {

			return false;
		}

		public function prepare_options( $options, $source ) {

			$_options = array();

			if ( empty( $options ) ) {
				return $_options;
			}

			foreach ( $options as $key => $option ) {

				if ( 'taxonomies' === $source || 'posts' === $source ) {
					$value = $option['selected_value'];
				} else {
					$value = $option['value'];
				}

				$_options[ $value ] = array(
					'image' => $option['source_image'],
					'color' => $option['source_color'],
					'label' => $option['label'],
				);

			}

			return $_options;
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

			$source     = get_post_meta( $filter_id, '_data_source', true );
			$type       = get_post_meta( $filter_id, '_color_image_type', true );
			$behavior   = get_post_meta( $filter_id, '_color_image_behavior', true );
			$options    = get_post_meta( $filter_id, '_source_color_image_input', true );
			$query_type = false;
			$query_var  = false;

			$options = $this->prepare_options( $options, $source );

			switch ( $source ) {
				case 'taxonomies':
					$tax              = get_post_meta( $filter_id, '_source_taxonomy', true );
					$only_child       = filter_var( get_post_meta( $filter_id, '_only_child', true ), FILTER_VALIDATE_BOOLEAN );
					$show_empty_terms = filter_var( get_post_meta( $filter_id, '_show_empty_terms', true ), FILTER_VALIDATE_BOOLEAN );
					$custom_query_var = $this->get_custom_query_var( $filter_id );

					$current_options = jet_smart_filters()->data->get_terms_for_options( $tax, $only_child, array(
						'hide_empty' => ! $show_empty_terms,
					) );
					$options = array_intersect_key( $options, $current_options );

					$query_type = 'tax_query';
					$query_var  = $tax;

					if ( $custom_query_var ) {
						$query_type = 'meta_query';
						$query_var  = $custom_query_var;
					}

					break;

				case 'posts':
					$query_type = 'meta_query';
					$query_var  = get_post_meta( $filter_id, '_query_var', true );
					break;

				case 'custom_fields':
					$custom_field    = get_post_meta( $filter_id, '_source_custom_field', true );
					$current_options = get_post_meta( get_the_ID(), $custom_field, true );
					$current_options = jet_smart_filters()->data->maybe_parse_repeater_options( $current_options );
					$query_type      = 'meta_query';
					$query_var       = get_post_meta( $filter_id, '_query_var', true );

					$options = array_intersect_key( $options, $current_options );
					break;

				default:
					$query_type = 'meta_query';
					$query_var  = get_post_meta( $filter_id, '_query_var', true );
					break;
			}

			// If radio behavior
			if ( $behavior === 'radio' ) {
				// Add all option
				$add_all_option = filter_var( get_post_meta( $filter_id, '_color_image_add_all_option', true ), FILTER_VALIDATE_BOOLEAN );

				if ( $add_all_option ) {
					$all_option_label = get_post_meta( $filter_id, '_color_image_add_all_option_lael', true );
					$all_option_image = get_post_meta( $filter_id, '_color_image_add_all_option_image', true );

					if ( $all_option_label || $all_option_image ) {
						$all_option = array(
							'label' => '',
							'color' => '',
							'image' => ''
						);

						if ( $all_option_label ) {
							$all_option['label'] = htmlspecialchars( $all_option_label );
						}
	
						if ( $all_option_image ) {
							$all_option['image'] = $all_option_image;
						}

						$options = array( 'all' => $all_option ) + $options;
					}
				}

				// Ability to deselect radio buttons
				$can_deselect = filter_var( get_post_meta( $filter_id, '_color_image_ability_deselect_radio', true ), FILTER_VALIDATE_BOOLEAN );
			}

			$options = apply_filters( 'jet-smart-filters/filters/filter-options', $options, $filter_id, $this );

			$result = array(
				'options'              => $options,
				'query_type'           => $query_type,
				'query_var'            => $query_var,
				'query_var_suffix'     => jet_smart_filters()->filter_types->get_filter_query_var_suffix( $filter_id ),
				'content_provider'     => $content_provider,
				'additional_providers' => $additional_providers,
				'apply_type'           => $apply_type,
				'filter_id'            => $filter_id,
				'type'                 => $type,
				'behavior'             => $behavior,
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

			if ( isset( $can_deselect ) ) {
				$result['can_deselect'] = $can_deselect;
			}

			return $result;
		}

		public function additional_filter_data_atts( $args ) {

			$additional_filter_data_atts = array();

			if ( ! empty( $args['can_deselect'] ) ) $additional_filter_data_atts['data-can-deselect'] = $args['can_deselect'];

			return $additional_filter_data_atts;
		}
	}
}
