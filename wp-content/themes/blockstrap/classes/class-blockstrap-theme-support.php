<?php
/**
 * Theme support
 *
 * @package BlockStrap
 * @since 1.0.0
 */

/**
 * Add theme support
 *
 * @since 1.0.0
 */
class BlockStrap_Theme_Support {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'load_translations' ) );
		add_action( 'after_setup_theme', array( $this, 'action_setup' ) );
		add_action( 'after_setup_theme', array( $this, 'action_content_width' ), 0 );
		add_filter( 'get_block_templates', array( $this, 'default_template_types' ), 20000, 3 );
		add_action( 'ayecode-ui-settings', array( $this, 'set_aui_settings' ), 10, 3 );

		// load only if theme is not blockstrap
		if ( ! defined('BLOCKSTRAP_BLOCKS_VERSION' ) ) {
			add_action( 'admin_notices', array( __CLASS__, 'plugin_notice' ) );
		}
	}

	/**
	 * Show a notice asking to install the BlocStrap BLocks Plugin.
	 *
	 * @return void
	 */
	public static function plugin_notice() {

		$pathpluginurl = WP_PLUGIN_DIR . '/blockstrap-page-builder-blocks/blockstrap-page-builder-blocks.php';

		$installed = file_exists( $pathpluginurl );

		if ( $installed ) {

			$activate_url     = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'activate',
						'plugin'  => 'blockstrap-page-builder-blocks/blockstrap-page-builder-blocks.php',
					),
					admin_url( 'plugins.php' )
				),
				'activate-plugin_blockstrap-page-builder-blocks/blockstrap-page-builder-blocks.php'
			);

			$class           = 'notice notice-warning is-dismissible';
			$name            = __( 'Thanks for installing the BlockStrap Theme', 'blockstrap' );
			$install_message = __( 'The BlockStrap theme works best with the BlockStrap Blocks plugin, please install it for full functionality.', 'blockstrap' );

			printf(
				'<div class="%1$s"><h3>%2$s</h3><p>%3$s</p><p><a href="%4$s" class="button button-primary">%5$s</a> </p></div>',
				esc_attr( $class ),
				esc_html( $name ),
				esc_html( $install_message ),
				esc_url_raw( $activate_url ),
				esc_html__( 'Activate BlockStrap Blocks Plugin', 'blockstrap' )
			);
		}else{
			$install_url     = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'install-plugin',
						'plugin'  => 'blockstrap-page-builder-blocks',
					),
					admin_url( 'update.php' )
				),
				'install-plugin_blockstrap-page-builder-blocks'
			);

			$class           = 'notice notice-warning is-dismissible';
			$name            = __( 'Thanks for installing the BlockStrap Theme', 'blockstrap' );
			$install_message = __( 'The BlockStrap theme works best with the BlockStrap Blocks plugin, please install it for full functionality.', 'blockstrap' );

			printf(
				'<div class="%1$s"><h3>%2$s</h3><p>%3$s</p><p><a href="%4$s" class="button button-primary">%5$s</a> </p></div>',
				esc_attr( $class ),
				esc_html( $name ),
				esc_html( $install_message ),
				esc_url_raw( $install_url ),
				esc_html__( 'Install BlockStrap Blocks Plugin', 'blockstrap' )
			);
		}


	}

	/**
	 * @param $settings
	 * @param $db_settings
	 * @param $defaults
	 *
	 * @return mixed
	 */
	public function set_aui_settings( $settings, $db_settings, $defaults ) {

		$settings['bs_ver'] = '5'; // set BS ver to 5
		$settings['css']    = 'core'; // set CSS to full mode

		return $settings;
	}

	/**
	 * Load the theme translation files.
	 *
	 * @return void
	 */
	public function load_translations() {
		load_theme_textdomain( 'blockstrapr', get_template_directory() . '/languages' );
	}

	/**
	 * Filter the theme FSE templates.
	 *
	 * @param $default_template_types
	 * @param $query
	 * @param $template_type
	 *
	 * @return mixed
	 */
	public function default_template_types( $default_template_types, $query, $template_type ) {

		foreach ( $default_template_types as $k => $t ) {

			if ( defined( 'GEODIRECTORY_VERSION' ) && defined( 'BLOCKSTRAP_BLOCKS_VERSION' ) && 'wp_template' === $template_type ) {
				if ( 'gd-single' === $t->slug ) {
					$default_template_types[ $k ]->title       = 'GD Single';
					$default_template_types[ $k ]->description = __( 'The default template for displaying any GD single post. Use the `add new` feature to add a template per CPT.', 'blockstrap' );
				}
			} else {
				// hide template if required plugins not installed
				if ( 'gd-single' === $t->slug || 'gd-archive' === $t->slug || 'gd-search' === $t->slug ) {
					unset( $default_template_types[ $k ] );
				}
			}
		}

		return $default_template_types;
	}

	/**
	 * Adds theme-supports.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function action_setup() {

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );

		/*
		* Switch default core markup to output valid HTML5.
		* The comments block uses the markup from the comments template.
		*/
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
			)
		);

		// Add support for Block Styles.
		add_theme_support( 'wp-block-styles' );

		// Enqueue editor styles.
		add_theme_support( 'editor-styles' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		// Theme color pallets
		add_theme_support( 'editor-color-palette' );

		// Menus
//		add_theme_support( 'menus' );

		// remove wp-container-X inline CSS helpers
		remove_filter( 'render_block', 'wp_render_layout_support_flag', 10, 2 );
		remove_filter( 'render_block', 'gutenberg_render_layout_support_flag', 10, 2 );

		// remove core WP patterns.
		remove_theme_support( 'core-block-patterns' );
	}


	/**
	 * Set the content width based on the theme's design and stylesheet.
	 *
	 * Priority 0 to make it available to lower priority callbacks.
	 *
	 * @global int $content_width Content width.
	 * @since 1.0.0
	 * @access public
	 */
	public function action_content_width() {
		// This variable is intended to be overruled from themes.
		// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
		$GLOBALS['content_width'] = apply_filters( 'blockstrap_content_width', 920 );
	}

}

new BlockStrap_Theme_Support();
