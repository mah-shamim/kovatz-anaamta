<?php
/**
 * Admin About Page
 *
 * @version 2.4.9
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap wcv_addons_wrap">
    <h2 class="wcv-page-title"><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <p><?php esc_html_e( 'Hello and welcome to WC Vendors - The Best WooCommerce Multi Vendor Plugin.', 'wc-vendors' ); ?></p>

    <div class="wcv_pro-about wcv_product-border">
		<div class="wcv_pro-about--left">
			<div class="wcv_pro-about--left-content">
				<h3>
					<?php esc_html_e( 'About The Makers - Rymera Web Co', 'wc-vendors' ); ?>
				</h3>
				<p>
					<?php esc_html_e( 'Over the years we\'ve worked thousands of smart store owners that were  frustrated by building innovating WooCommerce solutions to solve even the trickiest of store requirements.', 'wc-vendors' ); ?>
				</p>
				<p>
					<?php esc_html_e( 'We\'re proud to present WC Vendors - a state of the art solution for adding multi-vendor capabilities to easily create a destination where vendors can list and sell their items.', 'wc-vendors' ); ?>
				</p>
				<p>
					<?php esc_html_e( 'WC Vendors is brought to you by the same team that\'s behind Advanced Coupons, Wholesale Suite, and a whole host of other WooCommerce-based solutions. We\'ve been in the WordPress space for over a decade.', 'wc-vendors' ); ?>
				</p>
				<p>
					<?php esc_html_e( 'We\'re thrilled you\'re using our tool and invite you to try our other tools as well.', 'wc-vendors' ); ?>
				</p>
			</div>
        </div>
        <div class="wcv_pro-about--right">
            <img src="<?php echo esc_url( WCV_ASSETS_URL . 'images/rymera-team.jpg' ); ?>"  title="<?php echo esc_attr__( 'Rymera team', 'wc-vendors' ); ?>" alt="<?php echo esc_attr__( 'Rymera team', 'wc-vendors' ); ?>" />
        </div>
    </div>

    <ul class="products">
		<?php $plugin_installer->generate_boxes(); ?>
	</ul>
</div>
