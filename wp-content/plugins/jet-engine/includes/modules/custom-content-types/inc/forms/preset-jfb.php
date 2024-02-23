<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms;


use Jet_Engine\Modules\Custom_Content_Types\Factory;
use Jet_Engine\Modules\Custom_Content_Types\Module;
use Jet_Form_Builder\Classes\Tools;

class Preset_Jfb {

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
		require_once Module::instance()->module_path( "forms/preset-source-cct.php" );

		\Jet_Form_Builder\Presets\Preset_Manager::instance()->register_source_type( new Preset_Source_Cct() );
	}

	public function preset_controls( $config ) {
		$source = Module::instance()->form_preset->preset_source;

		$config['global_fields'][0]['options'][] = array(
			'value' => $source,
			'label' => __( 'Custom Content Type', 'jet-engine' ),
		);
		$config['global_fields'][]               = array(
			'name'      => 'post_from',
			'label'     => __( 'Get item ID from:', 'jet-engine' ),
			'type'      => 'select',
			'options'   => Tools::with_placeholder( array(
				array(
					'value' => 'current_post',
					'label' => __( 'Current post', 'jet-engine' ),
				),
				array(
					'value' => 'query_var',
					'label' => __( 'URL Query Variable', 'jet-engine' ),
				),
			) ),
			'condition' => array(
				'field' => 'from',
				'value' => $source,
			),
		);
		$config['global_fields'][]               = array(
			'name'             => 'query_var',
			'label'            => __( 'Query variable name:', 'jet-engine' ),
			'type'             => 'text',
			'custom_condition' => 'cct_query_var',
		);

		$config['map_fields'][] = array(
			'name'             => 'prop',
			'label'            => __( 'CCT Value', 'jet-engine' ),
			'type'             => 'grouped_select',
			'options'          => Tools::with_placeholder( $this->get_cct_props() ),
			'parent_condition' => array(
				'field' => 'from',
				'value' => $source
			),
		);

		return $config;
	}

	public function get_cct_props() {
		$options_list = array();
		$cct_types    = Module::instance()->manager->get_content_types();

		foreach ( $cct_types as $type => $instance ) {
			/** @var Factory $instance */

			$group['label'] = $instance->get_arg( 'name' );
			$fields         = $instance->get_fields_list( 'all' );

			if ( empty( $fields ) ) {
				continue;
			}

			$group['values'] = array();

			foreach ( $fields as $key => $label ) {
				$group['values'][] = array(
					'value'  => $type . '::' . $key,
					'label' => $label
				);
			}

			$options_list[] = $group;
		}

		return $options_list;
	}

}