<?php
namespace Jet_Engine\Relations\Controls;

use Jet_Engine\Relations\Types_Helper;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Term_Meta extends Base {

	/**
	 * Check if current control page is currently displayed
	 *
	 * @return boolean [description]
	 */
	public function is_control_page() {

		$args   = $this->get_args();
		$object = $args['object_name'];
		$screen = get_current_screen();

		if ( 'term' === $screen->base && 'edit-' . $object === $screen->id ) {
			return true;
		}

		return false;
	}

	/**
	 * Perform an control element wrapper initializtion
	 * @return [type] [description]
	 */
	public function init() {
		$args = $this->get_args();
		add_action( $args['object_name'] . '_edit_form', array( $this, 'render_meta_box' ), 9999, 2 );
	}

	/**
	 * Rewrite render control wrapper class to add title
	 *
	 * @return [type] [description]
	 */
	public function render_meta_box( $term, $tax ) {

		$this->print_current_object_id_for_js( $term->term_id );

		echo '<div class="jet-engine-terms-relations">';
		printf( '<h3>%s</h3>', $this->get_control_title() );
		printf( '<div id="%s"></div>', $this->get_el_id() );
		echo '</div>';

	}

}
