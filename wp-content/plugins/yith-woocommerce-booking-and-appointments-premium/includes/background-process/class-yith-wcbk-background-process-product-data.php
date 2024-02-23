<?php
/**
 * Class YITH_WCBK_Background_Process_Product_Data
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Background_Process_Product_Data' ) ) {
	/**
	 * Class YITH_WCBK_Background_Process_Product_Data
	 * handle creating booking product data in background
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Background_Process_Product_Data extends YITH_WCBK_Background_Process {
		/**
		 * Retrieve the action type
		 *
		 * @return string
		 */
		public function get_action_type() {
			return 'product_data';
		}

		/**
		 * Return a list of notices to show
		 *
		 * @return array
		 */
		public function get_notices() {
			return array();
		}
	}
}
