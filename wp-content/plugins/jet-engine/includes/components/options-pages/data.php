<?php
/**
 * Options data controller class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Options_Data' ) ) {

	/**
	 * Define Jet_Engine_Options_Data class
	 */
	class Jet_Engine_Options_Data extends Jet_Engine_Base_Data {

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
			'status' => 'page',
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
			);
		}

		/**
		 * Returns blacklisted post types slugs
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
			);
		}

		/**
		 * Prepare post data from request to write into database
		 *
		 * @return array
		 */
		public function sanitize_item_from_request() {

			$request = $this->request;

			$result = array(
				'slug'        => '',
				'status'      => 'page',
				'labels'      => array(),
				'args'        => array(),
				'meta_fields' => array(),
			);

			$slug = ! empty( $request['slug'] ) ? $this->sanitize_slug( $request['slug'] ) : false;
			$name = ! empty( $request['name'] ) ? sanitize_text_field( $request['name'] ) : false;

			if ( ! $slug ) {
				return false;
			}

			$labels = array(
				'name' => $name,
			);

			$labels_list = array(
				'menu_name',
			);

			foreach ( $labels_list as $label_key ) {
				if ( ! empty( $request[ $label_key ] ) ) {
					$labels[ $label_key ] = $request[ $label_key ];
				}
			}

			$args         = array();
			$regular_args = array(
				'parent'           => '',
				'icon'             => 'dashicons-admin-generic',
				'capability'       => 'manage_options',
				'position'         => '',
				'storage_type'     => 'default',
				'option_prefix'    => true,
				'hide_field_names' => false,
			);

			foreach ( $regular_args as $key => $default ) {
				if ( in_array( $key, array( 'option_prefix', 'hide_field_names' ) ) ) {
					$args[ $key ] = isset( $request[ $key ] ) ? filter_var( $request[ $key ], FILTER_VALIDATE_BOOLEAN ) : $default;
				} else {
					$args[ $key ] = ! empty( $request[ $key ] ) ? $request[ $key ] : $default;
				}
			}

			/**
			 * @todo Validate meta fields before saving - ensure that used correct types and all names was set.
			 */
			$meta_fields = ! empty( $request['meta_fields'] )
								? $request['meta_fields'] : ( ! empty( $request['fields'] )
									? $request['fields'] : array() );

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

			$args           = maybe_unserialize( $item['args'] );
			$item['labels'] = maybe_unserialize( $item['labels'] );
			$item['fields'] = maybe_unserialize( $item['meta_fields'] );

			$result = array_merge( $item, $args );

			unset( $result['args'] );
			unset( $result['status'] );
			unset( $result['meta_fields'] );

			return $result;
		}

		/**
		 * Filter post type for edit
		 *
		 * @return array
		 */
		public function filter_item_for_edit( $item ) {

			$result = array(
				'general_settings' => array(),
				'labels'           => array(),
				'fields'           => array(),
			);

			$args   = maybe_unserialize( $item['args'] );
			$labels = maybe_unserialize( $item['labels'] );
			$fields = array();

			// Set default value for `storage_type` setting if setting is not existing.
			if ( empty( $args['storage_type'] ) ) {
				$args['storage_type'] = 'default';
			}

			// Set default value for `option_prefix` setting if setting is not existing.
			if ( ! isset( $args['option_prefix'] ) ) {
				$args['option_prefix'] = true;
			}

			$result['general_settings'] = array_merge( array( 'slug' => $item['slug'] ), $labels, $args );

			if ( ! empty( $item['meta_fields'] ) ) {

				$fields = maybe_unserialize( $item['meta_fields'] );
				$fields = array_values( $fields );

				if ( jet_engine()->meta_boxes ) {
					$fields = jet_engine()->meta_boxes->data->sanitize_repeater_fields( $fields );
				}

			}

			$result['fields'] = $fields;

			return $result;
		}

	}

}
