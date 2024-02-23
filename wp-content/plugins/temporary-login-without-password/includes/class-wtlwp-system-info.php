<?php

/**
 * Created by PhpStorm.
 * User: malayladu
 * Date: 2019-01-11
 * Time: 14:59
 */
class Wtlwp_Sytem_Info {

	/**
	 *
	 */
	public function get_info( $space ) {

		global $wpdb;

		$settings = array(
			'SITE_URL'                 => site_url(),
			'HOME_URL'                 => home_url(),
			'--',
			'TLWP Version'             => WTLWP_PLUGIN_VERSION,
			'WordPress Version'        => get_bloginfo( 'version' ),
			'Permalink Structure'      => get_option( 'permalink_structure' ),
			'--',
			'PHP Version'              => PHP_VERSION,
			'MySQL Version'            => $wpdb->db_version(),
			'Web Server Info'          => $_SERVER['SERVER_SOFTWARE'],
			'User Agent'               => $_SERVER['HTTP_USER_AGENT'],
			'Multi-site'               => is_multisite() ? 'Yes' : 'No',
			'--',
			'PHP Memory Limit'         => ini_get( 'memory_limit' ),
			'PHP Post Max Size'        => ini_get( 'post_max_size' ),
			'PHP Upload Max File size' => ini_get( 'upload_max_filesize' ),
			'PHP Time Limit'           => ini_get( 'max_execution_time' ) . ' sec',
			'--',
			'WP_DEBUG'                 => defined( 'WP_DEBUG' ) ? ( WP_DEBUG ? 'Enabled' : 'Disabled' ) : 'Not set',
			'DISPLAY ERRORS'           => ( ini_get( 'display_errors' ) ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A',
			'--',
			'WP Table Prefix'          => 'Length: ' . strlen( $wpdb->prefix ) . ' Status:' . ( strlen( $wpdb->prefix ) > 16 ? ' ERROR: Too Long' : ' Acceptable' ),
			'WP DB Charset/Collate'    => $wpdb->get_charset_collate(),
			'--',
			'Session'                  => isset( $_SESSION ) ? 'Enabled' : 'Disabled',
			'Session Name'             => esc_html( ini_get( 'session.name' ) ),
			'Cookie Path'              => esc_html( ini_get( 'session.cookie_path' ) ),
			'Save Path'                => esc_html( ini_get( 'session.save_path' ) ),
			'Use Cookies'              => ini_get( 'session.use_cookies' ) ? 'On' : 'Off',
			'Use Only Cookies'         => ini_get( 'session.use_only_cookies' ) ? 'On' : 'Off',
			'--',
			'WordPress Memory Limit'   => ( size_format( (int) WP_MEMORY_LIMIT * 1048576 ) ),
			'WordPress Upload Size'    => ( size_format( wp_max_upload_size() ) ),
			'Filesystem Method'        => get_filesystem_method(),
			'SSL SUPPORT'              => extension_loaded( 'openssl' ) ? 'SSL extension loaded' : 'SSL extension NOT loaded',
			'MB String'                => extension_loaded( 'mbstring' ) ? 'MB String extensions loaded' : 'MB String extensions NOT loaded',
			'--',
			'ACTIVE PLUGINS'           => "<br />",
			'INACTIVE PLUGINS'         => '<br />',
			'--',
			'CURRENT THEME'            => '',
		);

		$plugins = $this->get_plugins();

		$settings['ACTIVE PLUGINS']   .= $plugins['ACTIVE PLUGINS'];
		$settings['INACTIVE PLUGINS'] .= $plugins['INACTIVE PLUGINS'];
		$settings['CURRENT THEME']    .= $this->get_current_theme();


		return apply_filters( 'wtlwp_system_info', $settings );

	}

	function get_plugins() {

		$plugins = array(
			'INACTIVE PLUGINS' => '',
			'ACTIVE PLUGINS'   => ''
		);

		$all_plugins    = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );

		foreach ( $all_plugins as $plugin_path => $plugin ) {
			// If the plugin isn't active, don't show it.
			if ( ! in_array( $plugin_path, $active_plugins ) ) {
				$plugins['INACTIVE PLUGINS'] .= $plugin['Name'] . ': ' . $plugin['Version'] . "<br />" . str_repeat( ' ', 30 );
			} else {
				$plugins['ACTIVE PLUGINS'] .= $plugin['Name'] . ': ' . $plugin['Version'] . "<br />" . str_repeat( ' ', 30 );
			}
		}

		return $plugins;
	}

	function get_current_theme() {

		$current_theme = '';
		if ( function_exists( 'wp_get_theme' ) ) {
			$theme_data    = wp_get_theme();
			$current_theme = $theme_data->Name . ': ' . $theme_data->Version . "<br />" . str_repeat( ' ', 30 ) . $theme_data->get( 'Author' ) . ' (' . $theme_data->get( 'AuthorURI' ) . ')';
		} else if ( function_exists( 'get_theme_data' ) ) {
			$theme_data    = get_theme_data( get_stylesheet_directory() . '/style.css' );
			$current_theme = $theme_data['Name'] . ': ' . $theme_data['Version'] . "<br />" . str_repeat( ' ', 30 ) . $theme_data['Author'] . ' (' . $theme_data['AuthorURI'] . ')';
		}

		return $current_theme;

	}

	function render_system_info_page() {

		$space       = 30;
		$information = $this->get_info( $space );
		$output      = "<div class='p-4'>### <p class='font-semibold text-base'>System Info </p>###<br /><br />";

		foreach ( $information as $name => $value ) {
			if ( $value == '--' ) {
				$output .= "<br />";
				continue;
			}

			$length = $space - strlen( $name );
			$output .= "<b>" . $name . "</b>: " . str_repeat( ' ', $length ) . $value . "<br />";
		}

		$output .= "<br/>###<p class='font-semibold text-base'> End System Info</p> ###<br /></div>";

		return $output;
	}


}