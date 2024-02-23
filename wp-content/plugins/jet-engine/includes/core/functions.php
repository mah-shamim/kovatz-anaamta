<?php
/**
 * Misc functions
 */

/**
 * Includes Jet_Engine_Img_Gallery class if it was not included before
 *
 * @return void
 */
function jet_engine_get_gallery() {
	if ( ! class_exists( 'Jet_Engine_Img_Gallery' ) ) {
		require_once jet_engine()->plugin_path( 'includes/classes/gallery.php' );
	}
}

/**
 * Callback for filter field option
 *
 * @return void
 */
function jet_engine_img_gallery_slider( $value = null, $args = array() ) {

	if ( is_array( $value ) ) {

		$value = array_values( $value );

		if ( ! is_array( $value[0] ) ) {
			$value = implode( ',', $value );
		}

	}

	return jet_engine()->listings->filters->img_gallery_slider( $value, $args );
}

/**
 * Callback for filter field option
 *
 * @return void
 */
function jet_engine_img_gallery_grid( $value = null, $args = array() ) {

	if ( is_array( $value ) ) {

		$value = array_values( $value );

		if ( ! is_array( $value[0] ) ) {
			$value = implode( ',', $value );
		}

	}

	return jet_engine()->listings->filters->img_gallery_grid( $value, $args );
}

/**
 * Returns image size array in slug => name format
 *
 * @return  array
 */
function jet_engine_get_image_sizes() {

	global $_wp_additional_image_sizes;

	$sizes  = get_intermediate_image_sizes();
	$result = array();

	foreach ( $sizes as $size ) {
		if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
			$result[ $size ] = ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) );
		} else {
			$result[ $size ] = sprintf(
				'%1$s (%2$sx%3$s)',
				ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
				$_wp_additional_image_sizes[ $size ]['width'],
				$_wp_additional_image_sizes[ $size ]['height']
			);
		}
	}

	return array_merge( array( 'full' => esc_html__( 'Full', 'jet-engine' ), ), $result );
}

/**
 * Sanitize WYSIWYG field
 *
 * @return string
 */
function jet_engine_sanitize_wysiwyg( $input ) {
	$input = wpautop( $input );
	return wp_kses_post( $input );
}

/**
 * Sanitize Textarea field
 *
 * @return string
 */
function jet_engine_sanitize_textarea( $input ) {
	return wp_check_invalid_utf8( $input, true );
}

/**
 * Sanitize Media field
 *
 * @param  string $input JSON string
 * @return array
 */
function jet_engine_sanitize_media_json( $input ) {

	if ( is_array( $input ) ) {
		return $input;
	}

	return json_decode( wp_unslash( $input ), true );
}

/**
 * Return multiselect values as string with passed delimiter
 *
 * @param  [type] $value     [description]
 * @param  [type] $delimiter [description]
 * @return [type]            [description]
 */
function jet_engine_render_multiselect( $value = null, $delimiter = ', ' ) {

	if ( empty( $value ) ) {
		return '';
	}

	if ( is_object( $value ) ) {
		$value = get_object_vars( $value );
	}

	if ( ! $value || ! is_array( $value ) ) {
		return $value;
	}

	return wp_kses_post( implode( $delimiter, $value ) );

}

/**
 * Returns prepared checkbox values list
 *
 * @param  [type] $value [description]
 * @return [type]        [description]
 */
function jet_engine_get_prepared_check_values( $value = null ) {

	$result = array();

	if ( in_array( 'true', $value ) || in_array( 'false', $value ) ) {
		foreach ( $value as $key => $val ) {
			if ( 'true' === $val ) {
				$result[] = $key;
			}
		}
	} else {
		$result = $value;
	}

	return $result;
}

/**
 * Return checkbox values as string with passed delimiter
 *
 * @param  [type] $value     [description]
 * @param  [type] $delimiter [description]
 * @return [type]            [description]
 */
function jet_engine_render_checkbox_values( $value = null, $delimiter = ', ' ) {

	if ( empty( $value ) ) {
		return '';
	}

	if ( is_object( $value ) ) {
		$value = get_object_vars( $value );
	}

	if ( ! $value || ! is_array( $value ) ) {
		return $value;
	}

	$result = jet_engine_get_prepared_check_values( $value );

	return wp_kses_post( implode( $delimiter, $result ) );

}

