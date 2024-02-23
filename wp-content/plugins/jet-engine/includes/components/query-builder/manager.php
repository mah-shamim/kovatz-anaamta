<?php
namespace Jet_Engine\Query_Builder;
/**
 * Options pages manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! trait_exists( '\Jet_Engine_Notices_Trait' ) ) {
	require_once jet_engine()->plugin_path( 'includes/traits/notices.php' );
}

/**
 * Define Jet_Engine_Glossaries class
 */
class Manager extends \Jet_Engine_Base_WP_Intance {

	/**
	 * Instance.
	 *
	 * Holds query builder instance.
	 *
	 * @access public
	 * @static
	 *
	 * @var Plugin
	 */
	public static $instance = null;

	/**
	 * Base slug for CPT-related pages
	 * @var string
	 */
	public $page = 'jet-engine-query';

	/**
	 * Action request key
	 *
	 * @var string
	 */
	public $action_key = 'query_action';

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
	public $object_type = 'query';

	public $types;
	public $advanced_fields = array();
	public $queries = array();
	public $listings;
	public $editor;

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the plugin class is loaded or can be loaded.
	 *
	 * @access public
	 * @static
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {

			self::$instance = new self();

		}

		return self::$instance;

	}

	/**
	 * Constructor for the class
	 */
	function __construct() {

		add_action( 'init', array( $this, 'register_instances' ), 11 );

		$this->init_data();

		add_action( 'jet-engine/rest-api/init-endpoints', array( $this, 'init_rest' ) );
		add_action( 'jet-engine/meta-boxes/init-options-sources', array( $this, 'init_options_source' ) );

		$this->init_admin_pages();

	}

	public function init_options_source() {
		require_once $this->component_path( 'meta-fields-options-source.php' );
		new Meta_Fields_Options_Source();
	}

	/**
	 * Init data instance
	 *
	 * @return [type] [description]
	 */
	public function init_data() {

		if ( ! class_exists( '\Jet_Engine_Base_Data' ) ) {
			require_once jet_engine()->plugin_path( 'includes/base/base-data.php' );
		}

		require $this->component_path( 'data.php' );

		$this->data = new Data( $this );

	}

	/**
	 * Initiizlize post type specific API endpoints
	 *
	 * @param  Jet_Engine_REST_API $api_manager API manager instance.
	 * @return void
	 */
	public function init_rest( $api_manager ) {

		require_once $this->component_path( 'rest-api/add-query.php' );
		require_once $this->component_path( 'rest-api/edit-query.php' );
		require_once $this->component_path( 'rest-api/get-query.php' );
		require_once $this->component_path( 'rest-api/delete-query.php' );
		require_once $this->component_path( 'rest-api/get-queries.php' );
		require_once $this->component_path( 'rest-api/search-preview.php' );
		require_once $this->component_path( 'rest-api/update-preview.php' );
		require_once $this->component_path( 'rest-api/search-query-field-options.php' );

		$api_manager->register_endpoint( new Rest\Add_Query() );
		$api_manager->register_endpoint( new Rest\Edit_Query() );
		$api_manager->register_endpoint( new Rest\Get_Query() );
		$api_manager->register_endpoint( new Rest\Delete_Query() );
		$api_manager->register_endpoint( new Rest\Get_Queries() );
		$api_manager->register_endpoint( new Rest\Search_Preview() );
		$api_manager->register_endpoint( new Rest\Update_Preview() );
		$api_manager->register_endpoint( new Rest\Search_Query_Field_Options() );

	}

	/**
	 * Return path to file inside component
	 *
	 * @param  [type] $path_inside_component [description]
	 * @return [type]                        [description]
	 */
	public function component_path( $path_inside_component ) {
		return jet_engine()->plugin_path( 'includes/components/query-builder/' . $path_inside_component );
	}

	/**
	 * Return URL of the file inside component
	 *
	 * @param  [type] $path_inside_component [description]
	 * @return [type]                        [description]
	 */
	public function component_url( $path_inside_component ) {
		return jet_engine()->plugin_url( 'includes/components/query-builder/' . $path_inside_component );
	}

	/**
	 * Register query instances where it required
	 * @return [type] [description]
	 */
	public function register_instances() {

		require $this->component_path( 'query-editor.php' );
		require $this->component_path( 'listings/manager.php' );
		require $this->component_path( 'query-gateway/manager.php' );
		require $this->component_path( 'helpers/posts-per-page-manager.php' );
		require $this->component_path( 'traits/query-count.php' );

		$this->editor   = new Query_Editor();
		$this->listings = new Listings\Manager();

		new Query_Gateway\Manager;

		do_action( 'jet-engine/query-builder/init', $this );

		$this->setup_queries();

		add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', array( $this, 'register_visibility_conditions' ) );
		add_action( 'jet-engine/elementor-views/dynamic-tags/register', array( $this, 'register_dynamic_tags' ), 10, 2 );

		add_filter( 'jet-engine/modules/dynamic-visibility/condition/args', array( $this, 'modify_condition_args_for_query_count' ) );

		add_action( 'jet-engine/register-macros', array( $this, 'register_macros' ) );

	}

	public function register_dynamic_tags( $dynamic_tags, $tags_module ) {
		require_once $this->component_path( 'dynamic-tags/query-count.php' );
		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Query_Count_Tag() );
	}

	public function register_visibility_conditions( $manager ) {
		require $this->component_path( 'conditions/has-items.php' );
		require $this->component_path( 'conditions/has-not-items.php' );

		$manager->register_condition( new Conditions\Has_Items() );
		$manager->register_condition( new Conditions\Has_Not_Items() );
	}

	public function register_macros() {

		require_once $this->component_path( 'macros/query-count.php' );
		require_once $this->component_path( 'macros/query-results.php' );

		new Macros\Query_Count_Macro();
		new Macros\Query_Results_Macro();

	}

	public function modify_condition_args_for_query_count( $args ) {

		if ( empty( $args['condition_settings']['__dynamic__']['jedv_field'] ) ) {
			return $args;
		}

		if ( false === strpos( $args['condition_settings']['__dynamic__']['jedv_field'], 'name="jet-query-count"' ) &&
			 false === strpos( $args['condition_settings']['__dynamic__']['jedv_field'], '"macros":"query_count"' )
		) {
			return $args;
		}

		$args['field'] = strip_tags( $args['field'] );

		return $args;
	}

	/**
	 * Ensure query factory class is included
	 * @return [type] [description]
	 */
	public function include_factory() {

		if ( ! class_exists( 'Jet_Engine\Query_Builder\Query_Factory' ) ) {
			require $this->component_path( 'query-factory.php' );
		}

		if ( ! class_exists( 'Jet_Engine\Query_Builder\Helpers\Empty_Items_Replacer' ) ) {
			require $this->component_path( 'helpers/empty-items-replacer.php' );
		}
	}

	/**
	 * Setup registeed query objects
	 *
	 * @return [type] [description]
	 */
	public function setup_queries() {

		$queries = $this->data->get_items();

		if ( empty( $queries ) ) {
			return;
		}

		$this->include_factory();

		foreach ( $queries as $query ) {
			$factory = new Query_Factory( $query );
			$this->queries[ $query['id'] ] = $factory->get_query();
		}

		// Enable this only if need from theme or plugin
		// example: add_filter( 'jet-engine/query-builder/flush-object-cache-on-save', '__return_true' );
		if ( apply_filters( 'jet-engine/query-builder/flush-object-cache-on-save', false ) ) {
			// If we have some queries created, add flush cache option to ensure queries data updated when site data is changed
			add_action( 'save_post', 'wp_cache_flush', 9999 );
		}
		
	}

	public function get_query_by_id( $id = null ) {

		if ( ! $id ) {
			return false;
		}

		return isset( $this->queries[ $id ] ) ? $this->queries[ $id ] : false;
	}

	public function get_queries() {
		return $this->queries;
	}

	/**
	 * Return admin pages for current instance
	 *
	 * @return array
	 */
	public function get_instance_pages() {

		return array(
			__NAMESPACE__ . '\Pages\Queries_List' => $this->component_path( 'pages/list.php' ),
			__NAMESPACE__ . '\Pages\Edit'         => $this->component_path( 'pages/edit.php' ),
		);

	}

	/**
	 * Returns current menu page title (for JetEngine submenu)
	 * @return [type] [description]
	 */
	public function get_page_title() {
		return __( 'Query Builder', 'jet-engine' );
	}

	/**
	 * Returns current instance slug
	 *
	 * @return [type] [description]
	 */
	public function instance_slug() {
		return 'query';
	}

	/**
	 * Returns queries list for the options
	 *
	 * @return [type] [description]
	 */
	public function get_queries_for_options( $blocks = false, $type = null, $raw = false ) {

		$items  = $this->data->get_items();
		$result = array();

		if ( $blocks ) {

			if ( ! $raw ) {
				$result[] = array(
					'value' => '',
					'label' => __( 'Select query...', 'jet-engine' ),
				);
			}

		} elseif ( ! $raw ) {

			$result[''] = __( 'Select query...', 'jet-engine' );

		}

		foreach ( $items as $item ) {

			$labels = maybe_unserialize( $item['labels'] );

			if ( $type ) {
				$args = maybe_unserialize( $item['args'] );

				if ( empty( $args['query_type'] ) || $args['query_type'] !== $type ) {
					continue;
				}

			}

			if ( $blocks ) {
				$result[] = array(
					'value' => $item['id'],
					'label' => $labels['name'],
				);
			} else {
				$result[ $item['id'] ] = $labels['name'];
			}
		}

		return $result;

	}

	public function get_orderby_options( $for = 'posts' ) {

		$result = array();

		switch ( $for ) {
			case 'posts':
				$result = array(
					array(
						'value' => 'ID',
						'label' => __( 'Order by post id', 'jet-engine' ),
					),
					array(
						'value' => 'author',
						'label' => __( 'By author', 'jet-engine' ),
					),
					array(
						'value' => 'title',
						'label' => __( 'By title', 'jet-engine' ),
					),
					array(
						'value' => 'name',
						'label' => __( 'By post name (post slug)', 'jet-engine' ),
					),
					array(
						'value' => 'type',
						'label' => __( 'By post type (available since version 4.0)', 'jet-engine' ),
					),
					array(
						'value' => 'date',
						'label' => __( 'By date', 'jet-engine' ),
					),
					array(
						'value' => 'modified',
						'label' => __( 'By last modified date', 'jet-engine' ),
					),
					array(
						'value' => 'parent',
						'label' => __( 'By post/page parent id', 'jet-engine' ),
					),
					array(
						'value' => 'rand',
						'label' => __( 'Random order', 'jet-engine' ),
					),
					array(
						'value' => 'comment_count',
						'label' => __( 'By number of comments', 'jet-engine' ),
					),
					array(
						'value' => 'relevance',
						'label' => __( 'By search terms relevance', 'jet-engine' ),
					),
					array(
						'value' => 'menu_order',
						'label' => __( 'By Page Order', 'jet-engine' ),
					),
					array(
						'value' => 'meta_value',
						'label' => __( 'Order by string meta value', 'jet-engine' ),
					),
					array(
						'value' => 'meta_value_num',
						'label' => __( 'Order by numeric meta value', 'jet-engine' ),
					),
					array(
						'value' => 'meta_clause',
						'label' => __( 'Order by meta clause', 'jet-engine' ),
					),
					array(
						'value' => 'post__in',
						'label' => __( 'Preserve post ID order given in the `Post in` option', 'jet-engine' ),
					),
					array(
						'value' => 'post_name__in',
						'label' => __( 'Preserve post slug order given in the `Post Name in` option', 'jet-engine' ),
					),
					array(
						'value' => 'post_parent__in',
						'label' => __( 'Preserve post parent order given in the `Post Parent in` option', 'jet-engine' ),
					),
					array(
						'value' => 'none',
						'label' => __( 'No order', 'jet-engine' ),
					),
				);
				break;
			
			case 'users':
				$result = array(
					array(
						'value' => 'ID',
						'label' => __( 'By user ID', 'jet-engine' ),
					),
					array(
						'value' => 'display_name',
						'label' => __( 'By user display name', 'jet-engine' ),
					),
					array(
						'value' => 'name',
						'label' => __( 'By user name', 'jet-engine' ),
					),
					array(
						'value' => 'include',
						'label' => __( 'By the included list of user IDs (requires the Include parameter)', 'jet-engine' ),
					),
					array(
						'value' => 'login',
						'label' => __( 'By user login', 'jet-engine' ),
					),
					array(
						'value' => 'nicename',
						'label' => __( 'By user nicename', 'jet-engine' ),
					),
					array(
						'value' => 'email',
						'label' => __( 'By user email', 'jet-engine' ),
					),
					array(
						'value' => 'url',
						'label' => __( 'By user url', 'jet-engine' ),
					),
					array(
						'value' => 'registered',
						'label' => __( 'By user registered date', 'jet-engine' ),
					),
					array(
						'value' => 'post_count',
						'label' => __( 'By user post count', 'jet-engine' ),
					),
					array(
						'value' => 'meta_value',
						'label' => __( 'Meta value', 'jet-engine' ),
					),
					array(
						'value' => 'meta_value_num',
						'label' => __( 'Numeric meta value', 'jet-engine' ),
					),
				);
				break;

			case 'terms':
				$result = array(
					array(
						'value' => 'name',
						'label' => __( 'Name', 'jet-engine' ),
					),
					array(
						'value' => 'slug',
						'label' => __( 'Slug', 'jet-engine' ),
					),
					array(
						'value' => 'term_group',
						'label' => __( 'Term group', 'jet-engine' ),
					),
					array(
						'value' => 'term_id',
						'label' => __( 'Term ID', 'jet-engine' ),
					),
					array(
						'value' => 'description',
						'label' => __( 'Description', 'jet-engine' ),
					),
					array(
						'value' => 'parent',
						'label' => __( 'Parent', 'jet-engine' ),
					),
					array(
						'value' => 'term_order',
						'label' => __( 'Term Order', 'jet-engine' ),
					),
					array(
						'value' => 'count',
						'label' => __( 'By the number of objects associated with the term', 'jet-engine' ),
					),
					array(
						'value' => 'include',
						'label' => __( 'Match the order of the `Include` param', 'jet-engine' ),
					),
					array(
						'value' => 'slug__in',
						'label' => __( 'Match the order of the `Slug` param', 'jet-engine' ),
					),
					array(
						'value' => 'meta_value',
						'label' => __( 'Order by string meta value', 'jet-engine' ),
					),
					array(
						'value' => 'meta_value_num',
						'label' => __( 'Order by numeric meta value', 'jet-engine' ),
					),
					array(
						'value' => 'meta_clause',
						'label' => __( 'Order by meta clause', 'jet-engine' ),
					),
				);
				break;
		}

		return apply_filters( 'jet-engine/query-builder/' . $for . '/orderby-options', $result );
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
			'api_path_get'        => jet_engine()->api->get_route( 'get-query' ),
			'edit_button_label'   => '', // Set individually for apropriate page,
			'item_id'             => false,
			'query_types'         => $this->editor->get_types_for_js(),
			'types_components'    => $this->editor->get_editor_components_map(),
			'post_types'          => \Jet_Engine_Tools::get_post_types_for_js(),
			'taxonomies'          => \Jet_Engine_Tools::get_taxonomies_for_js( false, true ),
			'redirect'            => '', // Set individually for apropriate page,
			'general_settings'    => array( 'query_type' => 'post' ),
			'notices'             => array(
				'name'    => __( 'Please, set query name', 'jet-engine' ),
				'success' => __( 'Query updated', 'jet-engine' ),
			),
		);

		return array_merge( $default, $config );

	}

	public function get_query_count_html( $query_id = false, $count_type = false ) {

		if ( ! $count_type ) {
			$count_type = 'total';
		}

		if ( ! $query_id ) {
			return 0;
		}

		$query = Manager::instance()->get_query_by_id( $query_id );

		if ( ! $query ) {
			return 0;
		}

		switch ( $count_type ) {

			case 'visible':
				$result = $query->get_items_page_count();
				break;

			case 'start-item':
				$result = $query->get_start_item_index_on_page();
				break;

			case 'end-item':
				$result = $query->get_end_item_index_on_page();
				break;

			default:
				$result = $query->get_items_total_count();
		}

		return sprintf( '<span class="jet-engine-query-count query-%2$s count-type-%3$s" data-query="%2$s">%1$s</span>', $result, $query_id, $count_type );
	}

}

Manager::instance();
