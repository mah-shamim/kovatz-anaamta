<?php
namespace Jet_Engine\Modules\Data_Stores\Macros;

use Jet_Engine\Modules\Data_Stores\Module;

class Get_Users_For_Store_Item extends \Jet_Engine_Base_Macros {

	/**
	 * @inheritDoc
	 */
	public function macros_tag() {
		return 'get_users_for_store_item';
	}

	/**
	 * @inheritDoc
	 */
	public function macros_name() {
		return esc_html__( 'Get users from store item', 'jet-engine' );
	}

	/**
	 * @inheritDoc
	 */
	public function macros_args() {
		return array(
			'store' => array(
				'label'   => __( 'Store', 'jet-engine' ),
				'type'    => 'select',
				'options' => Module::instance()->elementor_integration->get_store_options(),
			),
			'context' => array(
				'label'   => __( 'Context', 'jet-engine' ),
				'type'    => 'select',
				'options' => array(
					''                    => esc_html__( 'Select...', 'jet-engine' ),
					'post'                => esc_html__( 'Post', 'jet-engine' ),
					'wp_user'             => esc_html__( 'Current user (global)', 'jet-engine' ),
					'current_user'        => esc_html__( 'Current user (for current scope)', 'jet-engine' ),
					'queried_user'        => esc_html__( 'Queried user', 'jet-engine' ),
					'current_post_author' => esc_html__( 'Current post author', 'jet-engine' ),
				),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function macros_callback( $args = array() ) {

		$store   = ! empty( $args['store'] ) ? $args['store'] : false;
		$context = ! empty( $args['context'] ) ? $args['context'] : 'post';
		$item_id = false;

		if ( ! $store ) {
			return 'not found';
		}

		$store_instance = Module::instance()->stores->get_store( $store );

		if ( ! $store_instance ) {
			return 'not found';
		}

		switch ( $context ) {

			case 'wp_user':
				$user = wp_get_current_user();

				if ( $user ) {
					$item_id = $user->ID;
				}

				break;

			case 'current_user':
			case 'user':

				$user = jet_engine()->listings->data->get_current_user_object();

				if ( $user ) {
					$item_id = $user->ID;
				}

				break;

			case 'queried_user':

				$user = jet_engine()->listings->data->get_queried_user_object();

				if ( $user ) {
					$item_id = $user->ID;
				}

				break;

			case 'current_post_author':
			case 'post_author':
			case 'author':

				$user = jet_engine()->listings->data->get_current_author_object();

				if ( $user ) {
					$item_id = $user->ID;
				}

				break;

			default:

				$item_id = apply_filters(
					'jet-engine/data-stores/get-users-macros/context/' . $context,
					get_the_ID()
				);

				break;

		}

		global $wpdb;

		$slug    = $store_instance->get_slug();
		$item_id = '"' . $item_id . '"';
		$query   = "SELECT `user_id` FROM $wpdb->usermeta WHERE `meta_key` = 'je_data_store_$slug' AND `meta_value` LIKE '%:$item_id;%'";

		$result = $wpdb->get_results( $query );

		if ( empty( $result ) ) {
			return 'not found';
		}

		$glue = '';
		$ids  = '';

		foreach ( $result as $row ) {
			$ids .= $glue . $row->user_id;
			$glue = ',';
		}

		return $ids;
	}
}