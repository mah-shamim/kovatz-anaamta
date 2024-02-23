<?php
/**
 * Import JetWooBuilder Template Popup template.
 */
?>

<div class="jet-woo-builder-template-popup">
	<a id="jet-woo-builder-import-trigger" href="#" class="page-title-action">
		<?php esc_html_e( 'Import Template', 'jet-woo-builder' ); ?>
	</a>

	<form id="jet-woo-builder-import-form" class="jet-woo-builder-import-form" method="post" action="<?php echo $import_action ?>" enctype="multipart/form-data">
		<fieldset class="jet-woo-builder-import-form__inputs">
			<input type="file" class="file-button" name="file" accept=".json,application/json,.zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed" required>
			<input type="submit" class="button button-primary button-hero" value="<?php esc_html_e( 'Import Now', 'jet-woo-builder' ); ?>">
		</fieldset>
	</form>
</div>
