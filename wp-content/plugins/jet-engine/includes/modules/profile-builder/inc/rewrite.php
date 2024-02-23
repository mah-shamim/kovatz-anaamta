<?php
namespace Jet_Engine\Modules\Profile_Builder;

class Rewrite {

	public $page_var    = 'jet_pb';
	public $subpage_var = 'jet_pb_subpage';
	public $user_var    = 'jet_pb_user';

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		add_filter( 'rewrite_rules_array', array( $this, 'register_rewrites' ) );
		add_filter( 'query_vars', array( $this, 'register_variables' ) );
	}

	/**
	 * [register_variables description]
	 * @return [type] [description]
	 */
	public function register_variables( $vars ) {

		$vars[] = $this->page_var;
		$vars[] = $this->subpage_var;
		$vars[] = $this->user_var;

		return $vars;

	}

	/**
	 * Resiteer custom rewrite rules
	 *
	 * @return [type] [description]
	 */
	public function register_rewrites( $rules ) {

		$profile_rules = $this->get_profile_rewrites();

		if ( ! empty( $profile_rules ) ) {
			$rules = array_merge( $profile_rules, $rules );
		}

		return $rules;

	}

	/**
	 * Returns profile specific rewrite rules
	 *
	 * @return array
	 */
	public function get_profile_rewrites() {

		$result = array();
		$pages  = Module::instance()->settings->get_pages();

		foreach ( $pages as $page => $page_id ) {
			$rewrite = $this->get_page_rewrite( $page, $page_id );
			if ( $rewrite ) {
				$result[ $rewrite['regex'] ] = $rewrite['redirect'];
			}
		}

		return apply_filters( 'jet-engine/profile-builder/rewrite-rules', array_filter( $result ), $this );

	}

	/**
	 * Return rewrite information for current account page
	 *
	 * @param [type] $page    [description]
	 * @param [type] $page_id [description]
	 */
	public function get_page_rewrite( $page, $page_id ) {

		if ( ! $page_id ) {
			return false;
		}

		$page_object = get_page( $page_id );

		if ( ! $page_object ) {
			return;
		}

		$slug = $page_object->post_name;

		switch ( $page ) {

			case 'single_user_page':
				$regex    = $slug . '/([^/]+)/?([^/]+)?';
				$redirect = 'pagename=' . $slug . '&' . $this->page_var . '=' . $page . '&' . $this->user_var . '=$matches[1]' . '&' . $this->subpage_var . '=$matches[2]';
				break;

			default:
				$regex    = $slug . '/?([^/]+)?';
				$redirect = 'pagename=' . $slug . '&' . $this->page_var . '=' . $page . '&' . $this->subpage_var . '=$matches[1]';
				break;
		}

		return array(
			'regex'    => $regex,
			'redirect' => $redirect,
		);

	}

}
