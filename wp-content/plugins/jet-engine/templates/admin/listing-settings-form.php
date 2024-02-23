<?php
/**
 * Listing settings form
 *
 * if used directly you need to define these variables before calling:
 * - $data    = [];
 * - $sources = jet_engine()->listings->post_type->get_listing_item_sources();
 * - $views   = jet_engine()->listings->post_type->get_listing_views();
 */
?>
<div class="jet-listings-popup__form-row">
	<label for="listing_source"><?php esc_html_e( 'Listing source:', 'jet-engine' ); ?></label>
	<select id="listing_source" name="listing_source" class="jet-listings-popup__control"><?php
		foreach ( $sources as $source_key => $source_label ) {
			printf( 
				'<option value="%1$s" %3$s>%2$s</option>', 
				$source_key, 
				$source_label,
				( ! empty( $data['listing_source'] ) ? selected( $data['listing_source'], $source_key, false ) : '' )
			);
		}
	?></select>
</div>
<div class="jet-listings-popup__form-row jet-template-listing jet-template-posts jet-template-repeater jet-template-act">
	<label for="listing_post_type"><?php esc_html_e( 'From post type:', 'jet-engine' ); ?></label>
	<select id="listing_post_type" name="listing_post_type" class="jet-listings-popup__control"><?php
		foreach ( jet_engine()->listings->get_post_types_for_options() as $key => $value ) {
			printf( 
				'<option value="%1$s" %3$s>%2$s</option>',
				$key,
				$value,
				( ! empty( $data['listing_post_type'] ) ? selected( $data['listing_post_type'], $key, false ) : '' )
			);
		}
	?></select>
</div>
<div class="jet-listings-popup__form-row jet-template-listing jet-template-terms">
	<label for="listing_tax"><?php esc_html_e( 'From taxonomy:', 'jet-engine' ); ?></label>
	<select id="listing_tax" name="listing_tax" class="jet-listings-popup__control"><?php
		foreach ( jet_engine()->listings->get_taxonomies_for_options() as $key => $value ) {
			printf( 
				'<option value="%1$s" %3$s>%2$s</option>',
				$key,
				$value,
				( ! empty( $data['listing_tax'] ) ? selected( $data['listing_tax'], $key, false ) : '' )
			);
		}
	?></select>
</div>
<div class="jet-listings-popup__form-row jet-template-listing jet-template-query">
	<label for="query_id"><?php esc_html_e( 'Query:', 'jet-engine' ); ?></label>
	<select id="query_id" name="_query_id" class="jet-listings-popup__control">
		<?php
			foreach ( \Jet_Engine\Query_Builder\Manager::instance()->get_queries_for_options() as $query_id => $query_name ) {
				printf( 
					'<option value="%1$s" %3$s>%2$s</option>',
					$query_id,
					$query_name,
					( ! empty( $data['_query_id'] ) ? selected( $data['_query_id'], $query_id, false ) : '' )
				);
			}
		?>
	</select>
</div>
<div class="jet-listings-popup__form-row jet-template-listing jet-template-repeater">
	<label for="repeater_source"><?php esc_html_e( 'Repeater source:', 'jet-engine' ); ?></label>
	<select id="repeater_source" name="repeater_source" class="jet-listings-popup__control"><?php
		foreach ( jet_engine()->listings->repeater_sources() as $source_id => $source_name ) {
			printf( 
				'<option value="%1$s" %3$s>%2$s</option>',
				$source_id,
				$source_name,
				( ! empty( $data['repeater_source'] ) ? selected( $data['repeater_source'], $source_id, false ) : '' )
			);
		}
	?></select>
</div>
<div class="jet-listings-popup__form-row jet-template-listing jet-template-repeater">
	<div class="jet-listings-popup__form-cols">
		<div class="jet-listings-popup__form-col">
			<label for="repeater_field">
				<?php esc_html_e( 'Repeater field:', 'jet-engine' ); ?><br>
				<small><?php _e( 'if JetEngine, or ACF, or etc selected as source', 'jet-engine' ); ?></small>
			</label>
			<?php $repeater_field = ! empty( $data['repeater_field'] ) ? $data['repeater_field'] : ''; ?>
			<input type="text" id="repeater_field" name="repeater_field" placeholder="<?php esc_html_e( 'Set repeater field name', 'jet-engine' ); ?>" value="<?php echo $repeater_field; ?>" class="jet-listings-popup__control">
		</div>
		<div class="jet-listings-popup__form-delimiter">
			- <?php _e( 'or', 'jet-engine' ); ?> -
		</div>
		<div class="jet-listings-popup__form-col">
			<label for="repeater_option">
				<?php esc_html_e( 'Repeater option:', 'jet-engine' ); ?><br>
				<small><?php _e( 'if <b>JetEngine Options Page</b> selected as source', 'jet-engine' ); ?></small>
			</label>
			<select id="repeater_option" name="repeater_option" class="jet-listings-popup__control">
				<option value="">--</option>
				<?php
				foreach ( jet_engine()->options_pages->get_options_for_select( 'repeater' ) as $group ) {

					if ( empty( $group ) || empty( $group['options'] ) ) {
						continue;
					}

					echo '<optgroup label="' . $group['label'] . '">';
					foreach ( $group['options'] as $opt_key => $opt_name ) {
						printf( 
							'<option value="%1$s" %3$s>%2$s</option>',
							$opt_key,
							$opt_name,
							( ! empty( $data['repeater_option'] ) ? selected( $data['repeater_option'], $opt_key, false ) : '' )
						);
					}
					echo '</optgroup>';
				}
			?></select>
		</div>
	</div>
</div>
<?php do_action( 'jet-engine/templates/listing-options', $data ); ?>
<?php if ( ! empty( $data['main_popup'] ) ) { ?>
<div class="jet-listings-popup__form-row">
	<label for="template_name"><?php esc_html_e( 'Listing item name:', 'jet-engine' ); ?></label>
	<?php $template_name = ! empty( $data['template_name'] ) ? $data['template_name'] : ''; ?>
	<input type="text" id="template_name" name="template_name" placeholder="<?php esc_html_e( 'Set listing name', 'jet-engine' ); ?>" value="<?php echo $template_name; ?>" class="jet-listings-popup__control">
</div>
<div class="jet-listings-popup__form-row">
	<label for="listing_view_type"><?php esc_html_e( 'Listing view:', 'jet-engine' ); ?></label>
	<select id="listing_view_type" name="listing_view_type" class="jet-listings-popup__control"><?php
		foreach ( $views as $view_key => $view_label ) {
			printf( 
				'<option value="%1$s" %3$s>%2$s</option>',
				$view_key,
				$view_label,
				( ! empty( $data['_listing_type'] ) ? selected( $data['_listing_type'], $view_key, false ) : '' )
			);
		}
	?></select>
</div>
<?php } // endif ! empty( $data['main_popup'] ) ?>