<?php
namespace Jet_Engine\Relations\Rest;

/**
 * Get all relations endpoint
 */

class Get_Relations extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'get-relations';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$relations = jet_engine()->relations->data->get_item_for_register();

		return rest_ensure_response( array(
			'success' => true,
			'data'    => $this->prepare_items( $relations ),
		) );

	}

	/**
	 * Prepare items to sent into editor
	 *
	 * @param  [type] $items [description]
	 * @return [type]        [description]
	 */
	public function prepare_items( $items ) {
		return array_filter( array_map( function( $item ) {

			if ( ! empty( $item['is_legacy'] ) ) {

				$item['hash'] = jet_engine()->relations->legacy->get_relation_hash( $item['args']['parent_object'], $item['args']['child_object'] );
				$item['name'] = $item['args']['name'];
				$item['related_objects'] = jet_engine()->relations->types_helper->relation_verbose(
					jet_engine()->relations->types_helper->type_name_by_parts( 'posts', $item['args']['parent_object'] ),
					jet_engine()->relations->types_helper->type_name_by_parts( 'posts', $item['args']['child_object'] )
				);

			} else {

				$id       = $item['id'];
				$relation = jet_engine()->relations->get_active_relations( $id );

				if ( ! $relation ) {
					return false;
				}

				if ( ! empty( $item['labels']['name'] ) ) {
					$name = $item['labels']['name'];
				} else {
					$name = $relation->get_relation_name();
				}

				$item['related_objects'] = jet_engine()->relations->types_helper->relation_verbose(
					$item['args']['parent_object'],
					$item['args']['child_object']
				);

				$item['name'] = $name;
			}

			return $item;

		}, $items ) );
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

}
