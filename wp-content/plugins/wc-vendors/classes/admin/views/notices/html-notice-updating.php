<?php
/**
 * Admin View: Notice - Update
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$force_update_url = add_query_arg(
    array(
        'force_update_wcvendors' => 'true',
        '_wpnonce'               => wp_create_nonce( 'force_update_wcvendors' ),
    ),
    admin_url( 'admin.php?page=wcv-settings' )
);

?>
<div id="message" class="updated wcvendors-message wc-connect">
    <p>
        <strong><?php esc_html_e( 'WC Vendors data update', 'wc-vendors' ); ?></strong>
        &#8211; <?php esc_html_e( 'Your database is being updated in the background, this may take a while.', 'wc-vendors' ); ?>
        <a href="<?php echo esc_url( $force_update_url ); ?>">
            <?php esc_html_e( 'Click here to check the status.', 'wc-vendors' ); ?>
        </a>
    </p>
</div>
