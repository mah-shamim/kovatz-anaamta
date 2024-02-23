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
			<?php
			echo esc_html(
				sprintf(
				/* translators: %s: plugin name */
					__( 'Thank you for using our %s plugin!', 'acf-better-search' ),
					'ACF: Better Search'
				)
			);
			?>
		</h4>
		<p>
			<?php
			echo wp_kses_post(
				__( 'We are glad that you are using our plugin and we hope you are satisfied with it. If you want, you can support us in the development of the plugin by adding a plugin review. This is very important and gives us the opportunity to create even better tools for you. Thank you!', 'acf-better-search' )
			);
			?>
		</p>
		<div class="acfbsContent__buttons">
			<a href="https://wordpress.org/support/plugin/acf-better-search/reviews/#new-post"
				target="_blank"
				class="acfbsContent__button acfbsButton acfbsButton--bg acfbsButton--blue">
				<?php echo esc_html( __( 'Add a plugin review', 'acf-better-search' ) ); ?>
			</a>
			<button type="button" data-permanently
				class="acfbsContent__button acfbsButton acfbsButton--bg acfbsButton--gray">
				<?php echo esc_html( __( 'Hide and do not show again', 'acf-better-search' ) ); ?>
			</button>
		</div>
	</div>
</div>
