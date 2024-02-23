<?php
namespace Jet_Dashboard;

use Jet_Dashboard\Dashboard as Dashboard;
use Jet_Engine\Modules\Custom_Content_Types\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Notice_Manager Class
 */
class WP_Dashboard_Manager {

	/**
	 * @var float
	 */
	public $first_index = 58.15150;

	/**
	 * @var float
	 */
	public $last_index = 58.17999;

	/**
	 * @var float
	 */
	public $first_plugin_index = 58.15101;

	/**
	 * @var float
	 */
	public $first_post_type_index = 58.16001;

	/**
	 * @var float
	 */
	public $first_cct_index = 58.16201;

	/**
	 * @var float
	 */
	public $first_options_pages_index = 58.16401;

	/**
	 * @var array
	 */
	public $plugin_indexes = [];

	/**
	 * @var array
	 */
	public $post_type_indexes = [];

	/**
	 * @var array
	 */
	public $cct_indexes = [];

	/**
	 * @var array
	 */
	public $options_pages_indexes = [];

	/**
	 * @var string[]
	 */
	public $additional_plugins = [];

	/**
	 * @var null
	 */
	protected $allowed_post_types = null;

	/**
	 * @var null
	 */
	protected $allowed_cct = null;

	/**
	 * @var null
	 */
	protected $allowed_options_pages = null;

