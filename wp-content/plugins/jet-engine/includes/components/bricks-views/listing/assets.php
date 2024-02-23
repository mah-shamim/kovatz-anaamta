<?php
/**
 * Bricks views manager
 */
namespace Jet_Engine\Bricks_Views\Listing;

use Bricks\Query;
use Jet_Engine\Query_Builder\Manager as Query_Manager;

/**
 * Rewrite Bricks assets class
 */
class Assets extends \Bricks\Assets {

	public static $initial_inline_css = [];
	public static $initial_inline_css_dynamic_data = [];

	public static $css_cache_key = '_jet_engine_bricks_generated_listing_styles';
	public static $fonts_cache_key = '_jet_engine_bricks_generated_listing_fonts';
	public static $font_families_cache_key = '_jet_engine_bricks_generated_listing_font_families';
	public static $icons_cache_key = '_jet_engine_bricks_generated_listing_icons';

	public static $editor_fonts_to_load = [];

	public static $fonts_stack = [];

	public function __construct() {

		$wp_uploads_dir = wp_upload_dir( null, false );

		self::$wp_uploads_dir = $wp_uploads_dir['basedir'];
		self::$css_dir        = $wp_uploads_dir['basedir'] . '/bricks/css';
		self::$css_url        = $wp_uploads_dir['baseurl'] . '/bricks/css';

		add_action( 'wp_footer', [ $this, 'load_listing_fonts_late' ] );

	}

