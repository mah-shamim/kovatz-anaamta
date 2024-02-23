<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Elementor_Ajax_Handlers' ) ) {

	class Jet_Engine_Elementor_Ajax_Handlers {

		/**
		 * Load more
		 */
		public function listing_load_more() {

			$query           = ! empty( $_REQUEST['query'] ) ? $_REQUEST['query'] : array();
			$widget_settings = ! empty( $_REQUEST['widget_settings'] ) ? $_REQUEST['widget_settings'] : array();
			$response        = array();

			$data = array(
				'id'         => 'jet-listing-grid',
				'elType'     => 'widget',
				'settings'   => $widget_settings,
				'elements'   => array(),
				'widgetType' => 'jet-listing-grid',
			);

			$widget = Elementor\Plugin::$instance->elements_manager->create_element_instance( $data );

			if ( ! $widget ) {
				throw new \Exception( 'Widget not found.' );
			}

			do_action( 'jet-engine/elementor-views/ajax/load-more', $widget );

			ob_start();

			$base_class       = 'jet-listing-grid';
			$equal_cols_class = '';

			if ( ! empty( $widget_settings['equal_columns_height'] ) ) {
				$equal_cols_class = 'jet-equal-columns';
			}

			jet_engine()->listings->data->set_listing(
				Elementor\Plugin::$instance->documents->get_doc_for_frontend( $widget_settings['lisitng_id'] )
			);

			$listing_source = jet_engine()->listings->data->get_listing_source();
			$page           = ! empty( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
			$query['paged'] = $page;

			$render_instance = jet_engine()->listings->get_render_instance( 'listing-grid', $widget_settings );

			switch ( $listing_source ) {

				case 'posts':
					$widget_settings['posts_num'] = $query['posts_per_page'];

					$query = apply_filters(
						'jet-engine/listing/grid/posts-query-args',
						$query,
						$render_instance,
						$widget_settings
					);

					$offset          = ! empty( $query['offset'] ) ? absint( $query['offset'] ) : 0;
					$query['offset'] = $offset + ( $page - 1 ) * absint( $widget_settings['posts_num'] );
					$posts_query     = new WP_Query( $query );
					$posts           = $posts_query->posts;
					break;

				case 'terms':
					$offset          = ! empty( $query['offset'] ) ? absint( $query['offset'] ) : 0;
					$query['offset'] = $offset + ( $page - 1 ) * absint( $widget_settings['posts_num'] );
					$posts           = get_terms( $query );
					break;

				case 'users':

					$query['offset'] = ( $page - 1 ) * absint( $widget_settings['posts_num'] );
					$user_query      = new WP_User_Query( $query );
					$posts           = (array) $user_query->get_results();

					break;

				default:

					$posts = apply_filters(
						'jet-engine/listing/grid/query/' . $listing_source,
						array(),
						$widget_settings,
						$render_instance
					);

					break;
			}

			if ( 1 < $query['paged'] ) {
				$start_from = ( $query['paged'] - 1 ) * absint( $widget_settings['posts_num'] ) + 1;
			} else {
				$start_from = false;
			}

			Elementor\Plugin::instance()->frontend->start_excerpt_flag( null );

			$render_instance->posts_loop(
				$posts,
				$widget_settings,
				$base_class,
				$equal_cols_class,
				$start_from
			);

			$response['html'] = ob_get_clean();

			if ( class_exists( 'Jet_Engine_Listings_Ajax_Handlers' ) ) {
				Jet_Engine_Listings_Ajax_Handlers::maybe_add_enqueue_assets_data( $response );
			}

			wp_send_json_success( $response );

		}

	}

}
