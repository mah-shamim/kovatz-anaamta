<?php
/**
 * Admin View: Page - Addons
 *
 * @var string $view
 * @var object $addons
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap woocommerce wcv_addons_wrap">

	<h2><?php esc_html_e( 'Extensions', 'wc-vendors' ); ?></h2>
	<div class="wcv_pro-upsell wcv_product-border">
		<div class="wcv_pro-upsell--left">
			<h3><?php esc_html_e( 'Upgrade to WC Vendors Pro', 'wc-vendors' ); ?></h3>
			<p>
				<?php esc_html_e( 'WC Vendors Pro has all the tools & features to help you build a thriving marketplace that both your customers and Vendors will love. Provide a true frontend multi-vendor experience to rival the big platforms. Grow your marketplace faster with WC Vendors Pro.', 'wc-vendors' ); ?>
			</p>
			<ul class="wcv_checklist">
				<li>
					<?php esc_html_e( 'Unlock full frontend dashboard features', 'wc-vendors' ); ?>
				</li>
				<li>
					<?php esc_html_e( 'Vendor access to sales reports & commissions', 'wc-vendors' ); ?>
				</li>
				<li>
					<?php esc_html_e( 'Extended commission options and fees structures', 'wc-vendors' ); ?>
				</li>
			</ul>
			<?php if ( wcv_is_plugin_installed( 'wc-vendors-pro/wcvendors-pro.php' ) ) : ?>
				<button class="product-addons-button product-addons-button-solid installed">
					<span class="product-addons-button-text"><?php esc_html_e( 'Installed', 'wc-vendors' ); ?></span>
				</button>
			<?php else : ?>
				<a href="https://www.wcvendors.com/pricing/?utm_source=plugin&utm_medium=extensionspage&utm_campaign=upgratetoproboxcta" class="product-addons-button product-addons-button-solid">
					<?php esc_html_e( 'Get WC Vendors Pro and Unlock All Features', 'wc-vendors' ); ?>
				</a>
			<?php endif; ?>
			<br/>
			<a href="https://www.wcvendors.com/wc-vendors-pro/?utm_source=plugin&utm_medium=extensionspage&utm_campaign=learnmoreprolink" style="color: #555555;" alt="<?php echo esc_attr__( 'Learn more about pro features', 'wc-vendors' ); ?>">
				<small><u><?php esc_html_e( 'Learn more about Pro features', 'wc-vendors' ); ?></u></small>
			</a>
		</div>
		<div class="wcv_pro-upsell--right">
			<img src="<?php echo esc_url( WCV_ASSETS_URL . 'images/extensions/pro-upsell.png' ); ?>" alt="WC Vendors Pro Hero Banner" />
		</div>
	</div>

	<ul class="products">
		<?php $plugin_installer->generate_boxes(); ?>
	</ul>
</div>
