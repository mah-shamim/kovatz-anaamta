<?php
namespace Elementor;
use Elementor\Modules\DynamicTags\Module as TagsModule;
class ElementsKit_Extend_Advanced_Tooltip {
	private $url   = '';
	public function __construct() {
		$this->url = \ElementsKit::plugin_url() . 'modules/advanced-tooltip/';

		add_action( 'elementor/element/common/_section_style/after_section_end', [$this, 'register_controls'], 6 );
		add_action( 'elementor/widget/before_render_content', [$this, 'before_content'] );
	}

	public function register_controls( Controls_Stack $element ) {
		$element->start_controls_section(
			'ekit_adv_tooltip',
			[
				'label'	=> esc_html__('ElementsKit Advanced Tooltip', 'elementskit'),
				'tab'	=> Controls_Manager::TAB_ADVANCED,
			]
		);
			$element->add_control(
				'ekit_adv_tooltip_enable',
				[
					'label'					=> esc_html__('Enable Advanced Tooltip', 'elementskit'),
					'type'					=> Controls_Manager::SWITCHER,
					'frontend_available'	=> true,
				]
			);

			$element->start_controls_tabs(
				'ekit_adv_tooltip_tabs',
				[
					'separator'	=> 'before',
					'condition'	=> [
						'ekit_adv_tooltip_enable!' => '',
					],
				]
			);
				$element->start_controls_tab(
					'ekit_adv_tooltip_tab_settings',
					[
						'label'		=> esc_html__('Settings', 'elementskit'),
					]
				);
					$element->add_control(
						'ekit_adv_tooltip_content',
						[
							'label'					=> esc_html__('Content', 'elementskit'),
							'type'					=> Controls_Manager::TEXTAREA,
							'default'				=> esc_html__('Tooltip Content.', 'elementskit'),
							'frontend_available'	=> true,
						]
					);

					$element->add_control(
						'ekit_adv_tooltip_subcontent',
						[
							'label'					=> esc_html__('Sub-Content', 'elementskit'),
							'type'					=> Controls_Manager::TEXT,
							'default'				=> esc_html__('', 'elementskit'),
							'frontend_available'	=> true,
						]
					);

					$element->add_control(
						'ekit_adv_tooltip_image',
						[
							'label'					=> esc_html__('Image', 'elementskit'),
							'type'					=> Controls_Manager::MEDIA,
							'media_types' => [
								'image',
							],
							'dynamic' => [
								'categories' => [ TagsModule::IMAGE_CATEGORY ],
								'returnType' => 'object',
							],
							'frontend_available'	=> true,
						]
					);

					$element->add_control(
						'ekit_adv_tooltip_position',
						[
							'label'					=> esc_html__('Position', 'elementskit'),
							'type'					=> Controls_Manager::CHOOSE,
							'options'				=> [
								'top'		=> [
									'title'		=> esc_html__('Top', 'elementskit'),
									'icon'		=> 'eicon-caret-up',
								],
								'bottom'	=> [
									'title'		=> esc_html__('Bottom', 'elementskit'),
									'icon'		=> 'eicon-caret-down',
								],
								'left'		=> [
									'title'		=> esc_html__('Left', 'elementskit'),
									'icon'		=> 'eicon-caret-left',
								],
								'right'		=> [
									'title'		=> esc_html__('Right', 'elementskit'),
									'icon'		=> 'eicon-caret-right',
								],
							],
							'default'				=> 'top',
							'toggle'				=> false,
							'frontend_available'	=> true,
						]
					);

					$element->add_control(
						'ekit_adv_tooltip_animation',
						[
							'label'					=> esc_html__('Animation', 'elementskit'),
							'type'					=> Controls_Manager::SELECT,
							'options'				=> [
								'fade'			=> esc_html__('Fade', 'elementskit'),
								'perspective'	=> esc_html__('Perspective', 'elementskit'),
								'scale'			=> esc_html__('Scale', 'elementskit'),
								'shift-away'	=> esc_html__('Shift Away', 'elementskit'),
								'shift-toward'	=> esc_html__('Shift Toward', 'elementskit'),
							],
							'default'				=> 'fade',
							'frontend_available'	=> true
						]
					);

					$element->add_control(
						'ekit_adv_tooltip_arrow',
						[
							'label'					=> esc_html__('Arrow', 'elementskit'),
							'type'					=> Controls_Manager::SWITCHER,
							'return_value'			=> 1,
							'default'				=> 1,
							'frontend_available'	=> true
						]
					);

					$element->add_control(
						'ekit_adv_tooltip_trigger',
						[
							'label'					=> esc_html__('Trigger On', 'elementskit'),
							'type'					=> Controls_Manager::SELECT,
							'options'				=> [
								'mouseenter'			=> esc_html__('Hover', 'elementskit'),
								'click'					=> esc_html__('Click', 'elementskit'),
								'mouseenter click'		=> esc_html__('Both', 'elementskit'),
							],
							'default'				=> 'mouseenter',
							'frontend_available'	=> true
						]
					);
				$element->end_controls_tab(); // tab: ekit_adv_tooltip_tab_settings

				$element->start_controls_tab(
					'ekit_adv_tooltip_tab_styles',
					[
						'label'		=> esc_html__('Styles', 'elementskit'),
					]
				);
					// Width
					$element->add_control(
						'ekit_adv_tooltip_width',
						[
							'label'					=> esc_html__( 'Max Width', 'elementskit' ),
							'type'					=> Controls_Manager::SLIDER,
							'size_units'			=> [ 'px', 'em' ],
							'range'					=> [
								'px'	=> [
									'max'	=> 1000,
								],
								'em'	=> [
									'max'	=> 1000,
								],
							],
							'frontend_available'	=> true,
						]
					);

					// Typography
					$element->add_group_control(
						Group_Control_Typography::get_type(),
						[
							'name'		=> 'ekit_adv_tooltip_font',
							'label'		=> esc_html__( 'Typography', 'elementskit' ),
							'selector'	=> '.ekit-tippy.ekit-tippy-{{ID}} .tippy-content',
						]
					);

					// Text Color
					$element->add_control(
						'ekit_adv_tooltip_color',
						[
							'label'		=> esc_html__( 'Text Color', 'elementskit' ),
							'type'		=> Controls_Manager::COLOR,
							'selectors'	=> [
								'.ekit-tippy.ekit-tippy-{{ID}} .tippy-content' => 'color: {{VALUE}}',
							],
							'separator'				=> 'before',
						]
					);

					// Background Color
					$element->add_group_control(
						Group_Control_Background::get_type(),
						[
							'name'		=> 'ekit_adv_tooltip_bg',
							'selector'	=> '.ekit-tippy.ekit-tippy-{{ID}} .tippy-content',
							'fields_options'	=> [
								'background'		=> [
									'label'	=> esc_html__( 'Background Color', 'elementskit' ),
								],
							],
							'exclude'           => [ 'image' ],
						]
					);

					// Arrow Color
					$element->add_control(
						'ekit_adv_tooltip_arrow_color',
						[
							'label'		=> esc_html__( 'Arrow Color', 'elementskit' ),
							'type'		=> Controls_Manager::COLOR,
							'selectors'	=> [
								'.ekit-tippy-{{ID}} .tippy-arrow'	=> 'color: {{VALUE}}',
							],
							'condition'	=> [
								'ekit_adv_tooltip_arrow!'	=> '',
							]
						]
					);


					$element->add_control(
						'ekit_adv_tooltip_subcontent_heading',
						[
							'label'					=> esc_html__( 'Subcontent', 'elementskit' ),
							'type'					=> Controls_Manager::HEADING,
							'label_block'			=> true,
							'separator'				=> 'before',
						]
					);

					//Subcontent color
					$element->add_control(
						'ekit_adv_tooltip_subcontent_color',
						[
							'label'		=> esc_html__( 'Text Color', 'elementskit' ),
							'type'		=> Controls_Manager::COLOR,
							'selectors'	=> [
								'.ekit-tippy.ekit-tippy-{{ID}} .ekit-tippy-subcontent' => 'color: {{VALUE}}',
							],
						]
					);

					//Subcontent Typography
					$element->add_group_control(
						Group_Control_Typography::get_type(),
						[
							'name'		=> 'ekit_adv_tooltip_subcontent_font',
							'label'		=> esc_html__( 'Typography', 'elementskit' ),
							'selector'	=> '.ekit-tippy.ekit-tippy-{{ID}} .ekit-tippy-subcontent',
						]
					);

					//Subcontent Padding
					$element->add_control(
						'ekit_adv_tooltip_subcontent_padding',
						[
							'label'					=> esc_html__( 'Padding', 'elementskit' ),
							'type'					=> Controls_Manager::DIMENSIONS,
							'size_units'			=> ['px', 'em'],
							'selectors'				=> [
								'.ekit-tippy.ekit-tippy-{{ID}} .ekit-tippy-subcontent' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					// Offset X
					$element->add_control(
						'ekit_adv_tooltip_offset_x',
						[
							'label'					=> esc_html__( 'Offset X', 'elementskit' ),
							'type'					=> Controls_Manager::NUMBER,
							'placeholder'			=> 0,
							'frontend_available'	=> true,
							'separator'				=> 'before',
						]
					);

					// Offset Y
					$element->add_control(
						'ekit_adv_tooltip_offset_y',
						[
							'label'					=> esc_html__( 'Offset Y', 'elementskit' ),
							'type'					=> Controls_Manager::NUMBER,
							'placeholder'			=> 10,
							'frontend_available'	=> true,
						]
					);

					// Padding
					$element->add_control(
						'ekit_adv_tooltip_padding',
						[
							'label'					=> esc_html__( 'Padding', 'elementskit' ),
							'type'					=> Controls_Manager::DIMENSIONS,
							'size_units'			=> ['px', 'em'],
							'selectors'				=> [
								'.ekit-tippy.ekit-tippy-{{ID}} .tippy-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					// Border
					$element->add_group_control(
						Group_Control_Border::get_type(),
						[
							'name'					=> 'ekit_adv_tooltip_border',
							'label'					=> esc_html__( 'Border', 'elementskit' ),
							'selector'				=> '.ekit-tippy.ekit-tippy-{{ID}} .tippy-content',
						]
					);

					// Border Radius
					$element->add_control(
						'ekit_adv_tooltip_radius',
						[
							'label'					=> esc_html__( 'Border Radius', 'elementskit' ),
							'type'					=> Controls_Manager::DIMENSIONS,
							'size_units'			=> ['px', 'em', '%'],
							'default'				=> [
								'unit'	=> 'px',
							],
							'selectors'				=> [
								'.ekit-tippy.ekit-tippy-{{ID}} .tippy-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

					// Box Shadow
					$element->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name'		=> 'ekit_adv_tooltip_shadow',
							'label'		=> esc_html__( 'Box Shadow', 'elementskit' ),
							'selector'	=> '.ekit-tippy.ekit-tippy-{{ID}} .tippy-content, .ekit-tippy.ekit-tippy-{{ID}} :is(.tippy-arrow)',
						]
					);
				$element->end_controls_tab(); // tab: ekit_adv_tooltip_tab_styles
			$element->end_controls_tabs(); // tabs: ekit_adv_tooltip_tabs
		$element->end_controls_section(); // section: ekit_adv_tooltip
	}

	public function before_content($element) {
		$settings = $element->get_settings_for_display();

		if ( $settings['ekit_adv_tooltip_enable'] === 'yes' ) {
			add_action( 'elementor/frontend/before_enqueue_scripts', [$this, 'enqueue_scripts'] );
		}
	}

	/**
	 * Only load on Frontend if the tooltip option is enabled.
	 * !need optimization: similar method is also written on modules/advanced-tooltip/init.php file.
	 */
	public function enqueue_scripts() {
		$url = \ElementsKit::plugin_url() . 'modules/advanced-tooltip/';

		wp_enqueue_style( 'tippy-custom', $url . 'assets/css/tippy-custom.css', [], \ElementsKit::version() );

		wp_deregister_script( 'popper' );
		wp_deregister_script( 'tippyjs' );

		wp_enqueue_script( 'popper', $url . 'assets/js/popper.min.js', ['jquery'], \ElementsKit::version(), true );
		wp_enqueue_script( 'tippyjs', $url . 'assets/js/tippy.min.js', ['jquery'], \ElementsKit::version(), true );
		wp_enqueue_script( 'ekit-adv-tooltip', $url . 'assets/js/init.js', ['jquery', 'elementor-frontend'], \ElementsKit::version(), true );
	}
}
