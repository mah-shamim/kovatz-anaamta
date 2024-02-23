<?php
/**
 * Register Advanced date meta field type
 */

class Jet_Engine_Advanced_Date_Field_Data {

	public $field_type;

	/**
	 * Constructor for the class
	 */
	public function __construct( $field_type ) {

		$this->field_type = $field_type;

		add_action(
			'cx_post_meta/before_save',
			array( $this, 'save_advanced_date_meta' ),
			10, 3
		);

		add_filter(
			'cx_post_meta/pre_get_meta',
			array( $this, 'get_advacned_meta_for_editor' ),
			10, 5
		);

	}

	/**
	 * Check if given meta field config is for advanced date field
	 * 
	 * @param  array   $field [description]
	 * @return boolean        [description]
	 */
	public function is_advanced_date_field( $field = [] ) {
		return ! empty( $field['custom_type'] ) && $this->field_type === $field['custom_type'];
	}

	/**
	 * Returns value of advanced date field adapted to use in WP editor
	 * 
	 * @param  [type] $res        [description]
	 * @param  [type] $post       [description]
	 * @param  [type] $field_name [description]
	 * @param  [type] $default    [description]
	 * @param  [type] $field      [description]
	 * @return [type]             [description]
	 */
	public function get_advacned_meta_for_editor( $res, $post, $field_name, $default, $field ) {
		
		if ( ! $this->is_advanced_date_field( $field ) ) {
			return $res;
		}

		return $this->get_field_config( $post->ID, $field_name );

	}

	/**
	 * Returns end date subfield name for given advanced date field
	 * 
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function end_date_field_name( $field ) {
		return $field . '__end_date';
	}

	/**
	 * Returns end dates subfield data for given advanced date field
	 * 
	 * @param  [type] $post_id [description]
	 * @param  [type] $field   [description]
	 * @return [type]          [description]
	 */
	public function get_end_dates( $post_id, $field ) {
		return get_post_meta( $post_id, $field . '__end_date', false );
	}

	/**
	 * Returns dates data for given advanced date field
	 * 
	 * @param  [type] $post_id [description]
	 * @param  [type] $field   [description]
	 * @return [type]          [description]
	 */
	public function get_dates( $post_id, $field ) {
		return get_post_meta( $post_id, $field, false );
	}

	/**
	 * Returns config subfield name for given advanced date field
	 * 
	 * @param  [type] $field [description]
	 * @return [type]        [description]
	 */
	public function config_field_name( $field ) {
		return $field . '__config';
	}

	/**
	 * Returns config subfield data for given advanced date field
	 * 
	 * @param  [type] $post_id [description]
	 * @param  [type] $field   [description]
	 * @return [type]          [description]
	 */
	public function get_field_config( $post_id, $field, $decode = false ) {
	
		$config = get_post_meta( $post_id, $this->config_field_name( $field ), true );

		if ( $decode ) {
			$config = json_decode( $config );
		}

		return $config;
	}

	/**
	 * Attach custom callback to save advanced date field if given saved meta box contain it
	 * 
	 * @param  [type] $post_id            [description]
	 * @param  [type] $post               [description]
	 * @param  [type] $post_meta_instance [description]
	 * @return [type]                     [description]
	 */
	public function save_advanced_date_meta( $post_id, $post, $post_meta_instance ) {
		
		// find and advanced date fields if needed
		foreach( $post_meta_instance->args['fields'] as $name => $field ) {

			if ( ! $this->is_advanced_date_field( $field ) ) {
				continue;
			}

			add_filter( 'cx_post_meta/pre_process_key/' . $name, [ $this, 'update_field' ], 10, 3 );

		}

	}

	/**
	 * Custom callback to update advanced date value. 
	 * Using of this callback prevents default meta data processing
	 * 
	 * @param  [type] $result     [description]
	 * @param  [type] $post_id    [description]
	 * @param  [type] $field_name [description]
	 * @return [type]             [description]
	 */
	public function update_field( $result, $post_id, $field_name ) {
		
		$update_field = ! empty( $_REQUEST[ $field_name ] ) ? $_REQUEST[ $field_name ] : [];

		delete_post_meta( $post_id, $field_name );
		delete_post_meta( $post_id, $this->end_date_field_name( $field_name ) );

		if ( empty( ! $update_field ) ) {
			update_post_meta( $post_id, $this->config_field_name( $field_name ), json_encode( $update_field ) );
			$this->add_field_data( $post_id, $field_name, $update_field );
		}

		return true;
	}

	/**
	 * Add actual advanced dates schedule
	 * 
	 * @param [type] $post_id    [description]
	 * @param [type] $field_name [description]
	 * @param [type] $data       [description]
	 */
	public function add_field_data( $post_id, $field_name, $data ) {

		$start_date = ! empty( $data['date'] ) ? strtotime( $data['date'] ) : false;

		if ( ! $start_date ) {
			return;
		}

		// Add first date
		add_post_meta( $post_id, $field_name, $start_date, false );

		$end_date = ! empty( $data['is_end_date'] ) && ! empty( $data['end_date'] ) ? strtotime( $data['end_date'] ) : false;

		if ( $end_date ) {
			// Add real first end date
			add_post_meta( $post_id, $this->end_date_field_name( $field_name ), $end_date, false );
		}

		// if not recurring - nothing to do more. If yes - iterate dates
		if ( empty( $data['is_recurring'] ) ) {
			return;
		}

		if ( ! class_exists( 'Jet_Engine_Advanced_Date_Recurring_Dates' ) ) {
			require_once jet_engine()->plugin_path( 'includes/modules/calendar/advanced-date-field/recurring-dates.php' );
		}

		$recurring_dates = new Jet_Engine_Advanced_Date_Recurring_Dates( $data );
		$dates           = $recurring_dates->generate();
		$end_dates       = [];

		/*
		error_log( 'Start dates:' );
		error_log( print_r( array_map( function( $date ) {
			return date( 'l, d F Y', $date );
		}, $dates ), true ) );
		*/

		if ( $end_date && ! empty( $dates ) ) {
			$diff      = $end_date - $start_date;
			$end_dates = array_map( function( $date ) use ( $diff ) {
				return $date + $diff;
			}, $dates );
		}

		/*
		if ( ! empty( $end_dates ) ) {
			error_log( 'End dates:' );
			error_log( print_r( array_map( function( $date ) {
				return date( 'l, d F Y', $date );
			}, $end_dates ), true ) );
		}
		*/

		foreach( $dates as $index => $date ) {
			add_post_meta( $post_id, $field_name, $date, false );
			if ( ! empty( $end_dates[ $index ] ) ) {
				add_post_meta( $post_id, $this->end_date_field_name( $field_name ), $end_dates[ $index ], false );
			}
		}

	}

}
