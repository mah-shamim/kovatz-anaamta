<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Listings;

use Jet_Engine\Modules\Custom_Content_Types\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	public $source = 'custom_content_type';
	public $repeater_source = 'custom_content_type_repeater';
	public $current_item = false;

	public $query = null;

	/**
	 * Class constructor
	 */
	public function __construct() {

		require_once Module::instance()->module_path( 'listings/query.php' );
		require_once Module::instance()->module_path( 'listings/blocks.php' );
		require_once Module::instance()->module_path( 'listings/popups.php' );
		require_once Module::instance()->module_path( 'listings/context.php' );
		require_once Module::instance()->module_path( 'listings/maps.php' );

		$this->query = new Query( $this->source );
		new Blocks( $this );
		new Popups();
		new Context();
		new Maps( $this->source );

		if ( jet_engine()->has_elementor() ) {
			require_once Module::instance()->module_path( 'listings/elementor.php' );
			new Elementor( $this );
		}

		add_filter(
			'jet-engine/templates/listing-sources',
			array( $this, 'register_listing_source' )
		);

		add_filter(
			'jet-engine/templates/admin-columns/type/' . $this->source,
			array( $this, 'type_admin_column_cb' ),
			10, 2
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
			'jet-engine/listings/dynamic-image/fields',
			array( $this, 'add_image_source_fields' ),
			10, 2
		);

		add_filter(
			'jet-engine/listings/dynamic-link/fields',
			array( $this, 'add_source_fields' ),
			10, 2
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
			'jet-engine/listings/dynamic-link/delete-url-args',
			array( $this, 'set_delete_url_args' )
		);

		add_filter(
			'jet-engine/listings/delete-post/query-args',
			array( $this, 'set_final_delete_query_args' ),
			10, 2
		);

		add_action(
			'jet-engine/listings/delete-post/before',
			array( $this, 'maybe_delete_content_type_item' )
		);

		add_filter(
			'jet-engine/listings/data/object-date',
			array( $this, 'get_object_date' ),
			10, 2
		);

		add_action( 'jet-engine/register-macros', array( $this, 'register_macros' ) );

		add_filter( 'jet-engine/listings/macros/current-id', function( $result, $object ) {

			if ( isset( $object->cct_slug ) ) {
				$result = $object->_ID;
			}

			return $result;

		}, 10, 2 );

		add_filter( 'jet-engine/listing/render/default-settings', function( $settings ) {
			$settings['jet_cct_query'] = '{}';
			return $settings;
		} );

		add_filter( 'jet-engine/listing-injections/item-meta-value', array( $this, 'get_injection_cct_field_value' ), 10, 3 );

		// Dynamic Repeater hooks.
		add_filter( 'jet-engine/listings/dynamic-repeater/fields',        array( $this, 'add_repeater_source_fields' ) );
		add_filter( 'jet-engine/listings/dynamic-repeater/pre-get-saved', array( $this, 'get_dynamic_repeater_value' ), 10, 2 );

		// Repeater listing hooks.
		add_filter(
			'jet-engine/listing/grid/query/' . $this->repeater_source,
			array( $this, 'repeater_query_items' ),
			10, 3
		);

		add_filter(
			'jet-engine/listings/dynamic-field/custom-value',
			array( $this, 'get_dynamic_field_repeater_value' ),
			10, 2
		);

		add_filter( 'jet-engine/listing/repeater-listing-sources', array( $this, 'register_repeater_listing_source' ) );

		add_action(
			'jet-engine/listings/document/get-preview/' . $this->source,
			array( $this, 'setup_preview' )
		);

		add_action(
			'jet-engine/listings/document/get-preview/' . $this->repeater_source,
			array( $this, 'setup_preview' )
		);

		add_filter(
			'jet-engine/listings/frontend/custom-listing-url',
			array( $this, 'custom_listing_url' ),
			10, 2
		);

		add_filter(
			'jet-engine/listing/container-atts',
			array( $this, 'add_data_attr_for_listing' ),
			10, 3
		);

		add_filter(
			'jet-engine/listing/grid/queried-id',
			array( $this, 'modify_listing_queried_id_attr' ),
			10, 3
		);

		add_filter(
			'jet-engine/listings/data/object-by-id',
			array( $this, 'set_cct_object_by_id' ),
			10, 3
		);
	}

	public function get_object_date( $date, $object ) {

		if ( isset( $object->cct_created ) ) {
			return $object->cct_created;
		}

		return $date;
	}

	public function register_macros() {
		require_once Module::instance()->module_path( 'listings/macros/current-field.php' );

		new Macros\Current_Field();
	}

	public function type_admin_column_cb( $result, $listing_settings ) {

		$type = isset( $listing_settings['cct_type'] ) ? $listing_settings['cct_type'] : $listing_settings['listing_post_type'];

		if ( ! $type ) {
			return $result;
		}

		$type_instance = Module::instance()->manager->get_content_types( $type );

		if ( ! $type_instance ) {
			return $result;
		}

		return $type_instance->get_arg( 'name' );

	}

	public function maybe_delete_content_type_item( $manager ) {

		if ( empty( $_GET['cct_slug'] ) ) {
			return;
		}

		$type = Module::instance()->manager->get_content_types( esc_attr( $_GET['cct_slug'] ) );

		if ( ! $type ) {
			return;
		}

		$item_id = absint( $_GET[ $manager->query_var ] );
		$handler = $type->get_item_handler();
		$this->current_item = $type->db->get_item( $item_id );

		if ( ! $this->current_item ) {
			return;
		}

		add_filter( 'jet-engine/custom-content-types/user-has-access', array( $this, 'check_user_access_on_delete' ) );

		$handler->delete_item( $item_id, false );

		remove_filter( 'jet-engine/custom-content-types/user-has-access', array( $this, 'check_user_access_on_delete' ) );

		$redirect = ! empty( $_GET['redirect'] ) ? esc_url( $_GET['redirect'] ) : home_url( '/' );

		if ( $redirect ) {
			// Fixed '&' encoding
			$redirect = str_replace( '&#038;', '&', $redirect );
			wp_redirect( $redirect );
			die();
		}

	}

	public function check_user_access_on_delete( $res ) {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		if ( ! $res ) {
			if ( $this->current_item && absint( $this->current_item['cct_author_id'] ) === get_current_user_id() ) {
				$res = true;
			}
		}

		return $res;

	}

	public function set_delete_url_args( $args = array() ) {

		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! isset( $current_object->cct_slug ) ) {
			return $args;
		}

		$args['post_id'] = $current_object->_ID;
		$args['cct_slug'] = $current_object->cct_slug;

		return $args;

	}

	public function set_final_delete_query_args( $query_args, $request_args ) {

		if ( ! empty( $request_args['cct_slug'] ) ) {
			$query_args['cct_slug'] = $request_args['cct_slug'];
		}

		return $query_args;
	}

	public function set_item_id( $id, $object ) {

		if ( isset( $object->cct_slug ) && isset( $object->_ID ) ) {
			$id = $object->_ID;
		}

		return $id;

	}

	/**
	 * Register content types object fields
	 *
	 * @param [type] $groups [description]
	 */
	public function add_source_fields( $groups ) {

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {

			$fields = $instance->get_fields_list();
			$prefixed_fields = array(
				$type . '___ID' => __( 'Item ID', 'jet-engine' ),
			);

			foreach ( $fields as $key => $label ) {
				$prefixed_fields[ $type . '__' . $key ] = $label;
			}

			$groups[] = array(
				'label'   => __( 'Content Type:', 'jet-engine' ) . ' ' . $instance->get_arg( 'name' ),
				'options' => $prefixed_fields,
			);

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

		$object_context = ! empty( $settings['object_context'] ) ? $settings['object_context'] : false;
		$current_object = jet_engine()->listings->data->get_object_by_context( $object_context );

		if ( ! $current_object ) {
			$current_object = jet_engine()->listings->data->get_current_object();
		}

		// Added additional conditions to prepare the current object on Single Post.
		if ( ! isset( $current_object->cct_slug ) && isset( $current_object->ID ) && isset( $current_object->post_type ) ) {
			$current_object = $this->query->maybe_add_item_to_post( $current_object );
		}

		if ( ! isset( $current_object->cct_slug ) ) {
			return false;
		}

		$field  = isset( $settings[ $setting ] ) ? $settings[ $setting ] : '';
		$prefix = $current_object->cct_slug . '__';

		if ( '_permalink' === $field ) {
			$post_id = ! empty( $current_object->cct_single_post_id ) ? $current_object->cct_single_post_id : get_the_ID();

			if ( $post_id ) {
				return get_permalink( $post_id );
			} else {
				return false;
			}

		}

		if ( false === strpos( $field, $prefix ) ) {
			return false;
		}

		$prop = str_replace( $prefix, '', $field );

		$result = false;

		if ( isset( $current_object->$prop ) ) {
			$result = $current_object->$prop;
		} elseif ( isset( $current_object->$field ) ) { // for Single Post
			$result = $current_object->$field;
		}

		return wp_unslash( $result );

	}

	/**
	 * Register content types media fields for Dynamic Image source setting ( for Elementor and Blocks editors ).
	 *
	 * @param array  $groups
	 * @param string $for
	 *
	 * @return array
	 */
	public function add_image_source_fields( $groups, $for ) {

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {

			$fields = $instance->get_fields_list( $for );
			$prefixed_fields = array();

			if ( empty( $fields ) ) {
				continue;
			}

			foreach ( $fields as $key => $label ) {
				$prefixed_fields[ $type . '__' . $key ] = $label;
			}

			$groups[] = array(
				'label'   => __( 'Content Type:', 'jet-engine' ) . ' ' . $instance->get_arg( 'name' ),
				'options' => $prefixed_fields,
			);
		}

		return $groups;

	}

	/**
	 * Returns custom URL for the dynamic image
	 *
	 * @param  [type] $result   [description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function custom_image_url( $result, $settings ) {

		$source  = false;
		$listing = jet_engine()->listings->data->get_listing();

		if ( $listing ) {
			$source = $listing->get_settings( 'listing_source' );
		}

		if ( $this->repeater_source === $source && ! empty( $settings['image_link_source_custom'] ) ) {
			$url = $this->get_repeater_key_value( $settings['image_link_source_custom'] );
		} else {
			$url = $this->get_custom_value_by_setting( 'image_link_source', $settings );
		}

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

		$source  = false;
		$listing = jet_engine()->listings->data->get_listing();

		if ( $listing ) {
			$source = $listing->get_settings( 'listing_source' );
		}

		if ( $this->repeater_source === $source && ! empty( $settings['dynamic_link_source_custom'] ) ) {
			$url = $this->get_repeater_key_value( $settings['dynamic_link_source_custom'] );
		} else {
			$url = $this->get_custom_value_by_setting( 'dynamic_link_source', $settings );
		}

		if ( is_numeric( $url ) && empty( $settings['url_prefix'] ) ) {
			$permalink = get_permalink( $url );

			if ( $permalink ) {
				$url = $permalink;
			}
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

		$source  = false;
		$listing = jet_engine()->listings->data->get_listing();

		if ( $listing ) {
			$source = $listing->get_settings( 'listing_source' );
		}

		if ( $this->repeater_source === $source && ! empty( $settings['dynamic_image_source_custom'] ) ) {
			$image = $this->get_repeater_key_value( $settings['dynamic_image_source_custom'] );
		} else {
			$image = $this->get_custom_value_by_setting( 'dynamic_image_source', $settings );
		}

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
			'jet-engine/cct/image-alt/' . $current_object->cct_slug,
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
		$sources[ $this->source ] = __( 'Custom Content Type', 'jet-engine' );
		$sources[ $this->repeater_source ] = __( 'Custom Content Type Repeater Field', 'jet-engine' );
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
			<label for="listing_content_type"><?php esc_html_e( 'From content type:', 'jet-engine' ); ?></label>
			<select id="listing_content_type" name="cct_type" class="jet-listings-popup__control">
				<option value=""><?php _e( 'Select content type...', 'jet-engine' ); ?></option>
				<?php
				foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {
					printf( 
						'<option value="%1$s" %3$s>%2$s</option>',
						$type,
						$instance->get_arg( 'name' ),
						( ! empty( $data['cct_type'] ) ? selected( $data['cct_type'], $type, false ) : '' )
					);
				}
			?></select>
		</div>
		<div class="jet-listings-popup__form-row jet-template-listing jet-template-<?php echo $this->repeater_source; ?>">
			<label for="cct_repeater_field"><?php esc_html_e( 'Repeater Field:', 'jet-engine' ); ?></label>
			<select id="cct_repeater_field" name="cct_repeater_field">
				<option value=""><?php _e( 'Select...', 'jet-engine' ); ?></option>
				<?php
				foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {

					$fields = $instance->get_fields_list( 'repeater' );

					if ( empty( $fields ) ) {
						continue;
					}

					$group_label = __( 'Content Type:', 'jet-engine' ) . ' ' . $instance->get_arg( 'name' );

					echo '<optgroup label="' . $group_label . '">';

					foreach ( $fields as $key => $label ) {
						printf( 
							'<option value="%1$s" %3$s>%2$s</option>',
							$type . '__' . $key, 
							$label,
							( ! empty( $data['cct_repeater_field'] ) ? selected( $data['cct_repeater_field'], $type . '__' . $key, false ) : '' )
						);
					}

					echo '</optgroup>';
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

		if ( ! isset( $_REQUEST['listing_source'] ) ) {
			return $template_data;
		}

		if ( ! in_array( $_REQUEST['listing_source'], array( $this->source, $this->repeater_source ) ) ) {
			return $template_data;
		}

		if ( empty( $template_data['meta_input']['_listing_data'] ) ) {
			return $template_data;
		}

		switch ( $_REQUEST['listing_source'] ) {

			case $this->source:

				if ( empty( $_REQUEST['cct_type'] ) ) {
					return $template_data;
				}

				$cct = esc_attr( $_REQUEST['cct_type'] );

				$template_data['meta_input']['_listing_data']['post_type']                    = $cct;
				$template_data['meta_input']['_elementor_page_settings']['listing_post_type'] = $cct;
				$template_data['meta_input']['_elementor_page_settings']['cct_type']          = $cct;
				break;

			case $this->repeater_source:

				if ( empty( $_REQUEST['cct_repeater_field'] ) ) {
					return $template_data;
				}

				$r_field = esc_attr( $_REQUEST['cct_repeater_field'] );

				$template_data['meta_input']['_elementor_page_settings']['repeater_field']     = $r_field;
				$template_data['meta_input']['_elementor_page_settings']['cct_repeater_field'] = $r_field;
				break;
		}

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
			return $preview['_ID'];
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

		if ( $this->source !== $source && $this->repeater_source !== $source ) {
			return false;
		}

		$content_type = false;
		$from_field   = false;

		if ( $this->source === $source ) {

			$content_type = $document->get_settings( 'listing_post_type' );

		} elseif ( $this->repeater_source === $source ) {

			$r_field = $document->get_settings( 'repeater_field' );

			if ( ! empty( $r_field ) ) {
				$r_field_data = explode( '__', $r_field );
				$content_type = $r_field_data[0];
				$from_field   = $r_field_data[1];
			}
		}

		if ( ! $content_type ) {
			return false;
		}

		$type = Module::instance()->manager->get_content_types( $content_type );

		if ( ! $type ) {
			return false;
		}

		$flag = \OBJECT;
		$type->db->set_format_flag( $flag );

		$items = $type->db->query( array(), 1 );

		if ( ! empty( $items ) && false !== $from_field ) {
			$items = ( isset( $items[0]->$from_field ) && is_array( $items[0]->$from_field ) )
						? array_values( $items[0]->$from_field )
						: [];
		}

		if ( ! empty( $items ) ) {

			$item = is_object( $items[0] ) ? $items[0] : (object) $items[0];

			$item->cct_slug = $content_type;
			jet_engine()->listings->data->set_current_object( $item );

			return $item;

		} else {
			return false;
		}

	}

	public function get_injection_cct_field_value( $result, $obj, $field ) {

		if ( ! isset( $obj->cct_slug ) ) {
			return $result;
		}

		if ( ! isset( $obj->$field ) ) {
			return '';
		}

		return array( $obj->$field );
	}

	/**
	 * Register content types repeater fields for Dynamic Repeater source setting ( for Elementor and Blocks editors ).
	 *
	 * @param  array $groups
	 * @return array
	 */
	public function add_repeater_source_fields( $groups ) {

		foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {

			$fields = $instance->get_fields_list( 'repeater' );
			$prefixed_fields = array();

			if ( empty( $fields ) ) {
				continue;
			}

			foreach ( $fields as $key => $label ) {
				$prefixed_fields[ $type . '__' . $key ] = $label;
			}

			$groups[] = array(
				'label'   => __( 'Content Type:', 'jet-engine' ) . ' ' . $instance->get_arg( 'name' ),
				'options' => $prefixed_fields,
			);
		}

		return $groups;
	}

	/**
	 * Maybe retrieves repeater value of content type item.
	 *
	 * @param $value
	 * @param $settings
	 *
	 * @return false|array
	 */
	public function get_dynamic_repeater_value( $value, $settings ) {

		$result = $this->get_custom_value_by_setting( 'dynamic_field_source', $settings );

		if ( ! $result ) {
			return $value;
		}

		return $result;
	}

	/**
	 * Query CCT Repeater items by given arguments
	 *
	 * @param  array  $query
	 * @param  array  $settings
	 * @param  object $widget
	 * @return array
	 */
	public function repeater_query_items( $query, $settings, $widget ) {

		$value = $this->get_repeater_listing_items();

		if ( empty( $value ) ) {
			return $query;
		}

		$count = count( $value );
		$current_object = jet_engine()->listings->data->get_current_object();

		foreach ( $value as $item ) {
			$query[] = (object) array_merge( (array) $current_object, (array) $item );
		}

		$widget->query_vars['page']    = 1;
		$widget->query_vars['pages']   = 1;
		$widget->query_vars['request'] = false;

		return $query;
	}

	public function get_repeater_listing_items() {

		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! isset( $current_object->cct_slug ) ) {
			return false;
		}

		$listing = jet_engine()->listings->data->get_listing();
		$field   = $listing->get_settings( 'repeater_field' );
		$prefix  = $current_object->cct_slug . '__';

		if ( false === strpos( $field, $prefix ) ) {
			return false;
		}

		$prop   = str_replace( $prefix, '', $field );
		$result = false;

		if ( isset( $current_object->$prop ) ) {
			$result = $current_object->$prop;
		} elseif ( isset( $current_object->$field ) ) { // for Single Post
			$result = $current_object->$field;
		}

		return wp_unslash( $result );
	}

	public function get_dynamic_field_repeater_value( $result, $settings ) {

		$source = ! empty( $settings['dynamic_field_source'] ) ? $settings['dynamic_field_source'] : false;;
		$field  = ! empty( $settings['dynamic_field_post_meta_custom'] ) ? $settings['dynamic_field_post_meta_custom'] : false;

		if ( 'repeater_field' !== $source || ! $field ) {
			return $result;
		}

		$repeater_key_value = $this->get_repeater_key_value( $field );

		if ( empty( $repeater_key_value ) ) {
			return $result;
		}

		return $repeater_key_value;
	}

	public function get_repeater_key_value( $field ) {

		$repeater_value = $this->get_repeater_listing_items();

		if ( empty( $repeater_value ) ) {
			return false;
		}

		$index = jet_engine()->listings->data->repeater_index;

		$repeater_value = array_values( $repeater_value );

		if ( empty( $repeater_value[ $index ] ) ) {
			return false;
		}

		return isset( $repeater_value[ $index ][ $field ] ) ? $repeater_value[ $index ][ $field ] : false;
	}

	public function register_repeater_listing_source( $sources ) {

		$sources[] = $this->repeater_source;

		return $sources;
	}

	public function custom_listing_url( $result, $settings ) {

		$url = $this->get_custom_value_by_setting( 'listing_link_source', $settings );

		if ( is_numeric( $url ) ) {
			$url = get_permalink( $url );
		}

		if ( ! $url ) {
			return $result;
		} else {
			return $url;
		}
	}

	public function add_data_attr_for_listing( $attr, $settings, $render ) {

		$type = null;

		if ( $render->listing_query_id ) {
			$query = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $render->listing_query_id );

			if ( ! empty( $query->query_type ) && 'custom-content-type' === $query->query_type ) {
				$query->setup_query();
				$type = ! empty( $query->final_query['content_type'] ) ? $query->final_query['content_type'] : false;
			}

		} elseif ( 'custom_content_type' === jet_engine()->listings->data->get_listing_source() ) {
			$type = jet_engine()->listings->data->get_listing_post_type();
		}

		if ( ! empty( $type ) ) {
			$attr[] = 'data-cct-slug="' . esc_attr( $type ) . '"';
		}

		return $attr;
	}

	public function modify_listing_queried_id_attr( $queried_id, $object_id, $object ) {

		if ( isset( $object->cct_slug ) ) {
			$queried_id = sprintf( '%s|cct:%s', $object_id, $object->cct_slug );
		}

		return $queried_id;
	}

	public function set_cct_object_by_id( $object, $object_id, $object_type ) {

		if ( empty( $object_type ) || 0 !== strpos( $object_type, 'cct:' ) || empty( $object_id ) ) {
			return $object;
		}

		$object_type = explode( ':', $object_type );
		$cct_slug    = ! empty( $object_type[1] ) ? $object_type[1] : false;

		if ( ! $cct_slug ) {
			return $object;
		}

		$content_type = Module::instance()->manager->get_content_types( $cct_slug );

		if ( ! $content_type ) {
			return $object;
		}

		$flag = \OBJECT;
		$content_type->db->set_format_flag( $flag );

		$item = $content_type->db->get_item( $object_id );

		if ( empty( $item ) ) {
			return $object;
		}

		return $item;
	}

}
