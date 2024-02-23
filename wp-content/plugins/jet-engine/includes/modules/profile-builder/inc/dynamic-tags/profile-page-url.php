<?php
namespace Jet_Engine\Modules\Profile_Builder\Dynamic_Tags;

use Jet_Engine\Modules\Profile_Builder\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Profile_Page_URL extends \Elementor\Core\DynamicTags\Data_Tag {

	public $current_user_obj = false;

	public function get_name() {
		return 'jet-profile-page-url';
	}

	public function get_title() {
		return __( 'Profile Page URL', 'jet-engine' );
	}

	public function get_group() {
		return \Jet_Engine_Dynamic_Tags_Module::JET_GROUP;
	}

	public function get_categories() {
		return array(
			\Jet_Engine_Dynamic_Tags_Module::URL_CATEGORY,
		);
	}

	protected function register_controls() {

		$pages = Module::instance()->elementor->get_pages_for_options();

		$this->add_control(
			'profile_page',
			array(
				'label'     => __( 'Profile Page', 'jet-engine' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => '',
				'groups'    => $pages,
			)
		);

		$this->add_control(
			'user_context',
			array(
				'label'   => __( 'Context', 'jet-engine' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'queried_user',
				'options' => apply_filters( 'jet-engine/elementor/dynamic-tags/user-context-list', array(
					'queried_user'        => __( 'Queried User', 'jet-engine' ),
					'current_post_author' => __( 'Current Post Author', 'jet-engine' ),
				) ),
			)
		);

		$this->add_control(
			'add_query_args',
			array(
				'label'        => esc_html__( 'Add Query Arguments', 'jet-engine' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-engine' ),
				'label_off'    => esc_html__( 'No', 'jet-engine' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'query_args',
			array(
				'label'       => __( 'Query Arguments', 'jet-engine' ),
				'label_block' => true,
				'type'        => \Elementor\Controls_Manager::TEXTAREA,
				'default'     => '_post_id=%current_id%',
				'description' => __( 'One argument per line. Separate key and value with "="', 'jet-engine' ),
				'condition'   => array(
					'add_query_args' => 'yes',
				),
			)
		);

	}

	public function get_value( array $options = array() ) {

		$profile_page = $this->get_settings( 'profile_page' );
		$context      = $this->get_settings( 'user_context' );

		if ( ! $context ) {
			$context = 'current_user';
		}

		if ( ! $profile_page ) {
			return;
		}

		$change_user_context = false;

		switch ( $context ) {
			case 'current_post_author':
				$change_user_context = true;
				$user_object = jet_engine()->listings->data->get_current_author_object();
				break;

			default:
				$user_object = jet_engine()->listings->data->get_queried_user_object();
				$user_object = apply_filters( 'jet-engine/elementor/dynamic-tags/user-context-object/' . $context, $user_object );
				
				$change_user_context = apply_filters( 
					'jet-engine/profile-builder/page-url/change-user-context/' . $context,
					$change_user_context 
				);

				break;
		}

		if ( ! $user_object ) {
			return;
		}

		$url = null;
		$profile_page = explode( '::', $profile_page );

		if ( 1 < count( $profile_page ) ) {

			$this->maybe_change_user_context( $change_user_context, $user_object );

			$url = Module::instance()->settings->get_subpage_url( $profile_page[1], $profile_page[0] );
			$url = \Jet_Engine_Tools::add_query_args_by_settings( $url, array(
				'add_query_args' => $this->get_settings( 'add_query_args' ),
				'query_args'     => $this->get_settings( 'query_args' ),
			) );

			$this->maybe_reset_user_context( $change_user_context );

		}

		return $url;

	}

	public function maybe_change_user_context( $change_user_context = false, $user_object = false ) {

		if ( ! $change_user_context || ! $user_object ) {
			return;
		}

		$this->current_user_obj = $user_object;

		add_filter( 'jet-engine/profile-builder/query/pre-get-queried-user', array( $this, 'set_queried_user' ) );

	}

	public function maybe_reset_user_context( $change_user_context ) {

		if ( ! $change_user_context ) {
			return;
		}

		$this->current_user_obj = null;

		remove_filter( 'jet-engine/profile-builder/query/pre-get-queried-user', array( $this, 'set_queried_user' ) );

	}

	public function set_queried_user( $user ) {

		if ( $this->current_user_obj ) {
			$user = $this->current_user_obj;
		}

		return $user;

	}

}
