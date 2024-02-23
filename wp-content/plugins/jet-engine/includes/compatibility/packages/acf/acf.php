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

if ( ! class_exists( 'Jet_Engine_ACF_Package' ) ) {

	/**
	 * Define Jet_Engine_ACF_Package class
	 */
	class Jet_Engine_ACF_Package {

		private $fields_groups = null;
		private $found_fields  = array();

		/**
		 * Constructor for the class
		 */
		function __construct() {

			add_filter( 'jet-engine/listings/data/sources', array( $this, 'add_field_source' ) );
			add_filter( 'jet-engine/listings/dynamic-image/fields', array( $this, 'add_field_source' ) );
			add_filter( 'jet-engine/listings/dynamic-link/fields', array( $this, 'add_field_source' ) );
			add_filter( 'jet-engine/listings/dynamic-repeater/fields', array( $this, 'add_field_source' ) );


			add_action( 'jet-engine/listings/dynamic-field/source-controls', array( $this, 'field_controls' ) );
			add_action( 'jet-engine/listings/dynamic-link/source-controls', array( $this, 'link_controls' ) );
			add_action( 'jet-engine/listings/dynamic-repeater/source-controls', array( $this, 'repeater_controls' ) );
			add_action( 'jet-engine/listings/dynamic-image/source-controls', array( $this, 'image_controls' ) );
			add_action( 'jet-engine/listings/dynamic-image/link-source-controls', array( $this, 'linked_image_controls' ) );

			add_filter( 'jet-engine/listings/dynamic-image/custom-image', array( $this, 'image_render' ), 10, 4 );
			add_filter( 'jet-engine/listings/dynamic-image/custom-url', array( $this, 'image_url_render' ), 10, 2 );
			add_filter( 'jet-engine/listings/dynamic-link/custom-url', array( $this, 'link_render' ), 10, 2 );
			add_filter( 'jet-engine/listings/dynamic-field/field-value', array( $this, 'field_render' ), 10, 2 );
			add_filter( 'jet-engine/listings/dynamic-repeater/pre-get-saved', array( $this, 'repeater_val' ), 10, 2 );

			add_filter( 'jet-engine/listings/allowed-callbacks', array( $this, 'gallery_field_callbacks' ) );

			// Listing item link.
			add_action( 'jet-engine/listings/document/custom-link-source-controls', array( $this, 'listing_link_controls' ) );
			add_filter( 'jet-engine/listings/frontend/custom-listing-url',          array( $this, 'listing_link_render' ), 10, 2 );
			add_filter( 'jet-engine/blocks/editor/controls/link-settings',          array( $this, 'blocks_listing_link_controls' ), 10, 2 );
			add_action( 'jet-engine/blocks/editor/save-settings',                   array( $this, 'save_blocks_editor_settings' ) );

			// Blocks compatibility
			add_filter( 'jet-engine/blocks-views/editor-data', array( $this, 'localize_fields' ) );

			add_filter( 'jet-engine/blocks-views/block-types/attributes/dynamic-field',    array( $this, 'add_block_attr' ) );
			add_filter( 'jet-engine/blocks-views/block-types/attributes/dynamic-image',    array( $this, 'add_block_attr' ) );
			add_filter( 'jet-engine/blocks-views/block-types/attributes/dynamic-link',     array( $this, 'add_block_attr' ) );
			add_filter( 'jet-engine/blocks-views/block-types/attributes/dynamic-repeater', array( $this, 'add_block_attr' ) );

			require_once jet_engine()->plugin_path( 'includes/compatibility/packages/acf/repeater-query.php' );
			new Jet_Engine_ACF_Repeater_Query( $this );

			// For compatibility with ACF Dynamic Tags (Elementor Pro v3.8)
			if ( defined( 'ELEMENTOR_PRO_VERSION' )
				 && version_compare( ELEMENTOR_PRO_VERSION, '3.8.0', '>=' )
				 && version_compare( ELEMENTOR_PRO_VERSION, '3.9.0', '<' )
			) {
				add_filter( 'acf/pre_load_post_id', array( $this, 'set_post_id_in_listing' ), 10, 2 );
			}

		}

		public function set_post_id_in_listing( $result, $post_id ) {

			if ( ! $post_id ) {
				return $result;
			}

			if ( in_array( $post_id, array( 'option', 'options' ) ) ) {
				return $result;
			}

			$is_in_stack = jet_engine()->listings->objects_stack->is_in_stack();

			if ( ! $is_in_stack ) {
				return $result;
			}

			$listing      = jet_engine()->listings->data->get_listing();
			$listing_type = jet_engine()->listings->data->get_listing_type( $listing->get_main_id() );

			if ( 'elementor' !== $listing_type ) {
				return $result;
			}

			$current_obj = jet_engine()->listings->data->get_current_object();
			$class       = get_class( $current_obj );

			switch ( $class ) {
				case 'WP_Post':
					$post_id = $current_obj->ID;
					break;

				case 'WP_User':
					$post_id = 'user_' . $current_obj->ID;
					break;

				case 'WP_Term':
					$post_id = 'term_' . $current_obj->term_id;
					break;

				case 'WP_Comment':
					$post_id = 'comment_' . $current_obj->comment_ID;
					break;

				default:
					$post_id = null;
			}

			return $post_id;

		}

		/**
		 * Add ACF gallery filter callback to allowed callbacks list
		 *
		 * @param  array  $callbacks [description]
		 * @return [type]            [description]
		 */
		public function gallery_field_callbacks( $callbacks = array() ) {
			$callbacks['jet_engine_acf_gallery_wp'] = __( 'ACF Gallery as WP Gallery', 'jet-engine' );
			return $callbacks;
		}

		/**
		 * Returns repeater field value
		 *
		 * @param  [type] $val      [description]
		 * @param  [type] $settings [description]
		 * @return [type]           [description]
		 */
		public function repeater_val( $result, $settings ) {

			if ( 'acf_field_groups' !== $settings['dynamic_field_source'] ) {
				return $result;
			}

			$key     = isset( $settings['acf_field_key'] ) ? $settings['acf_field_key'] : false;
			$keyinfo = $this->parse_key( $key, true );

			if ( empty( $key ) ) {
				return $result;
			}

			$key = $keyinfo['key'];
			$id  = $keyinfo['id'];

			if ( 'options' === $id ) {
				$field_object = get_field_object( $key, $id );

				if ( $field_object ) {
					return $field_object['value'];
				}

				return $result;
			}

			$field      = acf_get_field( $id );
			$sub_fields = isset( $field['sub_fields'] ) ? $field['sub_fields'] : false;

			if ( empty( $sub_fields ) ) {
				return $result;
			}

			$count = jet_engine()->listings->data->get_meta( $key );

			if ( ! $count ) {
				return $result;
			}

			$result = array();

			for ( $i = 0; $i < absint( $count ); $i++ ) {

				$item = array();

				foreach ( $sub_fields as $sub_field ) {
					$item[ $sub_field['name'] ] = jet_engine()->listings->data->get_meta(
						$key . '_' . $i . '_' . $sub_field['name']
					);
				}


				$result[] = $item;
			}

			return $result;

		}

		/**
		 * Render field
		 *
		 * @param  [type] $result   [description]
		 * @param  array  $settings [description]
		 * @return [type]           [description]
		 */
		public function field_render( $result = null, $settings = array() ) {

			if ( 'acf_field_groups' !== $settings['dynamic_field_source'] ) {
				return $result;
			}

			$key = isset( $settings['acf_field_key'] ) ? $settings['acf_field_key'] : false;

			if ( ! empty( $settings['dynamic_field_post_meta_custom'] ) ) {
				$key = $settings['dynamic_field_post_meta_custom'];
			}

			$key = $this->parse_key( $key, true );

			if ( ! $key ) {
				return $result;
			}

			$field_id = $key['id'];
			$key      = $key['key'];

			if ( 'options' === $field_id ) {
				$field_object = get_field_object( $key, $field_id );
			} elseif ( 'custom_meta' !== $field_id ) {
				$field_object = get_field_object( $field_id, jet_engine()->listings->data->get_current_object() );
			} else {
				$field_object = false;
			}

			if ( $field_object ) {
				return $field_object['value'];
			}

			return jet_engine()->listings->data->get_meta( $key );

		}

		/**
		 * Return custom image for ACF
		 *
		 * @param  [type] $result   [description]
		 * @param  [type] $settings [description]
		 * @param  [type] $size     [description]
		 * @return [type]           [description]
		 */
		public function image_render( $result, $settings, $size, $render ) {

			if ( 'acf_field_groups' !== $settings['dynamic_image_source'] ) {
				return $result;
			}

			$key = isset( $settings['acf_field_key'] ) ? $settings['acf_field_key'] : false;

			if ( ! empty( $settings['dynamic_image_source_custom'] ) ) {
				$key = $settings['dynamic_image_source_custom'];
			}

			$key = $this->parse_key( $key, true );

			if ( ! $key ) {
				return $result;
			}

			$field_id = $key['id'];
			$key      = $key['key'];

			if ( 'options' === $field_id ) {
				$field_object = get_field_object( $key, $field_id );
			} else {
				$field_object = false;
			}

			if ( $field_object ) {
				$image = $field_object['value'];

				if ( ! empty( $field_object['return_format'] ) ) {

					switch ( $field_object['return_format'] ) {
						case 'array':
							$image = $image['ID'];
							break;
						case 'url':
							$image = attachment_url_to_postid( $image );
							break;
					}
				}

			} else {
				$image = jet_engine()->listings->data->get_meta( $key );
			}

			if ( ! $image ) {
				return $result;
			}

			$image = absint( $image );

			if ( ! $image ) {
				return $result;
			}

			return wp_get_attachment_image( $image, $size, false, array( 'alt' => $render->get_image_alt( $image, $settings ) ) );

		}

		/**
		 * Return custom image URL for ACF
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

			if ( 'acf_field_groups' !== $settings['image_link_source'] ) {
				return $url;
			}

			$key = isset( $settings['acf_link_field_key'] ) ? $settings['acf_link_field_key'] : false;
			$key = $this->parse_key( $key, true );

			if ( ! $key ) {
				return $url;
			}

			$field_id = $key['id'];
			$key      = $key['key'];

			if ( 'options' === $field_id ) {
				$field_object = get_field_object( $key, $field_id );

				if ( $field_object ) {
					return $field_object['value'];
				}
			}

			$val = jet_engine()->listings->data->get_meta( $key );

			if ( 0 < absint( $val ) ) {
				return get_permalink( $val );
			} else {
				return $val;
			}

		}

		/**
		 * Return custom image URL for ACF
		 *
		 * @param  [type] $url      [description]
		 * @param  array  $settings [description]
		 * @return [type]           [description]
		 */
		public function link_render( $url = false, $settings = array() ) {

			if ( ! empty( $settings['dynamic_link_source_custom'] ) ) {
				return $url;
			}

			$key = isset( $settings['acf_field_key'] ) ? $settings['acf_field_key'] : false;
			$key = $this->parse_key( $key, true );

			if ( empty( $key ) ) {
				return $url;
			}

			$field_id = $key['id'];
			$key      = $key['key'];

			if ( 'options' === $field_id ) {
				$field_object = get_field_object( $key, $field_id );
			} elseif ( 'custom_meta' !== $field_id ) {
				$field_object = get_field_object( $field_id, jet_engine()->listings->data->get_current_object() );
			} else {
				$field_object = false;
			}

			if ( $field_object ) {
				return $field_object['value'];
			}

			$val = jet_engine()->listings->data->get_meta( $key );

			if ( 0 < absint( $val ) ) {
				return get_permalink( $val );
			} else {
				return $val;
			}

		}

		/**
		 * Parse key from string
		 *
		 * @param  [type] $key [description]
		 * @return [type]      [description]
		 */
		public function parse_key( $key = null, $return_with_id = false ) {

			if ( ! $key ) {
				return false;
			}

			$key_parts = explode( '::', $key );

			if ( ! isset( $key_parts[1] ) ) {
				if ( true === $return_with_id ) {
					return array(
						'key' => $key,
						'id'  => 'custom_meta',
					);
				} else {
					return $key;
				}
			}

			$key = $key_parts[1];

			if ( true === $return_with_id ) {
				return array(
					'key' => $key,
					'id'  => $key_parts[0],
				);
			} else {
				return $key;
			}

		}

		/**
		 * Add field source
		 */
		public function add_field_source( $sources ) {
			$sources['acf_field_groups'] = __( 'ACF', 'jet-engine' );
			return $sources;
		}

		/**
		 * Repeater controls
		 *
		 * @return [type] [description]
		 */
		public function repeater_controls( $widget ) {

			$this->add_control( $widget, array(
				'group'     => 'repeater',
				'condition' => array(
					'dynamic_field_source' => 'acf_field_groups',
				),
			) );

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
					'dynamic_image_source' => 'acf_field_groups',
				),
			) );

		}

		public function linked_image_controls( $widget ) {

			$this->add_control( $widget, array(
				'id'        => 'acf_link_field_key',
				'group'     => 'links',
				'condition' => array(
					'linked_image'      => 'yes',
					'image_link_source' => 'acf_field_groups',
				),
			) );

		}

		public function link_controls( $widget ) {

			$this->add_control( $widget, array(
				'group'     => 'links',
				'condition' => array(
					'dynamic_link_source' => 'acf_field_groups',
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
					'dynamic_field_source' => 'acf_field_groups',
				),
			) );

		}

		public function add_control( $widget = null, $args = array() ) {

			$group     = isset( $args['group'] ) ? $args['group'] : 'fields';
			$condition = isset( $args['condition'] ) ? $args['condition'] : array();
			$id        = isset( $args['id'] ) ? $args['id'] : 'acf_field_key';

			$widget->add_control(
				$id,
				array(
					'label'     => __( 'ACF Field', 'jet-engine' ),
					'type'      => 'select',
					'default'   => '',
					'groups'    => $this->get_fields_goups( $group ),
					'condition' => $condition,
				)
			);

		}

		public function get_fields_goups( $group = 'fields', $for = 'elementor' ) {

			$cb = array(
				'fields'   => 'map_fields',
				'images'   => 'map_images',
				'links'    => 'map_links',
				'repeater' => 'map_repeater',
			);

			$groups = $this->get_raw_goups();
			$result = array();

			if ( empty( $groups ) ) {
				return $result;
			}

			foreach ( $groups as $data ) {

				$fields = array_filter( array_map( array( $this, $cb[ $group ] ), $data['options'] ) );

				if ( ! empty( $fields ) ) {

					if ( 'blocks' === $for ) {

						$blocks_fields = array();

						foreach ( $fields as $name => $label ) {
							$blocks_fields[] = array(
								'value' => $name,
								'label' => $label,
							);
						}

						$result[] = array(
							'label'  => $data['label'],
							'values' => $blocks_fields,
						);
					} else {
						$result[] = array(
							'label'   => $data['label'],
							'options' => $fields,
						);
					}
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

			if ( ! in_array( 'image', $whitelisted[ $type ] ) ) {
				return false;
			} else {
				return $field['label'];
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

			if ( ! in_array( 'link', $whitelisted[ $type ] ) ) {
				return false;
			} else {
				return $field['label'];
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

			if ( ! in_array( 'field', $whitelisted[ $type ] ) ) {
				return false;
			} else {
				return $field['label'];
			}

		}

		/**
		 * Map fields callback
		 *
		 * @param  [type] $field [description]
		 * @return [type]        [description]
		 */
		public function map_repeater( $field ) {

			$whitelisted = $this->whitelisted_fields();
			$type        = $field['type'];

			if ( ! in_array( 'repeater', $whitelisted[ $type ] ) ) {
				return false;
			} else {
				return $field['label'];
			}

		}

		/**
		 * Fields gorups
		 *
		 * @return array
		 */
		public function get_raw_goups() {

			if ( null !== $this->fields_groups ) {
				return $this->fields_groups;
			}

			// ACF >= 5.0.0
			if ( function_exists( 'acf_get_field_groups' ) ) {
				$groups = acf_get_field_groups();
			} else {
				$groups = apply_filters( 'acf/get_field_groups', [] );
			}

			$options_page_groups_ids = array();

			if ( function_exists( 'acf_options_page' ) ) {
				$pages = acf_options_page()->get_pages();

				foreach ( $pages as $slug => $page ) {
					$options_page_groups = acf_get_field_groups( array(
						'options_page' => $slug,
					) );

					foreach ( $options_page_groups as $options_page_group ) {
						$options_page_groups_ids[] = $options_page_group['ID'];
					}
				}
			}

			$result      = array();
			$whitelisted = $this->whitelisted_fields();

			foreach ( $groups as $group ) {

				// ACF >= 5.0.0
				if ( function_exists( 'acf_get_fields' ) ) {
					$fields = acf_get_fields( $group['ID'] );
				} else {
					$fields = apply_filters( 'acf/field_group/get_fields', [], $group['id'] );
				}

				$options = [];

				if ( ! is_array( $fields ) ) {
					continue;
				}

				$has_option_page_location = in_array( $group['ID'], $options_page_groups_ids, true );
				$is_only_options_page = $has_option_page_location && 1 === count( $group['location'] );

				foreach ( $fields as $field ) {

					if ( ! isset( $whitelisted[ $field['type'] ] ) ) {
						continue;
					}

					if ( $has_option_page_location ) {
						$key = 'options::' . $field['name'];

						$options[ $key ] = array(
							'type'  => $field['type'],
							'label' => __( 'Options', 'jet-engine' ) . ':' . $field['label'],
						);

						if ( $is_only_options_page ) {
							continue;
						}
					}

					$key = $field['key'] . '::' . $field['name'];
					$options[ $key ] = array(
						'type'  => $field['type'],
						'label' => $field['label']
					);

				}

				if ( empty( $options ) ) {
					continue;
				}

				$result[] = array(
					'label'   => $group['title'],
					'options' => $options,
				);
			}

			$this->fields_groups = $result;

			return $this->fields_groups;

		}

		/**
		 * Returns whitelisted fields
		 *
		 * @return [type] [description]
		 */
		public function whitelisted_fields() {

			return array(
				'text'             => array( 'field', 'link' ),
				'textarea'         => array( 'field' ),
				'number'           => array( 'field' ),
				'range'            => array( 'field' ),
				'email'            => array( 'field', 'link' ),
				'url'              => array( 'field', 'link' ),
				'wysiwyg'          => array( 'field' ),
				'image'            => array( 'link', 'image' ),
				'file'             => array( 'field', 'link' ),
				'gallery'          => array( 'field' ),
				'select'           => array( 'field' ),
				'radio'            => array( 'field' ),
				'checkbox'         => array( 'field' ),
				'button_group'     => array( 'field' ),
				'true_false'       => array( 'field' ),
				'page_link'        => array( 'field', 'link' ),
				'post_object'      => array( 'field', 'link' ),
				'relationship'     => array( 'field', 'link' ),
				'taxonomy'         => array( 'field', 'link' ),
				'date_picker'      => array( 'field', 'link' ),
				'date_time_picker' => array( 'field' ),
				'time_picker'      => array( 'field' ),
				'repeater'         => array( 'repeater' ),
				'oembed'           => array( 'field' ),
			);

		}

		public function listing_link_controls( $document ) {

			$this->add_control( $document, array(
				'group'     => 'links',
				'condition' => array(
					'listing_link_source' => 'acf_field_groups',
				),
			) );

		}

		public function blocks_listing_link_controls( $link_controls, $settings ) {

			$acf_link_controls = array(
				'jet_engine_listing_link_acf_field_key' => array(
					'label'     => __( 'ACF Field', 'jet-engine' ),
					'groups'    => $this->get_fields_goups( 'links' ),
					'value'     => ! empty( $settings['acf_field_key'] ) ? $settings['acf_field_key'] : '',
					'condition' => array(
						'jet_engine_listing_link'        => 'yes',
						'jet_engine_listing_link_source' => 'acf_field_groups',
					),
				)
			);

			$link_controls = \Jet_Engine_Tools::array_insert_after( $link_controls, 'jet_engine_listing_link_source', $acf_link_controls );

			return $link_controls;
		}

		public function save_blocks_editor_settings( $post_id ) {

			if ( ! isset( $_POST['jet_engine_listing_link_acf_field_key'] ) ) {
				return;
			}

			$elementor_page_settings = get_post_meta( $post_id, '_elementor_page_settings', true );

			$elementor_page_settings['acf_field_key'] = esc_attr( $_POST[ 'jet_engine_listing_link_acf_field_key' ] );

			update_post_meta( $post_id, '_elementor_page_settings', $elementor_page_settings );
		}

		public function listing_link_render( $url, $settings ) {

			if ( empty( $settings['listing_link_source'] ) || 'acf_field_groups' !== $settings['listing_link_source'] ) {
				return $url;
			}

			return $this->link_render( $url, $settings );
		}

		public function localize_fields( $config = array() ) {

			$config['acfFields']         = $this->get_fields_goups( $group = 'fields', 'blocks' );
			$config['acfLinksFields']    = $this->get_fields_goups( $group = 'links', 'blocks' );
			$config['acfImagesFields']   = $this->get_fields_goups( $group = 'images', 'blocks' );
			$config['acfRepeaterFields'] = $this->get_fields_goups( $group = 'repeater', 'blocks' );

			return $config;
		}

		public function add_block_attr( $atts = array() ) {

			$atts['acf_field_key'] = array(
				'type'    => 'string',
				'default' => '',
			);

			if ( false !== strpos( current_filter(), 'dynamic-image' ) ) {
				$atts['acf_link_field_key'] = array(
					'type'    => 'string',
					'default' => '',
				);
			}

			return $atts;
		}

	}

}

$jet_engine_acf = new Jet_Engine_ACF_Package();

/**
 * Define additional functions
 */
function jet_engine_acf_gallery_wp( $value ) {

	if ( ! $value ) {
		return false;
	}

	if ( ! is_array( $value ) ) {
		return false;
	}

	return do_shortcode( '[gallery ids="' . implode( ',', $value ) . '"]' );
}
