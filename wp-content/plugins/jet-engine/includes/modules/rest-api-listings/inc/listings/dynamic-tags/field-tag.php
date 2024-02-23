<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Listings\Dynamic_Tags;

use Jet_Engine\Modules\Rest_API_Listings\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Field_Tag extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'jet-rest-api-field';
	}

	public function get_title() {
		return __( 'REST API Field', 'jet-engine' );
	}

	public function get_group() {
		return \Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::NUMBER_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::URL_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::POST_META_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::COLOR_CATEGORY
		);
	}

	public function is_settings_required() {
		return true;
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

	public function render() {

		$field = $this->get_settings( 'rest_api_field' );
		$get_child = $this->get_settings( 'get_child' );

		if ( empty( $field ) ) {
			return;
		}

		$value = jet_engine()->listings->data->get_prop( $field );

		if ( $get_child && ( is_array( $value ) || is_object( $value ) ) ) {

			$filtered = jet_engine_recursive_get_child( $value, $this->get_settings( 'child_path' ) );

			if ( is_object( $filtered ) ) {
				echo __( '<b>Error:</b> can\'t render field data in the current format. You can try "Get child value" callback. Available children: ', 'jet-engine' ) . implode( ', ', array_keys( get_object_vars( $filtered ) ) ) . '. ' . __( 'Or one of array-related callbacks - "Multiple select field values", "Checkbox field values", "Checked values list" etc', 'jet-engine' );
			} elseif ( is_array( $filtered ) ) {
				echo __( '<b>Error:</b> can\'t render field data in the current format. You can try "Get child value" callback. Available children: ', 'jet-engine' ) . implode( ', ', array_keys( $filtered ) ) . '. ' . __( 'Or one of array-related callbacks - "Multiple select field values", "Checkbox field values", "Checked values list" etc', 'jet-engine' );
			} else {
				echo $filtered;
			}

		} elseif ( is_array( $value ) ) {
			echo jet_engine_render_checkbox_values( $value );
			return $value;
		} elseif ( is_object( $value ) ) {
			echo __( '<b>Error:</b> can\'t render field data in the current format. You can try "Get child value" callback. Available children: ', 'jet-engine' ) . implode( ', ', array_keys( get_object_vars( $value ) ) ) . '. ' . __( 'Or one of array-related callbacks - "Multiple select field values", "Checkbox field values", "Checked values list" etc', 'jet-engine' );
		} else {
			echo wp_kses_post( $value );
		}

	}

}
