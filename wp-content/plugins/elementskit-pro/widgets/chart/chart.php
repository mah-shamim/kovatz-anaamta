<?php

namespace Elementor;

use \Elementor\ElementsKit_Widget_Chart_Handler as Handler;
use \ElementsKit_Lite\Modules\Controls\Controls_Manager as ElementsKit_Controls_Manager;

if(!defined('ABSPATH')) {
	exit;
}

class ElementsKit_Widget_Chart extends Widget_Base {
	use \ElementsKit_Lite\Widgets\Widget_Notice;

	public $base;

	public function __construct( $data = [], $args = null ) {
		parent::__construct( $data, $args );
		$this->add_script_depends('chart-kit-js');
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
        return 'https://wpmet.com/doc/advanced-chart/';
    }

	protected function register_controls() {


		/*Data Section*/
		$this->start_controls_section(
			'ekit_chart_data_section', [
				'label' => esc_html__('Data ', 'elementskit'),
			]
		);

		// start repeter for lavel

		$chartRepeterCate = new Repeater();
		$chartRepeterCate->add_control(
			'ekit_chart_label', [
				'label'       => esc_html__('Name', 'elementskit'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('January', 'elementskit'),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'ekit_charts_labels_data',
			[
				'label'   => esc_html__('Categories', 'elementskit'),
				'type'    => Controls_Manager::REPEATER,
				'default' => [
					['ekit_chart_label' => esc_html__('January', 'elementskit')],
					['ekit_chart_label' => esc_html__('February', 'elementskit')],
					['ekit_chart_label' => esc_html__('March', 'elementskit')],

				],

				'fields'      => $chartRepeterCate->get_controls(),
				'title_field' => '{{{ ekit_chart_label }}}',
				'condition'   => ['ekit_chart_style' => ['bar', 'horizontalBar', 'line', 'radar']],
			]
		);

		// repeter 1
		$chartRepeter = new Repeater();
		$chartRepeter->add_control(
			'ekit_chart_data_label', [
				'label'       => esc_html__('Label', 'elementskit'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Label #1', 'elementskit'),
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
			]
		);
		$chartRepeter->add_control(
			'ekit_chart_data_set', [
				'label'       => esc_html__('Data', 'elementskit'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '10,23,15',
				'label_block' => true,
				'description' => esc_html__('Enter data values by "," separated(1). Example: 2,4,8,16,32 etc', 'elementskit'),
				'dynamic' => [
					'active' => true,
				],
			]
		);


		// start tabs section
		$chartRepeter->start_controls_tabs(
			'ekit_chart_data_bar_back_tab'
		);
		// start normal sections
		$chartRepeter->start_controls_tab(
			'ekit_chart_data_bar_back_normal',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$chartRepeter->add_control(
			'ekit_chart_data_bar_back_color', [
				'label'       => esc_html__('Background Color', 'elementskit'),
				'type'        => Controls_Manager::COLOR,
				'default'     => 'rgba(242,41,91,0.48)',
			]
		);

		$chartRepeter->add_control(
			'ekit_chart_data_bar_border_color', [
				'label'       => esc_html__('Border Color', 'elementskit'),
				'type'        => Controls_Manager::COLOR,
				'default'     => 'rgba(242,41,91,0.48)',
			]
		);

		$chartRepeter->end_controls_tab();
		// end normal sections
		// start hover sections
		$chartRepeter->start_controls_tab(
			'ekit_chart_data_bar_back_hover',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);
		$chartRepeter->add_control(
			'ekit_chart_data_bar_back_color_hover', [
				'label'       => esc_html__('Background Color', 'elementskit'),
				'type'        => Controls_Manager::COLOR,
			]
		);

		$chartRepeter->add_control(
			'ekit_chart_data_bar_border_color_hover', [
				'label'       => esc_html__('Border Color', 'elementskit'),
				'type'        => Controls_Manager::COLOR,
			]
		);
		$chartRepeter->end_controls_tab();
		// end hover sections
		$chartRepeter->end_controls_tabs();
		// end tabs section

		$chartRepeter->add_control(
			'ekit_chart_data_bar_border_width', [
				'label'       => esc_html__('Border Width', 'elementskit'),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '1',
			]
		);

		$this->add_control(
			'ekit_charts_data_set',
			[
				'label'   => esc_html__('Set Data', 'elementskit'),
				'type'    => Controls_Manager::REPEATER,
				'default' => [
					[
						'ekit_chart_data_label'            => esc_html__('Label #1', 'elementskit'),
						'ekit_chart_data_set'              => '13,20,15',
						'ekit_chart_data_bar_back_color'   => 'rgba(242,41,91,0.48)',
						'ekit_chart_data_bar_border_color' => 'rgba(242,41,91,0.48)',
						'ekit_chart_data_bar_border_width' => 1,
					],
					[
						'ekit_chart_data_label'            => esc_html__('Label #2', 'elementskit'),
						'ekit_chart_data_set'              => '20,10,33',
						'ekit_chart_data_bar_back_color'   => 'rgba(69,53,244,0.48)',
						'ekit_chart_data_bar_border_color' => 'rgba(69,53,244,0.48)',
						'ekit_chart_data_bar_border_width' => 1,
					],
					[
						'ekit_chart_data_label'            => esc_html__('Label #3', 'elementskit'),
						'ekit_chart_data_set'              => '10,3,23',
						'ekit_chart_data_bar_back_color'   => 'rgba(239,239,40,0.57)',
						'ekit_chart_data_bar_border_color' => 'rgba(239,239,40,0.57)',
						'ekit_chart_data_bar_border_width' => 1,
					],

				],

				'fields'      => $chartRepeter->get_controls(),
				'title_field' => '{{{ ekit_chart_data_label }}}',
				'condition'   => ['ekit_chart_style' => ['bar', 'horizontalBar', 'line', 'radar']],
			]
		);


		// repeter 2
		$chartRepeter2 = new Repeater();
		$chartRepeter2->add_control(
			'ekit_chart_data_label', [
				'label'       => esc_html__('Label', 'elementskit'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Label #1', 'elementskit'),
				'label_block' => true,
			]
		);

		$chartRepeter2->add_control(
			'ekit_chart_data_set2', [
				'label'       => esc_html__('Data', 'elementskit'),
				'type'        => Controls_Manager::TEXT,
				'default'     => '10',
				'label_block' => true,

			]
		);

		// start tabs section
		$chartRepeter2->start_controls_tabs(
			'ekit_chart_data_bar_back_tab'
		);
		// start normal sections
		$chartRepeter2->start_controls_tab(
			'ekit_chart_data_bar_back_normal',
			[
				'label' => esc_html__('Normal', 'elementskit'),
			]
		);

		$chartRepeter2->add_control(
			'ekit_chart_data_bar_back_color', [
				'label'       => esc_html__('Background Color', 'elementskit'),
				'type'        => Controls_Manager::COLOR,
				'default'     => 'rgba(242,41,91,0.48)',
			]
		);

		$chartRepeter2->add_control(
			'ekit_chart_data_bar_border_color', [
				'label'       => esc_html__('Border Color', 'elementskit'),
				'type'        => Controls_Manager::COLOR,
				'default'     => 'rgba(242,41,91,0.48)',
			]
		);

		$chartRepeter2->end_controls_tab();
		// end normal sections
		// start hover sections
		$chartRepeter2->start_controls_tab(
			'ekit_chart_data_bar_back_hover',
			[
				'label' => esc_html__('Hover', 'elementskit'),
			]
		);
		$chartRepeter2->add_control(
			'ekit_chart_data_bar_back_color_hover', [
				'label'       => esc_html__('Background Color', 'elementskit'),
				'type'        => Controls_Manager::COLOR,
			]
		);

		$chartRepeter2->add_control(
			'ekit_chart_data_bar_border_color_hover', [
				'label'       => esc_html__('Border Color', 'elementskit'),
				'type'        => Controls_Manager::COLOR,
			]
		);
		$chartRepeter2->end_controls_tab();
		// end hover sections
		$chartRepeter2->end_controls_tabs();
		// end tabs section

		$chartRepeter2->add_control(
			'ekit_chart_data_bar_border_width', [
				'label'       => esc_html__('Border Width', 'elementskit'),
				'type'        => Controls_Manager::NUMBER,
				'default'     => '1',
			]
		);

		$this->add_control(
			'ekit_charts_data_set2',
			[
				'label'   => esc_html__('Set Data', 'elementskit'),
				'type'    => Controls_Manager::REPEATER,
				'default' => [
					[
						'ekit_chart_data_label'            => esc_html__('Label #1', 'elementskit'),
						'ekit_chart_data_set2'             => '13',
						'ekit_chart_data_bar_back_color'   => 'rgba(242,41,91,0.48)',
						'ekit_chart_data_bar_border_color' => 'rgba(242,41,91,0.48)',
						'ekit_chart_data_bar_border_width' => 1,
					],
					[
						'ekit_chart_data_label'            => esc_html__('Label #2', 'elementskit'),
						'ekit_chart_data_set2'             => '20',
						'ekit_chart_data_bar_back_color'   => 'rgba(69,53,244,0.48)',
						'ekit_chart_data_bar_border_color' => 'rgba(69,53,244,0.48)',
						'ekit_chart_data_bar_border_width' => 1,
					],
					[
						'ekit_chart_data_label'            => esc_html__('Label #3', 'elementskit'),
						'ekit_chart_data_set2'             => '10',
						'ekit_chart_data_bar_back_color'   => 'rgba(239,239,40,0.57)',
						'ekit_chart_data_bar_border_color' => 'rgba(239,239,40,0.57)',
						'ekit_chart_data_bar_border_width' => 1,
					],

				],

				'fields'      => $chartRepeter2->get_controls(),
				'title_field' => '{{{ ekit_chart_data_label }}}',
				'condition'   => ['ekit_chart_style' => ['doughnut', 'pie', 'polarArea']],
			]
		);


		$this->end_controls_section();
		// end Data Section

		/*Account Settings*/
		$this->start_controls_section(
			'ekit_chart_settings', [
				'label' => esc_html__('Settings ', 'elementskit'),
			]
		);

		// chart style
		$this->add_control(
			'ekit_chart_style',
			[
				'label'   => esc_html__('Chart Styles', 'elementskit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bar',
				'options' => [
					'bar'           => esc_html__('Bar (Vertical)', 'elementskit'),
					'horizontalBar' => esc_html__('Bar (Horozontal)', 'elementskit'),
					'line'          => esc_html__('Line', 'elementskit'),
					'radar'         => esc_html__('Radar', 'elementskit'),
					'doughnut'      => esc_html__('Doughnut', 'elementskit'),
					'pie'           => esc_html__('Pie', 'elementskit'),
					'polarArea'     => esc_html__('Polar Area', 'elementskit'),
				],

			]
		);

		// title options
		$this->add_control(
			'ekit_charts_show_title',
			[
				'label'     => esc_html__('Show Title', 'elementskit'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'ekit_charts_title_text',
			[
				'label'       => esc_html__('Title', 'elementskit'),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'Element Kit Line Chart - Animation Progress Bar',
				'condition'   => ['ekit_charts_show_title' => 'yes'],
				'label_block' => true,
			]
		);


		// gridline options
		$this->add_control(
			'ekit_charts_grid_lines',
			[
				'label'     => esc_html__('Grid Lines', 'elementskit'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => ['ekit_chart_style!' => ['doughnut', 'pie', 'polarArea']],
			]
		);
		$this->add_control(
			'ekit_charts_grid_color',
			[
				'label'     => esc_html__('Grid Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.05)',
				'condition' => [
					'ekit_charts_grid_lines' => 'yes',
					'ekit_chart_style!'      => ['doughnut', 'pie', 'polarArea'],
				],
			]
		);

		// lavel options
		$this->add_control(
			'ekit_charts_show_lavel',
			[
				'label'     => esc_html__('Enable Label', 'elementskit'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		// legend options
		$this->add_control(
			'ekit_charts_show_legend',
			[
				'label'   => esc_html__('Enable Legends', 'elementskit'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		// tooltips options
		$this->add_control(
			'ekit_charts_show_tooltips',
			[
				'label'     => esc_html__('Show Tooltip', 'elementskit'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
		// end content account settings

		// start style sections


		// start title style here
		$this->start_controls_section(
			'ekit_chart_section_style_chart', [
				'label'     => esc_html__('Chart', 'elementskit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => ['ekit_chart_style' => ['line', 'pie', 'bubble', 'radar']],
			]
		);

		$this->add_control(
			'ekit_chart_section_style_chart_point_style',
			[
				'label'     => esc_html__('Point Style', 'elementskit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'circle',
				'options'   => [
					'circle'   => 'Circle',
					'cross'    => 'Cross',
					'star'     => 'Star',
					'triangle' => 'Triangle',
					'line'     => 'Line',
				],
				'condition' => ['ekit_chart_style' => ['line', 'radar', 'bubble']],
			]
		);

		// line chart
		$this->add_control(
			'ekit_chart_section_style_line_chart_stepped',
			[
				'label'     => esc_html__('Stepped Line', 'elementskit'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => ['ekit_chart_style' => 'line'],
			]
		);
		$this->add_control(
			'ekit_chart_section_style_line_chart_tension',
			[
				'label'     => esc_html__('Tension', 'elementskit'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0.4,
				'min'       => 0,
				'condition' => ['ekit_chart_style' => 'line', 'ekit_chart_section_style_line_chart_stepped' => 'yes'],
			]
		);
		$this->add_control(
			'ekit_chart_section_style_line_chart_fill',
			[
				'label'     => esc_html__('Fill', 'elementskit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom',
				'options'   => [
					'top'    => 'Top',
					'bottom' => 'Bottom',
				],
				'condition' => ['ekit_chart_style' => 'line'],
			]
		);

		$this->add_control(
			'ekit_chart_section_style_pie_chart_border_width',
			[
				'label'     => esc_html__('Border Width', 'elementskit'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 0,
				'condition' => ['ekit_chart_style' => 'pie'],
			]
		);
		$this->end_controls_section();
		// end chart style

		// start label style
		$this->start_controls_section(
			'ekit_chart_section_style_label', [
				'label'     => esc_html__('Categories', 'elementskit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => ['ekit_charts_show_lavel' => 'yes'],
			]
		);
		$this->add_control(
			'ekit_charts_label_font_color',
			[
				'label'   => esc_html__('Font Color', 'elementskit'),
				'type'    => Controls_Manager::COLOR,
				'default' => '#666',

			]
		);
		$this->add_control(
			'ekit_charts_label_font_size',
			[
				'label'   => esc_html__('Font Size', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 100,
				'step'    => 1,
				'default' => 12,


			]
		);
		$this->add_control(
			'ekit_charts_label_font_style',
			[
				'label'   => esc_html__('Font Style', 'elementskit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'bold'   => 'Bold',
					'normal' => 'Normal',
				],

			]
		);
		$this->add_control(
			'ekit_charts_label_padding',
			[
				'label'   => esc_html__('Padding', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 60,
				'step'    => 1,
				'default' => 5,

			]
		);


		$this->end_controls_section();
		// end label style

		// start legend style
		$this->start_controls_section(
			'ekit_chart_section_style_legend', [
				'label'     => esc_html__('Legend', 'elementskit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => ['ekit_charts_show_legend' => 'yes'],
			]
		);
		$this->add_control(
			'ekit_charts_legend_font_color',
			[
				'label'   => esc_html__('Font Color', 'elementskit'),
				'type'    => Controls_Manager::COLOR,
				'default' => '#666',

			]
		);
		$this->add_control(
			'ekit_charts_legend_font_size',
			[
				'label'   => esc_html__('Font Size', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 100,
				'step'    => 1,
				'default' => 12,


			]
		);
		$this->add_control(
			'ekit_charts_legend_font_style',
			[
				'label'   => esc_html__('Font Style', 'elementskit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'bold'   => 'Bold',
					'normal' => 'Normal',
				],

			]
		);
		$this->add_control(
			'ekit_charts_legend_padding',
			[
				'label'   => esc_html__('Padding', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 300,
				'step'    => 1,
				'default' => 10,

			]
		);

		$this->add_control(
			'ekit_charts_legend_point_style',
			[
				'label'        => esc_html__('Point Style', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'ekit_charts_legend_box_width',
			[
				'label'     => esc_html__('Box Width', 'elementskit'),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 0,
				'max'       => 300,
				'step'      => 1,
				'default'   => 40,
				'condition' => ['ekit_charts_legend_point_style!' => 'yes'],

			]
		);
		$this->add_control(
			'ekit_charts_legend_align',
			[
				'label'   => esc_html__('Position', 'elementskit'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'top',
				'options' => [
					'left'   => [
						'title' => esc_html__('Left', 'elementskit'),
						'icon'  => 'fa fa-long-arrow-left',
					],
					'top'    => [
						'title' => esc_html__('Top', 'elementskit'),
						'icon'  => 'fa fa-long-arrow-up',
					],
					'bottom' => [
						'title' => esc_html__('Bottom', 'elementskit'),
						'icon'  => 'fa fa-long-arrow-down',
					],
					'right'  => [
						'title' => esc_html__('Right', 'elementskit'),
						'icon'  => 'fa fa-long-arrow-right',
					],
				],

			]
		);
		$this->end_controls_section();
		// end legend style

		// start tooltips style
		$this->start_controls_section(
			'ekit_chart_section_style_tooltip', [
				'label'     => esc_html__('Tooltip', 'elementskit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => ['ekit_charts_show_tooltips' => 'yes'],
			]
		);
		$this->add_control(
			'ekit_charts_tooltips_back_color',
			[
				'label'   => esc_html__('Background Color', 'elementskit'),
				'type'    => Controls_Manager::COLOR,
				'default' => 'rgba(0,0,0,0.96)',

			]
		);
		// title
		$this->add_control(
			'ekit_charts_tooltips_title_font_color',
			[
				'label'   => esc_html__('Title Font Color', 'elementskit'),
				'type'    => Controls_Manager::COLOR,
				'default' => '#fff',

			]
		);
		$this->add_control(
			'ekit_charts_tooltips_title_font_size',
			[
				'label'   => esc_html__('Title Font Size', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 100,
				'step'    => 1,
				'default' => 13,


			]
		);
		$this->add_control(
			'ekit_charts_tooltips_title_font_style',
			[
				'label'   => esc_html__('Title Font Style', 'elementskit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'bold'   => 'Bold',
					'normal' => 'Normal',
				],

			]
		);
		// bodyFontColor
		$this->add_control(
			'ekit_charts_tooltips_body_font_color',
			[
				'label'   => esc_html__('Body Font Color', 'elementskit'),
				'type'    => Controls_Manager::COLOR,
				'default' => '#fff',

			]
		);
		$this->add_control(
			'ekit_charts_tooltips_body_font_size',
			[
				'label'   => esc_html__('Body Font Size', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 100,
				'step'    => 1,
				'default' => 12,


			]
		);
		$this->add_control(
			'ekit_charts_tooltips_body_font_style',
			[
				'label'   => esc_html__('Body Font Style', 'elementskit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'bold'   => 'Bold',
					'normal' => 'Normal',
				],

			]
		);

		$this->end_controls_section();
		// end tooltips style

		// start title style here
		$this->start_controls_section(
			'ekit_chart_section_style_title', [
				'label'     => esc_html__('Title', 'elementskit'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => ['ekit_charts_show_title' => 'yes'],
			]
		);
		$this->add_control(
			'ekit_charts_title_text_font_size',
			[
				'label'   => esc_html__('Font Size', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 100,
				'step'    => 1,
				'default' => 14,

			]
		);
		$this->add_control(
			'ekit_charts_title_text_font_color',
			[
				'label'   => esc_html__('Font Color', 'elementskit'),
				'type'    => Controls_Manager::COLOR,
				'default' => '#666',

			]
		);
		$this->add_control(
			'ekit_charts_title_text_font_style',
			[
				'label'   => esc_html__('Font Style', 'elementskit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bold',
				'options' => [
					'bold'   => 'Bold',
					'normal' => 'Normal',
				],

			]
		);
		$this->add_control(
			'ekit_charts_title_align',
			[
				'label'   => esc_html__('Position', 'elementskit'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'top',
				'options' => [
					'left'   => [
						'title' => esc_html__('Left', 'elementskit'),
						'icon'  => 'fa fa-long-arrow-left',
					],
					'top'    => [
						'title' => esc_html__('Top', 'elementskit'),
						'icon'  => 'fa fa-long-arrow-up',
					],
					'bottom' => [
						'title' => esc_html__('Bottom', 'elementskit'),
						'icon'  => 'fa fa-long-arrow-down',
					],
					'right'  => [
						'title' => esc_html__('Right', 'elementskit'),
						'icon'  => 'fa fa-long-arrow-right',
					],
				],

			]
		);
		$this->add_control(
			'ekit_charts_title_text_font_padding',
			[
				'label'   => esc_html__('Padding', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 300,
				'step'    => 1,
				'default' => 10,

			]
		);
		$this->add_control(
			'ekit_charts_title_text_font_line_height',
			[
				'label'   => esc_html__('Line Height', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 100,
				'step'    => 1,
				'default' => 1.2,


			]
		);
		$this->end_controls_section();
		// end title style

		// start animation style
		$this->start_controls_section(
			'ekit_chart_section_style_animation', [
				'label' => esc_html__('Animation', 'elementskit'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ekit_chart_section_style_animation_duration',
			[
				'label'   => esc_html__('Duration', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1000,
				'max'     => 10000,
				'step'    => 100,
				'default' => 1000,


			]
		);
		$this->add_control(
			'ekit_chart_section_style_animation_style',
			[
				'label'       => esc_html__('Style', 'elementskit'),
				'type'        => Controls_Manager::SELECT2,
				'default'     => 'linear',
				'label_block' => true,
				'options'     => [
					'linear'           => 'Linear',
					'easeInQuad'       => 'easeInQuad',
					'easeOutQuad'      => 'easeOutQuad',
					'easeInOutQuad'    => 'easeInOutQuad',
					'easeInCubic'      => 'easeInCubic',
					'easeOutCubic'     => 'easeOutCubic',
					'easeInOutCubic'   => 'easeInOutCubic',
					'easeInQuart'      => 'easeInQuart',
					'easeOutQuart'     => 'easeOutQuart',
					'easeInOutQuart'   => 'easeInOutQuart',
					'easeInQuint'      => 'easeInQuint',
					'easeOutQuint'     => 'easeOutQuint',
					'easeInOutQuint'   => 'easeInOutQuint',
					'easeInSine'       => 'easeInSine',
					'easeOutSine'      => 'easeOutSine',
					'easeInOutSine'    => 'easeInOutSine',
					'easeInExpo'       => 'easeInExpo',
					'easeOutExpo'      => 'easeOutExpo',
					'easeInOutExpo'    => 'easeInOutExpo',
					'easeInCirc'       => 'easeInCirc',
					'easeOutCirc'      => 'easeOutCirc',
					'easeInOutCirc'    => 'easeInOutCirc',
					'easeInElastic'    => 'easeInElastic',
					'easeOutElastic'   => 'easeOutElastic',
					'easeInOutElastic' => 'easeInOutElastic',
					'easeInBack'       => 'easeInBack',
					'easeOutBack'      => 'easeOutBack',
					'easeInOutBack'    => 'easeInOutBack',
					'easeInBounce'     => 'easeInBounce',
					'easeOutBounce'    => 'easeOutBounce',
					'easeInOutBounce'  => 'easeInOutBounce',
				],
			]
		);
		$this->end_controls_section();
		// end animation style

		/**
		 * Section: Responsive
		 */
		$this->start_controls_section(
			'ekit_chart_section_style_res',
			[
				'label' => esc_html__( 'Responsive', 'elementskit' ),
				'tab'	=> Controls_Manager::TAB_STYLE,
			]
		);
			/**
			 * Control: Enable/Disable
			 */
			$this->add_control(
				'ekit_chart_res',
				[
					'label'		=> esc_html__( 'Responsive Layout', 'elementskit' ),
					'type'		=> Controls_Manager::SWITCHER,
					'selectors'	=> [
						'{{WRAPPER}} .ekit-wid-con'	=> 'overflow: auto;',
						'{{WRAPPER}} .ekit-chart' => 'position: relative; margin: 0 auto;',
					],
				]
			);

			/**
			 * Control: Width
			 */
			$this->add_responsive_control(
				'ekit_chart_res_width',
				[
					'label'		=> esc_html__( 'Width (px)', 'elementskit' ),
					'type'		=> Controls_Manager::SLIDER,
					'range'		=> [
						'px'	=> [
							'min' => 0,
							'max' => 1600
						]
					],
					'default'	=> [
						'size'	=> '800',
					],
					'selectors'	=> [
						'{{WRAPPER}} .ekit-chart' => 'width: {{SIZE}}px;',
					],
					'condition'	=> [
						'ekit_chart_res' => 'yes',
					],
					'separator'	=> 'before',
				]
			);

			/**
			 * Control: Height
			 */
			$this->add_responsive_control(
				'ekit_chart_res_height',
				[
					'label'		=> esc_html__( 'Height (px)', 'elementskit' ),
					'type'		=> Controls_Manager::SLIDER,
					'range'		=> [
						'px'	=> [
							'min' => 0,
							'max' => 1600
						]
					],
					'default'	=> [
						'size'	=> '400',
					],
					'selectors'	=> [
						'{{WRAPPER}} .ekit-chart' => 'height: {{SIZE}}px;',
					],
					'condition'	=> [
						'ekit_chart_res' => 'yes',
					],
				]
			);
		$this->end_controls_section();

		$this->insert_pro_message();
	}


	protected function render() {
		echo '<div class="ekit-wid-con" >';
		$this->render_raw();
		echo '</div>';
	}


	protected function render_raw() {

		$settings = $this->get_settings_for_display();
		extract($settings);
		$Chart_id = 'ekit-chart-' . $this->get_id();


		$dataChartArray = [];

		if(is_array($ekit_charts_labels_data) && sizeof($ekit_charts_labels_data)):
			foreach($ekit_charts_labels_data AS $labelsData):
				$dataChartArray['labels'][] = $labelsData['ekit_chart_label'];
			endforeach;
		endif;

		$backColor = [];

		// set data
		if(in_array($ekit_chart_style, array('pie', 'doughnut', 'polarArea'))) {
			$backgroundColor      = [];
			$backgroundColorHover = [];
			$borderColor          = [];
			$borderColorHover     = [];
			$dataChart            = [];
			$chartLabel           = [];
			if(is_array($ekit_charts_data_set2) && sizeof($ekit_charts_data_set2)):
				foreach($ekit_charts_data_set2 AS $DataChart):

					$backgroundColor[]      = $DataChart['ekit_chart_data_bar_back_color'];
					$backgroundColorHover[] = strlen($DataChart['ekit_chart_data_bar_back_color_hover']) > 0 ? $DataChart['ekit_chart_data_bar_back_color_hover'] : $DataChart['ekit_chart_data_bar_back_color'];
					$borderColor[]          = $DataChart['ekit_chart_data_bar_border_color'];

					$borderColorHover[] = strlen($DataChart['ekit_chart_data_bar_border_color_hover']) > 0 ? $DataChart['ekit_chart_data_bar_border_color_hover'] : $DataChart['ekit_chart_data_bar_border_color'];
					$dataArray          = array_map('intval', explode(',', trim($DataChart['ekit_chart_data_set2'], ',')));
					$dataChart[]        = $dataArray[0];
					$chartLabel[]       = $DataChart['ekit_chart_data_label'];

				endforeach;
			endif;
			$dataChartArray['datasets'][] = [
				'data'                 => $dataChart,
				'backgroundColor'      => $backgroundColor,
				'hoverBackgroundColor' => $backgroundColorHover,
				'borderColor'          => $borderColor,
				'hoverBorderColor'     => $borderColorHover,
			];
			$dataChartArray['labels']     = $chartLabel;
		} else {
			if(is_array($ekit_charts_data_set) && sizeof($ekit_charts_data_set)):
				foreach($ekit_charts_data_set AS $DataChart):
					$backgroundColor      = $DataChart['ekit_chart_data_bar_back_color'];
					$backgroundColorHover = $DataChart['ekit_chart_data_bar_back_color_hover'];
					$borderColor          = $DataChart['ekit_chart_data_bar_border_color'];
					$borderColorHover     = $DataChart['ekit_chart_data_bar_border_color_hover'];
					$borderWidth          = $DataChart['ekit_chart_data_bar_border_width'];

					$backgroundColorHover = strlen($backgroundColorHover) > 0 ? $backgroundColorHover : $backgroundColor;
					$borderColorHover     = strlen($borderColorHover) > 0 ? $borderColorHover : $borderColor;

					$dataChartArray['datasets'][] = [
						'label'                => $DataChart['ekit_chart_data_label'],
						'data'                 => array_map('floatval', explode(',', trim($DataChart['ekit_chart_data_set'], ','))),
						'backgroundColor'      => $backgroundColor,
						'hoverBackgroundColor' => $backgroundColorHover,
						'borderColor'          => $borderColor,
						'hoverBorderColor'     => $borderColorHover,
						'borderWidth'          => $borderWidth,
					];
				endforeach;
			endif;
		}

		$dataLabelsJson = wp_json_encode(array_filter($dataChartArray['labels']));
		$dataJson       = wp_json_encode(array_filter($dataChartArray['datasets']));

		// start options
		$options = [];

		// Title options
		if($ekit_charts_show_title == 'yes') {
			$options['title'] = [
				'display'    => true,
				'position'   => strlen($ekit_charts_title_align) > 0 ? $ekit_charts_title_align : 'top',
				'fontSize'   => $ekit_charts_title_text_font_size,
				'fontColor'  => $ekit_charts_title_text_font_color,
				'fontStyle'  => $ekit_charts_title_text_font_style,
				'padding'    => $ekit_charts_title_text_font_padding,
				'lineHeight' => $ekit_charts_title_text_font_line_height,
				'text'       => $ekit_charts_title_text,
			];
		}

		// animations options
		$options['animation'] = [
			'duration'   => $ekit_chart_section_style_animation_duration,
			'easing'     => $ekit_chart_section_style_animation_style,
			'onProgress' => 'function(animation) {progress.value = animation.animationObject.currentStep / animation.animationObject.numSteps;}',
			'onComplete' => 'function() {window.setTimeout(function() { progress.value = 0;}, ' . $ekit_chart_section_style_animation_duration . ');',
		];

		// tooltip options options
		if($ekit_charts_show_tooltips == 'yes') {
			if($ekit_charts_tooltips_back_color) {
				$options['tooltips'] = [
					'backgroundColor' => $ekit_charts_tooltips_back_color,
					'intersect'       => true,
					'mode'            => 'nearest',
					'titleFontSize'   => $ekit_charts_tooltips_title_font_size,
					'titleFontColor'  => $ekit_charts_tooltips_title_font_color,
					'titleFontStyle'  => $ekit_charts_tooltips_title_font_style,
					'bodyFontSize'    => $ekit_charts_tooltips_body_font_size,
					'bodyFontColor'   => $ekit_charts_tooltips_body_font_color,
					'bodyFontStyle'   => $ekit_charts_tooltips_body_font_style,
				];
			}
		} else {
			$options['tooltips'] = ['enabled' => false];
		}

		//Elements optins
		if(in_array($ekit_chart_style, array('line', 'radar', 'bubble'))) {
			$options['elements']['point'] = [
				'radius'           => 5,
				'pointStyle'       => $ekit_chart_section_style_chart_point_style,
				'borderWidth'      => 3,
				'hoverRadius'      => 6,
				'hoverBorderWidth' => 4,
			];
		}
		if(in_array($ekit_chart_style, array('line'))) {
			$options['elements']['line'] = [
				'tension'        => $ekit_chart_section_style_line_chart_tension,
				'borderCapStyle' => 'butt',
				'fill'           => $ekit_chart_section_style_line_chart_fill,
				'stepped'        => isset($ekit_chart_section_style_line_chart_stepped) && $ekit_chart_section_style_line_chart_stepped == 'yes' ? false : true,

			];
		}

		if(in_array($ekit_chart_style, array('pie'))) {
			$options['elements']['arc'] = [
				'backgroundColor' => 'rgba(0, 0, 0, 0.1)',
				'borderColor'     => 'rgba(0, 0, 0, 0.1)',
				'borderWidth'     => $ekit_chart_section_style_pie_chart_border_width,
				'borderAlign'     => 'left',
			];
		}

		// legend options
		if($ekit_charts_show_legend == 'yes') {
			if($ekit_charts_legend_align) {
				$options['legend'] = [
					'position' => $ekit_charts_legend_align,
					'labels'   => [
						'boxWidth'      => $ekit_charts_legend_box_width,
						'fontColor'     => $ekit_charts_legend_font_color,
						'fontSize'      => $ekit_charts_legend_font_size,
						'fontStyle'     => $ekit_charts_legend_font_style,
						'padding'       => $ekit_charts_legend_padding,
						'usePointStyle' => (isset($ekit_charts_legend_point_style) & $ekit_charts_legend_point_style == 'yes') ? true : false,
					],
				];
			}
		} else {
			$options['legend'] = ['display' => false];
		}

		// gridline options
		if(!in_array($ekit_chart_style, array('doughnut', 'pie', 'polarArea'))) {
			$ticksArray = [
				'display'     => ($ekit_charts_show_lavel) ? true : false,
				'beginAtZero' => true,
				'fontColor'   => $ekit_charts_label_font_color,
				'fontSize'    => $ekit_charts_label_font_size,
				'fontStyle'   => $ekit_charts_label_font_style,
				'padding'     => $ekit_charts_label_padding,
			];

			if($ekit_charts_grid_lines == 'yes') {
				$options['scales']['yAxes'] = [
					[
						'ticks'     => $ticksArray,
						'gridLines' => [
							'drawBorder' => false,
							'color'      => $ekit_charts_grid_color,
						],
					],
				];
				$options['scales']['xAxes'] = [
					[
						'ticks'     => $ticksArray,
						'gridLines' => [
							'drawBorder' => false,
							'color'      => $ekit_charts_grid_color,
						],
					],
				];
			} else {
				$options['scales']['yAxes'] = [
					[
						'ticks'     => $ticksArray,
						'gridLines' => [
							'display' => false,
						],
					],
				];
				$options['scales']['xAxes'] = [
					[
						'ticks'     => $ticksArray,
						'gridLines' => [
							'display' => false,
						],
					],
				];
			}
		}

		if(in_array($ekit_chart_style, array('doughnut'))) {
			$options['cutoutPercentage'] = 50;
		}
		if(in_array($ekit_chart_style, array('pie'))) {
			$options['cutoutPercentage'] = 0;
		}

		// extra plugin
		$options['plugins'] = [
			'deferred' => [
				'xOffset' => 150,
				'yOffset' => '50%',
				'delay'   => 300,
			],
		];

		$opionsJson = wp_json_encode(array_filter($options));
		?>
		<div class="ekit-chart">
			<canvas id="<?php echo esc_attr($Chart_id); ?>"></canvas>
		</div>

		<script type="text/javascript">
            jQuery(function ($) {
                var ekitChartId = document.querySelector('#<?php echo esc_attr($Chart_id); ?>'),
					ekitChart = new Chart(ekitChartId, {
                    type: '<?php echo esc_js($ekit_chart_style);?>',
                    data: {
                        labels: <?php echo \ElementsKit_Lite\Utils::render($dataLabelsJson); ?>,
                        datasets: <?php echo \ElementsKit_Lite\Utils::render($dataJson); ?>
                    },
                    options: <?php echo \ElementsKit_Lite\Utils::render($opionsJson); ?>
                });
            });
		</script>
		<?php
	}

}
