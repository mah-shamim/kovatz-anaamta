<?php
/**
 * CPT data controller class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT_Tax_Data' ) ) {

	/**
	 * Define Jet_Engine_CPT_Tax_Data class
	 */
	class Jet_Engine_CPT_Tax_Data extends Jet_Engine_Base_Data {

		/**
		 * Table name
		 *
		 * @var string
		 */
		public $table = 'taxonomies';

		/**
		 * Table format
		 *
		 * @var string
		 */
		public $table_format = array( '%s', '%s', '%s', '%s', '%s', '%s' );

		/**
		 * Edit slug
		 *
		 * @var string
		 */
		public $edit = 'edit-tax';

		/**
		 * Query arguments
		 *
		 * @var array
		 */
		public $query_args = array(
			'status' => 'publish',
		);

		/**
		 * Sanitizr post type request
		 *
		 * @return void
		 */
		public function sanitize_item_request() {

			$valid   = true;
			$request = $this->request;

			if ( empty( $request['slug'] ) ) {
				$valid = false;
				$this->parent->add_notice(
					'error',
					__( 'Please set taxonomy slug', 'jet-engine' )
				);
			}

			if ( empty( $request['slug'] ) ) {
				$valid = false;
				$this->parent->add_notice(
					'error',
					__( 'Please set taxonomy name', 'jet-engine' )
				);
			}

			if ( empty( $request['object_type'] ) ) {
				$valid = false;
				$this->parent->add_notice(
					'error',
					__( 'Please set post type for taxonomy', 'jet-engine' )
				);
			}

			if ( isset( $request['slug'] ) && in_array( $request['slug'], $this->items_blacklist() ) ) {
				$valid = false;
				$this->parent->add_notice(
					'error',
					__( 'Please change taxonomy slug. Current is reserved for WordPress needs', 'jet-engine' )
				);
			}

			return $valid;

		}

		/**
		 * Returns blacklisted post types slugs
		 *
		 * @return array
		 */
		public function items_blacklist() {
			return array(
				'attachment',
				'attachment_id',
				'author',
				'author_name',
				'calendar',
				'cat',
				'category',
				'category__and',
				'category__in',
				'category__not_in',
				'category_name',
				'comments_per_page',
				'comments_popup',
				'customize_messenger_channel',
				'customized',
				'cpage',
				'day',
				'debug',
				'error',
				'exact',
				'feed',
				'fields',
				'hour',
				'link_category',
				'm',
				'minute',
				'monthnum',
				'more',
				'name',
				'nav_menu',
				'nonce',
				'nopaging',
				'offset',
				'order',
				'orderby',
				'p',
				'page',
				'page_id',
				'paged',
				'pagename',
				'pb',
				'perm',
				'post',
				'post__in',
				'post__not_in',
				'post_format',
				'post_mime_type',
				'post_status',
				'post_tag',
				'post_type',
				'posts',
				'posts_per_archive_page',
				'posts_per_page',
				'preview',
				'robots',
				's',
				'search',
				'second',
				'sentence',
				'showposts',
				'static',
				'subpost',
				'subpost_id',
				'tag',
				'tag__and',
				'tag__in',
				'tag__not_in',
				'tag_id',
				'tag_slug__and',
				'tag_slug__in',
				'taxonomy',
				'tb',
				'term',
				'theme',
				'themes',
				'type',
				'w',
				'withcomments',
				'withoutcomments',
				'year',
				'content',
				'general',
			);
		}

		/**
		 * Returns blacklisted post types slugs
		 *
		 * @return array
		 */
		public function meta_blacklist() {
			return array(
				'action',
				'screen',
				'taxonomy',
				'action',
				'post_type',
				'_wp_http_referer',
				'tag-name',
				'slug',
				'description',
			);
		}

		/**
		 * Prepare post data from request to write into database
		 *
		 * @return array
		 */
		public function sanitize_item_from_request( $is_built_in = false ) {

			$request = $this->request;

			if ( $is_built_in ) {
				$status = 'built-in';;
			} else {
				$status = 'publish';
			}

			$result = array(
				'slug'        => '',
				'status'      => 'publish',
				'labels'      => array(),
				'args'        => array(),
				'meta_fields' => array(),
			);

			if ( $is_built_in && ! empty( $request['id'] ) ) {
				$result['id'] = absint( $request['id'] );
			}

			if ( $is_built_in ) {
				$slug = ! empty( $request['slug'] ) ? $request['slug'] : false;
			} else {
				$slug = ! empty( $request['slug'] ) ? $this->sanitize_slug( $request['slug'] ) : false;
			}

			$name = ! empty( $request['name'] ) ? sanitize_text_field( $request['name'] ) : false;

			if ( ! $slug ) {
				return false;
			}

			$labels = array();

			if ( $is_built_in ) {

				if ( $name ) {
					$labels = array(
						'name' => $name,
					);
				} else {
					$name = array();
				}

			} else {
				$labels = array(
					'name' => $name,
				);
			}

			$labels_list = array(
				'name',
				'singular_name',
				'menu_name',
				'all_items',
				'edit_item',
				'view_item',
				'update_item',
				'add_new_item',
				'new_item_name',
				'parent_item',
				'parent_item_colon',
				'search_items',
				'popular_items',
				'separate_items_with_commas',
				'add_or_remove_items',
				'choose_from_most_used',
				'not_found',
				'back_to_items',
			);

			foreach ( $labels_list as $label_key ) {
				if ( ! empty( $request[ $label_key ] ) ) {
					$labels[ $label_key ] = $request[ $label_key ];
				}
			}

			$args        = array();
			$ensure_bool = array(
				'public',
				'publicly_queryable',
				'show_ui',
				'show_in_menu',
				'show_in_nav_menus',
				'show_in_rest',
				'rewrite',
				'hierarchical',
				'with_front',
				'show_edit_link',
				'hide_field_names',
				'rewrite_hierarchical',
			);

			foreach ( $ensure_bool as $key ) {
				if ( $is_built_in ) {
					if ( isset( $request[ $key ] ) ) {
						$args[ $key ] = filter_var( $request[ $key ], FILTER_VALIDATE_BOOLEAN );
					}
				} else {
					$args[ $key ] = ! empty( $request[ $key ] )
									? filter_var( $request[ $key ], FILTER_VALIDATE_BOOLEAN )
									: false;
				}
			}

			$regular_args = array(
				'query_var'       => '',
				'rewrite_slug'    => $slug,
				'capability_type' => '',
				'description'     => '',
			);

			foreach ( $regular_args as $key => $default ) {
				if ( $is_built_in ) {
					if ( isset( $request[ $key ] ) ) {
						$args[ $key ] = $request[ $key ];
					}
				} else {
					$args[ $key ] = ! empty( $request[ $key ] ) ? $request[ $key ] : $default;
				}
			}

			/**
			 * @todo Validate meta fields before saving - ensure that used correct types and all names was set.
			 */
			$meta_fields = ! empty( $request['meta_fields'] ) ? $request['meta_fields'] : array();

			$result['slug']        = $slug;
			$result['object_type'] = $request['object_type'];
			$result['labels']      = $labels;
			$result['args']        = $args;
			$result['meta_fields'] = $this->sanitize_meta_fields( $meta_fields );

			return $result;

		}

		/**
		 * Filter post type for register
		 *
		 * @return array
		 */
		public function filter_item_for_register( $item ) {

			$result = array();

			$args                = maybe_unserialize( $item['args'] );
			$item['labels']      = maybe_unserialize( $item['labels'] );
			$item['meta_fields'] = maybe_unserialize( $item['meta_fields'] );
			$item['object_type'] = maybe_unserialize( $item['object_type'] );

			$result = array_merge( $item, $args );

			if ( isset( $result['rewrite'] ) && false !== $result['rewrite'] ) {

				$with_front = isset( $result['with_front'] ) ? $result['with_front'] : true;
				$with_front = filter_var( $with_front, FILTER_VALIDATE_BOOLEAN );

				$rewrite_hierarchical = isset( $result['rewrite_hierarchical'] ) ? $result['rewrite_hierarchical'] : false;
				$rewrite_hierarchical = filter_var( $rewrite_hierarchical, FILTER_VALIDATE_BOOLEAN );

				$result['rewrite'] = array(
					'slug'         => $result['rewrite_slug'],
					'with_front'   => $with_front,
					'hierarchical' => $rewrite_hierarchical,
				);

				unset( $result['rewrite_slug'] );
				unset( $result['rewrite_hierarchical'] );
			}

			unset( $result['args'] );
			unset( $result['status'] );

			return $result;
		}

		/**
		 * Filter post type for edit
		 *
		 * @return array
		 */
		public function filter_item_for_edit( $item ) {

			$result = array(
				'general_settings'  => array(),
				'labels'            => array(),
				'advanced_settings' => array(),
				'meta_fields'       => array(),
			);

			$args        = maybe_unserialize( $item['args'] );
			$labels      = maybe_unserialize( $item['labels'] );
			$object_type = maybe_unserialize( $item['object_type'] );
			$name        = ! empty( $labels['name'] ) ? $labels['name'] : '';

			$result['general_settings'] = array(
				'name'             => $name,
				'slug'             => $item['slug'],
				'id'               => $item['id'],
				'object_type'      => $object_type,
				'show_edit_link'   => isset( $args['show_edit_link'] ) ? $args['show_edit_link'] : false,
				'hide_field_names' => isset( $args['hide_field_names'] ) ? $args['hide_field_names'] : false,
			);

			$meta_fields = array();

			if ( ! empty( $item['meta_fields'] ) ) {
				$meta_fields = maybe_unserialize( $item['meta_fields'] );
				$meta_fields = array_values( $meta_fields );

				if ( jet_engine()->meta_boxes ) {
					$meta_fields = jet_engine()->meta_boxes->data->sanitize_repeater_fields( $meta_fields );
				}

			}

			$with_front         = isset( $args['with_front'] ) ? $args['with_front'] : true;
			$with_front         = filter_var( $with_front, FILTER_VALIDATE_BOOLEAN );
			$args['with_front'] = $with_front;

			$result['labels']            = $labels;
			$result['advanced_settings'] = $args;
			$result['meta_fields']       = $meta_fields;

			if ( isset( $result['advanced_settings']['query_var'] ) && false === $result['advanced_settings']['query_var'] ) {
				$result['advanced_settings']['query_var'] = '';
			}

			return $result;

		}

		/**
		 * Before item delete
		 */
		public function before_item_delete( $item_id ) {

			$item = $this->get_item_for_edit( $item_id );

			if ( ! $item ) {
				return;
			}

			if ( empty( $item['advanced_settings']['capability_type'] ) ) {
				return;
			}

			$cap = $item['advanced_settings']['capability_type'];
			$this->remove_capability( $cap );
		}

		/**
		 * Before item update
		 */
		public function before_item_update( $item ) {
			
			if ( empty( $item['id'] ) ) {
				return;
			}
			
			$old_item = $this->get_item_for_edit( $item['id'] );

			if ( ! $old_item ) {
				return;
			}

			if ( empty( $old_item['advanced_settings']['capability_type'] ) ) {
				return;
			}

			$old_cap = $old_item['advanced_settings']['capability_type'];
			$new_cap = ! empty( $item['args']['capability_type'] ) ? $item['args']['capability_type'] : false;

			if ( $old_cap === $new_cap ) {
				return;
			}
			
			$this->remove_capability( $old_cap );

			if ( $new_cap ) {
				$this->add_capability( $new_cap );
			}

		}

		public function after_item_update( $item = array(), $is_new = false ) {
			
			if ( ! $is_new ) {
				return;
			}

			$new_cap = ! empty( $item['args']['capability_type'] ) ? $item['args']['capability_type'] : false;

			if ( ! $new_cap ) {
				return;
			}
			
			$this->add_capability( $new_cap );

		}

		public function add_capability( $cap ) {

			$cap = 'manage_' . $cap;

			global $wp_roles;

			if ( ! class_exists( 'WP_Roles' ) ) {
				return;
			}

			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}

			$wp_roles->add_cap( 'administrator', $cap );
			$wp_roles->add_cap( 'editor', $cap );

		}

		public function remove_capability( $cap ) {

			$cap = 'manage_' . $cap;
			
			global $wp_roles;

			if ( ! class_exists( 'WP_Roles' ) ) {
				return;
			}

			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}

			$wp_roles->remove_cap( 'administrator', $cap );
			$wp_roles->remove_cap( 'editor', $cap );

		}

		/**
		 * Edit built-in post type
		 *
		 * @param  boolean $redirect [description]
		 * @return [type]            [description]
		 */
		public function edit_built_in_item( $redirect = true ) {

			if ( ! current_user_can( 'manage_options' ) ) {
				$this->parent->add_notice(
					'error',
					__( 'You don\'t have permissions to do this', 'jet-engine' )
				);
				return false;
			}

			$item = $this->sanitize_item_from_request( true );

			if ( ! $item ) {
				$this->parent->add_notice(
					'error',
					__( 'Post type name not found in request. Please check your data and try again.', 'jet-engine' )
				);
				return false;
			}

			$this->query_args['status'] = 'built-in';

			$id = $this->update_item_in_db( $item );

			if ( ! $id ) {
				return false;
			} else {
				return $id;
			}

		}

		/**
		 * Get built-in post type from data base
		 *
		 * @param  [type] $post_type [description]
		 * @return [type]            [description]
		 */
		public function get_built_in_item_from_db( $slug = null ) {

			$item = $this->db->query(
				$this->table,
				array(
					'slug'   => $slug,
					'status' => 'built-in',
				),
				array( $this, 'filter_item_for_edit' )
			);

			if ( ! empty( $item ) ) {
				return $item[0];
			} else {
				return false;
			}

		}

		/**
		 * Remove modified data for built-in post type
		 *
		 * @return [type] [description]
		 */
		public function reset_built_in_item( $slug = null ) {

			$this->db->delete(
				$this->table,
				array(
					'slug'   => $slug,
					'status' => 'built-in',
				),
				array( '%s', '%s' )
			);

			return true;

		}

		/**
		 * Return user-modified built-in post types
		 *
		 * @return [type] [description]
		 */
		public function get_modified_built_in_items() {

			$items = $this->db->query(
				$this->table,
				array(
					'status' => 'built-in',
				)
			);

			if ( ! $items ) {
				return array();
			} else {
				return $items;
			}

		}

		/**
		 * Returns default built-in post type
		 *
		 * @return [type] [description]
		 */
		public function get_default_built_in_item( $slug ) {

			$object = get_taxonomy( $slug );

			if ( ! $object ) {

				$this->parent->add_notice(
					'error',
					__( 'Taxonomy not found', 'jet-engine' )
				);

				return false;
			}

			$object->labels = (array) $object->labels;
			$object         = (array) $object;

			$_defaults = ! empty( $this->parent->built_in_defaults[ $slug ] ) ? $this->parent->built_in_defaults[ $slug ] : false;

			if ( $_defaults ) {
				if ( ! empty( $_defaults['labels'] ) ) {

					$_defaults['labels'] = (array) $_defaults['labels'];

					$object['labels'] = array_merge( $object['labels'], $_defaults['labels'] );

					if ( ! empty( $_defaults['labels']['name'] ) ) {
						$object['label'] = $_defaults['labels']['name'];
					}

					unset( $_defaults['labels'] );
				}

				if ( ! empty( $_defaults ) ) {
					$object = array_merge( $object, $_defaults );
				}

			}

			$data = array(
				'general_settings' => array(
					'name'             => $object['label'],
					'slug'             => $object['name'],
					'object_type'      => $object['object_type'],
					'show_edit_link'   => false,
					'hide_field_names' => false,
				),
				'labels'        => $object['labels'],
				'meta_fields'   => array(),
			);

			unset( $object['labels'] );
			unset( $object['cap'] );
			unset( $object['label'] );
			unset( $object['name'] );

			$data['advanced_settings'] = $object;

			return $data;

		}

	}

}
