<ul class="ekit-price-menu">
    <?php foreach ($ekit_price_lists as $index => $item) :
        !empty($item['button_link']['url']) &&	$this->add_link_attributes('button_link'.$item['_id'], $item['button_link']);

        if ($item['media_type'] == 'image') :
            $item['ekit_price_menu_image_size_size'] = $settings['ekit_price_menu_image_size_size'];
            $item['ekit_price_menu_image_size_custom_dimension'] = $settings['ekit_price_menu_image_size_custom_dimension'];
            $image_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html($item, 'ekit_price_menu_image_size', 'ekit_price_menu_image');
        endif;
    ?>

    <li class="ekit-price-menu-item elementor-repeater-item-<?php echo esc_attr( $item[ '_id' ] ); ?>">
        <?php if ($item['media_type'] == 'image' && !empty($image_html)) : ?>
            <div class="ekit-price-menu-image">
                <?php echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
            </div>
        <?php endif;

        if ($item['media_type'] == 'icon' && !empty($item['icon']['value'])) : ?>
            <div class="ekit-price-menu-icon">
                <?php \Elementor\Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ); ?>
            </div>
        <?php endif; 

        if($ekit_price_menu_price_position == 'right') : ?>
            <div class="ekit-price-menu-caption">
                <div class="ekit-price-menu-caption-header">
                    <span class="ekit-price-menu-caption-title">
						<?php if($item['ekit_price_menu_button_show'] == 'yes') : ?>
							<?php echo esc_html($item['title']); ?>
						<?php endif;

						if ($item['ekit_price_menu_button_show'] != 'yes' && !empty($item['link']['url'])) : 
							$this->add_link_attributes('link'.$item['_id'], $item['link']); ?>
							<a <?php $this->print_render_attribute_string('link'.$item['_id']); ?>>
								<?php echo esc_html($item['title']); ?>
							</a>
						<?php endif; ?>
					</span>
                    <span class="ekit-price-menu-caption-separator"></span>
                    <span class="ekit-price-menu-caption-price"><?php echo esc_html($item['price']); ?></span>
                </div>
                <?php if(!empty($item['description'])) : ?>
                    <p class="ekit-price-menu-caption-description"><?php echo esc_html($item['description']); ?></p>
                <?php endif; ?> 
                <?php if ($item['ekit_price_menu_button_show'] == 'yes') : ?>
                    <a class="ekit-price-menu-caption-button" <?php $this->print_render_attribute_string('button_link'.$item['_id']); ?>>
                        <?php 
                        $item['button_icon_position'] == 'before' && $item['button_icon_switch'] == 'yes' && \Elementor\Icons_Manager::render_icon( $item['button_icon'], [ 'aria-hidden' => 'true', 'class' => 'price-menu-button-icon-before' ]);	
                        echo esc_html($item['button_text']); 	 
                        $item['button_icon_position'] == 'after' && $item['button_icon_switch'] == 'yes' && \Elementor\Icons_Manager::render_icon( $item['button_icon'], [ 'aria-hidden' => 'true', 'class' => 'price-menu-button-icon-after' ] );
                        ?>
                    </a>
                <?php endif; ?>
            </div>

        <?php elseif($ekit_price_menu_price_position == 'bottom') : ?>
            <div class="ekit-price-menu-caption">
                <div class="ekit-price-menu-caption-header">
                    <span class="ekit-price-menu-caption-title"><?php echo esc_html($item['title']); ?></span>
                </div>
                <?php if(!empty($item['description'])) : ?>
                    <p class="ekit-price-menu-caption-description"><?php echo esc_html($item['description']); ?></p>
                <?php endif; ?> 
                <p class="ekit-price-menu-caption-price"><?php echo esc_html($item['price']); ?></p>		
                <?php if ($item['ekit_price_menu_button_show'] == 'yes') : ?>
                    <a class="ekit-price-menu-caption-button" <?php $this->print_render_attribute_string('button_link'.$item['_id']); ?>>
                        <?php 
                        $item['button_icon_position'] == 'before' && $item['button_icon_switch'] == 'yes' && \Elementor\Icons_Manager::render_icon( $item['button_icon'], [ 'aria-hidden' => 'true', 'class' => 'price-menu-button-icon-before' ]);	
                        echo esc_html($item['button_text']); 	 
                        $item['button_icon_position'] == 'after' && $item['button_icon_switch'] == 'yes' && \Elementor\Icons_Manager::render_icon( $item['button_icon'], [ 'aria-hidden' => 'true', 'class' => 'price-menu-button-icon-after' ] );
                        ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>