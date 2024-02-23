<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Skins_Import' ) ) {

	/**
	 * Define Jet_Engine_Skins_Import class
	 */
	class Jet_Engine_Skins_Import {

		public $nonce = 'jet-engine-import';
		public $log  = array();
		public $errors = array();

		/**
		 * Initialize components
		 */
		public function __construct() {
			add_action( 'wp_ajax_jet_engine_import_skin', array( $this, 'process_import' ) );
			add_action( 'admin_footer', array( $this, 'print_templates' ) );
		}

		/**
		 * Process skin import
		 * @return [type] [description]
		 */
		public function process_import() {

			$nonce_action = jet_engine()->dashboard->get_nonce_action();

			if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], $nonce_action ) ) {
				wp_send_json_error( __( 'Nonce validation failed', 'jet-engine' ) );
			}

			if ( ! current_user_can( 'import' ) ) {
				wp_send_json_error( __( 'You don\'t have permissions to do this', 'jet-engine' ) );
			}

			if ( empty( $_FILES['_skin'] ) ) {
				wp_send_json_error( __( 'File not passed', 'jet-engine' ) );
			}

			$file = $_FILES['_skin'];

			if ( 'application/json' !== $file['type'] ) {
				wp_send_json_error( __( 'Format not allowed', 'jet-engine' ) );
			}

			$content = file_get_contents( $file['tmp_name'] );
			$content = ltrim( $content, "\xEF\xBB\xBF" ); // maybe remove the bom string
			$content = json_decode( $content, true );

			if ( ! $content ) {
				wp_send_json_error( __( 'No data found in file', 'jet-engine' ) );
			}

			$post_types    = isset( $content['post_types'] ) ? $content['post_types'] : array();
			$taxonomies    = isset( $content['taxonomies'] ) ? $content['taxonomies'] : array();
			$listings      = isset( $content['listings'] ) ? $content['listings'] : array();
			$meta_boxes    = isset( $content['meta_boxes'] ) ? $content['meta_boxes'] : array();
			$relations     = isset( $content['relations'] ) ? $content['relations'] : array();
			$options_pages = isset( $content['options_pages'] ) ? $content['options_pages'] : array();
			$queries       = isset( $content['queries'] ) ? $content['queries'] : array();
			$glossaries    = isset( $content['glossaries'] ) ? $content['glossaries'] : array();
			$posts         = isset( $content['content']['posts'] ) ? $content['content']['posts'] : array();
			$terms         = isset( $content['content']['terms'] ) ? $content['content']['terms'] : array();
			$options       = isset( $content['content']['options'] ) ? $content['content']['options'] : array();

			$this->import_post_types( $post_types );
			$this->import_taxonomies( $taxonomies );
			$this->import_listings( $listings );
			$this->import_meta_boxes( $meta_boxes );
			$this->import_relations( $relations );
			$this->import_options_pages( $options_pages );
			$this->import_posts( $posts );
			$this->import_terms( $terms );
			$this->import_queries( $queries );
			$this->import_glossaries( $glossaries );
			$this->import_options( $options );

			do_action( 'jet-engine/dashboard/import/process', $content, $this );

			wp_send_json_success( array(
				'success' => $this->log,
				'errors'  => $this->errors,
			) );

		}

		/**
		 * Process queries import
		 *
		 * @param  [type] $queries [description]
		 * @return [type]          [description]
		 */
		public function import_queries( $queries = array() ) {

			foreach ( $queries as $query ) {

				unset( $query['id'] );

				$query['labels']      = maybe_unserialize( $query['labels'] );
				$query['args']        = maybe_unserialize( $query['args'] );
				$query['meta_fields'] = maybe_unserialize( $query['meta_fields'] );

				$id = \Jet_Engine\Query_Builder\Manager::instance()->data->update_item_in_db( $query );

				if ( $id ) {

					if ( empty( $this->log['queries'] ) ) {
						$this->log['queries'] = array( 'items' => array() );
					}

					$this->log['queries']['items'][] = $query['labels']['name'];
				}

			}

			if ( ! empty( $this->log['queries'] ) ) {
				$this->log['queries']['label'] = __( 'Queries', 'jet-engine' );
			}

		}

		/**
		 * Process queries import
		 *
		 * @param  [type] $queries [description]
		 * @return [type]          [description]
		 */
		public function import_glossaries( $glossaries = array() ) {

			foreach ( $glossaries as $glossary ) {

				if ( ! empty( $glossary['insert_file'] ) && $insert_file = $this->insert_glossary_file( $glossary['insert_file'] ) ) {
					$glossary['source_file'] = $insert_file;
				}

				jet_engine()->glossaries->data->set_request( $glossary );
				$done = jet_engine()->glossaries->data->create_item( false );

				$insert_file = false;

				if ( $done ) {
					if ( empty( $this->log['glossaries'] ) ) {
						$this->log['glossaries'] = array( 'items' => array() );
					}

					$this->log['glossaries']['items'][] = $glossary['name'];
				}

			}

			if ( ! empty( $this->log['glossaries'] ) ) {
				$this->log['glossaries']['label'] = __( 'Glossaries', 'jet-engine' );
			}

		}

		/**
		 * Insert file data
		 *
		 * @param  [type] $file_data [description]
		 * @return [type]            [description]
		 */
		public function insert_glossary_file( $file_data ) {

			$mime_type = ! empty( $file_data['mime_type'] ) ? $file_data['mime_type'] : false;
			$name      = ! empty( $file_data['name'] ) ? $file_data['name'] : false;
			$content   = ! empty( $file_data['content'] ) ? $file_data['content'] : false;

			if ( 'application/json' === $mime_type ) {
				$content = json_encode( $content );
			}

			$file_data = wp_upload_bits( $name, null, $content );

			if( $file_data['error'] ) {
				return false;
			}

			$id = wp_insert_attachment( array(
				'post_mime_type' => $mime_type,
			), $file_data['file'] );

			if ( ! $id || is_wp_error( $id ) ) {
				return false;
			}

			return array(
				'id'   => $id,
				'name' => $name,
				'url'  => $file_data['url'],
			);

		}

		/**
		 * Import meta boxes
		 *
		 * @param  [type] $meta_boxes [description]
		 * @return [type]             [description]
		 */
		public function import_meta_boxes( $meta_boxes ) {

			foreach ( $meta_boxes as $meta_box ) {

				if ( isset( $meta_box['id'] ) ) {
					unset( $meta_box['id'] );
				}

				jet_engine()->meta_boxes->data->update_item_in_db( $meta_box );
				jet_engine()->meta_boxes->data->reset_raw_cache();

				if ( empty( $this->log['meta_boxes'] ) ) {
					$this->log['meta_boxes'] = array( 'items' => array() );
				}

				$this->log['meta_boxes']['items'][] = $meta_box['args']['name'];
			}

			if ( ! empty( $this->log['meta_boxes'] ) ) {
				$this->log['meta_boxes']['label'] = __( 'Meta Boxes', 'jet-engine' );
			}

		}

		/**
		 * Import relations
		 *
		 * @param  [type] $relations [description]
		 * @return [type]             [description]
		 */
		public function import_relations( $relations ) {

			foreach ( $relations as $relation ) {

				if ( isset( $relation['id'] ) ) {
					unset( $relation['id'] );
				}

				jet_engine()->relations->data->update_item_in_db( $relation );
				jet_engine()->relations->data->reset_raw_cache();

				if ( empty( $this->log['relations'] ) ) {
					$this->log['relations'] = array( 'items' => array() );
				}

				$this->log['relations']['items'][] = $relation['name'];
			}

			if ( ! empty( $this->log['relations'] ) ) {
				$this->log['relations']['label'] = __( 'Relations', 'jet-engine' );
			}

		}

		/**
		 * Import options pages
		 *
		 * @param  array $options_pages
		 * @return void
		 */
		public function import_options_pages( $options_pages ) {

			foreach ( $options_pages as $page ) {

				if ( isset( $page['id'] ) ) {
					unset( $page['id'] );
				}

				$page['slug']        = jet_engine()->options_pages->data->sanitize_slug( $page['slug'] );
				$page['labels']      = maybe_unserialize( $page['labels'] );
				$page['args']        = maybe_unserialize( $page['args'] );
				$page['meta_fields'] = maybe_unserialize( $page['meta_fields'] );

				$id = jet_engine()->options_pages->data->update_item_in_db( $page );

				if ( $id ) {

					if ( empty( $this->log['options_pages'] ) ) {
						$this->log['options_pages'] = array( 'items' => array() );
					}

					$this->log['options_pages']['items'][] = $page['labels']['name'];
				}
			}

			if ( ! empty( $this->log['options_pages'] ) ) {
				$this->log['options_pages']['label'] = __( 'Options Pages', 'jet-engine' );
			}

		}

		/**
		 * Import post types
		 *
		 * @return [type] [description]
		 */
		public function import_post_types( $post_types = array() ) {

			foreach ( $post_types as $post_type ) {

				unset( $post_type['id'] );

				$post_type['slug']        = jet_engine()->cpt->data->sanitize_slug( $post_type['slug'] );
				$post_type['labels']      = maybe_unserialize( $post_type['labels'] );
				$post_type['args']        = maybe_unserialize( $post_type['args'] );
				$post_type['meta_fields'] = maybe_unserialize( $post_type['meta_fields'] );

				$id = jet_engine()->cpt->data->update_item_in_db( $post_type );

				if ( $id ) {

					if ( empty( $this->log['post_types'] ) ) {
						$this->log['post_types'] = array( 'items' => array() );
					}

					$this->log['post_types']['items'][] = $post_type['labels']['name'];
				}

			}

			if ( ! empty( $this->log['post_types'] ) ) {
				$this->log['post_types']['label'] = __( 'Post types', 'jet-engine' );
			}

		}

		/**
		 * Import post types
		 *
		 * @return [type] [description]
		 */
		public function import_taxonomies( $taxonomies = array() ) {

			foreach ( $taxonomies as $tax ) {

				unset( $tax['id'] );

				$tax['slug']        = jet_engine()->taxonomies->data->sanitize_slug( $tax['slug'] );
				$tax['object_type'] = maybe_unserialize( $tax['object_type'] );
				$tax['labels']      = maybe_unserialize( $tax['labels'] );
				$tax['args']        = maybe_unserialize( $tax['args'] );
				$tax['meta_fields'] = maybe_unserialize( $tax['meta_fields'] );

				$id = jet_engine()->taxonomies->data->update_item_in_db( $tax );

				if ( $id ) {

					if ( empty( $this->log['taxonomies'] ) ) {
						$this->log['taxonomies'] = array( 'items' => array() );
					}

					$this->log['taxonomies']['items'][] = $tax['labels']['name'];
				}

			}

			if ( ! empty( $this->log['taxonomies'] ) ) {
				$this->log['taxonomies']['label'] = __( 'Taxonomies', 'jet-engine' );
			}

		}

		/**
		 * Import post types
		 *
		 * @return [type] [description]
		 */
		public function import_listings( $listings = array() ) {

			if ( class_exists( 'Elementor\Plugin' ) ) {
				$documents     = Elementor\Plugin::instance()->documents;
				$doc_type      = $documents->get_document_type( jet_engine()->listings->get_id() );
				$type_meta_key = $doc_type::TYPE_META_KEY;
			} else {
				$type_meta_key = '_elementor_template_type';
			}

			foreach ( $listings as $listing ) {

				$listing_type = ! empty( $listing['type'] ) ? $listing['type'] : 'elementor';

				$postarr = array(
					'post_title'  => $listing['title'],
					'post_status' => 'publish',
					'post_type'   => jet_engine()->post_type->slug(),
					'post_name'   => $listing['slug'],
					'meta_input'  => array(
						'_listing_type' => $listing_type,
						'_elementor_page_settings' => $listing['settings'],
					),
				);

				if ( 'elementor' === $listing_type ) {
					$postarr['meta_input']['_elementor_edit_mode'] = 'builder';
					$postarr['meta_input'][ $type_meta_key ] = jet_engine()->listings->get_id();
					$postarr['meta_input'][ '_elementor_data' ] = wp_slash( $listing['content'] );
				}

				if ( 'blocks' === $listing_type ) {
					$postarr['post_content'] = $listing['content'];
				}

				$id = wp_insert_post( $postarr );

				if ( $id ) {

					if ( empty( $this->log['listings'] ) ) {
						$this->log['listings'] = array( 'items' => array() );
					}

					$this->log['listings']['items'][] = $listing['title'];
				}
			}

			if ( ! empty( $this->log['listings'] ) ) {
				$this->log['listings']['label'] = __( 'Listings', 'jet-engine' );
			}
		}

		/**
		 * Import post types
		 *
		 * @return [type] [description]
		 */
		public function import_posts( $posts = array() ) {

			foreach ( $posts as $post ) {

				$post['meta_input']  = $this->prepare_meta( $post['meta_input'] );
				$post['post_status'] = 'publish';

				$id = wp_insert_post( $post );

				if ( $id ) {

					if ( empty( $this->log['posts'] ) ) {
						$this->log['posts'] = array( 'items' => array() );
					}

					$this->log['posts']['items'][] = $post['post_title'];
				}
			}

			if ( ! empty( $this->log['posts'] ) ) {
				$this->log['posts']['label'] = __( 'Posts', 'jet-engine' );
			}
		}

		/**
		 * Import post types
		 *
		 * @return [type] [description]
		 */
		public function import_terms( $terms = array() ) {

			foreach ( $terms as $term ) {

				$term['meta_input'] = $this->prepare_meta( $term['meta_input'] );

				$id = wp_insert_term( $term['name'], $term['taxonomy'], array(
					'slug'        => $term['slug'],
					'description' => $term['description'],
				) );

				if ( $id ) {

					foreach ( $term['meta_input'] as $key => $value) {
						update_term_meta( $id, $key, $value );
					}

					if ( empty( $this->log['terms'] ) ) {
						$this->log['terms'] = array( 'items' => array() );
					}

					$this->log['terms']['items'][] = $term['name'];
				}
			}

			if ( ! empty( $this->log['terms'] ) ) {
				$this->log['terms']['label'] = __( 'Terms', 'jet-engine' );
			}

		}

		public function prepare_meta( $meta_input ) {

			$result = array();

			foreach ( $meta_input as $key => $value ) {

				if ( is_array( $value ) && isset( $value['media'] ) && isset( $value['url'] ) ) {

					$format = isset( $value['format'] ) && ! empty( $value['format'] ) ? $value['format'] : 'id';

					$result[ $key ] = $this->import_image( $value['url'], $format );
				} else {
					$result[ $key ] = $value;
				}
			}

			return $result;
		}

		/**
		 * Import image
		 *
		 * @return [type] [description]
		 */
		public function import_image( $url = null, $format = 'id' ) {

			if ( ! $url ) {
				return false;
			}

			// Extract the file name and extension from the url.
			$filename     = basename( $url );
			$file_content = wp_remote_retrieve_body( wp_safe_remote_get( $url ) );

			if ( empty( $file_content ) ) {
				return false;
			}

			$upload = wp_upload_bits(
				$filename,
				'',
				$file_content
			);

			$post = array(
				'post_title' => $filename,
				'guid'       => $upload['url'],
			);

			$info = wp_check_filetype( $upload['file'] );

			if ( $info ) {
				$post['post_mime_type'] = $info['type'];
			} else {
				// For now just return the origin attachment
				return false;
				// return new \WP_Error( 'attachment_processing_error', __( 'Invalid file type.', 'elementor' ) );
			}

			$post_id = wp_insert_attachment( $post, $upload['file'] );

			wp_update_attachment_metadata(
				$post_id,
				wp_generate_attachment_metadata( $post_id, $upload['file'] )
			);

			switch ( $format ) {

				case 'url':
					$result = wp_get_attachment_url( $post_id );
					break;

				case 'both':
					$result = array(
						'id'  => $post_id,
						'url' => wp_get_attachment_url( $post_id ),
					);
					break;

				default:
					$result = $post_id;
			}

			return $result;
		}


		/**
		 * Import options
		 *
		 * @param  array $options
		 * @return void
		 */
		public function import_options( $options = array() ) {
			foreach ( $options as $option ) {

				$result = update_option(  $option['slug'], $option['value'] );

				if ( $result ) {

					if ( empty( $this->log['options'] ) ) {
						$this->log['options'] = array( 'items' => array() );
					}

					$this->log['options']['items'][] = $option['name'];
				}
			}

			if ( ! empty( $this->log['options'] ) ) {
				$this->log['options']['label'] = __( 'Options', 'jet-engine' );
			}
		}

		/**
		 * Export component template
		 *
		 * @return void
		 */
		public function print_templates() {

			ob_start();
			include jet_engine()->get_template( 'admin/pages/dashboard/import.php' );
			$content = ob_get_clean();

			printf( '<script type="text/x-template" id="jet_engine_skin_import">%s</script>', $content );

		}

	}

}
