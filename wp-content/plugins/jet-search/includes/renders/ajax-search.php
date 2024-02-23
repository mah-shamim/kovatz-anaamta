<?php
/**
 * Jet_Search_Render class
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
 * Define Jet_Search_Render class
 */
class Jet_Search_Render {

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
		return jet_search()->get_template( 'jet-ajax-search/global/' . $name . '.php' );
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

	public function preview_results() {
		$preview = ! empty( $_GET['previewResults'] ) ? filter_var( $_GET['previewResults'], FILTER_VALIDATE_BOOLEAN ) : false;
		return ( ! empty( $_GET['context'] ) && 'edit' === $_GET['context'] && $preview );
	}

	public function preview_navigation_template( $position ) {
		if ( ! $this->preview_results() ) {
			return;
		}

		$settings = $this->settings;

		if ( 'in_header' === $settings['navigation_arrows'] || 'in_footer' === $settings['navigation_arrows'] || 'both' === $settings['navigation_arrows'] ) {
			$prev_button = apply_filters( 'jet-search/ajax-search/prev-button-html', '<div role=button class="jet-ajax-search__prev-button jet-ajax-search__arrow-button jet-ajax-search__navigate-button jet-ajax-search__navigate-button-disable" data-direction="-1">%s</div>' );
			$next_button = apply_filters( 'jet-search/ajax-search/next-button-html', '<div role=button class="jet-ajax-search__next-button jet-ajax-search__arrow-button jet-ajax-search__navigate-button" data-direction="1">%s</div>' );
			$arrow       = Jet_Search_Tools::get_svg_arrows( $settings['navigation_arrows_type'] );
			$output_html = sprintf( $prev_button . $next_button, $arrow['left'], $arrow['right'] );
		}

		if ( 'top' === $position ) {
			if ( 'in_header' === $settings['bullet_pagination'] || 'both' === $settings['bullet_pagination'] ) {
				echo '<div class="jet-ajax-search__navigation-container">
					<div role=button class="jet-ajax-search__navigate-button jet-ajax-search__bullet-button jet-ajax-search__active-button" data-number="1"></div>
					<div role=button class="jet-ajax-search__navigate-button jet-ajax-search__bullet-button" data-number="2"></div>
				</div>';
			}

			if ( 'in_header' === $settings['number_pagination'] || 'both' === $settings['number_pagination'] ) {
				echo '<div class="jet-ajax-search__navigation-container">
					<div role=button class="jet-ajax-search__navigate-button jet-ajax-search__number-button jet-ajax-search__active-button" data-number="1"></div>
					<div role=button class="jet-ajax-search__navigate-button jet-ajax-search__number-button" data-number="2"></div>
				</div>';
			}

			if ( 'in_header' === $settings['navigation_arrows'] || 'both' === $settings['navigation_arrows'] ) {
				echo '<div class="jet-ajax-search__navigation-container">
						' . $output_html . '
				</div>';
			}
		}

		if ( 'bottom' === $position ) {
			if ( 'in_footer' === $settings['bullet_pagination'] || 'both' === $settings['bullet_pagination'] ) {
				echo '<div class="jet-ajax-search__navigation-container">
					<div role=button class="jet-ajax-search__navigate-button jet-ajax-search__bullet-button jet-ajax-search__active-button" data-number="1"></div>
					<div role=button class="jet-ajax-search__navigate-button jet-ajax-search__bullet-button" data-number="2"></div>
				</div>';
			}

			if ( 'in_footer' === $settings['number_pagination'] || 'both' === $settings['number_pagination'] ) {
				echo '<div class="jet-ajax-search__navigation-container">
					<div role=button class="jet-ajax-search__navigate-button jet-ajax-search__number-button jet-ajax-search__active-button" data-number="1"></div>
					<div role=button class="jet-ajax-search__navigate-button jet-ajax-search__number-button" data-number="2"></div>
				</div>';
			}

			if ( 'in_footer' === $settings['navigation_arrows'] || 'both' === $settings['navigation_arrows'] ) {
				echo '<div class="jet-ajax-search__navigation-container">
					' . $output_html . '
				</div>';
			}
		}
	}

