<?php
/**
 * Class Admin API for WC Vendors.
 */
class WCV_Admin_API extends WCV_API {
    /**
     * Register routes.
     */
    public function register_routes() {
        $this->register_route(
            '/vendors/settings/(?P<id>\d+)',
            'get_settings',
            WP_REST_Server::READABLE
        );
        $this->register_route(
            '/vendors/settings/(?P<id>\d+)',
            'save_settings',
            WP_REST_Server::EDITABLE
        );

        $this->register_route(
            '/vendors',
            'get_vendors',
            WP_REST_Server::READABLE,
            array(
                'page'   => array(
                    'description'       => __( 'Current page of the collection', 'wc-vendors' ),
                    'type'              => 'integer',
                    'default'           => 1,
                    'sanitize_callback' => 'absint',
                ),
                'limit'  => array(
                    'description'       => __( 'Maximum number of items to be returned in result set.', 'wc-vendors' ),
                    'type'              => 'integer',
                    'default'           => 10,
                    'sanitize_callback' => 'absint',
                ),
                'search' => array(
                    'description'       => __( 'Limit results to those matching a string.', 'wc-vendors' ),
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'status' => array(
                    'description'       => __( 'Limit result set to vendors assigned a specific status.', 'wc-vendors' ),
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
            )
        );

        $this->register_route(
            '/vendors/action/(?P<id>\d+)/(?P<action>\w+)',
            'do_vendor_action',
            WP_REST_Server::EDITABLE
        );
    }

    /**
     * Get settings.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_REST_Response
     */
    public function get_settings( $request ) {
        $vendor_id = (int) $request->get_param( 'id' );

        if ( ! $vendor_id ) {
            return new WP_REST_Response(
                array(
                    'error'   => 'no_vendor_id',
                    'success' => false,
                    'message' => __( 'No vendor ID provided.', 'wc-vendors' ),
                ),
                200
            );
        }

        $is_user = get_userdata( $vendor_id );

        if ( ! $is_user || ( ! WCV_Vendors::is_vendor( $vendor_id ) && ! WCV_Vendors::is_pending( $vendor_id ) ) ) {
            return new WP_REST_Response(
                array(
                    'error'   => 'not_vendor',
                    'success' => false,
                    'message' => __( 'Not a vendor.', 'wc-vendors' ),
                ),
                200
            );
        }

        $vendor = new Vendors_Settings( $vendor_id );
        if ( $vendor->get_prop( 'vendor_status' ) === 'inactive' ) {
            return new WP_REST_Response(
                array(
                    'error'   => 'vendor_inactive',
                    'success' => false,
                    'message' => __( 'You can\'t edit inactive vendor.', 'wc-vendors' ),
                ),
                200
            );
        }
        $vendor_settings = $vendor->get_settings();
        $response        = new WP_REST_Response( $vendor_settings, 200 );
        return $response;
    }

    /**
     * Save settings.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function save_settings( $request ) {

        $changes       = $request->get_params( 'changes' );
        $vendor_id     = (int) $request->get_param( 'id' );
        $vendor_status = get_user_meta( $vendor_id, '_wcv_vendor_status', true );
        $is_user       = get_userdata( $vendor_id );

        $response_result = array(
            'success' => false,
            'message' => __( 'Something went wrong.', 'wc-vendors' ),
        );

        if ( ! $is_user || ! $vendor_id || 'inactive' === $vendor_status || ( ! WCV_Vendors::is_vendor( $vendor_id ) && ! WCV_Vendors::is_pending( $vendor_id ) ) ) {
            return new WP_REST_Response( $response_result, 200 );
        }

        $vendor_settings = new Vendors_Settings( $vendor_id );
        foreach ( $changes as $key => $value ) {
            $vendor_settings->{$key} = $value;
        }
        $result = $vendor_settings->save();
        if ( $result ) {
            $response_result = array(
                'success' => true,
                'message' => __( 'Settings saved.', 'wc-vendors' ),
            );
        }
        return new WP_REST_Response( $response_result, 200 );
    }

    /**
     * Check permissions for the API.
     *
     * @return bool
     */
    public function get_api_permissions_check() {
        return current_user_can( 'manage_woocommerce' );
    }

    /**
     * Get vendor count for all statuses.
     *
     * @return array Counts of vendors for all statuses.
     */
    private function _get_vendor_count_for_all_status() {
        global $wpdb;

        $count_user_sql = "SELECT
            COUNT( CASE WHEN umt1.meta_value = 'active' AND ( umt2.meta_key = '{$wpdb->prefix}capabilities' AND umt2.meta_value LIKE '%vendor%') THEN 1 END ) AS active,
            COUNT( CASE WHEN umt1.meta_value = 'inactive' AND ( umt2.meta_key = '{$wpdb->prefix}capabilities' AND (umt2.meta_value NOT LIKE '%pending_vendor%' AND umt2.meta_value LIKE '%vendor%' )) THEN 1 END ) AS inactive,
            COUNT( CASE WHEN umt2.meta_key = '{$wpdb->prefix}capabilities' AND umt2.meta_value LIKE '%vendor%' THEN 1 END ) AS vendor,
            COUNT( CASE WHEN umt2.meta_key = '{$wpdb->prefix}capabilities' AND umt2.meta_value LIKE '%pending_vendor%' THEN 1 END ) AS pending
            FROM {$wpdb->usermeta} AS umt1
            INNER JOIN {$wpdb->usermeta} as umt2 ON umt1.user_id = umt2.user_id
            WHERE umt1.meta_key = '_wcv_vendor_status' AND umt2.meta_key = '{$wpdb->prefix}capabilities'
        ";

        return array_map( 'intval', (array) $wpdb->get_row( $count_user_sql ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
    }

    /**
     * Custom query to search customers.
     *
     * @param array $params Array of parameters.
     *
     * @return array $results Tuple of results and total results.
     */
    private function _query_vendor_ids( $params ) {
        global $wpdb;

        $params = wp_parse_args(
            $params,
            array(
                'search' => '',
                'status' => 'active',
                'limit'  => 10,
                'page'   => 1,
            ),
        );

        extract( $params ); // phpcs:ignore

        $offset = ( $page - 1 ) * $limit;

        $inner_joins  = "INNER JOIN {$wpdb->usermeta} AS ucap ON (u.ID = ucap.user_id AND ucap.meta_key = '{$wpdb->prefix}capabilities') ";
        $concat_query = '';
        $where_query  = "AND ucap.meta_value LIKE '%vendor%' ";
        $having_query = '';

        // build the query based on the search parameter.
        if ( $search ) {
            $regexsearch  = str_replace( ' ', '|', $search );
            $concat_query = ", u.user_login, u.user_nicename, u.user_email,
                GROUP_CONCAT( IF(um.meta_key REGEXP 'billing_|nickname|first_name last_name', um.meta_key, null) ORDER BY um.meta_key DESC SEPARATOR ' ' ) AS meta_keys,
                GROUP_CONCAT( IF(um.meta_key REGEXP 'billing_|nickname|first_name|last_name', IFNULL(um.meta_value, ''), null) ORDER BY um.meta_key DESC SEPARATOR ' ' ) AS meta_values
            ";
            $inner_joins .= "INNER JOIN {$wpdb->usermeta} um ON (u.ID = um.user_id)";
            $having_query = "HAVING (u.ID REGEXP '{$regexsearch}' OR meta_values REGEXP '{$regexsearch}' OR u.user_login REGEXP '{$regexsearch}' OR u.user_nicename REGEXP '{$regexsearch}' OR u.user_email REGEXP '{$regexsearch}')";
        }

        // build the query based on the status parameter.
        if ( $status ) {
            $inner_joins .= "INNER JOIN {$wpdb->usermeta} AS vstatus ON (u.ID = vstatus.user_id AND vstatus.meta_key = '_wcv_vendor_status')";
            $where_query .= 'pending' !== $status ? "AND vstatus.meta_value = '{$status}' AND ucap.meta_value NOT LIKE '%pending_vendor%' " : "AND ucap.meta_value LIKE '%pending_vendor%'";
        }

        // phpcs:disable
        $results = $wpdb->get_col(
            "SELECT SQL_CALC_FOUND_ROWS DISTINCT u.ID
            {$concat_query}
            FROM {$wpdb->users} AS u
            {$inner_joins}
            WHERE 1
            {$where_query}
            GROUP BY u.ID
            {$having_query}
            LIMIT {$limit} OFFSET {$offset}"
        );
        // phpcs:enable

        return array(
            array_map( 'absint', $results ), // SQL query results.
			(int) $wpdb->get_var( 'SELECT FOUND_ROWS()' ), // Total results.
		);
    }

    /**
     * Get vendors.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function get_vendors( $request ) {
        global $wpdb;

        // Get vendor counts.
        $vendor_count = $this->_get_vendor_count_for_all_status();

        // TODO: sanitize parameter values.
        $params = $request->get_params();

        // Query the vendor IDs based on the provided parameters.
        list( $vendor_ids, $result_count ) = $this->_query_vendor_ids( $params );

        // Return an empty data response when there are no vendors found.
        if ( empty( $vendor_ids ) ) {
            $response = new WP_REST_Response(
                array(
                    'vendors'      => array(),
                    'vendor_count' => $vendor_count,
                    'result_count' => (int) $result_count,
                ),
                200
            );
            return $response;
        }

        $vendors = array();
        foreach ( $vendor_ids as $vendor_id ) {
            $vendor           = new Vendors_Settings( $vendor_id );
            $date_time_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
            $vendors[]        = array(
                'id'              => $vendor_id,
                'displayname'     => $vendor->display_name,
                'shopname'        => $vendor->shop_name,
                'commission_due'  => $vendor->get_commission_due(),
                'commission_rate' => $vendor->get_commission_rates(),
                'status'          => $vendor->get_vendor_status(),
                'registered_date' => wp_date( $date_time_format, strtotime( $vendor->registered ) ),
                'shop_link'       => WCV_Vendors::get_vendor_shop_page( $vendor_id ),
            );
        }

        $response = new WP_REST_Response(
            array(
                'vendors'      => $vendors,
                'vendor_count' => $vendor_count,
                'result_count' => (int) $result_count,
            ),
            200
        );
        return $response;
    }

    /**
     * Do vendor action.
     *
     * @param WP_REST_Request $request Full details about the request.
     *
     * @return WP_Error|WP_REST_Response
     */
    public function do_vendor_action( $request ) {
        $vendor_id = (int) $request->get_param( 'id' );
        $action    = $request->get_param( 'action' );

        if ( ! $vendor_id ) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => __( 'No vendor ID provided.', 'wc-vendors' ),
                ),
                200
            );
        }

        $is_user = get_userdata( $vendor_id );
        if ( ( ! WCV_Vendors::is_vendor( $vendor_id ) && ! WCV_Vendors::is_pending( $vendor_id ) || ! $is_user ) ) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => __( 'Not a vendor.', 'wc-vendors' ),
                ),
                200
            );
        }

        switch ( $action ) {
            case 'activate':
                $result = $this->set_vendor_active( $vendor_id );
                break;
            case 'deactivate':
                $result = $this->set_vendor_inactive( $vendor_id );
                break;
            case 'approve':
                $result = $this->approve_vendor( $vendor_id );
                break;
            case 'deny':
                $result = $this->deny_vendor( $vendor_id );
                break;
            default:
                $result = array(
					'success' => false,
					'message' => __( 'Invalid action.', 'wc-vendors' ),
				);
                break;
        }

        return $result;
    }

    /**
     * Action inactive vendor
     *
     * @param int $vendor_id Vendor ID.
     */
    public function set_vendor_inactive( $vendor_id ) {

        $vendor = new Vendors_Settings( $vendor_id, true );
        $status = $vendor->get_prop( 'vendor_status' );
        if ( 'inactive' === $status ) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => __( 'Vendor is already inactive.', 'wc-vendors' ),
                ),
                200
            );
        }
        $vendor->set_prop( 'vendor_status', 'inactive' );
        $result = $vendor->save();
        do_action( 'wcvendors_set_vendor_inactive', $vendor_id );
        if ( $result ) {
            return new WP_REST_Response(
                array(
                    'success' => true,
                    'message' => __( 'Vendor has been deactivated.', 'wc-vendors' ),
                ),
                200
            );
        } else {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => __( 'An error occurred while deactivating the vendor.', 'wc-vendors' ),
                ),
                200
            );
        }
    }

    /**
     * Action active vendor
     *
     * @param int $vendor_id Vendor ID.
     */
    public function set_vendor_active( $vendor_id ) {

        $vendor = new Vendors_Settings( $vendor_id, true );
        $status = $vendor->get_prop( 'vendor_status' );
        if ( 'active' === $status ) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => __( 'Vendor is already active.', 'wc-vendors' ),
                ),
                200
            );
        }
        $vendor->set_prop( 'vendor_status', 'active' );
        $result = $vendor->save();
        do_action( 'wcvendors_set_vendor_active', $vendor_id );
        if ( $result ) {
            return new WP_REST_Response(
                array(
                    'success' => true,
                    'message' => __( 'Vendor has been activated.', 'wc-vendors' ),
                ),
                200
            );
        } else {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => __( 'An error occurred while activating the vendor.', 'wc-vendors' ),
                ),
                200
            );
        }
    }

    /**
     * Approve vendor.
     *
     * @param int $vendor_id Vendor ID.
     */
    public function approve_vendor( $vendor_id ) {
        $vendor = new WP_User( $vendor_id );
        $roles  = $vendor->roles;
        if ( ! in_array( 'pending_vendor', $roles, true ) ) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => __( 'Vendor cannot be approved. Please make sure the vendor is pending.', 'wc-vendors' ),
                ),
                200
            );
        }
        $vendor->remove_role( 'pending_vendor' );
        wcv_set_primary_vendor_role( $vendor );
        update_user_meta( $vendor_id, '_wcv_vendor_status', 'active' );
        return new WP_REST_Response(
            array(
				'success' => true,
				'message' => __( 'Vendor approved.', 'wc-vendors' ),
            ),
            200
        );
    }

    /**
     * Deny vendor.
     *
     * @param int $vendor_id Vendor ID.
     */
    public function deny_vendor( $vendor_id ) {

        $role   = apply_filters( 'wcvendors_denied_vendor_role', get_option( 'default_role', 'subscriber' ) );
        $vendor = new WP_User( $vendor_id );
        $roles  = $vendor->roles;
        if ( ! in_array( 'pending_vendor', $roles, true ) ) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => __( 'Vendor cannot be denied. Please make sure the vendor is pending.', 'wc-vendors' ),
                ),
                200
            );
        }
        $vendor->remove_role( 'pending_vendor' );

        if ( empty( $vendor->roles ) ) {
            $vendor->add_role( $role );
        }
        delete_user_meta( $vendor_id, '_wcv_vendor_status' );
        return new WP_REST_Response(
            array(
                'success' => true,
                'message' => __( 'Vendor denied.', 'wc-vendors' ),
            ),
            200
        );
    }
}
new WCV_Admin_API();
