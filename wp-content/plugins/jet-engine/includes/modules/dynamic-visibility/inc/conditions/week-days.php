<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Week_Days extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'week_days';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Days of week', 'jet-engine' );
	}

	/**
	 * Returns group for current operator
	 *
	 * @return string
	 */
	public function get_group() {
		return 'date_time';
	}

	/**
	 * Check condition by passed arguments
	 *
	 * @return boolean
	 */
	public function check( $args = array() ) {

		$type        = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$week_days   = ! empty( $args['condition_settings']['week_days'] ) ? $args['condition_settings']['week_days'] : array();
		$current_day = current_time( 'w' );

		if ( ! is_array( $week_days ) ) {
			$week_days = array( $week_days );
		}

		if ( 'hide' === $type ) {
			return ! in_array( $current_day, $week_days );
		} else {
			return in_array( $current_day, $week_days );
		}

	}

	/**
	 * Returns condition specific repeater controls
	 */
	public function get_custom_controls() {

		global $wp_locale;

		return array(
			'week_days' => array(
				'label'    => __( 'Days of Week', 'jet-engine' ),
				'type'     => 'select2',
				'multiple' => true,
				'default'  => array(),
				'options'  => $wp_locale->weekday,
			),
		);
	}

	/**
	 * Check if is condition available for meta fields control
	 *
	 * @return boolean [description]
	 */
	public function is_for_fields() {
		return false;
	}

	/**
	 * Check if is condition available for meta value control
	 *
	 * @return boolean [description]
	 */
	public function need_value_detect() {
		return false;
	}

}

add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', function( $manager ) {
	$manager->register_condition( new Week_Days() );
} );
