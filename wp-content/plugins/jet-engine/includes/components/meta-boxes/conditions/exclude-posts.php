<?php
namespace Jet_Engine\Components\Meta_Boxes\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Exclude_Posts class
 */
class Exclude_Posts extends Include_Posts {

	/**
	 * Returns conditions name to show in options
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Exclude Posts', 'jet-engine' );
	}

	/**
	 * Returns appropriate setting key for this condition
	 *
	 * @return [type] [description]
	 */
	public function get_key() {
		return 'excluded_posts';
	}

	/**
	 * Expression to check current condition
	 *
	 * @return [type] [description]
	 */
	public function check( $post_id, $posts ) {
		return ( empty( $post_id ) || ! in_array( $post_id, $posts ) );
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
			label="<?php _e( 'Exclude Posts', 'jet-engine' ); ?>"
			description="<?php _e( 'Select specific post to exclude meta box from', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'meta-condition' ]"
			:remote="true"
			:remote-callback="getPosts"
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
