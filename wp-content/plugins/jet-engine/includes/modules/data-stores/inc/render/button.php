<?php
namespace Jet_Engine\Modules\Data_Stores\Render;

use Jet_Engine\Modules\Data_Stores\Module as Module;

class Button extends \Jet_Engine_Render_Base {

	public function get_name() {
		return 'data-store-button';
	}

	public function default_settings() {
		return array(
			'store'         => '',
			'label'         => '',
			'icon'          => false,
			'synch_grid'    => false,
			'synch_grid_id' => '',
			'trigger_popup' => false,

			'action_after_added'   => 'remove_from_store',
			'added_to_store_label' => '',
			'added_to_store_icon'  => false,
			'added_to_store_url'   => '',

			'object_context' => 'default_object',

			'wrapper_class' => '',
		);
	}

	public function render() {

		$settings = $this->get_settings();

		$store   = ! empty( $settings['store'] ) ? $settings['store'] : false;
		$context = ! empty( $settings['object_context'] ) ? $settings['object_context'] : false;

		if ( ! $store ) {
			return;
		}

		$store_instance = Module::instance()->stores->get_store( $store );

		if ( ! $store_instance ) {
			return;
		}

		$context_object = jet_engine()->listings->data->get_object_by_context( $context );

		// Check if the context object is not empty.
		if ( $context_object && is_object( $context_object ) && 'stdClass' === get_class( $context_object ) ) {
			$obj_vars = get_object_vars( $context_object );

			if ( empty( $obj_vars ) ) {
				return;
			}
		}

		$post_id = jet_engine()->listings->data->get_current_object_id( $context_object );

		if ( $store_instance->is_user_store() ) {

			switch ( $context ) {

				case 'current_user':
					$user = jet_engine()->listings->data->get_current_user_object();
					break;

				case 'current_post_author':
					$user = jet_engine()->listings->data->get_current_author_object();
					break;

				default:
					$user = jet_engine()->listings->data->get_queried_user_object();
					break;
			}

			if ( $user ) {
				$post_id = $user->ID;
			}

		}

		$post_id = apply_filters( 'jet-engine/data-stores/store-post-id', $post_id, $store_instance );

		if ( ! $post_id ) {
			return;
		}

		$url   = '#';
		$label = ! empty( $settings['label'] ) ? $this->get_label_html( $settings['label'] ) : '';
		$icon  = ! empty( $settings['icon'] ) ? $this->get_icon_html( $settings['icon'] ) : '';


		$action_after_added = ! empty( $settings['action_after_added'] ) ? $settings['action_after_added'] : 'remove_from_store';

		$added_url   = ! empty( $settings['added_to_store_url'] ) ? $settings['added_to_store_url'] : '';
		$added_label = ! empty( $settings['added_to_store_label'] ) ? $this->get_label_html( $settings['added_to_store_label'] ) : '';
		$added_icon  = ! empty( $settings['added_to_store_icon'] ) ? $this->get_icon_html( $settings['added_to_store_icon'] ) : '';

		$open_popup  = ! empty( $settings['trigger_popup'] ) ? $settings['trigger_popup'] : '';
		$open_popup  = filter_var( $open_popup, FILTER_VALIDATE_BOOLEAN );

		$synch_grid = ! empty( $settings['synch_grid'] ) ? $settings['synch_grid'] : '';
		$synch_grid = filter_var( $synch_grid, FILTER_VALIDATE_BOOLEAN );
		$synch_id   = ! empty( $settings['synch_grid_id'] ) ? $settings['synch_grid_id'] : '';

		$data = array(
			'store' => array(
				'slug'     => $store,
				'type'     => $store_instance->get_type()->type_id(),
				'is_front' => $store_instance->get_type()->is_front_store(),
				'size'     => $store_instance->get_size(),
			),
			'post_id' => $post_id,
			'action_after_added' => $action_after_added,
		);

		if ( in_array( $action_after_added, array( 'remove_from_store', 'switch_status' ) ) ) {
			$data['label']       = $label;
			$data['icon']        = $icon;
			$data['added_url']   = $added_url;
			$data['added_label'] = $added_label;
			$data['added_icon']  = $added_icon;
		}

		if ( $synch_grid && $synch_id ) {
			$data['synch_id'] = $synch_id;
		}

		if ( $open_popup && function_exists( 'jet_popup' ) ) {
			$popup   = ! empty( $settings['jet_attached_popup'] ) ? absint( $settings['jet_attached_popup'] ) : false;
			$error   = '<div style="border: 1px solid #f00; color: #f00; padding: 20px; margin: 0 0 15px;">%1$s</div>';
			$trigger = ! empty( $settings['jet_trigger_type'] ) ? $settings['jet_trigger_type'] : false;

			if ( ! $popup && current_user_can( 'manage_options' ) ) {
				printf( $error, __( 'You enabled <b>Open popup</b> option but not selected Popup to show. Please select popup in the <b>Advanced > JetPopup</b> section or disable <b>Open popup</b> option', 'jet-engine' ) );
			} elseif ( 'none' !== $trigger && current_user_can( 'manage_options' ) ) {
				printf( $error, __( 'Please set <b>Advanced > JetPopup > Trigger Type</b> option to <b>None</b> to avoid unexpected popup behaviour', 'jet-engine' ) );
			} elseif ( $popup && 'none' === $trigger ) {
				$data['popup'] = $popup;
			}

			$engine_trigger = ! empty( $settings['jet_engine_dynamic_popup'] ) ? true : false;

			if ( $engine_trigger ) {
				$data['isJetEngine'] = $engine_trigger;
			}
		}

		$classes = array( 'jet-data-store-link' );
		$attr    = array(
			'data-args'  => htmlspecialchars( json_encode( $data ) ),
			'data-post'  => $post_id,
			'data-store' => $store,
		);

		if ( 'switch_status' === $action_after_added ) {
			$open_in_new = ! empty( $settings['open_in_new'] ) ? filter_var( $settings['open_in_new'], FILTER_VALIDATE_BOOLEAN ) : false;
			$rel_attr    = ! empty( $settings['rel_attr'] ) ? $settings['rel_attr'] : false;

			if ( ! empty( $open_in_new ) ) {
				$attr['target'] = '_blank';
			}

			if ( ! empty( $rel_attr ) ) {
				$attr['rel'] = $rel_attr;
			}
		}

		if ( $store_instance->get_type()->is_front_store() ) {
			$classes[] = 'jet-add-to-store';
			$classes[] = 'is-front-store';
		} else {

			if ( ! $store_instance->in_store( $post_id ) ) {
				$classes[] = 'jet-add-to-store';
			} else {

				switch ( $action_after_added ) {
					case 'remove_from_store':
						$url       = $added_url;
						$label     = $added_label;
						$icon      = $added_icon;
						$classes[] = 'jet-remove-from-store';
						$classes[] = 'in-store';

						break;

					case 'switch_status':
						$url       = $added_url;
						$label     = $added_label;
						$icon      = $added_icon;
						$classes[] = 'jet-add-to-store';
						$classes[] = 'in-store';

						break;

					case 'hide':
						$classes[] = 'jet-add-to-store';
						$classes[] = 'is-hidden';

						break;
				}
			}
		}

		$link_html = sprintf(
			'<a href="%1$s" class="%2$s" %3$s>%4$s%5$s</a>',
			$url,
			join( ' ', $classes ),
			\Jet_Engine_Tools::get_attr_string( $attr ),
			$icon,
			$label
		);

		$wrapper_css = ! empty( $settings['wrapper_css'] ) ? esc_attr( $settings['wrapper_css'] ) : '';

		printf( 
			'<div class="jet-data-store-link-wrapper %2$s">%1$s</div>', 
			$link_html,
			$wrapper_css
		);
	}

	public function get_label_html( $label ) {
		return sprintf( '<span class="jet-data-store-link__label">%s</span>', $label );
	}

	public function get_icon_html( $icon ) {
		return \Jet_Engine_Tools::render_icon( $icon, 'jet-data-store-link__icon' );
	}

}
