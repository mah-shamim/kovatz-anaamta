<?php
/**
 * Create JetWooBuilder Template Popup template.
 */
?>

<div class="jet-woo-builder-template-popup">
	<form id="jet-woo-builder-create-form" class="jet-woo-builder-create-form" method="POST" action="<?php echo $create_action; ?>" >
		<h3 class="jet-woo-builder-create-form__heading">
			<?php esc_html_e( 'Create Template', 'jet-woo-builder' ); ?>
		</h3>

		<div class="jet-woo-builder-create-form__row plain-row">
			<label for="template_type">
				<?php esc_html_e( 'This Template For:', 'jet-woo-builder' ); ?>
			</label>
			<select id="template_type" name="template_type">
				<?php
				foreach ( $doc_types as $type ) {
					printf(
						'<option value="%1$s" %3$s>%2$s</option>',
						$type['slug'],
						$type['name'],
						selected( $selected, $type['slug'], false )
					);
				}
				?>
			</select>
		</div>

		<div class="jet-woo-builder-create-form__row plain-row">
			<label for="template_name">
				<?php esc_html_e( 'Template Name:', 'jet-woo-builder' ); ?>
			</label>
			<input type="text" id="template_name" name="template_name" placeholder="<?php esc_html_e( 'Set Template Name', 'jet-woo-builder' ); ?>">
		</div>

		<h4 class="jet-woo-builder-create-form__subheading">
			<?php esc_html_e( 'Select Layout Preset', 'jet-woo-builder' ); ?>
		</h4>

		<div class="jet-woo-builder-create-form__row predesigned-row template-<?php echo $doc_types['single']['slug']; ?> is-active">
			<?php foreach ( $this->predesigned_templates( 'single', 8 ) as $id => $data ) : ?>
				<div class="jet-woo-builder-create-form__item">
					<label class="jet-woo-builder-create-form__label">
						<input type="radio" name="template_single" value="<?php echo $id; ?>">
						<img src="<?php echo $data['thumb']; ?>" alt="<?php echo $id; ?>">
					</label>
					<span class="jet-woo-builder-create-form__item-uncheck dashicons dashicons-no-alt"></span>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="jet-woo-builder-create-form__row predesigned-row template-<?php echo $doc_types['archive']['slug']; ?>">
			<?php foreach ( $this->predesigned_templates( 'archive' ) as $id => $data ) : ?>
				<div class="jet-woo-builder-create-form__item">
					<label class="jet-woo-builder-create-form__label">
						<input type="radio" name="template_archive" value="<?php echo $id; ?>">
						<img src="<?php echo $data['thumb']; ?>" alt="<?php echo $id; ?>">
					</label>
					<span class="jet-woo-builder-create-form__item-uncheck dashicons dashicons-no-alt"></span>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="jet-woo-builder-create-form__row predesigned-row template-<?php echo $doc_types['category']['slug']; ?>">
			<?php foreach ( $this->predesigned_templates( 'category' ) as $id => $data ) : ?>
				<div class="jet-woo-builder-create-form__item">
					<label class="jet-woo-builder-create-form__label">
						<input type="radio" name="template_category" value="<?php echo $id; ?>">
						<img src="<?php echo $data['thumb']; ?>" alt="<?php echo $id; ?>">
					</label>
					<span class="jet-woo-builder-create-form__item-uncheck dashicons dashicons-no-alt"></span>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="jet-woo-builder-create-form__row predesigned-row template-<?php echo $doc_types['shop']['slug']; ?>">
			<?php foreach ( $this->predesigned_templates( 'shop' ) as $id => $data ) : ?>
				<div class="jet-woo-builder-create-form__item">
					<label class="jet-woo-builder-create-form__label">
						<input type="radio" name="template_shop" value="<?php echo $id; ?>">
						<img src="<?php echo $data['thumb']; ?>" alt="<?php echo $id; ?>">
					</label>
					<span class="jet-woo-builder-create-form__item-uncheck dashicons dashicons-no-alt"></span>
				</div>
			<?php endforeach; ?>

			<div class="predesigned-templates__description">
				<?php esc_html_e( 'For creating this template, you need combine shop template and archive template in JetWooBuilder settings', 'jet-woo-builder' ); ?>
			</div>
		</div>

		<div class="jet-woo-builder-create-form__actions">
			<input  type="submit" id="templates_type_submit" class="button button-primary button-hero" value="<?php esc_html_e( 'Create Template', 'jet-woo-builder' ); ?>">
		</div>
	</form>
</div>