<?php
/**
 * Tools class
 */

class Jet_Engine_Tools {

	/**
	 * Process
	 *
	 * @param  [type] $filename [description]
	 * @param string $file [description]
	 *
	 * @return [type]           [description]
	 */
	public static function file_download( $filename = null, $file = '', $type = 'application/json' ) {

		if ( false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) ) {
			set_time_limit( 0 );
		}

		@session_write_close();

		if ( function_exists( 'apache_setenv' ) ) {
			$variable = 'no-gzip';
			$value    = 1;
			@apache_setenv( $variable, $value );
		}

		@ini_set( 'zlib.output_compression', 'Off' );

		nocache_headers();

		$file = apply_filters( 'jet-engine/tools/file-download', $file, $type, $filename );

		header( "Robots: none" );
		header( "Content-Type: " . $type );
		header( "Content-Description: File Transfer" );
		header( "Content-Disposition: attachment; filename=\"" . $filename . "\";" );
		header( "Content-Transfer-Encoding: binary" );

		// Set the file size header
		header( "Content-Length: " . strlen( $file ) );

		echo $file;
		die();

	}

	/**
	 * Add query arguments string by query arguments array
	 *
	 * @param [type] $url      [description]
	 * @param array $settings [description]
	 */
	public static function add_query_args_by_settings( $url = null, $settings = array() ) {

		if ( empty( $settings['add_query_args'] ) || empty( $settings['query_args'] ) ) {
			return $url;
		}

		$query_args = $settings['query_args'];
		$query_args = preg_split( '/\r\n|\r|\n/', $query_args );

		if ( empty( $query_args ) || ! is_array( $query_args ) ) {
			return $url;
		}

		$final_query_args = array();

		foreach ( $query_args as $arg ) {
			$arg = explode( '=', $arg, 2 );

			if ( 1 < count( $arg ) ) {
				$final_query_args[ $arg[0] ] = jet_engine()->listings->macros->do_macros( $arg[1], $url );
			}

		}

		if ( ! empty( $final_query_args ) ) {

			// To prevent errors on PHP 8.1+
			if ( is_null( $url ) ) {
				$url = '';
			}

			$url = add_query_arg( $final_query_args, $url );
		}

		return $url;

	}

	/**
	 * Get options prepared to use by JetEngine fields from the callback
	 * 
	 * @param  [type] $callback [description]
	 * @return [type]           [description]
	 */
	public static function get_options_from_callback( $callback = null, $is_blocks = false ) {
		
		if ( ! is_callable( $callback ) ) {
			return [];
		}

		$options = call_user_func( $callback );

		foreach ( $options as $value => $option ) {

			if ( $is_blocks ) {
				if ( ! is_array( $option ) ) {
					$options[ $value ] = [
						'value' => $value,
						'label' => $option,
					];
				} else {

					$value = isset( $option['value'] ) ? $option['value'] : $value;
					$label = isset( $option['label'] ) ? $option['label'] : array_values( $option )[0];
					
					$options[ $value ] = [
						'value' => $value,
						'label' => $label,
					];
				}
			} else {
				if ( ! is_array( $option ) ) {
					$options[ $value ] = $option;
				} else {
					$options[ $value ] = isset( $option['label'] ) ? $option['label'] : array_values( $option )[0];
				}
			}
		}

		if ( $is_blocks ) {
			return array_values( $options );
		} else {
			return $options;
		}

	}

	public static function safe_get( $key, $list = array(), $default = false ) {
		return isset( $list[ $key ] ) ? $list[ $key ] : $default;
	}

	public static function sanitize_html_tag( $input ) {
		$available_tags = array(
			'div',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'p',
			'span',
			'a',
			'section',
			'header',
			'footer',
			'main',
			'b',
			'em',
			'i',
			'nav',
			'article',
			'aside',
			'tr',
			'ul',
			'ol',
			'li'
		);

		return in_array( strtolower( $input ), $available_tags ) ? $input : 'div';
	}

	public static function prepare_controls_for_js( $controls = array() ) {

		$result = array();

		foreach ( $controls as $key => $data ) {

			$data['name'] = $key;

			if ( ! empty( $data['options'] ) ) {

				if ( ! empty( $data['add_placeholder'] ) ) {
					$data['options'] = array_merge( array( '' => $data['add_placeholder'] ), $data['options'] );
				}

				$data['options'] = self::prepare_list_for_js( $data['options'], ARRAY_A );

			}

			if ( ! empty( $data['groups'] ) ) {

				$groups = array();

				foreach ( $data['groups'] as $group ) {
					$groups[] = array(
						'label'  => $group['label'],
						'values' => self::prepare_list_for_js( $group['options'], ARRAY_A ),
					);
				}

				$data['groups'] = $groups;

			}

			$result[] = $data;
		}

		return $result;

	}

	/**
	 * Returns all post types list to use in JS components
	 *
	 * @return [type] [description]
	 */
	public static function get_post_types_for_js( $placeholder = false, $key = false ) {

		$post_types = get_post_types( array(), 'objects' );
		$types_list = self::prepare_list_for_js( $post_types, 'name', 'label', $key );

		if ( $placeholder && is_array( $placeholder ) ) {
			$types_list = array_merge( array( $placeholder ), $types_list );
		}

		return $types_list;
	}

	/**
	 * Return all taxonomies list to use in JS components
	 *
	 * @return [type] [description]
	 */
	public static function get_taxonomies_for_js( $key = false, $with_slug = false ) {
		
		$taxonomies          = get_taxonomies( array(), 'objects' );
		$prepared_taxonomies = self::prepare_list_for_js( $taxonomies, 'name', 'label', $key );

		if ( $with_slug ) {
			return array_map( function( $item ) {
				$item['label'] = $item['label'] . ' (' . $item['value'] . ')';
				return $item;
			}, $prepared_taxonomies );
		}

		return $prepared_taxonomies;
	}

	/**
	 * Returns all registeredroles for JS
	 */
	public static function get_user_roles_for_js() {

		$roles  = self::get_user_roles();
		$result = array();

		foreach ( $roles as $role => $label ) {
			if ( ! isset( $result[ $role ] ) ) {
				$result[ $role ] = array(
					'value' => $role,
					'label' => $label,
				);
			}
		}

		return array_values( $result );

	}

	/**
	 * Returns all registered user roles
	 *
	 * @return [type] [description]
	 */
	public static function get_user_roles() {

		if ( ! function_exists( 'get_editable_roles' ) ) {
			return array();
		} else {
			$roles  = get_editable_roles();
			$result = array();

			foreach ( $roles as $role => $data ) {
				$result[ $role ] = $data['name'];
			}

			return $result;
		}

	}

	/**
	 * Prepare passed array for using in JS options
	 *
	 * @return [type] [description]
	 */
	public static function prepare_list_for_js( $array = array(), $value_key = null, $label_key = null, $key = false ) {

		$result = array();

		if ( ! is_array( $array ) || empty( $array ) ) {
			return $result;
		}

		$array_key = false;

		foreach ( $array as $index => $item ) {

			$value = null;
			$label = null;

			if ( is_object( $item ) ) {
				$value = $item->$value_key;
				$label = $item->$label_key;

				if ( $key ) {
					$array_key = $item->$key;
				}

			} elseif ( is_array( $item ) ) {
				$value = $item[ $value_key ];
				$label = $item[ $label_key ];

				if ( $key ) {
					$array_key = $item[ $key ];
				}

			} else {

				if ( ARRAY_A === $value_key ) {
					$value = $index;
				} else {
					$value = $item;
				}

				$label = $item;

				if ( $key ) {
					$array_key = $index;
				}
			}

			if ( $key && false !== $array_key ) {
				$result[ $array_key ] = array(
					'value' => $value,
					'label' => $label,
				);
			} else {
				$result[] = array(
					'value' => $value,
					'label' => $label,
				);
			}

		}

		return $result;

	}

	/**
	 * Render new elementor icons
	 *
	 * @return [type] [description]
	 */
	public static function render_icon( $icon = null, $icon_class = '', $custom_atts = array() ) {

		$custom_atts_string = '';

		if ( ! empty( $custom_atts ) ) {
			foreach ( $custom_atts as $key => $value ) {
				$custom_atts_string .= sprintf( ' %1$s="%2$s"', $key, $value );
			}
		}

		static $total = 0;

		if ( ! is_array( $icon ) && is_numeric( $icon ) ) {

			ob_start();

			echo '<div class="' . $icon_class . ' is-svg-icon"' . $custom_atts_string . '>';

			$mime = get_post_mime_type( $icon );

			if ( 'image/svg+xml' === $mime ) {
				$file = get_attached_file( $icon );

				if ( file_exists( $file ) ) {
					include $file;
				}

			} else {
				echo wp_get_attachment_image( $icon, 'full' );
			}

			echo '</div>';

			return ob_get_clean();

		}
		// Render Bricks svg icon
		elseif ( ! is_array( $icon ) && false !== str_contains( $icon, '<svg' ) ) {

			ob_start();

			echo '<div class="' . $icon_class . ' is-svg-icon"' . $custom_atts_string . '>';
			echo $icon;
			echo '</div>';

			return ob_get_clean();

		}
		// Render Bricks font icon
		elseif ( ! is_array( $icon ) && false !== str_contains( $icon, '<i' ) ) {

			ob_start();

			echo '<div class="' . $icon_class . '">';
			echo $icon;
			echo '</div>';

			return ob_get_clean();
		}
		// Bricks font icon with array value
		elseif ( is_array( $icon ) && isset( $icon['library'] ) && isset( $icon['icon'] ) ) {
			return sprintf( '<div class="%1$s"><i class="%2$s"></i></div>', $icon_class, $icon['icon'] );
		}

		if ( empty( $icon['value'] ) ) {
			return false;
		}

		$is_new = class_exists( 'Elementor\Icons_Manager' ) && Elementor\Icons_Manager::is_migration_allowed();

		if ( $is_new ) {
			ob_start();

			$custom_atts['class']       = $icon_class;
			$custom_atts['aria-hidden'] = 'true';

			Elementor\Icons_Manager::render_icon( $icon, $custom_atts );

			$html = ob_get_clean();

			$is_svg_library = 'svg' === $icon['library'];
			$is_svg_inline  = false !== strpos( $html, 'e-font-icon-svg' );

			if ( $is_svg_library || $is_svg_inline ) {

				if ( $is_svg_inline ) {
					$html = str_replace( $icon_class . ' ', '', $html );
				}

				$html = sprintf( '<div class="%1$s is-svg-icon"%2$s>%3$s</div>', $icon_class, $custom_atts_string, $html );
			}

			return $html;

		} else {
			return false;
		}

	}

	/**
	 * Get html attributes string.
	 *
	 * @param array $attrs
	 *
	 * @return string
	 */
	public static function get_attr_string( $attrs ) {
		$result_array = array();

		foreach ( $attrs as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = join( ' ', $value );
			}

			$result_array[] = sprintf( '%1$s="%2$s"', $key, esc_attr( $value ) );
		}

		return join( ' ', $result_array );
	}

	/**
	 * Check if is valid timestamp
	 *
	 * @param mixed $timestamp
	 *
	 * @return boolean
	 */
	public static function is_valid_timestamp( $timestamp ) {

		if ( is_array( $timestamp ) || is_object( $timestamp ) ) {
			return false;
		}

		return ( ( string ) ( int ) $timestamp === $timestamp || ( int ) $timestamp === $timestamp )
		       && ( $timestamp <= PHP_INT_MAX )
		       && ( $timestamp >= ~PHP_INT_MAX );
	}

	/**
	 * Checks a value for being empty.
	 *
	 * @param mixed $source
	 * @param bool|string $key
	 *
	 * @return bool
	 */
	public static function is_empty( $source = null, $key = false ) {

		if ( is_array( $source ) && $key ) {

			if ( ! isset( $source[ $key ] ) ) {
				return true;
			}

			$source = $source[ $key ];
		}

		return empty( $source ) && '0' !== $source;
	}

	/**
	 * Determines whether the current request is a REST API request.
	 *
	 * @return bool
	 */
	public static function wp_doing_rest() {
		return apply_filters( 'jet-engine/wp_doing_rest', defined( 'REST_REQUEST' ) && REST_REQUEST );
	}

	/**
	 * Returns allowed operatos list in the given format
	 *
	 * @param array $exclude excluded operators list
	 * @param  [type] $format  ARRAY_N or ARRAY_A
	 *
	 * @return [type]          [description]
	 */
	public static function operators_list( $exclude = array(), $format = ARRAY_N ) {

		$operators = array(
			'='           => __( 'Equal (=)', 'jet-engine' ),
			'!='          => __( 'Not equal (!=)', 'jet-engine' ),
			'>'           => __( 'Greater than (>)', 'jet-engine' ),
			'>='          => __( 'Greater or equal (>=)', 'jet-engine' ),
			'<'           => __( 'Less than (<)', 'jet-engine' ),
			'<='          => __( 'Less or equal (<=)', 'jet-engine' ),
			'LIKE'        => __( 'Like', 'jet-engine' ),
			'NOT LIKE'    => __( 'Not like', 'jet-engine' ),
			'IN'          => __( 'In the list', 'jet-engine' ),
			'NOT IN'      => __( 'Not in the list', 'jet-engine' ),
			'BETWEEN'     => __( 'Between', 'jet-engine' ),
			'NOT BETWEEN' => __( 'Not between', 'jet-engine' ),
			'EXISTS'      => __( 'Exists', 'jet-engine' ),
			'NOT EXISTS'  => __( 'Not exists', 'jet-engine' ),
			'REGEXP'      => __( 'Regexp', 'jet-engine' ),
			'NOT REGEXP'  => __( 'Not regexp', 'jet-engine' ),
		);

		$allowed = array_diff( array_keys( $operators ), $exclude );
		$result  = array();

		foreach ( $allowed as $operator ) {
			switch ( $format ) {
				case ARRAY_N:
					$result[] = array(
						'value' => $operator,
						'label' => $operators[ $operator ],
					);
					break;

				case ARRAY_A:
					$result[ $operator ] = $operators[ $operator ];
					break;
			}
		}

		return $result;

	}

	/**
	 * Returns allowed data types list in the given format
	 *
	 * @param  [type] $format  ARRAY_N or ARRAY_A
	 *
	 * @return [type]          [description]
	 */
	public static function data_types_list( $format = ARRAY_N ) {

		$data_types = array(
			'CHAR'      => __( 'Char', 'jet-engine' ),
			'NUMERIC'   => __( 'Numeric', 'jet-engine' ),
			'DATE'      => __( 'Date', 'jet-engine' ),
			'DATETIME'  => __( 'Datetime', 'jet-engine' ),
			'TIMESTAMP' => __( 'Timestamp', 'jet-engine' ),
			'DECIMAL'   => __( 'Decimal', 'jet-engine' ),
			'TIME'      => __( 'Time', 'jet-engine' ),
			'BINARY'    => __( 'Binary', 'jet-engine' ),
			'SIGNED'    => __( 'Signed', 'jet-engine' ),
			'UNSIGNED'  => __( 'Unsigned', 'jet-engine' ),
		);

		if ( ARRAY_N === $format ) {

			$result = array();

			foreach ( $data_types as $type => $label ) {
				$result[] = array(
					'value' => $type,
					'label' => $label,
				);
			}

			return $result;

		} else {
			return $data_types;
		}

	}

	public static function get_post_statuses_for_js() {

		return array(
			array(
				'value' => 'any',
				'label' => __( 'Any', 'jet-engine' ),
			),
			array(
				'value' => 'publish',
				'label' => __( 'Publish', 'jet-engine' ),
			),
			array(
				'value' => 'pending',
				'label' => __( 'Pending', 'jet-engine' ),
			),
			array(
				'value' => 'draft',
				'label' => __( 'Draft', 'jet-engine' ),
			),
			array(
				'value' => 'future',
				'label' => __( 'Future', 'jet-engine' ),
			),
			array(
				'value' => 'private',
				'label' => __( 'Private', 'jet-engine' ),
			),
			array(
				'value' => 'trash',
				'label' => __( 'Trash', 'jet-engine' ),
			)
		);

	}

	public static function is_readable( $filename ) {
		return strlen( $filename ) <= PHP_MAXPATHLEN && is_readable( $filename );
	}

	/**
	 * Return registered image sizes array for options
	 * 
	 * @param  string $context [description]
	 * @return [type]          [description]
	 */
	public static function get_image_sizes( $context = 'elementor' ) {

		global $_wp_additional_image_sizes;

		$sizes         = get_intermediate_image_sizes();
		$result        = array();
		$blocks_result = array();

		foreach ( $sizes as $size ) {
			if ( in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ) ) ) {
				$label           = ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) );
				$result[ $size ] = $label;
				$blocks_result[] = array(
					'value' => $size,
					'label' => $label,
				);

			} else {

				$label = sprintf(
					'%1$s (%2$sx%3$s)',
					ucwords( trim( str_replace( array( '-', '_' ), array( ' ', ' ' ), $size ) ) ),
					$_wp_additional_image_sizes[ $size ]['width'],
					$_wp_additional_image_sizes[ $size ]['height']
				);

				$result[ $size ] = $label;
				$blocks_result[] = array(
					'value' => $size,
					'label' => $label,
				);
			}
		}

		$result        = array_merge( array( 'full' => __( 'Full', 'jet-engine' ), ), $result );
		$blocks_result = array_merge(
			array(
				array(
					'value' => 'full',
					'label' => __( 'Full', 'jet-engine' ),
				)
			),
			$blocks_result
		);

		if ( 'blocks' === $context ) {
			return $blocks_result;
		} else {
			return $result;
		}
	}

	/**
	 * Get attachment image data array from raw data.
	 *
	 * @param mixed $img_data Image data(id, url, array('id'=>'','url'=>'')).
	 * @param string $include Includes keys(id, url, all).
	 *
	 * @return array|bool
	 */
	public static function get_attachment_image_data_array( $img_data = null, $include = 'all' ) {

		$result = false;

		if ( empty( $img_data ) ) {
			return $result;
		}

		switch ( $include ) {
			case 'id':

				$id = null;

				if ( is_numeric( $img_data ) ) {
					$id = $img_data;
				} elseif ( is_array( $img_data ) && isset( $img_data['id'] ) && isset( $img_data['url'] ) ) {
					$id = $img_data['id'];
				} else {
					$id = attachment_url_to_postid( $img_data );
				}

				$result = array(
					'id' => $id,
				);
				break;

			case 'url':

				$url = null;

				if ( is_numeric( $img_data ) ) {
					$url = wp_get_attachment_url( $img_data );
				} elseif ( is_array( $img_data ) && isset( $img_data['id'] ) && isset( $img_data['url'] ) ) {
					$url = $img_data['url'];
				} else {
					$url = $img_data;
				}

				$result = array(
					'url' => $url,
				);
				break;

			default:

				$id  = null;
				$url = null;

				if ( is_numeric( $img_data ) ) {
					$id  = $img_data;
					$url = wp_get_attachment_url( $img_data );
				} elseif ( is_array( $img_data ) && isset( $img_data['id'] ) && isset( $img_data['url'] ) ) {
					$id  = $img_data['id'];
					$url = $img_data['url'];
				} else {
					$id  = attachment_url_to_postid( $img_data );
					$url = $img_data;
				}

				$result = array(
					'id'  => $id,
					'url' => $url,
				);
		}

		return $result;
	}

	/**
	 * Allows to insert new part pf array ($insert) into source array ($source) after gieven key ($after)
	 * @param  array  $source [description]
	 * @param  [type] $after  [description]
	 * @param  array  $insert [description]
	 * @return [type]         [description]
	 */
	public static function insert_after( $source = array(), $after = null, $insert = array() ) {

		$keys   = array_keys( $source );
		$index  = array_search( $after, $keys );

		if ( ! $source ) {
			$source = array();
		}

		if ( false === $index ) {
			return $source + $insert;
		}

		$offset = $index + 1;

		return array_slice( $source, 0, $offset, true ) + $insert + array_slice( $source, $offset, null, true );
	}

	/**
	 * Convert PHP date format to JS datepicker format. Ex.: Y-m-d => yy-mm-dd
	 *
	 * @param string $format PHP date format.
	 *
	 * @return string
	 */
	public static function convert_date_format_php_to_js( $format = '' ) {

		$replace_strings = array(
			// Day of Month
			'/(?<!\\\\)d/' => 'dd', // 01–31
			'/(?<!\\\\)j/' => 'd',  // 1–31
			'/(?<!\\\\)S/' => '',  // the English suffix for the day - st, nd or th in the 1st, 2nd or 15th.

			// Weekday
			'/(?<!\\\\)D/' => 'D',  // Mon – Sun
			'/(?<!\\\\)l/' => 'DD', // Sunday – Saturday

			// Month
			'/(?<!\\\\)m/' => 'mm', // 01–12
			'/(?<!\\\\)n/' => 'm',  // 1–12
			'/(?<!\\\\)M/' => 'M',  // Jan - Dec
			'/(?<!\\\\)F/' => 'MM', // January – December

			// Year
			'/(?<!\\\\)y/' => 'y',  // 21
			'/(?<!\\\\)Y/' => 'yy', // 2021

			// Time
			'/(?<!\\\\)a/' => 'tt', // am or pm
			'/(?<!\\\\)A/' => 'TT', // AM or PM
			'/(?<!\\\\)h/' => 'hh', // Hours 01-12
			'/(?<!\\\\)g/' => 'h',  // Hours 1-12
			'/(?<!\\\\)H/' => 'HH', // Hours 00-23
			'/(?<!\\\\)G/' => 'H',  // Hours 0-23
			'/(?<!\\\\)i/' => 'mm', // Minutes 00-59
			'/(?<!\\\\)s/' => 'ss', // Seconds 00-59
		);

		$result = preg_replace( array_keys( $replace_strings ), array_values( $replace_strings ), $format );

		// Prepare custom text to using in datepicker. Ex.: \a\t => 'at'.
		$result = preg_replace_callback( '/(?:\\\\.)+/', function ( $matches ) {
			return sprintf( '\'%s\'', wp_unslash( $matches[0] ) );
		}, $result );

		return $result;
	}

	/**
	 * Returns allowed `rel` attribute options in the given format
	 *
	 * @param  string $format ARRAY_N or ARRAY_A
	 * @return array
	 */
	public static function get_rel_attr_options( $format = ARRAY_A ) {

		$options = array(
			''           => esc_html__( 'No', 'jet-engine' ),
			'alternate'  => esc_html__( 'Alternate', 'jet-engine' ),
			'author'     => esc_html__( 'Author', 'jet-engine' ),
			'bookmark'   => esc_html__( 'Bookmark', 'jet-engine' ),
			'external'   => esc_html__( 'External', 'jet-engine' ),
			'help'       => esc_html__( 'Help', 'jet-engine' ),
			'license'    => esc_html__( 'License', 'jet-engine' ),
			'next'       => esc_html__( 'Next', 'jet-engine' ),
			'nofollow'   => esc_html__( 'Nofollow', 'jet-engine' ),
			'noreferrer' => esc_html__( 'Noreferrer', 'jet-engine' ),
			'noopener'   => esc_html__( 'Noopener', 'jet-engine' ),
			'prev'       => esc_html__( 'Prev', 'jet-engine' ),
			'search'     => esc_html__( 'Search', 'jet-engine' ),
			'tag'        => esc_html__( 'Tag', 'jet-engine' ),
		);

		if ( ARRAY_N === $format ) {

			$result = array();

			foreach ( $options as $value => $label ) {
				$result[] = array(
					'value' => $value,
					'label' => $label,
				);
			}

			return $result;
		}

		return $options;
	}

	public static function array_insert_after( $source = array(), $after = null, $insert = array() ) {

		$keys  = array_keys( $source );
		$index = array_search( $after, $keys );

		if ( ! $source ) {
			$source = array();
		}

		if ( false === $index ) {
			return $source + $insert;
		}

		$offset = $index + 1;

		return array_slice( $source, 0, $offset, true ) + $insert + array_slice( $source, $offset, null, true );
	}

	/**
	 * Returns list of menu positions with index and appropriate labels
	 * @return [type] [description]
	 */
	public static function get_available_menu_positions() {
		return apply_filters( 'jet-engine/tools/available-menu-positions', array(
			array(
				'value' => 3,
				'label' => __( 'Dashboard', 'jet-engine' ),
			),
			array(
				'value' => 4,
				'label' => __( 'First Separator', 'jet-engine' ),
			),
			array(
				'value' => 6,
				'label' => __( 'Posts', 'jet-engine' ),
			),
			array(
				'value' => 11,
				'label' => __( 'Media', 'jet-engine' ),
			),
			array(
				'value' => 16,
				'label' => __( 'Links', 'jet-engine' ),
			),
			array(
				'value' => 21,
				'label' => __( 'Pages', 'jet-engine' ),
			),
			array(
				'value' => 26,
				'label' => __( 'Comments', 'jet-engine' ),
			),
			array(
				'value' => 59,
				'label' => __( 'Second Separator', 'jet-engine' ),
			),
			array(
				'value' => 61,
				'label' => __( 'Appearance', 'jet-engine' ),
			),
			array(
				'value' => 66,
				'label' => __( 'Plugins', 'jet-engine' ),
			),
			array(
				'value' => 71,
				'label' => __( 'Users', 'jet-engine' ),
			),
			array(
				'value' => 76,
				'label' => __( 'Tools', 'jet-engine' ),
			),
			array(
				'value' => 81,
				'label' => __( 'Settings', 'jet-engine' ),
			),
			array(
				'value' => 100,
				'label' => __( 'Third Separator', 'jet-engine' ),
			),
		) );
	}

	/**
	 * Returns default menu poistion for JetEngine user-created instance.
	 * Main purpose - compatibility with JetDashboard module
	 * 
	 * @return [type] [description]
	 */
	public static function get_default_menu_position() {
		return apply_filters( 'jet-engine/tools/default-menu-position', '' );
	}

	/**
	 * Ensures a string is a valid SQL 'order by' clause.
	 *
	 * Accepts one or more columns, with or without a sort order (ASC / DESC).
	 * e.g. 'column_1', 'column_1, column_2', 'column_1 ASC, column_2 DESC' etc.
	 *
	 * Also accepts 'posts.column_1', 'posts.column_1, column_2', 'posts.column_1 ASC, column_2 DESC' etc.
	 *
	 * Also accepts 'RAND()'.
	 *
	 * @param string $orderby Order by clause to be validated.
	 * @return string|false Returns $orderby if valid, false otherwise.
	 */
	public static function sanitize_sql_orderby( $orderby ) {
		if ( preg_match( '/^\s*(([a-z0-9_\.]+|`[a-z0-9_\.]+`)(\s+(ASC|DESC))?\s*(,\s*(?=[a-z0-9_`\.])|$))+$/i', $orderby ) || preg_match( '/^\s*RAND\(\s*\)\s*$/i', $orderby ) ) {
			return $orderby;
		}
		return false;
	}

}
