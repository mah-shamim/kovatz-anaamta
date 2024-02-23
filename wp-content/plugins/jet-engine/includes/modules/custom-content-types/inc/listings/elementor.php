<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Listings;

use Jet_Engine\Modules\Custom_Content_Types\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Elementor {

	public $manager;

	public function __construct( $manager ) {

		$this->manager = $manager;

		add_action(
			'jet-engine/listings/document/custom-source-control',
			array( $this, 'add_document_controls' )
		);

		add_action(
			'elementor/document/after_save',
			array( $this, 'update_settings_on_document_save' ),
			10, 2
		);

	}

	public function update_settings_on_document_save( $document, $data ) {

		if ( empty( $data['settings'] ) || empty( $data['settings']['listing_source'] ) ) {
			return;
		}

		if ( ! in_array( $data['settings']['listing_source'], array( $this->manager->source, $this->manager->repeater_source ) ) ) {
			return;
		}

		switch( $data['settings']['listing_source'] ) {

			case $this->manager->source:

				if ( $data['settings']['cct_type'] === $data['settings']['listing_post_type'] ) {
					return;
				}

				$prev_data = get_post_meta( $document->get_main_id(), '_elementor_page_settings', true );

				if ( ! empty( $data['settings']['cct_type'] ) ) {
					$prev_data['listing_post_type'] = $data['settings']['cct_type'];
				} else {
					$prev_data['cct_type'] = $data['settings']['listing_post_type'];
				}

				update_post_meta( $document->get_main_id(), '_elementor_page_settings', wp_slash( $prev_data ) );

				break;

			case $this->manager->repeater_source:

				if ( $data['settings']['cct_repeater_field'] === $data['settings']['repeater_field'] ) {
					return;
				}

				$prev_data = get_post_meta( $document->get_main_id(), '_elementor_page_settings', true );

				if ( ! empty( $data['settings']['cct_repeater_field'] ) ) {
					$prev_data['repeater_field'] = $data['settings']['cct_repeater_field'];
				} else {
					$prev_data['cct_repeater_field'] = $data['settings']['repeater_field'];
				}

				update_post_meta( $document->get_main_id(), '_elementor_page_settings', wp_slash( $prev_data ) );

				break;
		}

	}

	/**
	 * Add document-specific controls
	 */
	public function add_document_controls( $document ) {

		$content_types   = array( '' => __( 'Select content type...', 'jet-engine' ) );
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

		$document->add_control(
			'cct_type',
			array(
				'label'       => esc_html__( 'Content type:', 'jet-engine' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => '',
				'options'     => $content_types,
				'label_block' => true,
				'condition'   => array(
					'listing_source' => $this->manager->source,
				),
			)
		);

		$document->add_control(
			'cct_repeater_field',
			array(
				'label'       => esc_html__( 'Repeater field:', 'jet-engine' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => '',
				'groups'      => $repeater_fields,
				'label_block' => true,
				'condition'   => array(
					'listing_source' => $this->manager->repeater_source,
				)
			)
		);

	}

}