/**
 * Return checkbox values as checkd list
 *
 * @param  [type] $value     [description]
 * @param  [type] $delimiter [description]
 * @return [type]            [description]
 */
function jet_engine_render_checklist( $value = null, $icon = null, $columns = 1, $divider = false, $glossary_id = false ) {

	if ( empty( $value ) ) {
		return '';
	}

	if ( is_object( $value ) ) {
		$value = get_object_vars( $value );
	}

	if ( ! $value || ! is_array( $value ) ) {
		return $value;
	}

	$result = jet_engine_get_prepared_check_values( $value );

	if ( empty( $result ) ) {
		return '';
	}

	ob_start();

	$classes = array(
		'jet-check-list',
		'jet-check-list--columns-' . $columns,
	);

	if ( $divider ) {
		$classes[] = 'jet-check-list--has-divider';
	}

	if ( $glossary_id ) {
		$result = array_map( function ( $result_item ) use ( $glossary_id ) {
			return jet_engine_label_by_glossary( $result_item, $glossary_id );
		}, $result );
	}

	echo '<div class="' . implode( ' ', $classes ) . '">';

	foreach ( $result as $item ) {
		printf( '<div class="jet-check-list__item">%2$s<div class="jet-check-list__item-content">%1$s</div></div>', $item, $icon );
	}

	echo '</div>';

	return ob_get_clean();

}

/**
 * Render filtered switcher result
 *
 * @param  [type] $value       [description]
 * @param  [type] $true_label  [description]
 * @param  [type] $false_label [description]
 * @return [type]              [description]
 */
function jet_engine_render_switcher( $value = null, $true_label = null, $false_label = null ) {

	$value = filter_var( $value, FILTER_VALIDATE_BOOLEAN );

	if ( $value ) {
		return $true_label;
	} else {
		return $false_label;
	}

}

/**
 * Return checkbox values as string with passed delimiter
 *
 * @param  [type] $value     [description]
 * @param  [type] $delimiter [description]
 * @return [type]            [description]
 */
function jet_engine_render_acf_checkbox_values( $value = null, $delimiter = ', ' ) {

	if ( empty( $value ) ) {
		return '';
	}

	if ( is_object( $value ) ) {
		$value = get_object_vars( $value );
	}

	if ( ! $value || ! is_array( $value ) ) {
		return $value;
	}

	return wp_kses_post( implode( $delimiter, $value ) );

}

/**
 * Return post titles from post IDs array as string with passed delimiter
 *
 * @param  [type] $value     [description]
 * @param  [type] $delimiter [description]
 * @return [type]            [description]
 */
function jet_engine_render_post_titles( $value = null, $delimiter = ', ' ) {

	if ( empty( $value ) ) {
		return '';
	}

	if ( is_object( $value ) ) {
		$value = get_object_vars( $value );
	}

	if ( ! $value ) {
		return $value;
	}

	if ( ! is_array( $value ) ) {
		$value = array( $value );
	}

	return wp_kses_post( implode( $delimiter, array_map( 'get_the_title', $value ) ) );

}

/**
 * Returns link to post by ID
 *
 * @return [type] [description]
 */
function jet_get_pretty_post_link( $value ) {

	if ( empty( $value ) ) {
		return;
	}

	$result = '';

	if ( is_array( $value ) ) {

		$delimiter = '';

		foreach ( $value as $post_id ) {

			$result .= sprintf(
				'%3$s<a href="%1$s">%2$s</a>',
				get_permalink( $post_id ),
				get_the_title( $post_id ),
				$delimiter
			);

			$delimiter = ', ';

		}

	} else {
		$post_id = $value;
		$result  = sprintf( '<a href="%1$s">%2$s</a>', get_permalink( $post_id ), get_the_title( $post_id ) );
	}

	return $result;

}

/**
 * Returns term name by ID
 *
 * @return [type] [description]
 */
function jet_get_term_name( $value ) {

	if ( empty( $value ) ) {
		return;
	}

	$result = '';

	if ( is_array( $value ) ) {

		$delimiter = '';

		foreach ( $value as $term_id ) {

			$term = get_term( $term_id );

			if ( $term ) {
				$result .= sprintf(
					'%2$s%1$s',
					$term->name,
					$delimiter
				);

				$delimiter = ', ';

			}

		}

	} else {

		$term_id = $value;
		$term    = get_term( $term_id );

		if ( $term ) {
			$result = $term->name;
		}

	}

	return $result;

}

