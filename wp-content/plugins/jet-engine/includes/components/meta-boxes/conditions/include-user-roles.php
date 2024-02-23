<?php
namespace Jet_Engine\Components\Meta_Boxes\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Base class
 */
class Include_User_Roles extends Base {

	/**
	 * Returns conditions name to show in options
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Included for User Roles', 'jet-engine' );
	}

	/**
	 * Returns appropriate setting key for this condition
	 *
	 * @return [type] [description]
	 */
	public function get_key() {
		return 'include_roles';
	}

	/**
	 * Expression to check current condition
	 *
	 * @return [type] [description]
	 */
	public function check( $roles, $roles_to_check ) {
		$intersect = array_intersect( $roles, $roles_to_check );
		return ! empty( $intersect );
	}

	/**
	 * Check condition
	 *
	 * @return [type] [description]
	 */
	public function check_condition( $args = array() ) {
		
		$settings = ! empty( $args['settings'] ) ? $args['settings'] : array();
		$user = wp_get_current_user();
		$roles = (array) $user->roles;
		$roles_to_check = ! empty( $settings[ $this->get_key() ] ) ? $settings[ $this->get_key() ] : array();

		// Added in v3.0.4
		// See: https://github.com/Crocoblock/issues-tracker/issues/1072
		global $pagenow;

		if ( 'user-edit.php' === $pagenow && ! empty( $_REQUEST['user_id'] ) ) {
			$user_page_data = get_userdata( $_REQUEST['user_id'] );
			$roles = (array) $user_page_data->roles;
		}

		if ( $this->check( $roles, $roles_to_check ) ) {
			return true;
		} else {
			return false;
		}
		
	}

	/**
	 * Renders appropriate UI control for current condition
	 *
	 * @return array
	 */
	public function get_control() {
		ob_start();
		?>
		<cx-vui-f-select
			label="<?php _e( 'Included for User Roles', 'jet-engine' ); ?>"
			description="<?php _e( 'Select specific user roles to show meta box only for these roles', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'meta-condition' ]"
			:options-list="userRoles"
			size="fullwidth"
			:style="conditionControlsInlineCSS( '<?php echo $this->get_key(); ?>' )"
			:multiple="true"
			:conditions="[
				{
					input: this.generalSettings.object_type,
					compare: 'in',
					value: <?php echo htmlentities( json_encode( $this->allowed_sources() ) ) ?>,
				},
				{
					input: '<?php echo $this->get_key() ?>',
					compare: 'in',
					value: this.generalSettings.active_conditions,
				}
			]"
			v-model="generalSettings.<?php echo $this->get_key() ?>"
			ref="<?php echo $this->get_key() ?>"
		><?php echo $this->remove_button(); ?></cx-vui-f-select>
		<?php

		return ob_get_clean();

	}

}
