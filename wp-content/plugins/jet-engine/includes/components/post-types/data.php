<?php
/**
 * CPT data controller class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT_Data' ) ) {

	/**
	 * Define Jet_Engine_CPT_Data class
	 */
	class Jet_Engine_CPT_Data extends Jet_Engine_Base_Data {

		/**
		 * Table name
		 *
		 * @var string
		 */
		public $table = 'post_types';

		/**
		 * Query arguments
		 *
		 * @var array
		 */
		public $query_args = array(
			'status' => 'publish',
		);

		/**
		 * Table format
		 *
		 * @var string
		 */
		public $table_format = array( '%s', '%s', '%s', '%s', '%s' );

		/**
		 * Returns blacklisted post types slugs
		 *
		 * @return array
		 */
		public function items_blacklist() {
			return array(
				'post',
				'page',
				'attachment',
				'revision',
				'nav_menu_item',
				'custom_css',
				'customize_changeset',
				'action',
				'author',
				'order',
				'theme',
				'themes',
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
				'status'      => $status,
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
				'singular_name',
				'menu_name',
				'name_admin_bar',
				'add_new',
				'add_new_item',
				'new_item',
				'edit_item',
				'view_item',
				'all_items',
				'search_items',
				'parent_item_colon',
				'not_found',
				'not_found_in_trash',
				'featured_image',
				'set_featured_image',
				'remove_featured_image',
				'use_featured_image',
				'archives',
				'insert_into_item',
				'uploaded_to_this_item',
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
				'query_var',
				'rewrite',
				'map_meta_cap',
				'has_archive',
				'hierarchical',
				'exclude_from_search',
				'with_front',
				'show_edit_link',
				'hide_field_names',
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
				'rewrite_slug'    => $slug,
				'capability_type' => 'post',
				'menu_position'   => null,
				'menu_icon'       => '',
				'supports'        => array(),
				'admin_columns'   => array(),
				'admin_filters'   => array(),
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

			if ( ! isset( $args['admin_columns'] ) ) {
				$args['admin_columns'] = array();
			}

			if ( ! isset( $args['admin_filters'] ) ) {
				$args['admin_filters'] = array();
			}

			// Remove collapsed trigger from admin columns and filters
			if ( ! empty( $args['admin_columns'] ) ) {
				for ( $i = 0; $i < count( $args['admin_columns'] ); $i++ ) {
					if ( isset( $args['admin_columns'][ $i ]['collapsed'] ) ) {
						unset( $args['admin_columns'][ $i ]['collapsed'] );
					}
				}
			}

			if ( ! empty( $args['admin_filters'] ) ) {
				for ( $i = 0; $i < count( $args['admin_filters'] ); $i++ ) {
					if ( isset( $args['admin_filters'][ $i ]['collapsed'] ) ) {
						unset( $args['admin_filters'][ $i ]['collapsed'] );
					}
				}
			}

			/**
			 * @todo Validate meta fields before saving - ensure that used correct types and all names was set.
			 */
			$meta_fields = ! empty( $request['meta_fields'] ) ? $request['meta_fields'] : array();

			$result['slug']        = $slug;
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

			$result = array_merge( $item, $args );

			if ( false !== $result['rewrite'] ) {

				$with_front = isset( $result['with_front'] ) ? $result['with_front'] : true;
				$with_front = filter_var( $with_front, FILTER_VALIDATE_BOOLEAN );

				$result['rewrite'] = array(
					'slug'       => $result['rewrite_slug'],
					'with_front' => $with_front,
				);

				unset( $result['rewrite_slug'] );

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
				'admin_columns'     => array(),
				'admin_filters'     => array(),
			);

			$args          = maybe_unserialize( $item['args'] );
			$labels        = maybe_unserialize( $item['labels'] );
			$admin_columns = array();
			$admin_filters = array();
			$name          = ! empty( $labels['name'] ) ? $labels['name'] : '';

			if ( $name ) {
				unset( $labels['name'] );
			}

			if ( ! empty( $args['admin_columns'] ) ) {
				$admin_columns = $args['admin_columns'];
				unset( $args['admin_columns'] );
			}

			if ( ! empty( $args['admin_filters'] ) ) {
				$admin_filters = $args['admin_filters'];
				unset( $args['admin_filters'] );
			}

			$result['general_settings'] = array(
				'name'             => $name,
				'slug'             => $item['slug'],
				'id'               => $item['id'],
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

			$admin_columns = ! empty( $admin_columns ) ? array_values( $admin_columns ) : array();
			$admin_filters = ! empty( $admin_filters ) ? array_values( $admin_filters ) : array();

			$with_front         = isset( $args['with_front'] ) ? $args['with_front'] : true;
			$with_front         = filter_var( $with_front, FILTER_VALIDATE_BOOLEAN );
			$args['with_front'] = $with_front;

			if ( ! isset( $args['map_meta_cap'] ) ) {
				$args['map_meta_cap'] = true;
			}

			$result['labels']            = $labels;
			$result['advanced_settings'] = $args;
			$result['meta_fields']       = $meta_fields;
			$result['admin_columns']     = $admin_columns;
			$result['admin_filters']     = $admin_filters;

			return $result;

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
		public function get_built_in_post_type_from_db( $post_type = null ) {

			$item = $this->db->query(
				$this->table,
				array(
					'slug'   => $post_type,
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
		public function reset_built_in_post_type( $post_type = null ) {

			$this->db->delete(
				$this->table,
				array(
					'slug'   => $post_type,
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
		public function get_modified_built_in_types() {

			$types = $this->db->query(
				$this->table,
				array(
					'status' => 'built-in',
				)
			);

			if ( ! $types ) {
				return array();
			} else {
				return $types;
			}

		}

		/**
		 * Returns default built-in post type
		 *
		 * @return [type] [description]
		 */
		public function get_default_built_in_post_type( $post_type ) {

			$post_type_object = get_post_type_object( $post_type );

			if ( ! $post_type_object ) {

				$this->parent->add_notice(
					'error',
					__( 'Post type not found', 'jet-engine' )
				);

				return false;
			}

			$post_type_object->labels = (array) $post_type_object->labels;
			$post_type_object         = (array) $post_type_object;

			$_defaults = ! empty( $this->parent->built_in_defaults[ $post_type ] ) ? $this->parent->built_in_defaults[ $post_type ] : false;

			if ( $_defaults ) {
				if ( ! empty( $_defaults['labels'] ) ) {

					$post_type_object['labels'] = array_merge( $post_type_object['labels'], $_defaults['labels'] );

					if ( ! empty( $_defaults['labels']['name'] ) ) {
						$post_type_object['label'] = $_defaults['labels']['name'];
					}

					unset( $_defaults['labels'] );
				}

				if ( ! empty( $_defaults ) ) {
					$post_type_object = array_merge( $post_type_object, $_defaults );
				}

			}

			$post_type_data = array(
				'general_settings' => array(
					'name'             => $post_type_object['label'],
					'slug'             => $post_type_object['name'],
					'show_edit_link'   => false,
					'hide_field_names' => false,
				),
				'labels'        => $post_type_object['labels'],
				'meta_fields'   => array(),
				'admin_columns' => array(),
				'admin_filters' => array(),
			);

			unset( $post_type_object['labels'] );
			unset( $post_type_object['cap'] );
			unset( $post_type_object['label'] );
			unset( $post_type_object['name'] );

			$supports = get_all_post_type_supports( $post_type );
			$supports = array_filter( $supports );
			$supports = array_keys( $supports );

			$post_type_object['supports'] = $supports;

			$post_type_data['advanced_settings'] = $post_type_object;

			return $post_type_data;

		}

	}

}
