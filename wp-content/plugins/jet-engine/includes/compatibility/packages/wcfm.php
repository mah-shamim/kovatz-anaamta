<?php
/**
 * WCFM compatibility package
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_WCFM_Package' ) ) {

	/**
	 * Define Jet_Engine_WCFM_Package class
	 */
	class Jet_Engine_WCFM_Package {

		public static $index = 0;

		public function __construct() {
			add_filter( 'jet-engine/meta-boxes/sources', array( $this, 'add_source' ) );
			add_action( 'jet-engine/meta-boxes/source-custom-controls', array( $this, 'add_controls' ) );
			add_action( 'jet-engine/meta-boxes/register-custom-source/wcfm', array( $this, 'register_tab' ), 10, 2 );
			add_action( 'wcfm_vendor_settings_update', array( $this, 'update_settings' ), 150, 2 );
			add_action( 'jet-engine/listings/data/queried-user', array( $this, 'set_vendor_as_queried_user' ) );
		}

		/**
		 * Set current vendoe as current queried user
		 *
		 * @param [type] $queried_user [description]
		 */
		public function set_vendor_as_queried_user( $queried_user ) {

			$wcfm_store_url = wcfm_get_option( 'wcfm_store_url', 'store' );
			$store_name = apply_filters( 'wcfmmp_store_query_var', get_query_var( $wcfm_store_url ) );

			if ( ! empty( $store_name ) ) {

				$store_user = get_user_by( 'slug', $store_name );

				if ( $store_user ) {
					$queried_user = $store_user;
				}
			}

			return $queried_user;

		}

		/**
		 * Update settings on ajax
		 *
		 * @return [type] [description]
		 */
		public function update_settings( $user_id = null, $wcfm_settings_form = array() ) {

			if ( empty( $wcfm_settings_form['jet_wcfm'] ) ) {
				return;
			}

			foreach ( $wcfm_settings_form['jet_wcfm'] as $meta_key => $meta_value ) {
				update_user_meta( $user_id, $meta_key, $meta_value );
			}

		}

		/**
		 * Register WCFM settings page tabs
		 *
		 * @return [type] [description]
		 */
		public function register_tab( $data ) {

			$args   = $data['args'];
			$fields = $data['meta_fields'];
			$hook   = ! empty( $args['wcfm_position'] ) ? $args['wcfm_position'] : 'end_wcfm_marketplace_settings';

			$name        = ! empty( $args['name'] ) ? $args['name'] : 'jet-engine-meta';
			$object_name = 'WCFM: ' . $name;
			jet_engine()->meta_boxes->store_fields( $object_name, $fields );

			add_action( $hook, function( $user_id ) use ( $args, $fields ) {

				global $WCFM, $WCFMmp;

				self::$index++;

				$name = ! empty( $args['name'] ) ? $args['name'] : 'jet-engine-meta';
				$id   = sanitize_key( $name . ' ' . self::$index );
				$icon = ! empty( $args['wcfm_icon'] ) ? $args['wcfm_icon'] : 'fa-cogs';

				?>
				<div class="page_collapsible" id="<?php echo $id; ?>">
					<label class="wcfmfa <?php echo $icon; ?>"></label>
					<?php echo $name; ?><span></span>
				</div>
				<div class="wcfm-container wcfm_marketplace_store_settings">
					<div class="wcfm-content"><?php

						$parsed_fields = array();

						if ( ! class_exists( 'Jet_Engine_CPT_Meta' ) ) {
							require jet_engine()->meta_boxes->component_path( 'post.php' );
						}

						$meta_box = new Jet_Engine_CPT_Meta();

						foreach ( $fields as $field ) {

							$args= array(
								'label'       => $field['title'],
								'placeholder' => ! empty( $field['placeholder'] ) ? $field['placeholder'] : '',
								'id'          => 'jet_wcfm_' . $field['name'],
								'name'        => 'jet_wcfm[' . $field['name'] . ']',
								'type'        => $field['type'],
								'class'       => 'wcfm-' . $field['type'] . ' wcfm_ele',
								'label_class' => 'wcfm_title wcfm_ele',
								'value'       => get_user_meta( $user_id, $field['name'], true ),
								'attributes'  => array(),
							);

							if ( ! empty( $field['description'] ) ) {
								$args['hints'] = $field['description'];
							}

							switch ( $field['type'] ) {
								case 'select':
									if ( empty( $field['options'] ) ) {
										$args['options'] = array();
									}

									$prepared_options = $meta_box->prepare_select_options( $field );

									$args['options'] = $prepared_options['options'];

									$multiple = ! empty( $field['is_multiple'] ) ? $field['is_multiple'] : false;
									$multiple = filter_var( $multiple, FILTER_VALIDATE_BOOLEAN );

									if ( $multiple ) {
										$args['attributes']['multiple'] = true;
									}

									break;

								case 'colorpicker':

									$args['class'] = 'wcfm-text wcfm_ele colorpicker';

									$WCFM->library->load_colorpicker_lib();

									wp_enqueue_script(
										'iris',
										admin_url( 'js/iris.min.js' ),
										array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
										false,
										1
									);

									wp_enqueue_script(
										'wp-color-picker',
										admin_url('js/color-picker.min.js'),
										array( 'iris' ),
										false,
										1
									);

									$colorpicker_l10n = array(
										'clear' => __('Clear'),
										'defaultString' => __('Default'),
										'pick' => __( 'Select Color')
									);

									wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );

									break;
							}

							$parsed_fields[ $field['name'] ] = $args;

						}

						$WCFM->wcfm_fields->wcfm_generate_form_field( $parsed_fields );

					?></div>
				</div>
				<div class="wcfm_clearfix"></div>
				<?php

			} );

		}

		/**
		 * Add WCFM-specific controls
		 */
		public function add_controls() {
			?>
			<cx-vui-component-wrapper
				:conditions="[
					{
						input: this.generalSettings.object_type,
						compare: 'equal',
						value: 'wcfm',
					}
				]"
				label='<?php _e( 'Note:', 'jet-engine' ); ?>'
				description='<?php
					_e( 'This option will add created field as new tab into WCFM marketplace front-end settings. Fields will be added using WCFM UI, thats why some restrictions are exists: <ul><li>- WCFM supports only Field Object Type</li><li>- Repeater, Switcher, Iconpicker, Gallery and Posts fields are not supported</li></ul>', 'jet-engine' );
				?>'
			></cx-vui-component-wrapper>
			</cx-vui-repeater-item>
			<cx-vui-select
				:conditions="[
					{
						input: this.generalSettings.object_type,
						compare: 'equal',
						value: 'wcfm',
					}
				]"
				:label="'<?php _e( 'Custom tab position', 'jet-engine' ); ?>'"
				:description="'<?php _e( 'Select where you want to add custom marketplace settings tab', 'jet-engine' ); ?>'"
				:options-list="[
					{
						value: 'wcfm_vendor_settings_after_location',
						label: '<?php _e( 'After location', 'jet-engine' ); ?>',
					},
					{
						value: 'wcfm_vendor_settings_after_payment',
						label: '<?php _e( 'After payment', 'jet-engine' ); ?>',
					},
					{
						value: 'wcfm_vendor_settings_after_shipping',
						label: '<?php _e( 'After shipping', 'jet-engine' ); ?>',
					},
					{
						value: 'wcfm_vendor_settings_after_seo',
						label: '<?php _e( 'After SEO', 'jet-engine' ); ?>',
					},
					{
						value: 'wcfm_vendor_settings_after_customer_support',
						label: '<?php _e( 'After customer support', 'jet-engine' ); ?>',
					},
					{
						value: 'end_wcfm_marketplace_settings',
						label: '<?php _e( 'After all', 'jet-engine' ); ?>',
					},
				]"
				:wrapper-css="[ 'equalwidth' ]"
				:size="'fullwidth'"
				v-model="generalSettings.wcfm_position"
			></cx-vui-select>
			<?php
		}

		/**
		 * Add WCFM source
		 */
		public function add_source( $sources ) {

			$sources[] = array(
				'value' => 'wcfm',
				'label' => 'WCFM - WooCommerce Multivendor Marketplace',
			);

			return $sources;

		}

	}

}

new Jet_Engine_WCFM_Package();
