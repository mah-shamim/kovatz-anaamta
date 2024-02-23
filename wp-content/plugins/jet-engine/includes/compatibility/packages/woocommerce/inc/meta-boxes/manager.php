<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Meta_Boxes;

use Jet_Engine\Compatibility\Packages\Jet_Engine_Woo_Package\Package;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * Has styles.
	 *
	 * Holds status of styles enqueue.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @var bool
	 */
	public $has_styles = false;

	/**
	 * Has inline script.
	 *
	 * Holds status of inline script.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @var bool
	 */
	public $has_inline_script = false;

	/**
	 * Instance.
	 *
	 * A reference to an instance of this class.
	 *
	 * @since  3.2.0
	 * @access private
	 *
	 * @var    object
	 */
	public static $instance = null;

	public function __construct() {

		add_filter( 'jet-engine/meta-boxes/sources', [ $this, 'add_sources' ] );

		add_action( 'jet-engine/meta-boxes/enqueue-assets', [ $this, 'add_editor_js' ] );
		add_action( 'jet-engine/meta-boxes/condition-controls', [ $this, 'render_display_controls' ] );
		add_action( 'jet-engine/meta-boxes/register-custom-source/woocommerce_product_data', [ $this, 'register_product_panel_meta_box' ] );
		add_action( 'jet-engine/meta-boxes/register-custom-source/woocommerce_product_variation', [ $this, 'register_product_variation_meta_box' ] );

	}

	/**
	 * Add Meta boxes editor JS file
	 */
	public function add_editor_js() {
		wp_enqueue_script(
			'jet-engine-wc-meta-boxes',
			Package::instance()->package_url( 'assets/js/admin/meta-boxes.js' ),
			array( 'jet-plugins' ),
			jet_engine()->get_version(),
			true
		);
	}

	/**
	 * Add source.
	 *
	 * Return extended list of sources for meta box.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @param array $sources List of sources.
	 *
	 * @return mixed
	 */
	public function add_sources( $sources ) {

		$sources[] = [
			'value' => 'woocommerce_product_data',
			'label' => __( 'WooCommerce Product Data', 'jet-engine' ),
		];

		$sources[] = [
			'value' => 'woocommerce_product_variation',
			'label' => __( 'WooCommerce Product Variation', 'jet-engine' ),
		];

		return $sources;

	}

	/**
	 * Register product panel meta box.
	 *
	 * Register meta box for product panel.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @param array $meta_box List of meta box settings.
	 *
	 * @return void
	 */
	public function register_product_panel_meta_box( $meta_box ) {

		if ( ! class_exists( 'Product_Data_Panel' ) ) {
			require_once jet_engine()->plugin_path( 'includes/compatibility/packages/woocommerce/inc/meta-boxes/product-data-panel.php' );
		}

		new Product_Data_Panel( $meta_box );

		$this->enqueue_custom_styles();

	}

	/**
	 * Register product variation meta box.
	 *
	 * Register meta box for product variation panel.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @param array $meta_box List of meta box settings.
	 *
	 * @return void
	 */
	public function register_product_variation_meta_box( $meta_box ) {

		if ( ! class_exists( 'Product_Variation_Panel' ) ) {
			require_once jet_engine()->plugin_path( 'includes/compatibility/packages/woocommerce/inc/meta-boxes/product-variation-panel.php' );
		}

		new Product_Variation_Panel( $meta_box );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_inline_script' ], 20 );

		$this->enqueue_custom_styles();

	}

	/**
	 * Render display controls.
	 *
	 * Display additional meta box controls for WooCommerce related sources.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @return void
	 */
	public function render_display_controls() {
		?>
		<cx-vui-select
			:label="'<?php _e( 'Product Data', 'jet-engine' ); ?>'"
			:description="'<?php _e( 'Select product data option panel where to display meta box.', 'jet-engine' ); ?>'"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			value="custom"
			:options-list="[
				{
					value: 'custom',
					label: '<?php _e( 'Custom', 'jet-engine' ) ?>'
				},
				{
					value: 'general_product_data',
					label: '<?php _e( 'General', 'jet-engine' ) ?>'
				},
				{
					value: 'inventory_product_data',
					label: '<?php _e( 'Inventory', 'jet-engine' ) ?>'
				},
				{
					value: 'shipping_product_data',
					label: '<?php _e( 'Shipping', 'jet-engine' ) ?>'
				},
				{
					value: 'related',
					label: '<?php _e( 'Linked Products', 'jet-engine' ) ?>'
				},
				{
					value: 'advanced',
					label: '<?php _e( 'Advanced', 'jet-engine' ) ?>'
				}
			]"
			:conditions="[
				{
					input: this.generalSettings.object_type,
					compare: 'equal',
					value: 'woocommerce_product_data',
				}
			]"
			v-model="generalSettings.wc_product_data_panel"
		></cx-vui-select>

		<cx-vui-select
			:label="'<?php _e( 'Exclude/Include', 'jet-engine' ); ?>'"
			:description="'<?php _e( 'Select condition for product types.', 'jet-engine' ); ?>'"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			:options-list="[
				{
					value: 'none',
					label: '<?php _e( 'None', 'jet-engine' ) ?>'
				},
				{
					value: 'hide_if',
					label: '<?php _e( 'Exclude', 'jet-engine' ) ?>'
				},
				{
					value: 'show_if',
					label: '<?php _e( 'Include', 'jet-engine' ) ?>'
				}
			]"
			:conditions="[
				{
					input: this.generalSettings.object_type,
					compare: 'equal',
					value: 'woocommerce_product_data',
				},
				{
					input: this.generalSettings.wc_product_data_panel,
					compare: 'equal',
					value: 'custom',
				}
			]"
			v-model="generalSettings.wc_product_data_exclude_include"
		></cx-vui-select>

		<cx-vui-f-select
			:label="'<?php _e( 'Exclude or Include Product Types', 'jet-engine' ); ?>'"
			:description="'<?php _e( 'Select product types where this meta box should be hidden or shown.', 'jet-engine' ); ?>'"
			:wrapper-css="[ 'equalwidth' ]"
			:options-list="[
				{
					value: 'simple',
					label: '<?php _e( 'Simple Product', 'jet-engine' ) ?>'
				},
				{
					value: 'variable',
					label: '<?php _e( 'Variable Product', 'jet-engine' ) ?>'
				},
				{
					value: 'grouped',
					label: '<?php _e( 'Grouped Product', 'jet-engine' ) ?>'
				},
				{
					value: 'external',
					label: '<?php _e( 'External/Affiliate Product', 'jet-engine' ) ?>'
				},
			]"
			:size="'fullwidth'"
			:multiple="true"
			:conditions="[
				{
					input: this.generalSettings.object_type,
					compare: 'equal',
					value: 'woocommerce_product_data',
				},
				{
					input: this.generalSettings.wc_product_data_panel,
					compare: 'equal',
					value: 'custom',
				},
				{
					'input':   this.generalSettings.wc_product_data_exclude_include,
					'compare': 'in',
					'value':   [ 'hide_if', 'show_if' ],
				}
			]"
			v-model="generalSettings.wc_product_data_product_types"
		></cx-vui-f-select>

		<cx-vui-input
			label="<?php _e( 'Tab Priority', 'jet-engine' ); ?>"
			description="<?php _e( 'Set numeric priority to arrange meta box tab based on values.', 'jet-engine' ); ?>"
			type="number"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			:conditions="[
				{
					input: this.generalSettings.object_type,
					compare: 'equal',
					value: 'woocommerce_product_data',
				},
				{
					input: this.generalSettings.wc_product_data_panel,
					compare: 'equal',
					value: 'custom',
				}
			]"
			v-model="generalSettings.wc_product_data_priority"
		></cx-vui-input>

		<cx-vui-select
			:label="'<?php _e( 'Position', 'jet-engine' ); ?>'"
			:description="'<?php _e( 'Select meta box display position.', 'jet-engine' ); ?>'"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			:options-list="[
				{
					value: 'woocommerce_variation_options',
					label: '<?php _e( 'Options', 'jet-engine' ) ?>'
				},
				{
					value: 'woocommerce_variation_options_pricing',
					label: '<?php _e( 'Pricing', 'jet-engine' ) ?>'
				},
				{
					value: 'woocommerce_variation_options_inventory',
					label: '<?php _e( 'Inventory', 'jet-engine' ) ?>'
				},
				{
					value: 'woocommerce_variation_options_dimensions',
					label: '<?php _e( 'Dimensions', 'jet-engine' ) ?>'
				},
				{
					value: 'woocommerce_variation_options_download',
					label: '<?php _e( 'Download', 'jet-engine' ) ?>'
				},
				{
					value: 'woocommerce_product_after_variable_attributes',
					label: '<?php _e( 'Attributes', 'jet-engine' ) ?>'
				}
			]"
			:conditions="[
				{
					input: this.generalSettings.object_type,
					compare: 'equal',
					value: 'woocommerce_product_variation',
				}
			]"
			v-model="generalSettings.wc_product_variation_position"
		></cx-vui-select>
		<?php
	}

	/**
	 * Enqueue custom styles.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @return void
	 */
	public function enqueue_custom_styles() {
		if ( ! $this->has_styles ) {
			wp_enqueue_style(
				'jet-engine-wc-meta-boxes',
				jet_engine()->plugin_url( 'includes/compatibility/packages/woocommerce/inc/assets/css/admin/meta-boxes.css' ),
				[],
				jet_engine()->get_version()
			);

			$this->has_styles = true;
		}
	}

	/**
	 * Enqueue inline scripts.
	 *
	 * Inline script initialize fields after variation loaded.
	 * Enable variation for save after some controls changed.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @return void
	 */
	public function enqueue_inline_script() {
		if ( ! $this->has_inline_script ) {
			$inline_script = "
				( function( $ ) {
					$( document ).on( 'woocommerce_variations_loaded', function ( event ) {
						$( event.target ).trigger( 'cx-control-init' );
						
						$( window ).on( 'cx-checkbox-change cx-control-change cx-switcher-change', function ( event ) {
							let field = $( '.cx-control[data-control-name=\"' + event.controlName + '\"]' );
							
							field.closest( '.woocommerce_variation' ).addClass( 'variation-needs-update' );
							$( 'button.cancel-variation-changes, button.save-variation-changes' ).prop( 'disabled', false );
						} )
					} );
				} )( jQuery );
			";

			wp_add_inline_script( 'cx-interface-builder', $inline_script );

			$this->has_inline_script = true;
		}
	}

	/**
	 * Instance.
	 *
	 * Returns the instance of the class.
	 *
	 * @since  3.2.0
	 * @access public
	 *
	 * @return object
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;

	}

}