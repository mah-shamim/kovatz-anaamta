<?php
/**
 * Relevanssi compatibility package
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Relevanssi_Package' ) ) {

	/**
	 * Define Jet_Engine_Relevanssi_Package class
	 */
	class Jet_Engine_Relevanssi_Package {

		public function __construct() {
			add_filter( 'jet-engine/query-builder/types/posts-query/wp-query', array( $this, 'modify_search_query' ) );
			add_action( 'jet-engine/query-builder/query/after-query-setup', array( $this, 'remove_relevanssi_jetsmartfilters_filter' ) );

		}

		public function modify_search_query( $query ) {

			if ( is_search() && $query->query_vars['s'] ) {
				relevanssi_do_query( $query );
			}

			return $query;
		}

		public function remove_relevanssi_jetsmartfilters_filter( $query ) {

			if ( 'posts' !== $query->query_type ) {
				return;
			}

			if ( ! is_search() || empty( $query->final_query['s'] ) ) {
				return;
			}

			if ( ! function_exists( 'jet_smart_filters' ) ) {
				return;
			}

			if ( ! Jet_Engine\Query_Builder\Manager::instance()->listings->filters->is_filters_request( $query ) ) {
				return;
			}

			remove_action( 'pre_get_posts', 'relevanssi_jetsmartfilters', 9999 );
		}

	}

}

new Jet_Engine_Relevanssi_Package();
