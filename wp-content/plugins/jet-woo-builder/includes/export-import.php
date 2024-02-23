<?php
/**
 * JetWooBuilder Export & Import Class.
 *
 * JetWooBuilder template library export/import handler class is responsible for
 * export/import local JetWooBuilder templates saved by the user locally on his site.
 *
 * @package JetWooBuilder
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Export_Import' ) ) {

	class Jet_Woo_Builder_Export_Import {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.12.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			// Add export action.
			add_filter( 'post_row_actions', [ $this, 'post_row_actions' ], 10, 2 );
			// Template import.
			add_action( 'admin_action_jet_woo_builder_import_template', [ $this, 'import_woo_builder_template' ] );
			// Admin initialized scripts.
			add_action( 'admin_init', [ $this, 'export_woo_builder_template' ] );

		}

		/**
		 * Post row actions.
		 *
		 * Add an export link to the template library action links table list.
		 *
		 * Fired by `post_row_actions` filter.
		 *
		 * @since  1.12.0
		 * @access public
		 *
		 * @param array    $actions An array of row action links.
		 * @param \WP_Post $post    The post object.
		 *
		 * @return array An updated array of row action links.
		 */
		public function post_row_actions( $actions, \WP_Post $post ) {

			global $current_screen;

			if ( ! $current_screen ) {
				return $actions;
			}

			if ( 'edit' === $current_screen->base && jet_woo_builder_post_type()->slug() === $current_screen->post_type ) {
				$actions['jet_woo_builder_export'] = sprintf( '<a id="jet-woo-builder-export-link" href="%s">%s</a>', $this->get_export_link( $post->ID ), __( 'Export Template', 'jet-woo-builder' ) );
			}

			return $actions;

		}

		/**
		 * Export link.
		 *
		 * Formation of query arg parameters.
		 *
		 * @since  1.12.0
		 * @access public
		 *
		 * @param int $template_id Export template id.
		 *
		 * @return string Export link with proper parameters.
		 */
		public function get_export_link( $template_id ) {
			return add_query_arg(
				[
					'action'                  => 'jet_woo_builder_export_template',
					'woo_builder_template_id' => $template_id,
				],
				admin_url( 'admin-ajax.php' )
			);
		}

		/**
		 * Export template.
		 *
		 * Prepare export template on admin initialization.
		 *
		 * @since  1.12.0
		 * @access public
		 */
		public function export_woo_builder_template() {

			if ( ! isset( $_GET['action'] ) ) {
				return;
			}

			if ( 'jet_woo_builder_export_template' !== $_GET['action'] && ! isset( $_GET['woo_builder_template_id'] ) ) {
				return;
			}

			$template_id = $_GET['woo_builder_template_id'];

			$this->export_template( $template_id );

		}

		/**
		 * Export local template.
		 *
		 * Export template to a file.
		 *
		 * @since  1.12.0
		 * @access public
		 *
		 * @param int $template_id The template ID.
		 *
		 * @return \WP_Error WordPress' error if template export failed.
		 */
		public function export_template( $template_id ) {

			$file_data = $this->prepare_woo_builder_template_export( $template_id );

			if ( is_wp_error( $file_data ) ) {
				return $file_data;
			}

			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Disposition: attachment; filename=' . $file_data['name'] );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate' );
			header( 'Pragma: public' );
			header( 'Content-Length: ' . strlen( $file_data['content'] ) );

			// Clear buffering just in case.
			@ob_end_clean();

			flush();

			// Output file contents.
			echo $file_data['content'];

			die();

		}

		/**
		 * Prepare template to export.
		 *
		 * Retrieve the relevant template data and return them as an array.
		 *
		 * @since  1.12.0
		 * @access private
		 *
		 * @param int $template_id The template ID.
		 *
		 * @throws Exception
		 * @return WP_Error|array Exported template data.
		 */
		public function prepare_woo_builder_template_export( $template_id ) {

			$document = \Elementor\Plugin::$instance->documents->get( $template_id );
			$content  = $document->get_elements_raw_data( null, true );

			if ( empty( $content ) ) {
				return new \WP_Error( 'empty_template', 'The template is empty' );
			}

			$template_data                  = [];
			$template_data['content']       = $content;
			$template_data['template_type'] = $document->get_name();

			$page_settings = get_post_meta( $template_id, '_elementor_page_settings', true );

			if ( $page_settings ) {
				$template_data['page_settings'] = $page_settings;
			}

			$export_data = [
				'version' => Elementor\DB::DB_VERSION,
				'title'   => get_the_title( $template_id ),
			];

			$export_data += $template_data;

			return [
				'name'    => 'jet-woo-builder-' . $template_id . '-' . date( 'Y-m-d' ) . '.json',
				'content' => wp_json_encode( $export_data ),
			];

		}

		/**
		 * Import local template.
		 *
		 * Import template from a file.
		 *
		 * @since  1.12.0
		 * @access private
		 */
		public function import_woo_builder_template() {

			if ( ! current_user_can( 'import' ) ) {
				wp_die( __( 'You don\'t have permissions to do this', 'jet-woo-builder' ) );
			}

			if ( empty( $_FILES ) ) {
				wp_die( __( 'File not passed', 'jet-woo-builder' ) );
			}

			$file = $_FILES['file'];

			if ( 'application/json' !== $file['type'] ) {
				wp_die( __( 'Format not allowed', 'jet-woo-builder' ) );
			}

			$content = file_get_contents( $file['tmp_name'] );
			$content = json_decode( $content, true );

			if ( ! $content ) {
				wp_die( __( 'No data found in file', 'jet-woo-builder' ) );
			}

			$documents         = Elementor\Plugin::instance()->documents;
			$doc_type          = $documents->get_document_type( jet_woo_builder_post_type()->slug() );
			$template_content  = $content['content'];
			$template_content  = $this->process_export_import_content( $template_content, 'on_import' );
			$template_settings = isset( $content['page_settings'] ) ? $content['page_settings'] : [];

			$post_data = [
				'post_type'  => jet_woo_builder_post_type()->slug(),
				'meta_input' => [
					'_elementor_edit_mode'     => 'builder',
					$doc_type::TYPE_META_KEY   => $content['template_type'],
					'_elementor_data'          => wp_slash( json_encode( $template_content ) ),
					'_elementor_page_settings' => $template_settings,
				],
			];

			$post_data['post_title'] = ! empty( $content['title'] ) ? $content['title'] : __( 'New Template', 'jet-woo-builder' );

			$template_id = wp_insert_post( $post_data );

			if ( ! $template_id ) {
				wp_die(
					esc_html__( 'Can\'t create template. Please try again', 'jet-woo-builder' ),
					esc_html__( 'Error', 'jet-woo-builder' )
				);
			}

			wp_redirect( jet_woo_builder()->elementor()->documents->get( $template_id )->get_edit_url() );

			die();

		}

		/**
		 * Process content for export/import.
		 *
		 * Process the content and all the inner elements, and prepare all the
		 * elements data for export/import.
		 *
		 * @since  1.12.0
		 * @access protected
		 *
		 * @param array  $content A set of elements.
		 * @param string $method  Accepts either `on_export` to export data or `on_import` to import data.
		 *
		 * @return mixed Processed content data.
		 */
		protected function process_export_import_content( $content, $method ) {
			return ELementor\Plugin::$instance->db->iterate_data( $content, function ( $element_data ) use ( $method ) {
				$element = ELementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );

				// If the widget/element isn't exist, like a plugin that creates a widget but deactivated
				if ( ! $element ) {
					return null;
				}

				return $this->process_element_export_import_content( $element, $method );
			} );
		}

		/**
		 * Process single element content for export/import.
		 *
		 * Process any given element and prepare the element data for export/import.
		 *
		 * @since  1.12.0
		 * @access protected
		 *
		 * @param Controls_Stack $element
		 * @param string         $method
		 *
		 * @return array Processed element data.
		 */
		protected function process_element_export_import_content( $element, $method ) {

			$element_data = $element->get_data();

			if ( method_exists( $element, $method ) ) {
				// TODO: Use the internal element data without parameters.
				$element_data = $element->{$method}( $element_data );
			}

			foreach ( $element->get_controls() as $control ) {
				$control_class = ELementor\Plugin::$instance->controls_manager->get_control( $control['type'] );

				// If the control isn't exist, like a plugin that creates the control but deactivated.
				if ( ! $control_class ) {
					return $element_data;
				}

				if ( method_exists( $control_class, $method ) ) {
					$element_data['settings'][ $control['name'] ] = $control_class->{$method}( $element->get_settings( $control['name'] ), $control );
				}
			}

			return $element_data;

		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.12.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;

		}

	}

}
