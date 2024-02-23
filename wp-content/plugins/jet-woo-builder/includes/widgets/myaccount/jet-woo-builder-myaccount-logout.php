<?php
/**
 * Class: Jet_Woo_Builder_MyAccount_Logout
 * Name: Account Logout
 * Slug: jet-myaccount-logout
 */

namespace Elementor;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_MyAccount_Logout extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-myaccount-logout';
	}

	public function get_title() {
		return esc_html__( 'Account Logout', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-my-account-logout';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-my-account-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'myaccount' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-woo-builder-myaccount-myaccount-logout/css-scheme',
			[
				'button' => '.jet-woo-builder-customer-logout a',
			]
		);

		$this->start_controls_section(
			'myaccount_logout_styles_section',
			[
				'label' => __( 'Logout', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		jet_woo_builder_common_controls()->register_button_style_controls( $this, 'myaccount_logout', $css_scheme['button'] );

		$this->end_controls_section();

	}

	protected function render() {

		$this->__open_wrap();

		include $this->get_template( 'myaccount/logout.php' );

		$this->__close_wrap();

	}
}
