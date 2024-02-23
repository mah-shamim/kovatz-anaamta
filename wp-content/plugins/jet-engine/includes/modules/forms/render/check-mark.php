<?php
namespace Jet_Engine\Forms\Render;

class Check_Mark extends \Jet_Engine_Render_Base {

	public function get_name() {
		return 'jet-engine-forms-check-mark';
	}

	public function default_settings() {
		return array(
			'check_mark_icon_default' => '',
			'check_mark_icon_checked' => '',
		);
	}

	public function render() {

		$settings = $this->get_settings();

		$default_icon = ! empty( $settings['check_mark_icon_default'] ) ? $settings['check_mark_icon_default'] : false;
		$checked_icon = ! empty( $settings['check_mark_icon_checked'] ) ? $settings['check_mark_icon_checked'] : false;

		$default_icon_html = \Jet_Engine_Tools::render_icon( $default_icon, 'jet-form__check-mark__icon' );
		$checked_icon_html = \Jet_Engine_Tools::render_icon( $checked_icon, 'jet-form__check-mark__icon' );

		printf( '<div class="jet-form__check-mark"><div class="jet-form__check-mark--default">%1$s</div><div class="jet-form__check-mark--checked">%2$s</div></div>', $default_icon_html, $checked_icon_html );

	}
}
