<?php
/**
 * .
 *
 * @var string   $submit_value      .
 * @var string[] $fields            .
 * @var mixed[]  $features_default  .
 * @var mixed[]  $features_advanced .
 * @var mixed[]  $config            .
 *
 * @package ACF: Better Search
 */

?>

<div class="acfbsPage__widget">
	<h3 class="acfbsPage__widgetTitle">
		<?php echo esc_html( __( 'Settings', 'acf-better-search' ) ); ?>
	</h3>
	<div class="acfbsContent">
		<div class="acfbsPage__widgetRow">
			<h4><?php echo esc_html( __( 'List of supported fields types', 'acf-better-search' ) ); ?></h4>
			<?php require dirname( __DIR__ ) . '/settings/fields.php'; ?>
		</div>
		<div class="acfbsPage__widgetRow">
			<h4><?php echo esc_html( __( 'Additional features', 'acf-better-search' ) ); ?></h4>
			<?php
			$features = $features_default;
			require dirname( __DIR__ ) . '/settings/features.php';
			?>
		</div>
		<div class="acfbsPage__widgetRow">
			<h4><?php echo esc_html( __( 'Advanced settings', 'acf-better-search' ) ); ?></h4>
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						__( 'The configuration only for Developers.', 'acf-better-search' ),
						'<strong>',
						'</strong>'
					)
				);
				?>
			</p>
			<?php
			$features = $features_advanced;
			require dirname( __DIR__ ) . '/settings/features.php';
			?>
		</div>
		<div class="acfbsPage__widgetRow">
			<button type="submit"
				name="<?php echo esc_attr( $submit_value ); ?>"
				class="acfbsButton acfbsButton--bg acfbsButton--blue">
				<?php echo esc_html( __( 'Save Changes', 'acf-better-search' ) ); ?>
			</button>
		</div>
	</div>
</div>
