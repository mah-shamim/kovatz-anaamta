<?php
/**
 * Listing document class
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Define Jet_Engine_Listings_Document class
 */
class Jet_Engine_Listings_Document {

	private $settings = array();
	private $main_id = null;

	/**
	 * Setup listing
	 * @param array $settings [description]
	 */
	public function __construct( $settings = array(), $id = null ) {

		if ( ! empty( $settings ) ) {
			
			$defaults = array(
				'listing_source'    => 'posts',
				'listing_post_type' => 'post',
				'listing_tax'       => 'category',
				'repeater_source'   => '',
				'repeater_field'    => '',
				'repeater_option'   => '',
			);

			$this->settings = array_merge( $defaults, $settings );
			
		} else {

			$listing_settings = get_post_meta( $id, '_elementor_page_settings', true );

			if ( empty( $listing_settings ) ) {
				$listing_settings = array();
			}

			// Ensure we get all possible data to cover all cases
			$all_meta = get_post_meta( $id );
			$use_meta = [];

			if ( ! empty( $all_meta ) ) {
				foreach ( $all_meta as $key => $value ) {
					$meta_set = isset( $value[0] ) ? maybe_unserialize( $value[0] ) : $value;
					if ( ! is_array( $meta_set ) ) {
						$use_meta[ $key ] = $meta_set;
					}
				}
			}

			$title            = get_the_title( $id );
			$listing_settings = array_merge( $use_meta, array( 'template_name' => $title ), $listing_settings );

			$this->settings = $listing_settings;

		}

		$this->main_id  = $id;
	}

	/**
	 * Update listing post settings
	 * 
	 * @param  array  $settings [description]
	 * @return [type]           [description]
	 */
	public function update_settings( $settings = [] ) {
		$settings = array_merge( $this->settings, $settings );
		$this->update_meta( '_elementor_page_settings', $settings );
		$this->update_meta( '_listing_data', $settings );
	}

	/**
	 * Listing CSS
	 * 
	 * @return [type] [description]
	 */
	public function get_listing_css() {
		return self::get_listing_css_by_id( $this->main_id );
	}

	public static function get_listing_css_by_id( $id ) {
		return get_post_meta( $id, '_jet_engine_listing_css', true );
	}

	/**
	 * Get meta data for current listing
	 * 
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function get_meta( $key ) {
		return get_post_meta( $this->main_id, $key, true );
	}

	/**
	 * Listing HTML
	 * 
	 * @return [type] [description]
	 */
	public function get_listing_html() {
		return self::get_listing_html_by_id( $this->main_id );
	}

	public static function get_listing_html_by_id( $id ) {
		return get_post_meta( $id, '_jet_engine_listing_html', true );
	}

	/**
	 * Listing CSS
	 * 
	 * @return [type] [description]
	 */
	public function update_listing_css( $css ) {
		return $this->update_meta( '_jet_engine_listing_css', $css );
	}

	/**
	 * Listing HTML
	 * 
	 * @return [type] [description]
	 */
	public function update_listing_html( $html ) {
		return $this->update_meta( '_jet_engine_listing_html', $html );
	}

	public function update_meta( $key, $value ) {
		update_post_meta( $this->main_id, $key, $value );
	}

	/**
	 * Returns listing ID
	 * @return [type] [description]
	 */
	public function get_main_id() {
		return $this->main_id;
	}

	/**
	 * Returns listing settings
	 *
	 * @param  string $setting [description]
	 * @return [type]          [description]
	 */
	public function get_settings( $setting = '' ) {

		if ( empty( $this->settings ) ) {
			return;
		}

		if ( empty( $setting ) ) {
			return $this->settings;
		} else {
			return isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : false;
		}

	}

}
