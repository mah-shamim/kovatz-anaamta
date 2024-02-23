<?php $has_title = !empty(trim($item['title'], " ")) ? ' has-title' : ''; ?>
<div class='ekit-feed-pinterest-pin<?php echo esc_attr($has_title) ?>'>

    <!-- Start top right logo -->
    <div class='ekit-feed-pinterest-pin--top-logo'>
        <?php 
            $migrated = isset( $settings['__fa4_migrated']['top_right_logo_icons'] );
            $is_new = empty( $top_right_logo_icon );
            if ( $is_new || $migrated ) :
                \Elementor\Icons_Manager::render_icon( $settings['top_right_logo_icons'], [ 'aria-hidden' => 'true'] );
            else : ?>
                <i class="<?php echo esc_attr($settings['top_right_logo_icons']); ?>" aria-hidden="true"></i>
            <?php endif;
        ?>
    </div>
    <!-- End top right logo -->

    <?php echo \ElementsKit_Lite\Utils::kses( $item['description'] ); ?>
</div>
