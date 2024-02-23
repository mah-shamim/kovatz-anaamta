<?php
namespace Jet_Engine\Relations;

/**
 * Relations manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Relations Manager class
 */
class Manager extends \Jet_Engine_Base_WP_Intance {

	/**
	 * Base slug for CPT-related pages
	 * @var string
	 */
	public $page = 'jet-engine-relations';

	/**
	 * Action request key
	 *
	 * @var string
	 */
	public $action_key = 'cpt_relation_action';

	/**
	 * Set object type
	 * @var string
	 */
	public $object_type = '';

	/**
	 * Active relations list
	 *
	 * @var array
	 */
	public $_active_relations = array();

	/**
	 * Legacy relations instance
	 * @var null
	 */
	public $legacy = null;

	/**
	 * Storage-related manager instance
	 * @var null
	 */
	public $storage = null;

	/**
	 * Listings integration manager
	 *
	 * @var null
	 */
	public $listing = null;

	/**
	 * Sources manager instance
	 *
	 * @var null
	 */
	public $sources = null;

	/**
	 * Hierarchy manager instance
	 *
	 * @var null
	 */
	public $hierachy = null;

	/**
	 * Types helper instance
	 *
	 * @var null
	 */
	public $types_helper = null;

	/**
	 * Constructor for the class
	 */
	function __construct() {

		add_action( 'init', array( $this, 'register_instances' ), 11 );

		$this->init_data();

		add_action( 'jet-engine/rest-api/init-endpoints', array( $this, 'init_rest' ) );

		$this->init_admin_pages();

		if ( wp_doing_ajax() ) {
			require_once $this->component_path( 'ajax-handlers.php' );
			new Ajax_Handlers();
		}

		require_once $this->component_path( 'listing.php' );
		$this->listing = new Listing();

	}

	/**
	 * Register relations related macros
	 *
	 * @return [type] [description]
	 */
	public function register_macros() {

		require_once $this->component_path( 'macros/get-related-items.php' );
		require_once $this->component_path( 'macros/get-related-siblings.php' );
		require_once $this->component_path( 'macros/get-related-items-count.php' );
		require_once $this->component_path( 'macros/get-related-item-meta.php' );

		new Macros\Get_Related_Items();
		new Macros\Get_Related_Siblings();
		new Macros\Get_Related_Items_Count();
		new Macros\Get_Related_Item_Meta();

	}