/**
 * Returns link to term by ID
 *
 * @return [type] [description]
 */
function jet_get_pretty_term_link( $value ) {

	if ( empty( $value ) ) {
		return;
	}

	$result = '';

	if ( is_array( $value ) ) {

		$delimiter = '';

		foreach ( $value as $term_id ) {

			$term = get_term( $term_id );

			if ( $term ) {
				$result .= sprintf(
					'%3$s<a href="%1$s">%2$s</a>',
					get_term_link( $term_id ),
					$term->name,
					$delimiter
				);

				$delimiter = ', ';

			}

		}

	} else {

		$term_id = $value;
		$term    = get_term( $term_id );

		if ( $term ) {
			$result = sprintf( '<a href="%1$s">%2$s</a>', get_term_link( $term_id ), $term->name );
		}

	}

	return $result;

}

/**
 * Return icon HTML for icon, set in JetEngine iconpicker
 *
 * @param  string $value Icon class
 * @return string
 */
function jet_engine_icon_html( $value = null ) {

	$format = apply_filters(
		'jet-engine/listings/icon-html-format',
		'<i class="fa %s"></i>',
		$value
	);

	return sprintf( $format, $value );

}

/**
 * Returns QR code for meta value
 *
 * @return string
 */
function jet_engine_get_qr_code( $meta_value = null, $size = 150 ) {

	$qr_code = jet_engine()->modules->get_module( 'qr-code' );
	return $qr_code->get_qr_code( $meta_value, $size );

}

/**
 * Render related posts array as HTML list
 *
 * @param  array  $related_posts [description]
 * @return [type]                [description]
 */
function jet_related_posts_list( $related_posts = array(), $tag = 'ul', $is_single = false, $is_linked = true, $delimiter = '' ) {

	if ( ! is_array( $related_posts ) ) {
		$related_posts = array_filter( array( absint( $related_posts ) ) );
	}

	if ( empty( $related_posts ) ) {
		return;
	}

	$tags = jet_engine_get_tags_tree( $tag );

	if ( $is_single ) {
		$related_posts = array( $related_posts[0] );
	}

	ob_start();

	printf( '<%s>', $tags['parent_tag'] );

	$count = count( $related_posts );
	$i     = 1;

	foreach ( $related_posts as $post_id ) {

		if ( $i === $count ) {
			$delimiter = '';
		}

		if ( $is_linked ) {

			printf(
				'<%1$s><a href="%3$s">%2$s</a>%4$s</%1$s>',
				$tags['child_tag'],
				get_the_title( $post_id ),
				get_permalink( $post_id ),
				$delimiter
			);

		} else {

			printf(
				'<%1$s>%2$s%3$s</%1$s>',
				$tags['child_tag'],
				get_the_title( $post_id ),
				$delimiter
			);

		}

		$i++;
	}

	printf( '</%s>', $tags['parent_tag'] );

	return ob_get_clean();

}

/**
 * Returns tags tree
 *
 * @param  array  $related_posts [description]
 * @return [type]                [description]
 */
function jet_engine_get_tags_tree( $tag = 'ul' ) {

	$result = array();

	switch ( $tag ) {
		case 'ol':
			$result['parent_tag'] = 'ol';
			$result['child_tag']  = 'li';
			break;

		case 'div':
			$result['parent_tag'] = 'div';
			$result['child_tag']  = 'span';
			break;

		default:
			$result['parent_tag'] = 'ul';
			$result['child_tag']  = 'li';
			break;
	}

	return $result;
}

/**
 * Render related posts array as HTML list
 *
 * @param  array  $related_posts [description]
 * @return [type]                [description]
 */
