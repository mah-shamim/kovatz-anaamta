<?php
/**
 * DB Updater module
 *
 * Version: 1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'CX_DB_Updater' ) ) {

	/**
	 * Class CX_DB_Updater.
	 *
	 * @since 1.0.0
	 */
	class CX_DB_Updater {

		/**
		 * Module arguments.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		private $args = array(
			'path'      => '',
			'url'       => '',
			'callbacks' => array(),
			'slug'      => null,
			'version'   => null,
			'labels'    => array(
				'start_update' => 'Start Update',
				'data_update'  => 'Data Update',
				'messages'     => array(
					'error'   => 'Module DB Updater init error in %s - version and slug is required arguments',
					'update'  => 'We need to update your database to the latest version.',
					'updated' => 'Update complete, thank you for updating to the latest version!',
				),
			),
		);

		/**
		 * Option key for DB version.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $version_key = '%s-db-version';

		/**
		 * Nonce format.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $nonce = '_%s-db-update-nonce';

		/**
		 * Messages array.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		protected $messages = array();

		/**
		 * Update done trigger.
		 *
		 * @since 1.0.0
		 * @var bool
		 */
		protected $updated = false;

		/**
		 * Cherry_Db_Updater constructor.
		 *
		 * @since  1.0.0
		 * @param  array $args Module arguments.
		 * @return void
		 */
		public function __construct( $args = array() ) {

			$this->args = wp_parse_args( $args, $this->args );

			if ( ! is_admin() || ! current_user_can( 'update_plugins' ) ) {
				return;
			}

			add_action( 'admin_notices', array( $this, 'init_notices' ) );
			add_action( 'admin_init',    array( $this, 'do_update' ) );

			$this->messages = $this->args['labels']['messages'];
		}

		/**
		 * Process DB update.
		 *
		 * @since 1.0.0
		 */
		public function do_update() {

			if ( ! $this->is_current_update() ) {
				return;
			}

			$callbacks = $this->prepare_callbacks();

			if ( ! empty( $callbacks ) ) {
				foreach ( $callbacks as $callback ) {
					if ( is_callable( $callback ) ) {
						call_user_func( $callback );
					}
				}
			}

			$this->set_updated();
		}

		/**
		 * Finalize update.
		 *
		 * @since 1.0.0
		 */
		public function set_updated() {
			$this->updated = true;
			$option        = sprintf( $this->version_key, esc_attr( $this->args['slug'] ) );
			update_option( $option, esc_attr( $this->args['version'] ) );
		}

		/**
		 * Prepare callbacks array.
		 *
		 * @since 1.0.0
		 * @return array
		 */
		private function prepare_callbacks() {

			$callbacks = array();

			if ( empty( $this->args['callbacks'] ) ) {
				return $callbacks;
			}

			ksort( $this->args['callbacks'] );

			foreach ( $this->args['callbacks'] as $ver => $ver_cb ) {
				if ( version_compare( $this->get_current_version(), $ver, '<' ) ) {
					$callbacks = array_merge( $callbacks, $ver_cb );
				}
			}

			return $callbacks;
		}

		/**
		 * Check if we processed update for plugin passed in arguments.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		private function is_current_update() {

			if ( empty( $_GET['cherry_x_db_update'] ) || empty( $_GET['slug'] ) || empty( $_GET['_nonce'] ) ) {
				return false;
			}

			if ( $_GET['slug'] !== $this->args['slug'] ) {
				return false;
			}

			$nonce_action = sprintf( $this->nonce, esc_attr( $this->args['slug'] ) );

			if ( ! wp_verify_nonce( $_GET['_nonce'], $nonce_action ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Init admin notices.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function init_notices() {

			$enabled = $this->validate_module_args();

			if ( ! $enabled ) {
				return;
			}

			$slug = esc_attr( $this->args['slug'] );

			if ( $this->is_update_required() ) {
				$this->show_notice( $slug );
			}

			if ( $this->is_updated() ) {
				$this->show_updated_notice();
			}
		}

		/**
		 * Returns current DB version.
		 *
		 * @since 1.0.0
		 * @return string
		 */
		private function get_current_version() {
			$option = sprintf( $this->version_key, esc_attr( $this->args['slug'] ) );

			return get_option( $option, '1.0.0' );
		}

		/**
		 * Check if database requires update.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		private function is_update_required() {
			$current = $this->get_current_version();

			return version_compare( $current, esc_attr( $this->args['version'] ), '<' );
		}

		/**
		 * Check if update was successfully done.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		private function is_updated() {

			if ( ! $this->is_current_update() ) {
				return false;
			}

			return (bool) $this->updated;
		}

		/**
		 * Validate module arguments.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		private function validate_module_args() {

			if ( empty( $this->args['slug'] ) || empty( $this->args['version'] ) ) {
				echo '<div class="error"><p>';
				printf(
					$this->messages['error'],
					'<b>' . str_replace( untrailingslashit( ABSPATH ), '', $this->args['path'] ) . '</b>'
				);
				echo '</p></div>';

				return false;
			}

			return true;
		}

		/**
		 * Show notice.
		 *
		 * @since  1.0.0
		 * @param  string $slug Plugin slug.
		 * @return void
		 */
		private function show_notice( $slug ) {

			echo '<div class="notice notice-info">';
				echo '<p>';
					$this->notice_title( $slug );
					echo $this->messages['update'];
				echo '</p>';
				echo '<p>';
					$this->notice_submit( $slug );
				echo '</p>';
			echo '</div>';
		}

		/**
		 * Show update notice.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function show_updated_notice() {

			$slug = esc_attr( $this->args['slug'] );

			echo '<div class="notice notice-success is-dismissible">';
				echo '<p>';
					$this->notice_title( $slug );
					echo $this->messages['updated'];
				echo '</p>';
			echo '</div>';
		}

		/**
		 * Show plugin notice submit button.
		 *
		 * @since  1.0.0
		 * @param  string $slug Plugin slug.
		 * @return void
		 */
		private function notice_submit( $slug = '' ) {

			$format = '<a href="%1s" class="button button-primary">%2$s</a>';
			$label  = $this->args['labels']['start_update'];
			$url    = add_query_arg(
				array(
					'cherry_x_db_update' => true,
					'slug'               => $slug,
					'_nonce'             => $this->create_nonce( $slug ),
				),
				esc_url( admin_url( 'index.php' ) )
			);

			printf( $format, $url, $label );

		}

		/**
		 * Create DB update nonce.
		 *
		 * @since  1.0.0
		 * @param  string $slug Plugin slug.
		 * @return string
		 */
		private function create_nonce( $slug ) {
			return wp_create_nonce( sprintf( $this->nonce, $slug ) );
		}

		/**
		 * Show plugin notice title.
		 *
		 * @since  1.0.0
		 * @param  string $slug Plugin slug.
		 * @return void
		 */
		private function notice_title( $slug ) {

			$name = str_replace( '-', ' ', $slug );
			$name = ucwords( $name );

			printf( '<strong>%1$s %2$s</strong> &#8211; ', $name, $this->args['labels']['data_update'] );
		}
	}
}
