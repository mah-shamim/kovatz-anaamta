<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms;


use Jet_Engine\Modules\Custom_Content_Types\Module;

class Action_Manager {

	public function __construct() {
		if ( $this->can_init() ) {
			add_action(
				'jet-form-builder/actions/register',
				array( $this, 'register_actions' )
			);

			add_action(
				'jet-form-builder/editor-assets/before',
				array( $this, 'editor_assets' )
			);
		}
	}

	public function register_actions( $manager ) {
		require_once Module::instance()->module_path( 'forms/action.php' );

		$manager->register_action_type( new Action() );
	}

	public function editor_assets() {
		wp_enqueue_script(
			Module::instance()->slug . '-jet-form-action',
			Module::instance()->module_url( 'assets/js/admin/blocks/jet-forms.js' ),
			array(),
			jet_engine()->get_version(),
			true
		);

	}

	public function can_init() {
		return function_exists( 'jet_form_builder' )
		       && version_compare( jet_form_builder()->get_version(), '1.2.3', '>=' );
	}

}