<?php
namespace Jet_Engine\Query_Builder\Rest;

use Jet_Engine\Query_Builder\Manager;

class Search_Query_Field_Options extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'search-query-field-options';
	}

	/**
	 * API callback
	 *
	 * @param $request
	 *
	 * @return void|\WP_Error|\WP_REST_Response
	 */
	public function callback( $request ) {

		$params   = $request->get_params();
		$search_q = $params['q'];
		$query_id = $params['query_id'];
		$values   = ! empty( $params['values'] ) ? explode( ',', $params['values'] ) : '';
		$result   = array();

		$query = Manager::instance()->get_query_by_id( $query_id );

		if ( ! $query ) {
			return rest_ensure_response( $result );
		}

		$items = $query->get_items();

		if ( empty( $items ) ) {
			return rest_ensure_response( $result );
		}

		$value_field = ! empty( $params['value_field'] ) ? $params['value_field'] : 'ID';
		$label_field = ! empty( $params['label_field'] ) ? $params['label_field'] : 'post_title';

		foreach ( $items as $item ) {

			$value = ( is_object( $item ) && isset( $item->$value_field ) ) ? $item->$value_field : null;

			if ( null === $value ) {
				continue;
			}

			$result[] = array(
				'value' => $value,
				'label' => isset( $item->$label_field ) ? $item->$label_field : $value,
			);
		}

		if ( ! empty( $values ) ) {

			$result = array_filter( $result, function ( $option ) use ( $values ) {
				return in_array( $option['value'], $values );
			} );

		} else {

			$search_q = trim( $search_q );
			$search_q = strtolower( $search_q );

			$result = array_filter( $result, function ( $option ) use ( $search_q ) {
				return false !== strpos( strtolower( $option['value'] ), $search_q ) || false !== strpos( strtolower( $option['label'] ), $search_q );
			} );
		}

		$result = array_values( $result );

		return rest_ensure_response( $result );
	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELETE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'GET';
	}

	/**
	 * Check user access to current end-point
	 *
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Returns arguments config
	 *
	 * @return array
	 */
	public function get_args() {
		return array(
			'q' => array(
				'default'  => '',
				'required' => false,
			),
			'query_id' => array(
				'default'  => '',
				'required' => true,
			),
			'value_field' => array(
				'default'  => '',
				'required' => false,
			),
			'label_field' => array(
				'default'  => '',
				'required' => false,
			),
			'values' => array(
				'default'  => '',
				'required' => false,
			),
		);
	}

}
