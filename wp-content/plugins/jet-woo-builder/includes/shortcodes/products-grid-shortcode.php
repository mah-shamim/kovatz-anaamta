<?php

/**
 * Products shortcode class
 */
class Jet_Woo_Products_Grid_Shortcode extends Jet_Woo_Builder_Shortcode_Base {

	use \Jet_Woo_Builder\Products_Shortcode_Trait;

	/**
	 * Shortcode tag
	 *
	 * @return string
	 */
	public function get_tag() {
		return 'jet-woo-products';
	}

	public function get_name() {
		return 'jet-woo-products-grid';
	}

	/**
	 * Shortcode attributes
	 *
	 * @return array
	 */
	public function get_atts() {

		$columns = jet_woo_builder_tools()->get_select_range( 12 );

		return apply_filters( 'jet-woo-builder/shortcodes/jet-woo-products/atts', [
			'presets'                  => [
				'type'    => 'select',
				'label'   => __( 'Presets', 'jet-woo-builder' ),
				'default' => 'preset-1',
				'options' => [
					'preset-1'  => __( 'Preset 1', 'jet-woo-builder' ),
					'preset-2'  => __( 'Preset 2', 'jet-woo-builder' ),
					'preset-3'  => __( 'Preset 3', 'jet-woo-builder' ),
					'preset-4'  => __( 'Preset 4', 'jet-woo-builder' ),
					'preset-5'  => __( 'Preset 5', 'jet-woo-builder' ),
					'preset-6'  => __( 'Preset 6', 'jet-woo-builder' ),
					'preset-7'  => __( 'Preset 7 ', 'jet-woo-builder' ),
					'preset-8'  => __( 'Preset 8 ', 'jet-woo-builder' ),
					'preset-9'  => __( 'Preset 9 ', 'jet-woo-builder' ),
					'preset-10' => __( 'Preset 10', 'jet-woo-builder' ),
				],
			],
			'columns'                  => [
				'type'               => 'select',
				'responsive'         => true,
				'label'              => __( 'Columns', 'jet-woo-builder' ),
				'options'            => $columns,
				'default'            => 4,
				'frontend_available' => true,
				'render_type'        => 'template',
				'selectors'          => [
					'{{WRAPPER}} .jet-woo-products .jet-woo-products__item' => '--columns: {{VALUE}}',
				],
			],
			'hover_on_touch'           => [
				'label' => __( 'Hover on Touch', 'jet-woo-builder' ),
				'type'  => 'switcher',
			],
			'equal_height_cols'        => [
				'label'        => __( 'Equal Columns Height', 'jet-woo-builder' ),
				'type'         => 'switcher',
				'return_value' => 'true',
			],
			'columns_gap'              => [
				'type'    => 'switcher',
				'label'   => __( 'Gap between columns', 'jet-woo-builder' ),
				'default' => 'yes',
			],
			'rows_gap'                 => [
				'type'    => 'switcher',
				'label'   => __( 'Gap between rows', 'jet-woo-builder' ),
				'default' => 'yes',
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
				'default'   => 4,
				'min'       => -1,
				'max'       => 1000,
				'step'      => 1,
				'condition' => [
					'enable_custom_query!' => 'yes',
				],
			],
			'products_query'           => [
				'type'      => 'select2',
				'label'     => __( 'Query by', 'jet-woo-builder' ),
				'default'   => 'all',
				'multiple'  => true,
				'options'   => jet_woo_builder_tools()->get_products_query_type(),
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
				'description' => __( 'Set comma separated IDs list (10, 22, 19 etc.).', 'jet-woo-builder' ),
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
			'enable_thumb_effect'      => [
				'type'    => 'switcher',
				'label'   => __( 'Enable Thumbnail Effect', 'jet-woo-builder' ),
				'default' => 'yes',
			],
			'add_thumb_link'           => [
				'type'    => 'switcher',
				'label'   => esc_html__( 'Enable Thumbnail Permalink', 'jet-woo-builder' ),
				'default' => 'yes',
			],
			'thumb_size'               => [
				'type'    => 'select',
				'label'   => __( 'Image Size', 'jet-woo-builder' ),
				'default' => 'woocommerce_thumbnail',
				'options' => jet_woo_builder_tools()->get_image_sizes(),
			],
			'show_badges'              => [
				'type'      => 'switcher',
				'label'     => __( 'Badges', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
				'default'   => 'yes',
			],
			'sale_badge_text'          => [
				'type'        => 'text',
				'label'       => __( 'Badge Label', 'jet-woo-builder' ),
				'default'     => __( 'Sale!', 'jet-woo-builder' ),
				'description' => __( 'Use %percentage_sale% and %numeric_sale% macros to display a withdrawal of discounts as a percentage or numeric of the initial price.', 'jet-woo-builder' ),
				'separator'   => 'after',
				'condition'   => [
					'show_badges' => 'yes',
				],
			],
			'show_excerpt'             => [
				'type'      => 'switcher',
				'label'     => __( 'Short Description', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
				'default'   => 'yes',
				'condition' => [
					'presets!' => 'preset-4',
				],
			],
			'excerpt_trim_type'        => [
				'type'      => 'select',
				'label'     => __( 'Trim Type', 'jet-woo-builder' ),
				'default'   => 'word',
				'options'   => [
					'word'    => 'Words',
					'letters' => 'Letters',
				],
				'condition' => [
					'show_excerpt' => 'yes',
				],
			],
			'excerpt_length'           => [
				'type'        => 'number',
				'label'       => __( 'Length', 'jet-woo-builder' ),
				'description' => __( 'Set -1 to show full short description and 0 to hide it.', 'jet-woo-builder' ),
				'min'         => -1,
				'default'     => 10,
				'separator'   => 'after',
				'condition'   => [
					'show_excerpt' => 'yes',
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
			'show_tag'                 => [
				'type'      => 'switcher',
				'label'     => __( 'Tags', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
				'default'   => 'yes',
			],
			'tags_count'               => [
				'type'        => 'number',
				'label'       => __( 'Tags Count', 'jet-woo-builder' ),
				'description' => __( 'Set 0 to show tags full list.', 'jet-woo-builder' ),
				'min'         => 0,
				'default'     => 0,
				'separator'   => 'after',
				'condition'   => [
					'show_tag' => 'yes',
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
				'type'        => 'switcher',
				'label'       => __( 'Use default ajax add to cart styles', 'jet-woo-builder' ),
				'description' => __( 'This option enables default WooCommerce styles for \'Add to Cart\' ajax button (\'Loading\' and \'Added\' statements)', 'jet-woo-builder' ),
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
				'description' => __( 'Give your Query a custom unique id to allow server side filtering.', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
			],
		] );

	}

	/**
	 *  Get products grid widget preset template.
	 *
	 * @return bool|string
	 */
	public function get_product_preset_template() {

		$template = jet_woo_builder()->get_template( $this->get_tag() . '/global/presets/' . $this->get_attr( 'presets' ) . '.php' );

		if ( ! $template ) {
			$template = jet_woo_builder()->get_template( 'widgets/global/products-grid/presets/' . $this->get_attr( 'presets' ) . '.php' );
		}

		return $template;

	}

}
