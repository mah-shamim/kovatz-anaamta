<?php
/**
 * Global Availability Rules view
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit(); // Exit if accessed directly.
?>
<form id="yith-wcbk-global-availability" method="post" autocomplete="off">
	<div class="yith-plugin-fw-panel-custom-tab-container">
		<div class="yith-plugin-fw-panel-custom-sub-tab-container">
			<h2 class="yith-plugin-fw-panel-custom-tab-title"><?php echo esc_html( _x( 'Availability rules', 'Tab title in plugin settings panel', 'yith-booking-for-woocommerce' ) ); ?></h2>
			<div id="yith-wcbk-settings-tab-wrapper" class="global-availability">

				<div class="yith-wcbk-availability-rules__expand-collapse">
					<span class="yith-wcbk-availability-rules__expand"><?php esc_html_e( 'Expand all', 'yith-booking-for-woocommerce' ); ?></span>
					<span class="yith-wcbk-availability-rules__collapse"><?php esc_html_e( 'Collapse all', 'yith-booking-for-woocommerce' ); ?></span>
				</div>

				<div class="yith-wcbk-settings-section__description">
					<?php
					echo implode(
						'<br />',
						array(
							esc_html__( 'Create advanced rules to manage availability on specific dates.', 'yith-booking-for-woocommerce' ),
							esc_html__( 'These rules are global and applied to all bookable products by default. You can override them with specific rules on the product editing page.', 'yith-booking-for-woocommerce' ),
						)
					);
					?>
				</div>
				<?php
				$field_name         = 'yith_booking_global_availability_range';
				$availability_rules = yith_wcbk()->settings->get_global_availability_rules();
				require YITH_WCBK_VIEWS_PATH . 'product-tabs/utility/html-availability-rules.php';
				?>
			</div>
			<?php wp_nonce_field( 'yith_wcbk_settings_fields', 'yith_wcbk_nonce', false ); ?>
			<input type="hidden" name="yith-wcbk-settings-page" value="global-availability-rules">
		</div>

		<p class="submit" style="padding-bottom: 0">
			<input class="button-primary" id="yith-wcbk-settings-tab-actions-save" type="submit" value="<?php esc_html_e( 'Save rules', 'yith-booking-for-woocommerce' ); ?>">
		</p>
	</div>
</form>
