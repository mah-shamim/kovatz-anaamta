<?php
/**
 * Revisions manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT_Revisions' ) ) {

	/**
	 * Define Jet_Engine_CPT_Revisions class
	 */
	class Jet_Engine_CPT_Revisions {

		public $post_type  = null;
		public $field      = null;
		public $field_name = null;

		public $post_meta_instance = null;

		public function __construct( $post_type = '', $field = array() ) {

			$this->post_type  = $post_type;
			$this->field      = $field;
			$this->field_name = $field['name'];

			$support_revisions = post_type_supports( $this->post_type, 'revisions' );

			if ( ! $support_revisions ) {
				add_post_type_support( $this->post_type, 'revisions' );
			}

			add_filter( 'wp_save_post_revision_post_has_changed', array( $this, 'check_post_has_changed' ), 10, 3 );
			add_filter( '_wp_post_revision_fields',               array( $this, 'add_revision_field' ), 10, 2 );

			add_action( '_wp_put_post_revision',    array( $this, 'save_revision' ) );
			add_action( 'wp_restore_post_revision', array( $this, 'restore_revision' ), 10, 2 );
		}

		public function check_post_has_changed( $post_has_changed, $last_revision, $post ) {

			if ( ! $this->is_allowed_post( $post ) ) {
				return $post_has_changed;
			}

			if ( $post_has_changed ) {
				return $post_has_changed;
			}

			$current_value       = $this->get_current_field_value();
			$last_revision_value = get_post_meta( $last_revision->ID, $this->field_name, true );

			if ( $current_value !== $last_revision_value ) {
				$post_has_changed = true;
			}

			return $post_has_changed;
		}

		public function is_allowed_post( $post ) {

			if ( $this->post_type !== get_post_type( $post ) ) {
				return false;
			}

			if ( ! isset( $_POST[ $this->field_name ] ) ) {
				return false;
			}

			return true;
		}

		public function add_revision_field( $fields, $post ) {

			if ( ! $this->is_revision_screen() && ! $this->is_diff_request() ) {
				return $fields;
			}

			if ( ! empty( $_GET['action'] ) && 'restore' === $_GET['action'] ) {
				return $fields;
			}

			if ( is_array( $post ) ) {
				$post_id = $post['ID'];
			} elseif ( is_object( $post ) ) {
				$post_id = $post->ID;
			} else {
				$post_id = false;
			}

			if ( ! $post_id ) {
				return $fields;
			}

			if ( $this->post_type !== get_post_type( $post_id ) ) {
				return $fields;
			}

			$fields[ $this->field_name ] = $this->field['title'];

			add_filter( '_wp_post_revision_field_' . $this->field_name, array( $this, 'wp_post_revision_field' ), 10, 4 );

			return $fields;
		}

		public function is_revision_screen() {

			if ( ! function_exists( 'get_current_screen' ) ) {
				return false;
			}

			$current_screen = get_current_screen();

			if ( empty( $current_screen ) || 'revision' !== $current_screen->id ) {
				return false;
			}

			return true;
		}

		public function is_diff_request() {
			return ! empty( $_REQUEST['action'] ) && 'get-revision-diffs' === $_REQUEST['action'];
		}

		public function wp_post_revision_field( $value, $field_name, $post, $direction ) {

			if ( empty( $value ) ) {
				return $value;
			}

			if ( is_array( $value ) ) {
				$value = json_encode( $value );
			}

			return $value;
		}

		public function save_revision( $revision_id ) {

			$parent_id = wp_is_post_revision( $revision_id );

			if ( ! $parent_id ) {
				return;
			}

			if ( $this->post_type !== get_post_type( $parent_id ) ) {
				return;
			}

			if ( isset( $_POST[ $this->field_name ] ) ) {
				$value = $this->get_current_field_value();
			} else {
				$value = get_post_meta( $parent_id, $this->field_name, true );
			}

			update_metadata( 'post', $revision_id, $this->field_name, $value );
		}

		public function get_post_meta_instance() {

			if ( null !== $this->post_meta_instance ) {
				return $this->post_meta_instance;
			}

			$this->post_meta_instance = new Cherry_X_Post_Meta();
			$this->post_meta_instance->args = array(
				'fields' => array(
					$this->field_name => $this->field,
				),
			);

			return $this->post_meta_instance;
		}

		public function get_current_field_value() {

			$post_meta_instance = $this->get_post_meta_instance();

			$value = $post_meta_instance->sanitize_meta( $this->field_name, $_POST[ $this->field_name ] );

			if ( 'textarea' === $this->field['type'] && false === strpos( $value, "\\" ) ) {
				$value = wp_slash( $value );
			}

			return $value;
		}

		public function restore_revision( $post_id, $revision_id ) {

			if ( $this->post_type !== get_post_type( $post_id ) ) {
				return;
			}

			$revision_value = get_post_meta( $revision_id, $this->field_name, true );

			update_post_meta( $post_id, $this->field_name, $revision_value );
		}

	}
}
