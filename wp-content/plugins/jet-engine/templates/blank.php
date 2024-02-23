<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title><?php echo wp_get_document_title(); ?></title>
		<?php endif; ?>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php

		do_action( 'jet-engine/blank-page/before-content' );

		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;

		do_action( 'jet-engine/blank-page/after-content' );

		wp_footer();
		?>
		<div id="secondary"></div>
	</body>
</html>