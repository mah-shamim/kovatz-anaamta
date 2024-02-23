<?php
namespace Jet_Engine\Modules\Maps_Listings\Geosearch\Query;

class Posts extends Base {

	public $query_type = 'posts';

	public function __construct() {

		parent::__construct();

		/**
		 * Temporary locked. Coud be added in the future
		 * 
		 * add_filter( 'jet-engine/listing/data/post-fields', array( $this, 'add_distance_field' ) );
		 */

		add_filter( 'posts_fields' , array( $this, 'posts_fields'  ), 10, 2 );
		add_filter( 'posts_join'   , array( $this, 'posts_join'    ), 10, 2 );
		add_filter( 'posts_where'  , array( $this, 'posts_where'   ), 10, 2 );
		add_filter( 'posts_orderby', array( $this, 'posts_orderby' ), 10, 2 );

	}

	public function add_distance_field( $fields ) {
		$fields[ $this->distance_term ] = __( 'Distance (for Geo queries)', 'jet-engine' );
		return $fields;
	}

	// add a calculated "distance" parameter to the sql query, using a haversine formula
	public function posts_fields( $sql, $query ) {
		
		global $wpdb;
		
		$geo_query = $query->get( 'geo_query' );
		
		if ( $geo_query ) {

			if ( $sql ) {
				$sql .= ', ';
			}

			$sql .= $this->haversine_term( $geo_query ) . " AS " . $this->distance_term;

		}

		return $sql;
	}

	public function posts_join( $sql, $query ) {
		
		global $wpdb;
		
		$geo_query = $query->get('geo_query');
		
		if ( $geo_query ) {

			if ( $sql ) {
				$sql .= ' ';
			}

			$sql .= "INNER JOIN $wpdb->postmeta AS geo_query_lat ON ( $wpdb->posts.ID = geo_query_lat.post_id ) ";
			$sql .= "INNER JOIN $wpdb->postmeta AS geo_query_lng ON ( $wpdb->posts.ID = geo_query_lng.post_id ) ";
		}

		return $sql;
	}

	// match on the right metafields, and filter by distance
	public function posts_where( $sql, $query ) {
		
		global $wpdb;
		
		$geo_query = $query->get( 'geo_query' );
		
		if ( $geo_query ) {
			$lat_field = 'latitude';
			if ( !empty( $geo_query['lat_field'] ) ) {
				$lat_field =  $geo_query['lat_field'];
			}
			$lng_field = 'longitude';
			if ( !empty( $geo_query['lng_field'] ) ) {
				$lng_field =  $geo_query['lng_field'];
			}
			$distance = 20;
			if ( isset( $geo_query['distance'] ) ) {
				$distance = $geo_query['distance'];
			}
			if ( $sql ) {
				$sql .= " AND ";
			}
			$haversine = $this->haversine_term( $geo_query );
			$new_sql = "( geo_query_lat.meta_key = %s AND geo_query_lng.meta_key = %s AND " . $haversine . " <= %f )";
			$sql .= $wpdb->prepare( $new_sql, $lat_field, $lng_field, $distance );
		}

		return $sql;

	}

	// handle ordering
	public function posts_orderby( $sql, $query ) {
	
		$geo_query = $query->get( 'geo_query' );
		
		if ( $geo_query ) {

			$orderby = $query->get('orderby');
			$order   = $query->get('order');

			if ( $orderby == 'distance' || ( is_array( $orderby ) && isset( $orderby['distance'] ) ) ) {

				if ( ! $order ) {
					$order = 'ASC';
				}

				$order = ( is_array( $orderby ) && ! empty( $orderby['distance'] ) ) ? $orderby['distance'] : $order;

				$distance_orderby = $this->distance_term . ' ' . $order;

				if ( is_array( $orderby ) && 1 < count( $orderby ) ) {
					$sql_array      = ! empty( $sql ) ? explode( ', ', $sql ) : array();
					$distance_index = array_search( 'distance', array_keys( $orderby ) );

					if ( 0 == $distance_index ) {
						array_unshift( $sql_array, $distance_orderby );
					} else {
						$sql_array = array_merge(
							array_slice( $sql_array, 0, $distance_index ),
							array( $distance_orderby ),
							array_slice( $sql_array, $distance_index, null )
						);
					}

					$sql = implode( ', ', $sql_array );

				} else {
					$sql = $distance_orderby;
				}
			}
		}

		return $sql;

	}

}

/*add_action( 'init', function() {

	$query = new WP_Query( array(
		'geo_query' => array(
			'lat_field' => '_latitude',  // this is the name of the meta field storing latitude
			'lng_field' => '_longitude', // this is the name of the meta field storing longitude 
			'latitude'  => 44.485261,    // this is the latitude of the point we are getting distance from
			'longitude' => -73.218952,   // this is the longitude of the point we are getting distance from
			'distance'  => 20,           // this is the maximum distance to search
			'units'     => 'miles'       // this supports options: miles, mi, kilometers, km
		),
		'orderby' => 'distance', // this tells WP Query to sort by distance
		'order'   => 'ASC'
	) );

}, 99 );*/