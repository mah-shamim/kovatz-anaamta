<?php
namespace Jet_Engine\Relations\Dynamic_Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Related_Siblings extends Related_Items {

	public function get_name() {
		return 'jet-engine-related-siblings';
	}

	public function get_title() {
		return __( 'Related Siblings', 'jet-engine' );
	}

	protected function register_controls() {
		parent::register_controls();
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

		if ( ! $rel_object_from ) {
			return false;
		}

		$object_id = jet_engine()->relations->sources->get_id_by_source( $rel_object_from, $rel_object_var );

		if ( ! $object_id ) {
			return false;
		}

		$ids = $relation->get_siblings( $object_id, $rel_object, 'ids' );
		$ids = ! empty( $ids ) ? $ids : array( PHP_INT_MAX );

		do_action( 'jet-engine/relations/macros/get-siblings', $relation, $ids, $this );

		return implode( ',', $ids );

	}

}
