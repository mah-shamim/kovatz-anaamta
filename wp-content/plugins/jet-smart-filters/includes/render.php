<?php
/**
 * Data class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Render' ) ) {
	/**
	 * Define Jet_Smart_Filters_Render class
	 */
	class Jet_Smart_Filters_Render {

		private $_rendered_providers = array();
		private $request_query_vars  = array(
			'tax',
			'meta',
			'date',
			'sort',
			'alphabet',
			'_s',
			'search',
			'pagenum',
			'plain_query',
		);

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			add_action( 'parse_request', array( $this, 'apply_filters_from_request' ) );
			add_action( 'parse_request', array( $this, 'apply_filters_from_permalink' ) );

			// backward compatibility
			add_action( 'parse_request', array( $this, 'apply_filters_from_request_backward_compatibility' ) );

			add_action( 'wp_ajax_jet_smart_filters', array( $this, 'ajax_apply_filters' ) );
			add_action( 'wp_ajax_nopriv_jet_smart_filters', array( $this, 'ajax_apply_filters' ) );

			add_action( 'wp_ajax_jet_smart_filters_get_hierarchy_level', array( $this, 'hierarchy_level' ) );
			add_action( 'wp_ajax_nopriv_jet_smart_filters_get_hierarchy_level', array( $this, 'hierarchy_level' ) );

			add_action( 'wp_ajax_jet_smart_filters_get_indexed_data', array( $this, 'get_indexed_data' ) );
			add_action( 'wp_ajax_nopriv_jet_smart_filters_get_indexed_data', array( $this, 'get_indexed_data' ) );
		}

		public function get_request_query_vars() {
			return apply_filters( 'jet-smart-filters/render/query-vars', $this->request_query_vars );
		}

		/**
		 * Update hierarchy levels starting from depth
		 */
		public function hierarchy_level() {

			$depth     = isset( $_REQUEST['depth'] ) ? absint( $_REQUEST['depth'] ) : false;
			$filter_id = isset( $_REQUEST['filter_id'] ) ? absint( $_REQUEST['filter_id'] ) : 0;

			if ( ! $filter_id ) {
				wp_send_json_error();
			}

			$values  = ! empty( $_REQUEST['values'] ) ? $_REQUEST['values'] : array();
			$args    = ! empty( $_REQUEST['args'] ) ? $_REQUEST['args'] : array();

			require jet_smart_filters()->plugin_path( 'includes/hierarchy.php' );

			$hierarchy = new Jet_Smart_Filters_Hierarchy(
				$filter_id,
				$depth,
				$values,
				$args
			);

			wp_send_json_success( $hierarchy->get_levels() );
		}

		/**
		 * Get indexed data
		 */
		public function get_indexed_data() {

			$provider_key     = isset( $_REQUEST['provider'] ) ? $_REQUEST['provider'] : false;
			$indexing_filters = isset( $_REQUEST['indexing_filters'] ) ? json_decode( stripcslashes( $_REQUEST['indexing_filters'] ), true ) : false;
			$query_args       = isset( $_REQUEST['query_args'] ) ? $_REQUEST['query_args'] : array();

			if ( ! ( $provider_key && $indexing_filters ) ) {
				return;
			}

			foreach ( $indexing_filters as $filter_id ) {
				jet_smart_filters()->indexer->data->add_indexing_data_from_filter( $provider_key, $filter_id );
			}

			$indexed_data = jet_smart_filters()->indexer->data->get_indexed_data($provider_key, $query_args);

			wp_send_json_success( $indexed_data );
		}

		/**
		 * Returns requested provider ID
		 */
		public function request_provider( $return = null ) {

			return jet_smart_filters()->query->get_current_provider( $return );
		}

		/**
		 * Apply filters form REQUEST parameters.
		 */
		public function apply_filters_from_request() {

			if ( empty( $_REQUEST['jsf'] ) ) {
				return;
			}

			$provider_name = ! empty( $_REQUEST['provider'] )
				? $_REQUEST['provider']
				: $_REQUEST['jsf'];

			jet_smart_filters()->query->set_provider_from_request( $provider_name );

			$provider_id = $this->request_provider( 'provider' );
			$provider    = jet_smart_filters()->providers->get_providers( $provider_id );

			if ( ! $provider && is_callable( array( $provider, 'apply_filters_in_request' ) ) ) {
				return;
			}

			foreach ( $this->get_request_query_vars() as $query_var ) {
				if ( empty( $_REQUEST[ $query_var ] ) ) {
					continue;
				}

				jet_smart_filters()->query->set_query_var_to_request( $query_var, $_REQUEST[ $query_var ] );
			}

			jet_smart_filters()->query->get_query_from_request();
			$provider->apply_filters_in_request();
		}

		/**
		 * Apply filters form url permalink.
		 */
		public function apply_filters_from_permalink( $query ) {

			if ( empty( $query->query_vars['jsf'] ) || isset( $_REQUEST['jsf'] ) ) {
				return;
			}

			$jsf_query_str = $query->query_vars['jsf'];

			$_REQUEST['jsf'] = strtok( $jsf_query_str, '/' );

			foreach ( $this->get_request_query_vars() as $query_var ) {
				preg_match_all( "/$query_var\/(.*?)(\/|$)/", $jsf_query_str, $matches );

				if ( empty( $matches[1][0] ) ) {
					continue;
				}

				$_REQUEST[ $query_var ] = apply_filters(
					'jet-smart-filters/render/set-query-var',
					urldecode( $matches[1][0] ),
					$query_var,
					$this
				);
			}

			$this->apply_filters_from_request();
		}

		/**
		 * Apply filters form REQUEST parameters backward compatibility.
		 */
		public function apply_filters_from_request_backward_compatibility() {

			if ( empty( $_REQUEST['jet-smart-filters'] ) ) {
				return;
			}

			$provider_id = $this->request_provider( 'provider' );
			$provider    = jet_smart_filters()->providers->get_providers( $provider_id );

			if ( ! $provider ) {
				return;
			}

			if ( is_callable( array( $provider, 'apply_filters_in_request' ) ) ) {
				jet_smart_filters()->query->get_query_from_request();
				$provider->apply_filters_in_request();
			}
		}

		/**
		 * Apply filters in AJAX request
		 */
		public function ajax_apply_filters() {

			$provider_id = $this->request_provider( 'provider' );
			$query_id    = $this->request_provider( 'query_id' );
			$apply_type  = ! empty( $_REQUEST['apply_type'] ) ? $_REQUEST['apply_type'] : 'ajax';
			$provider    = jet_smart_filters()->providers->get_providers( $provider_id );

			if ( ! $provider ) {
				return;
			}

			do_action( 'jet-smart-filters/render/ajax/before', $this, $provider_id, $query_id, $provider );

			jet_smart_filters()->query->get_query_from_request();

			if ( ! empty( $_REQUEST['props'] ) ) {

				jet_smart_filters()->query->set_props(
					$provider_id,
					$_REQUEST['props'],
					$query_id
				);
			}

			$args = array(
				'content'    => $this->render_content( $provider ),
				'pagination' => jet_smart_filters()->query->get_current_query_props()
			);

			if ( $provider->is_data() ) {
				$args['is_data'] = 1;
			}

			$args = apply_filters( 'jet-smart-filters/render/ajax/data', $args );

			wp_send_json( $args );
		}

		/**
		 * Render content
		 */
		public function render_content( $provider ) {

			ob_start();

			if ( is_callable( array( $provider, 'ajax_get_content' ) ) ) {
				$provider->ajax_get_content();
			} else {
				_e( 'Incorrect input data', 'jet-smart-filters' );
			}

			return ob_get_clean();
		}
	}
}
