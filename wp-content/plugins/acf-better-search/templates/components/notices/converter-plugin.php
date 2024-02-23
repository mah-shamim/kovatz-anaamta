<?php
/**
 * Notice displayed in admin panel.
 *
 * @var string $ajax_url     URL of admin-ajax.
 * @var string $close_action Action using in WP Ajax.
 *
 * @package ACF: Better Search
 */

?>

<div class="notice notice-success is-dismissible"
	data-notice="acf-better-search"
	data-notice-action="<?php echo esc_attr( $close_action ); ?>"
	data-notice-url="<?php echo esc_attr( $ajax_url ); ?>"
>
	<div class="acfbsContent acfbsContent--notice">
		<h4>
			<?php echo esc_html( __( 'New opportunities for your website', 'acf-better-search' ) ); ?>
		</h4>
		<p>
			<?php
			echo esc_html(
				sprintf(
				/* translators: %1$s: plugin name, %2$s: plugin name */
					__( 'We are glad you are using our %1$s plugin. We would like to introduce one of our highly rated plugins, %2$s, which can speed up your website by optimizing images to the WebP format and to the AVIF format.', 'acf-better-search' ),
					'ACF: Better Search',
					'Converter for Media'
				)
			);
			?>
		</p>
		<div class="acfbsContent__buttons">
			<a href="https://url.mattplugins.com/acf-notice-converter-plugin-button-read"
				target="_blank"
				class="acfbsContent__button acfbsButton acfbsButton--bg acfbsButton--blue">
				<?php echo esc_html( __( 'Explore new plugin', 'acf-better-search' ) ); ?>
			</a>
			<button type="button" data-permanently
				class="acfbsContent__button acfbsButton acfbsButton--bg acfbsButton--gray">
				<?php echo esc_html( __( 'Hide and do not show again', 'acf-better-search' ) ); ?>
			</button>
		</div>
	</div>
</div>
