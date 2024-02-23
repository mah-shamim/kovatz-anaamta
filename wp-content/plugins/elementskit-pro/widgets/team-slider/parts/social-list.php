<div class="profile-footer">
	<ul class="ekit-team-social-list">
		<?php 
		for ($i=0;$i<5;$i++) : 
			if(!empty($ekit_team_member['link_'.$i]['url'])) {
				$this->add_link_attributes( 'social_link_'.$i.$index, $ekit_team_member['link_'.$i] ); 
				$this->add_render_attribute( 'social_link_'.$i.$index, 'aria-label', 'social-link' );
			}

			if(!empty($ekit_team_member['icon_'.$i]['value'])) : ?>
				<li>
					<?php 
					$link = $this->get_render_attribute_string('social_link_'.$i.$index);
					$link = preg_replace('/href="(.+?)\s\1"/', 'href="$1"', $link);?>
					<a <?php echo $link; ?>>
						<?php \Elementor\Icons_Manager::render_icon( $ekit_team_member['icon_'.$i], [ 'aria-hidden' => 'true' ] ); ?>
					</a>
				</li>
			<?php endif;
		endfor; ?>
	</ul>
</div>
