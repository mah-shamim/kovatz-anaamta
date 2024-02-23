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

if ( ! class_exists( 'Jet_Engine_Img_Gallery' ) ) {

	/**
	 * Define Jet_Engine_Img_Gallery class
	 */
	class Jet_Engine_Img_Gallery {

		/**
		 * Render images gallery as slider
		 *
		 * @param  array  $images [description]
		 * @param  array  $args   [description]
		 * @return [type]         [description]
		 */
		public static function slider( $images = array(), $args = array() ) {

			if ( empty( $images ) ) {
				return '';
			}

			if ( wp_doing_ajax() ) {
				jet_engine()->frontend->register_listing_deps();
			}

			wp_enqueue_script( 'jquery-slick' );

			jet_engine()->frontend->ensure_lib( 'imagesloaded' );
			jet_engine()->frontend->frontend_scripts();

			$args = apply_filters( 'jet-engine/gallery/slider/args', wp_parse_args( $args, array(
				'size'             => 'full',
				'lightbox'         => false,
				'slides_to_show'   => 1,
				'slides_to_show_t' => false,
				'slides_to_show_m' => false,
				'css_classes'      => [ 'jet-engine-gallery-slider' ],
				'lightbox_classes' => [ 'jet-engine-gallery-slider', 'jet-engine-gallery-lightbox' ],
			) ) );

			$slider_atts =  array(
				'slidesToShow'   => $args['slides_to_show'],
				'dots'           => false,
				'slidesToScroll' => 1,
				'adaptiveHeight' => true,
				'prevArrow'      => "<div class='prev-arrow jet-engine-arrow slick-arrow'><svg width='180' height='180' viewBox='0 0 180 180' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M119 47.3166C119 48.185 118.668 48.9532 118.003 49.6212L78.8385 89L118.003 128.379C118.668 129.047 119 129.815 119 130.683C119 131.552 118.668 132.32 118.003 132.988L113.021 137.998C112.356 138.666 111.592 139 110.729 139C109.865 139 109.101 138.666 108.436 137.998L61.9966 91.3046C61.3322 90.6366 61 89.8684 61 89C61 88.1316 61.3322 87.3634 61.9966 86.6954L108.436 40.002C109.101 39.334 109.865 39 110.729 39C111.592 39 112.356 39.334 113.021 40.002L118.003 45.012C118.668 45.68 119 46.4482 119 47.3166Z' fill='black'/></svg></div>",
				'nextArrow'      => "<div class='next-arrow jet-engine-arrow slick-arrow'><svg width='180' height='180' viewBox='0 0 180 180' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M61 48.3166C61 49.185 61.3322 49.9532 61.9966 50.6212L101.162 90L61.9966 129.379C61.3322 130.047 61 130.815 61 131.683C61 132.552 61.3322 133.32 61.9966 133.988L66.9794 138.998C67.6438 139.666 68.4078 140 69.2715 140C70.1352 140 70.8992 139.666 71.5636 138.998L118.003 92.3046C118.668 91.6366 119 90.8684 119 90C119 89.1316 118.668 88.3634 118.003 87.6954L71.5636 41.002C70.8992 40.334 70.1352 40 69.2715 40C68.4078 40 67.6438 40.334 66.9794 41.002L61.9966 46.012C61.3322 46.68 61 47.4482 61 48.3166Z' fill='black'/></svg></div>",
				'rtl'            => is_rtl(),
			);

			$mobile_settings = apply_filters( 'jet-engine/gallery/slider/mobile-settings', array(
				'slides_to_show_t' => 1025,
				'slides_to_show_m' => 768,
			) );

			foreach ( $mobile_settings as $key => $breakpoint ) {

				if ( ! empty( $args[ $key ] ) ) {

					if ( ! isset( $slider_atts['responsive'] ) ) {
						$slider_atts['responsive'] = array();
					}

					$slider_atts['responsive'][] = array(
						'breakpoint' => $breakpoint,
						'settings'   => array(
							'slidesToShow' => $args[ $key ],
						),
					);

				}
			}

			$slider_atts = apply_filters( 'jet-engine/gallery/slider/atts', $slider_atts );
			$slider_atts = htmlspecialchars( json_encode( $slider_atts ) );

			echo '<div class="' . implode( ' ', $args['css_classes'] ) . '" data-atts="' . $slider_atts . '">';

			$gallery_id = self::get_gallery_id();

			foreach ( $images as $img_data ) {

				$img_data = self::get_img_data( $img_data, $args );
				$img_id   = $img_data['id'];
				$img_url  = $img_data['url'];
				$img_full = $img_data['full'];

				echo '<div class="jet-engine-gallery-slider__item">';

				if ( $args['lightbox'] ) {

					$lightbox_attr = array(
						'href'  => $img_full,
						'class' => array( 'jet-engine-gallery-slider__item-wrap', 'jet-engine-gallery-item-wrap', 'is-lightbox' ),
					);

					$lightbox_attr = apply_filters( 'jet-engine/gallery/lightbox-attr', $lightbox_attr, $img_data, $gallery_id );

					echo '<a ' . Jet_Engine_Tools::get_attr_string( $lightbox_attr ) . '>';
				} else {
					echo '<span class="jet-engine-gallery-slider__item-wrap jet-engine-gallery-item-wrap">';
				}

				$alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

				echo '<img src="' . $img_url . '" alt="' . $alt . '" class="jet-engine-gallery-slider__item-img">';

				if ( $args['lightbox'] ) {
					echo '</a>';
				} else {
					echo '</span>';
				}

				echo '</div>';

			}

			echo '</div>';

		}

		/**
		 * Ensure slider JS is enqueued.
		 *
		 * @param  string $content
		 * @return string
		 */
		public static function ensure_slider_js( $content ) {
			ob_start();

			jet_engine()->frontend->register_listing_deps();

			wp_scripts()->done[] = 'jquery';
			wp_scripts()->print_scripts( 'jquery-slick' );
			wp_scripts()->print_scripts( 'imagesloaded' );

			return $content . ob_get_clean();
		}

		/**
		 * Render images gallery as grid
		 *
		 * @param  array   $images   [description]
		 * @param  string  $size     [description]
		 * @param  boolean $lightbox [description]
		 * @return string
		 */
		public static function grid( $images = array(), $args = array() ) {

			if ( empty( $images ) ) {
				return '';
			}

			$args = apply_filters( 'jet-engine/gallery/grid/args', wp_parse_args( $args, array(
				'size'        => 'full',
				'lightbox'    => false,
				'cols_desk'   => 3,
				'cols_tablet' => 3,
				'cols_mobile' => 1,
				'css_classes' => [ 'jet-engine-gallery-grid' ],
			) ) );

			ob_start();

			$classes = array_merge( $args['css_classes'], array(
				'grid-col-desk-' . $args['cols_desk'],
				'grid-col-tablet-' . $args['cols_tablet'],
				'grid-col-mobile-' . $args['cols_mobile'],
			) );

			$classes = implode( ' ', $classes );

			echo '<div class="' . $classes . '">';

			$gallery_id = self::get_gallery_id();

			foreach ( $images as $img_data ) {

				$img_data = self::get_img_data( $img_data, $args );
				$img_id   = $img_data['id'];
				$img_url  = $img_data['url'];
				$img_full = $img_data['full'];

				echo '<div class="jet-engine-gallery-grid__item">';

				if ( $args['lightbox'] ) {

					$lightbox_attr = array(
						'href'  => $img_full,
						'class' => array( 'jet-engine-gallery-grid__item-wrap', 'jet-engine-gallery-item-wrap', 'is-lightbox' ),
					);

					$lightbox_attr = apply_filters( 'jet-engine/gallery/lightbox-attr', $lightbox_attr, $img_data, $gallery_id );

					echo '<a ' . Jet_Engine_Tools::get_attr_string( $lightbox_attr ) . '>';
				} else {
					echo '<span class="jet-engine-gallery-grid__item-wrap jet-engine-gallery-item-wrap">';
				}

				$alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

				echo '<img src="' . $img_url . '" alt="' . $alt . '" class="jet-engine-gallery-grid__item-img">';

				if ( $args['lightbox'] ) {
					echo '</a>';
				} else {
					echo '</span>';
				}

				echo '</div>';

			}

			echo '</div>';

			return ob_get_clean();
		}

		public static function get_img_data( $img_data = null, $args = array() ) {

			$result = Jet_Engine_Tools::get_attachment_image_data_array( $img_data );

			$result['full'] = $result['url'];

			if ( 'full' !== $args['size'] ) {
				$result['url'] = wp_get_attachment_image_url( $result['id'], $args['size'] );
			}

			return $result;
		}

		public static function get_full_img_sizes( $img_id = null ) {

			$result  = array();
			$img_src = wp_get_attachment_image_src( $img_id, 'full' );

			$result['width'] = $img_src[1];
			$result['height'] = $img_src[2];

			return $result;
		}

		/**
		 * Returns random ID for gallery
		 *
		 * @return [type] [description]
		 */
		public static function get_gallery_id() {
			return 'gallery_' . rand( 1000, 9999 );
		}
	}

}