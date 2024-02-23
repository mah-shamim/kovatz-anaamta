<?php
namespace Jet_Engine\Blocks_Views\Dynamic_Content;

/**
 * Apply actual dynamic data for given block and attributes list
 */
class Dynamic_Block_Parser {

	private $block;
	private $attrs;

	public function __construct( $block, $attrs, $data ) {
		$this->block = $block;
		$this->attrs = $attrs;
		$this->data  = $data;
	}

	public function apply_dynamic_data( $content, $parsed_attrs = array() ) {

		foreach ( $this->block->get_attrs() as $attr ) {

			$attr_name = $attr['attr'];

			if ( empty( $this->attrs[ $attr_name ] ) || empty( $this->attrs[ $attr_name ]['data_source'] ) ) {
				continue;
			}

			$dynamic_value = $this->data->get_dynamic_value( $this->attrs[ $attr_name ], $attr, $parsed_attrs );

			if ( ! empty( $attr['rewrite'] ) ) {
				if ( is_scalar( $dynamic_value ) ) {
					$content = str_replace( '%%' . $attr_name . '%%', $dynamic_value, $content );
				}
			} else {
				$content = $this->block->replace_attr_content( $attr_name, $dynamic_value, $content, $this->attrs, $parsed_attrs );
			}

		}

		return $content;

	}

	/**
	 * Returns dynamic value by given parameters
	 *
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function get_dynamic_value( $data, $attr, $parsed_attrs ) {

		if ( empty( $data['data_source'] ) ) {
			return;
		}

		$result         = null;
		$object_context = ! empty( $data['object_context'] ) ? $data['object_context'] : 'default_object';

		if ( ! empty( $attr['type'] ) ) {
			return $this->get_dynamic_value_by_type( $attr['type'], $data, $attr, $parsed_attrs );
		}

		switch ( $data['data_source'] ) {

			case 'object':

				if ( ! empty( $data['property'] ) ) {
					$result = jet_engine()->listings->data->get_prop(
						$data['property'],
						jet_engine()->listings->data->get_object_by_context( $object_context )
					);
				}

				break;

			case 'custom':

				if ( ! empty( $data['macros'] ) ) {

					$macros_context = jet_engine()->listings->macros->get_macros_context();
					jet_engine()->listings->macros->set_macros_context( $object_context );

					if ( 'jet_engine_field_name' === $data['macros'] ) {
						$data['is_value'] = true;
					}

					$result = jet_engine()->listings->macros->call_macros_func( $data['macros'], $data );

					jet_engine()->listings->macros->set_macros_context( $macros_context );

				}

				break;

			default:
				$result = apply_filters( 'jet-engine/blocks-views/dynamic-content/get-dynamic-value/' . $data['data_source'], null, $data, $parsed_attrs );
				break;
		}

		if ( ! empty( $data['filter_output'] ) && ! empty( $data['filter_callback'] ) ) {
			$result = jet_engine()->listings->apply_callback( $result, $data['filter_callback'], $data );
		}

		if ( is_wp_error( $result ) ) {
			$result = null;
		}

		return $result;

	}

	/**
	 * Retrieves dynamic value by specific attribute type
	 * @return [type] [description]
	 */
	public function get_dynamic_value_by_type( $type, $data = array(), $attr = array(), $parsed_attrs = array() ) {

		switch ( $type ) {
			case 'image':
				return $this->get_dynamic_image( $data, $attr, $parsed_attrs );
		}
	}

	/**
	 * Returns dyanmic image URL by given data
	 *
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function get_dynamic_image( $data, $attr, $parsed_attrs ) {

		$settings = array(
			'dynamic_image_source'        => \Jet_Engine_Tools::safe_get( 'data_source', $data, 'post_thumbnail' ),
			'image_url_prefix'            => \Jet_Engine_Tools::safe_get( 'image_url_prefix', $data, '' ),
			'dynamic_image_source_custom' => \Jet_Engine_Tools::safe_get( 'image_source_custom', $data, '' ),
			'object_context'              => \Jet_Engine_Tools::safe_get( 'object_context', $data, 'default_object' ),
		);

		if ( ! empty( $attr['custom_size'] ) ) {

			$image_size = $this->get_custom_image_size( $attr['custom_size'], $parsed_attrs );

			if ( $image_size ) {
				$settings['dynamic_image_size'] = $image_size;
			}

		}

		$render = jet_engine()->listings->get_render_instance( 'dynamic-image', $settings );

		ob_start();
		$render->render_image( $settings );
		$image = ob_get_clean();

		if ( ! $image ) {
			return;
		} else {
			preg_match( '/src=[\"\'](.*?)[\"\']/', $image, $matches );
			$url = array_pop( $matches );
			return $url;
		}

	}

	public function get_custom_image_size( $custom_size_attr, $attrs = array() ) {

		$attr_parts = explode( '/', $custom_size_attr );
		$parts = count( $attr_parts );

		foreach ( $attr_parts as $index => $attr ) {
			if ( $index === $parts - 1 ) {
				return isset( $attrs[ $attr ] ) ? $attrs[ $attr ] : null;
			} else {
				$attrs = ( isset( $attrs[ $attr ] ) && is_array( $attrs[ $attr ] ) ) ? $attrs[ $attr ] : array();
			}
		}

		// if for some reason we get here without returning - try to get latest result
		return isset( $attrs[ $attr ] ) ? $attrs[ $attr ] : null;

	}

}
