<?php
namespace Jet_Engine\Modules\Profile_Builder;

class Restrictions {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	private $_latest_message = false;

	public function current_user_can_submit_posts( $post_type = false ) {
		
		$res = true;
		$restrictions = Module::instance()->settings->get( 'posts_restrictions' );

		if ( empty( $restrictions ) ){
			return $res;
		}

		$found_restriction = false;

		if ( ! is_user_logged_in() ) {
			$roles = array( 'jet-engine-guest' );
		} else {
			$user  = wp_get_current_user();
			$roles = array_values( $user->roles );
		}

		$found_restrictions = array();
		$find_by_post_type  = false;

		foreach ( $restrictions as $restriction ) {

			if ( empty( $restriction['role'] ) ) {
				continue;
			}

			$intersect = array_intersect( $roles, $restriction['role'] );

			if ( ! empty( $intersect ) ) {
				$_post_type = ! empty( $restriction['post_type'] ) ? $restriction['post_type'] : 'all';

				if ( is_array( $_post_type ) ) {
					foreach ( $_post_type as $_p_type ) {
						$found_restrictions[ $_p_type ] = $restriction;
					}
				} else {
					$found_restrictions[ $_post_type ] = $restriction;
				}
			}
		}

		if ( isset( $found_restrictions[ $post_type ] ) ) {
			$found_restriction = $found_restrictions[ $post_type ];
			$find_by_post_type = $post_type;
		} else if ( isset( $found_restrictions['all'] ) ) {
			$found_restriction = $found_restrictions['all'];
		}

		$limit = ! empty( $found_restriction['limit'] ) ? absint( $found_restriction['limit'] ) : 0;

		if ( ! $limit ) {
			return $res;
		}

		$user_id    = isset( $user ) ? $user->ID : 0;
		$user_posts = $this->get_user_posts( $user_id, $find_by_post_type );

		if ( $user_posts >= $limit ) {
			$message = ! empty( $found_restriction['error_message'] ) ? $found_restriction['error_message'] : __( 'Posts limit reached', 'jet-engine' );
			$this->_latest_message = $message;
			$res = false;
		}

		return $res;

	}

	public function get_latest_message() {
		return $this->_latest_message;
	}

	/**
	 * Get user posts count
	 */
	public function get_user_posts( $user_id = null, $post_type = null ) {

		global $wpdb;

		$posts = $wpdb->posts;
		$user_id = absint( $user_id );

		$post_type_query = ! empty( $post_type ) ? "post_type = '$post_type' AND" : '';

		$query = sprintf( "SELECT COUNT(*) FROM $posts WHERE post_author = $user_id AND %s post_status IN ( 'draft', 'publish', 'trash' );", $post_type_query );

		$posts_num = $wpdb->get_var( $query );

		return absint( $posts_num );

	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

}
