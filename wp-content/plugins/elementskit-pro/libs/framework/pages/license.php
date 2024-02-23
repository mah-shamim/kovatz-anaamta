<?php
$licenseClass = ElementsKit\Libs\Framework\Classes\License::instance();

?>
<div class="ekit-wid-con">
    <div class="ekit_container" style="max-width: 768px;">
        <form action="" method="POST" class="ekit-admin-form" id="ekit-admin-license-form">
            <div class="ekit_tab_wraper">
                <div class="ekit-admin-section-header">
                    <h2 class="ekit-admin-section-heaer-title"><i class="icon icon-key2"></i><?php echo esc_html__('License Settings', 'elementskit'); ?></h2>
                </div>
                <div class="ekit-admin-card attr-tab-content ekit-admin-card-shadow">

                    <div class="attr-card-body">
                        <?php if($licenseClass->status() != 'valid'): ?>
                        <p><?php esc_html_e('Enter your license key here, to activate ElementsKit, and get auto updates, premium support and unlimited access to the template library.', 'elementskit'); ?></p>

                        <ol>
                            <li><?php printf( esc_html__( 'Log in to your %sWpmet account%s to get your license key.', 'elementskit' ), '<a href="https://account.wpmet.com/" target="_blank">', '</a>' ); ?></li>
                            <li><?php printf( esc_html__( 'If you don\'t yet have a license key, get %sElementsKit%s now.', 'elementskit' ), 
                                        '<a href="https://go.wpmet.com/ekitbuy" target="_blank">', '</a>' );
                                ?></li>
                            <li><?php esc_html_e('Copy the ElementsKit license key from your account and paste it below.', 'elementskit'); ?></li>
                        </ol>

                        <label for="ekit-admin-option-text-elementskit-license-key"><?php esc_html_e('Your License Key', 'elementskit'); ?></label>
                        <div class="ekit-admin-input-text">
                            <input
                                type="text"
                                class="attr-form-control"
                                id="ekit-admin-option-text-elementskit-license-key"
                                aria-describedby="ekit-admin-option-text-help-elementskit-license-key"
                                placeholder="<?php echo esc_attr('Please insert your license key here', 'elementskit'); ?>"
                                name="elementkit_pro_license_key"
                                value="<?php echo esc_attr($licenseClass->get_license_info()); ?>"
                            >
                            
                        </div>
                        

                        <div class="elementskit-license-form-result">
                            <p class="attr-alert attr-alert-info">
                                <?php printf( esc_html__( 'Still can\'t find your license key? %s', 'elementskit' ), '<a target="_blank" href="https://wpmet.com/support-ticket">Knock us here!</a>' ); ?>
                            </p>
                        </div>
                        <div class="attr-input-group-btn">
                            <input type="hidden" name="type" value="activate" />
                            <button class="attr-btn btn-license-activate attr-btn-primary ekit-admin-license-form-submit" type="submit" ><div class="ekit-spinner"></div><i class="ekit-admin-save-icon fa fa-check-circle"></i><?php esc_html_e('Activate', 'elementskit'); ?></button>
                        </div>
                        <?php else: ?>
                            <div class="elementskit-license-form-result">
                                <p class="attr-alert attr-alert-success">
                                    <?php printf( esc_html__('Congratulations! Your product is activated for "%s"', 'elementskit'), parse_url(home_url(), PHP_URL_HOST)); ?>
                                </p>
                            </div>
                            <div class="attr-revoke-btn-container">
                                <input type="hidden" name="type" value="revoke" />
                                <span data-attr-toggle='modal' data-target='#elementskit_revoke_license_modal' class="attr-btn attr-btn-primary" ><?php esc_html_e('Remove license from this domain', 'elementskit'); ?></span> <span style="padding-left:20px"><?php echo sprintf(esc_html__('See documention %shere%s.', 'elementskit'), '<a target="_blank" href="https://help.wpmet.com/docs/how-to-revoke-product-license-key/">', '</a>'); ?></span>
                            </div>

                            <div class="attr-modal attr-fade ekit-wid-con" id="elementskit_revoke_license_modal" tabindex="-1" role="dialog"
                                aria-labelledby="elementskit_revoke_license_modalLabel">
                                <div class="attr-modal-dialog attr-modal-dialog-centered ekit-go-pro-con" role="document">
                                    <div class="attr-modal-content">
                                    <button type="button" class="close ekit-go-pro-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                        <div class="attr-modal-body attr-text-center">
                                            <h3><?php esc_html_e('Are you sure to remove license from this site?', 'elementskit'); ?></h3>
                                            <a href="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=ekit_admin_license&type=revoke" class="" ><?php esc_html_e('Yes, I confrim', 'elementskit'); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>