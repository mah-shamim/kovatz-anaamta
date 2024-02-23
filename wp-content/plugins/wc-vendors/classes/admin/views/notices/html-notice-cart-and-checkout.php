<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="notice notice-error is-dismissible wcv-notice-container" id="wcv-switch-to-classic-cart-checkout-notice" data-notice-key="cart_and_checkout">
    <p>
        <?php
        printf(
            '%s <strong>%s</strong>',
            '<span class="dashicons dashicons-warning"></span>',
            esc_html__( 'We noticed that you\'re using the new Cart/Checkout experience in WooCommerce.', 'wc-vendors' ),
        );
        ?>
    </p>
    <p>
    <?php
        echo esc_html( 'Full compatibility is in the works with WC Vendors. We suggest switching to the Classic Cart/Checkout experience in the meantime, it\'s well tested and 100% compatible with our plugin and others as well.' );
    ?>
    </p>
    <button class="button button-primary" id="wcv-switch-to-classic-cart-checkout" style="margin-bottom: .5em;">
        <?php esc_html_e( 'Switch to Classic Cart/Checkout', 'wc-vendors' ); ?>
    </button>
</div>
