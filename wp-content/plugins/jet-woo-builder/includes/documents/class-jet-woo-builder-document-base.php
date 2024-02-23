<?php
/**
 * Class: Jet_Woo_Builder_Document_Base
 * Name: Document Base
 * Slug: jet-woo-builder-archive-document
 */

use Elementor\Controls_Manager;
use Elementor\Core\Base\Document;
use Elementor\Modules\PageTemplates\Module as PageTemplatesModule;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Jet_Woo_Builder_Document_Base extends Elementor\Core\Base\Document {

	public $first_product  = null;
	public $first_category = null;

	public function get_name() {
		return '';
	}

	public static function get_properties() {

		$properties = parent::get_properties();

		$properties['admin_tab_group'] = '';
		$properties['support_kit']     = true;
		$properties['cpt']             = [ 'jet-woo-builder' ];

		return $properties;

	}

	protected function register_controls() {

		parent::register_controls();

		$properties      = $this::get_properties();
		$enable_settings = isset( $properties['woo_builder_template_settings'] ) ? $properties['woo_builder_template_settings'] : false;

		if ( ! $enable_settings ) {
			return;
		}

		$this->start_injection( [
			'of'       => 'post_status',
			'fallback' => [
				'of' => 'post_title',
			],
		] );

		$this->add_control(
			'hide_template_title',
			[
				'label'       => __( 'Hide Title', 'jet-woo-builder' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Not working? You can set a different selector for the title in Site Settings > Layout', 'jet-woo-builder' ),
				'selectors'   => [
					':root' => '--page-title-display: none',
				],
			]
		);

		$this->add_control(
			'template_layout',
			[
				'label'   => __( 'Template Layout', 'jet-woo-builder' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''                                          => __( 'Default', 'jet-woo-builder' ),
					PageTemplatesModule::TEMPLATE_CANVAS        => __( 'Elementor Canvas', 'jet-woo-builder' ),
					PageTemplatesModule::TEMPLATE_HEADER_FOOTER => __( 'Elementor Full Width', 'jet-woo-builder' ),
				],
			]
		);

		$this->add_control(
			'default_template_layout_description',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'Default Page Template from your theme', 'jet-woo-builder' ),
				'separator'       => 'none',
				'content_classes' => 'elementor-descriptor elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'template_layout' => '',
				],
			]
		);

		$this->add_control(
			'canvas_template_layout_description',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'No header, no footer, just template content', 'jet-woo-builder' ),
				'separator'       => 'none',
				'content_classes' => 'elementor-descriptor elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'template_layout' => PageTemplatesModule::TEMPLATE_CANVAS,
				],
			]
		);

		$this->add_control(
			'header_footer_template_layout_description',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => __( 'This template includes the header, full-width content and footer', 'jet-woo-builder' ),
				'separator'       => 'none',
				'content_classes' => 'elementor-descriptor elementor-panel-alert elementor-panel-alert-info',
				'condition'       => [
					'template_layout' => PageTemplatesModule::TEMPLATE_HEADER_FOOTER,
				],
			]
		);

		$this->end_injection();

		self::register_style_controls( $this );

	}

	public static function register_style_controls( $document ) {

		$document->start_controls_section(
			'section_page_style',
			[
				'label' => __( 'Body Style', 'jet-woo-builder' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$document->add_group_control(
			Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'background',
				'selector' => 'body',
			]
		);

		$document->add_responsive_control(
			'padding',
			[
				'label'      => __( 'Padding', 'jet-woo-builder' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'body' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$document->end_controls_section();

		Elementor\Plugin::$instance->controls_manager->add_custom_css_controls( $document );

	}

	/**
	 * Query for first product ID.
	 *
	 * @return int|bool
	 */
	public function query_first_product() {

		if ( null !== $this->first_product ) {
			return $this->first_product;
		}

		$args = [
			'post_type'      => 'product',
			'post_status'    => [ 'publish', 'pending', 'draft', 'future' ],
			'posts_per_page' => 1,
		];

		$sample_product = get_post_meta( $this->get_main_id(), '_sample_product', true );

		if ( $sample_product ) {
			$args['p'] = $sample_product;
		}

		$wp_query = new WP_Query( $args );

		if ( ! $wp_query->have_posts() ) {
			return false;
		}

		$post = $wp_query->posts;

		return $this->first_product = $post[0]->ID;

	}

	/**
	 * Query for first product ID.
	 *
	 * @return int|bool
	 */
	public function query_first_category() {

		if ( null !== $this->first_category ) {
			return $this->first_category;
		}

		$product_categories = get_categories(
			[
				'taxonomy'     => 'product_cat',
				'orderby'      => 'name',
				'pad_counts'   => false,
				'hierarchical' => 1,
				'hide_empty'   => false,
			]
		);


		if ( ! empty( $product_categories ) ) {
			$product_category = $product_categories[0];
		}

		return $this->first_category = $product_category->term_id;

	}

	/**
	 * Save meta for current post
	 *
	 * @param $post_id
	 */
	public function save_template_item_to_meta( $post_id ) {

		$content = Elementor\Plugin::instance()->frontend->get_builder_content( $post_id, false );
		$content = preg_replace( '/<style>.*?<\/style>/', '', $content );

		update_post_meta( $post_id, '_jet_woo_builder_content', $content );

	}

	/**
	 * Save data for archive document types
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function save_archive_templates( $data = [] ) {

		if ( ! $this->is_editable_by_current_user() || empty( $data ) ) {
			return false;
		}

		if ( ! empty( $data['settings'] ) ) {
			if ( Document::STATUS_AUTOSAVE === $data['settings']['post_status'] ) {
				if ( ! defined( 'DOING_AUTOSAVE' ) ) {
					define( 'DOING_AUTOSAVE', true );
				}
			}

			$this->save_settings( $data['settings'] );

			//Refresh post after save settings.
			$this->post = get_post( $this->post->ID );
		}

		if ( ! empty( $data['elements'] ) ) {
			$this->save_elements( $data['elements'] );
		}

		$this->save_template_type();
		$this->save_version();
		$this->save_template_item_to_meta( $this->post->ID );

		// Update Post CSS
		if ( class_exists( 'Elementor\Core\Files\CSS\Post' ) ) {
			$css_file = new Elementor\Core\Files\CSS\Post( $this->post->ID );
		} else {
			$css_file = new Elementor\Post_CSS_File( $this->post->ID );
		}

		$css_file->enqueue();
		$css_file->update();

		return true;

	}

	/**
	 * Get elements data with new query
	 *
	 * @param null    $data
	 * @param boolean $with_html_content
	 *
	 * @return array
	 * @throws Exception
	 */
	public function get_elements_raw_data( $data = null, $with_html_content = false ) {

		jet_woo_builder()->documents->switch_to_preview_query();

		$editor_data = parent::get_elements_raw_data( $data, $with_html_content );

		jet_woo_builder()->documents->restore_current_query();

		return $editor_data;

	}

	/**
	 * Render current element
	 *
	 * @param $data
	 *
	 * @return string
	 * @throws Exception
	 */
	public function render_element( $data ) {

		jet_woo_builder()->documents->switch_to_preview_query();

		$render_html = parent::render_element( $data );

		jet_woo_builder()->documents->restore_current_query();

		return $render_html;

	}

	/**
	 * Return elements data
	 *
	 * @param string $status
	 *
	 * @return array
	 */
	public function get_elements_data( $status = 'publish' ) {

		if ( ! isset( $_GET[ jet_woo_builder_post_type()->slug() ] ) || ! isset( $_GET['preview'] ) ) {
			return parent::get_elements_data( $status );
		}

		jet_woo_builder()->documents->switch_to_preview_query();

		$elements = parent::get_elements_data( $status );

		jet_woo_builder()->documents->restore_current_query();

		return $elements;

	}

	public function __construct( array $data = [] ) {

		add_filter( 'body_class', [ $this, 'set_body_class' ] );
		add_filter( 'the_content', [ $this, 'add_template_wrapper' ], 9999999 );

		parent::__construct( $data );

	}

	protected static function get_editor_panel_categories() {

		$categories = [
			'jet-woo-builder' => [
				'title' => __( 'JetWooBuilder', 'jet-woo-builder' ),
			],
		];

		return $categories + parent::get_editor_panel_categories();

	}

	/**
	 * Add classes to body on template pages
	 *
	 * @param array $classes Default classes list.
	 *
	 * @return array
	 */
	public function set_body_class( $classes ) {

		$classes[] = array_push( $classes, 'woocommerce', 'woocommerce-page' );

		switch ( $this->get_name() ) {
			case 'jet-woo-builder':
				$classes[] = 'single-product';

				break;
			case 'jet-woo-builder-archive':
			case 'jet-woo-builder-category':
			case 'jet-woo-builder-shop':
				$classes[] = 'woocommerce-shop';

				break;
			case 'jet-woo-builder-cart':
				$classes[] = 'woocommerce-cart';

				break;
			case 'jet-woo-builder-thankyou':
				array_push( $classes, 'woocommerce-checkout', 'woocommerce-order-received' );

				break;
			case 'jet-woo-builder-checkout':
				$classes[] = 'woocommerce-checkout';

				break;
			case 'jet-woo-builder-myaccount':
				$classes[] = 'woocommerce-account';

				break;
			default:
				break;
		}

		return $classes;

	}

	/**
	 * Add wrapper to templates editor content.
	 *
	 * @since 1.12.0
	 *
	 * @param $content
	 *
	 * @return mixed|string
	 */
	public function add_template_wrapper( $content ) {

		switch ( $this->get_name() ) {
			case 'jet-woo-builder':
			case 'jet-woo-builder-archive':
				$content = sprintf( '<div class="product">%s</div>', $content );

				break;
			case 'jet-woo-builder-checkout':
				if ( jet_woo_builder()->elementor_views->in_elementor() ) {
					$content = sprintf( '<form class="checkout woocommerce-checkout">%s</form>', $content );
				}

				break;
			default:
				break;
		}

		return $content;

	}

}