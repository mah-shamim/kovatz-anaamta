<?php

namespace Jet_Woo_Builder;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

trait Products_Shortcode_Trait {

	/**
	 * Query.
	 *
	 * Returns products.
	 *
	 * @since  1.1.0
	 * @since  2.0.4 Added `jet-woo-builder/shortcodes/ . $this->get_tag() . /query-type/query-args` hook. New query type
	 *         `stock_status`.
	 * @access public
	 *
	 * @return null|array
	 */
	public function query() {

		$settings       = $this->get_settings();
		$query_products = apply_filters( 'jet-woo-builder/shortcodes/query-products', false, $settings, $this );

		if ( $query_products ) {
			return $query_products;
		}

		$defaults = apply_filters( 'jet-woo-builder/shortcodes/' . $this->get_tag() . '/query-args', [
			'post_status'   => 'publish',
			'post_type'     => 'product',
			'no_found_rows' => 1,
			'meta_query'    => [],
			'tax_query'     => [
				'relation' => 'AND',
			],
		], $this );

		$query_id = $this->get_attr( 'query_id' );

		if ( ! empty( $query_id ) ) {
			add_action( 'pre_get_posts', [ $this, 'pre_get_products_query_filter' ] );
		}

		if ( 'yes' === $this->get_attr( 'use_current_query' ) ) {
			global $wp_query;

			if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() ) {
				$wp_query->set( 'jet_use_current_query', 'yes' );
				$wp_query->set( 'posts_per_page', intval( $this->get_attr( 'number' ) ) );

				$query_args = wp_parse_args( $wp_query->query_vars, $defaults );
				$query_args = apply_filters( 'jet-woo-builder/shortcodes/' . $this->get_tag() . '/query-args', $query_args, $this );
				$query_args = apply_filters( 'jet-woo-builder/shortcodes/' . $this->get_tag() . '/final-query-args', $query_args, $this );
				$query_args = jet_woo_builder_tools()->get_wc_catalog_ordering_args( $query_args );
				$query_args = WC()->query->price_filter_post_clauses( $query_args, $wp_query );
				$query      = new \WP_Query( $query_args );

				return $query->posts;
			}
		}

