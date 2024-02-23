<?php
/**
 * Jet_Search_Suggestions_Render class
 *
 * @package   jet-search
 * @author    Zemez
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Search_Suggestions_Render class
 */
class Jet_Search_Suggestions_Render {

	public $settings       = array();
	public $id             = 0;
	public $processed_item = false;
	public $attributes     = array();
	public $current_query  = null;

	public function __construct( $settings = array(), $id = null ) {
		$this->settings = $settings;

		if ( $id ) {
			$this->id = $id;
		}

		jet_search_assets()->enqueue_scripts( $settings );
	}

	public function render() {
		include $this->get_global_template( 'index' );
	}

	public function get_id() {
		return $this->id;
	}

	/**
	 * Get global affected template
	 *
	 * @param  string $name Template name
	 * @return string
	 */
	public function get_global_template( $name = null ) {
		return jet_search()->get_template( 'jet-search-suggestions/global/' . $name . '.php' );
	}

	/**
	 * Include global template if any of passed settings is defined
	 *
	 * @param  string $name    File name.
	 * @param  array $settings Settings names list.
	 * @return void
	 */
	public function glob_inc_if( $name = null, $settings = array() ) {

		foreach ( $settings as $setting ) {

			$val = $this->get_settings_for_display( $setting );

			if ( ! empty( $val ) ) {
				include $this->get_global_template( $name );
				return;
			}

		}

	}

	public function preview_focus_items() {
		$preview = ! empty( $_GET['previewFocusItems'] ) ? filter_var( $_GET['previewFocusItems'], FILTER_VALIDATE_BOOLEAN ) : false;
		return ( ! empty( $_GET['context'] ) && 'edit' === $_GET['context'] && $preview );
	}

	public function preview_focus_items_template() {

		if ( ! $this->preview_focus_items() ) {
			return;
		}

		$manual_items  = ! empty( $_GET['previewFocusManualItems'] ) ? $_GET['previewFocusManualItems'] : "";
		$preview_items = array();

		ob_start();
		include jet_search()->get_template( 'jet-search-suggestions/global/focus-suggestion-item.php' );
		$item = ob_get_clean();

		if ( ""  != $manual_items ) {
			$list       = explode( ',', $manual_items );
			$manual_ist = array();

			foreach ( $list as $i => $suggestion ) {
				$manual_ist[$i] = array( 'name' => $suggestion );
			}

			$items_quantity = count( $manual_ist );

			for ( $i=0; $i < $items_quantity; $i++ ) {
				$preview_items[] = array(
					'{{{data.name}}}' => $manual_ist[$i]['name'],
				);
			}
		} else {
			$items_quantity = isset( $_GET['previewFocusItemsNumber'] ) ? $_GET['previewFocusItemsNumber'] : 5;

			for ( $i=0; $i < $items_quantity; $i++ ) {
				$preview_items[] = array(
					'{{{data.name}}}' => 'suggestion title #' . ( $i + 1 ),
				);
			}
		}

		foreach ( $preview_items as $item_data ) {
			echo str_replace( array_keys( $item_data ), array_values( $item_data ), $item );
		}

	}

	public function preview_inline_items() {
		$preview = ! empty( $_GET['previewInlineItems'] ) ? filter_var( $_GET['previewInlineItems'], FILTER_VALIDATE_BOOLEAN ) : false;
		return ( ! empty( $_GET['context'] ) && 'edit' === $_GET['context'] && $preview );
	}

	public function preview_inline_items_template() {

		if ( ! $this->preview_inline_items() ) {
			return;
		}

		$manual_items  = ! empty( $_GET['previewInlineManualItems'] ) ? $_GET['previewInlineManualItems'] : "";
		$preview_items = array();

		ob_start();
		include jet_search()->get_template( 'jet-search-suggestions/global/inline-suggestion-item.php' );
		$item = ob_get_clean();

		if ( ""  != $manual_items ) {
			$list       = explode( ',', $manual_items );
			$manual_ist = array();

			foreach ( $list as $i => $suggestion ) {
				$manual_ist[$i] = array( 'name' => $suggestion );
			}

			$items_quantity = count( $manual_ist );

			for ( $i=0; $i < $items_quantity; $i++ ) {
				$preview_items[] = array(
					'{{{data.name}}}' => $manual_ist[$i]['name'],
				);
			}
		} else {
			$items_quantity = ! empty( $_GET['previewInlineItemsNumber'] ) ? $_GET['previewInlineItemsNumber'] : 5;

			for ( $i=0; $i < $items_quantity; $i++ ) {
				$preview_items[] = array(
					'{{{data.name}}}' => 'suggestion title #' . ( $i + 1 ),
				);
			}
		}

		foreach ( $preview_items as $item_data ) {
			echo str_replace( array_keys( $item_data ), array_values( $item_data ), $item );
		}

	}

