<?php

namespace Jet_Search\Bricks_Views\Elements;

//use Bricks;
use Bricks\Element;
//use Bricks\Elements;
use Jet_Search\Bricks_Views\Helpers\Options_Converter;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
//search-suggestions
class Jet_Search_Bricks_Search_Suggestions extends Base {

	// Element properties
	public $category     = 'jetsearch'; // Use predefined element category 'general'
	public $name         = 'jet-search-search-suggestions'; // Make sure to prefix your elements
	public $icon         = 'jet-search-icon-suggestions'; // Themify icon font class
	public $css_selector = '.jet-search-suggestions'; // Default CSS selector
	public $scripts      = [ 'jetSearchBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'search-suggestions';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Search Suggestions', 'jet-search' );
	}

	// Set builder control groups
	public function set_control_groups() {
		$this->register_jet_control_group(
			'section_search_form_settings',
			[
				'title' => esc_html__( 'Search Form', 'jet-search' ),
				'tab'   => 'content',
			]
		);

		if ( function_exists( 'WC' ) ) {
			$this->register_jet_control_group(
				'section_woocommerce',
				[
					'title' => esc_html__( 'WooCommerce', 'jet-search' ),
					'tab'   => 'content',
				]
			);
		}

		$this->register_jet_control_group(
			'section_search_form_style',
			[
				'title' => esc_html__( 'Search Form', 'jet-search' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_search_input_style',
			[
				'title' => esc_html__( 'Input Field', 'jet-search' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_search_submit_style',
			[
				'title'    => esc_html__( 'Submit Button', 'jet-search' ),
				'tab'      => 'style',
				'required' => [ 'show_search_submit', '!=', false ],
			]
		);

		$this->register_jet_control_group(
			'section_search_category_style',
			[
				'title'    => esc_html__( 'Categories List', 'jet-search' ),
				'tab'      => 'style',
				'required' => [ 'show_search_category_list', '!=', false ],
			]
		);

		$this->register_jet_control_group(
			'section_inline_area_style',
			[
				'title' => esc_html__( 'Inline Area', 'jet-search' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_focus_area_style',
			[
				'title' => esc_html__( 'Focus Area', 'jet-search' ),
				'tab'   => 'style',
			]
		);

		$this->register_jet_control_group(
			'section_spinner_style',
			[
				'title'    => esc_html__( 'Spinner', 'jet-search' ),
				'tab'      => 'style',
				'required' => [ 'show_search_suggestions_list_on_focus_preloader', '!=', false ],
			]
		);
	}

	// Set builder controls
	public function set_controls() {

		$css_scheme = apply_filters(
			'jet-search/search-suggestions/css-scheme',
			array(
				'form'                   => '.jet-search-suggestions__form',
				'form_focus'             => '.jet-search-suggestions__form--focus',
				'fields_holder'          => '.jet-search-suggestions__fields-holder',
				'field_wrapper'          => '.jet-search-suggestions__field-wrapper',
				'field'                  => '.jet-search-suggestions__field',
				'categories'             => '.jet-search-suggestions__categories',
				'categories_select'      => '.jet-search-suggestions__categories-select',
				'categories_select_icon' => '.jet-search-suggestions__categories-select-icon',
				'submit'                 => '.jet-search-suggestions__submit',
				'submit_icon'            => '.jet-search-suggestions__submit-icon',
				'submit_label'           => '.jet-search-suggestions__submit-label',
				'focus_area'             => '.jet-search-suggestions__focus-area',
				'focus_area_item'        => '.jet-search-suggestions__focus-area-item',
				'focus_area_item_title'  => '.jet-search-suggestions__focus-area-item-title',
				'inline_area'            => '.jet-search-suggestions__inline-area',
				'inline_area_item'       => '.jet-search-suggestions__inline-area-item',
				'inline_area_item_title' => '.jet-search-suggestions__inline-area-item-title',
				'message'                => '.jet-search-suggestions__message',
				'spinner'                => '.jet-search-suggestions__spinner',
			)
		);

		/**
		 * `Search Form` Section
		 */

		$this->start_jet_control_group( 'section_search_form_settings' );

		$this->register_jet_control(
			'search_placeholder_text',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Placeholder Text', 'jet-search' ),
				'type'           => 'text',
				'default'        => esc_html__( 'Search ...', 'jet-search' ),
				'hasDynamicData' => false,
			]
		);

		$this->register_jet_control(
			'show_search_suggestions_list_on_focus_preloader',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Show preloader', 'jet-search' ),
				'description' => esc_html__( 'Add box with loading animation while suggestions data is fetching from the server', 'jet-search' ),
				'type'        => 'checkbox',
				'default'     => false,
			]
		);

		$this->register_jet_control(
			'highlight_searched_text',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Highlight Searched Text', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'search_suggestions_quantity_limit',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Number of Suggestions', 'jet-search' ),
				'type'    => 'number',
				'inline'  => true,
				'min'     => 1,
				'max'     => 50,
				'default' => 10,
			],
		);

		$this->register_jet_control(
			'show_search_submit',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show Submit Button', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => true,
			]
		);

		$this->register_jet_control(
			'search_submit_label',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Submit Button Label', 'jet-search' ),
				'type'           => 'text',
				'default'        => '',
				'hasDynamicData' => false,
				'required'       => [ 'show_search_submit', '=', true ],
			]
		);

		$this->register_jet_control(
			'selected_search_submit_icon',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Submit Button Icon', 'jet-search' ),
				'type'     => 'icon',
				'default'  => [
					'library' => 'themify',
					'icon'    => 'ti-search',
				],
				'css'      => [
					[
						'selector' => $this->css_selector( '__submit-icon svg' ),
					],
				],
				'required' => [ 'show_search_submit', '=', true ],
			],
		);

		$this->register_jet_control(
			'show_search_category_list',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show Categories List', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'search_taxonomy',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Taxonomy', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => \Jet_Search_Tools::get_taxonomies(),
				'default'  => 'category',
				'required' => [ 'show_search_category_list', '=', true ],
			]
		);

		$this->register_jet_control(
			'search_category_select_placeholder',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Select Placeholder', 'jet-search' ),
				'type'           => 'text',
				'default'        => esc_html__( 'All Categories', 'jet-search' ),
				'hasDynamicData' => false,
				'required'       => [ 'show_search_category_list', '=', true ],
			]
		);

		$this->register_jet_control(
			'show_search_suggestions_list_inline_start',
			[
				'type' => 'separator',
			]
		);

		$this->register_jet_control(
			'show_search_suggestions_list_inline',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show Suggestions Below Search Form', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'search_suggestions_list_inline',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Suggestions List', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => [
					'popular' => esc_html__( 'Most popular', 'jet-search' ),
					'latest'  => esc_html__( 'Latest', 'jet-search' ),
					'manual'  => esc_html__( 'Manual', 'jet-search' ),
				],
				'default'  => 'popular',
				'required' => [ 'show_search_suggestions_list_inline', '=', true ],
			]
		);

		$this->register_jet_control(
			'search_suggestions_list_inline_quantity',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Number of Suggestions', 'jet-search' ),
				'type'    => 'number',
				'inline'  => true,
				'min'     => 1,
				'max'     => 50,
				'default' => 10,
				'required' => [
					['search_suggestions_list_inline', '!=', 'manual'],
					['show_search_suggestions_list_inline', '=', true ],
				]
			],
		);

		$this->register_jet_control(
			'search_suggestions_list_inline_manual',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'List of Manual Suggestions', 'jet-search' ),
				'description'    => esc_html__( 'Write multiple suggestions by semicolon separated with "," sign.', 'jet-search' ),
				'type'           => 'textarea',
				'rows'           => 5,
				'inlineEditing'  => true,
				'default'        => '',
				'hasDynamicData' => false,
				'required'       => [
					['search_suggestions_list_inline', '=', 'manual'],
					['show_search_suggestions_list_inline', '=', true ],
				]
			]
		);

		$this->register_jet_control(
			'show_search_suggestions_list_inline_end',
			[
				'type' => 'separator',
			]
		);

		$this->register_jet_control(
			'show_search_suggestions_list_on_focus',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Show Suggestions on Input Focus', 'jet-search' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'search_suggestions_list_on_focus',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Suggestions List', 'jet-search' ),
				'type'     => 'select',
				'multiple' => false,
				'options'  => [
					'popular' => esc_html__( 'Most popular', 'jet-search' ),
					'latest'  => esc_html__( 'Latest', 'jet-search' ),
					'manual'  => esc_html__( 'Manual', 'jet-search' ),
				],
				'default'  => 'popular',
				'required' => [ 'show_search_suggestions_list_on_focus', '=', true ],
			]
		);

		$this->register_jet_control(
			'search_suggestions_list_on_focus_quantity',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Number of Suggestions', 'jet-search' ),
				'type'    => 'number',
				'inline'  => true,
				'min'     => 1,
				'max'     => 50,
				'default' => 10,
				'required' => [
					['search_suggestions_list_on_focus', '!=', 'manual'],
					['show_search_suggestions_list_on_focus', '=', true ],
				]
			],
		);

		$this->register_jet_control(
			'search_suggestions_list_on_focus_manual',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'List of Manual Suggestions', 'jet-search' ),
				'description'    => esc_html__( 'Write multiple suggestions by semicolon separated with "," sign.', 'jet-search' ),
				'type'           => 'textarea',
				'rows'           => 5,
				'inlineEditing'  => true,
				'default'        => '',
				'hasDynamicData' => false,
				'required'       => [
					['search_suggestions_list_on_focus', '=', 'manual'],
					['show_search_suggestions_list_on_focus', '=', true ],
				]
			]
		);

		$this->register_jet_control(
			'show_search_suggestions_list_on_focus_end',
			[
				'type' => 'separator',
			]
		);

		$this->register_jet_control(
			'manage_saved_suggestions',
			[
				'tab'     => 'content',
				'content' => sprintf(
					esc_html__( 'Manage Saved Suggestions %1$s', 'jet-search' ),
					'<a target="_blank" href="' . jet_search_settings()->get_settings_page_link() . '">' . esc_html__( 'here', 'jet-search' ) . '</a>', 'bricks' ),
				'type'    => 'info',
			]
		);

		$this->end_jet_control_group();

		/**
		 * `WooCommerce` Section
		 */
		if ( function_exists( 'WC' ) ) {
			$this->start_jet_control_group( 'section_woocommerce' );

			$this->register_jet_control(
				'is_product_search',
				[
					'tab'         => 'content',
					'label'       => esc_html__( 'Is Product Search', 'jet-search' ),
					'type'        => 'checkbox',
					'default'     => false,
				]
			);

			$this->end_jet_control_group();
		}

		/**
		 * `Search Form` Style Section
		 */
		$this->start_jet_control_group( 'section_search_form_style' );

		$this->register_jet_control(
			'tab_search_form_normal',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Normal', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_form_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['form'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_form_box_shadow',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['form'],
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'tab_search_form_focus',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Focus', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_form_bg_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['form_focus'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_form_border_color_focus',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Border Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['form_focus'],
					]
				],
				'required' => [ 'search_form_border', '!=', '' ],
			]
		);

		$this->register_jet_control(
			'search_form_box_shadow_focus',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'   => 'box-shadow',
				'css'    => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['form_focus'],
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'tab_search_form_focus_end',
			[
				'type'  => 'separator',
			]
		);

		$this->register_jet_control(
			'search_form_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['form'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_form_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-search' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['form'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Input Field` Style Section
		 */

		$this->start_jet_control_group( 'section_search_input_style' );

		$this->register_jet_control(
			'search_input_typography',
			[
				'tab'         => 'style',
				'label'       => esc_html__( 'Typography', 'jet-search' ),
				'description' => esc_html__( 'To properly display the Text decoration overline, you need to increase the value of Line height.', 'jet-search' ),
				'type'        => 'typography',
				'css'         => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['field'],
					],
				],
				'exclude'     => [ 'text-align', 'color' ]
			]
		);

		$this->register_jet_control(
			'search_placeholder_typography',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Placeholder Typography', 'jet-search' ),
				'type'     => 'typography',
				'css'      => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['field'] . '::placeholder',
					],
				],
				'exclude'  => [ 'text-align' ],
				'required' => [ 'search_placeholder_text', '!=', '' ],
			]
		);

		$this->register_jet_control(
			'tab_search_input_normal',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Normal', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_input_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Text Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['field'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_input_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['field'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_input_box_shadow',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['field'],
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'tab_search_input_focus',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Focus', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_input_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Text Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['form_focus'] . ' ' . $css_scheme['field'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_input_bg_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['form_focus'] . ' ' . $css_scheme['field'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_input_border_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['form_focus'] . ' ' . $css_scheme['field'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_input_box_shadow_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['form_focus'] . ' ' . $css_scheme['field'],
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'tab_search_input_focus_end',
			[
				'type'  => 'separator',
			]
		);

		$this->register_jet_control(
			'search_input_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['field'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_input_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['field'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_input_border',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Border Width', 'jet-search' ),
				'type'    => 'border',
				'css'     => [
					[
						'selector' => $css_scheme['field'],
					]
				],
				'default' => [
					'style' => 'solid',
					'color' => [
						'hex' => '#E1E5EB',
					],
					'width' => [
						'top'    => 1,
						'right'  => 1,
						'bottom' => 1,
						'left'   => 1,
					]
				]
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Submit Button` Style Section
		 */

		$this->start_jet_control_group( 'section_search_submit_style' );

		$this->register_jet_control(
			'search_submit_typography',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Typography', 'jet-search' ),
				'type'    => 'typography',
				'css'     => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['submit_label'],
					],
				],
				'exclude' => [ 'text-align', 'color' ]
			]
		);

		$this->register_jet_control(
			'search_submit_icon_font_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Icon Font Size', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'font-size',
						'selector' => $css_scheme['submit_icon']
					],
				],
				'required' => [ 'selected_search_submit_icon', '!=', '' ],
			]
		);

		$this->register_jet_control(
			'search_submit_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['submit'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_submit_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['submit'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_submit_box_shadow',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'   => 'box-shadow',
				'css'    => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['submit'],
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'search_submit_vertical_align',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Vertical Align', 'jet-search' ),
				'tooltip' => [
					'content'  => 'align-self',
					'position' => 'top-left',
				],
				'type'    => 'align-items',
				'css'     => [
					[
						'property' => 'align-self',
						'selector' => $css_scheme['submit'],
					],
				]
			]
		);

		$this->register_jet_control(
			'search_submit_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['submit'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_submit_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['submit'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_submit_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-search' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['submit'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Categories List` Style Section
		 */

		$this->start_jet_control_group( 'section_search_category_style' );

		$this->register_jet_control(
			'search_category_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Width', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'width',
						'selector' => $css_scheme['categories']
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_typography',
			[
				'tab'         => 'style',
				'label'       => esc_html__( 'Typography', 'jet-search' ),
				'description' => esc_html__( 'To properly display the Text decoration overline, you need to increase the value of Line height.', 'jet-search' ),
				'type'        => 'typography',
				'css'         => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['categories_select'],
					],
					[
						'property' => 'typography',
						'selector' => $css_scheme['categories'] . ' .chosen-single',
					],
				],
				'exclude'     => [ 'text-align', 'color' ]
			]
		);

		$this->register_jet_control(
			'search_category_icon_font_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Arrow Font Size', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'width',
						'selector' => $css_scheme['categories_select_icon'] . ' svg'
					],
					[
						'property' => 'height',
						'selector' => $css_scheme['categories_select_icon'] . ' svg'
					],
				],
			]
		);

		$this->register_jet_control(
			'tab_search_category_normal',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Normal', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_category_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Text Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['categories_select'],
					],
					[
						'property' => 'color',
						'selector' => $css_scheme['categories'] . ' .chosen-single',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_icon_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Arrow Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'fill',
						'selector' => $css_scheme['categories_select_icon'] . ' svg > *',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories_select'],
					],
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories'] . ' .chosen-single',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_box_shadow',
			[
				'tab'    => 'style',
				'label'  => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'   => 'box-shadow',
				'css'    => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['categories_select'],
					],
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['categories'] . ' .chosen-single',
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'tab_search_category_focus',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Focus', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_category_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Text Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['categories_select'] . ':focus',
					],
					[
						'property' => 'color',
						'selector' => $css_scheme['categories'] . ' .chosen-with-drop .chosen-single',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_icon_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Arrow Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'fill',
						'selector' => $css_scheme['categories_select'] . ':focus ~ ' . $css_scheme['categories_select_icon'] . ' svg > *',
					],
					[
						'property' => 'fill',
						'selector' => $css_scheme['categories'] . ' .chosen-with-drop ~ ' . $css_scheme['categories_select_icon'] . ' svg > *',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_bg_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories_select'] . ':focus',
					],
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories'] . ' .chosen-with-drop .chosen-single',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_border_color_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'border-color',
						'selector' => $css_scheme['categories_select'] . ':focus',
					],
					[
						'property' => 'border-color',
						'selector' => $css_scheme['categories'] . ' .chosen-with-drop .chosen-single',
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_box_shadow_focus',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['categories_select'] . ':focus',
					],
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['categories'] . ' .chosen-with-drop .chosen-single',
					],
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'tab_search_category_focus_end',
			[
				'type'  => 'separator',
			]
		);

		$this->register_jet_control(
			'search_category_padding',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Padding', 'jet-search' ),
				'type'     => 'dimensions',
				'css'      => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['categories_select'],
					],
					[
						'property' => 'padding',
						'selector' => $css_scheme['categories'] . ' .chosen-single',
					]
				],
				'rerender' => true,
			]
		);

		$this->register_jet_control(
			'search_category_margin',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Margin', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'margin',
						'selector' => $css_scheme['categories'],
					]
				],
			]
		);

		$this->register_jet_control(
			'search_category_border',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Border', 'jet-search' ),
				'type'    => 'border',
				'css'     => [
					[
						'property' => 'border',
						'selector' => $css_scheme['categories_select'],
					],
					[
						'property' => 'border',
						'selector' => $css_scheme['categories'] . ' .chosen-single',
					]
				],
				'default' => [
					'style' => 'solid',
					'color' => [
						'hex' => '#E1E5EB',
					],
					'width' => [
						'top'    => 1,
						'right'  => 1,
						'bottom' => 1,
						'left'   => 1,
					]
				]
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Dropdown Style', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_max_height',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Max Height', 'jet-search' ),
				'type'    => 'number',
				'units'   => true,
				'css'     => [
					[
						'property' => 'max-height',
						'selector' => $css_scheme['categories'] . ' .chosen-results'
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'margin-top',
						'selector' => $css_scheme['categories'] . ' .chosen-drop'
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories'] . ' .chosen-drop'
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_box_shadow',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Box Shadow', 'jet-search' ),
				'type'    => 'box-shadow',
				'css'     => [
					[
						'property' => 'box-shadow',
						'selector' => $css_scheme['categories'] . ' .chosen-drop',
					],
				],
				'default' => [
					'values' => [
						'offsetX' => "0",
						'offsetY' => "0",
						'blur'    => "10",
						'spread'  => "0"
					],
					'color'  => [
						'rgb' => "rgba(0, 0, 0, 0.5)"
					]
				],
				'inline' => true,
				'small'  => true,
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_padding',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Padding', 'jet-search' ),
				'type'     => 'dimensions',
				'rerender' => true,
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-search' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => $css_scheme['categories'] . ' .chosen-drop',
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_scrollbar_thumb_bg',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Scrollbar Thumb Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories'] . ' .chosen-drop ::-webkit-scrollbar-thumb'
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Dropdown Items Style', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-search' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['categories'] . ' .chosen-results li',
					],
				],
				'exclude' => [ 'text-align', 'color' ]
			]
		);

		$this->register_jet_control(
			'tab_search_category_dropdown_items_normal',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Normal', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['categories'] . ' .chosen-results li'
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories'] . ' .chosen-results li'
					],
				],
			]
		);

		$this->register_jet_control(
			'tab_search_category_dropdown_items_hov',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Hover', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_color_hov',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['categories'] . ' .chosen-results li.highlighted'
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_bg_color_hov',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['categories'] . ' .chosen-results li.highlighted'
					],
				],
			]
		);

		$this->register_jet_control(
			'tab_search_category_dropdown_items_hover_end',
			[
				'type'  => 'separator',
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['categories'] . ' .chosen-results li',
					],
				],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_border_radius',
			[
				'tab'     => 'style',
				'label'   => esc_html__( 'Border Radius', 'jet-search' ),
				'type'    => 'border',
				'css'     => [
					[
						'selector' => $css_scheme['categories'] . ' .chosen-results li',
					],
				],
				'exclude' => [ 'style', 'color', 'width'],
			]
		);

		$this->register_jet_control(
			'search_category_dropdown_items_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'margin-top',
						'selector' => $css_scheme['categories'] . ' .chosen-results li:not(:first-child)'
					],
				],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Inline Area` Style Section
		 */

		$this->start_jet_control_group( 'section_inline_area_style' );

		$this->register_jet_control(
			'inline_area_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Inline Area', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'inline_area_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'margin-top',
						'selector' => $css_scheme['inline_area'],
					],
				],
			]
		);

		$this->register_jet_control(
			'inline_area_item_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Inline Area Item', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'inline_area_item_title_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-search' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['inline_area_item_title'],
					],
				],
				'exclude' => [ 'text-align', 'color' ]
			]
		);

		$this->register_jet_control(
			'inline_area_item_column_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Column Gap', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'column-gap',
						'selector' => $css_scheme['inline_area'],
					],
				],
			]
		);

		$this->register_jet_control(
			'inline_area_item_rows_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Rows Gap', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'row-gap',
						'selector' => $css_scheme['inline_area'],
					],
				],
			]
		);

		$this->register_jet_control(
			'inline_area_item_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['inline_area_item_title'],
					],
				],
			]
		);

		$this->register_jet_control(
			'inline_area_item_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['inline_area_item_title'],
					],
				],
			]
		);

		$this->register_jet_control(
			'inline_area_item_border_radius',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Border Radius', 'jet-search' ),
				'type'     => 'border',
				'css'      => [
					[
						'selector' => $css_scheme['inline_area_item_title'],
					]
				],
				'exclude'  => [ 'style', 'color', 'width'],
				'required' => [ 'inline_area_item_bg_color', '!=', '' ],
			]
		);

		$this->register_jet_control(
			'inline_area_item_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['inline_area_item_title'],
					],
				],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Focus Area` Style Section
		 */

		$this->start_jet_control_group( 'section_focus_area_style' );

		$this->register_jet_control(
			'focus_area_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Focus Area', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'focus_area_gap',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Gap', 'jet-search' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'margin-top',
						'selector' => $css_scheme['focus_area'],
					],
				],
			]
		);

		$this->register_jet_control(
			'focus_area_item_heading',
			[
				'type'  => 'separator',
				'label' => esc_html__( 'Focus Area Item', 'jet-search' ),
			]
		);

		$this->register_jet_control(
			'focus_area_item_title_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-search' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => $css_scheme['focus_area_item_title'],
					],
				],
				'exclude' => [ 'text-align', 'color' ]
			]
		);

		$this->register_jet_control(
			'focus_area_item_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['focus_area_item'],
					],
				],
			]
		);

		$this->register_jet_control(
			'focus_area_item_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Color', 'jet-search' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => $css_scheme['focus_area_item_title'],
					],
				],
			]
		);

		$this->register_jet_control(
			'focus_area_item_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-search' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => $css_scheme['focus_area_item_title'],
					],
				],
			]
		);

		$this->register_jet_control(
			'focus_area_item_highlight',
			[
				'type'     => 'separator',
				'label'    => esc_html__( 'Results Highlight', 'jet-search' ),
				'required' => [ 'highlight_searched_text', '!=', false ],
			]
		);

		$this->register_jet_control(
			'focus_area_item_highlight_color',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'color',
						'selector' => $css_scheme['focus_area_item_title'] . ' mark'
					],
				],
				'required' => [ 'highlight_searched_text', '!=', false ],
			]
		);

		$this->register_jet_control(
			'focus_area_item_highlight_bg',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Background Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'background-color',
						'selector' => $css_scheme['focus_area_item_title'] . ' mark'
					],
				],
				'required' => [ 'highlight_searched_text', '!=', false ],
			]
		);

		$this->end_jet_control_group();

		/**
		 * `Spinner` Style Section
		 */

		$this->start_jet_control_group( 'section_spinner_style' );

		$this->register_jet_control(
			'spinner_color',
			[
				'tab'      => 'style',
				'label'    => esc_html__( 'Color', 'jet-search' ),
				'type'     => 'color',
				'css'      => [
					[
						'property' => 'color',
						'selector' => $css_scheme['spinner']
					],
				],
				'required' => [ 'show_search_suggestions_list_on_focus_preloader', '!=', false ],
			]
		);

		$this->end_jet_control_group();
	}

	// Render element HTML
	public function render() {

		parent::render();

		$settings   = $this->parse_jet_render_attributes( $this->get_jet_settings() );
		$element_id = $this->id;
		$inline_css = $this->generate_inline_css( $settings, $element_id );

		$this->set_attribute( '_root', 'class', 'brxe-' . $this->id );
		$this->set_attribute( '_root', 'class', 'brxe-jet-search-el' );
		$this->set_attribute( '_root', 'data-element-id', $this->id );

		echo "<div {$this->render_attributes( '_root' )}>";

		$render = new \Jet_Search_Suggestions_Render( $settings, $element_id );
		$render->render();

		echo "<style>" . $inline_css . "</style>";
		echo "</div>";
	}

	public function generate_inline_css( $settings, $element_id ) {
		$inline_css = '';

		$search_category_padding          = ! empty( $settings['search_category_padding'] ) ? $settings['search_category_padding'] : '';
		$search_category_dropdown_padding = ! empty( $settings['search_category_dropdown_padding'] ) ? $settings['search_category_dropdown_padding'] : '';
		$inline_area_item_bg_color        = ! empty( $settings['inline_area_item_bg_color:hover'] ) ? $settings['inline_area_item_bg_color:hover'] : '';
		$inline_area_item_color           = ! empty( $settings['inline_area_item_color:hover'] ) ? $settings['inline_area_item_color:hover'] : '';
		$inline_area_item_border_radius   = ! empty( $settings['inline_area_item_border_radius:hover']['radius'] ) ? $settings['inline_area_item_border_radius:hover']['radius'] : '';
		$focus_area_item_bg_color         = ! empty( $settings['focus_area_item_bg_color:hover'] ) ? $settings['focus_area_item_bg_color:hover'] : '';
		$focus_area_item_color            = ! empty( $settings['focus_area_item_color:hover'] ) ? $settings['focus_area_item_color:hover'] : '';
		$search_submit_color              = ! empty( $settings['search_submit_color:hover'] ) ? $settings['search_submit_color:hover'] : '';
		$search_submit_bg_color           = ! empty( $settings['search_submit_bg_color:hover'] ) ? $settings['search_submit_bg_color:hover'] : '';

		//search_category_padding
		if ( '' != $search_category_padding ) {
			$search_category_padding_left  = ! empty( $search_category_padding['left'] ) ? $search_category_padding['left'] : '';
			$search_category_padding_right = ! empty( $search_category_padding['right'] ) ? $search_category_padding['right'] : '';

			if ( '' != $search_category_padding_left ) {
				$inline_css .= "
					body.rtl #brxe-" . $element_id . " .jet-search-suggestions__categories-select-icon {
						left: " . $search_category_padding_left . "px
					}
				";
			}

			if ( '' != $search_category_padding_right ) {
				$inline_css .= "
					body:not(.rtl) #brxe-" . $element_id . " .jet-search-suggestions__categories-select-icon {
						right: " . $search_category_padding_right . "px
					}
				";
			}
		}

		//search_category_dropdown_padding
		if ( '' != $search_category_dropdown_padding ) {
			$search_category_dropdown_padding_top    = ! empty( $search_category_dropdown_padding['top'] ) ? $search_category_dropdown_padding['top'] : 0;
			$search_category_dropdown_padding_right  = ! empty( $search_category_dropdown_padding['right'] ) ? $search_category_dropdown_padding['right'] : 0;
			$search_category_dropdown_padding_bottom = ! empty( $search_category_dropdown_padding['bottom'] ) ? $search_category_dropdown_padding['bottom'] : 0;
			$search_category_dropdown_padding_left   = ! empty( $search_category_dropdown_padding['left'] ) ? $search_category_dropdown_padding['left'] : 0;

			$inline_css .= "
				#brxe-" . $element_id . " .jet-search-suggestions__categories .chosen-drop {
					padding: " . $search_category_dropdown_padding_top . "px 0 " . $search_category_dropdown_padding_bottom . "px 0;
				}

				#brxe-" . $element_id . " .jet-search-suggestions__categories .chosen-results {
					padding: 0 " . $search_category_dropdown_padding_right . "px 0 " . $search_category_dropdown_padding_left . "px
				}
			";
		}

		return $inline_css;
	}

	public function parse_jet_render_attributes( $attrs = [] ) {

		$attrs['selected_search_submit_icon']                     = isset( $attrs['selected_search_submit_icon'] ) ? Element::render_icon( $attrs['selected_search_submit_icon'] ) : null;
		$show_search_suggestions_list_inline                      = ! empty( $attrs['show_search_suggestions_list_inline'] ) ? $attrs['show_search_suggestions_list_inline'] : '';
		$show_search_suggestions_list_on_focus                    = ! empty( $attrs['show_search_suggestions_list_on_focus'] ) ? $attrs['show_search_suggestions_list_on_focus'] : '';
		$show_search_suggestions_list_on_focus_preloader          = ! empty( $attrs['show_search_suggestions_list_on_focus_preloader'] ) ? $attrs['show_search_suggestions_list_on_focus_preloader'] : '';
		$highlight_searched_text                                  = ! empty( $attrs['highlight_searched_text'] ) ? $attrs['highlight_searched_text'] : '';
		$attrs['show_search_suggestions_list_inline']             = true === $show_search_suggestions_list_inline ? 'yes' : '';
		$attrs['show_search_suggestions_list_on_focus']           = true === $show_search_suggestions_list_on_focus ? 'yes' : '';
		$attrs['show_search_suggestions_list_on_focus_preloader'] = true === $show_search_suggestions_list_on_focus_preloader ? 'yes' : '';
		$attrs['highlight_searched_text']                         = true === $highlight_searched_text ? 'yes' : '';

		return $attrs;
	}

	public function css_selector( $mod = null ) {
		return sprintf( '%1$s%2$s', $this->css_selector, $mod );
	}
}