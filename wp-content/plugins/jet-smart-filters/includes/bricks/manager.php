<?php
/**
 * Bricks views manager
 */
namespace Jet_Smart_Filters\Bricks_Views;

use Bricks\Api;
use Bricks\Database;
use Bricks\Helpers;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Manager class
 */
class Manager {

	/**
	 * Elementor Frontend instance
	 *
	 * @var null
	 */
	public $frontend = null;

	/**
	 * Constructor for the class
	 */
	function __construct() {

		if ( ! $this->has_bricks() ) {
			return;
		}

		add_action( 'init', array( $this, 'register_elements' ), 11 );
		add_action( 'wp_enqueue_scripts', [$this, 'enqueue_styles_for_builder'] );
		add_action( 'jet-smart-filters/render/ajax/before', [ $this, 'register_bricks_dynamic_data_on_ajax' ] );

		add_filter( 'bricks/builder/i18n', function( $i18n ) {
			$i18n['jetsmartfilters'] = esc_html__( 'JetSmartFilters', 'jet-smart-filters' );
			return $i18n;
		} );

		add_filter( 'bricks/posts/query_vars', [ $this, 'merge_query_vars' ], 10, 3 );
		add_filter( 'bricks/terms/query_vars', [ $this, 'merge_query_vars' ], 10, 3 );
		add_filter( 'bricks/users/query_vars', [ $this, 'merge_query_vars' ], 10, 3 );

		require jet_smart_filters()->plugin_path( 'includes/bricks/filters/manager.php' );
		new Filters\Manager();

	}

	public function component_path( $relative_path = '' ) {
		return jet_smart_filters()->plugin_path( 'includes/bricks/' . $relative_path );
	}

	public function register_elements() {

		if ( ! class_exists( '\Jet_Engine\Bricks_Views\Elements\Base' ) ) {
			require $this->component_path( 'compatibility/elements/base.php' );
			require $this->component_path( 'compatibility/helpers/options-converter.php' );
			require $this->component_path( 'compatibility/helpers/controls-converter/base.php' );
			require $this->component_path( 'compatibility/helpers/controls-converter/control-text.php' );
			require $this->component_path( 'compatibility/helpers/controls-converter/control-select.php' );
			require $this->component_path( 'compatibility/helpers/controls-converter/control-repeater.php' );
			require $this->component_path( 'compatibility/helpers/controls-converter/control-checkbox.php' );
			require $this->component_path( 'compatibility/helpers/controls-converter/control-default.php' );
			require $this->component_path( 'compatibility/helpers/controls-converter/control-icon.php' );
			require $this->component_path( 'compatibility/helpers/preview.php' );
			require $this->component_path( 'compatibility/helpers/repeater.php' );
		}

		require $this->component_path( 'elements/base.php' );
		require $this->component_path( 'elements/base-checkbox.php' );

		$element_files = array(
			$this->component_path( 'elements/active-filters.php' ),
			$this->component_path( 'elements/active-tags.php' ),
			$this->component_path( 'elements/alphabet.php' ),
			$this->component_path( 'elements/apply-button.php' ),
			$this->component_path( 'elements/check-range.php' ),
			$this->component_path( 'elements/checkboxes.php' ),
			$this->component_path( 'elements/color-image.php' ),
			$this->component_path( 'elements/date-period.php' ),
			$this->component_path( 'elements/date-range.php' ),
			$this->component_path( 'elements/pagination.php' ),
			$this->component_path( 'elements/radio.php' ),
			$this->component_path( 'elements/range.php' ),
			$this->component_path( 'elements/rating.php' ),
			$this->component_path( 'elements/remove-filters.php' ),
			$this->component_path( 'elements/select.php' ),
			$this->component_path( 'elements/search.php' ),
			$this->component_path( 'elements/sorting.php' ),
		);

		foreach ( $element_files as $file ) {
			\Bricks\Elements::register_element( $file );
		}

	}

