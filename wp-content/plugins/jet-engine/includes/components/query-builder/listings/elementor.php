<?php
namespace Jet_Engine\Query_Builder\Listings;

use Jet_Engine\Query_Builder\Manager as Query_Manager;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define listings manager class
 */
class Elementor {

	public $source;
	public $source_meta;

	public function __construct() {

		$this->source      = Query_Manager::instance()->listings->source;
		$this->source_meta = Query_Manager::instance()->listings->source_meta;

		add_filter( 'jet-engine/elementor-views/listing-document/init-data', array( $this, 'setup_document_data' ), 10 );
		add_filter( 'elementor/document/config', array( $this, 'setup_editor_settings' ), 10, 2 );
		add_action( 'jet-engine/listings/document/custom-source-control', array( $this, 'add_elementor_listing_settings' ) );
		add_action( 'elementor/document/after_save', array( $this, 'update_settings_on_document_save' ), 10, 2 );

	}

	/**
	 * Update Elementor settings on document save
	 *
	 * @param  [type] $document [description]
	 * @param  [type] $data     [description]
	 * @return [type]           [description]
	 */
	public function update_settings_on_document_save( $document, $data ) {

		if ( empty( $data['settings'] ) || empty( $data['settings'][ $this->source_meta ] ) ) {
			return;
		}

		if ( $this->source !== $data['settings']['listing_source'] ) {
			return;
		}

		update_post_meta( $document->get_main_id(), $this->source_meta, $data['settings'][ $this->source_meta ] );

	}

	/**
	 * Add query data to listing document settings
	 *
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function setup_document_data( $data ) {

		$post_id  = $data['id'];
		$query_id = get_post_meta( $post_id, $this->source_meta, true );

		if ( $query_id ) {

			if ( empty( $data['settings'] ) ) {
				$data['settings'] = array();
			}

			$data['settings'][ $this->source_meta ] = $query_id;

		}

		return $data;

	}

	/**
	 * Add query settings to localized settings in the elementor editor
	 *
	 * @param [type] $config [description]
	 */
	public function setup_editor_settings( $config ) {

		if ( \Elementor\Plugin::instance()->editor ) {
			$post_id = \Elementor\Plugin::instance()->editor->get_post_id();
			$query_id = get_post_meta( $post_id, $this->source_meta, true );

			if ( $query_id ) {
				$config['settings']['settings'][ $this->source_meta ] = $query_id;
			}

		}

		return $config;
	}

	/**
	 * Add controls
	 */
	public function add_elementor_listing_settings( $document ) {

		$document->add_control(
			$this->source_meta,
			array(
				'label'       => esc_html__( 'Query:', 'jet-engine' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => '',
				'label_block' => true,
				'options'     => Query_Manager::instance()->get_queries_for_options(),
				'condition'   => array(
					'listing_source' => $this->source,
				),
			)
		);

	}

}
