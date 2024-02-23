<?php
namespace Jet_Engine\Relations\Macros;

/**
 * Required methods:
 * macros_tag()  - here you need to set macros tag for JetEngine core
 * macros_name() - here you need to set human-readable macros name for different UIs where macros are available
 * macros_callback() - the main function of the macros. Returns the value
 * macros_args() - Optional, arguments list for the macros. Arguments format is the same ad for Elementor controls
 */
class Get_Related_Grandchildren extends Get_Related_Grandparents {

	/**
	 * Returns macros tag
	 *
	 * @return string
	 */
	public function macros_tag() {
		return 'rel_get_grandchildren';
	}

	/**
	 * Returns macros name
	 *
	 * @return string
	 */
	public function macros_name() {
		return __( 'Related Grandchildren', 'jet-engine' );
	}

	/**
	 * Returns related IDs list
	 * @param  [type] $rel_id    [description]
	 * @param  [type] $object_id [description]
	 * @return [type]            [description]
	 */
	public function get_related_ids( $rel_id, $object_id ) {
		return jet_engine()->relations->hierachy->get_grandchildren( $rel_id, $object_id );
	}

	/**
	 * Returns object option label
	 * @return [type] [description]
	 */
	public function object_option_label() {
		return __( 'Grandparent ID is', 'jet-engine' );
	}

}
