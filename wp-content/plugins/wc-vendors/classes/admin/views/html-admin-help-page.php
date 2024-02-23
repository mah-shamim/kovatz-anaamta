<?php
/**
 * Admin Help Page
 *
 * @version 2.4.9
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap">
    <h1 class="wcv-page-title"><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <p><?php esc_html_e( 'We\'re here to help you get the most out of WC Vendors.', 'wc-vendors' ); ?></p>
    <div class="wcv_addons_wrap">
        <ul class="products">
            <li class="wcv_product-border wcv-product-help">
                <h3> <?php esc_html_e( 'Knowledge Base', 'wc-vendors' ); ?> </h3>
                <p> <?php esc_html_e( 'Access our self-service help documentation via the Knowledge Base. You\'ll find answers and solutions for a wide range of well known situations. You\'ll also find a Getting Started guide here for the plugin.', 'wc-vendors' ); ?> </p>
                <a href="https://www.wcvendors.com/knowledge-base/?utm_source=plugin&utm_medium=helppage&utm_campaign=openknowledgebasebutton" class="button-primary wcv-help-button">
                    <?php esc_html_e( 'Open Knowledge Base', 'wc-vendors' ); ?>
                </a>
            </li>
            <li class="wcv_product-border wcv-product-help">
                <h3> <?php esc_html_e( 'Free Version WordPress.org Help Forums', 'wc-vendors' ); ?> </h3>
                <p> <?php esc_html_e( 'Our support staff regularly check and help our free users at the official plugin WordPress.org help forums. Submit a post there with your question and we\'ll get back to you as soon as possible.', 'wc-vendors' ); ?> </p>
                <a href="https://wordpress.org/support/plugin/wc-vendors/" class="button-primary wcv-help-button">
                    <?php esc_html_e( 'Visit WordPress.org Forums', 'wc-vendors' ); ?>
                </a>
            </li>
        </ul>
    </div>
</div>
