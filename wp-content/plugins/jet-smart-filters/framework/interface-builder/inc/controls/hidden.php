<?php
/**
 * Class for the building ui-hidden elements.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'CX_Control_Hidden' ) ) {

	/**
	 * Class for the building ui-hidden elements.
	 */
	class CX_Control_Hidden extends CX_Controls_Base {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $defaults_settings = array(
			'id'    => 'cx-ui-input-id',
			'name'  => 'cx-ui-input-name',
			'value' => '',
			'label' => '',
			'class' => '',
		);

		/**
		 * Render html UI_Hidden.
		 *
		 * @since 1.0.0
		 */
		public function render() {
			$html = '';

			$classes = array( 'cx-ui-container', 'hide' );

			if ( ! empty( $this->settings['class'] ) ) {
				$classes[] = $this->settings['class'];
			}

			$class = implode( ' ', $classes );

			$html .= '<div class="' . esc_attr( $class ) . '">';
				$html .= '<input type="hidden" id="' . esc_attr( $this->settings['id'] ) . '" class="cx-ui-hidden" name="' . esc_attr( $this->settings['name'] ) . '" value="' . esc_html( $this->settings['value'] ) . '">';
			$html .= '</div>';
			return $html;
		}
	}
}
