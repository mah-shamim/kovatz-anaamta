<?php
/**
 * Class: Jet_Woo_Builder_Single_Reviews_Form
 * Name: Single Reviews Form
 * Slug: jet-single-reviews-form
 */

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Builder_Single_Reviews_Form extends Jet_Woo_Builder_Base {

	public function get_name() {
		return 'jet-single-reviews-form';
	}

	public function get_title() {
		return __( 'Single Reviews Form', 'jet-woo-builder' );
	}

	public function get_icon() {
		return 'jet-woo-builder-icon-single-reviews-form';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/jetwoobuilder-how-to-create-and-set-a-single-product-page-template/';
	}

	public function show_in_panel() {
		return jet_woo_builder()->documents->is_document_type( 'single' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'single-reviews-form-general',
			[
				'label' => __( 'Product Reviews Form', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		jet_woo_builder_common_controls()->register_wc_style_warning( $this );

		$this->end_controls_section();

	}

	protected function render() {

		global $product;

		if ( ! is_a( $product, 'WC_Product' ) ) {
			return;
		}

		if ( $this->__set_editor_product() ) {
			$this->__open_wrap();

			// Add filters before displaying our Widget.
			if ( jet_woo_builder()->elementor_views->in_elementor() ) {
				add_filter( 'comments_template', [ 'WC_Template_Loader', 'comments_template_loader' ] );
			}

			comments_template();

			// Remove filters after displaying our Widget.
			if ( jet_woo_builder()->elementor_views->in_elementor() ) {
				remove_filter( 'comments_template', [ 'WC_Template_Loader', 'comments_template_loader' ] );
			}

			$this->__close_wrap();

			if ( jet_woo_builder()->elementor_views->in_elementor() ) {
				$this->__reset_editor_product();
			}
		}

		// On render widget from Editor - trigger the init manually.
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			?>
			<script>
				jQuery( '#rating' )
					.hide()
					.before(
						'<p class="stars">\
							<span>\
								<a class="star-1" href="#">1</a>\
								<a class="star-2" href="#">2</a>\
								<a class="star-3" href="#">3</a>\
								<a class="star-4" href="#">4</a>\
								<a class="star-5" href="#">5</a>\
							</span>\
						</p>'
					);
			</script>
			<?php
		}

	}

}
