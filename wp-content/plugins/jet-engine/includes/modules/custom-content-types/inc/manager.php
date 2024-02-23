<?php
namespace Jet_Engine\Modules\Custom_Content_Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Manager class
 */
class Manager extends \Jet_Engine_Base_WP_Intance {

	/**
	 * Base slug for CPT-related pages
	 * @var string
	 */
	public $page = 'jet-engine-cct';

	/**
	 * Action request key
	 *
	 * @var string
	 */
	public $action_key = 'cct_action';

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
	public $fields = array();

	private $_registered_instances = array();
	private $_post_types_map = array();

	/**
	 * Init data instance
	 *
	 * @return [type] [description]
	 */
	public function init_data() {

		require Module::instance()->module_path( 'data.php' );

		$this->data = new Data( $this );

	}

	/**
	 * Initiizlize post type specific API endpoints
	 *
	 * @param  Jet_Engine_REST_API $api_manager API manager instance.
	 * @return void
	 */
	public function init_rest( $api_manager ) {

		require_once Module::instance()->module_path( 'rest-api/add-content-type.php' );
		require_once Module::instance()->module_path( 'rest-api/edit-content-type.php' );
		require_once Module::instance()->module_path( 'rest-api/get-content-type.php' );
		require_once Module::instance()->module_path( 'rest-api/get-content-types.php' );
		require_once Module::instance()->module_path( 'rest-api/delete-content-type.php' );

		$api_manager->register_endpoint( new Rest\Add_Content_Type() );
		$api_manager->register_endpoint( new Rest\Edit_Content_Type() );
		$api_manager->register_endpoint( new Rest\Get_Content_Type() );
		$api_manager->register_endpoint( new Rest\Get_Content_Types() );
		$api_manager->register_endpoint( new Rest\Delete_Content_Type() );

	}

	public function get_content_type_by_listing( $listing_id ) {

		$data = get_post_meta( $listing_id, '_elementor_page_settings', true );

		if ( empty( $data ) ) {
			return false;
		}

		if ( empty( $data['listing_source'] ) || 'custom_content_type' !== $data['listing_source'] ) {
			return false;
		}

		$type = ! empty( $data['listing_post_type'] ) ? $data['listing_post_type'] : false;

		if ( ! $type || empty( $this->_registered_instances[ $type ] ) ) {
			return false;
		} else {
			return $type;
		}

	}

	/**
	 * Register metaboxes
	 *
	 * @return void
	 */
	public function register_instances() {

		$content_types = $this->data->get_item_for_register();

		do_action( 'jet-engine/custom-content-types/register-instances', $this );

		if ( empty( $content_types ) ) {
			return;
		}

		require Module::instance()->module_path( 'single-item-factory.php' );
		require Module::instance()->module_path( 'factory.php' );
		require Module::instance()->module_path( 'type-pages.php' );

		// initialize relations compatibility
		require Module::instance()->module_path( 'relations/manager.php' );
		Relations\Manager::instance();

		foreach ( $content_types as $type ) {

			$args     = maybe_unserialize( $type['args'] );
			$fields   = maybe_unserialize( $type['meta_fields'] );
			$type_id  = $type['id'];
			$instance = new Factory( $args, $fields, $type_id );

			\Jet_Engine_Meta_Boxes_Option_Sources::instance()->find_meta_fields_with_save_custom( 
				'cct',
				$args['slug'],
				$fields,
				$type_id,
				$this->data
			);

			jet_engine()->add_instance( 'custom-content-type', array(
				'id'   => $type_id,
				'args' => $type['args'],
			) );

			$this->_registered_instances[ $args['slug'] ] = $instance;

			if ( $instance->get_arg( 'has_single' ) && $instance->get_arg( 'related_post_type' ) ) {
				$this->_post_types_map[ $instance->get_arg( 'related_post_type' ) ] = $instance->get_arg( 'slug' );
			}

		}

		do_action( 'jet-engine/custom-content-types/after-register-instances', $this );

		/**
		 * Attach handler to save new custom values added into checkbox and radio feilds
		 * to add custom values to field settings (if allowed)
		 */
		add_action( 
			'jet-engine/meta-boxes/hook-save-custom/cct', 
			array( $this, 'attach_handler_to_save_custom' ), 
			10, 2 
		);

	}

	/**
	 * Handle custom options saving for check and radio fields where it is enabled
	 * @param  [type] $fields_data     [description]
	 * @param  [type] $options_manager [description]
	 * @return [type]                  [description]
	 */
	public function attach_handler_to_save_custom( $fields_data, $options_manager ) {
		foreach ( $fields_data as $cct => $fields ) {
			$hook_name = 'jet-engine/custom-content-types/updated-item/' . $cct;
			add_action( $hook_name, function( $item ) use ( $options_manager, $cct ) {
				$options_manager->save_custom_values( $item, $this->data, 'cct', $cct );
			} );
		}
	}

	/**
	 * Returns all post ypes with related CCTs
	 * @return [type] [description]
	 */
	public function get_post_types_map() {
		return $this->_post_types_map;
	}

	public function register_instance( $slug, $instance ) {
		$this->_registered_instances[ $slug ] = $instance;
	}

