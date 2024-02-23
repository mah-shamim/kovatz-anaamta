<?php
namespace Jet_Engine\Query_Builder\Macros;

use Jet_Engine\Query_Builder\Manager;

class Query_Results_Macro extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'query_results';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Query Results', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_args() {

		return array(
			'query_id' => array(
				'label'   => __( 'Query', 'jet-engine' ),
				'type'    => 'select',
				'options' => function() {

					$queries = Manager::instance()->get_queries_for_options();
					$page = Manager::instance()->get_current_page();

					if ( $page && 'edit' === $page->get_slug() && $page->item_id() && isset( $queries[ $page->item_id() ] ) ) {
						unset( $queries[ $page->item_id() ] );
					}

					return $queries;
				},
			),
			'result_type' => array(
				'label'   => esc_html__( 'Return', 'jet-engine' ),
				'type'    => 'select',
				'default' => 'ids',
				'options' => array(
					'ids'      => esc_html__( 'List of items IDs', 'jet-engine' ),
					'all'      => esc_html__( 'List of items objects', 'jet-engine' ),
					'selected' => esc_html__( 'List of selected fields from item object', 'jet-engine' ),
				),
			),
			'result_fields' => array(
				'label'     => esc_html__( 'Comma-separated fields list', 'jet-engine' ),
				'type'      => 'text',
				'default'   => 'ID',
				'condition' => array( 'result_type' => array( 'selected' ) ),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {
		
		$query_id    = ! empty( $args['query_id'] ) ? $args['query_id'] : false;
		$result_type = ! empty( $args['result_type'] ) ? $args['result_type'] : 'ids';

		if ( ! $query_id ) {
			return array();
		}

		$query = Manager::instance()->get_query_by_id( $query_id );

		if ( ! $query ) {
			return array();
		}

		$items = $query->get_items();

		switch ( $result_type ) {
			case 'ids':
				$items = array_map( function( $item ) {
					return jet_engine()->listings->data->get_current_object_id( $item );
				}, $items );
				break;

			case 'selected':

				$result_fields = ! empty( $args['result_fields'] ) ? $args['result_fields'] : '';
				$result_fields = explode( ',', $result_fields );

				$items = array_filter( array_map( function( $item ) use ( $result_fields ) {

					if ( ! $result_fields ) {
						return $item;
					}

					if ( 1 === count( $result_fields ) ) {
						$field = trim( $result_fields[0] );
						return isset( $item->$field ) ? $item->$field : false;
					} else {
						$result = array();
						$item   = get_object_vars( $item );
						foreach ( $result_fields as $field ) {
							$field            = trim( $field );
							$result[ $field ] = isset( $item[ $field ] ) ? $item[ $field ] : false;
						}
					}

					return $result;

				}, $items ) );

				break;

		}

		$items = array_filter( $items );

		return ! empty( $items ) ? $items : false;

	}

}
