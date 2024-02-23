<?php

namespace Jet_Engine\Bricks_Views\Bricks_Loop;

use Bricks\Database;
use Jet_Engine\Bricks_Views\Helpers\Options_Converter;


class Manager {

	public $initial_object;

	function __construct() {
		add_filter( 'bricks/setup/control_options', [ $this, 'setup_query_controls' ] );
		add_action( 'init', [ $this, 'add_control_to_elements' ], 40 );
		add_filter( 'bricks/query/run', [ $this, 'run_query' ], 10, 2 );
		add_filter( 'bricks/query/loop_object', [ $this, 'set_loop_object' ], 10, 3 );
		add_action( 'bricks/query/after_loop', [ $this, 'set_initial_object' ], 10, 2 );
		add_filter( 'jet-engine/listings/data/the-post/is-main-query', array( $this, 'maybe_modify_is_main_query' ), 10, 3 );
	}

	public function setup_query_controls( $control_options ) {

		// Add a new query loop type
		$control_options['queryTypes']['jet_engine_query_builder'] = esc_html__( 'JetEngine Query Builder', 'jet-engine' );

		return $control_options;

	}

	public function add_control_to_elements() {

		// Only container, block and div element have query controls
		$elements = [ 'container', 'block', 'div' ];

		foreach ( $elements as $name ) {
			add_filter( "bricks/elements/{$name}/controls", [ $this, 'add_jet_engine_controls' ], 40 );
		}

	}

	public function add_jet_engine_controls( $controls ) {

		$options = \Jet_Engine\Query_Builder\Manager::instance()->get_queries_for_options();

		// jet_engine_query_builder_id will be my option key
		$jet_engine_control['jet_engine_query_builder_id'] = [
			'tab'         => 'content',
			'label'       => esc_html__( 'JetEngine Queries', 'jet-engine' ),
			'type'        => 'select',
			'options'     => Options_Converter::remove_empty_key_in_options( $options ),
			'placeholder' => esc_html__( 'Choose a query', 'jet-engine' ),
			'required'    => array(
				[ 'query.objectType', '=', 'jet_engine_query_builder' ],
				[ 'hasLoop', '!=', false ]
			),
			'rerender'    => true,
			'description' => esc_html__( 'Please create a query in JetEngine Query Builder First', 'jet-engine' ),
			'searchable'  => true,
			'multiple'    => false,
		];

		// Below 2 lines is just some php array functions to force my new control located after the query control
		$query_key_index = absint( array_search( 'query', array_keys( $controls ) ) );
		$new_controls    = array_slice( $controls, 0, $query_key_index + 1, true ) + $jet_engine_control + array_slice( $controls, $query_key_index + 1, null, true );

		return $new_controls;
	}

	public function run_query( $results, $query_obj ) {

		// Only target if query type set is jet_engine_query_builder
		if ( $query_obj->object_type !== 'jet_engine_query_builder' ) {
			return $results;
		}

		$jet_engine_query = $this->get_jet_engine_query( $query_obj->settings );

		// Return empty results if query not found in JetEngine Query Builder
		if ( ! $jet_engine_query ) {
			return $results;
		}

		// Get the initial object before the repeater renders
		if ( $jet_engine_query->query_type === 'repeater' ) {
			$this->initial_object = jet_engine()->listings->data->get_current_object();
		}

		// Setup query args
		$jet_engine_query->setup_query();

		// Get the results
		return $jet_engine_query->get_items();

	}

	public function set_loop_object( $loop_object, $loop_key, $query ) {

		if ( $query->object_type !== 'jet_engine_query_builder' ) {
			return $loop_object;
		}

		global $post;

		// I only tested on JetEngine Posts Query, Terms Query, Comments Query and WC Products Query
		// I didn't set WP_Term condition because it's not related to the $post global variable
		if ( is_a( $loop_object, 'WP_Post' ) ) {
			$post = $loop_object;
		} elseif ( is_a( $loop_object, 'WC_Product' ) ) {
			// $post should be a WP_Post object
			$post = get_post( $loop_object->get_id() );
		} elseif ( is_a( $loop_object, 'WP_Comment' ) ) {
			// A comment should refer to a post, so I set the $post global variable to the comment's post
			// You might want to change this to $loop_object->comment_ID
			$post = get_post( $loop_object->comment_post_ID );
		}

		setup_postdata( $post );

		$jet_engine_query = $this->get_jet_engine_query( $query->settings );

		// Return empty results if query not found in JetEngine Query Builder
		if ( ! $jet_engine_query ) {
			return $loop_object;
		}

		// Set currnet object for JetEngine
		jet_engine()->listings->data->set_current_object( $loop_object );

		// We still return the $loop_object so \Bricks\Query::get_loop_object() can use it
		return $loop_object;

	}

	public function set_initial_object( $query, $args ) {

		if ( $query->object_type !== 'jet_engine_query_builder' ) {
			return false;
		}

		$jet_engine_query = $this->get_jet_engine_query( $query->settings );

		if ( ! $jet_engine_query ) {
			return false;
		}

		// Set the initial object after the repeater renders
		if ( $jet_engine_query->query_type === 'repeater' ) {
			jet_engine()->listings->data->set_current_object( $this->initial_object );
		}

	}

	public function get_jet_engine_query( $settings ) {

		$jet_engine_query_builder_id = ! empty( $settings['jet_engine_query_builder_id'] ) ? absint( $settings['jet_engine_query_builder_id'] ) : 0;

		// Return empty results if no query selected or Use Query is not checked
		if ( $jet_engine_query_builder_id === 0 || ! $settings['hasLoop'] ) {
			return false;
		}

		$query_builder = \Jet_Engine\Query_Builder\Manager::instance();

		// Get the query object from JetEngine based on the query id
		return $query_builder->get_query_by_id( $jet_engine_query_builder_id );

	}

	/**
	 * Modify the main query under certain conditions.
	 *
	 * @param bool   $is_main_query  Whether the query is the main query.
	 * @param object $post           The current post object.
	 * @param object $query          The current WP_Query object.
	 *
	 * @return bool  Modified value for $is_main_query.
	 */
	public function maybe_modify_is_main_query( $is_main_query, $post, $query ) {
		$content_type = Database::$active_templates['content_type'] ?? '';

		if ( $is_main_query && $content_type === 'archive' ) {
			return ! $is_main_query;
		}

		return $is_main_query;
	}
}