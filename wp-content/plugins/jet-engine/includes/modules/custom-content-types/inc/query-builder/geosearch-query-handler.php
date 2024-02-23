<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Query_Builder;

use Jet_Engine\Modules\Maps_Listings\Geosearch\Query\Base;

class Geosearch_Query extends Base {

	public $query_type = 'custom-content-type';

	public function __construct() {

		parent::__construct();
		add_filter( 'jet-engine/custom-content-types/sql-query-parts', array( $this, 'update_query_clauses' ), 10, 4 );
		add_filter( 'jet-engine/custom-content-types/sql-count-query', array( $this, 'update_count_query_clauses' ), 10, 4 );

	}

	public function update_count_query_clauses( $query, $table, $args, $db ) {

		if ( empty( $db->query_object->final_query['geo_query'] ) ) {
			return $query;
		}

		global $wpdb;

		$geo_query = $db->query_object->final_query['geo_query'];

		$distance = 20;
		if ( isset( $geo_query['distance'] ) ) {
			$distance = $geo_query['distance'];
		}
		$distance = floatval( $distance );

		$haversine = $this->haversine_term( $geo_query );
		
		$query .= $wpdb->prepare( " AND $haversine <= %f", $distance );

		return $query;

	}

	public function update_query_clauses( $query, $table, $args, $db ) {

		if ( empty( $db->query_object->final_query['geo_query'] ) ) {
			return $query;
		}

		global $wpdb;

		$geo_query = $db->query_object->final_query['geo_query'];

		$distance = 20;
		if ( isset( $geo_query['distance'] ) ) {
			$distance = $geo_query['distance'];
		}
		$distance = floatval( $distance );

		$haversine = $this->haversine_term( $geo_query );
		
		$query['where'] .= $wpdb->prepare( " AND $haversine <= %f", $distance );

		return $query;

	}

	public function haversine_term( $geo_query ) {
		
		global $wpdb;
		$units = "miles";
		
		if ( !empty( $geo_query['units'] ) ) {
			$units = strtolower( $geo_query['units'] );
		}
		
		$radius = 3959;
		
		if ( in_array( $units, array( 'km', 'kilometers' ) ) ) {
			$radius = 6371;
		}
		
		$fields = explode( ',', $geo_query['raw_field'] );

		if ( 2 === count( $fields ) ) {
			$lat_field = trim( $fields[0] );
			$lng_field = trim( $fields[1] );
		} else {
			$lat_field = str_replace( '+', '_', $geo_query['raw_field'] ) . '_lat';
			$lng_field = str_replace( '+', '_', $geo_query['raw_field'] ) . '_lng';
		}

		$lat = 0;
		$lng = 0;
		
		if ( isset( $geo_query['latitude'] ) ) {
			$lat = $geo_query['latitude' ];
		}
		
		if ( isset( $geo_query['longitude'] ) ) {
			$lng = $geo_query['longitude'];
		}
		
		$haversine  = "( " . $radius . " * ";
		$haversine .=     "acos( cos( radians(%f) ) * cos( radians( " . $lat_field . " ) ) * ";
		$haversine .=     "cos( radians( " . $lng_field . " ) - radians(%f) ) + ";
		$haversine .=     "sin( radians(%f) ) * sin( radians( " . $lat_field . " ) ) ) ";
		$haversine .= ")";
		$haversine  = $wpdb->prepare( $haversine, array( $lat, $lng, $lat ) );
		
		return $haversine;
	}

}
