<?php
namespace Jet_Engine\Macros;

/**
 * Return timestamp by string.
 */
class Current_Date extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'current_date';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Current date', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_args() {
		return array(
			'return_date' => array(
				'label'   => __( 'Return', 'jet-engine' ),
				'type'    => 'select',
				'default' => 'day',
				'options' => array(
					'day' => __( 'Current day of month', 'jet-engine' ),
					'day_of_week' => __( 'Current day of week', 'jet-engine' ),
					'month' => __( 'Current month name', 'jet-engine' ),
					'year' => __( 'Current year in 4-digit format', 'jet-engine' ),
					'custom' => __( 'Custom format', 'jet-engine' ),
				),
			),
			'date_format' => array(
				'label'      => __( 'Format', 'jet-engine' ),
				'type'       => 'text',
				'default'    => 'F j, Y',
				'condition' => array(
					'return_date' => array( 'custom' ),
				),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$return = ! empty( $args['return_date'] ) ? $args['return_date'] : 'day';
		$format = false;

		switch ( $return ) {
			case 'day':
				$format = 'd';
				break;

			case 'day_of_week':
				$format = 'l';
				break;

			case 'month':
				$format = 'F';
				break;

			case 'year':
				$format = 'Y';
				break;

			case 'custom':
				$format = ! empty( $args['date_format'] ) ? $args['date_format'] : '';
				break;
		}

		if ( $format ) {
			return jet_engine_date( $format, time() );
		}

	}
}