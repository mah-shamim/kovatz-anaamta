<?php
namespace Jet_Engine\Relations\Dynamic_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Related_Item_Meta extends \Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'jet-engine-related-item-meta';
	}

	public function get_title() {
		return __( 'Related Item Meta', 'jet-engine' );
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

		$raw_fields  = jet_engine()->relations->get_active_relations_meta_fields();
		$meta_fields = array( '' => __( 'Select...', 'jet-engine' ) );

		if ( empty( $raw_fields ) ) {
			return $meta_fields;
		}

		foreach ( $raw_fields as $rel_id => $rel_data ) {

			foreach ( $rel_data['fields'] as $field ) {

				if ( ! empty( $type ) && ! in_array( $field['type'], $type ) ) {
					continue;
				}

				$key = $rel_id . '::' . $field['name'];
				$meta_fields[ $key ] = $field['title'];

			}

		}

		$this->add_control(
			'rel_meta_key',
			array(
				'label'   => __( 'Meta Field', 'jet-engine' ),
				'type'    => 'select',
				'options' => $meta_fields,
				'default' => '',
			)
		);

		$this->add_control(
			'rel_parent_from',
			array(
				'label'       => __( 'Parent Object ID From', 'jet-engine' ),
				'label_block' => true,
				'type'        => 'select',
				'options'     => jet_engine()->relations->sources->get_sources(),
				'default'     => 'current_object',
			)
		);

		$this->add_control(
			'rel_parent_var',
			array(
				'label'       => __( 'Parent Object Variable Name', 'jet-engine' ),
				'label_block' => true,
				'type'        => 'text',
				'default'     => '',
				'condition'   => array( 'rel_parent_from' => array( 'query_var', 'object_var' ) ),
			)
		);

		$this->add_control(
			'rel_child_from',
			array(
				'label'       => __( 'Child Object ID From', 'jet-engine' ),
				'type'        => 'select',
				'label_block' => true,
				'options'     => jet_engine()->relations->sources->get_sources(),
				'default'     => 'current_object',
			)
		);

		$this->add_control(
			'rel_child_var',
			array(
				'label'       => __( 'Child Object Variable Name', 'jet-engine' ),
				'label_block' => true,
				'type'        => 'text',
				'default'     => '',
				'condition'   => array( 'rel_child_from' => array( 'query_var', 'object_var' ) ),
			)
		);
	}

	public function get_value( array $options = array() ) {

		$meta_field      = $this->get_settings( 'rel_meta_key' );
		$rel_parent_from = $this->get_settings( 'rel_parent_from' );
		$rel_parent_var  = $this->get_settings( 'rel_parent_var' );
		$rel_child_from  = $this->get_settings( 'rel_child_from' );
		$rel_child_var   = $this->get_settings( 'rel_child_var' );

		if ( ! $meta_field ) {
			return '';
		}

		$meta_field = explode( '::', $meta_field );

		$rel_id = $meta_field[0];
		$key    = $meta_field[1];

		if ( ! $rel_id ) {
			return '';
		}

		$relation = jet_engine()->relations->get_active_relations( $rel_id );

		if ( ! $relation ) {
			return '';
		}

		$parent_id = false;
		$child_id  = false;

		if ( $rel_parent_from ) {

			$parent_id = jet_engine()->relations->sources->get_id_by_source( $rel_parent_from, $rel_parent_var );

			if ( ! $parent_id ) {
				return '';
			}

		}

		if ( $rel_child_from ) {

			$child_id = jet_engine()->relations->sources->get_id_by_source( $rel_child_from, $rel_child_var );

			if ( ! $child_id ) {
				return '';
			}

		}

		$meta = $relation->get_meta( $parent_id, $child_id, $key );

		return ! empty( $meta ) ? $meta : '';

	}

}
