<?php
namespace Jet_Engine\Modules\Dynamic_Visibility\Conditions;

class Time_Period extends Base {

	/**
	 * Returns condition ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'time_period';
	}

	/**
	 * Returns condition name
	 *
	 * @return string
	 */
	public function get_name() {
		return __( 'Time Period', 'jet-engine' );
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

		$type      = ! empty( $args['type'] ) ? $args['type'] : 'show';
		$time_from = ! empty( $args['condition_settings']['time_from'] ) ? $args['condition_settings']['time_from'] : false;
		$time_to   = ! empty( $args['condition_settings']['time_to'] ) ? $args['condition_settings']['time_to'] : false;

		$current_time = current_time( 'H:i' );
		$between_time_period = false;

		if ( $time_from && $time_to ) {

			if ( $time_from <= $time_to ) { // 09:00 - 18:00
				if ( $current_time >= $time_from && $current_time <= $time_to ) {
					$between_time_period = true;
				}
			} else { // 18:00 - 09:00
				if ( $current_time >= $time_from || $current_time <= $time_to ) {
					$between_time_period = true;
				}
			}

		} else if ( $time_from ) {

			if ( $current_time >= $time_from ) {
				$between_time_period = true;
			}

		} else if ( $time_to ) {

			if ( $current_time <= $time_to ) {
				$between_time_period = true;
			}

		}

		if ( 'hide' === $type ) {
			return ! $between_time_period;
		} else {
			return $between_time_period;
		}

	}

	/**
	 * Returns condition specific repeater controls
	 */
	public function get_custom_controls() {
		return array(
			'time_from' => array(
				'label'       => __( 'Time From', 'jet-engine' ),
				'description' => __( 'If set the element will be visible starting from this time of day. Set time in 24 hour time format (14:00).', 'jet-engine' ),
				'type'        => 'text',
				'default'     => '',
				'dynamic'     => array(
					'active' => true,
				),
			),
			'time_to' => array(
				'label'       => __( 'Time To', 'jet-engine' ),
				'description' => __( 'If set the element will be visible until this time of day. Set time in 24 hour time format (23:00).', 'jet-engine' ),
				'type'        => 'text',
				'default'     => '',
				'dynamic'     => array(
					'active' => true,
				),
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
	$manager->register_condition( new Time_Period() );
} );
