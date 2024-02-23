<?php 
namespace ElementsKit\Libs\Framework;
use ElementsKit\Libs\Framework\Classes\Utils;

defined( 'ABSPATH' ) || exit;

class Attr{

    /**
	 * The class instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @var Attr
	 */
    public static $instance = null;
    public $utils;

    public static function get_dir(){
        return \ElementsKit::lib_dir() . 'framework/';
    }

    public static function get_url(){
        return \ElementsKit::lib_url() . 'framework/';
    }

    public static function key(){
        return 'elementskit';
    }

    public function __construct() {
        $this->utils = Classes\Utils::instance();
        new Classes\Ajax;

        add_action('admin_menu', [$this, 'register_sub_menus'], 999);

        // register js/ css
        add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );
        
    }

    public function include_files(){

    }

    public function enqueue_scripts(){
        // wp_register_style( 'elementskit-admin-global', \ElementsKit::lib_url() . 'framework/assets/css/admin-global.css', \ElementsKit::version() );
        // wp_enqueue_style( 'elementskit-admin-global' );
    }


    public function register_sub_menus(){

        add_submenu_page( self::key(), esc_html__( 'License', 'elementskit' ), esc_html__( 'License', 'elementskit' ), 'manage_options', self::key().'-license', [$this, 'register_settings_contents__license'], 11);
    }

    public function register_settings_contents__license(){
        include self::get_dir() . 'pages/license.php';
    }


    /**
     * Instance.
     * 
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @since 1.0.0
     * @access public
     * @static
     *
     * @return Build_Widgets An instance of the class.
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {

            // Fire the class instance
            self::$instance = new self();
        }

        return self::$instance;
    }
}