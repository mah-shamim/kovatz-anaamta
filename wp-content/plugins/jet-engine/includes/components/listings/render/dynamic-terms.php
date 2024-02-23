<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Render_Dynamic_Terms' ) ) {

	class Jet_Engine_Render_Dynamic_Terms extends Jet_Engine_Render_Base {

		private $show_field = true;

		public function get_name() {
			return 'jet-listing-dynamic-terms';
		}

		public function default_settings() {
			return array(
				'object_context' => 'default_object',
			);
		}

		/**
		 * Render taxonomies list
		 *
		 * @return [type] [description]
		 */
		public function render_taxonomies_list( $settings ) {

			$tax = isset( $settings['from_tax'] ) ? esc_attr( $settings['from_tax'] ) : false;

			if ( ! $tax ) {
				return;
			}

			$object_context = $this->get( 'object_context' );
			$object         = jet_engine()->listings->data->get_object_by_context( $object_context );

			if ( ! $object ) {
				$object = jet_engine()->listings->data->get_current_object();
			}

			if ( ! $object || ! is_object( $object ) ) {
				return;
			}

			$class = get_class( $object );

			if ( is_a( $object, 'WC_Product' ) ) {
				$class = 'WC_Product';
			}

			switch ( $class ) {
				case 'WP_Post':
				case 'WC_Product':

					$args      = array();
					$args_keys = array( 'orderby', 'order' );

					foreach ( $args_keys as $key ) {

						if ( empty( $settings[ $key ] ) ) {
							continue;
						}

						$args[ $key ] = $settings[ $key ];
					}

					$post_id = jet_engine()->listings->data->get_current_object_id( $object );

					$terms = wp_get_post_terms( $post_id, $tax, $args );
					$terms = apply_filters( 'jet-engine/listings/dynamic-terms/items', $terms, $post_id, $settings, $this );

					break;

				case 'WP_Term':
					$terms = array( $object );
					break;
				
				default:
					// Current object can`t be used with this widget. Only instances of WP_Post or WP_Term classes are allowed.
					return;
			}

			

			if ( empty( $terms ) || is_wp_error( $terms ) ) {

				if ( ! empty( $settings['hide_if_empty'] ) ) {
					$this->show_field = false;
				}

				return;
			}

			if ( ! isset( $settings['terms_delimiter'] ) ) {
				$settings['terms_delimiter'] = ', ';
			}

			$show_all = isset( $settings['show_all_terms'] ) ? $settings['show_all_terms'] : 'yes';
			$show_all = filter_var( $show_all, FILTER_VALIDATE_BOOLEAN );

			if ( ! $show_all ) {
				$num   = isset( $settings['terms_num'] ) ? absint( $settings['terms_num'] ) : 1;
				$terms = array_slice( $terms, 0, $num );
			}

			$add_delimiter = false;
			$delimiter     = '';

			if ( ! empty( $settings['terms_delimiter'] ) ) {
				$add_delimiter = true;
			}

			$this->render_icon( $settings );

			if ( ! empty( $settings['terms_prefix'] ) ) {
				printf( '<span class="%2$s__prefix">%1$s</span>', $settings['terms_prefix'], $this->get_name() );
			}

			$item_format = '<a href="%1$s" class="%3$s__link">%2$s</a>';
			$is_linked   = isset( $settings['terms_linked'] ) ? $settings['terms_linked'] : true;
			$is_linked   = filter_var( $is_linked, FILTER_VALIDATE_BOOLEAN );

			if ( ! $is_linked ) {
				$item_format = '<span class="%3$s__link">%2$s</span>';
			}

			foreach ( $terms as $term ) {

				if ( $add_delimiter ) {
					echo $delimiter;
					$delimiter = sprintf(
						'<span class="%2$s__delimiter">%1$s</span> ',
						$settings['terms_delimiter'],
						$this->get_name()
					);
				}

				if ( ! $term || ( is_array( $term ) && ! empty( $term['invalid_taxonomy'] ) ) ) {
					echo 'Can\'t retrieve term. In case if you changed taxonomy slug for this term, please update widget settings to use new taxonomy slug.';
					return;
				}

				/**
				 * Filter term name befor printing
				 *
				 * @var string
				 */
				$name = apply_filters( 'jet-engine/listings/dynamic-terms/term-name', $term->name, $term, $this );
				$link = get_term_link( $term, $tax );

				if ( is_wp_error( $link ) ) {
					echo 'Can\'t retrieve term link. In case if you changed taxonomy slug for this term, please update widget settings to use new taxonomy slug.';
					return;
				}

				printf( $item_format, get_term_link( $term, $tax ), $name, $this->get_name() );

			}

			if ( ! empty( $settings['terms_suffix'] ) ) {
				printf( '<span class="%2$s__suffix">%1$s</span>', $settings['terms_suffix'], $this->get_name() );
			}

		}

		public function render_icon( $settings ) {

			$icon          = ! empty( $settings['terms_icon'] ) ? $settings['terms_icon'] : false;;
			$new_icon      = ! empty( $settings['selected_terms_icon'] ) ? $settings['selected_terms_icon'] : false;
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

		public function render() {

			$base_class = $this->get_name();
			$settings   = $this->get_settings();

			$classes = array(
				'jet-listing',
				$base_class,
			);

			if ( ! empty( $settings['className'] ) ) {
				$classes[] = esc_attr( $settings['className'] );
			}

			ob_start();

			printf( '<div class="%1$s">', implode( ' ', $classes ) );

				do_action( 'jet-engine/listing/dynamic-terms/before-terms', $this );

				$this->render_taxonomies_list( $settings );

				do_action( 'jet-engine/listing/dynamic-terms/after-terms', $this );

			echo '</div>';

			$content = ob_get_clean();

			if ( $this->show_field ) {
				echo $content;
			}
		}

	}

}
