<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

use Jet_Engine\Bricks_Views\Helpers\Options_Converter;
use Bricks\Database;
use Bricks\Helpers;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Bricks_Base extends \Jet_Engine\Bricks_Views\Elements\Base {
	public $jet_element_render = 'base';
	public $filter_id_multiple = true;

	// Set builder control groups
	public function set_control_groups() {

		$this->register_general_group();
		$this->register_filter_settings_group();
		$this->register_filter_style_group();
		$this->register_filter_label_group();
		$this->register_filter_button_group();

		if ( $this->filter_id_multiple ) {
			$this->register_filter_group_group();
		}

	}

	// Set builder controls
	public function set_controls() {

		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/base/css-scheme',
			[
				'filter'               => '.jet-filter',
				'filters-label'        => '.jet-filter-label',
				'apply-filters'        => '.apply-filters',
				'apply-filters-button' => '.apply-filters__button',
				'group'                => '.jet-filters-group',
				'group-item'           => '.jet-filters-group .jet-filter, .jet-filter .jet-filters-group .jet-select',
			]
		);

		$this->register_general_controls();
		$this->register_filter_settings_controls();
		$this->register_filter_style_controls();
		$this->register_filter_label_controls( $css_scheme );
		$this->register_filter_button_controls( $css_scheme );

		if ( $this->filter_id_multiple ) {
			$this->register_filter_group_controls( $css_scheme );
		}
	}

	public function register_general_group() {

		$this->register_jet_control_group(
			'section_general',
			[
				'title' => esc_html__( 'General', 'jet-smart-filters' ),
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

		if ( $this->name !== 'jet-smart-filters-sorting' ) {
			$this->register_jet_control(
				'filter_id',
				[
					'label'       => esc_html__( 'Select filter', 'jet-smart-filters' ),
					'type'        => 'select',
					'options'     => jet_smart_filters()->data->get_filters_by_type( $this->jet_element_render ),
					'multiple'    => $this->filter_id_multiple,
					'searchable'  => true,
					'placeholder' => esc_html__( 'Select...', 'jet-smart-filters' ),
				]
			);
		}

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
					'ajax'   => esc_html__( 'AJAX', 'jet-smart-filters' ),
					'reload' => esc_html__( 'Page reload', 'jet-smart-filters' ),
					'mixed'  => esc_html__( 'Mixed', 'jet-smart-filters' ),
				],
				'default' => 'ajax',
			]
		);

		if ( $this->name === 'jet-smart-filters-date-range' ) {
			$this->register_jet_control(
				'hide_apply_button',
				[
					'tab'     => 'content',
					'label'   => esc_html__( 'Hide apply button', 'jet-smart-filters' ),
					'type'    => 'checkbox',
					'default' => false,
				]
			);

			$this->register_jet_control(
				'apply_button_text',
				[
					'tab'            => 'content',
					'label'          => esc_html__( 'Apply button text', 'jet-smart-filters' ),
					'type'           => 'text',
					'hasDynamicData' => false,
					'default'        => esc_html__( 'Apply filter', 'jet-smart-filters' ),
					'required'       => [ 'hide_apply_button', '=', false ],
				]
			);

			$this->register_jet_control(
				'apply_button_icon',
				[
					'tab'      => 'content',
					'label'    => esc_html__( 'Apply button icon', 'jet-smart-filters' ),
					'type'     => 'icon',
					'required' => [ 'hide_apply_button', '=', false ],
				]
			);
		} else {
			$this->register_jet_control(
				'apply_on',
				[
					'tab'      => 'content',
					'label'    => esc_html__( 'Apply on', 'jet-smart-filters' ),
					'type'     => 'select',
					'options'  => [
						'value'  => esc_html__( 'Value change', 'jet-smart-filters' ),
						'submit' => esc_html__( 'Click on apply button', 'jet-smart-filters' ),
					],
					'default'  => 'value',
					'required' => [ 'apply_type', '=', [ 'ajax', 'mixed' ] ],
				]
			);

			$this->register_jet_control(
				'apply_button',
				[
					'tab'     => 'content',
					'label'   => esc_html__( 'Show apply button', 'jet-smart-filters' ),
					'type'    => 'checkbox',
					'default' => false,
				]
			);

			$this->register_jet_control(
				'apply_button_text',
				[
					'tab'            => 'content',
					'label'          => esc_html__( 'Apply button text', 'jet-smart-filters' ),
					'type'           => 'text',
					'hasDynamicData' => false,
					'default'        => esc_html__( 'Apply filter', 'jet-smart-filters' ),
					'required'       => [ 'apply_button', '=', true ],
				]
			);
		}

		if ( $this->name === 'jet-smart-filters-sorting' ) {
			$this->register_jet_control(
				'label',
				[
					'tab'            => 'content',
					'label'          => esc_html__( 'Label', 'jet-smart-filters' ),
					'type'           => 'text',
					'hasDynamicData' => false,
				]
			);

			$this->register_jet_control(
				'label_block',
				[
					'tab'      => 'content',
					'label'    => esc_html__( 'Label Block', 'jet-smart-filters' ),
					'type'     => 'checkbox',
					'default'  => true,
					'required' => [ 'label', '!=', '' ],
				]
			);

			$this->register_jet_control(
				'placeholder',
				[
					'tab'            => 'content',
					'label'          => esc_html__( 'Placeholder', 'jet-smart-filters' ),
					'type'           => 'text',
					'hasDynamicData' => false,
					'default'        => esc_html__( 'Sort...', 'jet-smart-filters' ),
				]
			);
		} else {
			$this->register_jet_control(
				'show_label',
				[
					'tab'     => 'content',
					'label'   => esc_html__( 'Show filter label', 'jet-smart-filters' ),
					'type'    => 'checkbox',
					'default' => false,
				]
			);
		}

		if ( $this->name === 'jet-smart-filters-rating' ) {
			$this->register_jet_control(
				'rating_icon',
				[
					'tab'     => 'content',
					'label'   => esc_html__( 'Rating icon', 'jet-smart-filters' ),
					'type'    => 'icon',
					'default' => [
						'library' => 'fa',
						'icon'    => 'fa fa-star',
					],
				]
			);
		}

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

	public function register_filter_label_group() {

		$this->register_jet_control_group(
			'section_label_style',
			[
				'title'    => esc_html__( 'Label', 'jet-smart-filters' ),
				'tab'      => 'style',
				'required' => [ 'show_label', '=', true ],
			]
		);
	}

	public function register_filter_label_controls( $css_scheme = null ) {

		$this->start_jet_control_group( 'section_label_style' );

		$this->register_jet_control(
			'label_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-smart-filters' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['filters-label'],
					],
				],
			]
		);

		$this->register_jet_control(
			'label_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['filters-label'],
					],
				],
			]
		);

		$this->register_jet_control(
			'label_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['filters-label'],
					],
				],
			]
		);

		$this->register_jet_control(
			'label_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['filters-label'],
					],
				],
			]
		);

		$this->register_jet_control(
			'label_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-smart-filters' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['filters-label'],
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
				'title'    => esc_html__( 'Button', 'jet-smart-filters' ),
				'tab'      => 'style',
				'required' => [ 'apply_button', '=', true ],
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

		$this->register_jet_control(
			'filter_apply_button_alignment',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'type'  => 'align-items',
				'css'   => [
					[
						'property' => 'align-items',
						'selector' => $css_scheme['apply-filters'],
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	public function register_filter_group_group() {

		$this->register_jet_control_group(
			'section_group_filters_style',
			[
				'title' => esc_html__( 'Grouped Filters', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_filter_group_controls( $css_scheme = null ) {

		$this->start_jet_control_group( 'section_group_filters_style' );

		$this->register_jet_control(
			'columns',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Columns', 'jet-smart-filters' ),
				'type'    => 'number',
				'default' => 1,
				'min'     => 1,
				'max'     => 12,
				'css'     => [
					[
						'property' => '--columns',
						'selector' => $css_scheme['group'],
					],
				],
			],
		);

		$this->register_jet_control(
			'group_filters_gap_h',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Horizontal gap', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'column-gap',
						'selector' => $css_scheme['group'],
					],
					[
						'property' => '--column-gap',
						'selector' => $css_scheme['group'],
					],
				],
			]
		);

		$this->register_jet_control(
			'group_filters_gap_v',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Vertical gap', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'row-gap',
						'selector' => $css_scheme['group'],
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	/**
	 * Register filter settings controls. Specific for each widget.
	 *
	 * @return void
	 */
	public function register_filter_settings_group() {
	}

	public function register_filter_settings_controls() {
	}

	/**
	 * Register filter style controls. Specific for each widget.
	 *
	 * @return void
	 */
	public function register_filter_style_group() {
	}

	public function register_filter_style_controls() {
	}

	// Render element HTML
	public function render() {

		jet_smart_filters()->set_filters_used();

		$this->enqueue_scripts();

		$settings   = $this->parse_jet_render_attributes( $this->get_jet_settings() );
		$filter_ids = ! empty( $settings['filter_id'] ) ? $settings['filter_id'] : '';

		// STEP: Select filter is empty: Show placeholder text
		if ( empty( $filter_ids ) ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Please select filter to show.', 'jet-smart-filters' )
				]
			);
		}

		$provider = ! empty( $settings['content_provider'] ) ? $settings['content_provider'] : '';

		// STEP: Content provider is empty: Show placeholder text
		if ( empty( $provider ) ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Please select content provider to show.', 'jet-smart-filters' )
				]
			);
		}

		$base_class      = $this->name;
		$indexer_class   = '';
		$show_counter    = false;
		$show_items_rule = 'show';
		$group           = false;

		if ( ! is_array( $filter_ids ) ) {
			$filter_ids = array( $filter_ids );
		}

		if ( 1 < count( $filter_ids ) ) {
			$group = true;
		}

		$apply_type = ! empty( $settings['apply_type'] ) ? $settings['apply_type'] : 'ajax';
		$apply_on   = ! empty( $settings['apply_on'] ) ? $settings['apply_on'] : 'value';

		if ( 'submit' === $apply_on && in_array( $apply_type, [ 'ajax', 'mixed' ] ) ) {
			$apply_type = $apply_type . '-reload';
		}

		$query_id          = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$show_label        = ! empty( $settings['show_label'] ) ? filter_var( $settings['show_label'], FILTER_VALIDATE_BOOLEAN ) : false;
		$show_items_label  = ! empty( $settings['show_items_label'] ) ? $settings['show_items_label'] : false;
		$show_decorator    = ! empty( $settings['show_decorator'] ) ? $settings['show_decorator'] : false;
		$apply_indexer     = ! empty( $settings['apply_indexer'] ) ? filter_var( $settings['apply_indexer'], FILTER_VALIDATE_BOOLEAN ) : false;
		$filter_image_size = ! empty( $settings['filter_image_size'] ) ? $settings['filter_image_size'] : 'full';
		$change_items_rule = ! empty( $settings['change_items_rule'] ) ? $settings['change_items_rule'] : 'always';
		// search
		$search_enabled     = ! empty( $settings['search_enabled'] ) ? filter_var( $settings['search_enabled'], FILTER_VALIDATE_BOOLEAN ) : false;
		$search_placeholder = ! empty( $settings['search_placeholder'] ) && $search_enabled ? $settings['search_placeholder'] : false;
		// more/less
		$less_items_count = ! empty( $settings['moreless_enabled'] ) && ! empty( $settings['less_items_count'] ) ? (int) $settings['less_items_count'] : false;
		$more_text        = ! empty( $settings['more_text'] ) ? $settings['more_text'] : false;
		$less_text        = ! empty( $settings['less_text'] ) ? $settings['less_text'] : false;
		// dropdown
		$dropdown_enabled     = ! empty( $settings['dropdown_enabled'] ) ? $settings['dropdown_enabled'] : false;
		$dropdown_placeholder = ! empty( $settings['dropdown_placeholder'] ) ? $settings['dropdown_placeholder'] : false;
		// dropdown n selected
		$dropdown_n_selected_enabled = ! empty( $settings['dropdown_n_selected_enabled'] ) ? filter_var( $settings['dropdown_n_selected_enabled'], FILTER_VALIDATE_BOOLEAN ) : false;
		$dropdown_n_selected_number  = isset( $settings['dropdown_n_selected_number'] ) && $settings['dropdown_n_selected_number'] >= 0 ? $settings['dropdown_n_selected_number'] : 3;
		$dropdown_n_selected_text    = isset( $settings['dropdown_n_selected_text'] ) ? $settings['dropdown_n_selected_text'] : __( 'and {number} others', 'jet-smart-filters' );
		// scroll
		$scroll_height = ! empty( $settings['scroll_enabled'] ) && ! empty( $settings['scroll_height'] ) ? (int) $settings['scroll_height'] : false;

		if ( $apply_indexer ) {
			$indexer_class   = 'jet-filter-indexed';
			$show_counter    = ! empty( $settings['show_counter'] ) ? 'yes' : false;
			$show_items_rule = ! empty( $settings['show_items_rule'] ) ? $settings['show_items_rule'] : 'show';

			if ( $show_counter ) {
				$counter_prefix = ! empty( $settings['counter_prefix'] ) ? $settings['counter_prefix'] : false;
				$counter_suffix = ! empty( $settings['counter_suffix'] ) ? $settings['counter_suffix'] : false;
			}
		}

		if ( empty( $settings['apply_button_text'] ) ) {
			$settings['apply_button_text'] = '';
		}

		echo "<div {$this->render_attributes( '_root' )}>";

		if ( $group ) {
			echo '<div class="jet-filters-group">';
		}

		foreach ( $filter_ids as $filter_id ) {

			$filter_id = apply_filters( 'jet-smart-filters/render_filter_template/filter_id', $filter_id );

			jet_smart_filters()->admin_bar_register_item( $filter_id );

			printf(
				'<div class="%1$s jet-filter %2$s" data-indexer-rule="%3$s" data-show-counter="%4$s" data-change-counter="%5$s">',
				apply_filters( 'jet-smart-filters/render_filter_template/base_class', $base_class, $filter_id ),
				$indexer_class,
				$show_items_rule,
				$show_counter,
				$change_items_rule
			);

			$additional_providers = jet_smart_filters()->utils->get_additional_providers( $settings );

			$filter_template_args = array(
				'filter_id'            => $filter_id,
				'content_provider'     => $provider,
				'additional_providers' => $additional_providers,
				'apply_type'           => $apply_type,
				'query_id'             => $query_id,
				'show_label'           => $show_label,
				'display_options'      => array(
					'show_items_label'  => $show_items_label,
					'show_decorator'    => $show_decorator,
					'filter_image_size' => $filter_image_size,
					'show_counter'      => $show_counter,
				),
			);

			if ( ! empty( $counter_prefix ) ) {
				$filter_template_args['display_options']['counter_prefix'] = $counter_prefix;
			}

			if ( ! empty( $counter_suffix ) ) {
				$filter_template_args['display_options']['counter_suffix'] = $counter_suffix;
			}

			// search
			if ( $search_enabled ) {
				$filter_template_args['search_enabled'] = $search_enabled;
			}
			if ( $search_placeholder ) {
				$filter_template_args['search_placeholder'] = $search_placeholder;
			}
			// more/less
			if ( $less_items_count ) {
				$filter_template_args['less_items_count'] = $less_items_count;
			}
			if ( $more_text ) {
				$filter_template_args['more_text'] = $more_text;
			}
			if ( $less_text ) {
				$filter_template_args['less_text'] = $less_text;
			}
			//dropdown
			if ( $dropdown_enabled ) {
				$filter_template_args['dropdown_enabled'] = $dropdown_enabled;
			}
			if ( $dropdown_placeholder ) {
				$filter_template_args['dropdown_placeholder'] = $dropdown_placeholder;
			}
			//dropdown n selected
			if ( $dropdown_n_selected_enabled ) {
				$filter_template_args['dropdown_n_selected_enabled'] = $dropdown_n_selected_enabled;
				$filter_template_args['dropdown_n_selected_number'] = $dropdown_n_selected_number;
				$filter_template_args['dropdown_n_selected_text'] = $dropdown_n_selected_text;
			}
			// scroll
			if ( $scroll_height ) {
				$filter_template_args['scroll_height'] = $scroll_height;
			}
			//indexer
			if ( $apply_indexer ) {
				$filter_template_args['apply_indexer'] = $apply_indexer;
			}

			include jet_smart_filters()->get_template( 'common/filter-label.php' );

			jet_smart_filters()->filter_types->render_filter_template( $this->jet_element_render, $filter_template_args );

			echo '</div>';

		}

		if ( $group ) {
			echo '</div>';
		}

		include jet_smart_filters()->get_template( 'common/apply-filters.php' );

		echo "</div>";

	}
}