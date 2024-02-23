<?php
/**
 * Reports Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/dashboard/reports.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/dashboard
 * @version       2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>


<h2><?php esc_attr_e( 'Sales Report', 'wc-vendors' ); ?></h2>

<?php

if ( 'false' !== $datepicker ) {
    wc_get_template(
        'date-picker.php',
        array(
            'start_date' => $start_date,
            'end_date'   => $end_date,
        ),
        'wc-vendors/dashboard/',
        WCV_PLUGIN_DIR . 'templates/dashboard/'
    );
}

?>

<table class="table table-condensed table-vendor-sales-report">
    <thead>
    <tr>
        <th class="product-header"><?php esc_attr_e( 'Product', 'wc-vendors' ); ?></th>
        <th class="quantity-header"><?php esc_attr_e( 'Quantity', 'wc-vendors' ); ?></th>
        <th class="commission-header"><?php esc_attr_e( 'Commission', 'wc-vendors' ); ?></th>
        <th class="rate-header"><?php esc_attr_e( 'Rate', 'wc-vendors' ); ?></th>
        <th></th>
    </thead>
    <tbody>

    <?php if ( ! empty( $vendor_summary ) ) : ?>


        <?php if ( ! empty( $vendor_summary['products'] ) ) : ?>

            <?php
            foreach ( $vendor_summary['products'] as $product ) :
                $_product = wc_get_product( $product['id'] );
                ?>

                <tr>

                    <td class="product">
                        <strong>
                            <a href="<?php echo esc_url( get_permalink( $_product->get_id() ) ); ?>">
                                <?php echo wp_kses_post( $product['title'] ); ?>
                            </a>
                        </strong>
                        <?php
                        if ( ! empty( $_product->variation_id ) ) {
                            echo wp_kses_post( wc_get_formatted_variation( $_product->variation_data ) );
                        }
                        ?>
                    </td>
                    <td class="qty"><?php echo esc_attr( $product['qty'] ); ?></td>
                    <td class="commission"><?php echo wp_kses_post( wc_price( $product['cost'] ) ); ?></td>
                    <td class="rate"><?php echo esc_attr( sprintf( '%.2f%%', $product['commission_rate'] ) ); ?></td>

                    <?php if ( $can_view_orders ) : ?>
                        <td>
                            <a href="<?php echo esc_url_raw( $product['view_orders_url'] ); ?>"><?php esc_attr_e( 'Show Orders', 'wc-vendors' ); ?></a>
                        </td>
                    <?php endif; ?>

                </tr>

            <?php endforeach; ?>

            <tr>
                <td><strong><?php esc_attr_e( 'Totals', 'wc-vendors' ); ?></strong></td>
                <td><?php echo esc_attr( $vendor_summary['total_qty'] ); ?></td>
                <td><?php echo wp_kses_post( wc_price( $vendor_summary['total_cost'] ) ); ?></td>
                <td></td>

                <?php if ( $can_view_orders ) : ?>
                    <td></td>
                <?php endif; ?>

            </tr>

        <?php else : ?>

            <tr>
                <td colspan="5" style="text-align:center;">
                    <?php esc_attr_e( 'You have no sales during this period.', 'wc-vendors' ); ?>
                </td>
            </tr>

        <?php endif; ?>


    <?php else : ?>

        <tr>
            <td colspan="5" style="text-align:center;">
                <?php esc_attr_e( 'You haven\'t made any sales yet.', 'wc-vendors' ); ?>
            </td>
        </tr>

    <?php endif; ?>

    </tbody>
</table>