function jet_related_items_list( $items = array(), $tag = 'ul', $is_single = false, $is_linked = true, $delimiter = '', $prop = null ) {

	if ( ! is_array( $items ) ) {
		$items = array_filter( array( absint( $items ) ) );
	}

	if ( empty( $items ) ) {
		return;
	}

	$tags = jet_engine_get_tags_tree( $tag );

	if ( $is_single ) {
		$items = array( $items[0] );
	}

	ob_start();

	printf( '<%s>', $tags['parent_tag'] );

	$count  = count( $items );
	$i      = 1;
	$rel_id = str_replace( array( 'jet_engine_related_items_parents_', 'jet_engine_related_items_children_' ), '', $prop );

	if ( false !== strpos( $prop, 'children_' ) ) {
		$get = 'children';
	} elseif ( false !== strpos( $prop, 'parents_' ) ) {
		$get = 'parents';
	}

	$relation = jet_engine()->relations->get_active_relations( $rel_id );

	if ( ! $relation || ! is_object( $relation ) ) {
		return;
	}

	switch ( $get ) {
		case 'parents':
			$type = $relation->get_args( 'parent_object' );
			break;

		default:
			$type = $relation->get_args( 'child_object' );
			break;
	}

	foreach ( $items as $item_id ) {

		if ( $i === $count ) {
			$delimiter = '';
		}

		if ( $is_linked ) {

			printf(
				'<%1$s><a href="%3$s">%2$s</a>%4$s</%1$s>',
				$tags['child_tag'],
				jet_engine()->relations->types_helper->get_type_item_title( $type, $item_id, $relation ),
				jet_engine()->relations->types_helper->get_type_item_view_url( $type, $item_id, $relation ),
				$delimiter
			);

		} else {

			printf(
				'<%1$s>%2$s%3$s</%1$s>',
				$tags['child_tag'],
				jet_engine()->relations->types_helper->get_type_item_title( $type, $item_id, $relation ),
				$delimiter
			);

		}

		$i++;
	}

	printf( '</%s>', $tags['parent_tag'] );

	return ob_get_clean();

}

/**
 * Returns formatted date from post meta by post id, field and format string
 *
 * @param  integer $post_id [description]
 * @param  string  $field   [description]
 * @param  string  $format  [description]
 * @return [type]           [description]
 */
function jet_engine_custom_cb_date( $post_id = 0, $field = '', $format = '' ) {

	$value = get_post_meta( $post_id, $field, true );

	if ( $value ) {
		return date_i18n( $format, $value );
	} else {
		return null;
	}

}

/**
 * Returns post link from post meta by post id, field and format string
 *
 * @param  integer $post_id [description]
 * @param  string  $field   [description]
 * @param  string  $format  [description]
 * @return [type]           [description]
 */
function jet_engine_custom_cb_pretty_post_link( $post_id = 0, $field = '' ) {

	$value = get_post_meta( $post_id, $field, true );

	if ( $value ) {
		return jet_get_pretty_post_link( $value );
	} else {
		return null;
	}

}

/**
 * Returns menu order value from current post
 *
 * @param  integer $post_id [description]
 * @return [type]           [description]
 */
function jet_engine_custom_cb_menu_order( $post_id = 0 ) {

	$post = get_post( $post_id );

	if ( ! $post || is_wp_error( $post ) ) {
		return null;
	} else {
		return $post->menu_order;
	}

}

/**
 * Returns post link from post meta by post id, field and format string
 *
 * @param  integer $post_id [description]
 * @param  string  $field   [description]
 * @param  string  $format  [description]
 * @return [type]           [description]
 */
function jet_engine_custom_cb_related_posts( $post_id = 0, $field = '' ) {

	$value = get_post_meta( $post_id, $field, false );

	if ( $value ) {
		return jet_get_pretty_post_link( $value );
	} else {
		return null;
	}

}

/**
 * Returns link
 * @param  integer $item_id [description]
 * @param  string  $rel_id  [description]
 * @return [type]           [description]
 */
function jet_engine_custom_cb_related_items( $item_id = 0, $rel_id = '' ) {

	if ( ! $rel_id ) {
		return;
	}

	$relation = jet_engine()->relations->get_active_relations( $rel_id );

	if ( ! $relation ) {
		return;
	}

	$post_type   = get_post_type( $item_id );
	$for_object  = jet_engine()->relations->types_helper->type_name_by_parts( 'posts', $post_type );
	$related_ids = array();

	if ( $relation->get_args( 'parent_object' ) === $for_object ) {
		$from_object = $relation->get_args( 'child_object' );
		$related_ids = $relation->get_children( $item_id, 'ids' );
	} else {
		$from_object = $relation->get_args( 'parent_object' );
		$related_ids = $relation->get_parents( $item_id, 'ids' );
	}

	return jet_engine()->relations->types_helper->verbose_related_objects( $from_object, $related_ids, $relation );

}

