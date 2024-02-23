<?php
/**
 * Provider base class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Filter_Base' ) ) {
	/**
	 * Define Jet_Smart_Filters_Filter_Base class
	 */
	abstract class Jet_Smart_Filters_Filter_Base {
		/**
		 * Get filter name
		 */
		abstract public function get_name();

		/**
		 * Get filter ID
		 */
		abstract public function get_id();

		/**
		 * Get filter JS files
		 */
		abstract public function get_scripts();

		/**
		 * Return arguments
		 */
		public function get_args() {

			return array();
		}

		/**
		 * Get filtered provider content
		 */
		public function get_template( $args = array() ) {

			if ( isset( $args['dropdown_enabled'] ) && $args['dropdown_enabled'] ) {
				return jet_smart_filters()->get_template( 'common/filter-items-dropdown.php' );
			} else {
				return jet_smart_filters()->get_template( 'filters/' . $this->get_id() . '.php' );
			}
		}

		/**
		 * Get filter widget file
		 */
		public function widget() {

			return jet_smart_filters()->plugin_path( 'includes/widgets/' . $this->get_id() . '.php' );
		}

		/**
		 * Get custom query variable
		 */
		public function get_custom_query_var( $filter_id ) {

			$custom_query_var = false;

			if ( filter_var( get_post_meta( $filter_id, '_is_custom_query_var', true ), FILTER_VALIDATE_BOOLEAN ) ) {
				$custom_query_var = get_post_meta( $filter_id, '_custom_query_var', true );
			}

			return $custom_query_var;
		}

		/**
		 * Get filter accessibility label
		 */
		public function get_accessibility_label( $filter_id ) {

			$label = get_post_meta( $filter_id, '_filter_label', true );

			if ( !$label ) {
				$label = get_the_title( $filter_id );
			}

			return $label;
		}
	}
}
