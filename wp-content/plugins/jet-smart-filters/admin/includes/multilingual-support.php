<?php
/**
 * Jet Smart Filters Admin Multilingual Support class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Admin_Multilingual_Support' ) ) {

	/**
	 * Define Jet_Smart_Filters_Admin_Multilingual_Support class
	 */
	class Jet_Smart_Filters_Admin_Multilingual_Support {

		public $is_Enabled = false;

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			global $sitepress;

			$this->is_Enabled = $sitepress && ( defined( 'ICL_SITEPRESS_VERSION' ) || defined( 'WPML_ST_VERSION' ) );

			if ( ! $this->is_Enabled ) {
				return;
			}

			$this->default_language = $sitepress->get_default_language();
			$this->current_language = $sitepress->get_current_language();
			$this->languages        = array();

			foreach ( $sitepress->get_active_languages() as $key => $data ) {
				$this->languages[$key] = array();

				$this->languages[$key]['native_name']  = $data['native_name'];
				$this->languages[$key]['english_name'] = $data['english_name'];
				$this->languages[$key]['flag_url']     = $sitepress->get_flag_url( $key );
			}

			// Add endpoints
			add_action( 'jet-smart-filters/rest/init-endpoints', function( $jsf_rest_api_manager ) {
				$jsf_rest_api_manager->register_endpoint( $this->filter_add_translation_endpoint() );
			} );

			// Insert multilingual to localized data
			add_filter( 'jet-smart-filters/admin/localized-data', array( $this, 'insert_localize_data' ), -999 );

			// On post insert
			add_action( 'wp_insert_post',  array( $this, 'on_post_insert' ), 10, 2 );
		}

		public function insert_localize_data( $localize_data ) {

			$multilingual_data = array(
				'default_language' => $this->current_language === 'all' ? $this->default_language : $this->current_language,
				'current_language' => $this->current_language,
				'languages'        => $this->languages
			);

			$localize_data['multilingual'] = $multilingual_data;

			return $localize_data;
		}

		public function on_post_insert( $post_id, $post ) {

			if ( $post->post_type !== 'jet-smart-filters' ) {
				return;
			}

			$language_code = get_post_meta( $post_id, '_language_code', true );
			
			if ( ! $language_code ) {
				return;
			}

			delete_post_meta( $post_id, '_language_code' );

			// Update the post language info
			do_action( 'wpml_set_element_language_details', array(
				'element_id'           => $post_id,
				'element_type'         => 'post_jet-smart-filters',
				'language_code'        => $language_code,
				'source_language_code' => null
			) );
		}

		public function get_SQL_filters_parts( $language = false ) {

			global $wpdb;

			$SQL_filters_parts = array(
				'select' => '',
				'join'   => '',
				'where'  => ''
			);

			if ( $this->is_Enabled ) {
				$SQL_filters_parts['join']   = "LEFT JOIN {$wpdb->prefix}icl_translations as translations ON {$wpdb->prefix}posts.ID = translations.element_id";
				$SQL_filters_parts['where']  = $language ? "AND translations.language_code = '$language'" : '';
			}

			return $SQL_filters_parts;
		}

		public function get_translations_count() {

			global $wpdb;

			$sql_count = "
			SELECT language_code, COUNT(posts.ID) AS count
			FROM {$wpdb->prefix}icl_translations as translations
				JOIN $wpdb->posts as posts ON translations.element_id=posts.ID AND translations.element_type = CONCAT('post_', posts.post_type)
					WHERE posts.post_type='jet-smart-filters' 
						AND posts.post_status NOT IN ( 'trash', 'auto-draft' )
						AND translations.language_code IN ('" . implode( "','", array_keys( $this->languages ) ) . "')
					GROUP BY language_code";
			$result_count = $wpdb->get_results( $sql_count, ARRAY_A );

			return array_combine(
				array_map( function( $language ) {
					return $language['language_code'];
				}, $result_count ),
				array_map( function( $language ) {
					return $language['count'];
				}, $result_count )
			);
		}


		// Get translations data by filters ids
		public function get_translations_data( $ids ) {

			if ( ! is_array( $ids ) ) {
				$ids = array( $ids );
			}

			global $wpdb;

			$sql_translations = "
			SELECT translations.element_id as id, MIN(translations.language_code) as language_key, GROUP_CONCAT(translations_relation.language_code) as translations_keys, GROUP_CONCAT(translations_relation.element_id) as translations_ids
				FROM {$wpdb->prefix}icl_translations as translations
					LEFT JOIN {$wpdb->prefix}icl_translations AS translations_relation ON (translations.trid = translations_relation.trid AND translations.element_id != translations_relation.element_id)
				WHERE translations.element_id IN ('" . implode( "','", $ids ) . "')
				GROUP BY translations.element_id";
			$result_translations = $wpdb->get_results( $sql_translations, ARRAY_A );

			$translations_data = array();
			foreach ( $result_translations as $translation ) {
				$translations_data[$translation['id']] = array(
					'language_key' => $translation['language_key']
				);

				// translations relation data
				if ( ! empty( $translation['translations_keys'] ) && ! empty( $translation['translations_ids'] ) ) {
					$translations_data[$translation['id']]['translations'] = array_combine(
						explode( ',', $translation['translations_keys'] ),
						explode( ',', $translation['translations_ids'] )
					);
				}
			}

			return $translations_data;
		}

		// Add translations data to filters list
		public function add_data_to_list( &$filters_list ) {

			$ids = array_map( function( $filter ) {
				return $filter['ID'];
			}, $filters_list );

			$translations_data = $this->get_translations_data( $ids );

			// add translations data to filters list
			foreach ( $filters_list as &$filter ) {
				$translation = $translations_data[$filter['ID']];

				if ( empty( $translation ) ) {
					continue;
				}

				$filter['language'] = $translation['language_key'];
				if ( ! empty( $translation['translations'] ) ) {
					$filter['translations'] = $translation['translations'];
				}
			}
		}

		// Add translations data to filter
		public function add_data_to_filter( &$filter_data ) {

			$filter_id         = $filter_data['ID'];
			$translations_data = $this->get_translations_data( $filter_id );

			if ( ! $translations_data[$filter_id] ) {
				return;
			}

			$translation = $translations_data[$filter_id];

			$filter_data['language'] = $translation['language_key'];
			if ( ! empty( $translation['translations'] ) ) {
				$filter_data['translations'] = $translation['translations'];
			}
		}

		// Endpoints
		public function filter_add_translation_endpoint() {

			return new class extends Jet_Smart_Filters\Endpoints\Base {

				public function get_name() {

					return 'filter-add-translation';
				}

				public function get_args() {
			
					return array(
						'id' => array(
							'required' => true,
						),
						'language' => array(
							'required' => true,
						),
					);
				}

				public function callback( $request ) {

					$args             = $request->get_params();
					$original_post_id = $args['id'];
					$language_code    = $args['language'];

					$original_post_data = jet_smart_filters()->services->filter->get( $original_post_id );
					$translated_post_id = jet_smart_filters()->services->filter->update( 'new', $original_post_data );

					// get the language info of the original post
					$original_post_language_info = apply_filters( 'wpml_element_language_details', null, array(
						'element_id'   => $original_post_id,
						'element_type' => 'post'
					) );

					// ML filter connect on insert
					do_action( 'wpml_set_element_language_details', array(
						'element_id'           => $translated_post_id,
						'element_type'         => 'post_jet-smart-filters',
						'trid'                 => $original_post_language_info->trid,
						'language_code'        => $language_code,
						'source_language_code' => $original_post_language_info->language_code
					) );

					return rest_ensure_response( $translated_post_id );
				}
			};
		}
	}
}
