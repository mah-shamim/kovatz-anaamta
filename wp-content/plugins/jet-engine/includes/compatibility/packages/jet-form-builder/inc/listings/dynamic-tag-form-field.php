<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Form_Builder\Listings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Dynamic_Tag_Form_Field extends \Elementor\Core\DynamicTags\Tag {

	public function get_name() {
		return 'jet-form-builder-record-field';
	}

	public function get_title() {
		return __( 'JetFormBuilder Record Field', 'jet-engine' );
	}

	public function get_group() {
		return \Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::NUMBER_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::POST_META_CATEGORY,
		);
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {

		$this->add_control(
			'field_name',
			array(
				'label'       => __( 'Field Name', 'jet-engine' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __( 'Please enter form field name to show recorded value of this field. If field contains array of values, please add path to exact value. For example filed_name/0, field_name/1/nested_key, field_name/nested_key etc.', 'jet-engine' ),
			)
		);

		$this->add_control(
			'field_name_note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => __( '<b>Please note:</b> Works only with JetFromBuilder records query', 'jet-engine' ),
				'content_classes' => 'elementor-descriptor',
			)
		);

	}

	public function render() {

		$field_name = $this->get_settings( 'field_name' );

		if ( ! $field_name ) {
			return;
		}

		echo Manager::instance()->get_form_record_field( $field_name, false );

	}

}
