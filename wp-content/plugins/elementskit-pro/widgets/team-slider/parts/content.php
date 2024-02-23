<?php
use \Elementor\ElementsKit_Widget_Team_Slider_Handler as Handler;

$button_class = 'elementskit-btn';
$popup_attr = '';

if (!empty( $ekit_team_member['button_link']['url'])) {
    $this->add_link_attributes( 'button_link_'.$index, $ekit_team_member['button_link'] );
    $popup_attr = $this->get_render_attribute_string( 'button_link_'.$index);
}

if ($settings['ekit_team_chose_button_popup'] == 'yes') {
    $popup_attr = "href=javascript:void(0) data-mfp-src=#ekit_team_modal_".$ekit_team_member['_id'].$widget_id;
    $button_class = 'elementskit-btn ekit-team-popup';
}
?>
<div class="profile-body <?php if($ekit_team_content_stable == 'yes' && isset($ekit_team_content_text_align)) { echo esc_attr($ekit_team_content_text_align);} ?> <?php if($ekit_team_style == 'overlay_content_hover') echo 'overlay-content-hover' ?>">
    <h2 class="profile-title">
        <?php if ($settings['ekit_team_chose_popup'] == 'yes') : ?>
            <a  href="javascript:void(0)" data-mfp-src="#ekit_team_modal_<?php echo esc_attr($ekit_team_member['_id'].$widget_id); ?>" class="ekit-team-popup">
                <?php echo esc_html( $ekit_team_member['name'] ); ?>
            </a>
            <?php else: ?>
                <?php echo esc_html( $ekit_team_member['name'] ); ?>
        <?php endif; ?>
    </h2>
    <p class="profile-designation"> 
        <?php echo esc_html( $ekit_team_member['position'] ); ?>
    </p>

    <?php if($settings['ekit_team_choose_details'] == 'yes' && $ekit_team_member['short_description'] != '') : ?>
        <p class="profile-content"> <?php echo wp_kses($ekit_team_member['short_description'], \ElementsKit_Lite\Utils::get_kses_array()); ?></p>
    <?php endif;?>

    <?php if($settings['ekit_team_chose_button'] == 'yes') : ?>
        <a aria-label="chose-button" class="<?php echo esc_html($button_class); ?>" <?php echo $popup_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
            <?php $ekit_team_member['button_icon'] != '' && $ekit_team_member['button_icon_position'] == 'before' &&  \Elementor\Icons_Manager::render_icon( $ekit_team_member['button_icon'], [ 'aria-hidden' => 'true', 'class' => 'elementskit-btn-icon' ]);
            echo esc_html( $ekit_team_member['button_text'] );
            $ekit_team_member['button_icon'] != '' && $ekit_team_member['button_icon_position'] == 'after' &&  \Elementor\Icons_Manager::render_icon( $ekit_team_member['button_icon'], [ 'aria-hidden' => 'true', 'class' => 'elementskit-btn-icon' ]); ?>
        </a>
    <?php endif; ?>
    
    <?php
        if ( $settings['ekit_team_style'] == 'hover_info' && $settings['ekit_team_socail_enable'] == 'yes' ) {
            include Handler::get_dir() . 'parts/social-list.php';
        }
    ?>
</div>