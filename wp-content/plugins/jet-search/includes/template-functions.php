<?php
/**
 * Jet_Search_Template_Functions class
 *
 * @package   jet-search
 * @author    Zemez
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Search_Template_Functions' ) ) {

	/**
	 * Define Jet_Search_Template_Functions class
	 */
	class Jet_Search_Template_Functions {

		/**
		 * Return post thumbnail.
		 *
		 * @since 1.0.0
		 * @param array  $data
		 * @param object $post
		 *
		 * @return string
		 */
		static public function get_post_thumbnail( $data, $post ) {
			$visible = filter_var( $data['thumbnail_visible'], FILTER_VALIDATE_BOOLEAN );

			if ( ! $visible ) {
				return '';
			}

			$thumbnail_format = apply_filters( 'jet-search/ajax-search/thumbnail-html', '<div class="jet-ajax-search__item-thumbnail">%s</div>' );
			$thumbnail_size   = ! empty( $data['thumbnail_size'] ) ? $data['thumbnail_size'] : 'thumbnail';
			$thumbnail_attr   = array( 'class' => 'jet-ajax-search__item-thumbnail-img' );
			$thumbnail_html   = get_the_post_thumbnail( $post->ID, $thumbnail_size, $thumbnail_attr );

			if ( ! $thumbnail_html ) {

				if ( isset( $data['thumbnail_placeholder'] ) ) {
					$thumbnail_placeholder = $data['thumbnail_placeholder'];

					if ( ! empty( $thumbnail_placeholder['id'] ) ) {
						$thumbnail_html = wp_get_attachment_image( $thumbnail_placeholder['id'], $thumbnail_size, false, $thumbnail_attr );
					} elseif ( ! empty( $thumbnail_placeholder['url'] ) ) {
						$thumbnail_html = sprintf(
							'<img src="%1$s" class="%2$s" alt="">',
							esc_url( $thumbnail_placeholder['url'] ),
							esc_attr( $thumbnail_attr['class'] )
						);
					}

				} else {
					$thumbnail_html = apply_filters(
						'jet-search/ajax-search/thumbnail-placeholder-html',
						'<div class="jet-ajax-search__item-thumbnail-placeholder"></div>'
					);
				}
			}

			if ( ! $thumbnail_html ) {
				return '';
			}

			$output_html = sprintf( $thumbnail_format, $thumbnail_html );

			return $output_html;
		}

		/**
		 * Return post content.
		 *
		 * @since 1.0.0
		 * @since 2.0.0  Added `post_content_source` option
		 * @param array  $data
		 * @param object $post
		 *
		 * @return string
		 */
		static public function get_post_content( $data, $post ) {

			$after  = '&hellip;';
			$length = ( int ) $data['post_content_length'];

			if ( 0 !== $length ) {

				$source  = ! empty( $data['post_content_source'] ) ? $data['post_content_source'] : 'content';
				$content = apply_filters( 'jet-search/template/pre-get-content', false, $source, $post, $data );

				if ( false === $content ) {

					switch ( $source ) {
						case 'excerpt':
							$content = $post->post_excerpt;
							break;

						case 'custom-field':
							$key     = $data['post_content_custom_field_key'];
							$content = ! empty( $key ) ? get_post_meta( $post->ID, $key, true ) : '';
							break;

						default:
							$content = $post->post_content;
					}

				}

				$content = strip_shortcodes( $content );
				$content = str_replace( ']]>', ']]&gt;', $content );
				$content = preg_replace( '~\[[^\]]+\]~', '', $content );
				$content = wp_trim_words( $content, $length, $after );

				return $content;
			}

			return '';
		}

		/**
		 * Return product price.
		 *
		 * @since 1.1.0
		 * @param array  $data
		 * @param object $post
		 *
		 * @return string
		 */
		static public function get_product_price( $data, $post ) {
			$visible = filter_var( $data['show_product_price'], FILTER_VALIDATE_BOOLEAN );

			if ( ! $visible || 'product' !== $post->post_type || ! function_exists( 'wc' ) ) {
				return '';
			}

			if ( defined( 'WCML_VERSION' ) ) {
				add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );
			}

			$product    = wc_get_product( $post );
			$price_html = $product->get_price_html();

			if ( empty( $price_html ) ) {
				return '';
			}

			$price_format = apply_filters( 'jet-search/ajax-search/product-price-html', '<div class="jet-ajax-search__item-price"><div class="price">%s</div></div>' );

			return sprintf( $price_format, $price_html );
		}

		/**
		 * Return product rating.
		 *
		 * @since 1.1.0
		 * @param array  $data
		 * @param object $post
		 *
		 * @return string
		 */
		static public function get_product_rating( $data, $post ) {
			$visible = filter_var( $data['show_product_rating'], FILTER_VALIDATE_BOOLEAN );

			if ( ! $visible || 'product' !== $post->post_type || ! function_exists( 'wc' ) ) {
				return '';
			}

			$product = wc_get_product( $post );
			$rating  = $product->get_average_rating();

			if ( empty( $rating ) ) {
				return '';
			}
			$rating         = round( $rating, 1 );
			$floored_rating = (int) $rating;
			$rating_html    = '';
			$icon           = '&#61445;';

			for ( $stars = 1; $stars <= 5; $stars++ ) {
				if ( $stars <= $rating ) {
					$rating_html .= '<i class="jet-ajax-search__rating-star fas fa-star">' . $icon . '</i>';
				} elseif ( $floored_rating + 1 === $stars && $rating > $floored_rating ) {
					$rating_html .= '<i class="jet-ajax-search__rating-star fas fa-star jet-ajax-search__rating-star-' . ( $rating - $floored_rating ) * 10 . '">' . $icon . '</i>';
				} else {
					$rating_html .= '<i class="jet-ajax-search__rating-star fas jet-ajax-search__rating-star-empty">' . $icon . '</i>';
				}
			}

			$rating_format = apply_filters( 'jet-search/ajax-search/product-rating-html', '<div class="jet-ajax-search__item-rating">%s</div>' );

			return sprintf( $rating_format, $rating_html );
		}

		/**
		 * Get meta for passed position
		 *
		 * @since  2.0.0
		 * @param  array  $settings
		 * @param  object $post
		 * @param  string $position
		 * @param  string $base
		 * @param  array  $context
		 * @return string|void
		 */
		static public function get_meta_fields( $settings = array(), $post = null, $position = '', $base = '', $context = array( 'before' ) ) {

			$config_key    = $position . '_meta';
			$show_key      = 'show_' . $position . '_meta';
			$position_key  = 'meta_' . $position . '_position';
			$meta_show     = ! empty( $settings[ $show_key ] ) ? $settings[ $show_key ] : false;
			$meta_position = ! empty( $settings[ $position_key ] ) ? $settings[ $position_key ] : false;
			$meta_config   = ! empty( $settings[ $config_key ] ) ? $settings[ $config_key ] : false;

			if ( 'yes' !== $meta_show && 'true' !== $meta_show ) {
				return;
			}

			if ( ! $meta_position || ! in_array( $meta_position, $context ) ) {
				return;
			}

			if ( empty( $meta_config ) ) {
				return;
			}

			$result = '';

			foreach ( $meta_config as $meta ) {

				if ( empty( $meta['meta_key'] ) ) {
					continue;
				}

				$key      = $meta['meta_key'];
				$callback = ! empty( $meta['meta_callback'] ) ? $meta['meta_callback'] : false;

				$value = apply_filters( 'jet-search/template/pre-get-meta-field', false, $meta, $post, $settings, $position );

				if ( ! $value ) {
					$value = get_post_meta( $post->ID, $key, false );
				}

				if ( ! $value ) {
					continue;
				}

				$meta_callbacks = Jet_Search_Tools::allowed_meta_callbacks();

				unset( $meta_callbacks[''] );

				$allowed_functions = array_keys( $meta_callbacks );

				if ( in_array( $callback, $allowed_functions ) && ! empty( $callback ) && is_callable( $callback ) ) {

					$callback_args = array( $value[0] );

					switch ( $callback ) {

						case 'wp_get_attachment_image':

							$callback_args[] = 'full';

							break;

						case 'date':
						case 'date_i18n':

							$timestamp       = $value[0];
							$valid_timestamp = Jet_Search_Tools::is_valid_timestamp( $timestamp );

							if ( ! $valid_timestamp ) {
								$timestamp = strtotime( $timestamp );
							}

							$format        = ! empty( $meta['date_format'] ) ? $meta['date_format'] : 'F j, Y';
							$callback_args = array( $format, $timestamp );

							break;
					}

					$meta_val = call_user_func_array( $callback, $callback_args );
				} else {
					$meta_val = $value[0];
				}

				$meta_val = sprintf( $meta['meta_format'], $meta_val );

				$label = ! empty( $meta['meta_label'] )
					? sprintf( '<div class="%1$s__item-label">%2$s</div>', $base, $meta['meta_label'] )
					: '';

				$result .= sprintf(
					'<div class="%1$s__item %1$s__item-%4$s">%2$s<div class="%1$s__item-value">%3$s</div></div>',
					$base, $label, $meta_val, sanitize_html_class( str_replace( '%', '', $key )  )
				);

			}

			if ( empty( $result ) ) {
				return;
			}

			return sprintf( '<div class="%1$s">%2$s</div>', $base, $result );
		}
	}
}
