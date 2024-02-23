<?php

/**
 * Suggested plugins.
 *
 * @return void
 */
function blockstrap_theme_plugin_suggestions() {

	if ( isset( $_REQUEST['blockstrap_geodirectory_dismiss'] ) && check_admin_referer( 'blockstrap_geodirectory_nonce' ) ) {
		update_option( 'directory_geodirectory_dismiss', time() );
	}

	$no_gd = get_option( 'directory_geodirectory_dismiss' );

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

	$class           = 'notice notice-warning is-dismissible';
	$name            = __( 'GeoDirectory', 'directory' );
	$install_message = __( 'GeoDirectory is required for the directory functionality. Please install it now to continue.', 'directory' );

	printf(
		'<div class="%1$s"><h3>%2$s</h3><p>%3$s</p><p><a href="%4$s" class="button button-primary">%5$s</a> <a href="%6$s" class="button button-secondary">%7$s</a> </p></div>',
		esc_attr( $class ),
		esc_html( $name ),
		esc_html( $install_message ),
		esc_url_raw( $install_url ),
		esc_html__( 'Install GeoDirectory', 'directory' ),
		esc_url_raw( $dismiss_url ),
		esc_html__( 'No thanks', 'directory' )
	);
}