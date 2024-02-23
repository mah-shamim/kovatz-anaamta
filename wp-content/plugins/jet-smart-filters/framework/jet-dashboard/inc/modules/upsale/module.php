<?php
namespace Jet_Dashboard\Modules\Upsale;

use Jet_Dashboard\Base\Page_Module as Page_Module_Base;
use Jet_Dashboard\Dashboard as Dashboard;
use Jet_Dashboard\Utils as Utils;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Module extends Page_Module_Base {

	/**
	 * [$primary_license_data description]
	 * @var array
	 */
	public $primary_license_data = null;

	/**
	 * [$croco_upgrade_link description]
	 * @var string
	 */
	public $croco_upgrade_link = 'https://account.crocoblock.com/upgrade/';

	/**
	 * Returns module slug
	 *
	 * @return void
	 */
	public function get_page_slug() {
		return 'upsale-page';
	}

	/**
	 * [get_subpage_slug description]
	 * @return [type] [description]
	 */
	public function get_parent_slug() {
		return false;
	}

	/**
	 * [get_page_name description]
	 * @return [type] [description]
	 */
	public function get_page_name() {
		return esc_html__( 'Get Crocoblock', 'jet-dashboard' );
	}

	/**
	 * [get_category description]
	 * @return [type] [description]
	 */
	public function get_category() {
		return false;
	}

	/**
	 * [get_page_link description]
	 * @return [type] [description]
	 */
	public function get_page_link() {
		return Dashboard::get_instance()->get_dashboard_page_url( $this->get_page_slug(), $this->get_parent_slug() );
	}

	/**
	 * [init description]
	 * @return [type] [description]
	 */
	public function create() {
		add_action( 'admin_menu', array( $this, 'register_page' ), 9999 );
	}

	/**
	 * [register_page description]
	 * @return [type] [description]
	 */
	public function register_page() {

		$primary_license_data = $this->get_primary_license_data();
		$theme_info = Dashboard::get_instance()->data_manager->get_theme_info();
		$license_type = ! empty( $primary_license_data['type'] ) ? $primary_license_data['type'] . '-license' : 'license-not-activated';
		$utm_medium = $license_type . '/' . $theme_info['authorSlug'];

		if ( 'crocoblock' === $primary_license_data['type'] ) {

			if ( 'lifetime' === $primary_license_data['product'] ) {
				return false;
			}

			if ( 'all-inclusive' === $primary_license_data['product'] ) {

				add_submenu_page(
					Dashboard::get_instance()->dashboard_slug,
					esc_html__( 'Go Lifetime', 'jet-dashboard' ),
					esc_html__( 'Go Lifetime', 'jet-dashboard' ),
					'manage_options',
					add_query_arg(
						array(
							'utm_source'   => 'dashboard',
							'utm_medium'   => $utm_medium,
							'utm_campaign' => 'upgrade-to-lifetime',
						),
						$this->croco_upgrade_link . $primary_license_data['key']
					)
				);

				return false;
			}

			add_submenu_page(
				Dashboard::get_instance()->dashboard_slug,
				esc_html__( 'Go All-Inclusive', 'jet-dashboard' ),
				esc_html__( 'Go All-Inclusive', 'jet-dashboard' ),
				'manage_options',
				add_query_arg(
					array(
						'utm_source'   => 'dashboard',
						'utm_medium'   => $utm_medium,
						'utm_campaign' => 'upgrade-to-all-inclusive',
					),
					$this->croco_upgrade_link . $primary_license_data['key']
				)
			);

			return false;
		}

		add_submenu_page(
			Dashboard::get_instance()->dashboard_slug,
			$this->get_upsale_page_label(),
			$this->get_upsale_page_label(),
			'manage_options',
			Dashboard::get_instance()->dashboard_slug . '-upsale-page',
			function() {
				include Dashboard::get_instance()->get_view( 'common/dashboard' );
			}
		);

		return false;
	}

	/**
	 * [get_primary_license_data description]
	 * @return [type] [description]
	 */
	public function get_primary_license_data() {

		if ( null !== $this->primary_license_data ) {
			return $this->primary_license_data;
		}

		$this->primary_license_data = Dashboard::get_instance()->license_manager->get_primary_license_data();

		return $this->primary_license_data;
	}

	/**
	 * [get_upsale_page_label description]
	 * @return [type] [description]
	 */
	public function get_upsale_page_label() {
		$primary_license_data = $this->get_primary_license_data();

		if ( 'crocoblock' === $primary_license_data['type'] ) {

			if ( 'all-inclusive' === $primary_license_data['product'] ) {
				return esc_html__( 'Go Lifetime', 'jet-dashboard' );
			}

			return esc_html__( 'Go All-Inclusive', 'jet-dashboard' );
		}

		return esc_html__( 'Get Crocoblock', 'jet-dashboard' );
	}

	/**
	 * Enqueue module-specific assets
	 *
	 * @return void
	 */
	public function enqueue_module_assets() {

		wp_enqueue_script(
			'jet-dashboard-upsale-page',
			Dashboard::get_instance()->get_dashboard_url() . 'assets/js/upsale-page.js',
			array( 'cx-vue-ui' ),
			Dashboard::get_instance()->get_dashboard_version(),
			true
		);
	}

	/**
	 * License page config
	 *
	 * @param  array  $config  [description]
	 * @param  string $subpage [description]
	 * @return [type]          [description]
	 */
	public function page_config( $config = array(), $page = false, $subpage = false ) {

		$config['pageModule']        = $this->get_page_slug();
		$config['offersConfig']      = Dashboard::get_instance()->data_manager->get_dashboard_config( 'offers' );
		$config['extrasConfig']      = Dashboard::get_instance()->data_manager->get_dashboard_config( 'extras' );
		$config['generalConfig']     = Dashboard::get_instance()->data_manager->get_dashboard_config( 'general' );

		return $config;
	}

	/**
	 * Add welcome component template
	 *
	 * @param  array  $templates [description]
	 * @param  string $subpage   [description]
	 * @return [type]            [description]
	 */
	public function page_templates( $templates = array(), $page = false, $subpage = false ) {

		$templates['upsale-page'] = Dashboard::get_instance()->get_view( 'upsale/main' );

		return $templates;
	}
}
