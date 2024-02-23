<?php
namespace Jet_Engine\Modules\Profile_Builder;

class Access {

	public function __construct() {
		$this->check_admin_area_access();
	}

	/**
	 * Check if is admin area request and its accessible by current user
	 */
	public function check_admin_area_access() {

		if ( ! is_admin() ) {
			return;
		}

		$restrict = Module::instance()->settings->get( 'restrict_admin_access' );

		if ( ! $restrict ) {
			return;
		}

		if ( current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( wp_doing_ajax() ) {
			return;
		}

		$accessible_roles = Module::instance()->settings->get( 'admin_access_roles' );

		if ( empty( $accessible_roles ) ) {
			$accessible_roles = array();
		}

		$accessible_roles[] = 'administrator';

		$user = wp_get_current_user();
		$user_roles = ( array ) $user->roles;

		$res = false;

		foreach ( $user_roles as $role ) {
			if ( in_array( $role, $accessible_roles ) ) {
				$res = true;
			}
		}

		if ( ! $res ) {

			$account_page = Module::instance()->settings->get( 'account_page' );

			if ( $account_page ) {
				wp_redirect( get_permalink( $account_page ) );
			} else {
				wp_redirect( home_url( '/' ) );
			}

			die();

		}

	}

	/**
	 * Check if current user hass access to current page
	 *
	 * @return [type] [description]
	 */
	public function check_user_access() {

		$result = array(
			'access'   => true,
			'template' => null,
		);

		if ( ! Module::instance()->query->is_account_page() ) {
			return $result;
		}

		if ( is_user_logged_in() ) {
			return $this->check_access_by_role( $result );
		}

		$action = Module::instance()->settings->get( 'not_logged_in_action', 'login_redirect' );

		switch ( $action ) {

			case 'login_redirect':
				wp_redirect( wp_login_url( get_permalink() ), 303 );
				die();

			case 'page_redirect':

				$page_url = Module::instance()->settings->get( 'not_logged_in_redirect', home_url( '/' ) );
				$page_url = add_query_arg(
					apply_filters( 'jet-engine/profile-builder/not-logged-rediret-query-args', array(
						'redirect_to' => get_permalink(),
					) ),
					esc_url( $page_url )
				);

				wp_redirect( $page_url, 303 );
				die();

		}

		$template_id        = Module::instance()->settings->get( 'not_logged_in_template' );
		$result['access']   = false;
		$result['template'] = ! empty( $template_id ) ? $template_id[0] : false;

		return apply_filters( 'jet-engine/profile-builder/check-user-access', $result, Module::instance() );

	}

	/**
	 * Check access to the current page by user role
	 *
	 * @return [type] [description]
	 */
	public function check_access_by_role( $result = array() ) {

		$subpage = Module::instance()->query->get_subpage_data();

		if ( empty( $subpage ) || empty( $subpage['roles'] ) ) {
			return $result;
		}

		$user = wp_get_current_user();
		$intersect = array_intersect( $user->roles, $subpage['roles'] );

		if ( ! empty( $intersect ) ) {
			return $result;
		}

		$action = Module::instance()->settings->get( 'not_accessible_action', 'login_redirect' );

		switch ( $action ) {

			case 'page_redirect':

				$page_url = Module::instance()->settings->get( 'not_accessible_redirect', home_url( '/' ) );
				$page_url = add_query_arg(
					apply_filters( 'jet-engine/profile-builder/not-accessible-rediret-query-args', array(
						'redirect_to' => get_permalink(),
					) ),
					esc_url( $page_url )
				);

				wp_redirect( $page_url, 303 );
				die();

		}

		$template_id        = Module::instance()->settings->get( 'not_accessible_template' );
		$result['access']   = false;
		$result['template'] = ! empty( $template_id ) ? $template_id[0] : false;

		return $result;

	}

}
