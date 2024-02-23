<?php

/**
 * Shipping functions
 *
 * @author  Matt Gates <http://mgates.me>, WC Vendors <http://wcvendors.com>
 * @package WCVendors
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shipping class
 *
 * @class    WCV_Shipping
 * @version  2.0.0
 * @package  WCVendors/Classes
 */
class WCV_Shipping {
    /**
     * This shipping rates.
     *
     * @var array
     * @since 2.0.0
     */
    public static $trs2_shipping_rates;

    /**
     * The shipping calculation type.
     *
     * @var string
     * @since 2.0.0
     */
    public static $trs2_shipping_calc_type;

    /**
     * The shipping costs
     *
     * @var array
     * @version 1.0.0
     * @since   1.0.0
     */
    public static $pps_shipping_costs = array();

    /**
     * Constructor
     */
    public function __construct() {

        // Table Rate Shipping 2 by WooThemes.
        if ( function_exists( 'woocommerce_get_shipping_method_table_rate' ) ) {
        add_action( 'woocommerce_checkout_update_order_meta', array( 'WCV_Shipping', 'trs2_add_shipping_data' ), 1, 1 );
        add_action( 'wc_trs2_matched_rates', array( 'WCV_Shipping', 'trs2_store_shipping_data' ), 10, 3 );
        }
    }

    /**
     * Get the shipping due for a product
     *
     * @param int          $order_id    The order ID.
     * @param array|object $order_item The order item.
     * @param int          $author     The product author.
     * @param int          $product_id The product ID.
     *
     * @return array
     */
    public static function get_shipping_due( $order_id, $order_item, $author, $product_id = 0 ) {

        $shipping_costs = array(
            'amount' => 0,
            'tax'    => 0,
        );
        $shipping_due   = 0;
        $method         = '';
        $_product       = wc_get_product( $order_item['product_id'] );
        $order          = wc_get_order( $order_id );
        $tax_class      = $order_item->get_tax_class();

        if ( $_product && $_product->needs_shipping() && ! $_product->is_downloadable() ) {

            // Get Shipping methods.
            $shipping_methods = $order->get_shipping_methods();

            // TODO: Currently this only allows one shipping method per order, this definitely needs changing.
            foreach ( $shipping_methods as $shipping_method ) {
                $method = $shipping_method['method_id'];
                break;
            }

            // Per Product Shipping.
            if ( ( class_exists( 'WC_Shipping_Per_Product_Init' ) || function_exists( 'woocommerce_per_product_shipping' ) ) && 'per_product' === $method ) {
                $shipping_costs = self::pps_get_due( $order_id, $order_item );

            // Local Delivery.
            } elseif ( 'local_delivery' === $method ) {
                $local_delivery = get_option( 'woocommerce_local_delivery_settings' );

                if ( 'product' === $local_delivery['type'] ) {

                    $shipping_costs['amount'] = $order_item['qty'] * $local_delivery['fee'];
                    $shipping_costs['tax']    = self::calculate_shipping_tax( $shipping_costs['amount'], $order );
                }

                // International Delivery.
                } elseif ( 'international_delivery' === $method ) {

                $int_delivery = get_option( 'woocommerce_international_delivery_settings' );

                if ( 'item' === $int_delivery['type'] ) {
                    $WC_Shipping_International_Delivery = new WC_Shipping_International_Delivery();
                    $fee                                = $WC_Shipping_International_Delivery->get_fee( $int_delivery['fee'], $_product->get_price() );
                    $shipping_costs['amount']           = ( $int_delivery['cost'] + $fee ) * $order_item['qty'];
                    $shipping_costs['tax']              = ( 'taxable' === $int_delivery['tax_status'] ) ? self::calculate_shipping_tax( $shipping_costs['amount'], $order, $tax_class ) : 0;
                }
            }
        }

        $shipping_costs = apply_filters( 'wcvendors_shipping_due', $shipping_costs, $order_id, $order_item, $author, $product_id );

        return $shipping_costs;
    }


