<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Content_Ticker_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Content_Ticker extends Widget_Base {

	use \ElementsKit_Lite\Widgets\Widget_Notice;

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
	
	public function get_keywords(){
		return Handler::get_keywords();
	}

	public function get_help_url() {
		return Handler::get_help_url();
	}
	
	protected function register_controls() {

		// Settings options section
		$this->start_controls_section(
			'ekit_section_content_ticker_title',
			[
				'label' => esc_html__('Ticker Title', 'elementskit'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ekit_content_ticker_title_show',
			[
				'label' => esc_html__('Show Title', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Show', 'elementskit'),
				'label_off' => esc_html__('Hide', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->add_control(
			'ekit_content_ticker_title_style',
			[
				'label' => esc_html__('Title Style', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'label_block' => false,
				'options' => [
					'' => esc_html__( 'Default', 'elementskit' ),
					'top' => esc_html__('Diagonal Top', 'elementskit'),
					'bottom' => esc_html__('Diagonal Bottom', 'elementskit'),
					'middle' => esc_html__('Arrow', 'elementskit'),
				],
				'selectors_dictionary' => [
					'top'    => 'transform: skew(20deg); -webkit-transform: skew(20deg); min-width: 100%;',
					'middle' => 'transform: translateY(-50%); -webkit-transform: translateY(-50%);',
					'bottom' => 'transform: skew(-20deg); -webkit-transform: skew(-20deg); min-width: 100%;',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title::before' => '{{VALUE}}',
				],
				'prefix_class' => 'ekit-title-style-',
				'default' => '',
				'condition' => ['ekit_content_ticker_title_show' => 'yes']
			]
		);

		$this->add_control(
			'ekit_content_ticker_title',
			[
				'label'       => esc_html__('Title Text', 'elementskit'),
				'type'        => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default'     => esc_html__('Trending Today', 'elementskit'),
				'condition' => ['ekit_content_ticker_title_show' => 'yes']
			]
		);

		$this->add_control(
			'ekit_content_ticker_title_pointer',
			[
				'label'       => esc_html__('Enable Pointer', 'elementskit'),
				'type'        => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Show', 'elementskit'),
				'label_off' => esc_html__('Hide', 'elementskit'),
				'return_value' => 'yes',
				'condition' => ['ekit_content_ticker_title_show' => 'yes']
			]
		);

		$this->add_control(
			'ekit_content_ticker_title_icon',
			[
				'label' => esc_html__('Icon', 'elementskit'),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
				'condition' => [
					'ekit_content_ticker_title_show' => 'yes',
					'ekit_content_ticker_title_pointer!' => 'yes'
				]
			]
		);
		
		$this->add_control(
			'ekit_content_ticker_icon_position',
			[
				'label' => esc_html__('Position', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => esc_html__('Before', 'elementskit'),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__('After', 'elementskit'),
						'icon' => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'selectors_dictionary' => [
					'left' => 'flex-direction: row-reverse;',
					'right' => 'flex-direction: reverse;',
				],
				'prefix_class' => 'icon-pointer-position-',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'ekit_content_ticker_title_pointer',
							'operator' => '===',
							'value' => 'yes',
						],
						[
							'name' => 'ekit_content_ticker_title_icon[value]',
							'operator' => '!==',
							'value' => '',
						],
					],
				],
				'selectors' => [
					'{{WRAPPER}}  .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title' => '{{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_content_ticker_title_separator',
			[
				'label'       => esc_html__('Enable Separator', 'elementskit'),
				'type'        => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Show', 'elementskit'),
				'label_off' => esc_html__('Hide', 'elementskit'),
				'return_value' => 'yes',
				'prefix_class' => 'separator',
				'condition' => [
					'ekit_content_ticker_title_show' => 'yes',
					'ekit_content_ticker_title_style!' => 'middle'
				]
			]
		);

		$this->end_controls_section();

		// Content / Query options section
		$this->start_controls_section(
			'ekit_section_content_ticker_content',
			[
				'label' => esc_html__('Ticker Content / Query', 'elementskit'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ekit_content_ticker_select',
			[
				'label' => esc_html__('Content Select', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'default' => 'post',
				'options' => [
					'post' => esc_html__('Post', 'elementskit'),
					'custom' => esc_html__('Custom', 'elementskit'),
				],
			]
		);

		$this->add_control(
			'ekit_content_ticker_num',
			[
				'label'     => esc_html__('Content Count', 'elementskit'),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1,
				'max'       => 100,
				'default'   => 3,
				'condition' => [  
					'ekit_content_ticker_select' => 'post'
				]
			]
		);

		$this->add_control(
			'ekit_content_ticker_trim',
			[
				'label'     => esc_html__('Title Trim', 'elementskit'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => '10',
				'condition' => [  
					'ekit_content_ticker_select' => 'post'
				]
			]
		);

		$this->add_control(
			'ekit_content_ticker_trim_description',
			[
				'label'     => esc_html__('Description Trim', 'elementskit'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => '15',
				'condition' => [  
					'ekit_content_ticker_select' => 'post'
				]
			]
		);

		$this->add_control(
			'ekit_content_ticker_is_manual_selection',
			[
				'label' => esc_html__('Select Content by:', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					'recent'    => esc_html__('Recent Post', 'elementskit'),
					'yes'       => esc_html__('Selected Post', 'elementskit'),
					''        => esc_html__('Category Post', 'elementskit'),
				],
				'condition' => [  
					'ekit_content_ticker_select' => 'post'
				]
	
			]
		);

		$this->add_control(
			'ekit_content_manual_selection',
			[
				'label' =>esc_html__('Search & Select', 'elementskit'),
				'type'      => ElementsKit_Controls_Manager::AJAXSELECT2,
				'options'   =>'ajaxselect2/post_list',
				'label_block' => true,
				'multiple'  => true,
				'condition' => [ 
					'ekit_content_ticker_is_manual_selection' => 'yes', 
					'ekit_content_ticker_select' => 'post'
				]
			]
		);

		$this->add_control(
			'ekit_content_ticker_cats',
			[
				'label' =>esc_html__('Select Categories', 'elementskit'),
				'type'      => ElementsKit_Controls_Manager::AJAXSELECT2,
				'options'   =>'ajaxselect2/category',
				'label_block' => true,
				'multiple'  => true,
				'condition' => [ 
					'ekit_content_ticker_is_manual_selection' => '' ,
					'ekit_content_ticker_select' => 'post'
				]
			]
		);

		$this->add_control(
			'ekit_content_ticker_sort',
			[
				'label'   => esc_html__('Order', 'elementskit'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'ASC'  => esc_html__('ASC', 'elementskit'),
					'DESC' => esc_html__('DESC', 'elementskit'),
				],
				'default' => 'DESC',
				'condition' => [ 
					'ekit_content_ticker_select' => 'post'
				]
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'ekit_content_ticker_text',
			[
				'label' => esc_html__('Content Title', 'elementskit'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__('Content Item' , 'elementskit'),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'ekit_content_ticker_description',
			[
				'label' => esc_html__( 'Content Description', 'elementskit'),
				'type' => Controls_Manager::TEXTAREA,
				'default' => esc_html__( 'ElementsKit Elementor addons is an ultimate and all-in-one addons for Elementor Page Builder', 'elementskit'),
			]
		);

		$repeater->add_control(
			'ekit_content_ticker_image_choose',
			[
				'label' => esc_html__('Choose Image', 'elementskit'),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
					'id' => '-1'
				],
			]
		);

		$repeater->add_control(
			'ekit_content_ticker_text_url',
			[
				'label' => esc_html__('Link', 'elementskit'),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'elementskit'),
			],
		);

		$this->add_control(
			'ekit_content_ticker_text_list',
			[
				'label' => esc_html__('Content Items', 'elementskit'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'ekit_content_ticker_text' => esc_html__('Content Item #1', 'elementskit'),
						'ekit_content_ticker_description' => esc_html__('ElementsKit Elementor addons is an ultimate and all-in-one addons for Elementor Page Builder', 'elementskit'),
					],
					[
						'ekit_content_ticker_text' => esc_html__('Content Item #2', 'elementskit'),
						'ekit_content_ticker_description' => esc_html__('ElementsKit Elementor addons is an ultimate and all-in-one addons for Elementor Page Builder', 'elementskit'),
					],
				],
				'title_field' => '{{{ ekit_content_ticker_text }}}',
				'condition' => [ 
					'ekit_content_ticker_select' => 'custom'
				]
			]
		);

		$this->end_controls_section();

		// Settings options section
		$this->start_controls_section(
			'ekit_section_content_settings',
			[
				'label' => esc_html__('Settings', 'elementskit'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ekit_content_ticker_feature_img',
			[
				'label'     => esc_html__('Show Image', 'elementskit'),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'default'   => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'ekit_content_ticker_image',
				'default' => 'thumbnail',
				'condition' => [ 
					'ekit_content_ticker_feature_img' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_content_ticker_content_shadow',
			[
				'label' => esc_html__( 'Enable Shadow', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'no',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee::before' => 'background-image: linear-gradient(to left,rgba(255,255,255,0),{{_background_color.VALUE}});',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee::after' => 'background-image: linear-gradient(to right,rgba(255,255,255,0),{{_background_color.VALUE}});',
				],
			]
		);

		$this->add_control(
			'ekit_content_ticker_content_description',
			[
				'label' => esc_html__( 'Show Description', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_control(
			'ekit_content_ticker_effect_style',
			[
				'label' => esc_html__('Effect', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'default' => 'slide',
				'options' => [
					'slide'  => esc_html__('Default', 'elementskit'),
					'fade'  => esc_html__('Fade', 'elementskit'),
					'marquee' => esc_html__('Marquee', 'elementskit'),
					'typing' => esc_html__('Typing', 'elementskit'),
				],
			]
		);

		$this->add_control(
			'ekit_content_ticker_direction',
			[
				'label' => esc_html__('Direction', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal'  => esc_html__('Horizontal', 'elementskit'),
					'vertical'  => esc_html__('Vertical', 'elementskit'),
				],
				'condition' => [ 
					'ekit_content_ticker_effect_style!' => 'marquee'
				]
			]
		);

		$this->add_control(
			'ekit_content_ticker_speed',
			[
				'label'     => esc_html__('Animation Speed (s)', 'elementskit'),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => .5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'condition' => [ 
					'ekit_content_ticker_effect_style!' => 'typing'
				],
				'selectors' => [
					'{{WRAPPER}}  .ekit-content-ticker-wrapper .ticker .marquee-wrapper' => '--transition-timing: {{SIZE}}s',
				],
			]
		);

		$this->add_control(
			'ekit_content_ticker_auto_play',
			[
				'label'     => esc_html__('Auto Play', 'elementskit'),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'default'   => 'yes',
				'condition' => [ 
					'ekit_content_ticker_effect_style!' => 'marquee'
				]
			]
		);
		
		$this->add_control(
			'ekit_content_ticker_delay',
			[
				'label'     => esc_html__('Delay (m)', 'elementskit'),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10000,
						'step' => .5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 3000,
				],
				'condition' => [ 
					'ekit_content_ticker_auto_play' => 'yes',
					'ekit_content_ticker_effect_style!' => 'marquee'
				]
			]
		);
		
		$this->add_control(
			'ekit_content_ticker_reverse_direction',
			[
				'label'     => esc_html__('Reverse Direction', 'elementskit'),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__('No', 'elementskit'),
				'label_off' => esc_html__('Yes', 'elementskit'),
				'default'   => '',
			]
		);
		
		$this->add_control(
			'ekit_content_ticker_hover_paused',
			[
				'label'     => esc_html__('Hover Paused  ', 'elementskit'),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'default'   => '',
				'condition' => [ 
					'ekit_content_ticker_effect_style' => 'marquee'
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ticker.ticker-left:hover .marquee-wrapper' => '--animation-play-state : paused',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ticker.ticker-right:hover .marquee-wrapper' => '--animation-play-state : paused'
				],
			]
		);
		
		$this->add_control(
			'ekit_content_ticker_gap_between_loop',
			[
				'label'     => esc_html__('Gap Between Loop', 'elementskit'),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'default'   => 'yes',
				'condition' => [ 
					'ekit_content_ticker_effect_style' => 'marquee'
				]
			]
		);
		
		$this->add_control(
			'ekit_content_ticker_grab_cursor',
			[
				'label'     => esc_html__('Grab Cursor', 'elementskit'),
				'type'      => Controls_Manager::SWITCHER,
				'label_on'  => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'default'   => '',
				'condition' => [ 
					'ekit_content_ticker_effect_style!' => 'marquee'
				]
			]
		);

		$this->add_control(
			'ekit_content_ticker_arrow_show',
			[
				'label' => esc_html__( 'Show Arrow', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [ 
					'ekit_content_ticker_effect_style!' => 'marquee'
				]
			]
		);
		
		$this->add_control(
			'ekit_content_ticker_arrow_separator_show',
			[
				'label' => esc_html__( 'Show Separator', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'condition' => [
					'ekit_content_ticker_arrow_show' => 'yes',
					'ekit_content_ticker_effect_style!' => 'marquee'
				],
				'default' => '',
			]
		);
	
		$this->add_control(
			'ekit_content_ticker_nav_hide_mobile',
			[
				'label' => esc_html__( 'Arrow Hide On Mobile', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'ekit_content_ticker_arrow_show' => 'yes',
					'ekit_content_ticker_effect_style!' => 'marquee'
				],
				'selectors' => [
					'{{WRAPPER}}  .ekit-content-ticker-wrapper .ekit-marquee-button' => '--nav-hide-mobile: none',
				],
			]
		);


		$this->add_control(
			'ekit_content_ticker_left_arrow_icon',
			[
				'label' => esc_html__('Left Arrow Icon', 'elementskit'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'eicon-chevron-left',
					'library' => 'solid',
				],
				'condition' => [
					'ekit_content_ticker_arrow_show' => 'yes',
					'ekit_content_ticker_effect_style!' => 'marquee'
				]
			]
		);

		$this->add_control(
			'ekit_content_ticker_right_arrow_icon',
			[
				'label' => esc_html__('Right Arrow Icon', 'elementskit'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'eicon-chevron-right',
					'library' => 'solid',
				],
				'condition' => [
					'ekit_content_ticker_arrow_show' => 'yes',
					'ekit_content_ticker_effect_style!' => 'marquee'
				]
			]
		);

		$this->end_controls_section();

		// wrapper style section
		$this->start_controls_section(
			'ekit_section_content_wrapper',
			[
				'label' => esc_html__('Wrapper', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_content_ticker_wrapper_position',
			[
				'label' => esc_html__('Direction', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'default' => 'left',
				'toggle' => false,
				'options' => [
					'left' => esc_html__('Normal', 'elementskit'),
					'right' => esc_html__('Reverse', 'elementskit'),
				],
				'prefix_class' => 'ticker-position-',
				'selectors_dictionary' => [
					'left'  => 'flex-direction: row;',
					'right' => 'flex-direction: row-reverse;',
				],
				'selectors' => [
					'{{WRAPPER}}  .ekit-content-ticker-wrapper .ekit-content-items' => '{{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_section_content_wrapper_padding',
			[
				'label'      => esc_html__('Wrapper Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'allowed_dimensions' => 'vertical',
				'default' => [
					'top' => '08',
					'right' => 'auto',
					'bottom' => '08',
					'left' => 'auto',
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee .ekit-marquee-item' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title' => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);

		/** 
		*elimentor border control overflow auto
		*content ticker wrapper border radius issue solve control
		**/
		$this->add_control(
			'ekit_content_ticker_wrapper_border_radius',
			[
				'label' => esc_html__( 'radius', 'elementskit' ),
				'type' => Controls_Manager::HIDDEN,
				'selectors'  => [
					'{{WRAPPER}} .elementor-widget-container' => 'overflow: auto;',
				],
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();
		
		// title style section
		$this->start_controls_section(
			'ekit_section_content_ticker_style_title',
			[
				'label' => esc_html__('Ticker Title', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['ekit_content_ticker_title_show' => 'yes']
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_title_width',
			[
				'label' => esc_html__('Width', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}}  .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);
		
		$this->add_control(
			'ekit_content_ticker_title_alignment',
			[
				'label' => esc_html__('Alignment', 'elementskit'),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'elementskit'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'elementskit'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'elementskit'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}}  .ekit-content-ticker-wrapper  .ekit-content-items .ekit-ticker-title' => 'justify-content: {{VALUE}}',
				],
			]
		);		

		// Icon Style Section
		$condition = [
			'relation' => 'and',
			'terms' => [
				[
					'name' => 'ekit_content_ticker_title_icon[value]',
					'operator' => '!==',
					'value' => '',
				],
				[
					'name' => 'ekit_content_ticker_title_show',
					'operator' => '===',
					'value' => 'yes',
				],
				[
					'name' => 'ekit_content_ticker_title_pointer',
					'operator' => '!==',
					'value' => 'yes',
				],
			],
		];

		$this->add_control(
			'ekit_content_ticker_icon_options',
			[
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label' => esc_html__( 'Icon Option', 'elementskit'),
				'label_off' => esc_html__('Default', 'elementskit'),
				'label_on' => esc_html__('Custom', 'elementskit'),
				'return_value' => 'yes',
				'conditions' => $condition,
			]
		);
		
		$this->start_popover();

		$this->add_responsive_control(
			'ekit_content_ticker_icon_size',
			[
				'label' => esc_html__('Icon Size', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'custom' ],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title .ticker-title-focus-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'ekit_content_ticker_icon_space_between',
			[
				'label' => esc_html__('Space Between', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],

				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}}.icon-pointer-position-left .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title .ticker-title-focus-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.icon-pointer-position-right .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title .ticker-title-focus-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_popover();

		// Pointer Style Section
		$pointer_condition = [
			'relation' => 'and',
			'terms' => [
				[
					'name' => 'ekit_content_ticker_title_show',
					'operator' => '===',
					'value' => 'yes',
				],
				[
					'name' => 'ekit_content_ticker_title_pointer',
					'operator' => '===',
					'value' => 'yes',
				],
			],
		];

		$this->add_control(
			'ekit_content_ticker_pointer_popover-toggle',
			[
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label' => esc_html__( 'Pointer Option', 'elementskit' ),
				'label_off' => esc_html__( 'Default', 'elementskit' ),
				'label_on' => esc_html__( 'Custom', 'elementskit' ),
				'return_value' => 'yes',
				'conditions' => $pointer_condition,
			]
		);
		
		$this->start_popover();

		$this->add_responsive_control(
			'ekit_content_ticker_pointer_size',
			[
				'label' => esc_html__('Pointer Size', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'custom' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title .ticker-title-focus-pointer' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_pointer_space_between',
			[
				'label' => esc_html__('Space Between', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],

				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}}.icon-pointer-position-left .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title .ticker-title-focus-pointer' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.icon-pointer-position-right .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title .ticker-title-focus-pointer' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'ekit_content_pointer_color_normal',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				"default" => '#F64177',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title .ticker-title-focus-pointer' => 'background: {{VALUE}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title .ticker-title-focus-pointer::before' => 'border-color: {{VALUE}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title .ticker-title-focus-pointer::after' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_popover();

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'       => 'ekit_content_ticker_title_typography',
				'label' => esc_html__('Typography', 'elementskit'),
				'selector'   => '{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title .ticker-title-focus'
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_title_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'allowed_dimensions' => 'horizontal',
				'default' => [
					'top' => 'auto',
					'right' => '12',
					'bottom' => 'auto',
					'left' => '12',
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title' => 'padding-right: {{RIGHT}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_title_radius',
			[
				'label' => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'ekit_content_ticker_title_color'
		);

		$this->start_controls_tab(
			'ekit_content_ticker_title_color_normal',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_content_ticker_title_color_normal_tab',
			[
				'label' => esc_html__('Text Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper  .ekit-content-items .ekit-ticker-title .ticker-title-focus' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			'ekit_content_ticker_title_background_color_normal',
			[
				'label' => esc_html__('Background', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#1B8CFC',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title::before' => 'background-color: {{VALUE}}; border-color: transparent transparent transparent {{VALUE}};',
					'{{WRAPPER}}.ticker-position-left.ekit-title-style-middle .ekit-content-items .ekit-ticker-title::before' => 'background-color: transparent',
					'{{WRAPPER}}.ticker-position-right.ekit-title-style-middle .ekit-content-items .ekit-ticker-title::before' => 'background-color: transparent',
				],
			]
		);
		
		$this->add_control(
			'ekit_content_title_icon_color_normal_tab',
			[
				'label' => esc_html__('Icon Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper  .ekit-content-items .ekit-ticker-title .ticker-title-focus-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper  .ekit-content-items .ekit-ticker-title .ticker-title-focus-icon svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_content_ticker_title_color_hover_tab',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_content_title_color_hover_tab',
			[
				'label' => esc_html__('Text Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper  .ekit-content-items .ekit-ticker-title:hover .ticker-title-focus' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_content_title_background_color_hover_tab',
			[
				'label' => esc_html__('Background', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#1B8CFC',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title:hover' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title:hover::before' => 'background-color: {{VALUE}}; border-color: transparent transparent transparent {{VALUE}};',
					'{{WRAPPER}}.ticker-position-left.ekit-title-style-middle .ekit-content-items .ekit-ticker-title:hover::before' => 'background-color: transparent',
					'{{WRAPPER}}.ticker-position-right.ekit-title-style-middle .ekit-content-items .ekit-ticker-title:hover::before' => 'background-color: transparent',
				],
			]
		);

		$this->add_control(
			'ekit_content_title_icon_color_hover_tab',
			[
				'label' => esc_html__('Icon Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper  .ekit-content-items .ekit-ticker-title:hover .ticker-title-focus-icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper  .ekit-content-items .ekit-ticker-title:hover .ticker-title-focus-icon svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_content_title_style_diagonal_size',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Diagonal Size (px)', 'elementskit'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],			
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-ticker-title::before' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-title-style-middle .ekit-content-items .ekit-ticker-title::befor' => 'border-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ticker-position-right .ekit-content-items .ekit-ticker-title::before' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.ticker-position-left .ekit-content-items .ekit-ticker-title::before' => 'left: {{SIZE}}{{UNIT}};',
					
				],
				'separator' => 'before',
				'condition' => [
					'ekit_content_ticker_title_style' => [ 'middle', 'top', 'bottom' ],
				],
			]
		);

		// Title style shape Option
		$this->add_control(
			'ekit_content_ticker_style_separator',
			[
				'label' => esc_html__('Separator ', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_content_ticker_title_separator' => 'yes',
					'ekit_content_ticker_title_style!' => 'middle'
				],
			]
		);

		$this->add_control(
			'ekit_content_ticker_separator_popover_toggle',
			[
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label' => esc_html__( 'Separator Option', 'elementskit'),
				'label_off' => esc_html__('Default', 'elementskit'),
				'label_on' => esc_html__('Custom', 'elementskit'),
				'return_value' => 'yes',
				'condition' => [
					'ekit_content_ticker_title_separator' => 'yes',
					'ekit_content_ticker_title_style!' => 'middle'
				],
			]
		);
		
		$this->start_popover();
		
		$this->add_responsive_control(
			'ekit_content_ticker_title_style_separator',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'elementskit'),
				'size_units' => [ 'px', '%', 'em', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],			
				'selectors' => [
					'{{WRAPPER}}.separatoryes.ekit-title-style-top.ticker-position-right .ekit-content-items .ekit-ticker-title::before' => 'border-left: {{SIZE}}{{UNIT}} solid;',
					'{{WRAPPER}}.separatoryes.ekit-title-style-bottom.ticker-position-right .ekit-content-items .ekit-ticker-title::before' => 'border-left: {{SIZE}}{{UNIT}} solid;',
					'{{WRAPPER}}.separatoryes.ekit-title-style-top.ticker-position-left .ekit-content-items .ekit-ticker-title::before' => 'border-right: {{SIZE}}{{UNIT}} solid;',
					'{{WRAPPER}}.separatoryes.ekit-title-style-bottom.ticker-position-left .ekit-content-items .ekit-ticker-title::before' => 'border-right: {{SIZE}}{{UNIT}} solid;',
					
				],
				'condition' => [
					'ekit_content_ticker_title_style' => ['top', 'bottom' ],
				],
			]
		);

		
		$this->add_control(
			'ekit_title_style_separator_color',
			[
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__('Color', 'elementskit'),
				'default' => "#F64177",
				'selectors' => [
					'{{WRAPPER}}.separatoryes.ekit-title-style-top.ticker-position-right .ekit-content-items .ekit-ticker-title::before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}}.separatoryes.ekit-title-style-bottom.ticker-position-right .ekit-content-items .ekit-ticker-title::before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}}.separatoryes.ekit-title-style-top.ticker-position-left .ekit-content-items .ekit-ticker-title::before' => 'border-color: {{VALUE}};',
					'{{WRAPPER}}.separatoryes.ekit-title-style-bottom.ticker-position-left .ekit-content-items .ekit-ticker-title::before' => 'border-color: {{VALUE}};',
					
				],
				'condition' => [
					'ekit_content_ticker_title_style' => ['top', 'bottom' ],
				],
			]
		);

		$this->add_responsive_control(
			'ekit_title_style_separator_width_default',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'elementskit'),
				'size_units' => [ 'px', '%', 'em', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],			
				'selectors' => [
					'{{WRAPPER}}.separatoryes.ticker-position-right .ekit-content-items .ekit-ticker-title::after' => 'width: {{SIZE}}{{UNIT}}; left: 0; transform: translate(-50%, -50%);',  
					'{{WRAPPER}}.separatoryes.ticker-position-left .ekit-content-items .ekit-ticker-title::after' => 'width: {{SIZE}}{{UNIT}}; right: 0; transform: translate(50%, -50%)',  
				],
				'condition' => [
					'ekit_content_ticker_title_style!' => [ 'middle', 'top', 'bottom' ],
				],
			]
		);
		
		$this->add_responsive_control(
			'ekit_title_style_separator_height_default',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'elementskit'),
				'size_units' => [ 'px', '%', 'em', 'custom' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
					]
				],
				'default' => [
					'unit' => '%',
					'size' => 70,
				],			
				'selectors' => [
					'{{WRAPPER}}.separatoryes.ticker-position-right .ekit-content-items .ekit-ticker-title::after' => 'height: {{SIZE}}{{UNIT}};',  
					'{{WRAPPER}}.separatoryes.ticker-position-left .ekit-content-items .ekit-ticker-title::after' => 'height: {{SIZE}}{{UNIT}};',  
				],
				'condition' => [
					'ekit_content_ticker_title_style!' => [ 'middle', 'top', 'bottom' ],
				],
			]
		);

		$this->add_control(
			'ekit_title_style_separator_color_default',
			[
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__('Color', 'elementskit'),
				'default' => "#F64177",
				'selectors' => [
					'{{WRAPPER}}.separatoryes.ticker-position-right .ekit-content-items .ekit-ticker-title::after' => 'background-color: {{VALUE}};',
					'{{WRAPPER}}.separatoryes.ticker-position-left .ekit-content-items .ekit-ticker-title::after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'ekit_content_ticker_title_style!' => [ 'middle', 'top', 'bottom' ],
				],
			]
		);

		$this->end_popover();

		$this->end_controls_section();

		// content style section
		$this->start_controls_section(
			'ekit_section_content_ticker_style_content',
			[
				'label' => esc_html__('Ticker Content', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_content_ticker_content_alignment',
			[
				'label' => esc_html__('Alignment', 'elementskit'),
				'type' => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'elementskit'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'elementskit'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'elementskit'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}}  .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee .ekit-marquee-item' => 'justify-content: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_content_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'allowed_dimensions' => 'horizontal',
				'default' => [
					'top' => 'auto',
					'right' => '45',
					'bottom' => 'auto',
					'left' => '45',
					'unit' => 'px',
				],
				'render_type' => 'template',
				'selectors'  => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee' => 'margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_content_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'allowed_dimensions' => 'horizontal',
				'default' => [
					'top' => 'auto',
					'right' => '12',
					'bottom' => 'auto',
					'left' => '12',
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee .ekit-marquee-item' => 'padding-right: {{RIGHT}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_content_ticker_content_title',
			[
				'label' => esc_html__( 'Title', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_content_ticker_content_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#202020',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee .ekit-marquee-item a' => 'color: {{VALUE}}',
				],
			]
		);        
		
		$this->add_control(
			'ekit_content_ticker_content_hover_color',
			[
				'label' => esc_html__('Hover Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#1B8CFC',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee .ekit-marquee-item a:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'       => 'ekit_content_ticker_content_typography',
				'label' => esc_html__('Typography', 'elementskit'),
				'selector'   => '{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee .ekit-marquee-item .ekit-title-and-description'
			]
		);
		
		$this->add_control(
			'ekit_content_ticker_content_description_hed',
			[
				'label' => esc_html__( 'Description', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_content_ticker_content_description' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_content_ticker_content_color-description',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#202020',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee .ekit-marquee-item .ticker-description' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_content_ticker_content_description' => 'yes'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'       => 'ekit_content_ticker_content_typography_description',
				'label' => esc_html__('Typography', 'elementskit'),
				'condition' => [
					'ekit_content_ticker_content_description' => 'yes'
				],
				'selector'   => '{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee .ekit-marquee-item .ticker-description'
			]
		);

		$this->end_controls_section();

		// Image style section
		$this->start_controls_section(
			'ekit_section_content_ticker_style_image',
			[
				'label' => esc_html__('Image', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['ekit_content_ticker_feature_img' => 'yes'],
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_content_img_width',
			[
				'label' => esc_html__('Width', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 50
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => 50
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 50
				],
				'size_units' => [ 'px', '%'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee .ekit-marquee-item img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_content_img_height',
			[
				'label' => esc_html__('Height', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 50
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => 50
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 50
				],
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee .ekit-marquee-item img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_content_ticker_image_border',
				'selector' => '{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee .ekit-marquee-item img',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_image_border_radius',
			[
				'label' => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee .ekit-marquee-item img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_content_image_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'allowed_dimensions' => 'horizontal',
				'default' => [
					'top' => 'auto',
					'right' => '10',
					'bottom' => 'auto',
					'left' => '10',
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee .ekit-marquee-item img' => 'margin-right:{{RIGHT}}{{UNIT}}; margin-left:{{LEFT}}{{UNIT}};',
				],
			]
		);

		$this-> end_controls_section();

		// Arrow style section
		$this->start_controls_section(
			'ekit_section_content_ticker_style_arrow',
			[
				'label' => esc_html__('Arrows', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['ekit_content_ticker_arrow_show' => 'yes'],
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_arrow_width',
			[
				'label' => esc_html__('Width', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'custom'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'render_type' => 'template',
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button' => 'min-width : {{SIZE}}{{UNIT}}',
					'{{WRAPPER}}.ticker-position-left .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button::before' => 'right : calc({{SIZE}}{{UNIT}} - 0px)',
					'{{WRAPPER}}.ticker-position-right .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button::before' => 'left : calc({{SIZE}}{{UNIT}} - 1px)'
				]
			]
		);

		$this->add_control(
			'ekit_content_ticker_arrow_bg',
			[
				'label' => esc_html__( 'Background', 'elementskit' ),
				'default' => '#ffebcd',
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_content_arrow_width',
			[
				'label' => esc_html__('Width', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'custom' ],
				'default' => [
					'unit' => 'px',
					'size' => 35
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => 35
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 35
				],
				'range' => [
					'min' => 1,
					'max' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-next' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-prev' => 'width: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_content_arrow_height',
			[
				'label' => esc_html__('Height', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'custom' ],
				'default' => [
					'unit' => 'px',
					'size' => 35
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => 35
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 35
				],
				'range' => [
					'min' => 1,
					'max' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-next' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-prev' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button' => '--button-height: calc({{SIZE}}{{UNIT}} + 16px);',
				],
			]
		);

		$this->add_control(
			'ekit_content_ticker_arrow_position',
			[
				'label' => esc_html__('Arrow Position', 'elementskit'),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => esc_html__('Default', 'elementskit'),
				'label_on' => esc_html__('Custom', 'elementskit'),
				'return_value' => 'yes',
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->start_popover();

		$this->add_control(
			'ekit_content_ticker_prev_arrow_heading',
			[
				'label' => esc_html__('Previous Arrow', 'elementskit'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_prev_arrow_vertical_pos',
			[
				'label' => esc_html__('Vertical Position', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'custom' ],
				'default' => [
					'unit' => '%',
					'size' => -50
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-prev' => 'transform: translateY({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_prev_arrow_horizontal_pos_one',
			[
				'label' => esc_html__('Horizontal Position', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'custom' ],
				'default' => [
					'unit' => 'px',
					'size' => 50
				], 
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'condition' => [
					'ekit_content_ticker_wrapper_position' => [ 'left' ],
				],
				'selectors' => [
					'{{WRAPPER}}.ticker-position-left .ekit-content-ticker-wrapper .ekit-marquee-button .swiper-button-prev' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_prev_arrow_horizontal_pos_two',
			[
				'label' => esc_html__('Horizontal Position', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'custom' ],
				'default' => [
					'unit' => 'px',
					'size' => 10
				], 
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'condition' => [
					'ekit_content_ticker_wrapper_position' => [ 'right' ],
				],
				'selectors' => [
					'{{WRAPPER}}.ticker-position-right .ekit-content-ticker-wrapper .ekit-marquee-button .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_content_ticker_next_arrow_heading',
			[
				'label' => esc_html__( 'Next Arrow', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_next_arrow_vertical_pos',
			[
				'label' => esc_html__('Vertical Position', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'custom' ],
				'default' => [
					'unit' => '%',
					'size' => -50
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-next' => 'transform: translateY({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_content_ticker_next_arrow_horizontal_pos_one',
			[
				'label' => esc_html__('Horizontal Position', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'custom' ],
				'default' => [
					'unit' => 'px',
					'size' => 10
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'condition' => [
					'ekit_content_ticker_wrapper_position' => [ 'left' ],
				],
				'selectors' => [
					'{{WRAPPER}}.ticker-position-left .ekit-content-ticker-wrapper .ekit-marquee-button .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'ekit_content_ticker_next_arrow_horizontal_pos_two',
			[
				'label' => esc_html__('Horizontal Position', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'custom' ],
				'default' => [
					'unit' => 'px',
					'size' => 50
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'condition' => [
					'ekit_content_ticker_wrapper_position' => [ 'right' ],
				],
				'selectors' => [
					'{{WRAPPER}}.ticker-position-right .ekit-content-ticker-wrapper .ekit-marquee-button .swiper-button-next' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_popover();

		$this->add_control(
			'ekit_content_ticker_content_arrow_icon_size',
			[
				'label' => esc_html__('Icon Size', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'custom' ],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-prev' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-next' => 'font-size: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->start_controls_tabs(
			'ekit_content_ticker_arrow_color'
		);

		$this->start_controls_tab(
			'ekit_content_ticker_arrow_color_normal_tab',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_arrow_color_normal_tab',
			[
				'label' => esc_html__('Icon Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-prev' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-prev svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-next' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-next svg' => 'fill: {{VALUE}}',
				],
			]
		);
		
		$this->add_control(
			'ekit_arrow_background_color_normal_tab',
			[
				'label' => esc_html__('Background', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#1B8CFC',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-next' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-prev' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_arrow_nav_border',
				'label' => esc_html__('Border', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-next, {{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-prev',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_content_ticker_arrow_color_hover_tab',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_arrow_color_hover_tab',
			[
				'label' => esc_html__('Icon Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-prev:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-prev:hover svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-next:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-next:hover svg' => 'fill: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_arrow_background_color_hover_tab',
			[
				'label' => esc_html__('Background', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#1B8CFC',
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-next:hover' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-prev:hover' => 'background-color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_arrow_nav_border_hover',
				'label' => esc_html__('Border', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-next:hover, {{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-prev:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_content_ticker_arrow_border_radius',
			[
				'label' => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button .ekit-marquee-button-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_content_ticker_arrow_separator_options',
			[
				'label' => esc_html__( 'Separator Options', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_content_ticker_arrow_separator_show' => [ 'yes' ],
				],
			]
		);

		$this->add_control(
			'ekit_content_ticker_arrow_popover-toggle',
			[
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label' => esc_html__( 'Separator', 'elementskit' ),
				'label_off' => esc_html__( 'Default', 'elementskit' ),
				'label_on' => esc_html__( 'Custom', 'elementskit' ),
				'return_value' => 'yes',
				'condition' => [
					'ekit_content_ticker_arrow_separator_show' => [ 'yes' ],
				],
			]
		);

		$this->start_popover();

		$this->add_responsive_control(
			'ekit_content_ticker_arrow_separator_width',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'elementskit'),
				'size_units' => [ 'px', '%', 'custom' ],
				'range' => [
					'min' => 0,
					'max' => 100,
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'condition' => [
					'ekit_content_ticker_arrow_separator_show' => [ 'yes' ],
				],		
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button::before' => 'width: {{SIZE}}{{UNIT}};',  
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button::before' => 'width: {{SIZE}}{{UNIT}};',  
				],
			]
		);
		
		$this->add_responsive_control(
			'ekit_content_ticker_arrow_separator_height',
			[
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'elementskit'),
				'size_units' => [ 'px', '%', 'custom' ],
				'range' => [
					'min' => 0,
					'max' => 100,
				],
				'default' => [
					'unit' => '%',
					'size' => 70,
				],			
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button::before' => 'height: {{SIZE}}{{UNIT}};',  
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button::before' => 'height: {{SIZE}}{{UNIT}};',  
				],
			]
		);
		
		$this->add_control(
			'ekit_content_ticker_arrow_separator_color',
			[
				'type' => Controls_Manager::COLOR,
				'label' => esc_html__('Color', 'elementskit'),
				'default' => "#F64177",
				'selectors' => [
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button::before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .ekit-content-ticker-wrapper .ekit-content-items .ekit-marquee-button::before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_popover();
		$this->end_controls_section();
	}

	protected function render() {
		echo '<div class="ekit-wid-con" >';
		$this->render_raw();
		echo '</div>';
	}

	protected function render_raw() {
		$settings = $this->get_settings_for_display();
		extract($settings);

		$content_options = [
			'type' => 'content_ticker',
			'settingOptions' => [
				'tickerSpeed' =>  $ekit_content_ticker_speed['size'] ?? .5,
				'tickerDirection' =>  $ekit_content_ticker_direction,
				'tickerEffect' =>  $ekit_content_ticker_effect_style,
				'tickerAutoPlay' =>  $ekit_content_ticker_auto_play,
				'tickerDelay' =>  $ekit_content_ticker_delay['size'] ?? 3,
				'tickerReverseDirection' =>  $ekit_content_ticker_reverse_direction,
				'tickerGapBetween' =>  $ekit_content_ticker_gap_between_loop,
				'tickerGrabCursor' =>  $ekit_content_ticker_grab_cursor,
			],
		];
		
		$this->add_render_attribute( 'content-wrapper', [
			'id' => 'content-ticker-' . $this->get_id(),
			'class' => [
				'ekit-content-ticker-wrapper',
			],
			'data-content-settings' => wp_json_encode($content_options),
		] );

		// marquee effect class
		$ticker = $ekit_content_ticker_effect_style === 'marquee' ? 'ticker':  (method_exists('\ElementsKit_Lite\Utils', 'swiper_class') ? esc_attr(\ElementsKit_Lite\Utils::swiper_class()) : 'swiper');
		$marquee_wrapper = $ekit_content_ticker_effect_style === 'marquee' ? 'marquee-wrapper': 'swiper-wrapper';
		$ticker_item = $ekit_content_ticker_effect_style === 'marquee' ? 'ticker-item': 'swiper-slide';
		?>

		<div <?php $this->print_render_attribute_string('content-wrapper'); ?>>
			<div class="ekit-content-items">
				<?php if($ekit_content_ticker_title_show === 'yes') : ?>
					<div class="ekit-ticker-title">
						<span class="ticker-title-focus"><?php echo wp_kses($ekit_content_ticker_title, \ElementsKit_Lite\Utils::get_kses_array()); ?></span>
						<?php if ($ekit_content_ticker_title_pointer === 'yes') :?>
							<span class="ticker-title-focus-pointer"></span>
						<?php else :?>
							<span class="ticker-title-focus-icon">
								<?php Icons_Manager::render_icon($ekit_content_ticker_title_icon, [ 'aria-hidden' => 'true' ]); ?>
							</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
					<div class="<?php echo esc_attr($ticker); ?> ekitMarqueeSwiper ekit-marquee">
						<div class="<?php echo esc_attr($marquee_wrapper); ?>">
							<?php if ($ekit_content_ticker_select === 'post') :
									$content = $this->get_post_content($settings, $ticker_item); 
								elseif ($ekit_content_ticker_select === 'custom') : 
									$content = $this->get_custom_content($settings, $ticker_item);
								endif;
								echo wp_kses($content, \ElementsKit_Lite\Utils::get_kses_array()); ?>
						</div>
					</div>
				<?php if($ekit_content_ticker_arrow_show === 'yes') : ?>
					<!-- next / prev arrows -->
					<div class="ekit-marquee-button">
						<div class="ekit-marquee-button-prev swiper-button-prev">
							<?php Icons_Manager::render_icon($ekit_content_ticker_left_arrow_icon, [ 'aria-hidden' => 'true' ]); ?>
						</div>
						<div class="ekit-marquee-button-next swiper-button-next"> 
							<?php Icons_Manager::render_icon($ekit_content_ticker_right_arrow_icon, [ 'aria-hidden' => 'true' ]); ?>
						</div>
					</div>
					<!-- !next / prev arrows -->
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	protected function get_post_content($settings, $ticker_item = '') {
		extract($settings);
		$default    = [
			'orderby'           =>  $ekit_content_ticker_sort,
			'posts_per_page'    => $ekit_content_ticker_num,
			'post_status'       => 'publish'
		];

		if($ekit_content_ticker_is_manual_selection === 'yes') {
			$default = \ElementsKit_Lite\Utils::array_push_assoc(
				$default, 'post__in', (!empty($ekit_content_manual_selection  && count($ekit_content_manual_selection) > 0 )) ? $ekit_content_manual_selection : [-1]
			);
		}

		if($ekit_content_ticker_is_manual_selection == '' && $ekit_content_ticker_cats != '') {
			$default = \ElementsKit_Lite\Utils::array_push_assoc(
				$default, 'category__in', $ekit_content_ticker_cats
			);
		} 

		$post_query = new \WP_Query( $default );

		if ($post_query->have_posts()) : 
			while ($post_query->have_posts()) : $post_query->the_post(); ?>
					<div class="ekit-marquee-item <?php echo esc_attr($ticker_item); ?>">
						<?php if($ekit_content_ticker_feature_img === "yes" && has_post_thumbnail()) :
							$image_data = [
								'image'	=> [
									'id' => get_post_thumbnail_id(),
									'url' => get_the_post_thumbnail_url( $post_query->ID, 'full' ),
								],
								'image_size' => $ekit_content_ticker_image_size,
								'image_custom_dimension' => $ekit_content_ticker_image_custom_dimension,
							];
							Group_Control_Image_Size::print_attachment_image_html($image_data);
						endif; ?>
						<div class="ekit-title-and-description">
							<a href="<?php the_permalink(); ?>" target="_blank" rel="noopener noreferrer">
								<?php if($ekit_content_ticker_trim !='' || $ekit_content_ticker_trim > 0) : 
									echo esc_html(wp_trim_words(get_the_title(), $ekit_content_ticker_trim, false));
								else:
									the_title();
								endif; ?>
							</a>
							<?php if($ekit_content_ticker_content_description === 'yes') :?>
								<p class="ticker-description"><?php echo esc_html(wp_trim_words(get_the_content() , $ekit_content_ticker_trim_description, false)); ?></p>
							<?php endif; ?>
						</div>
					</div>
			<?php endwhile;
			// Restore original Post Data
			wp_reset_postdata(); 
		else : ?>
			<div class="ekit-marquee-item <?php echo esc_attr($ticker_item); ?>"> <a href="#"> <?php echo esc_html__('No content found!', 'elementskit'); ?> </a> </div>
		<?php endif; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- It will escape finally inside $this->render_raw() method
	}

	protected function get_custom_content($settings, $ticker_item = '') {
		extract($settings);
		
		foreach ($ekit_content_ticker_text_list as $index => $item) : 
			if (!empty($item['ekit_content_ticker_text_url']['url'])) :
				$this->add_link_attributes('item_link'.$index, $item['ekit_content_ticker_text_url']);
			endif; ?>
			<div class="elementor-repeater-item-<?php echo esc_attr($item['_id']); ?> ekit-marquee-item <?php echo esc_attr($ticker_item); ?>">
				<?php 	if($ekit_content_ticker_feature_img === "yes") :
					$item['ekit_content_ticker_image_size'] = $settings['ekit_content_ticker_image_size'];
					$item['ekit_content_ticker_image_custom_dimension'] = $settings['ekit_content_ticker_image_custom_dimension'];
					Group_Control_Image_Size::print_attachment_image_html($item,  'ekit_content_ticker_image', 'ekit_content_ticker_image_choose');
				endif; ?> 
				<div class="ekit-title-and-description">
					<a <?php $this->print_render_attribute_string('item_link'.$index); ?>><?php echo esc_html($item['ekit_content_ticker_text']); ?></a>
					<?php if($ekit_content_ticker_content_description === 'yes') : ?>
						<p class="ticker-description"><?php echo esc_html($item['ekit_content_ticker_description']); ?></p>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- It will escape finally inside $this->render_raw() method
	}
}