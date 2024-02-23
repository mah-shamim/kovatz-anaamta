<?php
/**
 * Vendor related functions.
 *
 * @since 2.4.0
 * @version 2.4.0
 * @since   2.4.8 - Addedd HPOS Compatibility.
 */

/**
 * Mark an order shipped for a particular vendor
 *
 * @param WC_Order $order The order to mark.
 * @param int $vendor_id The vendor id to mark shipped.
 *
 * @since 2.4.0
 */

if ( ! function_exists( 'wcv_mark_vendor_shipped' ) ) {

    /**
     * Mark vendor order shipped
     *
     * @param WC_Order $order     The order object.
     * @param int      $vendor_id The vendor id.
     * @return boolean $marked_shipped Whether the order was marked shipped or not.
     */
    function wcv_mark_vendor_shipped( $order, $vendor_id ) {

        if ( ! $order ) {
            return false;
        }

        // Only process orders with the required order status.
        if ( ! $order->has_status( wcv_marked_shipped_order_status() ) ) {
            return false;
        }

        $shippers = array_filter( (array) $order->get_meta( 'wc_pv_shipped', true ) );

        // If not in the shippers array mark as shipped otherwise do nothing.
        if ( ! in_array( (int) $vendor_id, $shippers, true ) ) {

            $shippers[] = $vendor_id;

            if ( ! empty( $mails ) ) {
                $email = WC()->mailer()->emails['WC_Email_Notify_Shipped'];
                $email->trigger( $order->get_id(), $vendor_id );
            }

            do_action( 'wcvendors_vendor_ship', $order->get_id(), $vendor_id, $order );

            $shop_name = WCV_Vendors::get_vendor_shop_name( $vendor_id );
            $order->add_order_note(
                apply_filters(
                    'wcvendors_vendor_shipped_note',
                    sprintf(
                    // translators: %s: vendor shop name.
                        __( '%s has marked as shipped. ', 'wc-vendors' ),
                        $shop_name
                    ),
                    $vendor_id,
                    $shop_name
                )
            );

        }

        $order->update_meta_data( 'wc_pv_shipped', $shippers );
        $order->save_meta_data();

        return true;
    }
}

/**
 * Mark an order unshipped for a particular vendor
 *
 * @param WC_Order $order The order to mark.
 * @param int $vendor_id The vendor id to mark unshipped.
 *
 * @since 2.4.9
 */
if ( ! function_exists( 'wcv_mark_vendor_unshipped' ) ) {

    /**
     * Mark vendor order unshipped
     *
     * @param WC_Order $order     The order object.
     * @param int      $vendor_id The vendor id.
     * @return boolean $marked_shipped Whether the order was marked shipped or not.
     */
    function wcv_mark_vendor_unshipped( $order, $vendor_id ) {

        if ( ! $order ) {
            return false;
        }

        // Only process orders with the required order status.
        if ( ! $order->has_status( wcv_marked_unshipped_order_status() ) ) {
            return false;
        }

        $shippers = array_filter( (array) $order->get_meta( 'wc_pv_shipped', true ) );

        // If in the shippers array mark as unshipped otherwise do nothing.
        if ( in_array( (int) $vendor_id, $shippers, true ) ) {

            $shippers = array_diff( $shippers, array( $vendor_id ) );

            do_action( 'wcvendors_vendor_unship', $order->get_id(), $vendor_id, $order );

            $shop_name = WCV_Vendors::get_vendor_shop_name( $vendor_id );
            $order->add_order_note(
                apply_filters(
                    'wcvendors_vendor_unshipped_note',
                    sprintf(
                    // translators: %s: vendor shop name.
                        __( '%s has marked as unshipped. ', 'wc-vendors' ),
                        $shop_name
                    ),
                    $vendor_id,
                    $shop_name
                )
            );

        }

        $order->update_meta_data( 'wc_pv_shipped', $shippers );
        $order->save_meta_data();

        return true;
    }
}


/**
 * Mark an order shipped for all vendors
 *
 * @param WC_Order $order The order to mark all vendors shipped for.
 *
 * @since 2.4.0
 */

if ( ! function_exists( 'wcv_mark_order_shipped' ) ) {
    /**
     * Mark order as shipped.
     *
     * @param WC_Order $order The order object.
     * @return void
     * @version 1.0.0
     * @since   1.0.0
     */
    function wcv_mark_order_shipped( $order ) {
        $order = 'shop_order_vendor' === $order->get_type() ? $order : wc_get_order( $order );

        $vendor_ids = (array) $order->get_meta( 'wcv_vendor_ids', true );

        foreach ( $vendor_ids as $vendor_id ) {
            wcv_mark_vendor_shipped( $order, $vendor_id );
        }
    }
}

/**
 * Mark an order unshipped for all vendors
 *
 * @param WC_Order $order The order to mark all vendors unshipped for.
 *
 * @since 2.4.9
 */
if ( ! function_exists( 'wcv_mark_order_unshipped' ) ) {
    /**
     * Mark order as unshipped.
     *
     * @param WC_Order $order The order object.
     * @return void
     * @version 2.4.9
     * @since   2.4.9
     */
    function wcv_mark_order_unshipped( $order ) {
        $order = 'shop_order_vendor' === $order->get_type() ? $order : wc_get_order( $order );

        $vendor_ids = (array) $order->get_meta( 'wcv_vendor_ids', true );

        foreach ( $vendor_ids as $vendor_id ) {
            wcv_mark_vendor_unshipped( $order, $vendor_id );
        }
    }
}

/**
 * Get the formatted shipped text to output on the WooCommerce order pages.
 *
 * @param WC_Order $order The WooCommerce order being referenced.
 * @param boolean $order_edit Is this the order edit screen.
 *
 * @since 2.4.0
 */
