<?php

add_action( 'admin_notices', 'directory_theme_downgrade_notice' );
/**
 * Maybe show the downgrade notice.
 *
 * @return void
 */
function directory_theme_downgrade_notice() {

	if ( isset( $_REQUEST['directory_v3_ok'] ) && check_admin_referer( 'directory_nonce' ) ) {
		update_option( 'directory_theme_v3', time() );
	}

	$v3_ok = get_option( 'directory_theme_v3' );

	// if accepted v3 then bail
	if ( $v3_ok ) {
		return;
	}

	$action      = 'install-theme';
	$slug        = 'directory';
	$install_url = wp_nonce_url(
		add_query_arg(
			array(
				'action'              => $action,
				'theme'               => $slug,
				'directory_downgrade' => 1,
			),
			admin_url( 'update.php' )
		),
		$action . '_' . $slug
	);

	$continue_url = wp_nonce_url(
		add_query_arg(
			array(
				'directory_v3_ok' => 1,
			)
		),
		'directory_nonce'
	);

	$learn_more_url = 'https://docs.wpgeodirectory.com/article/729-beta-release-of-the-new-fse-directory-theme';

	?>
	<div class="notice notice-error" style="text-align: center">
		<h1 style="font-size: 40px;font-weight: bold;text-align: center;">
			<?php
			esc_html_e( 'Directory Theme Notice', 'directory' );
			?>
		</h1>
		<h2 style="font-size: 22px;font-weight: bold;text-align: center;color: red;">
			<?php
			esc_html_e( 'Immediate attention required', 'directory' );
			?>
		</h2>
		<p>
			<strong>
			<?php
				/* translators: %1$s: Opening link tag %2$s PHP Closing link tag. */
				echo sprintf( __( 'Version 3 of Directory theme has changed to be a block theme, this will require manual work to recreate your current layout. %1$sLearn more.%2$s', 'directory' ), "<a href='" . esc_url_raw( $learn_more_url ) . "' target='_blank'>", '</a>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
				</strong>
		</p>
		<p><?php esc_html_e( 'Not ready? no problem', 'directory' ); ?><br><strong><a
						onclick="return confirm('<?php esc_html_e( 'This will downgrade directory to the latest version 2', 'directory' ); ?>');"
						class="button button-primary" href="<?php echo esc_url_raw( $install_url ); ?>" target="_parent"><i
							class="fas fa-undo-alt"></i> <?php esc_html_e( 'Downgrade to latest v2.8', 'directory' ); ?>
				</a></strong></p>
		<p><strong><?php esc_html_e( 'OR', 'directory' ); ?></strong></p>
		<p>
			<strong><a
						class="button button-primary" href="<?php echo esc_url_raw( $continue_url ); ?>" target="_parent"><?php esc_html_e( 'Continue with v3 block theme', 'directory' ); ?>
				</a></strong>

		</p>
		<div style="margin-bottom: 10px;"><?php esc_html_e( '( If this is a new install you can proceed and ignore this notice )', 'directory' ); ?></div>

	</div>
	<?php
}



/**
 * Maybe filter the package request for the theme and change it to v2.
 *
 * @param $options
 *
 * @return mixed
 */
function directory_theme_maybe_downgrade_v2( $options ) {

	if (
		! empty( $_REQUEST['directory_downgrade'] )
		&& ! empty( $options['package'] )
		&& strpos( $options['package'], 'https://downloads.wordpress.org/theme/directory.' ) === 0
		&& check_admin_referer( 'install-theme_directory' )
	) {
		$options['package']                     = 'https://downloads.wordpress.org/theme/directory.2.8.zip';
		$options['abort_if_destination_exists'] = false;
	}

	return $options;
}
add_filter( 'upgrader_package_options', 'directory_theme_maybe_downgrade_v2' );


/**
 * Old version has no child theme so we must set the template to match the main theme.
 *
 * @param $upgrader_object
 * @param $options
 *
 * @return void
 */
function directory_theme_downgrade_completed( $upgrader_object, $options ) {
	if ( 'theme' === $options['type'] ) {
		// Get the current theme version
		$current_theme   = wp_get_theme();
		$current_version = $current_theme->get( 'Version' );

		if (
			check_admin_referer( 'install-theme_directory' )
			&& ! empty( $_REQUEST['theme'] )
			&& 'directory' === $_REQUEST['theme']
			&& ! empty( $_REQUEST['directory_downgrade'] )
		) {
			update_option( 'template', 'directory' );
		}
	}
}
add_filter( 'upgrader_post_install', 'directory_theme_downgrade_completed', 10, 2 );
