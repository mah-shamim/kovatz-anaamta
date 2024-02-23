<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/woocommerce/myaccount/form-login.php.
 *
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

$template = apply_filters( 'jet-woo-builder/current-template/template-id', jet_woo_builder()->woocommerce->get_custom_form_login_template() );

jet_woo_builder()->admin_bar->register_post_item( $template );
?>

<div class="jet-woo-builder-woocommerce-myaccount-login-page">

	<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

	<div id="customer_login">
		<?php echo jet_woo_builder_template_functions()->get_woo_builder_content( $template ); ?>
	</div>

	<?php do_action( 'woocommerce_after_customer_login_form' ); ?>

</div>

