<?php
/**
 * .
 *
 * @package ACF: Better Search
 */

?>

<div class="acfbsPage__widget">
	<h3 class="acfbsPage__widgetTitle acfbsPage__widgetTitle--second">
		<?php echo esc_html( __( 'How does this work?', 'acf-better-search' ) ); ?>
	</h3>
	<div class="acfbsContent">
		<p>
			<?php
			echo wp_kses_post(
				__( 'Plugin changes all SQL queries by extending the standard search to selected fields of Advanced Custom Fields.', 'acf-better-search' )
			);
			?>
		</p>
		<p>
			<?php
			echo wp_kses_post(
				__( 'On search page and in admin panel everything works automatically. No need to add any additional code.', 'acf-better-search' )
			);
			?>
		</p>
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
				/* translators: %1$s: open anchor tag, %2$s: close anchor tag */
					__( 'For custom WP_Query loop and get_posts() function you must add %1$sSearch Parameter%2$s.', 'acf-better-search' ),
					'<a href="https://codex.wordpress.org/Class_Reference/WP_Query#Search_Parameter" target="_blank">',
					'</a>'
				)
			);
			?>
		</p>
	</div>
</div>
