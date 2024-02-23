<?php

namespace Jet_Engine\Modules\Data_Stores\Bricks_Views;

use Bricks\Element;
use Jet_Engine\Bricks_Views\Elements\Base;
use Jet_Engine\Bricks_Views\Helpers\Options_Converter;
use Jet_Engine\Modules\Data_Stores\Module as Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Button extends Base {
	// Element properties
	public $category = 'jetengine'; // Use predefined element category 'general'
	public $name = 'jet-engine-data-store-button'; // Make sure to prefix your elements
	public $icon = 'jet-engine-icon-data-store-button'; // Themify icon font class
	public $css_selector = '.jet-data-store-link'; // Default CSS selector
	public $scripts = [ 'jetEngineBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'data-store-button';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Data Store Button', 'jet-engine' );
	}

	// Set builder control groups
	public function set_control_groups() {

		$this->register_jet_control_group(
			'section_content',
			[
				'title' => esc_html__( 'General', 'jet-engine' ),
				'tab'   => 'content',
			]
		);

		$this->register_jet_control_group(
			'section_button_style',
			[
				'title' => esc_html__( 'Button', 'jet-engine' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_icon_style',
			[
				'title' => esc_html__( 'Icon', 'jet-engine' ),
				'tab'   => 'style',
			]
		);
	}

	// Set builder controls
	public function set_controls() {

		$this->start_jet_control_group( 'section_content' );

		$stores = Module::instance()->elementor_integration->get_store_options();

		$this->register_jet_control(
			'store',
			[
				'tab'        => 'content',
				'label'      => esc_html__( 'Select store', 'jet-engine' ),
				'type'       => 'select',
				'options'    => Options_Converter::convert_select_groups_to_options( $stores ),
				'searchable' => true,
			]
		);

		$this->register_jet_control(
			'label',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Label', 'jet-engine' ),
				'type'    => 'text',
				'default' => esc_html__( 'Add to store', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'icon',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Icon', 'jet-engine' ),
				'type'    => 'icon',
				'css'     => [
					[
						'selector' => $this->css_selector( '__icon svg' ), // Use to target SVG file
					],
				],
			]
		);

		$this->register_jet_control(
			'synch_grid',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Reload listing grid on success', 'jet-engine' ),
				'type'        => 'checkbox',
				'default'     => false,
				'description' => esc_html__( 'You can use this option to reload listing grid with current Store posts on success', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'synch_grid_id',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Listing grid ID', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Here you need to set listing ID to reload. The same ID must be set in the Advanced settings of selected listing', 'jet-engine' ),
				'required'    => [ 'synch_grid', '=', true ],
			]
		);

		$this->register_jet_control(
			'added_to_store',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'After Added to Store', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'action_after_added',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Action after an item added to store', 'jet-engine' ),
				'type'    => 'select',
				'default' => 'remove_from_store',
				'options' => [
					'remove_from_store' => esc_html__( 'Remove from store button', 'jet-engine' ),
					'switch_status'     => esc_html__( 'Switch button status', 'jet-engine' ),
					'hide'              => esc_html__( 'Hide button', 'jet-engine' ),
				],
			]
		);

		$this->register_jet_control(
			'added_to_store_label',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Label after added to store', 'jet-engine' ),
				'type'     => 'text',
				'required' => [ 'action_after_added', '=', [ 'switch_status', 'remove_from_store' ] ],
			]
		);

		$this->register_jet_control(
			'added_to_store_icon',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Icon after added to store', 'jet-engine' ),
				'type'     => 'icon',
				'required' => [ 'action_after_added', '=', [ 'switch_status', 'remove_from_store' ] ],
			]
		);

		$this->register_jet_control(
			'added_to_store_url',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'URL after added to store', 'jet-engine' ),
				'type'     => 'text',
				'required' => [ 'action_after_added', '=', 'switch_status' ],
			]
		);

		$this->register_jet_control(
			'open_in_new',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Open in new window', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [ 'action_after_added', '=', 'switch_status' ],
			]
		);

		$this->register_jet_control(
			'rel_attr',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Add "rel" attr', 'jet-engine' ),
				'type'        => 'select',
				'options'     => \Jet_Engine_Tools::get_rel_attr_options(),
				'placeholder' => esc_html__( 'No', 'jet-engine' ),
				'required'    => [ 'action_after_added', '=', 'switch_status' ],
			]
		);

		$this->register_jet_control(
			'context_separator',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Context', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'object_context',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Context', 'jet-engine' ),
				'type'    => 'select',
				'options' => jet_engine()->listings->allowed_context_list(),
				'default' => 'default_object',
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_button_style' );

		$this->register_jet_control(
			'button_in_store',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'In Store', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'button_color_in_store',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Color', 'jet-engine' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'color',
						'selector' => $this->css_selector( '.in-store' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'button_bg_in_store',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Background color', 'jet-engine' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'background-color',
						'selector' => $this->css_selector( '.in-store' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'button_border_color_in_store',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Border color', 'jet-engine' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'color',
						'selector' => $this->css_selector( '.in-store' ),
					],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_icon_style' );

		$this->register_jet_control(
			'button_icon_direction',
			[
				'tab'       => 'style',
				'label'     => esc_html__( 'Direction', 'jet-engine' ),
				'type'      => 'direction',
				'direction' => 'row',
				'css'       => [
					[
						'property' => 'flex-direction',
					],
				],
			]
		);

		$this->register_jet_control(
			'button_icon_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Icon color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $this->css_selector( '__icon' ),
					],
					[
						'property' => 'fill',
						'selector' => $this->css_selector( '__icon :is(svg)' ) . ', ' . $this->css_selector( '__icon :is(path)' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'button_icon_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Icon size', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'font-size',
						'selector' => $this->css_selector( '__icon' ),
					],
				],
			]
		);

		$this->register_jet_control(
			'button_icon_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Icon gap', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'gap',
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

		// STEP: Store field is empty: Show placeholder text
		if ( ! $this->get_jet_settings( 'store' ) ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Please select store to show.', 'jet-engine' )
				]
			);
		}

		$this->enqueue_scripts();

		$render = $this->get_jet_render_instance();

		// STEP: Data Store renderer class not found: Show placeholder text
		if ( ! $render ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Data Store renderer class not found', 'jet-engine' )
				]
			);
		}

		echo "<div {$this->render_attributes( '_root' )}>";
		$render->render_content();
		echo "</div>";

	}

	public function parse_jet_render_attributes( $attrs = [] ) {

		$attrs['icon']                = isset( $attrs['icon'] ) ? Element::render_icon( $attrs['icon'] ) : null;
		$attrs['added_to_store_icon'] = isset( $attrs['added_to_store_icon'] ) ? Element::render_icon( $attrs['added_to_store_icon'] ) : null;

		return $attrs;
	}

	public function css_selector( $mod = null ) {
		return sprintf( '%1$s%2$s', $this->css_selector, $mod );
	}
}