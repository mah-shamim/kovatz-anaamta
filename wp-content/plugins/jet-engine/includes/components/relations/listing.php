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
 * Define Listing class
 */
class Listing {

	private $processed_relation = false;

	private $processed_listing = false;

	private $relations_source = 'jet_engine_related_items';

	private $listing_source = 'relation_meta_data';

	public function __construct() {

		// setup and reset currently processed relation
		add_action( 'jet-engine/relations/macros/get-related', array( $this, 'set_relation' ) );
		add_action( 'jet-engine/listings/setup', array( $this, 'set_listing' ) );
		add_action( 'jet-engine/listings/reset', array( $this, 'reset_listing' ) );

		// add relation meta fields into available sources for dynamic widgets
		add_filter( 'jet-engine/listings/data/sources', array( $this, 'add_meta_source' ) );

		// Elementor integration
		add_action( 'jet-engine/listings/dynamic-field/source-controls', array( $this, 'elementor_dynamic_field_controls' ) );

		// Blocks integration
		add_filter( 'jet-engine/blocks-views/editor-data', array( $this, 'blocks_register_relations_meta' ) );
		add_filter( 'jet-engine/blocks-views/block-types/attributes/dynamic-field', array( $this, 'register_relations_meta_attr' ) );

		// Elementor + Blocks
		add_filter( 'jet-engine/listings/dynamic-image/fields', array( $this, 'dynamic_image_controls' ) );
		add_filter( 'jet-engine/listings/dynamic-link/fields', array( $this, 'dynamic_link_controls' ), 10, 2 );

		// Process meta value
		add_filter( 'jet-engine/listings/dynamic-field/field-value', array( $this, 'return_meta_value' ), 10, 2 );
		add_filter( 'jet-engine/listings/dynamic-image/custom-image', array( $this, 'custom_image_renderer' ), 10, 4 );
		add_filter( 'jet-engine/listings/dynamic-image/custom-url', array( $this, 'custom_image_url' ), 10, 2 );
		add_filter( 'jet-engine/listings/dynamic-link/custom-url', array( $this, 'custom_link_url' ), 10, 2 );

		// Add and ge relations props for the Dynamic Field widget
		add_filter( 'jet-engine/listing/data/object-fields-groups', array( $this, 'add_dynamic_field_props' ), 999 );
		add_filter( 'jet-engine/listings/data/prop-not-found', array( $this, 'get_dynamic_field_prop' ), 10, 3 );

	}

	/**
	 * Check id we trying to get relation property and get the appropriate data
	 */
	public function get_dynamic_field_prop( $result, $prop, $object ) {
		
		if ( false === strpos( $prop, $this->relations_source ) ) {
			return $result;
		}

		$prop   = str_replace( $this->relations_source . '_', '', $prop );
		$rel_id = false;
		$get    = false;

		if ( false !== strpos( $prop, 'children_' ) ) {
			$get    = 'children';
			$rel_id = str_replace( 'children_', '', $prop );
		} elseif ( false !== strpos( $prop, 'parents_' ) ) {
			$get = 'parents';
			$rel_id = str_replace( 'parents_', '', $prop );
		}

		if ( ! $get || ! $rel_id ) {
			return $result;
		}

		$relation = jet_engine()->relations->get_active_relations( $rel_id );

		if ( ! $relation ) {
			return $result;
		}

		$object_id = jet_engine()->listings->data->get_current_object_id( $object );

		if ( ! $object_id ) {
			return $result;
		}

		$related_ids = array();

		switch ( $get ) {
			case 'parents':
				$related_ids = $relation->get_parents( $object_id, 'ids' );
				break;

			default:
				$related_ids = $relation->get_children( $object_id, 'ids' );
				break;
		}

		$related_ids = ! empty( $related_ids ) ? $related_ids : array();

		return $related_ids;

	}

	/**
	 * Register listing source
	 */
	public function add_dynamic_field_props( $groups ) {

		$prefixed_rels_list = $this->get_prefixed_relations_sources();

		if ( ! empty( $prefixed_rels_list ) ) {

			$groups[] = array(
				'label'   => __( 'Related items for current object', 'jet-engine' ),
				'options' => $prefixed_rels_list,
			);
		}

		return $groups;
	}

	public function get_prefixed_relations_sources() {

		$relations = jet_engine()->relations->get_active_relations();
		$prefixed_rels_list = array();

		if ( ! empty( $relations ) ) {

			foreach ( $relations as $rel ) {
				$prefixed_rels_list[ $this->relations_source . '_children_' . $rel->get_id() ] = __( 'Children from', 'jet-engine' ) . ' ' . $rel->get_relation_name();
				$prefixed_rels_list[ $this->relations_source . '_parents_' . $rel->get_id() ] = __( 'Parents from', 'jet-engine' ) . ' ' . $rel->get_relation_name();
			}

		}

		return $prefixed_rels_list;

	}

