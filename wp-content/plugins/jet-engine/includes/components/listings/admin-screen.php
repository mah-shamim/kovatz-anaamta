<?php
/**
 * Handle listing admin columns and settings
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Engine_Listing_Admin_Screen {

	protected $post_type = null;

	public function __construct( $post_type ) {
	
		$this->post_type = $post_type;

		add_action( 'wp_ajax_jet_engine_get_edit_listing_popup', array( $this, 'get_edit_listing_popup' ) );
		add_action( 'wp_ajax_jet_engine_save_listing_settings', array( $this, 'save_template' ) );
		add_action( 'admin_action_jet_create_new_listing', array( $this, 'save_template' ) );

		if ( is_admin() ) {
			$this->register_admin_columns();
		}
	}

	/**
	 * Register lisitng admin columns
	 *
	 * @return [type] [description]
	 */
	public function register_admin_columns() {

		if ( ! class_exists( 'Jet_Engine_CPT_Admin_Columns' ) ) {
			require_once jet_engine()->plugin_path( 'includes/components/post-types/admin-columns.php' );
		}

		new Jet_Engine_CPT_Admin_Columns( $this->post_type, array(
			array(
				'type'     => 'custom_callback',
				'title'    => __( 'Source', 'jet-engine' ),
				'callback' => array( $this, 'get_source' ),
				'position' => 2,
			),
			array(
				'type'     => 'custom_callback',
				'title'    => __( 'For post type/taxonomy', 'jet-engine' ),
				'callback' => array( $this, 'get_type' ),
				'position' => 3,
			),
		) );

	}

	public function send_request_error( $message = '' ) {

		if ( $this->is_ajax_request() ) {
			wp_send_json_error( $message );
		} else {
			wp_die( $message, esc_html__( 'Error', 'jet-engine' ) );
		}

	}

	public function is_ajax_request() {
		return $is_ajax_request = ! empty( $_REQUEST['_is_ajax_form'] ) ? filter_var( $_REQUEST['_is_ajax_form'], FILTER_VALIDATE_BOOLEAN ) : false;
	}

	/**
	 * Save template by given data. Could be used to update listing from anywhere.
	 * Ensures hooks consistency
	 */
	public function update_template( $post_data, $view_type ) {

		/**
		 * Filter listing template data before create/update listing
		 * @var array
		 */
		$post_data = apply_filters( 'jet-engine/templates/create/data', $post_data );

		if ( ! empty( $post_data['ID'] ) ) {
			$template_id = wp_update_post( $post_data );
		} else {
			$template_id = wp_insert_post( $post_data );
		}

		if ( ! $template_id ) {
			return false;
		}

		/**
		 * Global created hook
		 */
		do_action( 'jet-engine/templates/created', $template_id, $post_data );

		/**
		 * View-specific created hook
		 */
		do_action( 'jet-engine/templates/created/' . $view_type, $template_id, $post_data );

		return $template_id;

	}

	/**
	 * Create new template
	 *
	 * @return [type] [description]
	 */
	public function save_template() {

		$nonce_action = $this->get_nonce_action();

		if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], $nonce_action ) ) {
			$this->send_request_error( __( 'Nonce validation failed', 'jet-engine' ) );
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			$this->send_request_error( esc_html__( 'You don\'t have permissions to do this', 'jet-engine' ) );
		}

		$post_data = array(
			'post_type'   => $this->post_type,
			'post_status' => 'publish',
			'meta_input'  => array(),
		);

		$is_edit = false;
		$default_view = 'elementor';

		if ( ! empty( $_REQUEST['_listing_id'] ) ) {
			
			if ( ! current_user_can( 'edit_post', $_REQUEST['_listing_id'] ) ) {
				$this->send_request_error( esc_html__( 'You don\'t have permissions to do this', 'jet-engine' ) );
			}

			$is_edit         = true;
			$post_data['ID'] = absint( $_REQUEST['_listing_id'] );
			$default_view    = get_post_meta( $_REQUEST['_listing_id'], '_listing_type', true );

		}

		$title = isset( $_REQUEST['template_name'] ) ? esc_attr( $_REQUEST['template_name'] ) : '';

		if ( $title ) {
			$post_data['post_title'] = $title;
		}

		$source     = ! empty( $_REQUEST['listing_source'] ) ? esc_attr( $_REQUEST['listing_source'] ) : 'posts';
		$post_type  = ! empty( $_REQUEST['listing_post_type'] ) ? esc_attr( $_REQUEST['listing_post_type'] ) : '';
		$tax        = ! empty( $_REQUEST['listing_tax'] ) ? esc_attr( $_REQUEST['listing_tax'] ) : '';
		$rep_source = ! empty( $_REQUEST['repeater_source'] ) ? esc_attr( $_REQUEST['repeater_source'] ) : '';
		$rep_field  = ! empty( $_REQUEST['repeater_field'] ) ? esc_attr( $_REQUEST['repeater_field'] ) : '';
		$rep_option = ! empty( $_REQUEST['repeater_option'] ) ? esc_attr( $_REQUEST['repeater_option'] ) : '';
		$view_type  = ! empty( $_REQUEST['listing_view_type'] ) ? $_REQUEST['listing_view_type'] : $default_view;

		$listing = array(
			'source'    => $source,
			'post_type' => $post_type,
			'tax'       => $tax,
		);

		$post_data['meta_input']['_listing_data'] = $listing;
		$post_data['meta_input']['_listing_type'] = $view_type;
		$post_data['meta_input']['_elementor_page_settings']['listing_source'] = $source;
		$post_data['meta_input']['_elementor_page_settings']['listing_post_type'] = $post_type;
		$post_data['meta_input']['_elementor_page_settings']['listing_tax'] = $tax;
		$post_data['meta_input']['_elementor_page_settings']['repeater_source'] = $rep_source;
		$post_data['meta_input']['_elementor_page_settings']['repeater_field'] = $rep_field;
		$post_data['meta_input']['_elementor_page_settings']['repeater_option'] = $rep_option;

		if ( 'elementor' === $view_type && $this->is_ajax_request() && ! $is_edit ) {
			$post_data['meta_input']['_elementor_data'] = '[{"id":"d75c8e8","elType":"section","settings":{"jedv_conditions":[{"_id":"b441260"}]},"elements":[{"id":"31b3d2a","elType":"column","settings":{"_column_size":100,"_inline_size":null,"jedv_conditions":[{"_id":"8e60841"}]},"elements":[{"id":"2c37cde","elType":"widget","settings":{"dynamic_excerpt_more":"...","date_format":"F j, Y","num_dec_point":".","num_thousands_sep":",","multiselect_delimiter":", ","dynamic_field_format":"%s","jedv_conditions":[{"_id":"a47f557"}]},"elements":[],"widgetType":"jet-listing-dynamic-field"}],"isInner":false}],"isInner":false}]';
		}

		$template_id = $this->update_template( $post_data, $view_type );

		if ( ! $template_id ) {
			$this->send_request_error( esc_html__( 'Can\'t create template. Please try again', 'jet-engine' ) );
		}

		$redirect = $this->get_edit_url( $view_type, $template_id );

		if ( ! $redirect ) {
			$this->send_request_error( __( 'Listing view instance is not found', 'jet-engine' ) );
		}

		if ( $this->is_ajax_request() ) {
			wp_send_json_success( array(
				'id'    => $template_id,
				'title' => $title,
			) );
		} elseif ( $is_edit ) {
			
			$response    = array();
			$open_editor = ! empty( $_REQUEST['_open_editor'] ) ? filter_var( $_REQUEST['_open_editor'], FILTER_VALIDATE_BOOLEAN ) : false;

			if ( $open_editor ) {
				$response['redirect'] = $redirect;
			}

			wp_send_json_success( $response );

		} else {
			wp_redirect( $redirect );
			die();
		}

	}

	public function get_edit_url( $view_type, $template_id ) {

		$redirect  = false;

		switch ( $view_type ) {
			case 'elementor':
				$redirect = jet_engine()->elementor_views->get_redirect_url( $template_id );
				break;

			case 'blocks':
				$redirect = jet_engine()->blocks_views->get_redirect_url( $template_id );
				break;

			default:
				$redirect = apply_filters( 'jet-engine/templates/edit-url/' . $view_type, $redirect, $template_id );
				break;
		}

		return $redirect;

	}

	/**
	 * Returns listing source
	 *
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	public function get_source( $post_id ) {

		$settings = get_post_meta( $post_id, '_elementor_page_settings', true );

		if ( empty( $settings ) || empty( $settings['listing_source'] ) ) {
			return 'Posts' . $this->get_settings_button( $post_id );
		}

		return ucfirst( $settings['listing_source'] ) . $this->get_settings_button( $post_id );

	}

	/**
	 * Returns listing content type
	 *
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	public function get_type( $post_id ) {

		$settings = get_post_meta( $post_id, '_elementor_page_settings', true );

		if ( empty( $settings ) ) {
			return 'Posts';
		}

		$source = ! empty( $settings['listing_source'] ) ? $settings['listing_source'] : 'posts';
		$result = '--';

		switch ( $source ) {

			case 'posts':
				$post_type = ! empty( $settings['listing_post_type'] ) ? $settings['listing_post_type'] : 'post';
				$object    = get_post_type_object( $post_type );

				if ( $object ) {
					$result = $object->labels->name;
				}
				break;

			case 'terms':
				$tax = ! empty( $settings['listing_tax'] ) ? $settings['listing_tax'] : false;

				if ( $tax ) {
					$object = get_taxonomy( $tax );
					if ( $object ) {
						$result = $object->labels->name;
					}
				}

				break;

			default:
				$result = apply_filters(
					'jet-engine/templates/admin-columns/type/' . $source, 
					$result, $settings, $post_id
				);
				break;
		}

		return $result;

	}

	public function get_settings_button( $post_id ) {
		return sprintf(
			'<button type="button" class="button button-small jet-engine-listing-edit-settings" data-listing-id="%1$s">%2$s<span class="spinner"></span></button>',
			$post_id,
			__( 'Edit Listing Settings', 'jet-engine' )
		);
	}

	public function get_listing_popup( $listing_id = false ) {
		
		$action = add_query_arg(
			array(
				'action' => 'jet_create_new_listing',
				'_nonce' => wp_create_nonce( $this->get_nonce_action() ),
			),
			esc_url( admin_url( 'admin.php' ) )
		);

		$sources = jet_engine()->listings->post_type->get_listing_item_sources();
		$views   = jet_engine()->listings->post_type->get_listing_views();
		$data    = array( 'main_popup' => true );

		if ( $listing_id ) {
			$listing = jet_engine()->listings->get_new_doc( [], $listing_id );
			$data    = $listing->get_settings();
		}

		ob_start();
		include jet_engine()->get_template( 'admin/listings-popup.php' );
		return ob_get_clean();

	}

	public function get_edit_listing_popup() {

		$nonce_action = $this->get_nonce_action();

		if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], $nonce_action ) ) {
			wp_send_json_error( __( 'Nonce validation failed', 'jet-engine' ) );
		}

		$listing_id = ! empty( $_REQUEST['listing_id'] ) ? absint( $_REQUEST['listing_id'] ) : false;

		if ( ! $listing_id ) {
			wp_send_json_error( 'Listing ID not found in the request' );
		}

		if ( ! current_user_can( 'edit_post', $listing_id ) ) {
			wp_send_json_error( 'You don`t have permissions to edit this Listing item' );
		}

		wp_send_json_success( $this->get_listing_popup( $listing_id ) );

	}

	public function get_nonce_action() {
		return jet_engine()->listings->post_type->get_nonce_action();
	}

}
