<?php
/**
 * Custom post types manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_CPT' ) ) {

	/**
	 * Define Jet_Engine_CPT class
	 */
	class Jet_Engine_CPT extends Jet_Engine_Base_WP_Intance {

		/**
		 * Base slug for CPT-related pages
		 * @var string
		 */
		public $page = 'jet-engine-cpt';

		/**
		 * Action request key
		 *
		 * @var string
		 */
		public $action_key = 'cpt_action';

		/**
		 * Metaboxes to register
		 *
		 * @var array
		 */
		public $meta_boxes = array();

		/**
		 * Metaboxes args to register
		 *
		 * @var array
		 */
		public $meta_boxes_args = array();

		/**
		 * Set object type
		 * @var string
		 */
		public $object_type = 'post-type';

		/**
		 * Meta fields for object
		 *
		 * @var null
		 */
		public $meta_fields = array();

		/**
		 * Store built-in defaults before filtering
		 *
		 * @var array
		 */
		public $built_in_defaults = array();

		public $edit_links = array();

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			$this->register_built_in_modifications();

			parent::__construct();

			add_action( 'jet-engine/meta-boxes/register-instances', array( $this, 'init_meta_boxes' ) );
			add_action( 'current_screen', array( $this, 'init_edit_links' ) );

		}

		/**
		 * Register modifications for built-in post types
		 * @return [type] [description]
		 */
		public function register_built_in_modifications() {

			if ( ! $this->data ) {
				$this->init_data();
			}

			$built_ins = $this->data->get_modified_built_in_types();

			if ( ! empty( $built_ins ) ) {
				foreach ( $built_ins as $built_in ) {

					$post_type = ! empty( $built_in['slug'] ) ? $built_in['slug'] : false;

					if ( ! $post_type ) {
						continue;
					}

					$built_in_args = ! empty( $built_in['args'] ) ? maybe_unserialize( $built_in['args'] ) : null;

					if ( $built_in_args && ! empty( $built_in_args['show_edit_link'] ) ) {

						$this->edit_links[ $post_type ] = add_query_arg(
							array(
								'page' => 'jet-engine-cpt',
								'cpt_action' => 'edit',
								'id' => -1,
								'edit-type' => 'built-in',
								'post-type' => $post_type,
							),
							admin_url( 'admin.php' )
						);

					}

					/**
					 * Anonymus function to register post type modifications.
					 * Called there to keep all data inside funciton.
					 */
					add_filter(
						'register_post_type_args',
						function( $args, $post_type_name ) use ( $post_type, $built_in ) {

							if ( $post_type_name !== $post_type ) {
								return $args;
							}

							if ( empty( $this->built_in_defaults[ $post_type ] ) ) {
								$this->built_in_defaults[ $post_type ] = $args;
							}

							$labels = ! empty( $built_in['labels'] ) ? maybe_unserialize( $built_in['labels'] ) : null;

							if ( ! empty( $labels ) ) {
								/**
								 * Anonymus function to register post type modifications.
								 * Called there to keep all data inside funciton.
								 */
								add_filter(
									'post_type_labels_' . $post_type,
									function( $default_labels ) use ( $labels, $post_type ) {

										$this->built_in_defaults[ $post_type ]['labels'] = (array) $default_labels;

										foreach ( $labels as $key => $label ) {
											$default_labels->$key = $label;
										}

										return $default_labels;
									}
								);

							}

							$all_args = ! empty( $built_in['args'] ) ? maybe_unserialize( $built_in['args'] ) : null;

							if ( ! empty( $all_args ) && isset( $all_args['admin_columns'] ) ) {
								unset( $all_args['admin_columns'] );
							}

							if ( ! empty( $all_args['rewrite_slug'] ) ) {

								if ( isset( $all_args['rewrite'] ) ) {
									unset( $all_args['rewrite'] );
								}

								if ( isset( $args['rewrite'] ) && is_array( $args['rewrite'] ) ) {
									$args['rewrite']['slug'] = $all_args['rewrite_slug'];
								} else {
									$args['rewrite'] = array(
										'slug' => $all_args['rewrite_slug'],
									);
								}
							}

							$args = array_merge( $args, $all_args );

							return $args;

						},
						10, 2
					);

					$args = ! empty( $built_in['args'] ) ? maybe_unserialize( $built_in['args'] ) : array();

					if ( ! empty( $built_in['meta_fields'] ) ) {

						/**
						 * Anonymus function to register apropriate meta fields.
						 * Should be called there, if called earlier - jet_engine()->meta_boxes instance is not defined
						 */
						add_action( 'jet-engine/meta-boxes/register-instances', function() use ( $built_in, $post_type, $args ) {

							$meta_fields = maybe_unserialize( $built_in['meta_fields'] );

							if ( empty( $meta_fields ) ) {
								return;
							}

							$meta_fields = apply_filters( 'jet-engine/meta-boxes/raw-fields', $meta_fields, $this );

							if ( ! empty( $args['hide_field_names'] ) ) {
								$this->meta_boxes_args[ $post_type ]['hide_field_names'] = $args['hide_field_names'];
							}

							$box_id = ! empty( $built_in['id'] ) ? $built_in['id'] : 'built_in_' . $post_type;

							$this->meta_boxes_args[ $post_type ]['id'] = $box_id;
							$this->meta_boxes_args[ $post_type ]['is_built_in'] = true;

							$this->meta_boxes[ $post_type ] = $meta_fields;
							if ( jet_engine()->meta_boxes ) {
								jet_engine()->meta_boxes->store_fields( $post_type, $meta_fields );
							}

						}, 9 );

					}

					if ( is_admin() ) {

						$columns = ! empty( $args['admin_columns'] ) ? $args['admin_columns'] : array();

						if ( ! empty( $columns ) && $this->columns_allowed( $post_type ) ) {

							if ( ! class_exists( 'Jet_Engine_CPT_Admin_Columns' ) ) {
								require_once $this->component_path( 'admin-columns.php' );
							}

							new Jet_Engine_CPT_Admin_Columns( $post_type, $columns );

						}

						$filters = ! empty( $args['admin_filters'] ) ? $args['admin_filters'] : array();

						if ( ! empty( $filters ) && $this->columns_allowed( $post_type ) ) {

							if ( ! class_exists( 'Jet_Engine_CPT_Admin_Filters' ) ) {
								require_once $this->component_path( 'admin-filters.php' );
							}

							new Jet_Engine_CPT_Admin_Filters( $post_type, $filters );

						}
					}

				}
			}

		}

		/**
		 * Returns allowed admin filters types list
		 *
		 * @return [type] [description]
		 */
		public function admin_filters_types() {
			return apply_filters( 'jet-engine/post-types/admin-filters-types', array(
				array(
					'value' => 'taxonomy',
					'label' => __( 'Filter by taxonomy', 'jet-engine' ),
				),
				array(
					'value' => 'meta',
					'label' => __( 'Filter by meta data', 'jet-engine' ),
				),
			) );
		}

		/**
		 * Check if admin columns registration is allowed for current page
		 *
		 * @param  string $post_type [description]
		 * @return [type]            [description]
		 */
		public function columns_allowed( $post_type = 'post' ) {

			if ( 'post' === $post_type ) {
				return true;
			}

			return ( ! empty( $_GET['post_type'] ) && $post_type === $_GET['post_type'] );

		}

		/**
		 * Register post meta
		 *
		 * @return [type] [description]
		 */
		public function init_meta_boxes() {
			if ( jet_engine()->components->is_component_active( 'meta_boxes' ) ) {
				$this->register_meta_boxes();
			}
		}

		/**
		 * Init data instance
		 *
		 * @return [type] [description]
		 */
		public function init_data() {

			if ( ! empty( $this->data ) ) {
				return;
			}

			if ( ! class_exists( 'Jet_Engine_Base_Data' ) ) {
				require_once jet_engine()->plugin_path( 'includes/base/base-data.php' );
			}

			require $this->component_path( 'data.php' );

			$this->data = new Jet_Engine_CPT_Data( $this );

		}

		/**
		 * Initiizlize post type specific API endpoints
		 *
		 * @param  Jet_Engine_REST_API $api_manager API manager instance.
		 * @return void
		 */
		public function init_rest( $api_manager ) {

			require_once $this->component_path( 'rest-api/add-post-type.php' );
			require_once $this->component_path( 'rest-api/edit-post-type.php' );
			require_once $this->component_path( 'rest-api/get-post-type.php' );
			require_once $this->component_path( 'rest-api/delete-post-type.php' );
			require_once $this->component_path( 'rest-api/get-post-types.php' );
			require_once $this->component_path( 'rest-api/get-built-in-post-type.php' );
			require_once $this->component_path( 'rest-api/edit-built-in-post-type.php' );
			require_once $this->component_path( 'rest-api/reset-built-in-post-type.php' );
			require_once $this->component_path( 'rest-api/copy-post-type.php' );

			$api_manager->register_endpoint( new Jet_Engine_CPT_Rest_Add_Post_Type() );
			$api_manager->register_endpoint( new Jet_Engine_CPT_Rest_Edit_Post_Type() );
			$api_manager->register_endpoint( new Jet_Engine_CPT_Rest_Get_Post_Type() );
			$api_manager->register_endpoint( new Jet_Engine_CPT_Rest_Delete_Post_Type() );
			$api_manager->register_endpoint( new Jet_Engine_CPT_Rest_Get_Post_Types() );
			$api_manager->register_endpoint( new Jet_Engine_CPT_Rest_Get_BI_Post_Type() );
			$api_manager->register_endpoint( new Jet_Engine_CPT_Rest_Edit_BI_Post_Type() );
			$api_manager->register_endpoint( new Jet_Engine_CPT_Rest_Reset_BI_Post_Type() );
			$api_manager->register_endpoint( new Jet_Engine_CPT_Rest_Copy_Post_Type() );

		}

		/**
		 * Return path to file inside component
		 *
		 * @param  string $path_inside_component $path Path inside component dir.
		 * @return string
		 */
		public function component_path( $path_inside_component ) {
			return jet_engine()->plugin_path( 'includes/components/post-types/' . $path_inside_component );
		}

		/**
		 * Return url to file inside component
		 *
		 * @param  string $path_inside_component $path Path inside component dir.
		 * @return string
		 */
		public function component_url( $path_inside_component ) {
			return jet_engine()->plugin_url( 'includes/components/post-types/' . $path_inside_component );
		}

		/**
		 * Register created post types
		 *
		 * @return void
		 */
		public function register_instances() {

			foreach ( $this->get_items() as $post_type ) {

				if ( ! empty( $post_type['meta_fields'] ) ) {

					$post_type['meta_fields'] = apply_filters( 'jet-engine/meta-boxes/raw-fields', $post_type['meta_fields'], $this );

					if ( ! empty( $post_type['hide_field_names'] ) ) {
						$this->meta_boxes_args[ $post_type['slug'] ]['hide_field_names'] = $post_type['hide_field_names'];
					}

					$this->meta_boxes_args[ $post_type['slug'] ]['id'] = $post_type['id'];

					$this->meta_boxes[ $post_type['slug'] ] = $post_type['meta_fields'];

					if ( jet_engine()->meta_boxes ) {
						jet_engine()->meta_boxes->store_fields( $post_type['slug'], $post_type['meta_fields'] );
					}

					unset( $post_type['meta_fields'] );
				}

				unset( $post_type['hide_field_names'] );

				if ( ! empty( $post_type['menu_position'] ) ) {
					$post_type['menu_position'] = intval( $post_type['menu_position'] );
				}

				if ( ! empty( $post_type['show_edit_link'] ) ) {

					$this->edit_links[ $post_type['slug'] ] = add_query_arg(
						array(
							'page' => 'jet-engine-cpt',
							'cpt_action' => 'edit',
							'id' => $post_type['id'],
						),
						admin_url( 'admin.php' )
					);

					unset( $post_type['show_edit_link'] );

				}

				/*
				if ( ! isset( $post_type['map_meta_cap'] ) ) {
					$post_type['map_meta_cap'] = true;
				}
				*/

				$post_type['map_meta_cap'] = true;

				if ( empty( $post_type['supports'] ) ) {
					$post_type['supports'] = false;
				}

				jet_engine()->add_instance( 'post-type', $post_type );

				register_post_type( $post_type['slug'], $post_type );

				if ( is_admin() ) {
					if ( ! empty( $post_type['admin_columns'] ) && $this->columns_allowed( $post_type['slug'] ) ) {

						if ( ! class_exists( 'Jet_Engine_CPT_Admin_Columns' ) ) {
							require_once $this->component_path( 'admin-columns.php' );
						}

						new Jet_Engine_CPT_Admin_Columns( $post_type['slug'], $post_type['admin_columns'] );

					}

					if ( ! empty( $post_type['admin_filters'] ) && $this->columns_allowed( $post_type['slug'] ) ) {

						if ( ! class_exists( 'Jet_Engine_CPT_Admin_Filters' ) ) {
							require_once $this->component_path( 'admin-filters.php' );
						}

						new Jet_Engine_CPT_Admin_Filters( $post_type['slug'], $post_type['admin_filters'] );

					}
				}

			}

		}

		public function init_edit_links( $current_screen ) {

			if ( ! $current_screen->post_type ) {
				return;
			}

			$edit_link = isset( $this->edit_links[ $current_screen->post_type ] ) ? $this->edit_links[ $current_screen->post_type ] : false;

			if ( ! $edit_link ) {
				return;
			}

			$current_screen->add_help_tab( array(
				'title'   => __( 'JetEngine Post Type', 'jet-engine' ),
				'id'      => 'jet-engine-cpt',
				'content' => sprintf(
					'<br><a href="%1$s" target="_blank">%2$s</a>',
					$edit_link,
					__( 'Edit post type settings', 'jet-engine' )
				),
			) );

		}

		/**
		 * Returns metafields for post type
		 *
		 * @param  [type] $post_type [description]
		 * @return [type]            [description]
		 */
		public function get_meta_fields_for_object( $object ) {

			if ( isset( $this->meta_fields[ $object ] ) ) {
				return $this->meta_fields[ $object ];
			}

			$meta_fields = array();

			if ( ! empty( $this->meta_boxes[ $object ] ) ) {
				$meta_fields = $this->meta_boxes[ $object ];
			}

			foreach ( $meta_fields as $i => $field ) {
				$meta_fields[ $i ]['title'] = isset( $meta_fields[ $i ]['title'] ) ? $meta_fields[ $i ]['title'] : $meta_fields[ $i ]['label'];
			}

			$this->meta_fields[ $object ] = apply_filters(
				'jet-engine/' . $this->object_type . '/' . $object . '/meta-fields',
				$meta_fields
			);

			return $this->meta_fields[ $object ];
		}

		/**
		 * Register metaboxes
		 *
		 * @return void
		 */
		public function register_meta_boxes() {

			if ( empty( $this->meta_boxes ) ) {
				return;
			}

			if ( ! class_exists( 'Jet_Engine_CPT_Meta' ) ) {
				require jet_engine()->plugin_path( 'includes/components/meta-boxes/post.php' );
			}

			foreach ( $this->meta_boxes as $post_type => $meta_box ) {

				$args = ! empty( $this->meta_boxes_args[ $post_type ] ) ? $this->meta_boxes_args[ $post_type ] : array();
				$is_built_in = isset( $args['is_built_in'] ) ? $args['is_built_in'] : false;

				Jet_Engine_Meta_Boxes_Option_Sources::instance()->find_meta_fields_with_save_custom( 
					'post',
					$post_type,
					$meta_box,
					$args['id'],
					$this->data,
					$is_built_in
				);

				$meta_instance = new Jet_Engine_CPT_Meta( $post_type, $meta_box, '', 'normal', 'high', $args );

				if ( ! empty( $this->edit_links[ $post_type ] ) ) {
					$meta_instance->add_edit_link( $this->edit_links[ $post_type ] );
				}

			}

		}

		/**
		 * Return admin pages for current instance
		 *
		 * @return array
		 */
		public function get_instance_pages() {

			$base_path = $this->component_path( 'pages/' );

			return array(
				'Jet_Engine_CPT_Page_List' => $base_path . 'list.php',
				'Jet_Engine_CPT_Page_Edit' => $base_path . 'edit.php',
			);
		}

		/**
		 * Returns available admin columns types
		 * @return [type] [description]
		 */
		public function get_admin_columns_types() {
			return array(
				array(
					'value' => 'meta_value',
					'label' => __( 'Meta Value', 'jet-engine' ),
				),
				array(
					'value' => 'post_terms',
					'label' => __( 'Post Terms', 'jet-engine' ),
				),
				array(
					'value' => 'post_id',
					'label' => __( 'Post ID', 'jet-engine' ),
				),
				array(
					'value' => 'custom_callback',
					'label' => __( 'Custom Callback', 'jet-engine' ),
				),
			);
		}

		/**
		 * Returns available labels list
		 *
		 * @return [type] [description]
		 */
		public function get_labels_list() {
			return array(
				array(
					'name'        => 'singular_name',
					'label'       => __( 'Singular name', 'jet-engine' ),
					'description' => __( 'Name for one object of this post type', 'jet-engine' ),
					'is_singular' => true,
					'default'     => '',
				),
				array(
					'name'        => 'add_new',
					'label'       => __( 'Add New', 'jet-engine' ),
					'description' => __( 'The add new text. The default is `Add New` for both hierarchical and non-hierarchical post types', 'jet-engine' ),
					'is_singular' => true,
					'default'     => _x( 'Add New %s%', 'Default value for add_new label', 'jet-engine' ),
				),
				array(
					'name'        => 'add_new_item',
					'label'       => __( 'Add New Item', 'jet-engine' ),
					'description' => __( 'Default is Add New Post/Add New Page', 'jet-engine' ),
					'is_singular' => true,
					'default'     => _x( 'Add New %s%', 'Default value for add_new_item label', 'jet-engine' ),
				),
				array(
					'name'        => 'new_item',
					'label'       => __( 'New Item', 'jet-engine' ),
					'description' => __( 'Default is New Post/New Page', 'jet-engine' ),
					'is_singular' => true,
					'default'     => _x( 'New %s%', 'Default value for new_item label', 'jet-engine' ),
				),
				array(
					'name'        => 'edit_item',
					'label'       => __( 'Edit Item', 'jet-engine' ),
					'description' => __( 'Default is Edit Post/Edit Page', 'jet-engine' ),
					'is_singular' => true,
					'default'     => _x( 'Edit %s%', 'Default value for edit_item label', 'jet-engine' ),
				),
				array(
					'name'        => 'view_item',
					'label'       => __( 'View Item', 'jet-engine' ),
					'description' => __( 'Default is View Post/View Page', 'jet-engine' ),
					'is_singular' => true,
					'default'     => _x( 'View %s%', 'Default value for view_item label', 'jet-engine' ),
				),
				array(
					'name'        => 'all_items',
					'label'       => __( 'All Items', 'jet-engine' ),
					'description' => __( 'String for the submenu', 'jet-engine' ),
					'is_singular' => false,
					'default'     => _x( 'All %s%', 'Default value for all_items label', 'jet-engine' ),
				),
				array(
					'name'        => 'search_items',
					'label'       => __( 'Search for items', 'jet-engine' ),
					'description' => __( 'Default is Search Posts/Search Pages', 'jet-engine' ),
					'is_singular' => false,
					'default'     => _x( 'Search for %s%', 'Default value for search_items label', 'jet-engine' ),
				),
				array(
					'name'        => 'parent_item_colon',
					'label'       => __( 'Parent Item', 'jet-engine' ),
					'description' => __( 'This string isn`t used on non-hierarchical types. In hierarchical ones the default is `Parent Page:`', 'jet-engine' ),
					'is_singular' => true,
					'default'     => _x( 'Parent %s%', 'Default value for parent_item_colon label', 'jet-engine' ),
				),
				array(
					'name'        => 'not_found',
					'label'       => __( 'Not found', 'jet-engine' ),
					'description' => __( 'Default is No posts found/No pages found', 'jet-engine' ),
					'is_singular' => false,
					'default'     => _x( 'Parent %s%', 'Default value for parent_item_colon label', 'jet-engine' ),
				),
				array(
					'name'        => 'not_found_in_trash',
					'label'       => __( 'Not found in trash', 'jet-engine' ),
					'description' => __( 'Default is No posts found in Trash/No pages found in Trash', 'jet-engine' ),
					'is_singular' => false,
					'default'     => _x( 'No %s% found in trash', 'Default value for not_found_in_trash label', 'jet-engine' ),
				),
				array(
					'name'        => 'menu_name',
					'label'       => __( 'Admin Menu', 'jet-engine' ),
					'description' => __( 'Default is the same as `name`', 'jet-engine' ),
					'is_singular' => false,
					'default'     => _x( '%s%', 'Default value for menu_name label', 'jet-engine' ),
				),
				array(
					'name'        => 'name_admin_bar',
					'label'       => __( 'Add New on Toolbar', 'jet-engine' ),
					'description' => __( 'String for use in New in Admin menu bar', 'jet-engine' ),
					'is_singular' => true,
					'default'     => _x( '%s%', 'Default value for name_admin_bar label', 'jet-engine' ),
				),
				array(
					'name'        => 'featured_image',
					'label'       => __( 'Featured Image', 'jet-engine' ),
					'description' => __( 'Default is Featured Image', 'jet-engine' ),
					'is_singular' => true,
					'default'     => _x( 'Featured Image', 'Default value for featured_image label', 'jet-engine' ),
				),
				array(
					'name'        => 'set_featured_image',
					'label'       => __( 'Set Featured Image', 'jet-engine' ),
					'description' => __( 'Default is Set featured image', 'jet-engine' ),
					'is_singular' => true,
					'default'     => _x( 'Set featured image', 'Default value for set_featured_image label', 'jet-engine' ),
				),
				array(
					'name'        => 'remove_featured_image',
					'label'       => __( 'Remove Featured Image', 'jet-engine' ),
					'description' => __( 'Default is Remove featured image', 'jet-engine' ),
					'is_singular' => true,
					'default'     => _x( 'Remove featured image', 'Default value for remove_featured_image label', 'jet-engine' ),
				),
				array(
					'name'        => 'use_featured_image',
					'label'       => __( 'Use Featured Image', 'jet-engine' ),
					'description' => __( 'Default is Use as featured image', 'jet-engine' ),
					'is_singular' => true,
					'default'     => _x( 'Use featured image', 'Default value for use_featured_image label', 'jet-engine' ),
				),
				array(
					'name'        => 'archives',
					'label'       => __( 'The post type archive label used in nav menus', 'jet-engine' ),
					'description' => __( 'String for use with archives in nav menus', 'jet-engine' ),
					'is_singular' => false,
					'default'     => _x( '%s%', 'Default value for archives label', 'jet-engine' ),
				),
				array(
					'name'        => 'insert_into_item',
					'label'       => __( 'Insert into post', 'jet-engine' ),
					'description' => __( 'String for the media frame button', 'jet-engine' ),
					'is_singular' => true,
					'default'     => _x( 'Insert into %s%', 'Default value for insert_into_item label', 'jet-engine' ),
				),
				array(
					'name'        => 'uploaded_to_this_item',
					'label'       => __( 'Uploaded to this post', 'jet-engine' ),
					'description' => __( 'String for the media frame filter', 'jet-engine' ),
					'is_singular' => true,
					'default'     => _x( 'Uploaded to this %s%', 'Default value for uploaded_to_this_item label', 'jet-engine' ),
				),
			);
		}

		/**
		 * Is meta fields functionality enabled
		 *
		 * @return boolean [description]
		 */
		public function is_meta_fields_enabled() {
			if ( jet_engine()->components->is_component_active( 'meta_boxes' ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Get allowed supports options
		 *
		 * @return array
		 */
		public function get_supports_options() {
			return array(
				array(
					'value' => 'title',
					'label' => __( 'Title', 'jet-engine' ),
				),
				array(
					'value' => 'editor',
					'label' => __( 'Editor', 'jet-engine' ),
				),
				array(
					'value' => 'comments',
					'label' => __( 'Comments', 'jet-engine' ),
				),
				array(
					'value' => 'revisions',
					'label' => __( 'Revisions', 'jet-engine' ),
				),
				array(
					'value' => 'trackbacks',
					'label' => __( 'Trackbacks', 'jet-engine' ),
				),
				array(
					'value' => 'author',
					'label' => __( 'Author', 'jet-engine' ),
				),
				array(
					'value' => 'excerpt',
					'label' => __( 'Excerpt', 'jet-engine' ),
				),
				array(
					'value' => 'page-attributes',
					'label' => __( 'Page Attributes', 'jet-engine' ),
				),
				array(
					'value' => 'thumbnail',
					'label' => __( 'Thumbnail (Featured Image)', 'jet-engine' ),
				),
				array(
					'value' => 'custom-fields',
					'label' => __( 'Custom Fields', 'jet-engine' ),
				),
				array(
					'value' => 'post-formats',
					'label' => __( 'Post Formats', 'jet-engine' ),
				),
			);
		}

		/**
		 * Returns available dashicons
		 *
		 * @return [type] [description]
		 */
		public function get_icons_options() {
			return array( 'menu','admin-site','dashboard','admin-media','admin-page','admin-comments','admin-appearance','admin-plugins','admin-users','admin-tools','admin-settings','admin-network','admin-generic','admin-home','admin-collapse','filter','admin-customizer','admin-multisite','admin-links','format-links','admin-post','format-standard','format-image','format-gallery','format-audio','format-video','format-chat','format-status','format-aside','format-quote','welcome-write-blog','welcome-edit-page','welcome-add-page','welcome-view-site','welcome-widgets-menus','welcome-comments','welcome-learn-more','image-crop','image-rotate','image-rotate-left','image-rotate-right','image-flip-vertical','image-flip-horizontal','image-filter','undo','redo','editor-bold','editor-italic','editor-ul','editor-ol','editor-quote','editor-alignleft','editor-aligncenter','editor-alignright','editor-insertmore','editor-spellcheck','editor-distractionfree','editor-expand','editor-contract','editor-kitchensink','editor-underline','editor-justify','editor-textcolor','editor-paste-word','editor-paste-text','editor-removeformatting','editor-video','editor-customchar','editor-outdent','editor-indent','editor-help','editor-strikethrough','editor-unlink','editor-rtl','editor-break','editor-code','editor-paragraph','editor-table','align-left','align-right','align-center','align-none','lock','unlock','calendar','calendar-alt','visibility','hidden','post-status','edit','post-trash','trash','sticky','external','arrow-up','arrow-down','arrow-left','arrow-right','arrow-up-alt','arrow-down-alt','arrow-left-alt','arrow-right-alt','arrow-up-alt2','arrow-down-alt2','arrow-left-alt2','arrow-right-alt2','leftright','sort','randomize','list-view','exerpt-view','excerpt-view','grid-view','move','hammer','art','migrate','performance','universal-access','universal-access-alt','tickets','nametag','clipboard','heart','megaphone','schedule','wordpress','wordpress-alt','pressthis','update','screenoptions','cart','feedback','cloud','translation','tag','category','archive','tagcloud','text','media-archive','media-audio','media-code','media-default','media-document','media-interactive','media-spreadsheet','media-text','media-video','playlist-audio','playlist-video','controls-play','controls-pause','controls-forward','controls-skipforward','controls-back','controls-skipback','controls-repeat','controls-volumeon','controls-volumeoff','yes','no','no-alt','plus','plus-alt','plus-alt2','minus','dismiss','marker','star-filled','star-half','star-empty','flag','info','warning','share','share1','share-alt','share-alt2','twitter','rss','email','email-alt','facebook','facebook-alt','networking','googleplus','location','location-alt','camera','images-alt','images-alt2','video-alt','video-alt2','video-alt3','vault','shield','shield-alt','sos','search','slides','analytics','chart-pie','chart-bar','chart-line','chart-area','groups','businessman','id','id-alt','products','awards','forms','testimonial','portfolio','book','book-alt','download','upload','backup','clock','lightbulb','microphone','desktop','laptop','tablet','smartphone','phone','smiley','index-card','carrot','building','store','album','palmtree','tickets-alt','money','thumbs-up','thumbs-down','layout','paperclip' );
		}

		/**
		 * Returns current menu page title (for JetEngine submenu)
		 * @return [type] [description]
		 */
		public function get_page_title() {
			return __( 'Post Types', 'jet-engine' );
		}

		/**
		 * Returns current instance slug
		 *
		 * @return [type] [description]
		 */
		public function instance_slug() {
			return 'cpt';
		}

		/**
		 * Returns default config for add/edit page
		 *
		 * @param  array  $config [description]
		 * @return [type]         [description]
		 */
		public function get_admin_page_config( $config = array() ) {

			$default_settings = array(
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_nav_menus'  => true,
				'show_in_rest'       => true,
				'query_var'          => true,
				'rewrite'            => true,
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
				'has_archive'        => true,
				'menu_icon'          => 'dashicons-format-standard',
				'supports'           => array( 'title', 'editor' ),
			);

			$default = array(
				'api_path_edit'       => '', // Should be set for apropriate page context
				'api_path_get'        => jet_engine()->api->get_route( 'get-post-type' ),
				'edit_button_label'   => '', // Should be set for apropriate page context
				'item_id'             => false,
				'redirect'            => '', // Should be set for apropriate page context
				'general_settings'    => array( 'name' => '' ),
				'labels'              => array( 'singular_name' => '' ),
				'advanced_settings'   => $default_settings,
				'meta_fields'         => array(),
				'admin_columns'       => array(),
				'admin_filters'       => array(),
				'supports'            => $this->get_supports_options(),
				'columns_types'       => $this->get_admin_columns_types(),
				'icons'               => $this->get_icons_options(),
				'labels_list'         => $this->get_labels_list(),
				'meta_fields_enabled' => $this->is_meta_fields_enabled(),
				'notices'             => array(
					'name'    => __( 'Please, set post type name', 'jet-engine' ),
					'slug'    => __( 'Please, set post type slug', 'jet-engine' ),
					'success' => __( 'Post type updated', 'jet-engine' ),
				),
			);

			return array_merge( $default, $config );

		}

	}

}
