<?php
/**
 * Backwards compatibility
 *
 * @package BlockStrap
 * @since 1.0.0
 */

/**
 * Prevent switching to the theme on old versions of WordPress.
 *
 * Switches to the default theme.
 *
 * @since 1.0.0
 * @return void
 */
function blockstrap_switch_theme() {
	switch_theme( WP_DEFAULT_THEME );
	unset( $_GET['activated'] );
	add_action( 'admin_notices', 'blockstrap_upgrade_notice' );
}
add_action( 'after_switch_theme', 'blockstrap_switch_theme' );

/**
 * Adds a message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to the theme.
 *
 * @since 1.0.0
 *
 * @global string $wp_version WordPress version.
 * @return void
 */
function blockstrap_upgrade_notice() {
	/* translators: %1$s: WordPress version. %2$s PHP version.*/
	$message = sprintf( esc_html__( 'This theme requires at least WordPress version 5.5 and PHP version 7.2. You are running WordPress version %1$s and PHP version %2$s. Please upgrade and try again.', 'blockstrap' ), $GLOBALS['wp_version'], PHP_VERSION );
	printf( '<div class="error"><p>%s</p></div>', esc_attr( $message ) ); // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * Prevents the Customizer from being loaded on WordPress versions prior to 5.5.
 *
 * @since 1.0.0
 *
 * @global string $wp_version WordPress version.
 * @return void
 */
function blockstrap_customize() {
	wp_die(
		sprintf(
			/* translators: %1$s: WordPress version. %2$s PHP version.*/
			esc_html__( 'This theme requires at least WordPress version 5.5 and PHP version 7.2. You are running WordPress version %1$s and PHP version %2$s. Please upgrade and try again.', 'blockstrap' ),
			esc_html( $GLOBALS['wp_version'] ),
			esc_html( PHP_VERSION )
		),
		'',
		array(
			'back_link' => true,
		)
	);
}
add_action( 'load-customize.php', 'blockstrap_customize' );

/**
 * Prevents the Theme Preview from being loaded on WordPress versions prior to 5.5.
 *
 * @since 1.0.0
 *
 * @global string $wp_version WordPress version.
 * @return void
 */
function blockstrap_preview() {
	if ( isset( $_GET['preview'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_die(
			sprintf(
				/* translators: %1$s: WordPress version. %2$s PHP version.*/
				esc_html__( 'This theme requires at least WordPress version 5.5 and PHP version 7.2. You are running WordPress version %1$s and PHP version %2$s. Please upgrade and try again.', 'blockstrap' ),
				esc_html( $GLOBALS['wp_version'] ),
				esc_html( PHP_VERSION )
			)
		);
	}
}
add_action( 'template_redirect', 'blockstrap_preview' );


