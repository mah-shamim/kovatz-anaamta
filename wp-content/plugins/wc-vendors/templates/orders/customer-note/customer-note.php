<?php
/**
 * Customer Note Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/orders/customer-note/customer-note.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/Emails/HTML
 * @version       2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<tr>
    <td colspan="100%">
        <h2><?php esc_html_e( 'Customer notes', 'wc-vendors' ); ?></h2>

        <?php if ( empty( $customer_notes ) ) : ?>
            <p><?php esc_html_e( 'No customer notes.', 'wc-vendors' ); ?></p>
        <?php else : ?>
            <?php foreach ( $customer_notes as $customer_note ) : ?>
            <p><?php echo wp_kses_post( $customer_note->comment_content ); ?></p>
            <?php endforeach; ?>
        <?php endif ?>
    </td>
</tr>