	/**
	 * [register_elementor_dynamic_tags description]
	 * @return [type] [description]
	 */
	public function register_elementor_dynamic_tags( $dynamic_tags, $tags_module ) {

		require_once $this->component_path( 'dynamic-tags/related-items.php' );
		require_once $this->component_path( 'dynamic-tags/related-siblings.php' );
		require_once $this->component_path( 'dynamic-tags/related-items-count.php' );
		require_once $this->component_path( 'dynamic-tags/related-item-meta.php' );
		
		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Related_Items() );
		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Related_Siblings() );
		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Related_Items_Count() );
		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Related_Item_Meta() );
	}

	/**
	 * Initiizlize post type specific API endpoints
	 *
	 * @param  Jet_Engine_REST_API $api_manager API manager instance.
	 * @return void
	 */
	public function init_rest( $api_manager ) {

		require_once $this->component_path( 'rest-api/add-relation.php' );
		require_once $this->component_path( 'rest-api/edit-relation.php' );
		require_once $this->component_path( 'rest-api/get-relation.php' );
		require_once $this->component_path( 'rest-api/delete-relation.php' );
		require_once $this->component_path( 'rest-api/get-relations.php' );

		$api_manager->register_endpoint( new Rest\Add_Relation() );
		$api_manager->register_endpoint( new Rest\Edit_Relation() );
		$api_manager->register_endpoint( new Rest\Get_Relation() );
		$api_manager->register_endpoint( new Rest\Delete_Relation() );
		$api_manager->register_endpoint( new Rest\Get_Relations() );

	}

	/**
	 * Return allowed relations types
	 *
	 * @return array
	 */
	public function get_relations_types() {

		return array(
			'one_to_one'   => __( 'One to one', 'jet-engine' ),
			'one_to_many'  => __( 'One to many', 'jet-engine' ),
			'many_to_many' => __( 'Many to many', 'jet-engine' ),
		);

	}

	/**
	 * Returns allowed relations types prepared to use in JS formst
	 *
	 * @return [type] [description]
	 */
	public function get_relations_types_for_js() {

		$result = array();

		foreach ( $this->get_relations_types() as $value => $label ) {
			$result[] = array(
				'value' => $value,
				'label' => $label,
			);
		}

		return $result;
	}

	/**
	 * Get relations for JS
	 * @return [type] [description]
	 */
	public function get_relations_for_js( $raw = false, $placeholder = false ) {

		$relations = $this->get_active_relations();
		$result    = array();

		if ( $placeholder ) {
			if ( $raw ) {
				$result[''] = $placeholder;
			} else {
				$result[] = array(
					'value' => '',
					'label' => $placeholder,
				);
			}
		}

		if ( ! empty( $relations ) ) {
			foreach ( $relations as $key => $relation ) {
				if ( $raw ) {
					$result[ $key ] = $relation->get_relation_name();
				} else {
					$result[] = array(
						'value' => $key,
						'label' => $relation->get_relation_name(),
					);
				}
			}
		}

		return $result;

	}

	/**
	 * Returns allowed meta fields list of all existing relation or for requested relation if $relation_id parameter was passed
	 *
	 * @return [type] [description]
	 */
	public function get_active_relations_meta_fields( $relation_id = false ) {

		$relations = $this->get_active_relations( $relation_id );

		if ( ! is_array( $relations ) ) {
			$relations = array( $relations );
		}

		$result = array();

		foreach ( $relations as $relation ) {

			$meta_fields = $relation->get_meta_fields( false, function( $item ) {
				return array(
					'name'  => $item['name'],
					'type'  => $item['type'],
					'title' => $item['title'],
				);
			} );

			if ( ! empty( $meta_fields ) ) {
				$result[ $relation->get_id() ] = array(
					'label'  => $relation->get_relation_name(),
					'fields' => $meta_fields,
				);
			}

		}

		if ( ! $relation_id ) {
			return $result;
		} else {
			return isset( $result[ $relation_id ] ) ? $result[ $relation_id ] : array();
		}

	}

	/**
	 * Return path to file inside component
	 *
	 * @param  [type] $path_inside_component [description]
	 * @return [type]                        [description]
	 */
	public function component_path( $path_inside_component ) {
		return jet_engine()->plugin_path( 'includes/components/relations/' . $path_inside_component );
	}

	/**
	 * Init data instance
	 *
	 * @return [type] [description]
	 */
	public function init_data() {

		require_once $this->component_path( 'data.php' );
		require_once $this->component_path( 'types-helper.php' );
		require_once $this->component_path( 'sources.php' );
		require_once $this->component_path( 'mix-type-helper.php' );
		require_once $this->component_path( 'storage/manager.php' );

		$this->types_helper = new Types_Helper();
		$this->sources      = new Sources();
		$this->storage      = new Storage\Manager();
		$this->data         = new Data( $this );

	}

	/**
	 * Initizlize 3rd party compatibility classes
	 *
	 * @return [type] [description]
	 */
	public function init_3rd_party() {

		// Register macros for relation only if we have at least 1 relation
		add_action( 'jet-engine/register-macros', array( $this, 'register_macros' ) );

		// Forms (JetEngine and JetFormBuilder) compatibility
		require_once $this->component_path( 'forms/manager.php' );
		Forms\Manager::instance();

		// Filters compatibility class
		if ( class_exists( '\Jet_Smart_Filters' ) ) {
			require_once $this->component_path( 'filters.php' );
			new Filters();
		}

		// Elementor Dynamic tags
		add_action( 'jet-engine/elementor-views/dynamic-tags/register', array( $this, 'register_elementor_dynamic_tags' ), 10, 2 );

	}

	/**
	 * Register relations meta boxes
	 *
	 * @return void
	 */
	public function register_instances() {

		require $this->component_path( 'legacy/manager.php' );
		$this->legacy = new Legacy\Manager();

		$relations = $this->data->get_items();
		$relations = apply_filters( 'jet-engine/relations/raw-relations', $relations );

		$legacy_relations     = array();
		$has_3rd_party_legacy = apply_filters( 'jet-engine/relations/registered-relation', array() );

		if ( empty( $relations ) ) {

			// Ensure 3rd party legacy relations processed even we don't have any custom relations
			if ( ! empty( $has_3rd_party_legacy ) ) {
				$this->register_legacy_relations( $legacy_relations );
			}

			return;
		}

		// Initialize 3rd party compatibility classes only when at least 1 relation exists
		$this->init_3rd_party();

		require_once $this->component_path( 'relation.php' );

		$has_hierarchy = false;

		foreach ( $relations as $rel_id => $relation ) {

			if ( ! empty( $relation['is_legacy'] ) ) {
				$legacy_relations[] = $relation;
				continue;
			}

			$rel_id = $relation['id'];

			if ( isset( $relation['args'] ) && isset( $relation['status'] ) && isset( $relation['slug'] ) ) {
				$relation = $relation['args'];
			}

			$relation['id'] = $rel_id;

			$relation_instance = new Relation( $rel_id, $relation );

			if ( $relation_instance->get_args( 'parent_rel' ) ) {
				$has_hierarchy = true;
			}

			if ( $relation_instance->is_valid() ) {
				$this->_active_relations[ $rel_id ] = $relation_instance;
			}

		}

		// Setup admin filters compatibility after relations registration
		if ( is_admin() ) {
			require_once $this->component_path( 'admin-filters.php' );
			new Admin_Filters();
		}

		if ( $has_hierarchy ) {
			require_once $this->component_path( 'hierarchy.php' );
			$this->hierachy = new Hierarchy();
		}

		if ( ! empty( $legacy_relations ) || ! empty( $has_3rd_party_legacy ) ) {
			$this->register_legacy_relations( $legacy_relations );
		}

		add_action( 'jet-engine/relation/update/after', array( $this, 'flush_cache' ), 10, 0 );
		add_action( 'jet-engine/relation/delete/after', array( $this, 'flush_cache' ), 10, 0 );
		add_action( 'jet-engine/relation/update-all-meta/after', array( $this, 'flush_cache' ), 10, 0 );

	}

	/**
	 * Flush cache after relation(meta) update / delete
	 *
	 * @return void
	 */
	public function flush_cache() {
		wp_cache_flush();
	}

	/**
	 * Register legacy relations instances
	 *
	 * @param  array  $legacy_relations [description]
	 * @return [type]                   [description]
	 */
	public function register_legacy_relations($legacy_relations = array() ) {
		$this->legacy->set_legacy_relations( $legacy_relations );
		$this->legacy->register_instances();
	}

	/**
	 * Add active relation to relations list
	 */
	public function add_active_relation( $rel_id, $relation_instance ) {
		$this->_active_relations[ $rel_id ] = $relation_instance;
	}

	/**
	 * Returns active relations list
	 *
	 * @return [type] [description]
	 */
	public function get_active_relations( $rel_id = false ) {

		if ( ! $rel_id ) {
			return $this->_active_relations;
		} else {
			return isset( $this->_active_relations[ $rel_id ] ) ? $this->_active_relations[ $rel_id ] : false;
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
			'Jet_Engine_Relations_Page_List' => $base_path . 'list.php',
			'Jet_Engine_Relations_Page_Edit' => $base_path . 'edit.php',
		);
	}

	/**
	 * Returns current menu page title (for JetEngine submenu)
	 * @return [type] [description]
	 */
	public function get_page_title() {
		return __( 'Relations', 'jet-engine' );
	}

	/**
	 * Returns current instance slug
	 *
	 * @return [type] [description]
	 */
	public function instance_slug() {
		return 'relations';
	}

	/**
	 * Returns default config for add/edit page
	 *
	 * @param  array  $config [description]
	 * @return [type]         [description]
	 */
	public function get_admin_page_config( $config = array() ) {

		$default = array(
			'api_path_edit'       => '', // Should be set for apropriate page context
			'api_path_get'        => jet_engine()->api->get_route( 'get-relation' ),
			'edit_button_label'   => '', // Should be set for apropriate page context
			'item_id'             => false,
			'redirect'            => '', // Should be set for apropriate page context
			'nonce'               => wp_create_nonce( 'jet-engine-relations' ),
			'args'                => array(
				'parent_object'  => 'posts::post',
				'child_object'   => 'posts::page',
				'type'           => 'one_to_one',
				'parent_control' => true,
				'child_control'  => true,
				'labels'         => array(
					'name' => '',
				),
			),
			'relations_types'     => $this->get_relations_types_for_js(),
			'notices'             => array(
				'success' => __( 'Relationship updated', 'jet-engine' ),
			),
		);

		return array_merge( $default, $config );

	}

	/**
	 * Returns list of supported field types for relations builder admin UI
	 *
	 * @return [type] [description]
	 */
	public function field_types_supports() {
		return array(
			'select',
			'radio',
			'checkbox',
			'textarea',
			'media',
			'date',
			'time',
			'textarea',
			'wysiwyg',
			'datetime-local',
			'gallery',
			'text',
		);
	}

	/**
	 * Legacy part to ensure 3rd parties used jet_engine()->relations->... methods will continue to work properly
	 */

	public function is_relation_key( $key ) {
		return $this->legacy->is_relation_key( $key );
	}

	public function get_relation_info( $key ) {
		return $this->legacy->get_relation_info( $key );
	}

	public function get_related_posts( $args = array() ) {
		return $this->legacy->get_related_posts( $args );
	}

	public function process_meta( $result = null, $post_id = null, $meta_key = '', $related_posts = array() ) {
		return $this->legacy->process_meta( $result, $post_id, $meta_key, $related_posts );
	}

}
