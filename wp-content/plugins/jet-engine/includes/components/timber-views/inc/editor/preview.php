<?php
/**
 * Timber editor render class
 */
namespace Jet_Engine\Timber_Views\Editor;

use Jet_Engine\Timber_Views\Package;
use Timber\Timber;
use Timber\Post;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Preview {

	private $nonce_action = 'jet_engine_timber_editor';

	public function __construct() {
		add_action( 'wp_ajax_' . $this->get_action(), [ $this, 'do_action' ] );
	}

	public function get_action() {
		return 'jet_engine_timber_reload_preview';
	}

	public function nonce() {
		return wp_create_nonce( $this->nonce_action );
	}

	public function verify_request() {
		
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], $this->nonce_action ) ) {
			wp_send_json_error( [
				'message' => __( 'Link is expired', 'jet-engine' ),
			] );
		}

		if ( empty( $_POST['id'] ) || ! current_user_can( 'edit_post', $_POST['id'] ) ) {
			wp_send_json_error( [
				'message' => __( 'You do not have access to given post', 'jet-engine' ),
			] );
		}

		return true;
	}

	public function do_action() {

		$this->verify_request();

		$settings   = ! empty( $_POST['settings'] ) ? $_POST['settings'] : [];
		$listing_id = ! empty( $_POST['id'] ) ? $_POST['id'] : false;
		$preview    = new \Jet_Engine_Listings_Preview( $settings, $listing_id );

		$preview_object = $preview->get_preview_object();

		if ( $preview_object && 'WP_Post' === get_class( $preview_object ) ) {
			global $post;
			$post = $preview_object;
		}

		$preview_html = Package::instance()->render_html(
			$_POST['html'], 
			Package::instance()->get_context_for_object( $preview_object )
		);

		wp_send_json_success( [
			'preview' => $preview_html
		] );

	}

}
