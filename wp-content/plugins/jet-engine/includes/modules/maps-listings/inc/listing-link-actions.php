<?php
namespace Jet_Engine\Modules\Maps_Listings;

class Listing_Link_Actions {

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		add_filter( 'jet-engine/listings/link/sources',                array( $this, 'add_map_sources' ) );
		add_filter( 'jet-engine/listings/frontend/custom-listing-url', array( $this, 'get_map_action_url' ), 10, 2 );

		// Add additional controls
		add_action(
			'jet-engine/listings/document/custom-link-source-controls',
			array( $this, 'register_elementor_controls' )
		);

		add_action(
			'elementor/element/jet-listing-items/jet_listing_settings/after_section_end',
			array( $this, 'update_elementor_link_controls' )
		);

		add_filter(
			'jet-engine/blocks/editor/controls/link-settings',
			array( $this, 'register_blocks_controls' ),
			10, 2
		);

		add_action(
			'jet-engine/blocks/editor/save-settings',
			array( $this, 'save_blocks_editor_settings' )
		);

	}

	public function get_map_sources() {
		return array(
			'open_map_listing_popup'       => esc_html__( 'Open Map Listing Popup', 'jet-engine' ),
			'open_map_listing_popup_hover' => esc_html__( 'Open Map Listing Popup on Hover', 'jet-engine' ),
		);
	}

	public function get_map_sources_keys() {
		return array_keys( $this->get_map_sources() );
	}

	public function add_map_sources( $sources = array() ) {
		$sources[0]['options'] = array_merge( $sources[0]['options'], $this->get_map_sources() );
		return $sources;
	}

	public function get_map_action_url( $url = false, $settings = array() ) {

		if ( empty( $settings['listing_link_source'] ) ) {
			return $url;
		}

		if ( ! in_array( $settings['listing_link_source'], $this->get_map_sources_keys() ) ) {
			return $url;
		}

		switch ( $settings['listing_link_source'] ) {
			case 'open_map_listing_popup':
				$event = 'click';
				break;

			case 'open_map_listing_popup_hover':
				$event = 'hover';
				break;

			default:
				$event = null;
		}

		$params = array();

		if ( ! empty( $settings['listing_link_map_zoom'] ) ) {
			$params['zoom'] = $settings['listing_link_map_zoom'];
		}

		$url = jet_engine()->modules->get_module( 'maps-listings' )->instance->get_action_url( null, $event, $params );

		return $url;
	}

	public function register_elementor_controls( $document ) {
		$document->add_control(
			'listing_link_map_zoom',
			array(
				'label'     => esc_html__( 'Zoom', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::NUMBER,
				'condition' => array(
					'listing_link'        => 'yes',
					'listing_link_source' => $this->get_map_sources_keys(),
				),
			)
		);
	}

	public function update_elementor_link_controls( $document ) {
		// Update conditions
		$hide_controls = array( 'listing_link_open_in_new', 'listing_link_rel_attr' );

		foreach( $hide_controls as $hide_control ) {
			$args       = $document->get_controls( $hide_control );
			$conditions = $args['condition'];

			if ( isset( $conditions['listing_link_source!'] ) ) {
				$conditions['listing_link_source!'] = array_merge(
					$conditions['listing_link_source!'],
					$this->get_map_sources_keys()
				);
			} else {
				$conditions['listing_link_source!'] = $this->get_map_sources_keys();
			}

			$document->update_control(
				$hide_control,
				array(
					'condition' => $conditions
				)
			);
		}
	}

	public function register_blocks_controls( $link_controls, $settings ) {

		$link_controls = \Jet_Engine_Tools::array_insert_after(
			$link_controls,
			'jet_engine_listing_link_source',
			array(
				'jet_engine_listing_link_map_zoom' => array(
					'label'      => esc_html__( 'Zoom', 'jet-engine' ),
					'input_type' => 'number',
					'value'      => ! empty( $settings['listing_link_map_zoom'] ) ? $settings['listing_link_map_zoom'] : '',
					'condition'  => array(
						'jet_engine_listing_link'        => 'yes',
						'jet_engine_listing_link_source' => $this->get_map_sources_keys(),
					),
				),
			)
		);

		// Update conditions
		$hide_controls = array( 'jet_engine_listing_link_open_in_new', 'jet_engine_listing_link_rel_attr' );

		foreach( $hide_controls as $hide_control ) {
			if ( isset( $link_controls[ $hide_control ]['condition']['jet_engine_listing_link_source!'] ) ) {
				$link_controls[ $hide_control ]['condition']['jet_engine_listing_link_source!'] = array_merge(
					$link_controls[ $hide_control ]['condition']['jet_engine_listing_link_source!'],
					$this->get_map_sources_keys()
				);
			} else {
				$link_controls[ $hide_control ]['condition']['jet_engine_listing_link_source!'] = $this->get_map_sources_keys();
			}
		}

		return $link_controls;
	}

	public function save_blocks_editor_settings( $post_id ) {

		if ( ! isset( $_POST['jet_engine_listing_link_map_zoom'] ) ) {
			return;
		}

		$elementor_page_settings = get_post_meta( $post_id, '_elementor_page_settings', true );
		$elementor_page_settings['listing_link_map_zoom'] = esc_attr( $_POST[ 'jet_engine_listing_link_map_zoom' ] );

		update_post_meta( $post_id, '_elementor_page_settings', $elementor_page_settings );
	}

}