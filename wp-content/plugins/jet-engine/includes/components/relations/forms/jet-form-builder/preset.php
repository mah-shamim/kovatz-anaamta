<?php


namespace Jet_Engine\Relations\Forms\Jet_Form_Builder_Forms;


use Jet_Engine\Relations\Forms\Manager as Forms;
use Jet_Form_Builder\Classes\Tools;

class Preset {

	public function __construct() {
		add_action( 'jet-form-builder/editor/preset-config', array( $this, 'preset_controls' ) );

		if (
			! class_exists( '\Jet_Form_Builder\Presets\Preset_Manager' )
			|| ! method_exists(
				\Jet_Form_Builder\Presets\Preset_Manager::instance(),
				'register_source_type'
			)
		) {
			return;
		}
		require_once jet_engine()->relations->component_path( 'forms/jet-form-builder/preset-source.php' );

		\Jet_Form_Builder\Presets\Preset_Manager::instance()->register_source_type( new Preset_Source() );
	}

	public function preset_controls( $config ) {
		$source = Forms::instance()->slug();

		$config['global_fields'][0]['options'][] = array(
			'value' => $source,
			'label' => __( 'Related Items', 'jet-engine' ),
		);
		$config['global_fields'][]               = array(
			'name'      => 'rel_id',
			'label'     => __( 'From Relation', 'jet-engine' ),
			'type'      => 'select',
			'options'   => Tools::with_placeholder(
				jet_engine()->relations->get_relations_for_js(),
				__( 'Select relation...', 'jet-engine' )
			),
			'condition' => array(
				'field' => 'from',
				'value' => $source,
			),
		);
		$config['global_fields'][]               = array(
			'name'      => 'rel_object',
			'label'     => __( 'From Object', 'jet-engine' ),
			'type'      => 'select',
			'options'   => array(
				array(
					'value' => '',
					'label' => __( 'Select relation object...', 'jet-engine' )
				),
				array(
					'value' => 'parent_object',
					'label' => __( 'Parent object', 'jet-engine' )
				),
				array(
					'value' => 'child_object',
					'label' => __( 'Child object', 'jet-engine' )
				),
			),
			'condition' => array(
				'field' => 'from',
				'value' => $source,
			),
		);

		$config['global_fields'][]               = array(
			'name'      => 'rel_object_from',
			'label'     => __( 'Initial Object ID From', 'jet-engine' ),
			'type'      => 'select',
			'options'   => Tools::prepare_list_for_js( jet_engine()->relations->sources->get_sources() ),
			'condition' => array(
				'field' => 'from',
				'value' => $source,
			),
		);

		$config['global_fields'][]               = array(
			'name'             => 'rel_object_var',
			'label'            => __( 'Variable name', 'jet-engine' ),
			'type'             => 'text',
			'custom_condition' => 'relation_query_var',
		);

		return $config;
	}


}