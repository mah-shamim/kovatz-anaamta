<?php
/**
 * Template options in WC Product Panel
 *
 * @var YITH_WCBK_Availability_Rule[] $availability_rules The availability rules.
 * @var string                        $field_name         The field name.
 *
 * @author  YITH
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

?>
<div class="yith-wcbk-availability-rules">
	<div class="yith-wcbk-settings-section-box__sortable-container yith-wcbk-availability-rules__list">
		<?php
		$index = 1;
		foreach ( $availability_rules as $key => $availability_rule ) {
			yith_wcbk_get_view( 'product-tabs/utility/html-availability-rule.php', compact( 'field_name', 'index', 'availability_rule' ) );
			$index ++;
		}
		?>
	</div>
	<div class="yith-wcbk-settings-section__content__actions">
		<span class="yith-plugin-fw__button--add yith-wcbk-availability-rules__new-rule"><?php esc_html_e( 'Add rule', 'yith-booking-for-woocommerce' ); ?></span>
		<div id="yith-wcbk-availability-rules__pre-new-rule"></div>
	</div>
</div>

<script type="text/html" id="tmpl-yith-wcbk-availability-rule">
	<?php
	yith_wcbk_get_view(
		'product-tabs/utility/html-availability-rule.php',
		array(
			'field_name'        => $field_name,
			'index'             => '{{data.ruleIndex}}',
			'availability_rule' => new YITH_WCBK_Availability_Rule(),
			'add_button'        => true,
		)
	);
	?>
</script>

<script type="text/html" id="tmpl-yith-wcbk-availability-rule-date-range">
	<?php
	yith_wcbk_get_view(
		'product-tabs/utility/html-availability-rule-date-range.php',
		array(
			'field_name'       => $field_name,
			'index'            => '{{data.ruleIndex}}',
			'date_range_index' => '{{data.dateRangeIndex}}',
			'from'             => '',
			'to'               => '',
		)
	);
	?>
</script>

<script type="text/html" id="tmpl-yith-wcbk-availability-rule-availability">
	<?php
	$index        = '{{data.ruleIndex}}';
	$_field_name  = "{$field_name}[{$index}][availabilities]";
	$availability = new YITH_WCBK_Availability();

	yith_wcbk_get_view(
		'product-tabs/utility/html-availability.php',
		array(
			'main_class'   => 'yith-wcbk-availability-rule',
			'field_name'   => $_field_name,
			'index'        => '{{data.availabilityIndex}}',
			'availability' => $availability,
		)
	);
	?>
</script>

<script type="text/html" id="tmpl-yith-wcbk-availability-rule-availability-time-slot">
	<?php
	$index       = '{{data.ruleIndex}}';
	$_field_name = "{$field_name}[{$index}][availabilities][{{data.availabilityIndex}}][time_slots]";

	yith_wcbk_get_view(
		'product-tabs/utility/html-availability-time-slot.php',
		array(
			'main_class' => 'yith-wcbk-availability-rule',
			'field_name' => $_field_name,
			'index'      => '{{data.timeSlotIndex}}',
			'from'       => '00:00',
			'to'         => '00:00',
		)
	);
	?>
</script>
