<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Listings\Dynamic_Tags;

use Jet_Engine\Modules\Rest_API_Listings\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Image_Tag extends \Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'jet-rest-api-image';
	}

	public function get_title() {
		return __( 'REST API Image', 'jet-engine' );
	}

	public function get_group() {
		return \Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			\Jet_Engine_Dynamic_Tags_Module::IMAGE_CATEGORY,
		);
	}

	protected function register_controls() {

		$this->add_control(
			'rest_api_field',
			array(
				'label'  => __( 'Field', 'jet-engine' ),
				'type'   => \Elementor\Controls_Manager::SELECT,
				'groups' => Module::instance()->listings->add_source_fields( array() ),
			)
		);

		$this->add_control(
			'get_child',
			array(
				'label'     => __( 'Get child item from object/array', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::SWITCHER,
				'default'   => '',
			)
		);

		$this->add_control(
			'child_path',
			array(
				'label'       => __( 'Child item name', 'jet-engine' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'description' => __( 'Name of the child item to get. Or path to the nested child item. Separate nesting levels with "/". For example - level-1-name/level-2-name/child-item-name', 'jet-engine' ),
				'condition'   => array(
					'get_child' => 'yes',
				),
			)
		);

	}

	/**
	 * [get_value description]
	 *
	 * @param  array  $options [description]
	 * @return [type]          [description]
	 */
	public function get_value( array $options = array() ) {

		$field = $this->get_settings( 'rest_api_field' );
		$get_child = $this->get_settings( 'get_child' );

		if ( empty( $field ) ) {
			return;
		}

		$value = jet_engine()->listings->data->get_prop( $field );

		if ( $get_child && ( is_array( $value ) || is_object( $value ) ) ) {
			return array(
				'id'  => false,
				'url' => jet_engine_recursive_get_child( $value, $this->get_settings( 'child_path' ) ),
			);
		} elseif ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return array(
				'id'  => false,
				'url' => $value,
			);
		}

	}

}
