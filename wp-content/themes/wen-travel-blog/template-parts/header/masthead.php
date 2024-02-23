<?php
/**
 * Displays header #masthead
 *
 * @package WEN_Travel
 */
?>

<header id="masthead" class="site-header">
	<?php get_template_part( 'template-parts/header-top/site-top-bar' ); ?>

	<div class="site-header-main">
		<div class="wrapper">
			<?php 
				get_template_part( 'template-parts/header/site-branding-style-two' );

				get_template_part( 'template-parts/navigation/navigation-primary-style-two' );
			?>
		</div><!-- .wrapper -->
	</div><!-- .site-header-main -->
</header><!-- #masthead -->
