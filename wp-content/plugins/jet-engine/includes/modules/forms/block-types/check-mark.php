<?php
namespace Jet_Engine\Forms\Block_Types;

/**
 * Check Mark block type.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Check_Mark block type class
 */
class Check_Mark extends \Jet_Engine_Blocks_Views_Type_Base {

	/**
	 * Returns block name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'check-mark';
	}

	/**
	 * Return attributes array
	 *
	 * @return array
	 */
	public function get_attributes() {
		return apply_filters( 'jet-engine/blocks-views/check-mark/attributes', array(
			'check_mark_icon_default' => array(
				'type' => 'number',
			),
			'check_mark_icon_default_url' => array(
				'type'    => 'string',
				'default' => '',
			),
			'check_mark_icon_checked' => array(
				'type' => 'number',
			),
			'check_mark_icon_checked_url' => array(
				'type'    => 'string',
				'default' => '',
			),
		) );
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
				'id'    => 'check_mark_style',
				'title' => esc_html__( 'Style', 'jet-engine' ),
				'initialOpen' => true,
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'check_mark_box_size',
				'label' => __( 'Box Size', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 5,
							'max'  => 600,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( array( '--default', '--checked' ) ) => 'width: {{VALUE}}px; height: {{VALUE}}px;',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'check_mark_icon_size',
				'label' => __( 'Icon Size', 'jet-engine' ),
				'type'  => 'range',
				'units' => array(
					array(
						'value'     => 'px',
						'intervals' => array(
							'step' => 1,
							'min'  => 9,
							'max'  => 100,
						),
					),
				),
				'css_selector' => array(
					$this->css_selector( array( '--default', '--checked' ) ) => 'font-size: {{VALUE}}px;',
				),
			)
		);

		$this->controls_manager->add_responsive_control(
			array(
				'id'    => 'check_mark_border_radius',
				'label' => esc_html__( 'Border Radius', 'jet-engine' ),
				'type'  => 'dimensions',
				'units' => array( 'px', '%' ),
				'css_selector' => array(
					$this->css_selector( array( '--default', '--checked' ) ) => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
				),
			)
		);

		$this->controls_manager->start_tabs(
			'style_controls',
			array(
				'id'        => 'tabs_check_marks_styles',
				'separator' => 'before',
			)
		);

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'check_mark_styles_default',
				'title' => esc_html__( 'Default', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'check_mark_bg_color',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '--default' ) => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'check_mark_icon_color',
				'label' => esc_html__( 'Icon Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '--default' ) => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'check_mark_border',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'disable_radius' => true,
				'css_selector'   => array(
					$this->css_selector( '--default' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->start_tab(
			'style_controls',
			array(
				'id'    => 'check_mark_styles_checked',
				'title' => esc_html__( 'Checked', 'jet-engine' ),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'check_mark_bg_color_checked',
				'label' => esc_html__( 'Background Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '--checked' ) => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'    => 'check_mark_icon_color_checked',
				'label' => esc_html__( 'Icon Color', 'jet-engine' ),
				'type'  => 'color-picker',
				'css_selector' => array(
					$this->css_selector( '--checked' ) => 'color: {{VALUE}};',
				),
			)
		);

		$this->controls_manager->add_control(
			array(
				'id'             => 'check_mark_border_checked',
				'label'          => esc_html__( 'Border', 'jet-engine' ),
				'type'           => 'border',
				'disable_radius' => true,
				'css_selector'   => array(
					$this->css_selector( '--checked' ) => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-color: {{COLOR}}',
				),
			)
		);

		$this->controls_manager->end_tab();

		$this->controls_manager->end_tabs();

		// check_mark_box_shadow

		$this->controls_manager->end_section();
	}

	/**
	 * Returns CSS selector for nested element
	 *
	 * @param  string|array $el
	 * @return string
	 */
	public function css_selector( $el = null ) {
		if ( ! is_array( $el ) ) {
			return sprintf( '{{WRAPPER}} .jet-form__check-mark%s', $el );
		} else {

			$res = array();
			foreach ( $el as $selector ) {
				$res[] = sprintf( '{{WRAPPER}} .jet-form__check-mark%s', $selector );
			}

			return implode( ', ', $res );
		}
	}

	public function render_callback( $attributes = array() ) {
		$render = $this->get_render_instance( $attributes );
		return $render->get_content();
	}

}
