<?php
/**
 * Admin View: Final Wizard Step.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<h1 class="ready-title"><?php esc_html_e( 'Your marketplace is ready!', 'wc-vendors' ); ?></h1>

<div class="wcvendors-message wcvendors-newsletter">
	<p><?php esc_html_e( 'Enter your details to get free marketplace growth ideas, marketing tips & resources – let us support you on your marketplace creation journey!', 'wc-vendors' ); ?></p>
	<iframe id="fluentform" scrolling="no" width="100%" loading="lazy" height="200px" style="min-height: 250px;width: 100%" frameborder="0" src="https://www.wcvendors.com/in-app-newsletter-signup/" onload="this.style.height=(this.contentWindow.document.body.scrollHeight+40)+'px';"></iframe>
</div>

<h4 class="help-title"><?php esc_html_e( 'You can now start adding products to your marketplace.', 'wc-vendors' ); ?></h4>
<p class="next-steps-help-text">
	<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=product' ) ); ?>" class="button-primary"><?php esc_html_e( 'Add Products', 'wc-vendors' ); ?></a>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=wcv-settings' ) ); ?>" class="button-secondary"><?php esc_html_e( 'View Settings', 'wc-vendors' ); ?></a>
</p>

<h5 class="help-title"><?php esc_html_e( 'Need more help? Read our getting started guide!', 'wc-vendors' ); ?></h5>
<p class="next-steps-help-text"><?php echo wp_kses_post( $help_text ); ?></p>

<div class="wcv-wizard-upgrade-box">
	<h2>Get The Best WooCommerce Marketplace Plugin... <br>Without The High Costs</h2>
	<p>Upgrade now and join over 10,000+ stores who are using WC Vendors Pro to grow their marketplace businesses with confidence.</p>
	<a href="https://www.wcvendors.com/pricing/?utm_source=plugin&utm_medium=setupwizard&utm_campaign=setupupgradetopro" class="button-primary" target="_blank">Upgrade to Pro &rarr;</a>
	<p class="testimonial"><em>“Advanced features for any type of market place, small or big.” <br><img class="stars" src="<?php echo esc_url( $image_assets_url ); ?>icons/5star.png" alt="5 star" /> – @yvesbenini via wordpress.org</em></p>
</div>
