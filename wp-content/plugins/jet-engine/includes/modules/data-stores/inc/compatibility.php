<?php
namespace Jet_Engine\Modules\Data_Stores;

/**
 * Define Compatibility class
 */
class Compatibility {

	public function __construct() {

		// WP Rocket Compatibility hooks.
		if ( defined('WP_ROCKET_VERSION' ) ) {
			add_action( 'jet-engine/data-stores/post-count-increased', array( $this, 'clean_wp_rocket_cache' ) );
			add_action( 'jet-engine/data-stores/post-count-decreased', array( $this, 'clean_wp_rocket_cache' ) );
		}

	}

	/**
	 * Clean WP Rocket Cache.
	 *
	 * @param $post_id
	 */
	public function clean_wp_rocket_cache( $post_id ) {

		if ( empty( $post_id ) ) {
			return;
		}

		if ( ! function_exists( 'rocket_clean_post' ) ) {
			return;
		}

		rocket_clean_post( $post_id );
	}

}