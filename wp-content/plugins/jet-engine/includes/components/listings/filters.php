<?php
/**
 * Filters class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Listings_Filters' ) ) {

	/**
	 * Define Jet_Engine_Listings_Filters class
	 */
	class Jet_Engine_Listings_Filters {

		/**
		 * Return available macros list
		 *
		 * @return [type] [description]
		 */
		public function get_all() {
			return apply_filters( 'jet-engine/listings/filters-list', array(
				'img_url_by_id'  => array(
					'cb'   => array( $this, 'get_img_url' ),
					'args' => 'full',
				),
				'img_alt_by_id' => array(
					'cb'   => array( $this, 'get_img_alt' ),
					'args' => false,
				),
				'file_url_by_id'  => array(
					'cb'   => array( $this, 'get_file_url' ),
					'args' => 'full',
				),
				'post_url_by_id' => array(
					'cb'   => array( $this, 'get_post_url' ),
					'args' => false,
				),
				'post_title_by_id' => array(
					'cb'   => array( $this, 'get_post_title' ),
					'args' => false,
				),
				'post_titles_by_ids' => array(
					'cb'   => 'jet_engine_render_post_titles',
					'args' => false,
				),
				'post_link_by_id' => array(
					'cb'   => array( $this, 'get_post_link' ),
					'args' => false,
				),
				'img_gallery_grid' => array(
					'cb'   => array( $this, 'img_gallery_grid' ),
					'args' => 'full',
				),
				'render_checkbox' => array(
					'cb'   => 'jet_engine_render_checkbox_values',
					'args' => false,
				),
				'render_acf_checkbox' => array(
					'cb'   => 'jet_engine_render_acf_checkbox_values',
					'args' => false,
				),
				'embed_url' => array(
					'cb'   => 'wp_oembed_get',
					'args' => false,
				),
				'format_date' => array(
					'cb'   => array( $this, 'format_date' ),
					'args' => false,
				),
				'term_title_by_id' => array(
					'cb'   => 'jet_engine_get_term_title',
					'args' => false,
				),
				'term_titles_by_ids' => array(
					'cb'   => 'jet_engine_get_term_titles',
					'args' => false,
				),
			) );
		}

		/**
		 * Find filters in sring and run apropriate function
		 *
		 * @param  [type] $value  [description]
		 * @param  [type] $filter [description]
		 * @return [type]         [description]
		 */
		public function apply_filters( $value = null, $filter = null ) {

			if ( ! $value ) {
				return null;
			}

			$filters = $this->get_all();

			if ( ! $filter ) {
				return $value;
			}

			preg_match( '/([a-zA-Z0-9_-]+)(\([a-zA-Z0-9\,\.\:\/\s_-]+\))?/', $filter, $filter_data );

			if ( empty( $filter_data ) ) {
				return $value;
			}

			$filter_name = isset( $filter_data[1] ) ? $filter_data[1] : false;
			$filter_arg  = isset( $filter_data[2] ) ? trim( $filter_data[2], '()' ) : false;

			if ( ! isset( $filters[ $filter_name ] ) ) {
				return $value;
			}

			$_filter = $filters[ $filter_name ];

			if ( ! $filter_arg ) {
				$filter_arg = $_filter['args'];
			}

			return call_user_func_array( $_filter['cb'], array_filter( array( $value, $filter_arg ) ) );

		}

		/**
		 * Format date
		 *
		 * @param  int|string $date
		 * @param  string     $date_format
		 * @return string
		 */
		public function format_date( $date = null, $date_format = '' ) {

			if ( ! Jet_Engine_Tools::is_valid_timestamp( $date ) ) {
				$date = strtotime( $date );
			}

			$format = ! empty( $date_format ) ? $date_format : get_option( 'date_format', 'F j, Y' );

			return date_i18n( $format, $date );
		}

		/**
		 * Render images gallery as grid.
		 *
		 * @return [type] [description]
		 */
		public function img_gallery_grid( $img_ids = null, $args = array() ) {

			if ( ! $img_ids ) {
				return null;
			}

			if ( ! is_array( $img_ids ) ) {
				$img_ids = explode( ',', $img_ids );
				$img_ids = array_map( 'trim', $img_ids );
			}

			if ( empty( $img_ids ) ) {
				return null;
			}

			// Ensure gallery class is included
			jet_engine_get_gallery();

			return Jet_Engine_Img_Gallery::grid( $img_ids, $args );

		}

		/**
		 * Render images gallery as slider.
		 *
		 * @return [type] [description]
		 */
		public function img_gallery_slider( $img_ids = null, $args = array() ) {

			if ( ! $img_ids ) {
				return null;
			}

			if ( ! is_array( $img_ids ) ) {
				$img_ids = explode( ',', $img_ids );
				$img_ids = array_map( 'trim', $img_ids );
			}

			if ( empty( $img_ids ) ) {
				return null;
			}

			// Ensure gallery class is included
			jet_engine_get_gallery();

			add_filter(
				'jet-engine/compatibility/popup-package/the_content',
				array( 'Jet_Engine_Img_Gallery', 'ensure_slider_js' )
			);

			ob_start();
			Jet_Engine_Img_Gallery::slider( $img_ids, $args );
			return ob_get_clean();

		}

		/**
		 * Returns image url by ID
		 *
		 * @param  int    $img_id
		 * @param  string $size
		 * @return string
		 */
		public function get_img_url( $img_id, $size ) {
			return wp_get_attachment_image_url( $img_id, $size );
		}

		/**
		 * Returns image alt by ID
		 *
		 * @param  int $img_id
		 * @return string
		 */
		public function get_img_alt( $img_id ) {
			if ( ! $img_id ) {
				return '';
			}

			$alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

			if ( ! $alt ) {
				$attachment = get_post( $img_id );

				$alt = $attachment->post_excerpt;

				if ( ! $alt ) {
					$alt = $attachment->post_title;
				}
			}

			return $alt;
		}

		/**
		 * Returns attachment file URL by attachment ID
		 * @param  int $attachment_id
		 * @return string
		 */
		public function get_file_url( $attachment_id ) {
			return wp_get_attachment_url( $attachment_id );
		}

		/**
		 * Returns Post URL by ID
		 *
		 * @param  int $post_id
		 * @return string
		 */
		public function get_post_url( $post_id ) {
			return get_permalink( $post_id );
		}

		/**
		 * Returns Post URL by ID
		 *
		 * @param  int $post_id
		 * @return string
		 */
		public function get_post_title( $post_id ) {
			return get_the_title( $post_id );
		}

		/**
		 * Returns Post URL by ID
		 *
		 * @param  int $post_id
		 * @return string
		 */
		public function get_post_link( $post_id ) {
			return sprintf(
				'<a href="%1$s">%2$s</a>',
				$this->get_post_url( $post_id ),
				$this->get_post_title( $post_id )
			);
		}

	}

}
