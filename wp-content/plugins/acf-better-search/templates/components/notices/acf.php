<?php
/**
 * Notice displayed in admin panel.
 *
 * @package ACF: Better Search
 */

?>

<div class="notice notice-error"
	data-notice="acf-better-search"
>
	<div class="acfbsContent acfbsContent--notice">
		<h4>
			<?php echo esc_html( __( 'ACF: Better Search error!', 'acf-better-search' ) ); ?>
		</h4>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
				/* translators: %1$s: open anchor tag, %2$s: close anchor tag */
					__( 'Unfortunately, but this plugin requires %1$sthe Advanced Custom Field plugin%2$s for all functionalities to work properly. Our plugin is an extension for the Advanced Custom Field plugin - our plugin extends the capabilities of that plugin.', 'acf-better-search' ),
					'<a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">',
					'</a>'
				)
			);
			?>
		</p>
	</div>
</div>