if ( ! function_exists( 'wcv_get_order_vendors_shipped_text' ) ) {
    /**
     * Get order shipped text.
     *
     * @param WC_Order $order      The order object.
     * @param boolean  $order_edit Is this the order edit screen.
     * @return bool|string
     */
    function wcv_get_order_vendors_shipped_text( $order, $order_edit = false ) {

        $vendors = (array) $order->get_meta( 'wcv_vendor_ids' );

        $vendors = array_filter( $vendors );

        if ( empty( $vendors ) ) {
            $vendors = array_filter( WCV_Vendors::get_vendors_from_order( $order ) );
        }

        $shipped = (array) $order->get_meta( 'wc_pv_shipped', true );

        ob_start();
        ?>
        <div class="wcv-mark-shipped">
            <h4><?php esc_html_e( 'Vendors shipped', 'wc-vendors' ); ?></h4>
            
            <?php

            foreach ( $vendors as $vendor_id ) :
                echo esc_attr( in_array( $vendor_id, $shipped, true ) ? '&#10004; ' : '&#10005; ' );
                ?>
                <span><?php echo esc_html( WCV_Vendors::get_vendor_shop_name( $vendor_id ) ); ?></span>
                <?php if ( wcv_vendor_shipped( $order, $vendor_id ) && $order->has_status( wcv_marked_unshipped_order_status() ) ) : ?>
                    <?php
                        $mark_vendor_unshipped_url = wp_nonce_url(
                            admin_url(
                                'admin-ajax.php?action=wcvendors_mark_order_vendor_unshipped&order_id=' . $order->get_id() . '&vendor_id=' . $vendor_id
                            ),
                            'wcvendors-mark-order-vendor-unshipped'
                        );
                    ?>
                    <a class="" href="<?php echo esc_url_raw( $mark_vendor_unshipped_url ); ?>">
                        <?php esc_html_e( 'Mark unshipped', 'wc-vendors' ); ?>
                    </a>
                <?php endif; ?>
                <?php
                if ( $order_edit && $order->has_status( wcv_marked_shipped_order_status() ) && ! wcv_vendor_shipped( $order, $vendor_id ) ) :
                    $mark_vendor_shipped_url = wp_nonce_url(
                        admin_url(
                            'admin-ajax.php?action=wcvendors_mark_order_vendor_shipped&order_id=' . $order->get_id() . '&vendor_id=' . $vendor_id
                        ),
                        'wcvendors-mark-order-vendor-shipped'
                    );
                ?>
                    <a class="" href="<?php echo esc_url_raw( $mark_vendor_shipped_url ); ?>">
                        <?php esc_html_e( 'Mark shipped', 'wc-vendors' ); ?>
                    </a>
                <?php endif; ?>
                <br />
            <?php endforeach; ?>
        </div>
        <?php

        return ob_get_clean();
    }
}

/**
 * Check of all vendors have shipped for the order
 *
 * @param WC_Order $order The order to check
 * @return boolean $all_shipped if all vendors have shipped
 *
 * @since 2.4.0
 */
if ( ! function_exists( 'wcv_all_vendors_shipped' ) ) {
    /**
     * Check if all vendors have shipped.
     *
     * @param WC_Order $order The order to check.
     * @return bool
     */
    function wcv_all_vendors_shipped( $order ) {
        $vendor_ids  = (array) $order->get_meta( 'wcv_vendor_ids' );
        $shipped     = array_filter( (array) $order->get_meta( 'wc_pv_shipped', true ) );
        $all_shipped = empty( array_diff( $vendor_ids, $shipped ) );

        return $all_shipped;
    }
}

/**
 * Check of all vendors have shipped for the order
 *
 * @param WC_Order $order The order to check
 * @return boolean $all_shipped if all vendors have shipped
 *
 * @since 2.4.0
 */
if ( ! function_exists( 'wcv_vendor_shipped' ) ) {
    /**
     * Check if a vendor has shipped the order.
     *
     * @param WC_Order $order The order to check.
     * @param int      $vendor_id The vendor id.
     * @return bool
     */
    function wcv_vendor_shipped( $order, $vendor_id ) {
        $shipped        = array_filter( (array) $order->get_meta( 'wc_pv_shipped', true ) );
        $vendor_shipped = in_array( $vendor_id, $shipped, true );
        return $vendor_shipped;
    }
}


/**
 * Define the order status's that can be marked shipped
 *
 * @return array $status's array of order status's
 *
 * @since 2.4.0
 */
function wcv_marked_shipped_order_status() {
    return apply_filters( 'wcvendors_order_mark_shipped_statuses', array( 'completed', 'processing' ) );
}

/**
 * Define the order status's that can be marked unshipped
 *
 * @return array $status's array of order status's
 *
 * @since 2.4.9
 */
function wcv_marked_unshipped_order_status() {
    return apply_filters( 'wcvendors_order_mark_unshipped_statuses', array( 'processing' ) );
}

if ( ! function_exists( 'wcv_get_vendor_avatar' ) ) {
    /**
     * Get vendors avatar
     *
     * @param  int $vendor_id The vendor id.
     */
    function wcv_get_vendor_avatar( $vendor_id ) {
        $avatar_source = get_option( 'wcvendors_display_vendors_avatar_source', 'mystery' );
        $avatar_size   = apply_filters( 'wcvendors_vendor_avatar_size', 200 );

        $vendor_avatar = get_avatar( $vendor_id, $avatar_size, $avatar_source, '', array( 'class' => 'wcv-avatar' ) );
        return apply_filters( 'wcvendors_vendor_avatar', $vendor_avatar, $vendor_id, $avatar_size, $avatar_source );
    }
}
