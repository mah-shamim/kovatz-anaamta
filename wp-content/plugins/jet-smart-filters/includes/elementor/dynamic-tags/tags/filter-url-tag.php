<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Jet_Smart_Filters_Elementor_Filter_URL_Tag extends Elementor\Core\DynamicTags\Tag {

	public function get_name() {

		return 'jet-smart-filters-url';
	}

	public function get_title() {

		return __( 'URL with filtered value', 'jet-engine' );
	}

	public function get_group() {

		return Jet_Smart_Filters_Elementor_Dynamic_Tags_Module::JET_SMART_FILTERS_GROUP;
	}

	public function get_categories() {

		return array(
			Jet_Smart_Filters_Elementor_Dynamic_Tags_Module::TEXT_CATEGORY,
			Jet_Smart_Filters_Elementor_Dynamic_Tags_Module::URL_CATEGORY,
		);
	}

	public function is_settings_required() {

		return true;
	}

	protected function register_controls() {

		$this->add_control(
			'filter_notice',
			array(
				'type' => Elementor\Controls_Manager::RAW_HTML,
				'raw'  => __( '<b>Please note!</b> <i>Apply type</i> option for the destination filter should be set into <b>Mixed</b> or <b>Reload</b> type ', 'jet-elements' )
			)
		);

		$this->add_control(
			'base_url',
			array(
				'label'       => __( 'Base URL', 'jet-engine' ),
				'label_block' => true,
				'type'        => Elementor\Controls_Manager::TEXT,
				'description' => __( 'URL of the page where filter should be applied', 'jet-engine' ),
			)
		);

		$this->add_control(
			'content_provider',
			array(
				'label'   => __( 'This filter for', 'jet-smart-filters' ),
				'type'    => Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => jet_smart_filters()->data->content_providers(),
			)
		);

		$this->add_control(
			'filter_id',
			array(
				'label'   => __( 'Filter', 'jet-engine' ),
				'type'    => Elementor\Controls_Manager::SELECT,
				'options' => $this->get_filters_list(),
			)
		);

		$this->add_control(
			'filter_value',
			array(
				'label'       => __( 'Value', 'jet-engine' ),
				'label_block' => true,
				'type'        => Elementor\Controls_Manager::TEXT,
				'description' => __( 'You can use JetEngine macros as field value. For example %current_id% - to get ID of the current post or term, %object_id% - ID of any current object, %current_meta|meta_key% - current meta value, etc.', 'jet-engine' ),
			)
		);

		$this->add_control(
			'query_id',
			array(
				'label'       => __( 'Query ID', 'jet-engine' ),
				'label_block' => true,
				'type'        => Elementor\Controls_Manager::TEXT,
				'description' => __( 'If your filter on the destination page uses some custom query ID, you can set it here.', 'jet-engine' ),
			)
		);
	}

	public function render() {

		$base_url         = $this->get_settings( 'base_url' );
		$filter_id        = $this->get_settings( 'filter_id' );
		$filter_value     = $this->get_settings( 'filter_value' );
		$content_provider = $this->get_settings( 'content_provider' );
		$query_id         = $this->get_settings( 'query_id' );

		if ( function_exists( 'jet_engine' ) && jet_engine()->listings ) {
			$filter_value = jet_engine()->listings->macros->do_macros( $filter_value );
		}

		$filter_value = do_shortcode( $filter_value );

		if ( ! $content_provider || ! $filter_id ) {
			return;
		}

		if ( ! $base_url ) {
			$base_url = home_url( '/' );
		}

		$filter = jet_smart_filters()->filter_types->get_filter_instance( $filter_id, null, array(
			'filter_id'        => $filter_id,
			'content_provider' => $content_provider,
			'query_id'         => $query_id,
		) );

		if ( ! $filter ) {
			return;
		}

		$args = array(
			array(
				'query_var'   => $filter->get_arg( 'query_var' ),
				'value'       => $filter_value,
				'filter_type' => $filter->filter_type,
				'query_type'  => $filter->get_arg( 'query_type' ),
				'suffix'      => $filter->get_arg( 'query_var_suffix' ),
			),
		);

		echo jet_smart_filters()->utils->get_filtered_url( $base_url, $query_id, $content_provider, $args );
	}

	/**
	 * Return allowed filters list
	 */
	private function get_filters_list() {

		return jet_smart_filters()->data->get_filters_by_type();
	}
}
