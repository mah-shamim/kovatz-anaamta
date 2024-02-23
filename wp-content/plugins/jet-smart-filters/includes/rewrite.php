<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Rewrite_Rules' ) ) {
	/**
	 * Define Jet_Smart_Filters_Rewrite_Rules class
	 */
	class Jet_Smart_Filters_Rewrite_Rules {

		/**
		 * Jet Smart Filters query pattern
		 */
		private $pattern = 'jsf/(.*)/?$';

		/**
		 * Jet Smart Filters query variable
		 */
		private $query_var = 'jsf';

		/**
		 * Jet Smart Filters query variable value
		 *
		 * @var string
		 */
		private $query_var_val = null;

		/**
		 * Contains queried filter value
		 *
		 * @var string|null
		 */
		private $queried_filter = null;

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			add_filter( 'rewrite_rules_array',  array( $this, 'register_rewrites' ) );
			add_filter( 'query_vars', array( $this, 'register_variables' ) );
			add_action( 'updated_option', array( $this, 'rewrite_rules_after_updated_option' ), 10, 3 );
			add_action( 'parse_request', array( $this, 'store_queried_filter' ), 9999 );
			add_action( 'parse_query', array( $this, 'restore_queried_filter_var' ), -1 );
		}

		/**
		 * Save qurrently quried filter.
		 * Also unset filters query variable from $wp object to avoid static front page and custom permalinks bugs
		 */
		public function store_queried_filter( &$wp ) {

			if ( ! empty( $wp->query_vars[ $this->query_var ] ) ) {
				$this->queried_filter = $wp->query_vars[ $this->query_var ];
				unset( $wp->query_vars[ $this->query_var ] );
			}
		}

		/**
		 * Restore queried filters variable
		 * This hook is required to fix front page redirect bug
		 */
		public function restore_queried_filter_var( &$wp_query ) {

			if ( null !== $this->queried_filter ) {
				remove_action( 'template_redirect', 'redirect_canonical' );
				remove_action( 'parse_query', array( $this, 'restore_queried_filter_var' ) );
				$wp_query->query_vars[ $this->query_var ] = $this->queried_filter;
				$wp_query->query[ $this->query_var ] = $this->queried_filter;
			}
		}

		public function register_rewrites( $rules ) {

			$rewrites = array(
				$this->pattern => 'index.php?' . $this->query_var . '=$matches[1]'
			);

			if ( class_exists( 'WooCommerce' ) ) {
				$shop_page_id = wc_get_page_id( 'shop' );

				if ( current_theme_supports( 'woocommerce' ) ) {
					$shop_page_slug = $shop_page_id && get_post( $shop_page_id ) ? urldecode( get_page_uri( $shop_page_id ) ) : 'shop';
					$rewrites[$shop_page_slug . '/' . $this->pattern] = 'index.php?post_type=product&' . $this->query_var . '=$matches[1]';
				}
			}

			$rewritable_post_types = jet_smart_filters()->settings->get( 'rewritable_post_types' );
			if ( is_array( $rewritable_post_types ) ) {
				foreach ( $rewritable_post_types as $post_type => $post_type_enabled ) {
					if ( $post_type_enabled === 'true' ) {
						$post_type_object = get_post_type_object( $post_type );

						if ( ! $post_type_object ) {
							continue;
						}

						$rewrite_slug = ! empty( $post_type_object->rewrite['slug'] ) ? $post_type_object->rewrite['slug'] : false;
						$taxonomies   = get_object_taxonomies( $post_type_object->name, 'objects ' );

						if ( $rewrite_slug ) {
							$rewrites["$rewrite_slug/$this->pattern"] = 'index.php?post_type=' . $post_type . '&jsf=$matches[1]';
						}

						foreach ( $taxonomies as $taxonomy ) {
							$tax_rewrite_slug = ! empty( $taxonomy->rewrite['slug'] ) ? $taxonomy->rewrite['slug'] : false;

							if ( $tax_rewrite_slug ) {
								// product cat & tag default taxonomy
								if ( $post_type_object->name === 'product' ) {
									if ( in_array( $taxonomy->name, array( 'product_cat', 'product_tag' ) ) ) {
										$rewrites["$tax_rewrite_slug/(.+?)/$this->pattern"] =  'index.php?' . $taxonomy->name . '=$matches[1]&jsf=$matches[2]';

										continue;
									}
								}

								// custom taxonomy
								$rewrites["$tax_rewrite_slug/(.+?)/$this->pattern"] =  'index.php?taxonomy=' . $taxonomy->name . '&term=$matches[1]&jsf=$matches[2]';
							}
						}
					}
				}
			}

			$rewrites['([0-9]+)/' . $this->pattern] = 'index.php?p=$matches[1]&' . $this->query_var . '=$matches[2]';
			$rewrites['(.?.+?)/' . $this->pattern]  = 'index.php?pagename=$matches[1]&' . $this->query_var . '=$matches[2]';
			$rewrites['([^/]+)/' . $this->pattern]  = 'index.php?name=$matches[1]&' . $this->query_var . '=$matches[2]';

			return array_merge( $rewrites, $rules );
		}

		public function register_variables( $vars ) {

			$vars[] = $this->query_var;

			return $vars;
		}

		public function rewrite_rules_after_updated_option( $option, $old_value, $value ) {

			if ( $option !== jet_smart_filters()->settings->key ) {
				return;
			}

			$update_permalinks = false;

			if ( $value['url_structure_type'] === 'permalink' && $old_value['url_structure_type'] !== 'permalink' ) {
				$update_permalinks = true;
			} else if ( $value['rewritable_post_types'] !== $old_value['rewritable_post_types'] ) {
				$update_permalinks = true;
			}

			// update permalinks
			if ( $update_permalinks ) {
				flush_rewrite_rules();
			}
		}
	}
}
