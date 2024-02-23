<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Meta_Box_Package' ) ) {

	/**
	 * Define Jet_Engine_Meta_Box_Package class
	 */
	class Jet_Engine_Meta_Box_Package {

		private $meta_boxes = array();

		public function __construct() {

			add_filter( 'rwmb_meta_boxes', array( $this, 'get_meta_boxes' ), 99999 );

			add_filter( 'jet-engine/listings/data/sources', array( $this, 'add_field_source' ) );
			add_filter( 'jet-engine/listings/dynamic-image/fields', array( $this, 'add_field_source' ) );
			add_filter( 'jet-engine/listings/dynamic-link/fields', array( $this, 'add_field_source' ) );


			add_action( 'jet-engine/listings/dynamic-field/source-controls', array( $this, 'field_controls' ) );
			add_action( 'jet-engine/listings/dynamic-link/source-controls', array( $this, 'link_controls' ) );
			add_action( 'jet-engine/listings/dynamic-image/source-controls', array( $this, 'image_controls' ) );
			add_action( 'jet-engine/listings/dynamic-image/link-source-controls', array( $this, 'linked_image_controls' ) );

			add_filter( 'jet-engine/listings/dynamic-image/custom-image', array( $this, 'image_render' ), 10, 4 );
			add_filter( 'jet-engine/listings/dynamic-image/custom-url', array( $this, 'image_url_render' ), 10, 2 );
			add_filter( 'jet-engine/listings/dynamic-link/custom-url', array( $this, 'link_render' ), 10, 2 );
			add_filter( 'jet-engine/listings/dynamic-field/field-value', array( $this, 'field_render' ), 10, 2 );

			if ( class_exists( 'RWMB_Group' ) ) {

				add_filter(
					'jet-engine/listing/repeater-sources',
					array( $this, 'add_repeater_source' )
				);

				add_filter(
					'jet-engine/listings/data/repeater-value/metabox_io',
					array( $this, 'repeater_val' ), 10, 5
				);
			}

		}

		/**
		 * Returns nested repeater value
		 *
		 * @param  [type] $value        [description]
		 * @param  [type] $object       [description]
		 * @param  [type] $source_field [description]
		 * @param  [type] $field        [description]
		 * @param  [type] $index        [description]
		 * @return [type]               [description]
		 */
		public function repeater_val( $value = null, $object = null, $source_field = null, $field = null, $index = 0 ) {

			if ( ! $object->ID || ! $source_field ) {
				return $value;
			}

			$value = get_post_meta( $object->ID, $source_field, true );

			if ( empty( $value ) || empty( $value[ $index ] ) ) {
				return false;
			}

			$value = isset( $value[ $index ][ $field ] ) ? $value[ $index ][ $field ] : false;

			return $value;

		}

		/**
		 * Metabox repeater source
		 */
		public function add_repeater_source( $sources ) {
			$sources['metabox_io'] = __( 'MetaBox.io', 'jet-engine' );
			return $sources;
		}

		/**
		 * Store metbaxes list to use it in controls
		 *
		 * @param  array  $meta_boxes [description]
		 * @return [type]             [description]
		 */
		public function get_meta_boxes( $meta_boxes = array() ) {

			$raw = $meta_boxes;

			foreach ( $raw as $meta_box ) {

				$fields = array();

				if ( ! empty( $meta_box['fields'] ) ) {
					foreach ( $meta_box['fields'] as $field ) {

						if ( empty( $field['id'] ) ) {
							continue;
						}

						$fields[ $field['id'] ] = $field;
					}
				}

				$meta_box['fields'] = $fields;
				$this->meta_boxes[] = $meta_box;

			}

			return $meta_boxes;

		}

		/**
		 * Add field source
		 */
		public function add_field_source( $sources ) {
			$sources['mb_field_groups'] = __( 'Meta Box', 'jet-engine' );
			return $sources;
		}

		/**
		 * Render field
		 *
		 * @param  [type] $result   [description]
		 * @param  array  $settings [description]
		 * @return [type]           [description]
		 */
		public function field_render( $result = null, $settings = array() ) {

			$key = isset( $settings['mb_field_key'] ) ? $settings['mb_field_key'] : false;

			if ( ! $key ) {
				return $result;
			}

			return jet_engine()->listings->data->get_meta( $key );

		}

		/**
		 * Return custom image for Meta Box
		 *
		 * @param  [type] $result   [description]
		 * @param  [type] $settings [description]
		 * @param  [type] $size     [description]
		 * @return [type]           [description]
		 */
		public function image_render( $result, $settings, $size, $render ) {

			if ( 'mb_field_groups' !== $settings['dynamic_image_source'] ) {
				return $result;
			}

			$key = isset( $settings['mb_field_key'] ) ? $settings['mb_field_key'] : false;

			if ( ! $key ) {
				return $result;
			}

			$image = jet_engine()->listings->data->get_meta( $key );

			if ( ! $image ) {
				return $result;
			}

			if ( filter_var( $image, FILTER_VALIDATE_URL ) ) {
				ob_start();
				$render->print_image_html_by_src( $image );
				return ob_get_clean();
			} else {
				return wp_get_attachment_image( $image, $size, false, array( 'alt' => $render->get_image_alt( $image, $settings ) ) );
			}

		}

		/**
		 * Return custom image URL for Meta Box
		 *
		 * @param  [type] $url      [description]
		 * @param  array  $settings [description]
		 * @return [type]           [description]
		 */
		public function image_url_render( $url = false, $settings = array() ) {

			$custom = ! empty( $settings['image_link_source_custom'] ) ? $settings['image_link_source_custom'] : false;

			if ( $custom ) {
				return $url;
			}

			$key = isset( $settings['mb_link_field_key'] ) ? $settings['mb_link_field_key'] : false;

			if ( ! $key ) {
				return $url;
			}

			$val = jet_engine()->listings->data->get_meta( $key );

			if ( 0 < absint( $val ) ) {
				return get_permalink( $val );
			} else {
				return $val;
			}

		}

		/**
		 * Return custom image URL for Meta Box
		 *
		 * @param  [type] $url      [description]
		 * @param  array  $settings [description]
		 * @return [type]           [description]
		 */
		public function link_render( $url = false, $settings = array() ) {

			$custom = $settings['dynamic_link_source_custom'];

			if ( $custom ) {
				return $url;
			}

			$key = isset( $settings['mb_field_key'] ) ? $settings['mb_field_key'] : false;

			if ( ! $key ) {
				return $url;
			}

			$val = jet_engine()->listings->data->get_meta( $key );

			if ( 0 < absint( $val ) ) {
				return get_permalink( $val );
			} else {
				return $val;
			}

		}

		/**
		 * Image controls
		 *
		 * @return [type] [description]
		 */
		public function image_controls( $widget ) {

			$this->add_control( $widget, array(
				'group'     => 'images',
				'condition' => array(
					'dynamic_image_source' => 'mb_field_groups',
				),
			) );

		}

		public function linked_image_controls( $widget ) {

			$this->add_control( $widget, array(
				'id'        => 'mb_link_field_key',
				'group'     => 'links',
				'condition' => array(
					'linked_image'      => 'yes',
					'image_link_source' => 'mb_field_groups',
				),
			) );

		}

		public function link_controls( $widget ) {

			$this->add_control( $widget, array(
				'group'     => 'links',
				'condition' => array(
					'dynamic_link_source' => 'mb_field_groups',
				),
			) );

		}

		/**
		 * Field controls
		 *
		 * @return [type] [description]
		 */
		public function field_controls( $widget ) {

			$this->add_control( $widget, array(
				'group'     => 'fields',
				'condition' => array(
					'dynamic_field_source' => 'mb_field_groups',
				),
			) );

		}

		public function add_control( $widget = null, $args = array() ) {

			$group     = isset( $args['group'] ) ? $args['group'] : 'fields';
			$condition = isset( $args['condition'] ) ? $args['condition'] : array();
			$id        = isset( $args['id'] ) ? $args['id'] : 'mb_field_key';

			$widget->add_control(
				$id,
				array(
					'label'     => __( 'Meta Box Field', 'jet-engine' ),
					'type'      => 'select',
					'default'   => '',
					'groups'    => $this->get_fields_goups( $group ),
					'condition' => $condition,
				)
			);

		}

		public function get_fields_goups( $group = 'fields' ) {

			$cb = array(
				'fields'   => 'map_fields',
				'images'   => 'map_images',
				'links'    => 'map_links',
			);

			$groups = $this->meta_boxes;
			$result = array();

			if ( empty( $groups ) ) {
				return $result;
			}

			foreach ( $groups as $data ) {

				$fields = array_filter( array_map( array( $this, $cb[ $group ] ), $data['fields'] ) );

				if ( ! empty( $fields ) ) {
					$result[] = array(
						'label'   => $data['title'],
						'options' => $fields,
					);
				}

			}

			return $result;

		}

		/**
		 * Map images callback
		 *
		 * @param  [type] $field [description]
		 * @return [type]        [description]
		 */
		public function map_images( $field ) {

			$whitelisted = $this->whitelisted_fields();
			$type        = $field['type'];

			if ( ! isset( $whitelisted[ $type ] ) ) {
				return false;
			}

			if ( ! in_array( 'image', $whitelisted[ $type ] ) ) {
				return false;
			} else {
				return $this->get_field_name( $field );
			}

		}

		/**
		 * Map links callback
		 *
		 * @param  [type] $field [description]
		 * @return [type]        [description]
		 */
		public function map_links( $field ) {

			$whitelisted = $this->whitelisted_fields();
			$type        = $field['type'];

			if ( ! isset( $whitelisted[ $type ] ) ) {
				return false;
			}

			if ( ! in_array( 'link', $whitelisted[ $type ] ) ) {
				return false;
			} else {
				return $this->get_field_name( $field );
			}

		}

		/**
		 * Map fields callback
		 *
		 * @param  [type] $field [description]
		 * @return [type]        [description]
		 */
		public function map_fields( $field ) {

			$whitelisted = $this->whitelisted_fields();
			$type        = $field['type'];

			if ( ! isset( $whitelisted[ $type ] ) ) {
				return false;
			}

			if ( ! in_array( 'field', $whitelisted[ $type ] ) ) {
				return false;
			} else {
				return $this->get_field_name( $field );
			}

		}

		/**
		 * Returns the field name.
		 *
		 * @param array $field Field arguments
		 *
		 * @return false|string
		 */
		public function get_field_name( $field = array() ) {
			$name = ! empty( $field['name'] ) ? $field['name'] : false;

			if ( ! $name ) {
				$name = ! empty( $field['id'] ) ? $field['id'] : false;
			}

			return $name;
		}

		/**
		 * Returns whitelisted fields
		 *
		 * @return [type] [description]
		 */
		public function whitelisted_fields() {

			return array(
				'text'           => array( 'field' ),
				'textarea'       => array( 'field' ),
				'url'            => array( 'field', 'link' ),
				'number'         => array( 'field' ),
				'range'          => array( 'field' ),
				'email'          => array( 'field', 'link' ),
				'wysiwyg'        => array( 'field' ),
				'image_advanced' => array( 'link', 'image' ),
				'image_select'   => array( 'link', 'image' ),
				'map'            => array( 'field' ),
				'select'         => array( 'field' ),
				'radio'          => array( 'field' ),
				'post'           => array( 'field', 'link' ),
				'taxonomy'       => array( 'field', 'link' ),
				'datetime'       => array( 'field' ),
				'date'           => array( 'field' ),
				'time'           => array( 'field' ),
			);

		}

	}

}

new Jet_Engine_Meta_Box_Package();
