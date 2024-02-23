<?php

namespace AcfBetterSearch\Search;

use AcfBetterSearch\HookableInterface;

/**
 * .
 */
class Init implements HookableInterface {

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_action( 'init', [ $this, 'init_search' ] );
	}

	/**
	 * @return void
	 */
	public function init_search() {
		if ( ! $this->is_search_available() ) {
			return;
		}

		( new Join() )->init_hooks();
		( new Query() )->init_hooks();
		( new Request() )->init_hooks();
		( new Where() )->init_hooks();
	}

	private function is_search_available(): bool {
		$is_ajax       = ( defined( 'DOING_AJAX' ) && DOING_AJAX );
		$is_media_ajax = ( isset( $_POST['action'] ) && in_array( $_POST['action'], [ 'query-attachments' ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$status = ( ! $is_ajax || ! $is_media_ajax );
		return apply_filters( 'acfbs_is_available', $status );
	}
}
