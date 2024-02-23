<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The display settings class
 *
 * @author      Jamie Madden, WC Vendors
 * @category    Settings
 * @package     WCVendors/Admin/Settings
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WCVendors_Settings_Display', false ) ) :

	/**
	 * WC_Admin_Settings_General.
	 */
	class WCVendors_Settings_Display extends WCVendors_Settings_Page {

		/**
		 * Constructor.
		 */
		public function __construct() {

			$this->id    = 'display';
			$this->label = __( 'Display', 'wc-vendors' );

			parent::__construct();
		}

		/**
		 * Get sections.
		 *
		 * @return array
		 */
		public function get_sections() {

			$sections = array(
				''         => __( 'General', 'wc-vendors' ),
				'labels'   => __( 'Labels', 'wc-vendors' ),
				'advanced' => __( 'Advanced', 'wc-vendors' ),
			);

			return apply_filters( 'wcvendors_get_sections_' . $this->id, $sections );
		}

		/**
		 * Get settings array.
		 *
		 * @param string $current_section Current section.
		 *
		 * @return array
		 */
		public function get_settings( $current_section = '' ) {

			if ( 'advanced' === $current_section ) {

				$settings = apply_filters(
					'wcvendors_settings_display_advanced',
					array(
						// Shop Display Options.
						array(
							'type' => 'title',
							'desc' => sprintf( __( 'Advanced options provide extra display options', 'wc-vendors' ), wcv_get_vendor_name( true, false ) ),
							'id'   => 'advanced_options',
						),
						array(
							'title'    => __( 'Product Page Stylesheet', 'wc-vendors' ),
							'desc'     => sprintf(
								/* translators: %s vendor label */
                                __( 'You can add CSS in this textarea, which will be loaded on the WooCommerce product edit page for %s in the WordPress admin.', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'desc_tip' => sprintf( __( 'This will output custom CSS on the product edit page ', 'wc-vendors' ), wcv_get_vendor_name( true, false ) ),
							'id'       => 'wcvendors_display_advanced_stylesheet',
							'css'      => 'width: 700px;min-height:100px',
							'default'  => '',
							'type'     => 'textarea',
						),
						array(
							'title'   => __( 'Use WooCommerce Registration', 'wc-vendors' ),
							'type'    => 'checkbox',
							'default' => 'no',
							'id'      => 'wcvendors_redirect_wp_registration_to_woocommerce_myaccount',
							'desc'    => __( 'This will redirect the WordPress registration to WooCommerce my-account page for registration.', 'wc-vendors' ),
						),

						array(
							'type' => 'sectionend',
							'id'   => 'advanced_options',
						),
					)
				);

			} elseif ( 'labels' === $current_section ) {

				$settings = apply_filters(
					'wcvendors_settings_display_labels',
					array(

						// Shop Display Options.
						array(
							'type' => 'title',
							'desc' => sprintf( __( 'Labels are shown on the front end, in orders or emails.', 'wc-vendors' ), wcv_get_vendor_name( true, false ) ),
							'id'   => 'label_options',
						),

						array(
							'title'    => sprintf(
								/* translators: %s vendor label */
                                __( '%s singluar term', 'wc-vendors' ),
                                wcv_get_vendor_name()
                            ),
							'desc_tip' => __( 'Change all references to vendor to this term', 'wc-vendors' ),
							'id'       => 'wcvendors_vendor_singular',
							'type'     => 'text',
							'default'  => sprintf(
								/* translators: %s vendor label */
                                __( '%s', 'wc-vendors' ), //phpcs:ignore
                                wcv_get_vendor_name()
                            ),
						),

						array(
							'title'    => sprintf(
								/* translators: %s vendor label */
                                __( '%s plural term', 'wc-vendors' ),
                                wcv_get_vendor_name( false )
                            ),
							'desc_tip' => __( 'Change all references to vendors to this term', 'wc-vendors' ),
							'id'       => 'wcvendors_vendor_plural',
							'type'     => 'text',
							'default'  => sprintf(
								/* translators: %s vendor label */
                                __( '%s', 'wc-vendors' ), //phpcs:ignore
                                wcv_get_vendor_name( false )
                            ),
						),

						array(
							'title'    => __( 'Sold By', 'wc-vendors' ),
							'desc'     => __( 'Enable sold by labels', 'wc-vendors' ),
							'desc_tip' => sprintf(
								/* translators: %s vendor label */
                                __( 'This enables the sold by labels used to show which %s shop the product belongs to', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'id'       => 'wcvendors_display_label_sold_by_enable',
							'default'  => 'yes',
							'type'     => 'checkbox',
						),

						array(
							'title'    => __( 'Sold By Separator', 'wc-vendors' ),
							'desc_tip' => __( 'The sold by separator', 'wc-vendors' ),
							'id'       => 'wcvendors_label_sold_by_separator',
							'type'     => 'text',
							'default'  => __( ':', 'wc-vendors' ),
						),

						array(
							'title'    => __( 'Sold By Label', 'wc-vendors' ),
							'desc_tip' => __( 'The sold by label', 'wc-vendors' ),
							'id'       => 'wcvendors_label_sold_by',
							'type'     => 'text',
							'default'  => __( 'Sold By', 'wc-vendors' ),
						),

						array(
							'title'   => sprintf(
								/* translators: %s vendor label */
                                __( 'Become A %s', 'wc-vendors' ),
                                wcv_get_vendor_name()
                            ),
							'desc'    => sprintf(
								/* translators: %s vendor label */
                                __( 'Show the "Become a %s" link on WooCommerce my-account page', 'wc-vendors' ),
                                wcv_get_vendor_name()
                            ),
							'id'      => 'wcvendors_become_a_vendor_my_account_link_visibility',
							'default' => 'yes',
							'type'    => 'checkbox',
						),

						array(
							'title'    => sprintf(
								/* translators: %s vendor label */
                                __( 'Become A %s Label', 'wc-vendors' ),
                                wcv_get_vendor_name()
                            ),
							'desc_tip' => sprintf(
								/* translators: %s vendor label */
                                __( 'The become a %s label', 'wc-vendors' ),
                                wcv_get_vendor_name()
                            ),
							'id'       => 'wcvendors_label_become_a_vendor',
							'type'     => 'text',
							'default'  => __( 'Become a', 'wc-vendors' ),
						),

						array(
							'title'   => sprintf(
								/* translators: %s vendor label */
                                __( '%s Store Info', 'wc-vendors' ),
                                wcv_get_vendor_name()
                            ),
							'desc'    => sprintf(
								/* translators: %s vendor label */
                                __( 'Enable %s store info tab on the single product page', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'id'      => 'wcvendors_label_store_info_enable',
							'default' => 'yes',
							'type'    => 'checkbox',
						),

						array(
							'title'   => sprintf(
								/* translators: %s vendor label */
                                __( '%s Store Info Label', 'wc-vendors' ),
                                wcv_get_vendor_name()
                            ),
							'desc'    => sprintf(
								/* translators: %s vendor label */
                                __( 'The %s store info label', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'id'      => 'wcvendors_display_label_store_info',
							'type'    => 'text',
							'default' => __( 'Store Info', 'wc-vendors' ),
						),

						array(
							'type' => 'sectionend',
							'id'   => 'label_options',
						),

					)
				);

			} else {
				$avatar_defaults = array(
					'mystery'          => __( 'Mystery Person', 'wc-vendors' ),
					'blank'            => __( 'Blank', 'wc-vendors' ),
					'gravatar_default' => __( 'Gravatar Logo', 'wc-vendors' ),
					'identicon'        => __( 'Identicon (Generated)', 'wc-vendors' ),
					'wavatar'          => __( 'Wavatar (Generated)', 'wc-vendors' ),
					'monsterid'        => __( 'MonsterID (Generated)', 'wc-vendors' ),
					'retro'            => __( 'Retro (Generated)', 'wc-vendors' ),
				);

				$avatar_source = apply_filters( 'wcvendors_avatar_source', $avatar_defaults );

				$settings = apply_filters(
					'wcvendors_settings_display_general',
					array(

						// General Options.
						array(
							'title' => __( 'Pages', 'wc-vendors' ),
							'type'  => 'title',
							'desc'  => sprintf(
								/* translators: %s vendor label */
                                __( 'These pages used on the front end by %s.', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'id'    => 'page_options',
						),
						array(
							'title'   => __( 'Dashboard', 'wc-vendors' ),
							'id'      => 'wcvendors_vendor_dashboard_page_id',
							'type'    => 'single_select_page',
							'default' => '',
							'class'   => 'wc-enhanced-select-nostd',
							'css'     => 'min-width:300px;',
							'desc'    => sprintf(
								/* translators: %s vendor label */
                                __( '<br />This sets the page used to display the front end %s dashboard. This page should contain the following shortcode. <code>[wcv_vendor_dashboard]</code>', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
						),
						array(
							'title'   => __( 'Shop Settings', 'wc-vendors' ),
							'id'      => 'wcvendors_shop_settings_page_id',
							'type'    => 'single_select_page',
							'default' => '',
							'class'   => 'wc-enhanced-select-nostd',
							'css'     => 'min-width:300px;',
							'desc'    => sprintf(
								/* translators: %s vendor label */
                                __( '<br />This sets the page used to display the %s shop settings page. This page should contain the following shortcode. <code>[wcv_shop_settings]</code>', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
						),
						array(
							'title'   => __( 'Orders', 'wc-vendors' ),
							'id'      => 'wcvendors_product_orders_page_id',
							'type'    => 'single_select_page',
							'default' => '',
							'class'   => 'wc-enhanced-select-nostd',
							'css'     => 'min-width:300px;',
							'desc'    => sprintf(
								/* translators: %s vendor label */
                                __( '<br />This sets the page used to display the %s orders page. This page should contain the following shortcode. <code>[wcv_orders]</code>', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
						),
						array(
							'title'   => sprintf(
								/* translators: %s vendor label */
                                __( '%s', 'wc-vendors' ), //phpcs:ignore
                                wcv_get_vendor_name( false )
                            ),
							'id'      => 'wcvendors_vendors_page_id',
							'type'    => 'single_select_page',
							'default' => '',
							'class'   => 'wc-enhanced-select-nostd',
							'css'     => 'min-width:300px;',
							'desc'    => sprintf(
								/* translators: %s vendor label */
                                __( '<br />This sets the page used to display a paginated list of all %1$s stores. Your %1$s stores will be available at <code>%2$s/page-slug/store-name/</code><br />This page should contain the following shortcode. <code>[wcv_vendorslist]</code>', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false ),
                                esc_html( home_url() )
                            ),
						),
						array(
							'title'   => __( 'Terms and Conditions', 'wc-vendors' ),
							'id'      => 'wcvendors_vendor_terms_page_id',
							'type'    => 'single_select_page',
							'default' => '',
							'class'   => 'wc-enhanced-select-nostd',
							'css'     => 'min-width:300px;',
							'desc'    => sprintf(
								/* translators: %s vendor label */
                                __( '<br />This sets the page used to display the terms and conditions when a %s signs up.', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
						),
						array(
							'title'             => __( 'Orders per page', 'wc-vendors' ),
							'desc'              => __( 'How many orders to show per page on the Vendor Dashboard', 'wc-vendors' ),
							'id'                => 'wcvendors_free_orders_per_page',
							'default'           => '10',
							'type'              => 'number',
							'custom_attributes' => array(
								'min'  => 1,
								'step' => 1,
							),
						),

						array(
							'type' => 'sectionend',
							'id'   => 'page_options',
						),

						// Shop Settings.
						array(
							'title' => __( 'Store Settings', 'wc-vendors' ),
							'type'  => 'title',
							'desc'  => sprintf(
								/* translators: %s vendor label */
                                __( 'These are the settings for the individual %s stores.', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'id'    => 'shop_options',
						),

						array(
							'title'   => sprintf(
								/* translators: %s vendor label */
                                __( '%s Store URL', 'wc-vendors' ),
                                wcv_get_vendor_name()
                            ),
							'desc'    => sprintf(
								/* translators: %s vendor label */
                                __( 'If you enter "vendors" your %1$s store will be %1$s/vendors/store-name/', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false ),
                                esc_html( home_url() )
                            ),
							'id'      => 'wcvendors_vendor_shop_permalink',
							'default' => 'vendors',
							'type'    => 'text',
						),

						array(
							'title'    => __( 'Shop Header', 'wc-vendors' ),
							'desc'     => sprintf(
								/* translators: %s vendor label */
                                __( 'Enable %s shop headers', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'desc_tip' => sprintf(
								/* translators: %s vendor label */
                                __( 'This enables the %s shop header template.', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'id'       => 'wcvendors_display_shop_headers',
							'default'  => 'no',
							'type'     => 'checkbox',
						),

						array(
							'title'   => __( 'Single Product Header', 'wcvendors-pro' ),
							'desc'    => __( 'Enable shop headers on single product pages.', 'wcvendors-pro' ),
							'tip'     => __( 'Check to enable the entire header on /shop/product-category/product-name/', 'wcvendors-pro' ),
							'id'      => 'wcvendors_store_single_headers',
							'type'    => 'checkbox',
							'default' => 'no',
						),

						array(
							'title'    => __( 'Shop Description', 'wc-vendors' ),
							'desc'     => sprintf(
								/* translators: %s vendor label */
                                __( 'Enable %s shop description', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'desc_tip' => sprintf(
								/* translators: %s vendor label */
                                __( 'This enables the %1$s shop description on the %1$s store page.', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'id'       => 'wcvendors_display_shop_description',
							'default'  => 'no',
							'type'     => 'checkbox',
						),

						array(
							'title'    => __( 'Shop HTML', 'wc-vendors' ),
							'desc'     => sprintf(
								/* translators: %s vendor label */
                                __( 'Allow HTML in %s shop description', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'desc_tip' => sprintf(
								/* translators: %s vendor label */
                                __( 'This will enable the WYSIWYG editor and for the %1$s shop description. You can enable or disable this per %1$s by editing the %1$s user account.', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'id'       => 'wcvendors_display_shop_description_html',
							'default'  => 'no',
							'type'     => 'checkbox',
						),

						array(
							'title'    => __( 'Display Name', 'wc-vendors' ),
							'id'       => 'wcvendors_display_shop_display_name',
							'desc_tip' => sprintf(
								/* translators: %s vendor label */
                                __( 'Select what will be used to display the %s name throughout the marketplace.', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'default'  => 'shop_name',
							'type'     => 'select',
							'class'    => 'wc-enhanced-select',
							'options'  => array(
								'display_name' => __( 'Display name', 'wc-vendors' ),
								'shop_name'    => __( 'Shop name', 'wc-vendors' ),
								'user_login'   => sprintf(
									/* translators: %s vendor label */
                                    __( '%s Username', 'wc-vendors' ),
                                    wcv_get_vendor_name()
                                ),
								'user_email'   => sprintf(
									/* translators: %s vendor label */
                                    __( '%s Email', 'wc-vendors' ),
                                    wcv_get_vendor_name()
                                ),
							),
						),
						array(
							'type' => 'sectionend',
							'id'   => 'shop_options',
						),
						array(
							'title' => __( 'Vendors List Settings', 'wc-vendors' ),
							'type'  => 'title',
							'desc'  => __( 'These settings are for the vendors list page.', 'wc-vendors' ),
							'id'    => 'vendors_list_options',
						),
						array(
							'title'   => __( 'List Display', 'wc-vendors' ),
							'desc'    => sprintf(
								/* translators: %s vendor label */
                                __( 'Select default display for %s list', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'id'      => 'wcvendors_display_vendors_list_type',
							'default' => 'grid',
							'type'    => 'select',
							'class'   => 'wc-enhanced-select',
							'options' => array(
								'grid' => __( 'Grid', 'wc-vendors' ),
								'list' => __( 'List', 'wc-vendors' ),
							),
						),
						array(
							'title'   => __( 'Default Avatar', 'wc-vendors' ),
							'desc'    => sprintf(
								/* translators: %s vendor label */
                                __( 'Select avatar for %s', 'wc-vendors' ),
                                wcv_get_vendor_name( true, false )
                            ),
							'id'      => 'wcvendors_display_vendors_avatar_source',
							'default' => 'gravatar',
							'type'    => 'select',
							'class'   => 'wc-enhanced-select',
							'options' => $avatar_source,
						),
						array(
							'type' => 'sectionend',
							'id'   => 'vendors_list_options',
						),
					)
				);

			}

			return apply_filters( 'wcvendors_get_settings_' . $this->id, $settings, $current_section );
		}
	}

endif;

return new WCVendors_Settings_Display();