	/**
	 * Generate inline CSS
	 *
	 * Bricks Settings: "CSS loading Method" set to "Inline Styles" (= default)
	 *
	 * - Color Vars
	 * - Theme Styles
	 * - Global CSS Classes
	 * - Global Custom CSS
	 * - Page Custom CSS
	 * - Header
	 * - Content
	 * - Footer
	 * - Custom Fonts
	 * - Template
	 *
	 * @param $post_id Post ID.
	 *
	 * @return string $inline_css
	 */
	public static function generate_inline_css( $post_id = 0 ) {

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$inline_css = '';
		$elements   = get_post_meta( $post_id, BRICKS_DB_PAGE_CONTENT, true );

		$cached          = get_post_meta( $post_id, self::$css_cache_key, true );
		$has_dynamic_css = false;

		if ( $cached ) {
			/**
			 * Build Google fonts array by scanning inline CSS for Google fonts
			 */
			self::jet_load_webfonts( $cached, $post_id );
			self::jet_load_extra_assets( $elements, $post_id );
			return $cached;
		}

		if ( empty( $elements ) ) {
			return '';
		}

		$query_args      = [];
		$any             = Query::is_any_looping();
		$query_object    = Query::get_query_object( $any );
		$settings        = $query_object->settings ?? [];
		$custom_query    = $settings['custom_query'] ?? 0;
		$custom_query_id = $settings['custom_query_id'] ?? 0;

		// Check if the listing query builder is set
		if ( $custom_query && $custom_query_id ) {
			$query_builder = Query_Manager::instance()->get_query_by_id( $custom_query_id );
			$query_args    = array_merge( $query_args, $query_builder->get_query_args() );
		} else {
			// Obtain the query instance for a listing grid
			$instance = jet_engine()->listings->get_render_instance( 'listing-grid', $settings );

			// Save the current listing data and set it based on the post ID
			$current_listing = jet_engine()->listings->data->get_listing();
			jet_engine()->listings->data->set_listing_by_id( $post_id );

			// Call the get_query method to generate the query_vars
			$instance->get_query( $settings );
			$query_args = array_merge( $query_args, $instance->query_vars['request'] );

			// Restore the original listing data
			jet_engine()->listings->data->set_listing( $current_listing );
		}

		if ( jet_engine()->listings->is_listing_ajax( 'listing_load_more' ) ) {
			$query_args['paged'] = $_REQUEST['page'] ?? 1;
		}

		// Saves $inline_css before generating Listing grid styles
		if ( ! empty( self::$inline_css ) ) {
			self::$initial_inline_css = self::$inline_css;
		}

		// Saves $inline_css_dynamic_data before generating Listing grid styles
		if ( ! empty( self::$inline_css_dynamic_data ) ) {
			self::$initial_inline_css_dynamic_data = self::$inline_css_dynamic_data;
		}

		self::$inline_css = [
			'color_vars'     => '',
			'theme_style'    => '',
			'global'         => '',
			'global_classes' => '',
			'page'           => '',
			'template'       => '',
			'header'         => '',
			'content'        => '',
			'footer'         => '',
			'custom_fonts'   => '',
		];

		self::$elements = [];

		self::$inline_css_dynamic_data = '';

		// Clear the list of elements already styled (@since 1.5)
		self::$css_looping_elements = [];

		// Create a wrapper element representing a container element for generating dynamic css
		$wrapper = [
			'id'       => 'jet-listing-elements',
			'name'     => 'div',
			'parent'   => 0,
			'settings' => [
				'hasLoop' => 1,
				'query'   => $query_args
			],
		];

		$children = [];

		// Iterate through each parent element and update it identifier to the wrapper's identifier
		foreach ( $elements as $index => $element ) {
			if ( $element['parent'] === 0 ) {
				$elements[ $index ]['parent'] = $wrapper['id'];
				$children[]                   = $element['id'];
			}
		}

		$wrapper['children'] = $children;

		array_unshift( $elements, $wrapper );

		// STEP: Content
		self::generate_css_from_elements( $elements, 'content' );

		// STEP: Global Classes
		if ( is_callable( [ '\Jet_Engine\Bricks_Views\Listing\Assets', 'generate_global_classes' ] ) ) {
			self::generate_global_classes();
		} elseif ( is_callable( [ '\Jet_Engine\Bricks_Views\Listing\Assets', 'generate_inline_css_global_classes' ] ) ) {
			self::generate_inline_css_global_classes();
		}

		// STEP: Concatinate styles (respecting precedences)

		// #1 Color palettes
		if ( ! empty( self::$inline_css['color_vars'] ) ) {
			$inline_css .= "/* COLOR VARS */\n" . self::$inline_css['color_vars'];
		}

		// #2 Theme Styles
		if ( self::$inline_css['theme_style'] ) {
			$inline_css .= "\n/* THEME STYLE CSS */\n" . self::$inline_css['theme_style'];
		}

		// #5.4 Global Classes
		if ( self::$inline_css['global_classes'] ) {
			// NOTE Not in use as closing "}" in @media query is stripped off.
			// Remove duplicate CSS rules (caused by global class custom CSS applied to multiple elements)
			// $global_classes_css_rules = explode( "\n", self::$inline_css['global_classes'] );
			// $global_classes_css_rules = array_unique( $global_classes_css_rules );
			// $global_classes_css_rules = implode( "\n", $global_classes_css_rules );
			// $inline_css .= "\n/* GLOBAL CLASSES CSS */\n" . $global_classes_css_rules;

			$inline_css .= "\n/* GLOBAL CLASSES CSS */\n" . self::$inline_css['global_classes'];
		}

		// #3 Bricks settings - Custom CSS
		if ( self::$inline_css['global'] ) {
			$inline_css .= "\n/* GLOBAL CSS */\n" . self::$inline_css['global'];
		}

		// #4 Page settings
		if ( isset( self::$inline_css['page'] ) ) {
			$inline_css .= "\n/* PAGE CSS */\n" . self::$inline_css['page'];
		}

		// #5.2 Content
		if ( self::$inline_css['content'] ) {
			$inline_css .= "\n/* CONTENT CSS */\n" . self::$inline_css['content'];
		}

		// #6 Custom Fonts @font-face (generated in: generate_css_from_elements)
		if ( self::$inline_css['custom_fonts'] ) {
			$inline_css .= "\n/* CUSTOM FONTS CSS */\n" . self::$inline_css['custom_fonts'];
		}

		// #7 Dynamic data CSS
		if ( ! empty( self::$inline_css_dynamic_data ) ) {
			$inline_css .= self::$inline_css_dynamic_data;
			$has_dynamic_css = true;
		}

		// Make CSS selector to nested listing elements builder agnostic
		$inline_css = str_replace( '.brxe-jet-listing-elements', '.jet-listing-base', $inline_css );

		/**
		 * Build Google fonts array by scanning inline CSS for Google fonts
		 */
		self::jet_load_webfonts( $inline_css, $post_id );
		self::jet_load_extra_assets( $elements, $post_id );

		if ( $has_dynamic_css ) {
			delete_post_meta( $post_id, self::$css_cache_key );
		} else {
			update_post_meta( $post_id, self::$css_cache_key, $inline_css );
		}

		// Returns $inline_css after generating Listing grid styles
		if ( ! empty( self::$initial_inline_css ) ) {
			self::$inline_css = self::$initial_inline_css;
			self::$initial_inline_css = [];
		}

		// Returns $inline_css_dynamic_data after generating Listing grid styles
		if ( ! empty( self::$initial_inline_css_dynamic_data ) ) {
			self::$inline_css_dynamic_data = self::$initial_inline_css_dynamic_data;
			self::$initial_inline_css_dynamic_data = [];
		}

		return $inline_css;
	}

