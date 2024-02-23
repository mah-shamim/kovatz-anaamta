<?php
/**
 * Filters manager class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Filter_Manager' ) ) {
	/**
	 * Define Jet_Smart_Filters_Filter_Manager class
	 */
	class Jet_Smart_Filters_Filter_Manager {

		private $_filter_types = array();

		/**
		 * Constructor for the class
		 */
		public function __construct() {
			$this->register_filter_types();

			// assets for elementor and default editor
			add_action( 'wp_footer', array( $this, 'filter_scripts' ), 15 );
			add_action( 'wp_footer', array( $this, 'filter_styles' ), 15 );

			add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'filter_editor_styles' ) );
			add_action( 'elementor/preview/enqueue_styles', array( $this, 'filter_editor_styles' ) );
		}

		/**
		 * Enqueue filter scripts
		 */
		public function filter_scripts() {

			if ( jet_smart_filters()->filters_not_used ) {
				return;
			}

			$dependencies = array( 'jquery', 'jet-plugins' );

			foreach ( $this->get_filter_types() as $filter ) {
				if ( ! method_exists( $filter, 'get_scripts' ) ) {
					continue;
				}

				$assets = $filter->get_scripts();

				if ( $assets ) {
					$dependencies = array_merge( $dependencies, $assets );
				}
			}

			wp_enqueue_script(
				'jet-smart-filters',
				jet_smart_filters()->plugin_url( 'assets/js/public.js' ),
				$dependencies,
				jet_smart_filters()->get_version(),
				true
			);

			$localized_data = apply_filters( 'jet-smart-filters/filters/localized-data', array(
				'ajaxurl'         => admin_url( 'admin-ajax.php' ),
				'siteurl'         => get_site_url(),
				'sitepath'        => jet_smart_filters()->data->get_sitepath(),
				'baseurl'         => jet_smart_filters()->data->get_baseurl(),
				'selectors'       => jet_smart_filters()->data->get_provider_selectors(),
				'queries'         => jet_smart_filters()->query->get_default_queries(),
				'settings'        => jet_smart_filters()->providers->get_provider_settings(),
				'misc'            => array(
					'week_start'       => get_option( 'start_of_week' ),
					'url_type'         => jet_smart_filters()->settings->get( 'url_structure_type' ),
					'valid_url_params' => $this->get_valid_url_params(),
				),
				'props'           => jet_smart_filters()->query->get_query_props(),
				'extra_props'     => array(),
				'templates'       => $this->get_localization_templates(),
				'plugin_settings' => array(
					'use_tabindex'       => jet_smart_filters()->settings->get( 'use_tabindex', false ),
					'use_url_aliases'    => jet_smart_filters()->settings->get( 'use_url_aliases', false ),
					'url_aliases'        => jet_smart_filters()->settings->get( 'url_aliases', array() ),
					'provider_preloader' => array(
						'template' => jet_smart_filters()->provider_preloader->is_enabled
							? jet_smart_filters()->provider_preloader->get_template()
							: '',
						'fixed_position' => jet_smart_filters()->provider_preloader->fixed_position,
						'fixed_edge_gap' => jet_smart_filters()->provider_preloader->fixed_edge_gap
					)
				)
			) );

			wp_localize_script( 'jet-smart-filters', 'JetSmartFilterSettings', $localized_data );
		}

		public function get_localization_templates() {

			$templates = [];

			$templates['active_tag'] = array(
				'label'  => jet_smart_filters()->utils->get_template_html( 'for-js/active-tag/label.php' ),
				'value'  => jet_smart_filters()->utils->get_template_html( 'for-js/active-tag/value.php' ),
				'remove' => jet_smart_filters()->utils->get_template_html( 'for-js/active-tag/remove.php' )
			);
			$templates['active_filter'] = array(
				'label'  => jet_smart_filters()->utils->get_template_html( 'for-js/active-filter/label.php' ),
				'value'  => jet_smart_filters()->utils->get_template_html( 'for-js/active-filter/value.php' ),
				'remove' => jet_smart_filters()->utils->get_template_html( 'for-js/active-filter/remove.php' )
			);
			$templates['pagination'] = array(
				'item'      => jet_smart_filters()->utils->get_template_html( 'for-js/pagination/item.php' ),
				'dots'      => jet_smart_filters()->utils->get_template_html( 'for-js/pagination/dots.php' ),
				'load_more' => jet_smart_filters()->utils->get_template_html( 'for-js/pagination/load-more.php' )
			);

			return $templates;
		}

		public function get_valid_url_params() {

			return apply_filters( 'jet-smart-filters/filters/valid-url-params', array(
				'jsf',
				'tax',
				'meta',
				'date',
				'sort',
				'alphabet',
				'_s',
				'pagenum',
				// backward compatibility
				'jet-smart-filters',
				'jet_paged',
				'search',
				'_tax_query_',
				'_meta_query_',
				'_date_query_',
				'_sort_',
				'__s_',
			) );
		}

		/**
		 * Enqueue filter styles
		 */
		public function filter_styles() {

			if ( jet_smart_filters()->filters_not_used ) {
				return;
			}

			wp_register_style(
				'font-awesome',
				jet_smart_filters()->plugin_url( 'assets/lib/font-awesome/font-awesome.min.css' ),
				array(),
				'4.7.0'
			);

			wp_enqueue_style(
				'jet-smart-filters',
				jet_smart_filters()->plugin_url( 'assets/css/public.css' ),
				array('font-awesome'),
				jet_smart_filters()->get_version()
			);

			// Filter inline styles
			$tabindex_color = jet_smart_filters()->settings->get( 'tabindex_color', '#0085f2' );

			$filter_inline_styles = "
				.jet-filter {
					--tabindex-color: $tabindex_color;
					--tabindex-shadow-color: " . jet_smart_filters()->utils->hex2rgba( $tabindex_color, 0.4 ) . ";
				}
			";

			if ( jet_smart_filters()->provider_preloader->is_enabled ) {
				$filter_inline_styles .= jet_smart_filters()->provider_preloader->css;
			}

			wp_add_inline_style( 'jet-smart-filters', $filter_inline_styles );
		}

		/**
		 * Enqueue editor filter styles
		 */
		public function filter_editor_styles() {

			wp_enqueue_style(
				'jet-smart-filters-icons-font',
				jet_smart_filters()->plugin_url( 'assets/css/lib/jet-smart-filters-icons/jet-smart-filters-icons.css' ),
				array(),
				jet_smart_filters()->get_version()
			);
		}

		/**
		 * Register all providers.
		 *
		 * @return void
		 */
		public function register_filter_types() {

			$base_path = jet_smart_filters()->plugin_path( 'includes/filters/' );

			$default_filter_types = array(
				'Jet_Smart_Filters_Checkboxes_Filter'  => $base_path . 'checkboxes.php',
				'Jet_Smart_Filters_Select_Filter'      => $base_path . 'select.php',
				'Jet_Smart_Filters_Range_Filter'       => $base_path . 'range.php',
				'Jet_Smart_Filters_Check_Range_Filter' => $base_path . 'check-range.php',
				'Jet_Smart_Filters_Date_Range_Filter'  => $base_path . 'date-range.php',
				'Jet_Smart_Filters_Date_Period_Filter' => $base_path . 'date-period.php',
				'Jet_Smart_Filters_Radio_Filter'       => $base_path . 'radio.php',
				'Jet_Smart_Filters_Rating_Filter'      => $base_path . 'rating.php',
				'Jet_Smart_Filters_Alphabet_Filter'    => $base_path . 'alphabet.php',
				'Jet_Smart_Filters_Search_Filter'      => $base_path . 'search.php',
				'Jet_Smart_Filters_Color_Image_Filter' => $base_path . 'color-image.php',
				'Jet_Smart_Filters_Sorting_Filter'     => $base_path . 'sorting.php',
				'Jet_Smart_Filters_Active_Filters'     => $base_path . 'active-filters.php',
				'Jet_Smart_Filters_Pagination_Filter'  => $base_path . 'pagination.php',
			);

			require $base_path . 'base.php';

			foreach ( $default_filter_types as $filter_class => $filter_file ) {
				$this->register_filter_type( $filter_class, $filter_file );
			}

			/**
			 * Register custom filter types on this hook
			 */
			do_action( 'jet-smart-filters/filter-types/register', $this );
		}

		/**
		 * Register new filter.
		 */
		public function register_filter_type( $filter_class, $filter_file ) {

			if ( ! file_exists( $filter_file ) ) {
				return;
			}

			require $filter_file;

			if ( class_exists( $filter_class ) ) {
				$instance                                   = new $filter_class();
				$this->_filter_types[ $instance->get_id() ] = $instance;
			}
		}

		/**
		 * Return all filter types list or specific filter by ID
		 */
		public function get_filter_types( $filter = null ) {

			if ( $filter ) {
				return isset( $this->_filter_types[ $filter ] ) ? $this->_filter_types[ $filter ] : false;
			}

			return $this->_filter_types;
		}

		/**
		 * Return suffix for query modify
		 */
		public function get_filter_query_var_suffix( $filter ) {

			$query_var_suffix   = array();
			$type               = get_post_meta( $filter, '_filter_type', true );
			$query_var          = get_post_meta( $filter, '_query_var', true );
			$data_source        = get_post_meta( $filter, '_data_source', true );
			$is_hierarchical    = false;
			$is_custom_checkbox = false;

			if ( 'select' === $type ) {
				$is_hierarchical = filter_var( get_post_meta( $filter, '_is_hierarchical', true ), FILTER_VALIDATE_BOOLEAN );
			}

			if ( in_array( $type, ['checkboxes', 'select', 'radio', 'color-image'] ) ) {
				$is_custom_checkbox = filter_var( get_post_meta( $filter, '_is_custom_checkbox', true ), FILTER_VALIDATE_BOOLEAN );
			}

			if ( 'checkboxes' === $type && 'cct' === $data_source ) {
				$is_custom_checkbox = true;
			}

			if ( in_array( $type, ['search', 'range', 'check-range', 'rating'] ) ) {
				$query_var_suffix[] = $type;
			}

			if ( in_array( $type, ['date-range', 'date-period'] ) ) {
				$query_var_suffix[] = 'date';
			}

			if ( $is_custom_checkbox ) {
				$query_var_suffix[] = 'is_custom_checkbox';
			}

			if ( $query_var && ! $is_hierarchical && ! $is_custom_checkbox ) {
				if ( in_array( $type, ['select', 'radio'] ) && 'taxonomies' !== $data_source ) {
					$query_compare = get_post_meta( $filter, '_query_compare', true );

					if ( 'equal' !== $query_compare ) {
						$query_var_suffix[] = 'compare-' . $query_compare;
					}
				}
			}

			if ( 'rating' === $type ) {
				$query_compare = get_post_meta( $filter, '_rating_compare_operand', true );

				if ( 'equal' !== $query_compare ) {
					$query_var_suffix[] = 'compare-' . $query_compare;
				}
			}

			return $query_var_suffix ? implode( ',', $query_var_suffix ) : false;
		}

		/**
		 * Returns filter instance by filter post ID
		 */
		public function get_filter_instance( $filter_id, $type = null, $args = array() ) {

			if ( null === $type ) {
				$type = get_post_meta( $filter_id, '_filter_type', true );
			}

			if ( ! $type ) {
				return false;
			}

			if ( ! class_exists( 'Jet_Smart_Filters_Filter_Instance' ) ) {
				require_once jet_smart_filters()->plugin_path( 'includes/filters/instance.php' );
			}

			return new Jet_Smart_Filters_Filter_Instance( $filter_id, $type, $args );
		}

		/**
		 * Render fiter type template
		 */
		public function render_filter_template( $filter_type, $args = array() ) {

			$filter = $this->get_filter_instance( $args['filter_id'], $filter_type, $args );
			$filter->render();
		}
	}
}
