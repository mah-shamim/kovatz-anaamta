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
class Create_Form {

	public $action = 'jet_engine_cct_create_form';

	public function __construct() {

		add_action(
			'jet-engine/custom-content-types/edit-type/custom-actions',
			array( $this, 'action_button' )
		);

		add_action(
			'admin_action_' . $this->action,
			array( $this, 'create_form' )
		);

	}

	public function create_form() {

		if ( empty( $_GET['cct_id'] ) ) {
			wp_die( 'CCT ID not found in the request', 'Error' );
		}

		if ( empty( $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], $this->action ) ) {
			wp_die( 'The link is expired, please return to the previous page and try again', 'Error' );
		}

		$content_type = Module::instance()->manager->get_content_type_by_id( absint( $_GET['cct_id'] ) );

		$fields      = $content_type->fields;
		$form_fields = array();
		$fields_map  = array();
		$index       = 0;

		foreach ( $fields as $i => $field ) {

			$prepared_field = $this->prepare_form_field( $field, $index );

			if ( false !== $prepared_field ) {

				$fields_map[ $field['name'] ] = $field['name'];
				$form_fields[] = $prepared_field;
				$index++;

				if ( 'repeater' === $field['type'] ) {

					foreach ( $field['repeater-fields'] as $child_field ) {
						$prepared_field = $this->prepare_form_field( $child_field, $index );
						$fields_map[ $child_field['name'] ] = $child_field['name'];
						$form_fields[] = $prepared_field;
						$index++;
					}

					$form_fields[] = array(
						'x' => 0,
						'y' => $index,
						'w' => 12,
						'h' => 1,
						'i' => (string) $index,
						'settings' => array(
							'label' => 'repeater_end',
							'name' => 'repeater_end',
							'is_message' => false,
							'is_submit' => false,
							'type' => 'repeater_end',
						),
						'moved' => false,
					);

					$index++;

				}

			}

			$last = $index;
		}

		$form_fields[] = array(
			'x' => 0,
			'y' => $last,
			'w' => 12,
			'h' => 1,
			'i' => (string) $last,
			'settings' => array(
				'label' => 'Submit',
				'name' => 'Submit',
				'is_message' => false,
				'is_submit' => true,
				'type' => 'submit',
				'alignment' => 'right',
				'class_name' => '',
			),
			'moved' => false,
		);

		$notification = array( array(
			'type' => 'insert_custom_content_type',
			'cct'  => array(
				'type' => $content_type->get_arg( 'slug' ),
				'fields_map' => $fields_map,
			),
		) );

		$post_id = wp_insert_post( array(
			'post_title'  => 'Add new ' . $content_type->get_arg( 'name' ) .  ' item',
			'post_type'   => 'jet-engine-booking',
			'post_status' => 'publish',
			'meta_input'  => array(
				'_captcha' => array(
					'enabled' => false,
					'key'     => '',
					'secret'  => '',
				),
				'_preset' => array(
					'enabled'    => false,
					'from'       => 'post',
					'post_from'  => 'current_post',
					'user_from'  => 'current_user',
					'query_var'  => '_post_id',
					'fields_map' => array(),
				),
			),
		) );

		update_post_meta( $post_id, '_form_data', wp_slash( json_encode( $form_fields, JSON_UNESCAPED_UNICODE ) ) );
		update_post_meta( $post_id, '_notifications_data', wp_slash( json_encode( $notification, JSON_UNESCAPED_UNICODE ) ) );

