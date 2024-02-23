<?php
/**
 * Taxonomy Tiles widget item template.
 *
 * This template can be overridden by copying it to yourtheme/jet-woo-builder/widgets/global/taxonomy-tiles.php.
 */

$settings = $this->get_settings_for_display();

$title        = jet_woo_builder_tools()->trim_text( $taxonomy->name, $settings['title_length'], 'word', '...' );
$title_tag    = isset( $settings['title_html_tag'] ) ? jet_woo_builder_tools()->sanitize_html_tag( $settings['title_html_tag'] ) : 'h5';
$description  = jet_woo_builder_tools()->trim_text( $taxonomy->description, $settings['desc_length'], 'symbols', '...' );
$before_count = isset( $settings['count_before_text'] ) ? esc_html__( $settings['count_before_text'], 'jet_woo_builder' ) : '(';
$after_count  = isset( $settings['count_after_text'] ) ? esc_html__( $settings['count_after_text'], 'jet_woo_builder' ) : ')';
$target_attr  = 'yes' === $settings['open_new_tab'] ? 'target="_blank"' : '';

if ( is_rtl() ) {
	$before_count = $after_count;
	$after_count  = $before_count;
}
?>

<div class="jet-woo-taxonomy-item">
	<div class="jet-woo-taxonomy-item__box" <?php $this->get_taxonomy_background( $taxonomy ); ?>>
		<div class="jet-woo-taxonomy-item__box-content">
			<div class="jet-woo-taxonomy-item__box-inner">
				<?php
				if ( ! empty( $title )  ) {
					printf( '<%2$s class="jet-woo-taxonomy-item__box-title">%1$s</%2$s>', $title, $title_tag );
				}

				if ( 'yes' === $settings['show_taxonomy_count'] ) {
					printf( '<div class="jet-woo-taxonomy-item__box-count">%2$s %1$s %3$s</div>', $taxonomy->count, $before_count, $after_count );
				}

				if ( ! empty( $description )  ) {
					printf( '<div class="jet-woo-taxonomy-item__box-description">%s</div>', $description );
				}
				?>
			</div>
		</div>

		<a href="<?php echo esc_url( get_category_link( $taxonomy->term_id ) ) ?>" class="jet-woo-taxonomy-item__box-link" <?php echo $target_attr; ?> ></a>
	</div>
</div>