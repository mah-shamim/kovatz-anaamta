<?php
namespace Jet_Engine\Relations\Forms\Jet_Engine_Forms;

use Jet_Engine\Relations\Forms\Manager as Forms;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Notification class
 */
class Preset {

	public $preset_source = null;

	public function __construct() {

		$this->preset_source = Forms::instance()->slug();

		add_filter( 'jet-engine/forms/preset-sources', array( $this, 'register_source' ) );
		add_filter( 'jet-engine/forms/preset-value/' . $this->preset_source, array( $this, 'apply_preset' ), 10, 4 );
		add_filter( 'jet-engine/forms/preset/sanitize-source', array( $this, 'sanitize_source' ), 10, 2 );

		add_action( 'jet-engine/forms/preset-editor/custom-controls-source', array( $this, 'preset_controls_source' ) );
		add_action( 'jet-engine/forms/preset-editor/custom-controls-global', array( $this, 'preset_controls_global' ) );
		add_action( 'jet-engine/forms/preset-editor/custom-controls-field', array( $this, 'preset_controls_field' ) );
	}

	/**
	 * Sanitize preset source
	 *
	 * @param  [type] $res    [description]
	 * @param  [type] $preset [description]
	 * @return [type]         [description]
	 */
	public function sanitize_source( $res, $preset ) {
		return true;
	}

	/**
	 * Apply form preset
	 *
	 * @param  [type] $value      [description]
	 * @param  [type] $field_data [description]
	 * @param  [type] $args       [description]
	 * @param  [type] $source     [description]
	 * @return [type]             [description]
	 */
	public function apply_preset( $value, $field_data, $args, $source ) {

		$preset = Forms::instance()->get_preset_items( $args );

		if ( ! $preset ) {
			return $value;
		} else {
			return $preset;
		}

	}

	/**
	 * Register preset source
	 *
	 * @param  [type] $sources [description]
	 * @return [type]          [description]
	 */
	public function register_source( $sources ) {

		$sources[] = array(
			'value' => $this->preset_source,
			'label' => __( 'Related Items', 'jet-engine' ),
		);

		return $sources;

	}

	/**
	 * Custom controls for the specific source
	 *
	 * @return [type] [description]
	 */
	public function preset_controls_source() {
		?>
		<div class="jet-form-canvas__preset-row" v-if="'<?php echo $this->preset_source; ?>' === preset.from && ! availableFields">
			<span><?php _e( 'From Relation', 'jet-engine' ); ?>:</span>
			<select v-model="preset.rel_id">
				<option value=""><?php _e( 'Select relation...', 'jet-engine' ); ?></option>
				<?php
					foreach ( jet_engine()->relations->get_relations_for_js() as $relation ) {
						printf( '<option value="%1$s">%2$s</option>', $relation['value'], $relation['label'] );
					}
				?>
			</select>
		</div>
		<div class="jet-form-canvas__preset-row" v-if="'<?php echo $this->preset_source; ?>' === preset.from && ! availableFields">
			<span><?php _e( 'From Object (what to show)', 'jet-engine' ); ?>:</span>
			<select v-model="preset.rel_object">
				<option value=""><?php _e( 'Select relation object...', 'jet-engine' ); ?></option>
				<option value="parent_object"><?php _e( 'Parent object', 'jet-engine' ); ?></option>
				<option value="child_object"><?php _e( 'Child object', 'jet-engine' ); ?></option>
			</select>
		</div>
		<div class="jet-form-canvas__preset-row" v-if="'<?php echo $this->preset_source; ?>' === preset.from && ! availableFields">
			<span><?php _e( 'Initial Object ID From (get initial ID here):', 'jet-engine' ); ?></span>
			<select type="text" v-model="preset.rel_object_from"><?php
				foreach ( jet_engine()->relations->sources->get_sources() as $source_id => $source_label ) {
					printf( '<option value="%1$s">%2$s</option>', $source_id, $source_label );
				}
			?></select>
		</div>
		<div class="jet-form-canvas__preset-row" v-if="'<?php echo $this->preset_source; ?>' === preset.from && ! availableFields && ( 'query_var' === preset.rel_object_from || 'object_var' === preset.rel_object_from )">
			<span><?php _e( 'Variable Name:', 'jet-engine' ); ?></span>
			<input type="text" v-model="preset.rel_object_var">
		</div>
		<?php
	}

	/**
	 * Show notice for global preset controls
	 *
	 * @return [type] [description]
	 */
	public function preset_controls_global() {
		?>
		<div class="jet-post-field-control__inner" v-if="'<?php echo $this->preset_source; ?>' === preset.from"><?php
			_e( 'This source is not available globally. You can set it directly for the required fields.', 'jet-engine' );
		?></div>
		<?php
	}

	/**
	 * Show notice for single field map controls
	 *
	 * @return [type] [description]
	 */
	public function preset_controls_field() {
		?>
		<span v-if="'<?php echo $this->preset_source; ?>' === preset.from"><?php
			_e( 'This control is not used for current preset source.', 'jet-engine' );
		?></span>
		<?php
	}

}
