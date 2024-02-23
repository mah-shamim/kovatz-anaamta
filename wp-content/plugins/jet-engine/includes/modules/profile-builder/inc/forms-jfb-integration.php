<?php
namespace Jet_Engine\Modules\Profile_Builder;


class Forms_Jfb_Integration {

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_filter( 'jet-form-builder/action/insert-post/pre-check', array( $this, 'check_posts_limit' ), 10, 3 );
	}

	/**
	 * Check posts limit
	 *
	 * @param $res
	 * @param $source_arr
	 * @param $action
	 *
	 * @return bool|mixed
	 * @throws \Jet_Form_Builder\Exceptions\Action_Exception
	 */
	public function check_posts_limit( $res, $postarr, $action ) {

		// Apply restrictions only for post inserting, not the update
		if ( ! empty( $postarr['ID'] ) ) {
			return $res;
		}
		
		$restrictions = Module::instance()->get_restrictions_handler();
		$check = $restrictions->current_user_can_submit_posts( $postarr['post_type'] );

		if ( ! $check ) {
			throw new \Jet_Form_Builder\Exceptions\Action_Exception(
				$restrictions->get_latest_message()
			);
		}

		return true;

	}

}