	public function get_content_type_for_post_type( $post_type = null ) {

		if ( ! $post_type ) {
			return false;
		}

		$content_type = isset( $this->_post_types_map[ $post_type ] ) ? $this->_post_types_map[ $post_type ] : false;

		if ( ! $content_type ) {
			return false;
		}

		$instance = $this->get_content_types( $content_type );

		if ( ! $instance ) {
			return false;
		} else {
			return $instance;
		}

	}

	public function get_item_for_post( $post_id = null, $content_type = null, $post_type = null ) {

		if ( ! $post_id ) {
			return false;
		}

		if ( ! $content_type && ! $post_type ) {
			$post_type = get_post_type( $post_id );
		}

		if ( ! $content_type ) {
			$content_type = $this->get_content_type_for_post_type( $post_type );
		}

		if ( ! $content_type ) {
			return false;
		}

		if ( ! $content_type->db->has_col( 'cct_single_post_id' ) ) {
			return;
		}

		$item = $content_type->db->get_item( $post_id, 'cct_single_post_id' );

		if ( ! $item ) {
			return false;
		} else {
			return $item;
		}

	}

	/**
	 * Retuns registered content types list
	 *
	 * @return [type] [description]
	 */
	public function get_content_types( $type = null ) {
		if ( ! $type ) {
			return $this->_registered_instances;
		} else {
			return isset( $this->_registered_instances[ $type ] ) ? $this->_registered_instances[ $type ] : false;
		}
	}

	/**
	 * Retuns registered content types list
	 *
	 * @return [type] [description]
	 */
	public function get_content_type_by_id( $type_id = null ) {

		if ( $type_id ) {
			foreach ( $this->_registered_instances as $instance ) {
				if ( absint( $instance->type_id ) === absint( $type_id ) ) {
					return $instance;
				}
			}
		}

		return false;
	}

	/**
	 * Return admin pages for current instance
	 *
	 * @return array
	 */
	public function get_instance_pages() {

		$base_path = Module::instance()->module_path( 'pages/' );

		return array(
			'Jet_Engine\Modules\Custom_Content_Types\Pages\Types_List' => $base_path. 'list.php',
			'Jet_Engine\Modules\Custom_Content_Types\Pages\Edit'       => $base_path . 'edit.php',
		);

	}

	/**
	 * Returns current menu page title (for JetEngine submenu)
	 * @return [type] [description]
	 */
	public function get_page_title() {
		return __( 'Custom Content Types', 'jet-engine' );
	}

	/**
	 * Returns current instance slug
	 *
	 * @return [type] [description]
	 */
	public function instance_slug() {
		return 'cct';
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
			'api_path_get'        => jet_engine()->api->get_route( 'get-content-type' ),
			'edit_button_label'   => '', // Set individually for apropriate page,
			'item_id'             => false,
			'post_types'          => \Jet_Engine_Tools::get_post_types_for_js(),
			'redirect'            => '', // Set individually for apropriate page,
			'general_settings'    => array(),
			'icons'               => jet_engine()->cpt->get_icons_options(),
			'meta_fields'         => array(),
			'notices'             => array(
				'name'    => __( 'Please, set content type title', 'jet-engine' ),
				'slug'    => __( 'Please, set content type slug', 'jet-engine' ),
				'success' => __( 'Content type updated', 'jet-engine' ),
			),
		);

		return array_merge( $default, $config );

	}

	public function get_service_fields( $args = array() ) {

		$fields = array();

		if ( ! empty( $args['add_id_field'] ) ) {
			$fields[] = array(
				'title'       => __( 'Item ID', 'jet-engine' ),
				'name'        => '_ID',
				'object_type' => 'service_field',
				'type'        => 'number',
			);
		}

		if ( ! empty( $args['has_single'] ) ) {
			$fields[] = array(
				'title'       => __( 'Single Post ID', 'jet-engine' ),
				'name'        => 'cct_single_post_id',
				'object_type' => 'service_field',
				'type'        => 'number',
			);
		}

		$fields[] = array(
			'title'       => __( 'Item Author', 'jet-engine' ),
			'name'        => 'cct_author_id',
			'object_type' => 'service_field',
			'type'        => 'number',
		);

		$fields[] = array(
			'title'       => __( 'Created Date', 'jet-engine' ),
			'name'        => 'cct_created',
			'object_type' => 'service_field',
			'type'        => 'sql-date',
		);

		$fields[] = array(
			'title'       => __( 'Modified Date', 'jet-engine' ),
			'name'        => 'cct_modified',
			'object_type' => 'service_field',
			'type'        => 'sql-date',
		);

		$fields[] = array(
			'title'       => __( 'Status', 'jet-engine' ),
			'name'        => 'cct_status',
			'object_type' => 'service_field',
			'type'        => 'text',
		);

		return apply_filters( 'jet-engine/custom-content-types/service-columns', $fields, $args );

	}

	public function get_additional_order_by_options( $for_js = false ) {
		$result  = array();
		$options = array(
			'random_order' => __( 'Random order', 'jet-engine' ),
		);

		if ( $for_js ) {
			foreach ( $options as $option_key => $option_label ) {
				$result[] = array(
					'value' => $option_key,
					'label' => $option_label,
				);
			}
		} else {
			$result = $options;
		}

		return $result;
	}

}
