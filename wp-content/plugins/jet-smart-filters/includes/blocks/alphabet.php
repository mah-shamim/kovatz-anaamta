<?php
/**
 * Alphabet Filter
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Block_Alphabet' ) ) {
	/**
	 * Define Jet_Smart_Filters_Block_Alphabet class
	 */
	class Jet_Smart_Filters_Block_Alphabet extends Jet_Smart_Filters_Block_Base {
		/**
		 * Returns block name
		 */
		public function get_name() {

			return 'alphabet';
		}

		public function set_css_scheme() {

			$this->css_scheme = apply_filters(
				'jet-smart-filters/widgets/alphabet/css-scheme',
				array(
					'list-wrapper' => '.jet-alphabet-list__wrapper',
					'list-item'    => '.jet-alphabet-list__row',
					'item'         => '.jet-alphabet-list__item',
					'button'       => '.jet-alphabet-list__button',
				)
			);
		}

		public function add_style_manager_options() {

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'          => 'items_style',
					'initialOpen' => true,
					'title'       => esc_html__( 'Items', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'    => 'items_space_between',
				'type'  => 'range',
				'label' => esc_html__( 'Space Between', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['list-item']    => 'padding: calc({{VALUE}}{{UNIT}}/2);',
					'{{WRAPPER}} ' . $this->css_scheme['list-wrapper'] => 'margin: calc(-{{VALUE}}{{UNIT}}/2);',
				],
				'attributes' => [
					'default' => [
						'value' => 10,
						'unit' => 'px'
					]
				],
				'units' => [
					[
						'value' => 'px',
						'intervals' => [
							'step' => 1,
							'min'  => 0,
							'max'  => 50,
						]
					],
				],
			]);

			$this->controls_manager->add_control([
				'id'        => 'items_alignment',
				'type'      => 'choose',
				'label'     => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'separator' => 'before',
				'options'   =>[
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-alignleft',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-aligncenter',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'jet-smart-filters' ),
						'icon'  => 'dashicons-editor-alignright',
					),
				],
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['list-wrapper'] => 'justify-content: {{VALUE}};',
				],
				'attributes' => [
					'default' => [
						'value' => 'left',
					]
				]
			]);

			$this->controls_manager->end_section();

			$this->controls_manager->start_section(
				'style_controls',
				[
					'id'    => 'item_style',
					'title' => esc_html__( 'Item', 'jet-smart-filters' )
				]
			);

			$this->controls_manager->add_control([
				'id'        => 'item_min_width',
				'type'      => 'range',
				'label'     => esc_html__( 'Minimal Width', 'jet-smart-filters' ),
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['button'] => 'width: {{VALUE}}{{UNIT}};',
				],
				'attributes' => [
					'default' => [
						'value' => 10,
						'unit' => 'px'
					]
				],
				'units' => [
					[
						'value' => 'px',
						'intervals' => [
							'step' => 1,
							'min'  => 0,
							'max'  => 500,
						]
					],
				],
			]);

			$this->controls_manager->add_control([
				'id'           => 'item_typography',
				'type'         => 'typography',
				'separator'    => 'before',
				'css_selector' => [
					'{{WRAPPER}} ' . $this->css_scheme['button'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
				],
			]);

			$this->controls_manager->start_tabs(
				'style_controls',
				[
					'id' => 'item_style_tabs',
					'separator'  => 'both',
				]
			);

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'item_normal_styles',
					'title' => esc_html__( 'Normal', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'       => 'item_normal_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Normal Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['button'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'item_normal_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Normal Background Color', 'jet-smart-filters' ),
				'css_selector' => array(
					'{{WRAPPER}} ' . $this->css_scheme['button'] => 'background-color: {{VALUE}}',
				),
				'attributes' => [
					'default' => [
						'value' => ''
					],
				],
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->start_tab(
				'style_controls',
				[
					'id'    => 'item_checked_styles',
					'title' => esc_html__( 'Checked', 'jet-smart-filters' ),
				]
			);

			$this->controls_manager->add_control([
				'id'       => 'item_checked_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Checked Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'attributes' => [
					'default' => [
						'value' => ''
					],
				],
				'css_selector' => array(
					'{{WRAPPER}} .jet-alphabet-list__input:checked ~ ' . $this->css_scheme['button'] => 'color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'item_checked_background_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Checked Background Color', 'jet-smart-filters' ),
				'separator'  => 'after',
				'attributes' => [
					'default' => [
						'value' => ''
					],
				],
				'css_selector' => array(
					'{{WRAPPER}} .jet-alphabet-list__input:checked ~ ' . $this->css_scheme['button'] => 'background-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->add_control([
				'id'       => 'item_checked_border_color',
				'type'     => 'color-picker',
				'label'     => esc_html__( 'Checked Border Color', 'jet-smart-filters' ),
				'attributes' => [
					'default' => [
						'value' => ''
					],
				],
				'css_selector' => array(
					'{{WRAPPER}} .jet-alphabet-list__input:checked ~ ' . $this->css_scheme['button'] => 'border-color: {{VALUE}}',
				),
			]);

			$this->controls_manager->end_tab();

			$this->controls_manager->end_tabs();

			$this->controls_manager->add_control([
				'id'         => 'item_padding',
				'type'       => 'dimensions',
				'label'      => esc_html__( 'Padding', 'jet-smart-filters' ),
				'units'      => array( 'px', '%' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['button'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
				'separator'  => 'after',
			]);

			$this->controls_manager->add_control([
				'id'         => 'item_border',
				'type'       => 'border',
				'label'       => esc_html__( 'Border', 'jet-smart-filters' ),
				'css_selector'  => array(
					'{{WRAPPER}} ' . $this->css_scheme['button'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
				),
			]);

			$this->controls_manager->end_section();
		}
	}
}
