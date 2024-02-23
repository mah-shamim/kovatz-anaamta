<?php
/**
 * Jet Smart Filters Admin Data class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Admin_Data' ) ) {

	/**
	 * Define Jet_Smart_Filters_Admin_Data class
	 */
	#[AllowDynamicProperties]
	class Jet_Smart_Filters_Admin_Data {
		/**
		 * Constructor for the class
		 */
		public function __construct() {

			// Taxonomy options list
			$this->taxonomy_options = apply_filters(
				'jet-smart-filters/admin/taxonomy-options',
				jet_smart_filters()->data->get_taxonomies_for_options()
			);

			// Post types options list
			$this->post_types_options = apply_filters(
				'jet-smart-filters/admin/post-types-options',
				jet_smart_filters()->data->get_post_types_for_options()
			);

			// Info blocks
			$this->date_formats_info        = jet_smart_filters()->utils->get_file_html( 'admin/templates/info-blocks/date-formats.php' );
			$this->min_max_date_period_info = jet_smart_filters()->utils->get_file_html( 'admin/templates/info-blocks/min-max-date-period-info.php' );

			// Settings data
			$this->settings_data = apply_filters(
				'jet-smart-filters/admin/settings-data',
				jet_smart_filters()->is_classic_admin
					? include jet_smart_filters()->plugin_path( 'admin/admin-classic/includes/filter-settings-list.php' )
					: include jet_smart_filters()->plugin_path( 'admin/includes/filter-settings-list.php' )
			);
		}

		/**
		 * Returns a list of plugin global settings
		 */
		public function plugin_settings() {

			return apply_filters( 'jet-smart-filters/admin/plugin-settings-data', array(
				'indexer_enabled' => filter_var( jet_smart_filters()->settings->get( 'use_indexed_filters' ), FILTER_VALIDATE_BOOLEAN )
			) );
		}

		/**
		 * Returns a list of options for a single filter
		 */
		public function settings() {

			// Filter settings
			return apply_filters( 'jet-smart-filters/admin/filter-settings-data', array(
				'title_block' => array(
					'settings' => array(
						'title' => array(
							'type'  => 'text',
							'title' => __( 'Filter Name', 'jet-smart-filters' ),
						),
					)
				),
				'settings_block' => array(
					'label'    => __( 'Filter Settings', 'jet-smart-filters' ),
					'settings' => apply_filters(
						// "jet-smart-filters/post-type/" hook prefix for backwards compatibility
						'jet-smart-filters/post-type/meta-fields-settings',
						$this->settings_data['settings']
					)
				),
				'labels_block' => array(
					'label'    => __( 'Filter Labels', 'jet-smart-filters' ),
					'settings' => apply_filters(
						// "jet-smart-filters/post-type/" hook prefix for backwards compatibility
						'jet-smart-filters/post-type/meta-fields-labels',
						$this->settings_data['labels']
					)
				)
			));
		}

		/**
		 * Returns a list of filter types
		 */
		public function types() {

			$filter_types = array();

			foreach ( jet_smart_filters()->filter_types->get_filter_types() as $filter_id => $filter ) {
				if ( ! method_exists( $filter, 'get_name' ) ) {
					continue;
				}

				$filter_types[$filter_id] = array(
					'label' => $filter->get_name(),
					'img'   => method_exists( $filter, 'get_icon_url' )
								? $filter->get_icon_url()
								: jet_smart_filters()->plugin_url( 'admin/assets/img/filter-types/default.png' )
				);

				if ( method_exists( $filter, 'get_info' ) ) {
					$filter_types[$filter_id]['info'] = $filter->get_info();
				}
			}

			return apply_filters( 'jet-smart-filters/admin/filter-types', $filter_types );
		}

		/**
		 * Returns a list of filter sources
		 */
		public function sources() {

			// "jet-smart-filters/post-type/" hook prefix for backwards compatibility
			return apply_filters( 'jet-smart-filters/post-type/options-data-sources', array(
				'taxonomies'    => __( 'Taxonomies', 'jet-smart-filters' ),
				'manual_input'  => __( 'Manual Input', 'jet-smart-filters' ),
				'posts'         => __( 'Posts', 'jet-smart-filters' ),
				'custom_fields' => __( 'Custom Fields', 'jet-smart-filters' )
			) );
		}

		/**
		 * Returns a list of sort by
		 */
		public function sort_by_list() {

			return apply_filters( 'jet-smart-filters/admin/sort-by-list', array(
				'title_asc'  => __( 'Title: ascending', 'jet-smart-filters' ),
				'title_desc' => __( 'Title: descending', 'jet-smart-filters' ),
				'date_asc'   => __( 'Date: oldest to newest', 'jet-smart-filters' ),
				'date_desc'  => __( 'Date: newest to oldest (by default)', 'jet-smart-filters' ),
			) );
		}

		/**
		 * Returns help block data
		 */
		public function help_block_data() {

			return apply_filters(
				'jet-smart-filters/admin/help-block-data',
				include jet_smart_filters()->plugin_path( 'admin/includes/help-block-data.php' )
			);
		}

		/**
		 * Returns a names list of registered settings
		 */
		public function registered_settings_names() {

			$settings_names = array();

			foreach ( $this->settings() as $settings_group ) {
				foreach ( $settings_group['settings'] as $setting_key => $setting_value ) {
					array_push($settings_names, $setting_key);
				}
			}

			return $settings_names;
		}

		/**
		 * Returns a settings values list of registered settings
		 */
		public function default_settings_values() {

			$settings_values = array();

			
			foreach ( $this->settings() as $group_key => $group_data ) {
				if ( $group_key === 'title_block' ) {
					continue;
				}

				foreach ( $group_data['settings'] as $option_key => $option_data ) {
					$settings_values[$option_key] = isset( $option_data['value'] ) ? $option_data['value'] : '';
				}
			}

			return $settings_values;
		}

		/**
		 * Santize range step before save
		 */
		public function sanitize_range_step( $input ) {

			return trim( str_replace( ',', '.', $input ) );
		}
	}
}