	public static function jet_load_extra_assets( $elements, $post_id ) {

		$used_icons = get_post_meta( $post_id, self::$icons_cache_key, true );

		if ( ! $used_icons ) {

			$bricks_settings_string = json_encode( $elements );
			$used_icons = [];

			if ( false !== strpos( $bricks_settings_string, '"library":"fontawesome' ) ) {
				$used_icons[] = [
					'handle' => 'bricks-font-awesome',
					'src' => BRICKS_URL_ASSETS . 'css/libs/font-awesome.min.css',
					'deps' => [ 'bricks-frontend' ],
					'ver' => filemtime( BRICKS_PATH_ASSETS . 'css/libs/font-awesome.min.css' ),
				];
			}

			if ( false !== strpos( $bricks_settings_string, '"library":"ionicons' ) ) {
				$used_icons[] = [
					'handle' => 'bricks-ionicons',
					'src' => BRICKS_URL_ASSETS . 'css/libs/ionicons.min.css',
					'deps' => [ 'bricks-frontend' ],
					'ver' => filemtime( BRICKS_PATH_ASSETS . 'css/libs/ionicons.min.css' ),
				];
			}

			if ( false !== strpos( $bricks_settings_string, '"library":"themify' ) ) {
				$used_icons[] = [
					'handle' => 'bricks-themify-icons',
					'src' => BRICKS_URL_ASSETS . 'css/libs/themify-icons.min.css',
					'deps' => [ 'bricks-frontend' ],
					'ver' => filemtime( BRICKS_PATH_ASSETS . 'css/libs/themify-icons.min.css' )
				];
			}

			update_post_meta( $post_id, self::$icons_cache_key, $used_icons );

		}

		if ( ! empty( $used_icons ) ) {
			foreach ( $used_icons as $icon_font ) {
				wp_enqueue_style( $icon_font['handle'], $icon_font['src'], $icon_font['deps'], $icon_font['ver'] );
			}
		}

	}

