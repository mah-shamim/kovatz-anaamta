<?php
/**
 * Trait to use bounded parent -> child data class notices system
 */

trait Jet_Engine_Notices_Trait {

	/**
	 * Notices list
	 *
	 * @var array
	 */
	public $notices = array();

	/**
	 * Add notice to stack
	 *
	 * @param string $type    [description]
	 * @param [type] $message [description]
	 */
	public function add_notice( $type = 'error', $message = null ) {
		$this->notices[] = array(
			'type'    => $type,
			'message' => $message,
		);
	}

	/**
	 * Add notice to stack
	 *
	 * @param string $type    [description]
	 * @param [type] $message [description]
	 */
	public function get_notices() {
		return $this->notices;
	}

	/**
	 * Print stored notices
	 *
	 * @return [type] [description]
	 */
	public function print_notices() {

		if ( empty( $this->notices ) ) {
			return;
		}

		?>
		<div class="cpt-notices"><?php
			foreach ( $this->notices as $notice ) {
				printf( '<div class="notice notice-%1$s"><p>%2$s</p></div>', $notice['type'], $notice['message'] );
			}
		?></div>
		<?php

	}

}
