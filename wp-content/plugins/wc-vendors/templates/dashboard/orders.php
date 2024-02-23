<?php
/**
 * Orders Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/dashboard/orders.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/dashboard/
 * @version       2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<script type="text/javascript">
    jQuery(function () {
        jQuery('a.view-items').on('click', function (e) {
            e.preventDefault();
            var id = jQuery(this).closest('tr').data('order-id');

            if (jQuery(this).text() == "<?php esc_html_e( 'Hide items', 'wc-vendors' ); ?>") {
                jQuery(this).text("<?php esc_html_e( 'View items', 'wc-vendors' ); ?>");
            } else {
                jQuery(this).text("<?php esc_html_e( 'Hide items', 'wc-vendors' ); ?>");
            }

            jQuery("#view-items-" + id).fadeToggle();
        });

        jQuery('a.view-order-tracking').on('click', function (e) {
            e.preventDefault();
            var id = jQuery(this).closest('tr').data('order-id');
            jQuery("#view-tracking-" + id).fadeToggle();
        });
    });
</script>

<h2><?php esc_html_e( 'Orders', 'wc-vendors' ); ?></h2>


<?php
if ( function_exists( 'wc_print_notices' ) ) {
    wc_print_notices();
}
?>

<table class="table table-condensed table-vendor-sales-report">
    <thead>
    <tr>
        <th class="product-header"><?php esc_html_e( 'Order', 'wc-vendors' ); ?></th>
        <?php if ( $can_view_address ) : ?>
            <th class="quantity-header"><?php esc_html_e( 'Shipping', 'wc-vendors' ); ?></th>
        <?php endif; ?>
        <th class="commission-header"><?php esc_html_e( 'Total', 'wc-vendors' ); ?></th>
        <th class="rate-header"><?php esc_html_e( 'Date', 'wc-vendors' ); ?></th>
        <th class="rate-header"><?php esc_html_e( 'Links', 'wc-vendors' ); ?></th>
    </thead>
    <tbody>

    <?php
    if ( ! empty( $order_summary ) ) :
        $order_totals = 0;
        $user_id      = get_current_user_id();
        ?>

        <?php
        foreach ( $order_summary as $vendor_order ) :

            $order_totals += $vendor_order->get_total();

            $parent_order    = $vendor_order->get_parent_order();
            $parent_order_id = $parent_order ? $parent_order->get_id() : $vendor_order->get_parent_id();

            $order_id       = $vendor_order->get_id();
            $order_products = WCV_Queries::get_products_for_order( $order_id );
            $valid_items    = array();
            $needs_shipping = false;

            $items = $vendor_order->get_items();

            foreach ( $items as $item ) {
                $valid_items[] = $item;
                // See if product needs shipping.
                $product_id     = $item->get_variation_id() > 0 ? $item->get_variation_id() : $item->get_product_id();
                $product        = wc_get_product( $product_id );
                $needs_shipping = ( ! $product->needs_shipping() || $product->is_downloadable() ) ? $needs_shipping : true;
            }

            $shippers = $parent_order ? (array) $parent_order->get_meta( 'wc_pv_shipped', true ) : array();

            $shipped = in_array( $user_id, $shippers, true );

            $order_date = $vendor_order->get_date_created();

            ?>

            <tr id="order-<?php echo esc_attr( $parent_order_id ); ?>" data-order-id="<?php echo esc_attr( $parent_order_id ); ?>">
                <td><?php echo esc_attr( $parent_order_id ); ?></td>
                <?php if ( $can_view_address ) : ?>
                    <td>
                        <?php

                        $formatted_shipping_address = $parent_order ? $parent_order->get_formatted_shipping_address() : '';

                        $google_maps_link  = 'http://maps.google.com/maps?&q=';
                        $google_maps_link .= rawurlencode(
                            esc_html(
                                preg_replace(
                                    '#<br\s*/?>#i',
                                    ', ',
                                    $formatted_shipping_address
                                )
                            )
                        );
                        $google_maps_link .= '&z=16';

                        echo wp_kses_post(
                            apply_filters(
                                'wcvendors_dashboard_google_maps_link',
                                sprintf(
                                    '<a target="_blank" href="%s">%s</a>',
                                    esc_url( $google_maps_link ),
                                    esc_html( preg_replace( '#<br\s*/?>#i', ', ', $formatted_shipping_address ) )
                                )
                            )
                        );
                ?>
                </td>
                <?php endif; ?>
                <td>
                    <?php echo wp_kses_post( wc_price( $vendor_order->get_total() ) ); ?>
                </td>
                <td><?php echo esc_attr( date_i18n( wc_date_format(), strtotime( $order_date ) ) ); ?></td>
                <td>
                    <?php
                    $order_actions = array(
                        'view' => array(
                            'class'   => 'view-items',
                            'content' => __( 'View items', 'wc-vendors' ),
                        ),
                    );
                    if ( $needs_shipping ) {
                        $order_actions['shipped'] = array(
                            'class'   => 'mark-shipped',
                            'content' => __( 'Mark shipped', 'wc-vendors' ),
                            'url'     => '?wc_pv_mark_shipped=' . esc_attr( $parent_order_id ) . '&_wpnonce=' . wp_create_nonce( 'wc_pv_mark_shipped' ),
                        );
                    }
                    if ( $shipped ) {
                        $order_actions['shipped'] = array(
                            'class'   => 'mark-shipped',
                            'content' => __( 'Shipped', 'wc-vendors' ),
                            'url'     => '#',
                        );
                    }

                    if ( $providers && $needs_shipping ) {
                        $order_actions['tracking'] = array(
                            'class'   => 'view-order-tracking',
                            'content' => __( 'Tracking', 'wc-vendors' ),
                        );
                    }

                    $order_actions = apply_filters( 'wcvendors_order_actions', $order_actions, $vendor_order );

                    if ( $order_actions ) {
                        $output = array();
                        foreach ( $order_actions as $key => $data ) {
                            $output[] = sprintf(
                                '<a href="%s" id="%s" class="%s">%s</a>',
                                ( isset( $data['url'] ) ) ? $data['url'] : '#',
                                ( isset( $data['id'] ) ) ? $data['id'] : $key . '-' . $order_id,
                                ( isset( $data['class'] ) ) ? $data['class'] : '',
                                $data['content']
                            );
                        }
                        echo wp_kses_post( implode( ' | ', $output ) );
                    }
                    ?>
                </td>
            </tr>

            <tr id="view-items-<?php echo esc_attr( $parent_order_id ); ?>" style="display:none;">
                <td colspan="5">
                    <?php
                    $product_id       = '';
                    $refunded         = array();
                    $order_refunded   = $vendor_order->get_total_refunded();
                    $is_full_refunded = $order_refunded === $vendor_order->get_total();

                    foreach ( $valid_items as $key => $item ) :

                        // Get variation data if there is any.
                        $variation_detail = ! empty( $item['variation_id'] ) ? WCV_Orders::get_variation_data( $item['variation_id'] ) : '';
                        $refunded_total   = $vendor_order->get_total_refunded_for_item( $item->get_id() );

                        if ( $is_full_refunded ) {
                            $refunded_total = $item['line_total'];
                        }

                        ?>
                        <?php echo esc_attr( $item['qty'] . 'x ' . $item['name'] ); ?>
                        <?php
                        if ( ! empty( $variation_detail ) ) {
                            echo '<br />' . wp_kses_post( $variation_detail );
                        }

                        if ( $refunded_total > 0 ) {
                            $refunded[] = wc_price( $refunded_total ) . ' - ' . $item['name'];
                        }
                        ?>

                    <?php endforeach; ?>
                    <?php if ( ! empty( $refunded ) && $show_reversed ) : ?>
                        <br/><strong><?php echo esc_html__( 'Refunded:', 'wc-vendors' ); ?></strong>
                        <?php echo esc_attr( implode( ', ', $refunded ) ); ?>
                    <?php endif; ?>

                </td>
            </tr>

            <?php if ( is_array( $providers ) ) : ?>
            <tr id="view-tracking-<?php echo esc_attr( $parent_order_id ); ?>" style="display:none;">
                <td colspan="5">
                    <div class="order-tracking">
                        <?php
                        wc_get_template(
                            'shipping-form.php',
                            array(
                                'order_id'   => $parent_order_id,
                                'product_id' => $product_id,
                                'providers'  => $providers,
                            ),
                            'wc-vendors/orders/shipping/',
                            WCV_PLUGIN_DIR . 'templates/orders/shipping/'
                        );
                        ?>
                    </div>
                </td>
            </tr>
            <?php endif; ?>

        <?php endforeach; ?>

        <tr>
            <td><b><?php esc_html_e( 'Total:', 'wc-vendors' ); ?></b></td>
            <td colspan="4"><?php echo wp_kses_post( wc_price( $order_totals ) ); ?></td>
        </tr>

    <?php else : ?>

        <tr>
            <td colspan="4" style="text-align:center;">
                <?php esc_html_e( 'You have no orders during this period.', 'wc-vendors' ); ?>
            </td>
        </tr>

    <?php endif; ?>

    </tbody>
</table>

<?php
if ( 1 < $total_pages ) :
echo wp_kses_post(
    paginate_links(
        apply_filters(
            'wcvendors_dashboard_pagination_args',
            array(
				'base'     => add_query_arg( 'paged', '%#%' ),
				'format'   => '?paged=%#%',
				'current'  => $paged,
				'total'    => $total_pages,
				'end_size' => 3,
				'mid_size' => 3,
            )
        )
    )
);
endif;
