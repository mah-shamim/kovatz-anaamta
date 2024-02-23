<?php
/**
 * List Temporary Logins
 *
 * @package Temporary Login Without Password
 */

?>
<table class="wp-list-table shadow rounded-lg overflow-hidden w-full">
	<thead>
	<?php echo Wp_Temporary_Login_Without_Password_Layout::prepare_header_footer_row(); ?>
	</thead>

	<tbody>
	<?php
	$users = Wp_Temporary_Login_Without_Password_Common::get_temporary_logins();

	if ( is_array( $users ) && count( $users ) > 0 ) {

		foreach ( $users as $user ) {
			echo Wp_Temporary_Login_Without_Password_Layout::prepare_single_user_row( $user );
		}
	} else {
		echo Wp_Temporary_Login_Without_Password_Layout::prepare_empty_user_row();
	}

	?>

	</tbody>

	<tfoot>
	<?php echo Wp_Temporary_Login_Without_Password_Layout::prepare_header_footer_row(); ?>
	</tfoot>
</table>
