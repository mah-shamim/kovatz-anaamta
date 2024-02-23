<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Listings;

use Jet_Engine\Modules\Rest_API_Listings\Module;

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

		add_action(
			'jet-engine/listing/custom-query-settings',
			array( $this, 'register_query_settings' )
		);

		add_action(
			'jet-engine/elementor-views/dynamic-tags/register',
			array( $this, 'register_dynamic_tags' ), 10, 2
		);

	}

	/**
	 * Register REST API related Dynamic tags
	 *
	 * @param  [type] $dynamic_tags [description]
	 * @param  [type] $tags_module [description]
	 * @return [type]              [description]
	 */
	public function register_dynamic_tags( $dynamic_tags, $tags_module ) {

		require_once Module::instance()->module_path( 'listings/dynamic-tags/field-tag.php' );
		require_once Module::instance()->module_path( 'listings/dynamic-tags/image-tag.php' );

		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Field_Tag() );
		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Image_Tag() );

	}

	/**
	 * Resgister query settings for the listig grid widget
	 */
	public function register_query_settings( $widget ) {

		$widget->start_controls_section(
			'section_jet_rest_query',
			array(
				'label' => __( 'REST API Query', 'jet-engine' ),
			)
		);

		$widget->add_control(
			'jet_rest_query',
			array(
				'label'       => __( 'Query Arguments', 'jet-engine' ),
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => '',
				'description' => __( 'Enter each argument in a separate line. Arguments format - argument_name=argument_value', 'jet-engine' ),
			)
		);

		$widget->end_controls_section();

	}

	public function update_settings_on_document_save( $document, $data ) {

		if ( empty( $data['settings'] ) || empty( $data['settings']['listing_source'] ) ) {
			return;
		}

		if ( $this->manager->source !== $data['settings']['listing_source'] ) {
			return;
		}

		if ( $data['settings']['rest_api_endpoint'] === $data['settings']['listing_post_type'] ) {
			return;
		}

		$prev_data = get_post_meta( $document->get_main_id(), '_elementor_page_settings', true );

		if ( ! empty( $data['settings']['rest_api_endpoint'] ) ) {
			$prev_data['listing_post_type'] = $data['settings']['rest_api_endpoint'];
		} else {
			$prev_data['rest_api_endpoint'] = $data['settings']['listing_post_type'];
		}

		update_post_meta( $document->get_main_id(), '_elementor_page_settings', wp_slash( $prev_data ) );

	}

	/**
	 * Add document-specific controls
	 */
	public function add_document_controls( $document ) {

		$endpoints = array( '' => __( 'Select endpoint...', 'jet-engine' ) );

		foreach ( Module::instance()->settings->get() as $endpoint ) {
			$endpoints[ $endpoint['id'] ] = $endpoint['name'] . ', ' . $endpoint['url'];
		}

		$document->add_control(
			'rest_api_endpoint',
			array(
				'label'       => esc_html__( 'Endpoint:', 'jet-engine' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => '',
				'options'     => $endpoints,
				'label_block' => true,
				'condition'   => array(
					'listing_source' => $this->manager->source,
				),
			)
		);

	}

}
