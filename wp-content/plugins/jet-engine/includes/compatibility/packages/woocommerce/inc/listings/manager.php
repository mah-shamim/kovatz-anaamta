<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Listings;

use Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Package;
use Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Query_Builder\Manager as Query_Manager;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	private $_products = [];

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'jet-engine/query-builder/init', [ $this, 'init' ] );

		add_filter(
			'jet-engine/listing/data/object-fields-groups',
			[ $this, 'add_source_fields' ]
		);

		add_filter(
			'jet-engine/listings/dynamic-link/fields',
			[ $this, 'add_link_source_fields' ]
		);

		// Works the same as prev, but placed only where only plain URL allowed to return
		add_filter(
			'jet-engine/listings/dynamic-link/fields/common',
			[ $this, 'add_link_source_fields' ],
			10, 3
		);

		add_filter(
			'jet-engine/listings/dynamic-image/fields',
			[ $this, 'add_image_source_fields' ],
			10, 2
		);

		/*
		Commented out this filter because the image source fields are automatically added to blocks editor
		by `jet-engine/listings/dynamic-image/fields` filter.
		add_filter(
			'jet-engine/blocks-views/editor/dynamic-image/fields',
			[ $this, 'add_blocks_editor_image_source_fields' ],
			10, 2
		);
		*/

		add_filter(
			'jet-engine/listings/dynamic-image/custom-image',
			[ $this, 'custom_image_renderer' ],
			10, 3
		);

		add_filter(
			'jet-engine/listings/dynamic-image/custom-url',
			[ $this, 'custom_image_url' ],
			10, 2
		);

		add_filter(
			'jet-engine/listings/dynamic-link/custom-url',
			[ $this, 'custom_link_url' ],
			10, 2
		);

		add_filter(
			'jet-engine/listing/custom-post-id',
			[ $this, 'set_wc_queried_product_id' ],
			10, 2
		);

		add_filter(
			'jet-engine/listings/macros/current-id',
			[ $this, 'set_wc_queried_product_id' ],
			10, 2
		);

		add_filter(
			'jet-reviews/compatibility/listing/post/current-id',
			[ $this, 'set_wc_queried_product_id' ],
			10, 2
		);

		if ( $this->is_attrs_autoregister_enabled() ) {
			add_filter(
				'jet-engine/listing/data/wc-product-query/object-fields-groups',
				[ $this, 'add_attrs_autoregister_source_fields' ]
			);
		}

		add_filter(
			'jet-engine/listings/data/prop-not-found',
			[ $this, 'get_wc_product_method_with_param' ],
			10, 3
		);

		add_filter(
			'jet-engine/listings/data/get-meta/query',
			[ $this, 'get_meta' ],
			10, 2
		);

		add_filter(
			'jet-engine/data-stores/store-post-id',
			[ $this, 'set_datastore_wc_queried_product_id' ]
		);

		add_filter(
			'jet-engine/listings/data/get-meta/repeater',
			[ $this, 'get_repeater_listing_meta' ],
			10, 2
		);

		add_filter(
			'jet-engine/listings/data/prop-not-found',
			[ $this, 'maybe_get_wc_product_object_prop' ],
			10, 3
		);

		add_action(
			'jet-engine/listing/before-grid-item',
			[ $this, 'maybe_print_wc_notices' ],
			10, 2
		);

		add_filter(
			'jet-engine/listings/dynamic-link/woocommerce-options',
			[ $this, 'register_dynamic_link_option' ]
		);

		add_filter(
			'jet-engine/listings/dynamic-link/pre-render-link',
			[ $this, 'maybe_render_links' ],
			10, 4
		);

		add_filter(
			'jet-engine/listings/dynamic-link/custom-url',
			[ $this, 'maybe_set_custom_url' ],
			10, 2
		);

		add_filter(
			'jet-engine/twig-views/functions/dynamic-url/controls',
			function( $controls ) {
				$controls['size']['condition']['source'][] = 'get_product_image_url';
				return $controls;
			}
		);

		add_filter(
			'acf/pre_load_post_id',
			[ $this, 'set_wc_queried_product_id' ],
			10, 2
		);

	}

	/**
	 * Initialize additional listings files.
	 */
	public function init() {

		require_once Package::instance()->package_path( 'listings/blocks-views/integration.php' );
		require_once Package::instance()->package_path( 'listings/elementor-views/integration.php' );
		require_once Package::instance()->package_path( 'listings/query.php' );

		new Blocks_Views\Integration();
		new Elementor_Views\Integration();
		new Query();

	}

	/**
	 * Register dynamic link option.
	 *
	 * Add required options to the dynamic link widget.
	 *
	 * @since  3.0.2
	 * @access public
	 *
	 * @param array $options List of options.
	 *
	 * @return array
	 */
	public function register_dynamic_link_option( $options ) {

		$options['add_to_cart'] = __( 'Add to Cart', 'jet-engine' );

		return $options;

	}

	/**
	 * Render link.
	 *
	 * Check dynamic widget link source and returns proper result according to it.
	 *
	 * @since  3.0.2
	 * @access public
	 *
	 * @param string $result     Dynamic link markup.
	 * @param array  $settings   List of widget settings.
	 * @param string $base_class Widget name.
	 * @param object $render     Dynamic link render instance.
	 *
	 * @return mixed|string
	 */
	public function maybe_render_links( $result, $settings, $base_class, $render ) {

		$source = ! empty( $settings['dynamic_link_source'] ) ? $settings['dynamic_link_source'] : '_permalink';

		if ( 'add_to_cart' === $source ) {
			$result = $this->add_to_cart_link( $result, $settings, $base_class, $render );
		}

		return $result;

	}

	/**
	 * Return only URL of add to cart link or WC product image, not whole HTMl
	 *
	 * This callback used where we need only plain URL, for example Twig templates
	 * 
	 * @param  bool|string $result   [description]
	 * @param  array  $settings [description]
	 * @return bool|string
	 */
	public function maybe_set_custom_url( $result = false, $settings = [] ) {

		$source = ! empty( $settings['dynamic_link_source'] ) ? $settings['dynamic_link_source'] : '_permalink';

		if ( ! in_array( $source, [ 'add_to_cart', 'get_product_image_url' ] ) ) {
			return $result;
		}

		global $product;

		if ( is_null( $product ) ) {
			$product = jet_engine()->listings->data->get_current_object();
		}

		// If product is not found - abort early
		if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
			return $result;
		}

		switch ( $source ) {
			case 'add_to_cart':
				return esc_url( $product->add_to_cart_url() );
			
			case 'get_product_image_url':

				$image_url = '';
				$size      = ! empty( $settings['size'] ) ? $settings['size'] : 'full';

				if ( $product->get_image_id() ) {
					$image_url = wp_get_attachment_image_url( $product->get_image_id(), $size );
				} elseif ( $product->get_parent_id() ) {
					$parent_product = wc_get_product( $product->get_parent_id() );
					if ( $parent_product && $parent_product->get_image_id() ) {
						$image_url = wp_get_attachment_image_url( $parent_product->get_image_id(), $size );
					}
				}

				if ( ! $image_url ) {
					$image_url = wc_placeholder_img_src( $size );
				}
				
				return $image_url;
		}

		// Just in case
		return $result;

	}

	/**
	 * Add to cart link.
	 *
	 * Returns dynamic link with WooCommerce add to cart functionality.
	 *
	 * @since  3.0.2
	 * @since  3.0.8 Added product quantity input.
	 * @access public
	 *
	 * @param string $result     Dynamic link markup.
	 * @param array  $settings   List of widget settings.
	 * @param string $base_class Widget name.
	 * @param object $render     Dynamic link render instance.
	 *
	 * @return string
	 */
	public function add_to_cart_link( $result, $settings, $base_class, $render ) {

		global $product;

		if ( is_null( $product ) ) {
			$product = jet_engine()->listings->data->get_current_object();
		}

		if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
			return $result;
		}

		$url   = esc_url( $product->add_to_cart_url() );
		$label = $render->get_link_label( $settings, $base_class, $url );
		$icon  = $product->is_type( 'simple' ) ? $render->get_link_icon( $settings, $base_class ) : '';
		$args  = [
			'quantity'   => $settings['dynamic_link_add_to_cart_quantity'] ?? $product->get_min_purchase_quantity(),
			'class'      => implode(
				' ',
				array_filter(
					[
						'button',
						wc_wp_theme_get_element_class_name( 'button' ),
						'jet-listing-dynamic-link__link',
						'product_type_' . $product->get_type(),
						'jet-woo-add-to-cart',
						$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
						$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
					]
				)
			),
			'attributes' => [
				'data-product_id'  => $product->get_id(),
				'data-product_sku' => $product->get_sku(),
				'aria-label'       => $product->add_to_cart_description(),
			],
		];

		$args             = apply_filters( 'jet-engine/listing/data/dynamic-link/add-to-cart-args', $args, $product );
		$enable_qty_input = isset( $settings['dynamic_link_enable_quantity_input'] ) ? filter_var( $settings['dynamic_link_enable_quantity_input'], FILTER_VALIDATE_BOOLEAN ) : false;

		if ( $enable_qty_input && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
			$format = '<form action="%1$s" class="cart" method="post" enctype="multipart/form-data">';
			$format .= woocommerce_quantity_input( [
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => '%2$s',
			], $product, false );
			$format .= '<button type="submit" data-quantity="%2$s" class="%3$s alt" %4$s >%5$s %6$s</button>';
			$format .= '</form>';
		} else {
			$format = '<a href="%s" data-quantity="%s" class="%s" %s rel="nofollow">%s %s</a>';
		}

		$result = sprintf(
			$format,
			$url,
			esc_attr( $args['quantity'] ?? $product->get_min_purchase_quantity() ),
			esc_attr( $args['class'] ?? 'button' ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			$icon,
			$label
		);

		return $result;

	}

	/**
	 * Tries to get product object for current context
	 *
	 * @return [type] [description]
	 */
	public function get_current_product() {

		$object = jet_engine()->listings->data->get_current_object();

		if ( ! is_a( $object, 'WC_Product' ) ) {

			global $product, $post;

			if ( $product ) {
				if ( is_a( $product, 'WC_Product' ) ) {
					$object = $product;
				} else {
					$object = wc_get_product( $post );
				}
			} else {
				$object = false;
			}

		}

		return $object;

	}

	/**
	 * Maybe print WooCommerce notices before WC listing
	 *
	 * @return [type] [description]
	 */
	public function maybe_print_wc_notices( $post, $listing ) {

		if ( empty( $listing->query_vars['request']['query_id'] ) ) {
			return;
		}

		$query = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $listing->query_vars['request']['query_id'] );

		if ( ! $query ) {
			return;
		}

		if ( $query->query_type !== Query_Manager::instance()->slug ) {
			return;
		}

		$print_notices = apply_filters(
			'jet-engine/listing/data/wc-product-query/print-notices-before-listing', true,
			$post, $listing, $this
		);

		if ( ! $print_notices ) {
			return;
		}

		$index = jet_engine()->listings->data->get_index();

		if ( 0 === $index && function_exists( 'wc_print_notices' ) ) {

			$notices = wc_print_notices( true );

			if ( $notices ) {
				printf( '<div class="woocommerce" style="flex: 1 1 100%%;">%s</div>', $notices );
			}

		}

	}

	/**
	 * Returns products attributes auto-register status for dynamic tags source.
	 *
	 * @return mixed|void
	 */
	public function is_attrs_autoregister_enabled() {
		return apply_filters( 'jet-engine/listing/data/wc-product-query/autoregister-wc-attributes', true );
	}

	/**
	 * Add source fields into the dynamic field widget
	 *
	 * @param $groups
	 *
	 * @return mixed
	 */
	public function add_source_fields( $groups ) {

		$groups[] = [
			'label'   => __( 'WooCommerce', 'jet-engine' ),
			'options' => $this->get_product_fields_list(),
		];

		return $groups;

	}

	public function get_product_fields_list() {
		return apply_filters( 'jet-engine/listing/data/wc-product-query/object-fields-groups', [
			'get_id'                       => __( 'Product ID', 'jet-engine' ),
			'get_permalink'                => __( 'Product URL', 'jet-engine' ),
			'get_title'                    => __( 'Title', 'jet-engine' ),
			'get_slug'                     => __( 'Product Slug', 'jet-engine' ),
			'get_type'                     => __( 'Type', 'jet-engine' ),
			'get_status'                   => __( 'Product Status', 'jet-engine' ),
			'get_sku'                      => __( 'SKU', 'jet-engine' ),
			'get_description'              => __( 'Description', 'jet-engine' ),
			'get_short_description'        => __( 'Short Description', 'jet-engine' ),
			'get_price_html'               => __( 'Price HTML String', 'jet-engine' ),
			'get_price'                    => __( 'Plain Price', 'jet-engine' ),
			'get_regular_price'            => __( 'Plain Regular Price', 'jet-engine' ),
			'get_sale_price'               => __( 'Plain Sale Price', 'jet-engine' ),
			'get_stock_status'             => __( 'Stock Status', 'jet-engine' ),
			'get_stock_quantity'           => __( 'Stock Quantity', 'jet-engine' ),
			'wc_get_product_category_list' => __( 'Categories', 'jet-engine' ),
			'wc_get_product_tag_list'      => __( 'Tags', 'jet-engine' ),
			'get_average_rating'           => __( 'Average Rating', 'jet-engine' ),
			'get_review_count'             => __( 'Review Count', 'jet-engine' ),
			'get_total_sales'              => __( 'Total Sales', 'jet-engine' ),
			'get_date_on_sale_from'        => __( 'Date on Sale from', 'jet-engine' ),
			'get_date_on_sale_to'          => __( 'Date on Sale to', 'jet-engine' ),
			'get_height'                   => __( 'Height', 'jet-engine' ),
			'get_length'                   => __( 'Length', 'jet-engine' ),
			'get_weight'                   => __( 'Weight', 'jet-engine' ),
			'get_width'                    => __( 'Width', 'jet-engine' ),
			'get_max_purchase_quantity'    => __( 'Max Purchase Quantity', 'jet-engine' ),
			'get_tax_status'               => __( 'Tax Status', 'jet-engine' ),
			'add_to_cart_url'              => __( 'Add to Cart URL', 'jet-engine' ),
			'add_to_cart_text'             => __( 'Add to Cart Text', 'jet-engine' ),
		] );
	}

	/**
	 * Add product attributes auto-register source fields into the dynamic field widget
	 *
	 * @param $fields
	 *
	 * @return mixed
	 */
	public function add_attrs_autoregister_source_fields( $fields ) {

		$attributes = wc_get_attribute_taxonomies();

		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $attribute ) {
				$fields[ 'wc_attr::' . $attribute->attribute_name ] = __( 'Product attr: ', 'jet-engine' ) . $attribute->attribute_label;
			}
		}

		return $fields;

	}

	/**
	 * Handle and return WC_Product class method with parameters.
	 *
	 * @param $result
	 * @param $prop
	 * @param $object
	 *
	 * @return false|mixed|string
	 */
	public function get_wc_product_method_with_param( $result, $prop, $object ) {

		if ( $object && is_callable( $prop ) && is_a( $object, 'WC_Product' ) ) {
			if ( 'wc_get_product_category_list' === $prop || 'wc_get_product_tag_list' === $prop ) {
				$result = call_user_func( $prop, $object->get_id() );
			}
		}

		if ( $this->is_attrs_autoregister_enabled() ) {
			if ( false !== strpos( $prop, 'wc_attr::' ) && is_callable( [ $object, 'get_attribute' ] ) ) {
				$result = $object->get_attribute( str_replace( 'wc_attr::', '', $prop ) );
			}
		}

		return $result;

	}

	/**
	 * Add link source fields.
	 *
	 * Returns extended dynamic links source fields list.
	 *
	 * @since  3.0.2
	 * @since  3.0.8 Added `jet-engine/listings/dynamic-link/woocommerce-options` to extent WooCommerce group.
	 * @access public
	 *
	 * @param array $groups Source fields groups list.
	 *
	 * @return mixed
	 */
	public function add_link_source_fields( $groups, $for = 'plain', $is_common = false ) {

		$options = [
			'get_permalink' => __( 'Permalink', 'jet-engine' ),
		];

		if ( $is_common ) {
			$options['get_product_image_url'] = __( 'Product Image URL', 'jet-engine' );
		}

		$groups[] = [
			'label'   => __( 'WooCommerce', 'jet-engine' ),
			'options' => apply_filters( 'jet-engine/listings/dynamic-link/woocommerce-options', $options ),
		];

		return $groups;

	}

	/**
	 * Add source fields into the dynamic image widget
	 *
	 * @param $groups
	 *
	 * @return mixed
	 */
	public function add_image_source_fields( $groups, $for ) {

		if ( 'media' === $for ) {
			$groups[] = [
				'label'   => __( 'WooCommerce', 'jet-engine' ),
				'options' => [
					'get_image' => __( 'Featured Image', 'jet-engine' ),
				],
			];
		} else {
			$groups[] = [
				'label'   => __( 'WooCommerce', 'jet-engine' ),
				'options' => [
					'get_permalink' => __( 'Permalink', 'jet-engine' ),
				],
			];
		}

		return $groups;

	}

	/**
	 * Add source fields into the blocks editor dynamic image widget
	 *
	 * @param $groups
	 *
	 * @return mixed
	 */
	public function add_blocks_editor_image_source_fields( $groups, $for ) {

		if ( 'media' === $for ) {
			$groups[] = [
				'label'  => __( 'WooCommerce', 'jet-engine' ),
				'values' => [
					[
						'value' => 'get_image',
						'label' => __( 'Featured Image', 'jet-engine' ),
					],
				],
			];
		} else {
			$groups[] = [
				'label'  => __( 'WooCommerce', 'jet-engine' ),
				'values' => [
					[
						'value' => 'get_permalink',
						'label' => __( 'Permalink', 'jet-engine' ),
					],
				],
			];
		}

		return $groups;

	}

	/**
	 * Custom image renderer for custom content type
	 *
	 * @param       $result
	 * @param       $settings
	 * @param       $size
	 *
	 * @return false|string
	 */
	public function custom_image_renderer( $result, $settings, $size ) {

		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! isset( $current_object ) ) {
			return $result;
		}

		$image = $settings['dynamic_image_source'] ?? '';

		if ( ! $image ) {
			return $result;
		}

		$_product = $this->maybe_convert_object_to_wc_product( $current_object );

		if ( is_callable( [ $_product, $image ] ) ) {
			ob_start();

			echo call_user_func( [ $_product, $image ], $size );

			return ob_get_clean();
		}

		return $result;

	}

	/**
	 * Returns custom URL for the dynamic image
	 *
	 * @param $result
	 * @param $settings
	 *
	 * @return false|mixed|string
	 */
	public function custom_image_url( $result, $settings ) {

		$url = $this->get_custom_link_by_setting( 'image_link_source', $settings );

		if ( ! $url ) {
			return $result;
		} else {
			return $url;
		}

	}

	/**
	 * Returns custom URL for dynamic link widget
	 *
	 * @param $result
	 * @param $settings
	 *
	 * @return false|mixed|string
	 */
	public function custom_link_url( $result, $settings ) {

		$url = $this->get_custom_link_by_setting( 'dynamic_link_source', $settings );

		if ( ! $url ) {
			return $result;
		} else {
			return $url;
		}

	}

	/**
	 * Returns custom value from dynamic prop by setting
	 *
	 * @param $setting
	 * @param $settings
	 *
	 * @return false|string|\WP_Error
	 */
	public function get_custom_link_by_setting( $setting, $settings ) {

		$current_object = jet_engine()->listings->data->get_current_object();
		$result         = false;

		if ( ! isset( $current_object ) ) {
			return false;
		}

		$link = $settings[ $setting ] ?? '';

		if ( ! $link ) {
			return false;
		}

		$_product = $this->maybe_convert_object_to_wc_product( $current_object );

		if ( is_callable( [ $_product, $link ] ) ) {
			$result = call_user_func( [ $_product, $link ] );
		}

		return $result;

	}

	/**
	 * Set correct products id after `WC_Product_Query` for post loop output.
	 *
	 * @param $id
	 * @param $object
	 *
	 * @return mixed
	 */
	public function set_wc_queried_product_id( $id, $object ) {

		if ( $object && is_object( $object ) && is_a( $object, 'WC_Product' ) ) {
			$id = $object->get_id();
		}

		return $id;

	}

	/**
	 * Set WC_Product_Query id as post id for Data Stores.
	 *
	 * @param $post_id
	 *
	 * @return int|mixed
	 */
	public function set_datastore_wc_queried_product_id( $post_id ) {

		$listing_object = jet_engine()->listings->data->get_current_object();

		if ( $listing_object && is_a( $listing_object, 'WC_Product' ) ) {
			$post_id = $listing_object->get_id();
		}

		return $post_id;

	}

	/**
	 * Returns `WC_Product_Query` current meta.
	 *
	 * @param $value
	 * @param $key
	 *
	 * @return mixed
	 */
	public function get_meta( $value, $key ) {

		$object = jet_engine()->listings->data->get_current_object();

		if ( $object && is_a( $object, 'WC_Product' ) ) {
			if ( jet_engine()->relations->legacy->is_relation_key( $key ) ) {
				$single = false;
			} else {
				$single = true;
			}

			return get_post_meta( $object->get_id(), $key, $single );
		} else {
			return $value;
		}

	}

	/**
	 * Returns `WC_Product_Query` repeater fields meta.
	 *
	 * @param $value
	 * @param $key
	 *
	 * @return false|mixed|void
	 */
	public function get_repeater_listing_meta( $value, $key ) {

		$object = jet_engine()->listings->data->get_current_object();

		if ( $object && is_a( $object, 'WC_Product' ) ) {
			return jet_engine()->listings->data->get_repeater_value( $key );
		} else {
			return $value;
		}

	}

	/**
	 * Returns WC Product properties.
	 *
	 * Checks the object being processed and returns its property value if it is product.
	 *
	 * @param $found
	 * @param $property
	 * @param $object
	 *
	 * @return false|mixed
	 */
	public function maybe_get_wc_product_object_prop( $found, $property, $object ) {

		$_product = $this->maybe_convert_object_to_wc_product( $object );

		if ( is_callable( [ $_product, $property ] ) ) {
			return call_user_func( [ $_product, $property ] );
		} else {
			$result = $this->get_wc_product_method_with_param( null, $property, $_product );

			if ( null !== $result ) {
				return $result;
			}
		}

		return $found;

	}

	/**
	 * Convert object to WC product.
	 *
	 * Check if post object is exist and its post type is product, then convert appropriately.
	 *
	 * @since  3.0.2
	 * @access public
	 *
	 * @param object $object Post object.
	 *
	 * @return false|mixed|\WC_Product|null
	 */
	public function maybe_convert_object_to_wc_product( $object ) {

		if ( $object && is_a( $object, 'WP_Post' ) && 'product' === $object->post_type ) {
			$_product = $this->_products[ $object->ID ] ?? false;

			if ( ! $_product ) {
				$this->_products[ $object->ID ] = $_product = wc_get_product( $object->ID );
			}

			return $_product;
		}

		return $object;

	}

	/**
	 * Returns the instance.
	 *
	 * @access public
	 * @return object
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

}
