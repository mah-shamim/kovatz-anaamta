<?php
/**
 * Theme Admin
 *
 * @package BlockStrap
 * @since 1.0.0
 */

/**
 * Add admin settings.
 *
 * @since 1.0.0
 */
//echo '###1';exit;
class BlockStrap_Admin {

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu_page' ) );
		add_filter( 'aui_screen_ids', array( $this, 'aui_screen_ids' ) );

		add_action( 'wp_ajax_blockstrap_plugin_management', array( $this, 'plugin_management_handler' ) );
		add_action( 'wp_ajax_blockstrap_page_management', array( $this, 'page_management_handler' ) );
		add_action( 'wp_ajax_blockstrap_save_recaptcha_keys', array( $this, 'recaptcha_management_handler' ) );

		// Settings sections
		add_action( 'blockstrap_admin_settings_sections', array( $this, 'get_required_pages_html' ) );
		add_action( 'blockstrap_admin_settings_sections', array( $this, 'get_required_plugins_html' ) );
		add_action( 'blockstrap_admin_settings_sections', array( $this, 'get_recaptcha_html' ) );

		// Page install on activation
		add_action('admin_init', array( $this, 'maybe_add_pages' ) );

	}

	/**
	 * Add demo pages on theme install.
	 *
	 * @return void
	 */
	public function maybe_add_pages(){
		$pages = $this->get_demo_pages();

		if ( ! empty( $pages ) ) {
			$theme_slug = sanitize_title_with_dashes( $this->get_theme_title() );
			if ( ! get_option( 'blockstrap_demo_pages_installed_' . $theme_slug ) ) {
				foreach ( $pages as $page ) {
					if(!$this->demo_page_exists( $page['slug'] )){
						$page_id = $this->add_demo_page( $page );
					}
				}
				update_option( 'blockstrap_demo_pages_installed_' . $theme_slug, true );
			}

		}
	}

	/**
	 * Load AUI on our settings page.
	 *
	 * @param $aui_screens
	 *
	 * @return mixed
	 */
	public function aui_screen_ids( $aui_screens ) {

		$aui_screens[] = 'appearance_page_blockstrap';

		return $aui_screens;
	}

	/**
	 * Register the menu item.
	 * @return void
	 */
	public function register_menu_page() {
		add_submenu_page(
			'themes.php',
			__( 'BlockStrap Settings', 'blockstrap' ),
			__( 'Theme Setup', 'blockstrap' ),
			'manage_options',
			'blockstrap',
			array( $this, 'output_settings_page' ),
			60
		);
	}

	/**
	 * Output the settings page HTML.
	 *
	 * @return void
	 */
	public function output_settings_page() {
		?>
		<div class="bsui blockstrap-settings">
			<div class="bg-white me-3 p-3 mt-3 rounded shadow-sm vh-100">
				<div class="d-flex align-items-center">
					<h1 class="text-dark "><?php echo esc_html( $this->get_theme_title() ); ?></h1>
					<span class="badge bg-faded-info fs-sm mb-2 ms-2">v<?php echo esc_html( $this->get_version() ); ?></span>
				</div>

				<div class="row row-cols-1 row-cols-md-2">
					<?php

					do_action( 'blockstrap_admin_settings_sections' );

					?>

				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get the theme version number.
	 *
	 * @return string|null
	 */
	public function get_version() {
		return esc_attr( wp_get_theme()->get( 'Version' ) );
	}

	/**
	 * Get the theme title.
	 *
	 * @return string|null
	 */
	public function get_theme_title() {
		return __( 'BlockStrap', 'blockstrap' );
	}

	/**
	 * Get the required pages HTML output.
	 *
	 * @return void
	 */
	public function get_required_pages_html() {
		$pages = $this->get_demo_pages();
		?>
		<div class="col">
		<div class="card h-100 mw-100">
		<h3 class="h4 text-dark"><?php _e( 'Optional Pages', 'blockstrap' ); ?></h3>
			<ul class="list-group  list-group-flush">

				<?php

				if ( empty( $pages ) ) {
					echo aui()->alert(
						array(
							'type'    => 'info',
							'content' => __( 'No demo pages for this theme', 'blockstrap' ),
							'class'   => 'mb-3 text-left text-start',
						)
					);
				} else {
					foreach ( $pages as $slug => $page ) {
						$active = $this->demo_page_exists( $slug );

						$exists = false;// !$active ? get_page_by_path($slug) : false;
						?>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="mr-auto me-auto"><?php echo esc_html( $page['title'] ); ?></span>
							<?php
							if ( $exists ) {
								echo '<i class="fas fa-exclamation-triangle mr-2 me-2 text-warning fa-lg c-pointer" data-bs-toggle="tooltip" data-bs-title="' . __( 'Page with same slug exists', 'blockstrap' ) . '"></i>';
							}

							if ( $active && false ) {
								$link = get_permalink( $active );
								echo '<a class="bs-demo-link" href="' . esc_url( $link ) . '" target="_blank" ><i class="fas fa-external-link-alt mr-2 me-2 text-muted fa-lg c-pointer" data-bs-toggle="tooltip" data-bs-title="' . __( 'Page with same slug exists', 'blockstrap' ) . '"></i></a>';
							}
							?>
							<div class="spinner-border spinner-border-sm mr-2 me-2 d-none text-muted" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							<div class="form-check form-switch mb-0">
								<input class="form-check-input" type="checkbox" role="switch" id="blockstrap-req-plugin-<?php echo esc_attr( $slug ); ?>"
									<?php
									if ( $active ) {
										echo 'checked';
									}

									if ( $exists ) {
										echo ' disabled ';
									}
									?>
									   onclick="blockstrap_admin_toggle_demo_page(this,'<?php echo esc_attr( $slug ); ?>',!jQuery(this).is(':checked'));">
							</div>
						</li>
						<?php
					}
				}

				?>
			</ul>
		</div>
		</div>
		<?php
	}

	/**
	 * Get the required plugins HTML output.
	 *
	 * @return void
	 */
	public function get_required_plugins_html() {
		$required_plugins = $this->get_required_plugins();
		?>
		<div class="col">
			<div class="card h-100 mw-100">
				<h3 class="h4 text-dark"><?php _e( 'Required Plugins', 'blockstrap' ); ?></h3>
				<ul class="list-group list-group-flush">

					<?php
					foreach ( $required_plugins as $slug => $name ) {
						$active = is_plugin_active( $slug . '/' . $slug . '.php' );
						?>
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<span class="mr-auto me-auto"><?php echo esc_html( $name ); ?></span>
							<div class="spinner-border spinner-border-sm mr-2 me-2 d-none text-muted" role="status">
								<span class="visually-hidden">Loading...</span>
							</div>
							<div class="form-check form-switch mb-0">
								<input class="form-check-input" type="checkbox" role="switch" id="blockstrap-req-plugin-<?php echo esc_attr( $slug ); ?>"
																																   <?php
																																	if ( $active ) {
																																		echo 'checked';}
																																	?>
								 onclick="blockstrap_admin_toggle_plugin_activation(this,'<?php echo esc_attr( $slug ); ?>',!jQuery(this).is(':checked'));">
							</div>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
		</div>
		<?php

		$this->get_setting_js();
	}

	/**
	 * Get the required plugins HTML output.
	 *
	 * @return void
	 */
	public function get_recaptcha_html() {
		$keys = get_option('blockstrap_recaptcha_keys');
		$site_key = isset($keys['site_key']) ? esc_attr($keys['site_key']) : '';
		$site_secret = isset($keys['site_secret']) ? esc_attr($keys['site_secret']) : '';
		?>
		<div class="col mt-5">
			<div class="card h-100 mw-100 p-0">
				<h5 class="card-header h4 text-dark"><?php _e( 'Recaptcha Keys', 'blockstrap' ); ?></h5>
				 <div class="card-body">
						<div class="alert alert-info" role="alert">
							<?php _e( 'Please enter your Google recaptcha <b>v2 Tickbox</b> keys. (this helps protect the contact form block)', 'blockstrap' ); ?>
							<a href="https://www.google.com/recaptcha/admin" target="_blank"> <?php _e( 'Get Keys', 'blockstrap' ); ?> <i class="fas fa-external-link-alt"></i></a>
						</div>
						<form class="w-100" onsubmit="blockstrap_admin_save_recaptcha_keys(this);return false;">
							<div class="mb-3">
								<label for="gc-site-key" class="form-label"><?php _e( 'Site Key', 'blockstrap' ); ?></label>
								<input type="text" class="form-control" name="site_key" id="gc-site-key" value="<?php echo esc_attr($site_key); ?>">
							</div>
							<div class="mb-3">
								<label for="gc-secret-key" class="form-label"><?php _e( 'Secret Key', 'blockstrap' ); ?></label>
								<input type="password" class="form-control" name="site_secret" id="gc-secret-key" value="<?php echo esc_attr($site_secret); ?>">
							</div>
							<button type="submit" class="btn btn-primary">  <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
								<?php _e( 'Save', 'blockstrap' ); ?></button>
						</form>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get the Settings JS.
	 *
	 * @return void
	 */
	public function get_setting_js() {

		?>
		<script>

			/**
			 * Toggle required plugin activations.
			 *
			 * @param $this
			 * @param $plugin
			 * @param $deactivate
			 */
			function blockstrap_admin_save_recaptcha_keys($this){

				var data = {
					action: 'blockstrap_save_recaptcha_keys',
					security: '<?php echo esc_attr( wp_create_nonce( 'blockstrap_save_recaptcha_keys' ) ); ?>',
					form_data: jQuery($this).serialize()
				};

				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					// dataType: 'html'
					beforeSend: function() {
						jQuery($this).find('.btn-primary').prop('disabled', true).find('.spinner-border').removeClass('d-none');
					},
					success: function(data) {
						if (data.success) {
							jQuery($this).find('.btn-primary').prop('disabled', false).find('.spinner-border').addClass('d-none');
							aui_toast('blockstrap_recaptcha_keys_success','success', data.data);

						} else {
							let $checked = !!data.deactivate;
							jQuery($this).find('.btn-primary').prop('disabled', false).find('.spinner-border').addClass('d-none');
							aui_toast('blockstrap_recaptcha_keys_error', data.data);
						}
					},
					error: function(xhr) { // if error occured
						alert("Error occured.please try again");
					},
					complete: function() {
						jQuery($this).find('.btn-primary').prop('disabled', false).find('.spinner-border').addClass('d-none');

					},
				});
			}

			/**
			 * Toggle required plugin activations.
			 *
			 * @param $this
			 * @param $plugin
			 * @param $deactivate
			 */
			function blockstrap_admin_toggle_plugin_activation($this,$plugin,$deactivate){

				var data = {
					action: 'blockstrap_plugin_management',
					security: '<?php echo esc_attr( wp_create_nonce( 'blockstrap_plugin_management' ) ); ?>',
					plugin_slug: $plugin,
					deactivate: $deactivate
				};

				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					// dataType: 'html'
					beforeSend: function() {
						jQuery($this).prop('disabled', true).parent().parent().find('.spinner-border').removeClass('d-none');
					},
					success: function(data) {
						if (data.success) {
							jQuery($this).prop('disabled', false).parent().parent().find('.spinner-border').addClass('d-none');
							if ($deactivate) {
								aui_toast('blockstrap_plugin_deactivation_success','success', data.data);
							}else{
								aui_toast('blockstrap_plugin_activation_success','success', data.data);
							}
						} else {
							let $checked = !!data.deactivate;
							jQuery($this).prop('disabled', false).prop('checked', $checked).parent().parent().find('.spinner-border').addClass('d-none');
							aui_toast('blockstrap_plugin_management_error_'+$plugin,'error', data.data);
						}
					},
					error: function(xhr) { // if error occured
						alert("Error occured.please try again");
					},
					complete: function() {
						jQuery($this).prop('disabled', false).parent().parent().find('.spinner-border').addClass('d-none');
					},
				});
			}

			/**
			 * Toggle demo page activations.
			 *
			 * @param $this
			 * @param $page_slug
			 * @param $delete
			 */
			function blockstrap_admin_toggle_demo_page($this,$page_slug,$delete){

				var data = {
					action: 'blockstrap_page_management',
					security: '<?php echo esc_attr( wp_create_nonce( 'blockstrap_page_management' ) ); ?>',
					page_slug: $page_slug,
					delete: $delete
				};

				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					// dataType: 'html'
					beforeSend: function() {
						jQuery($this).prop('disabled', true).parent().parent().find('.spinner-border').removeClass('d-none');
					},
					success: function(data) {
						if (data.success) {
							jQuery($this).prop('disabled', false).parent().parent().find('.spinner-border').addClass('d-none');
							if ($delete) {
								aui_toast('blockstrap_page_deactivation_success','success', data.data);
							}else{
								aui_toast('blockstrap_page_activation_success','success', data.data);
							}
						} else {
							let $checked = !!data.delete;
							jQuery($this).prop('disabled', false).prop('checked', $checked).parent().parent().find('.spinner-border').addClass('d-none');
							aui_toast('blockstrap_page_management_error','error', data.data);
						}
					},
					error: function(xhr) { // if error occured
						let $checked = !!data.delete;
						jQuery($this).prop('disabled', false).prop('checked', $checked).parent().parent().find('.spinner-border').addClass('d-none');
						alert("Error occured.please try again");
					},
					complete: function() {
						jQuery($this).prop('disabled', false).parent().parent().find('.spinner-border').addClass('d-none');
					},
				});
			}

		</script>
		<?php
	}

	/**
	 * Get the required plugins details array.
	 *
	 * @return array
	 */
	public function get_required_plugins() {
		return array(
			'blockstrap-page-builder-blocks' => __( 'BlockStrap Builder', 'blockstrap' ),
		);
	}

	/**
	 * Get the array of demo pages.
	 *
	 * @return array[]
	 */
	public function get_demo_pages() {
		return array();
	}

	/**
	 * Get the content of the template file.
	 *
	 * @param $path string Relative to theme root.
	 *
	 * @return false|string
	 */
	public function get_template_content( $path ) {
		ob_start();
		include $path;
		return ob_get_clean();
	}

	/**
	 * The AJAX function that will activate or deactivate require plugins.
	 *
	 * @return void
	 */
	public function plugin_management_handler() {

		if ( current_user_can( 'activate_plugins' ) ) {
			if ( ! check_ajax_referer( 'blockstrap_plugin_management', 'security', false ) ) {
				wp_send_json_error( 'Invalid nonce' );
				wp_die();
			}

			$plugin_slug = isset( $_POST['plugin_slug'] ) ? sanitize_text_field( $_POST['plugin_slug'] ) : '';
			$deactivate  = isset( $_POST['deactivate'] ) ? filter_var( $_POST['deactivate'], FILTER_VALIDATE_BOOLEAN ) : false;

			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			require_once ABSPATH . 'wp-admin/includes/plugin-install.php';

			// Check if the plugin is installed.
			$plugin_info = plugins_api( 'plugin_information', array( 'slug' => $plugin_slug ) );
			if ( is_wp_error( $plugin_info ) ) {
				wp_send_json_error( 'Plugin not found' );
				wp_die();
			}

			$plugin_file      = $plugin_slug . '/' . $plugin_info->slug . '.php';
			$plugin_installed = is_plugin_active( $plugin_file );
			if ( ! $plugin_installed ) {
				$installed_plugins = get_plugins();
				$plugin_installed  = array_key_exists( $plugin_file, $installed_plugins ) || in_array( $plugin_file, $installed_plugins, true );
			}

			if ( $deactivate ) {
				if ( $plugin_installed ) {
					deactivate_plugins( $plugin_file );
					wp_send_json_success( 'Plugin deactivated successfully' );
				} else {
					wp_send_json_error( 'Plugin not active' );
				}
			} else {
				if ( ! $plugin_installed ) {
					// Install and activate the plugin.
					$upgrader       = new Plugin_Upgrader();
					$install_result = $upgrader->install( $plugin_info->download_link );
					if ( is_wp_error( $install_result ) ) {
						wp_send_json_error( 'Plugin installation failed: ' . $install_result->get_error_message() );
						wp_die();
					}
				}

				// Activate the plugin.
				$activate_result = activate_plugin( $plugin_file );
				if ( is_wp_error( $activate_result ) ) {
					wp_send_json_error( 'Plugin activation failed: ' . $activate_result->get_error_message() );
					wp_die();
				} else {
					wp_send_json_success( 'Plugin activated successfully' );
				}
			}
		} else {
			wp_send_json_error( 'You do not have permission to activate/deactivate plugins' );
		}

		wp_die();
	}

	/**
	 * Check if demo page exists and return the ID if so.
	 *
	 * @param $page_slug
	 *
	 * @return int|string
	 */
	public function demo_page_exists( $page_slug ) {
		$theme_slug  = get_template();
		$option_key  = 'blockstrap_demo_pages';
		$page_status = get_option( $option_key );
		return isset( $page_status[ $theme_slug ][ $page_slug ] ) ? absint( $page_status[ $theme_slug ][ $page_slug ] ) : '';
	}

	/**
	 * The AJAX function that will add or remove the demo pages.
	 *
	 * @return void
	 */
	public function page_management_handler() {

		if ( current_user_can( 'activate_plugins' ) ) {
			if ( ! check_ajax_referer( 'blockstrap_page_management', 'security', false ) ) {
				wp_send_json_error( 'Invalid nonce' );
				wp_die();
			}

			$demo_pages = $this->get_demo_pages();
			$page_slug  = isset( $_POST['page_slug'] ) ? sanitize_text_field( $_POST['page_slug'] ) : '';
			$delete     = isset( $_POST['delete'] ) && $_POST['delete'] === 'true';

			if ( ! isset( $demo_pages[ $page_slug ] ) ) {
				wp_send_json_error( __( 'Page is not set', 'blockstrap' ) );
				wp_die();
			} else {
				$page = $demo_pages[ $page_slug ];
			}

			$theme_slug  = get_template();
			$option_key  = 'blockstrap_demo_pages';
			$page_status = get_option( $option_key );
			$page_id     = $this->demo_page_exists( $page_slug );

			if ( $delete ) {

				if ( $page_id ) {
					wp_trash_post( $page_id );
					unset( $page_status[ $theme_slug ][ $page_slug ] );
					update_option( $option_key, $page_status );
				}

				wp_send_json_success( __( 'Page moved to trash', 'blockstrap' ) );
				wp_die();

			} else {
				if ( ! $page_id ) {
					$page_id = $this-> add_demo_page( $page );

					if ( is_wp_error( $page_id ) ) {
						wp_send_json_error( __( 'Page failed to create', 'blockstrap' ) );
						wp_die();
					}

					wp_send_json_success( __( 'Page Added', 'blockstrap' ) );
					wp_die();
				} else {
					wp_send_json_error( __( 'Page already exists', 'blockstrap' ) );
					wp_die();
				}
			}
		} else {
			wp_send_json_error( 'You do not have permission to add/remove pages.' );
		}

		wp_die();
	}

	/**
	 * Add a demo page from arguments.
	 *
	 * @param $page
	 *
	 * @return false|int|WP_Error
	 */
	public function add_demo_page( $page ) {

		$theme_slug  = get_template();
		$option_key  = 'blockstrap_demo_pages';
		$page_status = get_option( $option_key );
		$page_slug   = esc_attr( $page['slug'] );

		$page_id = wp_insert_post(
			array(
				'post_title'   => $page['title'],
				'post_name'    => $page['slug'],
				'post_content' => $page['desc'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
			)
		);

		if ( is_wp_error( $page_id ) ) {
			return false;
		}else{
			$page_status[ $theme_slug ][ $page_slug ] = $page_id;
			update_option( $option_key, $page_status );

			if ( ! empty( $page['is_blog'] ) ) {
				update_option( 'page_for_posts', $page_id );
				update_option('show_on_front', 'page');

				if ( ! get_option( 'page_on_front' ) ) {
					update_option( 'page_on_front', 2 ); // if page on front not set then it will show blog page on front.
				}
			} elseif ( ! empty( $page['is_front'] ) ) {
				update_option( 'page_on_front', $page_id ); // this is probably not needed as the theme can set the front page anyway.
				update_option('show_on_front', 'page');
			}
		}

		return $page_id;


	}

	/**
	 * The AJAX function that will save the recaptcha keys.
	 *
	 * @return void
	 */
	public function recaptcha_management_handler() {

		if ( current_user_can( 'activate_plugins' ) ) {
			if ( ! check_ajax_referer( 'blockstrap_save_recaptcha_keys', 'security', false ) ) {
				wp_send_json_error( 'Invalid nonce' );
				wp_die();
			}


			parse_str( $_POST['form_data'], $data );
//			print_r($data );

			if(isset($data['site_key']) && isset($data['site_secret'])) {
				update_option('blockstrap_recaptcha_keys',array(
					'site_key' => sanitize_html_class($data['site_key']),
					'site_secret' => sanitize_html_class($data['site_secret']),
				));
				wp_send_json_success( __( 'Keys Saved', 'blockstrap' ) );
			}else{
				wp_send_json_error( 'Something went wrong' );
			}

		} else {
			wp_send_json_error( 'You do not have permission for this.' );
		}

		wp_die();
	}

}


