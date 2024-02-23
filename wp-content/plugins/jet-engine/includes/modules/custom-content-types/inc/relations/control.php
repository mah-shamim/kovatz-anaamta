<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Relations;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Control extends \Jet_Engine\Relations\Controls\Base {

	/**
	 * Check if current control page is currently displayed
	 *
	 * @return boolean [description]
	 */
	public function is_control_page() {

		$args   = $this->get_args();
		$object = $args['object_name'];

		if ( ! empty( $_GET['page'] ) && 'jet-cct-' . $object === $_GET['page'] && ! empty( $_GET['item_id'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Perform an control element wrapper initializtion
	 * @return [type] [description]
	 */
	public function init() {

		$args   = $this->get_args();
		$object = $args['object_name'];

		add_action( 'jet-engine/custom-content-types/after-edit-page/' . $object, array( $this, 'render_meta_box' ) );

	}

	/**
	 * Rewrite render control wrapper class to add title
	 *
	 * @return [type] [description]
	 */
	public function render_meta_box() {

		$item_id = ! empty( $_GET['item_id'] ) ? absint( $_GET['item_id'] ) : false;

		if ( ! $item_id ) {
			return;
		}

		$this->print_current_object_id_for_js( $item_id );

		echo '<div class="jet-engine-cct-relations">';
		printf( '<h3>%s</h3>', $this->get_control_title() );
		printf( '<div id="%s"></div>', $this->get_el_id() );
		echo '</div>';

	}

}
