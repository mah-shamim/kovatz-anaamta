<?php
/**
 * Vendor List Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/front/vendors-list-loop.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/Front
 * @since         2.4.2
 * @version       2.4.2 - More responsive
 *
 *  Template Variables available
 *  $shop_name : pv_shop_name
 *  $shop_description : pv_shop_description (completely sanitized)
 *  $shop_link : the vendor shop link
 *  $vendor_id  : current vendor id for customization
 *  $avatar : the vendor avatar
 *  $phone : the vendor store phone number
 *  $address : the vendor store address
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="vendor_list">
	<div class="vendor_list_avatar">
		<a href="<?php echo esc_url( $shop_link ); ?>"><?php echo $avatar; ?></a>
	</div>
	<div class="vendor_list_info">
		<h3 class="vendor_list--shop-name">
			<a href="<?php echo esc_url( $shop_link ); ?>"><?php echo esc_html( $shop_name ); ?></a>
		</h3>
		<small class="vendors_list--shop-phone"><span class="dashicons dashicons-smartphone"></span><span><?php echo esc_html( $phone ); ?></span></small> <br/>
		<small class="vendors_list--shop-address"><span class="dashicons dashicons-location"></span><span><?php echo esc_html( $address ); ?></span></small><br/>
		<a class="button vendors_list--shop-link" href="<?php echo esc_url( $shop_link ); ?>"><?php esc_html_e( 'Visit Store', 'wc-vendors' ); ?></a>
	</div>

</div>
