<?php
/**
 * Plugin box template
 *
 * @since 2.4.9
 * @version 2.4.9
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<li class="product wcv_product-border">
    <div class="product-addon-head">
        <div class="product-addon-head-icon">
            <img src="<?php echo esc_url( $plugin_data['logo'] ); ?>" alt="<?php echo esc_attr( $plugin_data['name'] ); ?>" />
        </div>
        <div class="product-addon-head-content">
            <h2 class="product-addon-head-content-title"><?php echo esc_html( $plugin_data['name'] ); ?></h2>
            <p class="product-addon-head-content-desc">
                <?php echo esc_html( $plugin_data['desc'] ); ?>
            </p>
        </div>
    </div>
    <div class="product-addon-bottom">
        <?php if ( wcv_is_plugin_installed( $plugin_data['base_name'] ) ) : ?>
            <button class="product-addons-button product-addons-button-solid installed">
                <span class="product-addons-button-text"><?php esc_html_e( 'Installed', 'wc-vendors' ); ?></span>
            </button>
        <?php elseif ( isset( $plugin_data['upgrade_link'] ) ) : ?>
            <a title="Upgrade to <?php echo esc_attr( $plugin_data['name'] ); ?>" href="<?php echo esc_url( $plugin_data['upgrade_link'] ); ?>" class="product-addons-button product-addons-button-solid"><?php esc_html_e( 'Upgrade Now', 'wc-vendors' ); ?></a>
        <?php else : ?>
            <button class="product-addons-button product-addons-button-solid product-addons-button-install" data-plugin_slug="<?php echo esc_attr( $plugin_slug ); ?>">
            <span class="wcv-loading-spinner"></span>
            <span class="product-addons-button-text"><?php esc_html_e( 'Install Now', 'wc-vendors' ); ?></span>
        </button>
        <span class="product-addons-install-status"></span>
        <?php endif; ?>
    </div>
</li>