	/**
	 *
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'modify_admin_menu_order' ], 9999 );

		add_filter( 'jet-engine/tools/available-menu-positions', function( $positions ) {
			return array_merge(
				array( array(
					'value' => -1,
					'label' => 'Crocoblock Section'
				) ),
				$positions
			);
		} );

		add_filter( 'jet-engine/tools/default-menu-position', function() {
			return -1;
		} );

	}

	/**
	 * @return void
	 */
	public function modify_admin_menu_order() {
		global $menu;

		$first_index = $last_index = $this->first_index;
		$first_plugin_index = $last_plugin_index = $this->first_plugin_index;
		$first_post_type_index = $last_post_type_index = $this->first_post_type_index;
		$first_cct_index = $last_cct_index = $this->first_cct_index;
		$first_options_pages_index = $last_options_pages_index = $this->first_options_pages_index;

		$is_jet_plugin_before_separator = false;
		$is_jet_engine_post_type_separator = false;
		$is_jet_engine_cct_separator = false;
		$is_jet_engine_options_pages_separator = false;

		$tmpmenu = $menu;

		foreach ( $tmpmenu as $index => $item_data ) {

			if ( empty( $item_data[2] ) ) {
				continue;
			}

			if ( 'jet-dashboard' === $item_data[2] ) {
				unset( $menu[ $index ] );
				$menu[ '' . $first_index ] = $item_data;
			}

			$is_jet_plugin = $this->is_jet_plugin( $item_data );

			if ( $is_jet_plugin ) {

				if ( ! $is_jet_plugin_before_separator ) {
					$last_plugin_index = $last_plugin_index + 0.00001;
					$last_plugin_index = number_format( $last_plugin_index, 5 );
					$last_index = $last_plugin_index;
				}

				$is_jet_plugin_before_separator = true;

				if ( $index < $first_plugin_index || $index > $last_plugin_index ) {
					unset( $menu[ $index ] );

					$item_data[4] = $item_data[4] . ' jet-plugin';
					$menu[ '' . $last_plugin_index ] = $item_data;
					$this->add_plugin_index_data( $last_plugin_index, $item_data[2] );

					$last_plugin_index = $last_plugin_index + 0.00001;
					$last_plugin_index = number_format( $last_plugin_index, 5 );
					$last_index = $last_plugin_index;
				}
			}

			if ( function_exists( 'jet_engine' ) && method_exists(jet_engine(), 'get_instances' ) ) {

				$is_jet_engine_post_type = $this->is_jet_engine_post_type( $item_data );

				if ( $is_jet_engine_post_type ) {
					$is_jet_engine_post_type_separator = true;

					if ( $index < $first_post_type_index || $index > $last_post_type_index ) {
						unset( $menu[ $index ] );
						$item_data[4] = $item_data[4] . ' jet-engine-post-type';
						$menu[ '' . $last_post_type_index ] = $item_data;
						$this->post_type_indexes[] = $last_post_type_index;
						$last_post_type_index = $last_post_type_index + 0.00001;
						$last_post_type_index = number_format( $last_post_type_index, 5 );
						$last_index = $last_post_type_index;
					}
				}

				$is_jet_engine_cct = $this->is_jet_engine_cct( $item_data );

				if ( $is_jet_engine_cct ) {
					$is_jet_engine_cct_separator = true;

					if ( $index < $first_cct_index || $index > $last_cct_index ) {
						unset( $menu[ $index ] );
						$item_data[4] = $item_data[4] . ' jet-engine-cct';
						$menu[ '' . $last_cct_index ] = $item_data;
						$this->cct_indexes[] = $last_cct_index;

						$last_cct_index = $last_cct_index + 0.00001;
						$last_cct_index = number_format( $last_cct_index, 5 );
						$last_index = $last_cct_index;
					}
				}

				$is_jet_engine_options_page = $this->is_jet_options_page( $item_data );

				if ( $is_jet_engine_options_page ) {
					$is_jet_engine_options_pages_separator = true;

					if ( $index < $last_options_pages_index || $index > $last_options_pages_index ) {
						unset( $menu[ $index ] );
						$item_data[4] = $item_data[4] . ' jet-engine-options-page';
						$menu[ '' . $last_options_pages_index ] = $item_data;
						$this->options_pages_indexes[] = $last_options_pages_index;

						$last_options_pages_index = $last_options_pages_index + 0.00001;
						$last_options_pages_index = number_format( $last_options_pages_index, 5 );
						$last_index = $last_options_pages_index;
					}
				}

			}


		}

		if ( $is_jet_plugin_before_separator ) {
			$menu[ '' . $first_plugin_index ] = array( '', 'read', 'separator-croco-plugins-before', '', 'wp-menu-separator separator-croco separator-croco--plugins-before' );
		}

		if ( $is_jet_engine_post_type_separator ) {
			$menu[ '' . $last_plugin_index ] = array( '', 'read', 'separator-croco-post-type-before', '', 'wp-menu-separator separator-croco separator-croco--post-type-before' );
		}

		if ( $is_jet_engine_cct_separator ) {
			$menu[ '' . $last_post_type_index ] = array( '', 'read', 'separator-croco-cct-before', '', 'wp-menu-separator separator-croco separator-croco--cct-before' );
		}

		if ( $is_jet_engine_options_pages_separator ) {
			$menu[ '' . $last_cct_index ] = array( '', 'read', 'separator-croco-options-pages-before', '', 'wp-menu-separator separator-croco separator-croco--options-pages-before' );
		}

		if ( $is_jet_plugin_before_separator || $is_jet_engine_post_type_separator || $is_jet_engine_cct_separator || $is_jet_engine_options_pages_separator ) {
			$menu[ '' . $this->last_index ] = array( '', 'read', 'separator-croco-after', '', 'wp-menu-separator separator-croco separator-croco--after' );
		}

	}

	/**
	 * @return array
	 */
	public function get_plugin_indexes() {
		return $this->plugin_indexes;
	}

	/**
	 * @param $index
	 * @param $data
	 * @return void
	 */
	public function add_plugin_index_data( $index = false, $slug = '' ) {
		$this->plugin_indexes[ '' . $index ] = $slug;
	}

	/**
	 * @return array
	 */
	public function get_post_type_indexes() {
		return $this->post_type_indexes;
	}

	/**
	 * @return array
	 */
	public function get_cct_indexes() {
		return $this->cct_indexes;
	}

	/**
	 * @return string[]
	 */
	public function get_plugin_slugs () {
		$default_plugin_slugs = [
			'jet-elements',
			'jet-tabs',
			'jet-reviews',
			'jet-menu',
			'jet-blog',
			'jet-blocks',
			'jet-tricks',
			'jet-smart-filters',
			'jet-popup',
			'jet-search',
			'jet-woo-builder',
			'jet-woo-product-gallery',
			'jet-compare-wishlist',
			'jet-engine',
			'jet-abaf-bookings',
			'jet-style-manager',
			'jet-apb-appointments',
			'jet-theme-core',
			'jet-form-builder',
		];

		return array_merge( $this->additional_plugins, $default_plugin_slugs );
	}

