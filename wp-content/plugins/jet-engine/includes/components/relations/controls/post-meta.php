<?php
namespace Jet_Engine\Relations\Controls;

use Jet_Engine\Relations\Types_Helper;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Post_Meta extends Base {

	/**
	 * Check if current control page is currently displayed
	 *
	 * @return boolean [description]
	 */
	public function is_control_page() {

		$args   = $this->get_args();
		$object = $args['object_name'];
		$screen = get_current_screen();

		if ( 'post' === $screen->base && $object === $screen->id ) {
			return true;
		}

		return false;
	}

	/**
	 * Perform an control element wrapper initializtion
	 * @return [type] [description]
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'init_meta_box' ) );
	}

	/**
	 * Initialize meta box
	 *
	 * @return [type] [description]
	 */
	public function init_meta_box() {

		if ( ! $this->is_control_page() ) {
			return;
		}

		$args = $this->get_args();

		add_meta_box(
			'related_' . $this->get_el_id(),
			$this->get_control_title(),
			array( $this, 'render_meta_box' ),
			$args['object_name']
		);

	}

	/**
	 * Render control app wrapper inside mat box
	 *
	 * @return [type] [description]
	 */
	public function render_meta_box() {

		global $post;

		$this->print_current_object_id_for_js( $post->ID );

		printf( '<div id="%s"></div>', $this->get_el_id() );

	}

}
