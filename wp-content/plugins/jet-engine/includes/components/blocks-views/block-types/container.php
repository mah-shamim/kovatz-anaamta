<?php
/**
 * Elementor views manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Blocks_Views_Type_Container' ) ) {

	/**
	 * Define Jet_Engine_Blocks_Views_Type_Container class
	 */
	class Jet_Engine_Blocks_Views_Type_Container extends Jet_Engine_Blocks_Views_Type_Base {

		public function __construct() {

			parent::__construct();

			if ( $this->has_style_manager() ) {
				add_filter(
					'jet_style_manager/gutenberg/prevent_block_wrap/' . $this->get_block_name(),
					'__return_true'
				);
			}

			add_filter( 'render_block', array( $this, 'update_background_image' ), 11, 2 );
		}

		/**
		 * Update backgroud image according selected image size
		 *
		 * @param  [type] $block_content [description]
		 * @param  [type] $block_data    [description]
		 * @return [type]                [description]
		 */
		public function update_background_image( $block_content, $block_data ) {

			$block_name = $block_data['blockName'];

			if ( $block_name === $this->get_block_name() ) {
				$attrs         = $block_data['attrs'];
				$block_content = $this->replace_image( $attrs, $block_content );
			}

			return $block_content;
		}

		/**
		 * Repalce background image in given block content
		 *
		 * @param  array  $attrs         [description]
		 * @param  string $block_content [description]
		 * @return [type]                [description]
		 */
		public function replace_image( $attrs = array(), $block_content = '' ) {

			if ( empty( $attrs['background_image_url'] ) || empty( $attrs['background_image_id'] ) ) {
				return $block_content;
			}

			$img_url             = $attrs['background_image_url'];
			$background_settings = ! empty( $attrs['background_settings'] ) ? $attrs['background_settings'] : array();
			$image_size          = ! empty( $background_settings['image_size'] ) ? $background_settings['image_size'] : 'full';

			if ( 'full' === $image_size ) {
				return $block_content;
			}

			$pos = strpos( $block_content, 'url(' . $img_url . ')' );

			if ( false !== $pos ) {
				$new_url       = wp_get_attachment_image_url( $attrs['background_image_id'], $image_size );
				$block_content = str_replace( 'url(' . $img_url . ')', 'url(' . $new_url . ')', $block_content );
			}

			return $block_content;

		}

		/**
		 * Returns block name
		 *
		 * @return [type] [description]
		 */
		public function get_name() {
			return 'container';
		}

		/**
		 * Returns path to JSON file with block configuration
		 *
		 * @return string
		 */
		public function block_file() {
			return jet_engine()->plugin_path( 'assets/js/admin/blocks-views/src/blocks/container/block.json' );
		}

		/**
		 * Return attributes array
		 *
		 * @return array
		 */
		public function get_attributes() {
			return $this->get_file_data( 'attributes', array() );
		}

		public function css_selector( $el = '' ) {
			return sprintf( '{{WRAPPER}}.jet-%1$s%2$s', $this->get_name(), $el );
		}

		/**
		 * Add style block options
		 *
		 * @return boolean
		 */
		public function add_style_manager_options() {

			$this->controls_manager->start_section(
				'style_controls',
				array(
					'id'           => 'section_field_style',
					'initial_open' => true,
					'title'        => esc_html__( 'General Styles', 'jet-engine' )
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'section_padding',
					'label'        => __( 'Padding', 'jet-engine' ),
					'type'         => 'dimensions',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector() => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			if ( 'section' !== $this->get_name() ) {

				$this->controls_manager->add_control(
					array(
						'id'           => 'section_margin',
						'label'        => __( 'Margin', 'jet-engine' ),
						'separator'    => 'before',
						'type'         => 'dimensions',
						'css_selector' => array(
							$this->css_selector() => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
						),
					)
				);

			}

			$this->controls_manager->add_control(
				array(
					'id'             => 'section_border',
					'label'          => __( 'Border', 'jet-engine' ),
					'type'           => 'border',
					'separator'      => 'before',
					'disable_radius' => true,
					'css_selector'   => array(
						$this->css_selector() => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
					),
				)
			);

			$this->controls_manager->add_control(
				array(
					'id'           => 'section_border_radius',
					'label'        => __( 'Border Radius', 'jet-engine' ),
					'type'         => 'dimensions',
					'separator'    => 'before',
					'css_selector' => array(
						$this->css_selector() => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
				)
			);

			$this->controls_manager->end_section();

		}

	}

}
