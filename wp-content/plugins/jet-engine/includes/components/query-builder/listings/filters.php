<?php
namespace Jet_Engine\Query_Builder\Listings;

use Jet_Engine\Query_Builder\Manager as Query_Manager;

class Filters {

	public function __construct() {
		add_action( 'jet-engine/query-builder/listings/on-query', array( $this, 'maybe_set_query_props' ), 10, 4 );
		add_action( 'jet-engine/query-builder/query/after-query-setup', array( $this, 'maybe_setup_filter' ) );
	}


	/**
	 * Private filters request checker.
	 * Used as separate method to filters its result later in single place in the code
	 * 
	 * @return boolean [description]
	 */
	private function _is_filters_request( $query = null ) {

		if ( function_exists( 'jet_smart_filters' ) && jet_smart_filters()->query->is_ajax_filter() ) {
			return $this->is_current_provider_query(
				$query,
				jet_smart_filters()->query->get_current_provider( 'query_id' ),
				jet_smart_filters()->query->get_current_provider( 'provider' )
			);
		}

		if ( ! empty( $_REQUEST['action'] ) && 'jet_smart_filters' === $_REQUEST['action'] && ! empty( $_REQUEST['provider'] ) ) {
			$jsf_data = explode( '/', $_REQUEST['provider'] );
			$provider = $jsf_data[0];
			$query_id = isset( $jsf_data[1] ) ? $jsf_data[1] : null;

			return $this->is_current_provider_query( $query, $query_id, $provider );

		}

		// Fixed the Load More listing after redirect to prefiltered page ( mixed apply type ).
		if ( ! empty( $_REQUEST['action'] ) && 'jet_engine_ajax' === $_REQUEST['action'] &&
			! empty( $_REQUEST['handler'] ) && 'listing_load_more' === $_REQUEST['handler']
		) {
			return false;
		}

		$request = $_REQUEST;

		if ( ! empty( $request['jet-smart-filters'] ) ) {
			$request['jsf'] = str_replace( '/', ':', $request['jet-smart-filters'] );
		}
		
		if ( ! empty( $request['jsf'] ) ) {

			// Ensure separator one more time
			$request['jsf'] = str_replace( '/', ':', $request['jsf'] );

			$jsf_data = explode( ':', $request['jsf'] );
			$provider = $jsf_data[0];
			$query_id = isset( $jsf_data[1] ) ? $jsf_data[1] : null;

			return $this->is_current_provider_query( $query, $query_id, $provider );
			
		}

		return false;

	}
	
	public function is_current_provider_query( $query, $query_id, $provider ) {
		
		$allowed_providers = apply_filters(
			'jet-engine/query-builder/filters/allowed-providers',
			array( 'jet-engine', 'jet-engine-maps', 'jet-engine-calendar' )
		);

		if ( $query_id && 'default' !== $query_id && $query && $query->query_id ) {
			return ( in_array( $provider, $allowed_providers ) && $query->query_id == $query_id );
		} elseif ( $query && $query->query_id && $query->query_id !== $query_id ) {
			return false;
		}

		return in_array( $provider, $allowed_providers );
		
	}

	/**
	 * Check if JetSmartFilters request is currently processing
	 *
	 * @return boolean [description]
	 */
	public function is_filters_request( $query = null ) {
		return apply_filters( 'jet-engine/query-builder/filters/is-filters-request', $this->_is_filters_request( $query ), $query );
	}