/**
 * Returns rendered switcher value from post meta by post id, field and format string
 *
 * @param  integer $post_id [description]
 * @param  string  $field   [description]
 * @param  string  $format  [description]
 * @return [type]           [description]
 */
function jet_engine_custom_cb_render_switcher( $post_id = 0, $field = '', $true_label = '', $false_label = '' ) {
	$value = get_post_meta( $post_id, $field, true );
	return jet_engine_render_switcher( $value, $true_label, $false_label );
}

/**
 * Returns rendered checkbox values from post meta by post id, field and format string
 *
 * @param  integer $post_id [description]
 * @param  string  $field   [description]
 * @param  string  $format  [description]
 * @return [type]           [description]
 */
function jet_engine_custom_cb_render_checkbox( $post_id = 0, $field = '', $delimiter = ', ' ) {

	$value = get_post_meta( $post_id, $field, true );

	if ( $value ) {

		if ( is_object( $value ) ) {
			$value = get_object_vars( $value );
		}

		if ( ! $value || ! is_array( $value ) ) {
			return $value;
		}

		$value = jet_engine_get_prepared_check_values( $value );

		return jet_engine_get_field_options_labels( $value, get_post_type( $post_id ), $field, $delimiter );
	} else {
		return null;
	}

}

/**
 * Render image tag by post id, meta field and pased size
 *
 * @param  integer $post_id [description]
 * @param  string  $field   [description]
 * @param  integer $size    [description]
 * @return [type]           [description]
 */
function jet_engine_custom_cb_render_image( $post_id = 0, $field = 'thumbnail', $size = 100 ) {

	$size = absint( $size );

	if ( ! $size ) {
		$size = 100;
	}

	if ( 'thumbnail' === $field ) {
		if ( has_post_thumbnail( $post_id ) ) {
			return get_the_post_thumbnail( $post_id, array( $size, $size ) );
		} else {
			return null;
		}
	} else {
		$value    = get_post_meta( $post_id, $field, true );
		$img_data = \Jet_Engine_Tools::get_attachment_image_data_array( $value, 'id' );
		$img_id   = ! empty( $img_data['id'] ) ? $img_data['id'] : false;

		if ( $img_id ) {
			return wp_get_attachment_image( $img_id, array( $size, $size ) );
		} else {
			return null;
		}

	}

}

/**
 * Render gallery values from post meta by post id, meta field and passed size
 *
 * @param  integer $post_id [description]
 * @param  string  $field   [description]
 * @param  integer $size    [description]
 * @return string
 */
function jet_engine_custom_cb_render_gallery( $post_id = 0, $field = '', $size = 100 ) {

	if ( empty( $field ) ) {
		return null;
	}

	$values = get_post_meta( $post_id, $field, true );

	return jet_engine_render_simple_gallery( $values, $size );
}

/**
 * Render grid gallery
 *
 * @param  [type]  $values [description]
 * @param  integer $size   [description]
 * @return [type]          [description]
 */
function jet_engine_render_simple_gallery( $values = null, $size = 100 ) {

	if ( empty( $values ) ) {
		return null;
	}

	$size = absint( $size );

	if ( ! $size ) {
		$size = 100;
	}

	if ( ! is_array( $values ) ) {
		$values = explode( ',', $values );
	}

	$values = array_map( function( $item ) {
		$img_data = \Jet_Engine_Tools::get_attachment_image_data_array( $item, 'id' );
		return ! empty( $img_data['id'] ) ? $img_data['id'] : false;
	}, $values );

	$images = '';

	foreach ( $values as $img_id ) {

		if ( empty( $img_id ) ) {
			continue;
		}

		$images .= wp_get_attachment_image( $img_id, array( $size, $size ), false );
	}

	return sprintf( '<div class="jet-engine-simple-gallery" style="display:flex;gap:5px;overflow:auto">%s</div>', $images );
}

/**
 * Return post thumbnail by give post ID.
 *
 * @param  array $values
 * @return int|void
 */
