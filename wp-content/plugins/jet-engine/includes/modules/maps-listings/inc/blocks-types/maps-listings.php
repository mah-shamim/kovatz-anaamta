<?php
namespace Jet_Engine\Modules\Maps_Listings;

/**
 * Maps Listing View
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Maps_Listing_Blocks_Views_Type class
 */
class Maps_Listing_Blocks_Views_Type extends \Jet_Engine_Blocks_Views_Type_Base {

	/**
	 * Returns block name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'maps-listing';
	}

	/**
	 * Return attributes array
	 *
	 * @return array
	 */
	public function get_attributes() {
		return apply_filters( 'jet-engine/blocks-views/maps-listing/attributes', array_merge( array(
			'lisitng_id' => array(
				'type'    => 'string',
				'default' => '',
			),
			'address_field' => array(
				'type'    => 'string',
				'default' => '',
			),
			'add_lat_lng' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'lat_lng_address_field' => array(
				'type'    => 'string',
				'default' => '',
			),
			'map_height' => array(
				'type'    => 'number',
				'default' => 500,
			),
			'posts_num' => array(
				'type'    => 'number',
				'default' => 6,
			),
			'auto_center' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'max_zoom' => array(
				'type' => 'number',
			),
			'min_zoom' => array(
				'type' => 'number',
			),
			'custom_center' => array(
				'type'    => 'string',
				'default' => '',
			),
			'custom_zoom' => array(
				'type'    => 'number',
				'default' => 11,
			),
			'custom_style' => array(
				'type'    => 'string',
				'default' => '',
			),
			'zoom_control' => array(
				'type'    => 'string',
				'default' => 'auto',
			),
			'zoom_controls' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'fullscreen_control' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'street_view_controls' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'map_type_controls' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'centering_on_open' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'zoom_on_open' => array(
				'type' => 'number',
			),

			// Marker
			'marker_type' => array(
				'type'    => 'string',
				'default' => 'icon',
			),
			'marker_icon' => array(
				'type' => 'number',
			),
			'marker_icon_url' => array(
				'type'    => 'string',
				'default' => '',
			),
			'marker_image_field' => array(
				'type'    => 'string',
				'default' => '',
			),
			'marker_image_field_custom' => array(
				'type'    => 'string',
				'default' => '',
			),
			'marker_label_type' => array(
				'type'    => 'string',
				'default' => 'post_title',
			),
			'marker_label_field' => array(
				'type'    => 'string',
				'default' => '',
			),
			'marker_label_field_custom' => array(
				'type'    => 'string',
				'default' => '',
			),
			'marker_label_text' => array(
				'type'    => 'string',
				'default' => '',
			),
			'marker_label_format_cb' => array(
				'type'    => 'string',
				'default' => '',
			),
			'marker_label_custom' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'marker_label_custom_output' => array(
				'type'    => 'string',
				'default' => '%s',
			),
			'multiple_marker_types' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'multiple_markers' => array(
				'type'    => 'array',
				'default' => array(),
			),
			'marker_clustering' => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'cluster_max_zoom' => array(
				'type' => 'number',
			),
			'cluster_radius' => array(
				'type' => 'number',
			),

			// Popup
			'popup_width' => array(
				'type'    => 'number',
				'default' => 320,
			),
			'popup_offset' => array(
				'type'    => 'number',
				'default' => 40,
			),
			'popup_pin' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'popup_preloader' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'popup_open_on' => array(
				'type'    => 'string',
				'default' => 'click',
			),

			// Custom Query
			'custom_query' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'custom_query_id' => array(
				'type'    => 'string',
				'default' => '',
			),

			// Posts Query
			'posts_query' => array(
				'type'    => 'array',
				'default' => array(),
			),
			'meta_query_relation' => array(
				'type'    => 'string',
				'default' => 'AND',
			),
			'tax_query_relation' => array(
				'type'    => 'string',
				'default' => 'AND',
			),

			// Block Visibility
			'hide_widget_if' => array(
				'type'    => 'string',
				'default' => '',
			),

			// Block ID
			'_block_id' => array(
				'type'    => 'string',
				'default' => '',
			),

			// Element ID
			'_element_id' => array(
				'type'    => 'string',
				'default' => '',
			),
		), jet_engine()->blocks_views->block_types->get_allowed_callbacks_atts() ) );
	}

