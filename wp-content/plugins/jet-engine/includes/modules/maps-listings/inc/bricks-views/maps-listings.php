<?php
namespace Jet_Engine\Modules\Maps_Listings\Bricks_Views;

use Bricks\Element;
use Jet_Engine\Bricks_Views\Elements\Listing_Grid;
use Jet_Engine\Bricks_Views\Helpers\Options_Converter;
use Jet_Engine\Bricks_Views\Helpers\Repeater;
use Jet_Engine\Modules\Maps_Listings\Module;
use Jet_Engine\Bricks_Views\Helpers\Controls_Hook_Bridge;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Maps_Listings extends Listing_Grid {
	// Element properties
	public $category = 'jetengine'; // Use predefined element category 'general'
	public $name = 'jet-engine-maps-listing'; // Make sure to prefix your elements
	public $icon = 'jet-engine-icon-map-listing'; // Themify icon font class
	public $css_selector = '.jet-map-listing'; // Default CSS selector
	public $scripts = [ 'jetEngineMapsBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'maps-listing';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'Map Listing', 'jet-engine' );
	}

	// Set builder control groups
	public function set_control_groups() {

		$this->register_jet_control_group(
			'section_general',
			[
				'title' => esc_html__( 'General', 'jet-engine' ),
				'tab'   => 'content',
			]
		);

		$this->register_jet_control_group(
			'section_marker_settings',
			[
				'title' => esc_html__( 'Marker', 'jet-engine' ),
				'tab'   => 'content',
			]
		);

		$this->register_jet_control_group(
			'section_popup_settings',
			[
				'title' => esc_html__( 'Popup', 'jet-engine' ),
				'tab'   => 'content',
			]
		);

		$this->register_group_query_settings();
		$this->register_group_visibility_settings();

		$this->register_jet_control_group(
			'section_marker_style',
			[
				'title' => esc_html__( 'Marker', 'jet-engine' ),
				'tab'   => 'style',
			]
		);


	}

	// Set builder controls
	public function set_controls() {

		$hooks = new Controls_Hook_Bridge( $this );

		$this->start_jet_control_group( 'section_general' );

		$this->register_jet_control(
			'lisitng_id',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Listing', 'jet-engine' ),
				'type'        => 'select',
				'options'     => jet_engine()->listings->get_listings_for_options(),
				'inline'      => true,
				'clearable'   => false,
				'searchable'  => true,
				'pasteStyles' => false,
			]
		);

		$this->register_jet_control(
			'address_field',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Address meta field', 'jet-engine' ),
				'type'           => 'text',
				'hasDynamicData' => false,
				'description'    => esc_html__( 'Set meta field key to get address from (for human-readable addresses). To get address from multiple meta fields, combine these fields names with "+" sign. For example: state+city+street', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'add_lat_lng',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Use Lat Lng address meta field', 'jet-engine' ),
				'type'        => 'checkbox',
				'default'     => false,
				'description' => esc_html__( 'Check this if you want to get item address for the map by latitude and longitude stored directly in the meta field', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'lat_lng_address_field',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Lat Lng address meta field', 'jet-engine' ),
				'type'           => 'text',
				'hasDynamicData' => false,
				'description'    => esc_html__( 'Set meta field key to get latitude and longitude from. To get address from latitude and longitude meta fields, combine these fields names with "+" sign. For example: _lat+_lng. Latitude field always should be first', 'jet-engine' ),
				'required'       => [ 'add_lat_lng', '=', true ],
			]
		);

		$this->register_jet_control(
			'map_height',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Map Height', 'jet-engine' ),
				'type'           => 'text',
				'hasDynamicData' => false,
				'default'        => 300,
				'description'    => esc_html__( 'Set height of the map in pixels', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'posts_num',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Posts number', 'jet-engine' ),
				'type'    => 'number',
				'default' => 6,
				'min'     => 1,
				'max'     => 1000,
			]
		);

		$this->register_jet_control(
			'auto_center',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Automatically detect map center', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => true,
			]
		);

		$this->register_jet_control(
			'custom_center',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Map center', 'jet-engine' ),
				'type'     => 'textarea',
				'required' => [ 'auto_center', '=', false ],
			]
		);

		$this->register_jet_control(
			'custom_zoom',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Custom zoom', 'jet-engine' ),
				'type'     => 'number',
				'default'  => 11,
				'min'      => 1,
				'max'      => 20,
				'required' => [ 'auto_center', '=', false ],
			]
		);

		$this->register_jet_control(
			'max_zoom',
			[
				'tab'   => 'content',
				'label' => esc_html__( 'Max zoom', 'jet-engine' ),
				'type'  => 'number',
				'min'   => 1,
				'max'   => 20,
			]
		);

		$this->register_jet_control(
			'min_zoom',
			[
				'tab'   => 'content',
				'label' => esc_html__( 'Min zoom', 'jet-engine' ),
				'type'  => 'number',
				'min'   => 1,
				'max'   => 10,
			]
		);

		$this->add_provider_controls( 'section_general' );

		$this->register_jet_control(
			'centering_on_open',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Centering Map when click on marker', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => false,
			]
		);

		$this->register_jet_control(
			'zoom_on_open',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Zoom Map', 'jet-engine' ),
				'type'     => 'number',
				'min'      => 1,
				'max'      => 20,
				'required' => [ 'centering_on_open', '=', true ],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_marker_settings' );

		$this->register_jet_control(
			'marker_type',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Marker type', 'jet-engine' ),
				'type'    => 'select',
				'options' => Module::instance()->get_marker_types(),
				'default' => 'icon',
			]
		);

		$this->register_jet_control(
			'marker_image',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Image', 'jet-engine' ),
				'type'     => 'image',
				'required' => [ 'marker_type', '=', 'image' ],
			]
		);

		$this->register_jet_control(
			'marker_icon',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Icon', 'jet-engine' ),
				'type'     => 'icon',
				'required' => [ 'marker_type', '=', 'icon' ],
				'default'  => [
					'library' => 'fontawesomeSolid',
					'icon'    => 'fas fa-location-dot',
				],
			]
		);

		$meta_fields = $this->get_meta_fields_list();

		$this->register_jet_control(
			'marker_image_field',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Meta field', 'jet-engine' ),
				'type'     => 'select',
				'options'  => Options_Converter::convert_select_groups_to_options( $meta_fields ),
				'required' => [ 'marker_type', '=', 'dynamic_image' ],
			]
		);

		$this->register_jet_control(
			'marker_image_field_custom',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Or enter meta field key', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Note: this field will override Meta Field value', 'jet-engine' ),
				'required'    => [ 'marker_type', '=', 'dynamic_image' ],
			]
		);

		$this->register_jet_control(
			'marker_label_type',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Marker label', 'jet-engine' ),
				'type'     => 'select',
				'options'  => Module::instance()->get_marker_label_types(),
				'default'  => 'post_title',
				'required' => [ 'marker_type', '=', 'text' ],
			]
		);

		$this->register_jet_control(
			'marker_label_field',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Meta field', 'jet-engine' ),
				'type'     => 'select',
				'options'  => Options_Converter::convert_select_groups_to_options( $meta_fields ),
				'required' => [
					[ 'marker_type', '=', 'text' ],
					[ 'marker_label_type', '=', 'meta_field' ],
				],
			]
		);

		$this->register_jet_control(
			'marker_label_field_custom',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Or enter meta field key', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Note: this field will override Meta Field value', 'jet-engine' ),
				'required'    => [
					[ 'marker_type', '=', 'text' ],
					[ 'marker_label_type', '=', 'meta_field' ],
				],
			]
		);

		$this->register_jet_control(
			'marker_label_text',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Or enter meta field key', 'jet-engine' ),
				'type'     => 'text',
				'required' => [
					[ 'marker_type', '=', 'text' ],
					[ 'marker_label_type', '=', 'static_text' ],
				],
			]
		);

		do_action( 'jet-engine/maps-listing/widget/custom-marker-label-controls', $this, 'bricks' );

		$this->register_jet_control(
			'marker_field_filter',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Filter field output', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [ 'marker_type', '=', 'text' ],
			]
		);

		$this->register_jet_control(
			'marker_label_format_cb',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Callback', 'jet-engine' ),
				'type'        => 'select',
				'options'     => jet_engine()->listings->get_allowed_callbacks(),
				'placeholder' => esc_html__( 'Select...', 'jet-engine' ),
				'required'    => [
					[ 'marker_type', '=', 'text' ],
					[ 'marker_field_filter', '=', true ],
				],
			]
		);

		foreach ( jet_engine()->listings->get_callbacks_args() as $control_name => $control_args ) {
			
			$control_args = Options_Converter::convert( $control_args );

			if ( ! empty( $control_args['required'] ) && is_array( $control_args['required'][0] ) ) {
				$control_args['required'] = end( $control_args['required'] );
				$control_args['required'][0] = 'marker_label_format_cb';
			}

			$this->register_jet_control( $control_name, $control_args );

		}

		/**
		 * Add custom controls for Callbacks
		 */
		$hooks->do_action( 'jet-engine/map-listing/callback-controls' );

		$this->register_jet_control(
			'marker_label_custom',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Customize output', 'jet-engine' ),
				'type'     => 'checkbox',
				'default'  => false,
				'required' => [ 'marker_type', '=', 'text' ],
			]
		);

		$this->register_jet_control(
			'marker_label_custom_output',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Label format', 'jet-engine' ),
				'type'        => 'textarea',
				'default'     => '%s',
				'description' => esc_html__( '%s will be replaced with field value', 'jet-engine' ),
				'required'    => [
					[ 'marker_type', '=', 'text' ],
					[ 'marker_label_custom', '=', true ],
				],
			]
		);

		$this->register_jet_control(
			'multiple_marker_separator',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Different markers', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'multiple_marker_types',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Use different markers by conditions', 'jet-engine' ),
				'type'        => 'checkbox',
				'default'     => false,
				'description' => esc_html__( 'Previously set marker will be used as default if conditions not met', 'jet-engine' ),
			]
		);

		$markers_repeater = new Repeater();

		$markers_repeater->add_control(
			'marker_type',
			[
				'label'   => esc_html__( 'Marker type', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'image' => esc_html__( 'Image', 'jet-engine' ),
					'icon'  => esc_html__( 'Icon', 'jet-engine' ),
				],
				'default' => 'image',
			]
		);

		$markers_repeater->add_control(
			'marker_image',
			[
				'label'    => esc_html__( 'Image', 'jet-engine' ),
				'type'     => 'image',
				'required' => [ 'marker_type', '=', 'image' ],
			]
		);

		$markers_repeater->add_control(
			'marker_icon',
			[
				'label'    => esc_html__( 'Icon', 'jet-engine' ),
				'type'     => 'icon',
				'required' => [ 'marker_type', '=', 'icon' ],
			]
		);

		$markers_repeater->add_control(
			'apply_type',
			[
				'label'   => esc_html__( 'Apply this marker if', 'jet-engine' ),
				'type'    => 'select',
				'options' => [
					'meta_field' => esc_html__( 'Post meta field is equal to value', 'jet-engine' ),
					'post_term'  => esc_html__( 'Post has term', 'jet-engine' ),
				],
				'default' => 'meta_field',
			]
		);

		$markers_repeater->add_control(
			'field_name',
			[
				'label'    => esc_html__( 'Meta field', 'jet-engine' ),
				'type'     => 'select',
				'options'  => Options_Converter::convert_select_groups_to_options( $meta_fields ),
				'required' => [ 'apply_type', '=', 'meta_field' ],
			]
		);

		$markers_repeater->add_control(
			'field_name_custom',
			[
				'label'       => esc_html__( 'Or enter meta field key', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'Note: this field will override Meta Field value', 'jet-engine' ),
				'required'    => [ 'apply_type', '=', 'meta_field' ],
			]
		);

		$markers_repeater->add_control(
			'field_value',
			[
				'label'    => esc_html__( 'Field value', 'jet-engine' ),
				'type'     => 'text',
				'required' => [ 'apply_type', '=', 'meta_field' ],
			]
		);

		$markers_repeater->add_control(
			'tax_name',
			[
				'label'       => esc_html__( 'Taxonomy slug', 'jet-engine' ),
				'type'        => 'text',
				'description' => esc_html__( 'You can find this slug in the address bar of taxonomy edit page', 'jet-engine' ),
				'required'    => [ 'apply_type', '=', 'post_term' ],
			]
		);

		$markers_repeater->add_control(
			'term_name',
			[
				'label'    => esc_html__( 'Term name, slug or ID', 'jet-engine' ),
				'type'     => 'text',
				'required' => [ 'apply_type', '=', 'post_term' ],
			]
		);

		$this->register_jet_control(
			'multiple_markers',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Repeater', 'jet-engine' ),
				'type'        => 'repeater',
				'placeholder' => esc_html__( 'Marker', 'jet-engine' ),
				'fields'      => $markers_repeater->get_controls(),
				'required'    => [ 'multiple_marker_types', '=', true ],
			]
		);

		$this->register_jet_control(
			'marker_clustering',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Marker clustering', 'jet-engine' ),
				'type'    => 'checkbox',
				'default' => true,
			]
		);

		$this->register_jet_control(
			'cluster_max_zoom',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Cluster Max Zoom', 'jet-engine' ),
				'description' => esc_html__( 'Maximum zoom level that a marker can be part of a cluster', 'jet-engine' ),
				'type'        => 'number',
				'min'         => 1,
				'max'         => 20,
				'required'    => [ 'marker_clustering', '=', true ],
			]
		);

		$this->register_jet_control(
			'cluster_radius',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Cluster Radius', 'jet-engine' ),
				'description' => esc_html__( 'Radius of each cluster when clustering markers in px', 'jet-engine' ),
				'type'        => 'number',
				'min'         => 10,
				'required'    => [ 'marker_clustering', '=', true ],
			]
		);

		$this->end_jet_control_group();

		$this->start_jet_control_group( 'section_popup_settings' );

		$this->register_jet_control(
			'popup_width',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Marker popup width', 'jet-engine' ),
				'type'        => 'number',
				'default'     => 320,
				'min'         => 150,
				'max'         => 600,
				'description' => esc_html__( 'Set marker popup width in pixels', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'popup_offset',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Vertical offset', 'jet-engine' ),
				'type'        => 'number',
				'default'     => 40,
				'min'         => 0,
				'max'         => 200,
				'description' => esc_html__( 'Set vertical popup offset in pixels', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'popup_preloader',
			[
				'tab'         => 'content',
				'label'       => esc_html__( 'Add popup preloader', 'jet-engine' ),
				'description' => esc_html__( 'Add box with loading animation while popup data is fetching from the server', 'jet-engine' ),
				'type'        => 'checkbox',
				'default'     => false,
			]
		);

		$this->add_provider_controls( 'section_popup_settings' );

		$this->register_jet_control(
			'popup_open_on',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Open On', 'jet-engine' ),
				'type'    => 'select',
				'options' => array(
					'click' => esc_html__( 'Click', 'jet-engine' ),
					'hover' => esc_html__( 'Hover', 'jet-engine' ),
				),
				'default' => 'click',
			]
		);

		$this->end_jet_control_group();

		$this->register_controls_query_settings();
		$this->register_controls_visibility_settings();

		$this->start_jet_control_group( 'section_marker_style' );

		$this->register_jet_control(
			'marker_width',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Width', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'width',
						'selector' => '.jet-map-marker-wrap, .jet-map-marker-image',
					],
				],
			]
		);

		$this->register_jet_control(
			'marker_text_separator',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Marker text', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'marker_typography',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Typography', 'jet-engine' ),
				'type'  => 'typography',
				'css'   => [
					[
						'property' => 'typography',
						'selector' => '.jet-map-marker-wrap',
					],
				],
			]
		);

		$this->register_jet_control(
			'marker_bg_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Background color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'background-color',
						'selector' => '.jet-map-marker-wrap',
					],
					[
						'property' => 'border-top-color',
						'selector' => '.jet-map-marker-wrap:after',
					],
				],
			]
		);

		$this->register_jet_control(
			'marker_padding',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Padding', 'jet-engine' ),
				'type'  => 'dimensions',
				'css'   => [
					[
						'property' => 'padding',
						'selector' => '.jet-map-marker-wrap',
					],
				],
			]
		);

		$this->register_jet_control(
			'marker_border',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Border', 'jet-engine' ),
				'type'  => 'border',
				'css'   => [
					[
						'property' => 'border',
						'selector' => '.jet-map-marker-wrap',
					],
				],
			]
		);

		$this->register_jet_control(
			'marker_box_shadow',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Box shadow', 'jet-engine' ),
				'type'  => 'box-shadow',
				'css'   => [
					[
						'property' => 'box-shadow',
						'selector' => '.jet-map-marker-wrap',
					],
				],
			]
		);

		$this->register_jet_control(
			'marker_pin_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Pin size', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'border-top-width',
						'selector' => '.jet-map-marker-wrap:after',
					],
					[
						'property' => 'border-right-width',
						'selector' => '.jet-map-marker-wrap:after',
					],
					[
						'property' => 'border-left-width',
						'selector' => '.jet-map-marker-wrap:after',
					],
					[
						'property' => 'margin-bottom',
						'selector' => '.jet-map-marker-wrap',
					],
					[
						'property' => 'margin-left',
						'selector' => '.jet-map-marker-wrap:after',
						'invert'   => true,
					],
				],
			]
		);

		$this->register_jet_control(
			'marker_icon_separator',
			[
				'tab'   => 'style',
				'type'  => 'separator',
				'label' => esc_html__( 'Marker icon', 'jet-engine' ),
			]
		);

		$this->register_jet_control(
			'marker_icon_color',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Icon color', 'jet-engine' ),
				'type'  => 'color',
				'css'   => [
					[
						'property' => 'color',
						'selector' => '.jet-map-marker',
					],
					[
						'property' => 'fill',
						'selector' => '.jet-map-marker path',
					],
				],
			]
		);

		$this->register_jet_control(
			'marker_icon_size',
			[
				'tab'   => 'style',
				'label' => esc_html__( 'Icon size', 'jet-engine' ),
				'type'  => 'number',
				'units' => true,
				'css'   => [
					[
						'property' => 'font-size',
						'selector' => '.jet-map-marker',
					],
				],
			]
		);

		$this->end_jet_control_group();
	}

	// Enqueue element styles and scripts
	public function enqueue_scripts() {
		wp_enqueue_style( 'jet-engine-frontend' );
	}

	// Render element HTML
	public function render() {

		// STEP: Listing field is empty: Show placeholder text
		if ( ! $this->get_jet_settings( 'lisitng_id' ) ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Please select listing to show.', 'jet-engine' )
				]
			);
		}

		// fix scrollwheel control bug
		if ( ! isset( $this->settings['scrollwheel'] ) ) {
			$this->settings['scrollwheel'] = false;
		}

		$this->set_attribute( '_root', 'data-is-block', 'jet-engine/bricks-' . $this->jet_element_render );
		$this->set_attribute( '_root', 'class', 'jet-listing-base' );

		$this->enqueue_scripts();

		$render = $this->get_jet_render_instance();

		// STEP: Maps Listings renderer class not found: Show placeholder text
		if ( ! $render ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'Maps Listings renderer class not found', 'jet-engine' )
				]
			);
		}

		add_filter( 'jet-engine/maps-listings/marker-data', array( $this, 'prepare_marker_data' ) );

		echo "<div {$this->render_attributes( '_root' )}>";

		$render->render_content();
		echo "</div>";

		remove_filter( 'jet-engine/maps-listings/marker-data', array( $this, 'prepare_marker_data' ) );

	}

	public function prepare_marker_data( $marker ) {

		if ( $marker && isset( $marker['html'] ) ) {
			$marker['html'] = str_replace( '"', '\'', $marker['html'] );
		}

		return $marker;
	}

	public function parse_jet_render_attributes( $attrs = [] ) {
		$attrs['auto_center']          = $attrs['auto_center'] ?? false;
		$attrs['zoom_controls']        = $attrs['zoom_controls'] ?? false;
		$attrs['fullscreen_control']   = $attrs['fullscreen_control'] ?? false;
		$attrs['street_view_controls'] = $attrs['street_view_controls'] ?? false;
		$attrs['map_type_controls']    = $attrs['map_type_controls'] ?? false;
		$attrs['marker_clustering']    = $attrs['marker_clustering'] ?? false;
		$attrs['marker_icon']          = ! empty( $attrs['marker_icon'] ) ? Element::render_icon( $attrs['marker_icon'] ) : null;

		if ( ! empty( $attrs['multiple_markers'] ) ) {
			foreach ( $attrs['multiple_markers'] as &$value ) {
				$value['marker_icon'] = ! empty( $value['marker_icon'] ) ? Element::render_icon( $value['marker_icon'] ) : null;
			}
		}

		return $attrs;
	}

	public function css_selector( $mod = null ) {
		return sprintf( '%1$s%2$s', $this->css_selector, $mod );
	}

	public function add_provider_controls( $section = null ) {

		$provider = Module::instance()->providers->get_active_map_provider();
		$settings = $provider->provider_settings();

		if ( empty( $settings ) || empty( $settings[ $section ] ) ) {
			return;
		}

		foreach ( $settings[ $section ] as $key => $control ) {
			$control = Options_Converter::convert( $control );
			$this->register_jet_control( $key, $control );
		}

	}

	/**
	 * Get meta fields for post type
	 *
	 * @return array
	 */
	public function get_meta_fields_list() {

		if ( jet_engine()->meta_boxes ) {
			return jet_engine()->meta_boxes->get_fields_for_select( 'plain' );
		} else {
			return array();
		}

	}
}