function jet_engine_post_thumbnail( $post_id = null, $image_size = 'full', $add_permalink = false ) {

	if ( ! empty( $post_id ) && is_array( $post_id ) ) {
		$post_id = array_values( $post_id );
		$post_id = $post_id[0];
	}

	$post_id = absint( $post_id );

	if ( ! $post_id || ! has_post_thumbnail( $post_id ) ) {
		return;
	}

	$thumb = get_the_post_thumbnail( $post_id, $image_size );

	if ( $add_permalink ) {
		return sprintf( '<a href="%1$s">%2$s</a>', get_permalink( $post_id ), $thumb );
	} else {
		return $thumb;
	}

}

/**
 * Render field values count.
 *
 * @param  array $values
 * @return int|void
 */
function jet_engine_render_field_values_count( $values = array() ) {

	if ( empty( $values ) ) {
		return;
	}

	if ( ! is_array( $values ) ) {
		if ( is_object( $values ) ) {
			return 1;
		} else {
			$values = explode( ',', $values );
		}
	}

	return count( $values );
}

/**
 * Returns rendered select value from post meta by post id, field string
 *
 * @param  integer $post_id
 * @param  string  $field
 * @param  string  $delimeter
 * @return mixed
 */
function jet_engine_custom_cb_render_select( $post_id = 0, $field = '', $delimeter = ', ' ) {

	$value = get_post_meta( $post_id, $field, true );

	if ( ! $value ) {
		return null;
	}

	return jet_engine_get_field_options_labels( $value, get_post_type( $post_id ), $field, $delimeter );
}

/**
 * Returns rendered field options labels.
 *
 * @param mixed  $value
 * @param string $obj_type
 * @param string $field
 * @param string $delimiter
 *
 * @return string
 */
function jet_engine_get_field_options_labels( $value = null, $obj_type = 'post', $field = '', $delimiter = ', ' ) {

	$all_fields  = jet_engine()->meta_boxes->get_registered_fields();
	$found_field = null;

	if ( ! isset( $all_fields[ $obj_type ] ) ) {
		return is_array( $value ) ? wp_kses_post( implode( $delimiter, $value ) ) : wp_kses_post( $value );
	}

	foreach ( $all_fields[ $obj_type ] as $field_data ) {
		if ( ! empty( $field_data['name'] ) && $field === $field_data['name'] ) {
			$found_field = $field_data;
		}
	}

	$post_meta        = new \Jet_Engine_CPT_Meta();
	$prepared_options = $post_meta->filter_options_list( [], $found_field );

	if ( empty( $prepared_options ) ) {
		return is_array( $value ) ? wp_kses_post( implode( $delimiter, $value ) ) : wp_kses_post( $value );
	}

	$value  = is_array( $value ) ? $value : array( $value );
	$result = [];

	if ( is_array( $prepared_options ) ) {
		$all_values = array_column( $prepared_options, 'key' );
		$all_labels = array_column( $prepared_options, 'value' );
		$prepared_options = array_combine( $all_values, $all_labels );
	} elseif ( is_callable( $prepared_options ) ) {
		$prepared_options = call_user_func( $prepared_options );
	}

	foreach ( $value as $single_value ) {
		if ( isset( $prepared_options[ $single_value ] ) ) {
			$result[] = is_array( $prepared_options[ $single_value ] ) 
						? $prepared_options[ $single_value ]['label']
						: $prepared_options[ $single_value ];
		}
	}

	return wp_kses_post( implode( $delimiter, $result ) );
}

/**
 * Return term title from ID
 *
 * @param mixed $id Term ID.
 *
 * @return string
 */
function jet_engine_get_term_title( $id = null ) {
	$term = get_term( $id );

	if ( is_wp_error( $term ) ) {
		return '';
	}

	return $term->name;
}

/**
 * Return term titles from terms IDs array as a string with passed delimiter
 *
 * @param array  $ids
 * @param string $delimiter
 *
 * @return mixed
 */
function jet_engine_get_term_titles( $ids = array(), $delimiter = ', ' ) {

	if ( ! $ids || ! is_array( $ids ) ) {
		return $ids;
	}

	$titles = array_map( 'jet_engine_get_term_title', $ids );
	$titles = array_filter( $titles );

	return wp_kses_post( implode( $delimiter, $titles ) );
}

