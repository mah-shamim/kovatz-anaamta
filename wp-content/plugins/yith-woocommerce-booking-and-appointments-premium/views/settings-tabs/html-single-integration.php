<?php
/**
 * Single integration
 *
 * @var YITH_WCBK_Integration $integration
 *
 * @author  YITH
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$class = $integration->is_enabled() ? 'active' : '';

$badges = array();
if ( $integration->is_new() ) {
	$badges['new'] = _x( 'New', 'text of integration badge', 'yith-booking-for-woocommerce' );
}

?>
<div class="yith-wcbk-integration">
	<?php
	if ( ! ! $badges ) {
		foreach ( $badges as $badge_type => $badge_text ) {
			yith_wcbk_get_view(
				'settings-tabs/html-badge.php',
				array(
					'type' => $badge_type,
					'text' => $badge_text,
				)
			);
		}
	}
	?>
	<img class="yith-wcbk-integration-icon" src="<?php echo esc_url( $integration->get_icon() ); ?>"/>
	<h5><?php echo esc_html( $integration->get_title() ); ?></h5>
	<div class="yith-wcbk-integration-content">
		<div class="yith-wcbk-integration-description"><?php echo wp_kses_post( $integration->get_description() ); ?></div>
		<?php if ( ! $integration->is_component_active() ) : ?>
			<div class="yith-wcbk-integration-needs-plugin">
				<?php
				echo esc_html(
					sprintf(
					// translators: 1. plugin name; 2. minimum version.
						__( '(needs %1$s plugin – version %2$s or greater)', 'yith-booking-for-woocommerce' ),
						$integration->get_name(),
						$integration->get_min_version()
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>

	<div class="yith-wcbk-integration-actions">
		<?php if ( ! $integration->is_component_active() ) : ?>
			<a href="<?php echo esc_url( $integration->get_landing_uri() ); ?>" class="yith-wcbk-admin-button yith-wcbk-admin-button--dark yith-wcbk-admin-button--icon-download" target="_blank">
				<?php esc_html_e( 'Get Plugin', 'yith-booking-for-woocommerce' ); ?>
			</a>
		<?php else : ?>
			<?php if ( $integration->is_enabled() ) : ?>
				<?php if ( $integration->is_optional() ) : ?>
					<a href="<?php echo esc_url( $integration->get_deactivation_url() ); ?>" class="yith-wcbk-admin-button yith-wcbk-admin-button--icon-close yith-wcbk-integration-action deactivate"><?php esc_html_e( 'Deactivate integration', 'yith-booking-for-woocommerce' ); ?></a>
				<?php else : ?>
					<span class="yith-wcbk-integration-automatically-active"><?php esc_html_e( 'This integration is automatically active', 'yith-booking-for-woocommerce' ); ?></span>
				<?php endif; ?>
			<?php else : ?>
				<?php if ( $integration->is_optional() ) : ?>
					<a href="<?php echo esc_url( $integration->get_activation_url() ); ?>" class="yith-wcbk-admin-button yith-wcbk-admin-button--icon-check yith-wcbk-integration-action activate"><?php esc_html_e( 'Activate integration', 'yith-booking-for-woocommerce' ); ?></a>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
