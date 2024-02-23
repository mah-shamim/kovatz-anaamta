<?php

namespace Elementor;

use \Elementor\ElementsKit_Widget_Zoom_Handler as Handler;
use ElementsKit_Lite\Widgets\Widget_Notice;

defined('ABSPATH') || exit;


class ElementsKit_Widget_Zoom extends Widget_Base {

    use Widget_Notice;

	public $base;


	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		wp_register_script('zoom-init', Handler::get_url() . 'assets/js/zoom.init.js', ['elementor-frontend'], \ElementsKit_Lite::VERSION(), true);

		$data['rest_url'] = get_rest_url();
		$data['nonce']    = wp_create_nonce('wp_rest');

		wp_localize_script('zoom-init', 'zoom_js', $data);
	}


	public function get_script_depends() {
		return ['zoom-init'];
	}


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
        return 'https://wpmet.com/doc/zoom-integration/';
    }

	protected function register_controls() {

		$default_hosts = count( Handler::get_cached_hosts() ) > 0 ? array_keys(Handler::get_cached_hosts())[0] : '';
		$default_timezone = count( Handler::get_timezone() ) > 0 ? array_keys(Handler::get_timezone())[0] : '';

		$this->start_controls_section(
			'ekit_btn_section_meeting',
			array(
				'label' => esc_html__('Create Meeting', 'elementskit'),
			)
		);

		$this->add_control(
			'meeting_cache',
			[
				'label' => __('Meeting Data', 'elementskit'),
				'type'  => \Elementor\Controls_Manager::HIDDEN,
			]
		);

		$this->add_control(
			'user_id',
			[
				'label'       => esc_html__('Meeting Hosts*', 'elementskit'),
				'type'        => Controls_Manager::SELECT,
				'options'     => Handler::get_cached_hosts(),
				'label_block' => true,
				'description' => esc_html__('Select a host of the meeting.(Required)', 'elementskit'),
				'default'	=> $default_hosts
			]
		);

		$this->add_control(
			'start_time',
			[
				'label'       => __('Start date/time*', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::DATE_TIME,
				'description' => esc_html__('Select start date and time.(Required)', 'elementskit'),
				'default'	=> date('y-m-d H:i')
			]
		);

		$this->add_control(
			'timezone',
			[
				'label'       => esc_html__('Time zone*', 'elementskit'),
				'type'        => Controls_Manager::SELECT,
				'options'     => Handler::get_timezone(),
				'description' => esc_html__('Select timezone for meeting .(Required)', 'elementskit'),
				'default'	=> $default_timezone
			]
		);

		$this->add_control(
			'duration',
			[
				'label' => __('Duration', 'elementskit'),
				'type'  => Controls_Manager::NUMBER,
				'min'   => 0,
			]
		);

		$this->add_control(
			'password',
			[
				'label'       => __('Password', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => __('Password to join the meeting. Password may only contain the following characters: [a-z A-Z 0-9]. Max of 10 characters.( Leave blank for auto generate )', 'elementskit'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'create-meeting',
			[
				'label'       => __('', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::BUTTON,
				'button_type' => 'success',
				'text'        => __('Create Meeting <span class="elementor-state-icon">
				<i class="eicon-loading eicon-animation-spin" aria-hidden="true"></i>
			</span>', 'elementskit'),
				'event'       => 'ekit:editor:create',
			]
		);


		$this->end_controls_section();


		$this->start_controls_section(
			'ekit_btn_section_settings',
			array(
				'label' => esc_html__('Settings', 'elementskit'),
			)
		);
		$this->add_control(
			'ekit_zoom_layout',
			[
				'label'       => esc_html__('Layout', 'elementskit'),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'normal' => esc_html__('Normal', 'elementskit'),
					'flat'   => esc_html__('Flat', 'elementskit'),
				],
				'label_block' => false,
				'description' => esc_html__('Select layout of the design', 'elementskit'),
				'default'     => 'normal',
			]
		);
		$this->add_control(
			'ekit_meeting_heading_text',
			[
				'label'       => __('Meeting Heading Text', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Details',
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'ekit_zoom_enable_protected_meeting',
			[
				'label'       => esc_html__('Enable Protected?', 'elementskit'),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__('Show', 'elementskit'),
				'label_off'   => esc_html__('Hide', 'elementskit'),
				'return_type' => 'yes',
			]
		);
		$this->add_control(
			'ekit_protected_heading',
			[
				'label'       => __('Heading', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'seperator'	=> 'before',
				'default'	=> 'Password Protected Meeting',
				'condition'	=> [
					'ekit_zoom_enable_protected_meeting'	=> 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'ekit_protected_subheading',
			[
				'label'       => __('Sub Heading', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'To view this protected meeting, please enter password below:',
				'condition'	=> [
					'ekit_zoom_enable_protected_meeting'	=> 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'ekit_protected_placeholder_text',
			[
				'label'       => __('Placeholder Text', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Type your password here',
				'condition'	=> [
					'ekit_zoom_enable_protected_meeting'	=> 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'ekit_protected_submit_text',
			[
				'label'       => __('Submit BTN Text', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Submit',
				'separator'	=> 'after',
				'condition'	=> [
					'ekit_zoom_enable_protected_meeting'	=> 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'ekit_zoom_enable_count_down_timer',
			[
				'label'       => esc_html__('Enable Count Down Timer?', 'elementskit'),
				'type'        => Controls_Manager::SWITCHER,
				'label_on'    => esc_html__('Show', 'elementskit'),
				'label_off'   => esc_html__('Hide', 'elementskit'),
				'return_type' => 'yes',
			]
		);
		$this->add_control(
			'ekit_zoom_timer_heading',
			[
				'label'       => __('Timer Heading', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Meeting starts in',
				'condition'	=> [
					'ekit_zoom_enable_count_down_timer'	=> 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'ekit_zoom_timer_days_text',
			[
				'label'       => __('Days Text', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Days',
				'condition'	=> [
					'ekit_zoom_enable_count_down_timer'	=> 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'ekit_zoom_timer_hours_text',
			[
				'label'       => __('Hours Text', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Hours',
				'condition'	=> [
					'ekit_zoom_enable_count_down_timer'	=> 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'ekit_zoom_timer_minutes_text',
			[
				'label'       => __('Minutes Text', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Minutes',
				'condition'	=> [
					'ekit_zoom_enable_count_down_timer'	=> 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'ekit_zoom_timer_seconds_text',
			[
				'label'       => __('Seconds Text', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Seconds',
				'condition'	=> [
					'ekit_zoom_enable_count_down_timer'	=> 'yes'
				],	
				'separator'	=> 'after',
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
            'ekit_zoom_enable_meeting_details',
            [
                'label' 		=> esc_html__('Enable Meeting Details?', 'elementskit'),
                'type' 			=> Controls_Manager::SWITCHER,
                'label_on' 		=> esc_html__('Show', 'elementskit'),
                'label_off' 	=> esc_html__('Hide', 'elementskit'),
				'return_type' 	=> 'yes',
				'default'		=> 'yes'
            ]
		);
		$this->add_control(
            'ekit_show_meeting_id',
            [
                'label' 		=> esc_html__('Show Meeting ID?', 'elementskit'),
                'type' 			=> Controls_Manager::SWITCHER,
                'label_on' 		=> esc_html__('Show', 'elementskit'),
                'label_off' 	=> esc_html__('Hide', 'elementskit'),
				'return_type' 	=> 'yes',
				'default'		=> 'yes',
				'condition'		=> [
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				]
            ]
		);
		$this->add_control(
			'ekit_meeting_id_label',
			[
				'label'       => __('Title', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Meeting ID',
				'condition'	=> [
					'ekit_show_meeting_id'	=> 'yes',
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				],	
				'separator'	=> 'after',
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
            'ekit_show_meeting_topic',
            [
                'label' 		=> esc_html__('Show Meeting Topic?', 'elementskit'),
                'type' 			=> Controls_Manager::SWITCHER,
                'label_on' 		=> esc_html__('Show', 'elementskit'),
                'label_off' 	=> esc_html__('Hide', 'elementskit'),
				'return_type' 	=> 'yes',
				'default'		=> 'yes',
				'condition'		=> [
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				]
            ]
		);
		$this->add_control(
			'ekit_meeting_topic_label',
			[
				'label'       => __('Title', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Topic',
				'condition'	=> [
					'ekit_show_meeting_topic'	=> 'yes',
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				],	
				'separator'	=> 'after',
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
            'ekit_show_meeting_status',
            [
                'label' 		=> esc_html__('Show Meeting Status?', 'elementskit'),
                'type' 			=> Controls_Manager::SWITCHER,
                'label_on' 		=> esc_html__('Show', 'elementskit'),
                'label_off' 	=> esc_html__('Hide', 'elementskit'),
				'return_type' 	=> 'yes',
				'default'		=> 'yes',
				'condition'		=> [
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				]
            ]
		);
		$this->add_control(
			'ekit_meeting_status_label',
			[
				'label'       => __('Title', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Meeting Status',
				'condition'	=> [
					'ekit_show_meeting_status'	=> 'yes',
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				],	
				'separator'	=> 'after',
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
            'ekit_show_meeting_start_time',
            [
                'label' 		=> esc_html__('Show Meeting Start Time?', 'elementskit'),
                'type' 			=> Controls_Manager::SWITCHER,
                'label_on' 		=> esc_html__('Show', 'elementskit'),
                'label_off' 	=> esc_html__('Hide', 'elementskit'),
				'return_type' 	=> 'yes',
				'default'		=> 'yes',
				'condition'		=> [
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				]
            ]
		);
		$this->add_control(
			'ekit_meeting_start_time_label',
			[
				'label'       => __('Title', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Start Time',
				'condition'	=> [
					'ekit_show_meeting_start_time'	=> 'yes',
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				],	
				'separator'	=> 'after',
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
            'ekit_show_meeting_duration',
            [
                'label' 		=> esc_html__('Show Meeting Duration?', 'elementskit'),
                'type' 			=> Controls_Manager::SWITCHER,
                'label_on' 		=> esc_html__('Show', 'elementskit'),
                'label_off' 	=> esc_html__('Hide', 'elementskit'),
				'return_type' 	=> 'yes',
				'default'		=> 'yes',
				'condition'		=> [
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				]
            ]
		);
		$this->add_control(
			'ekit_meeting_duration_label',
			[
				'label'       => __('Title', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Duration',
				'condition'	=> [
					'ekit_show_meeting_duration'	=> 'yes',
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				],	
				'separator'	=> 'after',
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
            'ekit_show_meeting_timezone',
            [
                'label' 		=> esc_html__('Show Meeting Time Zone?', 'elementskit'),
                'type' 			=> Controls_Manager::SWITCHER,
                'label_on' 		=> esc_html__('Show', 'elementskit'),
                'label_off' 	=> esc_html__('Hide', 'elementskit'),
				'return_type' 	=> 'yes',
				'default'		=> 'yes',
				'condition'		=> [
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				]
            ]
		);
		$this->add_control(
			'ekit_meeting_timezone_label',
			[
				'label'       => __('Title', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Time Zone',
				'condition'	=> [
					'ekit_show_meeting_timezone'	=> 'yes',
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				],	
				'separator'	=> 'after',
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
            'ekit_show_meeting_start_url',
            [
                'label' 		=> esc_html__('Show Meeting Start Url?', 'elementskit'),
                'type' 			=> Controls_Manager::SWITCHER,
                'label_on' 		=> esc_html__('Show', 'elementskit'),
                'label_off' 	=> esc_html__('Hide', 'elementskit'),
				'return_type' 	=> 'yes',
				'default'		=> 'yes',
				'condition'		=> [
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				]
            ]
		);
		$this->add_control(
			'ekit_meeting_start_url_label',
			[
				'label'       => __('Title', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Start Url',
				'condition'	=> [
					'ekit_show_meeting_start_url'	=> 'yes',
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				],	
				'separator'	=> 'after',
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'ekit_meeting_start_url_btn_text',
			[
				'label'       => __('Button Text', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Join Meeting Via Zoom App',
				'condition'	=> [
					'ekit_show_meeting_start_url'	=> 'yes',
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
            'ekit_show_meeting_join_url',
            [
                'label' 		=> esc_html__('Show Meeting Join Url?', 'elementskit'),
                'type' 			=> Controls_Manager::SWITCHER,
                'label_on' 		=> esc_html__('Show', 'elementskit'),
                'label_off' 	=> esc_html__('Hide', 'elementskit'),
				'return_type' 	=> 'yes',
				'default'		=> 'yes',
				'condition'		=> [
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				]
            ]
		);
		$this->add_control(
			'ekit_meeting_join_url_label',
			[
				'label'       => __('Title', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Join Url',
				'condition'	=> [
					'ekit_show_meeting_join_url'	=> 'yes',
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->add_control(
			'ekit_meeting_join_url_btn_text',
			[
				'label'       => __('Button Text', 'elementskit'),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'	=> 'Join Meeting Via Web Browser',
				'condition'	=> [
					'ekit_show_meeting_join_url'	=> 'yes',
					'ekit_zoom_enable_meeting_details'	=> 'yes'
				],	
				'separator'	=> 'after',
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_zoom_meeting_timer',
			[
				'label' => esc_html__('Count Down Timer', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'ekit_zoom_enable_count_down_timer' => 'yes'
				]
			]
		);

		$this->add_control(
            'ekit_zoom_meeting_timer_heading',
            [
                'label' => esc_html__( 'Heading:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
		);
		
		$this->add_responsive_control(
			'ekit_timer_heading_alignment',
			[
				'label'    => esc_html__( 'Alignment', 'elementskit' ),
				'type'     => Controls_Manager::CHOOSE,
				'options'  => [
					'left'   => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'  => 'center',
				'selectors'=> [
					'{{WRAPPER}} .ekit-zoom-counter-wrapper .ekit-zoom-counter-heading' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_timer_heading_typo',
                'selector'	 => '{{WRAPPER}} .ekit-zoom-counter-wrapper .ekit-zoom-counter-heading',
            ]
		);

		$this->add_control(
			'ekit_timer_heading_color',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-counter-wrapper .ekit-zoom-counter-heading'	=> 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_timer_heading_background',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-counter-wrapper .ekit-zoom-counter-heading',
            ]
        );
		
		$this->add_responsive_control(
			'ekit_timer_heading_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-counter-wrapper .ekit-zoom-counter-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_timer_heading_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-counter-wrapper .ekit-zoom-counter-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_timer_heading_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-counter-wrapper .ekit-zoom-counter-heading',
            ]
        );

        $this->add_control(
            'ekit_timer_heading_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-zoom-counter-wrapper .ekit-zoom-counter-heading' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_timer_heading_shadow',
                'label' => esc_html__( 'Box Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-counter-wrapper .ekit-zoom-counter-heading',
            ]
		);
		
		$this->add_control(
            'ekit_timer',
            [
                'label' => esc_html__( 'Timer:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
		);
		$this->add_responsive_control(
            'ekit_timer_width',
            [
                'label' => esc_html__( 'Width', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px','%' ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
					],
					'px' => [
                        'min' => 0,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-zoom-counter ul li' => 'flex: 0 0 {{SIZE}}{{UNIT}};',
                ]
            ]
		);
		
		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_timer_background',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-counter ul li',
            ]
        );
		
		$this->add_responsive_control(
			'ekit_timer_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-counter ul li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_timer_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-counter ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_timer_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-counter ul li',
            ]
        );

        $this->add_control(
            'ekit_timer_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-zoom-counter ul li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_timer_shadow',
                'label' => esc_html__( 'Box Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-counter ul li',
            ]
		);

		$this->start_controls_tabs('ekit_timer_tabs1');
		$this->start_controls_tab(
			'ekit_timer_number',
			[
				'label' => esc_html__('Number', 'elementskit'),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_timer_number_typo',
				'label'    => esc_html__('Typography', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-zoom-counter .number',
			]
		);
		$this->add_control(
			'ekit_timer_number_color',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-counter .number'	=> 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'ekit_timer_text',
			[
				'label' => esc_html__('Text', 'elementskit'),
			]
		);
		$this->add_control(
            'ekit_timer_text_position',
            [
                'label' => esc_html__( 'Text Position', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'column',
                'options' => [
                    'column-reverse'  => esc_html__( 'Top', 'elementskit' ),
                    'row' => esc_html__( 'Right', 'elementskit' ),
                    'column' => esc_html__( 'Bottom', 'elementskit' ),
                    'row-reverse' => esc_html__( 'Left', 'elementskit' ),
				],
				'selectors'	=> [
					'{{WRAPPER}} .ekit-zoom-counter ul li' => 'flex-direction: {{VALUE}}'
				]
            ]
		);
		$this->add_responsive_control(
			'ekit_timer_text_spacing',
			[
				'label' => esc_html__( 'Spacing', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-counter ul li .text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_timer_text_typo',
				'label'    => esc_html__('Typography', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-zoom-counter .text',
			]
		);
		$this->add_control(
			'ekit_timer_text_color',
			[
				'label'     => esc_html__('Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-counter .text'	=> 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_zoom_meeting_heading_style',
			[
				'label' => esc_html__('Meeting Heading', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'ekit_meeting_de_heading_alignment',
			[
				'label'    => esc_html__( 'Alignment', 'elementskit' ),
				'type'     => Controls_Manager::CHOOSE,
				'options'  => [
					'left'   => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'  => 'left',
				'selectors'=> [
					'{{WRAPPER}} .ekit-zoom-heading' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_zoom_meeting_heading_typo',
                'selector'	 => '{{WRAPPER}} .ekit-zoom-heading h2',
            ]
		);

		$this->add_control(
			'ekit_zoom_meeting_heading_color',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-heading h2'	=> 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_zoom_meeting_heading_bg',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-heading h2',
            ]
        );
		
		$this->add_responsive_control(
			'ekit_zoom_meeting_heading_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-heading h2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_zoom_meeting_heading_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-heading h2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_zoom_meeting_heading_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-heading h2',
            ]
        );

        $this->add_control(
            'ekit_zoom_meeting_heading_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-zoom-heading h2' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_zoom_meeting_heading_shadow',
                'label' => esc_html__( 'Box Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-heading h2',
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_zoom_meeting_details_style',
			[
				'label' => esc_html__('Meeting Details', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ekit_zoom_meeting_details_bgc',
				'label'		=> esc_html__('Background Color', 'elementskit'),
				'default'  => '#fff',
				'selector' => '{{WRAPPER}} .ekit-zoom-details',
				'fields_options' => [
					'background' => [
						'label'		=> esc_html__('Container Background Color', 'elementskit'),
					]
				],
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ekit_zoom_meeting_details_stripe_bgc',
				'default'  => '#E2E2E2',
				'selector' => '{{WRAPPER}} .ekit-zoom-details > .single-zoom-info:nth-child(odd)',
				'fields_options' => [
					'background' => [
						'label'		=> esc_html__('Stripe Background Color', 'elementskit'),
					]
				],
				'condition'	=> [
					'ekit_zoom_layout' => 'normal'
				]
			)
		);

		$this->add_responsive_control(
			'ekit_zoom_meeting_details_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-details' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_zoom_meeting_details_margin',
			[
				'label'      => esc_html__('margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-details' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_zoom_meeting_details_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-details',
            ]
		);
		
		$this->add_control(
            'ekit_zoom_meeting_details_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-zoom-details' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_zoom_meeting_details_shadow',
                'label' => esc_html__( 'Box Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-details',
            ]
        );

		$this->start_controls_tabs('ekit_zoom_meeting_details_tabs1');
		$this->start_controls_tab(
			'ekit_zoom_meeting_details_title',
			[
				'label' => esc_html__('Title', 'elementskit'),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_meeting_title_typo',
				'label'    => esc_html__('Typography', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-zoom-details > .single-zoom-info .zoom-title',
			]
		);
		$this->add_control(
			'ekit_meeting_title_color',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-details > .single-zoom-info .zoom-title'	=> 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ekit_meeting_title_hover_color',
			[
				'label'     => esc_html__('Hover Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-details > .single-zoom-info .zoom-title:hover'	=> 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ekit_meeting_title_background',
				'selector' => '{{WRAPPER}} .ekit-zoom-details > .single-zoom-info .zoom-title',
			)
		);
		$this->add_responsive_control(
			'ekit_meeting_title_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-details > .single-zoom-info .zoom-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_meeting_title_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-details > .single-zoom-info .zoom-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_zoom_meeting_details_subtitle',
			[
				'label' => esc_html__('Value', 'elementskit'),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_meeting_subtitle_typo',
				'label'    => esc_html__('Typography', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-zoom-details > .single-zoom-info .zoom-subtitle',
			]
		);
		$this->add_control(
			'ekit_meeting_subtitle_color',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-details > .single-zoom-info .zoom-subtitle'	=> 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'ekit_meeting_subtitle_hover_color',
			[
				'label'     => esc_html__('Hover Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-details > .single-zoom-info .zoom-subtitle:hover'	=> 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ekit_meeting_subtitle_background',
				'selector' => '{{WRAPPER}} .ekit-zoom-details > .single-zoom-info .zoom-subtitle',
			)
		);
		$this->add_responsive_control(
			'ekit_meeting_subtitle_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-details > .single-zoom-info .zoom-subtitle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_meeting_subtitle_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-details > .single-zoom-info .zoom-subtitle' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();


		$this->start_controls_section(
			'ekit_meeting_joins_links',
			[
				'label' => esc_html__('Join Links', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ekit_meeting_joins_links_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_meeting_joins_links_typography',
				'label'    => esc_html__('Typography', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-zoom-btn',
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'ekit_meeting_joins_links_shadow',
				'selector' => '{{WRAPPER}} .ekit-zoom-btn',
			]
		);

		$this->start_controls_tabs('ekit_meeting_joins_links_tabs_style');

		$this->start_controls_tab(
			'ekit_meeting_joins_links_tabnormal',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_meeting_joins_links_text_color',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-btn'          => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ekit_meeting_joins_links_bg_color',
				'default'  => '',
				'selector' => '{{WRAPPER}} .ekit-zoom-btn',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_meeting_joins_links_tab_button_hover',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_meeting_joins_links_hover_color',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-btn:hover'          => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'ekit_meeting_joins_links_bg_hover_color',
				'default'  => '',
				'selector' => '{{WRAPPER}} .ekit-zoom-btn:hover',
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();


		$this->start_controls_section(
			'ekit_protected',
			[
				'label' => esc_html__('Protected Area', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'ekit_zoom_enable_protected_meeting'	=> 'yes'
				]
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_protected_background',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-protected',
            ]
        );
		
		$this->add_responsive_control(
			'ekit_protected_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-protected' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_protected_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-protected' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_protected_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-protected',
            ]
        );

        $this->add_control(
            'ekit_protected_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-zoom-protected' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_protected_shadow',
                'label' => esc_html__( 'Box Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-protected',
            ]
		);

		$this->start_controls_tabs('ekit_protected_tabs1');
		$this->start_controls_tab(
			'ekit_protected_text',
			[
				'label' => esc_html__('Text', 'elementskit'),
			]
		);
		$this->add_control(
            'ekit_protected_title',
            [
                'label' => esc_html__( 'Title:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
		);
		$this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_protected_title_typo',
                'selector'	 => '{{WRAPPER}} .ekit-zoom-protected-form h4',
            ]
		);

		$this->add_control(
			'ekit_protected_title_color',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-protected-form h4'	=> 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_protected_title_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-protected-form h4' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
            'ekit_protected_subtitle',
            [
                'label' => esc_html__( 'Sub Title:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
		);
		$this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_protected_subtitle_typo',
                'selector'	 => '{{WRAPPER}} .ekit-zoom-protected-form p',
            ]
		);

		$this->add_control(
			'ekit_protected_subtitle_color',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-protected-form p'	=> 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_protected_subtitle_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-protected-form p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'ekit_protected_inputs',
			[
				'label' => esc_html__('Inputs', 'elementskit'),
			]
		);
		$this->add_control(
            'ekit_protected_password',
            [
                'label' => esc_html__( 'Password Input:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
		);
		$this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_protected_password_type',
                'selector'	 => '{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-password-field',
            ]
		);

		$this->add_control(
			'ekit_protected_password_color',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-password-field'	=> 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_protected_password_bg',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-password-field',
            ]
        );
		
		$this->add_responsive_control(
			'ekit_protected_password_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-password-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_protected_password_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-password-field' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_protected_password_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-password-field',
            ]
        );

        $this->add_control(
            'ekit_protected_password_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-password-field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
		);
		
		$this->add_control(
            'ekit_protected_submit',
            [
                'label' => esc_html__( 'Submit Button:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
		);
		$this->add_responsive_control(
			'ekit_protected_submit_align',
			[
				'label'    => esc_html__( 'Alignment', 'elementskit' ),
				'type'     => Controls_Manager::CHOOSE,
				'options'  => [
					'baseline'   => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon'  => 'eicon-text-align-center',
					],
					'flex-end'  => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'  => 'flex-end',
				'selectors'=> [
					'{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-submit-field' => 'align-self: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_protected_submit_type',
                'selector'	 => '{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-submit-field',
            ]
		);

		$this->add_control(
			'ekit_protected_submit_color',
			[
				'label'     => esc_html__('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-submit-field'	=> 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'ekit_protected_submit_bg',
                'label' => esc_html__( 'Background', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-submit-field',
            ]
        );
		
		$this->add_responsive_control(
			'ekit_protected_submit_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-submit-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_protected_submit_margin',
			[
				'label'      => esc_html__('Margin', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-submit-field' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'ekit_protected_submit_border',
                'label' => esc_html__( 'Border', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-submit-field',
            ]
        );

        $this->add_control(
            'ekit_protected_submit_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-zoom-protected-inner .ekit-zoom-submit-field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_section();

		$this->insert_pro_message();
	}


	private function zoom_join_links($zoom_data) {
		$settings = $this->get_settings_for_display();
		extract($settings);
		?>
		<?php if(isset($ekit_show_meeting_start_url) && $ekit_show_meeting_start_url == 'yes') : ?>
			<div class="single-zoom-info">
				<span class="zoom-title"><?php echo esc_html($ekit_meeting_start_url_label); ?></span>
				<span class="zoom-subtitle"><a class="ekit-zoom-btn" target="_blank" href="<?php echo esc_html($zoom_data->start_url); ?>"><?php echo esc_html($ekit_meeting_start_url_btn_text); ?></a></span>
			</div>
		<?php endif; ?>

		<?php if(isset($ekit_show_meeting_join_url) && $ekit_show_meeting_join_url == 'yes') : ?>
			<div class="single-zoom-info">
				<span class="zoom-title"><?php echo esc_html($ekit_meeting_join_url_label); ?></span>
				<span class="zoom-subtitle"><a class="ekit-zoom-btn" target="_blank" href="<?php echo esc_html($zoom_data->join_url); ?>"><?php echo esc_html($ekit_meeting_join_url_btn_text); ?></a></span>
			</div>
		<?php endif; ?>
		<?php
	}


	protected function render() {
		echo '<div class="ekit-wid-con" >';
		$this->render_raw();
		echo '</div>';
	}


	protected function render_raw() {

		$settings = $this->get_settings_for_display();
		extract($settings);
		$meeting = $settings['meeting_cache'];
		$meeting_timer_heading = isset($settings['ekit_zoom_timer_heading']) ? $settings['ekit_zoom_timer_heading'] : '';
		$is_protected = isset($settings['ekit_zoom_enable_protected_meeting']) ? $settings['ekit_zoom_enable_protected_meeting'] : '';
		$layout = isset($settings['ekit_zoom_layout']) ? $settings['ekit_zoom_layout'] : '';
		$isMettingDetails = isset($settings['ekit_zoom_enable_meeting_details']) ? $settings['ekit_zoom_enable_meeting_details'] : '';
		$meeting_heading_text = isset($settings['ekit_meeting_heading_text']) ? $settings['ekit_meeting_heading_text'] : '';
		$custom_settings = [
			'days' => esc_attr($settings['ekit_zoom_timer_days_text']),
			'hours' => esc_attr($settings['ekit_zoom_timer_hours_text']),
			'minutes' => esc_attr($settings['ekit_zoom_timer_minutes_text']),
			'seconds' => esc_attr($settings['ekit_zoom_timer_seconds_text'])
		];

		$zoom_data = empty($meeting) ? [] : json_decode($meeting);

		$widget_id = $this->get_id();

		if(isset($zoom_data) && is_object($zoom_data)) : ?>

            <div class="ekit-zoom-wrapper ekit-zoom-layout-<?php echo esc_attr($layout); ?> ekit-zoom-protected-<?php echo esc_attr($is_protected); ?>" data-settings='<?php echo json_encode($custom_settings); ?>'>
				<!-- Protected Wrapper -->
				<?php if($is_protected == 'yes') : ?>
					<div class="ekit-zoom-protected">
						<div class="ekit-zoom-protected-inner">
							<form class="ekit-zoom-protected-form">
								<?php if(!empty($ekit_protected_heading)) : ?>
									<h4><?php echo esc_html($ekit_protected_heading); ?>
									(<?php echo esc_html($zoom_data->id); ?>)</h4>
								<?php endif; ?>
								
								<?php if(!empty($ekit_protected_subheading)) : ?>
									<p><?php echo esc_html($ekit_protected_subheading); ?></p>
								<?php endif; ?>

								<input id="fld_pass" class="ekit-zoom-password-field" name="zoom-password" type="password"
									placeholder="<?php echo esc_attr($ekit_protected_placeholder_text); ?>">

								<input type="hidden" id="fld_post_id" name="zoom-post-id" value="<?php echo esc_attr(get_the_ID()); ?>">
								<input type="hidden" id="fld_widget_id" name="zoom-widget-id" value="<?php echo esc_attr($widget_id); ?>">

								<input id="fld_pass_verify" type="submit" class="ekit-zoom-submit-field"
									value="<?php echo !empty($ekit_protected_submit_text) ? esc_html($ekit_protected_submit_text) : ''; ?>">
							</form>
						</div>
					</div>
				<?php endif; ?>
                <!-- ./End Protected Wrapper -->

				<div class="ekit-zoom-main-content">
					<?php if(isset($settings['ekit_zoom_enable_count_down_timer']) && $settings['ekit_zoom_enable_count_down_timer'] == 'yes') : ?>
						<div class="ekit-zoom-counter-wrapper">
							<?php if(!empty($meeting_timer_heading)){
								echo "<h3 class='ekit-zoom-counter-heading'>". esc_html($meeting_timer_heading) ."</h3>";
							} ?>
							<div class="ekit-zoom-counter" data-date="<?php echo esc_attr($zoom_data->start_time); ?>"></div>
						</div>
					<?php endif; ?>
					<?php if($isMettingDetails == 'yes') : ?>

						<!-- meeting heading -->
						<?php if(!empty($meeting_heading_text)) : ?>
						<div class="ekit-zoom-heading">
							<h2><?php echo esc_html($meeting_heading_text); ?></h2>
						</div>
						<?php endif; ?>

						<!-- meeting details -->
						<div class="ekit-zoom-details">
							<?php if(isset($ekit_show_meeting_id) && $ekit_show_meeting_id == 'yes') : ?>
								<div class="single-zoom-info">
									<span class="zoom-title"><?php echo esc_html($ekit_meeting_id_label); ?></span>
									<span class="zoom-subtitle"><?php echo esc_html($zoom_data->id); ?></span>
								</div>
							<?php endif; ?>

							<?php if(isset($ekit_show_meeting_topic) && $ekit_show_meeting_topic == 'yes') : ?>
								<div class="single-zoom-info">
									<span class="zoom-title"><?php echo esc_html($ekit_meeting_topic_label); ?></span>
									<span class="zoom-subtitle"><?php echo esc_html($zoom_data->topic); ?></span>
								</div>
							<?php endif; ?>

							<?php if(isset($ekit_show_meeting_status) && $ekit_show_meeting_status == 'yes') : ?>
								<div class="single-zoom-info">
									<span class="zoom-title"><?php echo esc_html($ekit_meeting_status_label); ?></span>
									<span class="zoom-subtitle"><?php echo esc_html($zoom_data->status); ?></span>
								</div>
							<?php endif; ?>

							<?php if(isset($ekit_show_meeting_start_time) && $ekit_show_meeting_start_time == 'yes') : ?>
								<div class="single-zoom-info">
									<span class="zoom-title"><?php echo esc_html($ekit_meeting_start_time_label); ?></span>
									<span class="zoom-subtitle"><?php echo esc_html($zoom_data->start_time); ?></span>
								</div>
							<?php endif; ?>

							<?php if(isset($ekit_show_meeting_duration) && $ekit_show_meeting_duration == 'yes') : ?>
								<div class="single-zoom-info">
									<span class="zoom-title"><?php echo esc_html($ekit_meeting_duration_label); ?></span>
									<span class="zoom-subtitle"><?php echo esc_html($zoom_data->duration); ?></span>
								</div>
							<?php endif; ?>

							<?php if(isset($ekit_show_meeting_timezone) && $ekit_show_meeting_timezone == 'yes') : ?>
								<div class="single-zoom-info">
									<span class="zoom-title"><?php echo esc_html($ekit_meeting_timezone_label); ?></span>
									<span class="zoom-subtitle"><?php echo esc_html($zoom_data->timezone); ?></span>
								</div>
							<?php endif; ?>
							<?php
							if($layout != 'flat') {
								echo $this->zoom_join_links($zoom_data);
							}
							?>
						</div>

						<?php if($layout == 'flat') : ?>
							<div class="ekit-zoom-join-links">
								<?php echo $this->zoom_join_links($zoom_data); ?>
							</div>
						<?php endif; ?>
						<!-- End meeting details -->
					<?php endif; ?>
				</div>
            </div>
		<?php endif;
	}
}
