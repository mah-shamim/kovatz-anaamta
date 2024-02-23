<?php

namespace Jet_Engine\Modules\Profile_Builder\Bricks_Views\Elements;

use Elementor\Controls_Manager;
use Jet_Engine\Bricks_Views\Elements\Base;
use Jet_Engine\Modules\Profile_Builder\Module;

class Profile_Menu extends Base {
	// Element properties
	public $category = 'jetengine'; // Use predefined element category 'general'
	public $name = 'jet-engine-profile-menu'; // Make sure to prefix your elements
	public $icon = 'jet-engine-icon-profile-menu'; // Themify icon font class
	public $css_selector = '.jet-profile-menu__item-link'; // Default CSS selector
	public $scripts = [ 'jetEngineBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Profile Menu', 'jet-engine' );
	}

	// Set builder control groups
	public function set_control_groups() {
		$this->register_jet_control_group(
			'section_general',
			[
				'title' => esc_html__( 'General', 'jet-engine' ),
				'tab'   => 'content',
			]
		);

		$this->register_jet_control_group(
			'section_profile_menu_style',
			[
				'title' => esc_html__( 'General', 'jet-engine' ),
				'tab'   => 'style',
			]
		);
	}

	// Set builder controls
	public function set_controls() {
		$this->start_jet_control_group( 'section_general' );

		$this->register_jet_control(
			'menu_context',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Context', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'account_page' => esc_html__( 'Account', 'jet-engine' ),
					'user_page'    => esc_html__( 'Single User Page', 'jet-engine' ),
				],
				'default' => 'account_page',
			]
		);

		$account_roles = Module::instance()->frontend->menu->get_available_menu_roles( 'account_page' );

		if ( ! empty( $account_roles ) ) {

			$this->register_jet_control(
				'account_roles',
				[
					'tab'      => 'content',
					'label'    => esc_html__( 'Show menu for the role', 'jet-engine' ),
					'type'     => 'select',
					'multiple' => true,
					'options'  => $account_roles,
					'required' => [ 'menu_context', '=', 'account_page' ],
				]
			);
		}

		$user_roles = Module::instance()->frontend->menu->get_available_menu_roles( 'user_page' );

		if ( ! empty( $user_roles ) ) {

			$this->register_jet_control(
				'user_roles',
				[
					'tab'      => 'content',
					'label'    => esc_html__( 'Show menu for the role', 'jet-engine' ),
					'type'     => 'select',
					'multiple' => true,
					'options'  => $user_roles,
					'required' => [ 'menu_context', '=', 'user_page' ],
				]
			);
		}

		$this->register_jet_control(
			'add_main_slug',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Add subpage slug to the first page URL', 'jet-engine' ),
				'type'        => 'checkbox',
				'default'     => false,
				'description' => esc_html__( 'By default, the subpage slug is not added to the URL of the menu\'s first page. If you enable this option subpage slug will be added to all menu page URLs, including the first one', 'jet-engine' ),
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_profile_menu_style' );

		$this->register_jet_control(
			'menu_layout',
			[
				'tab'       => 'style',
				'label'     => esc_html__( 'Direction', 'jet-engine' ),
				'type'      => 'direction',
				'direction' => 'row',
				'css'       => [
					[
						'property' => 'flex-direction',
						'selector' => $this->css_selector(),
					],
				],
			]
		);

		$this->register_jet_control(
			'profile_menu_item_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Gap', 'jet-engine' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '12px',
				'css'     => [
					[
						'property' => 'gap',
						'selector' => $this->css_selector(),
					],
				],
			]
		);

		$this->register_jet_control(
			'profile_menu_item_width',
			[
				'tab'   => 'content',
				'label' => esc_html__( 'Item width', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'width',
						'selector' => $this->css_selector( '__item' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'profile_menu_active_state',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Active state', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'profile_menu_color_active',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => '.is-active ' . $this->css_selector( '__item-link' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'profile_menu_bg_active',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.is-active ' . $this->css_selector( '__item-link' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'profile_menu_border_active',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => '.is-active ' . $this->css_selector( '__item-link' ),
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	// Enqueue element styles and scripts
	public function enqueue_scripts() {
		wp_enqueue_style( 'jet-engine-frontend' );
	}

	// Render element HTML
	public function render() {
		$settings = $this->get_jet_settings();
		$context  = ! empty( $settings['menu_context'] ) ? $settings['menu_context'] : 'account_page';

		if ( 'user_page' === $context ) {
			$roles = ! empty( $settings['user_roles'] ) ? $settings['user_roles'] : false;
		} else {
			$roles = ! empty( $settings['account_roles'] ) ? $settings['account_roles'] : false;
		}

		$add_main_slug = ! empty( $settings['add_main_slug'] ) ? $settings['add_main_slug'] : false;
		$add_main_slug = filter_var( $add_main_slug, FILTER_VALIDATE_BOOLEAN );

		$this->enqueue_scripts();

		// For preview in Editor.
		if ( ! $this->is_frontend ) {
			remove_filter(
				'jet-engine/profile-builder/render/profile-menu-items',
				array( Module::instance()->frontend->menu, 'filter_menu_items' )
			);
		}

		echo "<div {$this->render_attributes( '_root' )}>";
		Module::instance()->frontend->profile_menu( array(
			'menu_context'       => $context,
			'roles'              => $roles,
			'add_main_slug'      => $add_main_slug,
			'menu_layout'        => ! empty( $settings['menu_layout'] ) ? $settings['menu_layout'] : 'horizontal',
			'menu_layout_tablet' => ! empty( $settings['menu_layout_tablet'] ) ? $settings['menu_layout_tablet'] : 'horizontal',
			'menu_layout_mobile' => ! empty( $settings['menu_layout_mobile'] ) ? $settings['menu_layout_mobile'] : 'horizontal',
		) );
		echo "</div>";

	}

	public function css_selector( $mod = null ) {
		return sprintf( '%1$s%2$s', '.jet-profile-menu', $mod );
	}
}