<?php
use Jet_Dashboard\Base\Page_Module as Page_Module_Base;
use Jet_Dashboard\Dashboard as Dashboard;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
abstract class Jet_Smart_Filters_Admin_Setting_Page_Base extends Page_Module_Base {

	public function get_parent_slug() {

		return 'settings-page';
	}

	public function get_category() {

		return 'jet-smart-filters-settings';
	}

	public function get_page_link() {

		return Dashboard::get_instance()->get_dashboard_page_url( $this->get_parent_slug(), $this->get_page_slug() );
	}

	public function enqueue_module_assets() {

		wp_enqueue_style(
			'jet-smart-filters-admin-css',
			jet_smart_filters()->plugin_url( 'admin/assets/css/settings-page.css' ),
			false,
			jet_smart_filters()->get_version()
		);

		wp_enqueue_script(
			'jet-smart-filters-admin-vue-components',
			jet_smart_filters()->plugin_url( 'admin/assets/js/jsf-admin-setting-pages.js' ),
			array( 'cx-vue-ui' ),
			jet_smart_filters()->get_version(),
			true
		);

		wp_localize_script(
			'jet-smart-filters-admin-vue-components',
			'jetSmartFiltersSettingsConfig',
			apply_filters( 'jet-smart-filters/admin/settings-page/localized-config', $this->get_page_config() )
		);
	}

	public function page_config( $config = array(), $page = false, $subpage = false ) {

		$config['pageModule'] = $this->get_parent_slug();
		$config['subPageModule'] = $this->get_page_slug();

		return $config;
	}

	/**
	 * Returns avaliable providers lists
	 */
	private $_avaliable_providers = array();
	public function get_avaliable_providers() {

		if ( $this->_avaliable_providers ) {
			return $this->_avaliable_providers;
		}

		foreach ( glob( jet_smart_filters()->plugin_path( 'includes/providers/' ) . '*.php' ) as $file ) {
			$data = get_file_data( $file, array( 'class'=>'Class', 'name' => 'Name', 'slug'=>'Slug' ) );

			if ( $data['name'] ) {
				$this->_avaliable_providers[ $data['class'] ] = $data['name'];
			}
		}

		return $this->_avaliable_providers;
	}

