<?php
/**
 * Shop Description Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/dashboard/settings/shop-description.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/Emails/HTML
 * @version       2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="pv_shop_description_container">
	<p><b><?php _e( 'Shop Description', 'wc-vendors' ); ?></b><br/>
		<?php printf( __( 'This is displayed on your <a href="%s">shop page</a>.', 'wc-vendors' ), $shop_page ); ?>
	</p>

	<p>
		<?php

		if ( $global_html || $has_html ) {
			$old_post        = $GLOBALS['post'];
			$GLOBALS['post'] = 0;
			wp_editor( $description, 'pv_shop_description' );
			$GLOBALS['post'] = $old_post;
		} else {
			?>
			<textarea class="large-text" rows="10" id="pv_shop_description_unhtml" style="width:95%"
			          name="pv_shop_description"><?php echo $description; ?></textarea>
			<?php
		}

		?>
	</p>

</div>
