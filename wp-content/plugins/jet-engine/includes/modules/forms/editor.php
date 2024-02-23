<?php
/**
 * Form editor class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Booking_Forms_Editor' ) ) {

	/**
	 * Define Jet_Engine_Booking_Forms_Editor class
	 */
	class Jet_Engine_Booking_Forms_Editor {

		public $manager;

		/**
		 * Constructor for the class
		 */
		function __construct( $manager ) {

			$this->manager = $manager;

			add_action( 'init', array( $this, 'register_post_type' ) );

			if ( is_admin() ) {
				add_action( 'current_screen', array( $this, 'init_meta' ) );
				add_action( 'admin_menu', array( $this, 'add_menu_page' ), 60 );
				add_action( 'add_meta_boxes_' . $this->manager->slug(), array( $this, 'disable_metaboxes' ), 9999 );
				add_filter( 'post_row_actions', array( $this, 'remove_view_action' ), 10, 2 );
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
			}

			add_action( 'save_post', array( $this, 'save_layout' ), 999 );

			add_action( 'wp_ajax_jet_engine_forms_get_mailchimp_data', array( $this, 'get_mailchimp_data' ) );
			add_action( 'wp_ajax_nopriv_jet_engine_form_get_mailchimp_data', array( $this, 'get_mailchimp_data' ) );
			add_action( 'wp_ajax_jet_engine_forms_getresponse_data', array( $this, 'getresponse_data' ) );
			add_action( 'wp_ajax_nopriv_jet_engine_forms_getresponse_data', array( $this, 'getresponse_data' ) );

		}

		/**
		 * Save layout and notifications data
		 *
		 * @param  [type] $post_id [description]
		 *
		 * @return [type]          [description]
		 */
		public function save_layout( $post_id ) {

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( $this->manager->slug() !== get_post_type( $post_id ) ) {
				return;
			}

			if ( ! current_user_can( 'edit_posts' ) ) {
				return;
			}

			if ( empty( $_POST ) || ! isset( $_POST['_wpnonce'] ) ) {
				return;
			}

			if ( ! empty( $_POST['action'] ) && 'inline-save' === $_POST['action'] && ! empty( $_POST['_inline_edit'] ) ) {
				return;
			}

			$form_data = isset( $_POST['_form_data'] ) ? $_POST['_form_data'] : json_encode( array() );
			$captcha   = isset( $_POST['_captcha'] ) ? $_POST['_captcha'] : array(
				'enabled' => false,
				'key'     => '',
				'secret'  => ''
			);

			update_post_meta( $post_id, '_form_data', wp_slash( $form_data ) );

			$this->manager->captcha->update_meta( $post_id );

			$preset = new Jet_Engine_Booking_Forms_Preset( $post_id );

			$preset->update_meta();

			$form_data = json_decode( wp_unslash( $form_data ), true );
			$builder   = $this->manager->get_form_builder( $post_id, $form_data );

			ob_start();
			$builder->render_form( true );
			$rendered_form = ob_get_clean();

			$n_data = isset( $_POST['_notifications_data'] ) ? $_POST['_notifications_data'] : json_encode( array() );
			update_post_meta( $post_id, '_notifications_data', wp_slash( $n_data ) );

			do_action( 'jet-engine/forms/editor/save-meta', $post_id, $this );

		}

		/**
		 * Returns generators list
		 *
		 * @return [type] [description]
		 */
		public function get_generators_list() {

			$generators = $this->manager->get_options_generators();
			$result     = array(
				0 => __( 'Select function...', 'jet-engine' ),
			);

			foreach ( $generators as $id => $generator ) {
				$result[ $id ] = $generator->get_name();
			}

			return $result;

		}

		/**
		 * Returns avalable sources to get select, chackbox and radio fields options from
		 *
		 * @return [type] [description]
		 */
		public function get_field_options_sources() {
			return apply_filters( 'jet-engine/forms/editor/field-options-sources', array(
				'manual_input' => __( 'Manual Input', 'jet-engine' ),
				'posts'        => __( 'Posts', 'jet-engine' ),
				'terms'        => __( 'Terms', 'jet-engine' ),
				'meta_field'   => __( 'Meta Field', 'jet-engine' ),
				'generate'     => __( 'Generate Dynamically', 'jet-engine' ),
			) );
		}

		/**
		 * Enqueue forms assets
		 *
		 * @return [type] [description]
		 */
		public function enqueue_assets() {

			if ( get_post_type() !== $this->manager->slug() ) {
				return;
			}

			$screen = get_current_screen();

			if ( 'post' !== $screen->base ) {
				return;
			}

			wp_enqueue_style(
				'jet-engine-forms',
				jet_engine()->plugin_url( 'assets/css/admin/forms.css' ),
				array(),
				jet_engine()->get_version()
			);

			$module_data = jet_engine()->framework->get_included_module_data( 'cherry-x-vue-ui.php' );

			$ui = new CX_Vue_UI( $module_data );

			$ui->enqueue_assets( true );

			do_action( 'jet-engine/forms/editor/before-assets', $this );

			wp_enqueue_script(
				'vue-grid-layout',
				jet_engine()->plugin_url( 'assets/lib/vue-grid-layout/vue-grid-layout.min.js' ),
				array(),
				'2.3.7',
				true
			);

			wp_enqueue_script(
				'vue-slicksort',
				jet_engine()->plugin_url( 'assets/lib/vue-slicksort/vue-slicksort.min.js' ),
				array(),
				jet_engine()->get_version(),
				true
			);

			wp_enqueue_script(
				'jet-engine-forms',
				jet_engine()->plugin_url( 'assets/js/admin/forms.js' ),
				array( 'jquery' ),
				jet_engine()->get_version(),
				true
			);

			$notifications = $this->get_notifications();

			if ( empty( $notifications ) ) {
				$notifications = array(
					array(
						'type'            => 'email',
						'mail_to'         => 'admin',
						'hook_name'       => '',
						'custom_email'    => '',
						'from_field'      => '',
						'post_type'       => '',
						'fields_map'      => array(),
						'meta_fields_map' => array(),
						'log_in'          => '',
						'email'           => array(
							'content'      => "Hi admin!

There are new order on your website.

Order details:
- Post ID: %post_id%",
							'subject'      => __( 'New order on website', 'jet-engine' ),
							'content_type' => 'text/html',
						),
						'mailchimp'       => array(
							'fields_map' => array(),
							'data'       => array(),
						),
						'activecampaign'  => array(
							'fields_map' => array(),
							'lists'      => array(),
						),
						'getresponse'     => array(
							'fields_map' => array(),
							'data'       => array(),
						),
					)
				);
			}

			$preset = new Jet_Engine_Booking_Forms_Preset( get_the_ID() );

			$user_fields = $this->get_user_fields();
			$user_props  = array_merge(
				array(
					0    => __( 'Select user property...', 'jet-engine' ),
					'ID' => __( 'User ID (will update this user)', 'jet-engine' ),
				),
				$user_fields,
				array(
					'user_meta' => __( 'User Meta', 'jet-engine' ),
				)
			);

			unset( $user_props['login'] );

			$preset_sources = apply_filters( 'jet-engine/forms/preset-sources', array(
				array(
					'value' => 'post',
					'label' => __( 'Post', 'jet-engine' ),
				),
				array(
					'value' => 'user',
					'label' => __( 'User', 'jet-engine' ),
				),
				array(
					'value' => 'query_vars',
					'label' => __( 'URL Query Variables', 'jet-engine' ),
				),
				array(
					'value' => 'option_page',
					'label' => __( 'Option Page', 'jet-engine' ),
				),
			) );

			$hidden_values = apply_filters( 'jet-engine/forms/hidden-values', array(
				'post_id'      => __( 'Current Post ID', 'jet-engine' ),
				'post_title'   => __( 'Current Post Title', 'jet-engine' ),
				'post_url'     => __( 'Current Post/Page URL', 'jet-engine' ),
				'post_meta'    => __( 'Current Post Meta', 'jet-engine' ),
				'user_id'      => __( 'Current User ID', 'jet-engine' ),
				'user_email'   => __( 'Current User Email', 'jet-engine' ),
				'user_name'    => __( 'Current User Name', 'jet-engine' ),
				'user_meta'    => __( 'Current User Meta', 'jet-engine' ),
				'author_id'    => __( 'Current Post Author ID', 'jet-engine' ),
				'author_email' => __( 'Current Post Author Email', 'jet-engine' ),
				'author_name'  => __( 'Current Post Author Name', 'jet-engine' ),
				'query_var'    => __( 'URL Query Variable', 'jet-engine' ),
				'current_date' => __( 'Current Date', 'jet-engine' ),
				'manual_input' => __( 'Manual Input', 'jet-engine' ),
			) );

			wp_localize_script( 'jet-engine-forms', 'JetEngineFormSettings', array(
				'field_types'              => $this->manager->get_field_types(),
				'confirm_message'          => __( 'Are you sure you want to delete this field?', 'jet-engine' ),
				'form_data'                => $this->get_form_data(),
				'notifications_data'       => $notifications,
				'notification_types'       => $this->manager->get_notification_types(),
				'input_types'              => $this->manager->get_input_types(),
				'messages'                 => $this->get_messages(),
				'post_types'               => jet_engine()->listings->get_post_types_for_options(),
				'taxonomies'               => jet_engine()->listings->get_taxonomies_for_options(),
				'options_pages'            => jet_engine()->options_pages->get_options_pages_for_select(),
				'options_list'             => jet_engine()->options_pages->get_options_for_select( 'all', 'blocks' ),
				'pages'                    => $this->get_pages_list(),
				'post_statuses'            => get_post_statuses(),
				'user_fields'              => $user_fields,
				'user_roles'               => \Jet_Engine_Tools::get_user_roles_for_js(),
				'all_mimes'                => get_allowed_mime_types(),
				'generators_list'          => $this->get_generators_list(),
				'listing_items'            => jet_engine()->listings->get_listings_for_options(),
				'hidden_values'            => $hidden_values,
				'options_sources'          => $this->get_field_options_sources(),
				'labels'                   => array(
					'field'           => __( 'Field', 'jet-engine' ),
					'message'         => __( 'Message', 'jet-engine' ),
					'submit'          => __( 'Submit Button', 'jet-engine' ),
					'redirect_notice' => __( 'The “Redirect” notification should be the last one on the list. No other notifications will be processed after Redirect is done.', 'jet-engine' ),
				),
				'object_fields'            => $this->manager->get_object_fields(),
				'captcha'                  => $this->manager->captcha->get_data(),
				'preset'                   => $preset->get_data(),
				'preset_sources'           => $preset_sources,
				'post_props'               => array(
					0               => __( 'Select post property...', 'jet-engine' ),
					'ID'            => __( 'Post ID (will update post)', 'jet-engine' ),
					'post_title'    => __( 'Post Title', 'jet-engine' ),
					'post_name'     => __( 'Post Slug', 'jet-engine' ),
					'post_content'  => __( 'Post Content', 'jet-engine' ),
					'post_date'     => __( 'Post Date', 'jet-engine' ),
					'post_date_gmt' => __( 'Post Date GMT', 'jet-engine' ),
					'post_excerpt'  => __( 'Post Excerpt', 'jet-engine' ),
					'_thumbnail_id' => __( 'Post Thumbnail', 'jet-engine' ),
					'post_meta'     => __( 'Post Meta', 'jet-engine' ),
					'post_terms'    => __( 'Post Terms', 'jet-engine' ),
					'post_author'   => __( 'Post Author', 'jet-engine' ),
				),
				'user_props'               => $user_props,
				'activecamp_fields'        => $this->get_activecampaign_fields(),
				'default_settings'         => array(
					'name'               => 'field_name',
					'desc'               => '',
					'required'           => 'required',
					'type'               => 'text',
					'visibility'         => 'all',
					'field_type'         => 'text',
					'hidden_value'       => '',
					'hidden_value_field' => '',
					'field_options_from' => 'manual_input',
					'field_options_key'  => '',
					'field_options'      => array(),
					'label'              => '',
					'calc_formula'       => '',
					'precision'          => 2,
					'is_message'         => false,
					'is_submit'          => false,
					'is_page_break'      => false,
					'class_name'         => '',
				),
				'global_tabs'              => \Jet_Engine\Modules\Forms\Tabs\Tab_Manager::instance()->all(),
				'pairs_notifications_tabs' => $this->action_tabs()
 			) );

			do_action( 'jet-engine/forms/editor/assets', $this );

			wp_enqueue_script(
				'jet-engine-forms-register-forms-hooks',
				jet_engine()->plugin_url( 'assets/js/admin/register-form-hooks.js' ),
				array(),
				jet_engine()->get_version(),
				true
			);

			add_action( 'admin_footer', array( $this, 'print_component_templates' ), 0 );

		}

		/**
		 * Print additional component templates for from editor
		 *
		 * @return void
		 */
		public function print_component_templates() {

			$components = array(
				'post-field-control',
				'form-preset-editor',
			);

			foreach ( $components as $component ) {

				ob_start();
				include jet_engine()->get_template( 'forms/admin/components/' . $component . '.php' );
				$content = ob_get_clean();

				printf(
					'<script type="text/x-template" id="jet-%1$s">%2$s</script>',
					$component,
					$content
				);

			}

		}

		/**
		 * Returns pages list
		 * @return [type] [description]
		 */
		public function get_pages_list() {

			$pages = get_pages();
			$list  = wp_list_pluck( $pages, 'post_title', 'ID' );

			return $list;
		}

		/**
		 * Returns user fields for user notification
		 *
		 * @return [type] [description]
		 */
		public function get_user_fields() {
			return array(
				'login'            => __( 'User Login', 'jet-engine' ),
				'email'            => __( 'Email', 'jet-engine' ),
				'password'         => __( 'Password', 'jet-engine' ),
				'confirm_password' => __( 'Confirm Password', 'jet-engine' ),
				'first_name'       => __( 'First Name', 'jet-engine' ),
				'last_name'        => __( 'Last Name', 'jet-engine' ),
				'user_url'         => __( 'User URL', 'jet-engine' ),
			);
		}

		/**
		 * Returns ActiveCampaign fields
		 *
		 * @return array
		 */
		public function get_activecampaign_fields() {
			return apply_filters( 'jet-engine/forms/booking/activecampaign/fields', array(
				'email'              => __( 'Email', 'jet-engine' ),
				'first_name'         => __( 'First Name', 'jet-engine' ),
				'last_name'          => __( 'Last Name', 'jet-engine' ),
				'phone'              => __( 'Phone', 'jet-engine' ),
				'customer_acct_name' => __( 'Organization', 'jet-engine' ),
			) );
		}

		/**
		 * Returns saved notifications
		 *
		 * @param  [type] $post_id [description]
		 *
		 * @return [type]          [description]
		 */
		public function get_notifications( $post_id = null ) {

			if ( ! $post_id ) {
				$post_id = get_the_ID();
			}

			$data = get_post_meta( $post_id, '_notifications_data', true );

			if ( ! $data ) {
				$data = '[]';
			}

			return json_decode( wp_unslash( $data ), true );

		}

		/**
		 * Returns messages
		 *
		 * @param  [type] $post_id [description]
		 *
		 * @return [type]          [description]
		 */
		public function get_messages( $post_id = null ) {

			if ( ! $post_id ) {
				$post_id = get_the_ID();
			}

			$data = get_post_meta( $post_id, '_messages', true );

			if ( ! $data ) {
				$data = array();
			}

			return $data;

		}

		/**
		 * Returns saved fields
		 *
		 * @param  [type] $post_id [description]
		 *
		 * @return [type]          [description]
		 */
		public function get_form_data( $post_id = null ) {

			if ( ! $post_id ) {
				$post_id = get_the_ID();
			}

			$form_data = get_post_meta( $post_id, '_form_data', true );

			if ( ! $form_data || '[]' === $form_data ) {
				$form_data = array(
					array(
						'x'        => 0,
						'y'        => 0,
						'w'        => 12,
						'h'        => 1,
						'i'        => '0',
						'settings' => array(
							'name'               => 'post_id',
							'desc'               => '',
							'required'           => 'required',
							'type'               => 'hidden',
							'hidden_value'       => 'post_id',
							'hidden_value_field' => '',
							'field_options_from' => 'manual_input',
							'field_options_key'  => '',
							'field_options'      => array(),
							'label'              => __( 'Current Post ID', 'jet-engine' ),
							'calc_formula'       => '',
							'precision'          => 2,
							'is_message'         => false,
							'is_submit'          => false,
							'default'            => '',
						),
					),
					array(
						'x'        => 0,
						'y'        => 1,
						'w'        => 12,
						'h'        => 1,
						'i'        => '1',
						'settings' => array(
							'label'      => __( 'Submit', 'jet-engine' ),
							'name'       => __( 'Submit', 'jet-engine' ),
							'is_message' => false,
							'is_submit'  => true,
							'type'       => 'submit',
							'alignment'  => 'right',
							'class_name' => '',
						),
					),
				);

			} else {
				$form_data = self::sanitize_form_data( $form_data );
			}

			return $form_data;

		}

		/**
		 * Sanitize form data
		 *
		 * @param  [type] $form_data [description]
		 *
		 * @return [type]            [description]
		 */
		public static function sanitize_form_data( $form_data ) {

			$parsed_data = json_decode( wp_unslash( $form_data ), true );

			if ( ! $parsed_data ) {
				$parsed_data = json_decode( $form_data, true );
			}

			if ( ! $parsed_data ) {
				$parsed_data = array();
			}

			foreach ( $parsed_data as $index => $value ) {
				$parsed_data[ $index ]['i'] = '' . $value['i'];
			}

			return $parsed_data;
		}

		/**
		 * Menu page
		 */
		public function add_menu_page() {

			add_submenu_page(
				jet_engine()->admin_page,
				esc_html__( 'Forms', 'jet-engine' ),
				esc_html__( 'Forms', 'jet-engine' ),
				'edit_pages',
				'edit.php?post_type=' . $this->manager->slug()
			);

		}

		/**
		 * Actions posts
		 *
		 * @param  [type] $actions [description]
		 * @param  [type] $post    [description]
		 *
		 * @return [type]          [description]
		 */
		public function remove_view_action( $actions, $post ) {

			if ( $this->manager->slug() === $post->post_type ) {
				unset( $actions['view'] );
			}

			return $actions;

		}

		/**
		 * Disable metaboxes from Jet Templates
		 *
		 * @return void
		 */
		public function disable_metaboxes() {
			global $wp_meta_boxes;
			unset( $wp_meta_boxes[ $this->manager->slug() ]['side']['core']['pageparentdiv'] );
		}

		/**
		 * Register templates post type
		 *
		 * @return void
		 */
		public function register_post_type() {

			$args = array(
				'labels'              => array(
					'name'               => esc_html__( 'Forms', 'jet-engine' ),
					'singular_name'      => esc_html__( 'Form', 'jet-engine' ),
					'add_new'            => esc_html__( 'Add New', 'jet-engine' ),
					'add_new_item'       => esc_html__( 'Add New Form', 'jet-engine' ),
					'edit_item'          => esc_html__( 'Edit Form', 'jet-engine' ),
					'new_item'           => esc_html__( 'Add New Item', 'jet-engine' ),
					'view_item'          => esc_html__( 'View Form', 'jet-engine' ),
					'search_items'       => esc_html__( 'Search Form', 'jet-engine' ),
					'not_found'          => esc_html__( 'No Forms Found', 'jet-engine' ),
					'not_found_in_trash' => esc_html__( 'No Forms Found In Trash', 'jet-engine' ),
					'menu_name'          => esc_html__( 'Forms', 'jet-engine' ),
				),
				'public'              => true,
				'show_ui'             => true,
				'show_in_admin_bar'   => true,
				'show_in_menu'        => false,
				'show_in_nav_menus'   => false,
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'has_archive'         => false,
				'query_var'           => false,
				'can_export'          => true,
				'rewrite'             => false,
				'capability_type'     => 'post',
				'supports'            => array( 'title' ),
				'capabilities' => array(
					'publish_posts'       => 'manage_options',
					'edit_posts'          => 'manage_options',
					'edit_others_posts'   => 'manage_options',
					'delete_posts'        => 'manage_options',
					'delete_others_posts' => 'manage_options',
					'read_private_posts'  => 'manage_options',
					'edit_post'           => 'manage_options',
					'delete_post'         => 'manage_options',
					'read_post'           => 'manage_options',
				),
			);

			$post_type = register_post_type(
				$this->manager->slug(),
				apply_filters( 'jet-engine/forms/booking/post-type/args', $args )
			);

		}

		/**
		 * Initialize filters meta
		 *
		 * @return void
		 */
		public function init_meta( $current_screen ) {

			if ( $current_screen->id !== $this->manager->slug() ) {
				return;
			}

			$field_types    = $this->manager->get_field_types();
			$field_types    = array( '' => __( 'Select type...', 'jet-engine' ) ) + $field_types;
			$default_fields = array(
				'item-0' => array(
					'_label'              => __( 'Current Post ID', 'jet-engine' ),
					'_name'               => 'post_id',
					'_desc'               => '',
					'_required'           => 'true',
					'_type'               => 'hidden',
					'_hidden_value'       => 'post_id',
					'_hidden_value_field' => '',
					'_field_options_from' => 'manual_input',
					'_field_options_key'  => '',
					'_field_options'      => array(),
					'_calc_formula'       => '',
					'_precision'          => 2,
				),
			);

			ob_start();
			include jet_engine()->get_template( 'forms/admin/form-builder.php' );
			$content = ob_get_clean();

			$meta_fields_settings = apply_filters( 'jet-engine/forms/booking/meta-fields-settings', array(
				'_build_layout' => array(
					'type' => 'html',
					'html' => $content,
				)
			) );

			ob_start();
			include jet_engine()->get_template( 'forms/admin/notifications.php' );
			$content = ob_get_clean();

			$notifications_settings = apply_filters( 'jet-engine/forms/booking/notifications-settings', array(
				'_notifications' => array(
					'type' => 'html',
					'html' => $content,
				)
			) );

			ob_start();
			include jet_engine()->get_template( 'forms/admin/messages.php' );
			$content = ob_get_clean();

			$messages_settings = apply_filters( 'jet-engine/forms/booking/messages-settings', array(
				'_messages' => array(
					'type' => 'html',
					'html' => $content,
				)
			) );

			new Cherry_X_Post_Meta( array(
				'id'            => 'fields-settings',
				'title'         => __( 'Fields Settings', 'jet-engine' ),
				'page'          => array( $this->manager->slug() ),
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder' ),
				'fields'        => $meta_fields_settings,
			) );

			new Cherry_X_Post_Meta( array(
				'id'            => 'notifications-settings',
				'title'         => __( 'Post-submit Actions / Notifications Settings', 'jet-engine' ),
				'page'          => array( $this->manager->slug() ),
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder' ),
				'fields'        => $notifications_settings,
			) );

			new Cherry_X_Post_Meta( array(
				'id'            => 'messages-settings',
				'title'         => __( 'Messages Settings', 'jet-engine' ),
				'page'          => array( $this->manager->slug() ),
				'context'       => 'normal',
				'priority'      => 'high',
				'callback_args' => false,
				'builder_cb'    => array( $this, 'get_builder' ),
				'fields'        => $messages_settings,
			) );

			do_action( 'jet-engine/forms/editor/meta-boxes', $this );

		}

		/**
		 * Return UI builder instance
		 *
		 * @return [type] [description]
		 */
		public function get_builder() {

			$data = jet_engine()->framework->get_included_module_data( 'cherry-x-interface-builder.php' );

			return new CX_Interface_Builder(
				array(
					'path' => $data['path'],
					'url'  => $data['url'],
				)
			);

		}

		/**
		 * Get MailChimp data
		 */
		public function get_mailchimp_data() {

			if ( empty( $_REQUEST['api_key'] ) ) {
				wp_send_json_error();
			}

			$handler = new Jet_Engine_Forms_MailChimp_Handler( $_REQUEST['api_key'] );

			if ( is_wp_error( $handler ) ) {
				wp_send_json_error();
			}

			$data = $handler->get_all_data();

			if ( empty( $data['lists'] ) ) {
				wp_send_json_error();
			}

			wp_send_json_success( $data );
		}

		/**
		 * Get GetResponse data
		 */
		public function getresponse_data() {

			if ( empty( $_REQUEST['api_key'] ) ) {
				wp_send_json_error();
			}

			$handler = new Jet_Engine_Forms_GetResponse_Handler( $_REQUEST['api_key'] );

			if ( is_wp_error( $handler ) ) {
				wp_send_json_error();
			}

			$data = $handler->get_all_data();

			if ( empty( $data['lists'] ) ) {
				wp_send_json_error();
			}

			wp_send_json_success( $data );
		}

		public function action_tabs() {
			return apply_filters( 'jet-engine/forms/booking/actions-tabs', array(
				'activecampaign' => 'active-campaign',
				'mailchimp'      => 'mailchimp',
				'getresponse'    => 'get-response'
			) );
		}

	}

}
