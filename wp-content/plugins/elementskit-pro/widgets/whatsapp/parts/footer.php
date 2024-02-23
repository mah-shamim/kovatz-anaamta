<div class="elementskit-whatsapp__input <?php echo esc_html($footer_class); ?>">
	<div class="elementskit-whatsapp__input--wrapper">
		<?php if($ekit_whatsapp_footer_style == "button"): ?>
			<a href="https://wa.me/<?php esc_attr_e($whatsapp_number, 'elementskit'); ?>" target='<?php echo esc_attr($ekit_whatsapp_footer_link_target) ?>' class="elementskit-whatsapp__input--button">
				<?php !empty($ekit_whatsapp_footer_btn_icon['value']) && \Elementor\Icons_Manager::render_icon( $ekit_whatsapp_footer_btn_icon, [ 'aria-hidden' => 'true', 'class' => 'whatsapp-footer-btn-icon' ]); ?>
				<span class="elementskit-whatsapp__input--button-text">	
					<?php echo esc_html( $ekit_whatsapp_input_footer_btn_text ); ?>
				</span>
			</a>
		<?php else : ?>
			<input name="text" type="text" placeholder="<?php echo esc_attr($whatsapp_input_placeholder, 'elementskit'); ?>" class="elementskit-whatsapp__input--field">
			<a href="https://api.whatsapp.com/send?phone=<?php echo esc_attr($whatsapp_number, 'elementskit'); ?>&text=" target='<?php echo esc_attr($ekit_whatsapp_footer_link_target) ?>' class="elementskit-whatsapp__input--btn" aria-label="massage-sent">
				<svg fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M0.453195 17.3115C0.311505 17.1931 0.197496 17.0451 0.119195 16.8778C0.0408957 16.7106 0.000209086 16.5282 -7.144e-07 16.3436C0.000171907 16.1958 0.0257933 16.0491 0.0757392 15.91L2.38527 9.44329L8.38541 9.44329C8.55436 9.44329 8.71639 9.37618 8.83585 9.25671C8.95532 9.13725 9.02243 8.97522 9.02243 8.80627C9.02243 8.63732 8.95532 8.47529 8.83585 8.35582C8.71639 8.23636 8.55436 8.16924 8.38541 8.16924L2.38527 8.16924L0.075736 1.70257C-0.0114937 1.4583 -0.0220929 1.19322 0.0453577 0.942766C0.112807 0.692312 0.255092 0.468409 0.453196 0.300983C0.651299 0.133556 0.895784 0.0305807 1.15398 0.00581934C1.41217 -0.0189421 1.67177 0.0356899 1.89809 0.162414L15.3484 7.69458C15.546 7.80525 15.7106 7.96659 15.8251 8.16199C15.9396 8.35739 16 8.57978 16 8.80627C16 9.03275 15.9396 9.25515 15.8251 9.45055C15.7106 9.64594 15.546 9.80728 15.3484 9.91795L1.89805 17.4501C1.67216 17.5783 1.41227 17.6338 1.15374 17.609C0.895214 17.5842 0.650603 17.4803 0.453195 17.3115Z" fill="#757575"/>
				</svg>
			</a>
		<?php endif;?>
	</div>
</div>