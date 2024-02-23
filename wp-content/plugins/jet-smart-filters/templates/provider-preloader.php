<?php
/**
 * Provider preloader template
 */
?>

<div class="jsf_provider-preloader">
	<div class="jsf_provider-preloader-<?php echo esc_attr( $type ); ?>">
		<?php
			if ( $structure ) {
				for ( $i = 0; $i < $structure; $i++ ) {
					echo '<div></div>';
				}
			} else if ( $type === 'circle-clip-growing' ) {
				?><svg width="0" height="0" viewBox="0 0 50 50"><circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle></svg><?php
			}
		?>
	</div>
</div>