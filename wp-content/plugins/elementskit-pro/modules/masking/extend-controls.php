<?php
namespace Elementor;

use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Extend_Masking {

	private $dir;
	private $url;

	public function __construct() {
		// get current directory path
		$this->dir = dirname(__FILE__) . '/';

		// get current module's url
		$this->url = \ElementsKit::plugin_url() . 'modules/masking/';

		foreach ( $this->register_widget_lists() as $stack_name => $section_id ) {
			add_action( 'elementor/element/' . $stack_name . '/' . $section_id . '/after_section_end', [$this, 'register_masking_section'] );
		}
	}

	/**
	 * `key` as widget name and `value` as widget section id
	 *
	 * @return array
	 */
	public function register_widget_lists() {
		return [
			'image' => 'section_image',
			'image-box' => 'section_image',
			'video' => 'section_image_overlay',
			'theme-post-featured-image' => 'section_image',
			'elementskit-team' => 'ekit_team_popup_details',
			'elementskit-image-box' => 'ekit_image_box_section_button',
			'elementskit-image-swap' => 'ekit_img_swap_content_section',
			'elementskit-image-hover-effect' => 'content_section',
		];
	}

	/**
	 * Return a string of CSS rules
	 *
	 * @return array
	 */
	public function get_selectors($rules) {
		$selectors = [
			'common' => 'div{{WRAPPER}}:not(.elementor-widget-video, .elementor-widget-elementskit-image-hover-effect) .elementor-widget-container img',
			'video' => 'div{{WRAPPER}}.elementor-widget-video .elementor-widget-container',
			'imagehover' => 'div{{WRAPPER}}.elementor-widget-elementskit-image-hover-effect .elementor-widget-container',
		];

		return [
			$selectors['common'] => $rules,
			$selectors['video'] => $rules,
			$selectors['imagehover'] => $rules,
		];
	}

	/**
	 * Register masking section.
	 *
	 * @return void
	 */
	public function register_masking_section($element) {

		$element->start_controls_section(
			'ekit_masking_section',
			[
				'label'	=> esc_html__('ElementsKit Masking', 'elementskit'),
				'tab'	=> Controls_Manager::TAB_CONTENT,
			]
		);

		$element->add_control(
			'ekit_enable_masking',
			[
				'label' => esc_html__('Enable Masking', 'elementskit'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$element->add_control(
			'ekit_masking_shape_type',
			[
				'label' => esc_html__('Shape Type', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__('Default', 'elementskit'),
					'custom' => esc_html__('Custom', 'elementskit'),
				],
				'condition' => [
					'ekit_enable_masking' => 'yes',
				],
			]
		);

		$element->add_control(
			'ekit_masking_default_shapes',
			[
				'label' => esc_html__('Choose Shape', 'elementskit'),
				'type' => ElementsKit_Controls_Manager::IMAGECHOOSE,
				'default' => 'shape-1',
				'options' => [
					'shape-1' => [
						'title' => esc_html__( 'Shape 1', 'elementskit' ),
						'imagelarge' => $this->url . 'assets/shapes/shape-1.svg',
						'imagesmall' => $this->url . 'assets/shapes/shape-1.svg',
						'imagesmallheight' => '50px',
						'width' => '25%',
					],
					'shape-2' => [
						'title' => esc_html__( 'Shape 2', 'elementskit' ),
						'imagelarge' => $this->url . 'assets/shapes/shape-2.svg',
						'imagesmall' => $this->url . 'assets/shapes/shape-2.svg',
						'imagesmallheight' => '50px',
						'width' => '25%',
					],
					'shape-3' => [
						'title' => esc_html__( 'Shape 3', 'elementskit' ),
						'imagelarge' => $this->url . 'assets/shapes/shape-3.svg',
						'imagesmall' => $this->url . 'assets/shapes/shape-3.svg',
						'imagesmallheight' => '50px',
						'width' => '25%',
					],
					'shape-4' => [
						'title' => esc_html__( 'Shape 4', 'elementskit' ),
						'imagelarge' => $this->url . 'assets/shapes/shape-4.svg',
						'imagesmall' => $this->url . 'assets/shapes/shape-4.svg',
						'imagesmallheight' => '50px',
						'width' => '25%',
					],
					'shape-5' => [
						'title' => esc_html__( 'Shape 5', 'elementskit' ),
						'imagelarge' => $this->url . 'assets/shapes/shape-5.svg',
						'imagesmall' => $this->url . 'assets/shapes/shape-5.svg',
						'imagesmallheight' => '50px',
						'width' => '25%',
					],
					'shape-6' => [
						'title' => esc_html__( 'Shape 6', 'elementskit' ),
						'imagelarge' => $this->url . 'assets/shapes/shape-6.svg',
						'imagesmall' => $this->url . 'assets/shapes/shape-6.svg',
						'imagesmallheight' => '50px',
						'width' => '25%',
					],
					'shape-7' => [
						'title' => esc_html__( 'Shape 7', 'elementskit' ),
						'imagelarge' => $this->url . 'assets/shapes/shape-7.svg',
						'imagesmall' => $this->url . 'assets/shapes/shape-7.svg',
						'imagesmallheight' => '50px',
						'width' => '25%',
					],
					'shape-8' => [
						'title' => esc_html__( 'Shape 8', 'elementskit' ),
						'imagelarge' => $this->url . 'assets/shapes/shape-8.svg',
						'imagesmall' => $this->url . 'assets/shapes/shape-8.svg',
						'imagesmallheight' => '50px',
						'width' => '25%',
					],
					'shape-9' => [
						'title' => esc_html__( 'Shape 3', 'elementskit' ),
						'imagelarge' => $this->url . 'assets/shapes/shape-9.svg',
						'imagesmall' => $this->url . 'assets/shapes/shape-9.svg',
						'imagesmallheight' => '50px',
						'width' => '25%',
					],
					'shape-10' => [
						'title' => esc_html__( 'Shape 10', 'elementskit' ),
						'imagelarge' => $this->url . 'assets/shapes/shape-10.svg',
						'imagesmall' => $this->url . 'assets/shapes/shape-10.svg',
						'imagesmallheight' => '50px',
						'width' => '25%',
					],
					'shape-11' => [
						'title' => esc_html__( 'Shape 11', 'elementskit' ),
						'imagelarge' => $this->url . 'assets/shapes/shape-11.svg',
						'imagesmall' => $this->url . 'assets/shapes/shape-11.svg',
						'imagesmallheight' => '50px',
						'width' => '25%',
					],
					'shape-12' => [
						'title' => esc_html__( 'Shape 12', 'elementskit' ),
						'imagelarge' => $this->url . 'assets/shapes/shape-12.svg',
						'imagesmall' => $this->url . 'assets/shapes/shape-12.svg',
						'imagesmallheight' => '50px',
						'width' => '25%',
					],
				],
				'selectors' => $this->get_selectors('-webkit-mask-image: url('.$this->url.'assets/shapes/{{VALUE}}.svg);'),
				'condition' => [
					'ekit_enable_masking' => 'yes',
					'ekit_masking_shape_type' => 'default',
				],
			]
		);

		$element->add_control(
			'ekit_masking_image',
			[
				'label' => esc_html__('Choose Image', 'elementskit'),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'image',
				'library_type' => 'image/svg+xml',
				'should_include_svg_inline_option' => true,
				'dynamic' => [
					'active' => true,
				],
				'selectors' => $this->get_selectors('-webkit-mask-image: url({{URL}});'),
				'condition' => [
					'ekit_enable_masking' => 'yes',
					'ekit_masking_shape_type' => 'custom',
				],
			]
		);

		$element->add_responsive_control(
			'ekit_masking_image_position',
			[
				'label' => esc_html__('Position', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'default' => 'center center',
				'options' => [
					'center center' => esc_html__('Center Center', 'elementskit'),
					'center left' => esc_html__('Center Left', 'elementskit'),
					'center right' => esc_html__('Center Right', 'elementskit'),
					'top center' => esc_html__('Top Center', 'elementskit'),
					'top left' => esc_html__('Top Left', 'elementskit'),
					'top right' => esc_html__('Top Right', 'elementskit'),
					'bottom center' => esc_html__('Bottom Center', 'elementskit'),
					'bottom left' => esc_html__('Bottom Left', 'elementskit'),
					'bottom right' => esc_html__('Bottom Right', 'elementskit'),
					'custom' => esc_html__('Custom', 'elementskit'),
				],
				'selectors' => $this->get_selectors('-webkit-mask-position: {{VALUE}};'),
				'condition' => [
					'ekit_enable_masking' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'ekit_masking_image_position_x',
			[
				'label' => esc_html__('Position X', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%', 'vw'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
					'em' => [
						'min' => -100,
						'max' => 100,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'vw' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 0,
					'unit' => '%',
				],
				'selectors' => $this->get_selectors('-webkit-mask-position-x: {{SIZE}}{{UNIT}};'),
				'condition' => [
					'ekit_enable_masking' => 'yes',
					'ekit_masking_image_position' => 'custom',
				],
			]
		);

		$element->add_responsive_control(
			'ekit_masking_image_position_y',
			[
				'label' => esc_html__('Position Y', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%', 'vw'],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
					'em' => [
						'min' => -100,
						'max' => 100,
					],
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'vw' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 0,
					'unit' => '%',
				],
				'selectors' => $this->get_selectors('-webkit-mask-position-y: {{SIZE}}{{UNIT}};'),
				'condition' => [
					'ekit_enable_masking' => 'yes',
					'ekit_masking_image_position' => 'custom',
				],
			]
		);

		$element->add_responsive_control(
			'ekit_masking_image_repeat',
			[
				'label' => esc_html__('Repeat', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'default' => 'no-repeat',
				'options' => [
					'no-repeat' => esc_html__('No-repeat', 'elementskit'),
					'repeat' => esc_html__('Repeat', 'elementskit'),
					'repeat-x' => esc_html__('Repeat-x', 'elementskit'),
					'repeat-Y' => esc_html__('Repeat-y', 'elementskit'),
					'round' => esc_html__('Round', 'elementskit'),
					'space' => esc_html__('Space', 'elementskit'),
				],
				'selectors' => $this->get_selectors('-webkit-mask-repeat: {{VALUE}};'),
				'condition' => [
					'ekit_enable_masking' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'ekit_masking_image_size',
			[
				'label' => esc_html__('Size', 'elementskit'),
				'type' => Controls_Manager::SELECT,
				'default' => 'contain',
				'options' => [
					'auto' => esc_html__('Auto', 'elementskit'),
					'cover' => esc_html__('Cover', 'elementskit'),
					'contain' => esc_html__('Contain', 'elementskit'),
					'custom' => esc_html__('Custom', 'elementskit'),
				],
				'selectors' => $this->get_selectors('-webkit-mask-size: {{VALUE}};'),
				'condition' => [
					'ekit_enable_masking' => 'yes',
				],
			]
		);

		$element->add_responsive_control(
			'ekit_masking_image_custom_size',
			[
				'label' => esc_html__('Custom Size', 'elementskit'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%', 'vw'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'em' => [
						'min' => 0,
						'max' => 100,
					],
					'%' => [
						'min' => 0,
						'max' => 200,
					],
					'vw' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors' => $this->get_selectors('-webkit-mask-size: {{SIZE}}{{UNIT}};'),
				'condition' => [
					'ekit_enable_masking' => 'yes',
					'ekit_masking_image_size' => 'custom',
				],
			]
		);

		$element->end_controls_section(); // end section: ekit_masking_section
	}
}
