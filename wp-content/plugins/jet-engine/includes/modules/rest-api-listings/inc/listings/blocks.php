<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Listings;

use Jet_Engine\Modules\Rest_API_Listings\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Blocks {

	public $manager;

	public function __construct( $manager ) {

		$this->manager = $manager;

		add_filter(
			'jet-engine/blocks-views/editor/config/object/' . $this->manager->source,
			array( $this, 'setup_blocks_object' ), 10, 2
		);

		add_filter(
			'jet-engine/listing/render/object/' . $this->manager->source,
			array( $this, 'get_block_preview_object' ), 10, 3
		);

		/*
		Commented out this filter to prevent duplicate fields in source setting because the link source fields are added
		to blocks editor by `jet-engine/listings/dynamic-link/fields` filter. See: blocks-views/editor.php L473
		add_filter(
			'jet-engine/blocks-views/dynamic-link-sources',
			array( $this, 'add_plain_source_fileds' ), 10, 3
		);
		*/

		add_filter(
			'jet-engine/blocks-views/listing-grid/attributes',
			array( $this, 'listing_grid_atts' )
		);

		add_action(
			'jet-engine/blocks-views/editor-script/after',
			array( $this, 'editor_js' )
		);

		add_filter(
			'jet-engine/blocks/editor/controls/settings',
			array( $this, 'add_editor_settings' ),
			10, 2
		);

		add_action(
			'jet-engine/blocks/editor/save-settings',
			array( $this, 'save_editor_settings' )
		);

	}

	public function add_editor_settings( $controls, $settings ) {
		$endpoint  = ( $this->manager->source === $settings['source'] && ! empty( $settings['post_type'] ) ) ? $settings['post_type'] : '';
		$endpoints = array( '' => __( 'Select endpoint...', 'jet-engine' ) );

		foreach ( Module::instance()->settings->get() as $endpoint_args ) {
			$url = jet_engine_trim_string( $endpoint_args['url'], 55, '...' );
			$endpoints[ $endpoint_args['id'] ] = $endpoint_args['name'] . ', ' . $url;
		}

		$controls[ 'jet_engine_listing_' . $this->manager->source ] = array(
			'label'   => __( 'Select endpoint', 'jet-engine' ),
			'options' => $endpoints,
			'value'   => $endpoint,
			'source'  => $this->manager->source,
		);

		return $controls;
	}

	public function save_editor_settings( $post_id ) {

		if ( ! isset( $_POST[ 'jet_engine_listing_source' ] ) ) {
			return;
		}

		if ( $this->manager->source !== $_POST[ 'jet_engine_listing_source' ] ) {
			return;
		}

		if ( ! isset( $_POST[ 'jet_engine_listing_' . $this->manager->source ] ) ) {
			return;
		}

		$endpoint                = esc_attr( $_POST[ 'jet_engine_listing_' . $this->manager->source ] );
		$listing_settings        = get_post_meta( $post_id, '_listing_data', true );
		$elementor_page_settings = get_post_meta( $post_id, '_elementor_page_settings', true );

		$listing_settings['post_type']                = $endpoint;
		$elementor_page_settings['listing_post_type'] = $endpoint;
		$elementor_page_settings['rest_api_endpoint'] = $endpoint;

		update_post_meta( $post_id, '_listing_data', $listing_settings );
		update_post_meta( $post_id, '_elementor_page_settings', $elementor_page_settings );
	}

	public function editor_js() {
		wp_enqueue_script(
			'jet-engine-rest-api-blocks-editor',
			Module::instance()->module_url( 'assets/js/admin/blocks/blocks.js' ),
			array(),
			jet_engine()->get_version(),
			true
		);
	}

	public function listing_grid_atts( $attributes ) {

		$attributes['jet_rest_query'] = array(
			'type' => 'string',
			'default' => '',
		);

		return $attributes;

	}

	public function add_plain_source_fileds( $groups ) {
		return $this->manager->add_source_fields_for_js( $groups, 'blocks' );
	}

	/**
	 * Setup blocks preview object ID
	 */
	public function setup_blocks_object() {
		$object = $this->manager->setup_preview();
		return false;
	}

	/**
	 * Returns preview object
	 *
	 * @param  [type] $object    [description]
	 * @param  [type] $object_id [description]
	 * @return [type]            [description]
	 */
	public function get_block_preview_object( $object, $object_id, $listing ) {
		$object = $this->manager->setup_preview();
		return $object;
	}

}
