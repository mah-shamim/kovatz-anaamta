<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Woo_Product_Carousel_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Woo_Product_Carousel extends Widget_Base {
	use \ElementsKit_Lite\Widgets\Widget_Notice;

	public $base;

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->add_script_depends('magnific-popup');
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
        return 'https://wpmet.com/doc/woocommerce-product-carousel/';
    }

    public function elementskit_navigation_position() {
		$position_options = [
			'top-left'      => esc_html__('Top Left', 'elementskit') ,
			'top-center'    => esc_html__('Top Center', 'elementskit') ,
			'top-right'     => esc_html__('Top Right', 'elementskit') ,
			'center'        => esc_html__('Center', 'elementskit') ,
			'bottom-left'   => esc_html__('Bottom Left', 'elementskit') ,
			'bottom-center' => esc_html__('Bottom Center', 'elementskit') ,
			'bottom-right'  => esc_html__('Bottom Right', 'elementskit') ,
		];

		return $position_options;
	}


	public function elementskit_pagination_position() {
		$position_options = [
			'top-left'      => esc_html__('Top Left', 'elementskit') ,
			'top-center'    => esc_html__('Top Center', 'elementskit') ,
			'top-right'     => esc_html__('Top Right', 'elementskit') ,
			'bottom-left'   => esc_html__('Bottom Left', 'elementskit') ,
			'bottom-center' => esc_html__('Bottom Center', 'elementskit') ,
			'bottom-right'  => esc_html__('Bottom Right', 'elementskit') ,
		];

		return $position_options;
    }

    function elementskit_transition_options() {
        $transition_options = [
            ''                    => esc_html__('None', 'elementskit'),
            'fade'                => esc_html__('Fade', 'elementskit'),
            'scale-up'            => esc_html__('Scale Up', 'elementskit'),
            'scale-down'          => esc_html__('Scale Down', 'elementskit'),
            'slide-top'           => esc_html__('Slide Top', 'elementskit'),
            'slide-bottom'        => esc_html__('Slide Bottom', 'elementskit'),
            'slide-left'          => esc_html__('Slide Left', 'elementskit'),
            'slide-right'         => esc_html__('Slide Right', 'elementskit'),
            'slide-top-small'     => esc_html__('Slide Top Small', 'elementskit'),
            'slide-bottom-small'  => esc_html__('Slide Bottom Small', 'elementskit'),
            'slide-left-small'    => esc_html__('Slide Left Small', 'elementskit'),
            'slide-right-small'   => esc_html__('Slide Right Small', 'elementskit'),
            'slide-top-medium'    => esc_html__('Slide Top Medium', 'elementskit'),
            'slide-bottom-medium' => esc_html__('Slide Bottom Medium', 'elementskit'),
            'slide-left-medium'   => esc_html__('Slide Left Medium', 'elementskit'),
            'slide-right-medium'  => esc_html__('Slide Right Medium', 'elementskit'),
        ];

        return $transition_options;
    }


    public function register_controls() {

		$this->start_controls_section(
			'ekit_section_content_query',
			[
				'label' => esc_html__( 'Filter', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_posts',
			[
				'label'   => esc_html__( 'Product Limit', 'elementskit' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8,
			]
		);

        $this->add_control(
			'ekit_woo_product_select',
			[
				'label'   => esc_html__( 'Show product by ', 'elementskit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => [
                    'category' => esc_html__('Category', 'elementskit'),
                    'product' => esc_html__('Product', 'elementskit'),
                ],
			]
        );

		$this->add_control(
			'ekit_woo_cat',
			[
				'label'   => esc_html__( 'Category', 'elementskit' ),
				'type'    => ElementsKit_Controls_Manager::AJAXSELECT2,
                'options' => 'ajaxselect2/product_cat',
                'label_block' => true,
                'multiple'  => true,
                'condition' => [
                    'ekit_woo_product_select' => 'category',
                ],
			]
        );

		$this->add_control(
			'ekit_woo_product',
			[
				'label'   => esc_html__( 'Product', 'elementskit' ),
				'type'    => ElementsKit_Controls_Manager::AJAXSELECT2,
                'options' => 'ajaxselect2/product_list',
                'label_block' => true,
                'multiple'  => true,
                'condition' => [
                    'ekit_woo_product_select' => 'product',
                ],
			]
		);

		$this->add_control(
			'ekit_orderby',
			[
				'label'   => esc_html__( 'Order by', 'elementskit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => [
					'date'     => esc_html__( 'Date', 'elementskit' ),
					'title'    => esc_html__( 'Title', 'elementskit' ),
					'category' => esc_html__( 'Category', 'elementskit' ),
					'rand'     => esc_html__( 'Random', 'elementskit' ),
				],
			]
		);

		$this->add_control(
			'ekit_order',
			[
				'label'   => esc_html__( 'Order', 'elementskit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => [
					'DESC' => esc_html__( 'Descending', 'elementskit' ),
					'ASC'  => esc_html__( 'Ascending', 'elementskit' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_section_content_layout',
			[
				'label' => esc_html__( 'Layout', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_product_alignment',
			[
				'label'   => esc_html__( 'Content Alignment', 'elementskit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'vertical',
				'options' => [
					'vertical' 		=> esc_html__('Vertical', 'elementskit'),
                    'horizontal' 	=> esc_html__('Horizontal', 'elementskit'),
				],
			]
		);

		$this->add_control(
            'ekit_product_flip_content',
            [
                'label' => esc_html__('Flip Content? ', 'elementskit'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' =>esc_html__( 'Yes', 'elementskit' ),
				'label_off' =>esc_html__( 'No', 'elementskit' ),
				'condition'	=> [
					'ekit_product_alignment'	=> 'horizontal'
				]
            ]
        );

		$this->add_control(
			'ekit_product_description_position',
			[
				'label'   => esc_html__( 'Content Postion', 'elementskit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'outside',
				'options' => [
                    'inside' 	=> esc_html__('Inside Thumb', 'elementskit'),
                    'outside' 	=> esc_html__('Outside Thumb', 'elementskit'),
				],
				'condition'	=> [
					'ekit_product_alignment'	=> 'vertical'
				]
			]
		);

		$this->add_control(
			'ekit_columns_desktop',
			[
				'label'          => esc_html__( 'Desktop Columns', 'elementskit' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products-wrapper' => '--ekit_columns_desktop {{SIZE}};',
				],
				'devices' => [ 'desktop' ],
			]
		);

		$this->add_control(
			'ekit_columns_tablet',
			[
				'label'          => esc_html__( 'Tablet Columns', 'elementskit' ),
				'type'           => Controls_Manager::SELECT,
				'default' => '3',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'devices' => ['desktop', 'tablet'],
			]
		);

		$this->add_control(
			'ekit_columns_mobile',
			[
				'label'          => esc_html__( 'Mobile Columns', 'elementskit' ),
				'type'           => Controls_Manager::SELECT,
				'default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				],
				'devices' => ['desktop', 'tablet'],
			]
		);

		$this->add_responsive_control(
			'ekit_item_gap',
			[
				'label'   => esc_html__( 'Column Gap', 'elementskit' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 5,
					],
				],
				'render_type' => 'template',
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products-wrapper' => '--ekit-slider-item-gap {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'    => 'ekit_image',
				'label'   => esc_html__( 'Image Size', 'elementskit' ),
				'exclude' => [ 'custom' ],
				'default' => 'medium',
			]
		);


		$this->add_control(
			'ekit_open_thumb_in_popup',
			[
				'label'     => esc_html__( 'Open Thumb in Popup', 'elementskit' ),
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'ekit_show_badge',
			[
				'label'   => esc_html__( 'Show Badge', 'elementskit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'ekit_show_categories',
			[
				'label'     => esc_html__( 'Categories', 'elementskit' ),
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'ekit_show_title',
			[
				'label'   => esc_html__( 'Title', 'elementskit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'ekit_show_rating',
			[
				'label'   => esc_html__( 'Rating', 'elementskit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'ekit_show_price',
			[
				'label'   => esc_html__( 'Price', 'elementskit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'ekit_show_cart',
			[
				'label'   => esc_html__( 'Add to Cart', 'elementskit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_section_carousel_settings',
			[
				'label' => esc_html__( 'Carousel Settings', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_autoplay',
			[
				'label'   => esc_html__( 'Autoplay', 'elementskit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',

			]
		);

		$this->add_control(
			'ekit_autoplay_speed',
			[
				'label'     => esc_html__( 'Autoplay Speed', 'elementskit' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [
					'ekit_autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'ekit_loop',
			[
				'label'   => esc_html__( 'Loop', 'elementskit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',

			]
		);

		$this->add_control(
			'ekit_speed',
			[
				'label'   => esc_html__( 'Animation Speed', 'elementskit' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50,
				],
				'range' => [
					'min'  => 100,
					'max'  => 1000,
					'step' => 10,
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_section_content_navigation',
			[
				'label' => esc_html__( 'Navigation', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_navigation',
			[
				'label'   => esc_html__( 'Navigation', 'elementskit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'both'   => esc_html__( 'Arrows and Dots', 'elementskit' ),
					'arrows' => esc_html__( 'Arrows', 'elementskit' ),
					'dots'   => esc_html__( 'Dots', 'elementskit' ),
					'none'   => esc_html__( 'None', 'elementskit' ),
				],
				'prefix_class' => 'ekit-navigation-type-',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'ekit_both_position',
			[
				'label'     => esc_html__( 'Arrows and Dots Position', 'elementskit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => $this->elementskit_navigation_position(),
				'condition' => [
					'ekit_navigation' => 'boths',
				],
			]
		);

		$this->add_control(
			'ekit_arrows_position',
			[
				'label'     => esc_html__( 'Arrows Position', 'elementskit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => $this->elementskit_navigation_position(),
				'condition' => [
					'ekit_navigation' => 'arrowss',
				],
			]
		);

		$this->add_control(
			'ekit_dots_position',
			[
				'label'     => esc_html__( 'Dots Position', 'elementskit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom-center',
				'options'   => $this->elementskit_pagination_position(),
				'condition' => [
					'ekit_navigation' => 'dotss',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_section_style_item',
			[
				'label' => esc_html__( 'Item', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'ekit_tabs_item_style' );

		$this->start_controls_tab(
			'ekit_tab_item_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_item_background',
			[
				'label'     => esc_html__( 'Background', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-wc-carousel-item' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_item_border',
				'label'       => esc_html__( 'Border Color', 'elementskit' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ekit-wc-carousel .ekit-wc-carousel-item',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_item_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-wc-carousel-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_item_shadow',
				'selector' => '{{WRAPPER}} .ekit-wc-carousel .ekit-wc-carousel-item',
			]
		);

		$this->add_responsive_control(
			'ekit_item_padding',
			[
				'label'      => esc_html__( 'Item Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-wc-carousel-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekit_desc_padding',
			[
				'label'      => esc_html__( 'Description Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-wc-carousel-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_tab_item_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_item_hover_background',
			[
				'label'     => esc_html__( 'Background', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-wc-carousel-item:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_item_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'item_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-wc-carousel-item:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_item_hover_shadow',
				'selector' => '{{WRAPPER}} .ekit-wc-carousel .ekit-wc-carousel-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ekti_content_section',
			[
				'label' => esc_html__( 'Content', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_alignment',
			[
				'label'   => esc_html__( 'Horizontal Alignment', 'elementskit' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
			]
		);

		$this->add_responsive_control(
			'ekit_vertical_alignment',
			[
				'label'   => esc_html__( 'Vertical Alignment', 'elementskit' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'top',
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'elementskit' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'elementskit' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'elementskit' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'condition'	=> [
					'ekit_product_description_position'	=> 'inside'
				]
			]
		);

		$this->add_control(
			'ekit_alignment_vertical_alignment',
			[
				'label'   => esc_html__( 'Vertical Alignment', 'elementskit' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'elementskit' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'elementskit' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'elementskit' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
				'condition'	=> [
					'ekit_product_alignment' => 'horizontal'
				]

			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekti_content_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-product .ekit-wc-product-desc',
			]
		);

		$this->add_responsive_control(
			'ekti_content_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product .ekit-wc-product-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_content_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product .ekit-wc-product-desc' => 'Margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
            'ekti_content_title',
            [
                'label' => esc_html__( 'Title', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'ekit_show_title' => 'yes',
				],
            ]
		);

		$this->add_control(
			'ekti_title_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product-title' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'ekit_show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'ekti_hover_title_color',
			[
				'label'     => esc_html__( 'Hover Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product:hover .ekit-wc-product-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'ekit_show_title' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_title_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ekit_show_title' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekti_title_typography',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-desc .ekit-wc-product-title',
				'condition' => [
					'ekit_show_title' => 'yes',
				],
			]
		); // end title

		$this->add_control(
            'ekti_content_rating',
            [
                'label' => esc_html__( 'Rating:', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_show_rating' => 'yes',
				],
            ]
		);

		$this->add_control(
			'ekti_rating_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e7e7e7',
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .star-rating:before' => 'color: {{VALUE}};',
				],
				'condition' => [
					'ekit_show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'ekti_active_rating_color',
			[
				'label'     => esc_html__( 'Active Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFCC00',
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .star-rating span' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'ekit_show_rating' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_active_rating_font_size',
			[
				'label' => esc_html__( 'Font Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-desc .ekit-wc-rating .star-rating:before, {{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-desc .ekit-wc-rating .star-rating span:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_show_rating' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_active_rating_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-desc .ekit-wc-rating' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ekit_show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
            'ekti_content_price',
            [
                'label' => esc_html__( 'Price:', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_show_price' => 'yes',
				],
            ]
		);

		$this->add_control(
			'ekti_old_price_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-product .ekit-wc-product-desc .ekit-wc-product-price .price' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'ekit_show_price' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_old_price_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-product .ekit-wc-product-desc .ekit-wc-product-price .price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; !important',
				],
				'condition' => [
					'ekit_show_price' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekti_old_price_typography',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-desc .ekit-wc-product-price ins .woocommerce-Price-amount, {{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-desc .ekit-wc-product-price .woocommerce-Price-amount',
				'condition' => [
					'ekit_show_price' => 'yes',
				],
			]
		);

		$this->add_control(
			'ekti_sale_price_heading',
			[
				'label'     => esc_html__( 'Sale Price', 'elementskit' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_show_price' => 'yes',
				],
			]
		);

		$this->add_control(
			'ekti_sale_price_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-product del .woocommerce-Price-amount' => 'color: {{VALUE}} !important;',
				],
				'condition' => [
					'ekit_show_price' => 'yes',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'ekit_image_section',
			[
				'label'	=> esc_html__( 'Image', 'elementskit' ),
				'tab'	=> Controls_Manager::TAB_STYLE
			]
		);

		$this->add_responsive_control(
			'ekti_image_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_image_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekti_image_background_color',
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-product-image',
			]
		);

		$this->add_responsive_control(
            'ekti_image_overlay_heading',
            [
                'label' => esc_html__( 'Overlay', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekti_image_overlay',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-product-image:before',
				'separator' => 'after',
			]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'ekti_image_border',
				'label'    => esc_html__( 'Image Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-product-image',
			]
		);

		$this->add_responsive_control(
			'ekti_image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'    => 'ekti_image_shadow',
				'exclude' => [
					'shadow_position',
				],
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-product-image',
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'ekti_section_style_button',
			[
				'label'     => esc_html__( 'Button', 'elementskit' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_show_cart' => 'yes',
				],
			]
		);

		$this->add_control(
            'ekti_tab_cart_btn',
            [
                'label' => esc_html__( 'Cart:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
		);

		$this->add_control(
			'ekti_button_fullwidth',
			[
				'label'     => esc_html__( 'Fullwidth Button', 'elementskit' ),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .add_to_cart_button' => 'width: 100%;',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_button_alignment',
			[
				'label'   => esc_html__( 'Horizontal Alignment', 'elementskit' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
			]
		);

		$this->add_control(
			'ekit_button_vertical_alignment',
			[
				'label'   => esc_html__( 'Vertical Alignment', 'elementskit' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'elementskit' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'elementskit' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'elementskit' ),
						'icon' => 'eicon-v-align-bottom',
					],
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekti_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .add_to_cart_button',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'ekti_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'	 => [
					'top'		=> 100,
					'right'		=> 100,
					'bottom'	=> 100,
					'left'		=> 100
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .add_to_cart_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekti_button_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'	 => [
					'top'		=> 10,
					'right'		=> 45,
					'bottom'	=> 10,
					'left'		=> 30
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .add_to_cart_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekti_button_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'	 => [
					'top'		=> 0,
					'right'		=> 0,
					'bottom'	=> 0,
					'left'		=> 0
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .add_to_cart_button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekti_button_shadow',
				'selector' => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .add_to_cart_button',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'ekti_button_typography',
				'label'     => esc_html__( 'Typography', 'elementskit' ),
				'selector'  => '{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-image .ekit-wc-add-to-cart .ekit-woo-add-cart-text',
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs( 'ekti_tabs_button_style' );

		$this->start_controls_tab(
			'ekti_tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekti_button_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .add_to_cart_button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekti_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .add_to_cart_button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekti_tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekti_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .add_to_cart_button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekti_button_background_hover_color',
			[
				'label'     => esc_html__( 'Background Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .add_to_cart_button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekti_button_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'ekit_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .add_to_cart_button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
            'ekit_tab_cart_icon_divider',
            [
                'type' => Controls_Manager::DIVIDER,
                'style' => 'thick',
            ]
		);

		$this->add_control(
            'ekit_tab_cart_icon_switch',
            [
                'label' => esc_html__('Add icon? ', 'elementskit'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' =>esc_html__( 'Yes', 'elementskit' ),
                'label_off' =>esc_html__( 'No', 'elementskit' ),
            ]
		);

		$this->add_control(
            'ekit_tab_cart_icons',
            [
                'label' 	=> esc_html__( 'Icon', 'elementskit' ),
				'type'		 => Controls_Manager::ICONS,
				'fa4compatibility' => 'ekit_tab_cart_icon',
                'default' => [
                    'value' => 'icon icon-cart2',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_tab_cart_icon_switch' => 'yes'
                ]
            ]
		);

		$this->add_control(
			'ekit_tab_cart_icon_position',
			[
				'label' 	=> esc_html__( 'Position', 'elementskit' ),
				'type' 		=> Controls_Manager::SELECT,
				'default' 	=> 'right',
				'options' 	=> [
					'left' 	=> esc_html__( 'Left', 'elementskit' ),
					'right' => esc_html__( 'Right', 'elementskit' ),
				],
				'condition' => [
                    'ekit_tab_cart_icon_switch' => 'yes'
                ]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_tab_cart_icon_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-image .ekit-wc-add-to-cart .add_to_cart_button:before',
				'condition' => [
                    'ekit_tab_cart_icon_switch' => 'yes'
                ]
			]
		);

		$this->add_control(
			'ekit_tab_cart_icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'	 => [
					'top'		=> 100,
					'right'		=> 100,
					'bottom'	=> 100,
					'left'		=> 100
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-image .ekit-wc-add-to-cart .add_to_cart_button:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
                    'ekit_tab_cart_icon_switch' => 'yes'
                ]
			]
		);

		$this->add_control(
			'ekit_tab_cart_icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'	 => [
					'top'		=> 6,
					'right'		=> 8,
					'bottom'	=> 6,
					'left'		=> 8
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-image .ekit-wc-add-to-cart .add_to_cart_button:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
                    'ekit_tab_cart_icon_switch' => 'yes'
                ]
			]
		);

		$this->add_control(
			'ekit_tab_cart_icon_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'	 => [
					'top'		=> 0,
					'right'		=> 2,
					'bottom'	=> 0,
					'left'		=> 0
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-image .ekit-wc-add-to-cart .add_to_cart_button:before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
                    'ekit_tab_cart_icon_switch' => 'yes'
                ]
			]
		);

		$this->start_controls_tabs( 'ekit_tab_cart_icon_color_tabs' );

		$this->start_controls_tab(
			'ekit_tab_cart_icon_normal_color',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
				'condition' => [
                    'ekit_tab_cart_icon_switch' => 'yes'
                ]
			]
		);

		$this->add_responsive_control(
			'ekit_tab_cart_icon_color',
			[
				'label' =>esc_html__( 'Icon Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-image .ekit-wc-add-to-cart .add_to_cart_button:before' => 'color: {{VALUE}};',
				],
				'condition' => [
                    'ekit_tab_cart_icon_switch' => 'yes'
                ]
			]
		);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_tab_cart_icon_bg_color',
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-image .ekit-wc-add-to-cart .add_to_cart_button:before',
				'condition' => [
                    'ekit_tab_cart_icon_switch' => 'yes'
                ]
            )
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_tab_cart_icon_hover_color',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
				'condition' => [
                    'ekit_tab_cart_icon_switch' => 'yes'
                ]
			]
		);

		$this->add_responsive_control(
			'ekit_tab_cart_icon_hover_text_color',
			[
				'label' =>esc_html__( 'Icon Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-image .ekit-wc-add-to-cart:hover .add_to_cart_button:before' => 'color: {{VALUE}};',
				],
				'condition' => [
                    'ekit_tab_cart_icon_switch' => 'yes'
                ]
			]
		);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_tab_cart_icon_hover_bg_color',
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-image .ekit-wc-add-to-cart:hover .add_to_cart_button:before',
				'condition' => [
                    'ekit_tab_cart_icon_switch' => 'yes'
                ]
            )
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_section_style_badge',
			[
				'label'     => esc_html__( 'Badge', 'elementskit' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_show_badge' => 'yes',
				],
			]
		);

		$this->add_control(
			'ekti_badge_fullwidth',
			[
				'label'     => esc_html__( 'Fullwidth Badge', 'elementskit' ),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products-badge > .onsale' => 'width: 100%;',
				],
				'separator' => 'after',
			]
		);

		$this->add_control(
			'ekti_badge_alignment',
			[
				'label'   => esc_html__( 'Horizontal Alignment', 'elementskit' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
			]
		);

		$this->add_control(
			'ekti_badge_vertical_alignment',
			[
				'label'   => esc_html__( 'Vertical Alignment', 'elementskit' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'top',
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'elementskit' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'elementskit' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'elementskit' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
			]
		);

		$this->add_responsive_control(
			'ekti_badge_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product .onsale' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekti_badge_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product .onsale' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekti_badge_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product .onsale' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'ekti_badge_typo',
				'label'     => esc_html__( 'Text Typography', 'elementskit' ),
				'selector'  => '{{WRAPPER}} .ekit-wc-products .ekit-wc-product .onsale',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekti_badge_shadow',
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-product .onsale',
				'separator' => 'before',
			]
		);

		$this->start_controls_tabs('ekti_badge_tabs_section');

		$this->start_controls_tab(
			'ekti_badge_normal',
			[
				'label'	=> esc_html__( 'Normal', 'elementskit' )
			]
		);

		$this->add_control(
			'ekti_badge_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product .onsale' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekti_badge_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product .onsale' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekti_badge_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ekit-wc-products .ekit-wc-product .onsale',
				'separator'   => 'before',
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekti_badge_hover',
			[
				'label'	=> esc_html__( 'Hover', 'elementskit' )
			]
		);

		$this->add_control(
			'ekti_badge_text_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product:hover .onsale' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekti_badge_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product:hover .onsale' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekti_badge_hover_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ekit-wc-products .ekit-wc-product:hover .onsale',
				'separator'   => 'before',
			]
		);


		$this->end_controls_tabs();

		$this->end_controls_section();





		$this->start_controls_section(
			'ekit_section_style_button',
			[
				'label'     => esc_html__( 'Add to Cart Button', 'elementskit' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_cart' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'ekit_tabs_button_style' );

		$this->start_controls_tab(
			'ekit_tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_button_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-wc-add-to-cart a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_button_background',
			[
				'label'     => esc_html__( 'Background', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-wc-add-to-cart a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_button_shadow',
				'selector' => '{{WRAPPER}} .ekit-wc-carousel .ekit-wc-add-to-cart a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_button_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ekit-wc-carousel .ekit-wc-add-to-cart a',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'ekit_button_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-wc-add-to-cart a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_button_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-wc-add-to-cart a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_overlay_animation',
			[
				'label'     => esc_html__( 'Overlay Animation', 'elementskit' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fade',
				'options'   => $this->elementskit_transition_options(),
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_overlay_background',
			[
				'label'  => esc_html__( 'Overlay Color', 'elementskit' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-overlay-default' => 'background: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_button_typography',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wc-carousel .ekit-wc-add-to-cart a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_button_hover_background',
			[
				'label' => esc_html__( 'Background', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-wc-add-to-cart a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_button_hover_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-wc-add-to-cart a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_button_hover_border_color',
			[
				'label' => esc_html__( 'Border Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'ekit_button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-wc-add-to-cart a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_section_style_navigation',
			[
				'label'     => esc_html__( 'Navigation', 'elementskit' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->start_controls_tabs('ekit_arrows_tabs');
			$this->start_controls_tab(
				'ekit_arrows_prev_icon_tab',
				[
					'label'	=> esc_html__('Previous', 'elementskit')
				]
			);

			$this->add_control(
				'ekit_arrows_prev_icons',
				[
					'label' 	=> esc_html__( 'Icon', 'elementskit' ),
					'type'		 => Controls_Manager::ICONS,
					'fa4compatibility' => 'ekit_arrows_prev_icon',
					'default' => [
						'value' => 'fas fa-angle-left',
						'library' => 'solid',
					],
					'condition' => [
						'ekit_tab_cart_icon_switch' => 'yes'
					]
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'ekit_arrows_next_icon_tab',
				[
					'label'	=> esc_html__('Next', 'elementskit')
				]
			);

			$this->add_control(
				'ekit_arrows_next_icons',
				[
					'label' 	=> esc_html__( 'Icon', 'elementskit' ),
					'type'		 => Controls_Manager::ICONS,
					'fa4compatibility' => 'ekit_arrows_next_icon',
					'default' => [
						'value' => 'fas fa-angle-right',
						'library' => 'solid',
					],
					'condition' => [
						'ekit_tab_cart_icon_switch' => 'yes'
					]
				]
			);

			$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'ekit_arrows_size',
			[
				'label' => esc_html__( 'Arrows Size', 'elementskit' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-slidenav-container .ekit-slidenav' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-slidenav-container .ekit-slidenav svg'	=> 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'ekit_arrows_background',
			[
				'label'     => esc_html__( 'Background Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-navigation-prev, {{WRAPPER}} .ekit-wc-carousel .ekit-navigation-next' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'ekit_arrows_hover_background',
			[
				'label'     => esc_html__( 'Hover Background Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-navigation-prev:hover, {{WRAPPER}} .ekit-wc-carousel .ekit-navigation-next:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'ekit_arrows_space',
			[
				'label' => esc_html__( 'Space', 'elementskit' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-navigation-prev' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .ekit-wc-carousel .ekit-navigation-next' => 'margin-left: {{SIZE}}px;',
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'ekit_navigation',
							'value' => 'both',
						],
						[
							'name'     => 'ekit_both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'ekit_arrows_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-navigation-prev, {{WRAPPER}} .ekit-wc-carousel .ekit-navigation-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_arrows_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-navigation-prev, {{WRAPPER}} .ekit-wc-carousel .ekit-navigation-next' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_arrow_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ekit-slidenav-container .ekit-slidenav',
			]
		);

		$this->add_control(
			'ekit_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'separator'  => 'after',
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-navigation-prev, {{WRAPPER}} .ekit-wc-carousel .ekit-navigation-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ekit_navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->start_controls_tabs('ekit_arrows_color_tabs');
		$this->start_controls_tab(
			'ekit_arrows_color_normal_tab',
			[
				'label'	=> esc_html__('Normal', 'elementskit')
			]
		);
		$this->add_control(
			'ekit_arrows_color',
			[
				'label'     => esc_html__( 'Arrows Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  .ekit-slidenav-container .ekit-slidenav' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-slidenav-container .ekit-slidenav svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
				'condition' => [
					'ekit_navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_arrows_color_hover_tab',
			[
				'label'	=> esc_html__('Hover', 'elementskit')
			]
		);

		$this->add_control(
			'ekit_arrows_hover_color',
			[
				'label'     => esc_html__( 'Arrows Hover Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  .ekit-slidenav-container .ekit-slidenav:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-slidenav-container .ekit-slidenav:hover svg path'	=> 'stroke: {{VALUE}}; fill: {{VALUE}};',
				],
				'condition' => [
					'ekit_navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control(
			'ekit_arrows_acx_position',
			[
				'label'   => esc_html__( 'Horizontal Offset', 'elementskit' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .ekit-wc-carousel .ekit-navigation-next' => 'right: {{SIZE}}px;',
				],
				'condition' => [
					'ekit_navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->add_control(
			'ekit_dots_nny_position',
			[
				'label'   => esc_html__( 'Vertical Offset', 'elementskit' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-carousel .ekit-navigation-prev, {{WRAPPER}} .ekit-wc-carousel .ekit-navigation-next' => 'transform: translateY({{SIZE}}px);',
				],
				'condition'   => [
					'ekit_navigation' => [ 'arrows', 'both' ],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit-woo-carousel-dots',
			[
				'label'	=> esc_html__( 'Dots', 'elementskit' ),
				'tab'	=> Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'ekit_navigation' => [ 'dots', 'both' ],
				]
			]
		);

		$this->add_control(
			'ekit_dots_size',
			[
				'label' => esc_html__( 'Dots Size', 'elementskit' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-swiper-pagination .swiper-pagination-bullet ' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_navigation' => [ 'dots', 'both' ],
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ekit_dots_space',
			[
				'label' => esc_html__( 'Dots Space', 'elementskit' ),
				'type'  => Controls_Manager::SLIDER,
				'default' => [
					'size' => 5,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],

				'selectors' => [
					'{{WRAPPER}} .ekit-swiper-pagination .swiper-pagination-bullet ' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_navigation' => [ 'dots', 'both' ],
				],
			]
		);

		$this->add_responsive_control(
			'ekit_dots_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-swiper-pagination' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_dots_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-swiper-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'ekit_dots_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'	 => [
					'top'		=> 100,
					'right'		=> 100,
					'bottom'	=> 100,
					'left'		=> 100
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-swiper-pagination .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ekit_navigation' => [ 'dots', 'both' ],
				],
			]
		);


		$this->add_control(
			'ekti_dots_alignment',
			[
				'label'   => esc_html__( 'Alignment', 'elementskit' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-swiper-pagination' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs('ekit_dots_color_tabs');
			$this->start_controls_tab(
				'ekit_dots_normal_tab',
				[
					'label'	=> esc_html__('Normal', 'elementskit')
				]
			);

			$this->add_control(
				'ekit_dots_color',
				[
					'label'     => esc_html__( 'Dots Color', 'elementskit' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ekit-swiper-pagination .swiper-pagination-bullet' => 'background-color: {{VALUE}}',
					],
					'condition' => [
						'ekit_navigation' => [ 'dots', 'both' ],
					],
					'separator' => 'after',
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'ekit_dots_hover_tab',
				[
					'label'	=> esc_html__('Hover', 'elementskit')
				]
			);

			$this->add_control(
				'ekit_dots_hover_color',
				[
					'label'     => esc_html__( 'Dots Color', 'elementskit' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ekit-swiper-pagination .swiper-pagination-bullet:hover, {{WRAPPER}} .ekit-swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active:hover' => 'background-color: {{VALUE}}',
					],
					'condition' => [
						'ekit_navigation' => [ 'dots', 'both' ],
					],
					'separator' => 'after',
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'ekit_dots_active_tab',
				[
					'label'	=> esc_html__('Active', 'elementskit')
				]
			);

			$this->add_control(
				'ekit_active_dot_color',
				[
					'label'     => esc_html__( 'Active Dots Color', 'elementskit' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ekit-swiper-pagination .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'background-color: {{VALUE}}',
					],
					'default'	=> '#cccccc',
					'condition' => [
						'ekit_navigation' => [ 'dots', 'both' ],
					],
					'separator' => 'after',
				]
			);

			$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();



		$this->start_controls_section(
			'ekti_section_style_categories',
			[
				'label'      => esc_html__( 'Categories', 'elementskit' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name'  => 'ekit_show_categories',
							'value' => 'yes',
						],
					],
				],
			]
		);

		$this->add_control(
			'ekti_categories_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-product-categories ul li a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekti_categories_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-product:hover .ekit-wc-product-categories ul li a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekti_categories_typography',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wc-product-categories ul li a',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_open_thumb_in_popup_section',
			[
				'label'      => esc_html__( 'Popup', 'elementskit' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_open_thumb_in_popup'	=> 'yes'
				],
			]
		);

		$this->add_responsive_control(
            'ekit_popup_icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default'		 => [
					'size' 	=> '16',
					'unit'	=> 'px'
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                        'step' => 5,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wc-product .ekit-wc-product-image .ekit-wc-product-popop .ekit-wc-product-popop--link' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
		);

		$this->add_control(
			'ekit_popup_alignment',
			[
				'label'   => esc_html__( 'Horizontal Alignment', 'elementskit' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'right',
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
			]
		);

		$this->add_control(
			'ekit_popup_vertical_alignment',
			[
				'label'   => esc_html__( 'Vertical Alignment', 'elementskit' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'top',
				'options' => [
					'top' => [
						'title' => esc_html__( 'Top', 'elementskit' ),
						'icon' => 'eicon-v-align-top',
					],
					'center' => [
						'title' => esc_html__( 'Middle', 'elementskit' ),
						'icon' => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => esc_html__( 'Bottom', 'elementskit' ),
						'icon' => 'eicon-v-align-bottom',
					],
				],
			]
		);

		$this->add_control(
			'ekit_popup_icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'	 => [
					'top'		=> 100,
					'right'		=> 100,
					'bottom'	=> 100,
					'left'		=> 100
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-product .ekit-wc-product-image .ekit-wc-product-popop .ekit-wc-product-popop--link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_popup_icon_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'	 => [
					'top'		=> 9,
					'right'		=> 9,
					'bottom'	=> 9,
					'left'		=> 9
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-product .ekit-wc-product-image .ekit-wc-product-popop .ekit-wc-product-popop--link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_popup_icon_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'	 => [
					'top'		=> 10,
					'right'		=> 10,
					'bottom'	=> 10,
					'left'		=> 10
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-product .ekit-wc-product-image .ekit-wc-product-popop .ekit-wc-product-popop--link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'ekit_popup_color_tabs' );

		$this->start_controls_tab(
			'ekit_popup_normal_color',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_popup_normal_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#495459',
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-product .ekit-wc-product-image .ekit-wc-product-popop .ekit-wc-product-popop--link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'		=> 'ekit_popup_normal_icon_bg_color',
				'default' => '#fff',
				'selector'	=> '{{WRAPPER}} .ekit-wc-product .ekit-wc-product-image .ekit-wc-product-popop .ekit-wc-product-popop--link'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'ekit_popup_normal_icon_border',
				'label'     => esc_html__( 'Border', 'elementskit' ),
				'selector'  => '{{WRAPPER}} .ekit-wc-product .ekit-wc-product-image .ekit-wc-product-popop .ekit-wc-product-popop--link',
				'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'width' => [
                        'default' => [
                            'top' => '1',
                            'right' => '1',
                            'bottom' => '1',
                            'left' => '1',
                            'isLinked' => false,
                        ],
                    ],
                    'color' => [
                        'default' => '#495459',
                    ],
                ],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_popup_hover_color',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_popup_hover_icon_color',
			[
				'label'     => esc_html__( 'Icon Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-product .ekit-wc-product-image .ekit-wc-product-popop .ekit-wc-product-popop--link:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'		=> 'ekit_popup_hover_icon_bg_color',
				'selector'	=> '{{WRAPPER}} .ekit-wc-product .ekit-wc-product-image .ekit-wc-product-popop .ekit-wc-product-popop--link:hover'
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'ekit_popup_hover_icon_border',
				'label'     => esc_html__( 'Border', 'elementskit' ),
				'selector'  => '{{WRAPPER}} .ekit-wc-product .ekit-wc-product-image .ekit-wc-product-popop .ekit-wc-product-popop--link:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();


		// view cart button
		$this->start_controls_section(
			'ekit_product_carousel_button_section',
			[
				'label' => esc_html__( 'View Cart Button', 'elementskit' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
        );

        $this->add_responsive_control(
            'ekit_product_carousel_button_padding',
            [
                'label' => esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'	 => [
					'top'		=> 8,
					'right'		=> 30,
					'bottom'	=> 8,
					'left'		=> 30
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
		);

		$this->add_responsive_control(
            'ekit_product_carousel_button_margin',
            [
                'label' => esc_html__( 'Margin', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default'	 => [
					'top'		=> 5,
					'right'		=> 0,
					'bottom'	=> 0,
					'left'		=> 0
				],
                'selectors' => [
                    '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_product_carousel_button_typo',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart',
			]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_product_carousel_button_txt_shadow',
                'label' => esc_html__( 'Text Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart',
            ]
        );

        $this->add_responsive_control(
			'ekit_product_carousel_button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'	=> [
					'top' => 100,
					'right' => 100,
					'bottom' => 100,
					'left' => 100,
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_product_carousel_button_shadow',
				'selector' => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart',
			]
		);

        $this->start_controls_tabs('ekit_product_carousel_button_color_tabs');
            $this->start_controls_tab(
                'ekit_product_carousel_button_color_normal_tab',
                [
                    'label' => esc_html__('Normal', 'elementskit')
                ]
            );

            $this->add_control(
                'ekit_product_carousel_button_normal_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                array(
                    'name'     => 'ekit_product_carousel_button_normal_bg_color',
                    'selector' => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart',
                )
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'      => 'ekit_product_carousel_button_border',
                    'label'     => esc_html__( 'Border', 'elementskit' ),
                    'selector'  => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'ekit_product_carousel_button_color_hover_tab',
                [
                    'label' => esc_html__('Hover', 'elementskit')
                ]
            );

            $this->add_control(
                'ekit_product_carousel_button_hover_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart:hover' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                array(
                    'name'     => 'ekit_product_carousel_button_hover_bg_color',
                    'selector' => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart:hover',
                )
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'      => 'ekit_product_carousel_hover_button_border',
                    'label'     => esc_html__( 'Border', 'elementskit' ),
                    'selector'  => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart:hover',
                ]
            );

            $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
		// end view cart button

		$this->insert_pro_message();
	}

	public function render_image() {
		global $product;
		$settings = $this->get_settings();
		?>
		<div class="ekit-wc-product-image ekit-wc-carousel-image ekit-background-cover">
			<!-- description -->
			<?php
				if($settings['ekit_product_description_position'] === 'inside' && $settings['ekit_product_alignment'] === 'vertical'){
					$this->render_description();
			   }
			?>


			<!-- popup content -->
			<?php if($settings['ekit_open_thumb_in_popup'] === 'yes') :
				$popupHorizontal_align = !empty($settings['ekit_popup_alignment']) ? ' popup-' . esc_attr( $settings['ekit_popup_alignment'] ) : '';
				$popupVertical_align = !empty($settings['ekit_popup_vertical_alignment']) ? ' popup-vertical-' . esc_attr( $settings['ekit_popup_vertical_alignment'] ) : '';
			?>
				<div class="ekit-wc-product-popop <?php echo esc_attr( $popupHorizontal_align ); ?> <?php echo esc_attr( $popupVertical_align ); ?>">
					<a class="ekit-wc-product-popop--link" href="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'full' ); ?>"><i class="fas fa-eye" aria-hidden="true"></i></a>
				</div>
			<?php endif; ?>

			<?php if ('yes' == $settings['ekit_show_badge']) :
				$horizontal_align = !empty($settings['ekti_badge_alignment']) ? ' badge-' . esc_attr( $settings['ekti_badge_alignment'] ) : '';
				$vertical_align = !empty($settings['ekti_badge_vertical_alignment']) ? ' badge-vertical-' . esc_attr( $settings['ekti_badge_vertical_alignment'] ) : '';
			?>
				<div class="ekit-wc-products-badge ekit-wc-carousel-badge ekit-position-top-left ekit-position-small <?php echo esc_attr( $horizontal_align ); ?> <?php echo esc_attr( $vertical_align ); ?>">
					<?php woocommerce_show_product_loop_sale_flash(); ?>
				</div>
			<?php endif; ?>

			<?php if ('yes' == $settings['ekit_show_cart']) :

				// new icon
				$migrated = isset( $settings['__fa4_migrated']['ekit_tab_cart_icons'] );
				// Check if its a new widget without previously selected icon using the old Icon control
				$is_new = empty( $settings['ekit_tab_cart_icon'] );

				$iconCls = $settings['ekit_tab_cart_icon_switch'] == 'yes' ? ($is_new || $migrated) ? esc_attr( $settings['ekit_tab_cart_icons']['value'] ) :  esc_attr( $settings['ekit_tab_cart_icon'] ) : '';
				$iconPos  = !empty($settings['ekit_tab_cart_icon_position']) ? 'ekit-cart-icon-pos-' . esc_attr( $settings['ekit_tab_cart_icon_position'] ) : '';
			?>
				<div class="ekit-position-cover ekit-overlay-default ekit-transition-<?php echo esc_attr($settings['ekit_overlay_animation']); ?>">
					<div class="ekit-wc-add-to-cart ekit-position-center ekit-cart-align-<?php echo esc_attr($settings['ekit_button_alignment']) ?> ekit-cart-vertical-align-<?php echo esc_attr($settings['ekit_button_vertical_alignment']) ?>">
					<?php
						$isCart = false;
						if($product->is_purchasable() && $product->is_type('simple') == true) { $isCart = true; }
						$add_cart_btn = '<a data-product_id="'. $product->get_id() .'" href="'. $product->add_to_cart_url() .'" class="'.$iconPos . ' ' . $iconCls . ' button product_type_simple add_to_cart_button '. ($isCart == true ? "ajax_add_to_cart" : '') .'">'. $product->add_to_cart_text() .'</a>';
						echo str_replace($product->add_to_cart_text(), '<span class="ekit-woo-add-cart-text">'. $product->add_to_cart_text() .'</span>', $add_cart_btn);
					?>
					</div>
				</div>
			<?php endif; ?>

			<a href="<?php the_permalink(); ?>">
				<img src="<?php echo wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['ekit_image_size']); ?>" alt="Product Thumb">
			</a>
		</div>
		<?php
	}

	public function render_description() {
		$settings = $this->get_settings();
		global $product;
		?>

		<div class="ekit-wc-product-desc ekit-wc-carousel-desc ekit-padding ekit-position-relative">
			<div class="ekit-wc-product-desc-inner ekit-wc-carousel-desc-inner">
				<!-- categories -->
				<?php
					if($settings['ekit_show_categories'] === 'yes'){
						$terms = get_the_terms( get_the_ID(), 'product_cat' );
						$terms_count = count($terms);

						if($terms_count > 0){
							echo "<div class='ekit-wc-product-categories'><ul>";
							foreach($terms as $key => $term){
								$sperator = $key !== ($terms_count -1) ? ',' : '';
								echo "<li><a href='". get_term_link($term->term_id) ."'>". esc_html( $term->name ) . $sperator . "</a></li>";
							}
							echo "</ul></div>";
						}
					}
				?>
				<!-- end categories -->

				<?php if ( 'yes' == $settings['ekit_show_title']) : ?>
					<a href="<?php echo get_the_permalink(); ?>">
						<div class="ekit-wc-product-title ekit-wc-carousel-title">
							<?php the_title(); ?>
						</div>
					</a>
				<?php endif; ?>

				<?php if (('yes' == $settings['ekit_show_price']) or ('yes' == $settings['ekit_show_rating'])) : ?>
					<div class="ekit-wc-carousel-price-wrapper ekit-flex-middle ekit-grid">
					<?php if ('yes' == $settings['ekit_show_rating']) : ?>
							<div class="ekit-wc-rating ekit-flex-right ekit-width-expand">
								<?php
									if($product->get_rating_count() > 0){
										woocommerce_template_loop_rating();
									} else {
										$rating_html  = '<div class="star-rating">';
										$rating_html .= wc_get_star_rating_html( 0, 0 );
										$rating_html .= '</div>';

										echo \ElementsKit_Lite\Utils::render($rating_html);
									}
								?>
							</div>
						<?php endif; ?>

						<?php if ( 'yes' == $settings['ekit_show_price']) : ?>
							<div class="ekit-wc-carousel-price ekit-width-auto">
								<div class="ekit-wc-product-price wae-product-price"><?php woocommerce_template_single_price(); ?></div>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php
	}

	public function render_header() {
		$settings = $this->get_settings();

		$id = 'ekit-wc-carousel-' . $this->get_id();

		$this->add_render_attribute('wc-carousel', 'class', 'ekit-wc-products-wrapper woocommerce' . ' ' . (method_exists('\ElementsKit_Lite\Utils', 'swiper_class') ? esc_attr(\ElementsKit_Lite\Utils::swiper_class()) : 'swiper') );

		$this->add_render_attribute('wc-carousel-wrapper', 'class', 'products swiper-wrapper');

		$this->add_render_attribute( 'carousel', 'id', esc_attr($id) );
		$this->add_render_attribute( 'carousel', 'class', 'ekit-wc-products ekit-wc-carousel ekit-wc-carousel-desc-position-' . esc_attr($settings['ekit_product_description_position']) );

		if ('arrows' == $settings['ekit_navigation']) {
			$this->add_render_attribute( 'carousel', 'class', 'ekit-arrows-align-'. $settings['ekit_arrows_position'] );
		}

		if ('dots' == $settings['ekit_navigation']) {
			$this->add_render_attribute( 'carousel', 'class', 'ekit-dots-align-'. $settings['ekit_dots_position'] );
		}

		if ('both' == $settings['ekit_navigation']) {
			$this->add_render_attribute( 'carousel', 'class', 'ekit-arrows-dots-align-'. $settings['ekit_both_position'] );
		}

		?>
		<div <?php echo $this->get_render_attribute_string( 'carousel' ); ?>>
			<div <?php echo $this->get_render_attribute_string( 'wc-carousel' ); ?>
				data-autoplay="<?php echo esc_attr(($settings['ekit_autoplay'] == 'yes') ? '{ "delay": ' . $settings['ekit_autoplay_speed'] . ' }' : 'false'); ?>"
				data-loop="<?php echo esc_attr($settings['ekit_loop'] == 'yes' ? 'true' : 'false'); ?>"
				data-speed="<?php echo esc_attr($settings['ekit_speed']['size']*10); ?>"
				data-space-between="<?php echo esc_attr($settings['ekit_item_gap']['size']); ?>"
				data-responsive-settings='{"ekit_columns_desktop": "<?php echo $settings['ekit_columns_desktop'] ? esc_attr($settings['ekit_columns_desktop']) : 4;?>", "ekit_columns_tablet": "<?php echo isset($settings['ekit_columns_tablet']) ? esc_attr($settings['ekit_columns_tablet']) : 3 ;?>", "ekit_columns_mobile": "<?php echo isset($settings['ekit_columns_mobile']) ? esc_attr($settings['ekit_columns_mobile']) : 1;?>"}'
			>

				<ul <?php echo $this->get_render_attribute_string( 'wc-carousel-wrapper' ); ?>>
		<?php
	}

	public function render_footer() {
		$settings = $this->get_settings();
		$id       = 'ekit-wc-carousel-' . $this->get_id();

		?>
				</ul>
			</div>
			<?php
			$wp_query = $this->render_query();

			if($wp_query->have_posts()) {
			?>
			<?php if ('both' == $settings['ekit_navigation']) : ?>
				<?php $this->render_both_navigation(); ?>
				<?php if ('center' === $settings['ekit_both_position']) : ?>
					<div class="ekit-dots-container">
						<div class="ekit-swiper-pagination"></div>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<?php $this->render_pagination(); ?>
				<?php $this->render_navigation(); ?>
			<?php endif; } ?>
		</div>

		<?php
	}

	public function render_query() {
		$settings = $this->get_settings();

        $args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $settings['ekit_posts'],
			'orderby'             => $settings['ekit_orderby'],
			'order'               => $settings['ekit_order'],
        );

        if($settings['ekit_woo_product_select'] == 'category'){
            $arg_tax =[
                'tax_query'      => [
                    [
                    'taxonomy'   => 'product_cat',
                    'field'        => 'term_id',
                    'terms'         => $settings['ekit_woo_cat'],
                    ],
                ]
            ];

            $args = array_merge($args, $arg_tax);
		}

        if($settings['ekit_woo_product_select'] == 'product' && !empty($settings['ekit_woo_product'])){
            $arg_product = [
				'post__in' => $settings['ekit_woo_product'],
			];
			$args = array_merge($args, $arg_product);
		}

		$wp_query = new \WP_Query($args);

		return $wp_query;
	}

	public function render_loop_item() {
		$settings = $this->get_settings();
		global $post;

		$wp_query = $this->render_query();

		if($wp_query->have_posts()) {

			$this->add_render_attribute('wc-carousel-item', 'class',
			[
				'ekit-wc-product',
				'ekit-wc-carousel-item',
				'swiper-slide',
				'ekit-transition-toggle',
				'ekit-wc-product-alignment-' 	. esc_attr( $settings['ekit_product_alignment'] ),
				'ekit-desc-horizontal-align-' 	. esc_attr( $settings['ekit_alignment'] ),
				'ekit-desc-vertical-align-' 	. esc_attr( $settings['ekit_vertical_alignment'] ),
				'ekit-wc-flip-' 				. esc_attr( $settings['ekit_product_flip_content'] ),
				'ekit-wc-vertical-align-' 		. esc_attr($settings['ekit_alignment_vertical_alignment']),
			]);



			while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
		  		<li <?php echo $this->get_render_attribute_string( 'wc-carousel-item' ); ?>>
		  			<div class="ekit-wc-product-inner ekit-wc-carousel-item-inner">

					   <?php
						   $this->render_image();

						   if($settings['ekit_product_description_position'] === 'outside'){
								$this->render_description();
						   }
						?>

					</div>
				</li>
			<?php endwhile;
			wp_reset_postdata();

		} else {
			echo '<div class="attr-alert attr-alert-warning">'. esc_html__( 'Oppps!! There is no product', 'elementskit' ) .'</div>';
		}
	}

	protected function render_both_navigation() {
		$settings = $this->get_settings();
		?>

			<div class="ekit-arrows-dots-container ekit-slidenav-container ">

				<div class="ekit-flex ekit-flex-middle">
					<div class="ekit-nav ekit-nav-prev">
						<a href="" class="ekit-navigation-prev ekit-slidenav-previous ekit-icon ekit-slidenav">
						<?php
							// new icon
							$migrated = isset( $settings['__fa4_migrated']['ekit_arrows_prev_icons'] );
							// Check if its a new widget without previously selected icon using the old Icon control
							$is_new = empty( $settings['ekit_arrows_prev_icon'] );
							if ( $is_new || $migrated ) {
								// new icon
								Icons_Manager::render_icon( $settings['ekit_arrows_prev_icons'], [ 'aria-hidden' => 'true' ] );
							} else {
								?>
								<i class="<?php echo esc_attr($settings['ekit_arrows_prev_icon']); ?>" aria-hidden="true"></i>
								<?php
							}
						?>
						</a>
					</div>

					<?php if ('center' !== $settings['ekit_both_position']) : ?>
						<div class="ekit-swiper-pagination"></div>
					<?php endif; ?>

					<div class="ekit-nav ekit-nav-next">
						<a href="" class="ekit-navigation-next ekit-slidenav-next ekit-icon ekit-slidenav">
							<?php
								// new icon
								$migrated = isset( $settings['__fa4_migrated']['ekit_arrows_next_icons'] );
								// Check if its a new widget without previously selected icon using the old Icon control
								$is_new = empty( $settings['ekit_arrows_next_icon'] );
								if ( $is_new || $migrated ) {
									// new icon
									Icons_Manager::render_icon( $settings['ekit_arrows_next_icons'], [ 'aria-hidden' => 'true' ] );
								} else {
									?>
									<i class="<?php echo esc_attr($settings['ekit_arrows_next_icon']); ?>" aria-hidden="true"></i>
									<?php
								}
							?>
						</a>
					</div>

				</div>
			</div>

		<?php
	}

	protected function render_navigation() {
		$settings = $this->get_settings();
		?>

		<?php if ( 'arrows' == $settings['ekit_navigation'] ) :

		?>
		<div class="ekit-position-z-index ekit-visible@m ekit-position-<?php echo esc_attr($settings['ekit_arrows_position']); ?>">
			<div class="ekit-arrows-container ekit-slidenav-container">
				<div class="ekit-nav ekit-nav-prev">
					<a href="" class="ekit-navigation-prev ekit-slidenav-previous ekit-icon ekit-slidenav">
					<?php
							// new icon
							$migrated = isset( $settings['__fa4_migrated']['ekit_arrows_prev_icons'] );
							// Check if its a new widget without previously selected icon using the old Icon control
							$is_new = empty( $settings['ekit_arrows_prev_icon'] );
							if ( $is_new || $migrated ) {
								// new icon
								Icons_Manager::render_icon( $settings['ekit_arrows_prev_icons'], [ 'aria-hidden' => 'true' ] );
							} else {
								?>
								<i class="<?php echo esc_attr($settings['ekit_arrows_prev_icon']); ?>" aria-hidden="true"></i>
								<?php
							}
						?>
					</a>
				</div>

				<div class="ekit-nav ekit-nav-next">
					<a href="" class="ekit-navigation-next ekit-slidenav-next ekit-icon ekit-slidenav">
					<?php
							// new icon
							$migrated = isset( $settings['__fa4_migrated']['ekit_arrows_next_icons'] );
							// Check if its a new widget without previously selected icon using the old Icon control
							$is_new = empty( $settings['ekit_arrows_next_icon'] );
							if ( $is_new || $migrated ) {
								// new icon
								Icons_Manager::render_icon( $settings['ekit_arrows_next_icons'], [ 'aria-hidden' => 'true' ] );
							} else {
								?>
								<i class="<?php echo esc_attr($settings['ekit_arrows_next_icon']); ?>" aria-hidden="true"></i>
								<?php
							}
						?>
					</a>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<?php
	}

	protected function render_pagination() {
		$settings = $this->get_settings();
		?>

		<?php if ( 'dots' == $settings['ekit_navigation'] ) : ?>
			<?php if ( 'arrows' !== $settings['ekit_navigation'] ) : ?>
				<div class="ekit-position-z-index ekit-position-<?php echo esc_attr($settings['ekit_dots_position']); ?>">
					<div class="ekit-dots-container">
						<div class="ekit-swiper-pagination"></div>
					</div>
				</div>
			<?php endif; ?>

		<?php endif; ?>

		<?php
	}


    protected function render(){
        echo '<div class="ekit-wid-con" >';
			$this->render_header();
			$this->render_loop_item();
			$this->render_footer();
        echo '</div>';
    }

}
