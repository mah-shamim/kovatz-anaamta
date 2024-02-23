<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Forms_File_Upload' ) ) {

	class Jet_Engine_Forms_File_Upload {

		private $nonce_key       = 'jet-engine-file-upload';
		private $errors          = array();
		private $custom_messages = array();

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
			add_action( 'wp_ajax_jet-engine-forms-upload-file',        array( $this, 'ajax_file_upload' ) );
			add_action( 'wp_ajax_nopriv_jet-engine-forms-upload-file', array( $this, 'ajax_file_upload' ) );

		}

		/**
		 * [set_custom_messages description]
		 * @param [type] $form_id [description]
		 */
		public function set_custom_messages( $form_id ) {

			$message_builder = jet_engine()->forms->get_messages_builder( $form_id );
			$messages        = $message_builder->get_messages_data();

			if ( ! empty( $messages ) ) {
				$this->custom_messages = array(
					'upload_limit' => $messages['upload_max_files'],
					'file_type'    => $messages['upload_mime_types'],
					'file_size'    => $messages['upload_max_size'],
				);
			}
		}

		/**
		 * Returns data arguments for files wrapper
		 */
		public function get_files_data_args( $args ) {

			$data_args = array(
				'max_files' => 1,
				'insert_attachment' => false,
				'value_format' => 'url',
			);

			foreach ( $data_args as $key => $value ) {
				$data_args[ $key ] = ! empty( $args[ $key ] ) ? $args[ $key ] : $value;
			}

			if ( ! is_user_logged_in() ) {
				$data_args['insert_attachment'] = false;
				$data_args['value_format'] = 'url';
			}

			return sprintf( ' data-args="%s"', htmlspecialchars( json_encode( $data_args ) ) );
		}

		/**
		 * Ajax callback for uploading files
		 *
		 * @return [type] [description]
		 */
		public function ajax_file_upload() {

			$nonce   = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;
			$form_id = ! empty( $_REQUEST['form_id'] ) ? absint( $_REQUEST['form_id'] ) : false;
			$field   = ! empty( $_REQUEST['field'] ) ? $_REQUEST['field'] : false;

			if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->nonce_key ) ) {
				wp_send_json_error( __( 'You not allowed to do this', 'jet-engine' ) );
			}

			if ( ! $form_id || ! $field ) {
				wp_send_json_error( __( 'Required parameters not found in request', 'jet-engine' ) );
			}

			$form_data = get_post_meta( $form_id, '_form_data', true );

			if ( ! $form_data ) {
				wp_send_json_error( __( 'Form data not found', 'jet-engine' ) );
			}

			$form_data  = Jet_Engine_Booking_Forms_Editor::sanitize_form_data( $form_data );
			$field_data = null;

			foreach ( $form_data as $item ) {
				if ( ! empty( $item['settings']['name'] ) && $item['settings']['name'] === $field ) {
					$field_data = $item['settings'];
					break;
				}
			}

			if ( ! $field_data ) {
				wp_send_json_error( __( 'Requested field not found', 'jet-engine' ) );
			}

			$cap = ! empty( $field_data['allowed_user_cap'] ) ? $field_data['allowed_user_cap'] : 'upload_files';

			if ( 'any_user' !== $cap && ! is_user_logged_in() ) {
				wp_send_json_error( __( 'You are not allowed to upload files', 'jet-engine' ) );
			}

			if ( ! in_array( $cap, array( 'all', 'any_user' ) ) && ! current_user_can( $cap ) ) {
				wp_send_json_error( __( 'You are not allowed to upload files', 'jet-engine' ) );
			}

			// Prevent non logged-in users insert attachment
			if ( ! is_user_logged_in() ) {
				$field_data['insert_attachment'] = false;
			}

			$settings = array(
				'max_size' => $this->get_max_size_for_field( $field_data ),
			);

			if ( ! empty( $field_data['allowed_mimes'] ) ) {
				$settings['mime_types'] = $field_data['allowed_mimes'];
			}

			if ( ! empty( $field_data['max_files'] ) ) {
				$settings['max_files'] = $field_data['max_files'];
			}

			if ( ! empty( $field_data['insert_attachment'] ) ) {
				$settings['insert_attachment'] = $field_data['insert_attachment'];
			}

			$message_builder      = jet_engine()->forms->get_messages_builder( $form_id );
			$settings['messages'] = $message_builder->get_messages_data();

			$result = $this->process_upload( $_FILES, $settings );

			if ( ! $result ) {
				wp_send_json_error( __( 'Internal error. Please check uploaded files and try again.', 'jet-engine' ) );
			}

			wp_send_json_success( array(
				'files'  => $result,
				'html'   => $this->get_result_html( $field_data, $result ),
				'value'  => $this->get_result_value( $field_data, $result ),
				'errors' => $this->get_errors_string(),
			) );

		}

		/**
		 * Try to get files array from field data
		 *
		 * @param  array  $field  [description]
		 * @param  string $format [description]
		 * @return [type]         [description]
		 */
		public function get_files_from_field( $field = array(), $format = 'url' ) {

			$files = array();
			$value = ! empty( $field['default'] ) ? $field['default'] : array();

			if ( ! is_array( $value ) ) {
				if ( 'both' !== $format ) {
					$value = explode( ',', str_replace( ', ', ',', $value ) );
				} else {
					if ( false !== strpos( $value, '{' ) ) {
						$value = json_decode( $value, true );
					} else {
						return $files;
					}
				}
			}

			if ( 'both' === $format ) {
				$value = isset( $value['id'] ) ? array( $value ) : $value;
			}

			foreach ( $value as $val ) {

				switch ( $format ) {

					case 'id':

						$files[] = array(
							'url'        => wp_get_attachment_url( $val ),
							'attachment' => $val,
						);

						break;

					case 'url':

						$files[] = array(
							'url' => $val,
						);

						break;

					case 'both':
						if ( is_array( $val ) && isset( $val['url'] ) && isset( $val['id'] ) ) {
							$files[] = array(
								'url'        => $val['url'],
								'attachment' => $val['id'],
							);
						}

						break;
				}

			}

			return $files;
		}

		/**
		 * Returns formatted HTML result
		 *
		 * @return [type] [description]
		 */
		public function get_result_html( $field = array(), $files = array() ) {

			if ( ! empty( $field['insert_attachment'] ) ) {
				$result_format = ! empty( $field['value_format'] ) ? $field['value_format'] : 'url';
			} else {
				$result_format = 'url';
			}

			if ( empty( $files ) ) {
				$files = $this->get_files_from_field( $field, $result_format );
			}

			if ( empty( $files ) ) {
				return;
			}

			$format = '<div class="jet-engine-file-upload__file" data-file="%1$s" data-id="%2$s" data-format="%3$s">%4$s<div class="jet-engine-file-upload__file-remove"><svg width="22" height="22" viewBox="0 0 14 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4.375 7H6.125V12.25H4.375V7ZM7.875 7H9.625V12.25H7.875V7ZM10.5 1.75C10.5 1.51302 10.4134 1.30794 10.2402 1.13477C10.0762 0.961589 9.87109 0.875 9.625 0.875H4.375C4.12891 0.875 3.91927 0.961589 3.74609 1.13477C3.58203 1.30794 3.5 1.51302 3.5 1.75V3.5H0V5.25H0.875V14C0.875 14.237 0.957031 14.4421 1.12109 14.6152C1.29427 14.7884 1.50391 14.875 1.75 14.875H12.25C12.4961 14.875 12.7012 14.7884 12.8652 14.6152C13.0384 14.4421 13.125 14.237 13.125 14V5.25H14V3.5H10.5V1.75ZM5.25 2.625H8.75V3.5H5.25V2.625ZM11.375 5.25V13.125H2.625V5.25H11.375Z"></path></svg></div></div>';

			$result = '';

			foreach ( $files as $file ) {

				if ( ! empty( $file['attachment'] ) && ! is_wp_error( $file['attachment'] ) ) {
					$attachment = $file['attachment'];
				} else {
					$attachment = 0;
				}

				$img_preview = '';

				$image_exts   = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'svg', 'webp' );
				$img_ext_preg = '!\.(' . join( '|', $image_exts ) . ')$!i';

				if ( preg_match( $img_ext_preg, $file['url'] ) ) {
					$img_preview = sprintf( '<img src="%s" alt="">', $file['url'] );
				}

				$result .= sprintf( $format, $file['url'], $attachment, $result_format, $img_preview );

			}

			return $result;

		}

		public function get_loader() {
			return '<div class="jet-engine-file-upload__loader">' . apply_filters(
				'jet-engine/forms/file-upload/loader',
				'<svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 38 38" stroke="#fff"><g fill="none" fill-rule="evenodd"><g transform="translate(1 1)" stroke-width="2"><circle stroke-opacity=".5" cx="18" cy="18" r="18"/><path d="M36 18c0-9.94-8.06-18-18-18" transform="rotate(137.826 18 18)"><animateTransform attributeName="transform" type="rotate" from="0 18 18" to="360 18 18" dur="1s" repeatCount="indefinite"/></path></g></g></svg>'
			) . '</div>';
		}

		/**
		 * Returns formatted result array
		 *
		 * @param  array  $field [description]
		 * @param  array  $files [description]
		 * @return [type]        [description]
		 */
		public function get_result_value( $field = array(), $files = array() ) {

			if ( ! empty( $field['insert_attachment'] ) ) {
				$format = ! empty( $field['value_format'] ) ? $field['value_format'] : 'url';
			} else {
				$format = 'url';
			}

			if ( empty( $files ) ) {
				$files = $this->get_files_from_field( $field, $format );
			}

			if ( empty( $files ) ) {
				return '';
			}

			$limit  = ! empty( $field['max_files'] ) ? absint( $field['max_files'] ) : 1;
			$limit  = $limit ? $limit : 1;
			$result = array();

			foreach ( $files as $file ) {

				if ( isset( $file['attachment'] ) && ! is_wp_error( $file['attachment'] ) ) {
					$id = $file['attachment'];
				} else {
					$id = false;
				}

				$url = ! empty( $file['url'] ) ? $file['url'] : false;

				switch ( $format ) {
					case 'id':
						if ( 1 < $limit ) {
							$result[] = $id;
						} else {
							$result = $id;
						}
						break;

					case 'url':
						if ( 1 < $limit ) {
							$result[] = $url;
						} else {
							$result = $url;
						}
						break;

					case 'both':
						if ( $url && $id ) {
							if ( 1 < $limit ) {
								$result[] = array(
									'id'  => $id,
									'url' => $url,
								);
							} else {
								$result = array(
									'id'  => $id,
									'url' => $url,
								);
							}
						}
						break;
				}
			}

			return is_array( $result ) ? array_filter( $result ) : $result;

		}

		/**
		 * Returns stringified uploading errors
		 *
		 * @return string
		 */
		public function get_errors_string() {

			if ( empty( $this->errors ) ) {
				return null;
			}

			if ( 1 === count( $this->errors ) ) {
				return $this->errors[0];
			} else {

				$result = '';

				foreach ( $this->errors as $error ) {
					$result .= '- ' . $error . '<br>';
				}

				return $result;

			}

		}

		/**
		 * Resturns max upload size based on field arguments
		 *
		 * @param  array  $args [description]
		 * @return [type]       [description]
		 */
		public function get_max_size_for_field( $args = array() ) {

			$max_size       = wp_max_upload_size();
			$field_max_size = $max_size;

			if ( ! empty( $args['max_size'] ) ) {

				$field_max_size = intval( floatval( $args['max_size'] ) * MB_IN_BYTES );

				if ( $field_max_size > $max_size ) {
					$field_max_size = $max_size;
				}

			}

			return $field_max_size;

		}

		/**
		 * Process files upload
		 *
		 * @param  boolean $files [description]
		 * @return [type]         [description]
		 */
		public function process_upload( $files = false, $settings = array() ) {

			$settings = wp_parse_args( $settings, array(
				'max_size'          => wp_max_upload_size(),
				'max_files'         => 1,
				'insert_attachment' => false,
			) );

			$insert_attachment = filter_var( $settings['insert_attachment'], FILTER_VALIDATE_BOOLEAN );

			if ( ! $files ) {
				$files = $_FILES;
			}

			if ( empty( $files ) || ! is_array( $files ) ) {
				return false;
			}

			if ( count( $files ) > $settings['max_files'] ) {
				wp_send_json_error( $settings['messages']['upload_max_files'] );
			}

			$result = array();
			$index  = 0;

			foreach ( $files as $file ) {

				if ( ! $file['size'] > $settings['max_size'] ) {
					wp_send_json_error( $settings['messages']['upload_max_size'] );
				}

				if ( ! empty( $settings['mime_types'] ) && ! in_array( $file['type'], $settings['mime_types'] ) ) {
					wp_send_json_error( $settings['messages']['upload_mime_types'] );
				}

				$result[] = $this->upload_file( $file, $insert_attachment );

			}

			return $result;

		}

		/**
		 * Upload file
		 *
		 * @return [type] [description]
		 */
		public function upload_file( $file = array(), $insert_attachment = false ) {

			$result = array();

			if ( ! function_exists( 'wp_handle_upload' ) ) {
				include_once ABSPATH . 'wp-admin/includes/file.php';
				include_once ABSPATH . 'wp-admin/includes/media.php';
			}

			add_filter( 'upload_dir', array( $this, 'apply_upload_dir' ) );

			$upload = wp_handle_upload(
				$file,
				array( 'test_form' => false )
			);

			if ( empty( $upload['error'] ) && $insert_attachment ) {

				$filepath   = $upload['file'];
				$attachment = wp_insert_attachment(
					array(
						'guid'           => $upload['url'],
						'post_mime_type' => $upload['type'],
						'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filepath ) ),
						'post_content'   => '',
						'post_status'    => 'publish'
					),
					$filepath,
					0,
					true
				);

				if ( ! is_wp_error( $attachment ) ) {
					$metadata = wp_generate_attachment_metadata( $attachment, $filepath );
					wp_update_attachment_metadata( $attachment, $metadata );

					// Updated an attachment url for compatibility with third party plugins ( Ex.: Performance Lab by WP )
					$upload['url'] = wp_get_attachment_url( $attachment );
				} else {
					$this->errors[] = $attachment->get_error_message();
				}

				$upload['attachment'] = $attachment;

			} elseif ( ! empty( $upload['error'] ) ) {
				$this->errors[] = $upload['error'];
			}

			remove_filter( 'upload_dir', array( $this, 'apply_upload_dir' ) );

			return $upload;

		}

		/**
		 * Returns upload subdirectory
		 *
		 * @return [type] [description]
		 */
		public function get_upload_dir() {

			$user_id       = get_current_user_id();
			$user_dir_name = $user_id ? $user_id : 'guest';
			$user_dir_name = apply_filters( 'jet-engine/forms/file-upload/user-dir-name', $user_dir_name );

			return $this->upload_base() . '/' . $user_dir_name;
		}

		/**
		 * Returns upload base directory
		 *
		 * @return [type] [description]
		 */
		public function upload_base() {
			return apply_filters( 'jet-engine/forms/file-upload/dir', 'jet-engine-forms' );
		}

		/**
		 * Change upload directory for JetEngine uploads
		 *
		 * @param  [type] $pathdata [description]
		 * @return [type]           [description]
		 */
		public function apply_upload_dir( $pathdata ) {

			$dir = $this->get_upload_dir();

			if ( empty( $pathdata['subdir'] ) ) {
				$pathdata['path']   = $pathdata['path'] . '/' . $dir;
				$pathdata['url']    = $pathdata['url'] . '/' . $dir;
				$pathdata['subdir'] = '/' . $dir;
			} else {
				$new_subdir         = '/' . $dir . $pathdata['subdir'];
				$pathdata['path']   = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['path'] );
				$pathdata['url']    = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['url'] );
				$pathdata['subdir'] = $new_subdir;
			}

			return $pathdata;

		}

		/**
		 * Register form-specific assets
		 *
		 * @return [type] [description]
		 */
		public function register_assets() {

			wp_register_script(
				'jet-engine-sortable',
				jet_engine()->plugin_url( 'assets/lib/jquery-sortable/sortable.js' ),
				array(),
				jet_engine()->get_version(),
				true
			);

			wp_register_script(
				'jet-engine-file-upload',
				jet_engine()->plugin_url( 'assets/js/file-upload.js' ),
				array( 'jet-engine-frontend', 'jet-engine-sortable' ),
				jet_engine()->get_version(),
				true
			);

		}

		/**
		 * Enqueue upload file JS
		 * @return [type] [description]
		 */
		public function enqueue_upload_script() {

			$upload_limit = ! empty( $this->custom_messages['upload_limit'] ) ? $this->custom_messages['upload_limit'] : __( 'Maximum upload files limit is reached', 'jet-engine' );
			$file_type = ! empty( $this->custom_messages['file_type'] ) ? $this->custom_messages['file_type'] : __( 'File type is not supported', 'jet-engine' );
			$file_size = ! empty( $this->custom_messages['file_size'] ) ? $this->custom_messages['file_size'] : __( 'Maximum upload file size is exceeded', 'jet-engine' );

			wp_localize_script( 'jet-engine-file-upload', 'JetEngineFileUploadConfig', array(
				'ajaxurl'         => esc_url( admin_url( 'admin-ajax.php' ) ),
				'action'          => 'jet-engine-forms-upload-file',
				'nonce'           => wp_create_nonce( $this->nonce_key ),
				'max_upload_size' => wp_max_upload_size(),
				'errors'          => array(
					'upload_limit' => $upload_limit,
					'file_type'    => $file_type,
					'file_size'    => $file_size,
					'internal'     => __( 'Internal error! Please contact website administrator.', 'jet-engine' ),

				),
			) );

			wp_enqueue_script( 'jet-engine-file-upload' );

		}

		public function ensure_media_js( $content = null, $popup_data = array() ) {
			
			ob_start();
			jet_engine()->frontend->frontend_scripts();
			$this->register_assets();
			$this->enqueue_upload_script();
			wp_scripts()->done[] = 'jet-engine-frontend';
			wp_scripts()->print_scripts( 'jet-engine-file-upload' );
			
			return $content . ob_get_clean();
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public static function instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

	}

}