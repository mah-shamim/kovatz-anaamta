<?php

/**
 * Vendor Signup
 *
 * This file is used to output the vendor signup options on the WordPress login form.
 *
 * @link       http://www.wcvendors.com
 * @since      1.9.0
 *
 * @package    WCVendors
 * @subpackage WCVendors/classes/front/signup/views
 */

?>

<?php do_action( 'wcvendors_login_apply_for_vendor_before' ); ?>

<p>
	<label for="apply_for_vendor" class="<?php echo esc_attr( $apply_label_css_classes ); ?>">
		<?php wp_nonce_field( 'apply_for_vendor', 'apply_for_vendor_nonce' ); ?>
		<input class="input-checkbox"
			id="apply_for_vendor"
			<?php checked( isset( $_POST['apply_for_vendor'] ), true ); // phpcs:ignore ?>
			type="checkbox"
			name="apply_for_vendor" value="1"/>
		<?php

		$vendor_application_label = apply_filters(
			'wcvendors_vendor_registration_checkbox',
			sprintf(
				// translators: 1$s Is the become a vendor call to action, 2$s The name used to refer to a vendor.
				__( 'Apply to %1$s %2$s? ', 'wc-vendors' ),
				$become_a_vendor_label,
				wcv_get_vendor_name( true, false )
			)
		);

		echo esc_attr( $vendor_application_label );
		?>
	</label>
	<br/>
</p>

<?php do_action( 'wcvendors_login_apply_for_vendor_after' ); ?>

<?php if ( $this->terms_page ) : ?>

	<?php

	do_action( 'wcvendors_login_agree_to_terms_before' );

	$terms_and_conditions_visibility = get_option( 'wcvendors_terms_and_conditions_visibility' );

	$terms_visible = apply_filters(
		'wcvendors_terms_and_conditions_visibility',
		wc_string_to_bool( $terms_and_conditions_visibility )
	);

	$display = $terms_visible ? 'block' : 'none';

	?>
	<input
		type="hidden"
		id="terms_and_conditions_visibility"
		value="<?php echo esc_attr( $terms_and_conditions_visibility ); ?>"
	/>
	<p class="agree-to-terms-container" style="display: <?php echo esc_attr( $display ); ?>">
		<label for="agree_to_terms" class="<?php echo esc_attr( $term_label_css_classes ); ?>">
			<input class="input-checkbox"
					id="agree_to_terms" <?php checked( isset( $_REQUEST['agree_to_terms'] ), true ); // phpcs:ignore ?> type="checkbox"
					name="agree_to_terms" value="1"/>
			<?php
            $confirmation_text = apply_filters(
				'wcvendors_vendor_registration_terms',
				sprintf(
					// translators: 1$s Confirmation that user read to terms.
					// 2$s The link to terms page.
					// 3$s The terms and conditions button text.
					'%1$s <a target="top" href="%2$s">%3$s</a>',
					__( 'I have read and accepted the', 'wc-vendors' ),
					get_permalink( $this->terms_page ),
					__( 'terms and conditions', 'wc-vendors' )
				)
            );

			echo wp_kses_post( $confirmation_text );
                    ?>
		</label>
	</p>

	<?php do_action( 'wcvendors_login_agree_to_terms_after' ); ?>


	<script type="text/javascript">
		<?php
		$accept_terms_text = apply_filters(
			'wcvendors_vendor_terms_msg',
			sprintf(
				// translators: 1$s Is the name used to refer to a vendor.
				__( 'You must accept the terms and conditions to become a %s.', 'wc-vendors' ),
				wcv_get_vendor_name( true, false )
			)
		);
		?>

		var accept_terms_text = "<?php echo esc_attr( $accept_terms_text ); ?>";

		jQuery(function ($) {
			<?php if ( 'none' === $display ) : ?>
			jQuery("#apply_for_vendor").change(function () {
				if (this.checked) {
					jQuery('.agree-to-terms-container').show();
				} else {
					jQuery('.agree-to-terms-container').hide();
				}
			});
			<?php endif; ?>

			$('form.register').on('submit', function (e) {
				if (jQuery('#agree_to_terms').is(':visible') && !jQuery('#agree_to_terms').is(':checked')) {
					e.preventDefault();
					alert( window.accept_terms_text );
				}
			});

		});
	</script>

<?php endif; ?>

<div class="clear"></div>