/**
 * Get child element from array or object by path to the child.
 * Path shouuld be in level-1/level-2/child-key format
 *
 * @param  array|object $stack [description]
 * @param  string       $path  [description]
 * @return mixed
 */
function jet_engine_get_child( $stack = array(), $path = false ) {
	return jet_engine_recursive_get_child( $stack, $path );
}

/**
 * Get child element from array or object by path to the child and current nesting level index.
 * Path shouuld be in level-1/level-2/child-key format
 *
 * @param  array|object $stack [description]
 * @param  string       $path  [description]
 * @return mixed
 */
function jet_engine_recursive_get_child( $stack = array(), $path = false, $level_index = false ) {

	if ( ! $stack ) {
		return false;
	}

	$path          = trim( $path, '/' );
	$exploded_path = explode( '/', $path );

	if ( false === $level_index ) {
		$level_index = 0;
	}

	if ( is_object( $stack ) ) {
		$stack = get_object_vars( $stack );
	}

	$key = isset( $exploded_path[ $level_index ] ) ? $exploded_path[ $level_index ] : false;

	if ( false === $key ) {
		return false;
	}

	if ( $level_index === ( count( $exploded_path ) - 1 ) ) {
		return isset( $stack[ $key ] ) ? $stack[ $key ] : false;
	} else {
		$level_index++;
		return jet_engine_recursive_get_child( $stack[ $key ], $path, $level_index );
	}

}

/**
 * Trim string by given chars numner
 *
 * @param  [type]  $string [description]
 * @param  integer $chars  [description]
 * @param  string  $suffix [description]
 * @return [type]          [description]
 */
function jet_engine_trim_string( $string = null, $chars = 200, $suffix = '...' ) {
	if ( strlen( $string ) > $chars ) {
		if ( function_exists( 'mb_substr' ) ) {
			$string = mb_substr( $string, 0, ( $chars - mb_strlen( $suffix ) ) ) . $suffix;
		} else {
			$string = substr( $string, 0, ( $chars - strlen( $suffix ) ) ) . $suffix;
		}
	}
	return $string;
}

/**
 * Returns labels from selected glossary for given values
 *
 * @return [type] [description]
 */
function jet_engine_label_by_glossary( $value = null, $glossary_id = null, $deliminter = ', ' ) {
	return jet_engine()->glossaries->get_labels_for_values( $value, $glossary_id, $deliminter );
}

/**
 * Returns value with applied URL scheme
 *
 * @param  [type] $value  [description]
 * @param  [type] $scheme [description]
 * @return [type]         [description]
 */
function jet_engine_url_scheme( $value = null, $scheme = null ) {
	return \Jet_Engine_URL_Shemes_Manager::instance()->apply_scheme( $value, $scheme );
}

/**
 * Return proptional value
 */
function jet_engine_proportional( $value = null, $divisor = 1, $multiplier = 1, $precision = 0 ) {

	$value      = floatval( $value );
	$divisor    = floatval( $divisor );
	$multiplier = floatval( $multiplier );
	$precision  = intval( $precision );

	if ( ! $divisor || ! $value ) {
		return $value;
	}

	if ( ! $multiplier ) {
		return 0;
	}

	$result = ( $value / $divisor ) * $multiplier;

	return round( $result, $precision );

}

/**
 * Return new Jet_Engine_Datetime object
 * @return Jet_Engine_Datetime
 */
function jet_engine_datetime() {

	if ( ! class_exists( '\Jet_Engine_Datetime' ) ) {
		require_once jet_engine()->plugin_path( 'includes/classes/datetime.php' );
	}

	return new \Jet_Engine_Datetime();

}

/**
 * Returns formatted date according timezone settings
 * 
 * @param  [type] $format    [description]
 * @param  [type] $timestamp [description]
 * @return [type]            [description]
 */
function jet_engine_date( $format, $timestamp ) {
	return jet_engine_datetime()->date( $format, $timestamp );
}

/**
 * Retruns given user property by given user ID
 * 
 * @param  [type] $user_id [description]
 * @param  [type] $prop    [description]
 * @return [type]          [description]
 */
function jet_engine_get_user_data_by_id( $user_id = 0, $prop = 'display_name' ) {
	return jet_engine()->listings->data->get_prop( $prop, get_user_by( 'ID', $user_id ) );
}
