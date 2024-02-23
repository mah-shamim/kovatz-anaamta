<?php
/**
 * Integration badge.
 *
 * @var string $type
 * @var string $text
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

?>
<div class="yith-wcbk-integration-badge <?php echo esc_attr( $type ); ?>">
	<div class="yith-wcbk-integration-badge-s1"></div>
	<div class="yith-wcbk-integration-badge-s2"></div>
	<div class="yith-wcbk-integration-badge-text"><?php echo esc_html( $text ); ?></div>
</div>
