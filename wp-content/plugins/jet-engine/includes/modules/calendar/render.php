<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Jet_Listing_Render_Calendar' ) ) {

	class Jet_Listing_Render_Calendar extends Jet_Engine_Render_Listing_Grid {

		public $is_first        = false;
		public $data            = false;
		public $first_day       = false;
		public $last_day        = false;
		public $multiday_events = array();
		public $posts_cache     = array();
		public $start_from      = false;

		public $prev_month_posts = array();
		public $next_month_posts = array();

		public $query_instance = null;

		public function get_name() {
			return 'jet-listing-calendar';
		}

		public function default_settings() {
			return array(
				'lisitng_id'               => '',
				'group_by'                 => 'post_date',
				'group_by_key'             => '',
				'allow_multiday'           => '',
				'end_date_key'             => '',
				'week_days_format'         => 'short',
				'custom_start_from'        => '',
				'start_from_month'         => date( 'F' ),
				'start_from_year'          => date( 'Y' ),
				'show_posts_nearby_months' => 'yes',
				'hide_past_events'         => '',
				'posts_query'              => array(),
				'meta_query_relation'      => 'AND',
				'tax_query_relation'       => 'AND',
				'hide_widget_if'           => '',
				'caption_layout'           => 'layout-1',
				'use_custom_post_types'    => '',
				'custom_post_types'        => array(),
				'custom_query'             => false,
				'custom_query_id'          => null,
				'_element_id'              => '',
			);
		}

		/**
		 * Get posts
		 *
		 * @param  array $settings
		 * @return array
		 */
		public function get_posts( $settings ) {

			add_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'add_calendar_query' ) );
			$args  = $this->build_posts_query_args_array( $settings );
			remove_filter( 'jet-engine/listing/grid/posts-query-args', array( $this, 'add_calendar_query' ) );

			$query = new \WP_Query( $args );

			return $query->posts;
		}

		public function render_posts() {

			add_action( 'jet-engine/query-builder/listings/on-query', array( $this, 'add_date_args_to_custom_query' ), 9 );
			parent::render_posts();
			remove_action( 'jet-engine/query-builder/listings/on-query', array( $this, 'add_date_args_to_custom_query' ), 9 );
			
			wp_cache_delete( 'jet_engine_calendar_requested_dates' );
		}

		public function add_date_args_to_custom_query( $query ) {
			$this->query_instance = $query;
			$query->final_query = $this->add_calendar_query( $query->final_query );

			// Reset query if it was stored before.
			$query->reset_query();
		}

		/**
		 * Prepare date query
		 *
		 * @return array
		 */
		public function add_calendar_query( $args ) {

			$settings = $this->get_settings();
			$group_by = $settings['group_by'];
			$args     = apply_filters( 'jet-engine/listing/calendar/query', $args, $group_by, $this );

			return $args;

		}

		public function get_date_period_for_query( $settings ) {

			$calendar_month = $this->get_current_month();

			$start = $calendar_month;
			$end   = $this->get_current_month( true );

			$show_posts_nearby_months = isset( $settings['show_posts_nearby_months'] ) ? filter_var( $settings['show_posts_nearby_months'], FILTER_VALIDATE_BOOLEAN ) : true;
			$hide_past_events         = isset( $settings['hide_past_events'] ) ? filter_var( $settings['hide_past_events'], FILTER_VALIDATE_BOOLEAN ) : false;

			if ( $show_posts_nearby_months ) {
				$week_begins = (int) get_option( 'start_of_week' );
				$first_day   = date( 'w', $start );
				$prev_days   = $first_day - $week_begins;
				$prev_days   = ( 0 > $prev_days ) ? 7 - abs( $prev_days ) : $prev_days;
				$next_days   = 7 - ( ( $prev_days + $this->get_days_num() ) % 7 );

				if ( $prev_days ) {
					$start = $start - $prev_days * 24 * 60 * 60;
				}

				if ( $next_days ) {
					$end = $end + $next_days * 24 * 60 * 60;
				}
			}

			if ( $hide_past_events ) {
				$today = strtotime( date_i18n( 'Y-m-d' ) );

				if ( $today > $start ) {
					$start = $today;
				}
			}

			$result = array(
				'start' => $start,
				'end'   => $end,
			);

			wp_cache_set( 'jet_engine_calendar_requested_dates', $result );

			return $result;
		}

		/**
		 * Prepare posts for calendar
		 *
		 * @since 3.3.0 added recurring dates support
		 *
		 * @param array $query
		 * @param array $settings
		 * @param array $month
		 * @return array
		 */
		public function prepare_posts_for_calendar( $query, $settings, $month ) {

			$prepared_posts = array();
			$group_by       = $settings['group_by'];
			$key            = false;

			if ( empty( $query ) ) {
				return $prepared_posts;
			}

			foreach ( $query as $post ) {

				switch ( $group_by ) {

					case 'post_date':
						$keys = strtotime( $post->post_date );
						break;

					case 'post_mod':
						$keys = strtotime( $post->post_modified );
						break;

					case 'meta_date':

						$meta_key = esc_attr( $settings['group_by_key'] );
						$multiday = isset( $settings['allow_multiday'] ) ? $settings['allow_multiday'] : '';
						$end_key  = isset( $settings['end_date_key'] ) ? $settings['end_date_key'] : false;
						
						if ( ! $end_key ) {
							$end_key = Jet_Engine_Advanced_Date_Field::instance()->data->end_date_field_name(
								$meta_key 
							);
						}

						if ( 'WP_Post' === get_class( $post ) ) {

							$keys = Jet_Engine_Advanced_Date_Field::instance()->data->get_dates( 
								$post->ID, $meta_key 
							);

							$end_dates = get_post_meta( $post->ID, $end_key, false );

						} else {
							$keys = $meta_key ? jet_engine()->listings->data->get_meta( $meta_key, $post ) : false;
							$end_dates = $end_key ? jet_engine()->listings->data->get_meta( $end_key, $post ) : false;
						}

						// Try to get data from object if returned empty val
						if ( null === $keys || empty( $keys ) ) {
							$keys = jet_engine()->listings->data->get_prop( $meta_key, $post );
						}

						if ( $end_key && null === $end_dates ) {
							$end_dates = jet_engine()->listings->data->get_prop( $end_key, $post );
						}

						if ( ! is_array( $keys ) ) {
							$keys = [ $keys ];
						}

						if ( $end_dates && ! is_array( $keys ) ) {
							$end_dates = [ $end_dates ];
						}

						$calendar_period = $this->get_date_period_for_query( $settings );

						if ( ! empty( $keys ) && ! empty( $end_dates ) && $multiday ) {

							foreach ( $keys as $index => $key ) {

								$end_date = ! empty( $end_dates[ $index ] ) ? $end_dates[ $index ] : false;

								if ( ! Jet_Engine_Tools::is_valid_timestamp( $key ) 
									|| ! Jet_Engine_Tools::is_valid_timestamp( $end_date ) ) {
									continue;
								}

								/*
								 * $days = absint( $end_date ) - absint( $key );
								 * This code changed on the following code to correctly get the days value
								 * if dates contain time value.
								 * Ex: 01.01.2023 15:00 and 04.01.2023 11:00.
								 */
								$days = absint( strtotime( date( 'Y-m-d', $end_date ) ) ) - absint( strtotime( date( 'Y-m-d', $key ) ) );
								$days = $days / ( 24 * 60 * 60 );

								for ( $i = 1; $i <= $days; $i++ ) {
									
									$day = strtotime( date( 'Y-m-d', $key ) . '+ ' . $i . ' days' );

									if ( $day < $calendar_period['start'] || $day > $calendar_period['end'] ) {
										continue;
									}

									$j = absint( date( 'j', $day ) );

									if ( $day < $month['start'] ) {

										if ( empty( $this->prev_month_posts[ $j ] ) ) {
											$this->prev_month_posts[ $j ] = array( $post );
										} else {
											$this->prev_month_posts[ $j ][] = $post;
										}

										continue;
									}

									if ( $day > $month['end'] ) {

										if ( empty( $this->next_month_posts[ $j ] ) ) {
											$this->next_month_posts[ $j ] = array( $post );
										} else {
											$this->next_month_posts[ $j ][] = $post;
										}

										continue;
									}

									if ( empty( $this->multiday_events[ $j ] ) ) {
										$this->multiday_events[ $j ] = array( $post );
									} else {
										$this->multiday_events[ $j ][] = $post;
									}

									$this->posts_cache[ jet_engine()->listings->data->get_current_object_id( $post ) ] = false;
								}

								if ( $key < $calendar_period['start'] ) {
									$keys[ $index ] = false;
								}

							}

						}

						// Filter `$keys` by `$calendar_period`
						$keys = array_filter( $keys, function ( $key ) use ( $calendar_period ) {
							return $key >= $calendar_period['start'] && $key <= $calendar_period['end'];
						} );

						break;

					default:

						/**
						 * Should return timestamp of required month day
						 * @var int
						 */
						$keys = apply_filters( 
							'jet-engine/listing/calendar/date-key', 
							false, $post, $group_by, $this 
						);
						break;

				}

				if ( ! is_array( $keys ) ) {
					$keys = [ $keys ];
				}

				foreach ( $keys as $key ) {

					if ( is_numeric( $key ) ) {

						$key     = date( 'j-n', $key );
						$item_id = jet_engine()->listings->data->get_current_object_id( $post );

						if ( isset( $prepared_posts[ $key ] ) ) {
							$prepared_posts[ $key ][ $item_id ] = $post;
						} else {
							$prepared_posts[ $key ] = array( $item_id => $post );
						}

					}

				}

			}

			return $prepared_posts;

		}

		/**
		 * Returns current month
		 *
		 * @param  bool $last_day
		 * @return bool|false|int
		 */
		public function get_current_month( $last_day = false ) {

			if ( false !== $this->first_day && ! $last_day ) {
				return $this->first_day;
			}

			if ( false !== $this->last_day && $last_day ) {
				return $this->last_day;
			}

			if ( isset( $_REQUEST['month'] ) ) {
				$month = date( '1 F Y', strtotime( $_REQUEST['month'] ) );
			} elseif ( $this->start_from ) {
				$month = date( '1 F Y', strtotime( $this->start_from ) );
			} else {
				$month = date_i18n( 'Y-m-1' );
			}

			$month = strtotime( $month );

			if ( ! $last_day ) {
				$this->first_day = $month;
				return $this->first_day;
			} else {
				$this->last_day = strtotime( date( 'Y-m-t 23:59:59', $month ) );
				return $this->last_day;
			}

		}

		/**
		 * Get days number for passed month
		 *
		 * @return false|string
		 */
		public function get_days_num() {
			return date( 't', $this->get_current_month() );
		}

		/**
		 * Render posts template.
		 * Moved to separate function to be rewritten by other layouts
		 *
		 * @param  array  $query    Query array.
		 * @param  array  $settings Settings array.
		 * @return void
		 */
		public function posts_template( $query, $settings ) {

			$base_class       = $this->get_name();
			$current_month    = $this->get_current_month();
			$month            = array(
				'start' => $current_month,
				'end'   => $this->get_current_month( true ),
			);
			$prepared_posts   = $this->prepare_posts_for_calendar( $query, $settings, $month );
			$days_num         = $this->get_days_num();
			$week_begins      = (int) get_option( 'start_of_week' );
			$first_week       = true;
			$human_read_month = date( 'F Y', $current_month );
			$first_day        = date( 'w', $current_month );
			$inc              = 0;
			$pad              = $first_day - $week_begins;
			$prev_month       = strtotime( $human_read_month . ' - 1 month' );
			$human_read_prev  = date( 'F Y', $prev_month );
			$human_read_next  = date( 'F Y', strtotime( $human_read_month . ' + 1 month' ) );
			$prev_month       = date( 't', $prev_month );
			$days_format      = isset( $settings['week_days_format'] ) ? $settings['week_days_format'] : 'short';
			$multiday         = isset( $settings['allow_multiday'] ) ? $settings['allow_multiday'] : '';
			$end_date_key     = isset( $settings['end_date_key'] ) ? $settings['end_date_key'] : false;

			if ( 0 > $pad ) {
				$pad = 7 - abs( $pad );
			}

			$data_settings = array(
				'lisitng_id'               => isset( $settings['lisitng_id'] ) ? $settings['lisitng_id'] : false,
				'week_days_format'         => $days_format,
				'allow_multiday'           => $multiday,
				'end_date_key'             => $end_date_key,
				'group_by'                 => isset( $settings['group_by'] ) ? $settings['group_by'] : false,
				'group_by_key'             => isset( $settings['group_by_key'] ) ? $settings['group_by_key'] : false,
				'posts_query'              => isset( $settings['posts_query'] ) ? $settings['posts_query'] : array(),
				'meta_query_relation'      => isset( $settings['meta_query_relation'] ) ? $settings['meta_query_relation'] : false,
				'tax_query_relation'       => isset( $settings['tax_query_relation'] ) ? $settings['tax_query_relation'] : false,
				'hide_widget_if'           => isset( $settings['hide_widget_if'] ) ? $settings['hide_widget_if'] : false,
				'caption_layout'           => isset( $settings['caption_layout'] ) ? $settings['caption_layout'] : 'layout-1',
				'show_posts_nearby_months' => isset( $settings['show_posts_nearby_months'] ) ? $settings['show_posts_nearby_months'] : true,
				'hide_past_events'         => isset( $settings['hide_past_events'] ) ? $settings['hide_past_events'] : false,
				'use_custom_post_types'    => isset( $settings['use_custom_post_types'] ) ? $settings['use_custom_post_types'] : false,
				'custom_post_types'        => isset( $settings['custom_post_types'] ) ? $settings['custom_post_types'] : array(),
				'custom_query'             => isset( $settings['custom_query'] ) ? $settings['custom_query'] : false,
				'custom_query_id'          => isset( $settings['custom_query_id'] ) ? $settings['custom_query_id'] : false,
				'_element_id'              => isset( $settings['_element_id'] ) ? $settings['_element_id'] : '',
			);

			$data_settings = htmlspecialchars( json_encode( $data_settings ) );

			$container_classes = [
				'jet-calendar',
				$base_class,
				'jet-listing-grid--' . $settings['lisitng_id'], // for inline CSS consistency between differen views and listing widgets
			];

			printf(
				'<div class="%1$s" data-settings="%2$s" data-post="%3$d" data-listing-source="%4$s" data-query-id="%5$s">',
				implode( ' ', $container_classes ),
				$data_settings,
				get_the_ID(),
				jet_engine()->listings->data->get_listing_source(),
				$this->listing_query_id
			);

			do_action( 'jet-engine/listing/grid/before', $this );

			do_action( 'jet-engine/listing/calendar/before', $settings, $this );

			echo '<table class="jet-calendar-grid" >';

			include jet_engine()->modules->get_module( 'calendar' )->get_template( 'header.php' );

			echo '<tbody>';

			jet_engine()->frontend->set_listing( $settings['lisitng_id'] );

			$fallback = 1;
			$today_date        = date_i18n( 'j-n-Y' );
			$current_year      = (int) date( 'Y', $current_month );
			$current_month_num = (int) date( 'n', $current_month );
			$prev_month_num    = $current_month_num - 1;
			$prev_month_num    = ( 0 >= $prev_month_num ) ? $prev_month_num + 12 : $prev_month_num;;
			$next_month_num    = $current_month_num + 1;
			$next_month_num    = ( 12 < $next_month_num ) ? $next_month_num - 12 : $next_month_num;

			// Add last days of previous month
			if ( 0 < $pad ) {

				for ( $i = 0; $i < $pad; $i++ ) {

					include jet_engine()->modules->get_module( 'calendar' )->get_template( 'week-start.php' );

					$num                     = $prev_month - $pad + $i + 1;
					$key                     = $num . '-' . $prev_month_num;
					$posts                   = ! empty( $prepared_posts[ $key ] ) ? $prepared_posts[ $key ] : array();
					$padclass                = ' day-pad';
					$current_multiday_events = ! empty( $this->prev_month_posts[ $num ] ) ? $this->prev_month_posts[ $num ] : array();

					include jet_engine()->modules->get_module( 'calendar' )->get_template( 'date.php' );
					include jet_engine()->modules->get_module( 'calendar' )->get_template( 'week-end.php' );

					$inc++;
				}

			}

			// Current month
			for ( $i = 1; $i <= $days_num; $i++ ) {

				include jet_engine()->modules->get_module( 'calendar' )->get_template( 'week-start.php' );

				$num      = $i;
				$key      = $num . '-' . $current_month_num;
				$posts    = ! empty( $prepared_posts[ $key ] ) ? $prepared_posts[ $key ] : array();
				$padclass = ! empty( $posts ) ? ' has-events' : '';

				$current_multiday_events = array();

				if ( ! empty( $this->multiday_events[ $i ] ) ) {
					$current_multiday_events = $this->multiday_events[ $i ];

					if ( ! $padclass ) {
						$padclass = ' has-events';
					}

				}

				$current_date = $key . '-' . $current_year;

				if ( $current_date === $today_date ) {
					$padclass .= ' current-day';
				}

				include jet_engine()->modules->get_module( 'calendar' )->get_template( 'date.php' );
				include jet_engine()->modules->get_module( 'calendar' )->get_template( 'week-end.php' );

				$inc++;

			}

			// Add first days of next month
			$days_left = 7 - ( $inc % 7 );

			if ( 0 < $days_left ) {

				$fallback = $days_num;

				for ( $i = 1; $i <= $days_left; $i++ ) {

					include jet_engine()->modules->get_module( 'calendar' )->get_template( 'week-start.php' );

					$num                     = $i;
					$key                     = $num . '-' . $next_month_num;
					$posts                   = ! empty( $prepared_posts[ $key ] ) ? $prepared_posts[ $key ] : array();
					$padclass                = ' day-pad';
					$current_multiday_events = ! empty( $this->next_month_posts[ $num ] ) ? $this->next_month_posts[ $num ] : array();

					include jet_engine()->modules->get_module( 'calendar' )->get_template( 'date.php' );
					include jet_engine()->modules->get_module( 'calendar' )->get_template( 'week-end.php' );

					$inc++;

				}

			}

			$this->multiday_events   = array();
			$this->posts_cache       = array();
			$current_multiday_events = array();

			jet_engine()->frontend->reset_listing();

			echo '</tbody>';

			echo '</table>';

			do_action( 'jet-engine/listing/grid/after', $this );

			do_action( 'jet-engine/listing/calendar/after', $settings, $this );

			echo '</div>';

		}

		public function maybe_set_listing( $listing_id ) {

			if ( null === jet_engine()->frontend->get_listing_id() ) {
				jet_engine()->frontend->set_listing( $listing_id );
			}

		}

	}

}
