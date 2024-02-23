<div class="elementkit-toggle-tab-wraper">
    <div class="elemenetskit-toogle-controls-wraper-outer">
        <div class="elemenetskit-toogle-controls-wraper">
            <div class="elemenetskit-toggle-indicator"></div>
            <ul class="nav nav-tabs elementkit-tab-nav">
                <?php 
                foreach ($ekit_toggle_items as $i=>$toggle) {
                    $is_active = ($toggle['ekit_toggle_title_is_active'] == 'yes') ? ' active' : '';
                    $is_active = ($has_user_defined_active_toggle == false && $i == 0) ? ' active' : $is_active;
                ?>
                    <li data-elementor_current_item="elementor-repeater-item-<?php echo esc_attr( $toggle[ '_id' ] ); ?>" class="elementor-repeater-item-<?php echo esc_attr( $toggle[ '_id' ] ); ?> <?php if ($toggle['ekit_toggle_title'] == '') { ?> ekit-tab-no-title <?php }; ?>">
                        <a class="elementskit-toggle-nav-link <?php echo esc_attr($is_active);?>" id="content-<?php echo esc_attr($toggle['_id'].$toggle_id); ?>-tab" data-ekit-toggle="tab" href="#content-<?php echo esc_attr($toggle['_id'].$toggle_id); ?>"
                        aria-describedby="content-<?php echo esc_attr($toggle['_id'].$toggle_id); ?>" data-indicator-color="<?php echo esc_attr($toggle['ekit_toggle_indicator_bg_color']);?>">
                            <?php if ($toggle['ekit_toggle_title'] !== '') { ?>
                                <span class="elementskit-tab-title"><?php echo esc_html($toggle['ekit_toggle_title']); ?></span>
                            <?php }; ?>
                        </a>
                    </li>
                <?php }; ?>
            </ul>
        </div>
    </div>
    <div class="tab-content elementkit-toggle-tab-content">
        <?php foreach ($ekit_toggle_items as $i=>$toggle) :
            $is_active = ($toggle['ekit_toggle_title_is_active'] == 'yes') ? ' active show' : '';
            $is_active = ($has_user_defined_active_toggle == false && $i == 0) ? ' active show' : $is_active;
        ?>
            <div 
            class="tab-pane elementkit-toggle-tab-pane elementor-repeater-item-<?php echo esc_attr( $toggle[ '_id' ] ); ?> <?php echo esc_attr($is_active);?>" 
            id="content-<?php echo esc_attr($toggle['_id'].$toggle_id); ?>" 
            role="tabpanel"
            aria-labelledby="content-<?php echo esc_attr($toggle['_id'].$toggle_id); ?>-tab">
                <div class="animated fadeIn">
                    <?php echo \ElementsKit_Lite\Modules\Controls\Widget_Area_Utils::parse( $toggle['ekit_toggle_content'], $this->get_id(), $toggle['_id'], $ekit_ajax_template, $i+1 ); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>