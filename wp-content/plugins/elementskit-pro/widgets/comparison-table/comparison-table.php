<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_Comparison_Table_Handler as Handler;

defined('ABSPATH') || exit;

class ElementsKit_Widget_Comparison_Table extends Widget_Base {
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
        return 'https://wpmet.com/doc/comparison-table/';
    }

    protected function register_controls() {

        $this->start_controls_section(
			'ekit_cp_table_heading_content_section',
			[
				'label' => esc_html__('Table Heading', 'elementskit'),
			]
		);

        $repeater_body = new Repeater();

		$repeater_body->add_responsive_control(
			'ekit_cp_table_heading_cell_width',
			[
				'label'          => __( 'Cell Width', 'elementskit' ),
				'type'           => Controls_Manager::SLIDER,
				'frontend_available'    => true,
				'size_units'     => ['%', 'px'],
				'range'          => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min'  => 0,
						'max'  => 2000,
						'step' => 1,
					],
				],
				'default' => [
					'size' => 50,
					'unit' => '%',
				],
				'selectors'   => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

        $repeater_body->add_control(
            'ekit_cp_table_heading_cell_text',
            [
                'label' => esc_html__( 'Content', 'elementskit' ),
                'type' => Controls_Manager::WYSIWYG,
                'default' => esc_html__( 'Row' , 'elementskit' ),
                'show_label' => false,
            ]
        );

        $repeater_body->add_control(
            'ekit_cp_table_heading_cell_url_show',
            [
                'label' => esc_html__('Add a url? ', 'elementskit'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' =>esc_html__( 'Yes', 'elementskit' ),
                'label_off' =>esc_html__( 'No', 'elementskit' ),
            ]
        );

        $repeater_body->add_control(
			'ekit_cp_table_heading_cell_url',
			[
				'label' =>esc_html__( 'URL', 'elementskit' ),
				'type' => Controls_Manager::URL,
				'placeholder' =>esc_url('https://wpmet.com'),
                'condition' => [
                    'ekit_cp_table_heading_cell_url_show' => 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

        $repeater_body->add_control(
            'ekit_cp_table_heading_cell_icon_type',
            [
                'label' => esc_html__( 'Type', 'elementskit' ),
                'label_block' => false,
                'type'  => Controls_Manager::CHOOSE,
                'options'   => [
                    'none'        => [
                        'title'   => esc_html__( 'None', 'elementskit' ),
                        'icon'    => 'eicon-ban',
                    ],
                    'icon'        => [
                        'title'   => esc_html__( 'Icon', 'elementskit' ),
                        'icon'    => 'eicon-star',
                    ],
                    'image'       => [
                        'title'   => esc_html__( 'Image', 'elementskit' ),
                        'icon'    => 'eicon-image',
                    ],
                ],
                'default' => 'none'
            ]
        );

        $repeater_body->add_control(
            'ekit_cp_table_heading_cell_icons',
            [
                'label' => __( 'Icon', 'elementskit' ),
                'type'  => Controls_Manager::ICONS,
                'fa4compatibility' => 'body_cell_icon',
                'default' => [
                    'value' => '',
                ],
                'condition'  => [
                    'ekit_cp_table_heading_cell_icon_type'   => 'icon',
                ],
            ]
        );

        $repeater_body->add_control(
            'ekit_cp_table_heading_cell_image',
            [
                'label'   => esc_html__( 'Image', 'elementskit' ),
                'type'    => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                    'id'  => -1
                ],
                'condition'             => [
                    'ekit_cp_table_heading_cell_icon_type' => 'image',
                ],
				'dynamic' => [
					'active' => true,
				],
            ]
        );

        $repeater_body->add_control(
            'ekit_cp_table_heading_cell_icon_position',
            [
                'label'         => esc_html__( 'Position', 'elementskit' ),
                'type'          => Controls_Manager::SELECT,
                'default'       => 'before',
                'options'       => [
                    'before'    => esc_html__( 'Before', 'elementskit' ),
                    'after'     => esc_html__( 'After', 'elementskit' ),
                    'top'       => esc_html__( 'Top', 'elementskit' ),
                    'bottom'    => esc_html__( 'Bottom', 'elementskit' ),
                ],
            ]
        );

		$repeater_body->add_control(
			'ekit_cp_table_heading_image_width',
			[
				'label' => esc_html__( 'Image Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-row {{CURRENT_ITEM}}.ekit-comparison-table-heading-cell a > img' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-row {{CURRENT_ITEM}}.ekit-comparison-table-heading-cell li > img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_cp_table_heading_cell_icon_type' => 'image',
				],
			]
		);

		$repeater_body->add_control(
			'ekit_cp_table_heading_cell_btn',
			[
				'label'        => __( 'Button', 'elementskit' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			]
		);

		$repeater_body->add_control(
			'ekit_cp_table_heading_cell_btn_text',
			[
				'label' => esc_html__( 'Button Text', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Read More', 'elementskit' ),
				'placeholder' => esc_html__( 'Type your title here', 'elementskit' ),
				'condition'     => [
					'ekit_cp_table_heading_cell_btn' => 'yes',
				],
			]
		);

		$repeater_body->add_control(
			'ekit_cp_table_heading_cell_btn_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-row {{CURRENT_ITEM}} .ekit-comparison-table-heading-button' => 'color: {{VALUE}}',
				],
				'condition'     => [
					'ekit_cp_table_heading_cell_btn' => 'yes',
				],
			]
		);

		$repeater_body->add_control(
			'ekit_cp_table_heading_cell_btn_bg',
			[
				'label' => esc_html__( 'Background', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-row {{CURRENT_ITEM}} .ekit-comparison-table-heading-button' => 'background-color: {{VALUE}}',
				],
				'condition'     => [
					'ekit_cp_table_heading_cell_btn' => 'yes',
				],
			]
		);

		$repeater_body->add_control(
			'ekit_cp_table_heading_align',
			[
				'label'   => esc_html__( 'Alignment', 'elementskit' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'start'    => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon'  => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} a, {{WRAPPER}} {{CURRENT_ITEM}} li' => 'align-items: {{VALUE}}; justify-content: flex-{{VALUE}}; justify-content: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} .ekit-comparison-table-heading-btn' => 'justify-content: flex-{{VALUE}}; justify-content: {{VALUE}};',
				],
			]
		);

        $this->add_control(
            'ekit_cp_table_heading_content',
            [
                'type'     => Controls_Manager::REPEATER,
                'default'  => [
                    [
                        'ekit_cp_table_heading_element'  => 'cell',
                        'cell_text'  => esc_html__( 'Column', 'elementskit' ),
                    ],
                    [
                        'ekit_cp_table_heading_element'  => 'cell',
                        'ekit_cp_table_heading_cell_text'  => esc_html__( 'Column', 'elementskit' ),
                    ],
                    [
                        'ekit_cp_table_heading_element'  => 'cell',
                        'ekit_cp_table_heading_cell_text'  => esc_html__( 'Column', 'elementskit' ),
                    ],
                    [
                        'ekit_cp_table_heading_element'  => 'cell',
                        'ekit_cp_table_heading_cell_text' => esc_html__( 'Column', 'elementskit' ),
                    ],
                    [
                        'ekit_cp_table_heading_element'  => 'cell',
                        'ekit_cp_table_heading_cell_text'  => esc_html__( 'Column', 'elementskit' ),
                    ],
                    [
                        'ekit_cp_table_heading_element'  => 'cell',
                        'ekit_cp_table_heading_cell_text' => esc_html__( 'Column', 'elementskit' ),
                    ],
                ],
                'fields'      => $repeater_body->get_controls(),
                'title_field' => ' {{{ ekit_cp_table_heading_cell_text }}}',
            ]
        );

        $this->end_controls_section();

        // table body cell 
        $this->start_controls_section(
			'ekit_cp_table_content_section',
			[
				'label' => esc_html__('Table Body', 'elementskit'),
			]
		);

        $repeater_body = new Repeater();

        $repeater_body->add_control(
            'ekit_cp_table_row', [
                'label' => esc_html__( 'New Row', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'label_on' => esc_html__( 'Yes', 'elementskit' ),
                'return_value' => esc_html__( 'Row', 'elementskit' ),
            ]
        );

		$repeater_body->add_responsive_control(
			'ekit_cp_table_column_width',
			[
				'label'          => __( 'Column Width', 'elementskit' ),
				'type'           => Controls_Manager::SLIDER,
				'frontend_available'    => true,
				'size_units'     => ['%', 'px'],
				'range'          => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min'  => 0,
						'max'  => 2000,
						'step' => 1,
					],
				],
				'default' => [
                    'size' => 50,
                    'unit' => '%',
                ],
				'selectors'   => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

        $repeater_body->add_control(
            'ekit_cp_table_cell_text',
            [
                'label' => esc_html__( 'Content', 'elementskit' ),
                'type' => Controls_Manager::WYSIWYG,
                'default' => esc_html__( 'Column' , 'elementskit' ),
                'show_label' => false,
            ]
        );

        $repeater_body->add_control(
            'ekit_cp_table_body_cell_url_show',
            [
                'label' => esc_html__('Add a url? ', 'elementskit'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' =>esc_html__( 'Yes', 'elementskit' ),
                'label_off' =>esc_html__( 'No', 'elementskit' ),
            ]
        );

        $repeater_body->add_control(
			'ekit_cp_table_body_cell_url',
			[
				'label' =>esc_html__( 'URL', 'elementskit' ),
				'type' => Controls_Manager::URL,
				'placeholder' =>esc_url('https://wpmet.com'),
				'condition' => [
					'ekit_cp_table_body_cell_url_show' => 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

        $repeater_body->add_control(
            'ekit_cp_table_body_cell_icon_type',
            [
                'label'           => esc_html__( 'Type', 'elementskit' ),
                'label_block'     => false,
                'type'            => Controls_Manager::CHOOSE,
                'options'         => [
                    'none'        => [
                        'title'   => esc_html__( 'None', 'elementskit' ),
                        'icon'    => 'eicon-ban',
                    ],
                    'icon'        => [
                        'title'   => esc_html__( 'Icon', 'elementskit' ),
                        'icon'    => 'eicon-star',
                    ],
                    'image'       => [
                        'title'   => esc_html__( 'Image', 'elementskit' ),
                        'icon'    => 'eicon-image',
                    ],
                ],
                'default'  => 'none',
            ]
        );

        $repeater_body->add_control(
            'ekit_cp_table_body_cell_icons',
            [
                'label'   => __( 'Icon', 'elementskit' ),
                'type'    => Controls_Manager::ICONS,
                'fa4compatibility' => 'body_cell_icon',
                'default' => [
                    'value' => '',
                ],
                'condition'             => [
                    'ekit_cp_table_body_cell_icon_type' => 'icon',
                ],
            ]
        );

        $repeater_body->add_control(
            'ekit_cp_table_body_cell_icon_image',
            [
                'label'   => esc_html__( 'Image', 'elementskit' ),
                'type'    => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                    'id'    => -1
                ],
                'condition' => [
                    'ekit_cp_table_body_cell_icon_type' => 'image',
                ],
				'dynamic' => [
					'active' => true,
				],
            ]
        );

        $repeater_body->add_control(
            'ekit_cp_table_body_cell_icon_position',
            [
                'label'         => esc_html__( 'Icon Position', 'elementskit' ),
                'type'          => Controls_Manager::SELECT,
                'default'       => 'before',
                'options'       => [
                    'before'    => esc_html__( 'Before', 'elementskit' ),
                    'after'     => esc_html__( 'After', 'elementskit' ),
                    'top'       => esc_html__( 'Top', 'elementskit' ),
                    'bottom'    => esc_html__( 'Bottom', 'elementskit' ),
                ],
            ]
        );

		$repeater_body->add_control(
			'ekit_cp_table_body_image_width',
			[
				'label' => esc_html__( 'Image Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.ekit-comparison-table-cell a > img' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.ekit-comparison-table-cell li > img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_cp_table_body_cell_icon_type' => 'image',
				],
			]
		);

		$repeater_body->add_control(
			'ekit_cp_table_cell_btn',
			[
				'label'        => __( 'Button', 'elementskit' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			]
		);

		$repeater_body->add_control(
			'ekit_cp_table_cell_btn_text',
			[
				'label' => esc_html__( 'Button Text', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Read More', 'elementskit' ),
				'placeholder' => esc_html__( 'Type your title here', 'elementskit' ),
				'condition'     => [
					'ekit_cp_table_cell_btn' => 'yes',
				],
			]
		);

		$repeater_body->add_control(
			'ekit_cp_table_cell_btn_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-row {{CURRENT_ITEM}} .ekit-cp-table-button' => 'color: {{VALUE}}',
				],
				'condition'     => [
					'ekit_cp_table_cell_btn' => 'yes',
				],
			]
		);

		$repeater_body->add_control(
			'ekit_cp_table_cell_btn_bg',
			[
				'label' => esc_html__( 'Background', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .ekit-cp-table-button' => 'background: {{VALUE}}',
				],
				'condition'     => [
					'ekit_cp_table_cell_btn' => 'yes',
				],
			]
		);

		$repeater_body->add_control(
			'ekit_cp_table_cell_btn_align',
			[
				'label'   => esc_html__( 'Alignment', 'elementskit' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'start'    => [
						'title' => esc_html__( 'Left', 'elementskit' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementskit' ),
						'icon'  => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'Right', 'elementskit' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} a, {{WRAPPER}} {{CURRENT_ITEM}} li' => 'align-items: {{VALUE}}; text-align: {{VALUE}}; justify-content: flex-{{VALUE}}; justify-content: {{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} .ekit-comparison-table-bcell-btn' => 'justify-content: flex-{{VALUE}}; justify-content: {{VALUE}};',
				],
				'default' => 'center',
			]
		);

        $this->add_control(
            'ekit_cp_table_body_content',
            [
                'type'     => Controls_Manager::REPEATER,
                'default'  => [
                    [
                        'ekit_cp_table_body_element'  => 'cell',
                        'cell_text'           => esc_html__( 'Row', 'elementskit' ),
                        'ekit_cp_table_row'  => 'Row',
                    ],
                    [
                        'ekit_cp_table_body_element'  => 'cell',
                        'cell_text' => esc_html__( 'Column', 'elementskit' ),
                    ],
                    [
                        'ekit_cp_table_body_element' => 'cell',
                        'ekit_cp_table_cell_text' => esc_html__( 'Column', 'elementskit' ),
                    ],
                    [
                        'ekit_cp_table_body_element' => 'cell',
                        'ekit_cp_table_cell_text' => esc_html__( 'Row', 'elementskit' ),
                        'ekit_cp_table_row' => 'Row',
                    ],
                    [
                        'ekit_cp_table_body_element' => 'cell',
                        'ekit_cp_table_cell_text' => esc_html__( 'Column', 'elementskit' ),
                    ],
                    [
                        'ekit_cp_table_body_element' => 'cell',
                        'ekit_cp_table_cell_text' => esc_html__( 'Column', 'elementskit' ),
                    ],
                ],
                'fields' => $repeater_body->get_controls(),
                'title_field' => '{{{ ekit_cp_table_row }}}: {{{ ekit_cp_table_cell_text }}}',
            ]
        );

        $this->end_controls_section();

        // table button repeater section
        $this->start_controls_section(
			'ekit_cp_table_button_section',
			[
				'label' => esc_html__('Table Button', 'elementskit'),
			]
		);

        $repeater = new Repeater();

		$repeater->add_control(
			'ekit_cp_table_btn_title',
			[
				'label'       => esc_html__( 'Title', 'elementskit' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Buy Now', 'elementskit' ),
				'placeholder' => esc_html__( 'Comparison Table', 'elementskit' ),
			]
		);

		$repeater->add_control(
			'ekit_cp_table_btn_link',
			[
				'label'         => esc_html__( 'Link', 'elementskit' ),
				'type'          => Controls_Manager::URL,
				'placeholder'   => esc_html__( 'https:wpmet.com', 'elementskit' ),
				'show_external' => true,
				'default'       => [
					'url'         => '#',
					'is_external' => true,
					'nofollow'    => true,
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

        $this->add_control(
			'ekit_cp_table_button_list',
			[
				'label'    => esc_html__( 'Buttons', 'elementskit' ),
				'type'     => Controls_Manager::REPEATER,
				'fields'   => $repeater->get_controls(),
				'default'  => [
					[
						'btn_title' => esc_html__( 'Download', 'elementskit' ),
						'link'      => [
							'url'         => '#',
							'is_external' => true,
							'nofollow'    => true,
						],
					],
					[
						'btn_title' => esc_html__( 'Buy Now', 'elementskit' ),
						'link'      => [
							'url'         => '#',
							'is_external' => true,
							'nofollow'    => true,
						],
					],
				],
				'title_field'   => '{{{ ekit_cp_table_btn_title }}}',
				'prevent_empty' => false,
			]
		);

        $this->end_controls_section();

		//Table Compare Section
		$this->start_controls_section(
			'ekit_cp_table_compare_button_section',
			[
				'label'	 => esc_html__( 'Compare Button', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_cp_table_compare_button_switch',
			[
				'label' => esc_html__( 'Button Style', 'elementskit' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => [
					'inline' => esc_html__( 'Inline Style', 'elementskit' ),
					'tab' => esc_html__( 'Tab Style', 'elementskit' ),
				],
			]
		);

		$this->add_control(
			'ekit_cp_table_compare_full_button_text',
			[
				'label' => esc_html__( 'Full Button', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Full', 'elementskit' ),
				'placeholder' => esc_html__( 'Type your title here', 'elementskit' ),
				'condition' => [
					'ekit_cp_table_compare_button_switch'  => 'tab',
				],
			]
		);

		$this->add_control(
			'ekit_cp_table_compare_button_text',
			[
				'label' => esc_html__( 'Difference Button', 'elementskit' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Difference', 'elementskit' ),
				'placeholder' => esc_html__( 'Type your title here', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_cp_table_compare_button_position',
			[
				'label'  => esc_html__( 'Position', 'elementskit' ),
				'type'  => Controls_Manager::SELECT,
				'default'  => 'bottom',
				'options'  => [
					'top'  => esc_html__( 'Top', 'elementskit' ),
					'bottom'  => esc_html__( 'Bottom', 'elementskit' ),
				],
				'condition' => [
					'ekit_cp_table_compare_button_text!'  => '',
					'ekit_cp_table_compare_full_button_text!'  => '',
				],
			]
		);

		$this->end_controls_section();

		//Table wrapper Style Section
		$this->start_controls_section(
			'ekit_cp_table_wrapper_style', [
				'label'	 => esc_html__( 'Wrapper', 'elementskit' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
            'ekit_cp_table_wrapper_tabs'
        );

        $this->start_controls_tab(
            'ekit_cp_table_wrapper_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_cp_table_wrapper_bg',
				'types' => [ 'classic', 'gradient', ],
				'selector' => '{{WRAPPER}} .ekit-comparison-table-content',
			]
		);

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_cp_table_wrapper_shadow',
                'selector' => '{{WRAPPER}} .ekit-comparison-table-content',

            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_wrapper_border',
				'selector' => '{{WRAPPER}} .ekit-comparison-table-content',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_cp_table_wrapper_hv_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_cp_table_wrapper_hv_bg',
				'types' => [ 'classic', 'gradient', ],
				'selector' => '{{WRAPPER}} .ekit-comparison-table-content:hover',
			]
		);

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_cp_table_wrapper_hv_shadow',
                'selector' => '{{WRAPPER}} .ekit-comparison-table-content:hover',
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_wrapper_hv_border',
				'selector' => '{{WRAPPER}} .ekit-comparison-table-content:hover',
			]
		);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
			'ekit_cp_table_wrapper_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
                'separator'  => 'before',
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_wrapper_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_wrapper_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

        //Table wrapper Style Section
		$this->start_controls_section(
			'ekit_cp_table_content_style', [
				'label'	 => esc_html__( 'Table Content', 'elementskit' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
			]
		);

        $this->start_controls_tabs(
            'ekit_cp_table_content_tabs'
        );

        $this->start_controls_tab(
            'ekit_cp_table_content_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_cp_table_content_bg',
				'types' => [ 'classic', 'gradient', ],
				'selector' => '{{WRAPPER}} .ekit-comparison-table-wrapper',
			]
		);

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_cp_table_content_shadow',
                'selector' => '{{WRAPPER}} .ekit-comparison-table-wrapper',

            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_content_border',
				'selector' => '{{WRAPPER}} .ekit-comparison-table-wrapper',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_cp_table_content_hv_tab',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_cp_table_content_hv_bg',
				'types' => [ 'classic', 'gradient', ],
				'selector' => '{{WRAPPER}} .ekit-comparison-table-wrapper:hover',
			]
		);

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_cp_table_content_hv_shadow',
                'selector' => '{{WRAPPER}} .ekit-comparison-table-wrapper:hover',
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_content_hv_border',
				'selector' => '{{WRAPPER}} .ekit-comparison-table-wrapper:hover',
			]
		);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
			'ekit_cp_table_content_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
                'separator'  => 'before',
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_content_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_content_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

        //Table Heading Style Section
		$this->start_controls_section(
			'ekit_cp_table_heading_style', [
				'label'	 => esc_html__( 'Table Heading', 'elementskit' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'ekit_cp_table_heading_icon',
			[
				'label' => esc_html__( 'Icon', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
			]
		);

        $this->add_control(
			'ekit_cp_table_heading_icon_size',
			[
				'label' => esc_html__( 'Font Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
                'separator' => 'after',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-heading-cell a > :is( i, svg )' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-comparison-table-heading-cell li :is( i, svg )' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_heading_image_radius',
			[
				'label' =>esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '',
					'right' => '',
					'bottom' => '' ,
					'left' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-heading-cell a > img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_heading_icon_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
                'separator' => 'before',
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-heading-cell a > :is( i, svg )' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-comparison-table-heading-cell a > img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-comparison-table-heading-cell li > :is( i, svg )' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-comparison-table-heading-cell li > img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_cp_table_heading_typography',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell a, {{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell li',
			]
		);

        $this->start_controls_tabs(
            'ekit_cp_table_heading_tabs'
        );

        $this->start_controls_tab(
            'ekit_cp_table_heading_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_control(
			'ekit_cp_table_heading_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell a, {{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell li' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell a :is( i, svg ), {{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell li :is( i, svg )' => 'color: {{VALUE}}; fill: {{VALUE}}; stroke: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_cp_table_heading_bg',
				'types' => [ 'classic', 'gradient', ],
                'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-wrapper .ekit-comparison-table-heading .ekit-comparison-table-heading-cell',
                'default' => '#fff',
                'exclude'  => ['image'],
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_heading_border',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_cp_table_heading_hv_tab',
            [
                'label' => esc_html__( 'hover', 'elementskit' ),
            ]
        );

        $this->add_control(
			'ekit_cp_table_heading_hv_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell:hover a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell:hover li' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell a:hover :is( i, svg ), {{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell li:hover :is( i, svg )' => 'color: {{VALUE}}; fill: {{VALUE}}; stroke: {{VALUE}};',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_cp_table_heading_hv_bg',
				'types' => [ 'classic', 'gradient', ],
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell:hover',
                'exclude'  => ['image']
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_heading_hv_border',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell:hover',
			]
		);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
			'ekit_cp_table_heading_cell_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'separator'	=> 'before',
				'size_units' => [ 'px', 'em' ],
                'default' => [
                    'top'      => '5',
                    'right'    => '10',
                    'bottom'   => '5',
                    'left'     => '10',
                    'unit'     => 'px',
                ],
                'isLinked' => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_heading_cell_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
                'default' => [
                    'top'      => '0',
                    'right'    => '0',
                    'bottom'   => '0',
                    'left'     => '0',
                    'unit'     => 'px',
                ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-cell' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_cp_table_style_heading',
			[
				'label' => esc_html__( 'Table Heading Wrapper', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_cp_table_heading_shadow',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_headin_border',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading',
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_heading_border_radius',
			[
				'label' => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_heading_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'isLinked' => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_heading_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->end_controls_section();

        //Table Body  Style Section
		$this->start_controls_section(
			'ekit_cp_table_body_style', [
				'label'	 => esc_html__( 'Table Body', 'elementskit' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'ekit_cp_table_body_icon',
			[
				'label' => esc_html__( 'Icon', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_control(
			'ekit_cp_table_body_icon_size',
			[
				'label' => esc_html__( 'Font Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
                'separator' => 'after',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-cell li :is( i, svg)' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-comparison-table-cell a :is( i, svg)' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_body_icon_size_image_radius',
			[
				'label' =>esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '',
					'right' => '',
					'bottom' => '' ,
					'left' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-cell a > img, .ekit-comparison-table-cell li > img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_body_icon_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell a > :is( i, svg), .ekit-comparison-table-cell li > :is( i, svg)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell a > img, .ekit-wid-con .ekit-comparison-table-cell li > img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_cp_table_body_typography',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell a, {{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell li',
				'separator' => 'after',
			]
		);

        $this->start_controls_tabs(
            'ekit_cp_table_body_tabs'
        );

        $this->start_controls_tab(
            'ekit_cp_table_body_normal_tab',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_control(
			'ekit_cp_table_body_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell a' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell li' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell li :is( i, svg)' => 'color: {{VALUE}}; fill: {{VALUE}}; stroke: {{VALUE}}',
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell a :is( i, svg)' => 'color: {{VALUE}}; fill: {{VALUE}}; stroke: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_cp_table_body_bg',
				'types' => [ 'classic', 'gradient', ],
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-body .ekit-comparison-row',
                'exclude'  => ['image']
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_cp_table_body_box_shadow',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-body .ekit-comparison-row',
			]
		);

        $this->add_control(
			'ekit_cp_table_odd_bg_heading',
			[
				'label' => esc_html__( 'Odd Row Background', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_cp_table_odd_bg',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-body .ekit-comparison-row:nth-child(odd) .ekit-comparison-table-cell',
				'exclude'  => ['image'],
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_body_border',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell',
                'separator' => 'before',
			]
		);

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_cp_table_body_hv_tab',
            [
                'label' => esc_html__( 'hover', 'elementskit' ),
            ]
        );

        $this->add_control(
			'ekit_cp_table_body_hv_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell:hover a, .ekit-wid-con .ekit-comparison-table-cell:hover li' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell li:hover :is( i, svg)' => 'color: {{VALUE}}; fill: {{VALUE}}; stroke: {{VALUE}}',
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell a:hover :is( i, svg)' => 'color: {{VALUE}}; fill: {{VALUE}}; stroke: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_cp_table_body_hv_bg',
				'types' => [ 'classic', 'gradient', ],
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-body .ekit-comparison-row:hover',
                'exclude'  => ['image']
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_cp_table_body_hv_box_shadow',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-body .ekit-comparison-row:hover',
			]
		);

        $this->add_control(
			'ekit_cp_table_odd_bg_hv_heading',
			[
				'label' => esc_html__( 'Odd Row Background', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'ekit_cp_table_odd_hv_bg',
				'types' => [ 'classic', 'gradient', ],
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-body .ekit-comparison-row:nth-child(odd):hover',
                'exclude'  => ['image'],
                'default' => '#DEDEDE',
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_body_hv_border',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell:hover',
                'separator' => 'before',
			]
		);

        $this->end_controls_tab();

        $this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_cp_table_body_last_cell_border',
			[
				'label' => esc_html__( 'Last Cell Border', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'separator'	=> 'before',
				'size_units' => [ 'px' ],
				'isLinked' => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell:last-child' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_body_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
                'default' => [
                    'top'      => '5',
                    'right'    => '10',
                    'bottom'   => '5',
                    'left'     => '10',
                    'unit'     => 'px',
                ],
                'isLinked' => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_body_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
                'default' => [
                    'top'      => '0',
                    'right'    => '0',
                    'bottom'   => '0',
                    'left'     => '0',
                    'unit'     => 'px',
                ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_cp_table_style_body_row',
			[
				'label' => esc_html__( 'Table Body Row', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_cp_table_body_row_shadow',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-body .ekit-comparison-row',
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_body_row_radius',
			[
				'label' => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-body .ekit-comparison-row' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_body_row_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'isLinked' => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-body .ekit-comparison-row' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_body_row_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-body .ekit-comparison-row' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		//Table heading Button Style Section
		$this->start_controls_section(
			'ekit_cp_table_hcell_button', [
				'label'	 => esc_html__( 'Table Heading Cell Button', 'elementskit' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_cp_table_hcell_button_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-btn .ekit-comparison-table-heading-button',
			]
		);

		$this->start_controls_tabs( 'ekit_cp_table_hcell_button_tabs_style' );

		$this->start_controls_tab(
			'ekit_cp_table_hcell_button_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_hcell_button_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-btn .ekit-comparison-table-heading-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_hcell_button_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-button' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_cp_table_hcell_button_shadow',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_hcell_button_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_cp_table_hcell_button_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_hcell_button_h_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_hcell_button_h_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-button:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_cp_table_hcell_button_h_shadow',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-button:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_hcell_button_h_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-button:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_cp_table_hcell_button_border_radius',
			[
				'label' => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'separator'	=> 'before',
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_hcell_button_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top'      => '5',
					'right'    => '10',
					'bottom'   => '5',
					'left'     => '10',
					'unit'     => 'px',
				],
				'isLinked' => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_hcell_button_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default' => [
					'top'      => '15',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
					'unit'     => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-heading-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		//Table Cell Button Style Section
		$this->start_controls_section(
			'ekit_cp_table_bcell_button', [
				'label'	 => esc_html__( 'Table Body Cell Button', 'elementskit' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_cp_table_bcell_button_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell .ekit-cp-table-button',
			]
		);

        $this->start_controls_tabs( 'ekit_cp_table_bcell_button_tabs_style' );

		$this->start_controls_tab(
			'ekit_cp_table_bcell_button_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_bcell_button_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-cell .ekit-cp-table-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_bcell_button_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-cell .ekit-cp-table-button' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_cp_table_bcell_button_shadow',
				'selector' => '{{WRAPPER}} .ekit-comparison-table-cell .ekit-cp-table-button',
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_bcell_button_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-comparison-table-cell .ekit-cp-table-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_cp_table_bcell_button_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_bcell_button_h_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-cell .ekit-cp-table-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_bcell_button_h_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-cell .ekit-cp-table-button:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_cp_table_bcell_button_h_shadow',
				'selector' => '{{WRAPPER}} .ekit-comparison-table-cell .ekit-cp-table-button:hover',
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_bcell_button_h_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-comparison-table-cell .ekit-cp-table-button:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_cp_table_bcell_button_border_radius',
			[
				'label' => esc_html__('Border Radius', 'elementskit'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'separator'	=> 'before',
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-cell .ekit-cp-table-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_bcell_button_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
                'default' => [
                    'top'      => '5',
                    'right'    => '10',
                    'bottom'   => '5',
                    'left'     => '10',
                    'unit'     => 'px',
                ],
                'isLinked' => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-cell .ekit-cp-table-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_bcell_button_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
                'default' => [
                    'top'      => '15',
                    'right'    => '0',
                    'bottom'   => '0',
                    'left'     => '0',
                    'unit'     => 'px',
                ],
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-cell .ekit-cp-table-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

        //Table Button Style Section
		$this->start_controls_section(
			'ekit_cp_table_button_style', [
				'label'	 => esc_html__( 'Table Button', 'elementskit' ),
				'tab'	 => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_cp_table_button_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-comparison-table-button .ekit-cp-button',
			]
		);

        $this->start_controls_tabs( 'ekit_cp_table_button_tabs_style' );

		$this->start_controls_tab(
			'ekit_cp_table_button_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_button_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-button .ekit-cp-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_button_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-button .ekit-cp-button' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_cp_table_button_shadow',
				'selector' => '{{WRAPPER}} .ekit-comparison-table-button .ekit-cp-button',
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_button_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-comparison-table-button .ekit-cp-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_cp_table_button_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_button_h_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-button .ekit-cp-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_button_h_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-button .ekit-cp-button:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_cp_table_button_h_shadow',
				'selector' => '{{WRAPPER}} .ekit-comparison-table-button .ekit-cp-button:hover',
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_button_h_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-comparison-table-button .ekit-cp-button:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_cp_table_button_radius',
			[
				'label' =>esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'separator'	=> 'before',
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top' => '5',
					'right' => '5',
					'bottom' => '5' ,
					'left' => '5',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-button .ekit-cp-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_button_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
                'default' => [
                    'top'      => '5',
                    'right'    => '10',
                    'bottom'   => '5',
                    'left'     => '10',
                    'unit'     => 'px',
                ],
                'isLinked' => false,
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-button .ekit-cp-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
			'ekit_cp_table_button_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
                'default' => [
                    'top'      => '15',
                    'right'    => '0',
                    'bottom'   => '0',
                    'left'     => '0',
                    'unit'     => 'px',
                ],
				'selectors' => [
					'{{WRAPPER}} .ekit-comparison-table-button .ekit-cp-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
            'ekit_cp_table_button_align',
            [
                'label'   => esc_html__( 'Alignment', 'elementskit' ),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'start'    => [
                        'title' => esc_html__( 'Left', 'elementskit' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__( 'Center', 'elementskit' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'end' => [
                        'title' => esc_html__( 'Right', 'elementskit' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-comparison-table-button' => 'text-align: {{VALUE}};',
				],
                'default' => 'center',
            ]
        );

        $this->end_controls_section();

        //Compare Button Style Section
        $this->start_controls_section(
            'ekit_cp_table_compare_button', [
                'label'	 => esc_html__( 'Compare Button', 'elementskit' ),
                'tab'	 => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_cp_table_compare_button_text!'  => '',
					'ekit_cp_table_compare_full_button_text!'  => '',
				],
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_cp_table_compare_button_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .compare-button-wrap .compare-button, {{WRAPPER}} .ekit-wid-con .ekit-diff-on',
			]
		);

        $this->start_controls_tabs( 'ekit_cp_table_compare_button_tabs_style' );

		$this->start_controls_tab(
			'ekit_cp_table_compare_button_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_compare_button_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .compare-button-wrap .compare-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ekit-wid-con .ekit-diff-on' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_compare_button_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .compare-button-wrap .compare-button' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .ekit-wid-con .ekit-diff-on' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_cp_table_compare_button_shadow',
				'selector' => '{{WRAPPER}} .compare-button-wrap .compare-button, {{WRAPPER}} .ekit-wid-con .ekit-diff-on',
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_compare_button_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .compare-button-wrap .compare-button, {{WRAPPER}} .ekit-wid-con .ekit-diff-on',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_cp_table_compare_button_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_compare_button_h_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .compare-button-wrap .compare-button:hover, {{WRAPPER}} .ekit-wid-con .ekit-diff-on:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_compare_button_h_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .compare-button-wrap .compare-button:hover, {{WRAPPER}} .ekit-wid-con .ekit-diff-on:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_cp_table_compare_button_h_shadow',
				'selector' => '{{WRAPPER}} .compare-button-wrap .compare-button:hover, {{WRAPPER}} .ekit-wid-con .ekit-diff-on:hover',
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_compare_button_h_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .compare-button-wrap .compare-button:hover, {{WRAPPER}} .ekit-wid-con .ekit-diff-on:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_cp_table_compare_button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
				],
				'selectors' => [
					'{{WRAPPER}} .compare-button-wrap .compare-button, {{WRAPPER}} .ekit-wid-con .ekit-diff-on' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_cp_table_compare_button_heading',
			[
				'label' => esc_html__( 'Full Compaire Button', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_cp_table_compare_button_switch' => 'tab',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_cp_table_cfull_button_typography',
				'label' => esc_html__( 'Typography', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-diff-off',
				'condition' => [
					'ekit_cp_table_compare_button_switch' => 'tab',
				],
			]
		);

        $this->start_controls_tabs( 'ekit_cp_table_cfull_button_tabs_style' );

		$this->start_controls_tab(
			'ekit_cp_table_cfull_button_tab_normal',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
				'condition' => [
					'ekit_cp_table_compare_button_switch' => 'tab',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_cfull_button_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-diff-off' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_cfull_button_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-diff-off' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_cp_table_cfull_button_shadow',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-diff-off',
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_cfull_button_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-diff-off',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_cp_table_cfull_button_hover',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
				'condition' => [
					'ekit_cp_table_compare_button_switch' => 'tab',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_cfull_button_h_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-diff-off:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_cfull_button_h_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-diff-off:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'ekit_cp_table_cfull_button_h_shadow',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-diff-off:hover',
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cp_table_cfull_button_h_border',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-diff-off:hover',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'ekit_cp_table_cfull_button_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'default' => [
					'top'      => '0',
					'right'    => '0',
					'bottom'   => '0',
					'left'     => '0',
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-diff-off' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_compare_button_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'separator' => 'before',
				'size_units' => [ 'px' ],
                'default' => [
                    'top'      => '10',
                    'right'    => '10',
                    'bottom'   => '10',
                    'left'     => '10',
                    'unit'     => 'px',
                ],
				'selectors' => [
					'{{WRAPPER}} .compare-button-wrap .compare-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit-diff-on' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ekit-wid-con .ekit-diff-off' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_compare_button_margin',
			[
				'label' => esc_html__( 'Margin', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
                'default' => [
                    'top'      => '15',
                    'right'    => '0',
                    'bottom'   => '0',
                    'left'     => '0',
                    'unit'     => 'px',
                ],
				'selectors' => [
					'{{WRAPPER}} .compare-button-wrap' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ekit_cp_table_compare_toggle_style',
			[
				'label' => esc_html__( 'Compare Toggle Button', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'ekit_cp_table_compare_button_switch' => 'tab',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'ekit_cp_table_compare_toggle_typography',
				'selector' => '{{WRAPPER}} .ekit-wid-con .ekit-diff-off::after',
			]
		);

		$this->add_control(
			'ekit_cp_table_compare_toggle_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-diff-off::after' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_cp_table_compare_button_switch' => 'tab',
				],
			]
		);

		$this->add_control(
			'ekit_cp_table_compare_toggle_bg',
			[
				'label' => esc_html__( 'Background', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-diff-off::after' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_cp_table_compare_button_switch' => 'tab',
				],
			]
		);

		$this->add_control(
			'ekit_cp_table_compare_toggle_width',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-diff-off::after' => 'width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_cp_table_compare_button_switch' => 'tab',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_compare_toggle_transformx',
			[
				'label' => esc_html__( 'Transform ( X )', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => -200,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => -5,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-diff-off::after' => 'transform: translateX({{SIZE}}{{UNIT}}) ;',
				],
				'condition' => [
					'ekit_cp_table_compare_button_switch' => 'tab',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_cp_table_compare_toggle_transformy',
			[
				'label' => esc_html__( 'Transform ( Y )', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => -200,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 86,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .ekit-diff-off::after' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_cp_table_compare_button_switch' => 'tab',
				],
			]
		);

		$this->add_control(
			'ekit_cp_table_compare_button_align',
			[
				'label' => esc_html__( 'Alignment', 'elementskit' ),
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
				'default' => 'center',
				'toggle' => true,
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ekit-wid-con .compare-button-wrap' => 'text-align: {{VALUE}};',
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

	protected function render_raw() {
		$settings = $this->get_settings_for_display();
		extract($settings);
		$row_open = '<div class="ekit-comparison-row">';
		$row_close = '</div>';
		$count = 0;
		?>
			<?php if($ekit_cp_table_compare_button_position === 'top'): ?>
				<div class="compare-button-wrap">
				<?php if($ekit_cp_table_compare_button_switch === 'inline'): ?>
						<button id="buttonId" class="compare-button"><?php echo esc_html_e($ekit_cp_table_compare_button_text, 'elementskit'); ?></button>
					<?php endif; 

					if($ekit_cp_table_compare_button_switch === 'tab'): ?>
						<div class="ekit-diff-toggle on" id="buttonId">
							<span class="ekit-diff-off"><?php esc_html_e($ekit_cp_table_compare_full_button_text, 'elementskit'); ?></span>
							<span class="ekit-diff-on"><?php esc_html_e($ekit_cp_table_compare_button_text, 'elementskit'); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="ekit-comparison-table-content">
				<div class="ekit-comparison-table-wrapper">
					<div class="ekit-comparison-table-heading">
						<div class="ekit-comparison-row">
							<?php foreach($settings['ekit_cp_table_heading_content'] as $index => $item) :
								!empty($item['ekit_cp_table_heading_cell_url']['url']) &&	$this->add_link_attributes('ekit_cp_table_heading_cell_url'.$item['_id'], $item['ekit_cp_table_heading_cell_url']); 
							?>
								<div class="ekit-comparison-table-heading-cell elementor-repeater-item-<?php echo esc_attr($item['_id']); ?> <?php echo esc_attr($item['ekit_cp_table_heading_cell_icon_position']); ?>">

									<?php if (empty($item['ekit_cp_table_heading_cell_url']['url'])): ?>
										<li>
											<?php require Handler::get_dir() . 'styles/heading-cell.php'; ?>
										</li>
									<?php else: ?>
										<a <?php $this->print_render_attribute_string('ekit_cp_table_heading_cell_url'.$item['_id']); ?> >
											<?php require Handler::get_dir() . 'styles/heading-cell.php'; ?>
										</a>
									<?php endif; ?>

									<?php if (!empty($item['ekit_cp_table_heading_cell_btn'] == 'yes')) : ?>
										<div class="ekit-comparison-table-heading-btn">

											<?php if (!empty($item['ekit_cp_table_heading_cell_btn_text'])) : ?>
												<a href="<?php echo (!empty($item['ekit_cp_table_heading_cell_url']['url']) ? esc_url($item['ekit_cp_table_heading_cell_url']['url']) : '#') ?>" class="ekit-comparison-table-heading-button">
													<?php echo wp_kses($item['ekit_cp_table_heading_cell_btn_text'], \ElementsKit_Lite\Utils::get_kses_array()); ?>
												</a>
											<?php endif; ?>

										</div>
									<?php endif; ?>

								</div>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="ekit-comparison-table-body">
						<?php foreach($ekit_cp_table_body_content as $index => $item) : 

							!empty($item['ekit_cp_table_body_cell_url']['url']) &&	$this->add_link_attributes('ekit_cp_table_body_cell_url'.$item['_id'], $item['ekit_cp_table_body_cell_url']);

							if($item['ekit_cp_table_row'] == 'Row') {
								if($count == 0) {
									echo wp_kses($row_open, \ElementsKit_Lite\Utils::get_kses_array());
								} elseif($count > 0) {
									echo wp_kses($row_close, \ElementsKit_Lite\Utils::get_kses_array());
									echo wp_kses($row_open, \ElementsKit_Lite\Utils::get_kses_array());
								}
								$count++;
							}

							?>
								<div class="ekit-comparison-table-cell elementor-repeater-item-<?php echo esc_attr($item['_id']); ?> <?php echo esc_attr($item['ekit_cp_table_body_cell_icon_position']); ?>">

									<?php if (empty($item['ekit_cp_table_body_cell_url']['url'])): ?>
										<li>
											<?php require Handler::get_dir() . 'styles/body-cell.php'; ?>
										</li>
									<?php else: ?>
										<a <?php $this->print_render_attribute_string('ekit_cp_table_body_cell_url'.$item['_id']); ?>>
											<?php require Handler::get_dir() . 'styles/body-cell.php'; ?>
										</a>
									<?php endif; ?>

								<?php if (!empty($item['ekit_cp_table_cell_btn_text'])) : ?>

									<div class="ekit-comparison-table-bcell-btn">
										<a href="<?php echo (!empty($item['ekit_cp_table_body_cell_url']['url']) ? esc_url($item['ekit_cp_table_body_cell_url']['url']) : '#') ?>" class="ekit-cp-table-button">
											<?php echo wp_kses($item['ekit_cp_table_cell_btn_text'], \ElementsKit_Lite\Utils::get_kses_array()); ?>
										</a>
									</div>
								<?php endif; ?>

								</div>
							<?php
								if($index == count( $ekit_cp_table_body_content ) -1) {
								echo wp_kses($row_close, \ElementsKit_Lite\Utils::get_kses_array());
							}
						endforeach; ?>
					</div>
				</div>
				<div class="ekit-comparison-table-button">
					<?php foreach($ekit_cp_table_button_list as $index => $item) :
						?>
							<a  class="ekit-cp-button elementor-repeater-item-<?php echo esc_attr($item['_id']); ?>" href="<?php echo esc_url($item['ekit_cp_table_btn_link']['url']) ?>"><?php echo esc_html($item['ekit_cp_table_btn_title']); ?></a>
						<?php
					endforeach; ?>
				</div>
			</div>

			<?php if($ekit_cp_table_compare_button_position === 'bottom'): ?>
				<div class="compare-button-wrap">
					<?php if($ekit_cp_table_compare_button_switch === 'inline'): ?>
						<button id="buttonId" class="compare-button"><?php esc_html_e($ekit_cp_table_compare_button_text, 'elementskit'); ?></button>
					<?php endif; 

					if($ekit_cp_table_compare_button_switch === 'tab'): ?>
						<div class="ekit-diff-toggle on" id="buttonId">
							<span class="ekit-diff-off"><?php esc_html_e($ekit_cp_table_compare_full_button_text, 'elementskit'); ?></span>
							<span class="ekit-diff-on"><?php esc_html_e($ekit_cp_table_compare_button_text, 'elementskit'); ?></span>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php

	}
}