<?php
namespace Jet_Engine\Modules\Maps_Listings\Geosearch\Query;

class Users extends Base {

	public $query_type = 'users';

	public function __construct() {

		parent::__construct();
		add_action( 'pre_user_query', array( $this, 'update_users_query' ) );

	}

	public function update_users_query( $query ) {
		
		if ( empty( $query->query_vars['geo_query'] ) ) {
			return;
		}

		$geo_query = $query->query_vars['geo_query'];

		global $wpdb;

		$query->query_from .= " ";
		$query->query_from .= "INNER JOIN $wpdb->usermeta AS geo_query_lat ON ( $wpdb->users.ID = geo_query_lat.user_id ) ";
		$query->query_from .= "INNER JOIN $wpdb->usermeta AS geo_query_lng ON ( $wpdb->users.ID = geo_query_lng.user_id ) ";

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
		
		$query->query_where .= $wpdb->prepare( $new_sql, $lat_field, $lng_field, $distance );

		$orderby = $query->get( 'orderby' );

		if ( 'distance' === $orderby ) {
			
			$order = $query->get('order');

			if ( ! $order ) {
				$order = 'ASC';
			}

			$query->query_fields  .= ", $haversine AS $this->distance_term";
			$query->query_orderby = "ORDER BY $this->distance_term $order";

		}

	}

}
