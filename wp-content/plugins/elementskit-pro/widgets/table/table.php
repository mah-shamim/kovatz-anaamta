<?php
namespace Elementor;

use \Elementor\ElementsKit_Widget_table_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if (! defined( 'ABSPATH' ) ) exit;

class ElementsKit_Widget_table extends Widget_Base {
    use \ElementsKit_Lite\Widgets\Widget_Notice;

    public $base;

    public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->add_script_depends('datatables');
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
        return 'https://wpmet.com/doc/wordpress-plugin-table-with-filter-table-widget/';
    }

    protected function register_controls() {
        $this->start_controls_section(
            'section_content_table',
            [
                'label' => esc_html__( 'Table', 'elementskit' ),
            ]
        );

        $this->add_control(
            'ekit_table_data_type',
            [
                'label'     => esc_html__('Data Type', 'elementskit'),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'custom'    => esc_html__('Custom', 'elementskit'),
                    'csv'       => 'CSV' . esc_html__(' File','elementskit')
                ],
                'default'   => 'custom'
            ]);
        $repeater_header = new Repeater();

        $repeater_header->add_control(
            'table_header_content',
            [
                'label'                 => esc_html__( 'Text', 'elementskit' ),
                'type'                  => Controls_Manager::TEXT,
                'placeholder'           => esc_html__( 'Table Header', 'elementskit' ),
                'default'               => esc_html__( 'Table Header', 'elementskit' ),
				'dynamic'               => [
					'active' => true,
				],
            ]
        );

        $repeater_header->add_control(
            'cell_bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit_table table.dataTable thead {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $repeater_header->add_control(
            'cell_text_color',
            [
                'label'     => esc_html__( 'Text Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit_table table.dataTable thead {{CURRENT_ITEM}}' => 'color: {{VALUE}};',
                ],
            ]
        );


        $repeater_header->add_control(
            'cell_icon_type',
            [
                'label'                 => esc_html__( 'Icon Type', 'elementskit' ),
                'label_block'           => false,
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
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
                'default'               => 'none',
            ]
        );

        
        $repeater_header->add_control(
            'cell_icons',
            [
                'label'                 => __( 'Icon', 'elementskit' ),
                'type'                  => Controls_Manager::ICONS,
                'fa4compatibility'      => 'cell_icon',
                'default' => [
                    'value' => '',
                ],
                'condition'             => [
                    'cell_icon_type' => 'icon',
                ],
            ]
        );

        $repeater_header->add_control(
            'cell_icon_image',
            [
                'label'                 => esc_html__( 'Image', 'elementskit' ),
                'type'                  => Controls_Manager::MEDIA,
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                    'id'    => -1
                ],
                'condition'             => [
                    'cell_icon_type'       => 'image',
                ],
				'dynamic'               => [
					'active' => true,
				],
            ]
        );

        $repeater_header->add_control(
            'cell_icon_position',
            [
                'label'                 => esc_html__( 'Icon Position', 'elementskit' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'before',
                'options'               => [
                    'before'    => esc_html__( 'Before', 'elementskit' ),
                    'after'     => esc_html__( 'After', 'elementskit' ),
                    'top'     => esc_html__( 'Top', 'elementskit' ),
                ],
                'condition'             => [
                    'cell_icon_type!'   => 'none',
                ],
            ]
        );
        $repeater_header->add_control('ekit_table_cell_icon_spacing',
            [
                'label'         => esc_html__('Icon Spacing', 'elementskit'),
                'type'          => Controls_Manager::SLIDER,
                'default'       => [
                    'unit'      => 'px',
                    'size'      => 5,
                ],
                'selectors'     => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .ekit-table-icon-before'   => 'margin-right: {{SIZE}}px',
                    '{{WRAPPER}} {{CURRENT_ITEM}} .ekit-table-icon-after'    => 'margin-left: {{SIZE}}px',
                ],
                'condition'             => [
                    'cell_icon_type!'   => 'none',
                ],
                'separator'     => 'below',
            ]
        );

        $repeater_header->add_control(
            'cell_icon_color',
            [
                'label'     => esc_html__( 'Icon Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .ekit-table-icon' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'cell_icon_type'   => 'icon'
                ]
            ]
        );

        $this->add_control(
            'ekit_table_build_header',
            [
                'label'                 => 'Header table area',
                'type'                  => Controls_Manager::REPEATER,
                'default'               => [
                    [
                        'table_header_content' => esc_html__( 'Header Col 1 ', 'elementskit' ),
                    ],
                    [
                        'table_header_content' => esc_html__( 'Header Col 2', 'elementskit' ),
                    ],
                    [
                        'table_header_content' => esc_html__( 'Header Col 3', 'elementskit' ),
                    ],
                ],
                'fields'                => $repeater_header->get_controls(),
                'title_field'           => '{{{ table_header_content }}}',
                'condition' => [
                    'ekit_table_data_type'   => 'custom',
                ],
            ]
        );
        $this->add_control(
            'ekit_table_csv_type',
            [
                'label'     => esc_html__('File Type', 'elementskit'),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    'file'                    => esc_html__('Upload File', 'elementskit'),
                    'url'                     => esc_html__('Remote File URL', 'elementskit'),
                    'google_sheet_viewer_url' => esc_html__('Google Sheet Viewer URL', 'elementskit')
                ],
                'default'   => 'file',
                'condition' => [
                    'ekit_table_data_type'   => 'csv',
                ],
            ]);

        $this->add_control(
            'ekit_table_upload_csv',
            [
                'label'     => esc_html__('Upload CSV File', 'elementskit'),
                'type'      => Controls_Manager::MEDIA,
                'media_type'=> array(),
                'condition' => [
                    'ekit_table_csv_type'    => 'file',
                    'ekit_table_data_type'   => 'csv',
				],
				'dynamic'    => [
					'active' => true,
				],
            ]);
        $this->add_control(
            'ekit_table_csv_url',
            [
                'label'         => esc_html__( 'Enter a CSV File URL', 'elementskit' ),
                'type'          => Controls_Manager::URL,
                'show_external' => false,
                'label_block'   => true,
                'default'       => [
                    'url' => Handler::get_url()  . 'assets/table.csv',
                ],
                'condition' => [
                    'ekit_table_data_type'   => 'csv',
                    'ekit_table_csv_type'    => 'url'
				],
				'dynamic'        => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
            'ekit_table_google_sheet_viewer_url',
            [
                'label'         => esc_html__( 'Enter a Google Sheet URL', 'elementskit' ),
                'type'          => Controls_Manager::URL,
                'show_external' => false,
                'label_block'   => true,
                'condition' => [
                    'ekit_table_data_type'   => 'csv',
                    'ekit_table_csv_type'    => 'google_sheet_viewer_url',
				],
				'dynamic'        => [
					'active' => true,
				],
            ]
        );
        $this->add_control(
            'header_align',
            [
                'label'   => esc_html__( 'Header Alignment', 'elementskit' ),
                'type'    => Controls_Manager::CHOOSE,
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
                'default'   => 'center',
                'selectors' => [
                    '{{WRAPPER}} .bdt-table th, {{WRAPPER}} .ekit_table_item_container, {{WRAPPER}} .ekit_table table.dataTable thead th, {{WRAPPER}} .ekit_table.ekit_table_data_type-csv table.dataTable tbody tr td' => 'text-align: {{VALUE}};',
                ],
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'section_body',
            [
                'label'                 => esc_html__( 'Body Content', 'elementskit' ),
                'condition' => [
                    'ekit_table_data_type'   => 'custom',
                ],
            ]
        );

        $repeater_body = new Repeater();

        $repeater_body->add_control(
            'ekit_table_row', [
                'label' => esc_html__( 'New Row', 'elementskit' ),
                'type' => Controls_Manager::SWITCHER,
                'label_off' => esc_html__( 'No', 'elementskit' ),
                'label_on' => esc_html__( 'Yes', 'elementskit' ),
                'return_value' => esc_html__( 'Row', 'elementskit' ),
            ]
        );

        $repeater_body->add_control(
            'cell_text',
            [
                'label'                 => esc_html__( 'Text', 'elementskit' ),
                'type'                  => Controls_Manager::WYSIWYG,
                'placeholder'           => '',
                'default'               => '',

            ]
        );

        $repeater_body->add_control(
            'body_cell_setting_url',
            [
                'label' => esc_html__('Add a url? ', 'elementskit'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' =>esc_html__( 'Yes', 'elementskit' ),
                'label_off' =>esc_html__( 'No', 'elementskit' ),
            ]
        );

        $repeater_body->add_control(
			'body_cell_url',
			[
				'label' =>esc_html__( 'URL', 'elementskit' ),
				'type' => Controls_Manager::URL,
				'placeholder' =>esc_url('https://wpmet.com'),
                'condition' => [
                    'body_cell_setting_url' => 'yes'
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

        $repeater_body->add_control(
            'body_cell_bg_color',
            [
                'label'     => esc_html__( 'Background Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit_table table.dataTable tbody {{CURRENT_ITEM}}' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $repeater_body->add_control(
            'body_cell_text_color',
            [
                'label'     => esc_html__( 'Text Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit_table table.dataTable tbody {{CURRENT_ITEM}} .ekit_table_body_container' => 'color: {{VALUE}};',
                ],
            ]
        );

        $repeater_body->add_control(
            'body_cell_icon_type',
            [
                'label'                 => esc_html__( 'Icon Type', 'elementskit' ),
                'label_block'           => false,
                'type'                  => Controls_Manager::CHOOSE,
                'options'               => [
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
                'default'               => 'none'
            ]
        );

        $repeater_body->add_control(
            'body_cell_icons',
            [
                'label'                 => __( 'Icon', 'elementskit' ),
                'type'                  => Controls_Manager::ICONS,
                'fa4compatibility'      => 'body_cell_icon',
                'default' => [
                    'value' => '',
                ],
                'condition'             => [
                    'body_cell_icon_type'       => 'icon',
                ],
            ]
        );

        $repeater_body->add_control(
            'body_cell_icon_image',
            [
                'label'                 => esc_html__( 'Image', 'elementskit' ),
                'type'                  => Controls_Manager::MEDIA,
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                    'id'    => -1
                ],
                'condition'             => [
                    'body_cell_icon_type'       => 'image',
                ],
				'dynamic'               => [
					'active' => true,
				],
            ]
        );

        $repeater_body->add_control(
            'cell_icon_position',
            [
                'label'                 => esc_html__( 'Icon Position', 'elementskit' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'before',
                'options'               => [
                    'before'    => esc_html__( 'Before', 'elementskit' ),
                    'after'     => esc_html__( 'After', 'elementskit' ),
                    'top'       => esc_html__( 'top', 'elementskit' ),
                ],
                'condition'             => [
                    'body_cell_icon_type!'      => 'none',
                ],
            ]
        );

        $repeater_body->add_control('ekit_tbody_cell_icon_position',
            [
                'label'         => esc_html__('Icon Spacing', 'elementskit'),
                'type'          => Controls_Manager::SLIDER,
                'default'       => [
                    'unit'      => 'px',
                    'size'      => 5,
                ],
                'selectors'     => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.ekit_table_data_before span.body_cell_icon'  => 'margin-right: {{SIZE}}px',
                    '{{WRAPPER}} {{CURRENT_ITEM}}.ekit_table_data_after span.body_cell_icon'   => 'margin-left: {{SIZE}}px',
                ],
                'condition'             => [
                    'body_cell_icon_type!'   => 'none',
                ],
                'separator'     => 'below',
            ]
        );

        $repeater_body->add_control(
            'body_cell_icon_color',
            [
                'label'     => esc_html__( 'Icon Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .body_cell_icon' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'body_cell_icon_type'   => 'icon'
                ]
            ]
        );

        
        $this->add_control(
            'table_body_content',
            [
                'label'                 => 'Body table area',
                'type'                  => Controls_Manager::REPEATER,
                'default'               => [
                    [
                        'table_body_element'  => 'cell',
                        'cell_text'           => esc_html__( 'Column 1', 'elementskit' ),
                        'ekit_table_row'  => 'Row',
                    ],
                    [
                        'table_body_element'  => 'cell',
                        'cell_text'           => esc_html__( 'Column 2', 'elementskit' ),
                    ],
                    [
                        'table_body_element'  => 'cell',
                        'cell_text'           => esc_html__( 'Column 3', 'elementskit' ),
                    ],
                    [
                        'table_body_element'  => 'cell',
                        'cell_text'           => esc_html__( 'Column 1', 'elementskit' ),
                        'ekit_table_row'  => 'Row',
                    ],
                    [
                        'table_body_element'  => 'cell',
                        'cell_text'           => esc_html__( 'Column 2', 'elementskit' ),
                    ],
                    [
                        'table_body_element'  => 'cell',
                        'cell_text'           => esc_html__( 'Column 3', 'elementskit' ),
                    ],
                ],
                'fields'                => $repeater_body->get_controls(),
                'title_field'           => ' {{{ ekit_table_row }}}: {{{ cell_text }}}',
            ]
        );

        $this->add_control(
            'body_align',
            [
                'label'   => esc_html__( 'Body Alignment', 'elementskit' ),
                'type'    => Controls_Manager::CHOOSE,
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
                'default' => 'center',
            ]
        );
        $this->end_controls_section();


        $this->start_controls_section(
            'section_style_data_table',
            [
                'label'     => esc_html__( 'Table Settings', 'elementskit' ),

            ]
        );

        $this->add_control(
            'show_serial',
            [
                'label' => esc_html__( 'Display Serial Number', 'elementskit' ),
                'type'  => Controls_Manager::SWITCHER,
                'condition' => [
                    'ekit_table_data_type'  => 'custom'
                ]
            ]
        );

        $this->add_control(
            'show_serial_header_text',
            [
                'label'     => esc_html__( 'Serial Header Text', 'elementskit' ),
                'type'      => Controls_Manager::TEXT,
                'default'   => '#',
                'condition' => [
                    'show_serial'   => 'yes'
				],
				'dynamic'   => [
					'active' => true,
				],
            ]
        );

        // Search Bar
        $this->add_control(
            'show_search',
            [
                'label'   => esc_html__( 'Search', 'elementskit' ),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        // Search bar placeholder
        $this->add_control(
            'search_placeholder',
            [
                'label'                 => esc_html__( 'Search Placeholder', 'elementskit' ),
                'type'                  => Controls_Manager::TEXT,
                'default'               => 'Type Here To Search...',
                'condition'             => [
                    'show_search'   => 'yes',
                ],
                'frontend_available'    => true,
				'dynamic'               => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
            'fixed_header',
            [
                'label'   => esc_html__( 'Fixed Header', 'elementskit' ),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_ordering',
            [
                'label'   => esc_html__( 'Ordering', 'elementskit' ),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );
        $this->add_control(
            'responsive_toggle',
            [
                'label'   => esc_html__( 'Responsive', 'elementskit' ),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_pagination',
            [
                'label'   => esc_html__( 'Pagination', 'elementskit' ),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'ekit_table_navigation_style',  [
            'label' => esc_html__('Nav Style', 'elementskit'),
            'type' => Controls_Manager::SELECT,
            'default' => 'both',
            'options' => [
                'both'      => esc_html__('Both', 'elementskit'),
                'arrow'     => esc_html__('Arrow', 'elementskit'),
                'text'      => esc_html__('Text', 'elementskit'),
            ],
            'condition' => [
                'show_pagination'   => 'yes'
            ]
        ]
        );
        $this->add_control(
            'ekit_table_navigation_prev_arrows',
            [
                'label' => esc_html__( 'Prev Arrow Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_table_navigation_prev_arrow',
                'default' => [
                    'value' => 'icon icon-arrow-left',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_table_navigation_style' => ['arrow', 'both'],
                    'show_pagination'   => 'yes'
                ]
            ]
        );
        $this->add_control(
            'ekit_table_navigation_next_arrows',
            [
                'label' => esc_html__( 'Next Arrow Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_table_navigation_next_arrow',
                'default' => [
                    'value' => 'icon icon-arrow-right',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_table_navigation_style' => ['arrow', 'both'],
                    'show_pagination'   => 'yes'
                ],
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'ekit_table_navigation_prev_text', [
                'label' => esc_html__('Prev Text', 'elementskit'),
                'type' => Controls_Manager::TEXT,
                'default'   => 'Prev',
                'label_block' => true,
                'condition' => [
                    'ekit_table_navigation_style'   => ['text', 'both'],
                    'show_pagination'               => 'yes'
                ],
                'separator' => 'before',
				'dynamic' => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
            'ekit_table_navigation_next_text', [
                'label' => esc_html__('Next Text', 'elementskit'),
                'type' => Controls_Manager::TEXT,
                'default'   => 'Next',
                'label_block' => true,
                'condition' => [
                    'ekit_table_navigation_style'   => ['text', 'both'],
                    'show_pagination'               => 'yes'
                ],
                'separator' => 'after',
				'dynamic' => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
            'show_info',
            [
                'label'   => esc_html__( 'Info', 'elementskit' ),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes'
            ]
        );

        // Info text
        $this->add_control(
            'info_text',
            [
                'label'                 => esc_html__( 'Info Text', 'elementskit' ),
                'label_block'           => true,
                'type'                  => Controls_Manager::TEXT,
                'default'               => 'Showing _START_ to _END_ of _TOTAL_ entries',
                'condition'             => [
                    'show_info'   => 'yes',
                ],
                'frontend_available'    => true,
				'dynamic'               => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
            'show_entries',
            [
                'label'   => esc_html__( 'Entries', 'elementskit' ),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'description' => 'This option will visible entries.',
            ]
        );

        // Entries text
        $this->add_control(
            'entries_text',
            [
                'label'                 => esc_html__( 'Entries Text', 'elementskit' ),
                'type'                  => Controls_Manager::TEXT,
                'default'               => 'Show _MENU_ entries',
                'condition'             => [
                    'show_entries'   => 'yes',
                ],
                'frontend_available'    => true,
				'dynamic'               => [
					'active' => true,
				],
            ]
        );

        $this->add_control(
            'show_button',
            [
                'label'   => esc_html__( 'Button', 'elementskit' ),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'show_entries!' => 'yes'
                ],
                'description' => 'This option will visible Copy, excel and CSV',
            ]
        );

        $this->add_control(
			'ekit_data_per_page',
			array(
				'label'      => esc_html__( 'Show item per page', 'elementskit' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px'
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 100,
					),
                ),
                'default' => [
					'unit' => 'px',
					'size' => 10,
				],
			)
		);

        $this->end_controls_section();

        // WRAPPER
        $this->start_controls_section(
            'section_style_wrapper',
            [
                'label' => esc_html__('Wrapper', 'elementskit'),
                'tab'   => Controls_Manager::TAB_STYLE
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_wrapper_bg_color',
				'selector' => '{{WRAPPER}} .dataTables_wrapper',
            )
        );

        $this->add_responsive_control(
            'ekit_wrapper_padding',
            [
                'label'      => esc_html__( 'Padding', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top'    => 0,
                    'bottom' => 0,
                    'left'   => 0,
                    'right'  => 0,
                    'unit'   => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_wrapper_margin',
            [
                'label'      => esc_html__( 'Margin', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top'    => 0,
                    'bottom' => 0,
                    'left'   => 0,
                    'right'  => 0,
                    'unit'   => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_wrapper_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'selector'    => '{{WRAPPER}} .dataTables_wrapper',
			]
        );
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
			  'name' => 'ekit_wrapper_box_shadow',
			  'selector' => '{{WRAPPER}} .dataTables_wrapper',
			]
		);

        $this->end_controls_section();

        // Header
        $this->start_controls_section(
            'section_style_header',
            [
                'label' => esc_html__( 'Header', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'header_background',
				'default' => '#6E5BDE',
				'selector' => '{{WRAPPER}} .bdt-table th, {{WRAPPER}} .ekit_table table.dataTable thead th',
            )
        );

        $this->add_control(
            'header_color',
            [
                'label'     => esc_html__( 'Text Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .bdt-table th, {{WRAPPER}} .ekit_table table.dataTable thead th' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .ekit_table table.dataTable thead th .ekit-table-icon svg path'    => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_header_border_style',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .bdt-table th, {{WRAPPER}} .ekit_table table.dataTable thead th',
			]
		);

        $this->add_responsive_control(
            'header_padding',
            [
                'label'      => esc_html__( 'Padding', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top'    => 17,
                    'bottom' => 17,
                    'left'   => 17,
                    'right'  => 17,
                    'unit'   => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-table th, {{WRAPPER}} .ekit_table table.dataTable thead th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_table_header_icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
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
                    '{{WRAPPER}} .ekit_table table.dataTable thead th .ekit-table-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit_table table.dataTable thead th .ekit-table-icon svg' => 'max-width: {{SIZE}}{{UNIT}}; height: auto',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_table_header_typography',
                'label' =>esc_html__( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit_table table.dataTable thead th',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_header_image',
            [
                'label' => esc_html__( 'Header Image', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
			'ekit_header_image_height_width',
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
			'ekit_table_header_image_height',
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
					'{{WRAPPER}} .ekit-table-icon img' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_header_image_height_width' => 'yes'
				]
			]
		);
		
		$this->add_responsive_control(
			'ekit_table_header_image_width',
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
					'{{WRAPPER}} .ekit-table-icon img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_header_image_height_width' => 'yes'
				]
			]
		);

        $this->add_responsive_control(
			'ekit_table_header_image_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ekit-table-icon img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
        );
        
        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_table_header_image_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .ekit-table-icon img',
			]
		);


        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_body',
            [
                'label' => esc_html__( 'Body', 'elementskit' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'ekit_cell_border_style',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .bdt-table td, {{WRAPPER}} .ekit_table table.dataTable tbody tr td',
			]
		);

        $this->add_responsive_control(
            'cell_padding',
            [
                'label'      => esc_html__( 'Cell Padding', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top'    => 17,
                    'bottom' => 17,
                    'left'   => 17,
                    'right'  => 17,
                    'unit'   => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-table td, {{WRAPPER}} .ekit_table table.dataTable tbody tr td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'after',
            ]
        );

        $this->add_responsive_control(
            'ekit_table_body_icon_size',
            [
                'label' => esc_html__( 'Icon Size', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
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
                    '{{WRAPPER}} table.dataTable tbody .ekit_table_body_container .body_cell_icon i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} table.dataTable tbody .ekit_table_body_container .body_cell_icon svg' => 'max-width: {{SIZE}}{{UNIT}}; height: auto',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_table_body_typography',
                'label' =>esc_html__( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit_table table.dataTable tbody .ekit_table_body_container',
            ]
        );

        $this->start_controls_tabs('tabs_body_style');

        $this->start_controls_tab(
            'tab_normal',
            [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
        );

        $this->add_control(
            'normal_background',
            [
                'label'     => esc_html__( 'Background', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#fff',
                'selectors' => [
                    '{{WRAPPER}} .bdt-table td, {{WRAPPER}} table.dataTable tbody td' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'normal_color',
            [
                'label'     => esc_html__( 'Text Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-table td, {{WRAPPER}} table.dataTable tbody .ekit_table_body_container' => 'color: {{VALUE}};',
                    '{{WRAPPER}} table.dataTable tbody .ekit_table_body_container .body_cell_icon svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_hover',
            [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
        );

        $this->add_control(
            'row_hover_background',
            [
                'label'     => esc_html__( 'Background', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table table tr:hover .ekit_table_body_container, {{WRAPPER}} .ekit-wid-con table.dataTable tbody tr:hover td, {{WRAPPER}} .ekit_table table.dataTable tbody tr.odd:hover td' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'row_hover_text_color',
            [
                'label'     => esc_html__( 'Text Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-table .bdt-table table tr:hover .ekit_table_body_container, {{WRAPPER}} .ekit-wid-con table.dataTable tbody tr:hover .ekit_table_body_container span, {{WRAPPER}} .ekit_table table.dataTable tbody tr.odd:hover .ekit_table_body_container span, {{WRAPPER}} table.dataTable tbody tr:hover .ekit_table_body_container, {{WRAPPER}} table.dataTable tbody tr.even:hover .ekit_table_body_container' => 'color: {{VALUE}};',
                    '{{WRAPPER}} table.dataTable tbody tr:hover .ekit_table_body_container .body_cell_icon svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_stripe',
            [
                'label'     => esc_html__( 'Stripe', 'elementskit' ),
            ]
        );

        $this->add_control(
            'stripe_background',
            [
                'label'     => esc_html__( 'Background', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-table .even td, {{WRAPPER}} .ekit_table table.dataTable tbody tr.even td' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'stripe_color',
            [
                'label'     => esc_html__( 'Text Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-table .even td, {{WRAPPER}} .ekit_table table.dataTable tbody tr.even .ekit_table_body_container span, {{WRAPPER}} table.dataTable tbody tr.even .ekit_table_body_container' => 'color: {{VALUE}};',
                    '{{WRAPPER}} table.dataTable tbody tr.even .ekit_table_body_container .body_cell_icon svg path' => 'stroke: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_body_image',
            [
                'label' => esc_html__( 'Body Image', 'elementskit' ),
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
			'ekit_table_image_height',
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
					'{{WRAPPER}} .body_cell_icon img' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_use_category_image_height_width' => 'yes'
				]
			]
		);
		
		$this->add_responsive_control(
			'ekit_table_image_width',
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
					'{{WRAPPER}} .body_cell_icon img' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ekit_use_category_image_height_width' => 'yes'
				]
			]
		);

        $this->add_responsive_control(
			'ekit_table_image_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .body_cell_icon img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_table_image_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .body_cell_icon img',
			]
		);


        $this->end_controls_section();

        
        // button
        $this->start_controls_section(
            'ekit_table_button_style',
            [
                'label'     => esc_html__( 'Button', 'elementskit' ),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_entries!'   => 'yes',
                    'show_button'     => 'yes'
                ]
            ]
        );


     
        // btn general settings
        $this->add_control(
            'ekit_table_btn_general_settings',
            [
                'label' => esc_html__( 'General Settings', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'ekit_table_btn_text_padding',
            [
                'label' =>esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} button.dt-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_table_btn_text_margin',
            [
                'label' =>esc_html__( 'Margin', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} button.dt-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_table_btn_typography',
                'label' =>esc_html__( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} button.dt-button',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ekit_table_btn_shadow',
                'selector' => '{{WRAPPER}} button.dt-button',
            ]
        );

        $this->start_controls_tabs( 'ekit_table_btn_tabs_style' );

            $this->start_controls_tab(
                'ekit_table_btn_tabnormal',
                [
                    'label' =>esc_html__( 'Normal', 'elementskit' ),
                ]
            );

            $this->add_control(
                'ekit_table_btn_text_color',
                [
                    'label' =>esc_html__( 'Text Color', 'elementskit' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} button.dt-button, {{WRAPPER}} .ekit_table button.dt-button:active, {{WRAPPER}} .ekit_table button.dt-button:focus' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                array(
                    'name'     => 'ekit_table_btn_bg_color',
                    'selector' => '{{WRAPPER}} button.dt-button, {{WRAPPER}} .ekit_table button.dt-button:active, {{WRAPPER}} .ekit_table button.dt-button:focus',
                )
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'ekit_table_btn_tab_button_hover',
                [
                    'label' =>esc_html__( 'Hover', 'elementskit' ),
                ]
            );

            $this->add_control(
                'ekit_table_btn_hover_color',
                [
                    'label' =>esc_html__( 'Text Color', 'elementskit' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#CCCCCC',
                    'selectors' => [
                        '{{WRAPPER}} button.dt-button:hover' => 'color: {{VALUE}} !important;',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                array(
                    'name'     => 'ekit_table_btn_bg_hover_color',
                    'selector' => '{{WRAPPER}} button.dt-button:hover',
                )
            );

            $this->end_controls_tab();
        $this->end_controls_tabs();
        // btn general settings

        // btn border settings
        $this->add_control(
            'ekit_table_btn_border_settings',
            [
                'label' => esc_html__( 'Border Settings', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
			'ekit_table_btn_border_style',
			[
				'label'     => esc_html_x( 'Border Type', 'Border Control', 'elementskit' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'solid',
				'options' => [
					'none' => esc_html__( 'None', 'elementskit' ),
					'solid' => esc_html_x( 'Solid', 'Border Control', 'elementskit' ),
					'double' => esc_html_x( 'Double', 'Border Control', 'elementskit' ),
					'dotted' => esc_html_x( 'Dotted', 'Border Control', 'elementskit' ),
					'dashed' => esc_html_x( 'Dashed', 'Border Control', 'elementskit' ),
					'groove' => esc_html_x( 'Groove', 'Border Control', 'elementskit' ),
				],
				'selectors' => [
					'{{WRAPPER}} .ekit_table  button.dt-button' => 'border-style: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'ekit_table_btn_border_dimensions',
			[
				'label' => esc_html_x( 'Width', 'Border Control', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .ekit_table  button.dt-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'ekit_table_btn_border_style!'  => 'none'
                ]
			]
		);
		$this->start_controls_tabs( 
            'xs_tabs_button_border_style',
            [
                'condition'  => [
                    'ekit_table_btn_border_style!'  => 'none'
                ]
            ]
        );
		$this->start_controls_tab(
			'ekit_table_btn_tab_border_normal',
			[
				'label' =>esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_table_btn_border_color',
			[
				'label' => esc_html_x( 'Color', 'Border Control', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ekit_table button.dt-button, {{WRAPPER}} .ekit_table button.dt-button:active' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'ekit_table_btn_tab_button_border_hover',
			[
				'label' =>esc_html__( 'Hover', 'elementskit' ),
			]
		);
		$this->add_control(
			'ekit_table_btn_hover_border_color',
			[
				'label' => esc_html_x( 'Color', 'Border Control', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ekit_table  button.dt-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);
        $this->end_controls_tab();
        
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'ekit_table_btn_border_radius',
			[
				'label' =>esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'default' => [
					'top' => '',
					'right' => '',
					'bottom' => '' ,
					'left' => '',
				],
				'selectors' => [
					'{{WRAPPER}} button.dt-button' =>  'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	

        // btn shadow settings
        $this->add_control(
            'ekit_table_btn_shadow_settings',
            [
                'label' => esc_html__( 'Shadow Settings', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
			  'name' => 'ekit_table_btn_box_shadow_group',
			  'selector' => '{{WRAPPER}} button.dt-button',
			]
		);
        // end btn shadow settings



        $this->end_controls_section();
        // end button

        // Search section
        $this->start_controls_section(
            'ekit_section_search_style',
            [
                'label'     => esc_html__('Search', 'elementskit'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_search'   => 'yes'
                ]
            ]
        );

        $this->add_control(
            'ekit_table_search_icon_heading',
            [
                'label'     => esc_html__( 'Icon:', 'elementskit' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'ekit_table_search_icon_color',
            [
                'label'     => esc_html__( 'Icon Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'default'   => '#cacaca',
                'selectors' => [
                    '{{WRAPPER}} .ekit-table-search-label i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_table_search_icon_font_size',
            array(
                'label'      => esc_html__( 'Font Size', 'elementskit' ),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => array(
                    'px', 'em', 'rem',
                ),
                'range'      => array(
                    'px' => array(
                        'min' => 1,
                        'max' => 100,
                    ),
                ),
                'selectors'  => array(
                    '{{WRAPPER}} .ekit-table-search-label i' => 'font-size: {{SIZE}}{{UNIT}}',
                ),
            )
        );

        $this->add_responsive_control(
            'ekit_table_search_icon_padding',
            [
                'label'      => esc_html__( 'Padding', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .ekit-table-search-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_table_search_icon_margin',
            [
                'label'      => esc_html__( 'Margin', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'selectors'  => [
                    '{{WRAPPER}} .ekit-table-search-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        

        $this->add_control(
            'ekit_table_search_input_heading',
            [
                'label'     => esc_html__( 'Input:', 'elementskit' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_responsive_control(
			'ekit_table_search_input_width',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%' ],
                'default'   => [
                    'size'  => 425,
                    'unit'  => 'px'
                ],
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
					'{{WRAPPER}} .dataTables_filter input' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_responsive_control(
            'ekit_table_search_input_padding',
            [
                'label'      => esc_html__( 'Padding', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top'    => 9,
                    'bottom' => 9,
                    'left'   => 20,
                    'right'  => 50,
                    'unit'   => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_filter input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_table_search_input_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'selector'    => '{{WRAPPER}} .dataTables_filter input',
			]
        );

        $this->add_responsive_control(
			'ekit_table_search_input_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dataTables_filter input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
        );
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
			  'name' => 'ekit_table_search_input_border_shadow',
			  'selector' => '{{WRAPPER}} .dataTables_filter input',
			]
		);

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_table_search_input_text_typography',
                'label' =>esc_html__( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} .dataTables_filter input',
            ]
        );

        $this->start_controls_tabs(
            'ekit_table_search_input_tabs'
        );

            $this->start_controls_tab(
                'ekit_table_search_input_normal_tab',
                [
                    'label'     => esc_html__( 'Normal', 'elementskit' ),
                ]
            );

            $this->add_control(
                'ekit_table_search_input_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dataTables_filter input' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'ekit_table_search_input_background_color',
                [
                    'label'     => esc_html__( 'Background Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dataTables_filter input' => 'background-color: {{VALUE}};',
                    ],
                ]
            );


            $this->end_controls_tab();


            $this->start_controls_tab(
                'ekit_table_search_input_hover_tab',
                [
                    'label'     => esc_html__( 'Hover', 'elementskit' ),
                ]
            );

            $this->add_control(
                'ekit_table_search_input_hover_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dataTables_filter input:hover' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'ekit_table_search_input_hover_background_color',
                [
                    'label'     => esc_html__( 'Background Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dataTables_filter input:hover' => 'background-color: {{VALUE}};',
                    ],
                ]
            );


            $this->end_controls_tab();

            $this->start_controls_tab(
                'ekit_table_search_input_focus_tab',
                [
                    'label'     => esc_html__( 'Focus', 'elementskit' ),
                ]
            );

            $this->add_control(
                'ekit_table_search_input_focus_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dataTables_filter input:focus' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'ekit_table_search_input_focus_background_color',
                [
                    'label'     => esc_html__( 'Background Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dataTables_filter input:focus' => 'background-color: {{VALUE}};',
                    ],
                ]
            );
    
            $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
            'ekit_table_search_input_placeholder_heading',
            [
                'label'     => esc_html__( 'Input Placeholder:', 'elementskit' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_table_search_input_placeholder_typo',
                'label' =>esc_html__( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} .ekit_table .dataTables_wrapper .dataTables_filter input::placeholder',
            ]
        );

        $this->add_control(
            'ekit_table_search_input_placeholder_color',
            [
                'label'     => esc_html__( 'Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit_table .dataTables_wrapper .dataTables_filter input::placeholder' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();


         // Info section
         $this->start_controls_section(
            'ekit_section_info_style',
            [
                'label' => esc_html__('Info', 'elementskit'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_info' => 'yes'
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_section_info_style_normal_color_typography',
                'label' =>esc_html__( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} .dataTables_info',
            ]
        );

        $this->start_controls_tabs(
            'ekit_section_info_style_tabs'
        );

            $this->start_controls_tab(
                'ekit_section_info_style_normal_tab',
                [
                    'label' => esc_html__('Normal', 'elementskit')
                ]
            );

            $this->add_control(
                'ekit_section_info_style_normal_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dataTables_info' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'ekit_section_info_style_hover_tab',
                [
                    'label' => esc_html__('Hover', 'elementskit')
                ]
            );

            $this->add_control(
                'ekit_section_info_style_hover_color',
                [
                    'label'     => esc_html__( 'Color', 'elementskit' ),
                    'type'      => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .dataTables_info:hover' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_tab();

        $this->end_controls_tabs();


        $this->end_controls_section();

        //  Entries
        $this->start_controls_section(
            'ekit_section_entries_style',
            [
                'label' => esc_html__('Entries', 'elementskit'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_entries' => 'yes'
                ]
            ]
        );


        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_entries_background',
				'default' => '#6E5BDE',
				'selector' => '{{WRAPPER}} .dataTables_length label, {{WRAPPER}} .dataTables_length select',
            )
        );

        $this->add_responsive_control(
            'ekit_entries_padding',
            [
                'label' =>esc_html__( 'Padding', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_length select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_entries_margin',
            [
                'label' =>esc_html__( 'Margin', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_length' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_entries_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'selector'    => '{{WRAPPER}} .dataTables_length label',
			]
        );

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
			  'name' => 'ekit_entries_box_shadow',
			  'selector' => '{{WRAPPER}} .dataTables_length label',
			]
		);

        $this->add_control(
            'ekit_section_entries_label_heading',
            [
                'label' => esc_html__( 'Label:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_section_entries_label_typo',
                'label' =>esc_html__( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} .dataTables_length label',
            ]
        );

        $this->add_control(
            'ekit_section_entries_label_color',
            [
                'label'     => esc_html__( 'Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dataTables_length label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_section_entries_select_heading',
            [
                'label' => esc_html__( 'Select:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ekit_section_entries_select_typography',
                'label' =>esc_html__( 'Typography', 'elementskit' ),
                'selector' => '{{WRAPPER}} .dataTables_length select',
            ]
        );

        $this->add_control(
            'ekit_section_entries_select_color',
            [
                'label'     => esc_html__( 'Color', 'elementskit' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dataTables_length select' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_section_entries_select_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'selector'    => '{{WRAPPER}} .dataTables_length select',
			]
        );


        $this->end_controls_section();

        // Navigation
        $this->start_controls_section(
            'ekit_tbl_nav_style_section',
            [
                'label' => esc_html__('Navigation', 'elementskit'),
                'tab'   => Controls_Manager::TAB_STYLE
            ]
        );

        $this->add_responsive_control(
            'ekit_tbl_nav_general',
            [
                'label' => esc_html__( 'General:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_tbl_nav_bg_color',
				'selector' => '{{WRAPPER}} .dataTables_paginate',
            )
        );

        $this->add_responsive_control(
            'ekit_tbl_nav_padding',
            [
                'label'      => esc_html__( 'Padding', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top'    => 0,
                    'bottom' => 0,
                    'left'   => 0,
                    'right'  => 0,
                    'unit'   => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_tbl_nav_margin',
            [
                'label'      => esc_html__( 'Margin', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top'    => 0,
                    'bottom' => 0,
                    'left'   => 0,
                    'right'  => 0,
                    'unit'   => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_tbl_nav_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'selector'    => '{{WRAPPER}} .dataTables_paginate',
			]
        );

        $this->add_responsive_control(
			'ekit_tbl_nav_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dataTables_paginate' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
			  'name' => 'ekit_tbl_nav_box_shadow',
			  'selector' => '{{WRAPPER}} .dataTables_paginate',
			]
        );
        

        $this->add_responsive_control(
            'ekit_tbl_nav_next_prev_btn',
            [
                'label' => esc_html__( 'Next and Previous Button Settings:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_tbl_nav_btn_bg_color',
				'selector' => '{{WRAPPER}} .dataTables_paginate > .paginate_button',
            )
        );

        $this->add_responsive_control(
            'ekit_tbl_nav_btn_prev_padding',
            [
                'label'      => esc_html__( 'Prev BTN Padding', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top'    => 0,
                    'bottom' => 0,
                    'left'   => 0,
                    'right'  => 15,
                    'unit'   => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate > .paginate_button.previous' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_tbl_nav_btn_next_padding',
            [
                'label'      => esc_html__( 'Next BTN Padding', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top'    => 0,
                    'bottom' => 0,
                    'left'   => 15,
                    'right'  => 0,
                    'unit'   => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate > .paginate_button.next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_tbl_nav_btn_margin',
            [
                'label'      => esc_html__( 'Margin', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top'    => 0,
                    'bottom' => 0,
                    'left'   => 25,
                    'right'  => 25,
                    'unit'   => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate > .paginate_button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_tbl_nav_btn_border_type',
            [
                'label' => esc_html_x( 'Border Type', 'Border Control', 'elementskit' ),
                'type' => Controls_Manager::SELECT,
                'default'   => 'none',
                'options' => [
                    'none' => esc_html__( 'None', 'elementskit' ),
                    'solid' => esc_html_x( 'Solid', 'Border Control', 'elementskit' ),
                    'double' => esc_html_x( 'Double', 'Border Control', 'elementskit' ),
                    'dotted' => esc_html_x( 'Dotted', 'Border Control', 'elementskit' ),
                    'dashed' => esc_html_x( 'Dashed', 'Border Control', 'elementskit' ),
                    'groove' => esc_html_x( 'Groove', 'Border Control', 'elementskit' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate > .paginate_button' => 'border-style: {{VALUE}} !important;',
                ],
            ]
        );
        $this->add_control(
            'ekit_tbl_nav_btn_border_width',
            [
                'label' => esc_html_x( 'Width', 'Border Control', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate > .paginate_button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                    '{{WRAPPER}} .dataTables_paginate > .paginate_button.previous '  => 'border-left: none !important;border-top: none !important;border-bottom: none !important;',
                    '{{WRAPPER}} .dataTables_paginate > .paginate_button.next '  => 'border-right: none !important;border-top: none !important;border-bottom: none !important;'
                ],
                'condition' => [
                    'ekit_tbl_nav_btn_border_type!' =>  'none',
                ]
            ]
        );

        $this->add_control(
            'ekit_tbl_nav_btn_border_color',
            [
                'label' => esc_html_x( 'Border Color', 'Border Control', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate > .paginate_button' => 'border-color: {{VALUE}} !important;',
                ],
                'condition' => [
                    'ekit_tbl_nav_btn_border_type!' =>  'none',
                ]
            ]
        );

        $this->add_responsive_control(
			'ekit_tbl_nav_btn_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'elementskit' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .dataTables_paginate > .paginate_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
        );
        
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
			  'name' => 'ekit_tbl_nav_btn_box_shadow',
              'selector' => '{{WRAPPER}} .dataTables_paginate > .paginate_button',
              'separator' => 'after',
			]
		);


        $this->add_control(
            'ekit_tbl_nav_text_color',
            [
                'label' =>esc_html__( 'Button Text Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-tbl-pagi-nav' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_tbl_nav_text_typo',
                'selector'	 => '{{WRAPPER}} .ekit-tbl-pagi-nav',
                'fields_options' => [
                    'typography' => [
                        'label' => esc_html__( 'Button Text Typography', 'elementskit' ),
                    ],
                ],
            ]
        );

        $this->add_control(
            'ekit_tbl_nav_icon_color',
            [
                'label' =>esc_html__( 'Icon Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .ekit-tbl-pagi-nav-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'ekit_tbl_nav_icon_font_size',
			[
				'label' => esc_html__( 'Icon Font Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
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
					'{{WRAPPER}} .ekit-tbl-pagi-nav-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
        
        $this->add_responsive_control(
            'ekit_tbl_nav_icon_spacing',
            [
                'label' => esc_html__( 'Icon Spacing', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', '%'],
                'default' => [
                    'size' => 5,
                    'unit' => 'px',
                ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .ekit-tbl-pagi-nav-prev-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .ekit-tbl-pagi-nav-next-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_tbl_nav_number',
            [
                'label' => esc_html__( 'Number:', 'elementskit' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'after',
            ]
        );

        $this->add_control(
			'ekit_tbl_nav_number_use_width_height',
			[
				'label' => esc_html__( 'Use Width and Height', 'elementskit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'elementskit' ),
				'label_off' => esc_html__( 'Hide', 'elementskit' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

        $this->add_responsive_control(
            'ekit_tbl_nav_number_width',
            [
                'label' => esc_html__( 'Width', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px','%' ],
                'range' => [
                    'px'    => [
                        'min'   => 0,
                        'max'   => 500
                    ],
                    '%'     => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 35,
                ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate span .paginate_button' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tbl_nav_number_use_width_height'  => 'yes'
                ] 
            ]
        );

        $this->add_responsive_control(
            'ekit_tbl_nav_number_height',
            [
                'label' => esc_html__( 'Height', 'elementskit' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px','%' ],
                'range' => [
                    'px'    => [
                        'min'   => 0,
                        'max'   => 500
                    ],
                    '%'     => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 35,
                ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate span .paginate_button' => 'height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'ekit_tbl_nav_number_use_width_height'  => 'yes'
                ] 
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name'		 => 'ekit_tbl_nav_number_typo',
                'selector'	 => '{{WRAPPER}} .dataTables_paginate span .paginate_button.current, {{WRAPPER}}  .dataTables_paginate .ellipsis',
            ]
        );

        $this->add_responsive_control(
            'ekit_tbl_nav_number_padding',
            [
                'label'      => esc_html__( 'Padding', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top'    => 0,
                    'bottom' => 0,
                    'left'   => 0,
                    'right'  => 0,
                    'unit'   => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate span .paginate_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'ekit_tbl_nav_number_margin',
            [
                'label'      => esc_html__( 'Margin', 'elementskit' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'default'    => [
                    'top'    => 0,
                    'bottom' => 0,
                    'left'   => 2,
                    'right'  => 2,
                    'unit'   => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate span .paginate_button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'ekit_tbl_nav_number_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'elementskit' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em' ],
                'default'   => [
                    'top'       => 50,
                    'right'     => 50,
                    'bottom'    => 50,
                    'left'      => 50,
                    'unit'      => 'px'
                ],
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate span .paginate_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'ekit_tbl_nav_number_box_shadow',
                'label' => esc_html__( 'Box Shadow', 'elementskit' ),
                'selector' => '{{WRAPPER}} .dataTables_paginate span .paginate_button',
            ]
        );

        $this->start_controls_tabs('ekit_tbl_nav_number_tabs');

        $this->start_controls_tab(
            'ekit_tbl_nav_number_normal_tab',
            [
                'label' => esc_html('Normal', 'elementskit-lite')
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_tbl_nav_number_bg_color',
				'selector' => '{{WRAPPER}} .dataTables_paginate span .paginate_button',
            )
        );

        $this->add_control(
            'ekit_tbl_nav_number_color',
            [
                'label' => esc_html__( 'Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate span .paginate_button, {{WRAPPER}} .dataTables_paginate .ellipsis' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_tbl_nav_number_hover_tab',
            [
                'label' => esc_html('Hover', 'elementskit-lite')
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_tbl_nav_number_hover_bg_color',
				'selector' => '{{WRAPPER}} .dataTables_paginate span .paginate_button:hover',
            )
        );

        $this->add_control(
            'ekit_tbl_nav_number_hover_color',
            [
                'label' => esc_html__( 'Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate span .paginate_button:hover' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'ekit_tbl_nav_number_active_tab',
            [
                'label' => esc_html('Active', 'elementskit-lite')
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            array(
				'name'     => 'ekit_tbl_nav_number_active_bg_color',
				'selector' => '{{WRAPPER}} .dataTables_paginate span .paginate_button.current',
            )
        );

        $this->add_control(
            'ekit_tbl_nav_number_active_color',
            [
                'label' => esc_html__( 'Color', 'elementskit' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .dataTables_paginate span .paginate_button.current' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'ekit_tbl_nav_number_active_border',
				'label'       => esc_html__( 'Border', 'elementskit' ),
				'selector'    => '{{WRAPPER}} .dataTables_paginate span .paginate_button.current',
			]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->insert_pro_message();
    }
    protected function ekit_parse_csv_to_table($filename, $header=true) {

        $response = wp_remote_get($filename);

        if(200 === wp_remote_retrieve_response_code($response)) {

            $data = str_getcsv($response['body'], "\n");

            if(empty($data[0])) {
                return;
            }
            
            echo '<table class="display" style="width:100%">';
            echo '<thead><tr>';
            foreach(str_getcsv($data[0]) as $th) {
                echo "<th>$th</th>";
            }
            echo '</tr></thead>';
            
            unset($data[0]);
            
            echo '<tbody>';
            foreach($data as $tr) {
                $tr = str_getcsv($tr);
                echo '<tr>';
                foreach($tr as $td) {
                    echo '<td data-order="'.$td.'"><div class="ekit_table_body_container">'. $td .'</div></td>';
                }
                echo '</tr>';
            }
            echo '</tbody></table>';
            
            return;
        }
        echo '<p class="ekit-no-csv-file-found">'. esc_html__('Please provide an csv file', 'elementskit') .'</p>';
    }

    /**
     * It will output formated table data from csv file
     */
    protected function render_csv_data($url){
        $url_ext = pathinfo($url, PATHINFO_EXTENSION);

        ob_start();
        if($url_ext === 'csv'){
            echo \ElementsKit_Lite\Utils::render($this->ekit_parse_csv_to_table($url, true));
        } else {
            echo '<p class="ekit-no-csv-file-found">'. esc_html__('Please provide an csv file', 'elementskit') .'</p>';
        }
        return \ob_get_clean();
    }

    protected function render_google_sheet_data($url){
        $pattern = "/\/d\/(.+)\//";

        ob_start();
        if(preg_match($pattern, $url, $matches)) {
            $url = "https://docs.google.com/spreadsheets/d/".$matches[1]."/export?format=csv&id=".$matches[1];
            echo \ElementsKit_Lite\Utils::render($this->ekit_parse_csv_to_table($url, true));
        } else {
            echo '<p class="ekit-no-csv-file-found">'. esc_html__('Please provide an valid google sheet public url', 'elementskit') .'</p>';
        }
        return \ob_get_clean();
    }

    protected function render( ) {
        echo '<div class="ekit-wid-con" >';
        $this->render_raw();
        echo '</div>';
    }

    protected function render_raw( ) {

        $settings = $this->get_settings_for_display();
        extract($settings);


        // new icon
        $migrated_prev = isset( $settings['__fa4_migrated']['ekit_table_navigation_prev_arrows'] );
        // Check if its a new widget without previously selected icon using the old Icon control
        $is_new_prev = empty( $settings['ekit_table_navigation_prev_arrow'] );

        // new icon
        $migrated_next = isset( $settings['__fa4_migrated']['ekit_table_navigation_next_arrows'] );
        // Check if its a new widget without previously selected icon using the old Icon control
        $is_new_next = empty( $settings['ekit_table_navigation_next_arrow'] );

        $table_settings = [
            'fixedHeader'       => ($settings['fixed_header'] == 'yes') ? true : false,
            'search'            => ($settings['show_search'] == 'yes') ? true : false,
            'responsive'        => $settings['responsive_toggle'] == 'yes' ? true : false,
            'pagination'        => $settings['show_pagination'] == 'yes' ? true : false,
            'button'            => $settings['show_button'] == 'yes' ? true : false,
            'entries'            => $settings['show_entries'] == 'yes' ? true : false,
            'info'              => $settings['show_info'] == 'yes' ? true : false,
            'ordering'          => $settings['show_ordering'] == 'yes' ? true : false,
            'item_per_page'     => $settings['ekit_data_per_page']['size'] ? $settings['ekit_data_per_page']['size'] : false,
            'nav_style'         => $settings['ekit_table_navigation_style'] ? $settings['ekit_table_navigation_style'] : '',
            'prev_text'         => !empty(trim($settings['ekit_table_navigation_prev_text'])) ? trim($settings['ekit_table_navigation_prev_text']) : '',
            'next_text'         => !empty(trim($settings['ekit_table_navigation_next_text'])) ? trim($settings['ekit_table_navigation_next_text']) : '',
            'prev_arrow'         => ($is_new_prev || $migrated_prev) ? (!empty($settings['ekit_table_navigation_prev_arrows']) && $settings['ekit_table_navigation_prev_arrows']['library'] != 'svg' ? trim($settings['ekit_table_navigation_prev_arrows']['value']) : '') : trim($settings['ekit_table_navigation_prev_arrow']),
            'next_arrow'         => ($is_new_next || $migrated_next) ? (!empty($settings['ekit_table_navigation_prev_arrows']) && $settings['ekit_table_navigation_prev_arrows']['library'] != 'svg' ?  trim($settings['ekit_table_navigation_next_arrows']['value']) : '') : trim($settings['ekit_table_navigation_next_arrow'])
        ];
        $this->add_render_attribute('table', 'data-settings', wp_json_encode($table_settings));
        ?>

        <div class="ekit_table display  ekit_table_data_type-<?php echo esc_attr( $ekit_table_data_type ); ?>" <?php echo $this->get_render_attribute_string('table'); ?>>
                <?php
                
                if($ekit_table_data_type == 'custom'){

                    echo '<table id="ekit-table-container-'.$this->get_id().'" class="display dataTable" style="width:100%"><thead><tr>';
                    echo \ElementsKit_Lite\Utils::render($settings['show_serial'] === 'yes' ? "<th>". esc_html( $settings['show_serial_header_text'] ) ."</th>" : '');
                   foreach((array)$ekit_table_build_header as $head){
                       $id =  $head['_id'];
                       if ( $head['cell_icon_type'] != 'none' ) {
                           $this->add_render_attribute( 'headicon-' . $id, 'class', 'ekit-table-icon' );
                           $this->add_render_attribute( 'headicon-' . $id, 'class', 'ekit-table-icon-' . $head['cell_icon_position'] );
                       }

                       ?>
                            <th class="elementor-repeater-item-<?php echo esc_attr($head['_id']); ?>"><div class="ekit_table_item_container  ekit-table-container-<?php echo esc_attr($head['cell_icon_position']); ?> ">
                                <?php 
                                    echo esc_html( $head['table_header_content'] ); 

                                    if ( $head['cell_icon_type'] != 'none') { ?>
                                        <span <?php echo $this->get_render_attribute_string( 'headicon-' . $id ); ?>> <?php
                                        
                                            if ( $head['cell_icon_type'] == 'icon' && $head['cell_icons'] != '' ) {
                                                // new icon
                                                $migrated = isset( $head['__fa4_migrated']['cell_icons'] );
                                                // Check if its a new widget without previously selected icon using the old Icon control
                                                $is_new = empty( $head['cell_icon'] );
                                                if ( $is_new || $migrated ) {
                                                    // new icon
                                                    Icons_Manager::render_icon( $head['cell_icons'], [ 'aria-hidden' => 'true' ] );
                                                } else {
                                                    ?>
                                                    <i class="<?php echo esc_attr($head['cell_icon']); ?>" aria-hidden="true"></i>
                                                    <?php
                                                } 
                                            } elseif ( $head['cell_icon_type'] == 'image' && $head['cell_icon_image']['url'] != '' ) {
                                                echo \Elementskit_Lite\Utils::get_attachment_image_html($head, 'cell_icon_image', 'full' ); 
                                            }
                                        ?></span><?php
                                    }
                                ?>
                            </div></th>
                       <?php

                   }
                   echo ' </tr></thead><tbody>';

                   $count = 0;
                   $row_count = 1;
                    foreach((array)$table_body_content as $key => $bodycon){
                        //url
                        $enabledUrl = $bodycon['body_cell_setting_url'];
                        
                        if ( ! empty( $bodycon['body_cell_url'] ) ) {
                            $this->add_link_attributes( 'link-' . $key, $bodycon['body_cell_url'] );
                        }
                
                        // end url

                        if($bodycon['ekit_table_row'] == 'Row') {
                            echo "<tr>";
                            echo \ElementsKit_Lite\Utils::render($settings['show_serial'] === 'yes' ? '<td class="elementor-repeater-item-'. $bodycon['_id'] .' ekit_table_data_'. esc_attr( $bodycon['cell_icon_position'] ) .'"><div class="ekit_table_body_container ekit_table_data_'. esc_attr( $bodycon['cell_icon_position'] ) .' ekit_body_align_'. esc_attr( $settings['body_align'] ) .'">'. $row_count .'</div></td>' : '');
                            $row_count++;
                        }
                       

                        // Output: table data 
                        ?>
                            <td data-order="<?php echo wp_strip_all_tags(\ElementsKit_Lite\Utils::kses( $bodycon['cell_text']) ); ?>" class="elementor-repeater-item-<?php echo esc_attr($bodycon['_id']); ?> ekit_table_data_<?php echo esc_attr($bodycon['cell_icon_position']); ?>">
                                <?php if($enabledUrl === 'yes') : ?>
                                    <a <?php echo $this->get_render_attribute_string( 'link-'. $key ); ?>>
                                <?php endif; ?>

                                    <div class="ekit_table_body_container ekit_table_data_<?php echo esc_attr($bodycon['cell_icon_position']); ?> ekit_body_align_<?php echo esc_attr($settings['body_align']); ?>">
                                        <?php 
                                            
                                            echo \ElementsKit_Lite\Utils::kses( $bodycon['cell_text'] );

                                            if ( $bodycon['body_cell_icon_type'] != 'none') { ?>

                                                <span class="body_cell_icon body-cell-icon-position-<?php echo esc_attr( $bodycon['cell_icon_position']  ) ?>"> <?php
                     
                                                if ( $bodycon['body_cell_icon_type'] == 'icon' && $bodycon['body_cell_icons'] != '' ) {

                                                    // new icon
                                                    $migrated = isset( $bodycon['__fa4_migrated']['body_cell_icons'] );
                                                    // Check if its a new widget without previously selected icon using the old Icon control
                                                    $is_new = empty( $bodycon['body_cell_icon'] );
                                                    if ( $is_new || $migrated ) {
                                                        // new icon
                                                        Icons_Manager::render_icon( $bodycon['body_cell_icons'], [ 'aria-hidden' => 'true' ] );
                                                    } else {
                                                        ?>
                                                        <i class="<?php echo esc_attr($bodycon['body_cell_icon']); ?>" aria-hidden="true"></i>
                                                        <?php
                                                    }



                                                } elseif ( $bodycon['body_cell_icon_type'] == 'image' && $bodycon['body_cell_icon_image']['url'] != '' ) {
                                                    echo \Elementskit_Lite\Utils::get_attachment_image_html($bodycon, 'body_cell_icon_image', 'full' );
                                                }

                                                ?></span><?php
                                            }
                                        ?>
                                    </div>

                                <?php if($enabledUrl === 'yes') : ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                        <?php
                        
                        $count++;
                    }

                    // filled up empty table data with empty <td></td> tag
                    $headerCount = count($ekit_table_build_header);
                    if($count % $headerCount !== 0){
                        for($i = 0; $i < abs(($count % $headerCount) - $headerCount); $i++){
                            echo "<td></td>";
                        }
                    }
                    echo ' </tbody></table>';

            
                } else if($ekit_table_data_type == 'csv'){
                    // checking csv file type
                    if($ekit_table_csv_type === 'file'){

                        if($ekit_table_upload_csv['url'] != ''){
                            echo \ElementsKit_Lite\Utils::render($this->render_csv_data($ekit_table_upload_csv['url']));
                        }

                    } else if($ekit_table_csv_type === 'url'){
                        if($ekit_table_csv_url['url'] != ''){
                            echo \ElementsKit_Lite\Utils::render($this->render_csv_data($ekit_table_csv_url['url']));
                        }

                    } else if($ekit_table_csv_type === 'google_sheet_viewer_url') {
                        if($ekit_table_google_sheet_viewer_url['url'] != ''){
                            echo \ElementsKit_Lite\Utils::render($this->render_google_sheet_data($ekit_table_google_sheet_viewer_url['url']));
                        }
                    }
                    
                }
                ?>
        </div>



    <?php
    }
}
