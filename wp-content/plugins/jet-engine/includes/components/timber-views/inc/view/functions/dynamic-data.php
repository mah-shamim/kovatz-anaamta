<?php
/**
 * Base function class
 */
namespace Jet_Engine\Timber_Views\View\Functions;

class Dynamic_Data extends Base {

	public function get_name() {
		return 'jet_engine_data';
	}

	public function get_label() {
		return __( 'Dynamic Data', 'jet-engine' );
	}

	public function get_result( $args ) {

		$options_map = [
			'source' => 'dynamic_field_source',
			'key' => 'dynamic_field_post_object',
			'wp_excerpt' => 'dynamic_field_wp_excerpt',
			'excerpt_more' => 'dynamic_excerpt_more',
			'excerpt_length' => 'dynamic_excerpt_length',
			'meta_key' => 'dynamic_field_post_meta',
			'option_name' => 'dynamic_field_option',
			'var_name' => 'dynamic_field_var_name',
			'custom_key' => 'dynamic_field_post_meta_custom',
			'context' => 'object_context',
		];

		$mapped_args = [];

		foreach ( $args as $key => $value ) {
			if ( isset( $options_map[ $key ] ) ) {
				$mapped_args[ $options_map[ $key ] ] = $value;
			}
		}

		$render = jet_engine()->listings->get_render_instance( 'dynamic-field', $mapped_args );
		$result = $render->get_field_content( $render->get_settings() );

		return ! empty( $result ) ? $result : ( ! empty( $args['fallback'] ) ? $args['fallback'] : '' );

	}

	public function get_args() {

		// Allow only default sources so far
		remove_all_filters( 'jet-engine/listings/data/sources' );

		$sources = jet_engine()->listings->data->get_field_sources();

		// And remove legacy Relations Hierarchy source
		if ( isset( $sources['relations_hierarchy'] ) ) {
			unset( $sources['relations_hierarchy'] );
		}

		$sources = \Jet_Engine_Tools::prepare_list_for_js( $sources, ARRAY_A );

		return [
			'source' => [
				'label'   => __( 'Source', 'jet-engine' ),
				'type'    => 'select',
				'default' => 'object',
				'options' => $sources,
			],
			'key' => [
				'label'     => __( 'Object Field', 'jet-engine' ),
				'type'      => 'select',
				'default'   => 'post_title',
				'groups'    => jet_engine()->listings->data->get_object_fields( 'blocks', 'options' ),
				'condition' => [
					'source' => 'object',
				],
			],
			'wp_excerpt' => [
				'label'     => __( 'Automatically generated excerpt', 'jet-engine' ),
				'type'      => 'switcher',
				'default'   => '',
				'condition' => [
					'source' => 'object',
					'key'    => 'post_excerpt',
				],
			],
			'excerpt_more' => [
				'label'     => __( 'More string', 'jet-engine' ),
				'type'      => 'text',
				'default'   => '...',
				'condition' => [
					'source'     => 'object',
					'key'        => 'post_excerpt',
					'wp_excerpt' => 'true',
				],
			],
			'excerpt_length' => [
				'label'     => __( 'Custom length', 'jet-engine' ),
				'type'      => 'number',
				'min'       => 0,
				'max'       => 300,
				'default'   => 0,
				'condition' => [
					'source'     => 'object',
					'key'        => 'post_excerpt',
					'wp_excerpt' => 'true',
				],
			],
			'meta_key' => [
				'label'     => __( 'Meta Field', 'jet-engine' ),
				'type'      => 'select',
				'default'   => '',
				'groups'    => jet_engine()->meta_boxes->get_fields_for_select( 'all', 'blocks' ),
				'condition' => [
					'source' => 'meta',
				],
			],
			'option_name' => [
				'label'     => __( 'Option', 'jet-engine' ),
				'type'      => 'select',
				'default'   => '',
				'groups'    => jet_engine()->options_pages->get_options_for_select( 'all', 'blocks' ),
				'condition' => [
					'source' => 'options_page',
				],
			],
			'var_name' => [
				'label'       => __( 'Variable Name', 'jet-engine' ),
				'label_block' => true,
				'type'        => 'text',
				'default'     => '',
				'condition'   => [
					'source' => 'query_var',
				],
			],
			'custom_key' => [
				'label'       => __( 'Custom Field Key', 'jet-engine' ),
				'type'        => 'text',
				'default'     => '',
				'label_block' => true,
				'description' => __( 'Accepts current object properites, meta field names or repeater field names. <br><b>Note: this field will override Object field / Meta field value</b>', 'jet-engine' ),
				'condition'   => [
					'source!' => [ 'query_var', 'options_page', 'relations_hierarchy' ],
				],
			],
			'fallback' => [
				'label'       => __( 'Fallback', 'jet-engine' ),
				'type'        => 'text',
				'default'     => '',
				'label_block' => true,
			],
			'context' => [
				'label'     => __( 'Context', 'jet-engine' ),
				'type'      => 'select',
				'default'   => 'default_object',
				'options'   => jet_engine()->listings->allowed_context_list( 'blocks' ),
			]
		];
	}

}
