<?php
/**
 * Bricks views manager
 */

namespace Jet_Engine\Bricks_Views\Listing;

use Jet_Engine\Query_Builder\Manager as Query_Manager;

/**
 * Define render class
 */
class Render {

	private $current_query;

	public function __construct() {
		add_filter( 'jet-engine/listing/content/bricks', [ $this, 'get_listing_content_cb' ], 10, 2 );
		add_filter( 'jet-engine/listing/grid/columns', [ $this, 'remap_columns' ], 10, 2 );
		add_filter( 'jet-engine/listing/posts-loop/start-from', [ $this, 'set_starting_index' ], 10, 3 );
		add_filter( 'jet-engine/listing/pre-get-item-content', [ $this, 'set_listing_loop_index' ], 10, 5 );

		add_action( 'jet-engine/listing/grid/before-render', [ $this, 'set_query_on_render' ] );
		add_action( 'jet-engine/listing/grid/after-render', [ $this, 'destroy_bricks_query' ] );

		add_action( 'jet-smart-filters/render/ajax/before', [ $this, 'set_query_on_filters_ajax' ] );
		add_action( 'jet-engine/ajax-handlers/before-do-ajax', [ $this, 'set_query_on_listing_ajax' ], 10, 2 );
	}

	public function set_bricks_query( $listing_id = 0, $settings = [] ) {

		if ( ! $listing_id ) {
			$listing_id = isset( $settings['lisitng_id'] ) ? absint( $settings['lisitng_id'] ) : 0;
		}

		if ( $listing_id && jet_engine()->bricks_views->is_bricks_listing( $listing_id ) ) {
			$this->current_query[ $listing_id ] = jet_engine()->bricks_views->listing->get_bricks_query( [
				'id'       => 'jet-engine-listing-grid',
				'settings' => $settings,
			] );
		}

	}

	public function get_current_query( $listing_id ) {
		return $this->current_query[ $listing_id ];
	}

	public function set_query_on_filters_ajax() {

		$settings   = isset( $_REQUEST['settings'] ) ? $_REQUEST['settings'] : [];
		$listing_id = ! empty ( $settings['lisitng_id'] ) ? $settings['lisitng_id'] : 0;
		$this->set_bricks_query( $listing_id, $settings );

	}

	public function set_query_on_listing_ajax( $ajax_handler, $request ) {

		$settings   = $request['widget_settings'] ?? $request['settings'] ?? [];
		$listing_id = ! empty ( $settings['lisitng_id'] ) ? $settings['lisitng_id'] : 0;
		$this->set_bricks_query( $listing_id, $settings );

	}

	public function set_query_on_render( $render ) {

		$listing_id = $render->get_settings( 'lisitng_id' );
		$this->set_bricks_query( $listing_id, $render->get_settings() );

	}

	public function destroy_bricks_query( $render ) {

		$listing_id = $render->get_settings( 'lisitng_id' );
		$current_query = $this->get_current_query( $listing_id );

		if ( $current_query ) {
			$current_query->is_looping = false;

			// Destroy Query to explicitly remove it from global store
			$current_query->destroy();

			unset( $this->current_query[ $listing_id ] );
		}

	}

	public function remap_columns( $columns, $settings ) {

		if ( ! empty( $settings['columns:tablet_portrait'] ) ) {
			$columns['tablet'] = absint( $settings['columns:tablet_portrait'] );
		}

		if ( ! empty( $settings['columns:mobile_portrait'] ) ) {
			$columns['mobile'] = absint( $settings['columns:mobile_portrait'] );
		}

		if ( ! empty( $settings['columns:mobile_landscape'] ) ) {
			$columns['mobile_landscape'] = absint( $settings['columns:mobile_landscape'] );
		}

		return $columns;

	}

