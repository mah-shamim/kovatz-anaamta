<?php

/**
 * Display list of all entries for a single form.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
class WPForms_Entries_List {

	/**
	 * Holds admin alerts.
	 *
	 * @since 1.1.6
	 *
	 * @var array
	 */
	public $alerts = array();

	/**
	 * Abort. Bail on proceeding to process the page.
	 *
	 * @since 1.1.6
	 *
	 * @var bool
	 */
	public $abort = false;

	/**
	 * Form ID.
	 *
	 * @since 1.1.6
	 *
	 * @var int
	 */
	public $form_id;

	/**
	 * Form object.
	 *
	 * @since 1.1.6
	 *
	 * @var WPForms_Form_Handler
	 */
	public $form;

	/**
	 * Forms object.
	 *
	 * @since 1.1.6
	 *
	 * @var WPForms_Form_Handler[]
	 */
	public $forms;

	/**
	 * Entries object.
	 *
	 * @since 1.1.6
	 *
	 * @var WPForms_Entries_Table
	 */
	public $entries;

	/**
	 * Array of entries to select for filtering.
	 *
	 * @since 1.4.4
	 *
	 * @var array
	 */
	protected $filter = array();

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Maybe load entries page.
		add_action( 'admin_init', array( $this, 'init' ) );

		// Setup screen options - this needs to run early.
		add_action( 'load-wpforms_page_wpforms-entries', array( $this, 'screen_options' ) );
		add_filter( 'set-screen-option', array( $this, 'screen_options_set' ), 10, 3 );

		// Heartbeat doesn't pass $_GET parameters checked by $this->init() condition.
		add_filter( 'heartbeat_received', array( $this, 'heartbeat_new_entries_check' ), 10, 3 );
	}

	/**
	 * Determine if the user is viewing the entries list page, if so, party on.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		// Check what page and view.
		$page = ! empty( $_GET['page'] ) ? $_GET['page'] : '';
		$view = $this->get_current_screen_view();

		// Only load if we are actually on the overview page.
		if ( 'wpforms-entries' === $page && 'list' === $view ) {

			// Load the classes that builds the entries table.
			if ( ! class_exists( 'WP_List_Table', false ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
			}
			require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/entries/class-entries-list-table.php';

			// Processing and setup.
			add_action( 'wpforms_entries_init', array( $this, 'process_export' ), 8, 1 );
			add_action( 'wpforms_entries_init', array( $this, 'process_read' ), 8, 1 );
			add_action( 'wpforms_entries_init', array( $this, 'process_columns' ), 8, 1 );
			add_action( 'wpforms_entries_init', array( $this, 'process_delete' ), 8, 1 );
			add_action( 'wpforms_entries_init', array( $this, 'process_filter_dates' ), 8, 1 );
			add_action( 'wpforms_entries_init', array( $this, 'process_filter_search' ), 8, 1 );
			add_action( 'wpforms_entries_init', array( $this, 'setup' ), 10, 1 );

			do_action( 'wpforms_entries_init', 'list' );

			// Output.
			add_action( 'wpforms_admin_page', array( $this, 'list_all' ) );
			add_action( 'wpforms_admin_page', array( $this, 'field_column_setting' ) );
			add_action( 'wpforms_entry_list_title', array( $this, 'list_form_actions' ), 10, 1 );

			// Enqueues.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueues' ) );
		}
	}

	/**
	 * Get the current Entries view: 'list' or 'details'.
	 *
	 * @since 1.4.4
	 *
	 * @return string
	 */
	protected function get_current_screen_view() {

		$view = ! empty( $_GET['view'] ) ? $_GET['view'] : 'list';

		return apply_filters( 'wpforms_entries_list_get_current_screen_view', $view );
	}

	/**
	 * Add per-page screen option to the Entries table.
	 *
	 * @since 1.0.0
	 */
	public function screen_options() {

		$screen = get_current_screen();

		if ( 'wpforms_page_wpforms-entries' !== $screen->id ) {
			return;
		}

		add_screen_option(
			'per_page',
			array(
				'label'   => esc_html__( 'Number of entries per page:', 'wpforms' ),
				'option'  => 'wpforms_entries_per_page',
				'default' => apply_filters( 'wpforms_entries_per_page', 30 ),
			)
		);
	}

	/**
	 * Entries table per-page screen option value
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $status
	 * @param string $option
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function screen_options_set( $status, $option, $value ) {

		if ( 'wpforms_entries_per_page' === $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Enqueue assets for the entries pages.
	 *
	 * @since 1.0.0
	 */
	public function enqueues() {

		// JavaScript.
		wp_enqueue_script(
			'wpforms-flatpickr',
			WPFORMS_PLUGIN_URL . 'assets/js/flatpickr.min.js',
			array( 'jquery' ),
			'4.5.5'
		);

		// CSS.
		wp_enqueue_style(
			'wpforms-flatpickr',
			WPFORMS_PLUGIN_URL . 'assets/css/flatpickr.min.css',
			array(),
			'4.5.5'
		);

		// Hook for addons.
		do_action( 'wpforms_entries_enqueue', 'list' );
	}

	/**
	 * Watches for and runs complete form exports.
	 *
	 * @since 1.1.6
	 */
	public function process_export() {

		// Check for run switch.
		if ( empty( $_GET['export'] ) || empty( $_GET['form_id'] ) || 'all' !== $_GET['export'] ) {
			return;
		}

		// Security check.
		if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wpforms_entry_list_export' ) ) {
			return;
		}

		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/entries/class-entries-export.php';

		$export             = new WPForms_Entries_Export();
		$export->entry_type = 'all';
		$export->form_id    = absint( $_GET['form_id'] );
		$export->export();
	}

	/**
	 * Watches for and runs complete marking all entries as read.
	 *
	 * @since 1.1.6
	 */
	public function process_read() {

		// Check for run switch.
		if ( empty( $_GET['action'] ) || empty( $_GET['form_id'] ) || 'markread' !== $_GET['action'] ) {
			return;
		}

		// Security check.
		if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wpforms_entry_list_markread' ) ) {
			return;
		}

		wpforms()->entry->mark_all_read( $_GET['form_id'] );

		$this->alerts[] = array(
			'type'    => 'success',
			'message' => esc_html__( 'All entries marked as read.', 'wpforms' ),
			'dismiss' => true,
		);
	}

	/**
	 * Watches for and updates list column settings.
	 *
	 * @since 1.4.0
	 */
	public function process_columns() {

		// Check for run switch and data.
		if ( empty( $_POST['action'] ) || empty( $_POST['form_id'] ) || 'list-columns' !== $_POST['action'] ) {
			return;
		}

		// Security check.
		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'wpforms_entry_list_columns' ) ) {
			return;
		}

		// Update or delete.
		if ( empty( $_POST['fields'] ) ) {

			wpforms()->form->delete_meta( $_POST['form_id'], 'entry_columns' );

		} else {

			$fields = array_map( 'intval', $_POST['fields'] );

			wpforms()->form->update_meta( $_POST['form_id'], 'entry_columns', $fields );
		}
	}

	/**
	 * Watches for mass entry deletion and triggers if needed.
	 *
	 * @since 1.4.0
	 */
	public function process_delete() {

		// Check for run switch.
		if ( empty( $_GET['action'] ) || empty( $_GET['form_id'] ) || 'deleteall' !== $_GET['action'] ) {
			return;
		}

		// Security check.
		if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'wpforms_entry_list_deleteall' ) ) {
			return;
		}

		wpforms()->entry->delete_by( 'form_id', absint( $_GET['form_id'] ) );
		wpforms()->entry_meta->delete_by( 'form_id', absint( $_GET['form_id'] ) );

		$this->alerts[] = array(
			'type'    => 'success',
			'message' => esc_html__( 'All entries for the currently selected form were successfully deleted.', 'wpforms' ),
			'dismiss' => true,
		);
	}

	/**
	 * Watches for filtering requests from a dates range selection.
	 *
	 * @since 1.4.4
	 */
	public function process_filter_dates() {

		// Check for run switch. Security is handled on general form submission level (same as pagination).
		if (
			empty( $_GET['action'] ) ||
			empty( $_GET['form_id'] ) ||
			empty( $_GET['date'] )
		) {
			return;
		}

		$dates = explode( ' - ', $_GET['date'] );
		$args  = array();

		// Prepare the params for the entries retrieval.
		if ( is_array( $dates ) && count( $dates ) === 2 ) {
			$args = array(
				'select'  => 'entry_ids',
				'number'  => 0,
				'form_id' => (int) $_GET['form_id'],
				'date'    => $dates,
			);
		}

		if ( empty( $args ) ) {
			return;
		}

		$this->prepare_entry_ids_for_get_entries_args(
			wpforms()->entry->get_entries( $args )
		);
	}

	/**
	 * Watches for filtering requests from a search field.
	 *
	 * @since 1.4.4
	 */
	public function process_filter_search() {

		// Check for run switch and that all data is present.
		if (
			empty( $_GET['action'] ) ||
			empty( $_GET['form_id'] ) ||
			! isset( $_GET['search'] ) ||
			(
				! isset( $_GET['search']['term'] ) ||
				! isset( $_GET['search']['field'] ) ||
				empty( $_GET['search']['comparison'] )
			)
		) {
			return;
		}

		// Prepare the data.
		$term       = sanitize_text_field( $_GET['search']['term'] );
		$field      = is_numeric( $_GET['search']['field'] ) ? (int) $_GET['search']['field'] : sanitize_text_field( $_GET['search']['field'] );
		$comparison = in_array( $_GET['search']['comparison'], array( 'contains', 'contains_not', 'is', 'is_not' ), true ) ? sanitize_text_field( $_GET['search']['comparison'] ) : 'contains';
		$args       = array();

		/*
		 * Because empty fields were not migrated to a fields table in 1.4.3, we don't have that data
		 * and can't filter those with empty values.
		 * The current workaround - display all entries (instead of none at all).
		 *
		 * TODO: remove this "! empty( $term )" check when empty data will be saved to DB table.
		 */
		if ( ! empty( $term ) ) {

			$args['select']        = 'entry_ids';
			$args['number']        = -1;
			$args['form_id']       = (int) $_GET['form_id'];
			$args['value']         = $term;
			$args['value_compare'] = $comparison;

			if ( is_numeric( $field ) ) {
				$args['field_id'] = $field;
			}
		}

		if ( empty( $args ) ) {
			return;
		}

		$this->prepare_entry_ids_for_get_entries_args(
			wpforms()->entry_fields->get_fields( $args )
		);
	}

	/**
	 * Get the entry IDs based on the entries array and pass it further to the WPForms_Entry_Handler::get_entries() method via a filter.
	 *
	 * @since 1.4.4
	 *
	 * @param array $entries
	 */
	protected function prepare_entry_ids_for_get_entries_args( $entries ) {

		$entry_ids = array();

		if ( ! empty( $entries ) && is_array( $entries ) ) {

			foreach ( $entries as $entry ) {
				$entry_ids[] = $entry->entry_id;
			}

			if ( ! empty( $this->filter['entry_id'] ) ) {
				$this->filter['entry_id'] = array_intersect( $this->filter['entry_id'], array_unique( $entry_ids ) );
			} else {
				$this->filter = array(
					'entry_id' => array_unique( $entry_ids ),
				);
			}
		}

		// TODO: when we will drop PHP 5.2 support, this can be changed to a closure.
		add_filter( 'wpforms_entry_handler_get_entries_args', array( $this, 'get_filtered_entry_table_args' ) );
	}

	/**
	 * Merge default arguments to entries retrieval with the one we send to filter.
	 *
	 * @since 1.4.4
	 *
	 * @param array $args
	 *
	 * @return array Filtered arguments.
	 */
	public function get_filtered_entry_table_args( $args ) {
		$this->filter['is_filtered'] = true;
		return array_merge( $args, $this->filter );
	}

	/**
	 * Setup entry list page data.
	 *
	 * This function does the error checking and variable setup.
	 *
	 * @since 1.1.6
	 */
	public function setup() {

		// Fetch all forms.
		$this->forms = wpforms()->form->get(
			'',
			array(
				'orderby' => 'ID',
				'order'   => 'ASC',
			)
		);

		// Check that the user has created at least one form.
		if ( empty( $this->forms ) ) {

			$this->alerts[] = array(
				'type'    => 'info',
				'message' =>
					sprintf(
						wp_kses(
							/* translators: %s - WPForms Builder page. */
							__( 'Whoops, you haven\'t created a form yet. Want to <a href="%s">give it a go</a>?', 'wpforms' ),
							array(
								'a' => array(
									'href' => array(),
								),
							)
						),
						admin_url( 'admin.php?page=wpforms-builder' )
					),
				'abort'   => true,
			);

		} else {
			$this->form_id = ! empty( $_GET['form_id'] ) ? absint( $_GET['form_id'] ) : apply_filters( 'wpforms_entry_list_default_form_id', absint( $this->forms[0]->ID ) );
			$this->form    = wpforms()->form->get( $this->form_id );
		}
	}

	/**
	 * Whether the current list of entries is filtered somehow or not.
	 *
	 * @since 1.4.4
	 *
	 * @return bool
	 */
	protected function is_list_filtered() {

		$is_filtered = false;

		if (
			isset( $_GET['search'] ) ||
			isset( $_GET['date'] )
		) {
			$is_filtered = true;
		}

		return apply_filters( 'wpforms_entries_list_is_list_filtered', $is_filtered );
	}

	/**
	 * List all entries in a specific form.
	 *
	 * @since 1.0.0
	 */
	public function list_all() {

		$form_data = ! empty( $this->form->post_content ) ? wpforms_decode( $this->form->post_content ) : '';
		?>

		<div id="wpforms-entries-list" class="wrap wpforms-admin-wrap">

			<h1 class="page-title"><?php esc_html_e( 'Entries', 'wpforms' ); ?></h1>

			<?php
			// Admin notices.
			$this->display_alerts();
			if ( $this->abort ) {
				echo '</div>'; // close wrap.

				return;
			}

			$this->entries            = new WPForms_Entries_Table();
			$this->entries->form_id   = $this->form_id;
			$this->entries->form_data = $form_data;
			$this->entries->prepare_items();

			$last_entry = wpforms()->entry->get_last( $this->form_id );
			?>

			<div class="wpforms-admin-content">

				<?php do_action( 'wpforms_entry_list_title', $form_data, $this ); ?>

				<form id="wpforms-entries-table" method="get"
				      action="<?php echo esc_url( admin_url( 'admin.php?page=wpforms-entries' ) ); ?>"
				      <?php echo ( ! $this->is_list_filtered() && isset( $last_entry->entry_id ) ) ? 'data-last-entry-id="' . absint( $last_entry->entry_id ) . '"' : ''; ?>>

					<input type="hidden" name="page" value="wpforms-entries"/>
					<input type="hidden" name="view" value="list"/>
					<input type="hidden" name="form_id" value="<?php echo esc_attr( $this->form_id ); ?>"/>

					<?php $this->entries->views(); ?>

					<?php $this->entries->search_box( esc_html__( 'Search', 'wpforms' ), 'wpforms-entries' ); ?>

					<?php $this->entries->display(); ?>

				</form>

			</div>

		</div>

		<?php
	}

	/**
	 * Settings for field column personalization!
	 *
	 * @since 1.4.0
	 */
	public function field_column_setting() {

		$form_data = ! empty( $this->form->post_content ) ? wpforms_decode( $this->form->post_content ) : array();
		?>
		<div id="wpforms-field-column-select" style="display:none;">

			<form method="post" action="<?php echo admin_url( 'admin.php?page=wpforms-entries&view=list&form_id=' . $this->form_id ); ?>" style="display:none;">
				<input type="hidden" name="action" value="list-columns"/>
				<input type="hidden" name="form_id" value="<?php echo $this->form_id; ?>"/>
				<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'wpforms_entry_list_columns' ); ?>">
				<p>
					<?php
					esc_html_e( 'Select the fields to show when viewing the entries list for this form.', 'wpforms' );
					if ( empty( $form_data['meta']['entry_columns'] ) ) {
						echo ' ' . esc_html__( 'Currently columns have not been configured, so we\'re showing the first 3 fields.', 'wpforms' );
					}
					?>
				</p>
				<select name="fields[]" multiple>
					<?php
					if ( ! empty( $form_data['meta']['entry_columns'] ) ) {
						foreach ( $form_data['meta']['entry_columns'] as $id ) {
							if ( empty( $form_data['fields'][ $id ] ) ) {
								continue;
							} else {
								$name = ! empty( $form_data['fields'][ $id ]['label'] ) ? wp_strip_all_tags( $form_data['fields'][ $id ]['label'] ) : esc_html__( 'Field', 'wpforms' );
								printf( '<option value="%d" selected>%s</option>', $id, $name );
							}
						}
					}
					if ( ! empty( $form_data['fields'] ) && is_array( $form_data['fields'] ) ) {
						foreach ( $form_data['fields'] as $id => $field ) {
							if (
								! empty( $form_data['meta']['entry_columns'] ) &&
								in_array( $id, $form_data['meta']['entry_columns'], true )
							) {
								continue;
							}
							if ( ! in_array( $field['type'], WPForms_Entries_Table::get_columns_form_disallowed_fields(), true ) ) {
								$name = ! empty( $field['label'] ) ? wp_strip_all_tags( $field['label'] ) : esc_html__( 'Field', 'wpforms' );
								printf( '<option value="%d">%s</option>', $id, $name );
							}
						}
					}
					?>
				</select>
			</form>

		</div>
		<?php
	}

	/**
	 * Entries list form actions.
	 *
	 * @since 1.1.6
	 *
	 * @param array $form_data Form data and settings.
	 */
	public function list_form_actions( $form_data ) {

		$base = add_query_arg(
			array(
				'page'    => 'wpforms-entries',
				'view'    => 'list',
				'form_id' => absint( $this->form_id ),
			),
			admin_url( 'admin.php' )
		);

		// Edit Form URL.
		$edit_url = add_query_arg(
			array(
				'page'    => 'wpforms-builder',
				'view'    => 'fields',
				'form_id' => absint( $this->form_id ),
			),
			admin_url( 'admin.php' )
		);

		// Preview Entry URL.
		$preview_url = esc_url( wpforms_get_form_preview_url( $this->form_id ) );

		// Export Entry URL.
		$export_url = wp_nonce_url(
			add_query_arg(
				array(
					'export' => 'all',
				),
				$base
			),
			'wpforms_entry_list_export'
		);

		// Mark Read URL.
		$read_url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'markread',
				),
				$base
			),
			'wpforms_entry_list_markread'
		);

		// Delete all entries.
		$delete_url = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'deleteall',
				),
				$base
			),
			'wpforms_entry_list_deleteall'
		);
		?>

		<div class="form-details wpforms-clear">

			<span class="form-details-sub"><?php esc_html_e( 'Select Form', 'wpforms' ); ?></span>

			<h3 class="form-details-title">
				<?php
				if ( ! empty( $form_data['settings']['form_title'] ) ) {
					echo wp_strip_all_tags( $form_data['settings']['form_title'] );
				}
				?>

				<div class="form-selector">
					<a href="#" title="<?php esc_attr_e( 'Open form selector', 'wpforms' ); ?>" class="toggle dashicons dashicons-arrow-down-alt2"></a>
					<div class="form-list">
						<ul>
							<?php
							foreach ( $this->forms as $key => $form ) {
								$form_url = add_query_arg(
									array(
										'page'    => 'wpforms-entries',
										'view'    => 'list',
										'form_id' => absint( $form->ID ),
									),
									admin_url( 'admin.php' )
								);
								echo '<li><a href="' . esc_url( $form_url ) . '">' . esc_html( $form->post_title ) . '</a></li>';
							}
							?>
						</ul>
					</div>
				</div>
			</h3>

			<div class="form-details-actions">

				<?php if ( $this->is_list_filtered() ) : ?>
					<a href="<?php echo $base; ?>" class="form-details-actions-entries">
						<span class="dashicons dashicons-list-view"></span>
						<?php esc_html_e( 'All Entries', 'wpforms' ); ?>
					</a>
				<?php endif; ?>

				<a href="<?php echo $edit_url; ?>" class="form-details-actions-edit">
					<span class="dashicons dashicons-edit"></span>
					<?php esc_html_e( 'Edit This Form', 'wpforms' ); ?>
				</a>

				<a href="<?php echo $preview_url; ?>" class="form-details-actions-preview" target="_blank" rel="noopener noreferrer">
					<span class="dashicons dashicons-visibility"></span>
					<?php esc_html_e( 'Preview Form', 'wpforms' ); ?>
				</a>

				<a href="<?php echo $export_url; ?>" class="form-details-actions-export">
					<span class="dashicons dashicons-migrate"></span>
					<?php esc_html_e( 'Download Export (CSV)', 'wpforms' ); ?>
				</a>

				<a href="<?php echo $read_url; ?>" class="form-details-actions-read">
					<span class="dashicons dashicons-marker"></span>
					<?php esc_html_e( 'Mark All Read', 'wpforms' ); ?>
				</a>

				<a href="<?php echo $delete_url; ?>" class="form-details-actions-deleteall">
					<span class="dashicons dashicons-trash"></span>
					<?php esc_html_e( 'Delete All', 'wpforms' ); ?>
				</a>

			</div>

		</div>
		<?php
	}

	/**
	 * Display admin notices and errors.
	 *
	 * @since 1.1.6
	 * @todo Refactor or eliminate this
	 *
	 * @param string $display
	 * @param bool $wrap
	 */
	public function display_alerts( $display = '', $wrap = false ) {

		if ( empty( $this->alerts ) ) {
			return;

		} else {

			if ( empty( $display ) ) {
				$display = array( 'error', 'info', 'warning', 'success' );
			} else {
				$display = (array) $display;
			}

			foreach ( $this->alerts as $alert ) {

				$type = ! empty( $alert['type'] ) ? $alert['type'] : 'info';

				if ( in_array( $type, $display, true ) ) {
					$class  = 'notice-' . $type;
					$class .= ! empty( $alert['dismiss'] ) ? ' is-dismissible' : '';
					$output = '<div class="notice ' . $class . '"><p>' . $alert['message'] . '</p></div>';
					if ( $wrap ) {
						echo '<div class="wrap">' . $output . '</div>';
					} else {
						echo $output;
					}
					if ( ! empty( $alert['abort'] ) ) {
						$this->abort = true;
						break;
					}
				}
			}
		}
	}

	/**
	 * Check for new entries using Heartbeat API.
	 *
	 * @since 1.5.0
	 *
	 * @param array  $response  The Heartbeat response.
	 * @param array  $data      The $_POST data sent.
	 * @param string $screen_id The screen id.
	 *
	 * @return array
	 */
	public function heartbeat_new_entries_check( $response, $data, $screen_id ) {

		if ( 'wpforms_page_wpforms-entries' !== $screen_id ) {
			return $response;
		}

		$entry_id = ! empty( $data['wpforms_new_entries_entry_id'] ) ? absint( $data['wpforms_new_entries_entry_id'] ) : 0;
		$form_id  = ! empty( $data['wpforms_new_entries_form_id'] ) ? absint( $data['wpforms_new_entries_form_id'] ) : 0;

		if ( empty( $form_id ) ) {
			return $response;
		}

		$entries_count = wpforms()->entry->get_next_count( $entry_id, $form_id );

		if ( empty( $entries_count ) ) {
			return $response;
		}

		/* translators: %d - Number of form entries. */
		$response['wpforms_new_entries_notification'] = esc_html( sprintf( _n( 'See %d new entry', 'See %d new entries', $entries_count, 'wpforms' ), $entries_count ) );

		return $response;
	}
}

new WPForms_Entries_List();
