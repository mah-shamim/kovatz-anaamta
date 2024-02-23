<?php


namespace Jet_Engine\Relations\Forms\Jet_Form_Builder_Forms;

use Jet_Engine\Relations\Forms;

class Actions_Manager {

	public function __construct() {
		if ( class_exists( '\Jet_Form_Builder\Actions\Manager' ) ) {
			require_once jet_engine()->relations->component_path( 'forms/jet-form-builder/action.php' );

			jet_form_builder()->actions->register_action_type( new Action() );
		}

		add_action(
			'jet-form-builder/editor-assets/before',
			array( $this, 'editor_assets' )
		);

	}

	public function editor_assets() {
		wp_enqueue_script(
			Forms\Manager::instance()->slug . '-jet-form-action',
			jet_engine()->plugin_url( 'includes/components/relations/assets/js/jfb-action.js' ),
			array(),
			jet_engine()->get_version(),
			true
		);

	}

}