	/**
	 * Set currently processed relation object
	 *
	 * @param [type] $relation [description]
	 */
	public function set_relation( $relation ) {
		$this->processed_relation = $relation;
	}

	/**
	 * Setup current listing for relation
	 */
	public function set_listing( $listing_id ) {
		if ( ! $this->processed_listing && $this->processed_relation ) {
			$this->processed_listing = $listing_id;
		}
	}

	/**
	 * Reset listing and relation when its processed
	 */
	public function reset_listing( $listing_id ) {

		if ( $this->processed_listing && $this->processed_relation && $this->processed_listing === $listing_id ) {
			$this->processed_listing  = false;
			$this->processed_relation = false;
		}
	}

	/**
	 * Register meta source for realtions meta data
	 *
	 * @param [type] $sources [description]
	 */
	public function add_meta_source( $sources ) {

		$meta_fields = jet_engine()->relations->get_active_relations_meta_fields();

		if ( ! empty( $meta_fields ) ) {
			$sources[ $this->listing_source ] = __( 'Relation Meta Data', 'jet-engine' );
		}

		return $sources;
	}

	/**
	 * Process meta value
	 *
	 * @return [type] [description]
	 */
	public function return_meta_value( $result, $settings ) {

		$source = ! empty( $settings['dynamic_field_source'] ) ? $settings['dynamic_field_source'] : false;

		if ( $this->listing_source !== $source ) {
			return $result;
		}

		$data = ! empty( $settings['dynamic_field_relation_meta'] ) ? $settings['dynamic_field_relation_meta'] : false;

		if ( ! $data ) {
			return $result;
		}

		$data     = explode( '::', $data, 2 );
		$rel_id   = $data[0];
		$field    = $data[1];
		$relation = jet_engine()->relations->get_active_relations( $rel_id );

		if ( ! $relation ) {
			return $result;
		}

		$object_context  = isset( $settings['object_context'] ) ? $settings['object_context'] : false;
		$current_context = 'rel_' . $rel_id;
		$default_object  = false;
		$current_object  = false;

		if ( $object_context === $current_context ) {

			$default_object = jet_engine()->listings->data->get_current_object();
			$current_object = $relation->apply_context();

			if ( is_array( $current_object ) ) {
				$current_object = (object) $current_object;
			}

			if ( $current_object && is_object( $current_object ) ) {
				jet_engine()->listings->data->set_current_object( $current_object );
			}

		}

		$meta = $relation->get_current_meta( $field );

		if ( $object_context === $current_context && $default_object && $current_object ) {
			jet_engine()->listings->data->set_current_object( $default_object );
		}

		return $meta;

	}

	/**
	 * Returns relation meta value for selected settings from all settings list
	 *
	 * @param  [type] $setting  [description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function get_meta_from( $setting, $settings ) {

		$source = ! empty( $settings[ $setting ] ) ? $settings[ $setting ] : false;

		if ( ! $source || false === strpos( $source, $this->listing_source . '::' ) ) {
			return false;
		}

		$data     = explode( '::', $source );
		$rel_id   = $data[1];
		$field    = $data[2];
		$relation = jet_engine()->relations->get_active_relations( $rel_id );

		if ( ! $relation ) {
			return false;
		}

		return $relation->get_current_meta( $field );

	}

	/**
	 * Renders custom image for given relation meta
	 *
	 * @return [type] [description]
	 */
	public function custom_image_renderer( $result = false, $settings = array(), $size = 'full', $render = null ) {

		$image = $this->get_meta_from( 'dynamic_image_source', $settings );

		if ( is_array( $image ) && isset( $image['url'] ) ) {

			if ( $size && 'full' !== $size ) {
				$image = $image['id'];
			} else {
				$image = $image['url'];
			}

		} elseif ( is_array( $image ) ) {
			$image = array_values( $image );
			$image = $image[0];
		}

		if ( ! $image ) {
			return $result;
		}

		ob_start();

		$current_object = jet_engine()->listings->data->get_current_object();

		$alt = apply_filters(
			'jet-engine/relations/meta/image-alt/',
			false,
			$current_object
		);

		if ( filter_var( $image, FILTER_VALIDATE_URL ) ) {
			$render->print_image_html_by_src( $image, $alt );
		} else {
			echo wp_get_attachment_image( $image, $size, false, array( 'alt' => $alt ) );
		}

		return ob_get_clean();

	}