	public function get_listing_content_cb( $result, $listing_id ) {

		$bricks_data = get_post_meta( $listing_id, BRICKS_DB_PAGE_CONTENT, true );

		if ( ! $bricks_data ) {
			return;
		}

		ob_start();
		jet_engine()->bricks_views->listing->render_assets( $listing_id );
		$result = ob_get_clean();

		// Prepare flat list of elements for recursive calls
		// Default Bricks logic not used in this case because it reset elements list after rendering
		foreach ( $bricks_data as $element ) {
			\Bricks\Frontend::$elements[ $element['id'] ] = $element;
		}

		// Prevent errors when handling non-post queries with WooCommerce is active
		if ( function_exists( 'WC' ) && \Bricks\Theme::instance()->woocommerce ) {
			remove_filter(
				'bricks/builder/data_post_id',
				[ \Bricks\Theme::instance()->woocommerce, 'maybe_set_post_id' ],
				10, 1
			);
		}

		if ( is_array( $bricks_data ) && count( $bricks_data ) ) {

			foreach ( $bricks_data as $element ) {

				if ( ! empty( $element['parent'] ) ) {
					continue;
				}

				$result .= \Bricks\Frontend::render_element( $element );

			}

		}

		if ( function_exists( 'WC' ) && \Bricks\Theme::instance()->woocommerce ) {
			add_filter(
				'bricks/builder/data_post_id',
				[ \Bricks\Theme::instance()->woocommerce, 'maybe_set_post_id' ],
				10, 1
			);
		}

		// Filter required for the compatibility with default Bricks dynamic data
		return apply_filters(
			'bricks/dynamic_data/render_content',
			$result,
			jet_engine()->listings->data->get_current_object(),
			null
		);

	}

	public function set_starting_index() {
		return 0;
	}

	/**
	 * Set the loop index for the current listing based on AJAX requests and pagination.
	 *
	 * @param string   $content    The current content.
	 * @param WP_Post  $post_obj   The current post object.
	 * @param int      $i          The loop index.
	 * @param stdClass $instance   The instance of the listing.
	 *
	 * @return string  Modified content with adjusted loop index.
	 */
	public function set_listing_loop_index( $content, $post_obj, $i, $instance ) {
		$listing_id = $instance->listing_id ?? 0;
		$jsf_action = $_REQUEST['action'] ?? '';
		$jsf_paged  = $_REQUEST['paged'] ?? 1;

		// Adjust loop index for Jet Smart Filters AJAX requests with pagination.
		if ( $jsf_action === 'jet_smart_filters' && $jsf_paged ) {
			$defaults       = $_REQUEST['defaults'] ?? [];
			$posts_per_page = $defaults['posts_per_page'] ?? 0;

			// If posts per page is not set, attempt to retrieve it from the specified query ID.
			if ( ! $posts_per_page ) {
				$query_id       = $_REQUEST['props']['query_id'] ?? 0;
				$posts_per_page = $this->get_query_builder_prop( $query_id, 'posts_per_page' );
			}

			if ( $posts_per_page ) {
				$i += $posts_per_page * ( $jsf_paged - 1 );
			}

			// Override the listing ID with the one provided in the AJAX request.
			$listing_id = $_REQUEST['settings']['lisitng_id'] ?? 0;
		}

		// Check if the current request is an AJAX request for loading more listings.
		elseif ( jet_engine()->listings->is_listing_ajax( 'listing_load_more' ) ) {
			$page           = $_REQUEST['page'] ?? 1;
			$query          = $_REQUEST['query'] ?? [];
			$posts_per_page = $query['posts_per_page'] ?? 0;

			// If posts per page is not set, attempt to retrieve it from the specified query ID.
			if ( ! $posts_per_page ) {
				$query_id       = $query['query_id'] ?? 0;
				$posts_per_page = $this->get_query_builder_prop( $query_id, 'posts_per_page' );
			}

			if ( $posts_per_page ) {
				$i += $posts_per_page * ( $page - 1 );
			}

			// Override the listing ID with the one provided in the AJAX request.
			$listing_id = $_REQUEST['widget_settings']['lisitng_id'] ?? 0;
		}

		// Retrieve the current query object based on the listing ID.
		$current_query = $this->get_current_query( $listing_id );

		// Set current query loop index to the adjusted value.
		if ( $current_query ) {
			$current_query->loop_index = $i;
		}

		return $content;
	}

	/**
	 * Get a specific property from the query builder based on the query ID.
	 *
	 * @param int      $query_id  The ID of the query builder.
	 * @param string   $key       The key of the property to retrieve.
	 *
	 * @return mixed|null  The value of the specified property or null if not found.
	 */
	public function get_query_builder_prop( $query_id, $key ) {
		$query_builder = $query_id ? Query_Manager::instance()->get_query_by_id( $query_id ) : null;
		$query_args    = $query_builder ? $query_builder->get_query_args() : [];

		return $query_args[$key] ?? false;
	}

}