    /**
     * Get dues for product.
     *
     * @param int        $order_id The order id.
     * @param WC_Product $product  The product.
     *
     * @return array
     */
    public static function pps_get_due( $order_id, $product ) {

        $item_shipping_cost = 0;
        $shipping_costs     = array();
        $settings           = get_option( 'woocommerce_per_product_settings' );
        $taxable            = $settings['tax_status'];
        $order              = wc_get_order( $order_id );
        $tax_class          = $product->get_tax_class();

        $shipping_country  = $order->get_shipping_country();
        $shipping_state    = $order->get_shipping_state();
        $shipping_postcode = $order->get_shipping_postcode();

        $package['destination']['country']  = $shipping_country;
        $package['destination']['state']    = $shipping_state;
        $package['destination']['postcode'] = $shipping_postcode;
        $product_id                         = ! empty( $product['variation_id'] ) ? $product['variation_id'] : $product['product_id'];

        if ( ! empty( $product['variation_id'] ) ) {
            $rule = woocommerce_per_product_shipping_get_matching_rule( $product['variation_id'], $package );
        }

        if ( empty( $rule ) ) {
            $rule = woocommerce_per_product_shipping_get_matching_rule( $product['product_id'], $package );
        }

        if ( ! empty( $rule ) ) {
            $item_shipping_cost += $rule->rule_item_cost * $product['qty'];

            if ( ! empty( self::$pps_shipping_costs[ $order_id ] ) && ! in_array( $rule->rule_id, self::$pps_shipping_costs[ $order_id ], true ) ) {
                $item_shipping_cost += $rule->rule_cost;
            } elseif ( empty( self::$pps_shipping_costs[ $order_id ] ) ) {
                $item_shipping_cost += $rule->rule_cost;
            }

            self::$pps_shipping_costs[ $order_id ][] = $rule->rule_id;
        }

        $shipping_costs['amount'] = $item_shipping_cost;
        $shipping_costs['tax']    = ( 'taxable' === $taxable ) ? self::calculate_shipping_tax( $item_shipping_cost, $order, $tax_class ) : 0;

        return $shipping_costs;
    }


    /**
     * Calculate the shipping tax due for the product
     *
     * @version 2.1.3
     */

    /**
     * Calculate the shipping tax due for the product
     *
     * @param number          $shipping_amount The shipping amount.
     * @param WC_Order|string $order           The order.
     * @param string          $tax_class       he tax class.
     * @return float
     * @version 2.4.8
     * @since   2.1.3
     */
    public static function calculate_shipping_tax( $shipping_amount, $order = '', $tax_class = '' ) {

        $tax_based_on   = get_option( 'woocommerce_tax_based_on' );
        $wc_tax_enabled = get_option( 'woocommerce_calc_taxes' );

        if ( ! is_a( $order, 'WC_Order' ) ) {
            $location = WC_Geolocation::geolocate_ip();

            $shipping_city     = '';
            $shipping_country  = $location['country'];
            $shipping_state    = $location['state'];
            $shipping_postcode = '';
            $billing_city      = '';
            $billing_country   = $location['country'];
            $billing_state     = $location['state'];
            $billing_postcode  = '';
        } else {
            $shipping_city     = $order->get_shipping_city();
            $shipping_country  = $order->get_shipping_country();
            $shipping_state    = $order->get_shipping_state();
            $shipping_postcode = $order->get_shipping_postcode();
            $billing_city      = $order->get_billing_city();
            $billing_country   = $order->get_billing_country();
            $billing_state     = $order->get_billing_state();
            $billing_postcode  = $order->get_billing_postcode();
        }

        $woocommerce_shipping_tax_class = get_option( 'woocommerce_shipping_tax_class' );
        $tax_class                      = ( 'inherit' === $woocommerce_shipping_tax_class ) ? $tax_class : $woocommerce_shipping_tax_class;

        // if taxes aren't enabled don't calculate them.
        if ( 'no' === $wc_tax_enabled ) {
            return 0;
        }

        if ( 'base' === $tax_based_on ) {

            $default  = wc_get_base_location();
            $country  = $default['country'];
            $state    = $default['state'];
            $postcode = '';
            $city     = '';

        } elseif ( 'billing' === $tax_based_on ) {

            $country  = $billing_country;
            $state    = $billing_state;
            $postcode = $billing_postcode;
            $city     = $billing_city;

        } else {

            $country  = $shipping_country;
            $state    = $shipping_state;
            $postcode = $shipping_postcode;
            $city     = $shipping_city;

        }

        // Now calculate shipping tax.
        $matched_tax_rates = array();

        $tax_rates = WC_Tax::find_rates(
            array(
                'country'   => $country,
                'state'     => $state,
                'postcode'  => $postcode,
                'city'      => $city,
                'tax_class' => $tax_class,
            )
        );

        if ( $tax_rates ) {
            foreach ( $tax_rates as $key => $rate ) {
            if ( isset( $rate['shipping'] ) && wc_string_to_bool( $rate['shipping'] ) ) {
                $matched_tax_rates[ $key ] = $rate;
            }
            }
        }

        $shipping_taxes     = WC_Tax::calc_shipping_tax( $shipping_amount, $matched_tax_rates );
        $shipping_tax_total = WC_Tax::round( array_sum( $shipping_taxes ) );

        return $shipping_tax_total;
    }

