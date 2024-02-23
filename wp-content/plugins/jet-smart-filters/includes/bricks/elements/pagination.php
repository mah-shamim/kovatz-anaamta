<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

use Bricks\Database;
use Bricks\Helpers;
use Jet_Engine\Bricks_Views\Helpers\Options_Converter;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Pagination_Widget extends \Jet_Engine\Bricks_Views\Elements\Base {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-pagination'; // Make sure to prefix your elements
	public $icon = 'jet-smart-filters-icon-pagination'; // Themify icon font class
	public $css_selector = '.jet-filters-pagination__link, .jet-filters-pagination__dots'; // Default CSS selector
	public $scripts = [ 'JetSmartFiltersBricksInit' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'pagination';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Pagination', 'jet-smart-filters' );
	}

	// Set builder control groups
	public function set_control_groups() {

		$this->register_jet_control_group(
			'section_general',
			[
				'title' => esc_html__( 'General', 'jet-smart-filters' ),
				'tab'   => 'content',
			]
		);

		$this->register_jet_control_group(
			'section_controls',
			[
				'title' => esc_html__( 'Controls', 'jet-smart-filters' ),
				'tab'   => 'content',
			]
		);

		$this->register_jet_control_group(
			'pagination_items_style',
			[
				'title' => esc_html__( 'Items', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'pagination_load_more_style',
			[
				'title'    => esc_html__( 'Load More', 'jet-smart-filters' ),
				'tab'      => 'style',
				'required' => [ 'enable_load_more', '=', true ],
			]
		);
	}

	// Set builder controls
	public function set_controls() {

		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/pagination/css-scheme',
			[
				'container'               => '.jet-smart-filters-pagination',
				'pagination'              => '.jet-filters-pagination',
				'pagination-item'         => '.jet-filters-pagination__item',
				'pagination-link'         => '.jet-filters-pagination__link',
				'pagination-link-current' => '.jet-filters-pagination__current .jet-filters-pagination__link',
				'pagination-dots'         => '.jet-filters-pagination__dots',
				'pagination-load-more'    => '.jet-filters-pagination__load-more .jet-filters-pagination__link',
			]
		);

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
				'label'      => esc_html__( 'Pagination for:', 'jet-smart-filters' ),
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
					'ajax'   => esc_html__( 'AJAX', 'jet-smart-filters' ),
					'reload' => esc_html__( 'Page reload', 'jet-smart-filters' ),
					'mixed'  => esc_html__( 'Mixed', 'jet-smart-filters' ),
				],
				'default' => 'ajax',
			]
		);

		$this->register_jet_control(
			'query_id',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Query ID', 'jet-smart-filters' ),
				'type'        => 'text',
				'description' => esc_html__( 'Set unique query ID if you use multiple widgets of same provider on the page. Same ID you need to set for filtered widget.', 'jet-smart-filters' ),
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_controls' );

		$this->register_jet_control(
			'enable_items',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Enable Items', 'jet-smart-filters' ),
				'type'    => 'checkbox',
				'default' => true,
			]
		);

		$this->register_jet_control(
			'pages_center_offset',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Items center offset', 'jet-smart-filters' ),
				'type'        => 'number',
				'default'     => 0,
				'min'         => 0,
				'max'         => 50,
				'step'        => 1,
				'description' => esc_html__( 'Set number of items to either side of current page, not including current page.Set 0 to show all items.', 'jet-smart-filters' ),
				'required'    => [ 'enable_items', '=', true ],
			]
		);

		$this->register_jet_control(
			'pages_end_offset',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Items edge offset', 'jet-smart-filters' ),
				'type'        => 'number',
				'default'     => 0,
				'min'         => 0,
				'max'         => 50,
				'step'        => 1,
				'description' => esc_html__( 'Set number of items on either the start and the end list edges.', 'jet-smart-filters' ),
				'required'    => [ 'enable_items', '=', true ],
			]
		);

		$this->register_jet_control(
			'enable_prev_next',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Enable prev/next buttons', 'jet-smart-filters' ),
				'type'    => 'checkbox',
				'default' => true,
			]
		);

		$this->register_jet_control(
			'prev_text',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Prev text', 'jet-smart-filters' ),
				'type'     => 'text',
				'default'  => esc_html__( 'Prev', 'jet-smart-filters' ),
				'required' => [ 'enable_prev_next', '=', true ],
			]
		);

		$this->register_jet_control(
			'next_text',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Next text', 'jet-smart-filters' ),
				'type'     => 'text',
				'default'  => esc_html__( 'Next', 'jet-smart-filters' ),
				'required' => [ 'enable_prev_next', '=', true ],
			]
		);

		$this->register_jet_control(
			'enable_load_more',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Enable Load More', 'jet-smart-filters' ),
				'type'    => 'checkbox',
				'default' => true,
			]
		);

		$this->register_jet_control(
			'load_more_text',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Load More Text', 'jet-smart-filters' ),
				'type'     => 'text',
				'default'  => esc_html__( 'Load More', 'jet-smart-filters' ),
				'required' => [ 'enable_load_more', '=', true ],
			]
		);

		$this->register_jet_control(
			'autoscroll',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Enable autoscroll', 'jet-smart-filters' ),
				'description' => esc_html__( 'Autoscroll to top of the provider when reloading content via AJAX.', 'jet-smart-filters' ),
				'type'        => 'checkbox',
				'default'     => true,
				'required'    => [ 'apply_type', '=', [ 'ajax', 'mixed' ] ],
			]
		);

		$this->register_jet_control(
			'provider_top_offset',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Provider top offset', 'jet-smart-filters' ),
				'type'     => 'number',
				'default'  => 0,
				'min'      => 0,
				'max'      => 999,
				'step'     => 1,
				'required' => [ 'autoscroll', '=', true ]
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'pagination_items_style' );

		$this->register_jet_control(
			'pagination_items_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Item width', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'min-width',
						'selector' => $css_scheme['pagination-item'],
					],
				],
			]
		);

		$this->register_jet_control(
			'pagination_items_gap',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Gap', 'jet-smart-filters' ),
				'type'    => 'number',
				'units'   => true,
				'default' => '12px',
				'css'     => [
					[
						'property' => 'gap',
						'selector' => $css_scheme['pagination'],
					],
				],
			]
		);

		$this->register_jet_control(
			'pagination_items_align_main_axis',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Align main axis', 'jet-smart-filters' ),
				'type'  => 'justify-content',
				'css'   => [
					[
						'property' => 'justify-content',
						'selector' => $css_scheme['pagination'],
					],
				],
			]
		);

		$this->register_jet_control(
			'pagination_item_current',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Current Item', 'jet-smart-filters' ),
			]
		);

		$this->register_jet_control(
			'pagination_item_color_current',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['pagination-link-current'],
					]
				],
			]
		);

		$this->register_jet_control(
			'pagination_item_bg_color_current',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['pagination-link-current'],
					]
				],
			]
		);

		$this->register_jet_control(
			'pagination_item_border_color_current',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['pagination-link-current'],
					]
				],
			]
		);

		$this->register_jet_control(
			'pagination_item_dots',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Dots Item', 'jet-smart-filters' ),
			]
		);

		$this->register_jet_control(
			'pagination_item_color_dots',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['pagination-dots'],
					]
				],
			]
		);

		$this->register_jet_control(
			'pagination_item_bg_color_dots',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['pagination-dots'],
					]
				],
			]
		);

		$this->register_jet_control(
			'pagination_item_border_color_dots',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['pagination-dots'],
					]
				],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'pagination_load_more_style' );

		$this->register_jet_control(
			'pagination_load_more_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['pagination-load-more'],
					],
				],
			]
		);

		$this->register_jet_control(
			'pagination_load_more_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['pagination-load-more'],
					],
				],
			]
		);

		$this->register_jet_control(
			'pagination_load_more_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['pagination-load-more'],
					],
				],
			]
		);

		$this->register_jet_control(
			'pagination_load_more_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['pagination-load-more'],
					],
				],
			]
		);

		$this->register_jet_control(
			'pagination_load_more_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-smart-filters' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['pagination-load-more'],
					],
				],
			]
		);

		$this->register_jet_control(
			'pagination_load_more_position',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Position', 'jet-smart-filters' ),
				'type'    => 'select',
				'options' => [
					'initial' => esc_html__( 'Right', 'jet-smart-filters' ),
					'-1'      => esc_html__( 'Left', 'jet-smart-filters' ),
				],
				'default' => 'initial',
				'css'     => [
					[
						'property' => 'order',
						'selector' => '.jet-filters-pagination__load-more',
					],
				],
			]
		);

		$this->end_jet_control_group();

	}

	// Render element HTML
	public function render() {
		jet_smart_filters()->set_filters_used();

		$base_class       = $this->name;
		$settings         = $this->parse_jet_render_attributes( $this->get_jet_settings() );
		$content_provider = ! empty( $settings['content_provider'] ) ? $settings['content_provider'] : '';

		// STEP: Content provider is empty: Show placeholder text
		if ( empty( $content_provider ) ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Please select content provider to show.', 'jet-smart-filters' )
				]
			);
		}

		$apply_type = ! empty( $settings['apply_type'] ) ? $settings['apply_type'] : 'ajax';
		$query_id   = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';

		$items_enabled     = isset( $settings['enable_items'] ) ? $settings['enable_items'] : '';
		$nav_enabled       = isset( $settings['enable_prev_next'] ) ? $settings['enable_prev_next'] : '';
		$load_more_enabled = isset( $settings['enable_load_more'] ) ? $settings['enable_load_more'] : '';
		$controls          = array();

		if ( true === $items_enabled ) {
			$controls['items_enabled']  = true;
			$controls['pages_mid_size'] = ! empty( $settings['pages_center_offset'] ) ? absint( $settings['pages_center_offset'] ) : 0;
			$controls['pages_end_size'] = ! empty( $settings['pages_end_offset'] ) ? absint( $settings['pages_end_offset'] ) : 0;
		} else {
			$controls['items_enabled'] = false;
		}

		if ( true === $nav_enabled ) {
			$controls['nav_enabled'] = true;
			$controls['prev']        = $settings['prev_text'];
			$controls['next']        = $settings['next_text'];
		} else {
			$controls['nav_enabled'] = false;
		}

		if ( true === $load_more_enabled ) {
			$controls['load_more_enabled'] = true;
			$controls['load_more_text']    = $settings['load_more_text'];
		} else {
			$controls['load_more_enabled'] = false;
		}

		if ( $settings['autoscroll'] === true ) {
			$controls['provider_top_offset'] = ! empty( $settings['provider_top_offset'] ) ? absint( $settings['provider_top_offset'] ) : 0;
		}

		echo "<div {$this->render_attributes( '_root' )}>";
		printf(
			'<div
				class="%1$s"
				data-apply-provider="%2$s"
				data-content-provider="%2$s"
				data-query-id="%3$s"
				data-controls="%4$s"
				data-apply-type="%5$s"
			>',
			$base_class,
			$content_provider,
			$query_id,
			htmlspecialchars( json_encode( $controls ) ),
			$apply_type
		);

		if ( ! $this->is_frontend ) {
			$pagination_filter_type = jet_smart_filters()->filter_types->get_filter_types( $this->jet_element_render );
			$pagination_filter_type->render_pagination_sample( $controls );
		}

		echo '</div>';

		echo "</div>";
	}

	public function parse_jet_render_attributes( $attrs = [] ) {

		$attrs['enable_items']     = $attrs['enable_items'] ?? false;
		$attrs['enable_prev_next'] = $attrs['enable_prev_next'] ?? false;
		$attrs['enable_load_more'] = $attrs['enable_load_more'] ?? false;
		$attrs['autoscroll']       = $attrs['autoscroll'] ?? false;


		return $attrs;
	}
}