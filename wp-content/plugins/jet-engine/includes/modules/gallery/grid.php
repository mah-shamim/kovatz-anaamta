<?php
/**
 * Gallery grid module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Module_Gallery_Grid' ) ) {

	/**
	 * Define Jet_Engine_Module_Gallery_Grid class
	 */
	class Jet_Engine_Module_Gallery_Grid extends Jet_Engine_Module_Base {

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gallery-grid';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Grid Gallery for Dynamic Field widget', 'jet-engine' );
		}

		/**
		 * Returns detailed information about current module for the dashboard page
		 * @return [type] [description]
		 */
		public function get_module_details() {
			return '<p>After activation, in the Callback feature of the Content settings tab of the Dynamic Field widget in Elementor page builder appears an “Images gallery grid” option.</p>
					<p>This option allows you to display the pictures added to the gallery-type meta field using JetEngine or Advanced Custom Fields plugin on the website’s page as a grid.</p>';
		}

		public function get_video_embed() {
			return 'https://www.youtube.com/embed/R-PTcKNs1Vs';
		}

		/**
		 * Returns array links to the module-related resources
		 * @return array
		 */
		public function get_module_links() {
			return array(
				array(
					'label' => 'JetEngine: Grid Gallery Options Overview within Dynamic Field Widget',
					'url'   => 'https://crocoblock.com/knowledge-base/articles/jetengine-dynamic-field-widget-grid-gallery-and-slider-gallery-options-overview/',
				),
			);
		}

		/**
		 * Module init
		 *
		 * @return void
		 */
		public function module_init() {

			// Common
			add_filter( 'jet-engine/listings/allowed-callbacks', array( $this, 'add_grid_cb' ) );
			add_filter( 'jet-engine/listing/dynamic-field/callback-args', array( $this, 'cb_args' ), 10, 4 );
			add_filter( 'jet-engine/listings/allowed-callbacks-args', array( $this, 'add_cb_args' ) );
			add_action( 'jet-engine/listing/dynamic-field/misc-style-controls', array( $this, 'style_controls' ) );
			
			// Blocks-specific
			add_action( 'jet-engine/blocks-views/dynamic-field/misc-style-controls', array( $this, 'block_style_controls' ), 10, 2 );
			add_filter( 'jet-engine/blocks-views/editor-data', array( $this, 'modify_cb_args' ) );

			// Bricks-specific
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

			if ( ! empty( $settings['dynamic_field_filter'] ) && 'jet_engine_img_gallery_grid' === $settings['filter_callback'] ) {
				wp_enqueue_style( 'bricks-photoswipe' );
				wp_enqueue_script( 'bricks-photoswipe' );
				wp_enqueue_script( 'bricks-photoswipe-lightbox' );
			}

		}

		/**
		 * Add grid gallery to callbacks
		 *
		 * @param array $callbacks
		 *
		 * @return array
		 */
		public function add_grid_cb( $callbacks = array() ) {
			$callbacks['jet_engine_img_gallery_grid'] = __( 'Images gallery grid', 'jet-engine' );

			return $callbacks;
		}

		/**
		 * Add gallery style controls
		 *
		 * @param  [type] $widget [description]
		 *
		 * @return [type]         [description]
		 */
		public function style_controls( $widget ) {

			$prefix = $widget->prevent_wrap() ? '__content' : '';

			$widget->add_responsive_control(
				'img_gallery_gap',
				array(
					'label'      => __( 'Images gap', 'jet-engine' ),
					'type'       => Elementor\Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range'      => array(
						'px' => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'  => array(
						$widget->css_selector( $prefix . ' .jet-engine-gallery-grid__item' ) => 'padding: calc( {{SIZE}}{{UNIT}}/2 );',
						$widget->css_selector( $prefix . ' .jet-engine-gallery-grid' )       => 'margin: calc( -{{SIZE}}{{UNIT}}/2 );',
					),
					'condition'  => array(
						'dynamic_field_filter' => 'yes',
						'filter_callback'      => array( 'jet_engine_img_gallery_grid' ),
					),
				)
			);

			$widget->start_controls_tabs(
				'tabs_overlay_style',
				array(
					'condition' => array(
						'dynamic_field_filter' => 'yes',
						'filter_callback'      => array(
							'jet_engine_img_gallery_grid',
							'jet_engine_img_gallery_slider'
						),
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
						'filter_callback'      => array(
							'jet_engine_img_gallery_grid',
							'jet_engine_img_gallery_slider'
						),
					),
				)
			);
		}

		public function block_style_controls( $controls_manager, $block ) {

			$controls_manager->add_responsive_control(
				array(
					'id'           => 'img_gallery_gap',
					'label'        => __( 'Images gap', 'jet-engine' ),
					'type'         => 'range',
					'units'        => array(
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
						$block->css_selector( ' .jet-engine-gallery-grid__item' ) => 'padding: calc( {{VALUE}}{{UNIT}}/2 );',
						$block->css_selector( ' .jet-engine-gallery-grid' )       => 'margin: calc( -{{VALUE}}{{UNIT}}/2 );',
					),
					'condition'    => array(
						'dynamic_field_filter' => true,
						'filter_callback'      => array( 'jet_engine_img_gallery_grid' ),
					),
				)
			);

		}

		/**
		 * Add gallery style controls for Bricks
		 */
		public function bricks_style_controls( $widget ) {
			$widget->register_jet_control(
				'img_gallery_gap',
				[
					'tab'   => 'style',
					'label' => esc_html__( 'Images gap', 'jet-engine' ),
					'type'  => 'number',
					'units' => true,
					'css'   => [
						[
							'property' => '--gap',
							'selector' => '.jet-engine-gallery-grid',
						],
					],
					'required' => [ 'filter_callback', '=', 'jet_engine_img_gallery_grid' ],
				]
			);
		}

		public function add_cb_args( $args = array() ) {

			$args['img_columns'] = array(
				'label'      => esc_html__( 'Columns', 'jet-engine' ),
				'type'       => 'number',
				'default'    => 3,
				'min'        => 1,
				'max'        => 6,
				'step'       => 1,
				'responsive' => true,
				'selectors'  => array(
					'{{WRAPPER}} .jet-listing-dynamic-field .jet-engine-gallery-grid' => '--columns: {{VALUE}}',
				),
				'condition'  => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_img_gallery_grid' ),
				),
			);

			$args['img_gallery_size'] = array(
				'label'     => __( 'Images Size', 'jet-engine' ),
				'type'      => 'select',
				'default'   => 'full',
				'options'   => jet_engine_get_image_sizes(),
				'condition' => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_img_gallery_grid' ),
				),
			);

			$args['img_gallery_lightbox'] = array(
				'label'     => __( 'Use lightbox', 'jet-engine' ),
				'type'      => 'switcher',
				'default'   => '',
				'condition' => array(
					'dynamic_field_filter' => 'yes',
					'filter_callback'      => array( 'jet_engine_img_gallery_grid' ),
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
		 *
		 * @return [type]           [description]
		 */
		public function cb_args( $args, $callback, $settings, $widget ) {

			if ( 'jet_engine_img_gallery_grid' !== $callback ) {
				return $args;
			}

			$gallery_args = array(
				'size'        => ! empty( $settings['img_gallery_size'] ) ? $settings['img_gallery_size'] : 'full',
				'lightbox'    => ! empty( $settings['img_gallery_lightbox'] ) ? true : false,
				'cols_desk'   => ! empty( $settings['img_columns'] ) ? $settings['img_columns'] : 3,
				'cols_tablet' => ! empty( $settings['img_columns_tablet'] ) ? $settings['img_columns_tablet'] : 3,
				'cols_mobile' => ! empty( $settings['img_columns_mobile'] ) ? $settings['img_columns_mobile'] : 1,
			);

			return array_merge( $args, array( $gallery_args ) );

		}

		public function modify_cb_args( $data ) {

			foreach ( $data['filterCallbacksArgs'] as $index => $arg ) {

				if ( 'img_gallery_lightbox' !== $arg['prop'] ) {
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
