<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Image_Swap_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Image_Swap extends Widget_Base {
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
        return 'https://wpmet.com/doc/image-swap/';
    }

    protected function register_controls() {
        /**
         * Section: Image Swap
         */
        $this->start_controls_section(
            'ekit_img_swap_content_section', [
                'label' => esc_html__( 'Image Swap', 'elementskit' ),
            ]
        );
            /**
             * Control: Front Image
             */
            $this->add_control(
                'ekit_img_front',
                [
                    'label'     => esc_html__( 'Front Image', 'elementskit' ),
                    'type'      => Controls_Manager::MEDIA,
                    'default'   => [
                        'url'       => Utils::get_placeholder_image_src(),
                        'id'    => -1
                    ],
					'dynamic' => [
						'active' => true,
					],
                ]
            );
            
            /**
             * Control: Back Image
             */
            $this->add_control(
                'ekit_img_back',
                [
                    'label'     => esc_html__( 'Back Image', 'elementskit' ),
                    'type'      => Controls_Manager::MEDIA,
                    'default'   => [
                        'url'       => Utils::get_placeholder_image_src(),
                        'id'    => -1
                    ],
					'dynamic' => [
						'active' => true,
					],
                ]
            );
            
            /**
             * Control: Back Image Size
             */
            $this->add_group_control(
                Group_Control_Image_Size::get_type(),
                [
                    'name'      => 'ekit_img_size',
                    'default'   => 'large',
                ]
            );

            /**
             * Control: Swap Style
             */
            $this->add_control(
                'ekit_img_swap_style',
                [
                    'label'     => esc_html__( 'Swap Style', 'elementskit' ),
                    'type'      => Controls_Manager::SELECT,
                    'options'   => [
                        'simple'            => esc_html__( 'Simple', 'elementskit' ),
                        'fade'              => esc_html__( 'Fade', 'elementskit' ),
                        'left-to-right'     => esc_html__( 'Left To Right', 'elementskit' ),
                        'right-to-left'     => esc_html__( 'Right To Left', 'elementskit' ),
                        'top-to-bottom'     => esc_html__( 'Top To Bottom', 'elementskit' ),
                        'bottom-to-top'     => esc_html__( 'Bottom To Top', 'elementskit' ),
                        'creative_top'     	=> esc_html__( 'Creative Top', 'elementskit' ),
                        'creative_bottom'   => esc_html__( 'Creative Bottom', 'elementskit' ),
                        'creative_left'     => esc_html__( 'Creative left', 'elementskit' ),
                        'creative_right'    => esc_html__( 'Creative Right', 'elementskit' ),
                        'zoom-in'           => esc_html__( 'Zoom In', 'elementskit' ),
                        'zoom-out'          => esc_html__( 'Zoom Out', 'elementskit' ),
                        'card-left'         => esc_html__( 'Card To Left', 'elementskit' ),
                        'card-right'        => esc_html__( 'Card To Right', 'elementskit' ),
                        'card-top'          => esc_html__( 'Card To Top', 'elementskit' ),
                        'card-bottom'       => esc_html__( 'Card To Bottom', 'elementskit' ),
                        'rotate-x'          => esc_html__( 'Rotate X', 'elementskit' ),
                        'rotate-y'          => esc_html__( 'Rotate Y', 'elementskit' ),
                        'rotate-circle'     => esc_html__( 'Rotate Circle', 'elementskit' ),
                        'skew-right'        => esc_html__( 'Skew Right', 'elementskit' ),
                        'skew-left'         => esc_html__( 'Skew Left', 'elementskit' ),
                    ],
                    'default'   => 'simple',
					'separator' => 'before',
                ]
            );

			$this->add_control(
				'ekit_img_swap_trigger',
				[
					'type'    => Controls_Manager::CHOOSE,
					'label'   => __('Trigger', 'elementskit'),
					'options' => [
						'hover' => [
							'title' => __('Hover', 'elementskit'),
							'icon'  => 'eicon-drag-n-drop',
						],
						'click' => [
							'title' => __('Click', 'elementskit'),
							'icon'  => 'eicon-click',
						],
					],
					'toggle' => true,
					'default' => 'hover',
				]
			);

            /**
             * Control: Change Behavior
             */
            $this->add_control(
                'ekit_img_swap_change',
                [
                    'label'     => esc_html__( 'Change Behavior', 'elementskit' ),
                    'type'      => Controls_Manager::CHOOSE,
                    'options'   => [
                        'hover'     => [
                            'title'     => esc_html__( 'Hover', 'elementskit' ),
                            'icon'      => 'eicon-drag-n-drop',
                        ],
                        'custom'    => [
                            'title'     => esc_html__( 'Parent Selector', 'elementskit' ),
                            'icon'      => 'eicon-custom-css',
                        ],
                    ],
                    'default'   => 'hover',
                    'toggle'    => false,
                    'condition' => [
                        'ekit_img_swap_style'   => 'fade',
                    ],
                ]
            );

            /**
             * Control: Custom Parent Selector
             */
            $this->add_control(
                'ekit_img_swap_selector',
                [
                    'label'                 => esc_html__( 'Parent Selector', 'elementskit' ),
                    'type'                  => Controls_Manager::TEXT,
                    'placeholder'           => esc_html__( '.ekit-sticky--active', 'elementskit' ),
                    'frontend_available'    => true,
                    'condition'             => [
                        'ekit_img_swap_style'   => 'fade',
                        'ekit_img_swap_change'  => 'custom',
                    ],
					'dynamic' => [
						'active' => true,
					],
                ]
            );

            /**
             * Control: Indicators
             */
            $this->add_control(
                'ekit_img_swap_indicators',
                [
                    'label'         => esc_html__( 'Indicators', 'elementskit' ),
                    'type'          => Controls_Manager::SWITCHER,
                    'selectors'     => [
                        '{{WRAPPER}} .ekit-image-swap:before' => 'display: block;',
                    ],
                ]
            );

            /**
             * Control: Link
             */
            $this->add_control(
                'ekit_img_swap_link',
                [
                    'label'         => esc_html__( 'Link', 'elementskit' ),
                    'type'          => Controls_Manager::URL,
                    'placeholder'   => esc_html__( 'https://wpmet.com', 'elementskit' ),
					'dynamic' => [
						'active' => true,
					],
                ]
            );
        $this->end_controls_section();

        
        /**
         * Section: Image Swap
         */
        $this->start_controls_section(
            'ekit_img_swap_img_style', [
                'label' => esc_html__( 'Image', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
            /**
             * Fixed Height
             */
            $this->add_responsive_control(
                'ekit_img_swap_fixed_height',
                [
                    'label'         => esc_html__( 'Fixed Height', 'elementskit' ),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['px', 'em'],
                    'range'         => [
                        'px'    => [
                            'max'   => 1000,
                        ],
                        'em'    => [
                            'max'   => 1000,
                        ],
                    ],
                    'selectors'     => [
                        '{{WRAPPER}} .ekit-image-swap'   => 'min-height: {{SIZE}}{{UNIT}};',
                    ],
                    'render_type'   => 'template',
                ]
            );
            
            /**
             * Transition Duration
             */
            $this->add_responsive_control(
                'ekit_img_swap_transition',
                [
                    'label'         => esc_html__( 'Transition Duration', 'elementskit' ),
                    'type'          => Controls_Manager::SLIDER,
                    'range'         => [
                        'px'    => [
                            'max'   => 3,
                            'step'  => 0.1,
                        ],
                    ],
                    'selectors'     => [
                        '{{WRAPPER}} .ekit-image-swap img'   => 'transition-duration: {{SIZE}}s;',
                    ],
                ]
            );
        $this->end_controls_section();

        /**
         * Section: Indicators Style
         */
        $this->start_controls_section(
            'ekit_img_swap_indicators_section', [
                'label'     => esc_html__( 'Indicators', 'elementskit' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'ekit_img_swap_indicators'  => 'yes',
                ],
            ]
        );
            /**
             * Control: Normal Color
             */
            $this->add_control(
                'ekit_img_swap_indicators_color_normal',
                [
                    'label'     => esc_html__( 'Normal Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit-image-swap:before'  => 'border-color: {{VALUE}};',
                    ],
                ]
            );
            
            /**
             * Control: Active Color
             */
            $this->add_control(
                'ekit_img_swap_indicators_color_active',
                [
                    'label'     => esc_html__( 'Active Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit-image-swap:not(:hover):not(:focus):before'  => 'border-left-color: {{VALUE}};',
                        '{{WRAPPER}} .ekit-image-swap:hover:before, {{WRAPPER}} .ekit-image-swap:focus:before'  => 'border-right-color: {{VALUE}};',
                    ],
                ]
            );
            
            /**
             * Control: Size
             */
            $this->add_responsive_control(
                'ekit_img_swap_indicators_size',
                [
                    'label'     => esc_html__( 'Size', 'elementskit' ),
                    'type'      => Controls_Manager::SLIDER,
                    'default'   => [
                        'size'  => 5,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ekit-image-swap:before'  => 'height: {{SIZE}}px; border-width: 0 {{SIZE}}px;',
                    ],
                    'separator' => 'before',
                ]
            );

            /**
             * Control: Size
             */
            $this->add_responsive_control(
                'ekit_img_swap_indicators_spacing',
                [
                    'label'     => esc_html__( 'Spacing', 'elementskit' ),
                    'type'      => Controls_Manager::SLIDER,
                    'selectors' => [
                        '{{WRAPPER}} .ekit-image-swap:before'  => 'width: calc({{SIZE}}px + ({{ekit_img_swap_indicators_size.SIZE}}px * 2));',
                    ],
                    'condition' => [
                        'ekit_img_swap_indicators_size[size]!' => '',
                    ],
                ]
            );

            /**
             * Control: Style
             */
            $this->add_control(
                'ekit_img_swap_indicator_style',
                [
                    'label'         => esc_html__( 'Style', 'elementskit' ),
                    'type'          => Controls_Manager::CHOOSE,
                    'options'       => [
                        'horizontal'    => [
                            'title'         => esc_html__( 'Horizontal', 'elementskit' ),
                            'icon'          => 'eicon-ellipsis-h',
                        ],
                        'vertical'      => [
                            'title'         => esc_html__( 'Vertical', 'elementskit' ),
                            'icon'          => 'eicon-ellipsis-v',
                        ],
                    ],
                    'default'       => 'horizontal',
                    'prefix_class'  => 'ekit-image-swap-',
                    'separator'     => 'before',
                ]
            );

            /**
             * Control: Position X
             */
            $this->add_responsive_control(
                'ekit_img_swap_indicator_pos_x',
                [
                    'label'         => esc_html__( 'Position X', 'elementskit' ),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['%', 'px'],
                    'default'       => [
                        'unit'          => '%',
                    ],
                    'selectors'     => [
                        '{{WRAPPER}} .ekit-image-swap:before'  => 'left: {{SIZE}}{{UNIT}}; right: auto;',
                    ],
                    'separator'     => 'before',
                ]
            );

            /**
             * Control: Position Y
             */
            $this->add_responsive_control(
                'ekit_img_swap_indicator_pos_y',
                [
                    'label'         => esc_html__( 'Position Y', 'elementskit' ),
                    'type'          => Controls_Manager::SLIDER,
                    'size_units'    => ['%', 'px'],
                    'default'       => [
                        'unit'          => '%',
                    ],
                    'selectors'     => [
                        '{{WRAPPER}} .ekit-image-swap:before'  => 'top: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );
        $this->end_controls_section();

        /**
         * Include: Unlock Pro Message
         */
        $this->insert_pro_message();
    }

    protected function render( ) {
        /**
         * Common Wrapper for All Widgets
         */
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
    }

    protected function render_raw( ) {
        /**
         * Setting Values
         */
        $settings = $this->get_settings_for_display();
        extract($settings);

        /**
         * Change Behavior
         */
        $change_behavior = ($ekit_img_swap_change === 'custom') && ($ekit_img_swap_selector !== '') ? ' no-hover' : ''; 
		
		/**
         * Click Behavior
         */
        $click_behavior = ($ekit_img_swap_trigger === 'click') ? 'click-inactive' : '';

        /**
         * Fixed Height
         */
        $is_fixed_height = $ekit_img_swap_fixed_height['size'] ? ' ekit-image-swap--fixed-height' : '';

        /**
         * Link
         */
        $wrapper_tag = $ekit_img_swap_link['url'] ? 'a' : 'div';
        $this->add_link_attributes( 'link', $ekit_img_swap_link );

        /**
         * Markup
         */
        ?>
        <<?php echo esc_attr( $wrapper_tag ); ?> data-trigger="<?php echo esc_html($click_behavior); ?>" class="ekit-image-swap ekit-image-swap--<?php echo esc_attr( $ekit_img_swap_style . $change_behavior . $is_fixed_height);?> <?php echo esc_attr($click_behavior ) ;?>" 
		<?php echo $this->get_render_attribute_string( 'link' ); ?> tabindex="-1">
            <?php
                echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'ekit_img_size', 'ekit_img_front' );
                echo Group_Control_Image_Size::get_attachment_image_html( $settings, 'ekit_img_size', 'ekit_img_back' );
            ?>
        </<?php echo esc_attr( $wrapper_tag ); ?>>
        <?php

        /**
         * Custom Selector Support
         */
        if ( ($ekit_img_swap_style === 'fade') && $ekit_img_swap_selector ) {
            $css_selector = $ekit_img_swap_selector .' .elementor-element-'. $this->get_id() .' .ekit-image-swap img:nth-child(2),';
            $css_selector .= $ekit_img_swap_selector .'.elementor-element-'. $this->get_id() .' .ekit-image-swap img:nth-child(2)';
            
            ?>
            <style>
                <?php echo esc_html__( $css_selector, 'elementskit' ); ?> {
                    opacity: 1;
                }
            </style>
            <?php
        }
    }
}
