<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Listings;

use Jet_Engine\Modules\Rest_API_Listings\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	public $source = 'rest_api_endpoint';
	public $current_item = false;

	/**
	 * Class constructor
	 */
	public function __construct() {

		require_once Module::instance()->module_path( 'listings/query.php' );
		require_once Module::instance()->module_path( 'listings/blocks.php' );

		new Query( $this->source );
		new Blocks( $this );

		if ( jet_engine()->has_elementor() ) {
			require_once Module::instance()->module_path( 'listings/elementor.php' );
			new Elementor( $this );
		}
		
		add_filter(
			'jet-engine/templates/listing-sources',
			array( $this, 'register_listing_source' )
		);

		add_action(
			'jet-engine/templates/listing-options',
			array( $this, 'register_listing_popup_options' )
		);

		add_filter(
			'jet-engine/templates/create/data',
			array( $this, 'modify_inject_listing_settings' ),
			99
		);

		add_filter(
			'jet-engine/listing/data/object-fields-groups',
			array( $this, 'add_source_fields' )
		);

		add_filter(
			'jet-engine/listings/dynamic-image/custom-image',
			array( $this, 'custom_image_renderer' ),
			10, 4
		);

		add_filter(
			'jet-engine/listings/dynamic-image/custom-url',
			array( $this, 'custom_image_url' ),
			10, 2
		);

		add_filter(
			'jet-engine/listings/dynamic-link/custom-url',
			array( $this, 'custom_link_url' ),
			10, 2
		);

		add_filter(
			'jet-engine/listing/custom-post-id',
			array( $this, 'set_item_id' ),
			10, 2
		);

		add_filter(
			'jet-engine/listings/data/get-meta/' . $this->source,
			array( $this, 'get_meta' ),
			10, 2
		);

		add_filter( 'jet-engine/listing/render/default-settings', function( $settings ) {
			$settings['jet_rest_query'] = '';
			return $settings;
		} );

		add_filter(
			'jet-engine/listings/dynamic-image/fields',
			array( $this, 'add_image_source_fields' ),
			10, 2
		);

		add_filter(
			'jet-engine/listings/dynamic-link/fields',
			array( $this, 'add_source_fields' ),
			10, 2
		);

		add_action(
			'jet-engine/listings/document/get-preview/' . $this->source,
			array( $this, 'setup_preview' )
		);

		add_action(
			'jet-engine/maps-listing/sources/register',
			array( $this, 'register_map_source' )
		);

	}

	public function set_item_id( $id, $object ) {

		if ( isset( $object->is_rest_api_endpoint ) ) {
			if ( isset( $object->_rest_api_item_id ) ) {
				$id = $object->_rest_api_item_id;
			} elseif ( isset( $object->ID ) ) {
				$id = $object->ID;
			} elseif ( isset( $object->_ID ) ) {
				$id = $object->_ID;
			} elseif ( isset( $object->id ) ) {
				$id = $object->id;
			}
		}

		return $id;

	}

	public function get_meta( $value, $key ) {

		$key    = str_replace( 'rest_api__', '', $key );
		$object = jet_engine()->listings->data->get_current_object();

		if ( $object && isset( $object->$key ) ) {
			return $object->$key;
		} else {
			return $value;
		}

	}

	/**
	 * Register content types object fields
	 *
	 * @param [type] $groups [description]
	 */
	public function add_source_fields( $groups ) {
		return $this->add_source_fields_for_js( $groups );
	}

	public function add_source_fields_for_js( $groups = array(), $for = 'elementor' ) {

		foreach ( Module::instance()->settings->get() as $endpoint ) {

			$fields = isset( $endpoint['fetched_fields'] ) ? $endpoint['fetched_fields'] : array();
			$prefixed_fields = array();

			foreach ( $fields as $field ) {
				if ( 'blocks' === $for ) {
					$prefixed_fields[] = array(
						'value' => 'rest_api__' . $field,
						'label' => $field,
					);
				} else {
					$prefixed_fields[ 'rest_api__' . $field ] = $field;
				}
			}

			if ( 'blocks' === $for ) {
				$groups[] = array(
					'label'  => __( 'REST API:', 'jet-engine' ) . ' ' . $endpoint['name'],
					'values' => $prefixed_fields,
				);
			} else {
				$groups[] = array(
					'label'   => __( 'REST API:', 'jet-engine' ) . ' ' . $endpoint['name'],
					'options' => $prefixed_fields,
				);
			}

		}

		return $groups;

	}

	/**
	 * Returns custom value from dynamic prop by setting
	 * @param  [type] $setting  [description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function get_custom_value_by_setting( $setting, $settings ) {

		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! isset( $current_object->is_rest_api_endpoint ) ) {
			return false;
		}

		$field  = isset( $settings[ $setting ] ) ? $settings[ $setting ] : '';
		$prefix = 'rest_api__';

		if ( '_permalink' === $field ) {
			return false;
		}

		if ( false === strpos( $field, $prefix ) ) {
			return false;
		}

		$prop = str_replace( $prefix, '', $field );

		return isset( $current_object->$prop ) ? $current_object->$prop : false;

	}

	/**
	 * Returns custom URL for the dynamic image
	 *
	 * @param  [type] $result   [description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function custom_image_url( $result, $settings ) {

		$url = $this->get_custom_value_by_setting( 'image_link_source', $settings );

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
	 * Returns custom URL for dynamic link widget
	 *
	 * @param  [type] $result   [description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function custom_link_url( $result, $settings ) {

		$url = $this->get_custom_value_by_setting( 'dynamic_link_source', $settings );

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
	 * Custom image renderer for custom content type
	 *
	 * @return [type] [description]
	 */
	public function custom_image_renderer( $result = false, $settings = array(), $size = 'full', $render = null ) {

		$image = $this->get_custom_value_by_setting( 'dynamic_image_source', $settings );

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
			'jet-engine/rest-api-listings/image-alt/',
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
	 * Register listing source
	 *
	 * @param  [type] $sources [description]
	 * @return [type]          [description]
	 */
	public function register_listing_source( $sources ) {
		$sources[ $this->source ] = __( 'REST API Endpoint', 'jet-engine' );
		return $sources;
	}

	/**
	 * Register additional options for the listing popup
	 *
	 * @return [type] [description]
	 */
	public function register_listing_popup_options( $data ) {

		?>
		<div class="jet-listings-popup__form-row jet-template-listing jet-template-<?php echo $this->source; ?>">
			<label for="listing_rest_endpoint"><?php esc_html_e( 'From API endpoint:', 'jet-engine' ); ?></label>
			<select id="listing_rest_endpoint" name="rest_api_endpoint" class="jet-listings-popup__control">
				<option value=""><?php _e( 'Select endpoint...', 'jet-engine' ); ?></option>
				<?php
				foreach ( Module::instance()->settings->get() as $endpoint ) {
					$url = jet_engine_trim_string( $endpoint['url'], 55, '...' );
					printf( 
						'<option value="%1$s" %3$s>%2$s</option>',
						$endpoint['id'],
						$endpoint['name'] . ', ' . $url,
						( ! empty( $data['rest_api_endpoint'] ) ? selected( $data['rest_api_endpoint'], $endpoint['id'], false ) : '' )
					);
				}
			?></select>
		</div>
		<?php

	}

	/**
	 * Modify inject listing settings
	 *
	 * @param  array $template_data
	 * @return array
	 */
	public function modify_inject_listing_settings( $template_data ) {

		if ( ! isset( $_REQUEST['listing_source'] ) || $this->source !== $_REQUEST['listing_source'] ) {
			return $template_data;
		}

		if ( empty( $_REQUEST['rest_api_endpoint'] ) ) {
			return $template_data;
		}

		if ( empty( $template_data['meta_input']['_listing_data'] ) ) {
			return $template_data;
		}

		$rest_endpoint = esc_attr( $_REQUEST['rest_api_endpoint'] );

		$template_data['meta_input']['_listing_data']['post_type']                    = $rest_endpoint;
		$template_data['meta_input']['_elementor_page_settings']['listing_post_type'] = $rest_endpoint;
		$template_data['meta_input']['_elementor_page_settings']['rest_api_endpoint'] = $rest_endpoint;

		return $template_data;

	}

	/**
	 * Set default blocks source
	 *
	 * @param [type] $object [description]
	 * @param [type] $editor [description]
	 */
	public function set_blocks_source( $object, $editor ) {

		$preview = $this->setup_preview( $object );

		if ( ! empty( $preview ) ) {
			return $preview['ID'];
		} else {
			return false;
		}

	}

	/**
	 * Setup preview
	 *
	 * @return [type] [description]
	 */
	public function setup_preview( $document = false ) {

		if ( ! $document ) {
			$document = jet_engine()->listings->data->get_listing();
		}

		$source = $document->get_settings( 'listing_source' );

		if ( $this->source !== $source ) {
			return false;
		}

		$endpoint_id = $document->get_settings( 'listing_post_type' );

		if ( ! $endpoint_id ) {
			return false;
		}

		$endpoint = Module::instance()->settings->get( $endpoint_id );

		if ( ! $endpoint ) {
			return false;
		}

		$sample_item = ! empty( $endpoint['sample_item'] ) ? $endpoint['sample_item'] : false;

		if ( $sample_item ) {

			$sample_item->is_rest_api_endpoint = true;

			jet_engine()->listings->data->set_current_object( $sample_item );
			return $sample_item;
		} else {
			return false;
		}

	}

	/**
	 * Register content types media fields
	 *
	 * @param [type] $groups [description]
	 */
	public function add_image_source_fields( $groups, $for ) {
		return $this->add_source_fields( $groups );
	}

	/**
	 * Register source for maps listings.
	 *
	 * @param object $sources_manager
	 */
	public function register_map_source( $sources_manager ) {
		require_once Module::instance()->module_path( 'listings/maps-source.php' );
		$sources_manager->register_source( new Rest_API_Maps_Source() );
	}

}
