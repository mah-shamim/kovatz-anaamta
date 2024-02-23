<?php
namespace Jet_Engine\Modules\Maps_Listings\Geosearch\Query;

class Terms extends Base {

	public $query_type = 'terms';

	public function __construct() {

		parent::__construct();
		add_action( 'terms_clauses', array( $this, 'update_terms_clauses' ), 10, 3 );

	}

	public function update_terms_clauses( $clauses, $taxonomies, $args ) {

		if ( empty( $args['geo_query'] ) ) {
			return $clauses;
		}

		$geo_query = $args['geo_query'];

		global $wpdb;

		$clauses['join'] .= " ";
		$clauses['join'] .= "INNER JOIN $wpdb->termmeta AS geo_query_lat ON ( t.term_id = geo_query_lat.term_id ) ";
		$clauses['join'] .= "INNER JOIN $wpdb->termmeta AS geo_query_lng ON ( t.term_id = geo_query_lng.term_id ) ";

		$lat_field = 'latitude';
		if ( ! empty( $geo_query['lat_field'] ) ) {
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

		$haversine = $this->haversine_term( $geo_query );
		$new_sql   = " AND ( geo_query_lat.meta_key = %s AND geo_query_lng.meta_key = %s AND " . $haversine . " <= %f )";
		
		$clauses['where'] .= $wpdb->prepare( $new_sql, $lat_field, $lng_field, $distance );

		$orderby = ! empty( $args['orderby'] ) ? $args['orderby'] : 'name';

		if ( 'distance' === $orderby ) {
			
			$clauses['fields'] .= ", $haversine AS $this->distance_term";
			$clauses['orderby'] = "ORDER BY $this->distance_term";

		}

		return $clauses;

	}

}
