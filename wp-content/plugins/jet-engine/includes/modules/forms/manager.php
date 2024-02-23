<?php
/**
 * Booking forms manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Booking_Forms' ) ) {

	/**
	 * Define Jet_Engine_Booking_Forms class
	 */
	#[AllowDynamicProperties]
	class Jet_Engine_Booking_Forms {

		public $post_type = 'jet-engine-booking';
		private $builder_instances = array();
		private $forms_for_options = null;
		public $generators = false;

		public $handler;
		public $editor;
		public $captcha;
		public $file_upload;
		public $export_import;
		private $block_types = array();

		/**
		 * Constructor for the class
		 */
		function __construct() {

			require_once jet_engine()->modules->modules_path( 'forms/handler.php' );
			require_once jet_engine()->modules->modules_path( 'forms/editor.php' );
			require_once jet_engine()->modules->modules_path( 'forms/captcha.php' );
			require_once jet_engine()->modules->modules_path( 'forms/preset.php' );
			require_once jet_engine()->modules->modules_path( 'forms/file-upload.php' );
			require_once jet_engine()->modules->modules_path( 'forms/shortcode.php' );
			require_once jet_engine()->modules->modules_path( 'forms/handlers/mailchimp.php' );
			require_once jet_engine()->modules->modules_path( 'forms/handlers/getresponse.php' );
			require_once jet_engine()->modules->modules_path( 'forms/tabs/tab-manager.php' );
			require_once jet_engine()->plugin_path( 'includes/dashboard/base-tab.php' );
			require_once jet_engine()->modules->modules_path( 'forms/dashboard-tab.php' );


			if ( apply_filters( 'jet-engine/forms/allow-gateways', false ) ) {
				require_once jet_engine()->modules->modules_path( 'forms/gateways/manager.php' );
				\Jet_Engine\Gateways\Manager::instance();
			}

			$this->editor  = new Jet_Engine_Booking_Forms_Editor( $this );
			$this->handler = new Jet_Engine_Booking_Forms_Handler( $this );
			$this->captcha = new Jet_Engine_Booking_Forms_Captcha();

			add_action( 'jet-engine/elementor-views/widgets/register', array( $this, 'register_widgets' ), 11, 2 );

			add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );

			/**
			 * Register Booking Forms scripts
			 */
			add_action( 'jet_plugins/frontend/register_scripts', array( $this, 'register_assets' ) );

			Jet_Engine_Forms_File_Upload::instance();
			Jet_Engine_Forms_Shortcode::instance();

			// WP import compatibility fix
			add_action( 'import_post_meta', function ( $post_id, $key, $value ) {
				$slashit = array( '_form_data', '_notifications_data' );
				if ( in_array( $key, $slashit ) && '[]' !== $value ) {
					delete_post_meta( $post_id, $key );
					update_post_meta( $post_id, $key, $value );
				}
			}, 10, 3 );

			if ( is_admin() ) {
				require_once jet_engine()->modules->modules_path( 'forms/export-import.php' );
				$this->export_import = new Jet_Engine_Forms_Export_Import();
			}

			// Register render classes.
			add_action( 'jet-engine/listings/renderers/registered', array( $this, 'register_render_classes' ) );

			// Blocks Integration
			add_action( 'jet-engine/blocks-views/register-block-types', array( $this, 'register_block_types' ) );
			add_filter( 'jet-engine/blocks-views/editor/config', array( $this, 'add_editor_config' ) );

			do_action( 'jet-engine/forms/init' );

			add_filter( 'jet-engine/dashboard/register-tabs', function ( $tabs ) {
				$tabs[] = new Jet_Engine_Booking_Forms_Dashboard_Tab();

				return $tabs;
			} );

			\Jet_Engine\Modules\Forms\Tabs\Tab_Manager::instance()->register_tabs();
		}

		/**
		 * Returns all instatnces of options genrators classes
		 *
		 * @return [type] [description]
		 */
		public function get_options_generators() {

			if ( false === $this->generators ) {

				if ( ! class_exists( '\Jet_Engine\Forms\Generators\Base' ) ) {
					require_once jet_engine()->modules->modules_path( 'forms/generators/base.php' );
				}

				require_once jet_engine()->modules->modules_path( 'forms/generators/num-range.php' );
				require_once jet_engine()->modules->modules_path( 'forms/generators/get-from-db.php' );
				require_once jet_engine()->modules->modules_path( 'forms/generators/get-from-field.php' );
				require_once jet_engine()->modules->modules_path( 'forms/generators/get-from-option.php' );

				$instances = array(
					new \Jet_Engine\Forms\Generators\Num_Range(),
					new \Jet_Engine\Forms\Generators\Get_From_DB(),
					new \Jet_Engine\Forms\Generators\Get_From_Field(),
					new \Jet_Engine\Forms\Generators\Get_From_Option(),
				);

				$instances = apply_filters( 'jet-engine/forms/options-generators', $instances, $this );

				$this->generators = array();

				foreach ( $instances as $instance ) {
					$this->generators[ $instance->get_id() ] = $instance;
				}

			}

			return $this->generators;

		}

		/**
		 * Register form JS
		 * @return [type] [description]
		 */
		public function register_assets() {

			wp_register_script(
				'jet-engine-frontend-forms',
				jet_engine()->plugin_url( 'assets/js/frontend-forms.js' ),
				array( 'jet-engine-frontend' ),
				jet_engine()->get_version(),
				true
			);

			wp_register_script(
				'jet-engine-inputmask',
				jet_engine()->plugin_url( 'assets/lib/inputmask/jquery.inputmask.min.js' ),
				array( 'jquery' ),
				jet_engine()->get_version(),
				true
			);

		}

		/**
		 * Return object fields
		 *
		 * @return [type] [description]
		 */
		public function get_object_fields() {
			return array(
				'ID',
				'post_title',
				'post_name',
				'post_content',
				'post_excerpt',
				'post_date',
				'post_date_gmt',
				'post_author',
			);
		}

		/**
		 * Returns all created forms for options
		 *
		 * @return array
		 */
		public function get_forms_for_options() {

			if ( null !== $this->forms_for_options ) {
				return $this->forms_for_options;
			}

			$forms = get_posts( array(
				'posts_per_page' => - 1,
				'post_status'    => 'publish',
				'post_type'      => $this->slug(),
			) );

			$this->forms_for_options = array( '' => esc_html__( 'Select...', 'jet-engine' ) ) + wp_list_pluck( $forms, 'post_title', 'ID' );

			return $this->forms_for_options;

		}

		/**
		 * Returns all created forms for options
		 *
		 * @return array
		 */
		public function get_forms_for_js_options() {
			$list = $this->get_forms_for_options();

			$result = array();

			foreach ( $list as $value => $label ) {
				$result[] = array(
					'value' => $value,
					'label' => $label,
				);
			}

			return $result;
		}

		/**
		 * Return builder for passed form ID
		 * @return [type] [description]
		 */
		public function get_form_builder( $form_id = null, $form_data = false, $args = array() ) {

			if ( ! class_exists( 'Jet_Engine_Booking_Forms_Builder' ) ) {
				require_once jet_engine()->modules->modules_path( 'forms/builder.php' );
			}

			if ( ! isset( $this->builder_instances[ $form_id ] ) ) {

				$builder = new Jet_Engine_Booking_Forms_Builder(
					$form_id,
					$form_data,
					$args,
					$this->captcha
				);

				$builder->set_manager( $this );

				$this->builder_instances[ $form_id ] = $builder;
			}

			return $this->builder_instances[ $form_id ];

		}

		/**
		 * Returns form messages
		 *
		 * @param  [type] $form_id [description]
		 *
		 * @return [type]          [description]
		 */
		public function get_messages_builder( $form_id ) {

			if ( ! class_exists( 'Jet_Engine_Booking_Forms_Messages' ) ) {
				require_once jet_engine()->modules->modules_path( 'forms/messages.php' );
			}

			return new Jet_Engine_Booking_Forms_Messages( $form_id );

		}

		/**
		 * Retuirns all available notification types
		 *
		 * @return [type] [description]
		 */
		public function get_notification_types() {
			return apply_filters( 'jet-engine/forms/booking/notification-types', array(
				'email'          => __( 'Send Email', 'jet-engine' ),
				'insert_post'    => __( 'Insert/Update Post', 'jet-engine' ),
				'register_user'  => __( 'Register New User', 'jet-engine' ),
				'update_user'    => __( 'Update User', 'jet-engine' ),
				'update_options' => __( 'Update Options', 'jet-engine' ),
				'hook'           => __( 'Call a Hook', 'jet-engine' ),
				'webhook'        => __( 'Call a Webhook', 'jet-engine' ),
				'redirect'       => __( 'Redirect to Page', 'jet-engine' ),
				'mailchimp'      => __( 'MailChimp', 'jet-engine' ),
				'activecampaign' => __( 'ActiveCampaign', 'jet-engine' ),
				'getresponse'    => __( 'GetResponse', 'jet-engine' ),
			) );
		}

		/**
		 * Returna available input types
		 *
		 * @return array
		 */
		public function get_input_types() {
			return apply_filters( 'jet-engine/forms/booking/input-types', array(
				'text'     => __( 'Text', 'jet-engine' ),
				'email'    => __( 'Email', 'jet-engine' ),
				'url'      => __( 'URL', 'jet-engine' ),
				'tel'      => __( 'Tel', 'jet-engine' ),
				'password' => __( 'Password', 'jet-engine' ),
			) );
		}

		/**
		 * Returns all messages types
		 *
		 * @return [type] [description]
		 */
		public function get_message_types() {

			return apply_filters( 'jet-engine/forms/booking/message-types', array(
				'success'           => array(
					'label'   => __( 'Form successfully submitted.', 'jet-engine' ),
					'default' => __( 'Form successfully submitted.', 'jet-engine' ),
				),
				'failed'            => array(
					'label'   => __( 'Submit failed.', 'jet-engine' ),
					'default' => __( 'There was an error trying to submit form. Please try again later.', 'jet-engine' ),
				),
				'validation_failed' => array(
					'label'   => __( 'Validation error', 'jet-engine' ),
					'default' => __( 'One or more fields have an error. Please check and try again.', 'jet-engine' ),
				),
				'invalid_email'     => array(
					'label'   => __( 'Entered an invalid email', 'jet-engine' ),
					'default' => __( 'The e-mail address entered is invalid.', 'jet-engine' ),
				),
				'empty_field'       => array(
					'label'   => __( 'Required field is empty', 'jet-engine' ),
					'default' => __( 'The field is required.', 'jet-engine' ),
				),
				'password_mismatch' => array(
					'label'   => __( 'Register User specific: Passwords mismatch', 'jet-engine' ),
					'default' => __( 'Passwords don\'t match.', 'jet-engine' ),
				),
				'username_exists'   => array(
					'label'   => __( 'Register User specific: Username Exists', 'jet-engine' ),
					'default' => __( 'This username already taken.', 'jet-engine' ),
				),
				'email_exists'      => array(
					'label'   => __( 'Register User specific: Email exists', 'jet-engine' ),
					'default' => __( 'This email address is already used.', 'jet-engine' ),
				),
				'sanitize_user'     => array(
					'label'   => __( 'Register User specific: Incorrect username', 'jet-engine' ),
					'default' => __( 'Username contains not allowed characters.', 'jet-engine' ),
				),
				'empty_username'    => array(
					'label'   => __( 'Register User specific: Empty username', 'jet-engine' ),
					'default' => __( 'Please set username.', 'jet-engine' ),
				),
				'empty_email'       => array(
					'label'   => __( 'Register User specific: Empty email', 'jet-engine' ),
					'default' => __( 'Please set user email.', 'jet-engine' ),
				),
				'empty_password'    => array(
					'label'   => __( 'Register User specific: Empty password', 'jet-engine' ),
					'default' => __( 'Please set user password.', 'jet-engine' ),
				),
				'already_logged_in' => array(
					'label'   => __( 'Register User specific: Logged in (appears only if register user is only notification)', 'jet-engine' ),
					'default' => __( 'You already logged in.', 'jet-engine' ),
				),
				'captcha_failed'    => array(
					'label'   => __( 'Captcha validation failed', 'jet-engine' ),
					'default' => __( 'Captcha validation failed', 'jet-engine' ),
				),
				'internal_error'    => array(
					'label'   => __( 'Internal server error', 'jet-engine' ),
					'default' => __( 'Internal server error. Please try again later.', 'jet-engine' ),
				),
				'upload_max_files'  => array(
					'label'   => __( 'Media Specific: Max files limit', 'jet-engine' ),
					'default' => __( 'Maximum upload files limit is reached.', 'jet-engine' ),
				),
				'upload_max_size'   => array(
					'label'   => __( 'Media Specific: Max size reached', 'jet-engine' ),
					'default' => __( 'Upload max size exceeded.', 'jet-engine' ),
				),
				'upload_mime_types' => array(
					'label'   => __( 'Media Specific: File type error', 'jet-engine' ),
					'default' => __( 'File type is not allowed.', 'jet-engine' ),
				),
			) );

		}

		/**
		 * Templates post type slug
		 *
		 * @return string
		 */
		public function slug() {
			return $this->post_type;
		}

		/**
		 * Returns field types
		 * @return [type] [description]
		 */
		public function get_field_types() {

			return apply_filters( 'jet-engine/forms/booking/field-types', array(
				'text'           => __( 'Text', 'jet-engine' ),
				'textarea'       => __( 'Textarea', 'jet-engine' ),
				'hidden'         => __( 'Hidden', 'jet-engine' ),
				'select'         => __( 'Select', 'jet-engine' ),
				'checkboxes'     => __( 'Checkboxes', 'jet-engine' ),
				'radio'          => __( 'Radio', 'jet-engine' ),
				'number'         => __( 'Number', 'jet-engine' ),
				'date'           => __( 'Date', 'jet-engine' ),
				'time'           => __( 'Time', 'jet-engine' ),
				'datetime-local' => __( 'Datetime', 'jet-engine' ),
				'calculated'     => __( 'Calculated', 'jet-engine' ),
				'media'          => __( 'Media', 'jet-engine' ),
				'wysiwyg'        => __( 'WYSIWYG', 'jet-engine' ),
				'range'          => __( 'Range', 'jet-engine' ),
				'heading'        => __( 'Heading', 'jet-engine' ),
				'group_break'    => __( 'Group Break', 'jet-engine' ),
				'repeater_start' => __( 'Repeatable Fields Group Start', 'jet-engine' ),
				'repeater_end'   => __( 'Repeatable Fields Group End', 'jet-engine' ),
			) );

		}

		/**
		 * Register plugin widgets
		 *
		 * @param $widgets_manager
		 * @param $elementor_views
		 *
		 * @return void
		 */
		public function register_widgets( $widgets_manager, $elementor_views ) {

			$elementor_views->register_widget(
				jet_engine()->modules->modules_path( 'forms/widgets/booking-form.php' ),
				$widgets_manager,
				'Elementor\Jet_Engine_Booking_Form_Widget'
			);

			$elementor_views->register_widget(
				jet_engine()->modules->modules_path( 'forms/widgets/check-mark.php' ),
				$widgets_manager,
				'Elementor\Jet_Engine_Check_Mark_Widget'
			);

		}

		/**
		 * !!! Deprecated
		 * Register new widget
		 *
		 * @return void
		 */
		public function register_widget( $file, $widgets_manager ) {

			$base  = basename( str_replace( '.php', '', $file ) );
			$class = ucwords( str_replace( '-', ' ', $base ) );
			$class = str_replace( ' ', '_', $class );
			$class = sprintf( 'Elementor\Jet_Engine_%s_Widget', $class );

			require_once $file;

			if ( class_exists( $class ) ) {

				if ( method_exists( $widgets_manager, 'register' ) ) {
					$widgets_manager->register( new $class );
				} else {
					$widgets_manager->register_widget_type( new $class );
				}
			}

		}

		/**
		 * Register render classes.
		 *
		 * @param $listings
		 */
		public function register_render_classes( $listings ) {

			$base = jet_engine()->modules->modules_path( 'forms/render/' );

			foreach ( glob( $base . '*.php' ) as $file ) {
				$name  = basename( str_replace( '.php', '', $file ) );
				$class = ucwords( str_replace( '-', ' ', $name ) );
				$class = str_replace( ' ', '_', $class );
				$class = sprintf( 'Jet_Engine\Forms\Render\%s', $class );

				$listings->register_render_class(
					$name,
					array(
						'class_name' => $class,
						'path'       => $file,
					)
				);
			}
		}

		/**
		 * Register block types
		 *
		 * @param object $blocks_types
		 *
		 * @return void
		 */
		public function register_block_types( $blocks_types ) {
			$base = jet_engine()->modules->modules_path( 'forms/block-types/' );

			foreach ( glob( $base . '*.php' ) as $file ) {
				$name  = basename( str_replace( '.php', '', $file ) );
				$class = ucwords( str_replace( '-', ' ', $name ) );
				$class = str_replace( ' ', '_', $class );
				$class = sprintf( 'Jet_Engine\Forms\Block_Types\%s', $class );

				require $file;

				$instance = new $class;

				$blocks_types->register_block_type( $instance );
				$this->block_types[] = $name;
			}
		}

		/**
		 * Add editor config.
		 *
		 * @param array $config
		 *
		 * @return array
		 */
		public function add_editor_config( $config = array() ) {

			foreach ( $this->block_types as $type ) {
				$name = ucwords( str_replace( '-', ' ', $type ) );
				$name = str_replace( ' ', '', $name );
				$name = lcfirst( $name );

				$config['atts'][ $name ] = jet_engine()->blocks_views->block_types->get_block_atts( $type );
			}

			$config['formsOptions'] = $this->get_forms_for_js_options();

			return $config;
		}

	}

}
