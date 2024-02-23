<?php
/**
 * Meta boxes manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Meta_Boxes' ) ) {

	/**
	 * Define Jet_Engine_Meta_Boxes class
	 */
	class Jet_Engine_Meta_Boxes extends Jet_Engine_Base_WP_Intance {

		/**
		 * Base slug for CPT-related pages
		 * @var string
		 */
		public $page = 'jet-engine-meta';

		/**
		 * Action request key
		 *
		 * @var string
		 */
		public $action_key = 'cpt_meta_action';

		/**
		 * Set object type
		 * @var string
		 */
		public $object_type = '';

		/**
		 * Meta fields for object
		 *
		 * @var null
		 */
		public $meta_fields = array();

		/**
		 * Conditions manager instance
		 *
		 * @var Jet_Engine_Meta_Boxes_Conditions
		 */
		public $conditions = null;

		private $custom_groups = array();

		/**
		 * Store meta fields data by context - post_type, user, taxonomy etx and object name
		 * @var array
		 */
		private $_fields_by_context = array();

		/**
		 * Meta fields with `save custom` option
		 *
		 * @var array
		 */
		public $meta_fields_save_custom = array();

		/**
		 * Init data instance
		 *
		 * @return [type] [description]
		 */
		public function init_data() {

			add_action( 'jet-engine/pages/cpt/register', array( $this, 'add_meta_fields_to_rel_components' ) );
			add_action( 'jet-engine/pages/taxonomies/register', array( $this, 'add_meta_fields_to_rel_components' ) );

			add_action( 'init', function() {
				require $this->component_path( 'fields-options/option-sources.php' );
				Jet_Engine_Meta_Boxes_Option_Sources::instance();
			}, 1 );

			require $this->component_path( 'data.php' );
			$this->data = new Jet_Engine_Meta_Boxes_Data( $this );

			add_action( 'jet-engine/post-types/deleted-post-type',      array( $this, 'remove_deleted_post_type_from_meta_boxes' ) );
			add_action( 'jet-engine/post-types/updated-post-type-slug', array( $this, 'update_post_type_in_meta_boxes' ), 10, 2 );

			add_action( 'jet-engine/taxonomies/deleted-taxonomy',      array( $this, 'remove_deleted_tax_from_meta_boxes' ) );
			add_action( 'jet-engine/taxonomies/updated-taxonomy-slug', array( $this, 'update_tax_in_meta_boxes' ), 10, 2 );

			jet_engine_datetime()->convert_meta_fields_dates();

		}

		/**
		 * Initialize conditions manager
		 *
		 * @return [type] [description]
		 */
		public function init_conditions() {

			require $this->component_path( 'conditions-manager.php' );
			$this->conditions = new Jet_Engine_Meta_Boxes_Conditions();

		}

		/**
		 * Returns allowed sources list
		 *
		 * @return [type] [description]
		 */
		public function get_sources() {
			return apply_filters( 'jet-engine/meta-boxes/sources', array(
				array(
					'value' => 'post',
					'label' => __( 'Post', 'jet-engine' ),
				),
				array(
					'value' => 'taxonomy',
					'label' => __( 'Taxonomy', 'jet-engine' ),
				),
				array(
					'value' => 'user',
					'label' => __( 'User', 'jet-engine' ),
				),
			) );
		}

		/**
		 * Add meta fields editor to components where it allowed
		 */
		public function add_meta_fields_to_rel_components() {

			$pages = $this->get_instance_pages();

			if ( ! class_exists( 'Jet_Engine_Meta_Boxes_Page_Edit' ) ) {
				require $pages['Jet_Engine_Meta_Boxes_Page_Edit'];
			}

			add_action(
				'jet-engine/post-type/edit/before-enqueue-assets',
				array( 'Jet_Engine_Meta_Boxes_Page_Edit', 'enqueue_meta_fields' )
			);

			add_action(
				'jet-engine/taxonomies/edit/before-enqueue-assets',
				array( 'Jet_Engine_Meta_Boxes_Page_Edit', 'enqueue_meta_fields' )
			);

			add_action(
				'jet-engine/post-types/meta-fields',
				function() {
					echo '<jet-meta-fields v-model="metaFields"></jet-meta-fields>';
				}
			);

			add_action(
				'jet-engine/taxonomies/meta-fields',
				function() {
					echo '<jet-meta-fields v-model="metaFields" :hide-options="[ \'quick_editable\', \'revision_support\' ]"></jet-meta-fields>';
				}
			);

		}

		/**
		 * Initiizlize post type specific API endpoints
		 *
		 * @param  Jet_Engine_REST_API $api_manager API manager instance.
		 * @return void
		 */
		public function init_rest( $api_manager ) {

			require_once $this->component_path( 'rest-api/add-meta-box.php' );
			require_once $this->component_path( 'rest-api/edit-meta-box.php' );
			require_once $this->component_path( 'rest-api/get-meta-box.php' );
			require_once $this->component_path( 'rest-api/get-meta-boxes.php' );
			require_once $this->component_path( 'rest-api/delete-meta-box.php' );

			$api_manager->register_endpoint( new Jet_Engine_Meta_Boxes_Rest_Add() );
			$api_manager->register_endpoint( new Jet_Engine_Meta_Boxes_Rest_Edit() );
			$api_manager->register_endpoint( new Jet_Engine_Meta_Boxes_Rest_Get() );
			$api_manager->register_endpoint( new Jet_Engine_Meta_Boxes_Rest_Get_All() );
			$api_manager->register_endpoint( new Jet_Engine_Meta_Boxes_Rest_Delete() );

		}

		/**
		 * Return path to file inside component
		 *
		 * @param  [type] $path_inside_component [description]
		 * @return [type]                        [description]
		 */
		public function component_path( $path_inside_component ) {
			return jet_engine()->plugin_path( 'includes/components/meta-boxes/' . $path_inside_component );
		}

		/**
		 * Regiter custom group
		 *
		 * @param  [type] $name  [description]
		 * @param  [type] $label [description]
		 * @return [type]        [description]
		 */
		public function register_custom_group( $name, $label ) {
			$this->custom_groups[ $name ] = $label;
		}

		/**
		 * Register metaboxes
		 *
		 * @return void
		 */
		public function register_instances() {

			$this->init_conditions();

			$meta_boxes = $this->data->get_raw();

			do_action( 'jet-engine/meta-boxes/register-instances', $this );

			if ( empty( $meta_boxes ) ) {
				$this->store_default_user_meta_fields();
				return;
			}

			foreach ( $meta_boxes as $meta_box ) {

				$args        = $meta_box['args'];
				$meta_fields = apply_filters( 'jet-engine/meta-boxes/raw-fields', $meta_box['meta_fields'], $this );
				$object_type = isset( $args['object_type'] ) ? esc_attr( $args['object_type'] ) : 'post';

				switch ( $object_type ) {

					case 'post':

						if ( ! class_exists( 'Jet_Engine_CPT_Meta' ) ) {
							require $this->component_path( 'post.php' );
						}

						$post_types = ! empty( $args['allowed_post_type'] ) ? $args['allowed_post_type'] : array();
						$title      = isset( $args['name'] ) ? $args['name'] : '';

						foreach ( $post_types as $post_type ) {

							if ( ! post_type_exists( $post_type ) ) {
								continue;
							}

							$this->store_fields( $post_type, $meta_fields, 'post_type' );
							
							Jet_Engine_Meta_Boxes_Option_Sources::instance()->find_meta_fields_with_save_custom( 
								$object_type, 
								$post_type,
								$meta_fields,
								$meta_box['id'],
								$this->data
							);

							$meta_instance = new Jet_Engine_CPT_Meta( $post_type, $meta_fields, $title, 'normal', 'high', $args );

							if ( ! empty( $args['show_edit_link'] ) ) {
								$meta_instance->add_edit_link( add_query_arg(
									array(
										'page' => 'jet-engine-meta',
										'cpt_meta_action' => 'edit',
										'id' => $meta_box['id'],
									),
									admin_url( 'admin.php' )
								) );
							}

						}

						break;

					case 'tax':
					case 'taxonomy':

						if ( ! class_exists( 'Jet_Engine_CPT_Tax_Meta' ) ) {
							require $this->component_path( 'tax.php' );
						}

						$taxonomies = ! empty( $args['allowed_tax'] ) ? $args['allowed_tax'] : array();

						foreach ( $taxonomies as $taxonomy ) {

							if ( ! taxonomy_exists( $taxonomy ) ) {
								continue;
							}

							$meta_instance = new Jet_Engine_CPT_Tax_Meta( $taxonomy, $meta_fields, $args );

							if ( ! empty( $args['show_edit_link'] ) ) {
								$meta_instance->add_edit_link( add_query_arg(
									array(
										'page' => 'jet-engine-meta',
										'cpt_meta_action' => 'edit',
										'id' => $meta_box['id'],
									),
									admin_url( 'admin.php' )
								) );
							}

							$this->store_fields( $taxonomy, $meta_fields, 'taxonomy' );
							
							Jet_Engine_Meta_Boxes_Option_Sources::instance()->find_meta_fields_with_save_custom(
								$object_type,
								$taxonomy,
								$meta_fields,
								$meta_box['id'],
								$this->data
							);
						}

						break;

					case 'user':

						if ( ! class_exists( 'Jet_Engine_CPT_User_Meta' ) ) {
							require $this->component_path( 'user.php' );
						}

						$meta_instance = new Jet_Engine_CPT_User_Meta( $args, $meta_fields );

						if ( ! empty( $args['show_edit_link'] ) ) {
							$meta_instance->add_edit_link( add_query_arg(
								array(
									'page' => 'jet-engine-meta',
									'cpt_meta_action' => 'edit',
									'id' => $meta_box['id'],
								),
								admin_url( 'admin.php' )
							) );
						}

						$object_name = $args['name'] . ' ' . __( '(User fields)', 'jet-engine' );

						$this->store_fields( $object_name, $meta_fields, 'user' );

						Jet_Engine_Meta_Boxes_Option_Sources::instance()->find_meta_fields_with_save_custom(
							$object_type,
							'user',
							$meta_fields,
							$meta_box['id'],
							$this->data
						);

						foreach ( $meta_fields as $field ) {

							if ( ! empty( $field['object_type'] ) && 'field' !== $field['object_type'] ) {
								continue;
							}

							if ( empty( $field['name'] ) ) {
								continue;
							}

							jet_engine()->listings->data->user_fields[] = $field['name'];

						}

						break;

					default:
						do_action( 'jet-engine/meta-boxes/register-custom-source/' . $object_type, $meta_box, $this );

						break;

				}

			}

			$this->store_default_user_meta_fields();

		}

		/**
		 * Register the same metabox as default but from outside of this instance
		 *
		 * @return [type] [description]
		 */
		public function register_metabox( $post_type = '', $meta_fields = array(), $title = '', $object_name = null, $context = 'post_type' ) {

			$object_name = ! empty( $object_name ) ? $object_name : $post_type;

			$this->store_fields( $object_name, $meta_fields, $context );

			if ( ! class_exists( 'Jet_Engine_CPT_Meta' ) ) {
				require $this->component_path( 'post.php' );
			}

			new Jet_Engine_CPT_Meta( $post_type, $meta_fields, $title );

		}

		/**
		 * Strore information aboutt all registered fields
		 *
		 * @param  string $post_type   [description]
		 * @param  array  $meta_fields [description]
		 * @return [type]              [description]
		 */
		public function store_fields( $object_type = 'post', $meta_fields = array(), $context = 'post_type' ) {

			if ( empty( $this->meta_fields[ $object_type ] ) ) {
				$this->meta_fields[ $object_type ] = array();
			}

			$fields = array_values( $meta_fields );

			$this->meta_fields[ $object_type ] = array_merge( $this->meta_fields[ $object_type ], $fields );

			if ( empty( $this->_fields_by_context[ $context ] ) ) {
				$this->_fields_by_context[ $context ] = array();
			}

			if ( empty( $this->_fields_by_context[ $context ][ $object_type ] ) ) {
				$this->_fields_by_context[ $context ][ $object_type ] = array();
			}

			$this->_fields_by_context[ $context ][ $object_type ] = array_merge( $this->_fields_by_context[ $context ][ $object_type ], $fields );

		}

		/**
		 * Returns fields for the given context and object
		 * Should be called on hook 'init' with priority 11 or later
		 *
		 * @param  string $context Context to get fields for.
		 * @param  string $object  Object inside context to get fields from. If not assed -returns all fields, grouped by objects
		 * @return array
		 */
		public function get_fields_for_context( $context = 'post_type', $object = null ) {

			$fields = isset( $this->_fields_by_context[ $context ] ) ? $this->_fields_by_context[ $context ] : array();

			if ( ! $object ) {
				return $fields;
			}

			return isset( $fields[ $object ] ) ? $fields[ $object ] : array();

		}

		/**
		 * Return all registered meta fields
		 *
		 * @return array
		 */
		public function get_registered_fields() {
			return $this->meta_fields;
		}

		/**
		 * Try to get current post ID from request
		 *
		 * @return [type] [description]
		 */
		public function get_post_id() {

			$post_id = isset( $_GET['post'] ) ? $_GET['post'] : false;

			if ( ! $post_id && isset( $_REQUEST['post_ID'] ) ) {
				$post_id = $_REQUEST['post_ID'];
			}

			return $post_id;

		}

		/**
		 * Return fields list registered for users
		 *
		 * @return [type] [description]
		 */
		public function store_default_user_meta_fields() {
			$this->store_fields(
				__( 'Default user fields', 'jet-engine' ),
				array(
					array(
						'name'  => 'first_name',
						'title' => __( 'First Name', 'jet-engine' ),
						'type'  => 'text',
					),
					array(
						'name'  => 'last_name',
						'title' => __( 'Last Name', 'jet-engine' ),
						'type'  => 'text',
					),
					array(
						'name'  => 'description',
						'title' => __( 'Biographical Info', 'jet-engine' ),
						'type'  => 'text',
					),
				),
				'user'
			);
		}

		/**
		 * Return list of meta fields for post type
		 *
		 * @param  string $object [description]
		 * @return [type]            [description]
		 */
		public function get_meta_fields_for_object( $object = 'post' ) {

			if ( isset( $this->meta_fields[ $object ] ) ) {
				return $this->meta_fields[ $object ];
			} else {
				return array();
			}

		}

		/**
		 * Returns all registered options (or depends on context) to use in select
		 *
		 * @return [type] [description]
		 */
		public function get_fields_for_select( $context = 'plain', $where = 'elementor', $for = 'all' ) {

			$result = array();
			$post_types = get_post_types( array(), 'objects' );
			$taxonomies = get_taxonomies( array(), 'objects' );

			foreach ( $this->meta_fields as $object => $fields ) {

				$group_label = false;

				if ( isset( $post_types[ $object ] ) ) {

					if ( ! in_array( $for, array( 'all', 'posts' ) ) ) {
						continue;
					}

					$group_label = $post_types[ $object ]->labels->name;

				} elseif ( isset( $taxonomies[ $object ] ) ) {

					if ( ! in_array( $for, array( 'all', 'taxonomies' ) ) ) {
						continue;
					}

					$group_label = $taxonomies[ $object ]->labels->name;

				} else {

					if ( ! in_array( $for, array( 'all', 'user' ) ) ) {
						continue;
					}

					$group_label = $object;
				}

				if ( ! $group_label ) {
					continue;
				}

				$group        = array();
				$blocks_group = array();

				foreach ( $fields as $field_data ) {

					if ( ! empty( $field_data['object_type'] ) && 'field' !== $field_data['object_type'] ) {
						continue;
					}

					if ( 'html' === $field_data['type'] ) {
						continue;
					}

					$name = apply_filters( 
						'jet-engine/meta-boxes/fields-for-select/name',
						$field_data['name'], $field_data, $object
					);

					$title = ! empty( $field_data['title'] ) ? $field_data['title'] : $name;

					switch ( $context ) {

						case 'all':

							$group[ $name ] = $title;

							$blocks_group[] = array(
								'value' => $name,
								'label' => $title,
							);

							break;

						case 'plain':

							if ( 'repeater' !== $field_data['type'] ) {
								$group[ $name ] = $title;

								$blocks_group[] = array(
									'value' => $name,
									'label' => $title,
								);

							}

							break;

						case 'repeater':

							if ( 'repeater' === $field_data['type'] ) {
								$group[ $name ] = $title;

								$blocks_group[] = array(
									'value' => $name,
									'label' => $title,
								);
							}

							break;

						case 'media':

							if ( 'media' === $field_data['type'] ) {
								$group[ $name ] = $title;

								$blocks_group[] = array(
									'value' => $name,
									'label' => $title,
								);

							}

							break;

						case 'gallery':

							if ( 'gallery' === $field_data['type'] ) {
								$group[ $name ] = $title;

								$blocks_group[] = array(
									'value' => $name,
									'label' => $title,
								);
							}

							break;

						case 'text':

							$text_types = array(
								'text',
								'textarea',
								'wysiwyg',
								'radio',
								'select',
							);

							if ( in_array( $field_data['type'], $text_types ) ) {
								$group[ $name ] = $title;

								$blocks_group[] = array(
									'value' => $name,
									'label' => $title,
								);
							}

							break;
					}

				}

				if ( ! empty( $group ) ) {
					if ( 'blocks' === $where ) {
						$result[] = array(
							'label'  => $group_label,
							'values' => $blocks_group,
						);
					} else {
						$result[] = array(
							'label'   => $group_label,
							'options' => $group,
						);
					}
				}

			}

			if ( 'elementor' === $where ) {
				$result = array( '' => esc_html__( 'Select...', 'jet-engine' ) ) + $result;
			}

			return $result;
		}

		/**
		 * Return admin pages for current instance
		 *
		 * @return array
		 */
		public function get_instance_pages() {

			$base_path = $this->component_path( 'pages/' );

			return array(
				'Jet_Engine_Meta_Boxes_Page_List' => $base_path . 'list.php',
				'Jet_Engine_Meta_Boxes_Page_Edit' => $base_path . 'edit.php',
			);

		}

		/**
		 * Returns current menu page title (for JetEngine submenu)
		 * @return [type] [description]
		 */
		public function get_page_title() {
			return __( 'Meta Boxes', 'jet-engine' );
		}

		/**
		 * Returns current instance slug
		 *
		 * @return [type] [description]
		 */
		public function instance_slug() {
			return 'meta';
		}

		/**
		 * Returns default config for add/edit page
		 *
		 * @param  array  $config [description]
		 * @return [type]         [description]
		 */
		public function get_admin_page_config( $config = array() ) {

			$default_settings = array(
				'type'  => 'text',
				'width' => '100%',
			);

			$default = array(
				'api_path_edit'       => '', // Set individually for apropriate page
				'api_path_get'        => jet_engine()->api->get_route( 'get-meta-box' ),
				'api_path_search'     => jet_engine()->api->get_route( 'search-posts' ),
				'edit_button_label'   => '', // Set individually for apropriate page,
				'item_id'             => false,
				'post_types'          => Jet_Engine_Tools::get_post_types_for_js(),
				'taxonomies'          => Jet_Engine_Tools::get_taxonomies_for_js(),
				'redirect'            => '', // Set individually for apropriate page,
				'general_settings'    => array( 'object_type' => 'post' ),
				'meta_fields'         => array(),
				'notices'             => array(
					'name'    => __( 'Please, set meta box title', 'jet-engine' ),
					'success' => __( 'Meta box updated', 'jet-engine' ),
				),
			);

			return array_merge( $default, $config );

		}

		/**
		 * Remove post type from `allowed_post_type` param in the meta boxes.
		 *
		 * @param $deleted_post_type
		 */
		public function remove_deleted_post_type_from_meta_boxes( $deleted_post_type ) {
			$this->update_object_type_in_meta_boxes( false, $deleted_post_type );
		}

		/**
		 * Update the post type slug in the meta boxes after it has been changed.
		 *
		 * @param $new_post_type
		 * @param $initial_post_type
		 */
		public function update_post_type_in_meta_boxes( $new_post_type, $initial_post_type ) {
			$this->update_object_type_in_meta_boxes( $new_post_type, $initial_post_type );
		}

		/**
		 * Remove tax from `allowed_tax` param in meta boxes.
		 *
		 * @param $deleted_tax
		 */
		public function remove_deleted_tax_from_meta_boxes( $deleted_tax ) {
			$this->update_object_type_in_meta_boxes( false, $deleted_tax, 'tax' );
		}

		/**
		 * Update the tax slug in the meta boxes after it has been changed.
		 *
		 * @param $new_tax
		 * @param $initial_tax
		 */
		public function update_tax_in_meta_boxes( $new_tax, $initial_tax ) {
			$this->update_object_type_in_meta_boxes( $new_tax, $initial_tax, 'tax' );
		}

		/**
		 * Update the post type/taxonomy slug in the meta boxes after it has been changed.
		 *
		 * To delete the post type/taxonomy in the meta boxes, set $new_obj_slug to false.
		 *
		 * @param $new_obj_slug
		 * @param $obj_slug
		 * @param $type
		 */
		public function update_object_type_in_meta_boxes( $new_obj_slug = null, $obj_slug = null, $type = 'post' ) {

			$meta_boxes = jet_engine()->meta_boxes->data->get_raw();

			if ( empty( $meta_boxes ) ) {
				return;
			}

			foreach ( $meta_boxes as $meta_box ) {
				$args        = $meta_box['args'];
				$object_type = isset( $args['object_type'] ) ? esc_attr( $args['object_type'] ) : 'post';

				switch ( $type ) {
					case 'post':
						$allowed_obj_types = array( 'post' );
						$setting_key       = 'allowed_post_type';
						break;

					case 'tax':
						$allowed_obj_types = array( 'tax', 'taxonomy' );
						$setting_key       = 'allowed_tax';
						break;

					default:
						$allowed_obj_types = false;
						$setting_key       = false;
				}

				if ( empty( $allowed_obj_types ) || empty( $setting_key ) ) {
					continue;
				}

				if ( ! in_array( $object_type, $allowed_obj_types ) ) {
					continue;
				}

				$allowed_obj = ! empty( $args[ $setting_key ] ) ? $args[ $setting_key ] : array();

				if ( ! in_array( $obj_slug, $allowed_obj ) ) {
					continue;
				}

				$allowed_obj = array_combine( $allowed_obj, $allowed_obj );

				if ( ! $new_obj_slug ) {
					unset( $allowed_obj[ $obj_slug ] );
				} else {
					$allowed_obj[ $obj_slug ] = $new_obj_slug;
				}

				$meta_box['args'][ $setting_key ] = array_values( $allowed_obj );

				jet_engine()->meta_boxes->data->update_item_in_db( $meta_box );
			}

		}

	}

}