		wp_redirect( get_edit_post_link( $post_id, 'return' ) );
		die();

	}

	public function prepare_form_field( $field, $index ) {

		if ( ! empty( $field['object_type'] ) && 'field' !== $field['object_type'] ) {
			return false;
		}

		if ( in_array( $field['type'], array( 'html' ) ) ) {
			return false;
		}

		$type = $field['type'];
		$field_options = array();
		$required = ! empty( $field['is_required'] ) ? 'required' : '';

		switch ( $field['type'] ) {

			case 'iconpicker':
			case 'colorpicker':
				$type = 'text';
				break;

			case 'switcher':
				$type = 'select';
				$field_options = array(
					array(
						'value' => 'true',
						'label' => __( 'On', 'jet-engine' ),
					),
					array(
						'value' => 'false',
						'label' => __( 'Off', 'jet-engine' ),
					),
				);
				break;

			case 'select':
			case 'radio':
			case 'checkbox':

				foreach ( $field['options'] as $option ) {
					$field_options[] = array(
						'value' => $option['key'],
						'label' => $option['value'],
					);
				}

				break;

		}

		$prepared_field = array(
			'x'        => 0,
			'y'        => $index,
			'w'        => 12,
			'h'        => 1,
			'i'        => (string) $index,
			'settings' => array(
				'name' => $field['name'],
				'label' => $field['title'],
				'desc' => '',
				'required' => $required,
				'type' => $type,
				'hidden_value' => 'post_id',
				'hidden_value_field' => '',
				'field_options_from' => 'manual_input',
				'field_options_key' => '',
				'field_options' => $field_options,
				'calc_formula' => '',
				'precision' =>  2,
				'is_message' =>  false,
				'is_submit' =>  false,
				'default' => isset( $field['default_val'] ) ? $field['default_val'] : '',
			),
			'moved'    => false,
		);

		switch ( $field['type'] ) {

			case 'text':

				if ( isset( $field['max_length'] ) ) {
					$prepared_field['settings']['maxlength'] = $field['max_length'];
				}

				break;

			case 'checkbox':
				$prepared_field['settings']['type'] = 'checkboxes';
				break;

			case 'media':
				$prepared_field['settings']['allowed_user_cap'] = 'all';
				$prepared_field['settings']['insert_attachment'] = true;
				$prepared_field['settings']['max_files'] = 1;
				$prepared_field['settings']['value_format'] = ! empty( $field['value_format'] ) ? $field['value_format'] : 'id';
				break;

			case 'posts':
				$post_type = ! empty( $field['search_post_type'] ) ? $field['search_post_type'][0] : 'post';
				$prepared_field['settings']['type'] = 'select';
				$prepared_field['settings']['field_options_from'] = 'posts';
				$prepared_field['settings']['field_options_post_type'] = $post_type;
				break;

			case 'gallery':
				$prepared_field['settings']['type'] = 'media';
				$prepared_field['settings']['allowed_user_cap'] = 'all';
				$prepared_field['settings']['insert_attachment'] = true;
				$prepared_field['settings']['max_files'] = 10;
				$prepared_field['settings']['value_format'] = ! empty( $field['value_format'] ) ? $field['value_format'] : 'id';
				$prepared_field['settings']['allowed_mimes'] = array( 'image/jpeg', 'image/png' );
				break;

			case 'repeater':
				$prepared_field['settings']['type'] = 'repeater_start';
				$prepared_field['settings']['manage_items_count'] = 'manually';
				break;

		}

		return $prepared_field;
	}

	/**
	 * Action button template
	 *
	 * @return [type] [description]
	 */
	public function action_button() {

		if ( ! function_exists( 'jet_form_builder' ) ) {

			$plugin_slug = 'jetformbuilder';

			$install_url = wp_nonce_url(
				add_query_arg(
					array(
						'action' => 'install-plugin',
						'plugin' => $plugin_slug,
						'from'   => 'admin.php?page=jet-engine',
					),
					self_admin_url( 'update.php' )
				),
				'install-plugin_' . $plugin_slug
			);

			?>
			<div class="cx-vui-subtitle"><?php _e( 'Forms', 'jet-engine' ); ?></div>
			<div v-if="isEdit">
				<div class="cx-vui-text"><?php
				_e( 'You need to install and activate JetFromBuilder plugin to create CCT-related forms', 'jet-engine' );
				?></div>
				<cx-vui-button
					button-style="accent-border"
					custom-css="fullwidth"
					size="mini"
					tag-name="a"
					target="_blank"
					url="<?php echo $install_url; ?>"
				>
					<span slot="label"><?php
						_e( 'Install JetFormBuilder', 'jet-engine' );
					?></span>
				</cx-vui-button>
			</div>
			<div class="cx-vui-text" v-else><?php
				_e( 'After adding CCT you\'ll can automatically generate a new form to fill it from the front-end', 'jet-engine' );
			?></div>
			<div class="cx-vui-hr"></div>
			<?php

			return;

		}

		$jfb_action_url = add_query_arg(
			array(
				'action' => Create_Jfb_Form::$action,
				'_nonce' => wp_create_nonce( Create_Jfb_Form::$action ),
			),
			admin_url( 'admin.php' )
		);
		$je_action_url = add_query_arg(
			array(
				'action' => $this->action,
				'_nonce' => wp_create_nonce( $this->action ),
			),
			admin_url( 'admin.php' )
		);

		?>
		<div class="cx-vui-subtitle"><?php _e( 'Forms', 'jet-engine' ); ?></div>
		<div v-if="isEdit">
			<div class="cx-vui-text"><?php
			_e( 'Here you can create new form for JetFormBuilder, configured to fill this CCT from the front-end', 'jet-engine' );
			?></div>
			<cx-vui-button
				button-style="accent-border"
				custom-css="fullwidth"
				size="mini"
				tag-name="a"
				target="_blank"
				:url="'<?php echo $jfb_action_url ?>&cct_id=' + isEdit"
			>
				<span slot="label"><?php _e( 'Create new form', 'jet-engine' ); ?></span>
			</cx-vui-button>
		</div>
		<div class="cx-vui-text" v-else><?php
			_e( 'After adding CCT you\'ll can automatically generate a new form to fill it from the front-end', 'jet-engine' );
		?></div>
		<div class="cx-vui-hr"></div>
		<?php

	}

}
