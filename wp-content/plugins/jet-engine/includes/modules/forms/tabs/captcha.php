<?php


namespace Jet_Engine\Modules\Forms\Tabs;


class Captcha extends Base_Form_Tab {

	public function slug() {
		return 'captcha';
	}

	public function on_get_request() {
		$this->verify_request();

		$secret = sanitize_text_field( $_POST['secret'] );
		$key   = sanitize_text_field( $_POST['key'] );

		$result = $this->update_options( array(
			'secret' => $secret,
			'key'   => $key
		) );

		$result ? wp_send_json_success( array(
			'message' => __( 'Saved successfully!', 'jet-engine' )
		) ) : wp_send_json_error( array(
			'message' => __( 'Unsuccessful save.', 'jet-engine' )
		) );
	}

	public function on_load() {
		return $this->get_options( array(
			'key'   => '',
			'secret' => ''
		) );
	}
}