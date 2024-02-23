<?php
/**
 * Admin View: Page - Go Pro
 *
 * @var string $view
 * @var object $go_pro
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// phpcs:disable
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<?php // phpcs:enable ?>
<div class="wrap wcv_addons_wrap">

    <img class="wcv-logo" src="<?php echo esc_url( WCV_ASSETS_URL ); ?>images/wcvendors_logo.png" alt="WC Vendors Pro">
    <h1><?php esc_html_e( 'Upgrade to WC Vendors Pro!', 'wc-vendors' ); ?></h1>
    <p class="align-center">
        <?php
        esc_html_e(
            'Empower your vendors to take control of their stores. Reduce your management workload and focus on the tasks that matter. ',
            'wc-vendors'
        );
        ?>
    </p>
    <br/>

    <div class="addons-banner-block">
            
            <h1><?php esc_html_e( 'Take your marketplace to the next level.', 'wc-vendors' ); ?></h1>
            <p class="align-center"><?php esc_html_e( 'Packed full of features', 'wc-vendors' ); ?></p>
            <div class="addons-banner-block-items">
                <div class="addons-banner-block-item">
                    <div class="addons-banner-block-item-icon">
                        <img class="addons-img" src="<?php echo esc_url( WCV_ASSETS_URL ); ?>images/extensions/wcvendors_dashboard.png" alt="WC Vendors Pro Dashboard">
                    </div>
                    <div class="addons-banner-block-item-content">
                        <h3><?php esc_html_e( 'Complete Frontend Experience', 'wc-vendors' ); ?></h3>
                        <p><?php esc_html_e( 'Our vendor dashboard provides vendors with an integrated frontend experience that blends seamlessly with your theme. Allow vendors to take control of their stores while you can focus on building the marketplace.', 'wc-vendors' ); ?></p>
                    </div>
                </div>
                <div class="addons-banner-block-item">
                    <div class="addons-banner-block-item-icon">
                    <img class="addons-img" src="<?php echo esc_url( WCV_ASSETS_URL ); ?>images/extensions/customize.png" alt="Customize with Ease">
                    </div>
                    <div class="addons-banner-block-item-content">
                        <h3><?php esc_html_e( 'Customize With Ease', 'wc-vendors' ); ?></h3>
                        <p><?php esc_html_e( 'Easy settings based configuration. No need for coding. As well as extensive options available for building integrations.', 'wc-vendors' ); ?></p>
                    </div>
                </div>
                <div class="addons-banner-block-item">
                    <div class="addons-banner-block-item-icon">
                        <img class="addons-img" src="<?php echo esc_url( WCV_ASSETS_URL ); ?>images/extensions/support.png" alt="Best in Class Support">
                    </div>
                    <div class="addons-banner-block-item-content">
                        <h3><?php esc_html_e( 'Best in Class Support', 'wc-vendors' ); ?></h3>
                        <p><?php esc_html_e( "Our premium support is fast and efficient, don't wait days for a support response.", 'wc-vendors' ); ?></p>
                    </div>
                </div>
            </div>
            <div class="addons-banner-block-items">
                <div class="addons-banner-block-item">
                    <div class="addons-banner-block-item-icon">
                    <img class="addons-img" src="<?php echo esc_url( WCV_ASSETS_URL ); ?>images/extensions/shipping.png" alt="Complete Shipping Solution">
                    </div>
                    <div class="addons-banner-block-item-content">
                        <h3><?php esc_html_e( 'Complete Shipping Solution', 'wc-vendors' ); ?></h3>
                        <p><?php esc_html_e( 'WC Vendors has the most comprehensive shipping system of any marketplace plugin. Let vendors manage their shipping. Flat rate or table rate shipping is available. Don’t let shipping stop your marketplace from succeeding!', 'wc-vendors' ); ?></p>
                    </div>
                </div>
                <div class="addons-banner-block-item">
                    <div class="addons-banner-block-item-icon">
                        <img class="addons-img" src="<?php echo esc_url( WCV_ASSETS_URL ); ?>images/extensions/payment.png" alt="WC Vendors Pro Dashboard">
                    </div>
                    <div class="addons-banner-block-item-content">
                        <h3><?php esc_html_e( 'Over 100+ Payment Gateways Supported', 'wc-vendors' ); ?></h3>
                        <p><?php esc_html_e( 'Use the payment gateway for your region to make sure your customers can pay seamlessly. Any payment gateway written for WooCommerce can be used.', 'wc-vendors' ); ?></p>
                    </div>
                </div>
                <div class="addons-banner-block-item">
                    <div class="addons-banner-block-item-icon">
                        <img class="addons-img" src="<?php echo esc_url( WCV_ASSETS_URL ); ?>images/extensions/commission.png" alt="WC Vendors Pro Dashboard">
                    </div>
                    <div class="addons-banner-block-item-content">
                        <h3><?php esc_html_e( 'Multiple Commission options', 'wc-vendors' ); ?></h3>
                        <p><?php esc_html_e( 'Chose from percentage, percentage + fee, fixed, fixed + fee, product category and tier rates based on total sales or product prices.', 'wc-vendors' ); ?></p>
                    </div>
                </div>
            </div>

            <p class="align-center">
                <a class="started-button product-addons-button-solid"
                    href="https://www.wcvendors.com/features/?utm_source=plugin&utm_medium=upgradetopropage&utm_campaign=gopro">And Much More
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 17.5 12.5" xml:space="preserve"><path d="M10.6,1.5c-0.4-0.4-0.4-0.9,0-1.3c0.4-0.3,0.9-0.3,1.3,0l5.3,5.3c0.2,0.2,0.3,0.4,0.3,0.7s-0.1,0.5-0.3,0.7
                    l-5.3,5.3c-0.4,0.4-0.9,0.4-1.3,0c-0.4-0.4-0.4-0.9,0-1.3l3.8-3.8H0.9C0.4,7.1,0,6.7,0,6.2s0.4-0.9,0.9-0.9h13.5L10.6,1.5z
                    M10.6,1.5" class="st0"></path></svg>
                </a>
            </p>
    </div>

    <!-- Comparison -->
    <div class="addons-featured">
        <div class="addons-wcs-banner-block-content">
            <div class="addons-column-block">
            <h1><?php esc_html_e( "What's included in WC Vendors Pro?", 'wc-vendors' ); ?></h1>
            <div class="wcv-columns">
            <table>
                <tr>
                    <th style="width:30%"><?php esc_html_e( 'Features', 'wc-vendors' ); ?></th>
                    <th>
                        <?php esc_html_e( 'WC Vendors Marketplace', 'wc-vendors' ); ?>
                        <br />
                        <?php esc_html_e( 'FREE', 'wc-vendors' ); ?>
                    </th>
                    <th>
                        <?php esc_html_e( 'WC Vendors Pro', 'wc-vendors' ); ?>
                        <br />
                        <?php esc_html_e( 'From $199/year', 'wc-vendors' ); ?>
                    </th>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Support', 'wc-vendors' ); ?></td>
                    <td><a href="https://wordpress.org/support/plugin/wc-vendors">WordPress.org</a></td>
                    <td><a href="https://www.wcvendors.com/submit-ticket/?utm_source=plugin&utm_medium=upgradetopropage&utm_campaign=gopro">Ticket Based</a></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Product Management', 'wc-vendors' ); ?></td>
                    <td><?php esc_html_e( 'WordPress Admin', 'wc-vendors' ); ?></td>
                    <td><?php esc_html_e( 'Full Featured Frontend Dashboard', 'wc-vendors' ); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Order Management', 'wc-vendors' ); ?></td>
                    <td><i class="fa fa-check"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Coupon Management', 'wc-vendors' ); ?></td>
                    <td><i class="fa fa-remove"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Shipping Management', 'wc-vendors' ); ?></td>
                    <td><i class="fa fa-remove"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Shipping Tracking & Packing Slips<', 'wc-vendors' ); ?>/td>
                    <td><i class="fa fa-remove"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Multiple Commission Types', 'wc-vendors' ); ?></td>
                    <td><i class="fa fa-remove"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Store SEO', 'wc-vendors' ); ?></td>
                    <td><i class="fa fa-remove"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Store Ratings', 'wc-vendors' ); ?></td>
                    <td><i class="fa fa-remove"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Store Vacation', 'wc-vendors' ); ?></td>
                    <td><i class="fa fa-remove"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Vendor Store Widgets', 'wc-vendors' ); ?></td>
                    <td><i class="fa fa-remove"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Vendor Verification', 'wc-vendors' ); ?></td>
                    <td><i class="fa fa-remove"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Trusted Vendor', 'wc-vendors' ); ?></td>
                    <td><i class="fa fa-remove"></i></td>
                    <td><i class="fa fa-check"></i></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Bookable Products', 'wc-vendors' ); ?></td>
                    <td><i class="fa fa-remove"></i></td>
                    <td><?php esc_html_e( 'Paid Add-on', 'wc-vendors' ); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Auction Products', 'wc-vendors' ); ?></td>
                    <td><i class="fa fa-remove"></i></td>
                    <td><?php esc_html_e( 'Paid Add-on', 'wc-vendors' ); ?></td>
                </tr>
                <tr>
                    <td><?php esc_html_e( 'Subscription Products', 'wc-vendors' ); ?></td>
                    <td><i class="fa fa-remove"></i></td>
                    <td><?php esc_html_e( 'Paid Add-on', 'wc-vendors' ); ?></td>
                </tr>
            </table>


            <p class="align-center">
                <a class="started-button product-addons-button-solid"
                    href="https://www.wcvendors.com/home/comparison/?utm_source=plugin&utm_medium=upgradetopropage&utm_campaign=gopro">View full comparison here
                    <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 17.5 12.5" xml:space="preserve"><path d="M10.6,1.5c-0.4-0.4-0.4-0.9,0-1.3c0.4-0.3,0.9-0.3,1.3,0l5.3,5.3c0.2,0.2,0.3,0.4,0.3,0.7s-0.1,0.5-0.3,0.7
                    l-5.3,5.3c-0.4,0.4-0.9,0.4-1.3,0c-0.4-0.4-0.4-0.9,0-1.3l3.8-3.8H0.9C0.4,7.1,0,6.7,0,6.2s0.4-0.9,0.9-0.9h13.5L10.6,1.5z
                    M10.6,1.5" class="st0"></path></svg>
                </a>
            </p>
        </div>
    </div>

    <!-- Testimonials -->
    <div class="addons-featured">
        <div class="addons-wcs-banner-block-content">
            <div class="addons-column-block">
            <h1><?php esc_html_e( 'What people are saying', 'wc-vendors' ); ?></h1>						
            <div class="carrousel">
                <input type="radio" name="slides" id="radio-1" checked>
                <input type="radio" name="slides" id="radio-2">
                <input type="radio" name="slides" id="radio-3">
                <input type="radio" name="slides" id="radio-4">
                <ul class="slides">
                    <li class="slide">
                        <p>
                        <q><?php esc_html_e( 'Highly recommend for new marketplace websites! Their support is outstanding.', 'wc-vendors' ); ?></q> 
                        <span class="author">
                        <img src="<?php echo esc_url( WCV_ASSETS_URL ); ?>images/extensions/bcgearexchange.png">
                        <?php esc_html_e( 'bcgearexchange', 'wc-vendors' ); ?>
                        </span>
                        </p>
                    </li>        
                    <li class="slide">
                    <p>
                        <q><?php esc_html_e( 'Great experience so far. Excellent support from Jamie – very helpful and to the point. Thank you!', 'wc-vendors' ); ?></q> 
                        <span class="author">
                        <img src="<?php echo esc_url( WCV_ASSETS_URL ); ?>images/extensions/sugarcactus.png">
                        <?php esc_html_e( 'sugarcactus', 'wc-vendors' ); ?>
                        </span>
                    </p>
                    </li>
                    <li class="slide">
                        <p>
                            <q><?php esc_html_e( 'WC Vendors Pro support staff have been FANTASTIC!! I have been so pleasantly surprised by the response times & valuable assistance. It’s wonderful that WC Vendors believes in providing exceptional customer service. Well done to WC Vendors & their support team, please keep up the great work :)!', 'wc-vendors' ); ?></q> 
                            <span class="author">
                            <img src="<?php echo esc_url( WCV_ASSETS_URL ); ?>images/extensions/screenshot-1.png">
                            <?php esc_html_e( 'Locate Australian', 'wc-vendors' ); ?>
                            </span>
                        </p>
                    </li>
                    <li class="slide">
                        <p>
                        <q><?php esc_html_e( 'Great support! Jamie was fantastic. He helped us resolve our issue and even put together a script to help resolve the issue quickly! Thanks WC Vendors team!', 'wc-vendors' ); ?></q> 
                        <span class="author">
                            <img src="<?php echo esc_url( WCV_ASSETS_URL ); ?>images/extensions/cody.png">
                            <?php esc_html_e( 'Cody Slingerland', 'wc-vendors' ); ?>
                        </span>
                    </p>
                </li>
            </ul>
            
            <div class="slidesNavigation">
                <label for="radio-1" id="dotForRadio-1"></label>
                <label for="radio-2" id="dotForRadio-2"></label>
                <label for="radio-3" id="dotForRadio-3"></label>
                <label for="radio-4" id="dotForRadio-4"></label>
            </div>
</div>


        </div>
    </div>

    <!-- Ready? -->
    <div class="addons-featured">
        <div class="addons-wcs-banner-block-content">
            <div class="addons-column-block">
            <h1><?php esc_html_e( 'Are you ready?', 'wc-vendors' ); ?></h1>
            <p><?php esc_html_e( 'WC Vendors Pro will take your marketplace to the next level all while saving you time and money. ', 'wc-vendors' ); ?></p>
            <p class="align-center">
                <a class="started-button product-addons-button-solid"
                    href="https://www.wcvendors.com/pricing/?utm_source=plugin&utm_medium=upgradetopropage&utm_campaign=gopro">Get Started Today</a>
            </p>
        </div>
    </div>
</div>
