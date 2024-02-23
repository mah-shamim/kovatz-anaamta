<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

\Elementor\Plugin::$instance->frontend->add_body_class( 'elementor-template-canvas' );
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> lang="">
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<?php if ( ! current_theme_supports( 'title-tag' ) ) : ?>
			<title><?php echo wp_get_document_title(); ?></title>
		<?php endif; ?>
		<?php wp_head(); ?>
		<?php
		// Keep the following line after `wp_head()` call, to ensure it's not overridden by another templates.
		echo \Elementor\Utils::get_meta_viewport( 'canvas' );
		?>
	</head>
	<body <?php body_class(); ?>>
		<?php
		do_action( 'jet-woo-builder/blank-page/before-content' );

		$wc_data = new WC_Structured_Data;

		$wc_data->generate_product_data();

		$template                 = jet_woo_builder()->woocommerce->get_custom_shop_template();
		$taxonomy_template = get_term_meta( get_queried_object_id(), 'jet_woo_builder_template', true );

		if ( is_product_taxonomy() && ! empty( $taxonomy_template ) ) {
			$template = jet_woo_builder()->woocommerce->get_custom_product_taxonomy_template();
		}

		$template = apply_filters( 'jet-woo-builder/current-template/template-id', $template );

		jet_woo_builder()->admin_bar->register_post_item( $template );

		echo jet_woo_builder_template_functions()->get_woo_builder_content( $template );

		do_action( 'jet-woo-builder/blank-page/after-content' );

		wp_footer();
		?>
	</body>
</html>
