<?php
namespace Jet_Engine\Modules\Data_Stores;

class Render_Links {

	public function __construct() {
		add_filter( 'jet-engine/listings/dynamic-link/pre-render-link', array( $this, 'maybe_render_links' ), 10, 4 );
		add_action( 'jet-engine/listings/dynamic-link/style-tabs', array( $this, 'elementor_link_style' ) );
	}

	/**
	 * Add in store styles to the ElementorLink widget
	 */
	public function elementor_link_style( $widget ) {

		$widget->start_controls_tab(
			'dynamic_link_in_store',
			array(
				'label' => __( 'In Store', 'jet-engine' ),
			)
		);

		$widget->add_control(
			'link_color_in_store',
			array(
				'label'  => __( 'Text Color', 'jet-engine' ),
				'type'   => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					$widget->css_selector( '__link.in-store' ) => 'color: {{VALUE}}',
				),
			)
		);

		$widget->add_control(
			'link_bg_in_store',
			array(
				'label'     => __( 'Background', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					$widget->css_selector( '__link.in-store' ) => 'background-color: {{VALUE}};',
				),
			)
		);

		$widget->add_control(
			'link_icon_color_in_store',
			array(
				'label'  => __( 'Icon Color', 'jet-engine' ),
				'type'   => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					$widget->css_selector( '__link.in-store .jet-listing-dynamic-link__icon' ) => 'color: {{VALUE}}',
					$widget->css_selector( '__link.in-store .jet-listing-dynamic-link__icon :is(svg, path)' ) => 'fill: {{VALUE}}',
				),
			)
		);

		$widget->add_control(
			'link_hover_border_in_store',
			array(
				'label' => __( 'Border Color', 'jet-engine' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'condition' => array(
					'link_border_border!' => '',
				),
				'selectors' => array(
					$widget->css_selector( '__link.in-store' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$widget->end_controls_tab();

	}

	/**
	 * register count macros
	 */
	public function store_count( $store ) {

		$store_instance = Module::instance()->stores->get_store( $store );

		if ( ! $store_instance ) {
			return '';
		}

		if ( ! $store_instance->get_type()->is_front_store() ) {
			$count = $store_instance->get_count();
		} else {
			$count = 0;
		}

		return sprintf(
			'<span class="jet-engine-data-store-count" data-store="%1$s" data-is-front="%2$s">%3$s</span>',
			$store,
			$store_instance->get_type()->is_front_store(),
			$count
		);
	}

	/**
	 * register count macros
	 */
	public function post_count( $store = null, $post_id = false ) {

		$store_instance = Module::instance()->stores->get_store( $store );

		if ( ! $store_instance ) {
			return '';
		}

		if ( $store_instance->is_user_store() ) {

			$user = jet_engine()->listings->data->get_queried_user_object();

			if ( $user ) {
				$post_id = $user->ID;
			}

		}

		$post_id = apply_filters( 'jet-engine/data-stores/store-post-id', $post_id, $store_instance );

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id ) {
			return '';
		}

		return sprintf(
			'<span class="jet-engine-data-post-count" data-store="%3$s" data-post="%2$d">%1$d</span>',
			$store_instance->get_post_count( $post_id ),
			$post_id,
			$store
		);
	}

	/**
	 * Check if we need to render add/remove stores link
	 */
	public function maybe_render_links( $result, $settings, $base_class, $render ) {

		$source = ! empty( $settings['dynamic_link_source'] ) ? $settings['dynamic_link_source'] : '_permalink';

		switch ( $source ) {
			case 'add_to_store':
				$result = $this->add_to_store_link( $result, $settings, $base_class, $render );
				break;

			case 'remove_from_store':
				$result = $this->remove_from_store_link( $result, $settings, $base_class, $render );
				break;
		}

		return $result;

	}

	public function remove_from_store_link( $result, $settings, $base_class, $render ) {

		$store   = ! empty( $settings['dynamic_link_store'] ) ? $settings['dynamic_link_store'] : false;
		$context = ! empty( $settings['object_context'] ) ? $settings['object_context'] : false;

		if ( ! $store ) {
			return $result;
		}

		$store_instance = Module::instance()->stores->get_store( $store );

		if ( ! $store_instance ) {
			return $result;
		}

		$settings = apply_filters( 'jet-engine/data-stores/remove-from-store/settings', $settings, $store_instance );
		$post_id = get_the_ID();

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
			return $result;
		}

		$url   = '#';
		$label = $render->get_link_label( $settings, $base_class, $url );
		$icon  = $render->get_link_icon( $settings, $base_class );

		$data = array(
			'store' => array(
				'slug' => $store,
				'type' => $store_instance->get_type()->type_id(),
				'is_front' => $store_instance->get_type()->is_front_store(),
				'size' => $store_instance->get_size(),
			),
			'post_id' => $post_id,
		);

		$remove_from_listing = ! empty( $settings['remove_post_from_listing'] ) ? $settings['remove_post_from_listing'] : false;
		$remove_from_listing = filter_var( $remove_from_listing, FILTER_VALIDATE_BOOLEAN );

		$data['remove_from_listing'] = $remove_from_listing;

		$class = '';

		if ( ! $store_instance->get_type()->is_front_store() && ! $store_instance->in_store( $post_id ) ) {
			$class = 'is-hidden';
		} elseif ( ! $store_instance->get_type()->is_front_store() && $store_instance->in_store( $post_id ) ) {
			$class = 'in-store';
		} elseif ( $store_instance->get_type()->is_front_store() ) {
			$class = 'is-front-store';
		}

		$result = sprintf(
			'<a href="%1$s" class="jet-remove-from-store jet-listing-dynamic-link__link %6$s" data-args="%2$s" data-post="%5$s" data-store="%7$s">%3$s%4$s</a>',
			$url,
			htmlspecialchars( json_encode( $data ) ),
			$icon,
			$label,
			$post_id,
			$class,
			$store
		);

		return $result;

	}

	/**
	 * Add to store link
	 */
	public function add_to_store_link( $result, $settings, $base_class, $render ) {

		$store   = ! empty( $settings['dynamic_link_store'] ) ? $settings['dynamic_link_store'] : false;
		$context = ! empty( $settings['object_context'] ) ? $settings['object_context'] : false;

		if ( ! $store ) {
			return $result;
		}

		$store_instance = Module::instance()->stores->get_store( $store );

		if ( ! $store_instance ) {
			return $result;
		}

		$settings = apply_filters( 'jet-engine/data-stores/add-to-store/settings', $settings, $store_instance );
		$post_id = get_the_ID();

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
			return $result;
		}

		$url   = '#';
		$label = $render->get_link_label( $settings, $base_class, $url );
		$icon  = $render->get_link_icon( $settings, $base_class );

		$added_url   = ! empty( $settings['added_to_store_url'] ) ? $settings['added_to_store_url'] : '';
		$added_label = ! empty( $settings['added_to_store_text'] ) ? $settings['added_to_store_text'] : '';
		$added_icon  = ! empty( $settings['added_to_store_icon'] ) ? $settings['added_to_store_icon'] : '';
		$open_popup  = ! empty( $settings['dynamic_link_trigger_popup'] ) ? $settings['dynamic_link_trigger_popup'] : '';
		$open_popup  = filter_var( $open_popup, FILTER_VALIDATE_BOOLEAN );
		$added_label = '<span class="jet-listing-dynamic-link__label">' . $added_label . '</span>';
		$added_label = jet_engine()->listings->macros->do_macros( $added_label, $store );

		$synch_grid = ! empty( $settings['dynamic_link_synch_grid'] ) ? $settings['dynamic_link_synch_grid'] : '';
		$synch_grid = filter_var( $synch_grid, FILTER_VALIDATE_BOOLEAN );
		$synch_id   = ! empty( $settings['dynamic_link_synch_grid_id'] ) ? $settings['dynamic_link_synch_grid_id'] : '';

		if ( $added_icon ) {
			$added_icon = \Jet_Engine_Tools::render_icon( $added_icon, $base_class . '__icon' );
		}

		$data = array(
			'label' => $label,
			'icon' => $icon,
			'added_url' => $added_url,
			'added_label' => $added_label,
			'added_icon' => $added_icon,
			'store' => array(
				'slug' => $store,
				'type' => $store_instance->get_type()->type_id(),
				'is_front' => $store_instance->get_type()->is_front_store(),
				'size' => $store_instance->get_size(),
			),
			'post_id' => $post_id,
		);

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

		$class = '';

		if ( ! $store_instance->get_type()->is_front_store() && $store_instance->in_store( $post_id ) ) {
			$url   = $added_url;
			$label = $added_label;
			$icon  = $added_icon;
			$class = 'in-store';
		} elseif ( $store_instance->get_type()->is_front_store() ) {
			$class = 'is-front-store';
		}

		$result = sprintf(
			'<a href="%1$s" class="jet-add-to-store jet-listing-dynamic-link__link %6$s" data-args="%2$s" data-post="%5$s" data-store="%7$s">%3$s%4$s</a>',
			$url,
			htmlspecialchars( json_encode( $data ) ),
			$icon,
			$label,
			$post_id,
			$class,
			$store
		);

		return $result;

	}

}
