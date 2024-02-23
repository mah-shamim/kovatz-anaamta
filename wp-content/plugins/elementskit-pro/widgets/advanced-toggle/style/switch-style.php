<div class="ekit-slide-toggle-wraper">
    <div class="ekit-switch-nav-wraper-outer" role="tablist">
        <label class="nav nav-tabs ekit-slide-toggle" id="nav-tab-<?php echo $this->get_id(); ?>">
        <?php 
            $toggleItem = 0;
            foreach ($ekit_toggle_items as $i=>$toggle) {
                $toggleItem++;
                $is_active = ($toggle['ekit_toggle_title_is_active'] == 'yes') ? ' active' : '';
                $is_active = ($has_user_defined_active_toggle == false && $i == 0) ? ' active' : $is_active;
                if ($toggleItem === 3) {
                    break;
                }
            ?>
            <a 
                class="nav-item nav-link elementskit-switch-nav-link elementskit-switch-nav-link-<?php echo esc_attr($toggleItem); ?> <?php echo esc_attr($is_active);?> <?php echo $toggle['ekit_toggle_title_is_active'] == 'yes' ? 'ekit-cehckbox-forcefully-checked' : ''; ?> elementor-repeater-item-<?php echo esc_attr( $toggle[ '_id' ] ); ?>"
                id="content-<?php echo esc_attr($toggle['_id'].$toggle_id); ?>-tab" 
                data-ekit-toggle="tab" 
                href="#content-<?php echo esc_attr($toggle['_id'].$toggle_id); ?>" 
                role="tab" 
                aria-controls="content-<?php echo esc_attr($toggle['_id'].$toggle_id); ?>" 
                aria-selected="true"
                data-toggled="<?php echo $toggleItem === 1 ? '' : '1'; ?>"
            >
            <?php if ($toggle['ekit_toggle_title'] !== '') { ?>
                <span class="elementskit-tab-title"><?php echo esc_html($toggle['ekit_toggle_title']); ?></span>
            <?php }; ?>
            </a>
            <?php if ($toggleItem === 1) { ?>
                <?php
                    $is_toggled = $ekit_toggle_items[1]['ekit_toggle_title_is_active'] === 'yes' ? ' checked' : '';
                ?>

                <input type="checkbox" class="ekit-custom-control-input" <?php echo esc_attr( $is_toggled ); ?>>
                <i class="ekit-custom-control-label elementor-repeater-item-<?php echo esc_attr( $toggle[ '_id' ] ); ?>"></i>
            <?php } ?>
            <?php }; ?>
        </label>
    </div>

    <div class="tab-content" id="nav-tabContent-<?php echo $this->get_id(); ?>">
    <?php 
        $toggleContent = 0;
        foreach ($ekit_toggle_items as $item=>$toggle) {
        $is_active = ($toggle['ekit_toggle_title_is_active'] == 'yes') ? ' active show' : '';
        $is_active = ($has_user_defined_active_toggle == false && $item == 0) ? ' active show' : $is_active;
        $toggleContent++;
        if ($toggleContent === 3) {
            break;
        }
    ?>
        <div 
        class="tab-pane fade ekit-toggle-switch-content elementor-repeater-item-<?php echo esc_attr( $toggle[ '_id' ] ); ?> <?php echo esc_attr($is_active);?>" id="content-<?php echo esc_attr($toggle['_id'].$toggle_id); ?>" 
        role="tabpanel" 
        aria-labelledby="content-<?php echo esc_attr($toggle['_id'].$toggle_id); ?>-tab"
        >   
            <div class="animated fadeIn">
                <?php echo \ElementsKit_Lite\Modules\Controls\Widget_Area_Utils::parse( $toggle['ekit_toggle_content'], $this->get_id(), ($item + 1), $ekit_ajax_template ); ?>
            </div>
        </div>
    <?php }; ?>
    </div>
</div>
