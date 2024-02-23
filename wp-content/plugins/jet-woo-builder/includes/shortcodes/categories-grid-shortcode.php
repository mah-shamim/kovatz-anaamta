<?php

/**
 * Categories shortcode class
 */
class Jet_Woo_Categories_Grid_Shortcode extends Jet_Woo_Builder_Shortcode_Base {

	/**
	 * Shortcode tag.
	 *
	 * @return string
	 */
	public function get_tag() {
		return 'jet-woo-categories';
	}

	public function get_name() {
		return 'jet-woo-categories';
	}

	/**
	 * Shortcode attributes
	 *
	 * @return array
	 */
	public function get_atts() {

		$columns = jet_woo_builder_tools()->get_select_range( 12 );

		return apply_filters( 'jet-woo-builder/shortcodes/jet-woo-categories/atts', [
			'presets'                => [
				'type'    => 'select',
				'label'   => __( 'Presets', 'jet-woo-builder' ),
				'default' => 'preset-1',
				'options' => [
					'preset-1' => __( 'Preset 1', 'jet-woo-builder' ),
					'preset-2' => __( 'Preset 2', 'jet-woo-builder' ),
					'preset-3' => __( 'Preset 3', 'jet-woo-builder' ),
					'preset-4' => __( 'Preset 4', 'jet-woo-builder' ),
					'preset-5' => __( 'Preset 5', 'jet-woo-builder' ),
				],
			],
			'columns'                => [
				'type'               => 'select',
				'responsive'         => true,
				'label'              => __( 'Columns', 'jet-woo-builder' ),
				'options'            => $columns,
				'desktop_default'    => 4,
				'tablet_default'     => 2,
				'mobile_default'     => 1,
				'frontend_available' => true,
				'render_type'        => 'template',
				'selectors'          => [
					'{{WRAPPER}} .jet-woo-categories .jet-woo-categories__item' => '--columns: {{VALUE}}',
				],
			],
			'hover_on_touch'         => [
				'label'     => __( 'Hover on Touch', 'jet-woo-builder' ),
				'type'      => 'switcher',
				'condition' => [
					'presets' => [ 'preset-2', 'preset-3' ],
				],
			],
			'equal_height_cols'      => [
				'label' => __( 'Equal Columns Height', 'jet-woo-builder' ),
				'type'  => 'switcher',
			],
			'columns_gap'            => [
				'type'    => 'switcher',
				'label'   => __( 'Gap Between Columns', 'jet-woo-builder' ),
				'default' => 'yes',
			],
			'rows_gap'               => [
				'type'    => 'switcher',
				'label'   => __( 'Gap Between Rows', 'jet-woo-builder' ),
				'default' => 'yes',
			],
			'clickable_item'         => [
				'type'  => 'switcher',
				'label' => __( 'Make Item Clickable', 'jet-woo-builder' ),
			],
			'open_new_tab'           => [
				'label' => __( 'Open in New Window', 'jet-woo-builder' ),
				'type'  => 'switcher',
			],
			'number'                 => [
				'type'      => 'number',
				'label'     => __( 'Categories Number', 'jet-woo-builder' ),
				'default'   => 4,
				'min'       => -1,
				'max'       => 1000,
				'step'      => 1,
				'separator' => 'before',
			],
			'show_by'                => [
				'type'    => 'select',
				'label'   => __( 'Query by', 'jet-woo-builder' ),
				'default' => 'all',
				'options' => [
					'all'                   => __( 'All', 'jet-woo-builder' ),
					'parent_cat'            => __( 'Parent Category', 'jet-woo-builder' ),
					'cat_ids'               => __( 'Categories IDs', 'jet-woo-builder' ),
					'manual_selection'      => __( 'Manual Selection', 'jet-woo-builder' ),
					'current_subcategories' => __( 'Current Subcategories', 'jet-woo-builder' ),
				],
			],
			'categories_exclude_ids' => [
				'type'        => 'text',
				'label'       => __( 'Exclude by IDs', 'jet-woo-builder' ),
				'description' => __( 'Set comma separated categories IDs list (10, 22, 19 etc.).', 'jet-woo-builder' ),
				'dynamic'     => [
					'active' => true,
				],
				'condition'   => [
					'show_by' => 'all',
				],
			],
			'parent_cat_ids'         => [
				'type'      => 'text',
				'label'     => __( 'Parent Category ID', 'jet-woo-builder' ),
				'condition' => [
					'show_by' => 'parent_cat',
				],
			],
			'direct_descendants'     => [
				'type'      => 'switcher',
				'label'     => __( 'Show Only Direct Descendants.', 'jet-woo-builder' ),
				'condition' => [
					'show_by' => 'parent_cat',
				],
			],
			'cat_ids'                => [
				'type'        => 'text',
				'label'       => __( 'Include by IDs', 'jet-woo-builder' ),
				'description' => __( 'Set comma separated categories IDs list (10, 22, 19 etc.).', 'jet-woo-builder' ),
				'condition'   => [
					'show_by' => 'cat_ids',
				],
			],
			'cat_selections'         => [
				'type'      => 'select2',
				'label'     => __( 'Categories', 'jet-woo-builder' ),
				'multiple'  => true,
				'options'   => jet_woo_builder_tools()->get_product_categories(),
				'condition' => [
					'show_by' => 'manual_selection',
				],
			],
			'sort_by'                => [
				'type'    => 'select',
				'label'   => __( 'Order by', 'jet-woo-builder' ),
				'default' => 'name',
				'options' => [
					'name'       => __( 'Name', 'jet-woo-builder' ),
					'id'         => __( 'IDs', 'jet-woo-builder' ),
					'count'      => __( 'Count', 'jet-woo-builder' ),
					'menu_order' => __( 'Menu Order', 'jet-woo-builder' ),
				],
			],
			'order'                  => [
				'type'    => 'select',
				'label'   => __( 'Order', 'jet-woo-builder' ),
				'default' => 'asc',
				'options' => jet_woo_builder_tools()->order_arr(),
			],
			'hide_empty'             => [
				'type'  => 'switcher',
				'label' => __( 'Hide Empty', 'jet-woo-builder' ),
			],
			'hide_subcategories'     => [
				'type'      => 'switcher',
				'label'     => __( 'Hide Subcategories', 'jet-woo-builder' ),
				'condition' => [
					'show_by' => [ 'all', 'cat_ids' ],
				],
			],
			'hide_default_cat'       => [
				'type'      => 'switcher',
				'label'     => __( 'Hide Uncategorized', 'jet-woo-builder' ),
				'condition' => [
					'show_by' => 'all',
				],
			],
			'thumb_size'             => [
				'type'      => 'select',
				'label'     => __( 'Image Size', 'jet-woo-builder' ),
				'default'   => 'woocommerce_thumbnail',
				'options'   => jet_woo_builder_tools()->get_image_sizes(),
				'separator' => 'before',
			],
			'show_title'             => [
				'type'      => 'switcher',
				'label'     => __( 'Title', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
				'default'   => 'yes',
			],
			'title_html_tag'         => [
				'type'      => 'select',
				'label'     => __( 'HTML Tag', 'jet-woo-builder' ),
				'default'   => 'h5',
				'options'   => jet_woo_builder_tools()->get_available_title_html_tags(),
				'condition' => [
					'show_title' => 'yes',
				],
			],
			'title_trim_type'        => [
				'type'      => 'select',
				'label'     => __( 'Trim Type', 'jet-woo-builder' ),
				'default'   => 'word',
				'options'   => jet_woo_builder_tools()->get_available_title_trim_types(),
				'condition' => [
					'show_title' => 'yes',
				],
			],
			'title_length'           => [
				'type'        => 'number',
				'label'       => __( 'Length', 'jet-woo-builder' ),
				'description' => __( 'Set -1 to show full title and 0 to hide it.', 'jet-woo-builder' ),
				'min'         => -1,
				'default'     => -1,
				'condition'   => [
					'show_title' => 'yes',
				],
			],
			'title_line_wrap'        => [
				'type'         => 'switcher',
				'label'        => __( 'Enable Line Wrap', 'jet-woo-builder' ),
				'prefix_class' => 'jet-woo-builder-title-line-wrap-',
				'condition'    => [
					'show_title' => 'yes',
				],
			],
			'title_tooltip'          => [
				'type'      => 'switcher',
				'label'     => __( 'Enable Title Tooltip', 'jet-woo-builder' ),
				'separator' => 'after',
				'condition' => [
					'show_title' => 'yes',
				],
			],
			'show_count'             => [
				'type'      => 'switcher',
				'label'     => __( 'Product Count', 'jet-woo-builder' ),
				'label_on'  => __( 'Show', 'jet-woo-builder' ),
				'label_off' => __( 'Hide', 'jet-woo-builder' ),
				'default'   => 'yes',
			],
			'count_before_text'      => [
				'type'      => 'text',
				'label'     => __( 'Before Count', 'jet-woo-builder' ),
				'default'   => '(',
				'condition' => [
					'show_count' => 'yes',
				],
			],
			'count_after_text'       => [
				'type'      => 'text',
				'label'     => __( 'After Count', 'jet-woo-builder' ),
				'default'   => ')',
				'separator' => 'after',
				'condition' => [
					'show_count' => 'yes',
				],
			],
			'desc_length'            => [
				'type'        => 'number',
				'label'       => __( 'Description Length', 'jet-woo-builder' ),
				'description' => __( 'Set -1 to show full description and 0 to hide it.', 'jet-woo-builder' ),
				'min'         => -1,
				'default'     => 10,
			],
			'desc_after_text'        => [
				'type'    => 'text',
				'label'   => __( 'Trimmed After', 'jet-woo-builder' ),
				'default' => '...',
			],
		] );

	}

	/**
	 * Template presets.
	 *
	 * Get categories grid preset template.
	 *
	 * @since  1.12.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_category_preset_template() {

		$template = $this->get_legacy_category_preset_template();

		if ( ! $template ) {
			$template = jet_woo_builder()->get_template( 'widgets/global/categories-grid/presets/' . $this->get_attr( 'presets' ) . '.php' );
		}
		return $template;

	}

	/**
	 * Legacy template presets.
	 *
	 * Get categories grid legacy preset template.
	 *
	 * @since  1.12.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_legacy_category_preset_template() {
		return jet_woo_builder()->get_template( $this->get_tag() . '/global/presets/' . $this->get_attr( 'presets' ) . '.php' );
	}

	/**
	 * Query categories by attributes
	 *
	 * @return object
	 */
	public function query() {

		$defaults = apply_filters(
			'jet-woo-builder/shortcodes/jet-woo-categories/query-args',
			array(
				'post_status'  => 'publish',
				'hierarchical' => 1,
			)
		);

		$cat_args = array(
			'number'     => intval( $this->get_attr( 'number' ) ),
			'orderby'    => $this->get_attr( 'sort_by' ),
			'hide_empty' => $this->get_attr( 'hide_empty' ),
			'order'      => $this->get_attr( 'order' ),
		);

		if ( $this->get_attr( 'sort_by' ) === 'menu_order' ) {
			$cat_args['menu_order'] = $this->get_attr( 'order' );
		}

		if ( $this->get_attr( 'hide_subcategories' ) ) {
			$cat_args['parent'] = 0;
		}

		switch ( $this->get_attr( 'show_by' ) ) {
			case 'all':
				if ( '' !== $this->get_attr( 'categories_exclude_ids' ) ) {
					$cat_args['exclude'] = $this->get_attr( 'categories_exclude_ids' );
				}

				break;

			case 'parent_cat':
				if ( filter_var( $this->get_attr( 'direct_descendants' ), FILTER_VALIDATE_BOOLEAN ) ) {
					$cat_args['parent'] = $this->get_attr( 'parent_cat_ids' );
				} else {
					$cat_args['child_of'] = $this->get_attr( 'parent_cat_ids' );
				}

				break;
			case 'cat_ids' :
				$cat_args['include'] = $this->get_attr( 'cat_ids' );
				break;
			case 'manual_selection' :
				$cat_args['include'] = $this->get_attr( 'cat_selections' );
				break;
			case 'current_subcategories':
				$cat_args['parent'] = get_queried_object_id();
				break;
			default:
				break;
		}

		if ( $this->get_attr( 'hide_default_cat' ) ) {
			if ( empty( $cat_args['exclude'] ) ) {
				$cat_args['exclude'] = get_option( 'default_product_cat', 0 );
			} else {
				$cat_args['exclude'] .= ',' . get_option( 'default_product_cat', 0 );
			}
		}

		$cat_args = wp_parse_args( $cat_args, $defaults );

		$product_categories = get_terms( 'product_cat', $cat_args );

		return apply_filters( 'jet-woo-builder/shortcodes/jet-woo-categories/categories-list', $product_categories );

	}

	/**
	 * Categories shortcode function
	 *
	 * @param null $content
	 *
	 * @return string
	 */
	public function _shortcode( $content = null ) {

		$query = $this->query();

		if ( 'current_subcategories' === $this->get_attr( 'show_by' ) && empty( $query ) || is_wp_error( $query ) ) {
			return false;
		} elseif ( empty( $query ) || is_wp_error( $query ) ) {
			echo sprintf( '<h3 class="jet-woo-categories__not-found">%s</h3>', esc_html__( 'Categories not found', 'jet-woo-builder' ) );

			return false;
		}

		$loop_start = $this->get_template( 'loop-start' );
		$loop_item  = $this->get_template( 'loop-item' );
		$loop_end   = $this->get_template( 'loop-end' );

		ob_start();

		// Hook before loop start template included.
		do_action( 'jet-woo-builder/shortcodes/jet-woo-categories/loop-start' );

		include $loop_start;

		foreach ( $query as $category ) {
			setup_postdata( $category );

			// Hook before loop item template included.
			do_action( 'jet-woo-builder/shortcodes/jet-woo-categories/loop-item-start' );

			include $loop_item;

			// Hook after loop item template included.
			do_action( 'jet-woo-builder/shortcodes/jet-woo-categories/loop-item-end' );

		}

		include $loop_end;

		// Hook after loop end template included.
		do_action( 'jet-woo-builder/shortcodes/jet-woo-categories/loop-end' );

		wp_reset_postdata();

		return ob_get_clean();

	}

}
