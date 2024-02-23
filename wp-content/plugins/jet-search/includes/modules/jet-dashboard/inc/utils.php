<?php
namespace Jet_Dashboard;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Dashboard_Utils class
 */
class Utils {

	/**
	 * [$api_url description]
	 * @var string
	 */
	public static $api_url = 'https://api.crocoblock.com';

	/**
	 * [$settings description]
	 * @var null
	 */
	public static $license_data = null;

	/**
	 * [$license_data_key description]
	 * @var string
	 */
	public static $license_data_key = 'jet-license-data';

	/**
	 * [get_api_url description]
	 * @return [type] [description]
	 */
	public static function get_api_url() {

		return apply_filters( 'jet-dashboard/license/api-url', self::$api_url );
	}

	/**
	 * [get_site_url description]
	 * @return [type] [description]
	 */
	public static function get_site_url() {
		$urlParts = parse_url( site_url( '/' ) );

		$site_url = $urlParts['host'] . $urlParts['path'];

		$site_url = preg_replace( '#^https?://#', '', rtrim( $site_url ) );

		$site_url = str_replace( 'www.', '', $site_url );

		return $site_url;
	}

	/**
	 * [get description]
	 * @param  [type]  $setting [description]
	 * @param  boolean $default [description]
	 * @return [type]           [description]
	 */
	public static function get_license_data( $setting = false, $default = false ) {

		if ( ! $setting ) {
			return get_option( self::$license_data_key, array() );
		}

		if ( null === self::$license_data ) {
			self::$license_data = get_option( self::$license_data_key, array() );
		}

		return isset( self::$license_data[ $setting ] ) ? self::$license_data[ $setting ] : $default;
	}

	/**
	 * [set_license_data description]
	 * @param [type]  $setting [description]
	 * @param boolean $value   [description]
	 */
	public static function set_license_data( $setting = false, $value = false ) {

		$current_license_data = get_option( self::$license_data_key, array() );

		$current_license_data[ $setting ] = $value;

		update_option( self::$license_data_key, $current_license_data );
	}

	/**
	 * [get_license_list description]
	 * @return [type] [description]
	 */
	public static function get_license_list() {

		$license_list = self::get_license_data( 'license-list', [] );

		return $license_list;
	}

	/**
	 * [license_data_expire_sync description]
	 * @return [type] [description]
	 */
	public static function license_data_expire_sync() {

		$license_list = self::get_license_data( 'license-list', [] );

		if ( ! empty( $license_list ) ) {

			foreach ( $license_list as $license_key => $license_data ) {
				$license_details = $license_data['licenseDetails'];

				$is_expired = ( 'expired' === $license_data['licenseStatus'] ) ? true : false;

				if ( ! empty( $license_details ) ) {
					$is_expired = self::license_expired_check( $license_details['expire'] );
				}

				if ( $is_expired ) {
					$license_list[$license_key]['licenseStatus'] = 'expired';
				}
			}
		}

		self::set_license_data( 'license-list', $license_list );
	}

	/**
	 * [get_plugin_license_key description]
	 * @param  boolean $setting [description]
	 * @param  boolean $value   [description]
	 * @return [type]           [description]
	 */
	public static function get_plugin_license_key( $plugin_slug ) {

		$license_list = self::get_license_data( 'license-list', [] );

		$plugin_license_key = false;

		if ( ! empty( $license_list ) ) {

			foreach ( $license_list as $license_key => $license_data ) {

				if ( 'expired' === $license_data['licenseStatus'] ) {
					continue;
				}

				$license_details = $license_data['licenseDetails'];

				if ( empty( $license_details ) ) {
					continue;
				}

				$is_expired = self::license_expired_check( $license_details['expire'] );

				if ( $is_expired ) {
					$license_list[$license_key]['licenseStatus'] = 'expired';

					continue;
				}

				$license_plugins = $license_details['plugins'];

				if ( array_key_exists( $plugin_slug, $license_plugins ) ) {
					$plugin_license_key = $license_key;

					break;
				}
			}
		}

		if ( $plugin_license_key ) {
			return $plugin_license_key;
		}

		return false;
	}

	/**
	 * [package_url description]
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public static function package_url( $plugin_slug = false, $version = false ) {

		$license_key = self::get_plugin_license_key( $plugin_slug );

		if ( ! $license_key ) {
			return false;
		}

		return add_query_arg(
			array(
				'action'   => 'get_plugin_update',
				'license'  => self::get_plugin_license_key( $plugin_slug ),
				'plugin'   => $plugin_slug,
				'version'  => $version,
				'site_url' => urlencode( self::get_site_url() ),
			),
			self::get_api_url()
		);
	}

	/**
	 * [if_license_expire_check description]
	 * @param  boolean $expire_date [description]
	 * @return [type]               [description]
	 */
	public static function license_expired_check( $expire_date = false, $day_to_expire = 0 ) {

		if ( '0000-00-00 00:00:00' === $expire_date
			|| '1000-01-01 00:00:00' === $expire_date
			|| 'lifetime' === $expire_date
		) {
			return false;
		}

		$current_time = time();

		$current_time = strtotime( sprintf( '+%s day', $day_to_expire ), $current_time );

		$expire_time = strtotime( $expire_date );

		if ( $current_time > $expire_time ) {
			return true;
		}

		return false;
	}

	/**
	 * @param array $attributes
	 * @return string
	 */
	public static function print_html_attributes( $attributes = [] ) {
		$rendered_attributes = [];

		foreach ( $attributes as $attribute_key => $attribute_values ) {
			if ( is_array( $attribute_values ) ) {
				$attribute_values = implode( ' ', $attribute_values );
			}

			$rendered_attributes[] = sprintf( '%1$s="%2$s"', $attribute_key, esc_attr( $attribute_values ) );
		}

		return implode( ' ', $rendered_attributes );
	}

}
