<?php
/**
 * Class for the building ui-radio elements.
 *
 * @package    Cherry_Framework
 * @subpackage Class
 * @author     Cherry Team <support@cxframework.com>
 * @copyright  Copyright (c) 2012 - 2015, Cherry Team
 * @link       http://www.cxframework.com/
 * @license    http://www.gnu.org/licenses/gpl-3.0.en.html
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'CX_Control_Radio' ) ) {

	/**
	 * Class for the building CX_Control_Radio elements.
	 */
	class CX_Control_Radio extends CX_Controls_Base {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $defaults_settings = array(
			'id'       => 'cx-ui-radio-id',
			'name'     => 'cx-ui-radio-name',
			'value'    => 'radio-2',
			'required' => false,
			'options'  => array(
				'radio-1' => array(
					'label'   => 'Radio 1',
					'img_src' => '',
				),
				'radio-2' => array(
					'label'   => 'Radio 2',
					'img_src' => '',
				),
				'radio-3' => array(
					'label'   => 'Radio 3',
					'img_src' => '',
				),
			),
			'allow_custom_value' => false,
			'layout' => 'vertical', // `vertical` or `horizontal`
			'label'  => '',
			'class'  => '',
		);

		/**
		 * Render html UI_Radio.
		 *
		 * @since 1.0.0
		 */
		public function render() {

			$html   = '';
			$layout = ! empty( $this->settings['layout'] ) ? $this->settings['layout'] : 'vertical';
			$class  = implode( ' ',
				array(
					$this->settings['class'],
				)
			);

			if ( isset( $this->settings['options_callback'] ) ) {
				$this->settings['options'] = call_user_func( $this->settings['options_callback'] );
			}

			$html .= '<div class="cx-ui-container ' . esc_attr( $class ) . '" >';
				if ( $this->settings['options'] && ! empty( $this->settings['options'] ) && is_array( $this->settings['options'] ) ) {
					if ( '' !== $this->settings['label'] ) {
						$html .= '<label class="cx-label" for="' . esc_attr( $this->settings['id'] ) . '">' . wp_kses_post( $this->settings['label'] ) . '</label> ';
					}
					$html .= '<div class="cx-radio-group cx-check-radio-group--' . esc_attr( $layout ) . '">';
						foreach ( $this->settings['options'] as $option => $option_value ) {

							$checked    = $option == $this->settings['value'] ? ' checked' : '';
							$radio_id   = $this->settings['id'] . '-' . $option;
							$img        = isset( $option_value['img_src'] ) && ! empty( $option_value['img_src'] ) ? '<img src="' . esc_url( $option_value['img_src'] ) . '" alt="' . esc_html( $option_value['label'] ) . '">' : '<span class="cx-radio-item"><i></i></span>';
							$class_box  = isset( $option_value['img_src'] ) && ! empty( $option_value['img_src'] ) ? 'cx-radio-img' : 'cx-radio-item' ;

							$html .= '<div class="' . $class_box . '">';
							$html .= '<input type="radio" id="' . esc_attr( $radio_id ) . '" class="cx-radio-input" name="' . esc_attr( $this->settings['name'] ) . '" ' . checked( $option, $this->settings['value'], false ) . ' value="' . esc_attr( $option ) . '"/>';
							$label_content = $img . $option_value['label'];
							$html .= '<label for="' . esc_attr( $radio_id ) . '"><span class="cx-lable-content">' . $label_content . '</span></label> ';
							$html .= '</div>';
						}

						if ( $this->settings['allow_custom_value'] ) {

							$custom_value = ! in_array( $this->settings['value'], array_keys( $this->settings['options'] ) ) ? $this->settings['value'] : '';
							$checked      = ( $custom_value === $this->settings['value'] && '' !== $custom_value ) ? ' checked' : '';

							$html .= '<div class="cx-radio-item">';
								$html .= '<label>';
									$html .= '<input type="radio" class="cx-radio-input" name="' . esc_attr( $this->settings['name'] ) . '"' . $checked . ' value="' . esc_attr( $custom_value ) . '"/>';
									$html .= '<span class="cx-radio-item"><i></i></span><input type="text" class="cx-radio-custom-value cx-ui-text" value="' . esc_attr( $custom_value ) . '">';
								$html .= '</label>';
							$html .= '</div>';
						}

						$html .= '<div class="clear"></div>';
					$html .= '</div>';
				}
			$html .= '</div>';

			return $html;
		}

		/**
		 * Enqueue javascript and stylesheet UI_Radio.
		 *
		 * @since 1.0.0
		 */
		public static function enqueue_assets() {
			wp_enqueue_style(
				'ui-radio',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-radio/assets/min/ui-radio.min.css', Cherry_UI_Elements::$module_path ) ),
				array(),
				Cherry_UI_Elements::$core_version,
				'all'
			);

			wp_enqueue_script(
				'ui-radio-min',
				esc_url( Cherry_Core::base_url( 'inc/ui-elements/ui-radio/assets/min/ui-radio.min.js', Cherry_UI_Elements::$module_path ) ),
				array( 'jquery' ),
				Cherry_UI_Elements::$core_version,
				true
			);
		}
	}
}
