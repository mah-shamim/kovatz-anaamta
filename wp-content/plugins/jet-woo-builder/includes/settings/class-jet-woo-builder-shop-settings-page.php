<?php
/**
 * WooCommerce Product Settings
 *
 * @author   WooThemes
 * @category Admin
 * @package  WooCommerce/Admin
 * @version  2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Jet_Woo_Builder_Shop_Settings_Page
 */
class Jet_Woo_Builder_Shop_Settings_Page extends WC_Settings_Page {

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->id    = jet_woo_builder_shop_settings()->key;
		$this->label = __( 'JetWooBuilder', 'jet-woo-builder' );

		parent::__construct();

	}

	/**
	 * Get sections.
	 *
	 * @return array
	 */
	public function get_sections() {

		$sections = array(
			'' => esc_html__( 'General', 'jet-woo-builder' ),
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );

	}

	/**
	 * Output the settings.
	 */
	public function output() {

		global $current_section;
		$settings = $this->get_settings( $current_section );

		WC_Admin_Settings::output_fields( $settings );

	}

	/**
	 * Save settings.
	 */
	public function save() {

		global $current_section;

		$settings = $this->get_settings( $current_section );

		WC_Admin_Settings::save_fields( $settings );

	}

	/**
	 * Get settings array.
	 *
	 * @param string $current_section Current section name.
	 *
	 * @return array
	 */
	public function get_settings( $current_section = '' ) {

		global $current_section;

		$settings = array(
			array(
				'title' => __( 'General', 'jet-woo-builder' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'general_options',
			),

			array(
				'title'   => __( 'Widgets Render Method', 'jet-woo-builder' ),
				'desc'    => __( 'Select widgets render method for archive product and archive category templates', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[widgets_render_method]',
				'default' => 'macros',
				'type'    => 'jet_woo_select_render_method_field',
				'class'   => 'wc-enhanced-select-nostd',
				'css'     => 'min-width:300px;',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'general_options',
			),

			array(
				'title' => __( 'Shop Page', 'jet-woo-builder' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'shop_options',
			),

			array(
				'title'   => __( 'Custom Shop Page', 'jet-woo-builder' ),
				'desc'    => __( 'Enable custom shop page', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[custom_shop_page]',
				'default' => '',
				'type'    => 'checkbox',
			),

			array(
				'title'    => __( 'Shop Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a global shop template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[shop_template]',
				'doc_type' => 'shop',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'title'   => __( 'Custom Taxonomy Template', 'jet-woo-builder' ),
				'desc'    => __( 'Enable custom taxonomy template. Read more about custom template <a href="https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-set-up-a-custom-product-taxonomy-template/" target="_blank" rel="nofollow">here</a>', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[custom_taxonomy_template]',
				'default' => '',
				'type'    => 'checkbox',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'shop_options',
			),

			array(
				'title' => __( 'Single Product', 'jet-woo-builder' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'single_options',
			),

			array(
				'title'   => __( 'Custom Single Product', 'jet-woo-builder' ),
				'desc'    => __( 'Enable custom single product page', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[custom_single_page]',
				'default' => '',
				'type'    => 'checkbox',
			),

			array(
				'title'    => __( 'Single Product Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a global single product template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[single_template]',
				'doc_type' => 'single',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'single_options',
			),

			array(
				'title' => __( 'Archive Product', 'jet-woo-builder' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'archive_options',
			),

			array(
				'title'   => __( 'Custom Archive Product', 'jet-woo-builder' ),
				'desc'    => __( 'Enable custom archive product', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[custom_archive_page]',
				'default' => '',
				'type'    => 'checkbox',
			),

			array(
				'title'    => __( 'Archive Product Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a global archive product template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[archive_template]',
				'doc_type' => 'archive',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'title'    => __( 'Search Page Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a global search page template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[search_template]',
				'doc_type' => 'archive',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'title'    => __( 'Product Shortcode Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a global product shortcode template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[shortcode_template]',
				'doc_type' => 'archive',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'title'    => __( 'Related and Up-Sells Products Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a global related products template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[related_template]',
				'doc_type' => 'archive',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'title'    => __( 'Cross-Sells Product Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a global cross-sells product template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[cross_sells_template]',
				'doc_type' => 'archive',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'archive_options',
			),

			array(
				'title' => __( 'Archive Category', 'jet-woo-builder' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'categories_options',
			),

			array(
				'title'   => __( 'Custom Archive Category', 'jet-woo-builder' ),
				'desc'    => __( 'Enable custom archive category', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[custom_archive_category_page]',
				'default' => '',
				'type'    => 'checkbox',
			),

			array(
				'title'    => __( 'Archive Category Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a global archive category template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[category_template]',
				'doc_type' => 'category',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'categories_options',
			),

			array(
				'title' => __( 'Cart', 'jet-woo-builder' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'cart_options',
			),

			array(
				'title'   => __( 'Custom Cart', 'jet-woo-builder' ),
				'desc'    => __( 'Enable custom cart', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[custom_cart_page]',
				'default' => '',
				'type'    => 'checkbox',
			),

			array(
				'title'    => __( 'Cart Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a cart template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[cart_template]',
				'doc_type' => 'cart',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'title'    => __( 'Empty Cart Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as  an empty cart template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[empty_cart_template]',
				'doc_type' => 'cart',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'cart_options',
			),

			array(
				'title' => __( 'Checkout', 'jet-woo-builder' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'checkout_options',
			),

			array(
				'title'   => __( 'Custom Checkout', 'jet-woo-builder' ),
				'desc'    => __( 'Enable custom checkout', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[custom_checkout_page]',
				'default' => '',
				'type'    => 'checkbox',
			),

			array(
				'title'    => __( 'Checkout Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a checkout template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[checkout_template]',
				'doc_type' => 'checkout',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'title'    => __( 'Checkout Top Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as  a checkout top content template (E.g: Coupon form, login form etc.)', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[checkout_top_template]',
				'doc_type' => 'checkout',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'checkout_options',
			),

			array(
				'title' => __( 'Thank You Page', 'jet-woo-builder' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'thankyou_options',
			),

			array(
				'title'   => __( 'Custom Thank You Page', 'jet-woo-builder' ),
				'desc'    => __( 'Enable custom thank you page', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[custom_thankyou_page]',
				'default' => '',
				'type'    => 'checkbox',
			),

			array(
				'title'    => __( 'Thank You Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a thank you template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[thankyou_template]',
				'doc_type' => 'thankyou',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'thankyou_options',
			),

			array(
				'title' => __( 'My Account Page', 'jet-woo-builder' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'myaccount_options',
			),

			array(
				'title'   => __( 'Custom My Account Page', 'jet-woo-builder' ),
				'desc'    => __( 'Enable custom my account page', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[custom_myaccount_page]',
				'default' => '',
				'type'    => 'checkbox',
			),

			array(
				'title'    => __( 'My Account Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a my account template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[myaccount_template]',
				'doc_type' => 'myaccount',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'title'    => __( 'My Account Login Page Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a login page template (E.g: Registration form, login form etc.)', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[form_login_template]',
				'doc_type' => 'myaccount',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'title'    => __( 'Custom My Account Page endpoints', 'jet-woo-builder' ),
				'desc'     => __( 'Enable custom my account page endpoints', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[custom_myaccount_page_endpoints]',
				'default'  => 'no',
				'type'     => 'checkbox',
				'desc_tip' => __( 'If you want to use My Account Page endpoint templates with current theme view, than make sure that My Account Template is set to `Default`.', 'jet-woo-builder' ),
			),

			array(
				'title'    => __( 'My Account Dashboard Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a my account dashboard template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[myaccount_dashboard_template]',
				'doc_type' => 'myaccount',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'title'    => __( 'My Account Orders Endpoint Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a my account orders endpoint template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[myaccount_orders_endpoint_template]',
				'doc_type' => 'myaccount',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'title'    => __( 'My Account Downloads Endpoint Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a my account downloads endpoint template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[myaccount_downloads_endpoint_template]',
				'doc_type' => 'myaccount',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'title'    => __( 'My Account Address Endpoint Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a my account edit address endpoint template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[myaccount_edit_address_endpoint_template]',
				'doc_type' => 'myaccount',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'title'    => __( 'My Account Edit Account Endpoint Template', 'jet-woo-builder' ),
				'desc'     => __( 'Select the template to use it as a my account edit account endpoint template', 'jet-woo-builder' ),
				'id'       => jet_woo_builder_shop_settings()->options_key . '[myaccount_edit_account_endpoint_template]',
				'doc_type' => 'myaccount',
				'default'  => '',
				'type'     => 'jet_woo_select_template',
				'class'    => 'wc-enhanced-select-nostd',
				'css'      => 'min-width:300px;',
			),

			array(
				'type' => 'sectionend',
				'id'   => 'myaccount_options',
			),

			array(
				'title' => __( 'Other Options', 'jet-woo-builder' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'other_options',
			),

			array(
				'title'   => __( 'Use AJAX Add to Cart', 'jet-woo-builder' ),
				'desc'    => __( 'Force use of AJAX Add to Cart instead of page reload on the product single page.', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[use_ajax_add_to_cart]',
				'default' => '',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'Use Native Templates', 'jet-woo-builder' ),
				'desc'    => __( 'Force use of native WooCommerce templates instead of those rewritten in theme.', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[use_native_templates]',
				'default' => '',
				'type'    => 'checkbox',
			),

			array(
				'title'   => __( 'Number of related products to show', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[related_products_per_page]',
				'type'    => 'number',
				'default' => 4,
				'step'    => 1,
				'min'     => 1,
				'max'     => '',
				'std'     => 10,
			),

			array(
				'title'   => __( 'Number of up-sells products to show', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[up_sells_products_per_page]',
				'type'    => 'number',
				'default' => 4,
				'step'    => 1,
				'min'     => 1,
				'max'     => '',
				'std'     => 10,
			),

			array(
				'title'   => __( 'Number of cross-sells products to show', 'jet-woo-builder' ),
				'id'      => jet_woo_builder_shop_settings()->options_key . '[cross_sells_products_per_page]',
				'type'    => 'number',
				'default' => 4,
				'step'    => 1,
				'min'     => 1,
				'max'     => '',
				'std'     => 10,
			),

			array(
				'type' => 'sectionend',
				'id'   => 'other_options',
			),
		);

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
	}

}
