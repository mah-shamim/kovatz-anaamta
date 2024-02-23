<?php
/**
 * WPML compatibility package class.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_WPML_Package' ) ) {

	class Jet_Woo_Builder_WPML_Package {

		public function __construct() {

			add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'wpml_widgets_to_translate_filter' ] );
			add_filter( 'jet-woo-builder/final-custom-archive-template', [ $this, 'set_translated_object' ] );
			add_filter( 'jet-woo-builder/woocommerce/products-loop/custom-archive-template', [ $this, 'set_translated_object' ] );
			add_filter( 'jet-woo-builder/current-template/template-id', [ $this, 'modify_template_id' ] );

			add_action( 'jet-woo-builder/rest/init-endpoints', function () {
				if ( defined( 'REST_REQUEST' ) && ! isset( WC()->cart ) ) {
					wc_load_cart();
				}
			} );

			add_filter( 'jet-woo-builder/purchase-popup-id', [ $this, 'modify_popup_id' ] );

		}

		/**
		 * WPML widgets to translate filter.
		 *
		 * Passes the array that lists all the widget types that need to be translated.
		 *
		 * @since  1.3.5
		 * @since  2.1.0 Update translatable nodes.
		 * @access public
		 *
		 * @param array $widgets List of the widgets to be translated.
		 *
		 * @return array
		 */
		public function wpml_widgets_to_translate_filter( $widgets ) {

			$widgets['jet-woo-products'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-products' ],
				'fields'     => [
					[
						'field'       => 'sale_badge_text',
						'type'        => __( 'Products Grid: Badge Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'in_stock_status_text',
						'type'        => __( 'Products Grid: Stock Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'on_backorder_status_text',
						'type'        => __( 'Products Grid: Backorder Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'out_of_stock_status_text',
						'type'        => __( 'Products Grid: Out of Stock Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'not_found_message',
						'type'        => __( 'Products Grid: Not Found Message', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-woo-products-list'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-products-list' ],
				'fields'     => [
					[
						'field'       => 'sale_badge_text',
						'type'        => __( 'Products Grid: Badge Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'not_found_message',
						'type'        => __( 'Products List: Not Found Message', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-woo-categories'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-categories' ],
				'fields'     => [
					[
						'field'       => 'count_before_text',
						'type'        => __( 'Categories Grid: Before Count', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'count_after_text',
						'type'        => __( 'Categories Grid: After Count', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'desc_after_text',
						'type'        => __( 'Categories Grid: Trimmed After', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-woo-taxonomy-tiles'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-taxonomy-tiles' ],
				'fields'     => [
					[
						'field'       => 'count_before_text',
						'type'        => __( 'Taxonomy Tiles: Before Count', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'count_after_text',
						'type'        => __( 'Taxonomy Tiles: After Count', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-woo-builder-archive-category-count'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-builder-archive-category-count' ],
				'fields'     => [
					[
						'field'       => 'archive_category_count_before_text',
						'type'        => __( 'Archive Category Count: Before Count', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'archive_category_count_after_text',
						'type'        => __( 'Archive Category Count: After Count', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-woo-builder-archive-category-description'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-builder-archive-category-description' ],
				'fields'     => [
					[
						'field'       => 'archive_category_description_after_text',
						'type'        => __( 'Archive Category Description: Trimmed After', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-woo-builder-archive-sale-badge'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-builder-archive-sale-badge' ],
				'fields'     => [
					[
						'field'       => 'archive_badge_text',
						'type'        => __( 'Archive Sale Badge: Badge Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-cart-cross-sells'] = [
				'conditions' => [ 'widgetType' => 'jet-cart-cross-sells' ],
				'fields'     => [
					[
						'field'       => 'cross_sell_products_edit_heading_area',
						'type'        => __( 'Cart Cross Sells: Heading', 'jet-woo-builder' ),
						'editor_type' => 'AREA',
					],
				],
			];

			$widgets['jet-cart-empty-message'] = [
				'conditions' => [ 'widgetType' => 'jet-cart-empty-message' ],
				'fields'     => [
					[
						'field'       => 'cart_empty_message_text',
						'type'        => __( 'Cart Empty Message', 'jet-woo-builder' ),
						'editor_type' => 'AREA',
					],
				],
			];

			$widgets['jet-cart-return-to-shop'] = [
				'conditions' => [ 'widgetType' => 'jet-cart-return-to-shop' ],
				'fields'     => [
					[
						'field'       => 'cart_return_to_shop_button_text',
						'type'        => __( 'Cart Return To Shop: Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					'cart_return_to_shop_button_link' => [
						'field'       => 'url',
						'type'        => __( 'Cart Return To Shop: Link', 'jet-woo-builder' ),
						'editor_type' => 'LINK',
					],
				],
			];

			$widgets['jet-cart-table'] = [
				'conditions'     => [ 'widgetType' => 'jet-cart-table' ],
				'fields_in_item' => [
					'cart_table_items' => [
						[
							'field'       => 'cart_table_heading_title',
							'type'        => __( 'Cart Table Column: Heading', 'jet-woo-builder' ),
							'editor_type' => 'LINE',
						],
					],
				],
				'fields'         => [
					[
						'field'       => 'cart_table_update_button_text',
						'type'        => __( 'Cart Table: Update Cart Button Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'cart_table_coupon_form_button_text',
						'type'        => __( 'Cart Table: Coupon Button Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'cart_table_coupon_form_placeholder_text',
						'type'        => __( 'Cart Table: Coupon Placeholder', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-checkout-additional-form'] = [
				'conditions' => [ 'widgetType' => 'jet-checkout-additional-form' ],
				'fields'     => [
					[
						'field'       => 'checkout_additional_form_title_text',
						'type'        => __( 'Checkout Additional Form: Heading', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-checkout-billing'] = [
				'conditions'     => [ 'widgetType' => 'jet-checkout-billing' ],
				'fields_in_item' => [
					'field_list' => [
						[
							'field'       => 'field_label',
							'type'        => __( 'Checkout Billing Form Item: Label', 'jet-woo-builder' ),
							'editor_type' => 'LINE',
						],
						[
							'field'       => 'field_placeholder',
							'type'        => __( 'Checkout Billing Form Item: Placeholder', 'jet-woo-builder' ),
							'editor_type' => 'LINE',
						],
						[
							'field'       => 'field_default_value',
							'type'        => __( 'Checkout Billing Form Item: Default Value', 'jet-woo-builder' ),
							'editor_type' => 'LINE',
						],
					],
				],
			];

			$widgets['jet-checkout-shipping-form'] = [
				'conditions'     => [ 'widgetType' => 'jet-checkout-shipping-form' ],
				'fields'         => [
					[
						'field'       => 'checkout_shipping_form_title_text',
						'type'        => __( 'Checkout Shipping Form: Heading', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
				'fields_in_item' => [
					'field_list' => [
						[
							'field'       => 'field_label',
							'type'        => __( 'Checkout Shipping Form Item: Label', 'jet-woo-builder' ),
							'editor_type' => 'LINE',
						],
						[
							'field'       => 'field_placeholder',
							'type'        => __( 'Checkout Shipping Form Item: Placeholder', 'jet-woo-builder' ),
							'editor_type' => 'LINE',
						],
						[
							'field'       => 'field_default_value',
							'type'        => __( 'Checkout Shipping Form Item: Default Value', 'jet-woo-builder' ),
							'editor_type' => 'LINE',
						],
					],
				],
			];

			$widgets['jet-checkout-coupon-form'] = [
				'conditions' => [ 'widgetType' => 'jet-checkout-coupon-form' ],
				'fields'     => [
					[
						'field'       => 'checkout_coupon_form_heading_notice_text',
						'type'        => __( 'Checkout Coupon Form: Toggle Text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'checkout_coupon_form_heading_link_text',
						'type'        => __( 'Checkout Coupon Form: Toggle Link Text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-checkout-login-form'] = [
				'conditions' => [ 'widgetType' => 'jet-checkout-login-form' ],
				'fields'     => [
					[
						'field'       => 'checkout_login_form_heading_notice_text',
						'type'        => __( 'Checkout Login Form: Toggle Text', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-woo-builder-products-loop'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-builder-products-loop' ],
				'fields'     => [
					[
						'field'       => 'main_layout_switcher_label',
						'type'        => __( 'Products Loop: Switcher Main Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'secondary_layout_switcher_label',
						'type'        => __( 'Products Loop: Switcher Secondary Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-woo-builder-products-navigation'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-builder-products-navigation' ],
				'fields'     => [
					[
						'field'       => 'prev_text',
						'type'        => __( 'Products Navigation: Prev Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'next_text',
						'type'        => __( 'Products Navigation: Next Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-woo-builder-products-pagination'] = [
				'conditions' => [ 'widgetType' => 'jet-woo-builder-products-pagination' ],
				'fields'     => [
					[
						'field'       => 'prev_text',
						'type'        => __( 'Products Pagination: Prev Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'next_text',
						'type'        => __( 'Products Pagination: Next Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-single-attributes'] = [
				'conditions' => [ 'widgetType' => 'jet-single-attributes' ],
				'fields'     => [
					[
						'field'       => 'block_title',
						'type'        => __( 'Single Attributes: Heading', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-single-rating'] = [
				'conditions' => [ 'widgetType' => 'jet-single-rating' ],
				'fields'     => [
					[
						'field'       => 'single_rating_reviews_link_url',
						'type'        => __( 'Single Rating: Link URL', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'single_rating_reviews_link_caption_single',
						'type'        => __( 'Single Rating: Singular Caption', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'single_rating_reviews_link_caption_plural',
						'type'        => __( 'Single Rating: Plural Caption', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'single_rating_reviews_link_before_caption',
						'type'        => __( 'Single Rating: Before Caption', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'single_rating_reviews_link_after_caption',
						'type'        => __( 'Single Rating: After Caption', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-single-sale-badge'] = [
				'conditions' => [ 'widgetType' => 'jet-single-sale-badge' ],
				'fields'     => [
					[
						'field'       => 'single_badge_text',
						'type'        => __( 'Single Sale Badge: Badge Label', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			$widgets['jet-thankyou-order'] = [
				'conditions' => [ 'widgetType' => 'jet-thankyou-order' ],
				'fields'     => [
					[
						'field'       => 'thankyou_message_text',
						'type'        => __( 'Thank You Order: Message', 'jet-woo-builder' ),
						'editor_type' => 'AREA',
					],
					[
						'field'       => 'thankyou_order_table_order_heading',
						'type'        => __( 'Thank You Order: Order Table Heading', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'thankyou_order_table_date_heading',
						'type'        => __( 'Thank You Order: Date Table Heading', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'thankyou_order_table_email_heading',
						'type'        => __( 'Thank You Order: Email Table Heading', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'thankyou_order_table_total_heading',
						'type'        => __( 'Thank You Order: Total Table Heading', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
					[
						'field'       => 'thankyou_order_table_payment_method_heading',
						'type'        => __( 'Thank You Order: Payment Method Table Heading', 'jet-woo-builder' ),
						'editor_type' => 'LINE',
					],
				],
			];

			return $widgets;

		}

		/**
		 * Modify template ID.
		 *
		 * Modify JetWooBuilder template ID.
		 *
		 * @since  1.4.2
		 * @access public
		 *
		 * @param $template_id
		 *
		 * @return mixed|void
		 */
		function modify_template_id( $template_id ) {
			return apply_filters( 'wpml_object_id', $template_id, jet_woo_builder_post_type()->slug(), true );
		}

		/**
		 * Returns translated popup ID to make WooBuilder related popups WPML-compatible
		 * 
		 * @param  int $popup_id
		 * @return int Translated popup ID or initial ID if no translation found
		 */
		public function modify_popup_id( $popup_id ) {
			return apply_filters( 'wpml_object_id', $popup_id, 'jet-popup', true );
		}

		/**
		 * Set translated object.
		 *
		 * Set translated object ID to show.
		 *
		 * @since  2.0.5
		 * @access public
		 *
		 * @param int $obj_id Object id.
		 *
		 * @return int
		 */
		public function set_translated_object( $obj_id ) {

			global $sitepress;

			$new_id = $sitepress->get_object_id( $obj_id );

			if ( $new_id ) {
				return $new_id;
			}

			return $obj_id;

		}

	}

}

new Jet_Woo_Builder_WPML_Package();