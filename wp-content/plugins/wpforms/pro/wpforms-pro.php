<?php
/**
 * WPForms Pro. Load Pro specific features/functionality.
 *
 * @since 1.2.1
 * @package WPForms
 */
class WPForms_Pro {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.2.1
	 */
	public function __construct() {

		$this->constants();
		$this->includes();

		add_action( 'wpforms_updater', array( $this, 'updater' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 10 );
		add_action( 'wpforms_loaded', array( $this, 'objects' ), 1 );
		add_action( 'wpforms_install', array( $this, 'install' ), 10 );
		add_filter( 'wpforms_settings_tabs', array( $this, 'register_settings_tabs' ), 5, 1 );
		add_filter( 'wpforms_settings_defaults', array( $this, 'register_settings_fields' ), 5, 1 );
		add_action( 'wpforms_process_entry_save', array( $this, 'entry_save' ), 10, 4 );
		add_action( 'wpforms_form_settings_general', array( $this, 'form_settings_general' ), 10 );
		add_filter( 'wpforms_overview_table_columns', array( $this, 'form_table_columns' ), 10, 1 );
		add_filter( 'wpforms_overview_table_column_value', array( $this, 'form_table_columns_value' ), 10, 3 );
		add_action( 'wpforms_form_settings_notifications', array( $this, 'form_settings_notifications' ), 8, 1 );
		add_action( 'wpforms_form_settings_confirmations', array( $this, 'form_settings_confirmations' ) );
		add_filter( 'wpforms_builder_strings', array( $this, 'form_builder_strings' ), 10, 2 );
		add_filter( 'wpforms_frontend_strings', array( $this, 'frontend_strings' ) );
		add_action( 'admin_notices', array( $this, 'conditional_logic_addon_notice' ) );
		add_action( 'wpforms_builder_print_footer_scripts', array( $this, 'builder_templates' ) );
		add_filter( 'wpforms_email_footer_text', array( $this, 'form_notification_footer' ) );
	}

	/**
	 * Load Pro plugin updater.
	 *
	 * @since 1.5.4
	 *
	 * @param string $key License key.
	 */
	public function updater( $key ) {

		// Go ahead and initialize the updater.
		new \WPForms_Updater(
			array(
				'plugin_name' => 'WPForms',
				'plugin_slug' => 'wpforms',
				'plugin_path' => plugin_basename( WPFORMS_PLUGIN_FILE ),
				'plugin_url'  => trailingslashit( WPFORMS_PLUGIN_URL ),
				'remote_url'  => WPFORMS_UPDATER_API,
				'version'     => WPFORMS_VERSION,
				'key'         => $key,
			)
		);
	}

	/**
	 * Loads the separate PRO plugin translation file.
	 *
	 * @since 1.5.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wpforms', false, dirname( plugin_basename( WPFORMS_PLUGIN_FILE ) ) . '/pro/languages/' );
	}

	/**
	 * Include files.
	 *
	 * @since 1.0.0
	 */
	private function includes() {

		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/class-db.php';
		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/class-entry.php';
		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/class-entry-fields.php';
		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/class-entry-meta.php';
		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/class-conditional-logic-fields.php';
		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/payments/class-payment.php';
		require_once WPFORMS_PLUGIN_DIR . 'pro/includes/payments/functions.php';

		if ( is_admin() ) {
			require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/class-upgrades.php';
			require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/ajax-actions.php';
			require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/entries/class-entries-single.php';
			require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/entries/class-entries-list.php';
			require_once WPFORMS_PLUGIN_DIR . 'pro/includes/admin/class-addons.php';
		}
	}

	/**
	 * Setup objects.
	 *
	 * @since 1.2.1
	 */
	public function objects() {

		// Global objects.
		wpforms()->entry        = new WPForms_Entry_Handler();
		wpforms()->entry_fields = new WPForms_Entry_Fields_Handler();
		wpforms()->entry_meta   = new WPForms_Entry_Meta_Handler();
	}

	/**
	 * Setup plugin constants.
	 *
	 * @since 1.2.1
	 */
	public function constants() {

	}

	/**
	 * Handles plugin installation upon activation.
	 *
	 * @since 1.2.1
	 */
	public function install() {

		$wpforms_install               = new stdClass();
		$wpforms_install->entry        = new WPForms_Entry_Handler();
		$wpforms_install->entry_fields = new WPForms_Entry_Fields_Handler();
		$wpforms_install->entry_meta   = new WPForms_Entry_Meta_Handler();

		// Entry tables.
		$wpforms_install->entry->create_table();
		$wpforms_install->entry_fields->create_table();
		$wpforms_install->entry_meta->create_table();
	}

	/**
	 * Register Pro settings tabs.
	 *
	 * @since 1.3.9
	 *
	 * @param array $tabs Admin area tabs list.
	 *
	 * @return array
	 */
	public function register_settings_tabs( $tabs ) {

		// Add Payments tab.
		$payments = array(
			'payments' => array(
				'name'   => esc_html__( 'Payments', 'wpforms' ),
				'form'   => true,
				'submit' => esc_html__( 'Save Settings', 'wpforms' ),
			),
		);

		$tabs = wpforms_array_insert( $tabs, $payments, 'validation' );

		return $tabs;
	}

	/**
	 * Register Pro settings fields.
	 *
	 * @since 1.3.9
	 *
	 * @param array $settings Admin area settings list.
	 *
	 * @return array
	 */
	public function register_settings_fields( $settings ) {

		$currencies      = wpforms_get_currencies();
		$currency_option = array();

		// Format currencies for select element.
		foreach ( $currencies as $code => $currency ) {
			$currency_option[ $code ] = sprintf( '%s (%s %s)', $currency['name'], $code, $currency['symbol'] );
		}

		// Validation settings for fields only available in Pro.
		$settings['validation']['validation-fileextension']   = array(
			'id'      => 'validation-fileextension',
			'name'    => esc_html__( 'File Extension', 'wpforms' ),
			'type'    => 'text',
			'default' => esc_html__( 'File type is not allowed.', 'wpforms' ),
		);
		$settings['validation']['validation-filesize']        = array(
			'id'      => 'validation-filesize',
			'name'    => esc_html__( 'File Size', 'wpforms' ),
			'type'    => 'text',
			'default' => esc_html__( 'File exceeds max size allowed.', 'wpforms' ),
		);
		$settings['validation']['validation-time12h']         = array(
			'id'      => 'validation-time12h',
			'name'    => esc_html__( 'Time (12 hour)', 'wpforms' ),
			'type'    => 'text',
			'default' => esc_html__( 'Please enter time in 12-hour AM/PM format (eg 8:45 AM).', 'wpforms' ),
		);
		$settings['validation']['validation-time24h']         = array(
			'id'      => 'validation-time24h',
			'name'    => esc_html__( 'Time (24 hour)', 'wpforms' ),
			'type'    => 'text',
			'default' => esc_html__( 'Please enter time in 24-hour format (eg 22:45).', 'wpforms' ),
		);
		$settings['validation']['validation-requiredpayment'] = array(
			'id'      => 'validation-requiredpayment',
			'name'    => esc_html__( 'Payment Required', 'wpforms' ),
			'type'    => 'text',
			'default' => esc_html__( 'Payment is required.', 'wpforms' ),
		);
		$settings['validation']['validation-creditcard']      = array(
			'id'      => 'validation-creditcard',
			'name'    => esc_html__( 'Credit Card', 'wpforms' ),
			'type'    => 'text',
			'default' => esc_html__( 'Please enter a valid credit card number.', 'wpforms' ),
		);
		$settings['validation']['validation-post_max_size']   = array(
			'id'      => 'validation-post_max_size',
			'name'    => esc_html__( 'File Upload Total Size', 'wpforms' ),
			'type'    => 'text',
			'default' => esc_html__( 'The total size of the selected files {totalSize} Mb exceeds the allowed limit {maxSize} Mb.', 'wpforms' ),
		);

		// Payment settings.
		$settings['payments']['payments-heading'] = array(
			'id'       => 'payments-heading',
			'content'  => '<h4>' . esc_html__( 'Payments', 'wpforms' ) . '</h4>',
			'type'     => 'content',
			'no_label' => true,
			'class'    => array( 'section-heading', 'no-desc' ),
		);
		$settings['payments']['currency']         = array(
			'id'        => 'currency',
			'name'      => esc_html__( 'Currency', 'wpforms' ),
			'type'      => 'select',
			'choicesjs' => true,
			'search'    => true,
			'default'   => 'USD',
			'options'   => $currency_option,
		);

		// Additional GDPR related options.
		$settings['general'] = wpforms_array_insert(
			$settings['general'],
			array(
				'gdpr-disable-uuid'    => array(
					'id'   => 'gdpr-disable-uuid',
					'name' => esc_html__( 'Disable User Cookies', 'wpforms' ),
					'desc' => esc_html__( 'Check this to disable user tracking cookies (UUIDs). This will disable the Related Entries feature and the Form Abandonment/Geolocation addons.', 'wpforms' ),
					'type' => 'checkbox',
				),
				'gdpr-disable-details' => array(
					'id'   => 'gdpr-disable-details',
					'name' => esc_html__( 'Disable User Details', 'wpforms' ),
					'desc' => esc_html__( 'Check this to disable storing the IP address and User Agent on all forms. If unchecked this can be managed on a form by form basis inside the form settings.', 'wpforms' ),
					'type' => 'checkbox',
				),
			),
			'gdpr'
		);

		return $settings;
	}

	/**
	 * Saves entry to database.
	 *
	 * @since 1.2.1
	 *
	 * @param array      $fields    List of form fields.
	 * @param array      $entry     User submitted data.
	 * @param int|string $form_id   Form ID.
	 * @param array      $form_data Prepared form settings.
	 */
	public function entry_save( $fields, $entry, $form_id, $form_data = array() ) {

		// Check if form has entries disabled.
		if ( isset( $form_data['settings']['disable_entries'] ) ) {
			return;
		}

		// Provide the opportunity to override via a filter.
		if ( ! apply_filters( 'wpforms_entry_save', true, $fields, $entry, $form_data ) ) {
			return;
		}

		$fields     = apply_filters( 'wpforms_entry_save_data', $fields, $entry, $form_data );
		$user_id    = is_user_logged_in() ? get_current_user_id() : 0;
		$user_ip    = wpforms_get_ip();
		$user_agent = ! empty( $_SERVER['HTTP_USER_AGENT'] ) ? substr( $_SERVER['HTTP_USER_AGENT'], 0, 256 ) : '';
		$user_uuid  = ! empty( $_COOKIE['_wpfuuid'] ) ? $_COOKIE['_wpfuuid'] : '';
		$date       = date( 'Y-m-d H:i:s' );

		// If GDPR enhancements are enabled and user details are disabled
		// globally or in the form settings, discard the IP and UA.
		if (
			(
				 wpforms_setting( 'gdpr', false ) &&
				(
				wpforms_setting( 'gdpr-disable-details', false ) ||
				! empty( $form_data['settings']['disable_ip'] )
				)
			) ||
			! apply_filters( 'wpforms_disable_entry_user_ip', '__return_false', $fields, $form_data )
		) {
			$user_agent = '';
			$user_ip    = '';
		}

		// Create entry.
		$entry_id = wpforms()->entry->add( array(
			'form_id'    => absint( $form_id ),
			'user_id'    => absint( $user_id ),
			'fields'     => wp_json_encode( $fields ),
			'ip_address' => sanitize_text_field( $user_ip ),
			'user_agent' => sanitize_text_field( $user_agent ),
			'date'       => $date,
			'user_uuid'  => sanitize_text_field( $user_uuid ),
		) );

		// Create fields.
		if ( $entry_id ) {
			foreach ( $fields as $field ) {

				$field = apply_filters( 'wpforms_entry_save_fields', $field, $form_data, $entry_id );

				if ( isset( $field['value'] ) && '' !== $field['value'] ) {
					wpforms()->entry_fields->add( array(
						'entry_id' => $entry_id,
						'form_id'  => absint( $form_id ),
						'field_id' => absint( $field['id'] ),
						'value'    => $field['value'],
						'date'     => $date,
					) );


				}
			}
		}

		wpforms()->process->entry_id = $entry_id;

	}

	/**
	 * Add additional form settings to the General section.
	 *
	 * @since 1.2.1
	 *
	 * @param \WPForms_Builder_Panel_Settings $instance Settings management panel instance.
	 */
	public function form_settings_general( $instance ) {

		// Only provide this option if the user has configured payments.
		if (
			isset( $instance->form_data['settings']['disable_entries'] ) ||
			(
				empty( $instance->form_data['payments']['paypal_standard']['enable'] ) ||
				empty( $instance->form_data['payments']['stripe']['enable'] )
			)
		) {
			wpforms_panel_field(
				'checkbox',
				'settings',
				'disable_entries',
				$instance->form_data,
				esc_html__( 'Disable storing entry information in WordPress', 'wpforms' )
			);
		}

		// Only provide this option if GDPR enhancements are enabled and user
		// details are not disabled globally.
		if ( wpforms_setting( 'gdpr', false ) && ! wpforms_setting( 'gdpr-disable-details', false ) ) {
			wpforms_panel_field(
				'checkbox',
				'settings',
				'disable_ip',
				$instance->form_data,
				esc_html__( 'Disable storing user details (IP address and user agent)', 'wpforms' )
			);
		}
	}

	/**
	 * Add entry counts column to form table.
	 *
	 * @since 1.2.1
	 *
	 * @param array $columns List of table columns.
	 *
	 * @return array
	 */
	public function form_table_columns( $columns ) {

		$columns['entries'] = esc_html__( 'Entries', 'wpforms' );

		return $columns;
	}

	/**
	 * Add entry counts value to entry count column.
	 *
	 * @since 1.2.1
	 *
	 * @param string $value
	 * @param object $form
	 * @param string $column_name
	 *
	 * @return string
	 */
	public function form_table_columns_value( $value, $form, $column_name ) {

		if ( 'entries' === $column_name ) {
			$count = wpforms()->entry->get_entries(
				array(
					'form_id' => $form->ID,
				),
				true
			);

			$value = sprintf(
				'<a href="%s">%d</a>',
				add_query_arg(
					array(
						'view'    => 'list',
						'form_id' => $form->ID,
					),
					admin_url( 'admin.php?page=wpforms-entries' )
				),
				$count
			);
		}

		return $value;
	}

	/**
	 * Form notification settings, supports multiple notifications.
	 *
	 * @since 1.2.3
	 *
	 * @param object $settings
	 */
	public function form_settings_notifications( $settings ) {

		$cc               = wpforms_setting( 'email-carbon-copy', false );
		$form_settings    = ! empty( $settings->form_data['settings'] ) ? $settings->form_data['settings'] : array();
		$notifications    = is_array( $form_settings ) && isset( $form_settings['notifications'] ) ? $form_settings['notifications'] : array();
		$from_name_after  = apply_filters( 'wpforms_builder_notifications_from_name_after', '' );
		$from_email_after = apply_filters( 'wpforms_builder_notifications_from_email_after', '' );

		// Fetch next ID and handle backwards compatibility.
		if ( empty( $notifications ) ) {
			$next_id = 2;

			/* translators: %s - form name. */
			$notifications[1]['subject']        = ! empty( $form_settings['notification_subject'] ) ? $form_settings['notification_subject'] : sprintf( esc_html__( 'New %s Entry', 'wpforms' ), $settings->form->post_title );
			$notifications[1]['email']          = ! empty( $form_settings['notification_email'] ) ? $form_settings['notification_email'] : '{admin_email}';
			$notifications[1]['sender_name']    = ! empty( $form_settings['notification_fromname'] ) ? $form_settings['notification_fromname'] : get_bloginfo( 'name' );
			$notifications[1]['sender_address'] = ! empty( $form_settings['notification_fromaddress'] ) ? $form_settings['notification_fromaddress'] : '{admin_email}';
			$notifications[1]['replyto']        = ! empty( $form_settings['notification_replyto'] ) ? $form_settings['notification_replyto'] : '';
		} else {
			$next_id = max( array_keys( $notifications ) ) + 1;
		}

		echo '<div class="wpforms-panel-content-section-title">';
			esc_html_e( 'Notifications', 'wpforms' );
			echo '<button class="wpforms-notifications-add wpforms-builder-settings-block-add" data-block-type="notification" data-next-id="' . absint( $next_id ) . '">' . esc_html__( 'Add New Notification', 'wpforms' ) . '</button>';
		echo '</div>';

		wpforms_panel_field(
			'select',
			'settings',
			'notification_enable',
			$settings->form_data,
			esc_html__( 'Notifications', 'wpforms' ),
			array(
				'default' => '1',
				'options' => array(
					'1' => esc_html__( 'On', 'wpforms' ),
					'0' => esc_html__( 'Off', 'wpforms' ),
				),
			)
		);

		foreach ( $notifications as $id => $notification ) {

			$name         = ! empty( $notification['notification_name'] ) ? $notification['notification_name'] : esc_html__( 'Default Notification', 'wpforms' );
			$closed_state = '';
			$toggle_state = '<i class="fa fa-chevron-up"></i>';

			if ( ! empty( $settings->form_data['id'] ) && 'closed' === wpforms_builder_settings_block_get_state( $settings->form_data['id'], $id, 'notification' ) ) {
				$closed_state = 'style="display:none"';
				$toggle_state = '<i class="fa fa-chevron-down"></i>';
			}

			do_action( 'wpforms_form_settings_notifications_single_before', $settings, $id );
			?>

			<div class="wpforms-notification wpforms-builder-settings-block" data-block-type="notification" data-block-id="<?php echo absint( $id ); ?>">

				<div class="wpforms-builder-settings-block-header">
					<div class="wpforms-builder-settings-block-actions">
						<?php do_action( 'wpforms_form_settings_notifications_single_action', $id, $notification, $settings ); ?>

						<button class="wpforms-builder-settings-block-edit"><i class="fa fa-pencil"></i></button>
						<button class="wpforms-builder-settings-block-toggle"><?php echo $toggle_state; ?></button>
						<button class="wpforms-builder-settings-block-delete"><i class="fa fa-times-circle"></i></button>
					</div>

					<div class="wpforms-builder-settings-block-name-holder">
						<span class="wpforms-builder-settings-block-name"><?php echo esc_html( $name ); ?></span>

						<div class="wpforms-builder-settings-block-name-edit">
							<input type="text" name="settings[notifications][<?php echo absint( $id ); ?>][notification_name]" value="<?php echo esc_attr( $name ); ?>">
						</div>
					</div>

				</div>

				<div class="wpforms-builder-settings-block-content" <?php echo $closed_state; ?>>

					<?php
					wpforms_panel_field(
						'text',
						'notifications',
						'email',
						$settings->form_data,
						esc_html__( 'Send To Email Address', 'wpforms' ),
						array(
							'default'    => '{admin_email}',
							'tooltip'    => esc_html__( 'Enter the email address to receive form entry notifications. For multiple notifications, separate email addresses with a comma.', 'wpforms' ),
							'smarttags'  => array(
								'type'   => 'fields',
								'fields' => 'email',
							),
							'parent'     => 'settings',
							'subsection' => $id,
							'input_id'   => 'wpforms-panel-field-notifications-email-' . $id,
							'class'      => 'email-recipient',
						)
					);
					if ( $cc ) :
						wpforms_panel_field(
							'text',
							'notifications',
							'carboncopy',
							$settings->form_data,
							esc_html__( 'CC', 'wpforms' ),
							array(
								'smarttags'  => array(
									'type'   => 'fields',
									'fields' => 'email',
								),
								'parent'     => 'settings',
								'subsection' => $id,
								'input_id'   => 'wpforms-panel-field-notifications-carboncopy-' . $id,
							)
						);
					endif;
					wpforms_panel_field(
						'text',
						'notifications',
						'subject',
						$settings->form_data,
						esc_html__( 'Email Subject', 'wpforms' ),
						array(
							/* translators: %s - form name. */
							'default'    => sprintf( esc_html__( 'New Entry: %s', 'wpforms' ), $settings->form->post_title ),
							'smarttags'  => array(
								'type' => 'all',
							),
							'parent'     => 'settings',
							'subsection' => $id,
							'input_id'   => 'wpforms-panel-field-notifications-subject-' . $id,
						)
					);
					wpforms_panel_field(
						'text',
						'notifications',
						'sender_name',
						$settings->form_data,
						esc_html__( 'From Name', 'wpforms' ),
						array(
							'default'    => sanitize_text_field( get_option( 'blogname' ) ),
							'smarttags'  => array(
								'type'   => 'fields',
								'fields' => 'name,text',
							),
							'parent'     => 'settings',
							'subsection' => $id,
							'input_id'   => 'wpforms-panel-field-notifications-sender_name-' . $id,
							'readonly'   => ! empty( $from_name_after ),
							'after'      => ! empty( $from_name_after ) ? '<p class="note">' . $from_name_after . '</p>' : '',
						)
					);
					wpforms_panel_field(
						'text',
						'notifications',
						'sender_address',
						$settings->form_data,
						esc_html__( 'From Email', 'wpforms' ),
						array(
							'default'    => '{admin_email}',
							'smarttags'  => array(
								'type'   => 'fields',
								'fields' => 'email',
							),
							'parent'     => 'settings',
							'subsection' => $id,
							'input_id'   => 'wpforms-panel-field-notifications-sender_address-' . $id,
							'readonly'   => ! empty( $from_email_after ),
							'after'      => ! empty( $from_email_after ) ? '<p class="note">' . $from_email_after . '</p>' : '',
						)
					);
					wpforms_panel_field(
						'text',
						'notifications',
						'replyto',
						$settings->form_data,
						esc_html__( 'Reply-To', 'wpforms' ),
						array(
							'smarttags'  => array(
								'type'   => 'fields',
								'fields' => 'email',
							),
							'parent'     => 'settings',
							'subsection' => $id,
							'input_id'   => 'wpforms-panel-field-notifications-replyto-' . $id,
						)
					);
					wpforms_panel_field(
						'textarea',
						'notifications',
						'message',
						$settings->form_data,
						esc_html__( 'Message', 'wpforms' ),
						array(
							'rows'       => 6,
							'default'    => '{all_fields}',
							'smarttags'  => array(
								'type' => 'all',
							),
							'parent'     => 'settings',
							'subsection' => $id,
							'input_id'   => 'wpforms-panel-field-notifications-message-' . $id,
							'class'      => 'email-msg',
							/* translators: %s - all fields smart tag. */
							'after'      => '<p class="note">' . sprintf( esc_html__( 'To display all form fields, use the %s Smart Tag.', 'wpforms' ), '<code>{all_fields}</code>' ) . '</p>',
						)
					);

					wpforms_conditional_logic()->builder_block( array(
						'form'        => $settings->form_data,
						'type'        => 'panel',
						'panel'       => 'notifications',
						'parent'      => 'settings',
						'subsection'  => $id,
						'actions'     => array(
							'go'   => esc_html__( 'Send', 'wpforms' ),
							'stop' => esc_html__( 'Don\'t send', 'wpforms' ),
						),
						'action_desc' => esc_html__( 'this notification if', 'wpforms' ),
						'reference'   => esc_html__( 'Email notifications', 'wpforms' ),
					) );

					// Hook for addons.
					do_action( 'wpforms_form_settings_notifications_single_after', $settings, $id );
					?>

				</div><!-- /.wpforms-builder-settings-block-content -->

			</div><!-- /.wpforms-builder-settings-block -->

			<?php
		}
	}

	/**
	 * Form confirmation settings, supports multiple confirmations.
	 *
	 * @since 1.4.8
	 * @param object $settings
	 */
	public function form_settings_confirmations( $settings ) {

		wp_enqueue_editor();

		$form_settings = ! empty( $settings->form_data['settings'] ) ? $settings->form_data['settings'] : array();
		$confirmations = is_array( $form_settings ) && isset( $form_settings['confirmations'] ) ? $form_settings['confirmations'] : array();

		// Fetch next ID and handle backwards compatibility.
		if ( empty( $confirmations ) ) {
			$next_id = 2;

			$confirmations[1]['type']           = ! empty( $form_settings['confirmation_type'] ) ? $form_settings['confirmation_type'] : 'message';
			$confirmations[1]['message']        = ! empty( $form_settings['confirmation_message'] ) ? $form_settings['confirmation_message'] : esc_html__( 'Thanks for contacting us! We will be in touch with you shortly.', 'wpforms' );
			$confirmations[1]['message_scroll'] = ! empty( $form_settings['confirmation_message_scroll'] ) ? $form_settings['confirmation_message_scroll'] : 1;
			$confirmations[1]['page']           = ! empty( $form_settings['confirmation_page'] ) ? $form_settings['confirmation_page'] : '';
			$confirmations[1]['redirect']       = ! empty( $form_settings['confirmation_redirect'] ) ? $form_settings['confirmation_redirect'] : '';

			$settings->form_data['settings']['confirmations'] = $confirmations;
		} else {
			$next_id = max( array_keys( $confirmations ) ) + 1;
		}

		$default_confirmation_key = min( array_keys( $confirmations ) );

		echo '<div class="wpforms-panel-content-section-title">';
		esc_html_e( 'Confirmations', 'wpforms' );
		echo '<button class="wpforms-confirmation-add wpforms-builder-settings-block-add" data-block-type="confirmation" data-next-id="' . absint( $next_id ) . '">' . esc_html__( 'Add New Confirmation', 'wpforms' ) . '</button>';
		echo '</div>';

		foreach ( $confirmations as $id => $confirmation ) {

			$name          = ! empty( $confirmation['name'] ) ? $confirmation['name'] : esc_html__( 'Default Confirmation', 'wpforms' );
			$closed_state  = '';
			$toggle_state  = '<i class="fa fa-chevron-up"></i>';
			$block_classes = 'wpforms-confirmation wpforms-builder-settings-block';

			if ( $default_confirmation_key === $id ) {
				$block_classes .= ' wpforms-confirmation-default';
			}

			if ( ! empty( $settings->form_data['id'] ) && 'closed' === wpforms_builder_settings_block_get_state( $settings->form_data['id'], $id, 'confirmation' ) ) {
				$closed_state = 'style="display:none"';
				$toggle_state = '<i class="fa fa-chevron-down"></i>';
			}

			do_action( 'wpforms_form_settings_confirmations_single_before', $settings, $id );
			?>

			<div class="<?php echo esc_attr( $block_classes ); ?>" data-block-type="confirmation" data-block-id="<?php echo absint( $id ); ?>">

				<div class="wpforms-builder-settings-block-header">
					<div class="wpforms-builder-settings-block-actions">
						<?php do_action( 'wpforms_form_settings_confirmations_single_action', $id, $confirmation, $settings ); ?>

						<button class="wpforms-builder-settings-block-edit"><i class="fa fa-pencil"></i></button>
						<button class="wpforms-builder-settings-block-toggle"><?php echo $toggle_state; ?></button>
						<button class="wpforms-builder-settings-block-delete"><i class="fa fa-times-circle"></i></button>
					</div>

					<div class="wpforms-builder-settings-block-name-holder">
						<span class="wpforms-builder-settings-block-name"><?php echo esc_html( $name ); ?></span>

						<div class="wpforms-builder-settings-block-name-edit">
							<input type="text" name="settings[confirmations][<?php echo absint( $id ); ?>][name]" value="<?php echo esc_attr( $name ); ?>">
						</div>
					</div>

				</div>

				<div class="wpforms-builder-settings-block-content" <?php echo $closed_state; ?>>

					<?php
					wpforms_panel_field(
						'select',
						'confirmations',
						'type',
						$settings->form_data,
						esc_html__( 'Confirmation Type', 'wpforms' ),
						array(
							'default'     => 'message',
							'options'     => array(
								'message'  => esc_html__( 'Message', 'wpforms' ),
								'page'     => esc_html__( 'Show Page', 'wpforms' ),
								'redirect' => esc_html__( 'Go to URL (Redirect)', 'wpforms' ),
							),
							'class'       => 'wpforms-panel-field-confirmations-type-wrap',
							'input_id'    => 'wpforms-panel-field-confirmations-type-' . $id,
							'input_class' => 'wpforms-panel-field-confirmations-type',
							'parent'      => 'settings',
							'subsection'  => $id,
						)
					);
					wpforms_panel_field(
						'textarea',
						'confirmations',
						'message',
						$settings->form_data,
						esc_html__( 'Confirmation Message', 'wpforms' ),
						array(
							'default'     => esc_html__( 'Thanks for contacting us! We will be in touch with you shortly.', 'wpforms' ),
							'tinymce'     => array(
								'editor_height' => '200',
							),
							'input_id'    => 'wpforms-panel-field-confirmations-message-' . $id,
							'input_class' => 'wpforms-panel-field-confirmations-message',
							'parent'      => 'settings',
							'subsection'  => $id,
						)
					);
					wpforms_panel_field(
						'checkbox',
						'confirmations',
						'message_scroll',
						$settings->form_data,
						esc_html__( 'Automatically scroll to the confirmation message', 'wpforms' ),
						array(
							'input_id'    => 'wpforms-panel-field-confirmations-message_scroll-' . $id,
							'input_class' => 'wpforms-panel-field-confirmations-message_scroll',
							'parent'      => 'settings',
							'subsection'  => $id,
						)
					);
					$p     = array();
					$pages = get_pages();
					foreach ( $pages as $page ) {
						$depth          = count( $page->ancestors );
						$p[ $page->ID ] = str_repeat( '-', $depth ) . ' ' . $page->post_title;
					}
					wpforms_panel_field(
						'select',
						'confirmations',
						'page',
						$settings->form_data,
						esc_html__( 'Confirmation Page', 'wpforms' ),
						array(
							'options'     => $p,
							'input_id'    => 'wpforms-panel-field-confirmations-page-' . $id,
							'input_class' => 'wpforms-panel-field-confirmations-page',
							'parent'      => 'settings',
							'subsection'  => $id,
						)
					);
					wpforms_panel_field(
						'text',
						'confirmations',
						'redirect',
						$settings->form_data,
						esc_html__( 'Confirmation Redirect URL', 'wpforms' ),
						array(
							'input_id'    => 'wpforms-panel-field-confirmations-redirect-' . $id,
							'input_class' => 'wpforms-panel-field-confirmations-redirect',
							'parent'      => 'settings',
							'subsection'  => $id,
						)
					);

					wpforms_conditional_logic()->builder_block( array(
						'form'        => $settings->form_data,
						'type'        => 'panel',
						'panel'       => 'confirmations',
						'parent'      => 'settings',
						'subsection'  => $id,
						'actions'     => array(
							'go'   => esc_html__( 'Use', 'wpforms' ),
							'stop' => esc_html__( 'Don\'t use', 'wpforms' ),
						),
						'action_desc' => esc_html__( 'this confirmation if', 'wpforms' ),
						'reference'   => esc_html__( 'Form confirmations', 'wpforms' ),
					) );

					do_action_deprecated(
						'wpforms_form_settings_confirmation',
						array( $settings ),
						'1.4.8 of WPForms plugin',
						'wpforms_form_settings_confirmations_single_after'
					);

					// Hook for addons.
					do_action( 'wpforms_form_settings_confirmations_single_after', $settings, $id );
					?>

				</div><!-- /.wpforms-builder-settings-block-content -->

			</div><!-- /.wpforms-builder-settings-block -->

			<?php
		}
	}

	/**
	 * Append additional strings for form builder.
	 *
	 * @since 1.2.6
	 *
	 * @param array  $strings List of strings.
	 * @param object $form
	 *
	 * @return array
	 */
	public function form_builder_strings( $strings, $form ) {

		$currency   = wpforms_setting( 'currency', 'USD' );
		$currencies = wpforms_get_currencies();

		$strings['currency']            = sanitize_text_field( $currency );
		$strings['currency_name']       = sanitize_text_field( $currencies[ $currency ]['name'] );
		$strings['currency_decimal']    = sanitize_text_field( $currencies[ $currency ]['decimal_separator'] );
		$strings['currency_thousands']  = sanitize_text_field( $currencies[ $currency ]['thousands_separator'] );
		$strings['currency_symbol']     = sanitize_text_field( $currencies[ $currency ]['symbol'] );
		$strings['currency_symbol_pos'] = sanitize_text_field( $currencies[ $currency ]['symbol_pos'] );

		return $strings;
	}

	/**
	 * Modify javascript `wpforms_settings` properties on site front end.
	 *
	 * @since 1.4.6
	 *
	 * @param array $strings Array wpforms_setting properties.
	 *
	 * @return array
	 */
	public function frontend_strings( $strings ) {

		// If the user has GDPR enhancements enabled and has disabled UUID,
		// disable the the setting, otherwise enable it.
		$strings['uuid_cookie'] = ! wpforms_setting( 'gdpr-disable-uuid', false );

		return $strings;
	}

	/**
	 * Check to see if the Conditional Logic addon is installed, if so notify
	 * the user that it can be removed.
	 *
	 * @since 1.3.8
	 */
	public function conditional_logic_addon_notice() {

		if ( file_exists( WP_PLUGIN_DIR . '/wpforms-conditional-logic/wpforms-conditional-logic.php' ) && ! defined( 'WPFORMS_DEBUG' ) ) {
			echo '<div class="notice notice-info"><p>';
			printf(
				wp_kses(
					/* translators: %s - WPForms.com announcement page URL. */
					__( 'Conditional logic functionality is now included in the core WPForms plugin! The WPForms Conditional Logic addon can be removed without affecting your forms. For more details <a href="%s" target="_blank" rel="noopener noreferrer">read our announcement</a>.', 'wpforms' ),
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
							'rel'    => array(),
						),
					)
				),
				'https://wpforms.com/announcing-wpforms-1-3-8/'
			);
			echo '</p></div>';
		}
	}

	/**
	 * Used to register the templates for setting blocks inside form builder.
	 *
	 * @since 1.4.8
	 */
	public function builder_templates() {
		?>

		<!-- Confirmation block 'message' field template -->
		<script type="text/html" id="tmpl-wpforms-builder-confirmations-message-field">
			<div id="wpforms-panel-field-confirmations-message-{{ data.id }}-wrap" class="wpforms-panel-field  wpforms-panel-field-textarea" style="display: block;">
				<label for="wpforms-panel-field-confirmations-message-{{ data.id }}"><?php esc_html_e( 'Confirmation Message', 'wpforms' ); ?></label>
				<textarea id="wpforms-panel-field-confirmations-message-{{ data.id }}" name="settings[confirmations][{{ data.id }}][message]" rows="3" placeholder="" class="wpforms-panel-field-confirmations-message"></textarea>
			</div>
		</script>

		<!-- Conditional logic toggle field template -->
		<script  type="text/html" id="tmpl-wpforms-builder-conditional-logic-toggle-field">
			<div id="wpforms-panel-field-settings-{{ data.type }}s-{{ data.id }}-conditional_logic-wrap" class="wpforms-panel-field wpforms-conditionals-enable-toggle wpforms-panel-field-checkbox">
				<input type="checkbox" id="wpforms-panel-field-settings-{{ data.type }}s-{{ data.id }}-conditional_logic-checkbox" name="settings[{{ data.type }}s][{{ data.id }}][conditional_logic]" value="1"
				       class="wpforms-panel-field-conditional_logic-checkbox"
				       data-name="settings[{{ data.type }}s][{{ data.id }}]"
				       data-actions="{{ data.actions }}"
				       data-action-desc="{{ data.actionDesc }}"><label for="wpforms-panel-field-settings-{{ data.type }}s-{{ data.id }}-conditional_logic-checkbox" class="inline"><?php esc_html_e( 'Enable conditional logic', 'wpforms' ); ?> <i class="fa fa-question-circle wpforms-help-tooltip tooltipstered"></i></label>
			</div>
		</script>

		<?php
	}

	/**
	 * Expired license notification in form notification email footer.
	 *
	 * @since 1.5.0
	 *
	 * @param string $text Footer text.
	 *
	 * @return string
	 */
	public function form_notification_footer( $text ) {

		$license = get_option( 'wpforms_license', array() );

		if (
			empty( $license['is_expired'] ) &&
			empty( $license['is_disabled'] ) &&
			empty( $license['is_invalid'] )
		) {
			return $text;
		}

		$notice = sprintf(
			wp_kses(
				/* translators: %1$s - WPForms.com Account dashboard URL */
				__( 'Your WPForms license key has expired. In order to continue receiving support and plugin updates you must renew your license key. Please log in to <a href="%1$s" target="_blank" rel="noopener noreferrer">your WPForms.com account</a> to renew your license.', 'wpforms' ),
				array(
					'a'      => array(
						'href'   => array(),
						'target' => array(),
						'rel'    => array(),
					),
				)
			),
			'https://wpforms.com/account/'
		);

		return $notice . '<br><br>' . $text;
	}
}
new WPForms_Pro();
