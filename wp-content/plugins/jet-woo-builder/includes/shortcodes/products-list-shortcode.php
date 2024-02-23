<?php

/**
 * Products list shortcode class
 */
class Jet_Woo_Products_List_Shortcode extends Jet_Woo_Builder_Shortcode_Base {

	use \Jet_Woo_Builder\Products_Shortcode_Trait;

	/**
	 * Shortcode tag
	 *
	 * @return string
	 */
	public function get_tag() {
		return 'jet-woo-products-list';
	}

	public function get_name() {
		return 'jet-woo-products-list';
	}

	/**
	 * Attributes.
	 *
	 * Shortcode attributes.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_atts() {
		return apply_filters( 'jet-woo-builder/shortcodes/jet-woo-products-list/atts', [
			'products_layout'          => [
				'type'    => 'select',
				'label'   => __( 'Thumbnail Position', 'jet-woo-builder' ),
				'default' => 'left',
				'options' => [
					'left'  => __( 'Left', 'jet-woo-builder' ),
					'right' => __( 'Right', 'jet-woo-builder' ),
					'top'   => __( 'Top', 'jet-woo-builder' ),
				],
			],
			'hidden_products'          => [
				'type'      => 'switcher',
				'label'     => __( 'Hidden Products', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
			],
			'clickable_item'           => [
				'type'  => 'switcher',
				'label' => __( 'Make Product Item Clickable', 'jet-woo-builder' ),
			],
			'open_new_tab'             => [
				'type'  => 'switcher',
				'label' => __( 'Open in New Window', 'jet-woo-builder' ),
			],
			'use_current_query'        => [
				'type'        => 'switcher',
				'label'       => __( 'Use Current Query', 'jet-woo-builder' ),
				'description' => __( 'This option works only on the products archive pages, and allows you to display products for current categories, tags and taxonomies.', 'jet-woo-builder' ),
				'separator'   => 'before',
				'condition'   => [
					'enable_custom_query!' => 'yes',
				],
			],
			'number'                   => [
				'type'      => 'number',
				'label'     => __( 'Products Number', 'jet-woo-builder' ),
				'default'   => 3,
				'min'       => 1,
				'max'       => 1000,
				'step'      => 1,
				'condition' => [
					'enable_custom_query!' => 'yes',
				],
			],
			'products_query'           => [
				'type'      => 'select2',
				'label'     => __( 'Query by', 'jet-woo-builder' ),
				'multiple'  => true,
				'options'   => jet_woo_builder_tools()->get_products_query_type(),
				'default'   => 'all',
				'condition' => [
					'use_current_query!'   => 'yes',
					'enable_custom_query!' => 'yes',
				],
			],
			'products_exclude_ids'     => [
				'type'        => 'text',
				'label'       => __( 'Exclude by IDs', 'jet-woo-builder' ),
				'description' => __( 'Set comma separated products IDs list (10, 22, 19 etc.).', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'products_query'       => 'all',
					'use_current_query!'   => 'yes',
					'enable_custom_query!' => 'yes',
				],
			],
			'products_ids'             => [
				'type'        => 'text',
				'label'       => __( 'Include by IDs', 'jet-woo-builder' ),
				'description' => __( 'Set comma separated products IDs list (10, 22, 19 etc.).', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'products_query'       => 'ids',
					'use_current_query!'   => 'yes',
					'enable_custom_query!' => 'yes',
				],
			],
			'products_cat'             => [
				'type'      => 'select2',
				'label'     => __( 'Include Categories', 'jet-woo-builder' ),
				'multiple'  => true,
				'options'   => jet_woo_builder_tools()->get_product_categories(),
				'condition' => [
					'products_query'       => 'category',
					'use_current_query!'   => 'yes',
					'enable_custom_query!' => 'yes',
				],
			],
			'products_cat_exclude'     => [
				'type'      => 'select2',
				'label'     => __( 'Exclude Categories', 'jet-woo-builder' ),
				'multiple'  => true,
				'options'   => jet_woo_builder_tools()->get_product_categories(),
				'condition' => [
					'products_query'       => 'category',
					'use_current_query!'   => 'yes',
					'enable_custom_query!' => 'yes',
				],
			],
			'products_tag'             => [
				'type'      => 'select2',
				'label'     => __( 'Include Tag', 'jet-woo-builder' ),
				'multiple'  => true,
				'options'   => jet_woo_builder_tools()->get_product_tags(),
				'condition' => [
					'products_query'       => 'tag',
					'use_current_query!'   => 'yes',
					'enable_custom_query!' => 'yes',
				],
			],
			'taxonomy_slug'            => [
				'type'      => 'text',
				'label'     => __( 'Taxonomy Slug', 'jet-woo-builder' ),
				'condition' => [
					'products_query'       => 'custom_tax',
					'use_current_query!'   => 'yes',
					'enable_custom_query!' => 'yes',
				],
			],
			'taxonomy_id'              => [
				'type'        => 'text',
				'label'       => __( 'Include by IDs', 'jet-woo-builder' ),
				'description' => __( 'Set comma separated taxonomies IDs list (10, 22, 19 etc.).', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'products_query'       => 'custom_tax',
					'use_current_query!'   => 'yes',
					'enable_custom_query!' => 'yes',
				],
			],
			'products_stock_status'    => [
				'type'      => 'select2',
				'label'     => __( 'Stock Status', 'jet-woo-builder' ),
				'multiple'  => true,
				'default'   => 'instock',
				'options'   => wc_get_product_stock_status_options(),
				'condition' => [
					'products_query'       => 'stock_status',
					'use_current_query!'   => 'yes',
					'enable_custom_query!' => 'yes',
				],
			],
			'variation_post_parent_id' => [
				'type'        => 'text',
				'label'       => __( 'Post Parent IDs', 'jet-woo-builder' ),
				'description' => __( 'Set comma separated post parents IDs list (10, 22, 19 etc.).', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'products_query'       => 'product_variation',
					'use_current_query!'   => 'yes',
					'enable_custom_query!' => 'yes',
				],
			],
			'products_orderby'         => [
				'type'      => 'select',
				'label'     => __( 'Order by', 'jet-woo-builder' ),
				'default'   => 'default',
				'options'   => jet_woo_builder_tools()->orderby_arr(),
				'condition' => [
					'use_current_query!'   => 'yes',
					'enable_custom_query!' => 'yes',
				],
			],
			'products_order'           => [
				'type'      => 'select',
				'label'     => __( 'Order', 'jet-woo-builder' ),
				'default'   => 'desc',
				'options'   => jet_woo_builder_tools()->order_arr(),
				'condition' => [
					'use_current_query!'   => 'yes',
					'enable_custom_query!' => 'yes',
				],
			],
			'show_title'               => [
				'type'      => 'switcher',
				'label'     => __( 'Title', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
				'default'   => 'yes',
				'separator' => 'before',
			],
			'add_title_link'           => [
				'type'      => 'switcher',
				'label'     => __( 'Enable Permalink', 'jet-woo-builder' ),
				'default'   => 'yes',
				'condition' => [
					'show_title' => 'yes',
				],
			],
			'title_html_tag'           => [
				'type'      => 'select',
				'label'     => __( 'HTML Tag', 'jet-woo-builder' ),
				'default'   => 'h5',
				'options'   => jet_woo_builder_tools()->get_available_title_html_tags(),
				'condition' => [
					'show_title' => 'yes',
				],
			],
			'title_trim_type'          => [
				'type'      => 'select',
				'label'     => __( 'Trim Type', 'jet-woo-builder' ),
				'default'   => 'word',
				'options'   => jet_woo_builder_tools()->get_available_title_trim_types(),
				'condition' => [
					'show_title' => 'yes',
				],
			],
			'title_length'             => [
				'type'        => 'number',
				'label'       => __( 'Length', 'jet-woo-builder' ),
				'description' => __( 'Set -1 to show full title and 0 to hide it.', 'jet-woo-builder' ),
				'min'         => -1,
				'default'     => -1,
				'condition'   => [
					'show_title' => 'yes',
				],
			],
			'title_line_wrap'          => [
				'type'         => 'switcher',
				'label'        => __( 'Enable Line Wrap', 'jet-woo-builder' ),
				'prefix_class' => 'jet-woo-builder-title-line-wrap-',
				'condition'    => [
					'show_title' => 'yes',
				],
			],
			'title_tooltip'            => [
				'type'      => 'switcher',
				'label'     => __( 'Enable Title Tooltip', 'jet-woo-builder' ),
				'separator' => 'after',
				'condition' => [
					'show_title' => 'yes',
				],
			],
			'show_image'               => [
				'type'      => 'switcher',
				'label'     => __( 'Thumbnail', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
				'default'   => 'yes',
			],
			'is_linked_image'          => [
				'type'      => 'switcher',
				'label'     => __( 'Enable Thumbnail Permalink', 'jet-woo-builder' ),
				'condition' => [
					'show_image!' => '',
				],
			],
			'thumb_size'               => [
				'type'      => 'select',
				'label'     => __( 'Image Size', 'jet-woo-builder' ),
				'default'   => 'woocommerce_thumbnail',
				'options'   => jet_woo_builder_tools()->get_image_sizes(),
				'condition' => [
					'show_image!' => '',
				],
			],
			'show_badges'              => [
				'type'      => 'switcher',
				'label'     => __( 'Badges', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
				'condition' => [
					'show_image!' => '',
				],
			],
			'sale_badge_text'          => [
				'type'        => 'text',
				'label'       => __( 'Badge Label', 'jet-woo-builder' ),
				'default'     => __( 'Sale!', 'jet-woo-builder' ),
				'description' => __( 'Use %percentage_sale% and %numeric_sale% macros to display a withdrawal of discounts as a percentage or numeric of the initial price.', 'jet-woo-builder' ),
				'separator'   => 'after',
				'condition'   => [
					'show_badges!' => '',
					'show_image!'  => '',
				],
			],
			'show_cat'                 => [
				'type'      => 'switcher',
				'label'     => __( 'Categories', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
				'default'   => 'yes',
			],
			'categories_count'         => [
				'type'        => 'number',
				'label'       => __( 'Categories Count', 'jet-woo-builder' ),
				'description' => __( 'Set 0 to show full categories list.', 'jet-woo-builder' ),
				'min'         => 0,
				'default'     => 0,
				'separator'   => 'after',
				'condition'   => [
					'show_cat' => 'yes',
				],
			],
			'show_price'               => [
				'type'      => 'switcher',
				'label'     => __( 'Price', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
				'default'   => 'yes',
			],
			'show_stock_status'        => [
				'type'      => 'switcher',
				'label'     => __( 'Stock Status', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
			],
			'in_stock_status_text'     => [
				'type'      => 'text',
				'label'     => __( 'In Stock Label', 'jet-woo-builder' ),
				'default'   => __( 'In Stock', 'jet-woo-builder' ),
				'condition' => [
					'show_stock_status' => 'yes',
				],
			],
			'on_backorder_status_text' => [
				'type'      => 'text',
				'label'     => __( 'On Backorder Label', 'jet-woo-builder' ),
				'default'   => __( 'On Backorder', 'jet-woo-builder' ),
				'condition' => [
					'show_stock_status' => 'yes',
				],
			],
			'out_of_stock_status_text' => [
				'type'      => 'text',
				'label'     => __( 'Out of Stock Label', 'jet-woo-builder' ),
				'default'   => __( 'Out of Stock', 'jet-woo-builder' ),
				'separator' => 'after',
				'condition' => [
					'show_stock_status' => 'yes',
				],
			],
			'show_rating'              => [
				'type'      => 'switcher',
				'label'     => __( 'Rating', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
				'default'   => 'yes',
			],
			'show_rating_empty'        => [
				'type'      => 'switcher',
				'label'     => __( 'Empty Rating', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
				'separator' => 'after',
				'condition' => [
					'show_rating' => 'yes',
				],
			],
			'show_sku'                 => [
				'type'      => 'switcher',
				'label'     => __( 'SKU', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
			],
			'show_button'              => [
				'type'      => 'switcher',
				'label'     => __( 'Add To Cart Button', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
				'default'   => 'yes',
			],
			'show_quantity'            => [
				'type'               => 'switcher',
				'label'              => __( 'Quantity Input', 'jet-woo-builder' ),
				'label_on'           => __( 'Show', 'jet-woo-builder' ),
				'label_off'          => __( 'Hide', 'jet-woo-builder' ),
				'frontend_available' => true,
				'condition'          => [
					'show_button' => 'yes',
				],
			],
			'button_use_ajax_style'    => [
				'label'       => __( 'Use default ajax add to cart styles', 'jet-woo-builder' ),
				'description' => __( 'This option enables default WooCommerce styles for \'Add to Cart\' ajax button (\'Loading\' and \'Added\' statements)', 'jet-woo-builder' ),
				'type'        => 'switcher',
				'condition'   => [
					'show_button' => 'yes',
				],
			],
			'not_found_message'        => [
				'type'      => 'text',
				'label'     => __( 'Not Found Message', 'jet-woo-builder' ),
				'default'   => __( 'Products not found.', 'jet-woo-builder' ),
				'separator' => 'before',
				'dynamic'   => [
					'active' => true,
				],
			],
			'query_id'                 => [
				'type'        => 'text',
				'label'       => __( 'Query ID', 'jet-woo-builder' ),
				'description' => __( 'Give your Query a custom unique id to allow server side filtering', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
			],
		] );
	}

}
