<?php
/**
 * Register Advanced date meta field type
 */

class Jet_Engine_Advanced_Date_Recurring_Dates {

	private $data = [];

	// English only weekdays to use for new dates generation
	private $weekdays = [
		1 => 'Monday', 
		2 => 'Tuesday', 
		3 => 'Wednesday', 
		4 => 'Thursday', 
		5 => 'Friday', 
		6 => 'Saturday', 
		7 => 'Sunday',
	];

	// English only months
	private $months = [
		1 => 'January', 
		2 => 'February', 
		3 => 'March', 
		4 => 'April', 
		5 => 'May', 
		6 => 'June', 
		7 => 'July', 
		8 => 'August', 
		9 => 'September', 
		10 => 'October', 
		11 => 'November', 
		12 => 'December' 
	];

	public function __construct( $data = [] ) {
		$this->data = $data;
	}

	/**
	 * Generate recurring dates list by given config
	 * 
	 * @return [type] [description]
	 */
	public function generate() {

		$dates = [];

		while ( $this->has_next_date( $dates ) ) {
			switch ( $this->data['recurring'] ) {
				case 'daily':
					$next_date = $this->generate_next_daily_recurring( $dates );
					break;
				
				case 'weekly':
					$next_date = $this->generate_next_weekly_recurring( $dates );
					break;

				case 'monthly':
					$next_date = $this->generate_next_monthly_recurring( $dates );
					break;

				case 'yearly':
					$next_date = $this->generate_next_yearly_recurring( $dates );
					break;
			}

			// if ends on date we need to do one more check to ensure new date also fits range
			if ( 'on_date' === $this->data['end'] && ! $this->is_date_in_range( $next_date ) ) {
				break;
			}

			$dates[] = $next_date;

		}

		return array_filter( $dates );

	}

	/**
	 * Check if need to generate one more date
	 * 
	 * @param  array   $dates [description]
	 * @return boolean        [description]
	 */
	public function has_next_date( $dates = [] ) {

		$res = false;

		switch ( $this->data['end'] ) {
			
			case 'after':

				$num = absint( $this->data['end_after'] );

				if ( count( $dates ) < ( $num - 1 ) ) {
					$res = true;
				}

				break;
			
			case 'on_date':
				$res = $this->is_date_in_range( $this->get_last_date( $dates ) );
				break;
		}

		return $res;

	}

