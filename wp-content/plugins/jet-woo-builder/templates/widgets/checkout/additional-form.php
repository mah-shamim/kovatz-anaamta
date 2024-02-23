<?php
/**
 * This template for displaying Checkout Additional Form widget.
 *
 * This template can be overridden by copying it to __your_theme/jet-woo-builder/widgets/checkout/additional-form.php.
 */

defined( 'ABSPATH' ) || exit;

$checkout = wc()->checkout();
$settings = $this->get_settings_for_display();

$heading_visible = isset( $settings['checkout_additional_form_heading_visibility'] )
	? filter_var( $settings['checkout_additional_form_heading_visibility'], FILTER_VALIDATE_BOOLEAN ) : false;
$heading_text    = isset( $settings['checkout_additional_form_title_text'] ) && ! empty( $settings['checkout_additional_form_title_text'] )
	? $settings['checkout_additional_form_title_text'] : 'Additional information';
?>

<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>
	<div class="woocommerce-additional-fields">
		<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

		<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', 'yes' === get_option( 'woocommerce_enable_order_comments', 'yes' ) ) ) : ?>
			<?php if ( $heading_visible ) : ?>
				<h3><?php esc_html_e( $heading_text, 'jet-woo-builder' ); ?></h3>
			<?php endif; ?>

			<div class="woocommerce-additional-fields__field-wrapper">
				<?php foreach ( $checkout->get_checkout_fields( 'order' ) as $key => $field ) : ?>
					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
	</div>
<?php endif; ?>