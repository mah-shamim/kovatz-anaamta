<?php
/**
 * My Account Logout widget Template.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
	<?php if ( $endpoint == 'customer-logout' ): ?>
		<div class="jet-woo-builder-customer-logout">
			<a href="<?php echo esc_url( wc_logout_url( wc_get_page_permalink( 'myaccount' ) ) ); ?>"><?php echo esc_html( $label ); ?></a>
		</div>
	<?php endif; ?>
<?php endforeach; ?>