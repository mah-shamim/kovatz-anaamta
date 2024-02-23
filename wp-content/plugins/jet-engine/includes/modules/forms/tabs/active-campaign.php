<?php


namespace Jet_Engine\Modules\Forms\Tabs;


class Active_Campaign extends Base_Form_Tab {

	public function slug() {
		return 'active-campaign';
	}

	public function on_get_request() {
		$this->verify_request();

		$key = sanitize_text_field( $_POST['api_key'] );
		$url = sanitize_text_field( $_POST['api_url'] );

		$result = $this->update_options( array(
			'api_key' => $key,
			'api_url' => $url
		) );

		$result ? wp_send_json_success( array(
			'message' => __( 'Saved successfully!', 'jet-fom-builder' )
		) ) : wp_send_json_error( array(
			'message' => __( 'Unsuccessful save.', 'jet-form-builder' )
		) );
	}

	public function on_load() {
		return $this->get_options( array(
			'api_key' => '',
			'api_url' => ''
		) );
	}
}