<?php
/**
 * .
 *
 * @package ACF: Better Search
 */

?>

<div class="acfbsPage__widget">
	<h3 class="acfbsPage__widgetTitle acfbsPage__widgetTitle--second">
		<?php echo esc_html( __( 'We are waiting for your message', 'acf-better-search' ) ); ?>
	</h3>
	<div class="acfbsContent">
		<p>
			<?php
			echo wp_kses_post(
				__( 'Do you have a technical problem? Please contact us. We will be happy to help you. Or maybe you have an idea for a new feature? Please let us know about it by filling the support form. We will try to add it!', 'acf-better-search' )
			);
			?>
		</p>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
				/* translators: %1$s: open anchor tag, %2$s: close anchor tag, %3$s: open anchor tag, %4$s: close anchor tag */
					__( 'Please %1$scheck our FAQ%2$s before adding a thread with technical problem. If you do not find help there, %3$scheck support forum%4$s for similar problems.', 'acf-better-search' ),
					'<a href="https://wordpress.org/plugins/acf-better-search/#faq" target="_blank">',
					'</a>',
					'<a href="https://wordpress.org/support/plugin/acf-better-search/" target="_blank">',
					'</a>'
				)
			);
			?>
		</p>
		<p class="center">
			<a href="https://wordpress.org/support/plugin/acf-better-search/#new-post" target="_blank"
				class="acfbsButton acfbsButton--blue">
				<?php echo esc_html( __( 'Get help', 'acf-better-search' ) ); ?>
			</a>
		</p>
		<p>
			<?php
			echo wp_kses_post(
				__( 'Do you like our plugin? Could you rate him? Please let us know what you think about our plugin. It is important that we can develop this tool. Thank you for all the reviews.', 'acf-better-search' )
			);
			?>
		</p>
		<p class="center">
			<a href="https://wordpress.org/support/plugin/acf-better-search/reviews/#new-post" target="_blank"
				class="acfbsButton acfbsButton--blue">
				<?php echo esc_html( __( 'Add a plugin review', 'acf-better-search' ) ); ?>
			</a>
		</p>
	</div>
</div>
