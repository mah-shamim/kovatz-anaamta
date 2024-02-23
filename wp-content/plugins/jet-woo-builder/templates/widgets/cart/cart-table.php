<?php
/**
 * Cart Table widget Template.
 */

defined( 'ABSPATH' ) || exit; ?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
		<thead>
			<tr>
				<?php
				foreach ( $table_settings['items'] as $item ) {
					printf(
						'<th class="product-%s elementor-repeater-item-%s">%s</th>',
						$item['cart_table_items'],
						$item['_id'],
						esc_html__( $item['cart_table_heading_title'], 'jet-woo-builder' )
					);
				}
				?>
			</tr>
		</thead>
		<tbody>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			/**
			 * Filter the product name.
			 *
			 * @since 2.1.0
			 * @param string $product_name Name of the product in the cart.
			 * @param array $cart_item The product in the cart.
			 * @param string $cart_item_key Key for the product in the cart.
			 */
			$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				?>
				<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

					<?php
					foreach ( $table_settings['items'] as $item ) {
						switch ( $item['cart_table_items'] ) {
							case 'remove':
								echo '<td class="product-remove elementor-repeater-item-' . $item['_id'] . '">';
								echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">%s</a>',
										esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
										/* translators: %s is the product name */
										esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
										esc_attr( $product_id ),
										esc_attr( $_product->get_sku() ),
										$widget->__render_icon( 'cart_table_remove_icon', '%s', '', false, $item )
									),
									$cart_item_key
								);
								echo '</td>';

								break;

							case 'thumbnail':
								echo '<td class="product-thumbnail elementor-repeater-item-' . $item['_id'] . '">';

								$thumbnail_size = isset( $item['cart_table_thumbnail_size'] ) ? $item['cart_table_thumbnail_size'] : 'thumbnail';
								$thumbnail      = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( $thumbnail_size ), $cart_item, $cart_item_key );

								if ( ! $product_permalink ) {
									echo $thumbnail;
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
								}

								echo '</td>';

								break;

							case 'name':
								echo '<td class="product-name elementor-repeater-item-' . $item['_id'] . '" data-title="' . esc_html__( $item['cart_table_heading_title'], 'jet-woo-builder' ) . '">';

								if ( ! $product_permalink ) {
									echo wp_kses_post( $product_name . '&nbsp;' );
								} else {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
								}

								do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

								// Meta data.
								echo wc_get_formatted_cart_item_data( $cart_item );

								// Backorder notification.
								if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
								}

								echo '</td>';

								break;

							case 'price':
								echo '<td class="product-price elementor-repeater-item-' . $item['_id'] . '" data-title="' . esc_html__( $item['cart_table_heading_title'], 'jet-woo-builder' ) . '">';
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
								echo '</td>';

								break;

							case 'quantity':
								echo '<td class="product-quantity elementor-repeater-item-' . $item['_id'] . '" data-title="' . esc_html__( $item['cart_table_heading_title'], 'jet-woo-builder' ) . '">';

								if ( $_product->is_sold_individually() ) {
									$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
								} else {
									$product_quantity = woocommerce_quantity_input(
										array(
											'input_name'   => "cart[{$cart_item_key}][qty]",
											'input_value'  => $cart_item['quantity'],
											'max_value'    => $_product->get_max_purchase_quantity(),
											'min_value'    => '0',
											'product_name' => $product_name,
										),
										$_product,
										false
									);
								}

								echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
								echo '</td>';

								break;

							case 'subtotal':
								echo '<td class="product-subtotal elementor-repeater-item-' . $item['_id'] . '" data-title="' . esc_html__( $item['cart_table_heading_title'], 'jet-woo-builder' ) . '">';
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
								echo '</td>';

								break;

							case 'custom_field':
								echo '<td class="product-subtotal elementor-repeater-item-' . $item['_id'] . '" data-title="' . esc_html__( $item['cart_table_heading_title'], 'jet-woo-builder' ) . '">';
								echo jet_woo_builder_template_functions()->get_cart_table_custom_field_value( $_product, $item );
								echo '</td>';

								break;

							default:
								break;
						}
					}
					?>

				</tr>
				<?php
			}
		}
		?>

		<?php do_action( 'woocommerce_cart_contents' ); ?>

		<?php
		$row_styles = '';

		if ( $table_settings['components']['update-automatically'] && ! wc_coupons_enabled() ) {
			$row_styles = 'style="display: none;"';
		}
		?>

		<tr <?php echo $row_styles; ?> >
			<td colspan="<?php echo count( $table_settings['items'] ); ?>" class="actions">

				<?php if ( wc_coupons_enabled() ) { ?>
					<div class="coupon">
						<label for="coupon_code" class="screen-reader-text"><?php esc_html_e( 'Coupon:', 'woocommerce' ); ?></label>
						<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( $table_settings['components']['coupon-form-placeholder'], 'jet-woo-builder' ); ?>"/>
						<button type="submit" class="button <?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="apply_coupon" value="<?php esc_attr_e( $table_settings['components']['coupon-form-button-label'], 'jet-woo-builder' ); ?>">
							<?php esc_attr_e( $table_settings['components']['coupon-form-button-label'], 'jet-woo-builder' ); ?>
						</button>
						<?php do_action( 'woocommerce_cart_coupon' ); ?>
					</div>
				<?php } ?>

				<button type="submit" class="button <?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>" name="update_cart" value="<?php esc_attr_e( $table_settings['components']['update-button-label'], 'jet-woo-builder' ); ?>">
					<?php esc_html_e( $table_settings['components']['update-button-label'], 'jet-woo-builder' ); ?>
				</button>

				<?php do_action( 'woocommerce_cart_actions' ); ?>

				<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
			</td>
		</tr>

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>
	<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>

<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

<div class="cart-collaterals">
	<?php do_action( 'woocommerce_cart_collaterals' ); ?>
</div>
