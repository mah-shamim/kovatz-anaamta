<?php

namespace AcfBetterSearch\Settings;

/**
 * .
 */
class Save {

	/**
	 * @var Options
	 */
	private $options;

	public function __construct( Options $options = null ) {
		$this->options = $options ?: new Options();
	}

	/**
	 * @return void
	 */
	public function init_saving() {
		if ( ! isset( $_REQUEST['acfbs_save'] ) || ! isset( $_REQUEST['_wpnonce'] )
			|| ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'acfbs-save' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			return;
		}

		$this->save_fields_types();
		$this->save_features();
	}

	/**
	 * @return void
	 */
	private function save_fields_types() {
		if ( get_option( 'acfbs_lite_mode', false ) ) {
			return;
		}

		$value = $_REQUEST['acfbs_fields_types'] ?? []; // phpcs:ignore WordPress.Security
		$types = $this->options->get_fields_settings();

		$value = array_filter(
			$value,
			function ( $type ) use ( $types ) {
				return array_key_exists( $type, $types );
			}
		);
		$this->save_option( 'acfbs_fields_types', $value );
	}

	/**
	 * @return void
	 */
	private function save_features() {
		$features = array_merge(
			$this->options->get_features_default_settings(),
			$this->options->get_features_advanced_settings()
		);

		foreach ( $features as $key => $label ) {
			$value = ( isset( $_REQUEST['acfbs_features'] ) && in_array( $key, $_REQUEST['acfbs_features'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->save_option( sprintf( 'acfbs_%s', $key ), $value );
		}
	}

	/**
	 * @param string $key   .
	 * @param mixed  $value .
	 *
	 * @return void
	 */
	private function save_option( string $key, $value ) {
		if ( get_option( $key, false ) !== false ) {
			update_option( $key, $value );
		} else {
			add_option( $key, $value );
		}
	}
}
