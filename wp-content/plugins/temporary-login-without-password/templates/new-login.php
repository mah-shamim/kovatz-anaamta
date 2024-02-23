<?php
/**
 * Create New Temporary Login template
 *
 * @package Temporary Login Without Password
 */

?>
<p class="text-base tracking-wide font-medium"> <?php echo esc_html__( 'Create a new Temporary Login', 'temporary-login-without-password' ); ?></h2>
<form method="post">
	<table class="form-table bg-white rounded-lg shadow-md">
		<tr class="form-field form-required pt-2">
			<th scope="row" class="wtlwp-form-row pt-2">
				<label for="user_email"><span class="text-sm font-medium text-gray-600 pb-2"><?php echo esc_html__( 'Email*', 'temporary-login-without-password' ); ?> </span></label>
			</th>
			<td class="pt-2">
				<input name="wtlwp_data[user_email]" type="text" id="user_email" value="" aria-required="true" maxlength="60" class="wtlwp-form-input form-input"/>
			</td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" class="wtlwp-form-row">
				<label for="user_first_name"><span class="text-sm font-medium text-gray-600 pb-2"><?php echo esc_html__( 'First Name', 'temporary-login-without-password' ); ?> </span></label>
			</th>
			<td>
				<input name="wtlwp_data[user_first_name]" type="text" id="user_first_name" value="" aria-required="true" maxlength="60" class="wtlwp-form-input form-input"/>
			</td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" class="wtlwp-form-row">
				<label for="user_last_name"><span class="text-sm font-medium text-gray-600 pb-2"><?php echo esc_html__( 'Last Name', 'temporary-login-without-password' ); ?> </span></label>
			</th>
			<td>
				<input name="wtlwp_data[user_last_name]" type="text" id="user_last_name" value="" aria-required="true" maxlength="60" class="wtlwp-form-input form-input"/>
			</td>
		</tr>

		<?php if ( is_network_admin() ) { ?>
			<tr class="form-field form-required">
				<th scope="row" class="wtlwp-form-row">
					<label for="user_super_admin"><span class="text-sm font-medium text-gray-600 pb-2"><?php echo esc_html__( 'Super Admin', 'temporary-login-without-password' ); ?> </span></label>
				</th>
				<td>
					<input type="checkbox" id="user_super_admin" name="wtlwp_data[super_admin]" class="form-checkbox"><?php echo esc_html__( 'Grant this user super admin privileges for the Network.', 'temporary-login-without-password' ); ?>
				</td>
			</tr>
		<?php } else { ?>
			<tr class="form-field">
				<th scope="row" class="wtlwp-form-row">
					<label for="adduser-role"><span class="text-sm font-medium text-gray-600 pb-2"><?php echo esc_html__( 'Role', 'temporary-login-without-password' ); ?></span></label>
				</th>
				<td>
					<select name="wtlwp_data[role]" id="user-role" class="form-select font-normal text-gray-600 h-8 shadow-sm">
						<?php Wp_Temporary_Login_Without_Password_Common::tlwp_dropdown_roles( $visible_roles, $default_role ); ?>
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
					<?php Wp_Temporary_Login_Without_Password_Common::tlwp_dropdown_redirect_to( $default_redirect_to ); ?>
                </select>
            </td>
        </tr>

		<tr class="form-field">
			<th scope="row" class="wtlwp-form-row">
				<label for="adduser-role"><span class="text-sm font-medium text-gray-600 pb-2"><?php echo esc_html__( 'Expiry', 'temporary-login-without-password' ); ?></span></label>
			</th>
			<td>
				<span id="expiry-date-selection">
						<select name="wtlwp_data[expiry]" id="new-user-expiry-time" class="form-select font-normal text-gray-600 h-8 shadow-sm">
							<?php Wp_Temporary_Login_Without_Password_Common::get_expiry_duration_html( $default_expiry_time ); ?>
						</select>
				</span>

				<span style="display:none;" id="new-custom-date-picker">
					<input type="date" id="datepicker" name="wtlwp_data[custom_date]" value="" class="new-custom-date-picker"/>
				</span>

			</td>
		</tr>

        <tr class="form-field">
            <th scope="row" class="wtlwp-form-row">
                <label for="language"><span class="text-sm font-medium text-gray-600 pb-2"><?php echo esc_html__( 'Language', 'temporary-login-without-password' ); ?></span></label>
            </th>
            <td scope="row" class="wtlwp-language-dropdown">
                <?php
                    wp_dropdown_languages(array('name' => 'wtlwp_data[locale]', 'selected' => get_locale()));
                ?>
            </td>
        </tr>


		<tr class="form-field">
			<th scope="row" class="wtlwp-form-row"><span class="text-sm font-medium text-gray-600 pb-2"><label for="adduser-role"></label></span></th>
			<td>
				<p class="submit">
					<input type="submit" class="pr-1 wtlwp-form-submit-button bg-indigo-600 p-2 rounded text-white cursor-pointer hover:bg-indigo-600" value="<?php esc_html_e( 'Submit', 'temporary-login-without-password' ); ?>" class="button button-primary" id="generatetemporarylogin" name="generate_temporary_login"> <?php esc_html_e( 'or', 'temporary-login-without-password' ); ?>
					<span class="cancel-new-login-form" id="cancel-new-login-form"><?php esc_html_e( 'Cancel', 'temporary-login-without-password' ); ?></span>
				</p>
			</td>
		</tr>
		<?php wp_nonce_field( 'wtlwp_generate_login_url', 'wtlwp-nonce', true, true ); ?>
	</table>
</form>
