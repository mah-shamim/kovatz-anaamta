<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/woocommerce/myaccount/dashboard.php.
 *
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

$template = apply_filters( 'jet-woo-builder/current-template/template-id', jet_woo_builder()->woocommerce->get_custom_myaccount_dashboard_template() );

jet_woo_builder()->admin_bar->register_post_item( $template );
?>

<div class="jet-woo-account-dashboard-content">
	<?php echo jet_woo_builder_template_functions()->get_woo_builder_content( $template ); ?>
</div>
