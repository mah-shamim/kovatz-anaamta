<?php

// load only if theme is not blockstrap
if ( defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) && ! defined( 'GEODIRECTORY_VERSION' ) ) {
	add_action( 'admin_notices', 'blockstrap_theme_plugin_suggestions' );
}

if ( ! function_exists( 'blockstrap_theme_plugin_suggestions' ) ) {
	/**
	 * Suggested plugins.
	 *
	 * @return void
	 */
	function blockstrap_theme_plugin_suggestions() {

		if ( isset( $_REQUEST['blockstrap_geodirectory_dismiss'] ) && check_admin_referer( 'blockstrap_geodirectory_nonce' ) ) {
			update_option( 'blockstrap_geodirectory_dismiss', time() );
		}

		$no_gd = get_option( 'blockstrap_geodirectory_dismiss' );

		// if accepted v3 then bail
		if ( $no_gd ) {
			return;
		}

		$install_url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'install-plugin',
					'plugin' => 'geodirectory',
				),
				admin_url( 'update.php' )
			),
			'install-plugin_geodirectory'
		);

		$dismiss_url = wp_nonce_url(
			add_query_arg(
				array(
					'blockstrap_geodirectory_dismiss' => 1,
				)
			),
			'blockstrap_geodirectory_nonce'
		);

		$class           = 'notice notice-info is-dismissible';
		$name            = __( 'GeoDirectory', 'blockstrap' );
		$install_message = __( 'BlockStrap works great with GeoDirectory to create a fast and modern listing directory.', 'blockstrap' );

		printf(
			'<div class="%1$s"><h3>%2$s</h3><p>%3$s</p><p><a href="%4$s" class="button button-primary">%5$s</a> <a href="%6$s" class="button button-secondary">%7$s</a> </p></div>',
			esc_attr( $class ),
			esc_html( $name ),
			esc_html( $install_message ),
			esc_url_raw( $install_url ),
			esc_html__( 'Install GeoDirectory', 'blockstrap' ),
			esc_url_raw( $dismiss_url ),
			esc_html__( 'No thanks', 'blockstrap' )
		);
	}
}

