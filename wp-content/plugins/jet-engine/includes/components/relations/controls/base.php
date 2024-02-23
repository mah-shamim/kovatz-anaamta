<?php
namespace Jet_Engine\Relations\Controls;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Base {

	public $args         = array();
	public $relation     = null;
	public $control_type = null;

	public static $common_printed = false;

	public function __construct( $args ) {

		$this->relation = $args['relation'];

		unset( $args['relation'] );

		if ( ! empty( $args['context'] ) ) {
			$this->control_type = ( 'parent_control' === $args['context'] ) ? 'child_object' : 'parent_object';
		}

		$this->args = $args;

		add_action( 'admin_enqueue_scripts', array( $this, 'common_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'localize_data' ) );

		$this->init();

	}

	/**
	 * Returns relation arguments
	 *
	 * @return [type] [description]
	 */
	public function get_args() {
		return $this->args;
	}

	/**
	 * Perform an control element wrapper initializtion
	 * @return [type] [description]
	 */
	public function init() {
	}

	/**
	 * Setup type of current control
	 * what we'll manage with it - parent related item or children
	 *
	 * @return [type] [description]
	 */
	public function setup_type() {

		if ( null !== $this->control_type ) {
			return $this->control_type;
		}

		$args           = $this->get_args();
		$current_object = jet_engine()->relations->types_helper->type_name_by_parts( $args['object_type'], $args['object_name'] );

		if ( $current_object === $this->relation->get_args( 'parent_object' ) ) {
			$this->control_type = 'child_object';
		} else {
			$this->control_type = 'parent_object';
		}

	}

	/**
	 * Returns type of current control, not the page where it located,
	 * bu tyoe of items which will be controlled by it
	 *
	 * @return [type] [description]
	 */
	public function get_control_type() {
		return $this->control_type;
	}

	/**
	 * Check if cotnrol type is equal to given type
	 *
	 * @param  [type]  $type [description]
	 * @return boolean       [description]
	 */
	public function _is( $type ) {
		return $this->get_control_type() === $type;
	}

	/**
	 * Register common assets required for all control types
	 *
	 * @return [type] [description]
	 */
	public function common_assets() {

		if ( ! $this->is_control_page() ) {
			return;
		}

		$this->setup_type();

		wp_enqueue_style( 'wp-components' );

		if ( $this->has_media_fields() ) {
			wp_enqueue_media();
		}

		$deps = array(
			'wp-polyfill',
			'wp-util',
			'wp-core-data',
			'wp-block-library',
			'wp-media-utils',
			'wp-components',
			'wp-element',
			'wp-blocks',
			'wp-block-editor',
			'wp-data',
			'wp-i18n',
			'lodash',
			'wp-api-fetch',
		);

		wp_enqueue_script(
			'jet-engine-relations-admin-controls',
			jet_engine()->plugin_url( 'includes/components/relations/assets/js/admin-controls.js' ),
			$deps,
			jet_engine()->get_version(),
			true
		);

		wp_enqueue_style(
			'jet-engine-relations',
			jet_engine()->plugin_url( 'includes/components/relations/assets/css/relations.css' ),
			array(),
			jet_engine()->get_version()
		);

	}

	/**
	 * Check if relation has media fields
	 *
	 * @return boolean [description]
	 */
	public function has_media_fields() {

		$fields = $this->relation->get_meta_fields();

		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				if ( ! empty( $field['type'] ) && 'media' === $field['type'] ) {
					return true;
				}
			}
		}

		$create_fields = $this->relation->get_create_control_fields( $this->get_control_type() );

		if ( ! empty( $create_fields ) ) {
			foreach ( $create_fields as $field ) {
				if ( ! empty( $field['type'] ) && 'media' === $field['type'] ) {
					return true;
				}
			}
		}

		return false;

	}

	/**
	 * Get object data current control is responsible for
	 *
	 * @return [type] [description]
	 */
	public function get_object_for_control() {

		$this->setup_type();

		$object_name = $this->relation->get_args( $this->get_control_type() );
		$object_data = jet_engine()->relations->types_helper->type_parts_by_name( $object_name );

		return array(
			'raw'         => $object_name,
			'object_type' => $object_data[0],
			'object'      => $object_data[1],
		);

	}

	/**
	 * Returns labels list for give control
	 *
	 * @return [type] [description]
	 */
	public function get_control_labels() {

		$from_object  = $this->get_object_for_control();
		$single_label = jet_engine()->relations->types_helper->get_type_label( 'single', $from_object['object_type'], $from_object['object'] );

		if ( $this->_is( 'parent_object' ) ) {
			$key = 'child_page_control_';
		} else {
			$key = 'parent_page_control_';
		}

		return array(
			'select'        => $this->get_label( $key . 'select', sprintf( __( 'Select %s', 'jet-engine' ), $single_label ) ),
			'createButton'  => $this->get_label( $key . 'create', sprintf( __( 'Add New %s', 'jet-engine' ), $single_label ) ),
			'connectButton' => $this->get_label( $key . 'connect', sprintf( __( 'Connect %s', 'jet-engine' ), $single_label ) ),
		);
	}

