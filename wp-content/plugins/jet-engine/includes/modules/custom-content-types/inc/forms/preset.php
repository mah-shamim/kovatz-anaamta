<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Forms;

use Jet_Engine\Modules\Custom_Content_Types\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Notification class
 */
class Preset {

	public $preset_source = 'custom_content_type';

	public function __construct() {

		add_filter( 'jet-engine/forms/preset-sources', array( $this, 'register_source' ) );
		add_filter( 'jet-engine/forms/preset-value/' . $this->preset_source, array( $this, 'apply_preset' ), 10, 4 );
		add_filter( 'jet-engine/forms/preset-source/' . $this->preset_source, array( $this, 'apply_source' ), 10, 2 );
		add_filter( 'jet-engine/forms/preset/sanitize-source', array( $this, 'sanitize_source' ), 10, 2 );

		add_action( 'jet-engine/forms/preset-editor/custom-controls-source', array( $this, 'preset_controls_source' ) );
		add_action( 'jet-engine/forms/preset-editor/custom-controls-global', array( $this, 'preset_controls_global' ) );
		add_action( 'jet-engine/forms/preset-editor/custom-controls-field', array( $this, 'preset_controls_field' ) );
	}

	/**
	 * Sanitize preset source
	 *
	 * @param  [type] $res    [description]
	 * @param  [type] $preset [description]
	 * @return [type]         [description]
	 */
	public function sanitize_source( $res, $preset ) {

		$data = $preset->get_data();

		if ( empty( $data['enabled'] ) || empty( $data['from'] ) || 'custom_content_type' !== $data['from'] ) {
			return $res;
		}

		$source = $preset->get_source( $data );

		if ( empty( $source ) || empty( $source['cct_author_id'] ) ) {
			return $res;
		}

		if ( ! is_user_logged_in() ) {
			return false;
		}

		$cct = Module::instance()->manager->get_content_types( $source['cct_slug'] );

		if ( ! $cct ) {
			return false;
		}

		if ( $cct->user_has_access() ) {
			return $res;
		}

		$author = absint( $source['cct_author_id'] );

		return $author === get_current_user_id();

	}

	/**
	 * Apply CCT sourece for the preset
	 *
	 * @param  [type] $source [description]
	 * @param  [type] $data   [description]
	 * @return [type]         [description]
	 */
	public function apply_source( $source, $data ) {

		$cct_slug = false;

		if ( ! empty( $data['fields_map'] ) ) {

			foreach ( (array) $data['fields_map'] as $field_data ) {

				if ( empty( $field_data['key'] ) ) {
					continue;
				}

				$key = explode( '::', $field_data['key'] );

				if ( 2 !== count( $key ) ) {
					continue;
				}

				$cct_slug = $key[0];

				break;
			}
		}

		if ( ! $cct_slug ) {
			return $source;
		}

		$item = $this->get_content_type_item( $cct_slug, $data );

		if ( ! $item ) {
			return $source;
		}

		if ( is_object( $item ) ) {
			$item = get_object_vars( $item );
		}

		return $item;
	}

	public function apply_preset( $value, $field_data, $args, $source ) {

		$key = ! empty( $field_data['key'] ) ? $field_data['key'] : false;

		if ( ! $key ) {
			return $value;
		}

		$key = explode( '::', $key );

		if ( 2 !== count( $key ) ) {
			return $value;
		}

		$field = $key[1];
		$item  = ! empty( $source ) ? $source : $this->get_content_type_item( $key[0], $args );

		if ( ! $item ) {
			return $value;
		} else {
			if ( is_array( $item ) ) {
				return isset( $item[ $field ] ) ? $item[ $field ] : $value;
			} elseif ( is_object( $item ) ) {
				return isset( $item->$field ) ? $item->$field : $value;
			} else {
				return $value;
			}

		}

	}

	public function get_content_type_item( $slug, $args ) {

		$content_type = Module::instance()->manager->get_content_types( $slug );

		$item      = false;
		$from      = ! empty( $args['post_from'] ) ? $args['post_from'] : 'current_post';
		$query_var = ! empty( $args['query_var'] ) ? $args['query_var'] : false;

		if ( 'current_post' === $from ) {

			$item = $content_type->db->get_queried_item();

			if ( ! $item ) {
				$post_id = get_the_ID();
				if ( $post_id ) {
					$item = Module::instance()->manager->get_item_for_post( $post_id, $content_type );
				}

			}

		} else {

			$item_id = ! empty( $_REQUEST[ $query_var ] ) ? $_REQUEST[ $query_var ] : false;

			if ( $item_id ) {
				$item = $content_type->db->get_item( $item_id );
			}

		}

		return $item;
	}

	public function register_source( $sources ) {

		$sources[] = array(
			'value' => $this->preset_source,
			'label' => __( 'Custom Content Type', 'jet-engine' ),
		);

		return $sources;

	}

	public function preset_controls_source() {
		?>
		<div class="jet-form-canvas__preset-row" v-if="'<?php echo $this->preset_source; ?>' === preset.from">
			<span><?php _e( 'Get item ID from:', 'jet-engine' ); ?></span>
			<select type="text" name="_preset[post_from]" v-model="preset.post_from">
				<option value="current_post"><?php _e( 'Current post', 'jet-engine' ); ?></option>
				<option value="query_var"><?php _e( 'URL Query Variable', 'jet-engine' ); ?></option>
			</select>
		</div>
		<div class="jet-form-canvas__preset-row" v-if="'<?php echo $this->preset_source; ?>' === preset.from && 'query_var' === preset.post_from">
			<span><?php _e( 'Query variable name:', 'jet-engine' ); ?></span>
			<input type="text" name="_preset[query_var]" v-model="preset.query_var">
		</div>
		<?php
	}

	public function preset_controls_global() {
		$this->preset_controls( "_preset[fields_map][' + field + '][key]", "preset.fields_map[ field ].key" );
	}

	public function preset_controls_field() {
		$this->preset_controls( "current_field_key", "preset.current_field_key" );
	}

	public function preset_controls( $name, $model ) {
		?>
		<div class="jet-post-field-control__inner" v-if="'<?php echo $this->preset_source; ?>' === preset.from">
			<select :name="'<?php echo $name; ?>'" v-model="<?php echo $model ?>">
				<option value=""><?php _e( 'Select custom content type field...', 'jet-engine' ); ?></option>
				<?php
					foreach ( Module::instance()->manager->get_content_types() as $type => $instance ) {

						$fields = $instance->get_fields_list( 'all' );
						$prefixed_fields = array();

						if ( empty( $fields ) ) {
							continue;
						}

						echo '<optgroup label="' . $instance->get_arg( 'name' ) . '">';

						foreach ( $fields as $key => $label ) {
							printf( '<option value="%1$s">%2$s</option>', $type . '::' . $key, $label );
						}

						echo '</optgroup>';

					}
				?>
			</select>
		</div>
		<?php
	}

}
