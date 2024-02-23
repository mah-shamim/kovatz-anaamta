<?php
/**
 * Base data controller class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Base_Data' ) ) {

	/**
	 * Define Jet_Engine_Base_Data class
	 */
	abstract class Jet_Engine_Base_Data {

		/**
		 * DB manager instance
		 *
		 * @var Jet_Engine_DB
		 */
		public $db = null;

		/**
		 * Parent manager instance
		 *
		 * @var Jet_Engine_CPT
		 */
		public $parent = null;

		/**
		 * Table name
		 *
		 * @var string
		 */
		public $table = null;

		/**
		 * DB query arguments
		 *
		 * @var array
		 */
		public $query_args = array();

		/**
		 * Table format
		 *
		 * @var string
		 */
		public $table_format = array();

		/**
		 * Edit slug
		 *
		 * @var string
		 */
		public $edit = 'edit';

		public $raw = false;

		public $request = array();

		/**
		 * Constructir for the class
		 */
		function __construct( $parent ) {
			$this->db     = jet_engine()->db;
			$this->parent = $parent;
		}

		/**
		 * Set current requset data
		 *
		 * @param [type] $request [description]
		 */
		public function set_request( $request ) {
			$this->request = $request;
		}

		/**
		 * Create new post type
		 *
		 * @return void
		 */
		public function create_item( $redirect = true ) {

			if ( ! current_user_can( 'manage_options' ) ) {
				$this->parent->add_notice(
					'error',
					__( 'You don\'t have permissions to do this', 'jet-engine' )
				);
				return;
			}

			if ( ! $this->sanitize_item_request() ) {
				return;
			}

			$item = $this->sanitize_item_from_request();
			$id   = $this->update_item_in_db( $item );

			$this->after_item_update( $item, true );

			if ( ! $id ) {
				$this->parent->add_notice(
					'error',
					__( 'Couldn\'t create item', 'jet-engine' )
				);
				return;
			}

			flush_rewrite_rules();

			if ( method_exists( $this->parent, 'get_page_link' ) ) {
				$redirect_url = add_query_arg(
					array(
						'id'     => $id,
						'notice' => 'added',
					),
					$this->parent->get_page_link( $this->edit )
				);
			} else {
				$redirect_url = false;
			}

			if ( $redirect && $redirect_url ) {

				wp_redirect( $redirect_url );

				die();

			} else {
				return $id;
			}

		}

		/**
		 * Update current item in data base
		 *
		 * @param  [type] $item [description]
		 * @return [type]       [description]
		 */
		public function update_item_in_db( $item ) {

			if ( ! empty( $this->query_args ) ) {
				$item = array_merge( $item, $this->query_args );
			}

			return $this->db->update( $this->table, $item, $this->table_format );
		}

		/**
		 * Rewrite this function in the child class to perform any actions on item update
		 */
		public function before_item_update( $item ) {}

		/**
		 * Rewrite this function in the child class to perform any actions on item update
		 */
		public function after_item_update( $item = array(), $is_new = false ) {}

		/**
		 * Rewrite this function in the child class to perform any actions on item delete
		 */
		public function before_item_delete( $item_id ) {}

		/**
		 * Update post post type
		 *
		 * @return void
		 */
		public function delete_item( $redirect = true ) {

			if ( ! current_user_can( 'manage_options' ) ) {

				$this->parent->add_notice(
					'error',
					__( 'You don\'t have permissions to do this', 'jet-engine' )
				);

				return;
			}

			$id = isset( $this->request['id'] ) ? esc_attr( $this->request['id'] ) : false;

			if ( ! $id ) {

				$this->parent->add_notice(
					'error',
					__( 'Please provide item ID to delete', 'jet-engine' )
				);

				return;

			}

			$this->before_item_delete( $id );

			$this->db->delete( $this->table, array( 'id' => $id ), array( '%d' ) );

			flush_rewrite_rules();

			if ( $redirect ) {
				if ( method_exists( $this->parent, 'get_page_link' ) ) {
					wp_redirect( $this->parent->get_page_link() );
					die();
				}
			} else {
				return true;
			}

		}

		/**
		 * Update post post type
		 *
		 * @return void
		 */
		public function edit_item( $redirect = true ) {

			if ( ! current_user_can( 'manage_options' ) ) {
				$this->parent->add_notice(
					'error',
					__( 'You don\'t have permissions to do this', 'jet-engine' )
				);
				return;
			}

			if ( ! $this->sanitize_item_request() ) {
				return;
			}

			$id = isset( $this->request['id'] ) ? esc_attr( $this->request['id'] ) : false;

			if ( ! $id ) {

				$this->parent->add_notice(
					'error',
					__( 'Item ID not passed', 'jet-engine' )
				);

				return;
			}

			$item       = $this->sanitize_item_from_request();
			$item['id'] = $id;

			$this->before_item_update( $item );

			$id = $this->update_item_in_db( $item );

			$this->after_item_update( $item );

			if ( ! $id ) {
				$this->parent->add_notice(
					'error',
					__( 'Couldn\'t update item', 'jet-engine' )
				);
				return;
			}

			flush_rewrite_rules();

			if ( $redirect ) {

				if ( method_exists( $this->parent, 'get_page_link' ) ) {

					wp_redirect( add_query_arg(
						array( 'id' => $id ),
						$this->parent->get_page_link( $this->edit )
					) );

					die();

				}

			} else {
				return true;
			}

		}

		/**
		 * Sanitizr post type request
		 *
		 * @return void
		 */
		public function sanitize_item_request() {

			$valid = true;

			if ( empty( $this->request['slug'] ) ) {
				$valid = false;
				$this->parent->add_notice(
					'error',
					__( 'Please set post type slug', 'jet-engine' )
				);
			}

			if ( empty( $this->request['name'] ) ) {
				$valid = false;
				$this->parent->add_notice(
					'error',
					__( 'Please set post type name', 'jet-engine' )
				);
			}

			if ( isset( $this->request['slug'] ) && in_array( $this->request['slug'], $this->items_blacklist() ) ) {
				$valid = false;
				$this->parent->add_notice(
					'error',
					__( 'Please change post type slug. Current is reserved for WordPress needs', 'jet-engine' )
				);
			}

			/**
			 * @todo  fix validation
			 */

			return $valid;

		}

		/**
		 * Sanizitze slug
		 *
		 * @param  [type] $slug [description]
		 * @return [type]       [description]
		 */
		public function sanitize_slug( $slug ) {

			$slug = esc_attr( $slug );
			$slug = strtolower( $slug );
			$slug = remove_accents( $slug );
			$slug = preg_replace( '/[^a-z0-9\s\-\_]/', '', $slug );
			$slug = str_replace( ' ', '-', $slug );

			return $slug;

		}

		/**
		 * Ensure that required database table is exists, create if not.
		 *
		 * @return void
		 */
		public function ensure_db_table() {

			if ( ! $this->db->is_table_exists( $this->table ) ) {
				$this->db->create_table( $this->table );
			}

		}

		/**
		 * Retrieve post for edit
		 *
		 * @return array
		 */
		public function get_item_for_edit( $id ) {

			$item = $this->db->query(
				$this->table,
				array( 'id' => $id ),
				array( $this, 'filter_item_for_edit' )
			);

			if ( ! empty( $item ) ) {
				return $item[0];
			} else {
				return false;
			}

		}

		/**
		 * Sanitize meta fields
		 *
		 * @param  [type] $meta_fields [description]
		 * @return [type]              [description]
		 */
		public function sanitize_meta_fields( $meta_fields ) {

			foreach ( $meta_fields as $key => $field ) {

				// If name is empty - create it from title, else - santize it
				if ( empty( $field['name'] ) && isset( $field['label'] ) ) {
					$field['name'] = $this->sanitize_slug( $field['label'] );
				} elseif ( empty( $field['name'] ) && isset( $field['title'] ) ) {
					$field['name'] = $this->sanitize_slug( $field['title'] );
				} else {
					$field['name'] = $this->sanitize_slug( $field['name'] );
				}

				// If still empty - create random name
				if ( empty( $field['name'] ) ) {
					$field['name'] = '_field_' . rand( 10000, 99999 );
				}

				// If name in blak list - add underscore at start
				if ( in_array( $field['name'], $this->meta_blacklist() ) ) {
					$meta_fields[ $key ]['name'] = '_' . $field['name'];
				} else {
					$meta_fields[ $key ]['name'] = $field['name'];
				}

				// migrate legacy options_from_glossary option to new options_source
				if ( ! isset( $field['options_source'] ) && ! empty( $field['options_from_glossary'] ) ) {
					$field['options_source'] = 'glossary';
					$meta_fields[ $key ] = $field;
				}
			}

			return $meta_fields;

		}

		/**
		 * Returns post type in prepared for register format
		 *
		 * @return array
		 */
		public function get_item_for_register() {

			return $this->db->query(
				$this->table,
				$this->query_args,
				array( $this, '_filter_item_for_register' )
			);

		}
		
		public function _filter_item_for_register( $item ) {

			$result = $this->filter_item_for_register( $item );
			$result['id'] = $item['id'];

			return $result;

		}

		/**
		 * Returns blacklisted meta fields slugs
		 *
		 * @return array
		 */
		public function meta_blacklist() {
			return array(
				'_wpnonce',
				'_wp_http_referer',
				'user_ID',
				'action',
				'originalaction',
				'post_author',
				'post_type',
				'original_post_status',
				'referredby',
				'_wp_original_http_referer',
				'post_ID',
				'meta-box-order-nonce',
				'closedpostboxesnonce',
				'post_title',
				'samplepermalinknonce',
				'content',
				'wp-preview',
				'hidden_post_status',
				'post_status',
				'hidden_post_password',
				'hidden_post_visibility',
				'visibility',
				'post_password',
				'mm',
				'jj',
				'aa',
				'hh',
				'mn',
				'ss',
				'hidden_mm',
				'cur_mm',
				'hidden_jj',
				'cur_jj',
				'hidden_aa',
				'cur_aa',
				'hidden_hh',
				'cur_hh',
				'hidden_mn',
				'cur_mn',
				'original_publish',
				'save',
				'post_format',
				'tax_input',
				'parent_id',
				'menu_order',
				'_thumbnail_id',
				'meta',
				'excerpt',
				'trackback_url',
				'_ajax_nonce',
				'metakeyselect',
				'metakeyinput',
				'metavalue',
				'advanced_view',
				'comment_status',
				'ping_status',
				'post_name',
				'post_author_override',
				'post_mime_type',
				'ID',
				'post_content',
				'post_excerpt',
				'post_parent',
				'to_ping',
				'screen',
				'taxonomy',
				'action',
				'tag-name',
				'slug',
				'description',
				'general',
				'advanced',
			);
		}

		/**
		 * Returns items by args without filtering
		 *
		 * @return array
		 */
		public function get_raw( $args = array() ) {

			if ( ! $this->raw ) {

				if ( ! empty( $this->query_args ) ) {
					$args = array_merge( $args, $this->query_args );
				}

				$this->raw = $this->db->query( $this->table, $args );
			}

			return $this->raw;
		}

		/**
		 * Reset raw data cache
		 */
		public function reset_raw_cache() {
			$this->raw = false;
		}

		/**
		 * Query post types
		 *
		 * @return array
		 */
		public function get_items() {
			return $types = $this->db->query( $this->table, $this->query_args );
		}

		/**
		 * Return totals post types count
		 *
		 * @return int
		 */
		public function total_items() {
			return $this->db->count( $this->table );
		}

		/**
		 * Prepare post data from request to write into database
		 *
		 * @return array
		 */
		abstract public function sanitize_item_from_request();


		/**
		 * Filter post type for register
		 *
		 * @return array
		 */
		abstract public function filter_item_for_register( $item );

		/**
		 * Filter post type for edit
		 *
		 * @return array
		 */
		abstract public function filter_item_for_edit( $item );

		/**
		 * Returns blacklisted post types slugs
		 *
		 * @return array
		 */
		abstract public function items_blacklist();

	}

}