	public function enqueue_styles_for_builder() {

		if ( bricks_is_builder() ) {

			jet_smart_filters()->set_filters_used();

			// Add JetSmartFilters icons font
			wp_enqueue_style(
				'jet-smart-filters-icons-font',
				jet_smart_filters()->plugin_url( 'assets/css/lib/jet-smart-filters-icons/jet-smart-filters-icons.css' ),
				array(),
				jet_smart_filters()->get_version()
			);

			jet_smart_filters()->filter_types->filter_styles();
		}
	}

	public function register_bricks_dynamic_data_on_ajax() {

		if ( ! function_exists( 'jet_engine' ) ) {
			// Backup if JetEngine is not installed
			global $wp_filter;
			if ( isset( $wp_filter['wp'][8] ) ) {
				foreach( $wp_filter['wp'][8] as $callback ) {
					if ( is_array( $callback['function'] ) && is_object( $callback['function'][0] ) ) {
						if ( 'Bricks\Integrations\Dynamic_Data\Providers' === get_class( $callback['function'][0] ) ) {
							call_user_func( $callback['function'] );
							break;
						}
					}
				}
			}
		}
	}

	// Combine JetSmartFilters $query_vars with Bricks $query_vars
	// for the correct operation of Load more and Infinite scroll.
	public function merge_query_vars( $query_vars, $settings, $element_id ) {

		$post_id = Database::$page_data['preview_or_post_id'];
		$bricks_data = Helpers::get_bricks_data( $post_id );
		$isLoadMore = false;

		if ( ! empty( $bricks_data ) ) {
			foreach ( $bricks_data as $element ) {
				$interactions = $element['settings']['_interactions'] ?? false;

				if ( ! empty( $interactions ) && isset( $interactions[0]['loadMoreQuery'] ) && $interactions[0]['loadMoreQuery'] === $element_id ) {
					$isLoadMore = true;
				}
			}
		}

		if ( isset( $settings['query']['infinite_scroll'] ) || $isLoadMore ) {

			$jsf_query_args = jet_smart_filters()->query->get_query_args();

			if ( ! empty( $jsf_query_args ) ) {

				$query_vars = wp_parse_args( $jsf_query_args, $query_vars );

			}

		}

		/**
		 * Check if current request is a load more/infinite scroll request
		 *
		 * If so, do not render wrappers.
		 *
		 * @since 1.8.1
		 */
		$is_load_more_request = Api::is_current_endpoint( 'load_query_page' );

		// Additional merge in case Bricks loop has default meta or tax query
		// and Bricks loop is also filtered by meta or tax query.
		if ( $is_load_more_request ) {

			$merge_vars = $settings['query']['_merge_vars'];

			if ( ! empty ( $merge_vars['meta_query'] ) && ! empty ( $query_vars['meta_query'] ) ) {
				$query_vars['meta_query'] = array_merge( $query_vars['meta_query'], $merge_vars['meta_query'] );
			}

			if ( ! empty ( $merge_vars['tax_query'] ) && ! empty ( $query_vars['tax_query'] ) ) {
				$query_vars['tax_query'] = array_merge( $query_vars['tax_query'], $merge_vars['tax_query'] );
			}
		}

		return $query_vars;
	}

	public function has_bricks() {
		return defined( 'BRICKS_VERSION' );
	}

	public static function get_allowed_providers() {

		$provider_allowed = [
			'bricks-query-loop'   => true,
		];

		if ( function_exists( 'jet_engine' ) ) {
			$provider_allowed = array_merge(
				$provider_allowed,
				[
					'jet-engine'          => true,
					'jet-engine-maps'     => jet_engine()->modules->is_module_active('maps-listings'),
					'jet-engine-calendar' => jet_engine()->modules->is_module_active('calendar'),
				]
			);
		}

		return apply_filters( 'jet-smart-filters/bricks/allowed-providers', $provider_allowed );
	}

}