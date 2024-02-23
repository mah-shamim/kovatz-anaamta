<?php
/**
 * Popup compatibility package
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Builder_Popup_Package' ) ) {

	/**
	 * Define Jet_Woo_Builder_Popup_Package class
	 */
	class Jet_Woo_Builder_Popup_Package {

		/**
		 * Holds JetWooBuilder quick view template
		 *
		 * @var null
		 */
		private $jet_woo_builder_qw_templates = null;

		/**
		 * Jet_Woo_Builder_Popup_Package constructor.
		 */
		public function __construct() {

			add_filter( 'jet-popup/widget-extension/widget-before-render-settings', array( $this, 'define_popups' ), 10, 2 );
			add_filter( 'jet-popup/popup-generator/before-define-popup-assets/popup-id', array( $this, 'define_popup_assets' ), 10, 2 );

			add_action( 'jet-popup/editor/widget-extension/after-base-controls', array( $this, 'register_controls' ), 10, 2 );
			add_filter( 'jet-popup/widget-extension/widget-before-render-settings', array( $this, 'pass_woo_builder_trigger' ), 10, 2 );
			add_filter( 'jet-popup/ajax-request/get-elementor-content', array( $this, 'get_popup_content' ), 10, 2 );

			add_filter( 'jet-woo-builder/templates/jet-woo-products/widget-attributes', [ $this, 'get_purchase_popup_data_attrs' ], 10, 2 );
			add_filter( 'jet-woo-builder/templates/jet-woo-products-list/widget-attributes', [ $this, 'get_purchase_popup_data_attrs' ], 10, 2 );
			add_filter( 'jet-woo-builder/jet-woo-archive-add-to-cart/widget-attributes', [ $this, 'get_purchase_popup_data_attrs' ], 10, 2 );
			add_filter( 'jet-woo-builder/jet-single-add-to-cart/widget-attributes', [ $this, 'get_purchase_popup_data_attrs' ], 10, 2 );

			// Add Quick View buttons controls to Products Grid widget
			add_action( 'elementor/element/jet-woo-products/section_dots_style/after_section_end', array( $this, 'register_quickview_button_content_controls' ), 10, 2 );
			add_action( 'elementor/element/jet-woo-products/section_dots_style/after_section_end', array( $this, 'register_quickview_button_style_controls' ), 10, 2 );
			add_action( 'elementor/element/jet-woo-products/section_general/before_section_end', array( $this, 'register_quickview_button_show_control' ), 10, 2 );
			add_action( 'jet-woo-builder/templates/jet-woo-products/quickview-button', array( $this, 'get_quickview_button_content' ) );

			// Add Quick View buttons controls to Products List widget
			add_action( 'elementor/element/jet-woo-products-list/section_button_style/after_section_end', array( $this, 'register_quickview_button_content_controls' ), 10, 2 );
			add_action( 'elementor/element/jet-woo-products-list/section_button_style/after_section_end', array( $this, 'register_quickview_button_style_controls' ), 10, 2 );
			add_action( 'elementor/element/jet-woo-products-list/section_general/before_section_end', array( $this, 'register_quickview_button_show_control' ), 10, 2 );
			add_action( 'jet-woo-builder/templates/jet-woo-products-list/quickview-button', array( $this, 'get_quickview_button_content' ) );

			// Add Quick View buttons new icon settings
			add_filter( 'jet-woo-builder/jet-woo-products-grid/settings', array( $this, 'quick_view_button_icon' ), 10, 2 );
			add_filter( 'jet-woo-builder/jet-woo-products-list/settings', array( $this, 'quick_view_button_icon' ), 10, 2 );

			//Missed woocommerce styles and scripts in quick view popup
			add_filter( 'jet-woo-builder/frontend/script-dependencies', [ $this, 'quick_view_woocommerce_scripts' ] );
			add_filter( 'jet-woo-builder/frontend/styles-dependencies', [ $this, 'quick_view_woocommerce_styles' ] );

		}

		/**
		 * Add Quick View buttons new icon settings.
		 *
		 * @param $settings
		 * @param $widget
		 *
		 * @return mixed
		 */
		public function quick_view_button_icon( $settings, $widget ) {

			if ( isset( $settings['selected_quickview_button_icon_normal'] ) || isset( $settings['quickview_button_icon_normal'] ) ) {
				$settings['selected_quickview_button_icon_normal'] = htmlspecialchars( $widget->__render_icon( 'quickview_button_icon_normal', '%s', '', false ) );
			}

			return $settings;

		}


		/**
		 * Define JetWooBuilder quick view popups
		 *
		 * @param $widget_settings
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function define_popups( $widget_settings, $settings ) {

			if ( ! isset( $settings['jet_woo_builder_qv'] ) ) {
				return $widget_settings;
			}

			$popup_id                   = $settings['jet_attached_popup'];
			$jet_woo_builder_qw_enabled = filter_var( $settings['jet_woo_builder_qv'], FILTER_VALIDATE_BOOLEAN );

			if ( $jet_woo_builder_qw_enabled && ! empty( $settings['jet_woo_builder_qv_template'] ) ) {
				$this->jet_woo_builder_qw_templates[ $popup_id ] = $settings['jet_woo_builder_qv_template'];
			}

			$this->enqueue_popup_styles( $settings['jet_attached_popup'] );

			return $widget_settings;

		}

		/**
		 * Enqueue current popup styles
		 *
		 * @param $popup_id
		 */
		public function enqueue_popup_styles( $popup_id ) {
			if ( $popup_id ) {
				if ( class_exists( 'Elementor\Core\Files\CSS\Post' ) ) {
					$css_file = new Elementor\Core\Files\CSS\Post( $popup_id );
				} else {
					$css_file = new Elementor\Post_CSS_File( $popup_id );
				}

				$css_file->enqueue();
			}
		}

		/**
		 * Define JetWooBuilder quick view content assets
		 *
		 * @param $popup_id
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function define_popup_assets( $popup_id, $settings ) {

			if ( empty( $this->jet_woo_builder_qw_templates ) ) {
				return $popup_id;
			}

			if ( isset( $this->jet_woo_builder_qw_templates[ $popup_id ] ) ) {
				$popup_id = $this->jet_woo_builder_qw_templates[ $popup_id ];
			}

			return $popup_id;

		}

		/**
		 * Register JetWooBuilder trigger
		 *
		 * @param $manager
		 *
		 * @return void [type] [description]
		 */
		public function register_controls( $manager ) {

			$manager->add_control(
				'jet_woo_builder_qv',
				array(
					'label'        => __( 'JetWooBuilder Quick View', 'jet-woo-builder' ),
					'description'  => __( 'For Products Grid and Product List widgets use Click On Custom Selector Trigger Type with .jet-quickview-button selector', 'jet-woo-builder' ),
					'type'         => Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'jet-woo-builder' ),
					'label_off'    => __( 'No', 'jet-woo-builder' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$manager->add_control(
				'jet_woo_builder_qv_template',
				array(
					'label'       => esc_html__( 'Template', 'jet-woo-builder' ),
					'label_block' => true,
					'type'        => 'jet-query',
					'query_type'  => 'post',
					'query'       => jet_woo_builder_post_type()->get_templates_query_args( 'single' ),
					'edit_button' => array(
						'active' => true,
						'label'  => __( 'Edit Template', 'jet-woo-builder' ),
					),
					'condition'   => array(
						'jet_woo_builder_qv' => 'yes',
					),
				)
			);

			$manager->add_control(
				'jet_woo_builder_cart_popup',
				[
					'label'       => __( 'JetWooBuilder Purchase Popup', 'jet-woo-builder' ),
					'description' => __( 'This option works with widgets that add products to the cart using AJAX request.', 'jet-woo-builder' ),
					'type'        => Elementor\Controls_Manager::SWITCHER,
				]
			);

			$manager->add_control(
				'jet_woo_builder_cart_popup_template',
				[
					'label'       => __( 'Purchase Popup', 'jet-woo-builder' ),
					'label_block' => true,
					'type'        => 'jet-query',
					'query_type'  => 'post',
					'query'       => apply_filters( 'jet_popup_default_query_args', [
						'post_type'      => jet_popup()->post_type->slug(),
						'order'          => 'DESC',
						'orderby'        => 'date',
						'posts_per_page' => -1,
						'post_status'    => 'publish',
					] ),
					'edit_button' => [
						'active' => true,
						'label'  => __( 'Edit Popup', 'jet-woo-builder' ),
					],
					'condition'   => [
						'jet_woo_builder_cart_popup' => 'yes',
					],
				]
			);

		}

		/**
		 * Get purchase popup data.
		 *
		 * If cart popup option enable - set appropriate popup id in data attribute.
		 *
		 * @param $attributes
		 * @param $settings
		 *
		 * @return string
		 */
		public function get_purchase_popup_data_attrs( $attributes, $settings ) {

			$popup_enable = isset( $settings['jet_woo_builder_cart_popup'] ) ? filter_var( $settings['jet_woo_builder_cart_popup'], FILTER_VALIDATE_BOOLEAN ) : false;

			if ( ! $popup_enable ) {
				return $attributes;
			}

			$popup_id   = isset( $settings['jet_woo_builder_cart_popup_template'] ) ? $settings['jet_woo_builder_cart_popup_template'] : '';
			$attributes .= sprintf(
				' data-purchase-popup-id=%s ',
				apply_filters( 'jet-woo-builder/purchase-popup-id', $popup_id )
			);

			return $attributes;

		}

		/**
		 * If jet_woo_builder_qv enabled - set appropriate key in localized popup data
		 *
		 * @param $data
		 * @param $settings
		 *
		 * @return mixed
		 */
		public function pass_woo_builder_trigger( $data, $settings ) {

			$popup_trigger  = ! empty( $settings['jet_woo_builder_qv'] );
			$popup_template = ! empty( $settings['jet_woo_builder_qv_template'] ) ? $settings['jet_woo_builder_qv_template'] : '';

			if ( $popup_trigger ) {
				$data['is-jet-woo-builder']          = true;
				$data['jet-woo-builder-qv-template'] = $popup_template;
			}

			return $data;

		}

		/**
		 * Get dynamic content related to passed post ID
		 *
		 * @param $content
		 * @param $popup_data
		 *
		 * @return mixed|void
		 */
		public function get_popup_content( $content, $popup_data ) {

			if ( empty( $popup_data['isJetWooBuilder'] ) || empty( $popup_data['productId'] ) || empty( $popup_data['templateId'] ) ) {
				return $content;
			}

			$template_id = $popup_data['templateId'];

			if ( empty( $template_id ) ) {
				return $content;
			}

			$plugin = Elementor\Plugin::instance();

			global $post;

			$post = get_post( $popup_data['productId'] );

			if ( empty( $post ) ) {
				return $content;
			}

			setup_postdata( $post );

			if ( function_exists( 'jet_engine' ) ) {
				jet_engine()->listings->data->set_current_object( $post, true );
			}

			$content = $plugin->frontend->get_builder_content( $template_id, true );
			$content = apply_filters( 'jet-woo-builder/integration/jet-popup/the_content', $content, $popup_data );

			wp_reset_postdata();

			return $content;

		}

		/**
		 * Quick view WooCommerce scripts.
		 *
		 * Added missed scripts to quick view popup.
		 *
		 * @since
		 * @since 2.1.1 Added `jet-woo-builder/compatibility/jet-popup/script-dependencies` for third-party plugins
		 *        injections. Removed JetGallery scripts handling.
		 *
		 * @param array $deps List of dependencies.
		 *
		 * @return array
		 */
		public function quick_view_woocommerce_scripts( $deps ) {

			array_push( $deps, 'wc-single-product', 'wc-add-to-cart-variation', 'flexslider' );

			return apply_filters( 'jet-woo-builder/compatibility/jet-popup/script-dependencies', $deps );

		}

		/**
		 * Added missed styles to quick view popup
		 *
		 * @param $deps
		 *
		 * @return mixed
		 */
		public function quick_view_woocommerce_styles( $deps ) {

			array_push( $deps, 'photoswipe', 'photoswipe-default-skin' );

			return $deps;

		}

		/**
		 * Get quick view button html
		 *
		 * @param $display_settings
		 */
		public function get_quickview_button_content( $display_settings ) {

			$button_classes = array(
				'jet-quickview-button',
				'jet-quickview-button__link',
				'jet-quickview-button__link--icon-' . $display_settings['quickview_button_icon_position'],
			); ?>

			<div class="jet-quickview-button__container">
				<a href="#" class="<?php echo implode( ' ', $button_classes ); ?>">
					<div class="jet-quickview-button__plane jet-quickview-button__plane-normal"></div>
					<div class="jet-quickview-button__state jet-quickview-button__state-normal">
						<?php
						if ( filter_var( $display_settings['quickview_use_button_icon'], FILTER_VALIDATE_BOOLEAN ) ) {
							printf( '<span class="jet-quickview-button__icon jet-woo-builder-icon">%s</span>', htmlspecialchars_decode( $display_settings['selected_quickview_button_icon_normal'] ) );
						}
						printf( '<span class="jet-quickview-button__label">%s</span>', esc_html__( $display_settings['quickview_button_label_normal'], 'jet-woo-builder' ) );
						?>
					</div>
				</a>
			</div>
			<?php

		}

		/**
		 * Register content controls
		 *
		 * @param       $obj
		 * @param array $args
		 */
		public function register_quickview_button_content_controls( $obj, $args ) {

			$obj->start_controls_section(
				'section_quickview_content',
				array(
					'label' => esc_html__( 'Quick View', 'jet-woo-builder' ),
				)
			);

			$obj->__add_advanced_icon_control(
				'quickview_button_icon_normal',
				array(
					'label'       => esc_html__( 'Button Icon', 'jet-woo-builder' ),
					'type'        => Elementor\Controls_Manager::ICON,
					'label_block' => true,
					'file'        => '',
					'default'     => 'fa fa-eye',
					'fa5_default' => array(
						'value'   => 'fas fa-eye',
						'library' => 'fa-solid',
					),
				)
			);

			$obj->add_control(
				'quickview_button_label_normal',
				array(
					'label'   => esc_html__( 'Button Label Text', 'jet-woo-builder' ),
					'type'    => Elementor\Controls_Manager::TEXT,
					'default' => esc_html__( 'Quick View', 'jet-woo-builder' ),
				)
			);

			$obj->add_control(
				'quickview_button_icon_settings_heading',
				array(
					'label'     => esc_html__( 'Icon', 'jet-woo-builder' ),
					'type'      => Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$obj->add_control(
				'quickview_use_button_icon',
				array(
					'label'        => esc_html__( 'Use Icon?', 'jet-woo-builder' ),
					'type'         => Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'jet-woo-builder' ),
					'label_off'    => esc_html__( 'No', 'jet-woo-builder' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$obj->add_control(
				'quickview_button_icon_position',
				array(
					'label'       => esc_html__( 'Icon Position', 'jet-woo-builder' ),
					'type'        => Elementor\Controls_Manager::SELECT,
					'options'     => array(
						'left'   => esc_html__( 'Left', 'jet-woo-builder' ),
						'top'    => esc_html__( 'Top', 'jet-woo-builder' ),
						'right'  => esc_html__( 'Right', 'jet-woo-builder' ),
						'bottom' => esc_html__( 'Bottom', 'jet-woo-builder' ),
					),
					'default'     => 'left',
					'render_type' => 'template',
					'condition'   => array(
						'quickview_use_button_icon' => 'yes',
					),
				)
			);

			$obj->end_controls_section();

		}

		/**
		 * Register style controls
		 *
		 * @param       $obj
		 * @param array $args
		 */
		public function register_quickview_button_style_controls( $obj, $args ) {

			$css_scheme = apply_filters(
				'jet-quickview-button/quickview-button/css-scheme',
				array(
					'container'    => '.jet-quickview-button__container',
					'button'       => '.jet-quickview-button__link',
					'plane_normal' => '.jet-quickview-button__plane-normal',
					'state_normal' => '.jet-quickview-button__state-normal',
					'icon_normal'  => '.jet-quickview-button__state-normal .jet-quickview-button__icon',
					'label_normal' => '.jet-quickview-button__state-normal .jet-quickview-button__label',
				)
			);

			/**
			 * General Style Section
			 */
			$obj->start_controls_section(
				'section_button_quickview_general_style',
				[
					'tab'        => Elementor\Controls_Manager::TAB_STYLE,
					'label'      => __( 'Quick View', 'jet-woo-builder' ),
				]
			);

			$obj->add_control(
				'quickview_custom_size',
				[
					'type'         => Elementor\Controls_Manager::SWITCHER,
					'label'        => __( 'Custom Size', 'jet-woo-builder' ),
				]
			);

			$obj->add_responsive_control(
				'quickview_button_custom_width',
				array(
					'label'      => esc_html__( 'Custom Width', 'jet-woo-builder' ),
					'type'       => Elementor\Controls_Manager::SLIDER,
					'size_units' => array(
						'px',
						'em',
						'%',
					),
					'range'      => array(
						'px' => array(
							'min' => 40,
							'max' => 1000,
						),
						'%'  => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . $css_scheme['button'] => 'width: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'quickview_custom_size' => 'yes',
					),
				)
			);

			$obj->add_responsive_control(
				'quickview_button_custom_height',
				array(
					'label'      => esc_html__( 'Custom Height', 'jet-woo-builder' ),
					'type'       => Elementor\Controls_Manager::SLIDER,
					'size_units' => array(
						'px',
						'em',
						'%',
					),
					'range'      => array(
						'px' => array(
							'min' => 10,
							'max' => 1000,
						),
						'%'  => array(
							'min' => 0,
							'max' => 100,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . $css_scheme['button'] => 'height: {{SIZE}}{{UNIT}};',
					),
					'condition'  => array(
						'quickview_custom_size' => 'yes',
					),
				)
			);

			$obj->add_group_control(
				Elementor\Group_Control_Typography::get_type(),
				[
					'name'     => 'quickview_button_typography',
					'selector' => '{{WRAPPER}} ' . $css_scheme['button'] . ',{{WRAPPER}} ' . $css_scheme['label_normal'],
				]
			);

			$obj->start_controls_tabs( 'quickview_button_style_tabs' );

			$obj->start_controls_tab(
				'quickview_button_normal_styles',
				array(
					'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
				)
			);

			$obj->add_control(
				'quickview_button_normal_color',
				array(
					'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
					'type'      => Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $css_scheme['label_normal'] => 'color: {{VALUE}}',
						'{{WRAPPER}} ' . $css_scheme['icon_normal']  => 'color: {{VALUE}}',
					),
				)
			);

			$obj->add_control(
				'quickview_button_normal_background',
				array(
					'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
					'type'      => Elementor\Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Elementor\Core\Schemes\Color::get_type(),
						'value' => Elementor\Core\Schemes\Color::COLOR_1,
					),
					'selectors' => array(
						'{{WRAPPER}} ' . $css_scheme['button'] . ' ' . $css_scheme['plane_normal'] => 'background-color: {{VALUE}}',
					),
				)
			);

			$obj->end_controls_tab();

			$obj->start_controls_tab(
				'quickview_button_hover_styles',
				array(
					'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
				)
			);

			$obj->add_control(
				'quickview_button_hover_color',
				array(
					'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
					'type'      => Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $css_scheme['button'] . ':hover ' . $css_scheme['label_normal'] => 'color: {{VALUE}}',
						'{{WRAPPER}} ' . $css_scheme['button'] . ':hover ' . $css_scheme['icon_normal']  => 'color: {{VALUE}}',
					),
				)
			);

			$obj->add_control(
				'quickview_button_hover_background',
				array(
					'label'     => esc_html__( 'Background Color', 'jet-woo-builder' ),
					'type'      => Elementor\Controls_Manager::COLOR,
					'scheme'    => array(
						'type'  => Elementor\Core\Schemes\Color::get_type(),
						'value' => Elementor\Core\Schemes\Color::COLOR_4,
					),
					'selectors' => array(
						'{{WRAPPER}} ' . $css_scheme['button'] . ':hover ' . $css_scheme['plane_normal'] => 'background-color: {{VALUE}}',
					),
				)
			);

			$obj->add_control(
				'quickview_button_border_hover_color',
				array(
					'label'     => esc_html__( 'Border Color', 'jet-woo-builder' ),
					'type'      => Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $css_scheme['button'] . ':hover ' . $css_scheme['plane_normal'] => 'border-color: {{VALUE}}',
					),
					'condition' => array(
						'quickview_button_border_border!' => '',
					),
				)
			);

			$obj->end_controls_tab();

			$obj->end_controls_tabs();

			$obj->add_control(
				'quickview_button_border_radius',
				array(
					'label'      => esc_html__( 'Border Radius', 'jet-woo-builder' ),
					'type'       => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} ' . $css_scheme['button']       => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						'{{WRAPPER}} ' . $css_scheme['plane_normal'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$obj->add_responsive_control(
				'quickview_button_alignment',
				array(
					'label'     => esc_html__( 'Alignment', 'jet-woo-builder' ),
					'type'      => Elementor\Controls_Manager::CHOOSE,
					'default'   => 'center',
					'options'   => jet_woo_builder_tools()->get_available_flex_h_align_types(),
					'selectors' => array(
						'{{WRAPPER}} ' . $css_scheme['container'] => 'justify-content: {{VALUE}};',
					),
					'separator' => 'before',
				)
			);

			$obj->add_responsive_control(
				'quickview_button_padding',
				array(
					'label'      => __( 'Padding', 'jet-woo-builder' ),
					'type'       => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} ' . $css_scheme['button'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$obj->add_responsive_control(
				'quickview_button_margin',
				array(
					'label'      => __( 'Margin', 'jet-woo-builder' ),
					'type'       => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} ' . $css_scheme['button'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$obj->add_control(
				'quickview_button_icon_heading',
				array(
					'label'     => esc_html__( 'Icon', 'jet-woo-builder' ),
					'type'      => Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$obj->start_controls_tabs( 'tabs_quickview_icon_styles' );

			$obj->start_controls_tab(
				'tab_quickview_icon_normal',
				array(
					'label' => esc_html__( 'Normal', 'jet-woo-builder' ),
				)
			);

			$obj->add_control(
				'normal_quickview_icon_color',
				array(
					'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
					'type'      => Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $css_scheme['icon_normal'] . ' i' => 'color: {{VALUE}}',
					),
				)
			);

			$obj->add_responsive_control(
				'normal_quickview_icon_font_size',
				array(
					'label'      => esc_html__( 'Font Size', 'jet-woo-builder' ),
					'type'       => Elementor\Controls_Manager::SLIDER,
					'size_units' => array(
						'px',
						'em',
						'rem',
					),
					'range'      => array(
						'px' => array(
							'min' => 1,
							'max' => 100,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} ' . $css_scheme['icon_normal'] => 'font-size: {{SIZE}}{{UNIT}}',
					),
				)
			);

			$obj->add_responsive_control(
				'normal_quickview_icon_margin',
				array(
					'label'      => __( 'Margin', 'jet-woo-builder' ),
					'type'       => Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} ' . $css_scheme['icon_normal'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			$obj->end_controls_tab();

			$obj->start_controls_tab(
				'tab_quickview_icon_hover',
				array(
					'label' => esc_html__( 'Hover', 'jet-woo-builder' ),
				)
			);

			$obj->add_control(
				'quickview_icon_color_hover',
				array(
					'label'     => esc_html__( 'Color', 'jet-woo-builder' ),
					'type'      => Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} ' . $css_scheme['button'] . ':hover ' . $css_scheme['icon_normal'] . ' i' => 'color: {{VALUE}}',
					),
				)
			);

			$obj->end_controls_tab();

			$obj->end_controls_tabs();

			$obj->end_controls_section();

		}

		/**
		 * Register displaying controls
		 *
		 * @param       $obj
		 * @param array $args
		 */
		public function register_quickview_button_show_control( $obj, $args ) {

			$obj->add_control(
				'show_quickview',
				array(
					'label'        => esc_html__( 'Show Quick View', 'jet-woo-builder' ),
					'type'         => Elementor\Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'Yes', 'jet-woo-builder' ),
					'label_off'    => esc_html__( 'No', 'jet-woo-builder' ),
					'return_value' => 'yes',
					'default'      => '',
					'separator'    => 'before',
				)
			);

			$obj->add_responsive_control(
				'quickview_button_order',
				array(
					'type'      => Elementor\Controls_Manager::NUMBER,
					'label'     => esc_html__( 'Quick View Button Order', 'jet-woo-builder' ),
					'default'   => 1,
					'min'       => 1,
					'max'       => 10,
					'step'      => 1,
					'selectors' => array(
						'{{WRAPPER}} ' . '.jet-quickview-button__container' => 'order: {{VALUE}}',
					),
				)
			);
		}

	}

}

new Jet_Woo_Builder_Popup_Package();
