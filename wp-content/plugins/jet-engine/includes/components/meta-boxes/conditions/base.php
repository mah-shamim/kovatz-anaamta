<?php
namespace Jet_Engine\Components\Meta_Boxes\Conditions;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Base class
 */
abstract class Base {

	public function __construct() {
		add_action( 'jet-engine/meta-boxes/condition-controls', array( $this, 'render_control' ) );
	}

	/**
	 * Returns conditions name to show in options
	 *
	 * @return [type] [description]
	 */
	abstract public function get_name();

	/**
	 * Returns appropriate setting key for this condition
	 *
	 * @return [type] [description]
	 */
	abstract public function get_key();

	/**
	 * Returns appropriate UI control for current condition
	 *
	 * @return string
	 */
	abstract public function get_control();

	/**
	 * Check condition
	 *
	 * @return [type] [description]
	 */
	abstract public function check_condition( $args = array() );

	/**
	 * Remove condition button
	 *
	 * @return [type] [description]
	 */
	public function remove_button() {
		?>
		<cx-vui-button
			button-style="link-error"
			size="mini"
			@click="removeCondition( '<?php echo $this->get_key(); ?>' )"
		>
			<span slot="label">&times; <?php _e( 'Remove', 'jet-engine' ); ?></span>
		</cx-vui-button>
		<?php
	}

	/**
	 * Returns array of allowed sources
	 *
	 * @return [type] [description]
	 */
	public function allowed_sources() {

		$all_sources = array();

		foreach ( jet_engine()->meta_boxes->get_sources() as $data ) {
			$all_sources[] = $data['value'];
		}

		return $all_sources;
	}

	/**
	 * Renders appropriate UI control for current condition
	 *
	 * @return string
	 */
	public function render_control() {

		$control = $this->get_control();

		if ( $control ) {
			echo $control;
		}

	}

	/**
	 * Return arguments list prepared for AJAX by given meta box arguments array
	 *
	 * @return array
	 */
	public function get_ajax_data_from_args( $args = array() ) {
		return array();
	}

	/**
	 * Determine is codition checked on AJAX request or not
	 *
	 * @return boolean [description]
	 */
	public function is_ajax() {
		return false;
	}

	/**
	 * Returns JS handler to pas data into AJAX request
	 *
	 * @return [type] [description]
	 */
	public function get_js_handler() {
		return false;
	}

}