	/**
	 * Load Google fonts according to inline CSS (source of truth) and remove loading wrapper
	 */
	public static function jet_load_webfonts( $inline_css, $post_id ) {

		// Return: Google fonts disabled
		if ( \Bricks\Helpers::google_fonts_disabled() ) {
			return;
		}

		$active_google_font_urls     = get_post_meta( $post_id, self::$fonts_cache_key, true );
		$active_google_font_families = get_post_meta( $post_id, self::$font_families_cache_key, true );

		if ( ! $active_google_font_urls || ! $active_google_font_families ) {

			$google_fonts_families_string = '';
			$assets_path_gf = BRICKS_URL_ASSETS . 'fonts/google-fonts.min.json';

			if ( is_callable( [ '\Bricks\Helpers', 'file_get_contents' ] ) ) {
				$google_fonts_families_string = \Bricks\Helpers::file_get_contents( $assets_path_gf );
			} elseif ( is_callable( [ '\Bricks\Helpers', 'get_file_contents' ] ) ) {
				$google_fonts_families_string = \Bricks\Helpers::get_file_contents( $assets_path_gf );
			}

			$google_fonts_families        = json_decode( $google_fonts_families_string, true );
			$google_fonts_families        = is_array( $google_fonts_families ) ? $google_fonts_families : [];
			$active_google_font_families  = [];
			$active_google_font_urls      = [];

			// Scan inline CSS for each Google font
			foreach ( $google_fonts_families as $google_font ) {
				$index           = strpos( $inline_css, $google_font['family'] );
				$add_google_font = false;

				// Skip iteration if this Google Font isn't found in inline CSS
				if ( ! $index ) {
					continue;
				}

				$font_weights = [];

				// Search all Google Font occurrences to build up font weights
				while ( $index = strpos( $inline_css, $google_font['family'], $index ) ) {
					$css_rule_index_start = strrpos( substr( $inline_css, 0, $index ), '{' ) + 1;
					$css_rule_index_end   = strpos( $inline_css, '}', $index );

					$css_rules_string = substr( $inline_css, $css_rule_index_start, $css_rule_index_end - $css_rule_index_start );
					$css_rules        = explode( '; ', $css_rules_string );

					foreach ( $css_rules as $css_rule_string ) {
						$css_rule     = explode( ': ', $css_rule_string );
						$css_property = $css_rule[0];
						$css_value    = str_replace( '"', '', $css_rule[1] ); // Remove added doulbe quotes (") from font-family value to find match

						// Remove fallback font (@since 1.5.1)
						$fallback_font_index = strpos( $css_value, ',' );

						if ( $fallback_font_index ) {
							$css_value = substr_replace( $css_value, '', $fallback_font_index, strlen( $css_value ) );
						}

						// Check for Google Font family
						if ( $css_property === 'font-family' && $css_value === $google_font['family'] ) {
							$add_google_font = $google_font['family'];
						}

						// Check for Google Font weight
						if ( $css_property === 'font-weight' && $add_google_font ) {
							// Check for italic
							if ( strpos( $css_rules_string, 'font-style: italic' ) !== false ) {
								$css_value .= 'italic';
							}

							if ( ! in_array( $css_value, $font_weights ) ) {
								$font_weights[] = $css_value;
							}
						}
					}

					// Increase index to start next iteration right after last inline CSS pointer
					$index++;

				}

				// Check next Google Font
				if ( ! $add_google_font ) {
					continue;
				}

				$google_font_family = '';

				// Default: Load all Google font variants (@since 1.5.1)
				$font_weights = ! empty( $google_font['variants'] ) && is_array( $google_font['variants'] ) ? $google_font['variants'] : [];

				// Optional: Theme Style typography: Load only selected font-variants
				$theme_style_typography = ! empty( \Bricks\Theme_Styles::$active_settings['typography'] ) ? \Bricks\Theme_Styles::$active_settings['typography'] : '';

				if ( $theme_style_typography ) {
					foreach ( $theme_style_typography as $typography_setting ) {
						$font_family   = ! empty( $typography_setting['font-family'] ) ? $typography_setting['font-family'] : false;
						$font_variants = ! empty( $typography_setting['font-variants'] ) ? $typography_setting['font-variants'] : false;

						if ( $font_family === $add_google_font && $font_variants ) {
							$font_weights = is_array( $font_variants ) ? $font_variants : [ $font_variants ];
						}
					}
				}

				if ( count( $font_weights ) ) {
					sort( $font_weights );

					$font_weights = join( ',', $font_weights );

					// Append font weights to Google font family name (e.g.: Roboto:100,300italic,700)
					$add_google_font .= ":$font_weights";
				}

				$google_font_family = $add_google_font;

				// Hack: https://github.com/typekit/webfontloader/issues/409#issuecomment-492831957
				$active_google_font_families[] = $google_font_family;
				$active_google_font_urls[]     = "https://fonts.googleapis.com/css?family=$google_font_family&display=swap";

			}

			update_post_meta( $post_id, self::$fonts_cache_key, $active_google_font_urls );
			update_post_meta( $post_id, self::$font_families_cache_key, $active_google_font_families );
		}

		foreach ( $active_google_font_families as $index => $family ) {
			if ( ! isset( self::$fonts_stack[ $family ] ) ) {
				self::$fonts_stack[ $family ] = $active_google_font_urls[ $index ];
			}
		}

		$has_fonts_to_load = ( count( $active_google_font_families ) && count( $active_google_font_urls ) ) ? true : false;

		// Frontend: Load Google font files (via Webfont loader OR stylesheets (= default))
		if ( ! bricks_is_builder() && $has_fonts_to_load ) {


			/*
			// Use wefont.min.js (and hide HTML until all webfonts are loaded)
			if ( ! \Bricks\Helpers::google_fonts_disabled() && \Bricks\Database::get_setting( 'webfontLoading' ) === 'webfontloader' ) {

			   $webfonts_js = 'WebFont.load({
				  classes: false,
				  loading: function() {
					 document.documentElement.style.opacity = 0
				  },
				  active: function() {
					 document.documentElement.removeAttribute("style")
				  },
				  custom: {
					 families: ' . json_encode( $active_google_font_families ) . ',
					 urls: ' . json_encode( $active_google_font_urls, JSON_UNESCAPED_SLASHES ) . '
				  }
			   })';

			   if ( wp_script_is( 'bricks-webfont' ) ) {
				  printf( '<script>%s</script>', $webfonts_js );
			   } else {
				  wp_enqueue_script( 'bricks-webfont' );
				  wp_add_inline_script( 'bricks-webfont', $webfonts_js );
			   }

			}

			// Use font stylesheet URLs
			else {
			   foreach ( $active_google_font_urls as $index => $active_google_font_url ) {
				  wp_enqueue_style( "bricks-google-font-jet-$index", $active_google_font_url, [], '' );
			   }
			}
			*/
		}

		if ( jet_engine()->bricks_views->is_bricks_editor() && $has_fonts_to_load ) {
			self::$editor_fonts_to_load = $active_google_font_urls;
		}
	}

