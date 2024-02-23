<?php
/**
 * Plugin admin settings class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Shop_Settings' ) ) {

	/**
	 * Define Jet_Woo_Builder_Shop_Settings class
	 */
	class Jet_Woo_Builder_Shop_Settings extends Jet_Woo_Builder_Settings {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		public $options_key = 'jet_woo_builder';

		/**
		 * Constructor for the class
		 */
		public function init() {

			add_filter( 'woocommerce_get_settings_pages', array( $this, 'register_woo_settings_page' ) );

			add_action( 'woocommerce_admin_field_jet_woo_select_template', array( $this, 'select_template_field' ) );
			add_action( 'woocommerce_admin_field_jet_woo_select_render_method_field', array( $this, 'select_render_method_field' ) );

		}

		/**
		 * Get shop setting by name
		 *
		 * @param       $name
		 * @param mixed $default
		 *
		 * @return mixed
		 */
		public function get( $name = '', $default = false ) {

			if ( empty( $this->settings ) ) {
				$this->settings = get_option( $this->options_key, array() );
			}

			return isset( $this->settings[ $name ] ) ? $this->settings[ $name ] : $default;

		}

		/**
		 * Select template field
		 *
		 * @param $value
		 *
		 * @return void
		 */
		public function select_template_field( $value ) {

			$doc_type       = $value['doc_type'] ?? 'single';
			$templates      = jet_woo_builder_post_type()->get_templates_list( $doc_type );
			$options        = get_option( $this->options_key );
			$current_option = str_replace( array( $this->options_key, '[', ']' ), '', $value['id'] );
			?>

			<tr valign="top" class="single_select_page">
				<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ); ?></th>
				<td class="forminp">
					<select style="<?php echo $value['css']; ?>" id="<?php echo $value['id'] ?>"
						name="<?php echo $value['id'] ?>" class="<?php echo $value['class']; ?>"
						data-placeholder="<?php esc_attr_e( 'Select a template&hellip;', 'jet-woo-builder' ); ?>">
						<option value="default"><?php echo __( 'Default', 'jet-woo-builder' ) ?></option>
						<?php foreach ( $templates as $template ) {
						printf( '<option value="%1$s" ' . selected( $options[ $current_option ], $template->ID, true ) . '>%2$s</option>', $template->ID, $template->post_title );
						} ?>
					</select><?php printf( '<br><span class="description">%s</span>', $value['desc'] ); ?>
				</td>
			</tr>

			<?php
		}

		/**
		 * Select archive widgets render method field
		 *
		 * @param $value
		 *
		 * @return void
		 */
		public function select_render_method_field( $value ) {

			$options        = get_option( $this->options_key );
			$current_option = str_replace( array( $this->options_key, '[', ']' ), '', $value['id'] );
			$default        = $value['default'] ?? '';
			$option_val     = $options[ $current_option ] ?? $default;
			?>

			<tr valign="top" class="render_method">
				<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ); ?></th>
				<td class="forminp">
					<select style="<?php echo $value['css']; ?>" id="<?php echo $value['id'] ?>"
						name="<?php echo $value['id'] ?>" class="<?php echo $value['class']; ?>"
						data-placeholder="<?php esc_attr_e( 'Select render method', 'jet-woo-builder' ); ?>">
						<option value="macros" <?php selected( $option_val, 'macros', true ) ?> ><?php echo __( 'Macros', 'jet-woo-builder' ) ?></option>
						<option value="elementor"<?php selected( $option_val, 'elementor', true ) ?>><?php echo __( 'Elementor Default', 'jet-woo-builder' ) ?></option>
					</select>
					<?php printf( '<br><span class="description">%s</span>', $value['desc'] ); ?>
				</td>
			</tr>

			<?php
		}

		/**
		 * Register WooCommerce settings page
		 *
		 * @param $settings
		 *
		 * @return array
		 */
		public function register_woo_settings_page( $settings ) {

			require jet_woo_builder()->plugin_path( 'includes/settings/class-jet-woo-builder-shop-settings-page.php' );

			$settings[ $this->key ] = new Jet_Woo_Builder_Shop_Settings_Page();

			return $settings;

		}

		/**
		 * Returns the instance.
		 *
		 * @return object
		 * @since  1.0.0
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;

		}
	}

}

/**
 * Returns instance of Jet_Woo_Builder_Shop_Settings
 *
 * @return object
 */
function jet_woo_builder_shop_settings() {
	return Jet_Woo_Builder_Shop_Settings::get_instance();
}
