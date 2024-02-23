<?php
/**
 * Register Advanced date meta field type
 */

class Jet_Engine_Advanced_Date_Field_Rest_API extends Jet_Engine_Advanced_Date_Field_Data {

	public $field_type;

	/**
	 * Constructor for the class
	 */
	public function __construct( $field_type ) {

		$this->field_type = $field_type;

		add_filter( 
			'jet-engine/meta-boxes/rest-api/fields/field-type', 
			array( $this, 'prepare_rest_api_field_type' ), 
			10, 2
		);

		add_filter(
			'jet-engine/meta-boxes/rest-api/fields/schema',
			array( $this, 'prepare_rest_api_schema' ),
			10, 3
		);

	}

	/**
	 * Adjust field type for registering advanced date field in Rest API
	 * 
	 * @param  [type] $type  [description]
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function prepare_rest_api_field_type( $type, $field ) {

		if ( $this->is_advanced_date_field( $field ) ) {
			$type = 'object';
		}

		return $type;

	}

	/**
	 * Setup advanced date field schema for rest API
	 * 
	 * @param  [type] $schema     [description]
	 * @param  [type] $field_type [description]
	 * @param  [type] $field      [description]
	 * @return [type]             [description]
	 */
	public function prepare_rest_api_schema( $schema, $field_type, $field ) {

		if ( ! $this->is_advanced_date_field( $field ) ) {
			return $schema;
		}

		$schema = array( 
			'type'             => 'object',
			'properties'       => array(
				'rrule' => array( 
					'type' => 'string'
				),
				'dates' => array( 
					'type'  => 'array',
					'items' => array(
						'type' => 'object',
						'properties' => array(
							'start' => array( 'type' => 'string' ),
							'end'   => array( 'type' => 'string' ),
						),
					),
				),
			),
			'prepare_callback' => function( $value, $request, $args ) {
				
				global $post;

				$result = array( 'rrule' => '', 'dates' => [] );

				if ( ! $post ) {
					return $result;
				}

				$post_id = $post->ID;
				$field   = $args['name'];
				$config  = $this->get_field_config( $post_id, $field, true );

				$result['rrule'] = $this->generate_rrule_from_config( $config );
				$result['dates'] = $this->get_next_dates( $post_id, $field );

				return $result;
			}
		);

		return $schema;
	}

	public function get_next_dates( $post_id, $field ) {

		$dates     = $this->get_dates( $post_id, $field );
		$end_dates = $this->get_end_dates( $post_id, $field );
		$result    = [];

		if ( empty( $dates ) ) {
			return $result;
		}

		$format = apply_filters( 'jet-engine/calendar/advanced-date/rest-api-date-format', false );
		$count  = 10;
		$now    = time();

		foreach ( $dates as $index => $date ) {

			if ( $date < $now ) {
				continue;
			}

			$item = [];

			$item['start'] = ( false !== $format ) ? date( $format, $date ) : $date;

			if ( ! empty( $end_dates ) && ! empty( $end_dates[ $index ] ) ) {
				$item['end'] = ( false !== $format ) ? date( $format, $end_dates[ $index ] ) : $end_dates[ $index ];
			}

			$result[] = $item;

			if ( $count === count( $result ) ) {
				break;
			}

		}

		return $result;

	}

	public function generate_rrule_from_config( $config ) {

		$result = [];

		if ( ! $config ) {
			return null;
		}

		$result[] = 'DTSTART:' . $this->prepare_date_for_rrule( $config->date );

		if ( $config && ! empty( $config->is_end_date ) && $config->end_date ) {
			$result[] = 'DTEND:' . $this->prepare_date_for_rrule( $config->end_date );
		}

		if ( ! empty( $config->is_recurring ) ) {

			if ( ! class_exists( 'Jet_Engine_Advanced_Date_Recurring_Dates' ) ) {
				require_once jet_engine()->plugin_path( 'includes/modules/calendar/advanced-date-field/recurring-dates.php' );
			}

			$recurring_dates = new Jet_Engine_Advanced_Date_Recurring_Dates( (array) $config );

			$rrule = [];

			$rrule[] = 'FREQ=' . strtoupper( $config->recurring );
			$rrule[] = 'INTERVAL=' . $config->recurring_period;

			switch ( $config->recurring ) {

				case 'weekly':

					$days = [];

					foreach ( $config->week_days as $day ) {
						$days[] = $recurring_dates->get_shorten_weekday( $day );
					}

					$rrule[] = 'BYDAY=' . implode( ',', $days );

					break;

				case 'monthly':
				case 'yearly':

					$days = [];

					if ( 'day' === $config->month_day_type_value ) {
						for ( $i = 1; $i <= 7; $i++ ) {
							$days[] = $recurring_dates->get_shorten_weekday( $i );
						}
					} else {
						$days[] = $recurring_dates->get_shorten_weekday( $config->month_day_type_value );
					}

					if ( 'on_day_type' === $config->monthly_type ) {
						$rrule[] = 'BYSETPOS=' . $recurring_dates->date_type_to_num( $config->month_day_type ) . ';BYDAY=' . implode( ',',  $days );
					} else {
						$rrule[] = 'BYMONTHDAY=' . $config->month_day;
					}

					if ( 'yearly' === $config->recurring ) {
						$rrule[] = 'BYMONTH=' . $config->month;
					}

					break;

				
			}
			
			if ( 'on_date' === $config->end ) {
				$rrule[] = 'UNTIL:' . $this->prepare_date_for_rrule( $config->end_after_date );
			} else {
				$rrule[] = 'COUNT:' . $config->end_after;
			}

			$result[] = 'RRULE:' . implode( ';', $rrule );
		}

		return implode( ';', $result );

	}

	public function prepare_date_for_rrule( $date ) {
		return date( 'Ymd', strtotime( $date) ) . 'T000000Z';
	}

}
