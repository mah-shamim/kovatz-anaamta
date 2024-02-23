<?php
namespace Jet_Engine\Modules\Maps_Listings;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'maps-listings';

	/**
	 * @var Settings
	 */
	public $settings;

	/**
	 * @var Lat_Lng
	 */
	public $lat_lng;

	/**
	 * @var Sources
	 */
	public $sources;

	/**
	 * @var Providers_Manager
	 */
	public $providers;

	/**
	 * @var Geosearch\Manager
	 */
	public $geosearch;

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_action( 'jet-engine/init', array( $this, 'init' ), 20 );
		add_action( 'jet-engine/rest-api/init-endpoints', array( $this, 'init_rest' ) );
		add_action( 'jet-engine/listings/renderers/registered', array( $this, 'register_render_class' ) );
	}

	/**
	 * Init module components
	 *
	 * @return void
	 */
	public function init() {

		require jet_engine()->modules->modules_path( 'maps-listings/inc/providers-manager.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/settings.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/preview-trait.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/elementor-integration.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/blocks-integration.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/lat-lng.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/sources.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/geosearch/manager.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/map-field.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/listing-link-actions.php' );

		// Bricks Integration
		require jet_engine()->modules->modules_path( 'maps-listings/inc/bricks-views/manager.php' );

		$this->providers = new Providers_Manager();
		$this->settings  = new Settings();
		$this->lat_lng   = new Lat_Lng();
		$this->sources   = new Sources();
		$this->geosearch = new Geosearch\Manager();

		new Elementor_Integration();
		new Blocks_Integration();
		new Bricks_Views\Manager();
		new Map_Field();
		new Listing_Link_Actions();

		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ) );

		// Init compatibility classes
		if ( defined( 'BORLABS_COOKIE_VERSION' ) ) {
			require jet_engine()->modules->modules_path( 'maps-listings/inc/compatibility/borlabs-cookie.php' );

			new Compatibility\Borlabs_Cookie();
		}

		if ( function_exists( 'jet_form_builder' ) ) {
			require jet_engine()->modules->modules_path( 'maps-listings/inc/compatibility/jet-form-builder.php' );
			new Compatibility\Jet_Form_Builder();
		}

		$provider = $this->providers->get_active_map_provider();
		$provider->public_init();

		if ( class_exists( '\Jet_Smart_Filters' ) ) {
			require jet_engine()->modules->modules_path( 'maps-listings/inc/filters/manager.php' );
			new Filters\Manager();
		}

	}

	/**
	 * Initialize REST API endpoints
	 *
	 * @return void
	 */
	public function init_rest( $api_manager ) {

		require jet_engine()->modules->modules_path( 'maps-listings/inc/rest/get-map-marker-info.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/rest/get-map-point-data.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/rest/get-map-location-hash.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/rest/get-map-location-data.php' );
		require jet_engine()->modules->modules_path( 'maps-listings/inc/rest/get-map-autocomplete-data.php' );

		$api_manager->register_endpoint( new Get_Map_Marker_Info() );
		$api_manager->register_endpoint( new Get_Map_Point_Data() );
		$api_manager->register_endpoint( new Get_Map_Location_Hash() );
		$api_manager->register_endpoint( new Get_Map_Location_Data() );
		$api_manager->register_endpoint( new Get_Map_Autocomplete_Data() );

	}

	/**
	 * Register module scripts
	 *
	 * @return void
	 */
	public function register_scripts() {

		$provider = $this->providers->get_active_map_provider();

		if ( $provider ) {
			$provider->register_public_assets();
		}

		wp_register_script(
			'jet-maps-listings',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/js/frontend-maps.js' ),
			array( 'jquery', 'jet-plugins' ),
			jet_engine()->get_version(),
			true
		);

	}

	/**
	 * Register render class.
	 *
	 * @param object $listings
	 */
	public function register_render_class( $listings ) {

		$listings->register_render_class(
			'maps-listing',
			array(
				'class_name' => 'Jet_Engine\Modules\Maps_Listings\Render',
				'path'       => jet_engine()->modules->modules_path( 'maps-listings/inc/render.php' ),
				'deps'       => array( 'listing-grid' ),
			)
		);
	}

	/**
	 * Get action url for open map popup
	 *
	 * @param  null  $specific_post_id
	 * @param  null  $event
	 * @param  array $params Additional arguments
	 * @return string
	 */
	public function get_action_url( $specific_post_id = null, $event = null, $params = array() ) {
		$object = jet_engine()->listings->data->get_current_object();
		$class  = get_class( $object );
		$event  = ! empty( $event ) ? $event : 'click';

		switch ( $class ) {
			case 'WP_Post':
			case 'WP_User':
				$post_id = $object->ID;
				break;

			case 'WP_Term':
				$post_id = $object->term_id;
				break;

			default:
				$post_id = apply_filters( 'jet-engine/listing/custom-post-id', get_the_ID(), $object );
		}

		$post_id = ! empty( $specific_post_id ) ? $specific_post_id : $post_id;

		$args = array(
			'id'    => $post_id,
			'event' => $event,
		);

		if ( ! empty( $params ) ) {
			$args = array_merge( $args, $params );
		}

		return jet_engine()->frontend->get_custom_action_url( 'open_map_listing_popup', $args );
	}

	/**
	 * Get marker types list.
	 *
	 * @return array
	 */
	public function get_marker_types() {
		return apply_filters( 'jet-engine/maps-listing/get-marker-types', array(
			'image'         => __( 'Image', 'jet-engine' ),
			'icon'          => __( 'Icon', 'jet-engine' ),
			'text'          => __( 'Text', 'jet-engine' ),
			'dynamic_image' => __( 'Dynamic Image (from meta field)', 'jet-engine' ),
		) );
	}

	/**
	 * Get marker label types list.
	 *
	 * @return array
	 */
	public function get_marker_label_types() {
		return apply_filters( 'jet-engine/maps-listing/get-marker-label-types', array(
			'post_title'  => __( 'Post Title', 'jet-engine' ),
			'meta_field'  => __( 'Meta Field', 'jet-engine' ),
			'static_text' => __( 'Static Text', 'jet-engine' ),
		) );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return Module
	 */
	public static function instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

}
