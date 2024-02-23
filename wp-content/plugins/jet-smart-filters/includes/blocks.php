<?php
/**
 * Gutenberg blocks manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Blocks_Manager' ) ) {

	/**
	 * Define Jet_Smart_Filters_Blocks_Manager class
	 */
	class Jet_Smart_Filters_Blocks_Manager {

		/**
		 * Constructor for the class
		 */
		function __construct() {

			if ( ! class_exists( 'Jet_Engine' ) && ! class_exists( 'WooCommerce' ) ) {
				return;
			}

			add_action( 'enqueue_block_editor_assets', array( $this, 'blocks_assets' ) );
			add_filter( 'block_categories_all', array( $this, 'add_filters_category' ) );

			$this->register_block_types();
		}

		/**
		 * Register blocks assets
		 */
		public function blocks_assets() {

			// enqueue assets
			jet_smart_filters()->filter_types->filter_scripts();
			jet_smart_filters()->filter_types->filter_styles();

			wp_enqueue_style(
				'jet-smart-filters-gutenberg-editor-styles',
				jet_smart_filters()->plugin_url( 'admin/assets/css/gutenberg.css' ),
				array('air-datepicker'),
				jet_smart_filters()->get_version()
			);

			wp_enqueue_script(
				'jet-smart-filters-blocks',
				jet_smart_filters()->plugin_url( 'assets/js/blocks.js' ),
				array('wp-blocks','wp-editor', 'wp-components', 'wp-i18n', 'jet-smart-filters', 'air-datepicker'),
				jet_smart_filters()->get_version(),
				true
			);

			$localized_data = apply_filters( 'jet-smart-filters/blocks/localized-data', array(
				'filters'         => $this->get_filter_types_data(),
				'providers'       => $this->get_providers_data(),
				'image_sizes'     => jet_smart_filters()->utils->get_image_sizes(),
				'sorting_orderby' => jet_smart_filters()->filter_types->get_filter_types( 'sorting' )->orderby_options()
			) );

			wp_localize_script( 'jet-smart-filters-blocks', 'JetSmartFilterBlocksData', $localized_data );
		}

		/**
		 * Returns filters of all types options
		 */
		public function get_filter_types_data() {

			$filter_types_data = array();

			foreach ( array_keys( jet_smart_filters()->data->filter_types() ) as $filter_type ) {

				$filter_types_data[$filter_type] = array( '0' => __( 'Select filter...', 'jet-smart-filters' ) ) + jet_smart_filters()->data->get_filters_by_type( $filter_type );

			}

			return $filter_types_data;
		}

		/**
		 * Returns providers options
		 */
		public function get_providers_data() {

			$providers_data = array(
				'not-selected' => __( 'Select provider...', 'jet-smart-filters' )
			);

			if ( class_exists( 'Jet_Engine' ) ) {
				$providers_data['jet-engine'] = __( 'Listing Grid', 'jet-smart-filters' );
			}

			if ( class_exists( 'WooCommerce' ) ) {
				$providers_data['woocommerce-shortcode'] = __( 'WooCommerce Shortcode', 'jet-smart-filters' );
			}

			return apply_filters( 'jet-smart-filters/blocks/allowed-providers', $providers_data );
		}

		/**
		 * Add new category for filters
		 */
		function add_filters_category( $categories ) {

			return array_merge(
				$categories,
				array(
					array(
						'slug'  => 'jet-smart-filters',
						'title' => __( 'Jet Smart Filters', 'jet-smart-filters' ),
						'icon'  => 'filter',
					),
				)
			);
		}

		/**
		 * Register block types
		 */
		public function register_block_types() {

			$types_dir = jet_smart_filters()->plugin_path( 'includes/blocks/' );

			require $types_dir . 'base.php';
			require $types_dir . 'checkboxes.php';
			require $types_dir . 'select.php';
			require $types_dir . 'range.php';
			require $types_dir . 'check-range.php';
			require $types_dir . 'radio.php';
			require $types_dir . 'date-range.php';
			require $types_dir . 'date-period.php';
			require $types_dir . 'rating.php';
			require $types_dir . 'alphabet.php';
			require $types_dir . 'search.php';
			require $types_dir . 'visual.php';
			require $types_dir . 'sorting.php';
			require $types_dir . 'active-filters.php';
			require $types_dir . 'active-tags.php';
			require $types_dir . 'apply-button.php';
			require $types_dir . 'remove-filters.php';
			require $types_dir . 'pagination.php';

			new Jet_Smart_Filters_Block_Checkboxes();
			new Jet_Smart_Filters_Block_Select();
			new Jet_Smart_Filters_Block_Range();
			new Jet_Smart_Filters_Block_Check_Range();
			new Jet_Smart_Filters_Block_Radio();
			new Jet_Smart_Filters_Block_Date_Range();
			new Jet_Smart_Filters_Block_Date_Period();
			new Jet_Smart_Filters_Block_Rating();
			new Jet_Smart_Filters_Block_Alphabet();
			new Jet_Smart_Filters_Block_Search();
			new Jet_Smart_Filters_Block_Visual();
			new Jet_Smart_Filters_Block_Sorting();
			new Jet_Smart_Filters_Block_Active_Filters();
			new Jet_Smart_Filters_Block_Active_Tags();
			new Jet_Smart_Filters_Block_Apply_Button();
			new Jet_Smart_Filters_Block_Remove_Filters();
			new Jet_Smart_Filters_Block_Pagination();
		}
	}
}
