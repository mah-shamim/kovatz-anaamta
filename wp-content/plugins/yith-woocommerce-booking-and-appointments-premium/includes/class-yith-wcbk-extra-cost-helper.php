<?php
/**
 * Class YITH_WCBK_Extra_Cost_Helper
 * helper class for Extra Cost
 *
 * @author  YITH
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Extra_Cost_Helper' ) ) {
	/**
	 * Class YITH_WCBK_Extra_Cost_Helper
	 *
	 * @author Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCBK_Extra_Cost_Helper {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * Get all person types by arguments
		 *
		 * @param array $args Arguments.
		 *
		 * @return array
		 */
		public function get_extra_costs( $args = array() ) {
			do_action( 'yith_wcbk_before_get_extra_costs', $args );

			$default_args = array(
				'post_type'        => YITH_WCBK_Post_Types::EXTRA_COST,
				'post_status'      => 'publish',
				'posts_per_page'   => - 1,
				'suppress_filters' => false,
				'fields'           => 'ids',
				'orderby'          => 'title',
				'order'            => 'ASC',
			);

			$args  = wp_parse_args( $args, $default_args );
			$posts = get_posts( $args );

			do_action( 'yith_wcbk_after_get_extra_costs', $args );

			return $posts;
		}
	}
}
