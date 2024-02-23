<?php
namespace ElementsKit;

use ElementsKit\Export\Export_Screen;
use ElementsKit\Import\Import_Screen;

defined( 'ABSPATH' ) || exit;


/**
 * ElementsKit - the God class.
 * Initiate all necessary classes, hooks, configs.
 *
 * @since 1.1.0
 */
class Plugin{


	/**
	 * The plugin instance.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @var Handler
	 */
    public static $instance = null;

    /**
     * Construct the plugin object.
     *
     * @since 1.0.0
     * @access public
     */
    public function __construct() {
        // Call the method for ElementsKit autoloader.
        $this->registrar_autoloader();

        Hooks\Register_Modules::instance();
        Hooks\Register_Widgets::instance();

        Libs\Framework\Attr::instance();

        new Widgets\Init\Enqueue_Scripts();

		add_action('wp_enqueue_scripts', [$this, 'common_enqueue_scripts']);

        // Register updater module
        new Libs\Updater\Init();

        Export_Screen::instance()->init();
		Import_Screen::instance()->init();

        // Register license module
        $license = Libs\Framework\Classes\License::instance(); 

        if($license->status() != 'valid' && apply_filters('elementskit/license/hide_banner', false) != true){
            \Oxaim\Libs\Notice::instance('elementskit', 'pro-not-active') 
            ->set_class('error')
            ->set_dismiss('global', (3600 * 24 * 30))
            ->set_message(esc_html__('Please activate ElementsKit to get automatic updates, premium support and unlimited access to the layout library of ElementsKit.', 'elementskit'))
            ->set_button([
                'url' => self_admin_url('admin.php?page=elementskit-license'),
                'text' => 'Activate License Now',
                'class' => 'button-primary'
            ])
            ->call();
        }   
    }

    /**
     * Enqueue common scripts
     *
     * @since 2.8.0
     * @access public
     */
	public function common_enqueue_scripts() {
		wp_register_script( 'animejs', \ElementsKit::module_url() . 'parallax/assets/js/anime.js', [], \ElementsKit::version(), true );
	}


    /**
     * Autoloader.
     *
     * ElementsKit autoloader loads all the classes needed to run the plugin.
     *
     * @since 1.0.0
     * @access private
     */
    private function registrar_autoloader() {
        require_once \ElementsKit::plugin_dir() . 'libs/composer/vendor/build/vendor/src/autoload.php';
        //require_once \ElementsKit::plugin_dir() . 'libs/composer/vendor/scoper-autoload.php';
        require_once \ElementsKit::plugin_dir() . 'autoloader.php';
        Autoloader::run();
    }


    /**
     * Instance.
     *
     * Ensures only one instance of the plugin class is loaded or can be loaded.
     *
     * @since 1.0.0
     * @access public
     * @static
     *
     * @return Handler An instance of the class.
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {

            // Fire when ElementsKit instance.
            self::$instance = new self();

            // Fire when ElementsKit was fully loaded and instantiated.
            do_action( 'elementskit/loaded' );
        }

        return self::$instance;
    }
}

// Run the instance.
Plugin::instance();