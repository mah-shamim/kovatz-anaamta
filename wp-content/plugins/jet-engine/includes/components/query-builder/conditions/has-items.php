<?php
namespace Jet_Engine\Query_Builder\Conditions;

use Jet_Engine\Query_Builder\Manager;

class Has_Items extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'query-has-items';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Query Has Items', 'jet-engine' );
	}

	public function get_custom_controls() {
		return array(
			'query_id' => array(
				'type'        => 'select',
				'label'       => __( 'Query to check', 'jet-engine' ),
				'label_block' => true,
				'default'     => '',
				'options'     => Manager::instance()->get_queries_for_options(),
			),
		);
	}

	/**
	 * Check if is condition available for meta fields control
	 *
	 * @return boolean [description]
	 */
	public function is_for_fields() {
		return false;
	}

	/**
	 * Check if is condition available for meta value control
	 *
	 * @return boolean [description]
	 */
	public function need_value_detect() {
		return false;
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$query_id = ! empty( $args['condition_settings']['query_id'] ) ? $args['condition_settings']['query_id'] : false;
		$type     = ! empty( $args['type'] ) ? $args['type'] : 'show';

		if ( ! $query_id ) {
			return ( 'show' === $type ) ? false : true;
		}

		$query = Manager::instance()->get_query_by_id( $query_id );

		if ( ! $query ) {
			return ( 'show' === $type ) ? false : true;
		}

		return ( 'show' === $type ) ? $query->has_items() : ! $query->has_items();

	}

}