	/**
	 * Add style block options
	 *
	 * @return void
	 */
	public function add_style_manager_options() {

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_popup_style',
				'title' => esc_html__( 'Popup Pin', 'jet-engine' ),
				'condition' => array(
					'popup_pin' => true,
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'popup_pin_size',
				'label' => __( 'Pin Size', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 4,
							'max'  => 60,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .popup-has-pin .jet-map-box:after' => 'margin: 0 0 0 -{{VALUE}}{{UNIT}}; border-width: {{VALUE}}{{UNIT}} {{VALUE}}{{UNIT}} 0 {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'           => 'popup_pin_color',
				'label'        => __( 'Pin Color', 'jet-engine' ),
				'type'         => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .popup-has-pin .jet-map-box:after' => 'border-color: {{VALUE}} transparent transparent transparent;',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_popup_preloader_style',
				'title' => esc_html__( 'Popup Preloader', 'jet-engine' ),
				'condition' => array(
					'popup_preloader' => true,
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'popup_preloader_bg_color',
				'label'     => __( 'Background Color', 'jet-engine' ),
				'type'      => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-map-preloader.is-active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'        => 'popup_preloader_color',
				'label'     => __( 'Loader Color', 'jet-engine' ),
				'type'      => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-map-preloader.is-active .jet-map-loader' => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'popup_preloader_height',
				'label' => __( 'Height', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 700,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-map-preloader.is-active' => 'height: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_marker_style',
				'title' => esc_html__( 'Marker', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'marker_width',
				'label' => __( 'Width', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 0,
							'max'  => 200,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-map-marker-wrap' => 'width: {{VALUE}}{{UNIT}};',
					'{{WRAPPER}} .jet-map-marker-image' => 'width: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'           => 'marker_typography',
				'label'        => __( 'Typography', 'jet-engine' ),
				'type'         => 'typography',
				'separator'    => 'before',
				'css_selector' => array(
					'{{WRAPPER}} .jet-map-marker-wrap' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'marker_icon_size',
				'label' => __( 'Icon Size', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min' => 10,
							'max' => 300,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-map-marker' => 'font-size: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->start_tabs(
			'style_controls',
			array(
				'id'        => 'tabs_marker_state',
				'separator' => 'both',
			)
		);

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'marker_state_normal',
				'title' => __( 'Normal', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'marker_color',
				'label' => __( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-map-marker-wrap' => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'marker_bg_color',
				'label' => __( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-map-marker-wrap' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .jet-map-marker-wrap:after' => 'border-color: {{VALUE}} transparent transparent transparent;',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'marker_icon_color',
				'label' => __( 'Icon Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-map-marker' => 'color: {{VALUE}}',
					'{{WRAPPER}} .jet-map-marker path' => 'fill: {{VALUE}} !important',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'marker_state_hover',
				'title' => __( 'Hover', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'marker_color_hover',
				'label' => __( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-map-marker-wrap:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'marker_bg_color_hover',
				'label' => __( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-map-marker-wrap:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .jet-map-marker-wrap:hover:after' => 'border-color: {{VALUE}} transparent transparent transparent;',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'marker_icon_color_hover',
				'label' => __( 'Icon Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jet-map-marker:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .jet-map-marker:hover path' => 'fill: {{VALUE}} !important',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->end_tabs();

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'marker_padding',
				'label' => __( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%', 'em' ),
				'css_selector'  => array(
					'{{WRAPPER}} .jet-map-marker-wrap' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'marker_border_radius',
				'label' => __( 'Border Radius', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} .jet-map-marker-wrap' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		// Not supported
		//$this->controls_manager->add_control(
		//	array(
		//		'id'    => 'marker_box_shadow',
		//		'label' => __( 'Box Shadow', 'jet-engine' ),
		//		'type'  => 'box-shadow',
		//		'css_selector' => '{{WRAPPER}} .jet-map-marker-wrap',
		//	)
		//);

		$this->controls_manager->add_control(
			array(
				'id'    => 'marker_pin_size',
				'label' => __( 'Pin Size', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min' => 4,
							'max' => 60,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jet-map-marker-wrap:after' => 'margin: 0 0 0 -{{VALUE}}{{UNIT}}; border-width: {{VALUE}}{{UNIT}} {{VALUE}}{{UNIT}} 0 {{VALUE}}{{UNIT}};',
					'{{WRAPPER}} .jet-map-marker-wrap' => 'margin-bottom: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'      => 'link_alignment',
				'label'   => __( 'Alignment', 'jet-engine' ),
				'type'    => 'choose',
				'attributes' => array(
					'default' => array(
						'value' => 'center',
					)
				),
				'options' => array(
					'left' => array(
						'shortcut' => __( 'Left', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignleft',
					),
					'center' => array(
						'shortcut' => __( 'Center', 'jet-engine' ),
						'icon'     => 'dashicons-editor-aligncenter',
					),
					'right' => array(
						'shortcut' => __( 'Right', 'jet-engine' ),
						'icon'     => 'dashicons-editor-alignright',
					),
				),
				'css_selector'  => array(
					'{{WRAPPER}} .jet-map-marker-wrap' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->end_section();

	}

	public function prepare_attributes( $attributes ) {

		if ( ! empty( $attributes['multiple_markers'] ) ) {
			$attributes['multiple_markers'] = array_map(
				function ( $item ) {
					$item['marker_type'] = 'icon';
					return $item;
				},
				$attributes['multiple_markers']
			);
		}

		return $attributes;
	}

	public function render_callback( $attributes = array() ) {

		if ( $this->is_edit_mode() ) {
			$content = sprintf(
				'<img class="jet-map-listing-block__placeholder" src="%1$s" %2$s>',
				jet_engine()->modules->modules_url( 'maps-listings/assets/images/dummy-map.png' ),
				'style="width:100%"'
			);
		} else {
			$content = parent::render_callback( $attributes );
		}

		return sprintf(
			'<div class="jet-map-listing-block" data-id="%2$s"%3$s>%1$s</div>',
			$content,
			esc_attr( $attributes['_block_id'] ),
			! empty( $attributes['_element_id'] ) ? ' id="' . esc_attr( $attributes['_element_id'] ) . '"' : ''
		);
	}

}