	/**
	 * Check if given date is before end date of recurring range
	 * 
	 * @param  [type]  $date [description]
	 * @return boolean       [description]
	 */
	public function is_date_in_range( $date ) {
		
		$end_on_date = strtotime( $this->data['end_after_date'] );
		
		if ( $end_on_date >= $date ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Get last date from already generated
	 * 
	 * @param  array  $dates [description]
	 * @return [type]        [description]
	 */
	public function get_last_date( $dates = [] ) {
		$dates = array_filter( $dates );
		return ! empty( $dates ) ? end( $dates ) : strtotime( $this->data['date'] );
	}

	/**
	 * Get recurring period (each N days, weeks, months, years)
	 * 
	 * @return [type] [description]
	 */
	public function get_recurring_period() {
		return ! empty( $this->data['recurring_period'] ) ? absint( $this->data['recurring_period'] ) : 1;
	}

	/**
	 * Generate next date for daily recurrings
	 * 
	 * @param  array  $dates [description]
	 * @return [type]        [description]
	 */
	public function generate_next_daily_recurring( $dates = [] ) {
		$last_date = $this->get_last_date( $dates );
		$period    = $this->get_recurring_period();
		return $last_date + $period * DAY_IN_SECONDS;
	}

	/**
	 * Generate next date for weekly recurrings
	 * 
	 * @param  array  $dates [description]
	 * @return [type]        [description]
	 */
	public function generate_next_weekly_recurring( $dates = [] ) {

		$last_date = $this->get_last_date( $dates );
		$period    = $this->get_recurring_period();

		$weekdays = ! empty( $this->data['week_days'] ) ? $this->data['week_days'] : [];

		if ( empty( $this->data['week_days'] ) ) {
			return false;
		}

		$last_date_dow = date( 'N', $last_date );
		$new_date = false;

		if ( in_array( $last_date_dow, $weekdays ) ) {
			$d_index = array_search( $last_date_dow, $weekdays );
			$d_index++;
		} else {
			$d_index = 0;
			foreach ( $weekdays as $index => $dow ) {
				if ( absint( $dow ) > absint( $last_date_dow ) ) {
					$d_index = $index;
					break;
				}
			}
		}

		$next_day  = isset( $weekdays[ $d_index ] ) ? $weekdays[ $d_index ] : $weekdays[0];
		$diff      = $next_day - $last_date_dow;
		$next_week = false;

		if ( 0 >= $diff ) {
			$diff = 7 - $last_date_dow + $next_day;
			$next_week = true;
		}

		$new_date = $last_date + $diff * DAY_IN_SECONDS;

		if ( $next_week ) {
			$new_date += 7 * DAY_IN_SECONDS * ( $period - 1 );
		}

		return $new_date;

	}

	/**
	 * Generate next date for monthly recurrings
	 * 
	 * @param  array  $dates [description]
	 * @return [type]        [description]
	 */
	public function generate_next_monthly_recurring( $dates = [] ) {

		$last_date = $this->get_last_date( $dates );
		$period    = $this->get_recurring_period();
		$new_date  = false;
		$dt        = new DateTime( '@' . $last_date );

		switch ( $this->data['monthly_type'] ) {
			
			case 'on_day_type':

				for ( $i = 1; $i <= $period; $i++ ) { 
					$dt->modify( sprintf( 
						'%1$s %2$s of next month', 
						$this->get_day_type(), 
						$this->get_day_type_value() 
					) );
				}

				break;
			
			default:
				
				$day = ! empty( $this->data['month_day'] ) ? absint( $this->data['month_day'] ) : 1;

				for ( $i = 1; $i <= $period; $i++ ) { 
					$dt->modify( 'first day of next month' );
				}

				$total_days_num = absint( $dt->format( 't' ) );
				$day = ( $total_days_num > $day ) ? $day : $total_days_num;
				$dt->setDate( $dt->format( 'Y' ), $dt->format( 'n' ), $day );

				break;
		}

		$new_date = $dt->getTimestamp();

		return $new_date;
	}

	/**
	 * Generate next date for yearly recurrings
	 * 
	 * @param  array  $dates [description]
	 * @return [type]        [description]
	 */
	public function generate_next_yearly_recurring( $dates = [] ) {
		
		$last_date = $this->get_last_date( $dates );
		$period    = $this->get_recurring_period();
		$new_date  = false;
		$dt        = new DateTime( '@' . $last_date );
		$month     = ! empty( $this->data['month'] ) ? absint( $this->data['month'] ) : 1;
		$month     = ! empty( $this->months[ $month ] ) ? $this->months[ $month ] : 'January';

		switch ( $this->data['monthly_type'] ) {
			
			case 'on_day_type':

				for ( $i = 1; $i <= $period; $i++ ) { 

					$dt->modify( 'first day of next year' );

					$dt->modify( sprintf( 
						'%1$s %2$s of %3$s',
						 $this->get_day_type(),
						$this->get_day_type_value(),
						$month 
					) );

				}

				break;
			
			default:

				$day = ! empty( $this->data['month_day'] ) ? absint( $this->data['month_day'] ) : 1;
				$total_days_num = absint( date( 't', strtotime( '1st ' . $month . ' of next year' ) ) );
				$day = ( $total_days_num > $day ) ? $day : $total_days_num;
				$suffixes = [ 'th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th' ];
				
				if ( ( $day % 100 ) >= 11 && ( $day % 100 ) <= 13 ) {
					$suffix = 'th';
				} else {
					$suffix = $suffixes[ $day % 10 ];
				}
				

				for ( $i = 1; $i <= $period; $i++ ) { 
					$dt->modify( sprintf( 
						'%1$s%2$s %3$s, next year', 
						$day,
						$suffix,
						$month
					) );
				}

				break;
		}

		$new_date = $dt->getTimestamp();

		return $new_date;

	}

	/**
	 * Extract day type value from config
	 * 
	 * @return [type] [description]
	 */
	public function get_day_type_value() {
		
		$day_val = ! empty( $this->data['month_day_type_value'] ) ? $this->data['month_day_type_value'] : 1;

		if ( 'day' !== $day_val ) {
			$day_val = absint( $day_val );
			$day_val = ! empty( $this->weekdays[ $day_val ] ) ? $this->weekdays[ $day_val ] : 'Monday';
		}

		return $day_val;

	}

	/**
	 * Get shorten weekday name by number
	 * 
	 * @param  [type] $day_index [description]
	 * @return [type]            [description]
	 */
	public function get_shorten_weekday( $day_index ) {
		$day_val = ! empty( $this->weekdays[ $day_index ] ) ? $this->weekdays[ $day_index ] : 'Monday';
		return strtoupper( substr( $day_val, 0, 2 ) );
	}

	/**
	 * Return day type number by string name
	 * 
	 * @param  string $day_type [description]
	 * @return [type]           [description]
	 */
	public function date_type_to_num( $day_type = 'first' ) {
		switch( $day_type ) {
			case 'second':
				return 2;
			case 'third':
				return 3;
			case 'fourth':
				return 4;
			case 'last':
				return -1;
			default:
				return 1;
		}
	}

	/**
	 * Extract day type from config
	 * 
	 * @return [type] [description]
	 */
	public function get_day_type() {
		return ! empty( $this->data['month_day_type'] ) ? $this->data['month_day_type'] : 'first';
	}

}
