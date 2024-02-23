<?php

namespace Jet_Engine\Modules\Maps_Listings\Filters\Bricks;

use Jet_Engine\Bricks_Views\Helpers\Options_Converter;
use Jet_Smart_Filters\Bricks_Views\Elements\Jet_Smart_Filters_Bricks_Base;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class User_Geolocation extends Jet_Smart_Filters_Bricks_Base {
	// Element properties
	public $category = 'jetsmartfilters'; // Use predefined element category 'general'
	public $name = 'jet-smart-filters-user-geolocation'; // Make sure to prefix your elements
	public $icon = 'jet-engine-icon-user-geolocation'; // Themify icon font class
	public $scripts = [ 'jetEngineBricks' ]; // Script(s) run when element is rendered on frontend or updated in builder

	public $jet_element_render = 'user-geolocation';

	// Return localised element label
	public function get_label() {
		return esc_html__( 'User Geolocation', 'jet-engine' );
	}

	// Set builder control groups
	public function set_control_groups() {
		$this->register_general_group();
	}

	// Set builder controls
	public function set_controls() {
		$this->register_general_controls();
	}

	public function register_general_controls() {

		$this->start_jet_control_group( 'section_general' );

		$query_builder_link = admin_url( 'admin.php?page=jet-engine-query' );

		$this->register_jet_control(
			'query_notice',
			[
				'content' => sprintf( __( 'This filter is compatible only with queries from <a href="%s" target="_blank">JetEngine Query Builder</a>. ALso you need to set up <a href="https://crocoblock.com/knowledge-base/jetengine/how-to-set-geo-search-based-on-user-geolocation/" target="_blank">Geo Query</a> in your query settings to meke filter to work correctly.', 'jet-engine' ), $query_builder_link ),
				'type' => 'info',
			]
		);

		$this->register_jet_control(
			'filter_id',
			[
				'label'       => esc_html__( 'Select filter', 'jet-smart-filters' ),
				'type'        => 'select',
				'options'     => jet_smart_filters()->data->get_filters_by_type( $this->jet_element_render ),
				'multiple'    => $this->filter_id_multiple,
				'searchable'  => true,
				'placeholder' => esc_html__( 'Select...', 'jet-smart-filters' ),
			]
		);

		if ( jet_smart_filters()->get_version() > '3.0.4' ) {
			$provider_allowed = \Jet_Smart_Filters\Bricks_Views\Manager::get_allowed_providers();
		} else {
			$provider_allowed = [
				'jet-engine'          => true,
				'jet-engine-calendar' => true,
				'jet-engine-maps'     => true,
			];
		}

		$this->register_jet_control(
			'content_provider',
			[
				'tab'        => 'content',
				'label'      => esc_html__( 'This filter for', 'jet-smart-filters' ),
				'type'       => 'select',
				'options'    => Options_Converter::filters_options_by_key( jet_smart_filters()->data->content_providers(), $provider_allowed ),
				'searchable' => true,
			]
		);

		$this->register_jet_control(
			'epro_posts_notice',
			[
				'tab'      => 'content',
				'label'    => esc_html__( 'Please set <b>jet-smart-filters</b> into Query ID option of Posts widget you want to filter', 'jet-smart-filters' ),
				'type'     => 'info',
				'required' => [ 'content_provider', '=', [ 'epro-posts', 'epro-portfolio' ] ],
			]
		);

		$this->register_jet_control(
			'apply_type',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Apply type', 'jet-smart-filters' ),
				'type'    => 'text',
				'default' => 'ajax',
				'hidden'  => true,
			]
		);

		$this->register_jet_control(
			'apply_on',
			[
				'tab'     => 'content',
				'label'   => esc_html__( 'Apply on', 'jet-smart-filters' ),
				'type'    => 'text',
				'default' => 'value',
				'hidden'  => true,
			]
		);

		$this->register_jet_control(
			'query_id',
			[
				'tab'            => 'content',
				'label'          => esc_html__( 'Query ID', 'jet-smart-filters' ),
				'type'           => 'text',
				'hasDynamicData' => false,
				'description'    => esc_html__( 'Set unique query ID if you use multiple widgets of same provider on the page. Same ID you need to set for filtered widget.', 'jet-smart-filters' ),
			]
		);

		// Include Additional Providers Settings
		include jet_smart_filters()->plugin_path( 'includes/bricks/elements/common-controls/additional-providers.php' );

		$this->end_jet_control_group();
	}
}