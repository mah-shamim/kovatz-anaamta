<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Woo_Category_List_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Woo_Category_List extends Widget_Base {
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
        return 'https://wpmet.com/doc/woocommerce-category-list/';
    }

    protected function register_controls() {
		
		$this->start_controls_section(
			'ekit_section_filter',
			[
				'label' => esc_html__( 'Filter', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ekit_source',
			[
				'label' => esc_html__( 'Filter by', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'by_id',
				'options' => [
					''  => esc_html__( 'All', 'elementskit' ),
					'by_id'  => esc_html__( 'Manual Selection', 'elementskit' ),
					'by_parent' => esc_html__( 'By Parent', 'elementskit' ),
				],
			]
		);

		$this->add_control(
			'ekit_categories',
			[
                'label'   => esc_html__( 'Categories', 'elementskit' ),
				'type'    => ElementsKit_Controls_Manager::AJAXSELECT2,
                'options' => 'ajaxselect2/product_cat',
                'label_block' => true,
                'multiple'  => true,
                'condition' => [
                    'ekit_source' => 'by_id',
                ],
			]
		);

		$this->add_control(
			'ekit_parent',
			[
                'label'   => esc_html__( 'Parent', 'elementskit' ),
                'type'    => ElementsKit_Controls_Manager::AJAXSELECT2,
                'default'   => '0',
                'options' => 'ajaxselect2/product_cat',
                'label_block' => true,
                'multiple'  => false,
                'condition' => [
                    'ekit_source' => 'by_parent',
                ],                
			]
		);

		$this->add_control(
			'ekit_orderby',
			[
				'label'   => esc_html__( 'Order by', 'elementskit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'name',
				'options' => [
					'default'     => esc_html__( 'Default', 'elementskit' ),
					'name'        => esc_html__( 'Name', 'elementskit' ),
					'slug'        => esc_html__( 'Slug', 'elementskit' ),
					'description' => esc_html__( 'Description', 'elementskit' ),
					'count'       => esc_html__( 'Count', 'elementskit' ),
				],
			]
		);

		$this->add_control(
			'ekit_order',
			[
				'label'   => esc_html__( 'Order', 'elementskit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => [
					'asc'  => esc_html__( 'ASC', 'elementskit' ),
					'desc' => esc_html__( 'DESC', 'elementskit' ),
				],
			]
		);

		$this->add_control(
			'ekit_wcl_hide_uncat_cat',
			[
				'label'        => esc_html__('Remove uncategorized category', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('Show', 'elementskit'),
				'label_off'    => esc_html__('Hide', 'elementskit'),
				'return_value' => 'yes',
				'default'      => '',
				'condition' => [
					'ekit_source' => '',
				],
			]
		);


		$this->add_control(
			'hide_empty_cat',
			[
				'label'   => esc_html__( 'Hide Empty Category', 'elementskit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_section_woocommerce_layout',
			[
				'label' => esc_html__( 'Layout', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'ekit_product_description_position',
			[
				'label'   => esc_html__( 'Label Postion', 'elementskit' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'inside',
				'options' => [
                    'inside' 	=> esc_html__('Inside Thumb', 'elementskit'),
                    'outside' 	=> esc_html__('Outside Thumb', 'elementskit'),
				],
			]
		);

		$this->add_control(
            'ekit_featured_cat',
            [
                'label' => esc_html__('Enable featured category?', 'elementskit'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' =>esc_html__( 'Yes', 'elementskit' ),
                'label_off' =>esc_html__( 'No', 'elementskit' ),
            ]
		);

		$this->add_control(
			'ekit_featured_cat_link',
			[
				'label' =>esc_html__( 'URL', 'elementskit' ),
				'type' => Controls_Manager::URL,
				'placeholder' =>esc_url('https://wpmet.com'),
				'dynamic' => [
                    'active' => true,
				],
				'condition' => [
                    'ekit_featured_cat' => 'yes'
                ],
			]
		);
		
		$this->add_control(
            'ekit_featured_cat_image',
            [
                'label' => esc_html__( 'Choose Image', 'elementskit' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
					'id'    => -1
                ],
                'condition' => [
                    'ekit_featured_cat' => 'yes'
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
		);

		$this->add_responsive_control(
			'ekit_columns',
			[
				'label'   => esc_html__( 'Columns', 'elementskit' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'default' => '3',
			]
		);


		$this->add_responsive_control(
			'ekit_item_gap',
			[
				'label'   => esc_html__( 'Item Gap', 'elementskit' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 6,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					// '{{WRAPPER}} ul.products'                            => 'margin: -{{SIZE}}px -{{SIZE}}px 0',
					// '(desktop){{WRAPPER}} .products li.product-category' => 'width: calc( 100% / {{columns.SIZE}} ); border: {{SIZE}}px solid transparent',
					// '(tablet){{WRAPPER}} .products li.product-category'  => 'width: calc( 100% / 2 ); border: {{SIZE}}px solid transparent',
					// '(mobile){{WRAPPER}} .products li.product-category'  => 'width: calc( 100% / 1 ); border: {{SIZE}}px solid transparent',
					'{{WRAPPER}} .ekit-woo-category-list-container .woocommerce ul.products'        => 'grid-gap: {{SIZE}}px !important',
					'{{WRAPPER}} .ekit-woo-category-list-container.ekit-woo-featured-cat-container' => 'column-gap: {{SIZE}}px !important',
				],
				'frontend_available' => true,
			]
		);


		$this->add_control(
			'ekit_number',
			[
				'label'   => esc_html__( 'Categories Count', 'elementskit' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => '4',
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
			'ekit_show_product_count',
			[
				'label'   => esc_html__( 'Product count', 'elementskit' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
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

		$this->start_controls_tabs( 'tabs_item_style' );

		$this->start_controls_tab(
			'ekit_tab_item_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_item_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .product-category a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_item_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce .product-category a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'ekit_item_border',
				'label'     => esc_html__( 'Item Border', 'elementskit' ),
				'selector'  => '{{WRAPPER}} .woocommerce .product-category a',
			]
		);

		$this->add_responsive_control(
			'ekit_item_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce .product-category a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_item_box_shadow',
				'selector' => '{{WRAPPER}} .woocommerce .product-category a',
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
				'label'     => esc_html__( 'Background Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .product-category a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'ekit_item_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .woocommerce .product-category a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_item_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce .product-category a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ekit_item_hover_shadow',
				'selector' => '{{WRAPPER}} .woocommerce .product-category a:hover',
			]
		);

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_section_style_image',
			[
				'label' => esc_html__( 'Image', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_use_category_image_height_width',
			[
				'label' => esc_html__( 'Use Height Width', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'no',
			]
		);

		$this->add_responsive_control(
			'ekit_woo_cat_image_height',
			[
				'label' => esc_html__( 'Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce .product-category a img' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_use_category_image_height_width' => 'yes'
				]
			]
		);
		
		$this->add_responsive_control(
			'ekit_woo_cat_image_width',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce .product-category a img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_use_category_image_height_width' => 'yes'
				]
			]
		);
		
		$this->start_controls_tabs( 'ekit_tabs_image_style' );

		$this->start_controls_tab(
			'ekit_tab_image_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_image_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .woocommerce .product-category a img',
			]
		);

		$this->add_responsive_control(
			'ekit_image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce .product-category a img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_tab_image_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_image_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'ekit_image_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .woocommerce .product-category a:hover img' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_image_hover_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce .product-category a:hover img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'ekit_section_style_title',
			[
				'label' => esc_html__( 'Label', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_section_label_height',
			[
				'label' => esc_html__( 'Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' 	=> 'px',
					'size'	=> 90
				],
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
					'{{WRAPPER}} .woocommerce .product-category .woocommerce-loop-category__title' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_title_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce .product-category .woocommerce-loop-category__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_title_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .woocommerce .product-category .woocommerce-loop-category__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_title_align',
			[
				'label'   => esc_html__( 'Horizontal Alignment', 'elementskit' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'    => [
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
					// '{{WRAPPER}} .woocommerce .product-category .woocommerce-loop-category__title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_title_vertical_align',
			[
				'label' => esc_html__( 'Vertical Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
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
				'default' => 'center',
				'toggle' => true,
				'condition'	=> [
					'ekit_product_description_position'	=> 'inside'
				]
			]
		);

		$this->add_control(
            'ekit_section_style_cat_title',
            [
                'label' => esc_html__( 'Category Title:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_title_typography',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .woocommerce .product-category .woocommerce-loop-category__title',
			]
		);

		$this->start_controls_tabs( 'ekit_tabs_title_style' );

		$this->start_controls_tab(
			'ekit_tab_title_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_title_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .woocommerce .product-category .woocommerce-loop-category__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'		=> 'ekit_cat_title_bg_color',
				'selector'	=> '{{WRAPPER}} .woocommerce .product-category .woocommerce-loop-category__title'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_tab_title_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_hover_title_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#333',
				'selectors' => [
					'{{WRAPPER}} .woocommerce .product-category a:hover .woocommerce-loop-category__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'		=> 'ekit_cat_hover_title_bg_color',
				'default' => 'rgba(0, 0, 0, 0.5)',
				'selector'	=> '{{WRAPPER}} .woocommerce .product-category a:hover .woocommerce-loop-category__title'
			]
		);

		$this->end_controls_tab();
		
		$this->end_controls_tabs();

		// product count
		$this->add_control(
            'ekit_section_style_product_count',
            [
                'label' => esc_html__( 'Product Count:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_section_product_count_typography',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .woocommerce .product-category .woocommerce-loop-category__title .count',
			]
		);

		$this->start_controls_tabs('ekit_section_product_count_tabs');
			$this->start_controls_tab(
				'ekit_section_product_count_tab_normal',
				[
					'label'	=> esc_html__('Normal', 'elementskit')
				]
			);

			$this->add_control(
				'ekit_section_product_count_tab_normal_color',
				[
					'label'     => esc_html__( 'Color', 'elementskit' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .woocommerce .product-category .woocommerce-loop-category__title .count' => 'color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'ekit_section_product_count_tab_hover',
				[
					'label'	=> esc_html__('Hover', 'elementskit')
				]
			);

			$this->add_control(
				'ekit_section_product_count_tab_hover_color',
				[
					'label'     => esc_html__( 'Color', 'elementskit' ),
					'type'      => Controls_Manager::COLOR,
					'default'	=> '#333',
					'selectors' => [
						'{{WRAPPER}} .woocommerce .product-category a:hover .woocommerce-loop-category__title .count' => 'color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		// Featured Cat
		$this->start_controls_section(
			'ekit_section_featured_cat',
			[
				'label' => esc_html__( 'Featured Category', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'ekit_featured_cat' => 'yes'
				],
			]
		);

		$this->add_responsive_control(
			'ekit_section_featured_cat_width',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' 	=> '%',
					'size'	=> 50
				],
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
					'{{WRAPPER}} .ekit-woo-category-list-container.ekit-woo-featured-cat-container .ekit-woo-featured-cat' => 'flex: 0 0 {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
            'ekit_section_style_featured_cat_label',
            [
                'label' => esc_html__( 'Label:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'ekit_featured_cat_label_height',
			[
				'label' => esc_html__( 'Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'unit' 	=> 'px',
					'size'	=> 90
				],
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
					'{{WRAPPER}} .ekit-woo-featured-cat-container .ekit-woo-featured-cat .woocommerce .product-category .woocommerce-loop-category__title' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_featured_cat_title_margin',
			[
				'label'      => esc_html__( 'Margin', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-woo-featured-cat-container .ekit-woo-featured-cat .woocommerce .product-category .woocommerce-loop-category__title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_featured_cat_title_padding',
			[
				'label'      => esc_html__( 'Padding', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-woo-featured-cat-container .ekit-woo-featured-cat .woocommerce .product-category .woocommerce-loop-category__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_featured_cat_title_align',
			[
				'label'   => esc_html__( 'Horizontal Alignment', 'elementskit' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left'    => [
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
			'ekit_featured_cat_title_vertical_align',
			[
				'label' => esc_html__( 'Vertical Alignment', 'elementskit' ),
				'type' => Controls_Manager::CHOOSE,
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
				'default' => 'center',
				'toggle' => true,
			]
		);

		$this->add_control(
            'ekit_section_style_featured_cat_title',
            [
                'label' => esc_html__( 'Category Title:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_featured_cat_title_typography',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-woo-featured-cat-container .ekit-woo-featured-cat .woocommerce .product-category .woocommerce-loop-category__title',
			]
		);

		$this->start_controls_tabs( 'ekit_featured_cat_tabs_title_style' );

		$this->start_controls_tab(
			'ekit_tab_featured_cat_title_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_featured_cat_title_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'default'	=> '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ekit-woo-featured-cat-container .ekit-woo-featured-cat .woocommerce .product-category .woocommerce-loop-category__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'		=> 'ekit_featured_cat_title_bg_color',
				'selector'	=> '{{WRAPPER}} .ekit-woo-featured-cat-container .ekit-woo-featured-cat .woocommerce .product-category .woocommerce-loop-category__title'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_tab_featured_cat_title_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_featured_cat_hover_title_color',
			[
				'label'     => esc_html__( 'Color', 'elementskit' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-woo-featured-cat-container .ekit-woo-featured-cat:hover .woocommerce .product-category .woocommerce-loop-category__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'		=> 'ekit_featured_cat_hover_title_bg_color',
				'default' => 'rgba(0, 0, 0, 0.5)',
				'selector'	=> '{{WRAPPER}} .ekit-woo-featured-cat-container .ekit-woo-featured-cat:hover .woocommerce .product-category .woocommerce-loop-category__title'
			]
		);

		$this->end_controls_tab();
		
		$this->end_controls_tabs();

		// product count
		$this->add_control(
            'ekit_section_style_featured_cat_product_count',
            [
                'label' => esc_html__( 'Product Count:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'ekit_featured_cat_product_count_typography',
				'label'    => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-woo-featured-cat-container .ekit-woo-featured-cat .woocommerce .product-category .woocommerce-loop-category__title .count',
			]
		);

		$this->start_controls_tabs('ekit_featured_cat_product_count_tabs');
			$this->start_controls_tab(
				'ekit_featured_cat_product_count_tab_normal',
				[
					'label'	=> esc_html__('Normal', 'elementskit')
				]
			);

			$this->add_control(
				'ekit_featured_cat_product_count_tab_normal_color',
				[
					'label'     => esc_html__( 'Color', 'elementskit' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ekit-woo-featured-cat-container .ekit-woo-featured-cat .woocommerce .product-category .woocommerce-loop-category__title .count' => 'color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'ekit_featured_cat_product_count_tab_hover',
				[
					'label'	=> esc_html__('Hover', 'elementskit')
				]
			);

			$this->add_control(
				'ekit_featured_cat_product_count_tab_hover_color',
				[
					'label'     => esc_html__( 'Color', 'elementskit' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ekit-woo-featured-cat-container .ekit-woo-featured-cat .woocommerce .product-category a:hover .woocommerce-loop-category__title .count' => 'color: {{VALUE}};',
					],
				]
			);

			$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->insert_pro_message();
	}

	public function render() {
		echo '<div class="ekit-wid-con" >';
			$this->render_raw();
        echo '</div>';
	}
	
	public function get_cat_info(){
		$settings = $this->get_settings();

		

		// featured cat settings
		$is_featured = $settings['ekit_featured_cat'];
		$featured_image = $settings['ekit_featured_cat_image'];
		if ( ! empty( $settings['ekit_featured_cat_link']['url'] ) ) {
			$this->add_link_attributes( 'feature_link', $settings['ekit_featured_cat_link'] );
		} elseif ( class_exists( 'WooCommerce' ) && empty($settings['ekit_featured_cat_link']['url']) ){
			$this->add_link_attributes( 'feature_link', ['url'=> get_permalink( wc_get_page_id( 'shop' ) )] );
		}

		// get prodcut count for all category
		$posts = class_exists( 'WooCommerce' ) ? wp_count_posts( 'product' ) : '';
		$count_post = !empty($posts) ? $posts->publish : 0;

		// Columns
		$ekit_col_desktop = $settings['ekit_columns'];
		$columns_count_tab = isset( $settings['ekit_columns_tablet'] ) ? $settings['ekit_columns_tablet'] : '';
		$columns_count_mobile = isset( $settings['ekit_columns_mobile'] ) ? $settings['ekit_columns_mobile'] : '';

		$woo_varaibles = "
				--ekit-woo-desktop-columns: $ekit_col_desktop;
				--ekit-woo-tablet-columns: $columns_count_tab;
				--ekit-woo-mobile-columns: $columns_count_mobile;
			";
		
		$this->add_render_attribute(
			[
				'ekit-cat-list-columns' => [
					'class' => [
						'woocommerce',
						'ekit-cat-items',
					],

					'style' => trim(preg_replace('/\s+/', ' ', $woo_varaibles))
				],
			]
		);


		$exc_cat = '';

		if(!empty($settings['ekit_wcl_hide_uncat_cat'])) {

			$uncat = get_term_by( 'slug', 'uncategorized', 'product_cat' );

			if(!empty($uncat)) {

				$exc_cat = $uncat->term_id;
			}
		}

		$args = [
			'limit'      => '-1', // -1 for all, no limit
            'orderby'    => $settings['ekit_orderby'],
            'order'      => $settings['ekit_order'],
            'hide_empty' => $settings['hide_empty_cat'] == 'yes',
            'taxonomy'   => 'product_cat',
			'pad_counts' => true,
			'exclude' => $exc_cat,
        ];


		if($settings['ekit_orderby'] == 'default') {
			$args['meta_key'] = 'order';
			$args['orderby'] = 'meta_value_num';
		}


        if('by_id' == $settings['ekit_source']){
            $args['include'] = $settings['ekit_categories'];
        }elseif ('by_parent' == $settings['ekit_source']) {
			$args['parent'] = $settings['ekit_parent'];
		}else{
			$args['include'] = '';
		}

		$all_categories = get_categories( $args );
		
		// Featured cat content
		if($is_featured == 'yes') :	
			?>
				<a <?php echo $this->get_render_attribute_string( 'feature_link' ); ?> class="ekit-woo-featured-cat" style="background-image: url(<?php echo esc_url($featured_image && $featured_image['url'] ?  $featured_image['url']  : ''); ?>)">
					<div class="woocommerce">
						<ul class="products ekit-woo-cat-list--products">
							<li class="product-category">
								<h2 class="woocommerce-loop-category__title">
									<?php esc_html_e('All Categories', 'elementskit'); ?>

									<?php if ( $settings['ekit_show_product_count'] ): ?>
									<mark class="count">
										<?php echo esc_html( $count_post ) .' '. esc_html__('Products', 'elementskit'); ?>
									</mark>
									<?php endif; ?>
								</h2>
							</li>
						</ul>
					</div>
				</a>
			<?php
		endif;
		// End Featured cat content

		echo '<div '. $this->get_render_attribute_string( 'ekit-cat-list-columns' ) .'>';
			echo '<ul class="products ekit-woo-cat-list--products">';
			foreach ($all_categories as $cat) {
				$thumbnail_id   = get_term_meta( $cat->term_id, 'thumbnail_id', true );
				$raw_image = wp_get_attachment_url( $thumbnail_id );
				$demo_image = plugin_dir_url( __FILE__ ).'assets/image/woocommerce-placeholder-300x300.png';
				$image = ($raw_image != false) ? $raw_image : $demo_image;

				$product_count = $settings['ekit_show_product_count'] === 'yes' ? '<mark class="count">'. esc_html( $cat->count ) .' '. esc_html__( 'Products', 'elementskit' ).'</mark>' : '';

				$cat_name = $settings['ekit_show_title'] === 'yes' ? esc_html($cat->name) : '';
				$product_title = $settings['ekit_show_title'] === 'yes' && $settings['ekit_show_title'] === 'yes' ? '<span>'. $cat_name .'</span>' : '';

				$output = '<li class="product-category product"><a href="'. get_term_link($cat->slug, 'product_cat') .'"><img src="'. esc_url( $image ) .'" alt="'. esc_attr( $cat->name ) .'"/><h2 class="woocommerce-loop-category__title">'. \ElementsKit_Lite\Utils::kses( $product_title ) . $product_count .'</h2></a>';

				echo \ElementsKit_Lite\Utils::render($output);
			}
			echo '</ul>';
		echo '</div>';

	}

	private function render_raw() {
		$settings = $this->get_settings();
		// featured cat settings
		$is_featured = $settings['ekit_featured_cat'];
		$featuredCls = $is_featured == 'yes' ? 'ekit-woo-featured-cat-container' : '';

		// Vertical Align
		$vertical_align_tab = isset( $settings['ekit_featured_cat_title_vertical_align_tablet'] ) ? $settings['ekit_featured_cat_title_vertical_align_tablet'] : '';
		$vertical_align_mobile = isset( $settings['ekit_featured_cat_title_vertical_align_mobile'] ) ? $settings['ekit_featured_cat_title_vertical_align_mobile'] : '';

		// Featured Title Align
		$featurend_title_align_tab = isset( $settings['ekit_featured_cat_title_align_tablet'] ) ? $settings['ekit_featured_cat_title_align_tablet'] : '';
		$featurend_title_align_mobile = isset( $settings['ekit_featured_cat_title_align_mobile'] ) ? $settings['ekit_featured_cat_title_align_mobile'] : '';

		// Title Align
		$title_align_tab = isset( $settings['ekit_title_align_tablet'] ) ? $settings['ekit_title_align_tablet'] : '';
		$title_align_mobile = isset( $settings['ekit_title_align_mobile'] ) ? $settings['ekit_title_align_mobile'] : '';

		// Title Vertial Align
		$title_valign_tab = isset( $settings['ekit_title_vertical_align_tablet'] ) ? $settings['ekit_title_vertical_align_tablet'] : '';
		$title_valign_mobile = isset( $settings['ekit_title_vertical_align_mobile'] ) ? $settings['ekit_title_vertical_align_mobile'] : '';

		$this->add_render_attribute(
			[
				'ekit-cat-list-alignment' => [
					'class' => [
						'ekit-woo-category-list-container',
						$featuredCls,
						// ekit-featured-cat-vertical-align
						'ekit-featured-cat-title-vertical-align-' . 		esc_attr( $settings['ekit_featured_cat_title_vertical_align'] ),
						'ekit-featured-cat-title-tablet-vertical-align-' . 	esc_attr( $vertical_align_tab ),
						'ekit-featured-cat-title-mobile-vertical-align-' . 	esc_attr( $vertical_align_mobile ),

						// ekit-featured-cat-horizontal-align
						'ekit-featured-cat-title-align-'. 			esc_attr( $settings['ekit_featured_cat_title_align'] ),
						'ekit-featured-cat-title-tablet-align-' . 	esc_attr( $featurend_title_align_tab ),
						'ekit-featured-cat-title-mobile-align-' . 	esc_attr( $featurend_title_align_mobile ),

						// ekit-featured-cat-horizontal-align
						'ekit-woo-category-list-align-'. 		esc_attr( $settings['ekit_title_align'] ),
						'ekit-woo-category-list-tablet-align-'. esc_attr( $title_align_tab ),
						'ekit-woo-category-list-mobile-align-'. esc_attr( $title_align_mobile ),

						// ekit-featured-cat-vertical-align
						'ekit-woo-category-list-vertical-align-'. 			esc_attr( $settings['ekit_title_vertical_align'] ),
						'ekit-woo-category-list-tablet-vertical-align-'. 	esc_attr( $title_valign_tab ),
						'ekit-woo-category-list-mobile-vertical-align-'. 	esc_attr( $title_valign_mobile ),

						// label position
						'ekit-wc-label-position-' . esc_attr($settings['ekit_product_description_position'])
					],
				],
			]
		);

		// end featured cat settings
		

		echo "<div " . $this->get_render_attribute_string( 'ekit-cat-list-alignment' ) . ">"; 
			$this->get_cat_info();
		echo "</div>";
	}

}
