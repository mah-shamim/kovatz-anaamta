<?php
namespace Jet_Engine\Relations\Dynamic_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Related_Items extends \Elementor\Core\DynamicTags\Data_Tag {

	public function get_name() {
		return 'jet-engine-related-items';
	}

	public function get_title() {
		return __( 'Related Items', 'jet-engine' );
	}

	public function get_group() {
		return \Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
			\Jet_Engine_Dynamic_Tags_Module::POST_META_CATEGORY,
		);
	}

	public function is_settings_required() {
		return true;
	}

	protected function register_controls() {

		$this->add_control(
			'rel_id',
			array(
				'label'   => __( 'From Relation', 'jet-engine' ),
				'type'    => 'select',
				'options' => jet_engine()->relations->get_relations_for_js( true ),
				'default' => '',
			)
		);

		$this->add_control(
			'rel_object',
			array(
				'label'   => __( 'From Object (what to show)', 'jet-engine' ),
				'type'    => 'select',
				'options' => array(
					'parent_object' => __( 'Parent Object', 'jet-engine' ),
					'child_object'  => __( 'Child Object', 'jet-engine' ),
				),
				'default' => 'child_object',
			)
		);

		$this->add_control(
			'rel_object_from',
			array(
				'label'   => __( 'Initial Object ID From (get initial ID here)', 'jet-engine' ),
				'type'    => 'select',
				'options' => jet_engine()->relations->sources->get_sources(),
				'default' => 'current_object',
			)
		);

		$this->add_control(
			'rel_object_var',
			array(
				'label'     => __( 'Variable Name', 'jet-engine' ),
				'type'      => 'text',
				'default'   => '',
				'condition' => array( 'rel_object_from' => array( 'query_var', 'object_var' ) ),
			)
		);
	}

	public function get_value( array $options = array() ) {

		$rel_id          = $this->get_settings( 'rel_id' );
		$rel_object      = $this->get_settings( 'rel_object' );
		$rel_object_from = $this->get_settings( 'rel_object_from' );
		$rel_object_var  = $this->get_settings( 'rel_object_var' );

		if ( ! $rel_id ) {
			return;
		}

		$relation = jet_engine()->relations->get_active_relations( $rel_id );

		if ( ! $relation ) {
			return;
		}

		$object_id = false;

		if ( $rel_object_from ) {

			$object_id = jet_engine()->relations->sources->get_id_by_source( $rel_object_from, $rel_object_var );

			if ( ! $object_id ) {
				return 'not-found';
			}

		}

		$related_ids = array();

		switch ( $rel_object ) {
			case 'parent_object':
				$related_ids = $relation->get_parents( $object_id, 'ids' );
				break;

			default:
				$related_ids = $relation->get_children( $object_id, 'ids' );
				break;
		}

		$related_ids = ! empty( $related_ids ) ? $related_ids : array( PHP_INT_MAX );

		do_action( 'jet-engine/relations/dynamic-tag/get-related', $relation, $related_ids, $this );

		return implode( ',', $related_ids );

	}

}
