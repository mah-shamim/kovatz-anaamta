<?php

namespace Jet_Smart_Filters\Bricks_Views\Elements;

use Bricks\Element;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Smart_Filters_Bricks_Rating extends Jet_Smart_Filters_Bricks_Base {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-rating'; // Make sure to prefix your elements
	public $icon = 'jet-smart-filters-icon-rating-filter'; // Themify icon font class
	public $css_selector = '.jet-smart-filters-rating'; // Default CSS selector
	public $scripts = [ 'JetSmartFiltersBricksInit' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'rating';
	public $filter_id_multiple = false;

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Rating Filter', 'jet-smart-filters' );
	}

	public function register_filter_style_group() {
		$this->register_jet_control_group(
			'section_stars_style',
			[
				'title' => esc_html__( 'Stars', 'jet-smart-filters' ),
				'tab'   => 'style',
			]
		);
	}

	public function register_filter_style_controls() {
		$css_scheme = apply_filters(
			'jet-smart-filters/widgets/rating/css-scheme',
			array(
				'filter-rating-stars'  => '.jet-rating-stars',
				'filter-rating-icon'   => '.jet-rating-star__icon',
				'filter'               => '.jet-filter',
				'filter-control'       => '.jet-rating__control',
				'filters-label'        => '.jet-filter-label',
				'apply-filters'        => '.apply-filters',
				'apply-filters-button' => '.apply-filters__button',
			)
		);

		$this->start_jet_control_group( 'section_stars_style' );

		$this->register_jet_control(
			'stars_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Size', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'font-size',
						'selector' => $css_scheme['filter-rating-icon'],
					],
				],
			]
		);

		$this->register_jet_control(
			'stars_gutter',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gutter', 'jet-smart-filters' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'margin-left',
						'selector' => $css_scheme['filter-rating-icon'],
					],
				],
			]
		);

		$this->register_jet_control(
			'stars_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => '--rating-color',
					],
				],
			]
		);

		$this->register_jet_control(
			'stars_selected_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Selected color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => '--rating-selected-color',
					],
				],
			]
		);

		$this->register_jet_control(
			'stars_color_second',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Selected hover color', 'jet-smart-filters' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => '--rating-hover-color',
					],
				],
			]
		);

		$this->register_jet_control(
			'stars_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-smart-filters' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['filter-rating-stars'],
					],
				],
			]
		);

		$this->register_jet_control(
			'stars_alignment',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Alignment', 'jet-smart-filters' ),
				'type'    => 'text-align',
				'exclude' => [
					'justify',
				],
				'css'     => [
					[
						'property' => 'text-align',
						'selector' => $css_scheme['filter-control'],
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

		$filter_id = apply_filters( 'jet-smart-filters/render_filter_template/filter_id', $filter_id );

		echo "<div {$this->render_attributes( '_root' )}>";

		printf( '<div class="%1$s jet-filter">', $base_class );

		$provider             = ! empty( $settings['content_provider'] ) ? $settings['content_provider'] : '';
		$query_id             = ! empty( $settings['query_id'] ) ? $settings['query_id'] : 'default';
		$show_label           = ! empty( $settings['show_label'] ) ? filter_var( $settings['show_label'], FILTER_VALIDATE_BOOLEAN ) : false;
		$additional_providers = jet_smart_filters()->utils->get_additional_providers( $settings );
		$rating_icon          = '';

		if ( ! empty( $settings['rating_icon'] ) ) {
			$rating_icon = Element::render_icon( $settings['rating_icon'] );
		}

		jet_smart_filters()->admin_bar_register_item( $filter_id );

		$apply_type = ! empty( $settings['apply_type'] ) ? $settings['apply_type'] : 'ajax';
		$apply_on   = ! empty( $settings['apply_on'] ) ? $settings['apply_on'] : 'value';

		if ( 'submit' === $apply_on && in_array( $apply_type, [ 'ajax', 'mixed' ] ) ) {
			$apply_type = $apply_type . '-reload';
		}

		if ( empty( $settings['apply_button_text'] ) ) {
			$settings['apply_button_text'] = '';
		}

		$filter_template_args = [
			'filter_id'            => $filter_id,
			'content_provider'     => $provider,
			'additional_providers' => $additional_providers,
			'query_id'             => $query_id,
			'apply_type'           => $apply_type,
			'button_text'          => $settings['apply_button_text'],
			'rating_icon'          => $rating_icon,
			'__widget_id'          => $this->id
		];

		include jet_smart_filters()->get_template( 'common/filter-label.php' );

		jet_smart_filters()->filter_types->render_filter_template( $this->jet_element_render, $filter_template_args );

		echo '</div>';

		include jet_smart_filters()->get_template( 'common/apply-filters.php' );

		echo "</div>";

	}
}