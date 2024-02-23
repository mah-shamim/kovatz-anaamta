<?php
/**
 * .
 *
 * @var string   $settings_url      .
 * @var string   $submit_value      .
 * @var string[] $fields            .
 * @var mixed[]  $features_default  .
 * @var mixed[]  $features_advanced .
 * @var mixed[]  $config            .
 *
 * @package ACF: Better Search
 */

?>

<div class="wrap">
	<hr class="wp-header-end">
	<form method="post" action="<?php echo esc_url( $settings_url ); ?>" class="acfbsPage">
		<h1 class="acfbsPage__headline"><?php echo esc_html( __( 'ACF: Better Search', 'acf-better-search' ) ); ?></h1>
		<div class="acfbsPage__inner">
			<ul class="acfbsPage__columns">
				<li class="acfbsPage__column acfbsPage__column--large">
					<?php if ( isset( $_POST[ $submit_value ] ) ) : // phpcs:ignore ?>
						<div class="acfbsPage__alert">
							<?php echo esc_html( __( 'Changes were successfully saved!', 'acf-better-search' ) ); ?>
						</div>
					<?php endif; ?>
					<?php
					require_once dirname( __DIR__ ) . '/components/widgets/settings.php';
					?>
				</li>
				<li class="acfbsPage__column acfbsPage__column--small">
					<?php
					require_once dirname( __DIR__ ) . '/components/widgets/about.php';
					require_once dirname( __DIR__ ) . '/components/widgets/support.php';
					?>
				</li>
			</ul>
		</div>
	</form>
</div>
