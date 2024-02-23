<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

use Bricks\Database;
use Bricks\Element;
use Bricks\Helpers;
use Jet_Engine\Bricks_Views\Helpers\Options_Converter;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Bricks_Search extends Jet_Smart_Filters_Bricks_Base {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-search'; // Make sure to prefix your elements
	public $icon = 'jet-smart-filters-icon-search-filter'; // Themify icon font class
	public $css_selector = '.jet-search-filter__input'; // Default CSS selector
	public $scripts = [ 'JetSmartFiltersBricksInit' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'search';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Search Filter', 'jet-smart-filters' );
	}

	// Set builder control groups
	public function set_control_groups() {

		$this->register_general_group();
		$this->register_filter_content_group();
		$this->register_filter_inputs_group();
		$this->register_filter_label_group();
		$this->register_filter_button_group();
	}

	// Set builder controls
	public function set_controls() {

		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/search/css-scheme',
			array(
				'filters-label'             => '.jet-filter-label',
				'filter-wrapper'            => '.jet-search-filter',
				'input'                     => '.jet-search-filter__input',
				'input-wrapper'             => '.jet-search-filter__input-wrapper',
				'input-clear'               => '.jet-search-filter__input-clear',
				'input-loading'             => '.jet-search-filter__input-loading',
				'apply-filters-button'      => '.jet-search-filter__submit',
				'apply-filters-button-icon' => '.jet-search-filter__submit-icon',
			)
		);

		$this->register_general_controls();
		$this->register_filter_content_controls( $css_scheme );
		$this->register_filter_inputs_controls( $css_scheme );
		$this->register_filter_label_controls( $css_scheme );
		$this->register_filter_button_controls( $css_scheme );
	}

	public function register_general_controls() {
		$this->start_jet_control_group( 'section_general' );

		$this->register_jet_control(
			'notice_cache_query_loop',
			[
				'tab'         => 'content',
				'type'        => 'info',
				'content'     => esc_html__( 'You have enabled the "Cache query loop" option.', 'jet-smart-filters' ),
				'description' => sprintf(
					esc_html__( 'This option will break the filters functionality. You can disable this option or use "JetEngine Query Builder" query type. Go to: %s > Cache query loop', 'jet-smart-filters' ),
					'<a href="' . Helpers::settings_url( '#tab-performance' ) . '" target="_blank">Bricks > ' . esc_html__( 'Settings', 'jet-smart-filters' ) . ' > Performance</a>'
				),
				'required'    => [
					[ 'content_provider', '=', 'bricks-query-loop' ],
					[ 'cacheQueryLoops', '=', true, 'globalSettings' ],
				],
			]
		);

		$this->register_jet_control(
			'filter_id',
			[
				'label'       => esc_html__( 'Select filter', 'jet-smart-filters' ),
				'type'        => 'select',
				'options'     => jet_smart_filters()->data->get_filters_by_type( $this->jet_element_render ),
				'multiple'    => false,
				'searchable'  => true,
				'placeholder' => esc_html__( 'Select...', 'jet-smart-filters' ),
			]
		);

		$provider_allowed = \Jet_Smart_Filters\Bricks_Views\Manager::get_allowed_providers();

		$this->register_jet_control(
			'content_provider',
			[
				'tab'        => 'content',
				'label'      => esc_html__( 'This filter for', 'jet-smart-filters' ),
				'type'       => 'select',
				'options'    => Options_Converter::filters_options_by_key( jet_smart_filters()->data->content_providers(), $provider_allowed ),
				'searchable' => true,
			]
		);

		$this->register_jet_control(
			'epro_posts_notice',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Please set <b>jet-smart-filters</b> into Query ID option of Posts widget you want to filter', 'jet-smart-filters' ),
				'type'     => 'info',
				'required' => [ 'content_provider', '=', [ 'epro-posts', 'epro-portfolio' ] ],
			]
		);

		$this->register_jet_control(
			'apply_type',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Apply type', 'jet-smart-filters' ),
				'type'    => 'select',
				'options' => [
					'ajax'          => esc_html__( 'AJAX', 'jet-smart-filters' ),
					'ajax-ontyping' => esc_html__( 'AJAX on typing', 'jet-smart-filters' ),
					'reload'        => esc_html__( 'Page reload', 'jet-smart-filters' ),
					'mixed'         => esc_html__( 'Mixed', 'jet-smart-filters' ),
				],
				'default' => 'ajax',
			]
		);

		$this->register_jet_control(
			'typing_min_letters_count',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Min number of letters', 'jet-smart-filters' ),
				'type'        => 'number',
				'default'     => 3,
				'min'         => 1,
				'max'         => 10,
				'step'        => 1,
				'description' => esc_html__( 'The minimum number of letters to start the search.', 'jet-smart-filters' ),
				'required'    => [ 'apply_type', '=', 'ajax-ontyping' ],
			]
		);

		$this->register_jet_control(
			'apply_button_text',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Search button text', 'jet-smart-filters' ),
				'type'           => 'text',
				'hasDynamicData' => false,
				'default'        => esc_html__( 'Apply filter', 'jet-smart-filters' ),
				'required'       => [
					[ 'apply_type', '=', [ 'ajax', 'reload', 'mixed' ] ],
					[ 'hide_apply_button', '=', false ],
				],
			]
		);

		$this->register_jet_control(
			'apply_button_icon',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Search button icon', 'jet-smart-filters' ),
				'type'     => 'icon',
				'required'       => [
					[ 'apply_type', '=', [ 'ajax', 'reload', 'mixed' ] ],
					[ 'hide_apply_button', '=', false ],
				],
			]
		);

		$this->register_jet_control(
			'hide_apply_button',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Hide apply button', 'jet-smart-filters' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [ 'apply_type', '=', [ 'ajax', 'reload', 'mixed' ] ],
			]
		);

		$this->register_jet_control(
			'show_label',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show filter label', 'jet-smart-filters' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'query_id',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Query ID', 'jet-smart-filters' ),
				'type'           => 'text',
				'hasDynamicData' => false,
				'description'    => esc_html__( 'Set unique query ID if you use multiple widgets of same provider on the page. Same ID you need to set for filtered widget.', 'jet-smart-filters' ),
			]
		);

		// Include Additional Providers Settings
		include jet_smart_filters()->plugin_path( 'includes/bricks/elements/common-controls/additional-providers.php' );

		$this->end_jet_control_group();
	}

	public function register_filter_content_group() {
		$this->register_jet_control_group(
			'section_search_content_style',
			[
				'title' => esc_html__( 'Content', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_filter_content_controls( $css_scheme = null ) {

		$this->start_jet_control_group( 'section_search_content_style' );

		$this->register_jet_control(
			'content_position',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Direction', 'jet-smart-filters' ),
				'type'  => 'direction',
				'css'   => [
					[
						'property' => 'flex-direction',
						'selector' => $css_scheme['filter-wrapper'],
					],
				],
			]
		);

		$this->register_jet_control(
			'menu_main_axis',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Align main axis', 'jet-smart-filters' ),
				'type'     => 'justify-content',
				'tooltip'  => [
					'content'  => 'justify-content',
					'position' => 'top-left',
				],
				'css'      => [
					[
						'property' => 'justify-content',
						'selector' => $css_scheme['filter-wrapper'],
					],
				],
				'required' => [ 'content_position', '=', 'row' ],
			]
		);

		$this->register_jet_control(
			'menu_cross_axis',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Align cross axis', 'jet-smart-filters' ),
				'type'    => 'align-items',
				'tooltip' => [
					'content'  => 'align-items',
					'position' => 'top-left',
				],
				'default' => 'flex-start',
				'css'     => [
					[
						'property' => 'align-items',
						'selector' => $css_scheme['filter-wrapper'],
					],
				],
			]
		);

		$this->register_jet_control(
			'content_date_range_inputs_width',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Inputs width', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '100%',
				'css'     => [
					[
						'property' => 'max-width',
						'selector' => $css_scheme['input-wrapper'],
					],
					[
						'property' => 'flex-basis',
						'selector' => $css_scheme['input-wrapper'],
					],
					[
						'property' => 'width',
						'selector' => $css_scheme['input-wrapper'],
						'value'    => '100%',
					],
				],
			]
		);

		$this->register_jet_control(
			'content_date_range_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Gap', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '20px',
				'css'     => [
					[
						'property' => 'gap',
						'selector' => $css_scheme['filter-wrapper'],
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	public function register_filter_inputs_group() {
		$this->register_jet_control_group(
			'section_search_input_style',
			[
				'title' => esc_html__( 'Input', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_filter_inputs_controls( $css_scheme = null ) {

		$this->start_jet_control_group( 'section_search_input_style' );

		$this->register_jet_control(
			'search_input_placeholder_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Placeholder color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['input'] . '::placeholder',
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	public function register_filter_button_group() {
		$this->register_jet_control_group(
			'section_filter_apply_button_style',
			[
				'title' => esc_html__( 'Button', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_filter_apply_button_icon_style',
			[
				'title'    => esc_html__( 'Button icon', 'jet-smart-filters' ),
				'tab'      => 'style',
				'required' => [
					[ 'apply_button_icon', '!=', '' ],
				],
			]
		);
	}

	public function register_filter_button_controls( $css_scheme = null ) {
		$this->start_jet_control_group( 'section_filter_apply_button_style' );

		$this->register_jet_control(
			'filter_apply_button_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-smart-filters' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_box_shadow',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box shadow', 'jet-smart-filters' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_filter_apply_button_icon_style' );

		$this->register_jet_control(
			'filter_apply_button_icon_direction',
			[
				'tab'       => 'style',
				'label'     => esc_html__( 'Direction', 'jet-smart-filters' ),
				'type'      => 'direction',
				'direction' => 'row',
				'css'       => [
					[
						'property' => 'flex-direction',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_icon_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Icon color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['apply-filters-button-icon'],
					],
					[
						'property' => 'fill',
						'selector' => $css_scheme['apply-filters-button-icon'] . ' :is(svg, path)',
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_icon_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Icon size', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'font-size',
						'selector' => $css_scheme['apply-filters-button-icon'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_apply_button_icon_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Icon gap', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '12px',
				'css'     => [
					[
						'property' => 'gap',
						'selector' => $css_scheme['apply-filters-button'],
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	// Render element HTML
	public function render() {
		jet_smart_filters()->set_filters_used();

		$base_class = $this->name;
		$settings   = $this->parse_jet_render_attributes( $this->get_jet_settings() );
		$filter_id  = ! empty( $settings['filter_id'] ) ? $settings['filter_id'] : '';

		// STEP: Select filter is empty: Show placeholder text
		if ( empty( $filter_id ) ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Please select filter to show.', 'jet-smart-filters' )
				]
			);
		}

		$provider  = ! empty( $settings['content_provider'] ) ? $settings['content_provider'] : '';
		$filter_id = apply_filters( 'jet-smart-filters/render_filter_template/filter_id', $filter_id );

		// STEP: Content provider is empty: Show placeholder text
		if ( empty( $provider ) ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Please select content provider to show.', 'jet-smart-filters' )
				]
			);
		}

		echo "<div {$this->render_attributes( '_root' )}>";

		printf( '<div class="%1$s jet-filter">', $base_class );

		$apply_type = ! empty( $settings['apply_type'] ) ? $settings['apply_type'] : 'ajax';
		$apply_on   = ! empty( $settings['apply_on'] ) ? $settings['apply_on'] : 'value';

		if ( 'submit' === $apply_on && in_array( $apply_type, [ 'ajax', 'mixed' ] ) ) {
			$apply_type = $apply_type . '-reload';
		}

		$query_id             = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$show_label           = ! empty( $settings['show_label'] ) ? filter_var( $settings['show_label'], FILTER_VALIDATE_BOOLEAN ) : false;
		$additional_providers = jet_smart_filters()->utils->get_additional_providers( $settings );
		$icon                 = '';

		if ( ! empty( $settings['apply_button_icon'] ) ) {
			$rendered_icon = Element::render_icon( $settings['apply_button_icon'] );
			$format        = '<span class="jet-search-filter__submit-icon">%s</span>';
			$icon          = sprintf( $format, $rendered_icon );
		}

		$typing_min_letters_count = ! empty( $settings['typing_min_letters_count'] ) ? $settings['typing_min_letters_count'] : 3;
		$apply_button_text        = ! empty( $settings['apply_button_text'] ) ? $settings['apply_button_text'] : '';
		$hide_apply_button        = ! empty( $settings['hide_apply_button'] ) ? $settings['hide_apply_button'] : '';

		$filter_template_args = [
			'filter_id'            => $filter_id,
			'content_provider'     => $provider,
			'additional_providers' => $additional_providers,
			'apply_type'           => $apply_type,
			'min_letters_count'    => $typing_min_letters_count,
			'button_text'          => $apply_button_text,
			'button_icon'          => $icon,
			'hide_apply_button'    => $hide_apply_button,
			'query_id'             => $query_id,
		];

		jet_smart_filters()->admin_bar_register_item( $filter_id );

		include jet_smart_filters()->get_template( 'common/filter-label.php' );

		jet_smart_filters()->filter_types->render_filter_template( $this->jet_element_render, $filter_template_args );

		echo '</div>';

		echo "</div>";

	}
}