<?php
/**
 * All vendors page feature main class
 */
class WCV_All_Vendors_Page {

    /**
     * Is hmr enabled
     *
     * @var bool $is_hmr_enabled Is hmr enabled
     */
    public $is_hmr_enabled = false;

    /**
     * Env
     *
     * @var array $env Env
     */
    protected $env = array();

    /**
     * Host
     *
     * @var string $host Host
     */
    protected $host = '';

    /**
     * Port
     *
     * @var string $port Port
     */
    protected $port = '';

    /**
     * Dev base url
     *
     * @var string $dev_base_url Dev base url.
     */
    protected $dev_base_url = '';

    /**
     * Constructor
     */
    public function __construct() {
        if ( defined( 'HMR_DEV' ) && HMR_DEV ) {
            $this->is_hmr_enabled = true;
            $this->parse_env();
            $this->host         = isset( $this->env['VITE_DEV_SERVER_HOST'] ) ? $this->env['VITE_DEV_SERVER_HOST'] : 'localhost';
            $this->port         = isset( $this->env['VITE_DEV_SERVER_PORT'] ) ? $this->env['VITE_DEV_SERVER_PORT'] : '3000';
            $this->dev_base_url = 'http://' . $this->host . ':' . $this->port;
        }
        $this->includes();
        // Check if is all vendors page and admin is the referrer.
        if ( isset( $_GET['page'] ) && 'wcv-all-vendors' === $_GET['page'] ) { // phpcs:ignore
            add_action( 'admin_enqueue_scripts', array( $this, 'load_assets' ) );
            add_filter( 'script_loader_tag', array( $this, 'add_module_to_scripts_tag' ), 10, 2 );
        }
    }

    /**
     * Read env file.
     *
     * @version 2.4.8
     * @since   2.4.8
     * @return void
     */
    protected function parse_env() {
        $env_file_path = WCV_PLUGIN_DIR . '/.env';
        if ( ! file_exists( $env_file_path ) ) {
            return;
        }

        $env_file = file_get_contents( $env_file_path ); // phpcs:ignore
        $env_file = explode( "\n", $env_file );
        foreach ( $env_file as $env ) {
            $env = explode( '=', $env );
            if ( isset( $env[0] ) && isset( $env[1] ) ) {
                $key               = trim( $env[0] );
                $val               = trim( $env[1] );
                $this->env[ $key ] = $val;
            }
        }
    }

    /**
     * Include any classes
     */
    public function includes() {
        include_once 'api/class-wcv-rest-api.php';
    }

    /**
     * Add module to scripts tag
     *
     * @param string $tag    The script tag.
     * @param string $handle The script handle.
     *
     * @return string
     */
    public function add_module_to_scripts_tag( $tag, $handle ) {
        if ( strpos( $handle, 'wcv-all-vendors-page' ) !== false ) {
            $tag = str_replace( ' src', ' type="module" src', $tag ); // phpcs:ignore
        }
        return $tag;
    }

