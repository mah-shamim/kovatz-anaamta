<?php
/**
 * Orders Table Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/orders/orders.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/Orders
 * @version       2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<?php
if ( function_exists( 'wc_print_notices' ) ) {
    wc_print_notices();
}
?>

<h2>
<?php
echo esc_html(
    sprintf(
            // translators: %s The product title.
        __( 'Orders for %s', 'wc-vendors' ),
        wc_get_product( $product_id )->get_title()
    )
);
?>
</h2>

<?php do_action( 'wc_vendors_before_order_detail', $body ); ?>

<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <?php foreach ( $headers as $header ) : ?>
            <th class="<?php echo esc_attr( sanitize_title( $header ) ); ?>"><?php echo esc_attr( $header ); ?></th>
        <?php endforeach; ?>
    </tr>
    </thead>
    <tbody>

    <?php
    foreach ( $body as $order_id => $current_order ) :

        $order_items = ! empty( $items[ $order_id ]['items'] ) ? $items[ $order_id ]['items'] : array();
        $count       = count( $order_items );
        $refund      = isset( $items[ $order_id ]['refund'] ) ? $items[ $order_id ]['refund'] : array();
        ?>

        <tr>

            <?php
            $order_keys  = array_keys( $current_order );
            $first_index = array_shift( $order_keys );
            $last_index  = end( $order_keys );
            foreach ( $current_order as $detail_key => $detail ) :
                if ( $detail_key === $last_index ) {
                    continue;
                }
                ?>
                <?php if ( $detail_key === $first_index ) : ?>

                <td class="<?php echo esc_attr( $detail_key ); ?>"
                    rowspan="<?php echo esc_attr( ( 1 === $count ? 3 : ( $count + 3 ) ) ); ?>">
                        <?php echo esc_attr( $detail ); ?>
                </td>

            <?php else : ?>

                <td class="<?php echo esc_attr( $detail_key ); ?>"  style="<?php echo 'email' === $detail_key ? 'word-break: break-word;' : ''; ?>">
                    <?php echo esc_attr( $detail ); ?>
                </td>

            <?php endif; ?>
            <?php endforeach; ?>

        </tr>

        <tr>

            <?php
            foreach ( $order_items as $item ) {

                wc_get_template(
                    'table-body.php',
                    array(
                        'item'     => $item,
                        'count'    => $count,
                        'order_id' => $order_id,
                        'refund'   => $refund,
                    ),
                    'wc-vendors/orders/',
                    WCV_PLUGIN_DIR . 'templates/orders/'
                );

            }

            if ( ! empty( $current_order['comments'] ) ) {
                $customer_notes = $current_order['comments'];

                wc_get_template(
                    'customer-note.php',
                    array(
						'customer_notes' => $customer_notes,
					),
                    'wc-vendors/orders/customer-note/',
                    WCV_PLUGIN_DIR . 'templates/orders/customer-note/'
                );
            }

            ?>

        <tr>
            <td colspan="100%">

                <?php
                $can_view_comments = 'yes' === get_option( 'wcvendors_capability_order_read_notes', 'no' ) ? true : false;
                $can_add_comments  = 'yes' === get_option( 'wcvendors_capability_order_update_notes', 'no' ) ? true : false;

                if ( $can_view_comments || $can_add_comments ) :
                    $order_comments = $can_view_comments ? $current_order['comments'] : array();
                    ?>
                    <a href="#" class="order-comments-link">
                        <p>
                            <?php
                                echo esc_attr(
                                    sprintf(
                                            // translators: %s The number of comments.
                                        __( 'Comments (%s)', 'wc-vendors' ),
                                        count( $order_comments )
                                    )
                                );
                            ?>
                        </p>
                    </a>

                <div class="order-comments">
                <?php

                endif;

                    if ( $can_view_comments && ! empty( $order_comments ) ) {
                        wc_get_template(
                            'existing-comments.php',
                            array(
                                'comments' => $order_comments,
                            ),
                            'wc-vendors/orders/comments/',
                            WCV_PLUGIN_DIR . 'templates/orders/comments/'
                        );
                    }

                    if ( $can_add_comments ) {
                        wc_get_template(
                            'add-new-comment.php',
                            array(
                                'order_id'   => $order_id,
                                'product_id' => $product_id,
                            ),
                            'wc-vendors/orders/comments/',
                            WCV_PLUGIN_DIR . 'templates/orders/comments/'
                        );
                    }

                    ?>
                </div>

                <?php if ( is_array( $providers ) ) : ?>
                    <a href="#" class="order-tracking-link">
                        <p>
                            <?php esc_attr_e( 'Shipping', 'wc-vendors' ); ?>
                        </p>
                    </a>

                    <div class="order-tracking">
                        <?php

                        wc_enqueue_js( WCV_Vendor_dashboard::wc_st_js( $provider_array ) );

                        $vendor_order = wc_get_order( $order_id );

                        wc_get_template(
                            'shipping-form.php',
                            array(
                                'order_id'       => $vendor_order->get_parent_id(),
                                'product_id'     => $product_id,
                                'providers'      => $providers,
                                'provider_array' => $provider_array,
                            ),
                            'wc-vendors/orders/shipping/',
                            WCV_PLUGIN_DIR . 'templates/orders/shipping/'
                        );
                        ?>
                    </div>

                <?php endif; ?>

            </td>
        </tr>

        </tr>

    <?php endforeach; ?>

    </tbody>
</table>
<?php
do_action( 'wc_vendors_after_order_detail', $body );
if ( 1 < $total_pages ) :

echo wp_kses_post(
    paginate_links(
        apply_filters(
            'wcvendors_dashboard_product_orders_pagination_args',
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
