<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Image_Hover_Effect_Handler as Handler;

defined('ABSPATH') || exit;
class ElementsKit_Widget_Image_Hover_Effect extends Widget_Base {
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
        return 'https://wpmet.com/doc/elementor-image-hover-effect/';
    }

    protected function register_controls() {

        $this->start_controls_section(
			'ekit_section_image_content',
			[
				'label' => esc_html__('Image Hover Style', 'elementskit'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ekit_hover_effect_heading',
			[
				'label' => esc_html__( 'Background Effect', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'ekit_hover_effect',
			[
				'label' => esc_html__('Choose Effect', 'elementskit'),
				'type' => Controls_Manager::SELECT2,
				'options' => [
					'ekit_effect_blind'  => esc_html__('Blind', 'elementskit'),
					'ekit_effect_block'  => esc_html__('Block', 'elementskit'),
					'ekit_effect_border'  => esc_html__('Border', 'elementskit'),
					'ekit_effect_blend'  => esc_html__('Blend', 'elementskit'),
					'ekit_effect_circle'  => esc_html__('Circle', 'elementskit'),
                    'ekit_effect_corner_zoom_back'  => esc_html__('Corner Zoom Back', 'elementskit'),
					'ekit_effect_fade'  => esc_html__('Fade', 'elementskit'),
					'ekit_effect_flash'  => esc_html__('Flash', 'elementskit'),
					'ekit_effect_grayscale'  => esc_html__('Grayscale', 'elementskit'),
					'ekit_effect_glitch'  => esc_html__('Glitch', 'elementskit'),
					'ekit_effect_scale_rotate' => esc_html__('Scale Rotate', 'elementskit'),
					'ekit_effect_scroll'  => esc_html__('Scroll', 'elementskit'),
					'ekit_effect_slide'  => esc_html__('Slide', 'elementskit'),
					'ekit_effect_splash'  => esc_html__('Splash', 'elementskit'),
					'ekit_effect_sutter_out'  => esc_html__('Sutter Out', 'elementskit'),
					'ekit_effect_zoom_in_overly'  => esc_html__('Zoom in Overly', 'elementskit'),
					'ekit_effect_zoom'  => esc_html__('Zoom', 'elementskit'),
					'ekit_effect_swap'  => esc_html__('Swap', 'elementskit'),
				],
				'default' => 'ekit_effect_blind',
			]
		);

        $this->add_control(
			'ekit_blend_effect',
			[
				'label' => esc_html__('Select Blend Style', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'darken'  => esc_html__('Darken', 'elementskit'),
					'multiply'  => esc_html__('Multiply', 'elementskit'),
					'color-burn'  => esc_html__('Color Burn', 'elementskit'),
					'lighten'  => esc_html__('Lighten', 'elementskit'),
					'screen'  => esc_html__('Screen', 'elementskit'),
					'color-dodge'  => esc_html__('Color Dodge', 'elementskit'),
					'overlay'  => esc_html__('Overlay', 'elementskit'),
					'soft-light'  => esc_html__('Soft Light', 'elementskit'),
					'hard-light'  => esc_html__('Hard Light', 'elementskit'),
					'difference'  => esc_html__('Difference', 'elementskit'),
					'hue'  => esc_html__('Hue', 'elementskit'),
					'satureation'  => esc_html__('Satureation', 'elementskit'),
					'color'  => esc_html__('Color', 'elementskit'),
					'luminosity'  => esc_html__('Luminosity', 'elementskit'),
				],
				'default' => 'darken',
                'condition' => [
					'ekit_hover_effect' => 'ekit_effect_blend',
				],
				'selectors'=> [
                    '{{WRAPPER}} .ekit-wid-con .ekit_image_blend_mode:hover + img' => 'mix-blend-mode: {{VALUE}};',
                ],
			]
		);

        $this->add_control(
			'ekit_blind_effect',
			[
				'label' => esc_html__('Select Blind Style', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'ekit_blind_horizontal'  => esc_html__('Horizontal', 'elementskit'),
					'ekit_blind_vertical'  => esc_html__('Vertical', 'elementskit'),
				],
				'default' => 'ekit_blind_horizontal',
                'condition' => [
					'ekit_hover_effect' => 'ekit_effect_blind',
				],
			]
		);

        $this->add_control(
			'ekit_sutter_out_effect',
			[
				'label' => esc_html__('Select Sutter Out Style', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'ekit_sutter_out_horizontal'  => esc_html__('Horizontal', 'elementskit'),
					'ekit_sutter_out_vertical'  => esc_html__('Vertical', 'elementskit'),
					'ekit_sutter_out_diagonal_right'  => esc_html__('Diagonal Right', 'elementskit'),
				],
				'default' => 'ekit_sutter_out_horizontal',
                'condition' => [
					'ekit_hover_effect' => 'ekit_effect_sutter_out',
				],
			]
		);

        $this->add_control(
			'ekit_slide_effect',
			[
				'label' => esc_html__('Select Slide Style', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'ekit_slide_effect_up'  => esc_html__('Up', 'elementskit'),
					'ekit_slide_effect_right'  => esc_html__('Right', 'elementskit'),
				],
				'default' => 'ekit_slide_effect_up',
                'condition' => [
					'ekit_hover_effect' => 'ekit_effect_slide',
				],
			]
		);

        $this->add_control(
			'ekit_splash_effect',
			[
				'label' => esc_html__('Select Splash Style', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'ekit_splash_effect_single'  => esc_html__('Single', 'elementskit'),
					'ekit_splash_effect_double'  => esc_html__('Double', 'elementskit'),
				],
				'default' => 'ekit_splash_effect_single',
                'condition' => [
					'ekit_hover_effect' => 'ekit_effect_splash',
				],
			]
		);

        $this->add_control(
			'ekit_zoom_effect',
			[
				'label' => esc_html__('Select Zoom Style', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'ekit_zoom_effect_in'  => esc_html__('Zoom In', 'elementskit'),
					'ekit_zoom_effect_out'  => esc_html__('Zoom Out', 'elementskit'),
					'ekit_zoom_effect_in_blur'  => esc_html__('Zoom In Blur', 'elementskit'),
				],
				'default' => 'ekit_zoom_effect_in',
                'condition' => [
					'ekit_hover_effect' => 'ekit_effect_zoom',
				],
			]
		);

        $this->add_control(
			'ekit_swap_effect',
			[
				'label' => esc_html__('Select Swap Style', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'ekit_swap_effect_one'  => esc_html__('Fade', 'elementskit'),
					'ekit_swap_effect_two'  => esc_html__('Creative Fade', 'elementskit'),
					'ekit_swap_effect_three'  => esc_html__('Left to Right', 'elementskit'),
				],
				'default' => 'ekit_swap_effect_one',
                'condition' => [
					'ekit_hover_effect' => 'ekit_effect_swap',
				],
			]
		);

        $this->add_control(
			'ekit_grayscale_effect',
			[
				'label' => esc_html__('Select Grayscale Style', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'ekit_grayscale_to_color_effect'  => esc_html__('Grayscale To Color', 'elementskit'),
					'ekit_color_to_grayscale_effect'  => esc_html__('Color To Grayscale', 'elementskit'),
				],
				'default' => 'ekit_grayscale_to_color_effect',
                'condition' => [
					'ekit_hover_effect' => 'ekit_effect_grayscale',
				],
			]
		);

		$this->add_control(
			'ekit_hover_image',
			[
				'label' => esc_html__('Choose Image', 'elementskit'),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::get_placeholder_image_src()
                ],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'ekit_hover_image2',
			[
				'label' => esc_html__('Choose Another Image', 'elementskit'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'ekit_hover_effect' => 'ekit_effect_swap',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name' => 'ekit_image',
                'default' => 'full',
                'exclude' => ['thumbnail','woocommerce_thumbnail','woocommerce_gallery_thumbnail','shop_thumbnail'],
				'condition' => [
					'ekit_hover_effect!' => 'ekit_effect_glitch',
				],
			]
        );

		$this->add_control(
			'ekit_hover_content_heading',
			[
				'label' => esc_html__( 'Content', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_hover_title',
			[
				'label' => esc_html__('Title', 'elementskit'),
				'type' => Controls_Manager::TEXT,
				'description'	=> esc_html__( '"Focused Title" Settings will be worked, If you use this {something} format', 'elementskit' ),
				'placeholder'	 =>esc_html__( 'Elements{Kit}', 'elementskit' ),
				'default'	 =>esc_html__( 'Elements{Kit}', 'elementskit' ),
				'label_block' => true,
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'ekit_hover_title_switch',
			[
				'label' => esc_html__('Show Title on Hover?', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
					'ekit_text_effect' => ['ekit_text_none','ekit_text_daigonal'],
				],
			]
		);

		$this->add_control(
			'ekit_hover_title_stable_switch',
			[
				'label' => esc_html__('Keep Title Stable?', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
					'ekit_text_effect!' => ['ekit_text_none','ekit_text_daigonal'],
				],
			]
		);

		$this->add_control(
			'ekit_hover_description',
			[
				'label' => esc_html__('Description', 'elementskit'),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 4,
				'default' => esc_html__('All-in-One Addons for Elementor', 'elementskit'),
				'placeholder' => esc_html__('Enter your description here', 'elementskit'),		
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch']
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'ekit_hover_description_switch',
			[
				'label' => esc_html__('Show Description on Hover?', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
					'ekit_text_effect' => ['ekit_text_none','ekit_text_daigonal'],
				],
			]
		);

		$this->add_control(
			'ekit_hover_description_stable_switch',
			[
				'label' => esc_html__('Keep Description Stable?', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
					'ekit_text_effect!' => ['ekit_text_none','ekit_text_daigonal'],
				],
			]
		);

		$this->add_control(
			'ekit_text_effect',
			[
				'label' => esc_html__('Text Style', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'ekit_text_none'  => esc_html__('None', 'elementskit'),
					'ekit_text_daigonal'  => esc_html__('Diagonal', 'elementskit'),
					'ekit_text_right_to_left'  => esc_html__('Right to Left', 'elementskit'),
					'ekit_text_left_to_right'  => esc_html__('Left to Right', 'elementskit'),
					'ekit_text_top_to_bottom'  => esc_html__('Top to Bottom', 'elementskit'),
					'ekit_text_bottom_to_top'  => esc_html__('Bottom to Top', 'elementskit'),
				],
				'default' => 'ekit_text_none',
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch']
				],
			]
		);

		// button
		$this->add_control(
			'ekit_hover_image_btn',
			[
				'label' => esc_html__('Use Button?', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'ekit_text_effect!' => 'ekit_text_daigonal',
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
				],
			]
		);

		$this->add_control(
			'ekit_hover_button_switch',
			[
				'label' => esc_html__('Show Button on Hover?', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no',
				'condition'	=> [
					'ekit_hover_image_btn' => 'yes',
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
					'ekit_text_effect' => ['ekit_text_none'],
					'ekit_text_effect!' => ['ekit_text_right_to_left','ekit_text_left_to_right', 'ekit_text_daigonal'],
				],
			]
		);

		$this->add_control(
			'ekit_hover_button_stable_switch',
			[
				'label' => esc_html__('Keep Button Stable?', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'ekit_hover_image_btn' => 'yes',
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
					'ekit_text_effect!' => ['ekit_text_none', 'ekit_text_daigonal'],
				],
			]
		);

		$this->add_control(
			'ekit_hover_link',
			[
				'label' => esc_html__('Image Link URL', 'elementskit'),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__('https://wpmet.com', 'elementskit'),
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],	
				'condition' => [
					'ekit_hover_image_btn!' => 'yes',
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'ekit_image_button_text',
			[
				'label' => esc_html__('Button Text', 'elementskit'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Learn More', 'elementskit'),
				'placeholder' => esc_html__('Enter button text', 'elementskit'),
				'condition' => [
					'ekit_hover_image_btn' => 'yes',
					'ekit_text_effect!' => 'ekit_text_daigonal',
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'ekit_image_button_link',
			[
				'label' => esc_html__('Button Link URL', 'elementskit'),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__('https://wpmet.com', 'elementskit'),
				'show_external' => true,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],		
				'condition' => [
					'ekit_hover_image_btn' => 'yes',
					'ekit_text_effect!' => 'ekit_text_daigonal',
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Content Animation', 'elementskit' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition'	=> [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
				],
			]
		);

		$this->add_control(
			'ekit_entrance_animation',
			[
				'label' => esc_html__( 'Entrance Animation', 'elementskit' ),
				'type' => Controls_Manager::ANIMATION,
			]
		);

		$this->add_control(
			'ekit_entrance_title_duration',
			[
				'label' => esc_html__( 'Title Animation Duration (Second)', 'elementskit' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 10,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit_image_caption_title' => 'animation-duration: {{VALUE}}s;',
				],
				'condition'	=> [
					'ekit_entrance_animation!' => 'none',
					'ekit_hover_title!' => '',
				],
			]
		);

		$this->add_control(
			'ekit_entrance_description_duration',
			[
				'label' => esc_html__( 'Description Animation Duration (Second)', 'elementskit' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 10,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit_image_caption_description' => 'animation-duration: {{VALUE}}s;',
				],
				'condition'	=> [
					'ekit_entrance_animation!' => 'none',
					'ekit_hover_description!' => '',
				],
			]
		);
		
		$this->add_control(
			'ekit_entrance_button_duration',
			[
				'label' => esc_html__( 'Button Animation Duration (Second)', 'elementskit' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 10,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit_image_caption_button' => 'animation-duration: {{VALUE}}s;',
				],
				'condition' => [
					'ekit_hover_image_btn' => 'yes',
					'ekit_entrance_animation!' => 'none',
				]
			]
		);

		$this->add_control(
			'ekit_hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'elementskit' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
				'default' => 'shrink',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_hover_animation_count',
			[
				'label' => esc_html__( 'Use Animation Count Infinite', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'infinite',
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit_image_caption' => 'animation-iteration-count: {{VALUE}} !important;',
				],
				'condition'	=> [
					'ekit_hover_animation!' => ['grow', 'shrink', 'rotate', 'grow-rotate', 'float', 'sink', 'skew', 'skew-forward', 'skew-backward'],
				],
			]
		);

		$this->add_control(
			'ekit_text_animation_heading',
			[
				'label' => esc_html__( 'Text Style Animation', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
					'ekit_text_effect!' => ['ekit_text_none','ekit_text_daigonal'],
				],
			]
		);

		$this->add_control(
			'ekit_title_animation_duration',
			[
				'label' => esc_html__('Title Animation Duration (s)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_bottom_to_top .ekit_image_caption_title' => 'transition-duration: {{SIZE}}s !important;',
				],
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
					'ekit_text_effect!' => ['ekit_text_none','ekit_text_daigonal'],
				],
			]
		);

		$this->add_control(
			'ekit_title_animation_delay',
			[
				'label' => esc_html__('Title Animation Delay (s)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_bottom_to_top .ekit_image_caption_title' => 'transition-delay: {{SIZE}}s !important;',
				],
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
					'ekit_text_effect!' => ['ekit_text_none','ekit_text_daigonal'],
				],
			]
		);

		$this->add_control(
			'ekit_description_animation_duration',
			[
				'label' => esc_html__('Description Animation Duration (s)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_bottom_to_top .ekit_image_caption_description' => 'transition-duration: {{SIZE}}s !important;',
				],
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
					'ekit_text_effect!' => ['ekit_text_none','ekit_text_daigonal'],
				],
			]
		);

		$this->add_control(
			'ekit_description_animation_delay',
			[
				'label' => esc_html__('Description Animation Delay (s)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_bottom_to_top .ekit_image_caption_description' => 'transition-delay: {{SIZE}}s !important;',
				],
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
					'ekit_text_effect!' => ['ekit_text_none','ekit_text_daigonal'],
				],
			]
		);

		$this->add_control(
			'ekit_button_animation_duration',
			[
				'label' => esc_html__('Button Animation Duration (s)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_bottom_to_top .ekit_image_caption_button' => 'transition-duration: {{SIZE}}s !important;',
				],
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
					'ekit_text_effect!' => ['ekit_text_none','ekit_text_daigonal'],
				],
			]
		);

		$this->add_control(
			'ekit_button_animation_delay',
			[
				'label' => esc_html__('Button Animation Delay (s)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_bottom_to_top .ekit_image_caption_button' => 'transition-delay: {{SIZE}}s !important;',
				],
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
					'ekit_text_effect!' => ['ekit_text_none','ekit_text_daigonal'],
				],
			]
		);

		$this->end_controls_section();

		/** widget style controls */

		$this->start_controls_section(
			'_section_content_style',
			[
				'label' => esc_html__('Content Wrap', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
			]

		);

		$this->add_control(
			'ekit_wrapper_align',
			[
				'label' => esc_html__( 'Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon' => 'eicon-text-align-right',
					],
                ],
                'selectors'=> [
                    '{{WRAPPER}} .ekit_image_caption' => 'text-align: {{VALUE}}; align-items: {{VALUE}};',
                ],
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
					'ekit_text_effect!' => 'ekit_text_daigonal',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_content_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit_image_caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
		
		//title style section
		$this->start_controls_section(
			'_section_title_style',
			[
				'label' => esc_html__('Title', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
				],
			]
		);

		$this->add_control(
			'ekit_title_align',
			[
				'label' => esc_html__( 'Text Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon' => 'eicon-text-align-right',
					],
                ],
				'default' => 'left',
                'selectors'=> [
                    '{{WRAPPER}} .ekit_text_daigonal .ekit_image_caption_title' => 'text-align: {{VALUE}};',
                ],
				'condition' => [
					'ekit_text_effect' => 'ekit_text_daigonal',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'label' => esc_html__('Title Typography', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit_image_caption_title, {{WRAPPER}} .ekit_image_caption_title_show',
				'fields_options' => [
					'typography' => ['default' => 'yes'],
					'font_family' => [
						'default' => 'Roboto',
					],
				],
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'focused_typography',
				'label' => esc_html__('Focused Title Typography', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit_image_caption_title span, {{WRAPPER}} .ekit_image_caption_title_show span',
				'fields_options' => [
					'typography' => ['default' => 'yes'],
					'font_family' => [
						'default' => 'Roboto',
					],
				],
			]
		);

		$this->add_control(
			'title_border',
			[
				'label' => esc_html__( 'Use Border?', 'elementskit' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'ekit_text_effect!' => 'ekit_text_daigonal',
				],
			]
		);

		$this->add_control(
			'title_border_spacing',
			[
				'label' => esc_html__('Border Spacing (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_image_caption_title' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_text_effect!' => 'ekit_text_daigonal',
					'title_border' => 'yes'
				],

			]
		);

		$this->add_control(
			'title_border_width',
			[
				'label' => esc_html__('Border Width (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_image_caption_title' => 'border-width: {{SIZE}}{{UNIT}}; border-left-style: solid;',
				],
				'condition' => [
					'ekit_text_effect!' => 'ekit_text_daigonal',
					'title_border' => 'yes'
				],

			]
		);

		$this->start_controls_tabs('style_tabs');

		$this->start_controls_tab(
			'tab_normal_title',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__('Title Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_image_caption_title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'focused_color',
			[
				'label' => esc_html__('Focused Title Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_image_caption_title_show span' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					'{{WRAPPER}} .ekit_image_caption_title span' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_hover_title',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_control(
			'title_hover_color',
			[
				'label' => esc_html__('Title Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_image_hover:hover .ekit_image_caption_title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'focused_hover_color',
			[
				'label' => esc_html__('Focused Title Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_image_hover:hover .ekit_image_caption_title span' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					'{{WRAPPER}} .ekit_image_hover:hover .ekit_image_caption_title_show span' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'title_margin',
			[
				'label' => esc_html__('Margin', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px','%'],
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_image_caption_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		// description style section
		$this->start_controls_section(
			'_section_description_style',
			[
				'label' => esc_html__('Description', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
				],
			]
		);

		$this->add_control(
			'ekit_description_align',
			[
				'label' => esc_html__( 'Text Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon' => 'eicon-text-align-right',
					],
                ],
				'default' => 'right',
                'selectors'=> [
                    '{{WRAPPER}} .ekit_text_daigonal .ekit_image_caption_description' => 'text-align: {{VALUE}};',
                ],
				'condition' => [
					'ekit_text_effect' => 'ekit_text_daigonal',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_description_width',
			[
				'label' => esc_html__('Width', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px','%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1200,
						'step' => 5,
					],
				],
				'devices' => ['desktop', 'tablet', 'mobile'],
				'desktop_default' => [
					'size' => 150,
					'unit' => 'px',
				],
				'tablet_default' => [
					'size' => 150,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 150,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_image_caption_description' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_text_effect' => 'ekit_text_daigonal',
				],

			]
		);

		$this->add_responsive_control(
			'ekit_description_border_spacing',
			[
				'label' => esc_html__('Border Spacing', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px','em'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'devices' => ['desktop', 'tablet', 'mobile'],
				'selectors' => [
					'{{WRAPPER}} .ekit_text_daigonal .ekit_image_caption_description' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_text_effect' => 'ekit_text_daigonal',
				],

			]
		);

		$this->add_control(
			'ekit_description_border_width',
			[
				'label' => esc_html__('Border Width (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_text_daigonal .ekit_image_caption_description' => 'border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_text_effect' => 'ekit_text_daigonal',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'label' => esc_html__('Description Typography', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit_image_caption_description, {{WRAPPER}} .ekit_image_caption_description_show',
				'fields_options' => [
					'typography' => ['default' => 'yes'],
					'font_family' => [
						'default' => 'Roboto',
					],
				],
			]
		);

		$this->add_control(
			'description_border_use',
			[
				'label' => esc_html__( 'Use Border?', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'ekit_text_effect!' => 'ekit_text_daigonal',
				],
			]
		);

		$this->add_control(
			'description_border_spacing_other',
			[
				'label' => esc_html__('Border Spacing (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_image_caption_description' => 'padding-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_text_effect!' => 'ekit_text_daigonal',
					'description_border_use' => 'yes'
				],

			]
		);

		$this->add_control(
			'description_border_width_other',
			[
				'label' => esc_html__('Border Width (px)', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 4,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_image_caption_description' => 'border-width: {{SIZE}}{{UNIT}}; border-left-style: solid;',
				],
				'condition' => [
					'ekit_text_effect!' => 'ekit_text_daigonal',
					'description_border_use' => 'yes'
				],

			]
		);

		$this->start_controls_tabs('_tabs_style');

		$this->start_controls_tab(
			'tab_normal_description',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_control(
			'description_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_image_caption_description' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_hover_description',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_control(
			'description_hover_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_image_hover:hover .ekit_image_caption_description' => 'color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'description_margin',
			[
				'label' => esc_html__('Margin Between', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px','%'],
				'default' => [
					'top' => '10',
					'right' => '0',
					'bottom' => '8',
					'left' => '0',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_image_caption_description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		//style for button
		$this->start_controls_section(
			'_section_button_style',
			[
				'label' => esc_html__('Button', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_hover_image_btn' => 'yes',
					'ekit_text_effect!' => 'ekit_text_daigonal',
					'ekit_hover_effect!' => ['ekit_effect_swap','ekit_effect_glitch'],
				],
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_image_btn_typography_group',
				'label' =>esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit_image_caption_button',
			]
		);

        $this->start_controls_tabs( 'tabs_button_style' );

        $this->start_controls_tab(
            'ekit_image_tab_button_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_image_box_button_text_color',
            [
                'label' => esc_html__( 'Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ekit_image_hover .elementskit-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_image_btn_background_group',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'classic' => 'image'
				],
                'selector' => '{{WRAPPER}} .ekit_image_hover .elementskit-btn',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_image_button_border_color_group',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit_image_caption_button',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_image_tab_button_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_responsive_control(
            'ekit_image_btn_hover_color',
            [
                'label' => esc_html__( 'Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit_image_caption_button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_image_btn_background_hover_group',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'types' => [ 'classic', 'gradient' ],
				'exclude' => [
					'classic' => 'image'
				],
                'selector' => '{{WRAPPER}} .ekit_image_caption_button:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_image_button_border_hv_color_group',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit_image_caption_button:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_image_btn_padding',
			[
				'label' =>esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit_image_caption_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_image_btn_border_radius',
			[
				'label' =>esc_html__( 'Border Radius (px)', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],
				'default' => [
					'top' => '',
					'right' => '',
					'bottom' => '' ,
					'left' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_image_caption_button' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_image_button_box_shadow',
                'selector' => '{{WRAPPER}} .ekit_image_caption_button',
            ]
        );

		$this->end_controls_section();    

		/** Background Overlay */
		$this->start_controls_section(
			'_section_overlay_style',
			[
				'label' => esc_html__('Background Overlay', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_hover_effect!' => ['ekit_effect_swap', 'ekit_effect_grayscale','ekit_effect_glitch'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'ekit_hover_overlay',
				'label' => esc_html__('Background', 'elementskit'),
				'show_label' => true,
				'types' => ['classic', 'gradient'],
				'exclude' => [
					'classic' => 'image'
				],
				'selector' => '{{WRAPPER}} .ekit_creative_image_hover::before, {{WRAPPER}} .ekit_creative_image_hover::after, {{WRAPPER}} .ekit_creative_image_hover .ekit_overlay_inner::before, {{WRAPPER}} .ekit_creative_image_hover .ekit_overlay_inner::after, {{WRAPPER}} .ekit_image_circle::before, {{WRAPPER}} .ekit_image_fade::before, {{WRAPPER}} .ekit_image_flash::before, {{WRAPPER}} .ekit_image_flash::after, {{WRAPPER}} .ekit_image_shutter_out::before, {{WRAPPER}} .ekit_image_slide::before, {{WRAPPER}} .ekit_splash_effect_on_hover::before, {{WRAPPER}} .ekit_image_blend_mode::before, {{WRAPPER}} .ekit_image_double_splash::after, {{WRAPPER}} .ekit_image_zoom_in_overlay::before, {{WRAPPER}} .ekit_image_corner_zoom_back:hover, {{WRAPPER}} .ekit_image_border_reveal_horizontal:hover, {{WRAPPER}} .ekit_image_scale_rotate_left::before, {{WRAPPER}} .ekit_image_zoom_in::before, {{WRAPPER}} .ekit_image_zoom_in_blur::before, {{WRAPPER}} .ekit_image_zoom_out::before, {{WRAPPER}} .ekit_image_scroll_effect::before',
			]
		);

		$this->add_control(
			'ekit_hover_background_color',
			[
				'label' => esc_html__('Second Background Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit_image_double_splash::before' => 'background-color: {{VALUE}};',
				],
				'separator' => 'before',
				'condition' => [
					'ekit_hover_effect' => ['ekit_effect_splash'],
					'ekit_splash_effect' => ['ekit_splash_effect_double'],
				],
			]
		);

		$this->add_control(
			'ekit_hover_flash_background_color',
			[
				'label' => esc_html__('Active Background Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit_image_flash::after' => 'background-color: {{VALUE}};',
				],
				'separator' => 'before',
				'condition' => [
					'ekit_hover_effect' => ['ekit_effect_flash'],
				],
			]
		);

		$this->end_controls_section(); 

		/** Border Style */
		$this->start_controls_section(
			'_section_border_style',
			[
				'label' => esc_html__('Border', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_hover_effect' => ['ekit_effect_blend','ekit_effect_corner_zoom_back', 'ekit_effect_border'],
				],
			]
		);

		$this->add_control(
			'ekit_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit_image_corner_zoom_back::before, {{WRAPPER}} .ekit_image_corner_zoom_back::after, {{WRAPPER}} .ekit_image_corner_zoom_back .ekit_overlay_inner::before, {{WRAPPER}} .ekit_image_corner_zoom_back .ekit_overlay_inner::after' => ' border-color: {{VALUE}};',
					'{{WRAPPER}} .ekit_image_blend_mode::after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .ekit_creative_image_hover::before, {{WRAPPER}} .ekit_creative_image_hover::after, {{WRAPPER}} .ekit_creative_image_hover .ekit_overlay_inner::before, {{WRAPPER}} .ekit_creative_image_hover .ekit_overlay_inner::after' => 'background-color: {{VALUE}};',
				],		
			]
		);

		$this->add_responsive_control(
			'ekit_hover_border_width',
			[
				'label' =>esc_html__( 'Border Width (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_image_corner_zoom_back::before' => 'border-width: 0px {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0px;',
					'{{WRAPPER}} .ekit_image_corner_zoom_back::after' => 'border-width: 0px 0px {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit_image_corner_zoom_back .ekit_overlay_inner::before' => 'border-width: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0px 0px;',
					'{{WRAPPER}} .ekit_image_corner_zoom_back .ekit_overlay_inner::after' => 'border-width: {{SIZE}}{{UNIT}} 0px 0px {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit_image_blend_mode::after' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit_image_border_reveal_horizontal::before, {{WRAPPER}} .ekit_image_border_reveal_horizontal::after' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit_image_border_reveal_horizontal .ekit_overlay_inner::before, {{WRAPPER}} .ekit_image_border_reveal_horizontal .ekit_overlay_inner::after' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_hover_border_padding',
			[
				'label' =>esc_html__( 'Border Spacing (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit_image_border_reveal_horizontal::before, {{WRAPPER}} .ekit-wid-con .ekit_image_border_reveal_horizontal::after' => 'left: {{SIZE}}{{UNIT}}; right:{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit_image_border_reveal_horizontal::before' => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit_image_border_reveal_horizontal::after' => 'bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit_image_border_reveal_horizontal .ekit_overlay_inner::before' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit_image_border_reveal_horizontal .ekit_overlay_inner::after' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit_image_border_reveal_horizontal .ekit_overlay_inner::before, {{WRAPPER}} .ekit-wid-con .ekit_image_border_reveal_horizontal .ekit_overlay_inner::after' => 'top: {{SIZE}}{{UNIT}}; bottom:{{SIZE}}{{UNIT}};',	
					'{{WRAPPER}} .ekit-wid-con .ekit_image_corner_zoom_back::before' => 'bottom: {{SIZE}}{{UNIT}}; right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit_image_corner_zoom_back::after' => 'bottom: {{SIZE}}{{UNIT}}; left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit_image_corner_zoom_back .ekit_overlay_inner::before' => 'top: {{SIZE}}{{UNIT}}; right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit_image_corner_zoom_back .ekit_overlay_inner::after' => 'top: {{SIZE}}{{UNIT}}; left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_hover_effect' => ['ekit_effect_corner_zoom_back', 'ekit_effect_border'],
				],
			]
		);

		$this->end_controls_section();

		/** Image Style */
		$this->start_controls_section(
			'_section_image_style',
			[
				'label' => esc_html__('Image', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_image_opacity',
			[
				'label' => esc_html__( 'Opacity', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit_image_hover > img' => 'opacity: {{SIZE}}',
				]
			]
		);

		$this->add_control(
			'ekit_image_hover_opacity',
			[
				'label' => esc_html__( 'Hover Opacity', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1,
						'step' => 0.1
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit_image_hover:hover > img' => 'opacity: {{SIZE}}',
				]
			]
		);

		$this->add_control(
			'ekit_image_border_radius',
			[
				'label' => esc_html__( 'Border Radius (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit_image_hover' => 'border-radius: {{SIZE}}px',
				]
			]
		);

		$this->add_control(
			'ekit_width',
			[
				'label' => esc_html__( 'Blur (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit_image_zoom_in_blur:hover > img' => 'filter: blur({{SIZE}}{{UNIT}});',
				],
				'condition' => [
					'ekit_hover_effect' => 'ekit_effect_zoom',
					'ekit_zoom_effect' => 'ekit_zoom_effect_in_blur'
				],
			]
		);
		
		$this->end_controls_section();
    }

     protected function render() {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
    }

	function image_link($ekit_hover_link) {
		 if (!empty($ekit_hover_link['url'])) {
			$this->add_link_attributes( 'ekit_hover_link', $ekit_hover_link );
			echo sprintf('<a class="ekit-wrapper-link" %1$s></a>', $this->get_render_attribute_string('ekit_hover_link'));
		}
	}

    protected function render_raw() {
        $settings = $this->get_settings_for_display();
        extract($settings);

		// image effect class
        $image_hover_class = "";
        switch($ekit_hover_effect) {
            case 'ekit_effect_blind':
                switch($settings['ekit_blind_effect']) {
                    case 'ekit_blind_horizontal':
                        $image_hover_class = "creative_image_hover ekit_image_blind ekit_image_blind_horizontal";
                        break;
                    case 'ekit_blind_vertical':
                        $image_hover_class = "creative_image_hover ekit_image_blind ekit_image_blind_vertical";
                        break;
                }
                break;
            case 'ekit_effect_block':
                $image_hover_class = "creative_image_hover ekit_image_blocks ekit_image_blocks_right";
                break;
            case 'ekit_effect_border':
                $image_hover_class = "creative_image_hover ekit_image_border_reveal ekit_image_border_reveal_horizontal";
                break;
            case 'ekit_effect_circle':
                $image_hover_class = "image_hover_filter ekit_image_circle ekit_image_circle_up";
                break;
            case 'ekit_effect_fade':
                $image_hover_class = "image_hover_filter ekit_image_fade ekit_image_fade_up";
                break;
            case 'ekit_effect_flash':
                $image_hover_class = "image_hover_filter ekit_image_flash ekit_image_flash_top_left";
                break;
            case 'ekit_effect_sutter_out':
                $image_hover_class = "ha-effect-diamond";
                switch($settings['ekit_sutter_out_effect']) {
                    case 'ekit_sutter_out_horizontal':
                        $image_hover_class = "image_hover_filter ekit_image_shutter_out ekit_image_shutter_out_horizontal";
                        break;
                    case 'ekit_sutter_out_vertical':
                        $image_hover_class = "image_hover_filter ekit_image_shutter_out ekit_image_shutter_out_vertical";
                        break;
                    case 'ekit_sutter_out_diagonal_right':
                        $image_hover_class = "image_hover_filter ekit_image_shutter_out ekit_image_shutter_out_diagonal_right";
                        break;
                }
                break;
            case 'ekit_effect_slide':
                switch($settings['ekit_slide_effect']) {
                    case 'ekit_slide_effect_up':
                        $image_hover_class = "image_hover_filter ekit_image_slide ekit_image_slide_up";
                        break;
                    case 'ekit_slide_effect_right':
                        $image_hover_class = "image_hover_filter ekit_image_slide ekit_image_slide_right";
                        break;     
                }
                break;
            case 'ekit_effect_splash':
                switch($settings['ekit_splash_effect']) {
                    case 'ekit_splash_effect_single':
                        $image_hover_class = "image_hover_filter ekit_splash_effect_on_hover";
                        break;
                    case 'ekit_splash_effect_double':
                        $image_hover_class = "image_hover_filter ekit_image_double_splash";
                        break;
                }
                break;
            case 'ekit_effect_blend':
                $image_hover_class = "image_hover_filter ekit_image_blend_mode";
                break;
            case 'ekit_effect_zoom_in_overly':
                $image_hover_class = "image_hover_filter ekit_image_zoom_in_overlay";
                break;
            case 'ekit_effect_corner_zoom_back':
                $image_hover_class = "creative_image_hover ekit_image_corner_zoom_back";
                break;
            case 'ekit_effect_zoom':
                switch($settings['ekit_zoom_effect']) {
					case 'ekit_zoom_effect_in':
						$image_hover_class = "image_hover ekit_image_zoom_in";
						break;
					case 'ekit_zoom_effect_out': 
						$image_hover_class = "image_hover ekit_image_zoom_out";
						break; 
					case 'ekit_zoom_effect_in_blur': 
						$image_hover_class = "image_hover ekit_image_zoom_in_blur";
						break; 
                }
                break;
            case 'ekit_effect_scale_rotate':
                $image_hover_class = "image_hover ekit_image_scale_rotate_left";
                break;
            case 'ekit_effect_swap':
                switch($settings['ekit_swap_effect']) {
					case 'ekit_swap_effect_one':
						$image_hover_class = "image_hover ekit_image_swap_effect ekit_image_swap_effect_one";
						break;
					case 'ekit_swap_effect_two':
						$image_hover_class = "image_hover ekit_image_swap_effect ekit_image_swap_effect_two";
						break;
					case 'ekit_swap_effect_three':
						$image_hover_class = "image_hover ekit_image_swap_effect ekit_image_swap_effect_three";
						break;
				}
				break;
            case 'ekit_effect_scroll':
                $image_hover_class = "image_hover ekit_image_scroll_effect";
                break;
            case 'ekit_effect_grayscale':
				switch($settings['ekit_grayscale_effect']) {
					case 'ekit_grayscale_to_color_effect':
						$image_hover_class = "image_hover ekit_grayscale_to_color";
						break;
					case 'ekit_color_to_grayscale_effect':
						$image_hover_class = "image_hover ekit_color_to_grayscale";
				}
                break;
            case 'ekit_effect_glitch':
                $image_hover_class = "image_hover ekit_image_glitch_effect";
                break;
            default:
                $image_hover_class = "";
                break;
        }

		// button link
		if ( !empty( $ekit_image_button_link['url'] ) ) {
			$this->add_link_attributes( 'ekit_image_button_link', $ekit_image_button_link );
		}

		// text effect class
		$hover_text_class = '';
		switch($ekit_text_effect) {
			case 'ekit_text_daigonal':
				$hover_text_class = 'ekit_text_daigonal';
				break;
			case 'ekit_text_right_to_left':
				$hover_text_class = 'ekit_right_to_left';
				break;
			case 'ekit_text_left_to_right':
				$hover_text_class = 'ekit_left_to_right';
				break;
			case 'ekit_text_top_to_bottom':
				$hover_text_class = 'ekit_top_to_bottom';
				break;
			case 'ekit_text_bottom_to_top':
				$hover_text_class = 'ekit_bottom_to_top';
				break;
			case 'ekit_text_none':
				$hover_text_class = 'ekit_text_none';
				break;
		}

		// show hide options from title, description, button
		$title_show = $ekit_hover_title_switch == 'yes' ? 'ekit_hover_show' : '';
		$description_show = $ekit_hover_description_switch == 'yes' ? 'ekit_hover_show' : '';
		$button_show = $ekit_hover_button_switch == 'yes' ? 'ekit_hover_show' : '';
		$title_stable_switch = $ekit_hover_title_stable_switch == 'yes' ? 'ekit_transform_stop' : '';
		$description_stable_switch = $ekit_hover_description_stable_switch == 'yes' ? 'ekit_transform_stop' : '';
		$button_stable_switch = $ekit_hover_button_stable_switch == 'yes' ? 'ekit_transform_stop' : '';

		// hover amination class
		$hoverAnimatedClass = '';
		if ( $ekit_hover_animation ) {
			$hoverAnimatedClass .= ' elementor-animation-' . $ekit_hover_animation;
		}

		// entrance animation class
		$entranceAnimatedClass = '';
		if ( $ekit_entrance_animation) {
			$entranceAnimatedClass = ' animated ' . $ekit_entrance_animation;
		}

		// entrance animation data attribute
		$entranceAnimationData = [
			'_animation' => $ekit_entrance_animation
		];

		// image attribute
		$image_html = $image_html2 = '';
		$image_html = Group_Control_Image_Size::get_attachment_image_html($settings, 'ekit_image', 'ekit_hover_image');
		if(isset($ekit_hover_image2)) {
			$image_html2 = Group_Control_Image_Size::get_attachment_image_html($settings, 'ekit_image', 'ekit_hover_image2');
		}
	
		// image markup
		if($ekit_hover_effect == 'ekit_effect_zoom' || $ekit_hover_effect == 'ekit_effect_scale_rotate' || $ekit_hover_effect == "ekit_effect_scroll" || $ekit_hover_effect == "ekit_effect_grayscale") { ?>
			<div class="ekit_<?php echo esc_attr($image_hover_class); ?>">
				<?php if(!empty($image_html)) {
					echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array());
				}
				$this->image_link($ekit_hover_link); 
				?>
				<div class="ekit_image_caption <?php echo esc_attr($hover_text_class.' '.$hoverAnimatedClass); ?>">
					<h2 class="ekit_image_caption_title <?php echo esc_attr($title_show.' '.$title_stable_switch.' '.$entranceAnimatedClass); ?>" data-settings="<?php echo esc_attr(json_encode($entranceAnimationData)); ?>">
						<?php echo wp_kses( \ElementsKit_Lite\Utils::kspan($ekit_hover_title), \ElementsKit_Lite\Utils::get_kses_array()); ?>
					</h2>
					<p class="ekit_image_caption_description <?php echo esc_attr($description_show.' '.$description_stable_switch.' '.$entranceAnimatedClass); ?>" data-settings="<?php echo esc_attr(json_encode($entranceAnimationData)); ?>">
						<?php echo esc_html($ekit_hover_description);?>
					</p>
					<?php if($ekit_hover_image_btn == 'yes'): ?>	
						<a class="elementskit-btn ekit_image_caption_button <?php echo esc_attr($button_show.' '.$button_stable_switch.' '.$entranceAnimatedClass); ?>" data-settings="<?php echo esc_attr(json_encode($entranceAnimationData)); ?>" <?php $this->print_render_attribute_string( 'ekit_image_button_link' ); ?>>
							<?php echo esc_html($ekit_image_button_text); ?>
						</a>		
					<?php endif; ?>
				</div>
			</div>
		<?php } elseif($ekit_hover_effect == 'ekit_effect_swap') { ?>
			<div class="ekit_<?php echo esc_attr($image_hover_class); ?>">
				<?php if(!empty($image_html)) {
					echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array());
				}
				if(!empty($image_html2)) {
					echo wp_kses($image_html2, \ElementsKit_Lite\Utils::get_kses_array());
				}
				?>
				<?php $this->image_link($ekit_hover_link); ?>
			</div>
		<?php } elseif($ekit_hover_effect == 'ekit_effect_glitch') { ?>
			<div class="ekit_<?php echo esc_attr($image_hover_class); ?>">
				<div style="background-image: url(<?php echo !empty($ekit_hover_image['url']) ? esc_url($ekit_hover_image['url']) : ''; ?>)" class="ekit_main_image"></div>
				<div style="background-image: url(<?php echo !empty($ekit_hover_image['url']) ? esc_url($ekit_hover_image['url']) : ''; ?>)" class="ekit_secondary_image"></div>
				<?php $this->image_link($ekit_hover_link); ?>
			</div>	
		<?php } else { ?>
			<div class="ekit_image_hover">
				<div class="ekit_<?php echo esc_attr($image_hover_class); ?>">
					<div class="ekit_overlay_inner"></div>
					<?php $this->image_link($ekit_hover_link); ?>
					<div class="ekit_image_caption <?php echo esc_attr($hover_text_class.' '.$hoverAnimatedClass); ?>" >
						<h2 class="ekit_image_caption_title <?php echo esc_attr($title_show.' '.$title_stable_switch.' '.$entranceAnimatedClass); ?>" data-settings="<?php echo esc_attr(json_encode($entranceAnimationData)); ?>">
							<?php echo wp_kses( \ElementsKit_Lite\Utils::kspan($ekit_hover_title), \ElementsKit_Lite\Utils::get_kses_array())?>
						</h2>
						<p class="ekit_image_caption_description <?php echo esc_attr($description_show.' '.$description_stable_switch.' '.$entranceAnimatedClass); ?>" data-settings="<?php echo esc_attr(json_encode($entranceAnimationData)); ?>">
							<?php echo esc_html($ekit_hover_description);?>
						</p>
						<?php if($ekit_hover_image_btn == 'yes'): ?>	
							<a class="elementskit-btn ekit_image_caption_button <?php echo esc_attr($button_show.' '.$button_stable_switch.' '.$entranceAnimatedClass); ?>" <?php $this->print_render_attribute_string( 'ekit_image_button_link' ); ?> data-settings="<?php echo esc_attr(json_encode($entranceAnimationData));?>">
								<?php echo esc_html($ekit_image_button_text); ?>
							</a>		
						<?php endif; ?>		
					</div>			
				</div>
				<?php if(!empty($image_html)) {
					echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array());
				} ?>
			</div>
		<?php }
	}
}