	/**
	 * Returns custom link URL for Dynamic Field widget/block
	 *
	 * @param  [type] $result   [description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function custom_link_url( $result, $settings ) {

		$url = $this->get_meta_from( 'dynamic_link_source', $settings );

		if ( is_numeric( $url ) ) {
			$url = get_permalink( $url );
		}

		if ( ! $url ) {
			return $result;
		} else {
			return $url;
		}

	}

	/**
	 * Returns custom link URL for image link for Dynamic Image widget/block
	 *
	 * @param  [type] $result   [description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function custom_image_url( $result, $settings ) {

		$url = $this->get_meta_from( 'image_link_source', $settings );

		if ( is_numeric( $url ) ) {
			$url = get_permalink( $url );
		}

		if ( ! $url ) {
			return $result;
		} else {
			return $url;
		}

	}

	/**
	 * Returns meta fields list for the requested context
	 *
	 * @param  [type] $context [description]
	 * @return [type]          [description]
	 */
	public function get_meta_fields_for_options( $context = 'elementor', $prefix = false, $type = array() ) {

		$raw_fields  = jet_engine()->relations->get_active_relations_meta_fields();
		$meta_fields = array();

		if ( empty( $raw_fields ) ) {
			return $meta_fields;
		}

		foreach ( $raw_fields as $rel_id => $rel_data ) {

			$group = array();

			foreach ( $rel_data['fields'] as $field ) {

				if ( ! empty( $type ) && ! in_array( $field['type'], $type ) ) {
					continue;
				}

				$key = $rel_id . '::' . $field['name'];

				if ( $prefix ) {
					$key = $this->listing_source . '::' . $key;
				}

				if ( 'blocks' === $context ) {
					$group[] = array(
						'value' => $key,
						'label' => $field['title'],
					);

					$group[] = array(
						'value' => $key . '::child',
						'label' => $field['title'] . ' ' . __( '(for child object)', 'jet-engine' ),
					);

					$group[] = array(
						'value' => $key . '::parent',
						'label' => $field['title'] . ' ' . __( '(for parent object)', 'jet-engine' ),
					);
				} else {
					$group[ $key ] = $field['title'];
					$group[ $key . '::child' ] = $field['title'] . ' ' . __( '(for child object)', 'jet-engine' );
					$group[ $key . '::parent' ] = $field['title'] . ' ' . __( '(for parent object)', 'jet-engine' );
				}

			}

			if ( ! empty( $group ) ) {

				$label = $rel_data['label'];

				if ( $prefix ) {
					$label = __( 'Relation Meta Data', 'jet-engine' ) . ': ' . $label;
				}

				if ( 'blocks' === $context ) {
					$meta_fields[] = array(
						'label'  => $label,
						'values' => $group,
					);
				} else {
					$meta_fields[] = array(
						'label'   => $label,
						'options' => $group,
					);
				}

			}
		}

		return $meta_fields;

	}

	/**
	 * Register relations meta fields for the block editor configuration
	 *
	 * @param  [type] $config [description]
	 * @return [type]         [description]
	 */
	public function blocks_register_relations_meta( $config ) {

		$config['relationsMeta'] = $this->get_meta_fields_for_options( 'blocks' );

		return $config;
	}

	/**
	 * Register `dynamic_field_relation_meta` attribute in the Dynamic Field block.
	 *
	 * @param  array $atts Block attributes array.
	 * @return array
	 */
	public function register_relations_meta_attr( $atts = array() ) {

		$atts['dynamic_field_relation_meta'] = array(
			'type'    => 'string',
			'default' => '',
		);

		return $atts;
	}

	/**
	 * Register realtion meta source control for the Elementor dynamic field widget
	 *
	 * @param  [type] $widget [description]
	 * @return [type]         [description]
	 */
	public function elementor_dynamic_field_controls( $widget ) {

		$meta_fields = $this->get_meta_fields_for_options( 'elementor' );

		if ( empty( $meta_fields ) ) {
			return;
		}

		$widget->add_control(
			'dynamic_field_relation_meta',
			array(
				'label'       => __( 'Meta Field', 'jet-engine' ),
				'type'        => 'select',
				'description' => __( 'By default you can use plain meta field name. For some cases (for example if you created relation by same post types, taxonomies etc.) you need to define direction of meta you trying to get. "for child object" means you getting meta from child object of current relation, "for parent" - from parent object of current relation' ),
				'default'     => '',
				'groups'      => $meta_fields,
				'condition'   => array(
					'dynamic_field_source' => $this->listing_source,
				),
			)
		);

	}

	/**
	 * Returns list of allowed media meta fields
	 *
	 * @param  [type] $result [description]
	 * @return [type]         [description]
	 */
	public function dynamic_image_controls( $result ) {

		$image_fields = $this->get_meta_fields_for_options( 'elementor', true, array( 'media' ) );

		if ( ! empty( $image_fields ) ) {
			$result = array_merge( $result, $image_fields );
		}

		return $result;

	}

	/**
	 * Returns list of allowed fields to use as links
	 *
	 * @param  [type] $result [description]
	 * @return [type]         [description]
	 */
	public function dynamic_link_controls( $result ) {

		$fields = $this->get_meta_fields_for_options( 'elementor', true );

		if ( ! empty( $fields ) ) {
			$result = array_merge( $result, $fields );
		}

		return $result;

	}

}
