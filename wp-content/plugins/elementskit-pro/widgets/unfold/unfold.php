<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Unfold_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;
use \ElementsKit_Lite\Modules\Controls\Widget_Area_Utils as Widget_Area_Utils;

if ( ! defined( 'ABSPATH' ) ) exit;


class ElementsKit_Widget_Unfold extends Widget_Base {
	use \ElementsKit_Lite\Widgets\Widget_Notice;

	public $base;

    public function get_name() {
        return Handler::get_name();
    }

    public function get_title() {
        return Handler::get_title();
    }

    public function get_icon() {
        return Handler::get_icon();
    }

    public function get_categories() {
        return Handler::get_categories();
    }
    
	public function get_keywords() {
		return Handler::get_keywords();
	}

    public function get_help_url() {
        return 'https://wpmet.com/doc/unfold/';
    }

    protected function register_controls() {


		$this->start_controls_section(
			'unfold_general_content',
			array(
				'label' => esc_html__( 'Content', 'elementskit' ),
			)
		);

		$this->add_control(
			'unfold_title',
			[
				'label' => esc_html__( 'Title', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'default'	=> 'Add Your Heading Text Here',
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'unfold_enable_template',
			[
				'label'   => esc_html__('Enable Template', 'elementskit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);
		$this->add_control(
			'unfold_content',
			[
				'label' => esc_html__( 'Content', 'elementskit' ),
				'type' => Controls_Manager::WYSIWYG,
				'default'	=> '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Phasellus hendrerit. Pellentesque aliquet nibh nec urna. In nisi neque, aliquet vel, dapibus id, mattis vel, nisi. Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, eget blandit nunc tortor eu nibh. Nullam mollis. Ut justo. Suspendisse potenti.</p>

				<p>Sed egestas, ante et vulputate volutpat, eros pede semper est, vitae luctus metus libero eu augue. Morbi purus libero, faucibus adipiscing, commodo quis, gravida id, est. Sed lectus. Praesent elementum hendrerit tortor. Sed semper lorem at felis. Vestibulum volutpat, lacus a ultrices sagittis, mi neque euismod dui, eu pulvinar nunc sapien ornare nisl. Phasellus pede arcu, dapibus eu, fermentum et, dapibus sed, urna.</p>',
				'condition'	=> [
					'unfold_enable_template!'	=> 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
            'unfold_widget_content', [
                'label' => esc_html__('Content', 'elementskit'),
                'type' => ElementsKit_Controls_Manager::WIDGETAREA,
                'label_block' => true,
            ]
        );
		$this->add_control(
			'unfold_expand_btn_text',
			[
				'label' => esc_html__( 'Expand Button Text', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'default'	=> 'Read More',
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'unfold_collapse_btn_text',
			[
				'label' => esc_html__( 'Collapse Button Text', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'default'	=> 'Read Less',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'unfold_content_options',
			array(
				'label' => esc_html__( 'Content Options', 'elementskit' ),
			)
		);

		$this->add_control(
			'unfold_content_expand_direction',  
			[
				'label' => esc_html__('Content Expand Direction', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'default' => 'above',
				'options' => [
					'above' => esc_html__('Above Button', 'elementskit'),
					'below' => esc_html__('Below Button', 'elementskit'),
				],
			]
		);
		
		$this->add_responsive_control(
			'unfold_collapsed_content_height', [
				'label'			 =>esc_html__( 'Collapsed Content Height', 'elementskit' ),
				'type'			 => Controls_Manager::SLIDER,
				'default'		 => [
					'size' => 79,
				],
				'range'			 => [
					'px' => [
						'min'	 => 0,
						'max'	=> 500,
						'step'	 => 1,
					],
				],
				'size_units'	 => ['px'],
				'selectors'		 => [
					'{{WRAPPER}} .ekit-unfold-data'	=> 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
            'unfold_transition_duration',
            [
                'label'     => esc_html__( 'Transition Duration', 'elementskit' ),
				'type'      => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 5000,
				'step' => 1,
                'default'   => 300,
            ]
		);
		
		$this->add_responsive_control(
			'unfold_btn_alignment',
			[
				'label' =>esc_html__( 'Button Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'baseline'    => [
						'title' =>esc_html__( 'Left', 'elementskit' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' =>esc_html__( 'Center', 'elementskit' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' =>esc_html__( 'Right', 'elementskit' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors'=> [
					 '{{WRAPPER}} .ekit-unfold-btn' => 'align-self: {{VALUE}};',
				 ],
				'default' => 'baseline',
			]
		);

		$this->end_controls_section();

		// styles
		$this->start_controls_section(
			'unfold_wrapper_style',
			[
				'label' => esc_html__( 'Wrapper', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'unfold_wrapper_width',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-unfold-wrapper' => 'width: {{SIZE}}{{UNIT}};',
                ]
			]
		);

		$this->add_responsive_control(
			'unfold_wrapper_alignment',
			[
				'label' =>esc_html__( 'Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' =>esc_html__( 'Left', 'elementskit' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' =>esc_html__( 'Center', 'elementskit' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' =>esc_html__( 'Right', 'elementskit' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'separator'	=> 'after',
				'default' => 'left',
				'condition'	=> [
					'unfold_wrapper_width!' => ''
				]
			]
		);
 
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'unfold_wrapper_background',
				'label'    => esc_html__( 'Background', 'elementskit' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-unfold-wrapper',
			]
		);

		$this->add_responsive_control(
			'unfold_wrapper_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-unfold-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	
		$this->add_responsive_control(
			'unfold_wrapper_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-unfold-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'unfold_wrapper_border',
				'label'    => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-unfold-wrapper',
			]
		);
		$this->add_responsive_control(
			'unfold_wrapper_radius',
			[
				'label'      => esc_html__( 'Border radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-unfold-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name'     => 'unfold_wrapper_shadow',
				'selector' => '{{WRAPPER}} .ekit-unfold-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'unfold_wrapper_overly',
				'label'    => esc_html__( 'Overlay Color', 'elementskit' ),
				'types'    => [ 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-unfold-data:after',
				'fields_options' => [
                    'background' => [
                        'label' => esc_html__( 'Overlay Color', 'elementskit' ),
                    ],
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
            'unfold_wrapper_overly_h',
            [
                'label' => esc_html__( 'Overlay Height', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px','%' ],
                'range' => [
                    'px'    => [
                        'min'   => 0,
                        'max'   => 500
                    ],
                    '%'     => [
                        'min' => 0,
                        'max' => 100,
                    ],
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-unfold-data:after' => 'height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
				],
				'separator'	=> 'after',
            ]
        );
	
		$this->end_controls_section();

		$this->start_controls_section(
			'unfold_title_style',
			[
				'label' => esc_html__( 'Title', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'unfold_title!' => ''
				]
			]
		);
		

		$this->add_responsive_control(
			'unfold_title_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-unfold-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'unfold_title_alignment',
			[
				'label' =>esc_html__( 'Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'baseline'    => [
						'title' =>esc_html__( 'Left', 'elementskit' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' =>esc_html__( 'Center', 'elementskit' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' =>esc_html__( 'Right', 'elementskit' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors'=> [
					 '{{WRAPPER}} .ekit-unfold-heading' => 'align-self: {{VALUE}};',
				 ],
				'default' => 'baseline',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'       => 'unfold_title_typo',
				'selector'   => '{{WRAPPER}} .ekit-unfold-heading',
			]
		);

		$this->add_control(
            'unfold_title_text_color',
            [
                'label'      => esc_html__( 'Text Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .ekit-unfold-heading' => 'color: {{VALUE}};'
                ],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'unfold_desc_style',
			[
				'label' => esc_html__( 'Description', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'unfold_content!'	=> ''
				]
			]
		);

		$this->add_responsive_control(
			'unfold_desc_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-unfold-data-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
            'unfold_desc_para_spacing',
            [
                'label' => esc_html__( 'Paragraph Spacing', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-unfold-raw-content p, {{WRAPPER}} .ekit-unfold-raw-content ul' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

		$this->add_responsive_control(
			'unfold_desc_alignment',
			[
				'label' =>esc_html__( 'Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' =>esc_html__( 'Left', 'elementskit' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' =>esc_html__( 'Center', 'elementskit' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' =>esc_html__( 'Right', 'elementskit' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors'=> [
					 '{{WRAPPER}} .ekit-unfold-data-inner' => 'text-align: {{VALUE}};',
				 ],
				'default' => 'left',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'       => 'unfold_desc_typo',
				'selector'   => '{{WRAPPER}} .ekit-unfold-raw-content p',
			]
		);

		$this->add_control(
            'unfold_desc_text_color',
            [
                'label'      => esc_html__( 'Text Color', 'elementskit' ),
                'type'       => Controls_Manager::COLOR,
                'selectors'  => [
                    '{{WRAPPER}} .ekit-unfold-raw-content p' => 'color: {{VALUE}};'
                ],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'unfold_button_style',
			[
				'label' => esc_html__( 'Button', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
 
		$this->add_responsive_control(
			'unfold_button_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-unfold-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'unfold_button_typography',
				'label' =>esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-unfold-btn',
			]
		);

        $this->add_group_control(
        	Group_Control_Text_Shadow::get_type(),
        	[
        		'name' => 'unfold_button_shadow',
        		'selector' => '{{WRAPPER}} .ekit-unfold-btn',
        	]
        );

		$this->start_controls_tabs( 'unfold_button_tabs_style' );

		$this->start_controls_tab(
			'unfold_button_tabnormal',
			[
				'label' =>esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'unfold_button_text_color',
			[
				'label' =>esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-unfold-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-unfold-btn:not(:hover):not(:active):not(.has-text-color)' => 'color: {{VALUE}};', // the wrapper is used to solved conflict with twenty twenty one theme
				],
			]
		);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'unfold_button_bg_color',
				'selector' => '{{WRAPPER}} .ekit-unfold-btn, {{WRAPPER}} .ekit-unfold-btn:not(:hover):not(:active):not(.has-text-color)', // second wrapper is used to solved conflict with twenty twenty one theme
            )
        );

		$this->end_controls_tab();

		$this->start_controls_tab(
			'unfold_button_tab_button_hover',
			[
				'label' =>esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_control(
			'unfold_button_hover_color',
			[
				'label' =>esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-unfold-btn:hover' => 'color: {{VALUE}};',
				],
			]
		);

	    $this->add_group_control(
		    Group_Control_Background::get_type(),
		    array(
			    'name'     => 'unfold_button_bg_hover_color',
			    'selector' => '{{WRAPPER}} .ekit-unfold-btn:hover',
		    )
	    );

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'unfold_btn_border',
				'label'    => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-unfold-btn',
			]
		);
		$this->add_responsive_control(
			'unfold_btn_radius',
			[
				'label'      => esc_html__( 'Border radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-unfold-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name'     => 'unfold_btn_shadow',
				'selector' => '{{WRAPPER}} .ekit-unfold-btn',
			]
		);

		$this->end_controls_section();
 

		$this->insert_pro_message();
    }

    protected function render( ) {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
    }

    protected function render_raw( ) {
		$settings = $this->get_settings_for_display();
		extract($settings);
		$config = [
            'expand_text'				=> (!empty($unfold_expand_btn_text) ? esc_attr($unfold_expand_btn_text) : ''),
            'collapse_text'			=>  (!empty($unfold_collapse_btn_text) ? esc_attr($unfold_collapse_btn_text) : ''),
            'collapse_height'			=>  (!empty($unfold_collapsed_content_height['size']) ? esc_attr($unfold_collapsed_content_height['size']) : ''),
            'transition_duration'			=>  (!empty($unfold_transition_duration) ? esc_attr($unfold_transition_duration) : ''),
        ];

		$this->add_render_attribute( 'wrapper-settings', [
			'data-config' =>  wp_json_encode($config),
			'class'	=> [
				'ekit-unfold-wrapper',
				'ekit-expand-directio-' . (!empty($unfold_content_expand_direction) ? esc_attr($unfold_content_expand_direction) : ''),
				'ekit-unfold-wrapper-align-' . (!empty($unfold_wrapper_alignment) ? esc_attr($unfold_wrapper_alignment) : '')
			]
		] );
		?>
			<div <?php echo \ElementsKit_Lite\Utils::render($this->get_render_attribute_string( 'wrapper-settings' )); ?>>
				<?php if(!empty($unfold_title)) : ?>
					<h3 class="ekit-unfold-heading"><?php echo esc_html($unfold_title); ?></h3>
				<?php endif; ?>

				<div class="ekit-unfold-data">
					<div class="ekit-unfold-data-inner">
						<?php if(!empty($unfold_enable_template) && $unfold_enable_template == 'yes') {
 							echo Widget_Area_Utils::parse( $settings['unfold_widget_content'], $this->get_id(), 99 );
						} else {
							echo '<div class="ekit-unfold-raw-content">'. do_shortcode( \ElementsKit_Lite\Utils::kses($unfold_content) ) .'</div>';
						} ?>
					</div>
				</div>
				
				<button class="ekit-unfold-btn"><?php echo!empty($unfold_expand_btn_text) ? esc_html($unfold_expand_btn_text) : ''; ?></button>
			</div>
        <?php
    }
}
