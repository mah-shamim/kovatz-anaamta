<?php
/**
 * Options pages manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Options_Pages' ) ) {

	/**
	 * Define Jet_Engine_Options_Pages class
	 */
	class Jet_Engine_Options_Pages extends Jet_Engine_Base_WP_Intance {

		/**
		 * Base slug for CPT-related pages
		 * @var string
		 */
		public $page = 'jet-engine-options-pages';

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
		 * Set object type
		 * @var string
		 */
		public $object_type = 'options';

		/**
		 * All options pages objects
		 *
		 * @var array
		 */
		public $registered_pages = array();

		/**
		 * Options list to use as select options
		 *
		 * @var array
		 */
		public $options_list = array();

		/**
		 * Init data instance
		 *
		 * @return [type] [description]
		 */
		public function init_data() {

			if ( ! class_exists( 'Jet_Engine_Base_Data' ) ) {
				require_once jet_engine()->plugin_path( 'includes/base/base-data.php' );
			}

			require $this->component_path( 'data.php' );

			$this->data = new Jet_Engine_Options_Data( $this );

		}

		/**
		 * Initiizlize post type specific API endpoints
		 *
		 * @param  Jet_Engine_REST_API $api_manager API manager instance.
		 * @return void
		 */
		public function init_rest( $api_manager ) {

			require_once $this->component_path( 'rest-api/add-options-page.php' );
			require_once $this->component_path( 'rest-api/edit-options-page.php' );
			require_once $this->component_path( 'rest-api/get-options-page.php' );
			require_once $this->component_path( 'rest-api/delete-options-page.php' );
			require_once $this->component_path( 'rest-api/get-options-pages.php' );

			$api_manager->register_endpoint( new Jet_Engine_Rest_Add_Options_Page() );
			$api_manager->register_endpoint( new Jet_Engine_Rest_Edit_Options_Page() );
			$api_manager->register_endpoint( new Jet_Engine_Rest_Get_Options_Page() );
			$api_manager->register_endpoint( new Jet_Engine_Rest_Delete_Options_Page() );
			$api_manager->register_endpoint( new Jet_Engine_Rest_Get_Options_Pages() );

		}

		/**
		 * Return path to file inside component
		 *
		 * @param  string $path_inside_component
		 * @return string
		 */
		public function component_path( $path_inside_component ) {
			return jet_engine()->plugin_path( 'includes/components/options-pages/' . $path_inside_component );
		}

		/**
		 * Return url to file inside component
		 *
		 * @param  string $path_inside_component
		 * @return string
		 */
		public function component_url( $path_inside_component ) {
			return jet_engine()->plugin_url( 'includes/components/options-pages/' . $path_inside_component );
		}

		/**
		 * Register new options page by passed arguments
		 *
		 * @return [type] [description]
		 */
		public function register_new_options_page( $args = array() ) {
			$page                                  = new Jet_Engine_Options_Page_Factory( $args );
			$this->registered_pages[ $page->slug ] = $page;
			$this->options_list[]                  = $page->get_options_for_select();
		}

		/**
		 * Register created post types
		 *
		 * @return void
		 */
		public function register_instances() {

			require_once $this->component_path( 'options-page.php' );

			$children = array();

			foreach ( $this->get_items() as $item ) {

				jet_engine()->add_instance( 'options-page', $item );

				if ( empty( $item['parent'] ) ) {
					$this->register_new_options_page( $item );
				} else {
					$children[] = $item;
				}

				Jet_Engine_Meta_Boxes_Option_Sources::instance()->find_meta_fields_with_save_custom( 
					'options',
					$item['slug'],
					$item['fields'],
					$item['id'],
					$this->data
				);

			}

			if ( ! empty( $children ) ) {
				foreach ( $children as $item ) {
					$this->register_new_options_page( $item );
				}
			}

		}

		/**
		 * Returns all registered options (or depends on context) to use in select
		 *
		 * @return [type] [description]
		 */
		public function get_options_for_select( $context = 'plain', $where = 'elementor' ) {

			$result = array();

			foreach ( $this->options_list as $slug => $data ) {

				$group        = array();
				$blocks_group = array();

				foreach ( $data['options'] as $name => $field_data ) {

					switch ( $context ) {

						case 'plain':

							$black_list = array( 'repeater', 'html', 'tab', 'accordion', 'endpoint' );

							if ( ! in_array( $field_data['type'], $black_list ) ) {
								$group[ $name ] = $field_data['title'];

								$blocks_group[] = array(
									'value' => $name,
									'label' => $field_data['title'],
								);
							}

							break;

						case 'repeater':

							if ( 'repeater' === $field_data['type'] ) {
								$group[ $name ] = $field_data['title'];

								$blocks_group[] = array(
									'value' => $name,
									'label' => $field_data['title'],
								);

							}

							break;

						case 'media':

							if ( 'media' === $field_data['type'] ) {
								$group[ $name ] = $field_data['title'];

								$blocks_group[] = array(
									'value' => $name,
									'label' => $field_data['title'],
								);

							}

							break;

						case 'gallery':

							if ( 'gallery' === $field_data['type'] ) {
								$group[ $name ] = $field_data['title'];

								$blocks_group[] = array(
									'value' => $name,
									'label' => $field_data['title'],
								);

							}

							break;

						case 'all':

							$group[ $name ] = $field_data['title'];

							$blocks_group[] = array(
								'value' => $name,
								'label' => $field_data['title'],
							);

							break;

					}

				}

				if ( ! empty( $group ) ) {
					if ( 'blocks' === $where ) {
						$result[] = array(
							'label'  => $data['label'],
							'values' => $blocks_group,
						);
					} else {
						$result[] = array(
							'label'   => $data['label'],
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
		 * Returns all options pages list to use in select
		 *
		 * @return array
		 */
		public function get_options_pages_for_select() {
			$result = array();
			$items  = jet_engine()->options_pages->data->get_items();

			foreach ( $items as $item ) {
				$item['labels']          = maybe_unserialize( $item['labels'] );
				$result[ $item['slug'] ] = $item['labels']['name'];
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
				'Jet_Engine_Options_Page_List' => $base_path . 'list.php',
				'Jet_Engine_Options_Page_Edit' => $base_path . 'edit.php',
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
			return __( 'Options Pages', 'jet-engine' );
		}

		/**
		 * Returns current instance slug
		 *
		 * @return [type] [description]
		 */
		public function instance_slug() {
			return 'options-pages';
		}

		/**
		 * Returns default config for add/edit page
		 *
		 * @param  array  $config [description]
		 * @return [type]         [description]
		 */
		public function get_admin_page_config( $config = array() ) {

			$default_settings = array(
				'name'       => '',
				'slug'       => '',
				'menu_name'  => '',
				'parent'     => '',
				'icon'       => 'dashicons-admin-generic',
				'capability' => 'manage_options',
				'position'   => '',
			);

			$default = array(
				'api_path_edit'       => '', // Should be set for apropriate page context
				'api_path_get'      => jet_engine()->api->get_route( 'get-options-page' ),
				'edit_button_label' => '', // Should be set for apropriate page context
				'item_id'           => false,
				'redirect'          => '', // Should be set for apropriate page context
				'general_settings'  => $default_settings,
				'fields'            => array(),
				'icons'             => $this->get_icons_options(),
				'notices'           => array(
					'name'    => __( 'Please, set page title', 'jet-engine' ),
					'slug'    => __( 'Please, set page slug', 'jet-engine' ),
					'success' => __( 'Page updated', 'jet-engine' ),
				),
				'help_links'        => array(
					array(
						'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-options-page-overview/?utm_source=jetengine&utm_medium=options-page&utm_campaign=need-help',
						'label' => __( 'Creating an Options page with JetEngine', 'jet-engine' ),
					),
					array(
						'url'   => 'https://gist.github.com/MjHead/49ebe7ecc20bff9aaf8516417ed27c38',
						'label' => __( 'Getting an option value in the PHP code', 'jet-engine' ),
					),
				),
			);

			return array_merge( $default, $config );

		}

	}

}
