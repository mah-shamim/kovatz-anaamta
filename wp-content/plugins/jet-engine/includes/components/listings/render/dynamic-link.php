<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Render_Dynamic_Link' ) ) {

	class Jet_Engine_Render_Dynamic_Link extends Jet_Engine_Render_Base {

		private $show_field = true;
		private $is_delete_link = false;
		private $raw_url = null;

		public function get_name() {
			return 'jet-listing-dynamic-link';
		}

		/**
		 * Set value for show_field trigger
		 */
		public function set_field_visibility( $show = true ) {
			$this->show_field = $show;
		}

		/**
		 * Get delete post URL
		 */
		public function get_delete_url( $settings ) {

			$redirect = null;

			if ( ! empty( $settings['delete_link_redirect'] ) ) {
				$redirect = jet_engine()->listings->macros->do_macros( $settings['delete_link_redirect'] );
				$redirect = esc_url( $redirect );
			}

			if ( empty( $redirect ) ) {
				$redirect = home_url( '/' );
			}

			$type = ! empty( $settings['delete_link_type'] ) ? $settings['delete_link_type'] : 'trash';

			return jet_engine()->listings->delete_post->get_delete_url( apply_filters( 'jet-engine/listings/dynamic-link/delete-url-args', array(
				'type' => $type,
				'redirect' => $redirect,
			) ) );

		}

		public function get_link_url( $settings ) {
			
			$source = ! empty( $settings['dynamic_link_source'] ) ? $settings['dynamic_link_source'] : '_permalink';
			$custom = ! empty( $settings['dynamic_link_source_custom'] ) ? $settings['dynamic_link_source_custom'] : '';

			$url = apply_filters(
				'jet-engine/listings/dynamic-link/custom-url',
				false,
				$settings
			);

			$object_context = ! empty( $settings['object_context'] ) ? $settings['object_context'] : false;

			if ( ! $url ) {
				if ( $custom ) {
					$url = jet_engine()->listings->data->get_meta_by_context( $custom, $object_context );
				} elseif ( '_permalink' === $source ) {

					$url = jet_engine()->listings->data->get_current_object_permalink(
						jet_engine()->listings->data->get_object_by_context( $object_context )
					);

				} elseif ( 'delete_post_link' === $source ) {
					$this->is_delete_link = true;
					$url = $this->get_delete_url( $settings );
				} elseif ( 'options_page' === $source ) {
					$option = ! empty( $settings['dynamic_link_option'] ) ? $settings['dynamic_link_option'] : false;
					$url    = jet_engine()->listings->data->get_option( $option );
				} elseif ( $source ) {
					$url = jet_engine()->listings->data->get_meta_by_context( $source, $object_context );
				}
			}

			if ( is_array( $url ) ) {
				$url = $url[0];
				$url = get_permalink( $url[0] );
			}

			if ( is_object( $url ) && $url instanceof WP_Post ) {
				$url = get_permalink( $url->ID );
			}

			$this->raw_url = $url;

			$url = $this->maybe_add_query_args( $url, $settings );

			if ( ! empty( $settings['url_prefix'] ) ) {
				$url = esc_attr( $settings['url_prefix'] ) . $url;
			}

			if ( ! empty( $settings['url_anchor'] ) ) {

				$url_anchor = $settings['url_anchor'];

				$macros_context = jet_engine()->listings->macros->get_macros_context();

				jet_engine()->listings->macros->set_macros_context( $object_context );

				$url_anchor = jet_engine()->listings->macros->do_macros( $url_anchor );

				// Reset macros context to initial.
				jet_engine()->listings->macros->set_macros_context( $macros_context );

				$url = $url . '#' . esc_attr( $url_anchor );
			}

			return $url;

		}

		/**
		 * Render link tag
		 *
		 * @param  [type] $settings   [description]
		 * @param  [type] $base_class [description]
		 * @return [type]             [description]
		 */
		public function render_link( $settings, $base_class ) {

			$format = '<a href="%1$s" class="%2$s__link"%5$s%6$s%7$s>%3$s%4$s</a>';

			$pre_render_link = apply_filters( 'jet-engine/listings/dynamic-link/pre-render-link', false, $settings, $base_class, $this );

			if ( $pre_render_link ) {
				echo $pre_render_link;
				return;
			}

			$url   = $this->get_link_url( $settings );
			$label = $this->get_link_label( $settings, $base_class, $this->raw_url );
			$icon  = $this->get_link_icon( $settings, $base_class );

			if ( is_wp_error( $url ) ) {
				echo $url->get_error_message();
				return;
			}

			$open_in_new = isset( $settings['open_in_new'] ) ? $settings['open_in_new'] : '';
			$rel_attr    = isset( $settings['rel_attr'] ) ? esc_attr( $settings['rel_attr'] ) : '';
			$rel         = '';
			$target      = '';

			if ( $rel_attr ) {
				$rel = sprintf( ' rel="%s"', $rel_attr );
			}

			if ( $open_in_new ) {
				$target = ' target="_blank"';
			}

			if ( ! empty( $settings['hide_if_empty'] ) && empty( $this->raw_url ) ) {
				$this->show_field = false;
				return;
			}

			$custom_attrs = '';

			if ( $this->is_delete_link ) {
				$message = ! empty( $settings['delete_link_dialog'] ) ? $settings['delete_link_dialog'] : '';
				$custom_attrs .= ' data-delete-link="1"';
				$custom_attrs .= ' data-delete-message="' . $message . '"';
			}

			if ( ! empty( $settings['aria_label_attr'] ) ) {
				$custom_attrs .= ' aria-label="' . esc_attr( $settings['aria_label_attr'] ) . '"';
			}

			$custom_attrs = apply_filters( 'jet-engine/listings/dynamic-link/custom-attrs', $custom_attrs, $this );

			printf( $format, $url, $base_class, $icon, $label, $rel, $target, $custom_attrs );

		}

		public function get_link_label( $settings, $base_class, $url ) {

			$label = ! Jet_Engine_Tools::is_empty( $settings['link_label'] ) ? $settings['link_label'] : false;

			if ( $label ) {

				$macros_context = jet_engine()->listings->macros->get_macros_context();
				$object_context = ! empty( $settings['object_context'] ) ? $settings['object_context'] : false;

				jet_engine()->listings->macros->set_macros_context( $object_context );

				$format = '<span class="%1$s__label">%2$s</span>';

				// If optimized DOM tweak is active and link not has an icon - we can use plain text
				if ( $this->prevent_wrap() 
					&& empty( $settings['link_icon'] ) 
					&& ( empty( $settings['selected_link_icon'] ) 
						|| empty( $settings['selected_link_icon']['value'] ) 
					)
				) {
					$format = '%2$s';
				}

				$label = jet_engine()->listings->macros->do_macros( $label, $url );
				$label = sprintf( $format, $base_class, $label );

				// Reset macros context to initial.
				jet_engine()->listings->macros->set_macros_context( $macros_context );
			}

			return $label;

		}

		public function get_link_icon( $settings, $base_class ) {

			$icon     = ! empty( $settings['link_icon'] ) ? $settings['link_icon'] : false;
			$new_icon = ! empty( $settings['selected_link_icon'] ) ? $settings['selected_link_icon'] : false;

			$new_icon_html = \Jet_Engine_Tools::render_icon( $new_icon, $base_class . '__icon' );

			if ( $new_icon_html ) {
				$icon = $new_icon_html;
			} elseif ( $icon ) {
				$icon = sprintf( '<i class="%1$s__icon %2$s"></i>', $base_class, $icon );
			}

			return $icon;

		}

		/**
		 * Maybe add query arguments to URL string
		 *
		 * @return [type] [description]
		 */
		public function maybe_add_query_args( $url = null, $settings = array() ) {
			return Jet_Engine_Tools::add_query_args_by_settings( $url, $settings );
		}

		public function render() {

			$base_class = $this->get_name();
			$settings   = $this->get_settings();
			$tag        = isset( $settings['link_wrapper_tag'] ) ? $settings['link_wrapper_tag'] : 'div';
			$tag        = Jet_Engine_Tools::sanitize_html_tag( $tag );

			ob_start();

			if ( ! $this->prevent_wrap() ) {
				printf( '<%1$s class="%2$s">', $tag, implode( ' ', $this->get_wrapper_classes() ) );
			}

			do_action( 'jet-engine/listing/dynamic-link/before-field', $this );

			$this->render_link( $settings, $base_class );

			do_action( 'jet-engine/listing/dynamic-link/after-field', $this );

			if ( ! $this->prevent_wrap() ) {
				printf( '</%s>', $tag );
			}

			$content = ob_get_clean();

			if ( $this->show_field ) {
				echo $content;
			}

		}

		public function is_delete_link() {
			return $this->is_delete_link;
		}

	}

}
