<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Listing_Item_Document extends Elementor\Core\Base\Document {

	/**
	 * Initialize the class.
	 *
	 * Set the raw data, the ID and the parsed settings.
	 *
	 * @since 2.9.0
	 * @access protected
	 *
	 * @param array $data Initial data.
	 */
	protected function init( $data ) {
		$data = apply_filters( 'jet-engine/elementor-views/listing-document/init-data', $data, $this );
		parent::init( $data );
	}

	public function get_name() {
		return jet_engine()->listings->get_id();
	}

	public static function get_title() {
		return __( 'Listing Item', 'jet-engine' );
	}

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['admin_tab_group'] = '';
		$properties['support_kit']     = true;
		$properties['cpt']             = [ jet_engine()->listings->post_type->slug() ];

		return $properties;
	}

	public function get_css_wrapper_selector() {
		return '.jet-listing-item.single-jet-engine.elementor-page-' . $this->get_main_id();
	}

	protected function register_controls() {

		parent::register_controls();

		$this->start_controls_section(
			'jet_listing_settings',
			array(
				'label' => __( 'Listing Settings', 'jet-engine' ),
				'tab' => Elementor\Controls_Manager::TAB_SETTINGS,
			)
		);

		$this->add_control(
			'reload_notice',
			array(
				'type' => Elementor\Controls_Manager::RAW_HTML,
				'raw'  => __( '<b>Please note:</b> You need to reload page after applying new source, changing post type or taxonomy. New meta fields and options for dynamic fields will be applied only after reloading.', 'jet-engine' ),
			)
		);

		$this->add_control(
			'listing_source',
			array(
				'label'       => esc_html__( 'Listing source:', 'jet-engine' ),
				'label_block' => true,
				'type'        => Elementor\Controls_Manager::SELECT,
				'default'     => 'posts',
				'options'     => jet_engine()->listings->post_type->get_listing_item_sources(),
			)
		);

		$this->add_control(
			'listing_post_type',
			array(
				'label'   => esc_html__( 'From post type:', 'jet-engine' ),
				'type'    => Elementor\Controls_Manager::SELECT,
				'options' => jet_engine()->listings->get_post_types_for_options(),
				'condition' => array(
					'listing_source' => array( 'posts', 'repeater' ),
				),
			)
		);

		$this->add_control(
			'listing_tax',
			array(
				'label'   => esc_html__( 'From taxonomy:', 'jet-engine' ),
				'type'    => Elementor\Controls_Manager::SELECT,
				'default' => 'right',
				'options' => jet_engine()->listings->get_taxonomies_for_options(),
				'condition' => array(
					'listing_source' => 'terms',
				),
			)
		);

		$this->add_control(
			'repeater_source',
			array(
				'label'   => esc_html__( 'Repeater source:', 'jet-engine' ),
				'type'    => Elementor\Controls_Manager::SELECT,
				'default' => 'jet_engine',
				'options' => jet_engine()->listings->repeater_sources(),
				'condition' => array(
					'listing_source' => 'repeater',
				),
			)
		);

		$this->add_control(
			'repeater_field',
			array(
				'label'       => esc_html__( 'Repeater field:', 'jet-engine' ),
				'type'        => Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'label_block' => true,
				'condition'   => array(
					'listing_source'   => 'repeater',
					'repeater_source!' => 'jet_engine_options',
				),
			)
		);

		$this->add_control(
			'repeater_option',
			array(
				'label'       => esc_html__( 'Repeater option:', 'jet-engine' ),
				'type'        => Elementor\Controls_Manager::SELECT,
				'default'     => '',
				'label_block' => true,
				'groups'      => jet_engine()->options_pages->get_options_for_select( 'repeater' ),
				'condition'   => array(
					'listing_source'  => 'repeater',
					'repeater_source' => 'jet_engine_options',
				),
			)
		);

		do_action( 'jet-engine/listings/document/custom-source-control', $this );

		$this->add_control(
			'preview_width',
			array(
				'label'      => esc_html__( 'Preview Width', 'jet-engine' ),
				'type'       => Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 300,
						'max' => 1200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} > .elementor' => 'width: {{SIZE}}{{UNIT}}; margin-left: auto; margin-right: auto;',
				),
			)
		);

		$this->add_control(
			'listing_link',
			array(
				'label'        => esc_html__( 'Make listing item clickable', 'jet-engine' ),
				'type'         => Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$meta_fields = $this->get_meta_fields_for_post_type();

		$this->add_control(
			'listing_link_source',
			array(
				'label'     => __( 'Link source', 'jet-engine' ),
				'type'      => Elementor\Controls_Manager::SELECT,
				'default'   => '_permalink',
				'groups'    => $meta_fields,
				'condition' => array(
					'listing_link' => 'yes',
				),
			)
		);

		if ( jet_engine()->options_pages ) {

			$options_pages_select = jet_engine()->options_pages->get_options_for_select( 'plain' );

			if ( ! empty( $options_pages_select ) ) {
				$this->add_control(
					'listing_link_option',
					array(
						'label'     => __( 'Option', 'jet-engine' ),
						'type'      => Elementor\Controls_Manager::SELECT,
						'default'   => '',
						'groups'    => $options_pages_select,
						'condition' => array(
							'listing_link'        => 'yes',
							'listing_link_source' => 'options_page',
						),
					)
				);
			}

		}

		/**
		 * Add 3rd-party controls for link sources
		 */
		do_action( 'jet-engine/listings/document/custom-link-source-controls', $this );

		$this->add_control(
			'listing_link_open_in_new',
			array(
				'label'        => esc_html__( 'Open in new window', 'jet-engine' ),
				'type'         => Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'listing_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'listing_link_rel_attr',
			array(
				'label'   => __( 'Add "rel" attr', 'jet-engine' ),
				'type'    => Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => \Jet_Engine_Tools::get_rel_attr_options(),
				'condition' => array(
					'listing_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'listing_link_aria_label',
			array(
				'label'   => __( 'Aria label attr / Link text', 'jet-engine' ),
				'type'    => Elementor\Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => array(
					'active' => true
				),
				'condition' => array(
					'listing_link' => 'yes',
				),
			)
		);

		$this->add_control(
			'listing_link_prefix',
			array(
				'label'       => __( 'Link prefix', 'jet-engine' ),
				'label_block' => true,
				'type'        => Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'condition'   => array(
					'listing_link' => 'yes',
				),
			)
		);

		$this->end_controls_section();

	}

	/**
	 * Get meta fields for post type
	 *
	 * @return array
	 */
	public function get_meta_fields_for_post_type() {
		return jet_engine()->listings->get_listing_link_sources();
	}

	public function get_preview_as_query_args() {

		/**
		$preview_id      = (int) $this->get_settings( 'preview_id' );
		$source          = $this->get_settings( 'listing_source' );
		$post_type       = $this->get_settings( 'listing_post_type' );
		$tax             = $this->get_settings( 'listing_tax' );
		$repeater_source = $this->get_settings( 'repeater_source' );
		$repeater_field  = $this->get_settings( 'repeater_field' );
		$repeater_option = $this->get_settings( 'repeater_option' );
		*/

		$preview = new Jet_Engine_Listings_Preview( $this->get_settings(), $this->get_main_id() );

		return apply_filters(
			'jet-engine/elementor-views/listing-document/preview-args',
			$preview->get_preview_args(), $this 
		);

	}

	public function get_elements_raw_data( $data = null, $with_html_content = false ) {

		jet_engine()->elementor_views->switch_to_preview_query();

		$editor_data = parent::get_elements_raw_data( $data, $with_html_content );

		jet_engine()->elementor_views->restore_current_query();

		return $editor_data;
	}

	public function render_element( $data ) {

		jet_engine()->elementor_views->switch_to_preview_query();

		$render_html = parent::render_element( $data );

		jet_engine()->elementor_views->restore_current_query();

		return $render_html;
	}

	public function get_elements_data( $status = 'publish' ) {

		if ( ! isset( $_GET[ jet_engine()->post_type->slug() ] ) || ! isset( $_GET['preview'] ) ) {
			return parent::get_elements_data( $status );
		}

		jet_engine()->elementor_views->switch_to_preview_query();

		$elements = parent::get_elements_data( $status );

		jet_engine()->elementor_views->restore_current_query();

		return $elements;
	}

}
