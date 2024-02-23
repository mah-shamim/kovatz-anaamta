<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Elementor\Controls;

use Jet_Engine\Modules\Custom_Content_Types\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 *
 * @param string $label         Optional. The label that appears next of the
 *                              field. Default is empty.
 * @param string $title         Optional. The field title that appears on mouse
 *                              hover. Default is empty.
 * @param string $description   Optional. The description that appears below the
 *                              field. Default is empty.
 * @param string|array $default Optional. The selected option key, or an array
 *                              of selected values if `multiple == true`.
 *                              Default is empty.
 * @param array  $options       Optional. An array of `key => value` pairs:
 *                              `[ 'key' => 'value', ... ]`
 *                              Default is empty.
 * @param bool   $multiple      Optional. Whether to allow multiple value
 *                              selection. Default is false.
 * @param string $separator     Optional. Set the position of the control separator.
 *                              Available values are 'default', 'before', 'after'
 *                              and 'none'. 'default' will position the separator
 *                              depending on the control type. 'before' / 'after'
 *                              will position the separator before/after the
 *                              control. 'none' will hide the separator. Default
 *                              is 'default'.
 * @param bool   $show_label    Optional. Whether to display the label. Default
 *                              is true.
 * @param bool   $label_block   Optional. Whether to display the label in a
 *                              separate line. Default is false.
 */
class Query_Dialog_Control extends \Elementor\Base_Data_Control {

	/**
	 * Retrieve select2 control type.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Control type.
	 */
	public function get_type() {
		return 'jet_query_dialog';
	}

	/**
	 * Retrieve select2 control default settings.
	 *
	 * Get the default settings of the select2 control. Used to return the
	 * default settings while initializing the select2 control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */
	protected function get_default_settings() {
		return array(
			'button_label' => __( 'Query Settings', 'jet-engine' ),
		);
	}

	/**
	 * Render select2 control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		$fetch_path  = Module::instance()->query_dialog()->api_path();
		?>
		<div class="elementor-control-field">
			<label for="<?php echo $control_uid; ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<input id="<?php echo $control_uid; ?>" class="elementor-input" type="hidden" data-setting="{{ data.name }}">
				<button data-input="<?php echo $control_uid; ?>" class="elementor-button elementor-button-default jet-query-trigger" type="button" data-fetch-path="<?php echo $fetch_path; ?>">{{{ data.button_label }}}</button>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}
}