	/**
	 * @param $slug
	 * @return void
	 */
	public function add_additional_plugin( $slug = false ) {

		if ( ! $slug ) {
			return;
		}

		$this->additional_plugins[] = $slug;
	}

	/**
	 * @param $page_title
	 * @param $menu_title
	 * @param $capability
	 * @param $menu_slug
	 * @param $callback
	 * @param $icon_url
	 * @return void
	 */
	public function add_plugin_page( $page_title = false, $menu_title = false, $capability = '', $menu_slug = '', $callback = '', $icon_url = '' ) {
		$plugin_indexes = $this->get_plugin_indexes();

		if ( empty( $plugin_indexes ) ) {
			$index = $this->first_plugin_index;
		} else {
			$index = array_key_last( $plugin_indexes );
		}

		$this->add_additional_plugin( $menu_slug );

		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url );
	}

	/**
	 * @param $item_data
	 * @return bool
	 */
	public function is_jet_plugin( $item_data ) {
		$plugin_slugs = $this->get_plugin_slugs();

		foreach ( $plugin_slugs as $plugin_slug ) {

			if ( in_array( str_replace( 'edit.php?post_type=', '', $item_data[2] ), $plugin_slugs ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param $item_data
	 * @return bool
	 */
	public function is_jet_engine_post_type( $item_data ) {

		$post_types_list = $this->get_allowed_post_types();

		if ( empty( $post_types_list ) ) {
			return false;
		}

		$check_post_type = str_replace( 'edit.php?post_type=', '', $item_data[2] );

		if ( $check_post_type && in_array( $check_post_type, $post_types_list ) ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * @return array|mixed|null
	 */
	public function get_allowed_post_types() {

		if ( null !== $this->allowed_post_types ) {
			return $this->allowed_post_types;
		}

		$post_types = jet_engine()->get_instances( 'post-type' );

		return $this->allowed_post_types = array_filter( array_map( function( $item ) {

			if ( isset( $item['menu_position'] ) && -1 == $item['menu_position'] ) {
				return $item['slug'];
			} else {
				return false;
			}
		}, $post_types ) );

	}

	/**
	 * @param $item_d
	 * @return bool
	 */
	public function is_jet_engine_cct( $item_data ) {

		if ( ! str_contains( $item_data[2], 'jet-cct-' ) ) {
			return false;
		}

		$cct_slug    = str_replace( 'jet-cct-', '', $item_data[2] );
		$allowed_cct = $this->get_allowed_cct();

		return in_array( $cct_slug, $allowed_cct );
	}

	/**
	 * @return array|mixed|null
	 */
	public function get_allowed_cct() {

		if ( null !== $this->allowed_cct ) {
			return $this->allowed_cct;
		}

		$cct = jet_engine()->get_instances( 'custom-content-type' );

		return $this->allowed_cct = array_filter( array_map( function( $item ) {
			if ( isset( $item['args']['position'] ) && -1 == $item['args']['position'] ) {
				return $item['args']['slug'];
			} else {
				return false;
			}
		}, $cct ) );

	}

	/**
	 * @param $item_data
	 * @return void
	 */
	public function is_jet_options_page( $item_data ) {

//		if ( ! str_contains( $item_data[2], '-options-page' ) ) {
//			return false;
//		}

		$allowed_options_pages = $this->get_allowed_options_pages();

		return in_array( $item_data[2], $allowed_options_pages );

	}

	/**
	 * @return array|mixed|null
	 */
	public function get_allowed_options_pages() {

		if ( null !== $this->allowed_options_pages ) {
			return $this->allowed_options_pages;
		}

		$options_pages = jet_engine()->get_instances( 'options-page' );

		return $this->allowed_options_pages = array_filter( array_map( function( $item ) {

			if ( isset( $item['position'] ) && -1 == $item['position'] ) {
				return $item['slug'];
			} else {
				return false;
			}
		}, $options_pages ) );

	}

}