    /**
     * Clear transients.
     */
    public function trs2_clear_transients() {
        if ( is_checkout() ) {
            wc_delete_product_transients();
        }
    }


    /**
     * Get the shipping due for the order.
     *
     * @param int $order_id   The order id.
     * @param int $product_id The product id.
     *
     * @return void
     */
    public function trs2_get_due( $order_id, $product_id ) {

        if ( ! function_exists( 'woocommerce_get_shipping_method_table_rate' ) ) {
            return;
        }

        $shipping_due = 0;

        self::trs2_retrieve_shipping_data( $order_id );
        if ( ! empty( self::$trs2_shipping_calc_type ) ) {

            $ship_id = ( 'class' === self::$trs2_shipping_calc_type ) ? wc_get_product( $product_id )->get_shipping_class_id() : $product_id;

            if ( ! empty( self::$trs2_shipping_rates[ $ship_id ] ) ) {
            $shipping_due = self::$trs2_shipping_rates[ $ship_id ];
            unset( self::$trs2_shipping_rates[ $ship_id ] );
            }
        }

        return $shipping_due;
    }


    /**
     * Retrieve shipping data from the order
     *
     * @since 2.4.8 - Add HPOS support
     * @version 2.4.8
     *
     * @param int $order_id Order ID.
     */
    public function trs2_retrieve_shipping_data( $order_id ) {

        $order = wc_get_order( $order_id );

        if ( ! empty( self::$trs2_shipping_rates ) ) {
            return;
        }

        $shipping_rates                = (array) $order->get_meta( '_wcvendors_trs2_shipping_rates', true );
        self::$trs2_shipping_rates     = array_filter( $shipping_rates );
        self::$trs2_shipping_calc_type = $order->get_meta( '_wcvendors_trs2_shipping_calc_type' );
    }

    /**
     * Store the shipping data in the session
     *
     * @param string $type     The type of shipping.
     * @param array  $rates    The rates.
     * @param mixed  $per_item The per item.
     */
    public function trs2_store_shipping_data( $type, $rates, $per_item ) {

        global $woocommerce;

        $types = (array) $woocommerce->session->trs2_shipping_class_type;
        $items = (array) $woocommerce->session->trs2_shipping_rates;

        $types[] = $type;
        $items[] = $per_item;

        $woocommerce->session->trs2_shipping_class_type = $types;
        $woocommerce->session->trs2_shipping_rates      = $items;
    }


    /**
     * Store the shipping data in the order
     *
     * @param int $order_id The order ID.
     *
     * @return bool|void
     */
    public function trs2_add_shipping_data( $order_id ) {

        global $woocommerce;

        if ( empty( $woocommerce->session->trs2_shipping_rates ) ) {
            return false;
        }

        $order = wc_get_order( $order_id );

        foreach ( $woocommerce->session->trs2_shipping_rates as $key => $shipping_rates ) {

            if ( is_array( $shipping_rates ) && array_sum( $shipping_rates ) === $order->order_shipping ) {
                $shipping_calc_type = $woocommerce->session->trs2_shipping_class_type[ $key ];

                $order->update_meta_data( '_wcvendors_trs2_shipping_rates', $shipping_rates );
                $order->update_meta_data( '_wcvendors_trs2_shipping_calc_type', $shipping_calc_type );
                $order->save_meta_data();

                break;
            }
        }

        unset( $woocommerce->session->trs2_shipping_rates, $woocommerce->session->trs2_shipping_class_type );
    }
}