	/**
	 * Returns given label from the relations labels list
	 *
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function get_label( $key, $default = false ) {
		$labels = $this->relation->get_args( 'labels', array() );
		return ! empty( $labels[ $key ] ) ? $labels[ $key ] :  $default;
	}

	/**
	 * Returns general title of the current control
	 *
	 * @return [type] [description]
	 */
	public function get_control_title() {

		$this->setup_type();

		if ( $this->_is( 'parent_object' ) ) {
			$key = 'child_page_control_title';
		} else {
			$key = 'parent_page_control_title';
		}

		$label = $this->get_label( $key );

		if ( $label ) {
			return $label;
		} else {
			$args = $this->get_args();
			return jet_engine()->relations->types_helper->get_relation_label( 
				$this->relation, 
				$args['object_type'], 
				$args['object_name'],
				'',
				$this->_is( 'parent_object' )
			);
		}

	}

	/**
	 * Add required JS data
	 *
	 * @return [type] [description]
	 */
	public function localize_data() {

		if ( ! $this->is_control_page() ) {
			return;
		}

		$args        = $this->get_args();
		$from_object = $this->get_object_for_control();
		$columns     = array();
		$raw_cols    = $this->relation->get_table_columns_for_object( $from_object['raw'] );

		foreach ( $raw_cols as $key => $label ) {
			$columns[] = array(
				'key'   => $key,
				'label' => $label,
			);
		}

		$js_data = array(
			'relEl'             => $this->get_el_id(),
			'relID'             => $this->relation->get_id(),
			'metaFields'        => $this->relation->get_meta_fields( true ),
			'tableColumns'      => $columns,
			'isParentProcessed' => $this->_is( 'parent_object' ),
			'labels'            => $this->get_control_labels(),
			'objectType'        => $from_object['object_type'],
			'object'            => $from_object['object'],
		);

		if ( 'parent_object' === $this->get_control_type() ) {
			$allowed = $this->relation->get_args( 'child_manager' );
		} else {
			$allowed = $this->relation->get_args( 'parent_manager' );
		}

		if ( $allowed ) {
			$js_data['createFields'] = $this->relation->get_create_control_fields( $this->get_control_type() );
		}

		printf(
			'<script>window.JetEngineRelationsControls = window.JetEngineRelationsControls || []; window.JetEngineRelationsControls.push( %s )</script>',
			json_encode( $js_data )
		);

		$js_common = array(
			'_nonce' => wp_create_nonce( 'jet-engine-relations-control' ),
			'help'   => array(
				'emptyObject' => $this->get_empty_object_help(),
			),
			'i18n' => array(
				'yes'           => esc_html__( 'Yes', 'jet-engine' ),
				'no'            => esc_html__( 'No', 'jet-engine' ),
				'edit'          => esc_html__( 'Edit', 'jet-engine' ),
				'view'          => esc_html__( 'View', 'jet-engine' ),
				'disconnect'    => esc_html__( 'Disconnect', 'jet-engine' ),
				'deleteItem'    => esc_html__( 'Delete Item', 'jet-engine' ),
				'confirmText'   => esc_html__( 'Are you sure?', 'jet-engine' ),
				'confirmDelete' => esc_html__( 'Are you sure? This item will be removed from your website.', 'jet-engine' ),
			),
		);

		if ( ! self::$common_printed ) {

			printf(
				'<script>window.JetEngineRelationsCommon = %s</script>',
				json_encode( $js_common )
			);

			self::$common_printed = true;
		}

	}

	/**
	 * Returns text of error message when your object ID is empty
	 *
	 * @return [type] [description]
	 */
	public function get_empty_object_help() {
		return __( 'Relations management are not allowed at this moment. You need to update post to edit relations', 'jet-engine' );
	}

	/**
	 * Print JS variable containinig current JS object
	 *
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function print_current_object_id_for_js( $id ) {
		printf(
			'<script>window.JetEngineCurrentObjectID = window.JetEngineCurrentObjectID || %d;</script>',
			$id
		);
	}

	/**
	 * Returns an ID of HTML wrapper for control app
	 *
	 * @return [type] [description]
	 */
	public function get_el_id() {
		$args = $this->get_args();
		return 'jet_engine_rel_' . $this->relation->get_id() . '_' . $this->get_control_type();
	}

	/**
	 * Check if current control page is currently displayed
	 *
	 * @return boolean [description]
	 */
	public function is_control_page() {
		return false;
	}

}
