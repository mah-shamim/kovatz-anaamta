<?php
/**
 * Shipping Form Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/orders/shipping/shipping-form.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/Emails/HTML
 * @version 2.4.8
 * @since   2.4.8 - Added HPOS compatibility.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<form method="post" name="track_shipment" id="track-shipment_<?php echo esc_attr( $order_id ); ?>">

    <?php
    wp_nonce_field( 'track-shipment' );

    $current_order = wc_get_order( $order_id );

    if ( ! $current_order ) {
        return;
    }

    $vendor_id = get_current_user_id();

    $order_tracking_details = (array) $current_order->get_meta( '_wcv_tracking_details', true );

    $tracking_details = isset( $order_tracking_details[ $vendor_id ] ) ? $order_tracking_details[ $vendor_id ] : array();

    $selected_provider        = isset( $tracking_details['_tracking_provider'] ) ? $tracking_details['_tracking_provider'] : '';
    $custom_tracking_provider = isset( $tracking_details['_custom_tracking_provider'] ) ? $tracking_details['_custom_tracking_provider'] : '';
    $tracking_umber           = isset( $tracking_details['_tracking_number'] ) ? $tracking_details['_tracking_number'] : '';
    $custom_tracking_link     = isset( $tracking_details['_custom_tracking_link'] ) ? $tracking_details['_custom_tracking_link'] : '';
    $date_shipped             = isset( $tracking_details['_date_shipped'] ) ? $tracking_details['_date_shipped'] : '';

    $button_label = ! empty( $tracking_details ) ? __( 'Update tracking number', 'wc-vendors' ) : __( 'Mark shipped', 'wc-vendors' );
    // Providers.
    ?>
    <p class="form-field tracking_provider_field">
        <label for="tracking_provider"><?php esc_html_e( 'Provider:', 'wc-vendors' ); ?></label><br/>
        <select id="tracking_provider" name="tracking_provider" class="tracking_provider" style="width:100%;">
            <option value=""><?php esc_html_e( 'Custom Provider', 'wc-vendors' ); ?></option>
            <?php
            $class = '';

            foreach ( $providers as $provider_group => $providers ) :
            ?>
            <optgroup label="<?php echo esc_attr( $provider_group ); ?>">
                <?php foreach ( $providers as $provider => $url ) : ?>
                    <option
                        value="<?php echo esc_attr( sanitize_title( $provider ) ); ?>"
                        <?php selected( sanitize_title( $provider ), $selected_provider, true ); ?>
                    >
                        <?php echo esc_attr( $provider ); ?>
                    </option>
                    <?php
                        if ( selected( sanitize_title( $provider ), $selected_provider ) ) {
                            $class = 'hidden';
                        }
                    endforeach;
            ?>
            </optgroup>
            <?php endforeach; ?>
        </select>
    </p>
    <?php

    woocommerce_wp_text_input(
        array(
            'id'            => 'custom_tracking_provider_name',
            'label'         => __( 'Provider Name:', 'wc-vendors' ),
            'wrapper_class' => $class,
            'placeholder'   => '',
            'description'   => '',
            'value'         => $custom_tracking_provider,
        ),
    );

    woocommerce_wp_text_input(
        array(
            'id'          => 'tracking_number',
            'label'       => __( 'Tracking number:', 'wc-vendors' ),
            'placeholder' => '',
            'description' => '',
            'value'       => $tracking_umber,
        )
    );

    woocommerce_wp_text_input(
        array(
            'id'            => 'custom_tracking_url',
            'label'         => __( 'Tracking link:', 'wc-vendors' ),
            'placeholder'   => 'http://',
            'wrapper_class' => $class,
            'description'   => '',
            'value'         => $custom_tracking_link,
        )
    );

    woocommerce_wp_text_input(
        array(
            'type'        => 'date',
            'id'          => 'date_shipped',
            'label'       => __( 'Date shipped:', 'wc-vendors' ),
            'placeholder' => 'YYYY-MM-DD',
            'description' => '',
            'class'       => 'date-picker-field',
            'value'       => ( '' !== $date_shipped ) ? gmdate( 'Y-m-d', $date_shipped ) : '',
        )
    );

    // Live preview.
    ?>
    <p class="preview_tracking_link" style="display:none;">
        <?php echo esc_attr__( 'Preview:', 'wc-vendors' ); ?>
        <a href="" target="_blank">
            <?php echo esc_html_e( 'Click here to track your shipment', 'wc-vendors' ); ?>
        </a>
    </p>

    <input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>">
    <input type="hidden" name="order_id" value="<?php echo esc_attr( $order_id ); ?>">
    <input
        class="button"
        type="submit"
        name="update_tracking"
        value="<?php echo esc_attr( $button_label ); ?>"
    >

</form>
