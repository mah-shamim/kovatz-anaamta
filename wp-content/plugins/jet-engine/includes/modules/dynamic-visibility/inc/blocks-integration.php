<?php
namespace Jet_Engine\Modules\Dynamic_Visibility;

class Blocks_Integration extends Condition_Checker {

	public function __construct() {
		add_action( 'jet-engine/blocks-views/editor-script/after', array( $this, 'enqueue_blocks_js' ) );
		add_filter( 'register_block_type_args', array( $this, 'register_visibility_attr' ), 10, 2 );

		if ( ! $this->editor_request() ) {
			add_filter( 'pre_render_block', array( $this, 'render_block' ), 20, 2 );
		}
		
	}

	public function editor_request() {
		return ( ! empty( $_GET['context'] ) && 'edit' === $_GET['context'] && ! empty( $_GET['attributes'] ) );
	}

	public function render_block( $result, $block_data ) {
		
		if ( empty( $block_data['attrs']['jetDynamicVisibility'] ) ) {
			return $result;
		}

		$settings = $block_data['attrs']['jetDynamicVisibility'];

		if ( empty( $settings['jedv_enabled'] ) || empty( $settings['jedv_conditions'] ) ) {
			return $result;
		}

		$conditions                = $settings['jedv_conditions'];
		$parsed_dynamic_conditions = array();
		$dynamic_settings          = $settings;

		foreach ( $conditions as $index => $condition ) {
			
			$condition['__dynamic__'] = array();
			$parsed_dynamic_conditions[ $index ] = array(
				'__dynamic__' => array(),
			);

			foreach ( $condition as $condition_key => $condition_value ) {
				
				if ( '__dynamic__' === $condition_key ) {
					continue;
				}

				$dynamic_data = $condition_value ? json_decode( $condition_value, true ) : false;

				if ( $dynamic_data && is_array( $dynamic_data ) ) {
					$condition['__dynamic__'][ $condition_key ] = $condition_value;
					$parsed_dynamic_conditions[ $index ]['__dynamic__'][ $condition_key ] = $condition_value;
					$parsed_dynamic_conditions[ $index ][ $condition_key ] = jet_engine()->blocks_views->dynamic_content->data->get_dynamic_value( $dynamic_data, array(), array() );
				} else {
					$parsed_dynamic_conditions[ $index ][ $condition_key ] = $condition_value;
				}
			}

			$conditions[ $index ] = $condition;

		}

		$dynamic_settings['jedv_conditions'] = $parsed_dynamic_conditions;
		$settings['jedv_conditions']         = $conditions;

		$is_visible = $this->check_cond( $settings, $dynamic_settings );

		if ( ! $is_visible ) {
			return '';
		}

		return $result;
	}

	public function register_visibility_attr( $args = array(), $block_type = null ) {
		
		if ( empty( $args['attributes'] ) ) {
			$args['attributes'] = array();
		}

		if ( empty( $args['attributes']['jetDynamicVisibility'] ) ) {
			$args['attributes']['jetDynamicVisibility'] = array(
				'type' => 'object',
				'default' => array( 'jedv_enabled' => false ),
			);
		}

		return $args;
	}

	public function enqueue_blocks_js() {
		
		wp_enqueue_script(
			'jet-engine-blocks-dynamic-visibility',
			jet_engine()->plugin_url( 'includes/modules/dynamic-visibility/inc/assets/js/dynamic-visibility.js' ),
			array( 'jet-engine-blocks-views' ),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script( 'jet-engine-blocks-dynamic-visibility', 'JetEngineDynamicVisibilityData', array(
			'controls' => \Jet_Engine_Tools::prepare_controls_for_js( Module::instance()->get_condition_controls() ),
		) );

		wp_add_inline_style( 'jet-engine-blocks-views', '.jet-engine-visibility-modal {overflow:visible} .jet-engine-visibility-modal .components-base-control select.components-select-control__input {max-width: 100%; width: 100%; box-sizing: border-box;} .jet-engine-visibility-dynamic-trigger {display: flex; justify-content: flex-end; margin: 0 0 -24px;}' );
		
	}

}
