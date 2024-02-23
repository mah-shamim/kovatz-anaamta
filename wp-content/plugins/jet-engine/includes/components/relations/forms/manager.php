<?php
namespace Jet_Engine\Relations\Forms;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	/**
	 * relation slug
	 *
	 * @var string
	 */
	public $slug = 'connect_relation_items';

	public function __construct() {
		add_action( 'init', array( $this, 'init_jet_engine_forms' ), 99 );
	}

	/**
	 * Initialize JetEngine forms compatibility
	 *
	 * @return [type] [description]
	 */
	public function init_jet_engine_forms() {

		// Load JetEngine compatibility class only if module loaded
		if ( jet_engine()->modules->is_module_active( 'booking-forms' ) ) {
			require jet_engine()->relations->component_path( 'forms/jet-engine/manager.php' );
			new Jet_Engine_Forms\Manager();
		}

		require_once jet_engine()->relations->component_path( 'forms/jet-form-builder/manager.php' );
		new Jet_Form_Builder_Forms\Manager();
	}

	/**
	 * Returns notification slug
	 *
	 * @return [type] [description]
	 */
	public function slug() {
		return $this->slug;
	}

	public function action_title() {
		return __( 'Connect Relation Items', 'jet-engine' );
	}

	/**
	 * Update related item from form action/notification
	 *
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function update_related_items( $args = array() ) {

		$relation         = ! empty( $args['relation'] ) ? $args['relation'] : false;
		$parent_id        = ! empty( $args['parent_id'] ) ? $args['parent_id'] : false;
		$child_id         = ! empty( $args['child_id'] ) ? $args['child_id'] : false;
		$context          = ! empty( $args['context'] ) ? $args['context'] : 'child';
		$store_items_type = ! empty( $args['store_items_type'] ) ? $args['store_items_type'] : 'replace';

		if ( ! $relation ) {
			return new \WP_Error( 'rel_empty', __( 'Relation ID is not set. Please check your form settings', 'jet-engine' ) );
		}

		$relation_instance = jet_engine()->relations->get_active_relations( $relation );

		if ( ! $relation_instance ) {
			return new \WP_Error( 'rel_not_found', __( 'Relation instance not found by ID. Please check your form settings', 'jet-engine' ) );
		}

		if ( empty( $parent_id ) ) {
			$parent_id = array();
		}

		if ( empty( $child_id ) ) {
			$child_id = array();
		}

		if ( ! is_array( $parent_id ) ) {
			$parent_id = array( $parent_id );
		}

		if ( ! is_array( $child_id ) ) {
			$child_id = array( $child_id );
		}

		// If we disconnect given items - just do it and return
		if ( 'disconnect' === $store_items_type ) {
			
			if ( empty( $parent_id ) ) {
				return new \WP_Error( 'disconnect_error', __( 'Parent is empty. To disconnect items you need both parent and child items provided.', 'jet-engine' ) );
			}

			if ( empty( $child_id ) ) {
				return new \WP_Error( 'disconnect_error', __( 'Child is empty. To disconnect items you need both parent and child items provided.', 'jet-engine' ) );
			}

			foreach ( $parent_id as $par_id ) {
				foreach ( $child_id as $c_id ) {
					$relation_instance->delete_rows( $par_id, $c_id );
				}
			}

			return true;

		}

		$relation_instance->set_update_context( $context );

		if ( 'child' === $context ) {
			
			/**
			 * We updating children items from the parent object,
			 * this mean we need to delete all existing children for the parent and set up new
			 */
			foreach ( $parent_id as $par_id ) {

				if ( 'replace' === $store_items_type ) {
					// If we replacing data - first of all completely delete all existing rows for the current parent
					$relation_instance->delete_rows( $par_id );
				}

				foreach ( $child_id as $c_id ) {
					$relation_instance->update( $par_id, $c_id );
				}

			}
		} else {
			/**
			 * We updating parent items from the child object,
			 * this mean we need to delete all existing parents for the processed child and set up new
			 */
			foreach ( $child_id as $c_id ) {

				if ( 'replace' === $store_items_type ) {
					// If we replacing data - first of all completely delete all existing rows for the current child
					$relation_instance->delete_rows( false, $c_id );
				}

				foreach ( $parent_id as $par_id ) {
					$relation_instance->update( $par_id, $c_id );
				}
			}
		}

	}

	/**
	 * Returns allowed sources for object IDs in preset
	 *
	 * @return [type] [description]
	 */
	public function get_preset_id_sources() {
		return jet_engine()->relations->sources->get_sources();
	}

	/**
	 * Update related item from form action/notification
	 *
	 * @param  array  $args [description]
	 * @return [type]       [description]
	 */
	public function get_preset_items( $args = array() ) {

		$rel_id          = isset( $args['rel_id'] ) ? $args['rel_id'] : false;
		$rel_object      = isset( $args['rel_object'] ) ? $args['rel_object'] : 'child_object';
		$rel_object_from = isset( $args['rel_object_from'] ) ? $args['rel_object_from'] : 'current_object';
		$rel_object_var  = isset( $args['rel_object_var'] ) ? $args['rel_object_var'] : '';

		if ( ! $rel_id ) {
			return false;
		}

		$relation = jet_engine()->relations->get_active_relations( $rel_id );

		if ( ! $relation ) {
			return false;
		}

		$object_id = jet_engine()->relations->sources->get_id_by_source( $rel_object_from, $rel_object_var );

		if ( ! $object_id ) {
			return false;
		}

		switch ( $rel_object ) {
			case 'parent_object':
				$is_single = $relation->is_single_parent();
				$related   = $relation->get_parents( $object_id, 'ids' );
				break;

			default:
				$is_single = $relation->is_single_child();
				$related   = $relation->get_children( $object_id, 'ids' );
				break;
		}

		if ( ! $related || empty( $related ) ) {
			return false;
		}

		return $is_single ? $related[0] : $related;

	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return Jet_Engine
	 */
	public static function instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

}
