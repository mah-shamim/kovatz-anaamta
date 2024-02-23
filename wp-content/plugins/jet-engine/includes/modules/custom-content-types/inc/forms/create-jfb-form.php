<?php


namespace Jet_Engine\Modules\Custom_Content_Types\Forms;


use Jet_Engine\Modules\Custom_Content_Types\Forms\Converting_Jfb_Fields\Block_Generator;
use Jet_Engine\Modules\Custom_Content_Types\Forms\Converting_Jfb_Fields\Convert_Manager;
use Jet_Engine\Modules\Custom_Content_Types\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Create_Jfb_Form
 * @package Jet_Engine\Modules\Custom_Content_Types\Forms
 */
class Create_Jfb_Form {

	public static $action = 'jet_form_builder_cct_create_form';
	public $action_type = 'insert_custom_content_type';

	public function __construct() {
		require_once Module::instance()->module_path( "forms/converting-jfb-fields/convert-manager.php" );

		add_action(
			'admin_action_' . self::$action,
			array( $this, 'create_form' )
		);
	}

	public function create_form() {
		if ( empty( $_GET['cct_id'] ) ) {
			wp_die( 'CCT ID not found in the request', 'Error' );
		}

		if ( empty( $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], self::$action ) ) {
			wp_die( 'The link is expired, please return to the previous page and try again', 'Error' );
		}

		$content_type = Module::instance()->manager->get_content_type_by_id( absint( $_GET['cct_id'] ) );
		$form_fields  = Convert_Manager::instance()->prepare_fields( $content_type->fields );
		$generator    = new Block_Generator( $form_fields );

		$generator->add_block( 'jet-forms/submit-field' );

		$notification = array(
			array(
				'type'     => $this->action_type,
				'id'       => 3333,
				'settings' => array(
					$this->action_type => array(
						'type'       => $content_type->get_arg( 'slug' ),
						'fields_map' => Convert_Manager::instance()->fields_map,
					),
				)
			)
		);

		$form = array(
			'post_title'   => 'Add new ' . $content_type->get_arg( 'name' ) . ' item',
			'post_type'    => jet_form_builder()->post_type->slug(),
			'post_status'  => 'publish',
			'post_content' => $generator->generate(),
			'meta_input'   => array(
				'_jf_actions' => wp_slash( json_encode( $notification, JSON_UNESCAPED_UNICODE ) )
			)
		);

		$post_id = wp_insert_post( $form );

		wp_redirect( get_edit_post_link( $post_id, 'return' ) );
		die();
	}


}