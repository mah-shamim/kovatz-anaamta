<?php
namespace Jet_Engine\Glossaries;

/**
 * Meta fields compatibility class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Forms {

	public function __construct() {
		add_filter( 'jet-engine/forms/editor/field-options-sources', array( $this, 'register_source' ) );
		add_action( 'jet-engine/forms/editor/field-options-controls', array( $this, 'register_controls' ) );
		add_filter( 'jet-engine/forms/field-options', array( $this, 'apply_glossary_options' ), 10, 2 );
	}

	public function apply_glossary_options( $options, $args ) {

		if ( ! empty( $args['field_options_from'] ) && 'glossary' === $args['field_options_from'] && ! empty( $args['glossary_id'] ) ) {

			$glossary = jet_engine()->glossaries->data->get_item_for_edit( absint( $args['glossary_id'] ) );

			if ( ! empty( $glossary ) && ! empty( $glossary['fields'] ) ) {
				$options = $glossary['fields'];
			}
		}

		return $options;
	}

	public function register_source( $sources = array() ) {
		$sources['glossary'] = __( 'Glossary', 'jet-engine' );
		return $sources;
	}

	public function register_controls() {
		?>
		<div class="jet-form-editor__row"
			v-if="inArray( currentItem.settings.type, [ 'select', 'checkboxes', 'radio' ] ) && 'glossary' === currentItem.settings.field_options_from"
		>
			<div class="jet-form-editor__row-label"><?php _e( 'Select Glossary:', 'jet-engine' ); ?></div>
			<div class="jet-form-editor__row-control">
				<select v-model="currentItem.settings.glossary_id">
					<option value=""><?php _e( 'Select glossary...', 'jet-engine' ); ?></option>
					<?php
						foreach ( jet_engine()->glossaries->settings->get() as $glossary ) {
							printf( '<option value="%1$s">%2$s</option>', $glossary['id'], $glossary['name'] );
						}
					?>
				</select>
			</div>
		</div>
		<?php
	}

}
