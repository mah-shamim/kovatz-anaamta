<?php
namespace Elementor;

use Elementor\Modules\DynamicTags\Module as TagsModule;
use \Elementor\ElementsKit_Widget_Audio_Player_Handler as Handler;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Audio_Player extends Widget_Base {

	use \ElementsKit_Lite\Widgets\Widget_Notice;

	public $base;

	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
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
		return 'https://wpmet.com/doc/audio-player/';
	}

	public function get_style_depends() {
		return ['wp-mediaelement'];
	}

	public function get_script_depends() {
		return ['wp-mediaelement'];
	}

	protected function register_controls() {

		$this->start_controls_section(
			'ekit_audio_player_content_section',
			[
				'label' => esc_html__('Content', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_audio_player_source',
			[
				'label' => esc_html__('Source', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'default' => 'self',
				'options' => [
					'self' => esc_html__('Self Hosted', 'elementskit'),
					'external' => esc_html__('External', 'elementskit'),
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_self_url',
			[
				'label' => esc_html__('URL', 'elementskit'),
				'type'  => Controls_Manager::MEDIA,
				'description' => esc_html__('Support MP3 audio format', 'elementskit'),
				'media_type' => ['audio'],
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::URL_CATEGORY,
						TagsModule::MEDIA_CATEGORY,
						TagsModule::POST_META_CATEGORY,
					],
				],
				'condition' => [
					'ekit_audio_player_source' => 'self',
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_external_url',
			[
				'label' => esc_html__('URL', 'elementskit'),
				'label_block' => true,
				'placeholder' => esc_html__('Enter audio URL', 'elementskit'),
				'description' => esc_html__('Input a valid audio url', 'elementskit'),
				'type'  => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					'categories' => [
						TagsModule::URL_CATEGORY,
						TagsModule::POST_META_CATEGORY,
					],
				],
				'condition' => [
					'ekit_audio_player_source' => 'external',
				],
			]
		);

		// Audio Options
		$this->add_control(
			'ekit_audio_player_options_heading',
			[
				'label' => esc_html__('Audio Options', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_audio_player_autoplay',
			[
				'label' => esc_html__('Autoplay', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'ekit_audio_player_loop',
			[
				'label' => esc_html__('Loop', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'ekit_audio_player_muted',
			[
				'label' => esc_html__('Muted', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
			]
		);

		// Control Options
		$this->add_control(
			'ekit_audio_player_control_options_heading',
			[
				'label' => esc_html__('Control Options', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_audio_player_playpause',
			[
				'label' => esc_html__('Play Pause', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'ekit_audio_player_progress',
			[
				'label' => esc_html__('Progress Bar', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'ekit_audio_player_progress_handler',
			[
				'label' => esc_html__('Progress Handler', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'condition' => [
					'ekit_audio_player_progress' => ['yes']
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time-total .mejs-time-handle::before' => 'content: "";',
					'{{WRAPPER}} .ekit-audio-player .mejs-controls .mejs-time-rail .mejs-time-total' => '--mejs-time-overflow: visible;',
				]
			]
		);

		$this->add_control(
			'ekit_audio_player_current',
			[
				'label' => esc_html__('Current Time', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'ekit_audio_player_duration',
			[
				'label' => esc_html__('Total Duration', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'ekit_audio_player_volume',
			[
				'label' => esc_html__('Volume Bar', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_audio_player_hide_volume_touch_devices',
			[
				'label' => esc_html__('Hide Volume On Touch Devices', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__('Yes', 'elementskit'),
				'label_off' => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'condition' => [
					'ekit_audio_player_volume' => ['yes']
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_volume_slider_layout',
			[
				'label' => esc_html__('Volume Slider Layout', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'vertical' => esc_html__('Vertical', 'elementskit'),
					'horizontal' => esc_html__('Horizontal', 'elementskit'),
				],
				'condition' => [
					'ekit_audio_player_volume' => ['yes']
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_start_volume',
			[
				'label' => esc_html__('Start Volume', 'elementskit'),
				'description' => esc_html__('Initial volume when the player starts.', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 0.8,
				],
			]
		);

		$this->end_controls_section();

		// Player icon section
		$this->start_controls_section(
			'ekit_audio_player_icon_section',
			[
				'label' => esc_html__('Player Icons', 'elementskit'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ekit_audio_player_play_icon',
			[
				'label' => esc_html__('Play Icon', 'elementskit'),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'exclude_inline_options' => ['svg'],
				'default' => [
					'value' => 'icon icon-play-button',
					'library' => 'ekiticons',
				],
				'recommended' => [
					'ekiticons' => [
						'play-button',
						'play',
					],
					'fa-regular' => [
						'play-circle',
					],
					'fa-solid' => [
						'play',
						'play-circle',
					],
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_pause_icon',
			[
				'label' => esc_html__('Pause Icon', 'elementskit'),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'exclude_inline_options' => ['svg'],
				'default' => [
					'value' => 'icon icon-pause-dark',
					'library' => 'ekiticons',
				],
				'recommended' => [
					'ekiticons' => [
						'pause-dark',
						'pause',
					],
					'fa-regular' => [
						'pause-circle',
					],
					'fa-solid' => [
						'pause',
						'pause-circle',
					],
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_replay_icon',
			[
				'label' => esc_html__('Replay Icon', 'elementskit'),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'exclude_inline_options' => ['svg'],
				'default' => [
					'value' => 'icon icon-play-button',
					'library' => 'ekiticons',
				],
				'recommended' => [
					'ekiticons' => [
						'reload',
						'redo',
						'play-button',
						'play',
					],
					'fa-regular' => [
						'play-circle',
					],
					'fa-solid' => [
						'redo',
						'redo-alt',
						'play',
						'play-circle',
					],
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_volume_unmute_icon',
			[
				'label' => esc_html__('Unmute Icon', 'elementskit'),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'exclude_inline_options' => ['svg'],
				'default' => [
					'value' => 'icon icon-volume-high-dark',
					'library' => 'ekiticons',
				],
				'recommended' => [
					'ekiticons' => [
						'volume-high-dark',
					],
					'fa-solid' => [
						'volume-up',
					],
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_volume_mute_icon',
			[
				'label' => esc_html__('Mute Icon', 'elementskit'),
				'type' => Controls_Manager::ICONS,
				'label_block' => false,
				'skin' => 'inline',
				'exclude_inline_options' => ['svg'],
				'default' => [
					'value' => 'icon icon-volume-mute',
					'library' => 'ekiticons',
				],
				'recommended' => [
					'ekiticons' => [
						'volume-mute',
					],
					'fa-solid' => [
						'volume-mute',
					],
				],
			]
		);

		$this->end_controls_section();

		// Play pause button section
		$this->start_controls_section(
			'ekit_audio_player_playpause_style_section',
			[
				'label' => esc_html__('Play Pause Button', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_audio_player_playpause' => ['yes']
				],
			]
		);

		$this->add_responsive_control(
			'ekit_audio_player_playpause_font_size',
			[
				'label' => esc_html__('Size', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'rem'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 12,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-audio-player .mejs-playpause-button button'	=> 'font-size: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->start_controls_tabs(
			'ekit_audio_player_playpause_style_tabs'
		);

		$this->start_controls_tab(
			'ekit_audio_player_playpause_normal_style_tab',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_audio_player_playpause_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default'	=> '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-playpause-button button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_playpause_bg_color',
			[
				'label' => esc_html__('Background Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-playpause-button button' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_audio_player_playpause_box_shadow',
				'label' => esc_html__('Box Shadow', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-audio-player .mejs-playpause-button button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_audio_player_playpause_border',
				'label' => esc_html__('Border', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-audio-player .mejs-playpause-button button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_audio_player_playpause_hover_style_tab',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_audio_player_playpause_hover_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default'	=> '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-playpause-button button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_playpause_bg_hover_color',
			[
				'label' => esc_html__('Background Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-playpause-button button:hover' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_audio_player_playpause_hover_box_shadow',
				'label' => esc_html__('Box Shadow', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-audio-player .mejs-playpause-button button:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_audio_player_playpause_hover_border',
				'label' => esc_html__('Border', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-audio-player .mejs-playpause-button button:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'ekit_audio_player_playpause_border_radius',
			[
				'label' => esc_html__('Border Radius (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-playpause-button button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_audio_player_playpause_padding',
			[
				'label' => esc_html__('Padding (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-playpause-button button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_audio_player_playpause_margin',
			[
				'label' => esc_html__('Margin (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-playpause-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		// Progress bar section
		$this->start_controls_section(
			'ekit_audio_player_progress_bar_style_section',
			[
				'label' => esc_html__('Progress Bar', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_audio_player_progress' => ['yes']
				],
			]
		);

		$this->add_responsive_control(
			'ekit_audio_player_progress_bar_height',
			[
				'label' => esc_html__('Height', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time-total'	=> 'height: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_progress_bar_bg_color',
			[
				'label' => esc_html__('Background Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time-total' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_audio_player_progress_bar_border',
				'label' => esc_html__('Border', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-audio-player .mejs-time-total',
			]
		);

		$this->add_control(
			'ekit_audio_player_progress_bar_border_radius',
			[
				'label' => esc_html__('Border Radius (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time-total' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'ekit_audio_player_progress_handler!' => ['yes']
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_progress_bar_loaded_heading',
			[
				'label' => esc_html__('Loaded Progress Bar', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'ekit_audio_player_progress_bar_loaded_bg_color',
			[
				'label' => esc_html__('Background Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time-total .mejs-time-loaded' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_progress_bar_current_heading',
			[
				'label' => esc_html__('Current Progress Bar', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'ekit_audio_player_progress_bar_current_bg_color',
			[
				'label' => esc_html__('Background Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time-total .mejs-time-current' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_progress_bar_current_border_radius',
			[
				'label' => esc_html__('Border Radius (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time-total .mejs-time-current' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'ekit_audio_player_progress_handler!' => ['yes']
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_progress_bar_time_hover_heading',
			[
				'label' => esc_html__('Time Hover', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'ekit_audio_player_progress_bar_time_hover_color',
			[
				'label' => esc_html__('Background Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF00',
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time-total .mejs-time-hovered' => 'background: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		// Progress handler section
		$this->start_controls_section(
			'ekit_audio_player_progress_handler_style_section',
			[
				'label' => esc_html__('Progress Handler', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_audio_player_progress_handler' => ['yes']
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_progress_handler_bg_color',
			[
				'label' =>esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#0073aa',
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time-total .mejs-time-handle::before' => '--mejs-time-handle-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_audio_player_progress_handler_width',
			[
				'label' => esc_html__( 'Width (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '15',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time-total .mejs-time-handle::before' => '--mejs-time-handle-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_audio_player_progress_handler_height',
			[
				'label' => esc_html__( 'Height (px)', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '15',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time-total .mejs-time-handle::before' => '--mejs-time-handle-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'ekit_audio_player_progress_handler_border',
				'label'    => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-audio-player .mejs-time-total .mejs-time-handle::before',
			]
		);

		$this->add_control(
			'ekit_audio_player_progress_handler_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'default'    => [
					'top' => '100',
					'right' => '100',
					'bottom' => '100',
					'left' => '100',
					'unit' => '%',
					'isLinked' => true,
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time-total .mejs-time-handle::before' => '--mejs-time-handle-border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_audio_player_progress_handler_box_shadow',
				'label' => esc_html__( 'Box Shadow', 'elementskit' ),
				'fields_options' => [
					'box_shadow' => [
						'default'	=> [
							'color' => 'rgba(0, 0, 0, 0.32)',
							'horizontal' => 0,
							'vertical' => 1,
							'blur' => 1,
							'spread' => 0,
						],
						'selectors' => [
							'{{WRAPPER}} .ekit-audio-player .mejs-time-total .mejs-time-handle::before' => '--mejs-time-handle-box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}};',
						]
					],
				],
			]
		);

		$this->end_controls_section();

		// Time section
		$this->start_controls_section(
			'ekit_audio_player_time_style_section',
			[
				'label' => esc_html__('Time', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'name' => 'ekit_audio_player_current',
							'operator' => '==',
							'value' => 'yes'
						],
						[
							'name' => 'ekit_audio_player_duration',
							'operator' => '==',
							'value' => 'yes'
						]
					]
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name' => 'ekit_audio_player_time_typography',
				'fields_options' => [
					'typography' => ['default' => 'yes'],
					'font_size'      => [
						'default'    => [
							'size' => '12',
							'unit' => 'px'
						],
						'label'      => 'Font size',
					],
				],
				'selector' => '{{WRAPPER}} .ekit-audio-player .mejs-time span',
			]
		);

		$this->add_control(
			'ekit_audio_player_currenttime_heading',
			[
				'label' => esc_html__('Current Time', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'ekit_audio_player_current' => ['yes']
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_audio_player_currenttime_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time.mejs-currenttime-container' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_audio_player_current' => ['yes']
				],
			]
		);

		$this->add_responsive_control(
			'ekit_audio_player_currenttime_margin',
			[
				'label' => esc_html__('Margin (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time .mejs-currenttime' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ekit_audio_player_current' => ['yes']
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_durationtime_heading',
			[
				'label' => esc_html__('Duration Time', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_audio_player_duration' => ['yes']
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_durationtime_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time.mejs-duration-container' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_audio_player_duration' => ['yes']
				],
			]
		);

		$this->add_responsive_control(
			'ekit_audio_player_durationtime_margin',
			[
				'label' => esc_html__('Margin (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-time .mejs-duration' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ekit_audio_player_duration' => ['yes']
				],
			]
		);

		$this->end_controls_section();

		// Volume Section
		$this->start_controls_section(
			'ekit_audio_player_volume_style_section',
			[
				'label' => esc_html__('Volume', 'elementskit'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_audio_player_volume' => ['yes']
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_volume_button_heading',
			[
				'label' => esc_html__('Volume Button', 'elementskit'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'ekit_audio_player_volume_size',
			[
				'label' => esc_html__('Size', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'rem'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 12,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-audio-player .mejs-volume-button button'	=> 'font-size: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->start_controls_tabs(
			'ekit_audio_player_volume_btn_style_tabs'
		);

		$this->start_controls_tab(
			'ekit_audio_player_volume_btn_normal_style_tab',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_audio_player_volume_btn_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default'	=> '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-volume-button button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_volume_btn_bg_color',
			[
				'label' => esc_html__('Background Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-volume-button button' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_audio_player_volume_btn_box_shadow',
				'label' => esc_html__('Box Shadow', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-audio-player .mejs-volume-button button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_audio_player_volume_btn_border',
				'label' => esc_html__('Border', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-audio-player .mejs-volume-button button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_audio_player_volume_btn_hover_style_tab',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);

		$this->add_control(
			'ekit_audio_player_volume_btn_hover_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'default'	=> '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-volume-button button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_volume_btn_bg_hover_color',
			[
				'label' => esc_html__('Background Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-volume-button button:hover' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_audio_player_volume_btn_hover_box_shadow',
				'label' => esc_html__('Box Shadow', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-audio-player .mejs-volume-button button:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_audio_player_volume_btn_hover_border',
				'label' => esc_html__('Border', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-audio-player .mejs-volume-button button:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'ekit_audio_player_volume_btn_border_radius',
			[
				'label' => esc_html__('Border Radius (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-volume-button button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_audio_player_volume_btn_padding',
			[
				'label' => esc_html__('Padding (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-volume-button button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_audio_player_volume_btn_margin',
			[
				'label' => esc_html__('Margin (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-volume-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// Volume slider
		$this->add_control(
			'ekit_audio_player_volume_slider_heading',
			[
				'label' => esc_html__('Volume Slider', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_audio_player_volume_slider_layout' => ['vertical']
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_volume_slider_bg_color',
			[
				'label' => esc_html__('Background Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'ekit_audio_player_volume_slider_layout' => ['vertical']
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-volume-slider' => 'background: {{VALUE}}',
				],
			]
		);

		// Volume bar
		$this->add_control(
			'ekit_audio_player_volume_bar_heading',
			[
				'label' => esc_html__('Volume Bar', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_audio_player_volume_bar_width',
			[
				'label' => esc_html__('Width', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-audio-player :is(.mejs-horizontal-volume-total, .mejs-volume-total)'	=> 'width: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'ekit_audio_player_volume_slider_layout' => ['horizontal']
				],
			]
		);

		$this->add_responsive_control(
			'ekit_audio_player_volume_bar_height',
			[
				'label' => esc_html__('Height', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-audio-player .mejs-horizontal-volume-total'	=> 'height: {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					'ekit_audio_player_volume_slider_layout' => ['horizontal']
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_volume_bar_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player :is(.mejs-horizontal-volume-total, .mejs-volume-total)' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_audio_player_volume_bar_border',
				'label' => esc_html__('Border', 'elementskit'),
				'selector' => '{{WRAPPER}} .ekit-audio-player .mejs-horizontal-volume-total',
				'condition' => [
					'ekit_audio_player_volume_slider_layout' => ['horizontal']
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_volume_bar_border_radius',
			[
				'label' => esc_html__('Border Radius (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-horizontal-volume-total' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'ekit_audio_player_volume_slider_layout' => ['horizontal']
				],
				'condition' => [
					'ekit_audio_player_progress_handler!' => ['yes']
				],
			]
		);

		// Current volume bar
		$this->add_control(
			'ekit_audio_player_current_volume_bar_heading',
			[
				'label' => esc_html__('Current Volume Bar', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'ekit_audio_player_current_volume_bar_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player :is(.mejs-horizontal-volume-current, .mejs-volume-current)' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_current_volume_bar_border_radius',
			[
				'label' => esc_html__('Border Radius (px)', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px'],
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-horizontal-volume-total .mejs-horizontal-volume-current' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'ekit_audio_player_volume_slider_layout' => ['horizontal'],
					'ekit_audio_player_progress_handler!' => ['yes']
				],
			]
		);

		// Current volume bar
		$this->add_control(
			'ekit_audio_player_volume_handle_heading',
			[
				'label' => esc_html__('Volume Handle', 'elementskit'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_audio_player_volume_slider_layout' => ['vertical']
				],
			]
		);

		$this->add_control(
			'ekit_audio_player_volume_handle_color',
			[
				'label' => esc_html__('Color', 'elementskit'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-audio-player .mejs-volume-handle' => 'background: {{VALUE}}',
				],
				'condition' => [
					'ekit_audio_player_volume_slider_layout' => ['vertical']
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		echo '<div class="ekit-wid-con">';
			$this->render_raw();
		echo '</div>';
	}

	protected function get_icon_html( array $icon, array $attributes = [ 'aria-hidden' => 'true' ], $tag = 'i' ) {
		/**
		 * When the library value is svg it means that it's a SVG media attachment uploaded by the user.
		 * Otherwise, it's the name of the font family that the icon belongs to.
		 */
		if ( 'svg' === $icon['library'] ) {
			$output =\Elementor\Icons_Manager::render_uploaded_svg_icon( $icon['value'] );
		} else {
			$output = \Elementor\Icons_Manager::render_font_icon( $icon, $attributes, $tag );
		}
		return $output;
	}

	protected function render_raw() {
		$settings = $this->get_settings_for_display();

		extract($settings);

		// set audio url
		$audio_url = '';
		if($ekit_audio_player_source === 'self') {
			$audio_url = !empty($ekit_audio_player_self_url['url']) ? $ekit_audio_player_self_url['url'] : '';
		} elseif($ekit_audio_player_source === 'external') {
			$audio_url = $ekit_audio_player_external_url;
		}

		// set player features
		$features = [];
		($ekit_audio_player_playpause === 'yes') && array_push($features, 'playpause');
		($ekit_audio_player_current === 'yes') && array_push($features, 'current');
		($ekit_audio_player_progress === 'yes') && array_push($features, 'progress');
		($ekit_audio_player_duration === 'yes') && array_push($features, 'duration');
		($ekit_audio_player_volume === 'yes') && array_push($features, 'volume');

		// set settings data attributes
		$data_settings['features'] = !empty($features) ? $features : ['playpause']; // playpause, current, progress, duration, volume
		$data_settings['hideVolumeOnTouchDevices'] = ($ekit_audio_player_hide_volume_touch_devices === 'yes') ? true : false;
		$data_settings['audioVolume'] = (!empty($ekit_audio_player_volume_slider_layout)) ? $ekit_audio_player_volume_slider_layout: 'horizontal';
		$data_settings['startVolume'] = (!empty($ekit_audio_player_start_volume['size'])) ? $ekit_audio_player_start_volume['size']: 0.8;

		$data_settings['playerIcons'] = [
			'play' => !empty($ekit_audio_player_play_icon['value']) ? $ekit_audio_player_play_icon['value'] : 'icon icon-play-button',
			'pause' => !empty($ekit_audio_player_pause_icon['value']) ? $ekit_audio_player_pause_icon['value'] : 'icon icon-pause-dark',
			'replay' => !empty($ekit_audio_player_replay_icon['value']) ? $ekit_audio_player_replay_icon['value'] : 'icon icon-reload',
			'unmute' => !empty($ekit_audio_player_volume_unmute_icon['value']) ? $ekit_audio_player_volume_unmute_icon['value'] : 'icon icon-volume-high-dark',
			'mute' => !empty($ekit_audio_player_volume_mute_icon['value']) ? $ekit_audio_player_volume_mute_icon['value'] : 'icon icon-volume-mute',
		];

		// registering audio player wrapper default attributes
		$this->add_render_attribute(
			'wrapper',
			[
				'class' => 'ekit-audio-player',
				'data-audio-settings' => esc_attr(json_encode($data_settings)),
			]
		);

		// registering audio player default attributes.
		$this->add_render_attribute(
			'player',
			[
				'class' => 'ekit-player',
				'src' => esc_url($audio_url),
				'preload' => 'none',
				'controls' => '',
				'poster' => '',
				'width'	=> '100%',
			]
		);

		// audio options
		if (!empty($ekit_audio_player_autoplay) && $ekit_audio_player_autoplay === 'yes') {
			$this->add_render_attribute('player', 'autoplay', '');
		}

		if (!empty($ekit_audio_player_loop) && $ekit_audio_player_loop === 'yes') {
			$this->add_render_attribute('player', 'loop', '');
		}

		if (!empty($ekit_audio_player_muted) && $ekit_audio_player_muted === 'yes') {
			$this->add_render_attribute('player', 'muted', '');
		}

		$this->add_render_attribute('player', 'hidden', '');

		// final output
		if(!empty($audio_url)) : ?>
			<div <?php $this->print_render_attribute_string('wrapper'); ?>>
				<audio <?php $this->print_render_attribute_string('player'); ?>>
					<?php echo esc_html__('Your browser does not support the audio tag.', 'elementskit'); ?>
				</audio>
			</div>
		<?php else : ?>
			<div class="elemenetskit-alert-info">
				<?php echo esc_html__('Upload/Select an audio or use external audio url to work this widget.', 'elementskit'); ?>
			</div>
		<?php endif;
	}
}
