<?php
/**
 * Downloads
 *
 * Shows downloads on the account page.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/woocommerce/myaccount/downloads.php.
 *
 * @version 7.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$template = apply_filters( 'jet-woo-builder/current-template/template-id', jet_woo_builder()->woocommerce->get_custom_myaccount_downloads_template() );

jet_woo_builder()->admin_bar->register_post_item( $template );
?>

<div class="jet-woo-account-downloads-content">
	<?php echo jet_woo_builder_template_functions()->get_woo_builder_content( $template ); ?>
</div>
