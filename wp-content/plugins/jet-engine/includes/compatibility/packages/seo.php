<?php
/**
 * Seo compatibility class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Seo_Package' ) ) {

	/**
	 * Define Jet_Engine_Seo_Package class
	 */
	class Jet_Engine_Seo_Package {

		public $settings_key = 'jet-engine-seo-settings';
		public $settings     = false;
		public $defaults     = array(
			'fields' => array(),
		);

		public function __construct() {

			add_action( 'jet-engine/dashboard/tabs',   array( $this, 'register_settings_tab' ), 99 );
			add_action( 'jet-engine/dashboard/assets', array( $this, 'enqueue_settings_js' ) );

			add_action( 'wp_ajax_jet_engine_seo_save_settings', array( $this, 'save_settings' ) );

			// Enqueue seo analysis script for Rank Math and Yoast.
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			// SEOPress Content Analysis.
			add_filter( 'seopress_content_analysis_content', array( $this, 'seopress_analysis_content' ), 10, 2 );

			// RankMath Snippets
			add_action( 'rank_math/vars/register_extra_replacements', array( $this, 'register_rank_math_cct_field_snippet' ) );

			// Set the primary term the first
			add_filter( 'jet-engine/listings/dynamic-terms/items', array( $this, 'set_primary_term_first' ), 10, 3 );
		}

		/**
		 * Is Rank Math Seo plugin activated.
		 *
		 * @return bool
		 */
		public function is_rank_math_activated() {
			return defined( 'RANK_MATH_VERSION' );
		}

		/**
		 * Is Yoast Seo plugin activated.
		 *
		 * @return bool
		 */
		public function is_yoast_activated() {
			return defined( 'WPSEO_VERSION' );
		}

		/**
		 * Is SEOPress plugin activated.
		 *
		 * @return bool
		 */
		public function is_seopress_activated() {
			return defined( 'SEOPRESS_VERSION' );
		}

		/**
		 * Register settings tab
		 *
		 * @return void
		 */
		public function register_settings_tab() {
			?>
			<cx-vui-tabs-panel
				name="seo_settings"
				label="<?php _e( 'SEO', 'jet-engine' ); ?>"
				key="seo_settings"
			>
				<keep-alive>
					<jet-engine-seo-settings></jet-engine-seo-settings>
				</keep-alive>
			</cx-vui-tabs-panel>
			<?php
		}

		/**
		 * Register settings JS file
		 *
		 * @return void
		 */
		public function enqueue_settings_js() {

			wp_enqueue_script(
				'jet-engine-seo-settings',
				jet_engine()->plugin_url( 'assets/js/admin/seo/settings.js' ),
				array( 'cx-vue-ui' ),
				jet_engine()->get_version(),
				true
			);

			$settings = $this->get_all();

			if ( ! empty( $settings['fields'] ) ) {
				$settings['fields'] = $this->prepared_fields_setting_for_js( $settings['fields'] );
			} else {
				$settings['fields'] = array();
			}

			wp_localize_script(
				'jet-engine-seo-settings',
				'JetEngineSeoSettings',
				array(
					'fields'   => $this->get_available_fields(),
					'settings' => $settings,
					'_nonce'   => wp_create_nonce( $this->settings_key ),
				)
			);

			add_action( 'admin_footer', array( $this, 'print_templates' ) );
		}

		/**
		 * Print Vue template for Seo settings
		 *
		 * @return void
		 */
		public function print_templates() {
			?>
			<script type="text/x-template" id="jet_engine_seo_settings">
				<div>
					<cx-vui-component-wrapper
						label="<?php _e( 'Fields to parse', 'jet-engine' ); ?>"
						description="<?php _e( 'Select meta fields you want to be parsed by SEO plugins ( Rank Math, Yoast or SEOPress )', 'jet-engine' ); ?>"
						:wrapper-css="[ 'vertical-fullwidth' ]"
					>
						<div class="cx-vui-inner-panel">
							<div class="jet-engine-skins-settings-grid">
								<div class="jet-engine-skins-settings-item" v-for="( group, objSlug ) in fields" :key="objSlug">
									<cx-vui-checkbox
										:name="objSlug"
										:label="group.label"
										return-type="array"
										:wrapper-css="[ 'vertical-fullwidth' ]"
										:options-list="group.fields"
										v-model="settings.fields"
									></cx-vui-checkbox>
								</div>
							</div>
						</div>
					</cx-vui-component-wrapper>
					<cx-vui-component-wrapper
						:wrapper-css="[ 'vertical-fullwidth', 'jet-is-stackable' ]"
					>
						<cx-vui-button
							button-style="accent"
							:loading="saving"
							@click="saveSettings"
						>
							<span
								slot="label"
								v-html="'<?php _e( 'Save', 'jet-engine' ); ?>'"
							></span>
						</cx-vui-button>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<span
							class="cx-vui-inline-notice cx-vui-inline-notice--success"
							v-if="'success' === result"
							v-html="successMessage"
						></span>
						<span
							class="cx-vui-inline-notice cx-vui-inline-notice--error"
							v-if="'error' === result"
							v-html="errorMessage"
						></span>
					</cx-vui-component-wrapper>
				</div>
			</script>
			<?php
		}

		public function get_available_fields() {

			$fields = jet_engine()->meta_boxes->get_registered_fields();
			$result = array();

			if ( empty( $fields ) ) {
				return $result;
			}

			$post_types = get_post_types( array(), 'objects' );
			//$taxonomies = get_taxonomies( array(), 'objects' );

			$available_field_types = apply_filters( 'jet-engine/compatibility/seo/available-field-types', array(
				'text',
				'textarea',
				'wysiwyg',
				'repeater',
			) );

			foreach ( $fields as $object => $obj_fields ) {

				$group_label = false;

				if ( isset( $post_types[ $object ] ) ) {
					$group_label = $post_types[ $object ]->labels->name;
				}

				//if ( isset( $taxonomies[ $object ] ) ) {
				//	$group_label = $taxonomies[ $object ]->labels->name;
				//}

				if ( ! $group_label ) {
					continue;
				}

				$group = array();

				foreach ( $obj_fields as $field_data ) {

					if ( ! empty( $field_data['object_type'] ) && 'field' !== $field_data['object_type'] ) {
						continue;
					}

					if ( ! in_array( $field_data['type'], $available_field_types ) ) {
						continue;
					}

					$name  = $object . '::' . $field_data['name'];
					$title = ! empty( $field_data['title'] ) ? $field_data['title'] : $name;

					if ( 'repeater' === $field_data['type'] ) {

						if ( empty( $field_data['repeater-fields'] ) ) {
							continue;
						}

						foreach ( $field_data['repeater-fields'] as $repeater_field ) {

							if ( ! in_array( $repeater_field['type'], $available_field_types ) ) {
								continue;
							}

							$r_name  = $repeater_field['name'];
							$r_title = ! empty( $repeater_field['title'] ) ? $repeater_field['title'] : $name;

							$group[] = array(
								'label' => $title . ': ' . $r_title,
								'value' => $name . '[' . $r_name . ']',
							);

						}

					} else {
						$group[] = array(
							'label' => $title,
							'value' => $name
						);
					}
				}

				if ( ! empty( $group ) ) {
					$result[ $object ] = array(
						'label'  => $group_label,
						'fields' => $group,
					);
				}
			}

			return $result;
		}

		/**
		 * Ajax callback to save settings
		 *
		 * @return void
		 */
		public function save_settings() {

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'Access denied', 'jet-engine' ) ) );
			}

			$nonce = ! empty( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;

			if ( ! $nonce || ! wp_verify_nonce( $nonce, $this->settings_key ) ) {
				wp_send_json_error( array( 'message' => __( 'Nonce validation failed', 'jet-engine' ) ) );
			}

			$settings = ! empty( $_REQUEST['settings'] ) ? $_REQUEST['settings'] : array();

			if ( ! empty( $settings['fields'] ) ) {
				$settings['fields'] = $this->prepared_fields_setting_for_db( $settings['fields'] );
			}

			update_option( $this->settings_key, $settings, false );

			wp_send_json_success( array( 'message' => __( 'Settings saved', 'jet-engine' ) ) );
		}

		/**
		 * Returns all settings
		 *
		 * @return array
		 */
		public function get_all() {

			if ( false === $this->settings ) {
				$this->settings = get_option( $this->settings_key, $this->defaults );
			}

			return $this->settings;
		}

		/**
		 * Returns specific setting
		 *
		 * @param  string $setting
		 * @return mixed
		 */
		public function get( $setting ) {

			$settings = $this->get_all();

			if ( isset( $settings[ $setting ] ) ) {
				return $settings[ $setting ];
			} elseif ( isset( $this->defaults[ $setting ] ) ) {
				return $this->defaults[ $setting ];
			} else {
				return false;
			}
		}

		public function admin_enqueue_scripts( $hook ) {

			if ( isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) {
				return;
			}

			if ( ! $this->is_rank_math_activated() && ! $this->is_yoast_activated() ) {
				return;
			}

			$obj = false;

			if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
				$obj = get_post_type();
			}

			//if ( in_array( $hook, array( 'term.php', 'edit-tags.php' ) ) ) {
			//	$obj = isset( $_GET['taxonomy'] ) ? $_GET['taxonomy'] : false;
			//}

			if ( ! $obj ) {
				return;
			}

			$all_fields = $this->get( 'fields' );
			$current_obj_fields = ! empty( $all_fields[ $obj ] ) ? $all_fields[ $obj ] : false;

			if ( empty( $current_obj_fields ) ) {
				return;
			}

			$script_depends = array( 'jquery' );

			if ( $this->is_rank_math_activated() ) {
				$script_depends[] = 'wp-hooks';
				$script_depends[] = 'rank-math-analyzer';
			}

			wp_enqueue_script(
				'jet-engine-seo-analysis',
				jet_engine()->plugin_url( 'assets/js/admin/seo/seo-analysis.js' ),
				$script_depends,
				jet_engine()->get_version(),
				true
			);

			wp_localize_script(
				'jet-engine-seo-analysis',
				'JetEngineSeoConfig',
				array(
					'isRankMathActived' => $this->is_rank_math_activated(),
					'isYoastActived'    => $this->is_yoast_activated(),
					'fields'            => $current_obj_fields,
				)
			);

		}

		public function prepared_fields_setting_for_db( $fields = array() ) {
			$prepared_fields = array();

			foreach ( $fields as $field ) {
				$field_data = explode( '::', $field );
				$field_obj  = $field_data[0];
				$field_name = $field_data[1];

				$prepared_fields[ $field_obj ][] = $field_name;
			}

			return $prepared_fields;
		}

		public function prepared_fields_setting_for_js( $fields = array() ) {
			$prepared_fields = array();

			foreach ( $fields as $obj => $obj_fields ) {

				if ( empty( $obj_fields ) ) {
					continue;
				}

				foreach ( $obj_fields as $field ) {
					$prepared_fields[] = $obj . '::' . $field;
				}
			}

			return $prepared_fields;
		}

		public function seopress_analysis_content( $content, $id ) {

			$post_type = get_post_type( $id );

			if ( ! $post_type ) {
				return $content;
			}

			$all_fields = $this->get( 'fields' );
			$current_obj_fields = ! empty( $all_fields[ $post_type ] ) ? $all_fields[ $post_type ] : false;

			if ( empty( $current_obj_fields ) ) {
				return $content;
			}

			$fields_content = '';

			foreach ( $current_obj_fields as $field_key ) {

				$is_repeater = ( false !== strpos( $field_key, '[' ) && false !== strpos( $field_key, ']' ) );

				if ( $is_repeater ) {

					preg_match( '/(.+)\[(.+)\]/', $field_key, $matches );

					$repeater_key       = ! empty( $matches[1] ) ? $matches[1] : false;
					$repeater_field_key = ! empty( $matches[2] ) ? $matches[2] : false;

					if ( ! $repeater_key || ! $repeater_field_key ) {
						continue;
					}

					$repeater_items = get_post_meta( $id, $repeater_key, true );

					if ( empty( $repeater_items ) ) {
						continue;
					}

					foreach ( $repeater_items as $repeater_item ) {

						if ( ! empty( $repeater_item[ $repeater_field_key ] ) ) {
							$fields_content .= "\n" . $repeater_item[ $repeater_field_key ];
						}

					}

				} else {
					$field_content = get_post_meta( $id, $field_key, true );

					if ( ! empty( $field_content ) ) {
						$fields_content .= "\n" . $field_content;
					}
				}
			}

			if ( ! empty( $fields_content ) ) {
				$content = $content . strip_tags( $fields_content );
			}

			return $content;
		}

		public function register_rank_math_cct_field_snippet() {

			if ( ! class_exists( '\Jet_Engine\Modules\Custom_Content_Types\Module' ) ) {
				return;
			}

			if ( ! function_exists( 'rank_math_register_var_replacement' ) ) {
				return;
			}

			rank_math_register_var_replacement(
				'jet_cct_field',
				array(
					'name'        => esc_html__( 'Custom Content Type Field', 'jet-engine' ),
					'description' => esc_html__( 'Custom Content Type Field value. Separate cct slug and field name with ::', 'jet-engine' ),
					'variable'    => 'jet_cct_field(cct-slug::field-name)',
					'example'     => esc_html__( 'Custom Content Type Field value', 'jet-engine' ),
				),
				array( $this, 'get_cct_field_value' )
			);
		}

		public function get_cct_field_value( $field ) {

			if ( empty( $field ) ) {
				return null;
			}

			$field = str_replace( '::', '__', $field );
			$value = jet_engine()->listings->data->get_prop( $field );

			if ( is_array( $value ) ) {
				return jet_engine_render_checkbox_values( $value );
			}

			return wp_kses_post( $value );
		}

		public function set_primary_term_first( $terms, $post_id, $settings ) {

			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				return $terms;
			}

			$primary_term_id = null;
			$tax = ! empty( $settings['from_tax'] ) ? $settings['from_tax'] : false;

			if ( ! $tax ) {
				return $terms;
			}

			if ( $this->is_rank_math_activated() ) {

				$post_type = get_post_type( $post_id );

				if ( ! $post_type ) {
					return $terms;
				}

				$titles_options  = get_option( 'rank-math-options-titles' );
				$primary_tax_key = 'pt_' . $post_type . '_primary_taxonomy';
				$primary_tax     = ! empty( $titles_options[ $primary_tax_key ] ) ? $titles_options[ $primary_tax_key ] : false;

				if ( ! $primary_tax || 'off' === $primary_tax || $primary_tax !== $tax ) {
					return $terms;
				}

				$primary_term_id = get_post_meta( $post_id, 'rank_math_primary_' . $primary_tax, true );

			} else if ( $this->is_yoast_activated() ) {

				$primary_term_helper = new \Yoast\WP\SEO\Helpers\Primary_Term_Helper();
				$primary_taxonomies  = $primary_term_helper->get_primary_term_taxonomies( $post_id );

				if ( empty( $primary_taxonomies ) || ! in_array( $tax, array_keys( $primary_taxonomies ) ) ) {
					return $terms;
				}

				$primary_term_id = get_post_meta( $post_id, '_yoast_wpseo_primary_' . $tax, true );
			}

			if ( empty( $primary_term_id ) ) {
				return $terms;
			}

			$terms_ids  = wp_list_pluck( $terms, 'term_id' );
			$find_index = array_search( $primary_term_id, $terms_ids );

			if ( false === $find_index ) {
				return $terms;
			}

			$primary_term = $terms[ $find_index ];

			unset( $terms[ $find_index ] );
			array_unshift( $terms, $primary_term );

			return $terms;
		}

	}

}

new Jet_Engine_Seo_Package();
