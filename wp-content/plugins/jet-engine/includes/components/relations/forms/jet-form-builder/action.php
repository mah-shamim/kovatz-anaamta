<?php


namespace Jet_Engine\Relations\Forms\Jet_Form_Builder_Forms;

use Jet_Engine\Relations\Forms\Manager as Forms;
use Jet_Form_Builder\Actions\Action_Handler;
use Jet_Form_Builder\Actions\Types\Base;
use Jet_Form_Builder\Exceptions\Action_Exception;

class Action extends Base {

	public function get_id() {
		return Forms::instance()->slug();
	}

	public function get_name() {
		return Forms::instance()->action_title();
	}

	/**
	 * @param array $request
	 * @param Action_Handler $handler
	 *
	 * @return void
	 * @throws Action_Exception
	 */
	public function do_action( array $request, Action_Handler $handler ) {
		$relation         = ! empty( $this->settings['relation'] ) ? $this->settings['relation'] : false;
		$parent_field     = ! empty( $this->settings['parent_id'] ) ? $this->settings['parent_id'] : false;
		$parent_id        = ! empty( $request[ $parent_field ] ) ? $request[ $parent_field ] : false;
		$child_field      = ! empty( $this->settings['child_id'] ) ? $this->settings['child_id'] : false;
		$context          = ! empty( $this->settings['context'] ) ? $this->settings['context'] : 'child';
		$store_items_type = ! empty( $this->settings['store_items_type'] ) ? $this->settings['store_items_type'] : 'replace';
		$child_id         = ! empty( $request[ $child_field ] ) ? $request[ $child_field ] : false;

		$res = Forms::instance()->update_related_items(
			array(
				'relation'         => $relation,
				'parent_id'        => $parent_id,
				'child_id'         => $child_id,
				'context'          => $context,
				'store_items_type' => $store_items_type,
			)
		);

		if ( is_wp_error( $res ) ) {
			throw ( new Action_Exception( $res->get_error_message() ) )->dynamic_error();
		}
	}

	public function self_script_name() {
		return 'JetFBRelationsConfig';
	}

	public function editor_labels() {
		return array(
			'relation'        => __( 'Relation:', 'jet-engine' ),
			'parent_id'       => __( 'Parent Item ID:', 'jet-engine' ),
			'child_id'        => __( 'Child Item ID:', 'jet-engine' ),
			'context'         => __( 'Update Context:', 'jet-engine' ),
			'store_items_type' => __( 'How to Store New Items:', 'jet-engine' ),
		);
	}

	public function action_data() {
		return array(
			'relations'               => jet_engine()->relations->get_relations_for_js(),
			'context_options'         => array(
				array(
					'value' => 'child',
					'label' => __( 'We updating children items for the parent object', 'jet-engine' ),
				),
				array(
					'value' => 'parent',
					'label' => __( 'We updating parent items for the child object', 'jet-engine' ),
				),
			),
			'store_items_type_options' => array(
				array(
					'value' => 'replace',
					'label' => __( 'Replace existing related items with items from the form (default)', 'jet-engine' ),
				),
				array(
					'value' => 'append',
					'label' => __( 'Append new items to already existing related items', 'jet-engine' ),
				),
				array(
					'value' => 'disconnect',
					'label' => __( 'Disconnect selected items', 'jet-engine' ),
				),
			),
		);
	}
}
