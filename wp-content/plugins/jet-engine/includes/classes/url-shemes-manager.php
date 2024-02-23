<?php
/**
 * Allow to apply selected URL scheme to a string
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_URL_Shemes_Manager' ) ) {

	/**
	 * Define Jet_Engine_URL_Shemes_Manager class
	 */
	class Jet_Engine_URL_Shemes_Manager {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Apply sheme to passed string
		 *
		 * @param  [type] $value  [description]
		 * @param  [type] $scheme [description]
		 * @return [type]         [description]
		 */
		public function apply_scheme( $value, $scheme = null ) {

			if ( ! $scheme || ! $this->is_allowed_scheme( $scheme ) ) {
				return $value;
			}

			$sanitize_cb = $this->get_url_sanitize_scheme( $scheme );

			if ( ! $sanitize_cb ) {
				return $scheme . ':' . $value;
			} else {
				return call_user_func( $sanitize_cb, $value, $scheme );
			}

		}

		/**
		 * Returns
		 * @return boolean [description]
		 */
		public function is_allowed_scheme( $scheme ) {
			$schemes = $this->get_allowed_url_schemes();
			return isset( $schemes[ $scheme ] );
		}

		/**
		 * Returns list of llowed URL schemes
		 *
		 * @return array
		 */
		public function get_allowed_url_schemes() {

			return apply_filters( 'jet-engine/url-schemes/allowed-url-schemes', array(
				'callto'           => __( 'Callto - launching Skype call (requires username or phone number)', 'jet-engine' ),
				'facetime'         => __( 'Facetime - call to FaceTime (requires phone number or address)', 'jet-engine' ),
				'fax'              => __( 'Fax - used for telefacsimile numbers (requires phone number)', 'jet-engine' ),
				'git'              => __( 'Git - a link to a GIT repository (requires a link to repository)', 'jet-engine' ),
				'gtalk'            => __( 'Gtalk - start a chat with a Google Talk user (requires an e-mail)', 'jet-engine' ),
				'mailto'           => __( 'Mailto - launch system e-mail UI (requires an e-mail)', 'jet-engine' ),
				'maps'             => __( 'Maps - link to map location (requires a location string)', 'jet-engine' ),
				'market_query'     => __( 'Google Play - link to search query (requires an query string)', 'jet-engine' ),
				'market_publisher' => __( 'Google Play - link to search query by publisher (requires an query string)', 'jet-engine' ),
				'skype'            => __( 'Skype - launching Skype call (requires username or phone number)', 'jet-engine' ),
				'spotify_artist'   => __( 'Spotify artist - show artist page in Spotify (requires artist ID)', 'jet-engine' ),
				'spotify_album'    => __( 'Spotify album - show album page in Spotify (requires album ID)', 'jet-engine' ),
				'spotify_track'    => __( 'Spotify track - show track page in Spotify (requires track ID)', 'jet-engine' ),
				'spotify_search'   => __( 'Spotify search - show seqrch query results in Spotify (requires query string)', 'jet-engine' ),
				'tel'              => __( 'Tel - call to telephone number', 'jet-engine' ),
				'zoommtg'          => __( 'Zoom - launch Zoom app (requires Zoom meeting URL)', 'jet-engine' ),
			) );

		}

		/**
		 * Return required data to apply URL scheme
		 *
		 * @param  [type] $scheme [description]
		 * @return [type]        [description]
		 */
		public function get_url_sanitize_scheme( $scheme ) {

			$data = apply_filters( 'jet-engine/url-schemes/sanitize-schemes-callbacks', array(
				'facetime'         => array( $this, 'sanitize_slashes' ),
				'git'              => array( $this, 'sanitize_slashes' ),
				'gtalk'            => array( $this, 'sanitize_gtalk' ),
				'maps'             => array( $this, 'sanitize_maps' ),
				'market_query'     => array( $this, 'sanitize_market_query' ),
				'market_publisher' => array( $this, 'sanitize_market_publisher' ),
				'spotify_artist'   => array( $this, 'sanitize_spotify_url' ),
				'spotify_album'    => array( $this, 'sanitize_spotify_url' ),
				'spotify_track'    => array( $this, 'sanitize_spotify_url' ),
				'spotify_search'   => array( $this, 'sanitize_spotify_url' ),
				'zoommtg'          => array( $this, 'sanitize_zoom' ),
			) );

			return ( isset( $data[ $scheme ] ) && is_callable( $data[ $scheme ] ) ) ? $data[ $scheme ] : false;

		}

		/**
		 * Sanitize slahes
		 *
		 * @return [type] [description]
		 */
		public function sanitize_slashes( $value, $scheme ) {
			return $scheme . '://' . ltrim( $value, '//' );
		}

		/**
		 * Sanitize Gtalk URL
		 *
		 * @param  [type] $scheme [description]
		 * @param  [type] $value [description]
		 * @return [type]        [description]
		 */
		public function sanitize_gtalk( $value, $scheme ) {
			return $scheme . ':chat?jid=' . sanitize_email( $value );
		}

		/**
		 * Sanitize maps URL
		 *
		 * @param  [type] $scheme [description]
		 * @param  [type] $value [description]
		 * @return [type]        [description]
		 */
		public function sanitize_maps( $value, $scheme ) {
			return $scheme . ':q=' . $value;
		}

		/**
		 * Sanitize paly market URL
		 *
		 * @param  [type] $scheme [description]
		 * @param  [type] $value [description]
		 * @return [type]        [description]
		 */
		public function sanitize_market_query( $value, $scheme ) {
			return 'market://search?q=' . $value;
		}

		/**
		 * Sanitize play market publisher URL
		 *
		 * @param  [type] $scheme [description]
		 * @param  [type] $value [description]
		 * @return [type]        [description]
		 */
		public function sanitize_market_publisher( $value, $scheme ) {
			return 'market://search?q=pub:' . $value;
		}

		/**
		 * Sanitize Spotify URL
		 *
		 * @param  [type] $scheme [description]
		 * @param  [type] $value  [description]
		 * @return [type]         [description]
		 */
		public function sanitize_spotify_url( $value, $scheme ) {
			$scheme = str_replace( 'spotify_', '', $scheme );
			$prefix = 'spotify';
			$prefix . ':' . $scheme . ':' . $value;
		}

		/**
		 * Sanitize Zoom URL
		 *
		 * @param  [type] $scheme [description]
		 * @param  [type] $value  [description]
		 * @return [type]         [description]
		 */
		public function sanitize_zoom( $value, $scheme ) {
			$value = preg_replace( '/^http[s]?:/', '', $value );
			return sanitize_slashes( $value, $scheme );
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return Jet_Engine
		 */
		public static function instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

	}

}
