<?php
/**
 * Timber editor render class
 */
namespace Jet_Engine\Timber_Views\Editor;

use Jet_Engine\Timber_Views\Package;
use Timber\Timber;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Save extends Preview {

	public function get_action() {
		return 'jet_engine_timber_save';
	}

	public function do_action() {

		$this->verify_request();

		$settings         = ! empty( $_POST['settings'] ) ? $_POST['settings'] : [];
		$listing_id       = ! empty( $_POST['id'] ) ? $_POST['id'] : false;
		$html             = ! empty( $_POST['html'] ) ? Package::instance()->sanitize_html( $_POST['html'] ) : '';
		$css              = ! empty( $_POST['css'] ) ? Package::instance()->sanitize_css( $_POST['css'] ) : '';
		$preview_settings = ! empty( $_POST['preview_settings'] ) ? $_POST['preview_settings'] : [];
		$title            = ! empty( $_POST['title'] ) ? $_POST['title'] : '';

		$listing = jet_engine()->listings->get_new_doc( [], $listing_id );

		$listing->update_listing_html( $html );
		$listing->update_listing_css( $css );

		/**
		 * Put listing settings directly into request for 3rd party callbacks comaptibility
		 */
		foreach ( $settings as $key => $value ) {
			$_REQUEST[ $key ] = $value;
		}

		jet_engine()->post_type->admin_screen->update_template( [
			'ID'         => $listing_id,
			'post_title' => $title,
			'meta_input' => [
				'_listing_type'            => Package::instance()->get_view_slug(),
				'_twig_preview_settings'   => $preview_settings,
				'_listing_data'            => $settings,
				'_elementor_page_settings' => $settings,
			],
		], Package::instance()->get_view_slug() );

		wp_send_json_success();

	}

}
