<?php
/**
 * Update Login template
 *
 * @package Temporary Login Without Password
 */

?>
<h2> <?php echo esc_html__( 'Update Temporary Login', 'temporary-login-without-password' ); ?></h2>
<form method="post">
    <table class="form-table bg-white rounded-lg shadow-md">
        <tr class="form-field form-required">
            <th scope="row" class="wtlwp-form-row">
                <label for="user_email"><?php echo esc_html__( 'Email', 'temporary-login-without-password' ); ?> </label>
            </th>
            <td>
                <label for="user_email"><?php echo esc_attr( $temporary_user_data['email'] ); ?></label>
            </td>
        </tr>

        <tr class="form-field form-required">
            <th scope="row" class="wtlwp-form-row">
                <label for="user_first_name"><?php echo esc_html__( 'First Name', 'temporary-login-without-password' ); ?> </label>
            </th>
            <td>
                <input name="wtlwp_data[user_first_name]" type="text" id="user_first_name" value="<?php echo esc_attr( $temporary_user_data['first_name'] ); ?>" aria-required="true" maxlength="60" class="wtlwp-form-input form-input"/>
            </td>
        </tr>

        <tr class="form-field form-required">
            <th scope="row" class="wtlwp-form-row">
                <label for="user_last_name"><?php echo esc_html__( 'Last Name', 'temporary-login-without-password' ); ?> </label>
            </th>
            <td>
                <input name="wtlwp_data[user_last_name]" type="text" id="user_last_name" value="<?php echo esc_attr( $temporary_user_data['last_name'] ); ?>" aria-required="true" maxlength="60" class="wtlwp-form-input form-input"/>
            </td>
        </tr>

		<?php if ( is_network_admin() ) { ?>
            <tr class="form-field form-required">
                <th scope="row" class="wtlwp-form-row">
                    <label for="user_super_admin"><?php echo esc_html__( 'Super Admin', 'temporary-login-without-password' ); ?> </label>
                </th>
                <td>
                    <input type="checkbox" id="user_super_admin" name="wtlwp_data[super_admin]" class="form-checkbox">
					<?php echo esc_html__( 'Grant this user super admin privileges for the Network.', 'temporary-login-without-password' ); ?>
                </td>
            </tr>
		<?php } else { ?>
            <tr class="form-field">
                <th scope="row" class="wtlwp-form-row">
                    <label for="adduser-role"><?php echo esc_html__( 'Role', 'temporary-login-without-password' ); ?></label>
                </th>
                <td>
                    <select name="wtlwp_data[role]" id="user-role" class="form-select font-normal text-gray-600 h-8 shadow-sm">
						<?php
						$role = $temporary_user_data['role'];
						Wp_Temporary_Login_Without_Password_Common::tlwp_dropdown_roles( $visible_roles, $role );
						?>
                    </select>
                </td>
            </tr>
		<?php } ?>

        <tr class="form-field">
            <th scope="row" class="wtlwp-form-row">
                <label for="redirect-to"><span class="text-sm font-medium text-gray-600 pb-2"><?php echo esc_html__( 'Redirect After Login', 'temporary-login-without-password' ); ?></span></label>
            </th>
            <td>
                <select name="wtlwp_data[redirect_to]" id="redirect-to" class="form-select font-normal text-gray-600 h-8 shadow-sm">
					<?php Wp_Temporary_Login_Without_Password_Common::tlwp_dropdown_redirect_to( $temporary_user_data['redirect_to'] ); ?>
                </select>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" class="wtlwp-form-row">
                <label for="adduser-role"><?php echo esc_html__( 'Extend Expiry', 'temporary-login-without-password' ); ?></label>
            </th>
            <td>
				<span id="expiry-date-selection">
						<select name="wtlwp_data[expiry]" id="update-user-expiry-time" class="form-select font-normal text-gray-600 h-8 shadow-sm">
							<?php Wp_Temporary_Login_Without_Password_Common::get_expiry_duration_html( 'week' ); ?>
						</select>
				</span>

                <span style="display:none;" id="update-custom-date-picker">
					<input type="date" id="tlwp-datepicker" name="wtlwp_data[custom_date]" value="" class="update-custom-date-picker"/>
				</span>

            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" class="wtlwp-form-row">
                <label for="language"><?php echo esc_html__( 'Language', 'temporary-login-without-password' ); ?></label>
            </th>
            <td scope="row" class="wtlwp-language-dropdown">
				<?php
				$locale = $temporary_user_data['locale'];
				wp_dropdown_languages( array( 'name' => 'wtlwp_data[locale]', 'selected' => $locale ) );
				?>
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" class="wtlwp-form-row"><label for="adduser-role"></label></th>
            <td>
                <p class="submit">
                    <input type="submit" class="wtlwp-form-submit-button bg-indigo-600 p-2 rounded text-white cursor-pointer hover:bg-indigo-600" value="<?php esc_html_e( 'Submit', 'temporary-login-without-password' ); ?>" class="button button-primary" id="generatetemporarylogin" name="generate_temporary_login"> <?php esc_html_e( 'or', 'temporary-login-without-password' ); ?>
                    <span class="cancel-update-login-form" id="cancel-update-login-form"><?php esc_html_e( 'Cancel', 'temporary-login-without-password' ); ?></span>
                </p>
            </td>
        </tr>
        <input type="hidden" name="wtlwp_action" value="update"/>
        <input type="hidden" name="wtlwp_data[user_id]" value="<?php echo esc_attr( $user_id ); ?>"/>
		<?php wp_nonce_field( 'manage-temporary-login_' . $user_id, 'manage-temporary-login', true, true ); ?>
    </table>
</form>
