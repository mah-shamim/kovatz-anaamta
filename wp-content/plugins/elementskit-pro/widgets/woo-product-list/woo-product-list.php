<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Woo_Product_List_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_Woo_Product_List extends Widget_Base {
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
        return 'https://wpmet.com/doc/woocommerce-product-list/';
    }

	protected function register_controls() {
		$this->start_controls_section(
			'ekit_section_content_query',
			[
				'label' => esc_html__( 'Filter', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_posts_per_page',
			[
				'label'   => esc_html__( 'Product Limit', 'elementskit' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 8,
			]
		);

		$this->add_control(
			'ekit_show_pagination',
			[
				'label' => esc_html__( 'Pagination', 'elementskit' ),
				'type'  => Controls_Manager::SWITCHER,
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
			'ekit_section_woocommerce_layout',
			[
				'label' => esc_html__( 'Layout', 'elementskit' ),
				'tab'	=> Controls_Manager::TAB_CONTENT
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

		$this->add_responsive_control(
			'ekit_columns',
			[
				'label'          => esc_html__( 'Columns', 'elementskit' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '2',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
                ],
                'default' => '4',
			]
		);

		$this->add_responsive_control(
			'ekit_item_gap',
			[
				'label'   => esc_html__( 'Column Gap', 'elementskit' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper.ekit-grid ul.products' => 'grid-column-gap:{{SIZE}}px !important;',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_row_gap',
			[
				'label'   => esc_html__( 'Row Gap', 'elementskit' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 200,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper.ekit-grid ul.products' => 'grid-row-gap:{{SIZE}}px !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'ekit_image',
				'label'     => esc_html__( 'Image Size', 'elementskit' ),
				'exclude'   => [ 'custom' ],
				'default'   => 'medium',
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
				'label'     => esc_html__( 'Show Badge', 'elementskit' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
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
			'ekit_show_categories',
			[
				'label'     => esc_html__( 'Categories', 'elementskit' ),
				'type'      => Controls_Manager::SWITCHER,
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
			'ekit_section_style_item',
			[
				'label'     => esc_html__( 'Item', 'elementskit' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'ekit_tabs_item_style' );

		$this->start_controls_tab(
			'ekti_tab_item_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekti_item_background',
			[
				'label'     => esc_html__( 'Background', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product .ekit-wc-product-inner' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekti_item_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ekit-wc-products .ekit-wc-product .ekit-wc-product-inner',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'ekti_item_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product .ekit-wc-product-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekti_item_shadow',
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-product .ekit-wc-product-inner',
			]
		);

		$this->add_responsive_control(
			'ekti_item_padding',
			[
				'label'      => esc_html__( 'Item Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product .ekit-wc-product-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_item_margin',
			[
				'label'      => esc_html__( 'Item Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product .ekit-wc-product-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekti_item__last_child_content',
			[
				'label' => esc_html__( 'Last Child', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'ekti_item_padding_last_child',
			[
				'label'      => esc_html__( 'Item Padding Last Child', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product:last-child .ekit-wc-product-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_item_margin_last_child',
			[
				'label'      => esc_html__( 'Item Margin Last Child', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product:last-child .ekit-wc-product-inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekti_item_border_last_child',
				'label'       => esc_html__( 'Item Border Last Child', 'elementskit' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ekit-wc-products .ekit-wc-product:last-child .ekit-wc-product-inner',
				]
			);

			$this->add_responsive_control(
				'ekti_desc_padding',
				[
				'label'      => esc_html__( 'Description Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'separator'   => 'before',
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekti_tab_item_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekti_item_hover_background',
			[
				'label'     => esc_html__( 'Background', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product .ekit-wc-product-inner:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekti_item_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'ekit_item_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product .ekit-wc-product-inner:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekti_item_hover_shadow',
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-product .ekit-wc-product-inner:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();



		$this->start_controls_section(
			'ekti_section_search_field_style',
			[
				'label' => esc_html__( 'Search Field', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_show_searching' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'ekti_tabs_search_field_style' );

		$this->start_controls_tab(
			'ekti_tab_search_field_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekti_search_field_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products input[type*="search"]' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekti_search_field_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products input[type*="search"]' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekti_search_field_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ekit-wc-products input[type*="search"], {{WRAPPER}} .ekit-wc-products select',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'ekti_search_field_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products input[type*="search"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_search_field_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products input[type*="search"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; height: auto;',
				],
			]
		);

		$this->add_control(
			'ekti_search_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .dataTables_filter label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_search_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'elementskit' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .dataTables_filter' => 'margin-bottom: {{SIZE}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'ekti_search_text_typography',
				'label'     => esc_html__( 'Text Typography', 'elementskit' ),
				'selector'  => '{{WRAPPER}} .ekit-wc-products .dataTables_filter label',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekti_tab_search_field_focus',
			[
				'label' => esc_html__( 'Focus', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekti_search_field_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'ekit_search_field_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products input[type*="search"]:focus' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ekti_section_select_field_style',
			[
				'label'     => esc_html__( 'Select Field', 'elementskit' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_show_pagination' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_select_field_style' );

		$this->start_controls_tab(
			'ekti_tab_select_field_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekti_select_field_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products select'   => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekti_select_field_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products select' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekti_select_field_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ekit-wc-products select',
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'ekti_select_field_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_select_field_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wc-products select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekti_select_text_color',
			[
				'label'     => esc_html__( 'Border Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'ekit_select_field_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .dataTables_length label' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'ekti_select_text_typography',
				'label'     => esc_html__( 'Text Typography', 'elementskit' ),
				'selector'  => '{{WRAPPER}} .ekit-wc-products .dataTables_length label',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekti_tab_select_field_focus',
			[
				'label' => esc_html__( 'Focus', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekti_select_field_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'ekit_select_field_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products select:focus'   => 'border-color: {{VALUE}};',
				],
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

		$this->add_responsive_control(
			'ekit_vertical_alignment',
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
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-product:hover .ekit-wc-product-title' => 'color: {{VALUE}} !important;',
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
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-product-title',
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

		$this->add_responsive_control(
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

		$this->add_responsive_control(
			'ekti_active_rating_color',
			[
				'label'     => esc_html__( 'Active Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFCC00',
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-products .star-rating span' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-desc .ekit-wc-rating .star-rating:before, {{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-desc .star-rating span:before' => 'font-size: {{SIZE}}{{UNIT}};',
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

		$this->add_responsive_control(
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
					'{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .add_to_cart_button' => 'width: 100%;display: block !important',
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
					'top'		=> 10,
					'right'		=> 10,
					'bottom'	=> 10,
					'left'		=> 10
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
					'{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-image .ekit-wc-add-to-cart .add_to_cart_button:hover:before' => 'color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-image .ekit-wc-add-to-cart .add_to_cart_button:hover:before',
				'condition' => [
                    'ekit_tab_cart_icon_switch' => 'yes'
                ]
            )
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// view cart button
		$this->start_controls_section(
			'ekit_product_list_button_section',
			[
				'label' => esc_html__( 'View Cart Button', 'elementskit' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
        );

        $this->add_responsive_control(
            'ekit_product_list_button_padding',
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
            'ekit_product_list_button_margin',
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
				'name'     => 'ekit_product_list_button_typo',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart',
			]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_product_list_button_txt_shadow',
                'label' => esc_html__( 'Text Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart',
            ]
        );

        $this->add_responsive_control(
			'ekit_product_list_button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'	=> [
					'top'	=> 100,
					'right'	=> 100,
					'bottom'	=> 100,
					'left'	=> 100
				],
				'selectors'  => [
					'{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_product_list_button_shadow',
				'selector' => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart',
			]
		);

        $this->start_controls_tabs('ekit_product_list_button_color_tabs');
            $this->start_controls_tab(
                'ekit_product_list_button_color_normal_tab',
                [
                    'label' => esc_html__('Normal', 'elementskit')
                ]
            );

            $this->add_control(
                'ekit_product_list_button_normal_color',
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
                    'name'     => 'ekit_product_list_button_normal_bg_color',
                    'selector' => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart',
                )
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'      => 'ekit_product_list_button_border',
                    'label'     => esc_html__( 'Border', 'elementskit' ),
                    'selector'  => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart',
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'ekit_product_list_button_color_hover_tab',
                [
                    'label' => esc_html__('Hover', 'elementskit')
                ]
            );

            $this->add_control(
                'ekit_product_list_button_hover_color',
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
                    'name'     => 'ekit_product_list_button_hover_bg_color',
                    'selector' => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart:hover',
                )
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name'      => 'ekit_product_list_hover_button_border',
                    'label'     => esc_html__( 'Border', 'elementskit' ),
                    'selector'  => '{{WRAPPER}} .ekit-wid-con .woocommerce ul.products .ekit-wc-add-to-cart .added_to_cart:hover',
                ]
            );

            $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
		// end view cart button

		$this->start_controls_section(
			'ekti_section_style_badge',
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
				'selector'    => '{{WRAPPER}} .ekit-wc-products:hover .ekit-wc-product .onsale',
				'separator'   => 'before',
			]
		);


		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section(
			'ekti_section_style_pagination',
			[
				'label'     => esc_html__( 'Pagination', 'elementskit' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_show_pagination' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_pagination_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'elementskit' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} ul.ekit-pagination'    => 'margin-top: {{SIZE}}px;',
					'{{WRAPPER}} .dataTables_paginate' => 'margin-top: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'ekti_pagination_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.ekit-pagination li a'    => 'color: {{VALUE}};',
					'{{WRAPPER}} ul.ekit-pagination li span' => 'color: {{VALUE}};',
					'{{WRAPPER}} .paginate_button'          => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'ekti_active_pagination_color',
			[
				'label'     => esc_html__( 'Active Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ul.ekit-pagination li.ekit-active a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .paginate_button.current'          => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_pagination_margin',
			[
				'label'     => esc_html__( 'Margin', 'elementskit' ),
				'type'      => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} ul.ekit-pagination li a'    => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
					'{{WRAPPER}} ul.ekit-pagination li span' => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
					'{{WRAPPER}} .paginate_button'          => 'margin: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_pagination_arrow_size',
			[
				'label'     => esc_html__( 'Arrow Size', 'elementskit' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} ul.ekit-pagination li a svg' => 'height: {{SIZE}}px; width: auto;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekti_pagination_typography',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} ul.ekit-pagination li a, {{WRAPPER}} ul.ekit-pagination li span, {{WRAPPER}} .dataTables_paginate',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekti_section_style_info',
			[
				'label'     => esc_html__( 'Info', 'elementskit' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_show_info' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'ekti_info_spacing',
			[
				'label'     => esc_html__( 'Spacing', 'elementskit' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .dataTables_info' => 'margin-top: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'ekti_info_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .dataTables_info' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekti_info_typography',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .dataTables_info',
			]
		);

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
					'{{WRAPPER}} .ekit-wc-product-categories ul li a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekti_categories_hover_color',
			[
				'label'     => esc_html__( 'Hover Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wc-product:hover .ekit-wc-product-categories ul li a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekti_categories_typography',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wc-products .ekit-wc-products-wrapper ul.products .ekit-wc-product .ekit-wc-product-categories ul li a',
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
				]
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
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color'  => [
						'default' => '#ffffff',
					],
				],
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

		$this->insert_pro_message();
	}

    protected function render(){
		echo '<div class="ekit-wid-con" >';
			$this->render_header();
			$this->render_loop_item();
			$this->render_footer();
        echo '</div>';
    }

    public function render_image() {
		$settings = $this->get_settings();
		global $product;

		?>
		<div class="ekit-wc-product-image ekit-background-cover">

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
					<a class="ekit-wc-product-popop--link" href="<?php echo get_the_post_thumbnail_url( get_the_ID(), 'full' ); ?>"><i class="fa fa-eye" aria-hidden="true"></i></a>
				</div>
			<?php endif; ?>

			<!-- badge content -->
			<?php if ('yes' == $settings['ekit_show_badge']) :
				$horizontal_align = !empty($settings['ekti_badge_alignment']) ? ' badge-' . esc_attr( $settings['ekti_badge_alignment'] ) : '';
				$vertical_align = !empty($settings['ekti_badge_vertical_alignment']) ? ' badge-vertical-' . esc_attr( $settings['ekti_badge_vertical_alignment'] ) : '';
			?>
				<div class="ekit-wc-products-badge <?php echo esc_attr( $horizontal_align ); ?> <?php echo esc_attr( $vertical_align ); ?>">
					<?php
						if ( function_exists( 'woocommerce_show_product_loop_sale_flash' ) ):
							woocommerce_show_product_loop_sale_flash();
						endif;
					?>
				</div>
			<?php endif; ?>

			<!-- Thumb content -->
			<a class="ekit_woo_product_img_link" href="<?php the_permalink(); ?>">
				<img src="<?php echo wp_get_attachment_image_url(get_post_thumbnail_id(), $settings['ekit_image_size']); ?>" alt="Product Thumb">
			</a>

			<!-- Add to cart button -->
			<?php if ('yes' == $settings['ekit_show_cart']) :
				// new icon
				$migrated = isset( $settings['__fa4_migrated']['ekit_tab_cart_icons'] );
				// Check if its a new widget without previously selected icon using the old Icon control
				$is_new = empty( $settings['ekit_tab_cart_icon'] );

				$iconCls = $settings['ekit_tab_cart_icon_switch'] == 'yes' ? ($is_new || $migrated) ? $settings['ekit_tab_cart_icons']['library'] !== 'svg' ? esc_attr( $settings['ekit_tab_cart_icons']['value'] ) : '' :  esc_attr( $settings['ekit_tab_cart_icon'] ) : '';

				$iconPos  = !empty($settings['ekit_tab_cart_icon_position']) ? 'ekit-cart-icon-pos-' . esc_attr( $settings['ekit_tab_cart_icon_position'] ) : '';

			?>
				<div class="ekit-wc-add-to-cart ekit-cart-align-<?php echo esc_attr($settings['ekit_button_alignment']) ?> ekit-cart-vertical-align-<?php echo esc_attr($settings['ekit_button_vertical_alignment']) ?>">
					<?php
						$isCart = false;
						if($product->is_purchasable() && $product->is_type('simple') == true) { $isCart = true; }
						$add_cart_btn = '<a data-product_id="'. $product->get_id() .'" href="'. $product->add_to_cart_url() .'" class="' . $iconPos . ' ' . $iconCls . ' button product_type_simple add_to_cart_button '. ($isCart == true ? "ajax_add_to_cart" : '') .'">'. $product->add_to_cart_text() .'</a>';
						echo str_replace($product->add_to_cart_text(), '<span class="ekit-woo-add-cart-text">'. $product->add_to_cart_text() .'</span>', $add_cart_btn);
					?>

				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	public function render_description() {
		$settings = $this->get_settings();
		global $product;

		?>
			<div class="ekit-wc-product-desc">
				<div class="ekit-wc-product-desc-inner">
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
						<a href="<?php the_permalink(); ?>" class="ekit-link-reset">
							<h2 class="ekit-wc-product-title">
								<?php the_title(); ?>
							</h2>
						</a>
					<?php endif; ?>

					<?php if ('yes' == $settings['ekit_show_rating']) : ?>
							<div class="ekit-wc-rating">

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

					<?php if (('yes' == $settings['ekit_show_price']) or ('yes' == $settings['ekit_show_rating'])) : ?>
						<?php if ( 'yes' == $settings['ekit_show_price']) : ?>
							<div class="ekit-wc-product-price">
								<?php woocommerce_template_single_price(); ?>
							</div>
						<?php endif; ?>


					<?php endif; ?>
				</div>
			</div>
		<?php
	}

	public function render_header($skin="default") {
		$settings = $this->get_settings();
		$this->add_render_attribute('ekit-wc-products', 'class', [
			'ekit-wc-products',
			'ekit-wc-products-skin-' . $skin,
			'ekit-wc-carousel-desc-position-' . esc_attr($settings['ekit_product_description_position'])
		]);
		?>
		<div <?php echo $this->get_render_attribute_string( 'ekit-wc-products' ); ?>>
		<?php
	}

	public function render_footer() {
		?>
		</div>
		<?php
	}

	public function render_query() {
		$settings = $this->get_settings();

		if ( get_query_var('paged') ) { $paged = get_query_var('paged'); }
		elseif ( get_query_var('page') ) { $paged = get_query_var('page'); }
		else { $paged = 1; }

		$args = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $settings['ekit_posts_per_page'],
			'orderby'             => $settings['ekit_orderby'],
			'order'               => $settings['ekit_order'],
			'paged'               => $paged,
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

		if($wp_query->have_posts() && post_type_exists('product')) {
			$ekit_col_desktop = $settings['ekit_columns'];
			$ekit_cols_tablet = isset($settings['ekit_columns_tablet']) ? $settings['ekit_columns_tablet'] : '2';
			$ekit_cols_mobile = isset($settings['ekit_columns_mobile']) ? $settings['ekit_columns_mobile'] : '1';

			$woo_varaibles = "
				--ekit-woo-desktop-columns: $ekit_col_desktop;
				--ekit-woo-tablet-columns: $ekit_cols_tablet;
				--ekit-woo-mobile-columns: $ekit_cols_mobile;
			";

			$this->add_render_attribute(
				[
					'ekit-wc-products-wrapper' => [
						'class' => [
							'ekit-wc-products-wrapper',
							'ekit-grid',
							'ekit-grid-medium',
							'woocommerce',
						],

						'style' => trim(preg_replace('/\s+/', ' ', $woo_varaibles))
					],
				]
			);

			?>
			<div <?php echo $this->get_render_attribute_string( 'ekit-wc-products-wrapper' ); ?>>
			<?php

			$this->add_render_attribute('ekit-wc-product', 'class', [
				'ekit-wc-product',
				]); ?>
				<ul class="products ekit-woo-product-list-widget">
					<?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
						<li class="ekit-wc-product product ekit-wc-product-alignment-<?php echo esc_attr( $settings['ekit_product_alignment'] ); ?> ekit-desc-horizontal-align-<?php echo esc_attr( $settings['ekit_alignment'] ); ?> ekit-desc-vertical-align-<?php echo esc_attr( $settings['ekit_vertical_alignment'] ); ?> ekit-wc-flip-<?php echo esc_attr( $settings['ekit_product_flip_content'] ); ?> ekit-wc-vertical-align-<?php echo esc_attr($settings['ekit_alignment_vertical_alignment']); ?>">
							<div class="ekit-wc-product-inner">

							<?php
								$this->render_image();
								if($settings['ekit_product_description_position'] === 'outside'){
									$this->render_description();
							   	}
							?>


							</div>

						</li>
					<?php endwhile;	?>
				</ul>
			</div>
			<?php

			wp_reset_postdata();

		} else {
			echo '<div class="attr-alert-warning attr-alert">' . esc_html__( 'Oops! No products were found.', 'elementskit' ) .'</div>';
		}
	}
}