	public function preview_template() {

		if ( ! $this->preview_results() ) {
			return;
		}

		ob_start();
		include jet_search()->get_template( 'jet-ajax-search/global/results-item.php' );
		$item = ob_get_clean();

		$preview_items = array(
			array(
				'{{{data.link}}}'             => '#',
				'{{{data.link_target_attr}}}' => '_self',
				'{{{data.thumbnail}}}'        => '<div class="jet-ajax-search__item-thumbnail"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAACWCAYAAAA8AXHiAAAAAXNSR0IArs4c6QAABjlJREFUeF7t3EtS40AQBFCZFRsOgA7ABXT/G5gLsDc+ABtWeELDJ4zRpz/VXZVVycozoW51Zb6QDRPM4Xg8Xh4fHwd+MQGpBM7n83A4nU6X+cU0TVL7cp/ACTw/Pw/jOH7Cml/Mf0FcgUUIjP5t6OeJNcOav4hLIN2gW1zb+QOLuIKqqBz79oG0CIu4KlMOtnzpXW4VFnEF01E47tpHp01YxFWYdpBlW5/Hd2ERVxAlmWPufZOXBIu4MlN3fvkeqnn8ZFjE5VxL4ngpqLJhEVdi+k4vS0VVBIu4nKrZGSsHVTEs4oqFKxdVFSziioGrBFU1LOLyjasUlQgs4vKJqwaVGCzi8oWrFpUoLOLygUsClTgs4sLGJYWqCSziwsQliaoZLOLCwiWNqiks4sLA1QJVc1jEZRtXK1RdYBGXTVwtUXWDRVy2cLVG1RUWcdnA1QNVd1jEpYurFyoVWMSlg6snKjVYxNUXV29UqrCIqw8uDVTqsIirLS4tVCZgEVcbXJqozMAiLllc2qhMwSIuGVwWUJmDRVx1uKygMgmLuMpwWUJlFhZx5eGyhso0LOJKw2URlXlYxLWNyyoqCFjEtYzLMioYWMT1G5d1VFCwiOsTFwIqOFhIwaZ99M67CgUVJKyouJBQwcKKhgsNFTSsKLgQUcHD8o4LFZULWF5xIaNyA8sbLnRUrmB5weUBlTtY6Li8oHIJCxWXJ1RuYaHh8obKNSwUXB5RuYdlHZdXVCFgWcXlGVUYWNZweUcVCpYVXBFQhYOljSsKqpCwtHBFQhUWVm9c0VCFhtULV0RU4WG1xhUVFWHNCTT6zZfIqAjrC5Y0ruioCOsKlhQuovoM9Xw+D4fT6XQZx/Em5ph/rIFRs9Zb2oS10GgJkJI13jBdz0NYK+3mQMm51jMmwkpsNwVMyjWJt3N1GZ9YO3VuwSGq9fAIK+E5sQSIqLaDI6wEWLc/iiCq/dAIaz+jnytmUPPXNE0Zq2JeSlgZvRNWeliElZjV9dsf3wr3QyOs/YwW/3tG4uKH9wQ665fwxw1l8fGJtZFbylMp5ZqyarBXERb/SaeJYMJaiLXkKVSypkmjRjYlrJsiaoDUrDXiQewYhHUVpQQMiT3E2lXciLC+wpcEIbmXoo2qWxMWf5miCtDa4vCwWj5dWu7dRIPgpqFh9Si+xz0EPYhtFRZWz8J73ktMRuVGIWFpFK1xz0obVcvDwdIsWPPeVUoKFoeCZaFYC2cocJK9JAwsS4VaOku2mMQFIWBZLNLimRLNJF3mHpblAi2fLUnPxkWuYSEUh3DGEmRuYSEVhnTWVGQuYSEWhXjmLWTuYCEXhHz2W2SuYHkoxsMMMzI3sLwUMpfiYRYXsDwUcftWgj4TPCz0ArY+ACPPBg0LOfjUb9tRZ4SFhRp4Kqjr6xBnhYSFGHQJKGRccLAiovoGhjQ7FCykYGufUGvrUTKAgYUSaCtQaG+LELCI6i9X65mYh2U9wB5PKMS3RdOwiGqfrdWMzMKyGth+1f2vsJiVSVgWg+rPJe+O1jIzB8taQHn16l5tKTtTsCwFo0uk/O5WMjQDy0og5ZXaWWkhSxOwLARhh4XMSbQzVYelHYBMjTZ30cxWFZbm4DYpyJ9KK2M1WFoDy1dnf0eNrFVgaQxqv/62J+ydeXdYvQdsWxfW7j2z7wqr52BYlfc7ba8OusHqNVC/inDv1KOLLrB6DIJbs87JW3fSHFbrAXRq8XHXlt00hdXy4D6q1Z+iVUfNYLU6sH4V/k7QoqsmsFoc1F+dtiaS7kwclvQBbcXv+zSS3YnCkjyY7wrtTifVoRgsqQPZjTzOySS6FIElcZA4tWFMWttpNazaA2DEHPOUNd1Wwaq5ccyq8KYu7bgYVukN8aLliUu6LoJVciPWg51AbufZsHJvgB0nT1+aQBYsoiqN2c+6VAPJsFI39BMhJ1lLIMVCEqyUjVhDrATe3t6Gh4eH1aF3YRFVLDA5076/vw/39/eLSzZhEVVOzDGvXTOyCouoYkIpmXrJyiIsoiqJN/aal5eX4enp6SeEP7CIKjaQmuk/Pj6Gu7u7/1v8gkVUNbFy7ZzAt6EfWPOLaZqYDhOoTmDGNY7jcDgej5f5Bb+YgFQCr6+vwz/MJlUaZd8AGAAAAABJRU5ErkJggg=="></div>',
				'{{{data.before_title}}}'     => '',
				'{{{data.title}}}'            => 'Preview title #1',
				'{{{data.after_title}}}'      => '',
				'{{{data.before_content}}}'   => '',
				'{{{data.content}}}'          => 'Content of the preview item #1',
				'{{{data.after_content}}}'    => '',
				'{{{data.rating}}}'           => '',
				'{{{data.price}}}'            => '',
			),
			array(
				'{{{data.link}}}'             => '#',
				'{{{data.link_target_attr}}}' => '_self',
				'{{{data.thumbnail}}}'        => '<div class="jet-ajax-search__item-thumbnail"><img src="data:image/png;base64,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"></div>',
				'{{{data.before_title}}}'     => '',
				'{{{data.title}}}'            => 'Preview title #2',
				'{{{data.after_title}}}'      => '',
				'{{{data.before_content}}}'   => '',
				'{{{data.content}}}'          => 'Content of the preview item #2',
				'{{{data.after_content}}}'    => '',
				'{{{data.rating}}}'           => '',
				'{{{data.price}}}'            => '',
			),
		);

		echo '<div class="jet-ajax-search__results-slide">';
		foreach ( $preview_items as $item_data ) {
			echo str_replace( array_keys( $item_data ), array_values( $item_data ), $item );
		}
		echo '</div>';

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

		$allowed = apply_filters( 'jet-search/ajax-search/data-settings', array(

			// Query
			'search_source',
			'search_taxonomy',
			'include_terms_ids',
			'exclude_terms_ids',
			'exclude_posts_ids',
			'custom_fields_source',
			'limit_query',
			'limit_query_tablet',
			'limit_query_mobile',
			'limit_query_in_result_area',
			'results_order_by',
			'results_order',
			'sentence',
			'search_in_taxonomy',
			'search_in_taxonomy_source',

			// Result area
			'results_area_width_by',
			'results_area_custom_width',
			'results_area_custom_position',
			'thumbnail_visible',
			'thumbnail_size',
			'thumbnail_placeholder',
			'post_content_source',
			'post_content_custom_field_key',
			'post_content_length',
			'show_product_price',
			'show_product_rating',
			'show_result_new_tab',
			'highlight_searched_text',
			'symbols_for_start_searching',
			'search_by_empty_value',

			// Navigations
			'bullet_pagination',
			'number_pagination',
			'navigation_arrows',
			'navigation_arrows_type',

			// Custom Fields
			'show_title_related_meta',
			'meta_title_related_position',
			'title_related_meta',
			'show_content_related_meta',
			'meta_content_related_position',
			'content_related_meta',

			// Notification
			'negative_search',
			'server_error'
		), $settings );

		$result = array();

		foreach ( $allowed as $setting ) {
			$result[ $setting ] = isset( $settings[ $setting ] ) ? $settings[ $setting ] : '';
		}

		$lang = jet_search_compatibility()->get_current_lang();

		if ( '' != $lang ) {
			$result['lang'] = $lang;
		}

		$result['search_source'] = ! empty( $result['search_source'] ) ? $result['search_source'] : 'any';

		$this->set_current_query_args( $result );


		return esc_attr( json_encode( $result ) );
	}

