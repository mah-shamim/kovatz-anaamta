<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package;

class Package {

	/**
	 * A reference to an instance of this class.
	 *
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		$this->init_package_components();
	}

	/**
	 * Init package components
	 *
	 * @return void
	 */
	public function init_package_components() {

		require_once $this->package_path( 'listings/manager.php' );
		Listings\Manager::instance();

		require_once $this->package_path( 'query-builder/manager.php' );
		Query_Builder\Manager::instance();

		require_once $this->package_path( 'meta-boxes/manager.php' );
		Meta_Boxes\Manager::instance();

		add_filter( 'jet-engine/modules/dynamic-visibility/conditions/groups', [ $this, 'register_conditions_group' ] );
		add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', [ $this, 'register_conditions' ] );

		add_action( 'jet-engine/elementor-views/dynamic-tags/register', [ $this, 'register_dynamic_tags' ], 10, 2 );
		add_action( 'jet-engine/register-macros', [ $this, 'register_macros' ] );

		add_filter( 'jet-engine/query-builder/types/sql-query/cast-objects', [ $this, 'add_product_to_cast' ] );

	}

	public function add_product_to_cast( $objects ) {
		$objects['wc_get_product'] = __( 'WC Product', 'jet-engine' );
		return $objects;
	}

	public function register_macros() {

		require_once $this->package_path( 'macros/products-in-cart.php' );
		require_once $this->package_path( 'macros/purchased-products.php' );

		new Macros\Products_In_Cart();
		new Macros\Purchased_Products();

	}

	/**
	 * Register condition group.
	 *
	 * Register and returns specific WooCommerce dynamic visibility conditions group.
	 *
	 * @since  3.0.2
	 * @access public
	 *
	 * @param array $groups Predefined groups list.
	 *
	 * @return mixed
	 */
	public function register_conditions_group( $groups ) {

		$groups['woocommerce'] = [
			'label'   => __( 'WooCommerce', 'jet-engine' ),
			'options' => [],
		];

		return $groups;

	}

	/**
	 * Register conditions.
	 *
	 * Register specific WooCommerce dynamic visibility conditions.
	 *
	 * @since  3.0.2
	 * @access public
	 *
	 * @param object $conditions_manager Dynamic visibility condition manager instance.
	 */
	public function register_conditions( $conditions_manager ) {

		require_once $this->package_path( 'conditions/has-enough-stock.php' );
		require_once $this->package_path( 'conditions/is-downloadable.php' );
		require_once $this->package_path( 'conditions/is-featured.php' );
		require_once $this->package_path( 'conditions/is-in-stock.php' );
		require_once $this->package_path( 'conditions/is-on-backorder.php' );
		require_once $this->package_path( 'conditions/is-on-sale.php' );
		require_once $this->package_path( 'conditions/is-purchasable.php' );
		require_once $this->package_path( 'conditions/is-purchased.php' );
		require_once $this->package_path( 'conditions/is-sold-individually.php' );
		require_once $this->package_path( 'conditions/is-type.php' );
		require_once $this->package_path( 'conditions/is-virtual.php' );

		$conditions_manager->register_condition( new Conditions\Has_Enough_Stock() );
		$conditions_manager->register_condition( new Conditions\Is_Downloadable() );
		$conditions_manager->register_condition( new Conditions\Is_Featured() );
		$conditions_manager->register_condition( new Conditions\Is_In_Stock() );
		$conditions_manager->register_condition( new Conditions\Is_On_Backorder() );
		$conditions_manager->register_condition( new Conditions\Is_On_Sale() );
		$conditions_manager->register_condition( new Conditions\Is_Purchasable() );
		$conditions_manager->register_condition( new Conditions\Is_Purchased() );
		$conditions_manager->register_condition( new Conditions\Is_Sold_Individually() );
		$conditions_manager->register_condition( new Conditions\Is_Type() );
		$conditions_manager->register_condition( new Conditions\Is_Virtual() );

	}

	public function register_dynamic_tags( $dynamic_tags, $tags_module ) {

		require_once $this->package_path( 'dynamic-tags/product-field-tag.php' );
		require_once $this->package_path( 'dynamic-tags/product-image-tag.php' );
		require_once $this->package_path( 'dynamic-tags/product-gallery-tag.php' );

		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Product_Field_Tag() );
		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Product_Image_Tag() );
		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Product_Gallery_Tag() );

	}

	/**
	 * Return path inside package.
	 *
	 * @param string $relative_path
	 *
	 * @return string
	 */
	public function package_path( $relative_path = '' ) {
		return jet_engine()->plugin_path( 'includes/compatibility/packages/woocommerce/inc/' . $relative_path );
	}

	/**
	 * Return url inside package.
	 *
	 * @param string $relative_path
	 *
	 * @return string
	 */
	public function package_url( $relative_path = '' ) {
		return jet_engine()->plugin_url( 'includes/compatibility/packages/woocommerce/inc/' . $relative_path );
	}

	/**
	 * Returns the instance.
	 *
	 * @access public
	 * @return object
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

}
