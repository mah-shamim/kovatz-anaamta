<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Search_Settings' ) ) {

	/**
	 * Define Jet_Search_Settings class
	 */
	class Jet_Search_Settings {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * [$key description]
		 * @var string
		 */
		public $key = 'jet-search-settings';

		/**
		 * [$builder description]
		 * @var null
		 */
		public $builder  = null;

		/**
		 * [$settings description]
		 * @var null
		 */
		public $settings = null;

		/**
		 * [$settings_page_config description]
		 * @var [type]
		 */
		public $settings_page_config = [];

		/**
		 * Init page
		 */
		public function init() {

		}

		/**
		 * [generate_frontend_config_data description]
		 * @return [type] [description]
		 */
		public function generate_frontend_config_data() {

			$rest_api_url = apply_filters( 'jet-search/rest/frontend/url', get_rest_url() );

			$this->settings_page_config = [
				'messages' => [
					'saveSuccess' => esc_html__( 'Saved', 'jet-elements' ),
					'saveError'   => esc_html__( 'Error', 'jet-elements' ),
				],
				'settingsApiUrl'      => $rest_api_url . 'jet-search-api/v1/plugin-settings',
				'getSuggestionsUrl'   => $rest_api_url . 'jet-search/v1/get-suggestions',
				'addSuggestionUrl'    => $rest_api_url . 'jet-search/v1/add-suggestion',
				'updateSuggestionUrl' => $rest_api_url . 'jet-search/v1/update-suggestion',
				'deleteSuggestionUrl' => $rest_api_url . 'jet-search/v1/delete-suggestion',
				'ajaxUrl'             => esc_url( admin_url( 'admin-ajax.php' ) ),
				'nonce'               => wp_create_nonce( $this->key ),
				'settingsData' => [

				],
			];

			return $this->settings_page_config;
		}

		/**
		 * Return settings page URL
		 *
		 * @param  string $subpage
		 * @return string
		 */
		public function get_settings_page_link( $subpage = 'suggestions' ) {

			return add_query_arg(
				array(
					'page'    => 'jet-dashboard-settings-page',
					'subpage' => 'jet-search-' . $subpage . '-settings',
				),
				esc_url( admin_url( 'admin.php' ) )
			);

		}

		/**
		 * [get description]
		 * @param  [type]  $setting [description]
		 * @param  boolean $default [description]
		 * @return [type]           [description]
		 */
		public function get( $setting, $default = false ) {

			if ( null === $this->settings ) {
				$this->settings = get_option( $this->key, array() );
			}

			return isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : $default;

		}


		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}
}

/**
 * Returns instance of Jet_Search_Settings
 *
 * @return object
 */
function jet_search_settings() {
	return Jet_Search_Settings::get_instance();
}

jet_search_settings()->init();
