<?php
/**
 * This class watch for currently did posts content to avoid infinity loops appearing, but allow to output the same content by different listings
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Did_Posts_Watcher class
 */
class Jet_Engine_Did_Posts_Watcher {

	/**
	 * Property to store all posts fired post_content prop
	 * @var array
	 */
	private $did_posts = array();

	/**
	 * Property to store current listing posts fired post_content prop
	 * @var array
	 */
	private $currently_did_posts = array();

	/**
	 * Do current post
	 *
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	public function do_post( $post_id = null ) {

		if ( ! $post_id || $this->did_post( $post_id ) ) {
			return;
		}

		$this->did_posts[] = $post_id;

		if ( jet_engine()->frontend->get_listing_id() ) {
			$this->currently_did_posts[] = $post_id;
		}

	}

	/**
	 * Check if current post already did
	 * 
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	public function did_post( $post_id ) {
		return in_array( $post_id, $this->did_posts );
	}

	/**
	 * Remove curretly did posts from total did posts to allow output content of the same post with another listing
	 *
	 * @return [type] [description]
	 */
	public function reset_currently_did_posts() {

		if ( ! empty( $this->currently_did_posts ) ) {
			$this->did_posts = array_diff( $this->did_posts, $this->currently_did_posts );
		}

		$this->currently_did_posts = array();
	}

}
