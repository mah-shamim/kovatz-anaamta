<?php
/**
 * Class for the building checkbox elements.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'CX_Control_Checkbox_Raw' ) ) {

	/**
	 * Class for the building CX_Control_Checkbox elements.
	 */
	class CX_Control_Checkbox_Raw extends CX_Controls_Base {

		/**
		 * Default settings.
		 *
		 * @since 1.0.0
		 * @var array
		 */
		public $defaults_settings = array(
			'id'       => 'cx-checkbox-id',
			'name'     => 'cx-checkbox-name',
			'required' => false,
			'value'    => array(
				'checkbox-1',
				'checkbox-2',
				'checkbox-3',
			),
			'options' => array(
				'checkbox-1' => 'checkbox 1',
				'checkbox-2' => 'checkbox 2',
				'checkbox-3' => 'checkbox 3',
			),
			'allow_custom_value' => false,
			'add_button_label'   => 'Add custom value',
			'label'  => '',
			'class'  => '',
		);

		public static $index = 0;

		/**
		 * Render html UI_Checkbox.
		 *
		 * @since 1.0.0
		 */
		public function render() {

			$html  = '';

			$class = implode( ' ',
				array(
					$this->settings['class'],
				)
			);

			if ( isset( $this->settings['options_callback'] ) ) {
				$this->settings['options'] = call_user_func( $this->settings['options_callback'] );
			}

			$data_options = htmlspecialchars( json_encode( array_keys( $this->settings['options'] ) ) );
			$allow_custom = $this->settings['allow_custom_value'];
			$allow_custom = filter_var( $allow_custom, FILTER_VALIDATE_BOOLEAN );

			$html .= '<div class="cx-ui-control-container ' . esc_attr( $class ) . '" data-options="' . $data_options . '" data-allow-custom="' . $allow_custom . '" data-index="' . self::$index . '">';

			if ( ! empty( $this->settings['options'] ) && is_array( $this->settings['options'] ) ) {

				if ( ! is_array( $this->settings['value'] ) ) {
					$this->settings['value'] = array( $this->settings['value'] );
				}

				if ( '' !== $this->settings['label'] ) {
					$html .= '<label class="cx-label" for="' . esc_attr( $this->settings['id'] ) . '">' . wp_kses_post( $this->settings['label'] ) . '</label> ';
				}

				foreach ( $this->settings['options'] as $option => $option_value ) {

					if ( ! empty( $this->settings['value'] ) ) {
						$option_checked = array_key_exists( $option, $this->settings['value'] ) ? strval( $option ) : '';
						$item_value     = ! $this->is_empty( $option_checked ) ? $this->settings['value'][ $option ] : 'false';
					} else {
						$option_checked = '';
						$item_value     = 'false';
					}

					$checked      = ( ! $this->is_empty( $option_checked ) && filter_var( $item_value, FILTER_VALIDATE_BOOLEAN ) ) ? 'checked' : '';
					$item_value   = filter_var( $item_value, FILTER_VALIDATE_BOOLEAN ) ? 'true' : 'false';
					$option_label = isset( $option_value ) && is_array( $option_value ) ? $option_value['label'] : $option_value;
					$index        = $option;

					$html .= '<div class="cx-checkbox-item">';
						$html .= '<label class="cx-checkbox-label">';

							$html .= sprintf(
								'<input type="checkbox" class="cx-checkbox-input" name="%1$s[]" %2$s value="%3$s" %4$s>',
								esc_attr( $this->settings['name'] ),
								$checked,
								$option,
								$this->get_required()
							);

							$html .= '<span class="cx-label-content">' . esc_html( $option_label ) . '</span>';
						$html .= '</label>';
					$html .= '</div>';

				}

				if ( $this->settings['allow_custom_value'] ) {

					if ( ! empty( $this->settings['value'] ) ) {

						$custom_options = array_diff( array_values( $this->settings['value'] ), array_keys( $this->settings['options'] ) );

						if ( ! empty( $custom_options ) ) {

							foreach ( $custom_options as $custom_option ) {

								$html .= '<div class="cx-checkbox-item" style="padding: 0 0 4px;">';
									$html .= '<label class="cx-checkbox-label">';
										$html .= sprintf(
											'<input type="checkbox" class="cx-checkbox-input" name="%1$s[]" checked value="%2$s">',
											esc_attr( $this->settings['name'] ),
											$custom_option
										);
										$html .= '<input type="text" class="cx-checkbox-custom-value cx-ui-text" value="' . esc_attr( $custom_option ) . '">';
									$html .= '</label>';
								$html .= '</div>';
							}

						}
					}

					$html .= sprintf(
						'<a href="#" class="cx-checkbox-add-button" data-index="%2$s">%1$s</a>',
						esc_html( $this->settings['add_button_label'] ),
						self::$index
					);

					$html .= sprintf(
						'<template id="cx_checkbox_custom_template_%1$d"><div class="cx-checkbox-item" style="padding: 0 0 4px;"><label class="cx-checkbox-label"><input type="checkbox" class="cx-checkbox-input" name="%2$s[]" checked value=""><input type="text" class="cx-checkbox-custom-value cx-ui-text" value=""></label></div></template>',
						self::$index,
						esc_attr( $this->settings['name'] )
					);

				}
			}

			$html .= '</div>';

			self::$index++;

			return $html;
		}
	}
}
