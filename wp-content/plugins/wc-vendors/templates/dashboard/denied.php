<?php
/**
 * Denied Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/dashboard/denied.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/dashboard
 * @version       2.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<?php
if ( function_exists( 'wc_print_notices' ) ) {
    wc_print_notices();
}

$apply_label_css_classes = apply_filters( 'wcvendors_vendor_registration_apply_label_css_classes', 'apply_for_vendor_label ' );
$term_label_css_classes  = apply_filters( 'wcvendors_vendor_registration_term_label_css_classes', 'agree_to_terms_label ' );
?>

<?php if ( WCV_Vendors::is_pending( get_current_user_id() ) ) { ?>

    <p>
        <?php
        echo esc_html(
            sprintf(
            // translators: %s is the name used to refer to a vendor.
                __( 'Your account has not yet been approved to become a %s.  When it is, you will receive an email telling you that your account is approved!', 'wc-vendors' ),
                wcv_get_vendor_name( true, false )
            )
        );
        ?>
        </p>

<?php } else { ?>

    <p>
        <?php
        echo esc_html(
            sprintf(
            // translators: %s is the name used to refer to a vendor.
                __( 'Your account is not setup as a %s.', 'wc-vendors' ),
                wcv_get_vendor_name( true, false )
            )
        );
    ?>
    </p>

    <?php if ( 'yes' === get_option( 'wcvendors_vendor_allow_registration', 'no' ) ) { ?>
        <form method="POST" action="">
            <div class="clear"></div>
            <p class="form-row">
                <?php wp_nonce_field( 'apply_for_vendor', 'apply_for_vendor_nonce' ); ?>
                <input
                    class="input-checkbox"
                    id="apply_for_vendor" <?php checked( isset( $_POST['apply_for_vendor'] ), true ); // phpcs:ignore ?>
                    type="checkbox" name="apply_for_vendor" value="1"/>
                <label
                    for="apply_for_vendor"
                    class="checkbox <?php echo esc_attr( $apply_label_css_classes ); ?>">
                        <?php
                        echo esc_html(
                            apply_filters(
                                'wcvendors_vendor_registration_checkbox',
                                sprintf(
                                    // translators: %s is the name used to refer to a vendor.
                                    __( 'Apply to become a %s? ', 'wc-vendors' ),
                                    wcv_get_vendor_name( true, false )
                                )
                            )
                        );
                        ?>
                </label>
            </p>

            <div class="clear"></div>

            <?php
            $terms_page = get_option( 'wcvendors_vendor_terms_page_id' );

            if ( $terms_page ) {

                $terms_and_conditions_visibility = get_option( 'wcvendors_terms_and_conditions_visibility' );

                $display = apply_filters( 'wcvendors_terms_and_conditions_visibility', wc_string_to_bool( $terms_and_conditions_visibility ) ) ? 'block' : 'none';

                ?>
                <input
                    type="hidden"
                    id="terms_and_conditions_visibility"
                    value="<?php echo esc_attr( $terms_and_conditions_visibility ); ?>"
                />
                <p class="form-row agree-to-terms-container" style="display:<?php echo esc_attr( $display ); ?>">
                    <input class="input-checkbox"
                            id="agree_to_terms" <?php checked( isset( $_POST['agree_to_terms'] ), true ); // phpcs:ignore ?>
                            type="checkbox" name="agree_to_terms" value="1"/>
                    <label
                        for="agree_to_terms"
                        class="checkbox <?php echo esc_attr( $term_label_css_classes ); ?>">
                            <?php echo esc_html__( 'I have read and accepted', 'wc-vendors' ); ?>&nbsp;
                            <a href="<?php echo esc_url( get_permalink( $terms_page ) ); ?>">
                                <?php echo esc_html__( 'the terms and conditions', 'wc-vendors' ); ?>
                            </a>
                    </label>
                </p>

                <script type="text/javascript">
                    jQuery(function () {
                        if (jQuery('#terms_and_conditions_visibility').val() == 'no') {
                            if ( jQuery( '#apply_for_vendor' ) . is( ':checked' ) ) {
                                jQuery( '.agree-to-terms-container' ).show();
                            }

                            jQuery( '#apply_for_vendor' ).on(
                                'click',
                                function () {
                                    jQuery( '.agree-to-terms-container' ).slideToggle();
                                }
                            );
                        }
                    });
                </script>

                <div class='clear'></div>
            <?php
            }
            ?>

            <p class="form-row">
                <input type="submit"
                    class="button"
                    name="apply_for_vendor_submit"
                    value="<?php esc_attr_e( 'Submit', 'wc-vendors' ); ?>"
                />
            </p>
        </form>
    <?php } ?>

<?php } ?>

<br class="clear">
