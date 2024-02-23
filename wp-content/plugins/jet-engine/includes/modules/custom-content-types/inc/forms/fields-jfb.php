<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms;



use Jet_Engine\Modules\Custom_Content_Types\Module;

class Fields_Jfb {

	public function __construct() {
		$this->register();
	}

	public function register() {
		if ( ! $this->can_init() ) {
			return;
		}

		add_filter( 'jet-form-builder/blocks/items', array( $this, 'replace_map_field' ) );
	}

	/**
	 * @param array $blocks
	 *
	 * @return array
	 */
	public function replace_map_field( array $blocks ) {
		require_once Module::instance()->module_path( "forms/map-field-jfb.php" );

		$blocks[] = new Map_Field_Jfb();

		return $blocks;
	}

	private function can_init() {
		return (
			function_exists( 'jet_form_builder' ) &&
			version_compare( jet_form_builder()->get_version(), '2.1.2', '>=' )
		);
	}

}