<?php
/**
 * Template type popup
 */
$popup_class = ! empty( $listing_id ) ? 'jet-listings-popup--' . $listing_id : 'jet-listings-popup--new';
$form_id     = ! empty( $listing_id ) ? 'templates_type_form_' . $listing_id : 'templates_type_form';

?>
<div class="jet-listings-popup <?php echo $popup_class; ?>" style="display: none;">
	<div class="jet-listings-popup__overlay"></div>
	<div class="jet-listings-popup__content">
		<div class="jet-listings-popup__close">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><rect x="0" fill="none" width="20" height="20"/><g><path d="M14.95 6.46L11.41 10l3.54 3.54-1.41 1.41L10 11.42l-3.53 3.53-1.42-1.42L8.58 10 5.05 6.47l1.42-1.42L10 8.58l3.54-3.53z"/></g></svg>
		</div>
		<h3 class="jet-listings-popup__heading"><?php
			esc_html_e( 'Setup Listing Item', 'jet-engine' );
		?></h3>
		<form class="jet-listings-popup__form" id="<?php echo $form_id; ?>" method="POST" action="<?php echo $action; ?>" >
			<?php
			
				include jet_engine()->get_template( 'admin/listing-settings-form.php' );

				if ( ! empty( $data['_listing_type'] ) && ! isset( $views[ $data['_listing_type'] ] ) ) {
					?>
					<div class="notice notice-error" style="margin: 0 0 15px;">
						<p><?php printf( 
							__( 'This listing uses <code style="text-transform: capitalize;">%s</code> type, which is not active right now. Please activate appropriate builder or change listing type and rebuild it with currently supported type.', 'jet-engine' ),
							$data['_listing_type']
						); ?></p>
					</div>
					<?php
				}
			?>
			<div class="jet-listings-popup__form-actions">
				<?php if ( $listing_id ) { ?>
					<div class="jet-listings-popup__form-actions-group">
						<button type="button" class="button button-primary button-hero jet-engine-listing-save open-editor" data-listing-id="<?php echo $listing_id; ?>"><?php
							esc_html_e( 'Save & Open Editor', 'jet-engine' );
						?></button>
						<button type="button" class="button button-primary button-hero jet-engine-listing-save" data-listing-id="<?php echo $listing_id; ?>"><?php
							esc_html_e( 'Save', 'jet-engine' );
						?></button>
						<button type="button" class="button button-hero jet-engine-listing-cancel"><?php
							esc_html_e( 'Cancel', 'jet-engine' );
						?></button>
					</div>
				<?php } else { ?>
					<button type="submit" id="templates_type_submit" class="button button-primary button-hero"><?php
						esc_html_e( 'Create Listing Item', 'jet-engine' );
					?></button>
				<?php } ?>
			</div>
		</form>
	</div>
</div>
