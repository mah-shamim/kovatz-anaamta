<?php
/**
 * Gallery slider module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Module_Gallery_Slider' ) ) {

	/**
	 * Define Jet_Engine_Module_Gallery_Slider class
	 */
	class Jet_Engine_Module_Gallery_Slider extends Jet_Engine_Module_Base {

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gallery-slider';
		}

		/**
		 * Returns detailed information about current module for the dashboard page
		 * @return [type] [description]
		 */
		public function get_module_details() {
			return '<p>After activation, in the Callback feature of the Content settings tab of the Dynamic Field widget in Elementor page builder appears an “Images gallery slider” option.</p>
					<p>This option allows you to display the pictures added to the gallery-type meta field using JetEngine or Advanced Custom Fields plugin on the website’s page as a slider.</p>';
		}

		public function get_video_embed() {
			return 'https://www.youtube.com/embed/b_6wE0WAnuw';
		}

		/**
		 * Returns array links to the module-related resources
		 * @return array
		 */
		public function get_module_links() {
			return array(
				array(
					'label' => 'JetEngine: Slider Gallery Options Overview within Dynamic Field Widget',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-dynamic-field-widget-grid-gallery-and-slider-gallery-options-overview/',
				),
			);
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Slider Gallery for Dynamic Field widget', 'jet-engine' );
		}

		/**
		 * Module init
		 *
		 * @return void
		 */
		public function module_init() {

			add_filter( 'jet-engine/listings/allowed-callbacks', array( $this, 'add_slider_cb' ) );
			add_filter( 'jet-engine/listing/dynamic-field/callback-args', array( $this, 'cb_args' ), 10, 4 );
			add_filter( 'jet-engine/listings/allowed-callbacks-args', array( $this, 'add_cb_args' ) );
			add_action( 'jet-engine/listing/dynamic-field/misc-style-controls', array( $this, 'style_controls' ) );
			add_action( 'jet-engine/blocks-views/dynamic-field/misc-style-controls', array( $this, 'block_style_controls' ), 10, 2 );
			add_filter( 'jet-engine/blocks-views/editor-data', array( $this, 'modify_cb_args' ) );
			add_action( 'jet-engine/bricks-views/dynamic-field/misc-style-controls', array( $this, 'bricks_style_controls' ), 10, 2 );
			add_action( 'jet-engine/bricks-views/dynamic-field/assets', array( $this, 'bricks_gallery_assets' ) );

		}

		/**
		 * Gallery assets
		 *
		 * @return [type] [description]
		 */
		public function bricks_gallery_assets( $element ) {

			$settings = $element->get_jet_settings();

			if ( ! empty( $settings['dynamic_field_filter'] ) && 'jet_engine_img_gallery_slider' === $settings['filter_callback'] ) {
				wp_enqueue_style( 'bricks-photoswipe' );
				wp_enqueue_script( 'bricks-photoswipe' );
				wp_enqueue_script( 'bricks-photoswipe-lightbox' );
			}

		}

		/**
		 * Add grid gallery to callbacks
		 *
		 * @param  array $callbacks
		 * @return array
		 */
		public function add_slider_cb( $callbacks = array() ) {
			$callbacks['jet_engine_img_gallery_slider'] = __( 'Images gallery slider', 'jet-engine' );
			return $callbacks;
		}

		/**
		 * Add gallery style controls
		 *
		 * @param  [type] $widget [description]
		 * @return [type]         [description]
		 */
		public function style_controls( $widget ) {

			$prefix = $widget->prevent_wrap() ? '__content' : '';

			if ( ! jet_engine()->modules->is_module_active( 'gallery-grid' ) ) {

				$widget->start_controls_tabs(
					'tabs_overlay_style',
					array(
						'condition' => array(
							'dynamic_field_filter' => 'yes',
							'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
						),
					)
				);

				$widget->start_controls_tab(
					'tab_overlay_normal',
					array(
						'label' => esc_html__( 'Image Overlay', 'jet-engine' ),
					)
				);

				$widget->add_control(
					'img_overlay_color',
					array(
						'label'     => __( 'Color', 'jet-engine' ),
						'type'      => Elementor\Controls_Manager::COLOR,
						'selectors' => array(
							$widget->css_selector( $prefix . ' .jet-engine-gallery-item-wrap:after' ) => 'background: {{VALUE}}',
						),
					)
				);

				$widget->end_controls_tab();

				$widget->start_controls_tab(
					'tab_overlay_hover',
					array(
						'label' => esc_html__( 'Hover Overlay', 'jet-engine' ),
					)
				);

				$widget->add_control(
					'img_hover_overlay_color',
					array(
						'label'     => __( 'Color', 'jet-engine' ),
						'type'      => Elementor\Controls_Manager::COLOR,
						'selectors' => array(
							$widget->css_selector( $prefix . ' .jet-engine-gallery-item-wrap:hover:after' ) => 'background: {{VALUE}}',
						),
					)
				);

				$widget->end_controls_tab();

				$widget->end_controls_tabs();

				$widget->add_control(
					'img_icon_color',
					array(
						'separator' => 'before',
						'label'     => __( 'Lightbox Icon Color', 'jet-engine' ),
						'type'      => Elementor\Controls_Manager::COLOR,
						'selectors' => array(
							$widget->css_selector( $prefix . ' .jet-engine-gallery-item-wrap:before' ) => 'color: {{VALUE}}',
						),
						'condition' => array(
							'dynamic_field_filter' => 'yes',
							'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
						),
					)
				);

			}

			$widget->add_responsive_control(
				'img_slides_gap',
				array(
					'label'      => __( 'Images gap', 'jet-engine' ),
					'type'       => Elementor\Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'separator'  => 'before',
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'  => array(
						$widget->css_selector( $prefix . ' .jet-engine-gallery-slider__item' ) => 'padding: 0 calc( {{SIZE}}{{UNIT}}/2 );',
						$widget->css_selector( $prefix . ' .slick-list' ) => 'margin-left: calc( -{{SIZE}}{{UNIT}}/2 ); margin-right: calc( -{{SIZE}}{{UNIT}}/2 );',
					),
					'condition' => array(
						'dynamic_field_filter' => 'yes',
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
					'render_type' => 'template',
				)
			);

			$widget->add_control(
				'slider_controls_styles',
				array(
					'label'     => esc_html__( 'Slider arrows', 'jet-engine' ),
					'type'      => Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'dynamic_field_filter' => 'yes',
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$widget->add_responsive_control(
				'arrows_box_size',
				array(
					'label'      => __( 'Slider arrows box size', 'jet-engine' ),
					'type'       => Elementor\Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range'      => array(
						'px' => array(
							'min' => 16,
							'max' => 120,
						),
					),
					'selectors'  => array(
						$widget->css_selector( $prefix . ' .jet-engine-arrow' ) => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; margin-top: calc( -{{SIZE}}{{UNIT}}/2 );',
					),
					'condition' => array(
						'dynamic_field_filter' => 'yes',
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$widget->add_responsive_control(
				'arrows_size',
				array(
					'label'      => __( 'Slider arrows size', 'jet-engine' ),
					'type'       => Elementor\Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range'      => array(
						'px' => array(
							'min' => 10,
							'max' => 50,
						),
					),
					'selectors'  => array(
						$widget->css_selector( $prefix . ' .jet-engine-arrow' ) => 'font-size: {{SIZE}}{{UNIT}};',
						$widget->css_selector( $prefix . ' .jet-engine-arrow svg' ) => 'height: {{SIZE}}{{UNIT}};',
					),
					'condition' => array(
						'dynamic_field_filter' => 'yes',
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$widget->start_controls_tabs( 'tabs_arrow_style' );

			$widget->start_controls_tab(
				'tab_arrow_normal',
				array(
					'label' => esc_html__( 'Normal', 'jet-engine' ),
					'condition' => array(
						'dynamic_field_filter' => 'yes',
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$widget->add_control(
				'arrow_color',
				array(
					'label'     => __( 'Color', 'jet-engine' ),
					'type'      => Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						$widget->css_selector( $prefix . ' .jet-engine-arrow' ) => 'color: {{VALUE}}',
					),
					'condition' => array(
						'dynamic_field_filter' => 'yes',
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$widget->add_control(
				'arrow_bg_color',
				array(
					'label'     => __( 'Background', 'jet-engine' ),
					'type'      => Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						$widget->css_selector( $prefix . ' .jet-engine-arrow' ) => 'background: {{VALUE}}',
					),
					'condition' => array(
						'dynamic_field_filter' => 'yes',
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$widget->end_controls_tab();

			$widget->start_controls_tab(
				'tab_arrow_hover',
				array(
					'label' => esc_html__( 'Hover', 'jet-engine' ),
					'condition' => array(
						'dynamic_field_filter' => 'yes',
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$widget->add_control(
				'arrow_color_hover',
				array(
					'label'     => __( 'Color', 'jet-engine' ),
					'type'      => Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						$widget->css_selector( $prefix . ' .jet-engine-arrow:hover' ) => 'color: {{VALUE}}',
					),
					'condition' => array(
						'dynamic_field_filter' => 'yes',
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$widget->add_control(
				'arrow_bg_color_hover',
				array(
					'label'     => __( 'Background', 'jet-engine' ),
					'type'      => Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						$widget->css_selector( $prefix . ' .jet-engine-arrow:hover' ) => 'background: {{VALUE}}',
					),
					'condition' => array(
						'dynamic_field_filter' => 'yes',
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$widget->end_controls_tab();

			$widget->end_controls_tabs();

		}

		public function block_style_controls( $controls_manager, $block ) {

			$controls_manager->add_responsive_control(
				array(
					'id'    => 'img_slides_gap',
					'label' => __( 'Images gap', 'jet-engine' ),
					'type'  => 'range',
					'units' => array(
						array(
							'value'     => 'px',
							'intervals' => array(
								'step' => 1,
								'min'  => 0,
								'max'  => 100,
							),
						),
					),
					'css_selector' => array(
						$block->css_selector( ' .jet-engine-gallery-slider__item' ) => 'padding: 0 calc( {{VALUE}}{{UNIT}}/2 );',
						$block->css_selector( ' .slick-list' ) => 'margin-left: calc( -{{VALUE}}{{UNIT}}/2 ); margin-right: calc( -{{VALUE}}{{UNIT}}/2 );',
					),
					'condition' => array(
						'dynamic_field_filter' => true,
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$controls_manager->add_control(
				array(
					'id'        => 'slider_controls_styles',
					'label'     => esc_html__( 'Slider arrows', 'jet-engine' ),
					'type'      => 'text',
					'separator' => 'before',
					'condition' => array(
						'dynamic_field_filter' => true,
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$controls_manager->add_responsive_control(
				array(
					'id'    => 'arrows_box_size',
					'label' => __( 'Slider arrows box size', 'jet-engine' ),
					'type'  => 'range',
					'units' => array(
						array(
							'value'     => 'px',
							'intervals' => array(
								'step' => 1,
								'min'  => 16,
								'max'  => 120,
							),
						),
					),
					'css_selector' => array(
						$block->css_selector( ' .jet-engine-arrow' ) => 'width: {{VALUE}}{{UNIT}}; height: {{VALUE}}{{UNIT}}; line-height: {{VALUE}}{{UNIT}}; margin-top: calc( -{{VALUE}}{{UNIT}}/2 );',
					),
					'condition' => array(
						'dynamic_field_filter' => true,
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$controls_manager->add_responsive_control(
				array(
					'id'    => 'arrows_size',
					'label' => __( 'Slider arrows size', 'jet-engine' ),
					'type'  => 'range',
					'units' => array(
						array(
							'value'     => 'px',
							'intervals' => array(
								'step' => 1,
								'min'  => 10,
								'max'  => 50,
							),
						),
					),
					'css_selector' => array(
						$block->css_selector( ' .jet-engine-arrow' ) => 'font-size: {{VALUE}}{{UNIT}};',
					),
					'condition' => array(
						'dynamic_field_filter' => true,
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$controls_manager->start_tabs(
				'style_controls',
				array(
					'id'        => 'tabs_arrow_style',
					'condition' => array(
						'dynamic_field_filter' => true,
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$controls_manager->start_tab(
				'style_controls',
				array(
					'id'    => 'tab_arrow_normal',
					'title' => esc_html__( 'Normal', 'jet-engine' ),
					'condition' => array(
						'dynamic_field_filter' => true,
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$controls_manager->add_control(
				array(
					'id'        => 'arrow_color',
					'label'     => __( 'Color', 'jet-engine' ),
					'type'      => 'color-picker',
					'css_selector' => array(
						$block->css_selector( ' .jet-engine-arrow' ) => 'color: {{VALUE}}',
					),
					'condition' => array(
						'dynamic_field_filter' => true,
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$controls_manager->add_control(
				array(
					'id'        => 'arrow_bg_color',
					'label'     => __( 'Background', 'jet-engine' ),
					'type'      => 'color-picker',
					'css_selector' => array(
						$block->css_selector( ' .jet-engine-arrow' ) => 'background: {{VALUE}}',
					),
					'condition' => array(
						'dynamic_field_filter' => true,
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$controls_manager->end_tab();

			$controls_manager->start_tab(
				'style_controls',
				array(
					'id'    => 'tab_arrow_hover',
					'title' => esc_html__( 'Hover', 'jet-engine' ),
					'condition' => array(
						'dynamic_field_filter' => true,
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$controls_manager->add_control(
				array(
					'id'        => 'arrow_color_hover',
					'label'     => __( 'Color', 'jet-engine' ),
					'type'      => 'color-picker',
					'css_selector' => array(
						$block->css_selector( ' .jet-engine-arrow:hover' ) => 'color: {{VALUE}}',
					),
					'condition' => array(
						'dynamic_field_filter' => true,
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$controls_manager->add_control(
				array(
					'id'        => 'arrow_bg_color_hover',
					'label'     => __( 'Background', 'jet-engine' ),
					'type'      => 'color-picker',
					'css_selector' => array(
						$block->css_selector( ' .jet-engine-arrow:hover' ) => 'background: {{VALUE}}',
					),
					'condition' => array(
						'dynamic_field_filter' => true,
						'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
					),
				)
			);

			$controls_manager->end_tab();

			$controls_manager->end_tabs();
		}

		/**
		 * Add gallery style controls for Bricks
		 */
		public function bricks_style_controls($widget) {
			$widget->register_jet_control(
				'img_slides_gap',
				[
					'tab'     => 'style',
					'label'   => esc_html__( 'Images gap', 'jet-engine' ),
					'type'    => 'number',
					'units'   => true,
					'css'     => [
						[
							'property' => '--column-gap',
							'selector' => '.jet-engine-gallery-slider',
						],
					],
					'required' => [ 'filter_callback', '=', 'jet_engine_img_gallery_slider' ],
				]
			);

			$widget->register_jet_control(
				'arrows_box_size',
				[
					'tab'      => 'style',
					'label'    => esc_html__( 'Slider arrows box size', 'jet-engine' ),
					'type'     => 'number',
					'units'    => true,
					'css'      => [
						[
							'property' => 'width',
							'selector' => '.slick-arrow',
						],
						[
							'property' => 'height',
							'selector' => '.slick-arrow',
						],
						[
							'property' => 'line-height',
							'selector' => '.slick-arrow',
						],
						[
							'property' => 'margin-top',
							'selector' => '.slick-arrow',
							'value'    => 'calc( %s / -2 )',
						],
					],
					'required' => [ 'filter_callback', '=', 'jet_engine_img_gallery_slider' ],
				]
			);

			$widget->register_jet_control(
				'arrows_size',
				[
					'tab'      => 'style',
					'label'    => esc_html__( 'Slider arrows size', 'jet-engine' ),
					'type'     => 'number',
					'units'    => true,
					'css'      => [
						[
							'property' => 'font-size',
							'selector' => '.slick-arrow',
						],
					],
					'required' => [ 'filter_callback', '=', 'jet_engine_img_gallery_slider' ],
				]
			);

			$widget->register_jet_control(
				'arrows_border',
				[
					'tab'      => 'style',
					'label'    => esc_html__( 'Arrows border', 'jet-engine' ),
					'type'     => 'border',
					'css'      => [
						[
							'property' => 'border',
							'selector' => '.slick-arrow',
						],
					],
					'required' => [ 'filter_callback', '=', 'jet_engine_img_gallery_slider' ],
				]
			);

			$widget->register_jet_control(
				'arrow_color',
				[
					'tab'      => 'style',
					'label'    => esc_html__( 'Color', 'jet-engine' ),
					'type'     => 'color',
					'css'      => [
						[
							'property' => 'color',
							'selector' => '.slick-arrow',
						],
					],
					'required' => [ 'filter_callback', '=', 'jet_engine_img_gallery_slider' ],
				]
			);

			$widget->register_jet_control(
				'arrow_bg_color',
				[
					'tab'      => 'style',
					'label'    => esc_html__( 'Background color', 'jet-engine' ),
					'type'     => 'color',
					'css'      => [
						[
							'property' => 'background-color',
							'selector' => '.slick-arrow',
						],
					],
					'required' => [ 'filter_callback', '=', 'jet_engine_img_gallery_slider' ],
				]
			);
		}


		public function add_cb_args( $args = array() ) {

			$args['img_slider_cols'] = array(
				'label'              => esc_html__( 'Slides to show', 'jet-engine' ),
				'type'               => 'number',
				'default'            => 1,
				'min'                => 1,
				'max'                => 6,
				'step'               => 1,
				'frontend_available' => true,
				'responsive'         => true,
				'condition'          => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
				),
			);

			$args['img_slider_size'] = array(
				'label'     => __( 'Images Size', 'jet-engine' ),
				'type'      => 'select',
				'default'   => 'full',
				'options'   => jet_engine_get_image_sizes(),
				'condition' => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
				),
			);

			$args['img_slider_lightbox'] = array(
				'label'     => __( 'Use lightbox', 'jet-engine' ),
				'type'      => 'switcher',
				'default'   => '',
				'condition' => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_img_gallery_slider' ),
				),
			);

			return $args;
		}

		/**
		 * Callback arguments
		 *
		 * @param  [type] $args     [description]
		 * @param  [type] $callback [description]
		 * @param  [type] $settings [description]
		 * @param  [type] $widget   [description]
		 * @return [type]           [description]
		 */
		public function cb_args( $args, $callback, $settings, $widget ) {

			if ( 'jet_engine_img_gallery_slider' !== $callback ) {
				return $args;
			}

			$gallery_args = array(
				'size'             => ! empty( $settings['img_slider_size'] ) ? $settings['img_slider_size'] : 'full',
				'lightbox'         => ! empty( $settings['img_slider_lightbox'] ) ? true : false,
				'slides_to_show'   => ! empty( $settings['img_slider_cols'] ) ? absint( $settings['img_slider_cols'] ) : 1,
				'slides_to_show_t' => ! empty( $settings['img_slider_cols_tablet'] ) ? absint( $settings['img_slider_cols_tablet'] ) : 1,
				'slides_to_show_m' => ! empty( $settings['img_slider_cols_mobile'] ) ? absint( $settings['img_slider_cols_mobile'] ) : 1,
			);

			// Responsive image gallery slider for Bricks
			if ( ! empty( $settings['img_slider_cols:tablet_portrait'] ) ) {
				$gallery_args['slides_to_show_t'] = absint( $settings['img_slider_cols:tablet_portrait'] );
			}

			if ( ! empty( $settings['img_slider_cols:mobile_portrait'] ) ) {
				$gallery_args['slides_to_show_m'] = absint( $settings['img_slider_cols:mobile_portrait'] );
			}

			return array_merge( $args, array( $gallery_args ) );

		}

		public function modify_cb_args( $data ) {

			foreach ( $data['filterCallbacksArgs'] as $index => $arg ) {

				if ( 'img_slider_lightbox' !== $arg['prop'] ) {
					continue;
				}

				$data['filterCallbacksArgs'][ $index ]['label'] = __( 'Add link', 'jet-engine' );
			}

			return $data;
		}

		/**
		 * Is module supports blocks view
		 *
		 * @return [type] [description]
		 */
		public function support_blocks() {
			return true;
		}
	}

}
