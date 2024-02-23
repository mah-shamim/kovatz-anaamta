<?php
namespace Jet_Engine\Modules\Maps_Listings\Filters\Blocks;

use Jet_Engine\Modules\Maps_Listings\Filters\Types\Location_Distance_Render;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Location_Distance class
 */
class Location_Distance extends \Jet_Smart_Filters_Block_Base {

	/**
	 * Returns block name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'location-distance';
	}

	public function set_css_scheme(){
		$this->css_scheme = array();
	}

	public function add_style_manager_options() {

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_content_style',
				'title' => __( 'Location & Distance Inputs', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'=> 'location_width',
				'label' => __( 'Location Input Width', 'jet-engine' ),
				'description' => esc_html__( 'Distance control will automatically fill the rest of space in the line', 'jet-engine' ),
				'type' => 'range',
				'units' => array(
					array(
						'value'     => '%',
						'intervals' => array(
							'step' => 1,
							'min'  => 10,
							'max'  => 95,
						),
					),
				),
				'default' => array(
					'value' => array(
						'value' => 80,
						'unit'  => '%'
					)
				),
				'css_selector' => array(
					'{{WRAPPER}} .jsf-location-distance__location' => 'flex-basis: {{VALUE}}%;',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'=> 'location_distance_gap',
				'label' => __( 'Gap', 'jet-engine' ),
				'description' => esc_html__( 'Gap between location and distance inputs', 'jet-engine' ),
				'type' => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 1,
							'max'  => 50,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jsf-location-distance' => 'gap: {{VALUE}}{{UNIT}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'location_distance_color',
				'label' => __( 'Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} input.jsf-location-distance__location-input' => 'color: {{VALUE}};',
					'{{WRAPPER}} select.jsf-location-distance__distance' => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'location_distance_background_color',
				'label' => __( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} input.jsf-location-distance__location-input' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} select.jsf-location-distance__distance' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'location_distance_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'separator'      => 'before',
				'css_selector'   => array(
					'{{WRAPPER}} input.jsf-location-distance__location-input' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
					'{{WRAPPER}} select.jsf-location-distance__distance' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'           => 'location_distance_padding',
				'label'        => __( 'Padding', 'jet-engine' ),
				'type'         => 'dimensions',
				'separator'    => 'before',
				'css_selector' => array(
					'{{WRAPPER}} input.jsf-location-distance__location-input' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					'{{WRAPPER}} select.jsf-location-distance__distance' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->end_section();

		$this->controls_manager->start_section(
			'style_controls',
			array(
				'id'    => 'section_location_controls_style',
				'title' => esc_html__( 'Additional Location Controls', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'location_icons_color',
				'label' => __( 'Icons Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jsf-location-distance__location-icon path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'=> 'location_icons_opacity',
				'label' => __( 'Icons Opacity', 'jet-engine' ),
				'type' => 'range',
				'units' => array(
					array(
						'value'     => '%',
						'intervals' => array(
							'step' => 1,
							'min'  => 20,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jsf-location-distance__location-icon' => 'opacity: {{VALUE}}%;',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'location_icons_color_hover',
				'label' => __( 'Icons Hover Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jsf-location-distance__location-control:hover .jsf-location-distance__location-icon path' => 'fill: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'=> 'location_icons_opacity_hover',
				'label' => __( 'Icons Hover Opacity', 'jet-engine' ),
				'type' => 'range',
				'units' => array(
					array(
						'value'     => '%',
						'intervals' => array(
							'step' => 1,
							'min'  => 20,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					'{{WRAPPER}} .jsf-location-distance__location-control:hover .jsf-location-distance__location-icon' => 'opacity: {{VALUE}}%;',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'location_dropdown_color',
				'label' => __( 'Locations Dropdown Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'separator' => 'before',
				'css_selector' => array(
					'{{WRAPPER}} .jsf-location-distance__location-dropdown' => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'location_dropdown_bg_color',
				'label' => __( 'Locations Dropdown Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jsf-location-distance__location-dropdown' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'location_dropdown_color_hover',
				'label' => __( 'Hover Text Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jsf-location-distance__location-dropdown-item:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'location_dropdown_bg_color_hover',
				'label' => __( 'Hover Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					'{{WRAPPER}} .jsf-location-distance__location-dropdown-item:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'           => 'location_dropdown_padding',
				'label'        => __( 'Padding', 'jet-engine' ),
				'type'         => 'dimensions',
				'css_selector' => array(
					'{{WRAPPER}} .jsf-location-distance__location-dropdown-item' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->end_section();

	}

	/**
	 * Return attributes array
	 */
	public function get_attributes() {
		return array(
			'__internalWidgetId' => array(
				'type'    => 'string',
				'default' => '',
			),
			// General
			'filter_id' => array(
				'type'    => 'number',
				'default' => 0,
			),
			'content_provider' => array(
				'type'    => 'string',
				'default' => 'not-selected',
			),
			'apply_type' => array(
				'type'    => 'string',
				'default' => 'ajax',
			),
			'apply_on' => array(
				'type'    => 'string',
				'default' => 'value',
			),
			'placeholder' => array(
				'type'    => 'string',
				'default' => '',
			),
			'geolocation_placeholder' => array(
				'type'    => 'string',
				'default' => '',
			),
			'query_id' => array(
				'type'    => 'string',
				'default' => '',
			),
			'distance_units' => array(
				'type'    => 'string',
				'default' => 'km',
			),
			'distance_list' => array(
				'type'    => 'string',
				'default' => '5,10,25,50,100',
			),
		);
	}

	/**
	 * Return callback
	 */
	public function render_callback( $settings = array() ) {

		if ( empty( $settings['filter_id'] ) ) {
			return $this->is_editor() ? __( 'Please select a filter', 'jet-smart-filters' ) : false;
		}

		if ( empty( $settings['content_provider'] ) || $settings['content_provider'] === 'not-selected' ) {
			return $this->is_editor() ? __( 'Please select a provider', 'jet-smart-filters' ) : false;
		}

		ob_start();

		$base_class = 'jet-smart-filters-' . $this->get_name();
		$filter_id  = absint( $settings['filter_id'] );

		printf(
			'<div class="%1$s jet-filter" data-is-block="jet-smart-filters/%2$s">',
			apply_filters( 'jet-smart-filters/render_filter_template/base_class', $base_class, $filter_id ),
			$this->get_name()
		);

		if ( ! empty( $settings['distance_list'] ) && is_string( $settings['distance_list'] ) ) {
			$settings['distance_list'] = explode( ',', $settings['distance_list'] );
			$settings['distance_list'] = array_map( 'trim', $settings['distance_list'] );
		}

		$render_instance = new Location_Distance_Render();
		$render_instance->render( $settings, $this->get_name() );

		echo '</div>';

		return ob_get_clean();
	}
}