		$query_type                  = explode( ',', str_replace( ' ', '', $this->get_attr( 'products_query' ) ) );
		$product_visibility_term_ids = wc_get_product_visibility_term_ids();
		$viewed_products             = ! empty( $_COOKIE['woocommerce_recently_viewed'] ) ? (array)explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) : [];
		$viewed_products             = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );

		$query_args['posts_per_page'] = intval( $this->get_attr( 'number' ) );

		for ( $i = 0; $i < count( $query_type ); $i++ ) {
			if ( ( 'viewed' === $query_type[ $i ] ) && empty( $viewed_products ) ) {
				return null;
			}

			if ( $this->is_linked_products( $query_type[ $i ] ) ) {
				global $product;

				if ( ! is_a( $product, 'WC_Product' ) && ! ( jet_woo_builder()->documents->is_document_type( 'cart' ) || is_cart() ) ) {
					return null;
				}

				switch ( $query_type[ $i ] ) {
					case 'related':
						$query_args['post__in'] = wc_get_related_products( $product->get_id(), $query_args['posts_per_page'], $product->get_upsell_ids() );
						$query_args['orderby']  = 'post__in';
						break;
					case 'up-sells':
						$query_args['post__in'] = $product->get_upsell_ids();
						$query_args['orderby']  = 'post__in';
						break;
					case 'cross-sells':
						$query_args['post__in'] = ( jet_woo_builder()->documents->is_document_type( 'cart' ) || is_cart() ) ? WC()->cart->get_cross_sells() : $product->get_cross_sell_ids();
						$query_args['orderby']  = 'post__in';
						break;
				}

				if ( empty( $query_args['post__in'] ) ) {
					return null;
				}
			}

			switch ( $query_type[ $i ] ) {
				case 'all':
					if ( '' !== $this->get_attr( 'products_exclude_ids' ) ) {
						$query_args['post__not_in'] = explode(
							',',
							str_replace( ' ', '', jet_woo_builder()->macros->do_macros( $this->get_attr( 'products_exclude_ids' ) ) )
						);
					}
					break;
				case 'category':
					if ( '' !== $this->get_attr( 'products_cat' ) ) {
						$query_args['tax_query'][] = [
							'taxonomy' => 'product_cat',
							'field'    => 'term_id',
							'terms'    => explode( ',', $this->get_attr( 'products_cat' ) ),
							'operator' => 'IN',
						];
					}
					if ( '' !== $this->get_attr( 'products_cat_exclude' ) ) {
						$query_args['tax_query'][] = [
							'taxonomy' => 'product_cat',
							'field'    => 'term_id',
							'terms'    => explode( ',', $this->get_attr( 'products_cat_exclude' ) ),
							'operator' => 'NOT IN',
						];
					}
					break;
				case 'tag':
					if ( '' !== $this->get_attr( 'products_tag' ) ) {
						$query_args['tax_query'][] = [
							'taxonomy' => 'product_tag',
							'field'    => 'term_id',
							'terms'    => explode( ',', $this->get_attr( 'products_tag' ) ),
							'operator' => 'IN',
						];
					}
					break;
				case 'ids':
					if ( '' !== $this->get_attr( 'products_ids' ) ) {
						$query_args['post__in'] = explode(
							',',
							str_replace( ' ', '', jet_woo_builder()->macros->do_macros( $this->get_attr( 'products_ids' ) ) )
						);
					}
					break;
				case 'featured':
					$query_args['tax_query'][] = [
						'taxonomy' => 'product_visibility',
						'field'    => 'term_taxonomy_id',
						'terms'    => $product_visibility_term_ids['featured'],
					];
					break;
				case 'bestsellers':
					$query_args['meta_query'][] = [
						'key'     => 'total_sales',
						'value'   => 0,
						'compare' => '>',
					];
					break;
				case 'sale':
					$query_args['post__in'] = array_merge( [ 0 ], wc_get_product_ids_on_sale() );
					break;
				case 'viewed':
					$query_args['post__in'] = $viewed_products;
					$query_args['orderby']  = 'post__in';
					break;
				case 'custom_tax':
					if ( '' !== $this->get_attr( 'taxonomy_slug' ) ) {
						$query_args['tax_query'][] = [
							'taxonomy' => $this->get_attr( 'taxonomy_slug' ),
							'field'    => 'term_id',
							'terms'    => explode( ',', str_replace( ' ', '', jet_woo_builder()->macros->do_macros( $this->get_attr( 'taxonomy_id' ) ) ) ),
							'operator' => 'IN',
						];
					}
					break;
				case 'stock_status':
					$query_args['meta_query'][] = [
						'key'   => '_stock_status',
						'value' => explode( ',', $this->get_attr( 'products_stock_status' ) ),
					];
					break;
				case 'product_variation':
					$query_args['post_type'] = 'product_variation';

					if ( '' !== $this->get_attr( 'variation_post_parent_id' ) ) {
						$query_args['post_parent__in'] = explode( ',', str_replace( ' ', '', jet_woo_builder()->macros->do_macros( $this->get_attr( 'variation_post_parent_id' ) ) ) );
					}
					break;
				default:
					$query_args = apply_filters( 'jet-woo-builder/shortcodes/' . $this->get_tag() . '/query-type/query-args', $query_args, $query_type[ $i ], $this );
					break;
			}
		}

		switch ( $this->get_attr( 'products_orderby' ) ) {
			case 'id' :
				$query_args['orderby'] = 'ID';
				break;
			case 'modified' :
				$query_args['orderby'] = 'modified';
				break;
			case 'price' :
				$query_args['meta_key'] = '_price';
				$query_args['orderby']  = 'meta_value_num';
				break;
			case 'rand' :
				$query_args['orderby'] = 'rand';
				break;
			case 'sales' :
				$query_args['meta_key'] = 'total_sales';
				$query_args['orderby']  = 'meta_value_num';
				break;
			case 'rated':
				$query_args['meta_key'] = '_wc_average_rating';
				$query_args['orderby']  = 'meta_value_num';
				break;
			case 'current':
				$query_args = jet_woo_builder_tools()->get_wc_catalog_ordering_args( $query_args );
				break;
			case 'menu_order':
				$query_args['orderby'] = 'menu_order';
				break;
			case 'title':
				$query_args['orderby'] = 'title';
				break;
			case 'sku':
				$query_args['meta_key'] = '_sku';
				$query_args['orderby']  = 'meta_value';
				break;
			case 'stock_status':
				$query_args['meta_key'] = '_stock_status';
				$query_args['orderby']  = 'meta_value';
				break;
			default :
				$query_args['orderby'] = 'date';
		}

		switch ( $this->get_attr( 'products_order' ) ) {
			case 'desc':
				$query_args['order'] = 'DESC';
				break;
			case 'asc':
				$query_args['order'] = 'ASC';
				break;
			default :
				$query_args['order'] = 'DESC';
		}

		if ( 'yes' == get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
			$query_args['meta_query'][] = [
				'key'     => '_stock_status',
				'value'   => 'outofstock',
				'compare' => 'NOT LIKE',
			];
		}

		if ( 'yes' !== $this->get_attr( 'hidden_products' ) ) {
			$query_args['tax_query'][] = [
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => [ 'exclude-from-catalog' ],
				'operator' => 'NOT IN',
			];
		}

		$query_args = wp_parse_args( $query_args, $defaults );
		$query_args = apply_filters( 'jet-woo-builder/shortcodes/' . $this->get_tag() . '/final-query-args', $query_args, $this );
		$query      = new \WP_Query( $query_args );

		remove_action( 'pre_get_posts', [ $this, 'pre_get_products_query_filter' ] );

		return $query->posts;

	}

	public function pre_get_products_query_filter( $wp_query ) {
		if ( $this ) {
			do_action( 'jet-woo-builder/query/' . $this->get_attr( 'query_id' ), $wp_query, $this );
		}
	}

	/**
	 * Linked Products.
	 *
	 * Return true if linked products query type.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param $query_type
	 *
	 * @return bool
	 */
	public function is_linked_products( $query_type ) {

		if ( 'related' === $query_type || 'up-sells' === $query_type || 'cross-sells' === $query_type ) {
			return true;
		}

		return false;

	}

	/**
	 * Shortcode.
	 *
	 * @param null $content
	 *
	 * @return string
	 */
	public function _shortcode( $content = null ) {

		$query = $this->query();

		if ( ! $query || empty( $query ) || is_wp_error( $query ) ) {
			return jet_woo_builder_tools()->get_products_not_found_message( $this->get_tag(), $this->get_attr( 'not_found_message' ), $this );
		}

		global $product;

		ob_start();

		// Hook before loop start template included.
		do_action( 'jet-woo-builder/shortcodes/' . $this->get_tag() . '/loop-start' );

		include $this->get_template( 'loop-start' );

		foreach ( $query as $_product ) {
			if ( is_a( $_product, 'WC_Product' ) ) {
				$product = $_product;
			} else {
				setup_postdata( $_product );
			}

			// Hook before loop item template included.
			do_action( 'jet-woo-builder/shortcodes/' . $this->get_tag() . '/loop-item-start' );

			include $this->get_template( 'loop-item' );

			// Hook after loop item template included.
			do_action( 'jet-woo-builder/shortcodes/' . $this->get_tag() . '/loop-item-end' );
		}

		include $this->get_template( 'loop-end' );

		wp_reset_postdata();

		// Hook after loop end template included
		do_action( 'jet-woo-builder/shortcodes/' . $this->get_tag() . '/loop-end' );

		return ob_get_clean();

	}

}