	/**
	 * Get query settings json.
	 */
	public function get_query_settings_json() {

		$settings = $this->get_settings_for_display();

		$allowed = apply_filters( 'jet-search/ajax-search/query-settings', array(
			'search_source',
			'search_taxonomy',
			'include_terms_ids',
			'exclude_terms_ids',
			'exclude_posts_ids',
			'custom_fields_source',
			'results_order_by',
			'results_order',
			'sentence',
			'search_in_taxonomy',
			'search_in_taxonomy_source',
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
	 * Get post types string.
	 *
	 * @since  1.1.0
	 * @return string
	 */
	public function get_post_types_string() {

		$post_types = $this->get_settings( 'search_source' );

		if ( empty( $post_types ) ) {
			return '';
		}

		return esc_attr( join( ',', $post_types ) );
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

		$select_wrapper_html = apply_filters( 'jet-search/ajax-search/categories-wrapper-select', '<div class="jet-ajax-search__categories">%1$s%2$s</div>' );
		$select_icon_html    = apply_filters( 'jet-search/ajax-search/categories-select-icon', '
			<i class="jet-ajax-search__categories-select-icon">
				<svg xmlns="http://www.w3.org/2000/svg" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd" viewBox="0 0 512 336.36"><path fill-rule="nonzero" d="M42.47.01 469.5 0C492.96 0 512 19.04 512 42.5c0 11.07-4.23 21.15-11.17 28.72L294.18 320.97c-14.93 18.06-41.7 20.58-59.76 5.65-1.8-1.49-3.46-3.12-4.97-4.83L10.43 70.39C-4.97 52.71-3.1 25.86 14.58 10.47 22.63 3.46 32.57.02 42.47.01z"/></svg>
			</i>'
		);

		$placeholder = ! empty( $settings['search_category_select_placeholder'] ) ? $settings['search_category_select_placeholder'] : esc_html__( 'All Categories', 'jet-search' );
		$taxonomy    = ! empty( $settings['search_taxonomy'] ) ? $settings['search_taxonomy'] : 'category';
		$include_ids = ! empty( $settings['include_terms_ids'] ) ? $settings['include_terms_ids'] : array();
		$exclude_ids = ! empty( $settings['exclude_terms_ids'] ) ? $settings['exclude_terms_ids'] : array();

		$include_categories = apply_filters( 'jet-search/ajax-search/categories-select/include-categories', $include_ids, $taxonomy );
		$exclude_categories = apply_filters( 'jet-search/ajax-search/categories-select/exclude-categories', $exclude_ids, $taxonomy );

		$args = apply_filters( 'jet-search/ajax-search/categories-select/args', array(
			'id'              => 'jet_ajax_search_categories_' . $this->get_id(),
			'name'            => 'jet_ajax_search_categories',
			'class'           => 'jet-ajax-search__categories-select',
			'echo'            => 0,
			'show_option_all' => $placeholder,
			'hierarchical'    => 1,
			'hide_if_empty'   => true,
			'include'         => $include_categories,
			'exclude'         => $exclude_categories,
			'taxonomy'        => $taxonomy,
			'orderby'         => 'name',
		) );

		$categories_list = wp_dropdown_categories( $args );

		if ( is_wp_error( $categories_list ) || empty( $categories_list ) ) {
			return '';
		}

		$categories_list = str_replace( 'name=\'jet_ajax_search_categories\'', 'name="jet_ajax_search_categories" data-placeholder="' . $placeholder . '"' , $categories_list );

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