	/**
	 * Returns page config
	 */
	public function get_page_config() {

		foreach ( $this->get_avaliable_providers() as $key => $value ) {
			$default_avaliable_providers[ $key ] = 'true';
		}

		foreach ( $this->get_post_types_for_options() as $key => $value ) {
			$default_avaliable_post_types[ $key ] = 'false';
		}

		foreach ( $this->get_rewritable_post_types_options() as $key => $value ) {
			$default_rewritable_post_types[ $key ] = 'false';
		}

		$default_url_aliases = array( array(
			'needle'      => '/jsf/jet-engine/',
			'replacement' => '/filter/'
		) );

		$rest_api_url = apply_filters( 'jet-smart-filters/rest/frontend/url', get_rest_url() );

		return array(
			'settingsApiUrl' => $rest_api_url . 'jet-smart-filters-api/v1/plugin-settings',
			'ajaxurl'        => admin_url( 'admin-ajax.php' ),
			'nonce'          => wp_create_nonce( 'wp_rest' ),
			'settings'       => array(
				'avaliable_providers'               => jet_smart_filters()->settings->get( 'avaliable_providers', $default_avaliable_providers ),
				'use_indexed_filters'               => jet_smart_filters()->settings->get( 'use_indexed_filters' ),
				'avaliable_post_types'              => jet_smart_filters()->settings->get( 'avaliable_post_types', $default_avaliable_post_types ),
				'use_auto_indexing'                 => jet_smart_filters()->settings->get( 'use_auto_indexing' ),
				'url_structure_type'                => jet_smart_filters()->settings->get( 'url_structure_type', 'plain' ),
				'rewritable_post_types'             => jet_smart_filters()->settings->get( 'rewritable_post_types', $default_rewritable_post_types ),
				'use_url_aliases'                   => jet_smart_filters()->settings->get( 'use_url_aliases', 'false' ),
				'url_aliases'                       => jet_smart_filters()->settings->get( 'url_aliases', $default_url_aliases ),
				'use_url_aliases_example'           => jet_smart_filters()->settings->get( 'use_url_aliases_example', 'false' ),
				'url_aliases_example'               => htmlspecialchars_decode( jet_smart_filters()->settings->get( 'url_aliases_example', '/page/jsf/jet-engine/tax/category:1;post_tag:2/meta/meta-key:data-1/' ) ),
				'ajax_request_types'                => jet_smart_filters()->settings->get( 'ajax_request_types', 'default' ),
				'use_tabindex'                      => jet_smart_filters()->settings->get( 'use_tabindex', false ),
				'tabindex_color'                    => jet_smart_filters()->settings->get( 'tabindex_color', '#0085f2' ),
				'use_provider_preloader'            => jet_smart_filters()->provider_preloader->is_enabled,
				'provider_preloader_fixed_position' => jet_smart_filters()->provider_preloader->fixed_position,
				'provider_preloader_fixed_edge_gap' => jet_smart_filters()->provider_preloader->fixed_edge_gap,
				'provider_preloader_type'           => jet_smart_filters()->provider_preloader->type,
				'provider_preloader_styles'         => jet_smart_filters()->provider_preloader->styles,
				'provider_preloader_css'            => jet_smart_filters()->provider_preloader->css,
			),
			'data'           => array(
				'avaliable_providers_options'  => $this->get_avaliable_providers(),
				'avaliable_post_types_options' => $this->get_post_types_for_options(),
				'ajax_request_types_options'   => array(
					array(
						'value' => 'default',
						'label' => 'Default (ajax admin-ajax.php request)',
					),
					array(
						'value' => 'referrer',
						'label' => 'Referrer (ajax admin-ajax.php request + referrer)',
					),
					array(
						'value' => 'self',
						'label' => 'Self (request for the current page)',
					)
				),
				'url_structure_type_options' => array(
					array(
						'value' => 'plain',
						'label' => 'Plain',
					),
					array(
						'value' => 'permalink',
						'label' => 'Permalink',
					)
				),
				'rewritable_post_types_options'   => $this->get_rewritable_post_types_options(),
				'url_aliases_example_default'     => '/page/jsf/jet-engine/tax/category:1;post_tag:2/meta/meta-key:data-1/',
				'provider_preloader_type_options' => jet_smart_filters()->provider_preloader->type_options,
			)
		);
	}

	/**
	 * Returns rewritable taxonomies list for options
	 */
	public function get_rewritable_post_types_options() {

		$rewritable_post_types_exceptions = apply_filters( 'jet-smart-filters/settings/rewritable-post-types-exceptions', array(
			'jet-popup',
			'jet-menu'
		) );

		$rewritable_post_types = array(
			'post' => get_post_type_object('post')->label
		);

		foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $post_type ) {
			if ( in_array( $post_type->name, $rewritable_post_types_exceptions ) || empty( $post_type->rewrite ) ) {
				continue;
			}
			
			$rewritable_post_types[$post_type->name] = $post_type->label;
		}

		return $rewritable_post_types;
	}

	/**
	 * Returns post types list for options
	 */
	public function get_post_types_for_options() {

		$indexed_post_types_exceptions = apply_filters( 'jet-smart-filters/indexed-post-types-exceptions', array( 
			'attachment',
			'elementor_library',
			'e-landing-page',
			'jet-woo-builder',
			'jet-engine',
			'jet-engine-booking'
		) );

		$args = array(
			'public' => true,
		);

		$post_types = get_post_types( $args, 'objects', 'and' );
		$post_types = wp_list_pluck( $post_types, 'label', 'name' );

		if ( isset( $post_types[ jet_smart_filters()->post_type->slug() ] ) ) {
			unset( $post_types[jet_smart_filters()->post_type->slug()] );
		}

		foreach ( $post_types as $key => $value ) {
			if ( in_array( $key, $indexed_post_types_exceptions ) ) {
				unset( $post_types[$key] );
			}
		}

		$post_types['users'] = __( 'Users', 'jet-smart-filters' );

		return $post_types;
	}
}
