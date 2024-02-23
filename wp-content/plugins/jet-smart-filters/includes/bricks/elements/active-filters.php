<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

use Bricks\Database;
use Bricks\Helpers;
use Jet_Engine\Bricks_Views\Helpers\Options_Converter;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Bricks_Active_Filters extends \Jet_Engine\Bricks_Views\Elements\Base {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-active'; // Make sure to prefix your elements
	public $icon = 'jet-smart-filters-icon-active-filters'; // Themify icon font class
	public $css_selector = '.jet-active-filter'; // Default CSS selector
	public $scripts = [ 'JetSmartFiltersBricksInit' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'active-filters';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Active Filters', 'jet-smart-filters' );
	}

	// Set builder control groups
	public function set_control_groups() {

		$this->register_general_group();
		$this->register_filter_style_group();
		$this->register_filter_label_group();
	}

	// Set builder controls
	public function set_controls() {

		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/active-filters/css-scheme',
			array(
				'filters'       => '.jet-smart-filters-active',
				'filters-list'  => '.jet-active-filters__list',
				'filters-title' => '.jet-active-filters__title',
				'filter'        => '.jet-active-filter',
				'filter-label'  => '.jet-active-filter__label',
				'filter-value'  => '.jet-active-filter__val',
				'filter-remove' => '.jet-active-filter__remove',
			)
		);

		$this->register_general_controls();
		$this->register_filter_style_controls( $css_scheme );
		$this->register_filter_label_controls( $css_scheme );
	}

	public function register_general_group() {
		$this->register_jet_control_group(
			'section_general',
			[
				'title' => esc_html__( 'Content', 'jet-smart-filters' ),
				'tab'   => 'content',
			]
		);
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

		$provider_allowed = \Jet_Smart_Filters\Bricks_Views\Manager::get_allowed_providers();

		$this->register_jet_control(
			'content_provider',
			[
				'tab'        => 'content',
				'label'      => esc_html__( 'Show active filters for:', 'jet-smart-filters' ),
				'type'       => 'select',
				'options'    => Options_Converter::filters_options_by_key( jet_smart_filters()->data->content_providers(), $provider_allowed ),
				'searchable' => true,
			]
		);

		$this->register_jet_control(
			'apply_type',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Apply type', 'jet-smart-filters' ),
				'type'    => 'select',
				'options' => [
					'ajax'   => esc_html__( 'AJAX', 'jet-smart-filters' ),
					'reload' => esc_html__( 'Page reload', 'jet-smart-filters' ),
					'mixed'  => esc_html__( 'Mixed', 'jet-smart-filters' ),
				],
				'default' => 'ajax',
			]
		);

		$this->register_jet_control(
			'filters_label',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Label', 'jet-smart-filters' ),
				'type'           => 'text',
				'hasDynamicData' => false,
				'default'        => esc_html__( 'Active filters:', 'jet-smart-filters' ),
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

	public function register_filter_style_group() {

		$this->register_jet_control_group(
			'section_filters_list_styles',
			[
				'title' => esc_html__( 'List', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_filters_items_styles',
			[
				'title' => esc_html__( 'Items', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_filter_style_controls( $css_scheme = null ) {

		$this->start_jet_control_group( 'section_filters_list_styles' );

		$this->register_jet_control(
			'filters_direction',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Direction', 'jet-smart-filters' ),
				'type'  => 'direction',
				'css'   => [
					[
						'property' => 'flex-direction',
						'selector' => $css_scheme['filters'],
					],
					[
						'property' => 'flex-direction',
						'selector' => $css_scheme['filters-list'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filters_align_main_axis',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Align main axis', 'jet-smart-filters' ),
				'type'     => 'justify-content',
				'exclude'  => [
					'space-between',
					'space-around',
					'space-evenly',
				],
				'css'      => [
					[
						'property' => 'justify-content',
						'selector' => $css_scheme['filters-list'],
					],
				],
				'required' => [ 'filters_direction', '=', 'row' ],
			]
		);

		$this->register_jet_control(
			'filters_align_cross_axis',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Align cross axis', 'jet-smart-filters' ),
				'type'  => 'align-items',
				'css'   => [
					[
						'property' => 'align-items',
						'selector' => $css_scheme['filters-list'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filters_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'gap',
						'selector' => $css_scheme['filters-list'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_filters_items_styles' );

		$this->register_jet_control(
			'filter_min_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Minimal width', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'min-width',
						'selector' => $css_scheme['filter'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_item_direction',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Direction', 'jet-smart-filters' ),
				'type'  => 'direction',
				'css'   => [
					[
						'property' => 'flex-direction',
						'selector' => $css_scheme['filter'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_item_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'gap',
						'selector' => $css_scheme['filter'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_item_label_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Label typography', 'jet-smart-filters' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['filter-label'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_item_remove_heading',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Remove', 'jet-smart-filters' ),
			]
		);

		$this->register_jet_control(
			'filter_item_remove_size',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Size', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '12px',
				'css'     => [
					[
						'property' => 'font-size',
						'selector' => $css_scheme['filter-remove'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_item_remove_offset_top',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Offset top', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'top',
						'selector' => $css_scheme['filter-remove'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_item_remove_offset_right',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Offset right', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'right',
						'selector' => $css_scheme['filter-remove'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_item_remove_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['filter-remove'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_item_remove_bg',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['filter-remove'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_item_remove_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['filter-remove'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filter_item_remove_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-smart-filters' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['filter-remove'],
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	public function register_filter_label_group() {

		$this->register_jet_control_group(
			'section_filters_label_style',
			[
				'title' => esc_html__( 'Label', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_filter_label_controls( $css_scheme = null ) {

		$this->start_jet_control_group( 'section_filters_label_style' );

		$this->register_jet_control(
			'filters_label_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['filters-title'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filters_label_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['filters-title'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filters_label_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['filters-title'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filters_label_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['filters-title'],
					],
				],
			]
		);

		$this->register_jet_control(
			'filters_label_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-smart-filters' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['filters-title'],
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	// Render element HTML
	public function render() {

		jet_smart_filters()->set_filters_used();

		$base_class           = $this->name;
		$settings             = $this->parse_jet_render_attributes( $this->get_jet_settings() );
		$provider             = ! empty( $settings['content_provider'] ) ? $settings['content_provider'] : '';

		// STEP: Content provider is empty: Show placeholder text
		if ( empty( $provider ) ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Please select content provider to show.', 'jet-smart-filters' )
				]
			);
		}

		$query_id             = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$apply_type           = ! empty( $settings['apply_type'] ) ? $settings['apply_type'] : 'ajax';
		$filters_label        = ! empty( $settings['filters_label'] ) ? $settings['filters_label'] : '';
		$additional_providers = jet_smart_filters()->utils->get_additional_providers( $settings );

		echo "<div {$this->render_attributes( '_root' )}>";

		printf(
			'<div class="%1$s jet-active-filters" data-label="%6$s" data-content-provider="%2$s" data-additional-providers="%3$s" data-apply-type="%4$s" data-query-id="%5$s">',
			$base_class,
			$provider,
			$additional_providers,
			$apply_type,
			$query_id,
			$filters_label
		);

		if ( ! $this->is_frontend ) {
			$active_filters_type = jet_smart_filters()->filter_types->get_filter_types( $this->jet_element_render );
			$active_filters_type->render_filters_sample( $settings );
		}

		echo '</div>';

		echo "</div>";

	}
}