<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

namespace Jet_Engine\Compatibility\Packages;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Woo_Package' ) ) {

	/**
	 * Define Jet_Engine_Woo_Package class
	 */
	class Jet_Engine_Woo_Package {

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			add_filter( 'jet-engine/post-type/product/meta-fields', array( $this, 'product_fields' ) );
			add_filter( 'jet-engine/taxonomy/product_cat/meta-fields', array( $this, 'product_cat_fields' ) );
			add_filter( 'jet-engine/listings/taxonomies-for-options', array( $this, 'add_visibility_tax' ) );
			add_action( 'jet-engine/listing/after-tax-fields', array( $this, 'add_tax_visibility_options' ), 10, 2 );
			add_filter( 'jet-engine/listing/grid/tax-query-item-settings', array( $this, 'apply_visibility_terms' ) );
			add_filter( 'jet-engine/elementor-views/frontend/add-inline-css', array( $this, 'add_inline_css' ) );
			add_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'modify_default_query_args' ) );

			add_action( 'woocommerce_shop_loop', array( $this, 'set_object_on_each_loop_post' ) );
			add_action( 'woocommerce_product_duplicate', [ $this, 'duplicate_custom_taxonomy_terms' ], 10, 2 );

			add_action( 'jet-engine/listing/grid/before-render', array( $this, 'remove_ordering_filters_before_listing' ) );

			$this->create_wc_package_instance();

		}

		/**
		 * Create package instance
		 *
		 * @return void
		 */
		public function create_wc_package_instance() {
			require jet_engine()->plugin_path( 'includes/compatibility/packages/woocommerce/inc/package.php' );
			\Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Package::instance();
		}

		/**
		 * Apply visibility settings for listing grid widget
		 *
		 * @return [type] [description]
		 */
		public function apply_visibility_terms( $settings ) {

			if ( ! empty( $settings['tax_query_wc_visibility_term'] ) ) {
				$settings['tax_query_terms'] = $settings['tax_query_wc_visibility_term'];
			}

			return $settings;

		}

		/**
		 * Add product_visibility to allowed taxonomies to query for
		 *
		 * @param [type] $taxonomies [description]
		 */
		public function add_visibility_tax( $taxonomies ) {

			if ( taxonomy_exists( 'product_visibility' ) ) {
				$taxonomies['product_visibility'] = __( 'Product Visibility', 'jet-engine' );
			}

			return $taxonomies;

		}

		/**
		 * Add taxonomy visibility option
		 *
		 * @param [type] $repeater [description]
		 * @param [type] $widget   [description]
		 */
		public function add_tax_visibility_options( $repeater, $widget ) {

			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
			$options                     = array();

			foreach ( $product_visibility_term_ids as $term_name => $term_id ) {
				$options[ $term_id ] = ucfirst( str_replace( array( '_', '-' ), '', $term_name ) );
			}

			$repeater->add_control(
				'tax_query_wc_visibility_term',
				array(
					'label'         => __( 'WooCommerce Visibility Term', 'jet-engine' ),
					'type'          => 'select',
					'options'       => $options,
					'default'       => '',
					'display_block' => true,
					'condition'     => array(
						'type'               => 'tax_query',
						'tax_query_taxonomy' => 'product_visibility',
					),
				)
			);

		}

		/**
		 * Set current product as current listing object for each product loop item
		 */
		public function set_object_on_each_loop_post() {
			if ( jet_engine()->listings ) {
				global $post;
				jet_engine()->listings->data->set_current_object( $post );
			}
		}

		/**
		 * Duplicate custom taxonomy terms.
		 *
		 * Set custom taxonomy terms for duplicated products.
		 *
		 * @since  3.0.1
		 * @access public
		 *
		 * @param $duplicate
		 * @param $product
		 */
		public function duplicate_custom_taxonomy_terms( $duplicate, $product ) {

			$taxonomies = jet_engine()->taxonomies->data->get_items();

			foreach ( $taxonomies as $taxonomy ) {
				if ( 'product' === $taxonomy['object_type'] || false !== strpos( $taxonomy['object_type'], 'product' ) ) {
					$terms = get_the_terms( $product->get_id(), $taxonomy['slug'] );

					if ( ! is_wp_error( $terms ) ) {
						wp_set_object_terms( $duplicate->get_id(), wp_list_pluck( $terms, 'term_id' ), $taxonomy['slug'] );
					}
				}
			}

		}

		/**
		 * Product fields
		 *
		 * @return array
		 */
		public function product_fields( $fields ) {

			if ( empty( $fields ) ) {
				$fields = array();
			}

			$fields[] = array(
				'name'  => '_regular_price',
				'type'  => 'text',
				'title' => __( 'Price', 'jet-engine' ),
			);

			$fields[] = array(
				'name'  => '_sale_price',
				'type'  => 'text',
				'title' => __( 'Sale Price', 'jet-engine' ),
			);

			$fields[] = array(
				'name'  => '_sku',
				'type'  => 'text',
				'title' => __( 'SKU', 'jet-engine' ),
			);

			$fields[] = array(
				'name'  => 'total_sales',
				'type'  => 'text',
				'title' => __( 'Sales', 'jet-engine' ),
			);

			$fields[] = array(
				'name'  => '_wc_average_rating',
				'type'  => 'text',
				'title' => __( 'Average Rating', 'jet-engine' ),
			);

			$fields[] = array(
				'name'  => '_stock_status',
				'type'  => 'text',
				'title' => __( 'Stock Status', 'jet-engine' ),
			);

			$fields[] = array(
				'name'  => '_weight',
				'type'  => 'text',
				'title' => __( 'Weight', 'jet-engine' ),
			);

			$fields[] = array(
				'name'  => '_length',
				'type'  => 'text',
				'title' => __( 'Length', 'jet-engine' ),
			);

			$fields[] = array(
				'name'  => '_width',
				'type'  => 'text',
				'title' => __( 'Width', 'jet-engine' ),
			);

			$fields[] = array(
				'name'  => '_height',
				'type'  => 'text',
				'title' => __( 'Height', 'jet-engine' ),
			);

			return $fields;
		}

		/**
		 * Product category fields
		 *
		 * @return array
		 */
		public function product_cat_fields( $fields ) {

			$fields[] = array(
				'name'  => 'thumbnail_id',
				'type'  => 'media',
				'title' => __( 'Thumbnail', 'jet-engine' ),
			);

			return $fields;

		}

		/**
		 * Enable adding inline css for a listing item on Cart template
		 *
		 * @param bool $add_inline_css
		 *
		 * @return bool
		 */
		public function add_inline_css( $add_inline_css ) {

			if ( isset( $_REQUEST['removed_item'] ) && $_REQUEST['removed_item'] ) {
				return true;
			}

			return $add_inline_css;
		}

		/**
		 * Modify default query arguments.
		 *
		 * @param array $args Query arguments.
		 *
		 * @return array
		 */
		public function modify_default_query_args( $args ) {

			if ( wp_doing_ajax() ) {

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

			} else {
				global $wp_query;

				if ( $wp_query->get( 'wc_query' ) ) {
					$args['wc_query'] = $wp_query->get( 'wc_query' );
				}
			}

			return $args;
		}

		public function remove_ordering_filters_before_listing() {
			WC()->query->remove_ordering_args();
		}

	}

}

new Jet_Engine_Woo_Package();
