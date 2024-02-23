<?php
namespace Jet_Engine\Compatibility\Packages\Jet_Form_Builder\Listings;

use Jet_Engine\Compatibility\Packages\Jet_Form_Builder\Package;
use Jet_Engine\Compatibility\Packages\Jet_Form_Builder\Query_Builder\Manager as QueryManager;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @access private
	 * @var    object
	 */
	public static $instance = null;

	public function __construct() {
		
		add_filter( 'jet-engine/listing/data/object-fields-groups', array( $this, 'add_source_fields' ) );

		add_action( 'jet-engine/elementor-views/dynamic-tags/register', array( $this, 'register_dynamic_tags' ), 10, 2 );
		add_action( 'jet-engine/register-macros', array( $this, 'register_macros' ) );

		add_filter( 'jet-engine/listing/custom-post-id', array( $this, 'set_form_record_item_id' ), 10, 2 );

		add_filter(
			'jet-engine/compatibility/popup-package/query/' . QueryManager::instance()->slug . '/post-object',
			array( $this, 'set_form_record_popup_object'),
			10, 3
		);

	}

	public function register_dynamic_tags( $dynamic_tags, $tags_module ) {
		require_once Package::instance()->package_path( 'listings/dynamic-tag-form-field.php' );
		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tag_Form_Field() );
	}

	/**
	 * Register relations related macros
	 *
	 * @return [type] [description]
	 */
	public function register_macros() {

		require_once Package::instance()->package_path( 'listings/macros-form-field.php' );

		new Macros_Form_Field();

	}

	/**
	 * Add source fields into the dynamic field widget
	 *
	 * @param $groups
	 *
	 * @return mixed
	 */
	public function add_source_fields( $groups ) {

		$groups[] = [
			'label'   => __( 'JetFormBuilder', 'jet-engine' ),
			'options' => $this->get_fields_list(),
		];

		return $groups;

	}

	public function get_fields_list() {
		return apply_filters( 'jet-engine/listing/data/form-records-query/object-fields-groups', [
			'jfb_ID'                => __( 'Record ID', 'jet-engine' ),
			'jfb_form_id'           => __( 'Form ID', 'jet-engine' ),
			'jfb_from_content_id'   => __( 'Submited From (page/post ID)', 'jet-engine' ),
			'jfb_from_content_type' => __( 'Submited From (post type)', 'jet-engine' ),
			'jfb_status'            => __( 'Status', 'jet-engine' ),
			'jfb_referrer'          => __( 'Referrer', 'jet-engine' ),
			'jfb_submit_type'       => __( 'Submit Type', 'jet-engine' ),
			'jfb_submit_date'       => __( 'Submit Date', 'jet-engine' ),
			'jfb_fields'            => __( 'Form Fields', 'jet-engine' ),
		] );
	}

	public function get_form_record_field( $field_name = '', $object = false ) {

		if ( ! $object ) {
			$object = jet_engine()->listings->data->get_current_object();
		}

		if ( ! isset( $object->jfb_fields ) ) {
			return __( '<b>Please note:</b>Dynamic tag <b>JetFormBuilder Record Field</b> works only with JetFromBuilder records query', 'jet-engine' );
		}

		$field_data = explode( '/', $field_name );
		$value      = isset( $object->jfb_fields[ $field_data[0] ] ) ? $object->jfb_fields[ $field_data[0] ] : false;

		if ( ! $value ) {
			return;
		}

		if ( is_object( $value ) ) {
			$value = (array) $value;
		}

		if ( ! is_array( $value ) ) {
			return $this->get_santized_value( $value, $field_name );
		} else {
			
			for ( $i = 1; $i < count( $field_data ); $i++ ) {
				
				$key = $field_data[ $i ];
			 	
			 	if ( ! isset( $value[ $key ] ) ) {
			 		return __( 'Can`t find nested items. Please check the path.', 'jet-engine' );
			 	}

			 	$value = $value[ $key ];

			}

			if ( ! is_array( $value ) ) {
				return $this->get_santized_value( $value, $field_name ); 
			} else {
				return __( 'We can`t print field value because it`s array. Please set path to required array item', 'jet-engine' );
			}
			
		}
	}

	public function get_santized_value( $value, $field_name ) {
		return apply_filters( 
			'jet-engine/listing/jet-form-builder-record-field/print-value', 
			wp_kses_post( $value ), 
			$value, 
			$field_name 
		);
	}

	public function set_form_record_item_id( $id, $object ) {

		if ( isset( $object->jfb_ID ) ) {
			$id = $object->jfb_ID;
		}

		return $id;
	}

	public function set_form_record_popup_object( $post_obj, $popup_data, $query ) {

		$record_id = ! empty( $popup_data['postId'] ) ? absint( $popup_data['postId'] ) : false;

		if ( ! $record_id ) {
			return $post_obj;
		}

		$query->setup_query();

		$query->final_query['record__in'] = array( $record_id );
		$query->final_query['limit_per_page'] = 1;
		$query->final_query['offset'] = 0;

		$items = $query->get_items();

		return isset( $items[0] ) ? $items[0] : $post_obj;
	}

	/**
	 * Returns the instance.
	 *
	 * @access public
	 * @return object
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

}
