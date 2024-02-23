<?php
namespace Jet_Engine\Components\Meta_Boxes\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Base class
 */
class Include_Posts extends Base {

	/**
	 * Returns conditions name to show in options
	 *
	 * @return [type] [description]
	 */
	public function get_name() {
		return __( 'Include Posts', 'jet-engine' );
	}

	/**
	 * Returns appropriate setting key for this condition
	 *
	 * @return [type] [description]
	 */
	public function get_key() {
		return 'allowed_posts';
	}

	/**
	 * Expression to check current condition
	 *
	 * @return [type] [description]
	 */
	public function check( $post_id, $posts ) {
		return ( $post_id && in_array( $post_id, $posts ) );
	}

	/**
	 * Check condition
	 *
	 * @return [type] [description]
	 */
	public function check_condition( $args = array() ) {

		$post_id = $this->get_post_id();
		$settings = ! empty( $args['settings'] ) ? $args['settings'] : array();

		$posts = ! empty( $settings[ $this->get_key() ] ) ? $settings[ $this->get_key() ] : array();
		$post_id = $this->get_post_id();

		if ( $this->check( $post_id, $posts ) ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Try to get current post ID from request
	 *
	 * @return [type] [description]
	 */
	public function get_post_id() {

		$post_id = isset( $_GET['post'] ) ? $_GET['post'] : false;

		if ( ! $post_id && isset( $_REQUEST['post_ID'] ) ) {
			$post_id = $_REQUEST['post_ID'];
		}

		return $post_id;

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
			label="<?php _e( 'Include Posts', 'jet-engine' ); ?>"
			description="<?php _e( 'Select specific post to show meta box on', 'jet-engine' ); ?>"
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

	/**
	 * Returns array of allowed sources
	 *
	 * @return [type] [description]
	 */
	public function allowed_sources() {
		return array( 'post' );
	}

}
