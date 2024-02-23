<?php
/**
 * Settings Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/dashboard/settings/settings.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/Emails/HTML
 * @version       2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<h2><?php esc_html_e( 'Settings', 'wc-vendors' ); ?></h2>

<?php
if ( function_exists( 'wc_print_notices' ) ) {
    wc_print_notices();
}
?>

<form method="post">
    <?php
    do_action( 'wcvendors_settings_before_commission_payout_method' );

    wc_get_template(
        'commission-payout-method.php',
        array(
            'user_id' => $user_id,
        ),
        'wc-vendors/dashboard/settings/',
        WCV_PLUGIN_DIR . 'templates/dashboard/settings/'
    );

    do_action( 'wcvendors_settings_after_commission_payout_method' );
    do_action( 'wcvendors_settings_before_paypal' );

    if ( 'false' !== $paypal_address ) {
        wc_get_template(
            'paypal-email-form.php',
            array(
                'user_id' => $user_id,
            ),
            'wc-vendors/dashboard/settings/',
            WCV_PLUGIN_DIR . 'templates/dashboard/settings/'
        );
    }

    do_action( 'wcvendors_settings_after_paypal' );

    ?>

    <?php do_action( 'wcvendors_settings_before_bank_details', $user_id ); ?>

    <?php if ( apply_filters( 'wcvendors_vendor_dashboard_bank_details_enable', true ) ) : ?>

        <?php
            wc_get_template(
                'bank-details.php',
                array(
                    'user_id' => $user_id,
                ),
                'wc-vendors/dashboard/settings/',
                WCV_PLUGIN_DIR . 'templates/dashboard/settings/'
            );
        ?>

        <?php do_action( 'wcvendors_settings_after_bank_details', $user_id ); ?>

    <?php endif; ?>

    <?php

    wc_get_template(
        'shop-name.php',
        array(
            'user_id' => $user_id,
        ),
        'wc-vendors/dashboard/settings/',
        WCV_PLUGIN_DIR . 'templates/dashboard/settings/'
    );

    do_action( 'wcvendors_settings_after_shop_name' );

    wc_get_template(
        'seller-info.php',
        array(
            'global_html' => $global_html,
            'has_html'    => $has_html,
            'seller_info' => $seller_info,
        ),
        'wc-vendors/dashboard/settings/',
        WCV_PLUGIN_DIR . 'templates/dashboard/settings/'
    );

    do_action( 'wcvendors_settings_after_seller_info' );

    if ( 'false' !== $shop_description ) {
        wc_get_template(
            'shop-description.php',
            array(
                'description' => $description,
                'global_html' => $global_html,
                'has_html'    => $has_html,
                'shop_page'   => $shop_page,
                'user_id'     => $user_id,
            ),
            'wc-vendors/dashboard/settings/',
            WCV_PLUGIN_DIR . 'templates/dashboard/settings/'
        );

        do_action( 'wcvendors_settings_after_shop_description' );
    }
    ?>

    <?php wp_nonce_field( 'save-shop-settings', 'wc-product-vendor-nonce' ); ?>
    <input type="submit" class="btn btn-inverse btn-small" style="float:none;" name="vendor_application_submit"
            value="<?php esc_html_e( 'Save', 'wc-vendors' ); ?>"/>
</form>
