<?php
/**
 * Register and hadle jet-engine related shortcodes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Engine_Shortcodes {

	private $controls = array();

	public function __construct() {
		add_shortcode( 'jet_engine', array( $this, 'base_shortcode' ) );
		add_shortcode( 'jet_engine_data', array( $this, 'data_shortcode' ) );
	}

	/**
	 * Handle shortcode
	 *
	 * @param  array  $atts [description]
	 * @return [type]       [description]
	 */
	public function data_shortcode( $atts = array() ) {

		if ( ! is_array( $atts ) ) {
			$atts = array();
		}

		$atts = array_merge( array(
			'dynamic_field_post_meta' => '',
		), $atts );

		foreach ( $atts as $key => $value ) {
			// Ensure boolean values correctly pased
			if ( in_array( $value, array( 'true', 'false', 'yes', 'no' ) ) ) {
				$atts[ $key ] = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
			}
		}

		$add_wrap = false;

		// Convert filter callbacks string into array
		if ( ! empty( $atts['filter_callbacks'] ) ) {
			$filter_callbacks = str_replace( '&amp;', '&', $atts['filter_callbacks'] );
			$filter_callbacks = rtrim( ltrim( $filter_callbacks, '{' ), '}' );
			$filter_callbacks = explode( '},{', $filter_callbacks );
			
			$atts['filter_callbacks'] = array_map( function( $row ) {
				parse_str( $row, $parsed_row );
				return $parsed_row;
			}, $filter_callbacks );

			foreach ( $atts['filter_callbacks'] as $cb_args ) {

				if ( empty( $cb_args['filter_callback'] ) ) {
					continue;
				}

				if ( 'jet_engine_img_gallery_slider' === $cb_args['filter_callback'] ) {
					$add_wrap = true;
				}
			}

		}
		
		$renderer = jet_engine()->listings->get_render_instance( 'dynamic-field', $atts );
		
		ob_start();
		$renderer->render_field_content( $renderer->get_settings() );
		$content = ob_get_clean();

		if ( $add_wrap ) {
			$content = sprintf( '<div data-is-block="jet-engine/dynamic-field">%s</div>', $content );
		}

		return $content;

	}

	public function catch_source_controls() {
		do_action( 'jet-engine/listings/dynamic-field/source-controls', $this );
		return $this->controls;
	}

	public function add_responsive_control( $name, $data ) {
		$this->add_control( $name, $data );
		return $this->controls;
	}

	public function add_control( $name, $data = array() ) {
		$this->controls[] = array(
			'name' => $name,
			'data' => $data,
		);
	}

	public function get_generator_config() {

		$sources = jet_engine()->listings->data->get_field_sources();

		// remove legacy Relations Hierarchy source for shortcode
		if ( isset( $sources['relations_hierarchy'] ) ) {
			unset( $sources['relations_hierarchy'] );
		}

		$sources = \Jet_Engine_Tools::prepare_list_for_js( $sources, ARRAY_A );

		return array(
			'sources'       => $sources,
			'object_fields' => jet_engine()->listings->data->get_object_fields( 'blocks', 'options' ),
			'source_args'   => $this->catch_source_controls(),
			'meta_fields'   => jet_engine()->meta_boxes->get_fields_for_select( 'all', 'blocks' ),
			'options_pages' => jet_engine()->options_pages->get_options_for_select( 'all', 'blocks' ),
			'callbacks'     => \Jet_Engine_Tools::prepare_list_for_js( 
				jet_engine()->listings->get_allowed_callbacks(), 
				ARRAY_A 
			),
			'cb_args'       => jet_engine()->listings->get_callbacks_args(),
			'context_list'  => jet_engine()->listings->allowed_context_list( 'blocks' ),
			'labels'        => $this->get_controls_labels(),
		);
	}

	public function get_controls_labels() {
		return array(
			'dynamic_field_source' => array(
				'label' => __( 'Source', 'jet-engine' ),
			),
			'dynamic_field_post_object' => array(
				'label' => __( 'Object Field', 'jet-engine' ),
			),
			'dynamic_field_wp_excerpt' => array(
				'label' => __( 'Automatically generated excerpt', 'jet-engine' ),
			),
			'dynamic_excerpt_more' => array(
				'label' => __( 'More string', 'jet-engine' ),
			),
			'dynamic_excerpt_length' => array(
				'label' => __( 'Custom length', 'jet-engine' ),
			),
			'dynamic_field_post_meta' => array(
				'label' => __( 'Meta Field', 'jet-engine' ),
			),
			'dynamic_field_option' => array(
				'label' => __( 'Option', 'jet-engine' ),
			),
			'dynamic_field_var_name' => array(
				'label' => __( 'Variable Name', 'jet-engine' ),
			),
			'dynamic_field_post_meta_custom' => array(
				'label'       => __( 'Custom Object Field / Meta field / Repeater key', 'jet-engine' ),
				'description' => __( 'Note: this field will override Object Field / Meta Field value', 'jet-engine' ),
			),
			'dynamic_field_filter' => array(
				'label' => esc_html__( 'Filter Field Output', 'jet-engine' ),
			),
			'hide_if_empty' => array(
				'label' => esc_html__( 'Hide if Empty', 'jet-engine' ),
			),
			'field_fallback' => array(
				'label' => esc_html__( 'Fallback Value', 'jet-engine' ),
			),
			'filter_callback' => array(
				'label' => __( 'Callback', 'jet-engine' ),
			),
			'dynamic_field_custom' => array(
				'label' => esc_html__( 'Customize field output', 'jet-engine' ),
			),
			'dynamic_field_format' => array(
				'label'       => __( 'Field format', 'jet-engine' ),
				'description' => __( '%s will be replaced with field value. If you need use plain % sign, replace it with %% (for example for JetEngine macros wrappers)', 'jet-engine' ),
			),
			'object_context' => array(
				'label' => __( 'Context', 'jet-engine' ),
			),
		);
	}

	/**
	 * Handle shortcode
	 *
	 * @param  array  $atts [description]
	 * @return [type]       [description]
	 */
	public function base_shortcode( $atts = array() ) {

		$atts = shortcode_atts( apply_filters( 'jet-engine/shortcodes/default-atts', array(
			'component' => 'meta_field',
			'field'     => false,
			'page'      => false,
			'post_id'   => false,
		) ), $atts, 'jet_engine' );

		$result = '';

		switch ( $atts['component'] ) {

			case 'option':
				if ( ! empty( $atts['page'] ) && ! empty( $atts['field'] ) ) {
					$result = jet_engine()->listings->data->get_option( $atts['page'] . '::' . $atts['field'] );
				}
				break;

			case 'meta_field':
				if ( ! empty( $atts['field'] ) ) {
					$post_id = ! empty( $atts['post_id'] ) ? absint( $atts['post_id'] ) : get_the_ID();
					$result = get_post_meta( $post_id, $atts['field'], true );
				}

				break;

			default:
				$result = apply_filters( 'jet-engine/shortcodes/' . $atts['component'] . '/result', $result, $atts );
		}

		if ( ! empty( $result ) && is_array( $result ) ) {
			$result = implode( ', ', $result );
		}

		return $result;

	}

}