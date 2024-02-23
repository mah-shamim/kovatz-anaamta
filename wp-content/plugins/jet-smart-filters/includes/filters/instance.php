<?php
/**
 * New filter instance class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Filter_Instance' ) ) {

	class Jet_Smart_Filters_Filter_Instance {

		public $type      = null;
		public $args      = null;
		public $filter_id = null;
		public $hierarchy = null;
		public $depth     = null;

		/**
		 * Constructor for the class
		 */
		public function __construct( $filter_id = 0, $filter_type = null, $args = array() ) {

			$this->filter_id   = $filter_id;
			$this->filter_type = $filter_type;
			$this->type        = jet_smart_filters()->filter_types->get_filter_types( $filter_type );

			$args['filter_id'] = $this->filter_id;
			$this->args        = $this->type->prepare_args( $args );

			$this->args['query_id']        = isset( $args['query_id'] ) ? $args['query_id'] : 'default';
			$this->args['show_label']      = isset( $args['show_label'] ) ? $args['show_label'] : false;
			$this->args['display_options'] = isset( $args['display_options'] ) ? $args['display_options'] : array();

			/**
			 * Allow to filter instatnce args from 3rd party
			 */
			$this->args = apply_filters( 'jet-smart-filters/filter-instance/args', $this->args, $this );

			if ( isset( $args['apply_indexer'] ) ) {
				jet_smart_filters()->indexer->add_filter( $this->args );
			}
		}

		/**
		 * Returns current instance arguments
		 */
		public function get_args() {

			return $this->args;
		}

		/**
		 * Return single argument from the arguments list
		 */
		public function get_arg( $key = null ) {

			$args = $this->get_args();

			return isset( $args[ $key ] ) ? $args[ $key ] : false;
		}

		/**
		 * Returns current instance filter ID
		 */
		public function get_filter_id() {

			return $this->filter_id;
		}

		public function get_query_var( $args = null ) {

			if ( ! $args ) {
				$args = $this->get_args();
			}

			$query_var = sprintf( '_%s_%s', $args['query_type'], $args['query_var'] );

			if ( false !== $args['query_var_suffix'] ) {
				$query_var .= '|' . $args['query_var_suffix'];
			}

			return $query_var;
		}

		/**
		 * Return current filter value from request by filter arguments
		 */
		public function get_current_filter_value( $args = array() ) {

			$query_var = $this->get_query_var( $args );

			if ( isset( $_REQUEST[ $query_var ] ) ) {
				return $_REQUEST[ $query_var ];
			}

			if ( isset( $args['current_value'] ) ) {
				return $args['current_value'];
			}

			return false;
		}

		/**
		 * Print required data-attributes for filter container
		 */
		public function filter_data_atts( $args ) {

			$provider             = ! empty( $args['content_provider'] ) ? $args['content_provider'] : '';
			$additional_providers = ! empty( $args['additional_providers'] ) ? $args['additional_providers'] : '';
			$query_id             = ! empty( $args['query_id'] ) ? $args['query_id'] : 'default';
			$filter_id            = ! empty( $args['filter_id'] ) ? $args['filter_id'] : 0;
			$active_label         = get_post_meta( $filter_id, '_active_label', true );

			$atts = array(
				'data-query-type'           => $args['query_type'],
				'data-query-var'            => $args['query_var'],
				'data-smart-filter'         => $this->type->get_id(),
				'data-filter-id'            => $filter_id,
				'data-apply-type'           => $args['apply_type'],
				'data-content-provider'     => $provider,
				'data-additional-providers' => $additional_providers,
				'data-query-id'             => $query_id,
				'data-active-label'         => htmlspecialchars($active_label),
				'data-layout-options'       => array(
					'show_label'      => ! empty( $args['show_label'] ) ? $args['show_label'] : '',
					'display_options' => ! empty( $args['display_options'] ) ? $args['display_options'] : array(),
				),
			);

			if ( isset( $args['query_var_suffix'] ) ) {
				$atts['data-query-var-suffix'] = $args['query_var_suffix'];
			}

			if ( ! empty( $args['is_hierarchical'] ) ) {
				$atts['data-hierarchical'] = true;
			}

			if ( ! empty( $args['relational_operator'] ) && 'OR' !== $args['relational_operator'] ) {
				$atts['data-relational-operator'] = $args['relational_operator'];
			}

			if ( ! empty( $args['dropdown_enabled'] ) ) {
				$additional_filter_data_atts['data-dropdown-enabled'] = $args['dropdown_enabled'];
				$additional_filter_data_atts['data-dropdown-placeholder'] = isset( $args['dropdown_placeholder'] ) ? $args['dropdown_placeholder'] : __( 'Select some options', 'jet-smart-filters' );
			}

			if ( ! empty( $args['inputs_separators_enabled'] ) && ! empty( $args['inputs_enabled'] ) ) {
				$atts['data-inputs-separators'] = $args['inputs_separators_enabled'];
			}

			if ( method_exists( $this->type, 'additional_filter_data_atts' ) ) {
				$atts = array_merge( $atts, $this->type->additional_filter_data_atts( $args ) );
			}

			echo $this->get_atts_string( $atts );
		}

		/**
		 * Return HTML attributes string from key=>value array
		 */
		public function get_atts_string( $atts ) {

			$result = array();

			foreach ( $atts as $key => $value ) {

				if ( is_array( $value ) ) {
					$value = htmlspecialchars( json_encode( $value ) );
				}

				$result[] = sprintf( '%1$s="%2$s"', $key, $value );
			}

			return implode( ' ', $result );
		}

		/**
		 * Render filter of current instance
		 */
		public function render() {

			if ( empty( $this->type->get_template() ) || ! file_exists( $this->type->get_template() ) ) {
				return;
			}

			$args = $this->args;

			if ( ! empty( $args['is_hierarchical'] ) ) {

				if ( ! class_exists( 'Jet_Smart_Filters_Hierarchy' ) ) {
					require jet_smart_filters()->plugin_path( 'includes/hierarchy.php' );
				}

				$hierarchy = new Jet_Smart_Filters_Hierarchy( $this, false, array(), $args, true );
				$levels    = $hierarchy->get_levels();

				include jet_smart_filters()->get_template( 'filters/hierarchical.php' );
			} else {
				include $this->type->get_template( $args );
			}
		}

		/**
		 * Returns rendered tempalte for current type
		 */
		public function get_rendered_template( $args = array() ) {

			ob_start();
			include $this->type->get_template( $args );

			return ob_get_clean();
		}
	}
}
