<?php
/**
 * Base function class
 */
namespace Jet_Engine\Timber_Views\View\Functions;

if ( ! trait_exists( '\Jet_Engine_Get_Data_Sources_Trait' ) ) {
	require_once jet_engine()->plugin_path( 'includes/traits/get-data-sources.php' );
}

class Dynamic_URL extends Base {

	use \Jet_Engine_Get_Data_Sources_Trait;

	public function get_name() {
		return 'jet_engine_url';
	}

	public function get_label() {
		return __( 'Dynamic URL', 'jet-engine' );
	}

	public function get_result( $args ) {

		$options_map = apply_filters( 'jet-engine/twig-views/functions/dynamic-url/controls-map', [
			'source' => 'dynamic_link_source',
			'option' => 'dynamic_link_option',
			'custom_source' => 'dynamic_link_source_custom',
			'context' => 'object_context',
		] );

		$mapped_args = [];

		// pre-process thumbnail URL and Image URL by ID if selected
		if ( ! empty( $args['source'] ) && '_thumbnail_url' === $args['source'] ) {
			return $this->get_thumbnail_url( $args );
		} elseif ( ! empty( $args['source'] ) && '_img_by_id' === $args['source'] ) {
			return $this->get_image_url( $args );
		}

		foreach ( $args as $key => $value ) {
			if ( isset( $options_map[ $key ] ) ) {
				$mapped_args[ $options_map[ $key ] ] = $value;
			} else {
				$mapped_args[ $key ] = $value;
			}
		}

		$render = jet_engine()->listings->get_render_instance( 'dynamic-link', $mapped_args );
		$result = $render->get_link_url( $render->get_settings() );

		return ! empty( $result ) ? $result : ( ! empty( $args['fallback'] ) ? $args['fallback'] : '' );

	}

	public function get_image_url( $args ) {
		
		$size           = ! empty( $args['size'] ) ? $args['size'] : 'full';
		$object_context = ! empty( $args['context'] ) ? $args['context'] : 'default_object';
		$object         = jet_engine()->listings->data->get_object_by_context( $object_context );

		if ( ! $object ) {
			$object = jet_engine()->listings->data->get_current_object();
		}

		$from_field = ! empty( $args['custom_source'] ) ? $args['custom_source'] : false;

		if ( ! $object || ! $from_field || empty( $object->$from_field ) ) {
			return ! empty( $args['fallback'] ) ? $args['fallback'] : '';
		} else {
			return wp_get_attachment_image_url( $object->$from_field, $size );
		}
		
	}

	public function get_thumbnail_url( $args ) {
		
		$size = ! empty( $args['size'] ) ? $args['size'] : 'full';
		$object_context = ! empty( $args['context'] ) ? $args['context'] : 'default_object';

		$post = jet_engine()->listings->data->get_object_by_context( $object_context );

		if ( ! $post ) {
			$post = jet_engine()->listings->data->get_current_object();
		}

		if ( ! $post || 'WP_Post' !== get_class( $post ) || ! has_post_thumbnail( $post ) ) {
			return ! empty( $args['fallback'] ) ? $args['fallback'] : '';
		} else {
			return get_the_post_thumbnail_url( $post, $size );
		}
		
	}

	public function get_args() {

		// Renamed filter to add only sources whis are returns URLs
		$sources = $this->get_dynamic_sources( 'plain', true );

		// And remove delete post URL
		foreach ( $sources[0]['values'] as $index => $value ) {
			if ( 'delete_post_link' === $value['value'] ) {
				unset( $sources[0]['values'][ $index ] );
			}
		}

		// + add 'Thumbnail URL' and 'Image URL by image ID' source
		$sources[0]['values'] = array_merge( [ 
			[ 'value' => '_thumbnail_url', 'label' => __( 'Thumbnail URL', 'jet-engine' ) ],
			[ 'value' => '_img_by_id', 'label' => __( 'Image URL by Image ID', 'jet-engine' ) ],
		], $sources[0]['values'] );

		return apply_filters( 'jet-engine/twig-views/functions/dynamic-url/controls', [
			'source' => [
				'label'   => __( 'Source', 'jet-engine' ),
				'type'    => 'select',
				'default' => '_permalink',
				'groups'  => $sources,
			],
			'size' => [
				'label'   => __( 'Image Size', 'jet-engine' ),
				'type'    => 'select',
				'default' => 'full',
				'options' => jet_engine()->listings->get_image_sizes( 'blocks' ),
				'condition' => [
					'source' => [ '_thumbnail_url' ],
				],
			],
			'option' => [
				'label'     => __( 'Option', 'jet-engine' ),
				'type'      => 'select',
				'default'   => '',
				'groups'    => jet_engine()->options_pages->get_options_for_select( 'plain', 'blocks' ),
				'condition' => [
					'source' => 'options_page',
				],
			],
			'custom_source' => [
				'label'       => __( 'Custom Field Name', 'jet-engine' ),
				'description' => __( 'Could be meta field name, repeater field key or object property key', 'jet-engine' ),
				'type'        => 'text',
				'default'     => '',
				'condition'   => [
					'source!' => '_thumbnail_url',
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
		] );
	}

}
