<?php
/**
 * Admin view: Review Request Notice
 */
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }
?>

<div class="notice notice-info is-dismissible wcv-notice-container" id="wcv-review-notice" data-notice-key="review_request"> 
<p>
    <?php
    esc_html_e(
        'Hey, I noticed you have been using WC Vendors Marketplace for some time - that\'s awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress to help us spread the word and boost our motivation?',
        'wc-vendors'
    );
?>
</p>
<p><strong>~ Josh Kohlbach<br>CEO of WC Vendors</strong></p>
<p><a class="wcv-dismiss-notice" href="https://wordpress.org/support/plugin/wc-vendors/reviews/?filter=5#new-post" target="_blank"><?php esc_html_e( 'Ok, you deserve it!', 'wc-vendors' ); ?></a></p>
<p><a class="wcv-dismiss-notice" href="#"><?php esc_html_e( 'I already did', 'wc-vendors' ); ?></a></p>
<p><a class="wcv-dismiss-notice-delay" href="#"><?php esc_html_e( 'Nope, maybe later', 'wc-vendors' ); ?></a></p>
<?php wp_nonce_field( 'wcv_review_notice', 'wcv_review_notice_nonce' ); ?>
</div>
