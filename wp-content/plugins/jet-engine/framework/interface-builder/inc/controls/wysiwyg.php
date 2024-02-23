<?php
/**
 * Class for the building ui-wysiwyg elements
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'CX_Control_Wysiwyg' ) ) {

	/**
	 * Class for the building CX_Control_Wysiwyg elements.
	 */
	class CX_Control_Wysiwyg extends CX_Controls_Base {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $defaults_settings = array(
			'id'          => 'cx-ui-wysiwyg-id',
			'name'        => 'cx-ui-wysiwyg-name',
			'value'       => '',
			'placeholder' => '',
			'rows'        => '20',
			'cols'        => '20',
			'label'       => '',
			'class'       => '',
		);

		/**
		 * Register control dependencies
		 *
		 * @return void
		 */
		public function register_depends() {
			wp_enqueue_editor();
			wp_enqueue_media();

			static $is_first = true;

			if ( $is_first ) {
				$editor_id = 'cx_wysiwyg';

				$settings = _WP_Editors::parse_settings( $editor_id, array() );

				_WP_Editors::editor_settings( $editor_id, $settings );

				$is_first = false;
			}
		}

		/**
		 * Render html UI_Textarea.
		 *
		 * @since 1.0.0
		 */
		public function render() {

			$html = '';
			$class = implode( ' ',
				array(
					$this->settings['class'],
				)
			);

			$html .= '<div class="cx-ui-container ' . esc_attr( $class ) . '">';

				$editor_id  = str_replace( array( '_', '-' ), '', strtolower( $this->settings['id'] ) );
				$editor_id .= $this->get_rand_str();

				if ( '' !== $this->settings['label'] ) {
					$html .= '<label class="cx-label" for="' . esc_attr( $editor_id )  .'">' . wp_kses_post( $this->settings['label'] ) . '</label>';
				}

				$html .= sprintf( '<textarea id="%1$s" class="cx-ui-wysiwyg wp-editor-area" name="%2$s" rows="%3$s">%4$s</textarea>',
					esc_attr( $editor_id ),
					esc_attr( $this->settings['name'] ),
					esc_attr( $this->settings['rows'] ),
					esc_textarea( $this->settings['value'] )
				);

			$html .= '</div>';

			return $html;
		}

		/**
		 * Get random string
		 *
		 * @return string
		 */
		public function get_rand_str() {

			$letters = array( 'a', 'b', 'c', 'd', 'e', 'f', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z' );

			$res = '';

			for ( $i = 0; $i < 4; $i++ ) {
				$index = rand( 0, count( $letters ) - 1 );
				$res  .= isset( $letters[ $index ] ) ? $letters[ $index ] : '';
			}

			return $res;

		}

	}
}
