<?php
namespace Jet_Engine\Modules\Profile_Builder;

class Elementor_Integration extends Base_Integration {

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		add_action( 'jet-engine/listings/dynamic-link/source-controls', array( $this, 'register_link_controls' ), 10 );
		add_action( 'jet-engine/listings/dynamic-image/link-source-controls', array( $this, 'register_img_link_controls' ), 10 );

		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return;
		}

		add_action( 'jet-engine/elementor-views/widgets/register', array( $this, 'register_widgets' ), 11, 2 );

		add_action( 'jet-engine/elementor-views/dynamic-tags/register', array( $this, 'register_dynamic_tags' ), 10, 2 );
		add_action( 'jet-engine/profile-builder/template/assets', array( $this, 'enqueue_template_styles' ) );

		add_filter( 'jet-engine/profile-builder/template/content', array( $this, 'render_template_content' ), 0, 2 );


	}

	/**
	 * Check if profile template is Elementor template, render it with Elementor
	 *
	 * @param  [type] $content     [description]
	 * @param  [type] $template_id [description]
	 * @return [type]              [description]
	 */
	public function render_template_content( $content, $template_id ) {

		$elementor_content = \Elementor\Plugin::instance()->frontend->get_builder_content( $template_id );

		if ( $elementor_content ) {
			remove_all_filters( 'jet-engine/profile-builder/template/content' );
			return $elementor_content;
		}

		return $content;
	}

	/**
	 * Enqueue profile template assets
	 *
	 * @param  [type] $template_id [description]
	 * @return [type]              [description]
	 */
	public function enqueue_template_styles( $template_id ) {

		\Elementor\Plugin::instance()->frontend->enqueue_styles();

		$css_file = new \Elementor\Core\Files\CSS\Post( $template_id );
		$css_file->enqueue();

	}

	/**
	 * Register Elementor-related dynamic tags
	 *
	 * @param  [type] $dynamic_tags [description]
	 * @param  [type] $tags_module [description]
	 * @return [type]              [description]
	 */
	public function register_dynamic_tags( $dynamic_tags, $tags_module ) {

		require_once jet_engine()->modules->modules_path( 'profile-builder/inc/dynamic-tags/profile-page-url.php' );

		$tags_module->register_tag( $dynamic_tags, new Dynamic_Tags\Profile_Page_URL() );

	}

	/**
	 * Add account URL into the link options Dynamic Image widget
	 * @param  [type] $widget [description]
	 * @return [type]         [description]
	 */
	public function register_img_link_controls( $widget ) {
		$this->register_link_controls( $widget, true );
	}

	/**
	 * Register link control
	 *
	 * @param  [type] $widget [description]
	 * @return [type]         [description]
	 */
	public function register_link_controls( $widget = null, $is_image = false ) {

		$pages = $this->get_pages_for_options( 'elementor' );

		$condition = array(
			'dynamic_link_source' => 'profile_page',
		);

		if ( $is_image ) {
			$condition = array(
				'linked_image'      => 'yes',
				'image_link_source' => 'profile_page',
			);
		}

		$widget->add_control(
			'dynamic_link_profile_page',
			array(
				'label'     => __( 'Profile Page', 'jet-engine' ),
				'type'      => 'select',
				'default'   => '',
				'groups'    => $pages,
				'condition' => $condition,
			)
		);

	}

	/**
	 * Register profile builder widgets
	 *
	 * @return void
	 */
	public function register_widgets( $widgets_manager, $elementor_views ) {

		$elementor_views->register_widget(
			jet_engine()->modules->modules_path( 'profile-builder/inc/widgets/profile-menu-widget.php' ),
			$widgets_manager,
			__NAMESPACE__ . '\Profile_Menu_Widget'
		);

		$template_mode = Module::instance()->settings->get( 'template_mode' );

		if ( 'content' === $template_mode ) {
			$elementor_views->register_widget(
				jet_engine()->modules->modules_path( 'profile-builder/inc/widgets/profile-content-widget.php' ),
				$widgets_manager,
				__NAMESPACE__ . '\Profile_Content_Widget'
			);
		}

	}

}
