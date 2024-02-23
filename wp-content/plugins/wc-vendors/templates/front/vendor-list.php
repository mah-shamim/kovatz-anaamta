<?php
/**
 * Vendor List Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/front/vendors-list.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/Front
 * @version       2.0.0
 * @version       2.4.2 - More responsive
 *
 *  Template Variables available
 *  $display_mode : Vendor list display mode grid or list
 *  $search_term : The search term to use for filtering
 *  $vendors_list : The vendors to display
 *  $vendor_count : The total number of vendors
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php
	/**
	 * wcvendors_before_vendor_list_loop hook.
	 *
	 * @hooked wcvendors_before_vendor_list_loop - 10
	 */
	do_action( 'wcvendors_vendor_list_filter', $display_mode, $search_term, $vendors_count );
?>
<?php
	/**
	 * wcvendors_before_vendor_list hook.
	 *
	 * @hooked wcvendors_before_vendor_list - 10
	 */
	do_action( 'wcvendors_before_vendor_list', $display_mode );
?>
	<?php
		/**
		 * wcvendors_before_vendor_list_loop hook.
		 *
		 * @hooked wcvendors_before_vendor_list_loop - 10
		 */
		do_action( 'wcvendors_vendor_list_loop', $vendors_list );
	?>
<?php
	/**
	 * wcvendors_after_vendor_list hook.
	 *
	 * @hooked wcvendors_after_vendor_list - 10
	 */
	do_action( 'wcvendors_after_vendor_list' );
