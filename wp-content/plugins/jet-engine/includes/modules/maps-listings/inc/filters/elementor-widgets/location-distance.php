<?php
namespace Jet_Engine\Modules\Maps_Listings\Filters\Elementor_Widgets;

use \Elementor\Controls_Manager;
use \Elementor\Repeater;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Core\Schemes\Typography as Scheme_Typography;

use Jet_Engine\Modules\Maps_Listings\Filters\Types\Location_Distance_Render;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Location_Distance extends \Elementor\Jet_Smart_Filters_Base_Widget {

	public function get_name() {
		return 'jet-smart-filters-location-distance';
	}

	public function get_title() {
		return __( 'Location & distance', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-location-distance';
	}

	public function get_help_url() {}

	protected function register_controls() {

		$css_scheme = [];

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-engine' ),
			)
		);

		$query_builder_link = admin_url( 'admin.php?page=jet-engine-query' );

		$this->add_control(
			'query_notice',
			array(
				'label' => '',
				'type' => Controls_Manager::RAW_HTML,
				'raw' => sprintf( __( '<b>Please note!</b><br><div class="elementor-control-field-description">This filter is compatible only with queries from <a href="%s" target="_blank">JetEngine Query Builder</a>. ALso you need to set up <a href="https://crocoblock.com/knowledge-base/jetsmartfilters/location-distance-filter-overview/" target="_blank">Geo Query</a> in your query settings to meke filter to work correctly.</div>', 'jet-engine' ), $query_builder_link ),
			)
		);

		$this->add_control(
			'filter_id',
			$this->get_filter_control_settings()
		);

		$this->add_control(
			'content_provider',
			array(
				'label'   => __( 'This filter for', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => jet_smart_filters()->data->content_providers(),
			)
		);

		$this->add_control(
			'epro_posts_notice',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => __( 'Please set <b>jet-engine</b> into Query ID option of Posts widget you want to filter', 'jet-engine' ),
				'condition' => array(
					'content_provider' => array( 'epro-posts', 'epro-portfolio' ),
				),
			)
		);

		$this->add_control(
			'apply_type',
			array(
				'label'   => __( 'Apply type', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'ajax',
				'options' => array(
					'ajax'   => __( 'AJAX', 'jet-engine' ),
					'reload' => __( 'Page reload', 'jet-engine' ),
					'mixed'  => __( 'Mixed', 'jet-engine' ),
				),
			)
		);

		$this->add_control(
			'apply_on',
			array(
				'label'     => __( 'Apply on', 'jet-engine' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'value',
				'options'   => array(
					'value'  => __( 'Value change', 'jet-engine' ),
					'submit' => __( 'Click on apply button', 'jet-engine' ),
				),
				'condition' => array(
					'apply_type' => array( 'ajax', 'mixed' ),
				),
			)
		);
	
		$this->add_control(
			'placeholder',
			array(
				'label'       => __( 'Placeholder', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Enter your location...', 'jet-engine' ),
				'default'     => __( 'Enter your location...', 'jet-engine' ),
				'description' => __( 'Placeholder text for the location input', 'jet-engine' ),
			)
		);

		$this->add_control(
			'geolocation_placeholder',
			array(
				'label'       => __( 'Text for user geolocation control', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'placeholder' => __( 'Your current location', 'jet-engine' ),
				'default'     => __( 'Your current location', 'jet-engine' ),
				'description' => __( 'This text used for User Geolocation icon tooltip and as location input value, when User Geolocation is used', 'jet-engine' ),
			)
		);

		$this->add_control(
			'query_id',
			array(
				'label'       => esc_html__( 'Query ID', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'description' => __( 'Set unique query ID if you use multiple widgets of same provider on the page. Same ID you need to set for filtered widget.', 'jet-engine' ),
			)
		);

		// Include Additional Providers Settings
		include jet_smart_filters()->plugin_path( 'includes/widgets/common-controls/additional-providers.php' );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_distance_list',
			array(
				'label' => __( 'Distance List', 'jet-engine' ),
			)
		);

		$this->add_control(
			'distance_units',
			array(
				'label'     => __( 'Distance Units', 'jet-engine' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'km',
				'options'   => array(
					'km' => __( 'Kilometers', 'jet-engine' ),
					'mi' => __( 'Miles', 'jet-engine' ),
				),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'distance',
			array(
				'label'   => __( 'Distance', 'jet-engine' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 1000,
				'default' => 50
			)
		);

		$this->add_control(
			'distance_list',
			array(
				'label' => __( 'Distance List', 'jet-engine' ),
				'type'  => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => array(
					array(
						'distance' => 5
					),
					array(
						'distance' => 10
					),
					array(
						'distance' => 25
					),
					array(
						'distance' => 50
					),
					array(
						'distance' => 100
					),
				),
				'title_field' => '{{{ distance }}}'
			)
		);

		$this->end_controls_section();

		$this->register_filter_style_controls();

	}

	public function register_filter_style_controls() {

		$this->start_controls_section(
			'section_content_style',
			array(
				'label'      => esc_html__( 'Location & Distance Inputs', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'location_width',
			array(
				'label'       => esc_html__( 'Location Input Width', 'jet-engine' ),
				'description' => esc_html__( 'Distance control will automatically fill the rest of space in the line', 'jet-engine' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array(
					'%',
				),
				'range'      => array(
					'%'  => array(
						'min' => 10,
						'max' => 95,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 80,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jsf-location-distance__location' => 'flex-basis: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'location_distance_gap',
			array(
				'label'       => esc_html__( 'Gap', 'jet-engine' ),
				'description' => esc_html__( 'Gap between location and distance inputs', 'jet-engine' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array(
					'px',
				),
				'range'      => array(
					'px'  => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jsf-location-distance' => 'gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'location_distance_typography',
				'selector' => '{{WRAPPER}} input.jsf-location-distance__location-input, {{WRAPPER}} select.jsf-location-distance__distance',
			)
		);

		$this->add_control(
			'location_distance_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} input.jsf-location-distance__location-input' => 'color: {{VALUE}};',
					'{{WRAPPER}} select.jsf-location-distance__distance' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'location_distance_background_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-engine' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} input.jsf-location-distance__location-input' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} select.jsf-location-distance__distance' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'location_distance_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} input.jsf-location-distance__location-input, {{WRAPPER}} select.jsf-location-distance__distance',
				'separator'   => 'before'

			)
		);

		$this->add_control(
			'location_distance_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} input.jsf-location-distance__location-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} select.jsf-location-distance__distance' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'location_distance_box_shadow',
				'selector' => '{{WRAPPER}} input.jsf-location-distance__location-input, {{WRAPPER}} select.jsf-location-distance__distance',
			)
		);

		$this->add_responsive_control(
			'location_distance_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} input.jsf-location-distance__location-input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} select.jsf-location-distance__distance' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before'
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_location_controls_style',
			array(
				'label'      => esc_html__( 'Additional Location Controls', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'location_control_icons',
			array(
				'label'     => esc_html__( 'Icons', 'jet-smart-filters' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->start_controls_tabs( 'tabs_location_icons' );

		$this->start_controls_tab(
			'location_icons_default',
			array(
				'label' => esc_html__( 'Default', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'location_icons_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jsf-location-distance__location-icon path' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'location_icons_opacity',
			array(
				'label'       => esc_html__( 'Opacity', 'jet-engine' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array(
					'%',
				),
				'range'      => array(
					'%'  => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 50,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jsf-location-distance__location-icon' => 'opacity: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'location_icons_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-smart-filters' ),
			)
		);

		$this->add_control(
			'location_icons_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jsf-location-distance__location-control:hover .jsf-location-distance__location-icon path' => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'location_icons_opacity_hover',
			array(
				'label'       => esc_html__( 'Opacity', 'jet-engine' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array(
					'%',
				),
				'range'      => array(
					'%'  => array(
						'min' => 20,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} .jsf-location-distance__location-control:hover .jsf-location-distance__location-icon' => 'opacity: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'locations_dropdown',
			array(
				'label'     => esc_html__( 'Locations Dropdown', 'jet-smart-filters' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'location_dropdown_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jsf-location-distance__location-dropdown' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'location_dropdown_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jsf-location-distance__location-dropdown' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'location_dropdown_color_hover',
			array(
				'label'     => esc_html__( 'Hover Text Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .jsf-location-distance__location-dropdown-item:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'location_dropdown_bg_color_hover',
			array(
				'label'     => esc_html__( 'Hover Background Color', 'jet-smart-filters' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jsf-location-distance__location-dropdown-item:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'location_dropdown_box_shadow',
				'selector' => '{{WRAPPER}} .jsf-location-distance__location-dropdown',
			)
		);

		$this->add_responsive_control(
			'location_dropdown_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .jsf-location-distance__location-dropdown-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before'
			)
		);

		$this->end_controls_section();

	}


	protected function render() {

		$render_instance = new Location_Distance_Render();
		$render_instance->render( $this->get_settings(), $this->get_name() );

	}

}