	/**
	 * Setup filtered data if it was filters request
	 *
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public function maybe_setup_filter( $query ) {

		$remove_hook = false;

		// Get filtered query
		if ( $this->is_filters_request( $query ) ) {

			$filtered_query = jet_smart_filters()->query->get_query_from_request();

			if ( null === $filtered_query ) {
				$filtered_query = jet_smart_filters()->query->_query;
			}

			if ( ! empty( $filtered_query ) ) {

				$query->setup_query();

				do_action( 'jet-engine/query-builder/filters/before-set-props', $query );

				foreach ( $filtered_query as $prop => $value ) {
					$query->set_filtered_prop( $prop, $value );
				}

				do_action( 'jet-engine/query-builder/filters/before-after-props', $query );

			}

			$remove_hook = true;

			// For compatibility with the Load More feature.
			add_filter( 'jet-engine/listing/grid/query-args', function ( $args, $widget, $settings ) use ( $filtered_query, $query ) {

				$use_load_more = ! empty( $settings['use_load_more'] ) ? $settings['use_load_more'] : false;
				$use_load_more = filter_var( $use_load_more, FILTER_VALIDATE_BOOLEAN );

				if ( $use_load_more && ! empty( $filtered_query ) ) {
					$args['filtered_query'] = $filtered_query;
				}

				$args = Query_Manager::instance()->listings->query->maybe_add_load_more_query_args( $args, $query, $settings );

				return $args;
			}, 10, 3 );

			add_filter( 'jet-smart-filters/render/ajax/data', function( $data ) use ( $query ) {

				if ( ! isset( $data['fragments'] ) ) {
					$data['fragments'] = array();
				}

				$data['fragments'][ '.jet-engine-query-count.count-type-total.query-' . $query->id ] = $query->get_items_total_count();
				$data['fragments'][ '.jet-engine-query-count.count-type-visible.query-' . $query->id ] = $this->get_visible_items_count( $query );
				$data['fragments'][ '.jet-engine-query-count.count-type-end-item.query-' . $query->id ] = $query->get_end_item_index_on_page();

				if ( ! $this->is_filter_load_more() ) {
					$data['fragments'][ '.jet-engine-query-count.count-type-start-item.query-' . $query->id ] = $query->get_start_item_index_on_page();
				}

				return $data;

			} );

		}

		// Process pager
		if ( $this->is_filters_request( $query ) && ( ! empty( $_REQUEST['paged'] ) || ! empty( $_REQUEST['jet_paged'] ) ) ) {

			if ( ! empty( $_REQUEST['paged'] ) ) {
				$page = absint( $_REQUEST['paged'] );
			} elseif ( ! empty( $_REQUEST['jet_paged'] ) ) {
				$page = absint( $_REQUEST['jet_paged'] );
			} else {
				$page = 1;
			}

			$query->set_filtered_prop( '_page', $page );

			$remove_hook = true;

		}

		if ( $remove_hook ) {
			remove_action( 'jet-engine/query-builder/query/after-query-setup', array( $this, 'maybe_setup_filter' ) );
		}

	}

	public function maybe_set_query_props( $query, $settings, $widget, $query_manager ) {

		if ( ! $query ) {
			return;
		}

		$query_id = ! empty( $settings['_element_id'] ) ? $settings['_element_id'] : false;

		if ( ! $query_id && $query->query_id ) {
			$query_id = $query->query_id;
		}

		if ( ! $query_id && $this->is_filters_request( $query ) ) {
			$query_id = jet_smart_filters()->query->get_current_provider( 'query_id' );
		}

		switch ( $widget->get_name() ) {

			case 'jet-engine-maps-listing':
				$provider = 'jet-engine-maps';
				break;

			case 'jet-listing-calendar':
				$provider = 'jet-engine-calendar';
				break;

			default:
				$provider = apply_filters( 'jet-engine/query-builder/filter-provider', 'jet-engine', $widget, $query, $settings, $query_manager );
				break;
		}

		// Setup props for the pager
		jet_smart_filters()->query->set_props(
			$provider,
			apply_filters( 'jet-engine/query-builder/set-props', array(
				'found_posts'   => $query->get_items_total_count(),
				'max_num_pages' => $query->get_items_pages_count(),
				'page'          => $query->get_current_items_page(),
				'query_type'    => $query->query_type,
				'query_id'      => $query->id,
				'query_meta'    => $query->get_query_meta(),
			), $provider, $query_id ),
			$query_id
		);

		// Store settings to localize it by SmartFilters later
		jet_smart_filters()->providers->store_provider_settings(
			$provider,
			$widget->get_required_settings(),
			$query_id
		);

		// Store current query to allow indexer to get correct posts count for current query
		jet_smart_filters()->query->store_provider_default_query(
			$provider,
			$query->get_query_args(),
			$query_id
		);

		/**
		 * After indexer get required data, remove query builder-related arguments from localized filters data to avoid it from sending
		 * with AJAX requests and break these requests if query have to much args
		 */
		add_filter( 'jet-smart-filters/filters/localized-data', function( $data = array() ) use ( $provider, $query_id ) {

			$query_id = $query_id ? $query_id : 'default';

			if ( isset( $data['queries'][ $provider ][ $query_id ] ) ) {
				unset( $data['queries'][ $provider ][ $query_id ] );
			}

			return $data;
		}, 999 );

	}

	public function is_filter_load_more() {
		return ! empty( $_REQUEST['props'] ) && ! empty( $_REQUEST['props']['pages'] );
	}

	public function get_visible_items_count( $query ) {

		if ( $this->is_filter_load_more() ) {
			$pages         = $_REQUEST['props']['pages'];
			$max_pages     = $query->get_items_pages_count();
			$visible_items = 0;

			foreach ( $pages as $page ) {

				if ( $page == $max_pages ) {
					$visible_items += $query->get_items_page_count();
				} else {
					$visible_items += $query->get_items_per_page();
				}

			}

			return $visible_items;
		}

		return $query->get_items_page_count();
	}

}