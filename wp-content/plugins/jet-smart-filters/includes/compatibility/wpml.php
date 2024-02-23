<?php
/**
 * Compatibility filters and actions
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Smart_Filters_Compatibility_WMPL class
 */
class Jet_Smart_Filters_Compatibility_WMPL {

	/**
	 * Constructor for the class
	 */
	function __construct() {

		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) && ! defined( 'WPML_ST_VERSION' ) ) {
			return;
		}
		
		add_filter( 'wcml_multi_currency_ajax_actions', array( $this, 'add_action_to_multi_currency_ajax' ) );
		add_filter( 'jet-smart-filters/render_filter_template/filter_id', array( $this, 'modify_filter_id' ) );
		add_filter( 'jet-smart-filters/filters/posts-source/args', array( $this, 'modify_posts_source_args' ) );

		//convert current currency to default
		add_filter( 'jet-smart-filters/query/final-query', array( $this, 'wpml_wc_convert_currency' ) );

		// For Indexer
		add_filter( 'jet-smart-filters/indexer/tax-query-args', array( $this, 'remove_wpml_terms_filters' ) );

	}

	public function add_action_to_multi_currency_ajax( $ajax_actions = array() ) {

		$ajax_actions[] = 'jet_smart_filters';
		
		return $ajax_actions;
	}

	public function modify_filter_id( $filter_id ) {

		return apply_filters( 'wpml_object_id', $filter_id, jet_smart_filters()->post_type->slug(), true );
	}

	public function modify_posts_source_args( $args ) {

		if ( isset( $args['post_type'] ) ) {
			$is_translated_post_type = apply_filters( 'wpml_is_translated_post_type', null, $args['post_type'] );

			if ( $is_translated_post_type ) {
				$args['suppress_filters'] = false;
			}
		}

		return $args;
	}

	public function remove_wpml_terms_filters( $args ) {

		global $sitepress;

		remove_filter( 'get_term',       array( $sitepress, 'get_term_adjust_id' ), 1 );
		remove_filter( 'get_terms_args', array( $sitepress, 'get_terms_args_filter' ), 10 );
		remove_filter( 'terms_clauses',  array( $sitepress, 'terms_clauses' ), 10 );

		$args['suppress_filters'] = true;

		return $args;
	}

	public function wpml_wc_convert_currency( $args ) {

		global $woocommerce_wpml;
		$providers = strtok( $args['jet_smart_filters'], '/' );

		if ( $woocommerce_wpml && $woocommerce_wpml->multi_currency && in_array( $providers, ['jet-woo-products-grid', 'jet-woo-products-list', 'epro-products', 'epro-archive-products', 'woocommerce-shortcode', 'woocommerce-archive'] ) ) {
			if ( $currency !== wcml_get_woocommerce_currency_option() ) {
				if ( ! empty( $args['meta_query'] ) && is_array( $args['meta_query'] ) ) {
					for ( $i = 0; $i < count( $args['meta_query'] ); $i++ ) {
						if ( $args['meta_query'][$i]['key'] === '_price' && ! empty( $args['meta_query'][$i]['value'] ) ) {
							if ( is_array( $args['meta_query'][$i]['value'] ) ) {
								$min_price_in_default_currency = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount($args['meta_query'][$i]['value'][0]);
								$max_price_in_default_currency = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount($args['meta_query'][$i]['value'][1]);

								$args['meta_query'][$i]['value'][0] = $min_price_in_default_currency;
								$args['meta_query'][$i]['value'][1] = $max_price_in_default_currency;
							}

							$i = count( $args['meta_query'] );
						}
					}
				}
			}
		}

		return $args;
	}

}
