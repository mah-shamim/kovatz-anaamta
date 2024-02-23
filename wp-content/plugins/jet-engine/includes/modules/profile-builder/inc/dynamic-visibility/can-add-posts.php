<?php
namespace Jet_Engine\Modules\Profile_Builder\Dynamic_Visibility;

use Jet_Engine\Modules\Profile_Builder\Module;

class User_Can_Add_Posts extends \Jet_Engine\Modules\Dynamic_Visibility\Conditions\Base {

	/**
	 * Returns condition ID
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'user-can-add-posts';
	}

	/**
	 * Returns condition name
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'User Can Add Posts', 'jet-engine' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return [type] [description]
	 */
	public function get_group() {
		return 'user';
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return [type] [description]
	 */
	public function check( $args = array() ) {

		$type = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$restrictions = Module::instance()->get_restrictions_handler();
		$post_type = ! empty( $args['value'] ) ? $args['value'] : false;

		if ( 'hide' === $type ) {
			return ! $restrictions->current_user_can_submit_posts( $post_type );
		} else {
			return $restrictions->current_user_can_submit_posts( $post_type );
		}

	}

	/**
	 * Check if is condition available for meta fields control
	 *
	 * @return boolean [description]
	 */
	public function is_for_fields() {
		return false;
	}

	/**
	 * Check if is condition available for meta value control
	 *
	 * @return boolean [description]
	 */
	public function need_value_detect() {
		return true;
	}

	/**
	 * Returns condition specific repeater controls
	 */
	public function get_custom_controls() {
		return array(
			'user_can_add_posts_notice' => array(
				'type' => 'raw_html',
				'raw'  => __( 'If you restricted posts by post types, set restricted post type slug into the value field', 'jet-engine' ),
			),
		);
	}

}
