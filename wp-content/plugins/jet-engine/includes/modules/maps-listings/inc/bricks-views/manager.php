<?php
namespace Jet_Engine\Modules\Maps_Listings\Bricks_Views;

use Jet_Engine\Modules\Maps_Listings\Preview_Trait;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	use Preview_Trait;

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
		add_action( 'jet-engine/bricks-views/register-elements', array( $this, 'init' ), 11 );
	}

	public function setup_bricks_query( $listing_id ) {
		jet_engine()->bricks_views->listing->render->set_bricks_query( $listing_id, [] );
	}

	public function module_path( $relative_path = '' ) {
		return jet_engine()->plugin_path( 'includes/modules/maps-listings/inc/bricks-views/' . $relative_path );
	}

	public function init() {

		if ( ! $this->has_bricks() ) {
			return;
		}

		$this->register_elements();

		if ( bricks_is_builder() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'preview_scripts' ) );
		}

		add_action( 'jet-engine/maps-listings/get-map-marker', array( $this, 'setup_bricks_query' ) );

	}

	public function register_elements() {

		\Bricks\Elements::register_element( $this->module_path( 'maps-listings.php' ) );

	}

	public function has_bricks() {
		return jet_engine()->bricks_views->has_bricks();
	}
}