	/**
	 * Add render attributes by slug
	 *
	 * @param [type] $slug      [description]
	 * @param string $attribute [description]
	 * @param [type] $value     [description]
	 */
	public function add_render_attribute( $slug, $attribute = '', $value = null ) {

		if ( ! isset( $this->attributes[ $slug ] ) ) {
			$this->attributes[ $slug ] = array();
		}

		if ( ! is_array( $attribute ) ) {
			$this->attributes[ $slug ][ $attribute ] = $value;
		} else {
			foreach ( $attribute as $attr_name => $attr_value ) {
				$this->add_render_attribute( $slug, $attr_name, $attr_value );
			}
		}

	}

	public function print_render_attribute_string( $slug ) {

		if ( empty( $this->attributes[ $slug ] ) ) {
			return;
		}

		echo implode( ' ', array_map( function( $attr, $value ) {
			return $attr . '="' . esc_attr( $value ) . '"';
		}, array_keys( $this->attributes[ $slug ] ), array_values( $this->attributes[ $slug ] ) ) );

	}

	/**
	 * Print HTML icon template
	 *
	 * @param  array  $setting
	 * @param  string $format
	 * @param  string $icon_class
	 * @param  bool   $echo
	 *
	 * @return void|string
	 */
	public function icon( $setting = null, $format = '%s', $icon_class = '', $echo = true ) {

		if ( false === $this->processed_item ) {
			$settings = $this->get_settings_for_display();
		} else {
			$settings = $this->processed_item;
		}

		$new_setting = 'selected_' . $setting;

		// Pre-process Gutenberg icon
		if ( ! empty( $settings[ $new_setting ] ) ) {

			$icon_src = null;

			if ( is_array( $settings[ $new_setting ] ) && ! empty( $settings[ $new_setting ]['src'] ) ) {
				$icon_src = $settings[ $new_setting ]['src'];
			} elseif ( ! is_array( $settings[ $new_setting ] ) ) {
				$icon_src = $settings[ $new_setting ];
			}

			if ( $icon_src ) {
				printf( $format, $icon_src );
				return;
			}

		}

		$migrated = isset( $settings['__fa4_migrated'][ $new_setting ] );
		$is_new   = empty( $settings[ $setting ] ) && class_exists( 'Elementor\Icons_Manager' ) && Elementor\Icons_Manager::is_migration_allowed();

		$icon_html = '';

		if ( $is_new || $migrated ) {

			$attr = array( 'aria-hidden' => 'true' );

			if ( ! empty( $icon_class ) ) {
				$attr['class'] = $icon_class;
			}

			if ( isset( $settings[ $new_setting ] ) && class_exists( 'Elementor\Icons_Manager' ) ) {
				ob_start();
				Elementor\Icons_Manager::render_icon( $settings[ $new_setting ], $attr );

				$icon_html = ob_get_clean();
			}

		} else if ( ! empty( $settings[ $setting ] ) ) {

			if ( empty( $icon_class ) ) {
				$icon_class = $settings[ $setting ];
			} else {
				$icon_class .= ' ' . $settings[ $setting ];
			}

			$icon_html = sprintf( '<i class="%s" aria-hidden="true"></i>', $icon_class );
		}

		if ( empty( $icon_html ) ) {
			return;
		}

		if ( ! $echo ) {
			return sprintf( $format, $icon_html );
		}

		printf( $format, $icon_html );

	}

	/**
	 * Print HTML template
	 *
	 * @param  string $setting Passed setting.
	 * @param  string $format  Required markup.
	 * @return mixed
	 */
	public function html( $setting = null, $format = '%s' ) {

		if ( is_array( $setting ) ) {
			$key     = $setting[1];
			$setting = $setting[0];
		}

		$val = $this->get_settings_for_display( $setting );

		if ( ! is_array( $val ) && '0' === $val ) {
			printf( $format, $val );
		}

		if ( is_array( $val ) && empty( $val[ $key ] ) ) {
			return '';
		}

		if ( ! is_array( $val ) && empty( $val ) ) {
			return '';
		}

		if ( is_array( $val ) ) {
			printf( $format, $val[ $key ] );
		} else {
			printf( $format, $val );
		}

	}

	/**
	 * Returns all settings
	 *
	 * Used for backward compatibility
	 *
	 * @param  [type] $setting [description]
	 * @return [type]          [description]
	 */
	public function get_settings_for_display( $setting = null ) {

		if ( ! $setting ) {
			return $this->settings;
		}

		return isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : null;
	}

	/**
	 * Returns all settings
	 *
	 * Used for backward compatibility
	 *
	 * @param  [type] $setting [description]
	 * @return [type]          [description]
	 */
	public function get_settings( $setting = null ) {

		$settings = $this->get_settings_for_display();

		if ( ! $setting ) {
			return $settings;
		}

		return isset( $settings[ $setting ] ) ? $settings[ $setting ] : false;

	}

