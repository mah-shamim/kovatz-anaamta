<?php
namespace Jet_Engine\Relations\Controls;

use Jet_Engine\Relations\Types_Helper;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class User_Meta extends Base {

	/**
	 * Show on profile or not
	 *
	 * @return [type] [description]
	 */
	public function show_on_profile() {
		return apply_filters( 'jet-engine/relations/user-meta-control/show-on-profile', true );
	}

	/**
	 * Check if current control page is currently displayed
	 *
	 * @return boolean [description]
	 */
	public function is_control_page() {

		$args    = $this->get_args();
		$object  = $args['object_name'];
		$screen  = get_current_screen();
		$allowed = array( 'user-edit' );

		if ( $this->show_on_profile() ) {
			$allowed[] = 'profile';
		}

		if ( in_array( $screen->base, $allowed ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Perform an control element wrapper initializtion
	 * @return [type] [description]
	 */
	public function init() {

		add_action( 'edit_user_profile', array( $this, 'render_meta_box' ), 10 );

		if ( $this->show_on_profile() ) {
			add_action( 'show_user_profile', array( $this, 'render_meta_box' ), 10 );
		}
	}

	/**
	 * Returns current user ID
	 *
	 * @return [type] [description]
	 */
	public function get_user_id() {

		$user_id = isset( $_GET['user_id'] ) ? absint( $_GET['user_id'] ) : false;

		if ( ! $user_id && 'profile' === get_current_screen()->base ) {
			$user_id = get_current_user_id();
		}

		return $user_id;

	}

	/**
	 * Render control app wrapper inside mat box
	 *
	 * @return [type] [description]
	 */
	public function render_meta_box() {

		if ( ! $this->is_control_page() ) {
			return;
		}

		$user_id = $this->get_user_id();

		$this->print_current_object_id_for_js( $user_id );

		echo '<div class="jet-engine-user-relations">';
		printf( '<h3>%s</h3>', $this->get_control_title() );
		printf( '<div id="%s"></div>', $this->get_el_id() );
		echo '</div>';
	}

}
