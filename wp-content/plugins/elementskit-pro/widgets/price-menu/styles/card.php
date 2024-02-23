<?php 
$config = [
	'rtl'				=> is_rtl(),
	'arrows'			=> !empty($ekit_price_menu_show_arrow),
	'dots'				=> !empty($ekit_price_menu_show_dot),
	'autoplay'			=> !empty($ekit_price_menu_autoplay),
	'speed'				=> $ekit_price_menu_speed,
	'slidesPerView'		=> $ekit_price_menu_slidetoshow['size'] ?? 4,
	'slidesPerGroup'	=> $ekit_price_menu_slidesToScroll['size'] ?? 1,
	'spaceBetween' 		=> $ekit_price_menu_slider_spacing['size'] ?? 30,
	'pauseOnHover'	    => !empty($ekit_price_menu_pause_on_hover),
	'loop'  			=> !empty($ekit_price_menu_loop),
	'breakpoints'		=> [
		360 => [
			'slidesPerView'      => $ekit_price_menu_slidetoshow_mobile['size'] ?? 1,
			'slidesPerGroup'    => $ekit_price_menu_slidesToScroll_mobile['size'] ?? 1
		],
		767 => [
			'slidesPerView'      => $ekit_price_menu_slidetoshow_tablet['size'] ?? 2,
			'slidesPerGroup'    => $ekit_price_menu_slidesToScroll_tablet['size'] ?? 1,
		],
		1024 => [
			'slidesPerView'      => $ekit_price_menu_slidetoshow['size'] ?? 2,
			'slidesPerGroup'    => $ekit_price_menu_slidesToScroll['size'] ?? 1,
		]
	],
];

$slider_container_class = $ekit_price_menu_enable_slider == 'yes' ? (method_exists('\ElementsKit_Lite\Utils', 'swiper_class') ? \ElementsKit_Lite\Utils::swiper_class() : 'swiper') : '';
$wrapper_class = $ekit_price_menu_enable_slider == 'yes' ?  'swiper-wrapper' : '';
$slide_class =  $ekit_price_menu_enable_slider == 'yes' ?  'swiper-slide' : '';
?>
<div class="ekit-price-card-slider">
	<div class="<?php echo esc_attr($slider_container_class) ?>">
		<ul class="ekit-price-card <?php echo esc_attr($wrapper_class) ?>" data-config ="<?php echo esc_attr(json_encode($config)); ?>" >
			<?php foreach ($ekit_price_lists as $index => $item) :
				!empty($item['button_link']['url']) &&	$this->add_link_attributes('button_link'.$item['_id'], $item['button_link']);
				
				if ($item['media_type'] == 'image') :
					$item['ekit_price_menu_image_size_size'] = $settings['ekit_price_menu_image_size_size'];
					$item['ekit_price_menu_image_size_custom_dimension'] = $settings['ekit_price_menu_image_size_custom_dimension'];
					$image_html = \Elementor\Group_Control_Image_Size::get_attachment_image_html($item, 'ekit_price_menu_image_size', 'ekit_price_menu_image');
				endif;
			?>
			
			<li class="ekit-price-card-item elementor-repeater-item-<?php echo esc_attr( $item[ '_id' ].' '.$slide_class ); ?>">
				<?php if ($item['ekit_price_menu_button_show'] != 'yes' && !empty($item['link']['url'])) : 
					$this->add_link_attributes('link'.$item['_id'], $item['link']); ?>
					<a class="ekit-price-card-item-link" <?php $this->print_render_attribute_string('link'.$item['_id']); ?>></a>
				<?php endif;

				if ($item['media_type'] == 'image' && !empty($image_html)) : ?>
					<div class="ekit-price-card-image">
						<?php echo wp_kses($image_html, \ElementsKit_Lite\Utils::get_kses_array()); ?>
					</div>
				<?php endif; 

				if ($item['media_type'] == 'icon' && !empty($item['icon']['value'])) : ?>
					<div class="ekit-price-menu-icon">
						<?php \Elementor\Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ); ?>
					</div>
				<?php endif; ?>
	
				<div class="ekit-price-card-caption">
					<div class="ekit-price-card-caption-header">
						<span class="ekit-price-card-caption-header-title"><?php echo esc_html($item['title']); ?></span>
						<?php if(!empty($item['description'])) : ?>
							<p class="ekit-price-card-caption-header-description"><?php echo esc_html($item['description']); ?></p>
						<?php endif; ?>
					</div>
					
					<div class="ekit-price-card-caption-footer">
						<span class="ekit-price-card-caption-footer-price"><?php echo esc_html($item['price']); ?></span>	
						<?php if ($item['ekit_price_menu_button_show'] == 'yes') : ?>
							<a class="ekit-price-card-caption-footer-button" <?php  $this->print_render_attribute_string('button_link'.$item['_id']); ?>>
							<?php
							$item['button_icon_position'] == 'before' && $item['button_icon_switch'] == 'yes' && \Elementor\Icons_Manager::render_icon( $item['button_icon'], [ 'aria-hidden' => 'true', 'class' => 'price-menu-button-icon-before' ]);	
							echo esc_html($item['button_text']); 	 
							$item['button_icon_position'] == 'after' && $item['button_icon_switch'] == 'yes' && \Elementor\Icons_Manager::render_icon( $item['button_icon'], [ 'aria-hidden' => 'true', 'class' => 'price-menu-button-icon-after' ] );
							?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</li>
			<?php endforeach; ?>
		</ul>

		<?php if(!empty($ekit_price_menu_show_dot)) : ?>
			<div class="swiper-pagination"></div>
		<?php endif; ?>

		<?php if(!empty($ekit_price_menu_show_arrow)) : ?>
			<div class="elementor-swiper-button ekit-price-card-slider-button-prev">
				<?php \Elementor\Icons_Manager::render_icon( $ekit_price_menu_slider_left_arrow_icon, [ 'aria-hidden' => 'true' ]); ?>
			</div>
			<div class="elementor-swiper-button ekit-price-card-slider-button-next">
				<?php \Elementor\Icons_Manager::render_icon( $ekit_price_menu_slider_right_arrow_icon, [ 'aria-hidden' => 'true' ]); ?>
			</div>
		<?php endif; ?>
	</div>
</div>