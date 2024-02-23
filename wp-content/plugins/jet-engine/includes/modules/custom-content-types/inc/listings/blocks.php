<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Listings;

use Jet_Engine\Modules\Custom_Content_Types\Module;

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
			'jet-engine/blocks-views/editor/config/object/' . $this->manager->repeater_source,
			array( $this, 'setup_blocks_object' ), 10, 2
		);

		add_filter(
			'jet-engine/listing/render/object/' . $this->manager->source,
			array( $this, 'get_block_preview_object' ), 10, 3
		);

		add_filter(
			'jet-engine/listing/render/object/' . $this->manager->repeater_source,
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
			10, 3
		);

		add_action(
			'jet-engine/blocks/editor/save-settings',
			array( $this, 'save_editor_settings' )
		);

	}

	public function editor_js() {

		Module::instance()->query_dialog()->assets();

		wp_enqueue_script(
			'jet-engine-cct-blocks-editor',
			Module::instance()->module_url( 'assets/js/admin/blocks/blocks.js' ),
			array(),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script(
			'jet-engine-cct-blocks-editor',
			'JetEngineCCTBlocksData',
			apply_filters( 'jet-engine/custom-content-types/blocks/data', array(
				'fetchPath' => Module::instance()->query_dialog()->api_path(),
			) )
		);

	}

	public function listing_grid_atts( $attributes ) {

		$attributes['jet_cct_query'] = array(
			'type' => 'string',
			'default' => '',
		);

		return $attributes;

	}

	public function add_plain_source_fileds( $groups ) {

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {

			$fields = $instance->get_fields_list( 'plain' );
			$prefixed_fields = array();

			if ( empty( $fields ) ) {
				continue;
			}

			foreach ( $fields as $key => $label ) {
				$prefixed_fields[] = array(
					'value' => $type . '__' . $key,
					'label' => $label,
				);
			}

			$groups[] = array(
				'label'  => __( 'Content Type:', 'jet-engine' ) . ' ' . $instance->get_arg( 'name' ),
				'values' => $prefixed_fields,
			);
		}

		return $groups;
	}

	/**
	 * Setup blocks preview object ID
	 */
	public function setup_blocks_object() {

		$object = $this->manager->setup_preview();

		if ( $object ) {
			return $object->_ID;
		} else {
			return false;
		}
	}

	/**
	 * Returns preview object
	 *
	 * @param  [type] $object    [description]
	 * @param  [type] $object_id [description]
	 * @return [type]            [description]
	 */
	public function get_block_preview_object( $object, $object_id, $listing ) {

		$content_type = false;

		if ( $this->manager->source === $listing['listing_source'] ) {
			$content_type = $listing['listing_post_type'];
		} elseif ( $this->manager->repeater_source === $listing['listing_source'] ) {

			$r_field = $listing['repeater_field'];

			if ( ! empty( $r_field ) ) {
				$r_field_data = explode( '__', $r_field );
				$content_type = $r_field_data[0];
			}
		}

		if ( ! $content_type ) {
			return false;
		}

		$type = Module::instance()->manager->get_content_types( $content_type );

		if ( ! $type ) {
			return false;
		}

		$flag = \OBJECT;
		$type->db->set_format_flag( $flag );

		$item = $type->db->get_item( $object_id );

		return $item;

	}

	public function add_editor_settings( $controls, $settings, $post ) {
		$content_type  = ( $this->manager->source === $settings['source'] && ! empty( $settings['post_type'] ) ) ? $settings['post_type'] : '';
		$content_types = array( '' => __( 'Select content type...', 'jet-engine' ) );

		$page_settings = get_post_meta( $post->ID, '_elementor_page_settings', true );

		$repeater_field  = ( $this->manager->repeater_source === $settings['source'] && ! empty( $page_settings['cct_repeater_field'] ) ) ? $page_settings['cct_repeater_field'] : '';
		$repeater_fields = array( '' => __( 'Select...', 'jet-engine' ) );

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {
			$content_types[ $type ] = $instance->get_arg( 'name' );

			$fields = $instance->get_fields_list( 'repeater' );

			if ( empty( $fields ) ) {
				continue;
			}

			$prefixed_fields = array();

			foreach ( $fields as $key => $label ) {
				$prefixed_fields[ $type . '__' . $key ] = $label;
			}

			$repeater_fields[] = array(
				'label'   => __( 'Content Type:', 'jet-engine' ) . ' ' . $instance->get_arg( 'name' ),
				'options' => $prefixed_fields,
			);
		}

		$controls[ 'jet_engine_listing_' . $this->manager->source ] = array(
			'label'   => __( 'Select Content Type', 'jet-engine' ),
			'options' => $content_types,
			'value'   => $content_type,
			'source'  => $this->manager->source,
		);

		$controls[ 'jet_engine_listing_cct_repeater_field' ] = array(
			'label'  => __( 'Repeater Field', 'jet-engine' ),
			'groups' => $repeater_fields,
			'value'  => $repeater_field,
			'source' => $this->manager->repeater_source,
		);

		return $controls;
	}

	public function save_editor_settings( $post_id ) {

		if ( ! isset( $_POST[ 'jet_engine_listing_source' ] ) ) {
			return;
		}

		if ( ! in_array( $_POST[ 'jet_engine_listing_source' ], array( $this->manager->source, $this->manager->repeater_source ) ) ) {
			return;
		}

		switch( $_POST[ 'jet_engine_listing_source' ] ) {

			case $this->manager->source:

				if ( ! isset( $_POST[ 'jet_engine_listing_' . $this->manager->source ] ) ) {
					return;
				}

				$content_type            = esc_attr( $_POST[ 'jet_engine_listing_' . $this->manager->source ] );
				$listing_settings        = get_post_meta( $post_id, '_listing_data', true );
				$elementor_page_settings = get_post_meta( $post_id, '_elementor_page_settings', true );

				$listing_settings['post_type']                = $content_type;
				$elementor_page_settings['listing_post_type'] = $content_type;
				$elementor_page_settings['cct_type']          = $content_type;

				update_post_meta( $post_id, '_listing_data', $listing_settings );
				update_post_meta( $post_id, '_elementor_page_settings', $elementor_page_settings );
				break;

			case $this->manager->repeater_source:

				if ( ! isset( $_POST[ 'jet_engine_listing_cct_repeater_field' ] ) ) {
					return;
				}

				$repeater_field          = esc_attr( $_POST[ 'jet_engine_listing_cct_repeater_field' ] );
				$elementor_page_settings = get_post_meta( $post_id, '_elementor_page_settings', true );

				$elementor_page_settings['repeater_field']     = $repeater_field;
				$elementor_page_settings['cct_repeater_field'] = $repeater_field;

				update_post_meta( $post_id, '_elementor_page_settings', $elementor_page_settings );
				break;
		}
	}

}
