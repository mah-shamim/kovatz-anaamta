<?php
/**
 * Bricks views manager
 */

namespace Jet_Engine\Bricks_Views\Listing;

use Bricks\Conditions;
use Bricks\Database;

/**
 * Define Manager class
 */
class Manager {

	protected $slug = 'bricks';
	protected $css_rendered = [];
	protected $settings = [];

	public $render;

	public function __construct() {

		add_filter( 'jet-engine/templates/listing-views', [ $this, 'add_view' ] );

		add_filter( 'jet-engine/templates/edit-url/' . $this->get_slug(), [ $this, 'edit_url' ], 10, 2 );
		add_filter( 'jet-engine/listings/ajax/settings-by-id/' . $this->get_slug(), [
			$this,
			'get_ajax_settings'
		], 10, 3 );
		add_action( 'jet-engine/templates/created/' . $this->get_slug(), [ $this, 'set_template_meta' ] );

		add_action( 'save_post_' . jet_engine()->post_type->slug(), [ $this, 'reset_assets_cache' ] );

		add_filter( 'jet-engine/listing/grid/masonry-options', [ $this, 'set_masonry_gap' ], 10, 3 );

		add_action( 'jet-smart-filters/render/ajax/before', [ $this, 'register_bricks_dynamic_data_on_ajax' ] );
		add_action( 'jet-engine/ajax-handlers/before-do-ajax', [ $this, 'register_bricks_dynamic_data_on_ajax' ] );

		add_action( 'jet-smart-filters/render/ajax/before', [ $this, 'set_page_data' ] );

		add_action( 'jet-engine/listing/grid/before-render', [ $this, 'set_global_post_for_listing' ] );

		add_filter( 'bricks/link_css_selectors', [ $this, 'link_css_selectors' ], 10, 1 );
		add_action( 'jet-engine/listing-element/before-render', [ $this, 'set_current_object_in_bricks_loop' ] );
		add_action( 'bricks/query/after_loop', [ $this, 'reset_current_object_in_bricks_loop' ] );

		add_action( 'jet-engine/listing/grid/before', [ $this, 'actions_before_grid_items' ], 10 );
		add_action( 'jet-engine/listing/grid/after', [ $this, 'actions_after_grid_items' ], 10 );

		require_once jet_engine()->bricks_views->component_path( 'listing/render.php' );
		$this->render = new Render();

		$this->ensure_listing_post_type_support();

	}

	public function set_global_post_for_listing() {

		$post_id = ! empty( $_REQUEST['postId'] ) ? $_REQUEST['postId'] : false;

		if ( ! $post_id ) {
			$post_id = isset( Database::$page_data['original_post_id'] ) ? Database::$page_data['original_post_id'] : Database::$page_data['preview_or_post_id'];
		}

		if ( ! $post_id ) {
			return;
		}

		if ( get_post_type( $post_id ) === jet_engine()->post_type->slug() ) {
			return;
		}

		global $post;
		$post = get_post( $post_id );

	}

	public function get_slug() {
		return $this->slug;
	}

	public function register_bricks_dynamic_data_on_ajax() {
		global $wp_filter;
		if ( isset( $wp_filter['wp'][8] ) ) {
			foreach ( $wp_filter['wp'][8] as $callback ) {
				if ( is_array( $callback['function'] ) && is_object( $callback['function'][0] ) ) {
					if ( 'Bricks\Integrations\Dynamic_Data\Providers' === get_class( $callback['function'][0] ) ) {
						call_user_func( $callback['function'] );
						break;
					}
				}
			}
		}
	}

	public function get_ajax_settings( $settings = [], $element_id = null, $post_id = 0 ) {

		if ( ! $element_id || ! $post_id ) {
			return $settings;
		}

		$bricks_data = get_post_meta( $post_id, BRICKS_DB_PAGE_CONTENT, true );

		if ( empty( $bricks_data ) ) {
			return $settings;
		}

		foreach ( $bricks_data as $el_id => $element ) {
			if ( $element['id'] === $element_id ) {
				return $element['settings'];
			}
		}

		return $settings;
	}

