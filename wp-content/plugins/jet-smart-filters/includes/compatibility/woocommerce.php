<?php
/**
 * Woocommerce compatibility class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Smart_Filters_Compatibility_Woocommerce class
 */
class Jet_Smart_Filters_Compatibility_WC {
	/**
	 * Constructor for the class
	 */
	function __construct() {
		add_action( 'jet-smart-filters/referrer/request', array( $this, 'setup_wc_product' ) );
		add_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'wc_modify_sort_query_args' ), 20 );
	}

	public function setup_wc_product() {

		global $wp;

		if ( ! function_exists( 'wc_setup_product_data' ) ) {
			return;
		}

		if ( empty( $wp->query_vars['post_type'] ) || 'product' !== $wp->query_vars['post_type'] ) {
			return;
		}

		if ( empty( $wp->query_vars['product'] ) ) {
			return;
		}

		$posts = get_posts( [
			'post_type' => 'product',
			'name' => $wp->query_vars['product'],
			'posts_per_page' => 1
		] );

		if ( empty( $posts ) ) {
			return;
		}

		global $post;
		$post = $posts[0];

		wc_setup_product_data( $post );

	}

	public function wc_modify_sort_query_args( $args ) {

		if ( ! isset( $args['jet_smart_filters'] ) || ! jet_smart_filters()->query->get_query_args() ) {
			return $args;
		}

		if ( isset( $args['wc_query'] ) ) {
			if ( isset( $args['orderby'] ) && isset( $args['order'] ) ) {
				$ordering_args = WC()->query->get_catalog_ordering_args( $args['orderby'], $args['order'] );

				// Prevent rewrite the order only to DESC if the orderby is relevance.
				if ( 'relevance' === $args['orderby'] && ! empty( $args['order'] ) ) {
					$ordering_args['order'] = $args['order'];
				}
			} else {
				$ordering_args = WC()->query->get_catalog_ordering_args();
			}

			$args['orderby'] = $ordering_args['orderby'];
			$args['order']   = $ordering_args['order'];

			if ( $ordering_args['meta_key'] ) {
				$args['meta_key'] = $ordering_args['meta_key'];
			}
		}

		return $args;

	}

}
