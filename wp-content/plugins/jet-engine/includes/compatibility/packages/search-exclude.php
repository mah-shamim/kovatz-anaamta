<?php
/**
 * Search Exclude compatibility package
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Search_Exclude_Package' ) ) {

	class Jet_Engine_Search_Exclude_Package {

		public function __construct() {
			add_filter( 'searchexclude_filter_search', array( $this, 'maybe_merged_post__not_in_arg' ), 10, 2 );
		}

		public function maybe_merged_post__not_in_arg( $exclude, $query ) {

			if ( $exclude ) {

				$post__not_in = $query->get( 'post__not_in' );

				if ( ! empty( $post__not_in ) && is_array( $post__not_in ) ) {

					$excluded_array = get_option( 'sep_exclude', array() );

					$query->set( 'post__not_in', array_merge( $post__not_in, $excluded_array ) );
					$exclude = false;
				}
			}

			return $exclude;
		}
	}

}

new Jet_Engine_Search_Exclude_Package();
