<?php
namespace Jet_Engine\Query_Builder\Listings;

use Jet_Engine\Query_Builder\Manager;

class Filters_Switch_Query extends Filters_Options_Source {

	protected $query_var    = 'jet_engine_switch_query';
	protected $source_slug  = 'query_builder_switcher';
	protected $new_query_id = null;

	public function __construct() {

		parent::__construct();

		add_action( 'jet-smart-filters/admin/register-dynamic-query', array( $this, 'register_query_var' ) );
		add_action( 'jet-smart-filters/query/final-query', array( $this, 'store_switched_query' ) );
		add_action( 'jet-engine/query-builder/listings/query-id', array( $this, 'switch_query' ), 10, 3 );
		
		add_filter( 'jet-smart-filters/service/filter/serialized-keys', array( $this, 'add_key_to_serialize' ) );
		add_filter( 'jet-smart-filters/filters/indexed-data/query-type-data', array( $this, 'index_data' ), 0, 4 );

	}

	/**
	 * Index queries
	 * 
	 * @return [type] [description]
	 */
	public function index_data( $data, $query_type, $index_key, $index_data ) {

		if ( $this->query_var === $index_key ) {
			foreach ( $index_data as $query_id ) {
				$query = Manager::instance()->get_query_by_id( $query_id );
				$data[ $index_key ][ $query_id ] = $query->get_items_total_count();
			}
		}

		return $data;
	}

	/**
	 * Set new query ID
	 * @param [type] $query_id [description]
	 */
	public function set_query_id( $query_id ) {
		$this->new_query_id = $query_id;
	}

	/**
	 * Store query ID we want to switch
	 * 
	 * @param  [type] $query [description]
	 * @return [type]        [description]
	 */
	public function store_switched_query( $query ) {

		if ( ! empty( $query['meta_query'] ) ) {
			foreach ( $query['meta_query'] as $index => $row ) {
				if ( isset( $row['key'] ) && $this->query_var === $row['key'] ) {
					$this->set_query_id( $row['value'] );
					do_action( 'jet-engine/query-builder/filters/switch-query', $this->new_query_id );
					unset( $query['meta_query'][ $index ] );
				}
			}
		}

		return $query;
	}

	/**
	 * Perform query switch
	 * 
	 * @param  [type] $query_id [description]
	 * @return [type]           [description]
	 */
	public function switch_query( $query_id, $listing_id, $settings ) {

		$query = Manager::instance()->get_query_by_id( $query_id );

		if ( $this->new_query_id && Manager::instance()->listings->filters->is_filters_request( $query ) ) {
			$_query_id = $query_id;
			$query_id  = $this->new_query_id;
			$this->new_query_id = null;

			$has_load_more = ! empty( $settings['use_load_more'] ) ? $settings['use_load_more'] : false;
			$has_load_more = filter_var( $has_load_more, FILTER_VALIDATE_BOOLEAN );

			if ( $has_load_more ) {
				add_filter( 'jet-engine/listing/grid/query-args', function ( $args ) use ( $_query_id ) {
					$args['switched_query_id'] = $_query_id;
					return $args;
				} );
			}
		}

		if ( jet_engine()->listings->is_listing_ajax( 'listing_load_more' )
			 && ! empty( $_REQUEST['query'] ) && ! empty( $_REQUEST['query']['query_id'] )
			 && ! empty( $_REQUEST['query']['switched_query_id'] )
			 && intval( $query_id ) === intval( $_REQUEST['query']['switched_query_id'] )
		) {
			$query_id = $_REQUEST['query']['query_id'];
		}

		return $query_id;

	}

	/**
	 * Register query var for JSF admin
	 * 
	 * @param  [type] $dynamic_query_manager [description]
	 * @return [type]                        [description]
	 */
	public function register_query_var( $dynamic_query_manager ) {
		$dynamic_query_manager->register_items( array( $this->query_var => __( 'Switch JetEngine query' ) ) );
	}

	/**
	 * Register query switcher source
	 * 
	 * @param  array  $sources [description]
	 * @return [type]          [description]
	 */
	public function register_source( $sources = array() ) {
		$sources[ $this->source_slug ] = __( 'JetEngine Query Builder Switcher', 'jet-engine' );
		return $sources;
	}

	/**
	 * Add this key to serialized settings keys.
	 * @param [type] $keys [description]
	 */
	public function add_key_to_serialize( $keys ) {
		$keys[] = '_query_builder_queries_to_switch';
		return $keys;
	}

	/**
	 * Register query switcher controls
	 * 
	 * @param  [type] $fields [description]
	 * @return [type]         [description]
	 */
	public function register_controls( $fields ) {

		if ( jet_smart_filters()->get_version() >= '3.0.0' && ! jet_smart_filters()->is_classic_admin ) {
			$notice = array(
				'title'      => __( 'Warning!', 'jet-smart-filters' ),
				'type'       => 'html',
				'fullwidth'  => true,
				'html'       => __( 'Query switcher source works correctly only with Select and Radio filter types.', 'jet-smart-filters' ),
				'conditions' => array(
					'_filter_type' => array( 'color-image', 'checkboxes' ),
					'_data_source' => $this->source_slug,
				)
			);
		} else {
			$notice = array(
				'title'       => __( 'Warning!', 'jet-engine' ),
				'type'        => 'text',
				'input_type'  => 'hidden',
				'element'     => 'control',
				'description' => __( 'Query switcher source works correctly only with Select and Radio filter types.', 'jet-engine' ),
				'class'       => 'cx-control',
				'conditions'  => array(
					'_filter_type' => array( 'color-image', 'checkboxes' ),
					'_data_source' => $this->source_slug,
				),
			);
		}

		$queries = Manager::instance()->get_queries_for_options();

		unset( $queries[''] );

		$insert = array(
			'_query_switcher_notice' => $notice,
			'_query_builder_queries_to_switch' => array(
				'title'       => __( 'Select Query', 'jet-engine' ),
				'type'        => 'select',
				'element'     => 'control',
				'multiple'    => true,
				'placeholder' => __( 'Select Query', 'jet-engine' ),
				'options'     => $queries,
				'conditions'  => array(
					'_filter_type' => array( 'select', 'radio' ),
					'_data_source' => $this->source_slug,
				),
			),
		);

		$fields = $this->insert_after( $fields, '_data_source', $insert );

		return $fields;

	}

	/**
	 * Apply query options
	 * 
	 * @param  [type] $options   [description]
	 * @param  [type] $filter_id [description]
	 * @param  [type] $filter    [description]
	 * @return [type]            [description]
	 */
	public function apply_options( $options, $filter_id, $filter ) {

		$source = get_post_meta( $filter_id, '_data_source', true );

		if ( $this->source_slug !== $source ) {
			return $options;
		}

		$queries = get_post_meta( $filter_id, '_query_builder_queries_to_switch', true );

		if ( empty( $queries ) ) {
			return $options;
		}

		$type = get_post_meta( $filter_id, '_filter_type', true );
		$new_options = array();

		foreach ( $queries as $query_id ) {

			if ( ! $query_id || is_array( $query_id ) ) {
				continue;
			}

			$query                    = Manager::instance()->get_query_by_id( $query_id );
			$new_options[ $query_id ] = $query->name;
		}

		if ( 'select' === $type ) {

			$placeholder = get_post_meta( $filter_id, '_placeholder', true );

			if ( $placeholder ) {
				$new_options = array( '' => $placeholder ) + $new_options;
			}

		}

		return $new_options;

	}

}
