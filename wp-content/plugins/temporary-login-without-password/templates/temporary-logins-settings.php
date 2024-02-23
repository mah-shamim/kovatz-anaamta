<?php
/**
 * Temporary Login settings template
 *
 * @package Temporary Login Without Password
 */

?>
<h2 class="font-semibold text-gray-700"> <?php echo esc_html__( 'Temporary Login Settings', 'temporary-login-without-password' ); ?></h2>


<div class="bg-white rounded-lg shadow-md meta-box-sortables ui-sortable">
    <form class="flex-row pt-8 mt-2 ml-5 mr-4 text-left item-center" method="post" action="">

        <!-- Visible Roles -->
        <div class="flex flex-row border-b border-gray-100">
            <div class="flex w-1/5">
                <div class="pt-6">
                    <label for="visible_roles"><span class="block pt-1 pb-2 pr-4 text-sm font-medium text-gray-600"><?php echo esc_html__( 'Visible Roles', 'temporary-login-without-password' ); ?></span></label>
                    <p class="italic text-xs text-gray-400 mt-1 font-normal leading-snug"><?php echo esc_html__( 'Select roles from which you want to create a temporary login', 'temporary-login-without-password' ); ?></p>
                </div>
            </div>
            <div class="flex w-4/5">
                <div class="w-full h-30 mt-4 mb-4 ml-16 mr-4">
                    <div class="relative h-30">
                        <select multiple name="tlwp_settings_data[visible_roles][]" id="visible-roles" class="visible-roles-dropdown form-multiselect font-normal text-gray-600 text-sm h-8 shadow-sm">
							<?php Wp_Temporary_Login_Without_Password_Common::tlwp_multi_select_dropdown_roles( $visible_roles ); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Default Role -->
        <div class="flex flex-row border-b border-gray-100">
            <div class="flex w-1/5">
                <div class="pt-6">
                    <label for="adduser-role" class="text-sm font-medium text-gray-600 pb-2"><?php echo esc_html__( 'Default Role', 'temporary-login-without-password' ); ?></label>
                </div>
            </div>
            <div class="flex w-4/5">
                <div class="w-full h-10 mt-4 mb-4 ml-16 mr-4">
                    <div class="relative h-10">
                        <select name="tlwp_settings_data[default_role]" id="default-role" class="default-role-dropdown form-select font-normal text-gray-600 h-8 shadow-sm">
							<?php wp_dropdown_roles( $default_role ); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Default Redirect After Login -->
        <div class="flex flex-row border-b border-gray-100">
            <div class="flex w-1/5">
                <div class="pt-6">
                    <label for="redirect-to"><span class="text-sm font-medium text-gray-600 pb-2"><?php echo esc_html__( 'Default Redirect After Login', 'temporary-login-without-password' ); ?></span></label>
                </div>
            </div>
            <div class="flex w-4/5">
                <div class="w-full h-10 mt-4 mb-4 ml-16 mr-4">
                    <div class="relative h-10">
                        <select name="tlwp_settings_data[default_redirect_to]" id="redirect-to" class="form-select font-normal text-gray-600 h-8 shadow-sm">
							<?php Wp_Temporary_Login_Without_Password_Common::tlwp_dropdown_redirect_to( $default_redirect_to ); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Default Expiry Time -->
        <div class="flex flex-row border-b border-gray-100">
            <div class="flex w-1/5">
                <div class="pt-6">
                    <label for="adduser-role" class="text-sm font-medium text-gray-600 pb-2"><?php echo esc_html__( 'Default Expiry Time', 'temporary-login-without-password' ); ?></label>
                </div>
            </div>
            <div class="flex w-4/5">
                <div class="w-full h-10 mt-4 mb-4 ml-16 mr-4">
                    <div class="relative h-10">
                        <select name="tlwp_settings_data[default_expiry_time]" id="default-expiry-time" class="form-select font-normal text-gray-600 h-8 shadow-sm">
							<?php Wp_Temporary_Login_Without_Password_Common::get_expiry_duration_html( $default_expiry_time, array( 'custom_date' ) ); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete plugin data on uninstall -->
        <div class="flex flex-row border-gray-100">
            <div class="flex w-1/5">
                <div class="pt-6">
                    <label for="delete-plugin-data" class="text-sm font-medium text-gray-600 pb-2"><?php echo esc_html__( 'Delete plugin data on uninstall', 'temporary-login-without-password' ); ?></label>
                </div>
            </div>
            <div class="flex w-4/5">
                <div class="w-full h-10 mt-4 mb-4 ml-16 mr-4">
                    <div class="relative h-10 mt-2">
                        <input type="checkbox" name="tlwp_settings_data[delete_data_on_uninstall]" value="1" class="form-checkbox mt-4" <?php if ( 1 == $delete_data_on_uninstall ) {
							echo "checked=checked";
						} ?>/>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <p class="submit">
            <input type="submit" class="wtlwp-form-submit-button bg-indigo-600 p-2 rounded text-white cursor-pointer hover:bg-indigo-600" value="<?php esc_html_e( 'Submit', 'temporary-login-without-password' ); ?>" id="generatetemporarylogin" name="generate_temporary_login">
        </p>

		<?php wp_nonce_field( 'wtlwp_login_settings', 'wtlwp-settings-nonce', true, true ); ?>

    </form>
</div>