	public function load_listing_fonts_late() {

		$fonts_to_load = self::$fonts_stack;
		$loaded_fonts = '';

		if ( ! \Bricks\Helpers::google_fonts_disabled() && \Bricks\Database::get_setting( 'webfontLoading' ) === 'webfontloader' ) {

			$has_webfonts_js = false;

			if ( wp_script_is( 'bricks-webfont' ) && ! empty( self::$fonts_stack ) ) {

				$data = wp_scripts()->get_data( 'bricks-webfont', 'after' );
				$has_webfonts_js = true;

				foreach ( $data as $row ) {
					if ( false !== strpos( $row, 'WebFont.load' ) ) {
						$loaded_fonts = $row;
					}
				}

				if ( ! empty( $loaded_fonts ) ) {

					foreach ( self::$fonts_stack as $family => $url ) {
						if ( false !== strpos( $loaded_fonts, $family ) ) {
							unset( $fonts_to_load[ $family ] );
						}
					}

				}

			}

			if ( ! empty( $fonts_to_load ) ) {

				$webfonts_js = 'WebFont.load({
               classes: false,
               loading: function() {
                  document.documentElement.style.opacity = 0
               },
               active: function() {
                  document.documentElement.removeAttribute("style")
               },
               custom: {
                  families: ' . json_encode( array_keys( $fonts_to_load ) ) . ',
                  urls: ' . json_encode( array_values( $fonts_to_load ), JSON_UNESCAPED_SLASHES ) . '
               }
            })';

				if ( $has_webfonts_js ) {
					printf( '<script>%s</script>', $webfonts_js );
				} else {
					wp_enqueue_script( 'bricks-webfont' );
					wp_add_inline_script( 'bricks-webfont', $webfonts_js );
				}
			}

		} else {
			$n = 0;

			while ( wp_style_is( 'bricks-google-font-' . $n ) ) {
				$loaded_fonts .= wp_styles()->query( 'bricks-google-font-' . $n )->src;
				$n++;
			}

			if ( ! empty( $loaded_fonts ) ) {

				foreach ( self::$fonts_stack as $family => $url ) {
					if ( false !== strpos( $loaded_fonts, $url ) ) {
						unset( $fonts_to_load[ $family ] );
					}
				}

			}

			foreach ( $fonts_to_load as $font => $url ) {
				wp_enqueue_style( $font, $url, [], '' );
			}

		}
	}

	public static function jet_print_editor_fonts() {

		if ( empty( self::$editor_fonts_to_load ) ) {
			return;
		}

		foreach ( self::$editor_fonts_to_load as $font_url ) {
			printf( '<link href="%s" rel="stylesheet">', $font_url );
		}
	}

}
