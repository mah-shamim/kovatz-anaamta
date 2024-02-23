<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Relations;

use Jet_Engine\Modules\Custom_Content_Types\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Edit class
 */
class Edit {

	public function __construct() {
		add_action( 'jet-engine/relations/edit/custom-controls', array( $this, 'register_editor_controls' ) );
		add_action( 'jet-engine/relations/edit/before-enqueue-assets', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register custom CCT-related controls for the relations editor
	 * @return [type] [description]
	 */
	public function register_editor_controls() {
		?>
		<jet-cct-relation
			:relation-args="args"
			:object-types="objectTypes"
			:value="args.cct"
			@input="( newValue ) => { setArg( newValue, 'cct' ) }"
		/>
		<?php

		add_action( 'admin_footer', array( $this, 'print_templates' ), 99 );

	}

	/**
	 * Enqueue assets
	 *
	 * @return [type] [description]
	 */
	public function enqueue_assets() {

		wp_enqueue_script(
			'jet-engine-cct-edit-relation',
			Module::instance()->module_url( 'assets/js/admin/edit-relation.js' ),
			array( 'cx-vue-ui', 'wp-api-fetch', ),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script( 'jet-engine-cct-edit-relation', 'JetCCTRelationConfig', array(
			'types' => $this->get_types_for_relations()
		) );

	}

	/**
	 * Returns types for relations.
	 *
	 * @return [type] [description]
	 */
	public function get_types_for_relations() {

		$types = array();

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {

			$types[ jet_engine()->relations->types_helper->type_name_by_parts( 'cct', $type ) ] = array(
				'label'   => $instance->get_arg( 'name' ),
				'options' => $instance->get_fields_list( 'custom', 'blocks' ),
			);

		}

		return $types;

	}

	/**
	 * Print CCT templates
	 *
	 * @return [type] [description]
	 */
	public function print_templates() {

		ob_start();
		include Module::instance()->module_path( 'templates/admin/edit-relation.php' );
		$content = ob_get_clean();
		printf( '<script type="text/x-template" id="jet-cct-relation">%s</script>', $content );

	}

}
