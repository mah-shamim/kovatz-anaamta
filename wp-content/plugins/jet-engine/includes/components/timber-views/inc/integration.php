<?php
/**
 * Timber view class
 */
namespace Jet_Engine\Timber_Views;

use \Jet_Engine\Modules\Performance\Module as Perforamce_Module;


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Integration {

	private $nonce_action = 'jet_engine_timber_views_wizard';

	public function __construct() {

		// Add dashboard tweaks config
		add_action( 'jet-engine/modules/performance/tweaks-tab', [ $this, 'render_dashboard_tab' ] );
		add_filter( 'jet-engine/modules/performance/default-tweaks', [ $this, 'register_timber_tweak' ] );

		// Install timber
		add_action( 'wp_ajax_jet_engine_timber_views_install_source', [ $this, 'install_source' ] );

	}

	public function install_source() {
		
		$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce_action ) ) {
			wp_send_json_error( 'Link is expired. Please reload page and try again.' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Access denied.' );
		}

		if ( $this->has_timber() ) {
			wp_send_json_error( 'Timber already installed. Please uninstall current version of Timber library before installing the new one.' );
		}

		$sources = $this->timber_sources();
		$source_index = isset( $_REQUEST['source'] ) ? absint( $_REQUEST['source'] ) : -1;

		if ( 0 > $source_index ) {
			wp_send_json_error( 'Timber source not found in the request' );
		}

		if ( ! isset( $sources[ $source_index ] ) ) {
			wp_send_json_error( 'Such source does not exists' );
		}

		$this->install_timber( $sources[ $source_index ] );

	}

	public function install_timber( array $source = [] ):bool {
		
		$is_wp  = ( false === strpos( $source['url'], 'https://' ) ) ? true : false;
		$plugin = $source['slug'];

		if ( $is_wp ) {

			require_once ABSPATH . 'wp-admin/includes/plugin-install.php'; // Need for plugins_api

			$api = plugins_api(
				'plugin_information',
				array( 'slug' => $source['url'], 'fields' => array( 'sections' => false ) )
			);

			if ( is_wp_error( $api ) ) {
				return false;
			}

			if ( isset( $api->download_link ) ) {
				$url = $api->download_link;
			}

		} else {
			$url = $sources['url'];
		}

		if ( ! class_exists( '\Plugin_Upgrader', false ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		$skin     = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Plugin_Upgrader( $skin );
		$result   = $upgrader->install( $url );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result->get_error_message() );
		} elseif ( is_wp_error( $skin->result ) ) {
			if ( 'folder_exists' !== $skin->result->get_error_code() ) {
				wp_send_json_error( $skin->result->get_error_message() );
			}
		} elseif ( $skin->get_errors()->has_errors() ) {
			wp_send_json_error( $skin->get_error_message() );
		} elseif ( is_null( $result ) ) {
			global $wp_filesystem;

			$msg = __( 'Unable to connect to the filesystem. Please confirm your credentials.' );

			// Pass through the error from WP_Filesystem if one was raised.
			if ( $wp_filesystem instanceof WP_Filesystem_Base 
				&& is_wp_error( $wp_filesystem->errors ) 
				&& $wp_filesystem->errors->has_errors() 
			) {
				$msg = esc_html( $wp_filesystem->errors->get_error_message() );
			}

			wp_send_json_error( $msg );

		}

		Perforamce_Module::instance()->update_tweaks( [ 'timber_slug' => $plugin ] );

		if ( ! is_plugin_active( $plugin ) ) {
			$activate = activate_plugin( $plugin );
		}

		if( ! function_exists('get_plugin_data') ){
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		$plugin_data = get_plugin_data( trailingslashit( WP_PLUGIN_DIR ) . $plugin );

		wp_send_json_success( [
			'version' => $plugin_data['Version'],
		] );

	}

	public function render_dashboard_tab() {

		wp_enqueue_script(
			'jet-engine-timber-wizard', 
			Package::instance()->package_url( 'assets/js/tweaks-wizard.js' ),
			[],
			jet_engine()->get_version(),
			true
		);

		wp_localize_script( 
			'jet-engine-timber-wizard', 
			'JetEngineTimberViewsWizard',
			[
				'sources'     => $this->timber_sources(),
				'has_timber'  => $this->has_timber(),
				'version'     => $this->timber_version(),
				'nonce'       => wp_create_nonce( $this->nonce_action ),
				'description' => __( 'Timber is a plugin for integrating the Twig template engine into WordPress. Using Twig syntax and some plain HTML and CSS, you’ll be able to create high-performance Listings.', 'jet-engine' ),
			]
		);

		add_action( 'admin_footer', [ $this, 'print_styles' ] );

		?>
		<cx-vui-switcher
			label="<?php _e( 'Timber/Twig Views', 'jet-engine' ); ?>"
			description="<?php _e( 'Enable/disable high performant Timber/Twig view type for Listings', 'jet-engine' ); ?>"
			:wrapper-css="[ 'equalwidth', 'collpase-sides' ]"
			v-model="tweaks.enable_timber_views"
		></cx-vui-switcher>
		<jet-timber-views-wizard v-if="tweaks.enable_timber_views"></jet-timber-views-wizard>
		<?php
	}

	public function print_styles() {
		?>
		<style>
			.jet-timber-views-wizard__desc {
				display: flex;
				gap: 10px;
				color: #7B7E81;
				padding: 0 0 15px;
				& svg {
					margin: 3px 0 0 0;
				}
			}
			.jet-timber-views-wizard__version {
				padding: 0 0 15px;
				font-size: 15px;
				line-height: 20px;
				color: #23282D;
				font-weight: 500;
			}
			.jet-timber-views-wizard__sources {
				display: flex;
				gap: 20px;
				padding: 0 0 20px;
			}
			.jet-timber-views-wizard__source {
				flex: 1;
				padding: 15px;
				border: 2px solid #ECECEC;
				border-radius: 4px;
				cursor: pointer;
				transition: 150ms linear;
				&:hover {
					border-color: #007CBA;
					& .jet-timber-views-wizard__source-name {
						color: #007CBA;
					}
				}
			}
			.jet-timber-views-wizard__source-name {
				font-weight: bold;
				color: #23282D;
				transition: 150ms linear;
			}
			.jet-timber-views-wizard__source-desc {
				color: #7B7E81;
			}
			.jet-timber-views-wizard__error {
				color: #C92C2C;
				padding: 0 0 20px;
			}
		</style>
		<?php
	}

	public function register_timber_tweak( $tweaks ) {
		$tweaks['enable_timber_views'] = false;
		$tweaks['timber_slug']         = false;
		return $tweaks;
	}

	public function is_enabled() {
		return Perforamce_Module::instance()->is_tweak_active( 'enable_timber_views' );
	}

	public function has_timber():bool {
		return class_exists( '\Timber\Timber' );
	}

	public function timber_version():string {
		return $this->has_timber() ? \Timber\Timber::$version : 'not found';
	}

	public function timber_sources():array {
		return [
			[
				'name' => 'Timber 1.X',
				'description' => 'Timber version from WP.org',
				'url' => 'timber-library',
				'slug' => 'timber-library/timber.php',
			],
			[
				'name' => 'Timber 2.X',
				'description' => 'Timber 2.0.0+ bundled into separate plugin',
				'url' => 'https://account.crocoblock.com/free-download/crocoblock-timber-library.zip',
				'slug' => 'crocoblock-timber-library/crocoblock-timber-library',
			]
		];
	}

}
