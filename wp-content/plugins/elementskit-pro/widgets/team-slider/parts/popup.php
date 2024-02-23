<?php
use \Elementor\ElementsKit_Widget_Team_Slider_Handler as Handler;
?>
<div class="zoom-anim-dialog mfp-hide elementskit-team-popup" id="ekit_team_modal_<?php echo esc_attr($ekit_team_member['_id'].$widget_id); ?>" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <button type="button" class="ekit-team-modal-close">
                <?php \Elementor\Icons_Manager::render_icon( $settings['ekit_team_close_icon_changes'], ['aria-hidden' => 'true'] ); ?>
            </button>

            <div class="modal-body">
                <?php if ( !empty($image_html) ) { ?>
                    <div class="ekit-team-modal-img">
                        <?php echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
                    </div>
                <?php } ?> 

                <div class="ekit-team-modal-info<?php echo esc_html(!empty($image_html) ? ' has-img' : ''); ?>">
                    <h2 class="ekit-team-modal-title"><?php echo esc_html( $ekit_team_member['name'] ); ?></h2>
                    <p class="ekit-team-modal-position"><?php echo esc_html( $ekit_team_member['position'] ); ?></p>
                    
                    <div class="ekit-team-modal-content">
                        <?php echo wp_kses($ekit_team_member['short_description'], \ElementsKit_Lite\Utils::get_kses_array()); ?>
                    </div>

                    <?php if ( $ekit_team_member['popup_phone'] || $ekit_team_member['popup_email'] || $ekit_team_member['popup_website']['url'] ) { ?>
                        <ul class="ekit-team-modal-list">
                            <?php if ( $ekit_team_member['popup_phone'] ) : ?>
                                <li><strong><?php esc_html_e( 'Phone', 'elementskit' ); ?>:</strong><a href="tel:<?php echo esc_attr( $ekit_team_member['popup_phone'] ); ?>" aria-label="tel"><?php echo esc_html( $ekit_team_member['popup_phone'] ); ?></a></li>
                            <?php endif; ?>

                            <?php if ( $ekit_team_member['popup_email'] ) : ?>
                                <li><strong><?php esc_html_e( 'Email', 'elementskit' ); ?>:</strong><a href="mailto:<?php echo esc_attr( $ekit_team_member['popup_email'] ); ?>" aria-label="email"><?php echo esc_html( $ekit_team_member['popup_email'] ); ?></a></li>
                            <?php endif; ?>

                            <?php if ( !empty( $ekit_team_member['popup_website']['url']) ) : 
				                $this->add_link_attributes( 'popup_website_link_'.$index,  $ekit_team_member['popup_website'] ); 
                                ?>
                                <li><strong><?php esc_html_e( 'Website', 'elementskit' ); ?>:</strong><a <?php $this->print_render_attribute_string('popup_website_link_'.$index); ?> aria-label="website-link"><?php echo esc_html( $ekit_team_member['popup_website']['url'] ); ?></a></li>
                            <?php endif; ?>
                        </ul>
                    <?php } ?>
                    
                    <?php
                        if ( $settings['ekit_team_socail_enable'] == 'yes' || $settings['ekit_socialmedai_popup_enable'] == 'yes') {
                            include Handler::get_dir() . 'parts/social-list.php';
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>