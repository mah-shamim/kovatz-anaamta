<?php
/**
 * Polylang compatibility package
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Polylang_Package' ) ) {

	class Jet_Engine_Polylang_Package {

		public function __construct() {
			add_filter( 'jet-engine/listings/frontend/rendered-listing-id', array( $this, 'set_translated_object' ) );
			add_filter( 'jet-engine/forms/render/form-id',                  array( $this, 'set_translated_object' ) );

			// Translate Admin Labels
			add_filter( 'jet-engine/compatibility/translate-string', array( $this, 'translate_admin_labels' ) );

			// Disable `suppress_filters` in the `get_posts` args.
			add_filter( 'jet-engine/compatibility/get-posts/args', array( $this, 'disable_suppress_filters' ) );
		}

		/**
		 * Set translated object ID to show
		 *
		 * @param int|string $obj_id Object ID
		 *
		 * @return false|int|null
		 */
		public function set_translated_object( $obj_id ) {

			if ( function_exists( 'pll_get_post' ) ) {

				$translation_obj_id = pll_get_post( $obj_id );

				if ( null === $translation_obj_id ) {
					// the current language is not defined yet
					return $obj_id;
				} elseif ( false === $translation_obj_id ) {
					//no translation yet
					return $obj_id;
				} elseif ( $translation_obj_id > 0 ) {
					// return translated post id
					return $translation_obj_id;
				}
			}

			return $obj_id;
		}

		/**
		 * Translate Admin Labels
		 *
		 * @param  string $label
		 * @return string
		 */
		public function translate_admin_labels( $label ) {

			pll_register_string( 'jet-engine', $label, 'JetEngine', true );

			return pll__( $label );
		}

		public function disable_suppress_filters( $args = array() ) {
			$args['suppress_filters'] = false;
			return $args;
		}

	}

}

new Jet_Engine_Polylang_Package();
