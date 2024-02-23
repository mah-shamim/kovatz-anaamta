<?php
/**
 * Admin View: Setup Steps
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<ol class="wcv-setup-steps">
	<?php foreach ( $output_steps as $step_key => $step ) : ?>
		<li class="
			<?php
		if ( $step_key === $this->step ) {
			echo 'active';
		} elseif ( array_search( $this->step, array_keys( $this->steps ), true ) > array_search( $step_key, array_keys( $this->steps ), true ) ) {
			echo 'done';
		}
		?>
		"><?php echo esc_html( $step['name'] ); ?></li>
	<?php endforeach; ?>
</ol>
