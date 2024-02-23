<?php
namespace Jet_Engine\Modules\Profile_Builder;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Profile_Menu_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'jet-engine-profile-menu';
	}

	public function get_title() {
		return __( 'Profile Menu', 'jet-engine' );
	}

	public function get_icon() {
		return 'jet-engine-icon-profile-menu';
	}

	public function get_categories() {
		return array( 'jet-listing-elements' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/article-category/jet-engine/?utm_source=jetengine&utm_medium=profile-menu&utm_campaign=need-help';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => __( 'Content', 'jet-engine' ),
			)
		);

		$this->add_control(
			'menu_context',
			array(
				'label'   => __( 'Context', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'account_page',
				'options' => array(
					'account_page' => __( 'Account', 'jet-engine' ),
					'user_page'    => __( 'Single User Page', 'jet-engine' ),
				),
			)
		);

		$account_roles = Module::instance()->frontend->menu->get_available_menu_roles( 'account_page' );
		$user_roles    = Module::instance()->frontend->menu->get_available_menu_roles( 'user_page' );

		if ( ! empty( $account_roles ) ) {
			$this->add_control(
				'account_roles',
				array(
					'label'       => __( 'Show menu for the role', 'jet-engine' ),
					'type'        => Controls_Manager::SELECT2,
					'default'     => '',
					'multiple'    => true,
					'label_block' => true,
					'options'     => $account_roles,
					'condition'   => array(
						'menu_context' => 'account_page',
					),
				)
			);
		} else {
			$this->add_control(
				'account_roles',
				array(
					'type'        => Controls_Manager::HIDDEN,
					'default'     => '',
				)
			);
		}

		if ( ! empty( $user_roles ) ) {
			$this->add_control(
				'user_roles',
				array(
					'label'       => __( 'Show menu for the role', 'jet-engine' ),
					'type'        => Controls_Manager::SELECT2,
					'default'     => '',
					'multiple'    => true,
					'label_block' => true,
					'options'     => $user_roles,
					'condition'   => array(
						'menu_context' => 'user_page',
					),
				)
			);
		} else {
			$this->add_control(
				'user_roles',
				array(
					'type'        => Controls_Manager::HIDDEN,
					'default'     => '',
				)
			);
		}

		$this->add_control(
			'add_main_slug',
			array(
				'label'        => __( 'Add subpage slug to the first page URL', 'jet-engine' ),
				'description'  => __( 'By default, the subpage slug is not added to the URL of the menu\'s first page. If you enable this option subpage slug will be added to all menu page URLs, including the first one', 'jet-engine' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'jet-engine' ),
				'label_off'    => __( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_responsive_control(
			'menu_layout',
			array(
				'label'   => __( 'Menu Layout', 'jet-engine' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => array(
					'horizontal' => __( 'Horizontal', 'jet-engine' ),
					'vertical'   => __( 'Vertical', 'jet-engine' ),
				),
				'selectors_dictionary' => array(
					'horizontal' => 'row',
					'vertical'   => 'column',
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-profile-menu' => 'flex-direction: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_profile_menu_style',
			array(
				'label'      => __( 'General', 'jet-engine' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'profile_menu_alignment_v',
			array(
				'label'   => esc_html__( 'Items Alignment', 'jet-engine' ),
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
						'title' => esc_html__( 'Justify', 'jet-engine' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'label_block' => true,
				'selectors'  => array(
					'{{WRAPPER}} .jet-profile-menu' => 'align-items: {{VALUE}};',
				),
				'condition' => array(
					'menu_layout' => 'vertical',
				),
			)
		);

		$this->add_responsive_control(
			'profile_menu_alignment_h',
			array(
				'label'   => esc_html__( 'Items Alignment', 'jet-engine' ),
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
					'space-between' => array(
						'title' => esc_html__( 'Justify', 'jet-engine' ),
						'icon'  => 'eicon-h-align-stretch',
					),
				),
				'label_block' => true,
				'selectors'  => array(
					'{{WRAPPER}} .jet-profile-menu' => 'justify-content: {{VALUE}};',
				),
				'condition' => array(
					'menu_layout' => 'horizontal',
				),
			)
		);

		$this->add_responsive_control(
			'profile_menu_width',
			array(
				'label'      => esc_html__( 'Item Width', 'jet-engine' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( '%', 'px' ) ),
				'range'      => array(
					'%'  => array(
						'min' => 0,
						'max' => 100,
					),
					'px' => array(
						'min' => 0,
						'max' => 400,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-profile-menu__item' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'profile_menu_text_alignment',
			array(
				'label'   => esc_html__( 'Item Text Alignment', 'jet-engine' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => array(
					'left' => array(
						'title' => esc_html__( 'Start', 'jet-engine' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-engine' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'End', 'jet-engine' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'label_block' => true,
				'separator'  => 'after',
				'selectors'  => array(
					'{{WRAPPER}} .jet-profile-menu__item-link' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'profile_menu_typography',
				'selector'  => '{{WRAPPER}} .jet-profile-menu__item-link',
			)
		);

		$this->start_controls_tabs( 'tabs_profile_menu_style' );

		$this->start_controls_tab(
			'profile_menu_tab_normal',
			array(
				'label' => __( 'Normal', 'jet-engine' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'profile_menu_bg',
				'selector' => '{{WRAPPER}} .jet-profile-menu__item-link',
			)
		);

		$this->add_control(
			'profile_menu_color',
			array(
				'label'  => __( 'Text Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-profile-menu__item-link' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'profile_menu_tab_hover',
			array(
				'label' => __( 'Hover', 'jet-engine' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'profile_menu_bg_hover',
				'selector' => '{{WRAPPER}} .jet-profile-menu__item-link:hover',
			)
		);

		$this->add_control(
			'profile_menu_color_hover',
			array(
				'label'  => __( 'Text Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-profile-menu__item-link:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'profile_menu_color_border_hover',
			array(
				'label'  => __( 'Border Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-profile-menu__item-link:hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'profile_menu_border_border!' => '',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'profile_menu_tab_active',
			array(
				'label' => __( 'Active', 'jet-engine' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'profile_menu_bg_active',
				'selector' => '{{WRAPPER}} .is-active .jet-profile-menu__item-link',
			)
		);

		$this->add_control(
			'profile_menu_color_active',
			array(
				'label'  => __( 'Text Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .is-active .jet-profile-menu__item-link' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'profile_menu_color_border_active',
			array(
				'label'  => __( 'Border Color', 'jet-engine' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .is-active .jet-profile-menu__item-link' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'profile_menu_border_border!' => '',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'profile_menu_padding',
			array(
				'label'      => __( 'Padding', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-profile-menu__item-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'profile_menu_margin',
			array(
				'label'      => __( 'Margin', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%', 'em' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-profile-menu__item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'profile_menu_border',
				'label'          => __( 'Border', 'jet-engine' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} .jet-profile-menu__item-link',
			)
		);

		$this->add_responsive_control(
			'profile_menu_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-engine' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => jet_engine()->elementor_views->add_custom_size_unit( array( 'px', '%' ) ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-profile-menu__item-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'profile_menu_box_shadow',
				'selector' => '{{WRAPPER}} .jet-profile-menu__item-link',
			)
		);

		$this->end_controls_section();

	}

	protected function render() {

		$settings = $this->get_settings();
		$context  = ! empty( $settings['menu_context'] ) ? $settings['menu_context'] : 'account_page';

		if ( 'user_page' === $context ) {
			$roles = ! empty( $settings['user_roles'] ) ? $settings['user_roles'] : false;
		} else {
			$roles = ! empty( $settings['account_roles'] ) ? $settings['account_roles'] : false;
		}

		$add_main_slug = ! empty( $settings['add_main_slug'] ) ? $settings['add_main_slug'] : false;
		$add_main_slug = filter_var( $add_main_slug, FILTER_VALIDATE_BOOLEAN );

		// For preview in Editor.
		if ( \Elementor\Plugin::instance()->editor->is_edit_mode() ) {
			remove_filter(
				'jet-engine/profile-builder/render/profile-menu-items',
				array( Module::instance()->frontend->menu, 'filter_menu_items' )
			);
		}

		Module::instance()->frontend->profile_menu( array(
			'menu_context'       => $context,
			'roles'              => $roles,
			'add_main_slug'      => $add_main_slug,
			'menu_layout'        => ! empty( $settings['menu_layout'] ) ? $settings['menu_layout'] : 'horizontal',
			'menu_layout_tablet' => ! empty( $settings['menu_layout_tablet'] ) ? $settings['menu_layout_tablet'] : 'horizontal',
			'menu_layout_mobile' => ! empty( $settings['menu_layout_mobile'] ) ? $settings['menu_layout_mobile'] : 'horizontal',
		) );
	}

}
