<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Bricks_Base_Checkbox extends Jet_Smart_Filters_Bricks_Base {
	public $checkbox_icon = true;

	/**
	 * Register checkbox settings and style controls. Specific for checkbox and radio widget.
	 *
	 * @return void
	 */
	public function register_checkbox_group() {
		$this->register_jet_control_group(
			'additional_settings',
			[
				'title' => esc_html__( 'Additional Settings', 'jet-smart-filters' ),
				'tab'   => 'content',
			]
		);

		$this->register_jet_control_group(
			'section_items_style',
			[
				'title' => esc_html__( 'Items', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_item_style',
			[
				'title' => esc_html__( 'Item (Checked state)', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_checkbox_style',
			[
				'title'    => esc_html__( 'Checkbox', 'jet-smart-filters' ),
				'tab'      => 'style',
				'required' => [ 'show_decorator', '=', true ],
			]
		);

		$this->register_jet_control_group(
			'search_items_style_section',
			[
				'title'    => esc_html__( 'Search Items', 'jet-smart-filters' ),
				'tab'      => 'style',
				'required' => [ 'search_enabled', '=', true ],
			]
		);

		$this->register_jet_control_group(
			'more_less_style_section',
			[
				'title'    => esc_html__( 'More Less Toggle', 'jet-smart-filters' ),
				'tab'      => 'style',
				'required' => [ 'moreless_enabled', '=', true ],
			]
		);

		$this->register_jet_control_group(
			'dropdown_style_section',
			[
				'title'    => esc_html__( 'Dropdown', 'jet-smart-filters' ),
				'tab'      => 'style',
				'required' => [ 'dropdown_enabled', '=', true ],
			]
		);
	}

	public function register_checkbox_controls( $css_scheme = [] ) {
		$this->start_jet_control_group( 'additional_settings' );

		if ( $this->checkbox_icon ) {
			$this->register_jet_control(
				'show_decorator',
				[
					'tab'     => 'style',
					'label'   => esc_html__( 'Show checkbox', 'jet-smart-filters' ),
					'type'    => 'checkbox',
					'default' => true,
				]
			);
		}

		// Include Additional Filter Settings
		include jet_smart_filters()->plugin_path( 'includes/bricks/elements/common-controls/additional-filter-settings.php' );

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_items_style' );

		$this->register_jet_control(
			'filters_position',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Filters position', 'jet-smart-filters' ),
				'type'    => 'direction',
				'default' => 'column',
				'css'     => [
					[
						'property' => 'flex-direction',
						'selector' => $css_scheme['list-wrapper'] . ', ' . $css_scheme['list-children'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filters_align_main_axis',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Align main axis', 'jet-smart-filters' ),
				'type'     => 'justify-content',
				'css'      => [
					[
						'property' => 'justify-content',
						'selector' => $css_scheme['list-wrapper'] . ', ' . $css_scheme['list-children'],
					],
				],
				'required' => [ 'filters_position', '=', 'row' ],
			]
		);

		$this->register_jet_control(
			'filters_align_cross_axis',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Align cross axis', 'jet-smart-filters' ),
				'type'     => 'align-items',
				'css'      => [
					[
						'property' => 'align-items',
						'selector' => $css_scheme['list-wrapper'] . ', ' . $css_scheme['list-children'],
					],
				],
				'required' => [ 'filters_position', '=', 'column' ],
			]
		);

		$this->register_jet_control(
			'filters_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'gap',
						'selector' => $css_scheme['list-wrapper'] . ', ' . $css_scheme['list-children'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_item_style' );

		$this->register_jet_control(
			'item_checked_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['item-checked'],
					]
				],
			]
		);

		$this->register_jet_control(
			'item_checked_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['item-checked'],
					]
				],
			]
		);

		$this->register_jet_control(
			'item_checked_border_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['item-checked'],
					]
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_checkbox_style' );

		$this->register_jet_control(
			'checkbox_size',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Box size', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '16px',
				'css'     => [
					[
						'property' => 'width',
						'selector' => $css_scheme['checkbox'],
					],
					[
						'property' => 'height',
						'selector' => $css_scheme['checkbox'],
					],
				],
			]
		);

		if ( $this->checkbox_icon ) {
			$this->register_jet_control(
				'checkbox_icon_size',
				[
					'tab'     => 'style',
					'label'   => esc_html__( 'Icon size', 'jet-smart-filters' ),
					'type'    => 'number',
					'units'   => true,
					'default' => '16px',
					'css'     => [
						[
							'property' => 'font-size',
							'selector' => $css_scheme['checkbox-checked-icon'],
						],
					],
				]
			);
		}

		$this->register_jet_control(
			'checkbox_offset',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Gap', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '12px',
				'css'     => [
					[
						'property' => 'margin-right',
						'selector' => $css_scheme['checkbox'],
					],
				],
			]
		);

		$this->register_jet_control(
			'checkbox_align-v',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Vertical alignment', 'jet-smart-filters' ),
				'type'    => 'align-items',
				'default' => 'center',
				'css'     => [
					[
						'property' => 'align-self',
						'selector' => $css_scheme['checkbox'],
					],
				],
			]
		);

		if ( $this->checkbox_icon ) {
			$this->register_jet_control(
				'checkbox_color',
				[
					'tab'   => 'style',
					'label' => esc_html__( 'Icon color', 'jet-smart-filters' ),
					'type'  => 'color',
					'css'   => [
						[
							'property' => 'color',
							'selector' => $css_scheme['checkbox-checked-icon'],
						]
					],
				]
			);

			$this->register_jet_control(
				'checkbox_bg_color',
				[
					'tab'   => 'style',
					'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
					'type'  => 'color',
					'css'   => [
						[
							'property' => 'background-color',
							'selector' => $css_scheme['checkbox'],
						]
					],
				]
			);
		}

		$this->register_jet_control(
			'checkbox_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-smart-filters' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['checkbox'],
					],
				],
			]
		);

		$this->register_jet_control(
			'checkbox_checked_styles',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Checked state', 'jet-smart-filters' ),
			]
		);

		if ( $this->checkbox_icon ) {
			$this->register_jet_control(
				'checkbox_checked_bg_color',
				[
					'tab'   => 'style',
					'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
					'type'  => 'color',
					'css'   => [
						[
							'property' => 'background-color',
							'selector' => $css_scheme['checkbox-checked'],
						]
					],
				]
			);
		}

		$this->register_jet_control(
			'checkbox_checked_border_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['checkbox-checked'],
					]
				],
			]
		);

		$this->end_jet_control_group();

		// Include Additional Filter Settings Style
		include jet_smart_filters()->plugin_path( 'includes/bricks/elements/common-controls/additional-filter-style.php' );
	}

	public function parse_jet_render_attributes( $attrs = [] ) {
		$attrs['show_decorator'] = $attrs['show_decorator'] ?? false;

		return $attrs;
	}
}