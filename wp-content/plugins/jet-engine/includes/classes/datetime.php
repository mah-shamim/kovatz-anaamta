<?php
/**
 * Datetime management class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Datetime management class
 */
class Jet_Engine_Datetime {

	/**
	 * Check if wp_date used instead fo date to process data
	 * 
	 * @return [type] [description]
	 */
	public function use_wp_date() {
		return apply_filters( 'jet-engine/datetime/use-wp-date', false );
	}

	/**
	 * Return human-readable localized date from timestamp
	 * 
	 * @param  [type] $format [description]
	 * @param  [type] $time   [description]
	 * @return [type]         [description]
	 */
	public function date( $format = null, $time = null ) {
		if ( $this->use_wp_date() ) {
			return wp_date( $format, $time );
		} else {
			return date_i18n( $format, $time );
		}
	}

	/**
	 * Convert string to time according timezone settings
	 * 
	 * @param  [type] $timestring [description]
	 * @return [type]             [description]
	 */
	public function to_time( $timestring ) {
		if ( $this->use_wp_date() ) {

			if ( empty( $timestring ) ) {
				return $timestring;
			}

			$date = new DateTime( $timestring, wp_timezone() );
			return $date->format( 'U' );
		} else {
			return strtotime( $timestring );
		}
	}

	/**
	 * Convert meta value date to timestamp according timezone settings
	 * 
	 * @param  [type] $timestamp [description]
	 * @param  [type] $raw_time  [description]
	 * @return [type]            [description]
	 */
	public function meta_to_time( $timestamp, $raw_time ) {
		return $this->to_time( $raw_time );
	}

	/**
	 * Convert meta value date to formatted date according timezone settings
	 * 
	 * @param  [type] $timestamp [description]
	 * @param  [type] $raw_time  [description]
	 * @return [type]            [description]
	 */
	public function meta_to_date( $formatted_date, $timestamp, $format ) {
		return $this->date( $format, $timestamp );
	}

	/**
	 * Convert meta fileds timestamp dates on save or get field values
	 * @return [type] [description]
	 */
	public function convert_meta_fields_dates() {
		
		add_filter( 'cx_post_meta/strtotime', array( $this, 'meta_to_time' ), 10, 2 );
		add_filter( 'cx_post_meta/date', array( $this, 'meta_to_date' ), 10, 3 );

		add_filter( 'cx_term_meta/strtotime', array( $this, 'meta_to_time' ), 10, 2 );
		add_filter( 'cx_term_meta/date', array( $this, 'meta_to_date' ), 10, 3 );

		add_filter( 'cx_user_meta/strtotime', array( $this, 'meta_to_time' ), 10, 2 );
		add_filter( 'cx_user_meta/date', array( $this, 'meta_to_date' ), 10, 3 );

		add_filter( 'jet-engine/custom-content-types/strtotime', array( $this, 'meta_to_time' ), 10, 2 );
		add_filter( 'jet-engine/custom-content-types/date', array( $this, 'meta_to_date' ), 10, 3 );

		add_filter( 'jet-engine/options-pages/strtotime', array( $this, 'meta_to_time' ), 10, 2 );
		add_filter( 'jet-engine/options-pages/date', array( $this, 'meta_to_date' ), 10, 3 );

	}

}
