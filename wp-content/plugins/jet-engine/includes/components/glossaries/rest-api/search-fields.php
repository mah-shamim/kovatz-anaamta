<?php
namespace Jet_Engine\Glossaries\Rest;

class Search_Fields extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'search-glossary-fields';
	}

	/**
	 * API callback
	 *
	 * @param $request
	 *
	 * @return void|\WP_Error|\WP_REST_Response
	 */
	public function callback( $request ) {

		$params      = $request->get_params();
		$query       = $params['query'];
		$glossary_id = $params['glossary_id'];
		$values      = $params['values'];

		if ( ! empty( $values ) ) {
			$values = explode( ',', $values );
		}

		$item = jet_engine()->glossaries->data->get_item_for_edit( $glossary_id );

		if ( empty( $item ) || empty( $item['fields'] ) ) {
			return rest_ensure_response( array() );
		}

		$result = $item['fields'];

		if ( ! empty( $values ) ) {

			$result = array_filter( $result, function ( $option ) use ( $values ) {
				return in_array( $option['value'], $values );
			} );

		} else {

			$query = trim( $query );
			$query = strtolower( $query );

			$result = array_filter( $result, function ( $option ) use ( $query ) {
				return false !== strpos( strtolower( $option['value'] ), $query ) || false !== strpos( strtolower( $option['label'] ), $query );
			} );

		}

		$result = array_values( $result );

		return rest_ensure_response( $result );
	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELTE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'GET';
	}

	/**
	 * Check user access to current end-popint
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
			'query' => array(
				'default'  => '',
				'required' => false,
			),
			'glossary_id' => array(
				'default'  => '',
				'required' => true,
			),
			'values' => array(
				'default'  => '',
				'required' => false,
			),
		);
	}

}
