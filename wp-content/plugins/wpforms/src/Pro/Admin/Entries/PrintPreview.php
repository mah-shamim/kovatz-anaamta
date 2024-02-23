<?php

namespace WPForms\Pro\Admin\Entries;

/**
 * Print view for single form entries.
 *
 * @package    WPForms\Pro\Admin\Entries
 * @author     WPForms
 * @since      1.5.1
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 201p, WPForms LLC
 */
class PrintPreview {

	/**
	 * Entry object.
	 *
	 * @since 1.5.1
	 *
	 * @var object
	 */
	public $entry;

	/**
	 * Form data.
	 *
	 * @since 1.5.1
	 *
	 * @var array
	 */
	public $form_data;

	/**
	 * Constructor.
	 *
	 * @since 1.5.1
	 */
	public function __construct() {

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.5.1
	 */
	public function hooks() {

		\add_action( 'admin_init', array( $this, 'print_html' ), 1 );
	}

	/**
	 * Check if current page request meets requirements for entry print page.
	 *
	 * @since 1.5.1
	 *
	 * @return bool
	 */
	public function is_print_page() {

		// Only proceed for the form builder.
		if ( ! \wpforms_is_admin_page( 'entries', 'print' ) ) {
			return false;
		}

		// Check for user with correct capabilities.
		if ( ! \wpforms_current_user_can() ) {
			return false;
		}

		// Check that entry ID was passed.
		if ( empty( $_GET['entry_id'] ) ) { //phpcs:ignore;
			return false;
		}

		// Fetch the entry.
		$this->entry = \wpforms()->entry->get( \absint( $_GET['entry_id'] ) ); //phpcs:ignore

		// Check valid entry was found.
		if ( empty( $this->entry ) ) {
			return false;
		}

		// Fetch form details for the entry.
		$this->form_data = \wpforms()->form->get(
			$this->entry->form_id,
			array(
				'content_only' => true,
			)
		);

		// Check valid form was found.
		if ( empty( $this->form_data ) ) {
			return false;
		}

		// Everything passed, fetch entry notes.
		$this->entry->entry_notes = \wpforms()->entry_meta->get_meta(
			array(
				'entry_id' => $this->entry->entry_id,
				'type'     => 'note',
			)
		);

		return true;
	}

	/**
	 * Outputs HTML markup for the print preview page.
	 *
	 * @since 1.5.1
	 */
	public function print_html() {

		// Under normal circumstances this should never return false.
		if ( ! $this->is_print_page() ) {
			return;
		}

		$min = \wpforms_get_min_suffix();
		?>
		<!doctype html>
		<html>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
			<title>WPForms Print Preview - <?php echo ucfirst( \esc_html( \sanitize_text_field( $this->form_data['settings']['form_title'] ) ) ); ?> </title>
			<meta name="description" content="">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<meta name="robots" content="noindex,nofollow,noarchive">
			<link rel="stylesheet" href="<?php echo \esc_url( \includes_url( 'css/buttons.min.css' ) ); ?>" type="text/css">
			<link rel="stylesheet" href="<?php echo \WPFORMS_PLUGIN_URL; ?>assets/css/entry-print<?php echo $min; ?>.css" type="text/css">
			<script type="text/javascript" src="<?php echo \esc_url( \includes_url( 'js/jquery/jquery.js' ) ); ?>"></script>
			<script type="text/javascript">
				jQuery( function( $ ){
					var showEmpty   = false,
						showNotes   = false,
						showCompact = false;
					// Print page.
					$( document ).on( 'click', '.print', function( event ) {
						event.preventDefault();
						window.print();
					} );
					// Close page.
					$( document ).on( 'click', '.close-window', function( event ) {
						event.preventDefault();
						window.close();
					} );
					// Toggle empty fields.
					$( document ).on( 'click', '.toggle-empty', function( event ) {
						event.preventDefault();
						if ( ! showEmpty ) {
							$( this ).text( '<?php \esc_html_e( 'Hide empty fields', 'wpforms-lite' ); ?>' );
						} else {
							$( this ).text( '<?php \esc_html_e( 'Show empty fields', 'wpforms-lite' ); ?>' );
						}
						$( '.field.empty' ).toggle();
						showEmpty = !showEmpty;
					} );
					// Toggle notes.
					$( document ).on( 'click', '.toggle-notes', function( event ) {
						event.preventDefault();
						if ( ! showNotes ) {
							$( this ).text( '<?php \esc_html_e( 'Hide notes', 'wpforms-lite' ); ?>' );
						} else {
							$( this ).text( '<?php \esc_html_e( 'Show notes', 'wpforms-lite' ); ?>' );
						}
						$( '.notes, .notes-head' ).toggle();
						showNotes = !showNotes;
					});
					// Toggle compact view.
					$( document ).on( 'click', '.toggle-view', function( event ) {
						event.preventDefault();
						if ( ! showCompact ) {
							$( this ).text( '<?php \esc_html_e( 'Normal view', 'wpforms-lite' ); ?>' );
						} else {
							$( this ).text( '<?php \esc_html_e( 'Compact view', 'wpforms-lite' ); ?>' );
						}
						$( '#print' ).toggleClass( 'compact' );
						showCompact = !showCompact;
					} );
				} );
			</script>
			<?php \do_action( 'wpforms_pro_admin_entries_printpreview_print_html_head', $this->entry, $this->form_data ); ?>
		</head>
		<body class="wp-core-ui">
			<div class="wpforms-preview" id="print">
				<?php \do_action( 'wpforms_pro_admin_entries_printpreview_print_html_header_before', $this->entry, $this->form_data ); ?>
				<h1>
					<?php /* translators: %d - entry ID. */ ?>
					<?php echo \esc_html( \sanitize_text_field( $this->form_data['settings']['form_title'] ) ); ?> <span> - <?php printf( \esc_html__( 'Entry #%d', 'wpforms-lite' ), \absint( $this->entry->entry_id ) ); ?></span>
					<div class="buttons">
						<a href="" class="button button-secondary close-window"><?php \esc_html_e( 'Close', 'wpforms-lite' ); ?></a>
						<a href="" class="button button-primary print"><?php \esc_html_e( 'Print', 'wpforms-lite' ); ?></a>
					</div>
				</h1>
				<div class="actions">
					<a href="#" class="toggle-empty"><?php \esc_html_e( 'Show empty fields', 'wpforms-lite' ); ?></a> &bull;
					<?php echo ! empty( $this->entry->entry_notes ) ? '<a href="#" class="toggle-notes">' . \esc_html__( 'Show notes', 'wpforms-lite' ) . '</a> &bull;' : ''; ?>
					<a href="#" class="toggle-view"><?php \esc_html_e( 'Compact view', 'wpforms-lite' ); ?></a>
				</div>
				<?php
				\do_action_deprecated(
					'wpforms_pro_admin_entries_printpreview_print_hrml_header_after',
					array( $this->entry, $this->form_data ),
					'1.5.5 of the WPForms plugin',
					'wpforms_pro_admin_entries_printpreview_print_html_header_after'
				);
				\do_action( 'wpforms_pro_admin_entries_printpreview_print_html_header_after', $this->entry, $this->form_data );
				$fields = \apply_filters( 'wpforms_entry_single_data', \wpforms_decode( $this->entry->fields ), $this->entry, $this->form_data );

				if ( empty( $fields ) ) {

					// Whoops, no fields! This shouldn't happen under normal use cases.
					echo '<p class="no-fields">' . \esc_html__( 'This entry does not have any fields', 'wpforms-lite' ) . '</p>';

				} else {

					echo '<div class="fields">';

					// Display the fields and their values.
					foreach ( $fields as $key => $field ) {

						$field_value  = \apply_filters( 'wpforms_html_field_value', \wp_strip_all_tags( $field['value'] ), $field, $this->form_data, 'entry-single' );
						$field_class  = \sanitize_html_class( 'wpforms-field-' . $field['type'] );
						$field_class .= empty( $field_value ) ? ' empty' : '';
						echo '<div class="field ' . \esc_attr( $field_class ) . '">';
							echo '<p class="field-name">';
								/* translators: %d - field ID. */
								echo ! empty( $field['name'] ) ? \esc_html( \wp_strip_all_tags( $field['name'] ) ) : sprintf( \esc_html__( 'Field ID #%d', 'wpforms-lite' ), \absint( $field['id'] ) );
							echo '</p>';
							echo '<p class="field-value">';
								echo ! empty( $field_value ) ? nl2br( \make_clickable( $field_value ) ) : \esc_html__( 'Empty', 'wpforms-lite' ); //phpcs:ignore
							echo '</p>';
						echo '</div>';
					}
					echo '</div>';
				}

				\do_action_deprecated(
					'wpforms_pro_admin_entries_printpreview_print_hrml_fields_after',
					array( $this->entry, $this->form_data ),
					'1.5.5 of the WPForms plugin',
					'wpforms_pro_admin_entries_printpreview_print_html_fields_after'
				);
				\do_action( 'wpforms_pro_admin_entries_printpreview_print_html_fields_after', $this->entry, $this->form_data );

				if ( ! empty( $this->entry->entry_notes ) ) {

					echo '<h2 class="notes-head">' . \esc_html__( 'Notes', 'wpforms-lite' ) . '</h2>';
					echo '<div class="notes">';

					foreach ( $this->entry->entry_notes as $note ) {

						$user        = \get_userdata( $note->user_id );
						$user_name   = \esc_html( ! empty( $user->display_name ) ? $user->display_name : $user->user_login );
						$date_format = sprintf( '%s %s', \get_option( 'date_format' ), \get_option( 'time_format' ) );
						$date        = \date_i18n( $date_format, strtotime( $note->date ) + ( \get_option( 'gmt_offset' ) * 3600 ) );

						echo '<div class="note">';
							echo '<div class="note-byline">';
								/* translators: %1$s - user name; %2$s - date */
								printf( \esc_html__( 'Added by %1$s on %2$s', 'wpforms-lite' ), $user_name, $date );
							echo '</div>';
							echo '<div class="note-text">' . \wp_kses_post( $note->data ) . '</div>';
						echo '</div>';
					}
					echo '</div>';
				}

				\do_action_deprecated(
					'wpforms_pro_admin_entries_printpreview_print_hrml_notes_after',
					array( $this->entry, $this->form_data ),
					'1.5.5 of the WPForms plugin',
					'wpforms_pro_admin_entries_printpreview_print_html_notes_after'
				);
				\do_action( 'wpforms_pro_admin_entries_printpreview_print_html_notes_after', $this->entry, $this->form_data );
				?>
			</div>
			<p class="site"><a href="<?php echo \esc_url( \home_url() ); ?>"><?php echo \esc_html( \get_bloginfo( 'name' ) ); ?></a></p>
		</body>
		<?php
		exit();
	}
}
