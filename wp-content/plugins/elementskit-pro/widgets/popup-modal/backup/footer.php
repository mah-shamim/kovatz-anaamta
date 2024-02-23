<?php if($show_footer_buttons == 'yes' && count($popup_footer_buttons) > 0 ): ?>
    <div class='ekit-popup-modal__actions'>
        <?php foreach($popup_footer_buttons as $button):
            $btn_classes = "elementskit-btn ekit-popup-btn whitespace--normal";
            $btn_classes .= " elementor-repeater-item-" . $button['_id'];
            $btn_classes .= " " . $button['type'];
            $btn_classes .= " " . $button['action'];
        ?>
            <a 
                href="<?php echo !empty($button['url']) ? esc_url( $button['url'] ) : '#' ?>" 
                class='<?php echo esc_attr( $btn_classes ); ?>'
                target="<?php echo $button['open_in_new_tab'] =='yes' ? "_blank": null ?>"
            >
                <?php echo esc_html( $button['label'] ); ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>