	/**
	 * Get settings json.
	 */
	public function get_settings_json() {

		$settings = $this->get_settings_for_display();

		$allowed = apply_filters( 'jet-search/search-suggestions/data-settings', array(
			'search_taxonomy',
			'search_suggestions_quantity_limit',
			'show_search_suggestions_list_inline',
			'search_suggestions_list_inline',
			'search_suggestions_list_inline_quantity',
			'search_suggestions_list_inline_manual',
			'show_search_suggestions_list_on_focus',
			'search_suggestions_list_on_focus',
			'search_suggestions_list_on_focus_quantity',
			'search_suggestions_list_on_focus_manual',
			'show_search_suggestions_list_on_focus_preloader',
			'highlight_searched_text',
			//'search_results_url'
		), $settings );

		$result = array();

		foreach ( $allowed as $setting ) {
			$result[ $setting ] = isset( $settings[ $setting ] ) ? $settings[ $setting ] : '';
		}

		$this->set_current_query_args( $result );

		return esc_attr( json_encode( $result ) );
	}

	/**
	 * Get suggestions settings json.
	 */
	public function get_suggestions_settings_json() {

		$settings = $this->get_settings_for_display();

		$allowed = apply_filters( 'jet-search/search-suggestions/settings', array(
			'search_taxonomy',
		), $settings );

		$result = array();

		foreach ( $allowed as $setting ) {

			if ( empty( $settings[ $setting ] ) ) {
				continue;
			}

			$result[ $setting ] = $settings[ $setting ];
		}

		$result['search_source'] = ! empty( $result['search_source'] ) ? $result['search_source'] : 'any';

		// For compatibility with Product Search Page created with Elementor Pro
		if ( is_array( $result['search_source'] ) && 1 === count( $result['search_source'] ) ) {
			$result['search_source'] = $result['search_source'][0];
		}

		$this->set_current_query_args( $result );

		return esc_attr( json_encode( $result ) );
	}

	/**
	 * Get categories list
	 *
	 * @since  1.0.0
	 * @since  1.0.1 Added 'id' argument for 'wp_dropdown_categories' function.
	 * @since  1.1.0 Added filter `jet-search/ajax-search/categories-select/args`
	 * @return string
	 */
	public function get_categories_list() {
		$settings           = $this->get_settings_for_display();
		$show_category_list = ! empty( $settings['show_search_category_list'] ) ? $settings['show_search_category_list'] : false;
		$visible            = filter_var( $show_category_list, FILTER_VALIDATE_BOOLEAN );

		if ( ! $visible ) {
			return '';
		}

		$select_wrapper_html = apply_filters( 'jet-search/search-suggestions/categories-wrapper-select', '<div class="jet-search-suggestions__categories">%1$s%2$s</div>' );
		$select_icon_html    = apply_filters( 'jet-search/search-suggestions/categories-select-icon', '
			<i class="jet-search-suggestions__categories-select-icon">
				<svg xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 336.36"><path fill-rule="nonzero" d="M42.47.01 469.5 0C492.96 0 512 19.04 512 42.5c0 11.07-4.23 21.15-11.17 28.72L294.18 320.97c-14.93 18.06-41.7 20.58-59.76 5.65-1.8-1.49-3.46-3.12-4.97-4.83L10.43 70.39C-4.97 52.71-3.1 25.86 14.58 10.47 22.63 3.46 32.57.02 42.47.01z"/></svg>
			</i>'
		);

		$placeholder = ! empty( $settings['search_category_select_placeholder'] ) ? $settings['search_category_select_placeholder'] : esc_html__( 'All Categories', 'jet-search' );
		$taxonomy    = ! empty( $settings['search_taxonomy'] ) ? $settings['search_taxonomy'] : 'category';

		$args = apply_filters( 'jet-search/search-suggestions/categories-select/args', array(
			'id'              => 'jet_search_suggestions_categories_' . $this->get_id(),
			'name'            => 'jet_search_suggestions_categories',
			'class'           => 'jet-search-suggestions__categories-select',
			'echo'            => 0,
			'show_option_all' => $placeholder,
			'hierarchical'    => 1,
			'hide_if_empty'   => true,
			'taxonomy'        => $taxonomy,
			'orderby'         => 'name',
		) );

		$categories_list = wp_dropdown_categories( $args );

		if ( is_wp_error( $categories_list ) || empty( $categories_list ) ) {
			return '';
		}

		$categories_list = str_replace( 'name=\'jet_search_suggestions_categories\'', 'name="jet_search_suggestions_categories" data-placeholder="' . $placeholder . '"' , $categories_list );

		return sprintf( $select_wrapper_html, $categories_list, $select_icon_html );
	}

	/**
	 * Set current query arguments.
	 *
	 * @param array $args
	 */
	public function set_current_query_args( &$args ) {
		$is_current_query = $this->get_settings( 'current_query' );

		if ( filter_var( $is_current_query, FILTER_VALIDATE_BOOLEAN ) ) {
			$current_query = $this->get_current_query_args();

			if ( ! empty( $current_query ) ) {
				$args['current_query'] = $current_query;
			}
		}
	}

	/**
	 * Get current query arguments.
	 *
	 * @return array
	 */
	public function get_current_query_args() {

		if ( null === $this->current_query ) {
			global $wp_query;

			$this->current_query = $wp_query->query;

			if ( class_exists( 'WooCommerce' ) ) {
				if ( is_shop() || is_product_taxonomy() || is_post_type_archive( 'product' ) ) {
					$this->current_query['post_type'] = 'product';
				}
			}
		}

		return $this->current_query;
	}
}
