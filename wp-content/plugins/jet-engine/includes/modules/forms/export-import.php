<?php
/**
 * Export/import, duplicate manager class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Forms_Export_Import class
 */
class Jet_Engine_Forms_Export_Import {

	protected $nonce = 'jet-engine-export-import';

	public function __construct() {

		add_filter( 'post_row_actions', array( $this, 'action_links' ), 10, 2 );

		add_action( 'admin_action_jet_engine_forms_duplicate', array( $this, 'duplicate_form_cb' ) );
		add_action( 'admin_action_jet_engine_forms_export', array( $this, 'export_form_cb' ) );
		add_action( 'admin_action_jet_engine_forms_import', array( $this, 'import_form_cb' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'import_form_js' ) );

	}

	public function verify_nonce() {
		
		$nonce = ! empty( $_REQUEST['_nonce'] ) ? $_REQUEST['_nonce'] : false;

		if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce ) ) {
			wp_die( 'Link is expired. Ppease reload page and try again.', 'Error' );
		}

	}

	public function import_form_cb() {

		if ( ! current_user_can( 'publish_posts' ) ) {
			wp_die( 'Acess denied', 'Error' );
		}

		$this->verify_nonce();

		$file = ! empty( $_FILES['form_file'] ) ? $_FILES['form_file'] : false;

		if ( ! $file ) {
			wp_die( 'File not found in request', 'Error' );
		}

		$type = $file['type'];

		if ( 'application/json' !== $type ) {
			wp_die( 'Incorrect file type', 'Error' );
		}

		if ( MB_IN_BYTES < $file['size'] ) {
			wp_die( 'File to large', 'Error' );
		}

		$content = file_get_contents( $file['tmp_name'] );

		unlink( $file['tmp_name'] );

		$content = json_decode( $content, true );

		if ( ! $content ) {
			wp_die( 'Incorrect file format', 'Error' );
		}

		$form_id = $this->import_form( $content );

		wp_redirect( get_edit_post_link( $form_id, 'url' ) );
		die();

	}

	public function import_form_js( $hook ) {

		global $current_screen;

		if ( 'edit-' . jet_engine()->forms->slug() !== $current_screen->id ) {
			return;
		}

		$form_action = add_query_arg(
			array(
				'action' => 'jet_engine_forms_import',
				'_nonce' => wp_create_nonce( $this->nonce ),
			),
			esc_url( admin_url( 'admin.php' ) )
		);

		ob_start();
		?>
		( function( $ ) {

			document.addEventListener( 'DOMContentLoaded', function() {

				var $newFormButton = $( '.page-title-action' );

				if ( $newFormButton.length ) {
					$newFormButton.after( '<a href="#" class="page-title-action" id="jet-engine-form-import-trigger"><?php _e( 'Import Form', 'jet-engine' ); ?></a><form id="jet-engine-form-import" style="display: none; margin: -0 0 0 20px; padding: 5px 15px; align-items: center; background: #fff;" method="post" action="<?php echo $form_action; ?>" enctype="multipart/form-data"><input type="file" name="form_file" accept="application/json" multiple="false"><button class="button button-primary" type="submit" style="margin: 0 0 0 5px;"><?php _e( 'Start Import', 'jet-engine' ); ?></button></form>' );
				}

			});

			$( document ).on( 'click', '#jet-engine-form-import-trigger', function() {
				$( '#jet-engine-form-import' ).css( 'display', 'inline-flex' );
			} );

		}( jQuery ) );
		<?php

		$script = ob_get_clean();

		wp_add_inline_script( 'jquery', $script );

	}

	/**
	 * Get post ID from the current request and validate user acess to this post
	 *
	 * @return [type] [description]
	 */
	public function get_post_id_from_request() {

		$post_id = ! empty( $_GET['post'] ) ? absint( $_GET['post'] ) : false;

		if ( ! $post_id ) {
			wp_die( 'Form ID not found in the request', 'Error!' );
		}

		if ( ! $this->check_user_access( $post_id ) ) {
			wp_die( 'You haven`t access to this form', 'Error!' );
		}

		return $post_id;

	}

	/**
	 * Returns from data by ID
	 *
	 * @param  [type] $form_id [description]
	 * @return [type]          [description]
	 */
	public function get_from_data( $form_id ) {

		$title   = get_the_title( $form_id );
		$decoded = array();

		foreach ( array( '_form_data', '_notifications_data' ) as $meta_key ) {

			$meta_value = get_post_meta( $form_id, $meta_key, true );

			if ( ! $meta_value ) {
				$meta_value = '[]';
			} else {
				$meta_value = wp_unslash( $meta_value );
			}

			$decoded[ $meta_key ] = json_decode( $meta_value, true );;

		}

		$data = array(
			'title' => $title,
			'meta'  => array(
				'_form_data'          => $decoded['_form_data'],
				'_notifications_data' => $decoded['_notifications_data'],
				'_messages'           => get_post_meta( $form_id, '_messages', true ),
				'_captcha'            => get_post_meta( $form_id, '_captcha', true ),
				'_preset'             => get_post_meta( $form_id, '_preset', true ),
			),
		);

		return $data;

	}

	/**
	 * Duplicate from action callback
	 *
	 * @return [type] [description]
	 */
	public function duplicate_form_cb() {

		$this->verify_nonce();

		$post_id = $this->get_post_id_from_request();

		if ( ! current_user_can( 'publish_posts' ) || ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( 'Acess denied', 'Error' );
		}

		$form_data = $this->get_from_data( $post_id );

		$title              = $form_data['title'];
		$form_data['title'] = $title . ' (Copy)';

		$this->import_form( $form_data );

		wp_redirect( add_query_arg( array( 'post_type' => jet_engine()->forms->slug() ), admin_url( 'edit.php' ) ) );
		die();

	}

	/**
	 * Import form by data
	 *
	 * @param  array  $form_data [description]
	 * @return [type]            [description]
	 */
	public function import_form( $form_data = array() ) {

		$form_data = wp_parse_args( $form_data, array(
			'title' => 'New form',
		) );

		$post_id = wp_insert_post( array(
			'post_type'   => jet_engine()->forms->slug(),
			'post_title'  => $form_data['title'],
			'post_status' => 'publish',
		) );

		if ( is_wp_error( $post_id ) ) {
			wp_die( $post_id->get_error_message(), 'Error' );
		}

		if ( ! empty( $form_data['meta'] ) ) {

			$encode = array( '_form_data', '_notifications_data' );

			foreach ( $form_data['meta'] as $key => $value ) {

				if ( in_array( $key, $encode ) ) {

					if ( empty( $value ) ) {
						$value = '[]';
					} else {
						$value = wp_slash( json_encode( $value ) );
					}

					$value = wp_slash( $value );

				}

				update_post_meta( $post_id, $key, $value );

			}
		}

		return $post_id;

	}

	/**
	 * Export form action callback
	 *
	 * @return [type] [description]
	 */
	public function export_form_cb() {

		$this->verify_nonce();

		$post_id = $this->get_post_id_from_request();

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( 'Acess denied', 'Error' );
		}

		$form_data = $this->get_from_data( $post_id );
		$form      = get_post( $post_id );

		Jet_Engine_Tools::file_download( $form->post_name . '.json', json_encode( $form_data ) );

	}

	public function check_user_access( $post_id = null ) {

		$res = true;

		if ( ! current_user_can( 'edit_posts' ) ) {
			$res = false;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			$res = false;
		}

		return $res;

	}

	public function action_links( $actions, $post ) {

		if ( ! $this->check_user_access( $post->ID ) ) {
			return $actions;
		}

		if ( jet_engine()->forms->slug() !== $post->post_type ) {
			return $actions;
		}

		$admin_url = esc_url( admin_url( 'admin.php' ) );
		$clone_url = add_query_arg(
			array(
				'action' => 'jet_engine_forms_duplicate',
				'post'   => $post->ID,
				'_nonce' => wp_create_nonce( $this->nonce ),
			),
			$admin_url
		);

		$export_url = add_query_arg(
			array(
				'action' => 'jet_engine_forms_export',
				'post'   => $post->ID,
				'_nonce' => wp_create_nonce( $this->nonce ),
			),
			$admin_url
		);

		$trash = ! empty( $actions['trash'] ) ? $actions['trash'] : false;

		if ( $trash ) {
			unset( $actions['trash'] );
		}

		$actions['jet_engine_duplicate'] = sprintf(
			'<a href="%1$s" title="%3$s" rel="permalink">%2$s</a>',
			$clone_url,
			__( 'Duplicate', 'jet-engine' ),
			__( 'Duplicate this form', 'jet-engine' )
		);

		$actions['jet_engine_export'] = sprintf(
			'<a href="%1$s" title="%3$s" rel="permalink">%2$s</a>',
			$export_url,
			__( 'Export', 'jet-engine' ),
			__( 'Export this form as JSON file', 'jet-engine' )
		);

		$actions['trash'] = $trash;

		return $actions;

	}

}
