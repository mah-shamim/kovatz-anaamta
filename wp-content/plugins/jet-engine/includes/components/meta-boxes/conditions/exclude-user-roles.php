<?php
namespace Jet_Engine\Components\Meta_Boxes\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Base class
 */
class Exclude_User_Roles extends Include_User_Roles {

	/**
	 * Returns conditions name to show in options
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Excluded for User Roles', 'jet-engine' );
	}

	/**
	 * Returns appropriate setting key for this condition
	 *
	 * @return [type] [description]
	 */
	public function get_key() {
		return 'exclude_roles';
	}

	/**
	 * Expression to check current condition
	 *
	 * @return [type] [description]
	 */
	public function check( $roles, $roles_to_check ) {
		$intersect = array_intersect( $roles, $roles_to_check );
		return empty( $intersect );
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
			label="<?php _e( 'Exclude for User Roles', 'jet-engine' ); ?>"
			description="<?php _e( 'Select specific user roles to hide meta box only for these roles', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'meta-condition' ]"
			:options-list="userRoles"
			size="fullwidth"
			:multiple="true"
			:style="conditionControlsInlineCSS( '<?php echo $this->get_key(); ?>' )"
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