    /**
     * Loadd assets
     */
    public function load_assets() {
        wp_enqueue_editor();
        wp_enqueue_media();
        $js_object = $this->js_object();

        if ( $this->is_hmr_enabled ) {
            wp_enqueue_script( 'wcv-all-vendors-page', "$this->dev_base_url/@vite/client", array(), time(), true );
            wp_enqueue_script( 'wcv-all-vendors-page-main', "$this->dev_base_url/src/main.ts", array( 'jquery', 'editor' ), time(), true );
        } else {
            $manifest_json = $this->get_manifest_json();
            foreach ( $manifest_json as $entry => $info ) {
                $file_ext = $this->get_file_extension( $info['file'] );

                if ( 'js' === $file_ext ) {
                    wp_enqueue_script( 'wcv-all-vendors-page', esc_url( WCV_PLUGIN_APPS_PATH . 'avp/dist/' . $info['file'] ), array(), WCV_VERSION, true );
                } elseif ( 'css' === $file_ext ) {
                    $file_name = $this->get_entry_file_name( $entry );
                    wp_enqueue_style( 'wcv-all-vendors-page-' . $file_name, esc_url( WCV_PLUGIN_APPS_PATH . 'avp/dist/' . $info['file'] ), array(), WCV_VERSION );
                }
            }
        }

        if ( $js_object['maybe_init_map'] ) {
            $map_api_key = $js_object['map_api_key'];
            wp_enqueue_script( 'wcv-all-vendors-page-google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $map_api_key . '&libraries=places', array(), WCV_VERSION, false );
        }
        unset( $js_object['map_api_key'] );
        wp_localize_script( 'wcv-all-vendors-page', 'wcv_avp', $js_object );
    }

    /**
     * JS object
     */
    public function js_object() {
        $i18n             = require_once __DIR__ . '/wcv-all-vendors-page-i18n.php';
        $is_pro_active    = is_wcv_pro_active();
        $map_api_key      = get_option( 'wcvendors_pro_google_maps_api_key', '' );
        $maybe_init_map   = '' !== $map_api_key ? true : false;
        $view_order_nonce = wp_create_nonce( 'wcv_vendor_orders' );
        $js_object        = array(
            'rest_url'          => rest_url( 'wcv-api/v1' ),
            'nonce'             => wp_create_nonce( 'wp_rest' ),
            'view_products_url' => admin_url() . 'edit.php?post_type=product&vendor=',
            'view_orders_url'   => admin_url() . "edit.php?post_type=shop_order&wcv_vendor_orders=$view_order_nonce&vendor_id=",
            'is_pro_active'     => $is_pro_active,
            'logo_path'         => esc_url( WCV_ASSETS_URL ) . 'images/wcvendors_logo.png',
            'wc_countries'      => wcv_get_countries_states(),
            'wc_currency'       => get_woocommerce_currency_symbol(),
            'opening_times'     => ! $is_pro_active ? array() : array_merge(
				get_time_interval_options(),
				array(
					array( 'closed' => __( 'Closed', 'wcvendors-pro' ) ),
					array( 'open' => __( 'Open', 'wcvendors-pro' ) ),
				)
			),
            'opening_days'      => ! $is_pro_active ? array() : wcv_days_labels(),
            'map_zoom_level'    => get_option( 'wcvendors_pro_google_maps_zoom_level', 18 ),
            'html_settings'     => array(
                'use_media' => wc_string_to_bool( get_option( 'wcvendors_allow_editor_media', 'no' ) ),
            ),
            'maybe_init_map'    => $maybe_init_map,
            'map_api_key'       => $map_api_key,
            'i18n'              => $i18n,
            'tabs'              => require_once __DIR__ . '/wcv-all-vendors-page-setting-fields.php',
            'pluginDirUrl'      => esc_url( WCV_PLUGIN_APPS_PATH . 'avp' ),
            'admin_url'         => admin_url(),
        );
        return $js_object;
    }

    /**
     * Get Manifest.json file content
     */
    public function get_manifest_json() {
        $manifest_json_path = apply_filters( 'wcv_avp_manifest_json_path', WCV_PLUGIN_DIR . 'apps/avp/dist/manifest.json' );
        $response           = file_get_contents( $manifest_json_path ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

        return json_decode( $response, true );
    }

    /**
     * Get file extension
     *
     * @param string $file File path.
     */
    public function get_file_extension( $file ) {
        $file_info      = explode( '.', $file );
        $file_extension = end( $file_info );
        return $file_extension;
    }

    /**
     * Get entry file name
     *
     * @param string $entry Entry.
     */
    public function get_entry_file_name( $entry ) {
        $file_info           = explode( '/', $entry );
        $file_with_extension = end( $file_info );
        $file_info           = explode( '.', $file_with_extension );
        return $file_info[0];
    }
}
new WCV_All_Vendors_Page();
