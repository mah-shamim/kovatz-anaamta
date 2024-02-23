<?php
/**
 * Popup compatibility package
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Smart_Filters_Package' ) ) {

	/**
	 * Define Jet_Engine_Smart_Filters_Package class
	 */
	class Jet_Engine_Smart_Filters_Package {

		public function __construct() {

			add_filter(
				'jet-smart-filters/providers/jet-engine/stored-settings',
				array( $this, 'store_layout_settings' ),
				10, 2
			);

			add_filter(
				'jet-engine/ajax/get_listing/response',
				array( $this, 'add_to_response_filters_data' ),
				10, 3
			);

			/*
			This filter not needed anymore with new ajax listing url.
			add_filter(
				'jet-engine/listing/grid/is_lazy_load',
				array( $this, 'maybe_disable_lazy_load_listing' ),
				10, 2
			);
			*/

			add_filter(
				'jet-engine/listing/grid/posts-query-args',
				array( $this, 'maybe_enable_users_count' ),
				10, 2
			);

			add_filter(
				'jet-engine/listing/grid/users-query-results',
				array( $this, 'maybe_store_user_query_props' ),
				10, 3
			);

			add_filter(
				'jet-engine/listing/grid/lazy-load/options',
				array( $this, 'add_redirect_filter_data' )
			);

			add_filter(
				'jet-engine/listing/posts-loop/start-from',
				array( $this, 'update_start_from_on_pagination_request' ),
				10, 3
			);

		}

		public function is_filters_request() {

			if ( ! empty( $_REQUEST['action'] ) && 'jet_smart_filters' === $_REQUEST['action'] && ! empty( $_REQUEST['provider'] ) ) {
				return true;
			}

			if ( ! empty( $_REQUEST['jet-smart-filters'] ) ) {
				return true;
			}

			if ( ! empty( $_REQUEST['jsf'] ) ) {
				return true;
			}

			return false;
		}

		public function maybe_enable_users_count( $args, $widget ) {

			if ( isset( $args['count_total'] ) && ! empty( $args['jet_smart_filters'] ) ) {
				$args['count_total'] = true;
			}

			return $args;

		}

		public function maybe_store_user_query_props( $users, $users_query, $widget ) {

			if ( isset( $users_query->query_vars['jet_smart_filters'] ) ) {

				$provider_data = $users_query->query_vars['jet_smart_filters'];
				$provider_data = jet_smart_filters()->query->decode_provider_data( $provider_data );

				if ( isset( $_REQUEST['jet_paged'] ) ) {
					$page = absint( $_REQUEST['jet_paged'] );
				} elseif ( wp_doing_ajax() && isset( $_REQUEST['paged'] ) ) {
					$page = absint( $_REQUEST['paged'] );
				} elseif ( defined( 'JET_SMART_FILTERS_DOING_REQUEST' ) && isset( $_REQUEST['paged'] ) ) {
					$page = absint( $_REQUEST['paged'] );
				} else {
					$page = $widget->query_vars['page'];
				}

				jet_smart_filters()->query->set_props(
					$provider_data['provider'],
					array(
						'found_posts'   => $users_query->get_total(),
						'max_num_pages' => $widget->query_vars['pages'],
						'page'          => $page,
					),
					$provider_data['query_id']
				);

			}

			return $users;

		}

		/**
		 * Add the filters data to response data.
		 *
		 * @param array $response
		 * @param array $widget_settings
		 *
		 * @return array
		 */
		public function add_to_response_filters_data( $response, $widget_settings, $query = array() ) {

			if ( empty( $widget_settings['lazy_load'] ) ) {
				return $response;
			}

			if ( empty( $widget_settings['_element_id'] ) ) {
				$query_id = 'default';
			} else {
				$query_id = $widget_settings['_element_id'];
			}

			$filters_data = array();

			$filters_settings = array(
				'queries'   => jet_smart_filters()->query->get_default_queries(),
				'settings'  => jet_smart_filters()->providers->get_provider_settings(),
				'props'     => jet_smart_filters()->query->get_query_props(),
			);

			foreach ( $filters_settings as $param => $data ) {
				if ( ! empty( $data['jet-engine'][ $query_id ] ) ) {
					$filters_data[ $param ][ $query_id ] = $data['jet-engine'][ $query_id ];
				}
			}

			if ( ! empty( $filters_data ) ) {
				$response['filters_data'] = $filters_data;
			}

			if ( jet_smart_filters()->indexer->data ) {
				$response['indexer_data'] = array(
					'provider' => 'jet-engine/' . $query_id,
					'query'    => wp_parse_args(
						$query,
						isset( $filters_data['queries'][ $query_id ] ) ? $filters_data['queries'][ $query_id ] : array()
					)
				);
			}

			$query_builder_id = false;

			if ( class_exists( 'Jet_Engine\Query_Builder\Manager' ) ) {
				$query_builder_id = Jet_Engine\Query_Builder\Manager::instance()->listings->get_query_id( $widget_settings['lisitng_id'], $widget_settings );
			}

			/**
			 * After indexer get required data, remove query builder-related arguments from filters data to avoid it from sending
			 * with AJAX requests and break these requests if query have to much args
			 */
			if ( ! empty( $query_builder_id ) && ! empty( $response['filters_data'] ) && ! empty( $response['filters_data']['queries'] ) ) {
				$response['filters_data']['queries'][ $query_id ] = array();
			}

			return $response;
		}

		/**
		 * Disable lazy loading if reload type filters are applied
		 *
		 * @param bool  $is_lazy_load
		 * @param array $settings
		 *
		 * @return bool
		 */
		public function maybe_disable_lazy_load_listing( $is_lazy_load, $settings ) {

			if ( ! $is_lazy_load ) {
				return $is_lazy_load;
			}

			if ( ! empty( $_REQUEST['jsf'] ) ) {
				$request_provider = $_REQUEST['jsf'];
				$current_provider = 'jet-engine' . ( $settings['_element_id'] ? ':' . $settings['_element_id'] : '' );
			} else if ( ! empty( $_REQUEST['jet-smart-filters'] ) ) {
				$request_provider = $_REQUEST['jet-smart-filters'];
				$current_provider = 'jet-engine' . ( $settings['_element_id'] ? '/' . $settings['_element_id'] : '/default' );
			} else {
				return $is_lazy_load;
			}

			if ( $request_provider !== $current_provider ) {
				return $is_lazy_load;
			}

			return false;
		}

		/**
		 * Store additional settings
		 *
		 * @param  [type] $stored_settings [description]
		 * @param  [type] $widget_settings [description]
		 * @return [type]                  [description]
		 */
		public function store_layout_settings( $stored_settings, $widget_settings ) {

			$settings_to_store = array(
				'inject_alternative_items',
				'injection_items',
				'use_load_more',
				'load_more_id',
			);

			foreach ( $settings_to_store as $setting ) {
				if ( isset( $widget_settings[ $setting ] ) )  {
					$stored_settings[ $setting ] = $widget_settings[ $setting ];
				}
			}

			return $stored_settings;
		}

		public function add_redirect_filter_data( $options ) {

			if ( empty( $_POST['jet-smart-filters-redirect'] ) ) {
				return $options;
			}

			if ( ! empty( $_POST['jsf'] ) || ! empty( $_POST['jet-smart-filters'] ) ) {

				if ( empty( $options['extra_props'] ) ) {
					$options['extra_props'] = array();
				}

				$options['extra_props'] = array_merge( $options['extra_props'], $_POST );
			}

			return $options;
		}

		public function update_start_from_on_pagination_request( $start_from, $settings, $render ) {

			if ( ! $this->is_filters_request() ) {
				return $start_from;
			}

			if ( empty( $_REQUEST['paged'] ) && empty( $_REQUEST['jet_paged'] ) ) {
				return $start_from;
			}

			$request_provider = jet_smart_filters()->query->get_current_provider( 'raw' );

			if ( ! $request_provider ) {
				return $start_from;
			}

			$current_provider = 'jet-engine' . ( ! empty( $settings['_element_id'] ) ? '/' . $settings['_element_id'] : '/default' );

			if ( $request_provider !== $current_provider ) {
				return $start_from;
			}

			if ( ! empty( $_REQUEST['paged'] ) ) {
				$page = absint( $_REQUEST['paged'] );
			} elseif ( ! empty( $_REQUEST['jet_paged'] ) ) {
				$page = absint( $_REQUEST['jet_paged'] );
			} else {
				$page = 1;
			}

			if ( 1 < $page ) {

				$per_page = $settings['posts_num'];

				if ( $render->listing_query_id ) {
					$query = Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $render->listing_query_id );

					if ( $query ) {
						$per_page = $query->get_items_per_page();
					}
				}

				$start_from = ( $page - 1 ) * absint( $per_page ) + 1;
			}

			return $start_from;
		}

	}

}

new Jet_Engine_Smart_Filters_Package();
