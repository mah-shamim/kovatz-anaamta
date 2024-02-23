<?php
/**
 * The Template for displaying profile page
 */

defined( 'ABSPATH' ) || exit;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
		<title><?php echo wp_get_document_title(); ?></title>
	<?php endif; ?>
	<?php wp_head(); ?>
	<?php echo apply_filters( 'jet-engine/profile-builder/template/viewport_tag', '<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />' ); ?>
</head>
<body <?php body_class(); ?>>

	<?php
	/**
	 * Hook before main page content output.
	 * Add template wrappers start on this hook
	 */
	do_action( 'jet-engine/profile-builder/template/before-main-content' );

	/**
	 * Hoor to display main page content
	 */
	do_action( 'jet-engine/profile-builder/template/main-content' );

	/**
	 * Hook before main page content output.
	 * Add template wrappers start on this hook
	 */
	do_action( 'jet-engine/profile-builder/template/after-main-content' );

	wp_footer();

	?>
	</body>
</html>
