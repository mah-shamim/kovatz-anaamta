<?php
/**
 * Accessibility manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Accessibility' ) ) {

	class Jet_Engine_Accessibility {

		/**
		 * Contrast UI
		 * @return [type] [description]
		 */
		public function contrast_ui( $handle ) {

			if ( ! apply_filters( 'jet-engine/accessibility/contrast-ui', false ) ) {
				return;
			}

			wp_add_inline_style( $handle, $this->get_contrast_css() );

		}

		/**
		 * Returns contrast UI
		 *
		 * @return [type] [description]
		 */
		public function get_contrast_css() {
			ob_start();
			include jet_engine()->plugin_path( 'assets/css/admin/cotrast-ui.css' );
			return ob_get_clean();
		}

	}

}
