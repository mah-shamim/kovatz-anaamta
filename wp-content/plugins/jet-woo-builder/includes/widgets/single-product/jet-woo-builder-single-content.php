<?php
/**
 * Class: Jet_Woo_Builder_Single_Content
 * Name: Single Content
 * Slug: jet-single-content
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Single_Content extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-single-content';
	}

	public function get_title() {
		return __( 'Single Content', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-single-content';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-and-set-a-single-product-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'single' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-woo-builder/jet-single-content/css-scheme',
			array(
				'content_wrapper' => '.jet-woo-builder .jet-single-content',
			)
		);

		$this->start_controls_section(
			'section_single_content_style',
			[
				'label' => __( 'Style', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['content_wrapper'],
			)
		);

		$this->add_control(
			'single_content_text_color',
			[
				'type'      => Controls_Manager::COLOR,
				'label'     => __( 'Color', 'jet-woo-builder' ),
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['content_wrapper'] => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'single_content_align',
			array(
				'label'     => esc_html__( 'Alignment', 'jet-woo-builder' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => jet_woo_builder_tools()->get_available_h_align_types( true ),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['content_wrapper'] => 'text-align: {{VALUE}};',
				),
				'classes'   => 'elementor-control-align',
			)
		);

		$this->end_controls_section();

	}

	protected function render() {
		if ( $this->__set_editor_product() ) {
			$this->__open_wrap();

			include $this->get_template( 'single-product/content.php' );

			$this->__close_wrap();

			if ( jet_woo_builder()->elementor_views->in_elementor() ) {
				$this->__reset_editor_product();
			}
		}
	}

}
