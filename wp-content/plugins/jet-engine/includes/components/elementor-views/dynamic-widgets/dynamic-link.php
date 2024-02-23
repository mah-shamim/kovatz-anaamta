<?php
namespace Elementor;

use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Listing_Dynamic_Link_Widget extends \Jet_Listing_Dynamic_Widget {

	public function get_name() {
		return 'jet-listing-dynamic-link';
	}

	public function get_title() {
		return __( 'Dynamic Link', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-dynamic-link';
	}

	public function get_categories() {
		return array( 'jet-listing-elements' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetengine-dynamic-link-widget-overview/?utm_source=jetengine&utm_medium=dynamic-link&utm_campaign=need-help';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-engine' ),
			)
		);

		$meta_fields = $this->get_meta_fields_for_post_type();

		$this->add_control(
			'dynamic_link_source',
			array(
				'label'   => __( 'Source', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '_permalink',
				'groups'  => $meta_fields,
			)
		);

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'plain' );

			if ( ! empty( $options_pages_select ) ) {
				$this->add_control(
					'dynamic_link_option',
					array(
						'label'     => __( 'Option', 'jet-engine' ),
						'type'      => Controls_Manager::SELECT,
						'default'   => '',
						'groups'    => $options_pages_select,
						'condition' => array(
							'dynamic_link_source' => 'options_page',
						),
					)
				);
			}

		}

		/**
		 * Add 3rd-party controls for sources
		 */
		do_action( 'jet-engine/listings/dynamic-link/source-controls', $this );

		$this->add_control(
			'dynamic_link_source_custom',
			array(
				'label'       => __( 'Custom meta field/repeater key', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => __( 'Note: this field will override Meta Field value', 'jet-engine' ),
				'condition'   => array(
					'dynamic_link_source!' => 'delete_post_link',
				),
			)
		);

		$this->add_control(
			'delete_link_dialog',
			array(
				'label'       => __( 'Confirm deletion message', 'jet-engine' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => __( 'Are you sure you want to delete this post?', 'jet-engine' ),
				'label_block' => true,
				'description' => __( 'Only users with appropriate permissions can delete posts', 'jet-engine' ),
				'condition'   => array(
					'dynamic_link_source' => 'delete_post_link',
				),
			)
		);

		$this->add_control(
			'delete_link_redirect',
			array(
				'label'       => __( 'Redirect after delete', 'jet-engine' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'description' => __( 'If empty will redirect to home page. Use the %current_page_url% macro to redirect to the current page.', 'jet-engine' ),
				'dynamic'     => array(
					'active'     => true,
					'categories' => array(
						\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
						\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
					),
				),
				'condition'   => array(
					'dynamic_link_source' => 'delete_post_link',
				),
			)
		);

		$this->add_control(
			'delete_link_type',
			array(
				'label'   => __( 'Delete post type', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'trash',
				'options' => array(
					'trash' => __( 'Move to trash', 'jet-engine' ),
					'permanently' => __( 'Delete permanently', 'jet-engine' ),
				),
				'condition'   => array(
					'dynamic_link_source' => 'delete_post_link',
				),
			)
		);

		$this->add_control(
			'link_label',
			array(
				'label'       => __( 'Label', 'jet-engine' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'Read More', 'jet-engine' ),
				'description' => __( 'You can use JetEngine/Macros dynamic tag to add macros', 'jet-engine' ),
				'separator'   => 'before',
				'dynamic'     => array(
					'active' => true,
					'categories' => array(
						\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
						\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
					),
				),
			)
		);

		$this->add_control(
			'add_query_args',
			array(
				'label'        => esc_html__( 'Add Query Arguments', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'query_args',
			array(
				'label'       => __( 'Query Arguments', 'jet-engine' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => '_post_id=%current_id%',
				'description' => __( 'One argument per line. Separate key and value with "="', 'jet-engine' ),
				'condition'   => array(
					'add_query_args' => 'yes',
				),
			)
		);

		$this->add_control(
			'url_prefix',
			array(
				'label'       => __( 'URL Prefix', 'jet-engine' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __( 'Add prefix to the URL, for example tel:, mailto: etc.', 'jet-engine' ),
			)
		);

		$this->add_control(
			'url_anchor',
			array(
				'label'       => __( 'URL Anchor', 'jet-engine' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'description' => __( 'Add anchor to the URL. Without #.', 'jet-engine' ),
				'dynamic'     => array(
					'active' => true,
					'categories' => array(
						\Jet_Engine_Dynamic_Tags_Module::TEXT_CATEGORY,
						\Jet_Engine_Dynamic_Tags_Module::JET_MACROS_CATEGORY,
					),
				),
			)
		);

		if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '2.6.0', '>=' ) ) {
			$this->add_control(
				'selected_link_icon',
				array(
					'label'            => __( 'Field Icon', 'jet-engine' ),
					'type'             => Controls_Manager::ICONS,
					'label_block'      => true,
					'fa4compatibility' => 'field_icon',
					'separator'        => 'before',

				)
			);
		} else {
			$this->add_control(
				'link_icon',
				array(
					'label'       => __( 'Field Icon', 'jet-engine' ),
					'type'        => Controls_Manager::ICON,
					'label_block' => true,
					'file'        => '',
					'default'     => '',
					'separator'   => 'before',
				)
			);
		}

		if ( ! $this->prevent_wrap() ) {
			$this->add_control(
				'link_wrapper_tag',
				array(
					'label'   => __( 'HTML wrapper', 'jet-engine' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'div',
					'options' => array(
						'div'  => 'DIV',
						'h1'   => 'H1',
						'h2'   => 'H2',
						'h3'   => 'H3',
						'h4'   => 'H4',
						'h5'   => 'H5',
						'h6'   => 'H6',
						'span' => 'SPAN',
					),
				)
			);
		}

		$this->add_control(
			'open_in_new',
			array(
				'label'        => esc_html__( 'Open in new window', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'rel_attr',
			array(
				'label'   => __( 'Add "rel" attr', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => \Jet_Engine_Tools::get_rel_attr_options(),
			)
		);

		$this->add_control(
			'aria_label_attr',
			array(
				'label'   => __( 'Aria label attr', 'jet-engine' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => array(
					'active' => true
				),
			)
		);

		$this->add_responsive_control(
			'link_alignment',
			array(
				'label'   => __( 'Alignment', 'jet-engine' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'flex-start',
				'options' => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-engine' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-engine' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-engine' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
					'stretch' => array(
						'title' => __( 'Fullwidth', 'jet-engine' ),
						'icon' => 'eicon-h-align-stretch',
					),
				),
				'selectors'  => array(
					$this->css_selector( '__link' ) => 'align-self: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'hide_if_empty',
			array(
				'label'        => esc_html__( 'Hide if value is empty', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'object_context',
			array(
				'label'     => __( 'Context', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => 'default_object',
				'options'   => jet_engine()->listings->allowed_context_list(),
				'separator' => 'before',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_link_style',
			array(
				'label'      => __( 'General', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'link_typography',
				'selector' => $this->css_selector( '__link' ),
			)
		);

		$this->start_controls_tabs( 'tabs_form_submit_style' );

		$this->start_controls_tab(
			'dynamic_link_normal',
			array(
				'label' => __( 'Normal', 'jet-engine' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'link_bg',
				'selector' => $this->css_selector( '__link' ),
			)
		);

		$this->add_control(
			'link_color',
			array(
				'label'  => __( 'Text Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__link' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'link_icon_color',
			array(
				'label'  => __( 'Icon Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__icon' ) => 'color: {{VALUE}}',
					$this->css_selector( '__icon :is(svg, path)' ) => 'fill: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'dynamic_link_hover',
			array(
				'label' => __( 'Hover', 'jet-engine' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'link_bg_hover',
				'selector' => $this->css_selector( '__link:hover' ),
			)
		);

		$this->add_control(
			'link_color_hover',
			array(
				'label'  => __( 'Text Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__link:hover' ) => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'link_icon_color_hover',
			array(
				'label'  => __( 'Icon Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					$this->css_selector( '__link:hover .jet-listing-dynamic-link__icon' ) => 'color: {{VALUE}}',
					$this->css_selector( '__link:hover .jet-listing-dynamic-link__icon :is(svg, path)' ) => 'fill: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'link_hover_border_color',
			array(
				'label' => __( 'Border Color', 'jet-engine' ),
				'type' => Controls_Manager::COLOR,
				'condition' => array(
					'link_border_border!' => '',
				),
				'selectors' => array(
					$this->css_selector( '__link:hover' ) => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		do_action( 'jet-engine/listings/dynamic-link/style-tabs', $this );

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'link_padding',
			array(
				'label'      => __( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'separator'  => 'before',
				'selectors'  => array(
					$this->css_selector( '__link' ) => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'link_margin',
			array(
				'label'      => __( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					$this->css_selector( '__link' ) => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'link_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => $this->css_selector( '__link' ),
			)
		);

		$this->add_responsive_control(
			'link_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					$this->css_selector( '__link' ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'link_box_shadow',
				'selector' => $this->css_selector( '__link' ),
			)
		);

		$low_order = $this->prevent_wrap() ? -1 : 1;

		$this->add_control(
			'link_icon_position',
			array(
				'label'   => __( 'Icon Position', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => $low_order,
				'options' => array(
					$low_order => __( 'Before Label', 'jet-engine' ),
					3 => __( 'After Label', 'jet-engine' )
				),
				'selectors'  => array(
					$this->css_selector( '__icon' ) => 'order: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'link_icon_orientation',
			array(
				'label'   => __( 'Icon Orientation', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'row',
				'options' => array(
					'row'    => __( 'Horizontal', 'jet-engine' ),
					'column' => __( 'Vertical', 'jet-engine' ),
				),
				'selectors'  => array(
					$this->css_selector( '__link' ) => 'flex-direction: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'link_icon_size',
			array(
				'label'      => __( 'Icon Size', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors'  => array(
					$this->css_selector( '__icon' ) => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'link_icon_gap_right',
			array(
				'label'      => __( 'Icon Gap', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'body:not(.rtl) ' . $this->css_selector( '__icon' ) => 'margin-right: {{SIZE}}{{UNIT}};',
					'body.rtl ' . $this->css_selector( '__icon' ) => 'margin-left: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'link_icon_position' => array( $low_order, '' . $low_order ),
					'link_icon_orientation!' => 'column',
				),
			)
		);

		$this->add_responsive_control(
			'link_icon_gap_left',
			array(
				'label'      => __( 'Icon Gap', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					'body:not(.rtl) ' . $this->css_selector( '__icon' ) => 'margin-left: {{SIZE}}{{UNIT}};',
					'body.rtl ' . $this->css_selector( '__icon' ) => 'margin-right: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'link_icon_position' => array( '2', 2, '3', 3 ),
					'link_icon_orientation!' => 'column',
				),
			)
		);

		$this->add_responsive_control(
			'link_icon_gap_top',
			array(
				'label'      => __( 'Icon Gap', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					$this->css_selector( '__icon' ) => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'link_icon_position' => array( $low_order, '' . $low_order ),
					'link_icon_orientation' => 'column',
				),
			)
		);

		$this->add_responsive_control(
			'link_icon_gap_bottom',
			array(
				'label'      => __( 'Icon Gap', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors'  => array(
					$this->css_selector( '__icon' ) => 'margin-top: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'link_icon_position' => array( '2', 2, '3', 3 ),
					'link_icon_orientation' => 'column',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Returns CSS selector for nested element
	 *
	 * @param  [type] $el [description]
	 * @return [type]     [description]
	 */
	public function css_selector( $el = null ) {
		return sprintf( '{{WRAPPER}} .%1$s%2$s', $this->get_name(), $el );
	}

	/**
	 * Get meta fields for post type
	 *
	 * @return array
	 */
	public function get_meta_fields_for_post_type() {

		$default = array(
			'label'   => __( 'General', 'jet-engine' ),
			'options' => apply_filters( 'jet-engine/elementor-view/dynamic-link/generel-options', array(
				'_permalink' => __( 'Permalink', 'jet-engine' ),
				'delete_post_link' => __( 'Delete current post link', 'jet-engine' ),
			) ),
		);

		$result      = array();
		$meta_fields = array();

		if ( jet_engine()->options_pages ) {
			$default['options']['options_page'] = __( 'Options', 'jet-engine' );
		}

		if ( jet_engine()->modules->is_module_active( 'profile-builder' ) ) {
			$default['options']['profile_page'] = __( 'Profile Page', 'jet-engine' );
		}

		if ( jet_engine()->meta_boxes ) {
			$meta_fields = jet_engine()->meta_boxes->get_fields_for_select( 'plain' );
		}

		return apply_filters(
			'jet-engine/listings/dynamic-link/fields',
			array_merge( array( $default ), $meta_fields )
		);

	}

	protected function render() {
		jet_engine()->listings->render_item( 'dynamic-link', $this->get_settings_for_display() );
	}

}
