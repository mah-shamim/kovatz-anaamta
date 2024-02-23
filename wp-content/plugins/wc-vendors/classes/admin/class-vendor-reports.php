<?php
/**
 * Vendor Reports Class
 *
 * @class   WCV_Vendor_Reports
 * @package WCVendors
 * @category    Class
 *
 * @version 2.4.8
 * @since   2.4.8 - Added HPOS compatibility.
 */
class WCV_Vendor_Reports {
	/**
	 * Vendor ID
	 *
	 * @var int
	 */
	private $vendor_id;

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->vendor_id = ! current_user_can( 'manage_woocommerce' ) ? wp_get_current_user()->ID : 0;

		if ( ! empty( $this->vendor_id ) ) {
			add_filter( 'woocommerce_reports_charts', array( $this, 'filter_tabs' ), 99 );
			add_filter( 'woocommerce_json_search_found_products', array( $this, 'filter_products_json' ) );
			add_filter( 'woocommerce_reports_product_sales_order_items', array( $this, 'filter_products' ) );
			add_filter( 'woocommerce_reports_top_sellers_order_items', array( $this, 'filter_products' ) );
			add_filter( 'woocommerce_reports_top_earners_order_items', array( $this, 'filter_products' ) );
		}
	}

	/**
	 * Show only reports that are useful to a vendor
	 *
	 * @param array $tabs The list of tabs.
	 *
	 * @return array
	 */
	public function filter_tabs( $tabs ) {

		global $woocommerce;

		$remove = array(
			'woocommerce_sales_overview',
			'woocommerce_daily_sales',
			'woocommerce_monthly_sales',
			'woocommerce_monthly_taxes',
			'woocommerce_category_sales',
			'woocommerce_coupon_sales',
		);

		$reports = $tabs['orders']['reports'];

		foreach ( $reports as $key => $chart ) {
			if ( 'coupon_usage' === $key ) {
				unset( $tabs['orders']['reports'][ $key ] );
			}
		}

		// These are admin tabs.
		$return = array(
			'orders' => $tabs['orders'],
		);

		return $return;
	}


	/**
	 * Filter products based on current vendor
	 *
	 * @version 2.1.14
	 * @since   2.4.8 - Added HPOS compatibility.
	 * @param array $orders List of orders.
	 *
	 * @return array
	 */
	public function filter_products( $orders ) {
    $products = WCV_Vendors::get_vendor_products( $this->vendor_id );

		$ids = array();
		foreach ( $products as $product ) {
			$ids[] = wcv_hpos_enabled() ? $product->get_id() : $product->ID;
		}

		foreach ( $orders as $key => $order ) {

			if ( ! in_array( $order->product_id, $ids, true ) ) {
				unset( $orders[ $key ] );
				continue;
			} elseif ( ! empty( $order->line_total ) ) {
					$orders[ $key ]->line_total = WCV_Commission::calculate_commission(
						$order->line_total,
						$order->product_id,
						$order,
						$order->qty
					);
			}
		}

		return $orders;
	}


	/**
	 * Filter products based on current vendor
	 *
	 * @version 2.4.8
	 * @since  2.4.8 - Added HPOS compatibility.
	 *
	 * @param array $products The list of products.
	 *
	 * @return array[WC_Product|WP_Post]
	 */
	public function filter_products_json( $products ) {

		$vendor_products = WCV_Vendors::get_vendor_products( $this->vendor_id );

		$ids = array();
		foreach ( $vendor_products as $vendor_product ) {
			if ( wcv_hpos_enabled() ) {
				$ids[ $vendor_product->get_id() ] = $vendor_product->get_title();
			} else {
				$ids[ $vendor_product->ID ] = $vendor_product->post_title;
			}
    }

		return array_intersect_key( $products, $ids );
	}
}
