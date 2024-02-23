<?php
/**
 * Vendor Mini Header Template
 *
 * THIS FILE WILL LOAD ON VENDORS INDIVIDUAL PRODUCT URLs (such as yourdomain.com/shop/product-name/)
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/front/vendor-mini-header.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/Emails/HTML
 * @version       2.0.0
 *
 *
 * Template Variables available
 * $vendor :            For pulling additional user details from vendor account.  This is an array.
 * $vendor_id  :        current vendor user id number
 * $shop_name :        Store/Shop Name (From Vendor Dashboard Shop Settings)
 * $shop_description : Shop Description (completely sanitized) (From Vendor Dashboard Shop Settings)
 * $seller_info :        Seller Info(From Vendor Dashboard Shop Settings)
 * $vendor_email :        Vendors email address
 * $vendor_login :    Vendors user_login name
 * $vendor_shop_link : URL to the vendors store
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wcv-shop-header-name"><?php echo $shop_name; ?></div>
<div class="wcv_shop_description">
	<?php echo $shop_description; ?>
</div>
