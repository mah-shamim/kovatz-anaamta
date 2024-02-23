<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Render_Dynamic_Meta' ) ) {

	class Jet_Engine_Render_Dynamic_Meta extends Jet_Engine_Render_Base {

		public function get_name() {
			return 'jet-listing-dynamic-meta';
		}

		public function get_parsed_settings( $settings = array() ) {

			if ( isset( $settings['meta_items'] ) ) {
				return $settings;
			}

			$settings['meta_items'] = array();

			if ( ! isset( $settings['date_enabled'] ) || true === $settings['date_enabled'] ) {

				$settings['meta_items'][] = array(
					'type'          => 'date',
					'selected_icon' => isset( $settings['date_selected_icon'] ) ? $settings['date_selected_icon'] : false,
					'prefix'        => isset( $settings['date_prefix'] ) ? $settings['date_prefix'] : false,
					'suffix'        => isset( $settings['date_suffix'] ) ? $settings['date_suffix'] : false,
				);

			}

			if ( ! isset( $settings['author_enabled'] ) || true === $settings['author_enabled'] ) {

				$settings['meta_items'][] = array(
					'type'          => 'author',
					'selected_icon' => isset( $settings['author_selected_icon'] ) ? $settings['author_selected_icon'] : false,
					'prefix'        => isset( $settings['author_prefix'] ) ? $settings['author_prefix'] : false,
					'suffix'        => isset( $settings['author_suffix'] ) ? $settings['author_suffix'] : false,
				);

			}

			if ( ! isset( $settings['comments_enabled'] ) || true === $settings['comments_enabled'] ) {

				$settings['meta_items'][] = array(
					'type'          => 'comments',
					'selected_icon' => isset( $settings['comments_selected_icon'] ) ? $settings['comments_selected_icon'] : false,
					'prefix'        => isset( $settings['comments_prefix'] ) ? $settings['comments_prefix'] : false,
					'suffix'        => isset( $settings['comments_suffix'] ) ? $settings['comments_suffix'] : false,
				);

			}

			$unset = array(
				'date_enabled',
				'date_selected_icon',
				'date_prefix',
				'date_suffix',
				'author_enabled',
				'author_selected_icon',
				'author_prefix',
				'author_suffix',
				'comments_enabled',
				'comments_selected_icon',
				'comments_prefix',
				'comments_suffix',
			);


			foreach ( $unset as $key ) {
				if ( isset( $settings[ $key ] ) ) {
					unset( $settings[ $key ] );
				}
			}

			return $settings;

		}

		/**
		 * Render meta block
		 *
		 * @return [type] [description]
		 */
		public function render_meta( $settings ) {

			$current_object = jet_engine()->listings->data->get_current_object();

			if ( ! $current_object || 'WP_Post' !== get_class( $current_object ) ) {
				return $this->wrong_source_notice();
			}

			$meta_items = isset( $settings['meta_items'] ) ? $settings['meta_items'] : array();

			if ( empty( $meta_items ) ) {
				return;
			}

			foreach ( $meta_items as $meta_item ) {
				$this->render_meta_item( $meta_item, $settings );
			}

		}

		/**
		 * Render single meta item
		 *
		 * @param  [type] $item     [description]
		 * @param  [type] $settings [description]
		 * @return [type]           [description]
		 */
		public function render_meta_item( $item, $settings ) {

			switch ( $item['type'] ) {
				case 'date':

					$this->render_date( $item, $settings );

					break;

				case 'comments':

					$this->render_comments( $item, $settings );

					break;

				case 'author':

					$this->render_author( $item, $settings );

					break;

				default:

					/**
					 * Render custom meta type.
					 */
					do_action( 'jet-engine/listings/dynamic-meta/render-type/' . $item['type'], $item, $settings, $this );

					break;

			}

		}

		/**
		 * Render post date meta item
		 *
		 * @param  [type] $item     [description]
		 * @param  [type] $settings [description]
		 * @return [type]           [description]
		 */
		public function render_date( $item, $settings ) {

			$this->open_item_wrap( 'date' );

			$this->render_icon( $item );
			$this->render_prefix( $item );

			$format = ! empty( $settings['date_format'] ) ? esc_attr( $settings['date_format'] ) : 'F j, Y';
			$date   = sprintf(
				'<time datetime="%1$s">%2$s</time>',
				get_the_date( 'c', get_the_ID() ),
				get_the_date( $format, get_the_ID() )
			);

			$link = ! empty( $settings['date_link'] ) ? esc_attr( $settings['date_link'] ) : 'archive';

			if ( 'no-link' === $link ) {
				printf( '<span class="%1$s__item-val">%2$s</span>', $this->get_name(), $date );
			} else {

				if ( 'archive' === $link ) {
					$url = get_month_link( get_the_date( 'Y', get_the_ID() ), get_the_date( 'm', get_the_ID() ) );
				} else {
					$url = get_permalink();
				}

				printf( '<a href="%3$s" class="%1$s__item-val">%2$s</a>', $this->get_name(), $date, $url );
			}

			$this->render_suffix( $item );
			$this->close_item_wrap( 'date' );

		}

		/**
		 * Render posts comments meta item
		 *
		 * @param  [type] $item     [description]
		 * @param  [type] $settings [description]
		 * @return [type]           [description]
		 */
		public function render_comments( $item, $settings ) {

			$this->open_item_wrap( 'comments' );

			$this->render_icon( $item );
			$this->render_prefix( $item );

			$link = ! empty( $settings['comments_link'] ) ? esc_attr( $settings['comments_link'] ) : 'single';
			$zero = ! empty( $settings['zero_comments_format'] ) ? esc_attr( $settings['zero_comments_format'] ) : 0;
			$one  = ! empty( $settings['one_comment_format'] ) ? esc_attr( $settings['one_comment_format'] ) : 1;
			$more = ! empty( $settings['more_comments_format'] ) ? esc_attr( $settings['more_comments_format'] ) : '%';

			$post_id = get_the_ID();

			$comments_num = get_comments_number( $post_id );
			$comments_num = absint( $comments_num );
			$result       = '';

			if ( ! $comments_num || 1 > $comments_num ) {
				$result = $zero;
			} elseif ( 1 === $comments_num ) {
				$result = $one;
			} else {
				$result = str_replace( '%', $comments_num, $more );
			}

			if ( 'no-link' === $link ) {
				printf( '<span class="%1$s__item-val">%2$s</span>', $this->get_name(), $result );
			} else {
				$url = get_comments_link();
				printf( '<a href="%3$s" class="%1$s__item-val">%2$s</a>', $this->get_name(), $result, $url );
			}

			$this->render_suffix( $item );

			$this->close_item_wrap( 'comments' );

		}

		/**
		 * Render post author meta item
		 *
		 * @param  [type] $item [description]
		 * @param  [type] $settings [description]
		 * @return [type]       [description]
		 */
		public function render_author( $item, $settings ) {

			$this->open_item_wrap( 'author' );

			$this->render_icon( $item );
			$this->render_prefix( $item );

			$link   = ! empty( $settings['author_link'] ) ? esc_attr( $settings['author_link'] ) : 'archive';
			$author = null;

			global $authordata;

			if ( $authordata ) {
				$author = get_the_author();
			} else {

				$post = get_post();

				if ( $post ) {
					$author    = get_the_author_meta( 'display_name', $post->post_author );
					$author_id = $post->post_author;
				}
			}

			if ( 'no-link' === $link ) {
				printf( '<span class="%1$s__item-val">%2$s</span>', $this->get_name(), $author );
			} else {

				if ( 'archive' === $link ) {
					$id  = isset( $author_id ) ? $author_id : get_the_author_meta( 'ID' );
					$url = get_author_posts_url( $id );
				} else {
					$url = get_permalink();
				}

				printf( '<a href="%3$s" class="%1$s__item-val">%2$s</a>', $this->get_name(), $author, $url );
			}

			$this->render_suffix( $item );

			$this->close_item_wrap( 'author' );

		}

		/**
		 * Render opening meta item div
		 *
		 * @param  [type] $meta_name [description]
		 * @return [type]       [description]
		 */
		public function open_item_wrap( $meta_name ) {
			printf(
				'<div class="%1$s__%2$s %1$s__item">',
				$this->get_name(),
				$meta_name
			);
		}

		/**
		 * Render closing meta item div
		 *
		 * @param  [type] $meta_name [description]
		 * @return [type]       [description]
		 */
		public function close_item_wrap( $meta_name ) {
			echo '</div>';
		}

		/**
		 * Render meta item prefix
		 *
		 * @param  [type] $prefix [description]
		 * @return [type]       [description]
		 */
		public function render_prefix( $item = array() ) {

			$prefix = ! empty( $item['prefix'] ) ? $item['prefix'] : '';

			if ( empty( $prefix ) ) {
				return;
			}

			printf(
				'<span class="%2$s__prefix">%1$s</span>',
				$prefix,
				$this->get_name()
			);
		}

		/**
		 * Render meta item suffix
		 *
		 * @param  [type] $suffix [description]
		 * @return [type]       [description]
		 */
		public function render_suffix( $item = array() ) {

			$suffix = ! empty( $item['suffix'] ) ? $item['suffix'] : '';

			if ( empty( $suffix ) ) {
				return;
			}

			printf(
				'<span class="%2$s__suffix">%1$s</span>',
				$suffix,
				$this->get_name()
			);
		}

		/**
		 * Render icon tag for passed class
		 *
		 * @param  [type] $item [description]
		 * @return [type]       [description]
		 */
		public function render_icon( $item = null ) {

			$icon          = ! empty( $item['icon'] ) ? $item['icon'] : false;
			$new_icon      = ! empty( $item['selected_icon'] ) ? $item['selected_icon'] : false;
			$new_icon_html = \Jet_Engine_Tools::render_icon( $new_icon, $this->get_name() . '__icon' );

			if ( $new_icon_html ) {
				echo $new_icon_html;
			} elseif ( $icon ) {
				printf(
					'<i class="%1$s %2$s__icon"></i>',
					$icon,
					$this->get_name()
				);
			}

		}

		/**
		 * Show notice if source is terms
		 */
		public function wrong_source_notice() {
			_e( 'Dynamic Meta widget allowed only for Posts listing source or in Post context', 'jet-engine' );
		}

		public function render() {

			$base_class = $this->get_name();
			$settings   = $this->get_settings();
			$layout     = ! empty( $settings['layout'] ) ? $settings['layout'] : 'inline';

			$classes = array(
				'jet-listing',
				$base_class,
				'meta-layout-' . $layout
			);

			if ( ! empty( $settings['className'] ) ) {
				$classes[] = esc_attr( $settings['className'] );
			}

			printf( '<div class="%1$s">', implode( ' ', $classes ) );

				do_action( 'jet-engine/listing/dynamic-terms/before-terms', $this );

				$this->render_meta( $settings );

				do_action( 'jet-engine/listing/dynamic-terms/after-terms', $this );

			echo '</div>';

		}

	}

}