	public function set_masonry_gap( $data = array(), $settings = array(), $render = null ) {

		$data['gap'] = [
			'horizontal' => ! empty( $settings['horizontal_gap'] ) ? absint( $settings['horizontal_gap'] ) : 20,
			'vertical'   => ! empty( $settings['vertical_gap'] ) ? absint( $settings['vertical_gap'] ) : 20,
		];

		return $data;

	}

	public function get_bricks_query( $args = [] ) {

		if ( ! class_exists( '\Jet_Engine\Bricks_Views\Listing\Bricks_Query' ) ) {
			require_once jet_engine()->bricks_views->component_path( 'listing/bricks-query.php' );
		}

		return new Bricks_Query( $args );

	}

	public function reset_assets_cache( $post_id ) {

		if ( ! class_exists( '\Jet_Engine\Bricks_Views\Listing\Assets' ) ) {
			require_once jet_engine()->bricks_views->component_path( 'listing/assets.php' );
		}

		delete_post_meta( $post_id, Assets::$css_cache_key );
		delete_post_meta( $post_id, Assets::$fonts_cache_key );
		delete_post_meta( $post_id, Assets::$font_families_cache_key );
		delete_post_meta( $post_id, Assets::$icons_cache_key );

	}

	public function ensure_listing_post_type_support() {

		if ( ! is_array( \Bricks\Database::$global_settings['postTypes'] ) ) {
			\Bricks\Database::$global_settings['postTypes'] = [];
		}

		if ( ! in_array( jet_engine()->post_type->slug(), \Bricks\Database::$global_settings['postTypes'] ) ) {
			\Bricks\Database::$global_settings['postTypes'][] = jet_engine()->post_type->slug();
		}

	}

	public function set_template_meta( $post_id ) {
		update_post_meta( $post_id, '_bricks_editor_mode', 'bricks' );
	}

	public function edit_url( $url, $post_id ) {
		return add_query_arg( [ 'bricks' => 'run' ], get_permalink( $post_id ) );
	}

	public function add_view( $views ) {
		$views[ $this->get_slug() ] = __( 'Bricks', 'jet-engine' );

		return $views;
	}

	public function render_assets( $listing_id ) {

		if ( ! class_exists( '\Jet_Engine\Bricks_Views\Listing\Assets' ) ) {
			require_once jet_engine()->bricks_views->component_path( 'listing/assets.php' );
			new Assets();
		}

		if ( ! in_array( $listing_id, $this->css_rendered ) ) {
			$this->css_rendered[] = $listing_id;
			printf( '<style>%s</style>', Assets::generate_inline_css( $listing_id ) );
			Assets::jet_print_editor_fonts();
		}

	}

	public function link_css_selectors( $selectors ) {
		$selectors[] = '.jet-listing-dynamic-link__link';

		return $selectors;
	}

	public function actions_before_grid_items() {
		add_filter( 'bricks/builder/data_post_id', [ $this, 'set_post_id' ], 10 );
	}

	public function actions_after_grid_items() {
		remove_filter( 'bricks/builder/data_post_id', [ $this, 'set_post_id' ] );
	}

	// Integration of bricks condition into the listing grid widget
	public function set_post_id( $post_id ) {
		return jet_engine()->listings->data->get_current_object_id();
	}

	// Set current User or Term object to dynamic widgets in a bricks loop
	public function set_current_object_in_bricks_loop() {
		if ( ! \Bricks\Query::is_looping() ) {
			return;
		}

		if ( in_array( \Bricks\Query::get_query_object_type(), [ 'user', 'term' ] ) ) {
			jet_engine()->listings->data->set_current_object( \Bricks\Query::get_loop_object() );
		}
	}

	// Reset current User or Term object for dynamic widgets in a bricks loop
	public function reset_current_object_in_bricks_loop() {
		if ( in_array( \Bricks\Query::get_query_object_type(), [ 'user', 'term' ] ) ) {
			jet_engine()->listings->data->reset_current_object();
		}
	}

	// Set page data for list grid during ajax filter
	public function set_page_data() {
		Database::set_page_data();
	}

}
