<?php
/**
 * Admin Settings Template
 *
 * @package Temporary Login Without Password
 */

?>
<h2 class="nav-tab-wrapper">
    <?php if(! $is_temporary_login)  { ?>
        <a href="<?php echo esc_url( admin_url( 'users.php?page=wp-temporary-login-without-password&tab=home' ) ); ?>" class="nav-tab <?php echo 'home' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__( 'Temporary Logins', 'temporary-login-without-password' ); ?></a>
        <a href="<?php echo esc_url( admin_url( 'users.php?page=wp-temporary-login-without-password&tab=settings' ) ); ?>" class="nav-tab <?php echo 'settings' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__( 'Settings', 'temporary-login-without-password' ); ?></a>
	<?php } ?>
    <a href="<?php echo esc_url( admin_url( 'users.php?page=wp-temporary-login-without-password&tab=system-info' ) ); ?>" class="nav-tab <?php echo 'system-info' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__( 'System Info', 'temporary-login-without-password' ); ?></a>
    <a href="<?php echo esc_url( admin_url( 'users.php?page=wp-temporary-login-without-password&tab=other-plugins' ) ); ?>" class="nav-tab <?php echo 'other-plugins' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__( 'Other Awesome Plugins', 'temporary-login-without-password' ); ?></a>
</h2>

<?php if ( 'home' === $active_tab && !$is_temporary_login ) { ?>
    <div class="wrap wtlwp wtlwp-settings-wrap" id="temporary-logins">
        <h2 class="font-semibold text-gray-700">
			<?php echo esc_html__( 'Temporary Logins', 'temporary-login-without-password' ); ?> 
            <span class="cursor-pointer ml-3 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white transition duration-150 ease-in-out px-3 py-1 hover:bg-gray-50 hover:text-gray-700 focus:ring-2 focus:ring-blue-200" id="add-new-wtlwp-form-button"><?php esc_html_e( 'Create New', 'temporary-login-without-password' ); ?></span>
        </h2>
        <div class="wtlwp wtlwp-settings">
            <!-- Add New Form Start -->

            <div class="wrap new-wtlwp-form" id="new-wtlwp-form">
				<?php include WTLWP_PLUGIN_DIR . '/templates/new-login.php'; ?>
            </div>

			<?php if ( $do_update ) { ?>

                <div class="wrap update-wtlwp-form" id="update-wtlwp-form">
					<?php include WTLWP_PLUGIN_DIR . '/templates/update-login.php'; ?>
                </div>

			<?php } ?>

			<?php $wtlwp_generated_url = esc_url( $wtlwp_generated_url );
			if ( ! empty( $wtlwp_generated_url ) ) { ?>

                <div class="wrap rounded-md bg-white shadow-md my-4 py-4 pl-4 pr-3 border-indigo-600 border-2" id="generated-wtlwp-login-link">
                    <p class="py-1.5 text-gray-500 font-medium tracking-wide text-sm">
						<?php esc_attr_e( "Here's a temporary login link", 'temporary-login-without-password' ); ?>
                    </p>
                    <input id="wtlwp-click-to-copy-btn" type="text" class="wtlwp-wide-input form-input text-sm" value="<?php echo esc_url( $wtlwp_generated_url ); ?>">
                    <button class="wtlwp-copy-to-clipboard p-2 border-transparent text-indigo-600 rounded-full hover:text-gray-600 focus:outline-none focus:text-gray-600 focus:bg-gray-100 transition duration-150 ease-in-out ml-1 hover:rounded-full hover:bg-gray-100" data-clipboard-action="copy" data-clipboard-target="#wtlwp-click-to-copy-btn">
                        <svg data-clipboard-action="copy" data-clipboard-target="#wtlwp-click-to-copy-btn" class="w-6 h-6 inline-block -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title><?php echo esc_html__( 'Copy', 'temporary-login-without-password' ); ?></title><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </button>
                    
                    
                    <span id="copied-text-message-wtlwp-click-to-copy-btn"></span>
                    <p class="py-1.5 tracking-wide text-gray-600">
						<?php
						esc_attr_e( 'User can directly login to WordPress admin panel without username and password by opening this link.', 'temporary-login-without-password' );
						if ( ! empty( $user_email ) ) {
							/* translators: %s: mailto link */
							echo " " . sprintf( __( '<a href="%s">Email</a> temporary login link to user', 'temporary-login-without-password' ), $mailto_link ); //phpcs:ignore
						}
						?>
                    </p>

                </div>
			<?php } ?>
            <!-- Add New Form End -->

            <!-- List All Generated Logins Start -->
            <div class="wrap list-wtlwp-logins mt-4" id="list-wtlwp-logins">
				<?php load_template( WTLWP_PLUGIN_DIR . '/templates/list-temporary-logins.php' ); ?>
            </div>
            <!-- List All Generated Logins End -->
        </div>
    </div>
<?php } elseif ( 'settings' === $active_tab && ! $is_temporary_login) { ?>
    <div class="wrap wtlwp list-wtlwp-logins" id="wtlwp-logins-settings">
		<?php include WTLWP_PLUGIN_DIR . '/templates/temporary-logins-settings.php'; ?>
    </div>

<?php } elseif ('other-plugins' === $active_tab ) { ?>
    <div class="wrap wtlwp list-wtlwp-logins" id="wtlwp-logins-other-plugins">
		<?php include WTLWP_PLUGIN_DIR . '/templates/other-plugins.php'; ?>
    </div>

<?php } else {  ?>
    <div class="wrap wtlwp tlwp-sytem-info" id="tlwp-system-info">
		<?php include WTLWP_PLUGIN_DIR . '/templates/system-info.php'; ?>
    </div>
<?php }  ?>
