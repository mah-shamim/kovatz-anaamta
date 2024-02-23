<?php
/**
 * WPML compatibility package
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_WPML_Package' ) ) {

	class Jet_Engine_WPML_Package {

		public function __construct() {

			if ( ! class_exists( 'SitePress' ) ) {
				return;
			}
			
			add_filter( 'wpml_elementor_widgets_to_translate',              array( $this, 'add_translatable_nodes' ) );
			add_filter( 'jet-engine/listings/frontend/rendered-listing-id', array( $this, 'set_translated_object' ) );
			add_filter( 'jet-engine/forms/render/form-id',                  array( $this, 'set_translated_object' ) );
			add_filter( 'jet-engine/profile-builder/template-id',           array( $this, 'set_translated_object' ) );
			add_filter( 'jet-engine/relations/get_related_posts',           array( $this, 'set_translated_related_posts' ) );
			add_filter( 'jet-engine/compatibility/translate/post',          array( $this, 'set_translated_object' ) );
			add_filter( 'jet-engine/compatibility/translate/term',          array( $this, 'set_translated_object' ), 10, 2 );

			// Translate CPT Name
			if ( jet_engine()->cpt ) {
				$cpt_items = jet_engine()->cpt->get_items();

				if ( ! empty( $cpt_items ) ) {
					foreach ( $cpt_items as $post_type ) {
						add_filter( "post_type_labels_{$post_type['slug']}", array( $this, 'translate_cpt_name' ) );
					}
				}
			}

			// Translate Admin Labels
			add_filter( 'jet-engine/compatibility/translate-string', array( $this, 'translate_admin_labels' ) );

			// Relations
			if ( jet_engine()->relations ) {
				$this->relations_hooks();
			}

			// Post meta conditions
			add_filter( 'jet-engine/meta-boxes/conditions/post-has-terms/check-terms', array( $this, 'set_translated_check_terms' ), 10, 2 );

			// Disable `suppress_filters` in the `get_posts` args.
			add_filter( 'jet-engine/compatibility/get-posts/args', array( $this, 'disable_suppress_filters' ) );

			// Data stores hooks
			add_filter( 'jet-engine/data-stores/store/data', array( $this, 'set_translated_store' ), 10, 2 );

			// Translated media and posts fields.
			add_filter( 'jet-engine/listing/data/get-post-meta', array( $this, 'set_translated_post_meta' ), 10, 3 );

			// Fixed the translated tax query on archive page at ajax( pagination, load more, lazy load ).
			// See: https://github.com/Crocoblock/issues-tracker/issues/2055
			if ( wpml_is_ajax() && class_exists( 'WPML_Display_As_Translated_Tax_Query' ) ) {
				global $sitepress, $wpml_term_translations;

				$translated_tax_query = new WPML_Display_As_Translated_Tax_Query( $sitepress, $wpml_term_translations );
				$translated_tax_query->add_hooks();
			}
		}

		public function relations_hooks() {

			add_filter( 'jet-engine/relations/types/posts/get-items', array( $this, 'filtered_relations_posts_items' ), 10, 2 );
			add_filter( 'jet-engine/relations/raw-args',              array( $this, 'translate_relations_labels' ) );

			$auto_sync_relations = apply_filters( 'jet-engine/compatibility/wpml/auto-sync-relations', true );

			if ( $auto_sync_relations ) {

				if ( is_admin() ) {
					add_action( 'icl_make_duplicate', array( $this, 'sync_relations_on_make_duplicate' ), 10, 4 );
				}

				if ( is_admin() || wpml_is_rest_request() ) {
					add_action( 'icl_pro_translation_completed', array( $this, 'sync_relations_on_translation_completed' ), 10, 3 );
				}

				add_action( 'jet-engine/relation/update/after', array( $this, 'sync_relations_on_update' ), 10, 4 );
				add_action( 'jet-engine/relation/delete/after', array( $this, 'sync_relations_on_delete' ), 10, 4 );

			}
		}

		public function sync_relations_on_make_duplicate( $original_id, $lang, $post_array, $translated_id ) {
			$this->sync_relations_items( $original_id, $translated_id, $lang );
		}

		public function sync_relations_on_translation_completed( $translated_id, $fields, $job ) {
			$original_id = ! empty( $job->original_doc_id ) ? $job->original_doc_id : false;
			$lang        = ! empty( $job->language_code ) ? $job->language_code : null;

			if ( empty( $original_id ) ) {
				return;
			}

			$this->sync_relations_items( $original_id, $translated_id, $lang );
		}

		public function sync_relations_items( $original_id, $translated_id, $lang ) {

			$post_type = get_post_type( $original_id );
			$rel_type  = jet_engine()->relations->types_helper->type_name_by_parts( 'posts', $post_type );

			$active_relations = jet_engine()->relations->get_active_relations();

			$relations = array_filter( $active_relations, function( $relation ) use ( $rel_type ) {

				if ( $rel_type === $relation->get_args( 'parent_object' ) ) {
					return true;
				}

				if ( $rel_type === $relation->get_args( 'child_object' ) ) {
					return true;
				}

				return false;
			} );

			if ( empty( $relations ) ) {
				return;
			}

			foreach ( $relations as $rel_id => $relation ) {

				$is_parent   = $rel_type === $relation->get_args( 'parent_object' );
				$meta_fields = $relation->get_args( 'meta_fields' );

				if ( $is_parent ) {
					$rel_items = $relation->get_children( $original_id, 'ids' );
					$obj_data  = jet_engine()->relations->types_helper->type_parts_by_name( $relation->get_args( 'child_object' ) );
					$is_single = $relation->is_single_child();
				} else {
					$rel_items = $relation->get_parents( $original_id, 'ids' );
					$obj_data  = jet_engine()->relations->types_helper->type_parts_by_name( $relation->get_args( 'parent_object' ) );
					$is_single = $relation->is_single_parent();
				}

				$rel_items    = array_reverse( $rel_items );
				$obj_type     = $obj_data[0];
				$obj_sub_type = $obj_data[1];

				foreach ( $rel_items as $rel_item ) {

					if ( in_array( $obj_type, array( 'posts', 'terms' ) ) ) {
						$new_rel_item = apply_filters( 'wpml_object_id', $rel_item, $obj_sub_type, true, $lang );
					} else {
						$new_rel_item = $rel_item;
					}

					if ( $is_single && $new_rel_item == $rel_item ) {
						continue;
					}

					if ( $is_parent ) {
						$relation->update( $translated_id, $new_rel_item );

						if ( empty( $meta_fields ) ) {
							continue;
						}

						$meta     = $relation->get_all_meta( $original_id, $rel_item );
						$new_meta = $relation->get_all_meta( $translated_id, $new_rel_item );
						$new_meta = array_merge( $meta, $new_meta );

						if ( ! empty( $new_meta ) ) {
							$relation->update_all_meta( $new_meta, $translated_id, $new_rel_item );
						}

					} else {
						$relation->update( $new_rel_item, $translated_id );

						if ( empty( $meta_fields ) ) {
							continue;
						}

						$meta     = $relation->get_all_meta( $rel_item, $original_id );
						$new_meta = $relation->get_all_meta( $new_rel_item, $translated_id );
						$new_meta = array_merge( $meta, $new_meta );

						if ( ! empty( $new_meta ) ) {
							$relation->update_all_meta( $meta, $new_rel_item, $translated_id );
						}
					}
				}
			}
		}

		public function sync_relations_on_update( $parent_id, $child_id, $item_id, $relation ) {

			if ( empty( $item_id ) ) {
				return;
			}

			$parent_obj_data = jet_engine()->relations->types_helper->type_parts_by_name( $relation->get_args( 'parent_object' ) );
			$child_obj_data  = jet_engine()->relations->types_helper->type_parts_by_name( $relation->get_args( 'child_object' ) );

			$support_types = array( 'posts', 'terms' );

			if ( ! in_array( $parent_obj_data[0], $support_types ) || ! in_array( $child_obj_data[0], $support_types ) ) {
				return;
			}

			if ( ! $this->is_item_translated( $parent_obj_data[1], $parent_obj_data[0] ) ||
				 ! $this->is_item_translated( $child_obj_data[1], $child_obj_data[0] )
			) {
				return;
			}

			$parent_translations = $this->get_item_translations( $parent_id, $parent_obj_data[1] );
			$child_translations  = $this->get_item_translations( $child_id, $child_obj_data[1] );

			remove_action( 'jet-engine/relation/update/after', array( $this, 'sync_relations_on_update' ) );

			foreach ( $parent_translations as $lang => $translation ) {

				if ( $translation->element_id == $parent_id ) {
					continue;
				}

				if ( ! isset( $child_translations[ $lang ] ) ) {
					continue;
				}

				$child_trans_id = $child_translations[ $lang ]->element_id;

				$relation->update( $translation->element_id, $child_trans_id );
			}

			add_action( 'jet-engine/relation/update/after', array( $this, 'sync_relations_on_update' ), 10, 4 );
		}

		public function sync_relations_on_delete( $parent_id, $child_id, $clear_meta, $relation ) {

			$parent_obj_data = jet_engine()->relations->types_helper->type_parts_by_name( $relation->get_args( 'parent_object' ) );
			$child_obj_data  = jet_engine()->relations->types_helper->type_parts_by_name( $relation->get_args( 'child_object' ) );

			$support_types = array( 'posts', 'terms' );

			if ( ! in_array( $parent_obj_data[0], $support_types ) || ! in_array( $child_obj_data[0], $support_types ) ) {
				return;
			}

			if ( ! $this->is_item_translated( $parent_obj_data[1], $parent_obj_data[0] ) ||
				 ! $this->is_item_translated( $child_obj_data[1], $child_obj_data[0] )
			) {
				return;
			}

			$parent_translations = $this->get_item_translations( $parent_id, $parent_obj_data[1] );
			$child_translations  = $this->get_item_translations( $child_id, $child_obj_data[1] );

			remove_action( 'jet-engine/relation/delete/after', array( $this, 'sync_relations_on_delete' ) );

			foreach ( $parent_translations as $lang => $translation ) {

				if ( $translation->element_id == $parent_id ) {
					continue;
				}

				if ( ! isset( $child_translations[ $lang ] ) ) {
					continue;
				}

				$rel_items      = $relation->get_children( $translation->element_id, 'ids' );
				$child_trans_id = $child_translations[ $lang ]->element_id;

				if ( ! in_array( $child_trans_id, $rel_items ) ) {
					continue;
				}

				$relation->delete_rows( $translation->element_id, $child_trans_id );
			}

			add_action( 'jet-engine/relation/delete/after', array( $this, 'sync_relations_on_delete' ), 10, 4 );
		}

		public function is_item_translated( $type = null, $obj_type = 'posts' ) {

			switch ( $obj_type ) {
				case 'posts':
					$is_translated = is_post_type_translated( $type );
					break;

				case 'terms':
					$is_translated = is_taxonomy_translated( $type );
					break;

				default:
					$is_translated = false;
			}

			return $is_translated;
		}

		public function get_item_translations( $id, $type ) {
			$elem_type = apply_filters( 'wpml_element_type', $type );
			$trid      = apply_filters( 'wpml_element_trid', false, $id, $elem_type );

			return apply_filters( 'wpml_get_element_translations', array(), $trid, $elem_type );
		}

		/**
		 * Set translated object ID to show
		 *
		 * @param int    $obj_id   Object ID.
		 * @param string $obj_type Object type: post type or taxonomy slug.
		 *
		 * @return int
		 */
		public function set_translated_object( $obj_id = null, $obj_type = null ) {

			global $sitepress;

			if ( empty( $obj_type ) ) {
				$obj_type = get_post_type( $obj_id );
			}

			$new_id = $sitepress->get_object_id( $obj_id, $obj_type );

			if ( $new_id ) {
				return $new_id;
			}

			return $obj_id;
		}

		/**
		 * Set translated related posts
		 *
		 * @param  mixed $ids
		 * @return mixed
		 */
		public function set_translated_related_posts( $ids ) {

			if ( is_array( $ids ) ) {
				foreach ( $ids as $id ) {
					$ids[ $id ] = apply_filters( 'wpml_object_id', $id, get_post_type( $id ), true );
				}
			} else {
				$ids = apply_filters( 'wpml_object_id', $ids, get_post_type( $ids ), true );
			}

			return $ids;
		}

		public function filtered_relations_posts_items( $items, $post_type ) {

			if ( ! is_post_type_translated( $post_type ) ) {
				return $items;
			}

			global $sitepress;

			$current_lang = $sitepress->get_current_language();

			$items = array_filter( $items, function ( $item ) use ( $sitepress, $post_type, $current_lang ) {
				$lang = $sitepress->get_language_for_element( $item['value'], 'post_' . $post_type );
				return $current_lang === $lang;
			} );

			return $items;
		}

		/**
		 * Add translation strings
		 */
		public function add_translatable_nodes( $nodes ) {

			$nodes['jet-listing-grid'] = array(
				'conditions' => array(
					'widgetType' => 'jet-listing-grid'
				),
				'fields'     => array(
					array(
						'field'       => 'not_found_message',
						'type'        => esc_html__( 'Listing Grid: Not found message', 'jet-engine' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$nodes['jet-listing-dynamic-field'] = array(
				'conditions' => array(
					'widgetType' => 'jet-listing-dynamic-field'
				),
				'fields'     => array(
					array(
						'field'       => 'date_format',
						'type'        => esc_html__( 'Field: Date format (if used)', 'jet-engine' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'num_dec_point',
						'type'        => esc_html__( 'Field: Separator for the decimal point (if used)', 'jet-engine' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'num_thousands_sep',
						'type'        => esc_html__( 'Field: Thousands separator (if used)', 'jet-engine' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'dynamic_field_format',
						'type'        => esc_html__( 'Field: Field format (if used)', 'jet-engine' ),
						'editor_type' => 'AREA',
					),
				),
			);

			$nodes['jet-listing-dynamic-link'] = array(
				'conditions' => array(
					'widgetType' => 'jet-listing-dynamic-link'
				),
				'fields'     => array(
					array(
						'field'       => 'link_label',
						'type'        => esc_html__( 'Link: Label (if used)', 'jet-engine' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'added_to_store_text',
						'type'        => esc_html__( 'Link: Added to store text (if used)', 'jet-engine' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$nodes['jet-listing-dynamic-meta'] = array(
				'conditions' => array(
					'widgetType' => 'jet-listing-dynamic-meta'
				),
				'fields'     => array(
					array(
						'field'       => 'prefix',
						'type'        => esc_html__( 'Meta: Prefix (if used)', 'jet-engine' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'suffix',
						'type'        => esc_html__( 'Meta: Suffix (if used)', 'jet-engine' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'zero_comments_format',
						'type'        => esc_html__( 'Meta: Zero Comments Format (if used)', 'jet-engine' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'one_comment_format',
						'type'        => esc_html__( 'Meta: One Comments Format (if used)', 'jet-engine' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'more_comments_format',
						'type'        => esc_html__( 'Meta: More Comments Format (if used)', 'jet-engine' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'date_format',
						'type'        => esc_html__( 'Meta: Date Format (if used)', 'jet-engine' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$nodes['jet-listing-dynamic-terms'] = array(
				'conditions' => array(
					'widgetType' => 'jet-listing-dynamic-terms'
				),
				'fields'     => array(
					array(
						'field'       => 'terms_prefix',
						'type'        => esc_html__( 'Terms: Prefix (if used)', 'jet-engine' ),
						'editor_type' => 'LINE',
					),
					array(
						'field'       => 'terms_suffix',
						'type'        => esc_html__( 'Terms: Suffix (if used)', 'jet-engine' ),
						'editor_type' => 'LINE',
					),
				),
			);

			$nodes['jet-listing-dynamic-repeater'] = array(
				'conditions' => array(
					'widgetType' => 'jet-listing-dynamic-repeater'
				),
				'fields'     => array(
					array(
						'field'       => 'dynamic_field_format',
						'type'        => esc_html__( 'Repeater: Field format (if used)', 'jet-engine' ),
						'editor_type' => 'AREA',
					),
				),
			);

			return $nodes;

		}

		/**
		 * Translate CPT Name
		 *
		 * @param  object $labels
		 * @return object
		 */
		public function translate_cpt_name( $labels ) {
			do_action( 'wpml_register_single_string', 'Jet Engine CPT Labels', "Jet Engine CPT Name ({$labels->name})", $labels->name );
			$labels->name = apply_filters( 'wpml_translate_single_string', $labels->name, 'Jet Engine CPT Labels', "Jet Engine CPT Name ({$labels->name})" );

			return $labels;
		}

		/**
		 * Translate Admin Labels
		 *
		 * @param  string $label
		 * @return string
		 */
		public function translate_admin_labels( $label ) {

			global $sitepress;

			$wpml_default_lang = apply_filters( 'wpml_default_language', null );

			$lang = method_exists( $sitepress, 'get_current_language' ) ? $sitepress->get_current_language() : null;

			$name = "Admin Label - {$label}";

			if ( 160 < strlen( $name ) ) {
				$name = jet_engine_trim_string( $name, 100, '' ) . '... - ' . md5( $label );
			}

			if ( $lang === $wpml_default_lang ) {
				do_action( 'wpml_register_single_string', 'Jet Engine Admin Labels', $name, $label );
			}

			$label = apply_filters( 'wpml_translate_single_string', $label, 'Jet Engine Admin Labels', $name, $lang );

			return $label;
		}

		public function translate_relations_labels( $args ) {

			if ( empty( $args['labels'] ) ) {
				return $args;
			}

			global $sitepress;

			$relation_name = ! empty( $args['labels']['name'] ) ? $args['labels']['name'] : esc_html__( 'Relation Label', 'jet-engine' );
			$lang          = method_exists( $sitepress, 'get_current_language' ) ? $sitepress->get_current_language() : null;

			foreach ( $args['labels'] as $key => $label ) {

				if ( 'name' === $key ) {
					continue;
				}

				if ( empty( $label ) ) {
					continue;
				}

				do_action( 'wpml_register_single_string', 'Jet Engine Relations Labels', $relation_name . ' - ' . $label, $label );
				$args['labels'][ $key ] = apply_filters( 'wpml_translate_single_string', $label, 'Jet Engine Relations Labels', $relation_name . ' - ' . $label, $lang );
			}

			return $args;
		}

		public function set_translated_check_terms( $terms, $tax ) {
			return array_map( function ( $term ) use ( $tax ) {
				return apply_filters( 'wpml_object_id', $term, $tax, true );
			}, $terms );
		}

		public function disable_suppress_filters( $args = array() ) {
			$args['suppress_filters'] = false;
			return $args;
		}

		public function set_translated_store( $store, $store_id ) {

			if ( empty( $store ) ) {
				return $store;
			}

			$store_instance = Jet_Engine\Modules\Data_Stores\Module::instance()->stores->get_store( $store_id );

			if ( $store_instance->is_user_store() || $store_instance->get_arg( 'is_cct' ) ) {
				return $store;
			}

			$store = array_map( function( $item ) {

				if ( ! is_array( $item ) ) {
					$item = apply_filters( 'wpml_object_id', $item, get_post_type( $item ), true );
				}

				return $item;
			}, $store );

			return $store;
		}

		public function set_translated_post_meta( $value, $key, $post_id ) {

			if ( empty( $value ) ) {
				return $value;
			}

			$post_type = get_post_type( $post_id );

			if ( ! is_post_type_translated( $post_type ) ) {
				return $value;
			}

			$post_type_fields = jet_engine()->meta_boxes->get_meta_fields_for_object( $post_type );

			if ( empty( $post_type_fields ) ) {
				return $value;
			}

			$field_args = null;

			foreach ( $post_type_fields as $field ) {
				if ( ! empty( $field['name'] ) && $key === $field['name'] ) {
					$field_args = $field;
					break;
				}
			}

			if ( empty( $field_args ) ) {
				return $value;
			}

			$supported_field_types = array( 'media', 'posts' );

			if ( empty( $field_args['type'] ) || ! in_array( $field_args['type'], $supported_field_types ) ) {
				return $value;
			}

			$tm_settings = wpml_load_core_tm()->get_settings();

			if ( empty( $tm_settings ) ) {
				return $value;
			}

			if ( ! isset( $tm_settings['custom_fields_translation'] ) || ! isset( $tm_settings['custom_fields_translation'][ $key ] ) ) {
				return $value;
			}

			if ( WPML_IGNORE_CUSTOM_FIELD === $tm_settings['custom_fields_translation'][ $key ] ) {
				return $value;
			}

			switch ( $field_args['type'] ) {

				case 'media':

					if ( is_numeric( $value ) ) {

						$value = apply_filters( 'wpml_object_id', $value, 'attachment', true );

					} elseif ( is_array( $value ) && isset( $value['id'] ) ) {

						$value['id'] = apply_filters( 'wpml_object_id', $value['id'], 'attachment', true );

					} elseif ( is_array( $value ) ) {

						$value = array_map( function( $item ) {

							if ( is_numeric( $item ) ) {

								return apply_filters( 'wpml_object_id', $item, 'attachment', true );

							} elseif ( is_array( $item ) && isset( $item['id'] )  ) {

								$item['id'] = apply_filters( 'wpml_object_id', $item['id'], 'attachment', true );
								return $item;
							}

							return $item;
						}, $value );
					}

					break;

				case 'posts':

					if ( is_array( $value ) ) {

						$value = array_map( function( $item ) {
							return apply_filters( 'wpml_object_id', $item, get_post_type( $item ), true );
						}, $value );

					} else {
						$value = apply_filters( 'wpml_object_id', $value, get_post_type( $value ), true );
					}

					break;
			}

			return $value;
		}

	}

}

new Jet_Engine_WPML_Package();
