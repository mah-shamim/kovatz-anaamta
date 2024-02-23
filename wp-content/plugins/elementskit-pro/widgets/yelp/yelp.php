<?php

namespace Elementor;

use \Elementor\ElementsKit_Widget_Yelp_Handler as Handler;

if (!defined('ABSPATH')) exit;

class ElementsKit_Widget_Yelp extends Widget_Base
{

    public function get_name()
    {
        return Handler::get_name();
    }

    public function get_title()
    {
        return Handler::get_title();
    }

    public function get_icon()
    {
        return Handler::get_icon();
    }

    public function get_categories()
    {
        return Handler::get_categories();
    }

	public function get_keywords() {
		return Handler::get_keywords();
	}

    public function get_help_url() {
        return 'https://wpmet.com/doc/yelp/';
    }

    private function get_stars_rating($rating) {
		$htm = '';
		for($i = 1; $i <= 5; $i++) {
			$star_icon = $i <= $rating ? 'icon-star-1' : 'icon-star1';
			$htm .= '<span class="mr-1"><i class="icon ' . $star_icon . '"></i></span>';
		}
		return $htm;
    }

    private function get_user_thumbnail($thumbnail) {
		if(!empty($thumbnail)) return $thumbnail;
        return Handler::get_url() . 'assets/images/profile-placeholder.png';
	}

	private function get_formatted_text($txt, $additional_flag, $max_len = 120) {
		$len = strlen($txt);
		if($additional_flag === true && $len > $max_len) {
            return
                '<span>'.substr($txt, 0, $max_len).'</span>'.
                '<span
                    class="more"
                    data-collapsed="true"
                    data-text="'.$txt.'"
                > ...More
                </span>'
            ;
		}
		return $txt;
	}

	protected function format_column( $settings, $control_name ){
		$column = $settings[$control_name];
		if(isset($settings[$control_name.'_tablet'])){
			$splitted = explode('ekit-fb-col-',$settings[$control_name.'_tablet']);
			$column .= ' ekit-fb-col-tablet-' . $splitted[1];
		}
		if(isset($settings[$control_name.'_mobile'])){
			$splitted = explode('ekit-fb-col-',$settings[$control_name.'_mobile']);
			$column .= ' ekit-fb-col-mobile-' . $splitted[1];
		}
		return $column;
	}

	protected function get_slideshow_column( $settings, $slides_to_show, $slides_to_scroll ){
		$responsive = [];
		$slides_to_show_tablet = !empty($settings[$slides_to_show . "_tablet"])? esc_attr($settings[$slides_to_show . "_tablet"]) : '2';
		$slides_to_show_mobile = !empty($settings[$slides_to_show . "_mobile"]) ? esc_attr($settings[$slides_to_show . "_mobile"]) : '1';
		$slides_to_show_desktop = esc_attr($settings[$slides_to_show]);

		$slides_to_scroll_tablet = !empty($settings[$slides_to_scroll . "_tablet"]) ? esc_attr($settings[$slides_to_scroll . "_tablet"]) : '1';
		$slides_to_scroll_mobile = !empty($settings[$slides_to_scroll . "_mobile"]) ? esc_attr($settings[$slides_to_scroll . "_mobile"]) : '1';
		$slides_to_scroll_desktop = esc_attr($settings[$slides_to_scroll]);

		if($slides_to_show_mobile || $slides_to_scroll_mobile) {
			$settings_mobile = [];
			if($slides_to_show_mobile) {
				$settings_mobile['slidesPerView'] = (int) $slides_to_show_mobile;
			}
			if($slides_to_scroll_mobile) {
				$settings_mobile['slidesPerGroup'] = (int) $slides_to_scroll_mobile;
			}
			$responsive[320] = $settings_mobile;
		}

		if($slides_to_show_tablet || $slides_to_scroll_tablet) {
			$settings_tablet = [];
			if($slides_to_show_tablet) {
				$settings_tablet['slidesPerView'] = (int) $slides_to_show_tablet;
			}
			if($slides_to_scroll_tablet) {
				$settings_tablet['slidesPerGroup'] = (int) $slides_to_scroll_tablet;
			}

			$responsive[768] = $settings_tablet;
		}

		if($slides_to_show || $slides_to_scroll) {
			$settings_desktop = [];
			if($slides_to_show) {
				$settings_desktop['slidesPerView'] = (int) $slides_to_show_desktop;
			}
			if($slides_to_scroll) {
				$settings_desktop['slidesPerGroup'] = (int) $slides_to_scroll_desktop;
			}

			$responsive[1024] = $settings_desktop;
		}

		return $responsive;
	}

	/**
	 * Convert number or array of number to dimension format
	 *
	 * @param number|array	$value		16 | [0, 0 , 16, 0 ]
	 * @param string		$unit		px | em | rem | % | vh | vw
	 * @param boolean		$linked		true | false
	 * @return array 		
	 *	[ 
	 *		'top' 		=> '16', 		'right' 	=> '16', 
	 *		'bottom' 	=> '16', 		'left' 		=> '16', 
	 *		'unit' 		=> 'px', 		'isLinked' 	=> true 
	 *	];
	 */
	 private function get_dimension( $value = 1, $unit = 'em', $linked = true ){
        $is_arr = is_array( $value );
        return [
			'top'      => strval($is_arr ? $value[0] : $value), 
			'right'    => strval($is_arr ? $value[1] : $value),
			'bottom'   => strval($is_arr ? $value[2] : $value), 
			'left'     => strval($is_arr ? $value[3] : $value),
            'unit'     => $unit, 'isLinked' =>  $linked,
        ];
    }

	private function controls_section( $config, $callback ){

		// New configs
		$newConfig = [ 'label' => $config['label'] ];
		
		// Formatting configs
		if(isset($config['tab'])) $newConfig['tab'] = $config['tab'];
		if(isset($config['condition'])) $newConfig['condition'] = $config['condition'];

		// Start section
		$this->start_controls_section( $config['key'],  $config);

		// Call the callback function
		call_user_func(array($this, $callback));

		// End section
		$this->end_controls_section();
	}

	private function control_border($key, $selectors, $config = [ 'default' => '8', 'unit' => 'px' ], $condition = NULL){
		
		$selectors = array_map( function($selector) { return "{{WRAPPER}} " . $selector ;}, $selectors );

		// Check if $condition is null, assign an empty string to $conditions if it is, or assign the value of $condition if it's not
		$conditions = is_null($condition) ? '' : $condition;

		// Border heading
		$this->add_control( $key, [
			'label'     => esc_html__('Border', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => $conditions,
		]);

		// Review card border
		$this->add_group_control(
			Group_Control_Border::get_type(), [
				'name'     => $key . '_type',
				'label'    => esc_html__('Border Type', 'elementskit'),
				'selector' => implode(',', $selectors),
				'condition' => $conditions,
			]
		);

		$new_selectors = array();
		$border_radius = 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';
		foreach ($selectors as $key) { $new_selectors[$key] = $border_radius; }

		// Review card border radius
		$this->add_control( $key . '_radius', [
			'label'			=> esc_html__('Border Radius', 'elementskit'),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> ['px', '%', 'em'],
			'selectors'		=> $new_selectors,
			'condition' => $conditions,
			'default'    => [
				'top'      => $config['default'], 'right'	=> $config['default'],
				'bottom'   => $config['default'], 'left'	=> $config['default'],
				'unit'     => $config['unit'], 'isLinked' => true,
			]
		]);
	}

	private function control_text( $key, $selector, $exclude = [], $config = [] ){

		// Page name color
		$this->add_control( $key . '_color', [
			'label'     => __('Text Color', 'elementskit'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} '. $selector => 'color: {{VALUE}}',
			],
		]);

		if(!in_array("shadow", $exclude)){
			// Page name text shadow
			$this->add_group_control(
				Group_Control_Text_Shadow::get_type(), [
					'name' => $key . '_text_shadow',
					'label' => __( 'Text Shadow', 'elementskit' ),
					'selector' => '{{WRAPPER}} ' . $selector
				]
			);
		}

		if(!in_array("typography", $exclude)){
			// Page name typography
			$this->add_group_control(
				Group_Control_Typography::get_type(), [
					'name'     => $key . '_typography',
					'label'    => __('Typography', 'elementskit'),
					'selector' => '{{WRAPPER}} ' . $selector
				]
			);
		}

		if(!in_array("margin", $exclude)){ 
			// controls_section_overview_page_name_margin
			$value = '{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};';

			$def_margin = isset($config['def_margin']) 
				? $config['def_margin'] : [ 'bottom' => '16', 'unit' => 'px', 'isLinked' => false ];

			$this->add_responsive_control( $key . '_margin', [
				'label'          => esc_html__('Margin', 'elementskit'),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => ['px', '%', 'em'],
				'default'        => $def_margin,
				'tablet_default' => $def_margin,
				'mobile_default' => $def_margin,
				'selectors'      => [ '{{WRAPPER}} ' . $selector => 'margin:' . $value ],
			]);
		}
	}

	private function controls_section_layout(){

		// ekit_review_styles
		$this->add_control(
			'ekit_review_styles',
			[
				'label'   => esc_html__('Layout Styles', 'elementskit'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'reviews',
				'options' => [
					'reviews'   => esc_html__('With Reviews', 'elementskit'),
					'slideshow' => esc_html__('Slideshow', 'elementskit'),
				],
			]
		);

        // ekit_yelp_review_only_positive
		$this->add_control(
			'ekit_yelp_review_only_positive',
			[
				'label'     => esc_html__('Review Type', 'elementskit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'both',
				'options'   => [
					'both'     => esc_html__('Both', 'elementskit'),
					'positive' => esc_html__('Only Positive', 'elementskit'),
				],
				'condition' => [
					'ekit_review_styles!' => 'default',
				],
			]
		);

        // ekit_review_card_type
		$this->add_control(
			'ekit_review_card_type',
			[
				'label'     => esc_html__('Card Type', 'elementskit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'default',
				'options'   => [
					'default' => esc_html__('Box Card', 'elementskit'),
					'bubble'  => esc_html__('Bubble Card', 'elementskit'),
				],
				'condition' => [
					'ekit_review_styles!' => 'default',
				],
			]
        );

        // ekit_review_card_appearance
		$this->add_control(
			'ekit_review_card_appearance',
			[
				'label'     => esc_html__('Card Appearance', 'elementskit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'masonry',
				'options'   => [
					'grid'    => esc_html__('Grid', 'elementskit'),
					'masonry' => esc_html__('Masonry', 'elementskit'),
					'list'    => esc_html__('List', 'elementskit'),
				],
				'condition' => [
					'ekit_review_styles' => 'reviews',
				],
			]
		);

		$this->add_responsive_control(
			'ekit_review_responsive_column',
			[
				'label'     => esc_html__('Column Count', 'elementskit'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'ekit-fb-col-4',
				'tablet_default'   => 'ekit-fb-col-6',
				'mobile_default'   => 'ekit-fb-col-12',
				'options'   => [
					'ekit-fb-col-12' => esc_html__('1 Columns', 'elementskit'),
					'ekit-fb-col-6' => esc_html__('2 Columns', 'elementskit'),
					'ekit-fb-col-4' => esc_html__('3 Columns', 'elementskit'),
					'ekit-fb-col-3' => esc_html__('4 Columns', 'elementskit'),
					'ekit-fb-col-2' => esc_html__('6 Columns', 'elementskit'),
				],
				'condition' => [
					'ekit_review_styles' => 'reviews',
				],
			]
		);
        
        // Grid column gap
		$this->add_responsive_control( 'ekit_review_grid_column_gap', [
				'label'           => esc_html__('Column Gap', 'elementskit'),
				'type'            => Controls_Manager::SLIDER,
				'size_units'      => ['px','em'],
				'range'           => [
					'px' => [ 'min'  => 0, 'max'  => 96, 'step' => 2 ],
					'em' => [ 'min'  => 0, 'max'  => 6, 'step' => 0.2 ]
				],
				'devices'         => ['desktop', 'tablet', 'mobile'],
				'tablet_default'  => [ 'size' => 8, 'unit' => 'px' ],
				'mobile_default'  => [ 'size' => 1, 'unit' => 'em' ],
				'default'         => [ 'size' => 24, 'unit' => 'px' ],
				'selectors'       => [
                    '{{WRAPPER}} .ekit-review-cards.ekit-review-cards-grid .row > div' =>  'padding-left: {{SIZE}}{{UNIT}};
                        padding-right:calc({{SIZE}}{{UNIT}} / 2);margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'       => [
					'ekit_review_styles'          => 'reviews',
					'ekit_review_card_appearance' => 'grid',
				],
			]
		);

		// Masonry Column gap
		$this->add_responsive_control(
			'ekit_review_masonry_column_gap', [
				'label'           => esc_html__('Column Gap', 'elementskit'),
				'type'            => Controls_Manager::SLIDER,
				'size_units'      => ['px','em'],
				'range'           => [
					'px' => [ 'min'  => 0, 'max'  => 96, 'step' => 2 ],
					'em' => [ 'min'  => 0, 'max'  => 6, 'step' => 0.2 ]
				],
				'devices'         => ['desktop', 'tablet', 'mobile'],
				'tablet_default'  => [ 'size' => 8, 'unit' => 'px' ],
				'mobile_default'  => [ 'size' => 1, 'unit' => 'em' ],
				'default'         => [ 'size' => 24, 'unit' => 'px' ],
				'selectors'       => [
					'{{WRAPPER}} .ekit-review-cards.ekit-review-cards-masonry .masonry' => 'column-gap: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-review-cards.ekit-review-cards-masonry .masonry' .' > div' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'       => [
					'ekit_review_styles'          => 'reviews',
					'ekit_review_card_appearance' => 'masonry',
				],
			]
        );
	}

	private function controls_section_contents(){

		// ekit_review_card_thumbnail_badge
		$this->add_control(
			'ekit_review_card_thumbnail_badge',
			[
				'label'        => esc_html__('Thumbnail Badge', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'ekit_review_styles!' => 'default',
				],
			]
		);

		// ekit_review_card_align_center
		$this->add_control(
			'ekit_review_card_align_center',
			[
				'label'        => esc_html__('Align Content Center', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'ekit_review_card_type' => 'default',
					'ekit_review_styles!'   => 'default',
				],
			]
		);

		// ekit_review_card_thumbnail_left
		$this->add_control(
			'ekit_review_card_thumbnail_left',
			[
				'label'        => esc_html__('Thumbnail Left', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'ekit_review_styles!'              => 'default',
					'ekit_review_card_type'            => 'default',
					'ekit_review_card_align_center!'   => 'yes',
					'ekit_review_card_name_at_bottom!' => 'yes',
				],
			]
		);

		// ekit_review_card_stars_inline
		$this->add_control(
			'ekit_review_card_stars_inline',
			[
				'label'        => esc_html__('Stars Inline', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'ekit_review_styles!'              => 'default',
					'ekit_review_card_type'            => 'default',
					'ekit_review_card_align_center!'   => 'yes',
					'ekit_review_card_name_at_bottom!' => 'yes',
				],
			]
		);

		// ekit_review_card_posted_on
		$this->add_control(
			'ekit_review_card_posted_on',
			[
				'label'        => esc_html__('Bottom Posted On', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => [
					'ekit_review_card_type' => 'default',
					'ekit_review_styles!'   => 'default',
				],
			]
		);

		// ekit_review_card_name_at_bottom
		$this->add_control(
			'ekit_review_card_name_at_bottom',
			[
				'label'        => esc_html__('Name at Bottom', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
				'condition'    => [
					'ekit_review_card_type' => 'default',
					'ekit_review_styles!'   => 'default',
				],
			]
		);

		// ekit_review_card_top_right_logo
		$this->add_control(
			'ekit_review_card_top_right_logo',
			[
				'label'        => esc_html__('Top Right Logo', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

	}

	private function controls_section_slideshow(){

		// Left right spacing
		$this->add_responsive_control(
			'ekit_review_slideshow_left_right_spacing',
			[
				'label'           => esc_html__('Spacing Left Right', 'elementskit'),
				'type'            => Controls_Manager::SLIDER,
				'size_units'      => ['px'],
				'range'           => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 1,
					],
				],
				'devices'         => ['desktop', 'tablet', 'mobile'],
				'desktop_default' => [
					'size' => 15,
					'unit' => 'px',
				],
				'tablet_default'  => [
					'size' => 10,
					'unit' => 'px',
				],
				'mobile_default'  => [
					'size' => 10,
					'unit' => 'px',
				],
				'default'         => [
					'size' => 15,
					'unit' => 'px',
				],
				'render_type' => 'template',
				'selectors'       => [
					'{{WRAPPER}} .ekit-review-slider-wrapper-yelp' => '--ekit_review_slider_left_right_spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Slides to show
		$this->add_responsive_control(
			'ekit_review_slideshow_slides_to_show',
			[
				'label'   => esc_html__('Slides To Show', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 5,
				'step'    => 1,
				'default' => 3,
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'render_type' => 'template',
				'{{WRAPPER}} .ekit-review-slider-wrapper-yelp' => '--ekit_review_slider_slidetoshow: {{SIZE}};',
			]
		);

		// Slides to scroll
		$this->add_responsive_control(
			'ekit_review_slideshow_slides_to_scroll',
			[
				'label'   => esc_html__('Slides To Scroll', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 20,
				'step'    => 1,
				'default' => 1,
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
			]
		);

		// Slideshow speed
		$this->add_control(
			'ekit_review_slideshow_speed',
			[
				'label'   => esc_html__('Speed', 'elementskit'),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 10000,
				'step'    => 1,
				'default' => 1000,
			]
		);

		// Slideshow autoplay
		$this->add_control(
			'ekit_review_slideshow_autoplay',
			[
				'label'        => esc_html__('Autoplay', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('Yes', 'elementskit'),
				'label_off'    => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		// Show arrows
		$this->add_control(
			'ekit_review_slideshow_show_arrow',
			[
				'label'        => esc_html__('Show arrow', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('Yes', 'elementskit'),
				'label_off'    => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		//Show dot
		$this->add_control(
			'ekit_review_slideshow_show_dot',
			[
				'label'        => esc_html__('Show dots', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('Yes', 'elementskit'),
				'label_off'    => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default'      => '',
			]
		);

		// Pause on hover
		$this->add_control(
			'ekit_review_slideshow_pause_on_hover',
			[
				'label'        => esc_html__('Pause on Hover', 'elementskit'),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__('Yes', 'elementskit'),
				'label_off'    => esc_html__('No', 'elementskit'),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'carousel_loop',
			[
				'label' => esc_html__( 'Enable Loop?', 'elementskit' ),
				'type' =>   Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Yes', 'elementskit' ),
				'label_off' => esc_html__( 'No', 'elementskit' ),
				'return_value' => 'yes',
				'default' => '',
			]
        );

		$this->add_control(
			'slideshow_left_arrows',
			[
				'label' => esc_html__( 'Left Arrow', 'elementskit' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'slideshow_left_arrow',
                'default' => [
                    'value' => 'icon icon-left-arrow2',
                    'library' => 'ekiticons',
                ],
                'condition' => [
					'ekit_review_slideshow_show_arrow' => 'yes',
                ]
			]
        );

        $this->add_control(
			'slideshow_right_arrows',
			[
				'label' => esc_html__( 'Right Arrow', 'elementskit' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'slideshow_right_arrow',
                'default' => [
                    'value' => 'icon icon-right-arrow2',
                    'library' => 'ekiticons',
                ],
                'condition' => [
                    'ekit_review_slideshow_show_arrow' => 'yes',
                ]
			]
		);
	}

	private function controls_section_widget(){

		// ekit_review_widget_background
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'ekit_review_widget_background',
				'label'    => esc_html__('Widget Background', 'elementskit'),
				'types'    => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .ekit-review-wrapper-yelp',
			]
		);

		// ekit_review_widget_padding_only_overview
		$this->add_responsive_control(
			'ekit_review_widget_padding_only_overview',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'default'    => $this->get_dimension(1),
				'selectors'  => [
					'{{WRAPPER}} .ekit-review-wrapper-yelp' => 
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				"condition"  => [
					'ekit_review_styles' => 'default',
				],
			]
		);

		// ekit_review_widget_padding
		$this->add_responsive_control(
			'ekit_review_widget_padding',
			[
				'label'      => esc_html__('Padding', 'elementskit'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .ekit-review-wrapper-yelp' => 
						'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default'           => $this->get_dimension(2),
				'tablet_default'    => $this->get_dimension(1),
				'mobile_default'    => $this->get_dimension(8 , 'px'),
				"condition"  => [ 'ekit_review_styles!' => 'default' ],
			]
		);

		// ekit_yelp_review_widget_border
		$this->control_border(
			'ekit_yelp_review_widget_border', 
            [ '.ekit-review-wrapper-yelp' ],
            [ 'default' => '0', 'unit' => 'px' ]
		);

	}

	private function controls_section_header_button(){

		// Header button typography
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'		 => 'ekit_yelp_review_ss_header_button_typography',
			'selector'	 => '{{WRAPPER}} .ekit-review-overview--actions .btn-primary',
		]);

		// Header button border radius
		$this->control_border(
			'ekit_yelp_review_ss_header_button_border', [ '.ekit-review-overview--actions .btn-primary' ],
			[ 'default' => '2', 'unit' => 'em' ]
		);
		
		// Header button tabs
		$this->start_controls_tabs( 'ekit_yelp_review_ss_header_button_tabs' );

		// Header button tab normal
        $this->start_controls_tab(
            'ekit_yelp_review_ss_header_button_tab_normal', [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
		);

		// Header button background color
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'ekit_yelp_review_ss_header_button_bg_color_normal',
				'label'    => esc_html__('Background', 'elementskit'),
				'types'    => ['classic'],
				'selector' => '{{WRAPPER}} .ekit-review-overview--actions .btn-primary',
			]
		);

		// Header button color
		$this->add_control( 'ekit_yelp_review_ss_header_button_color_normal',
			[
				'label'     => __('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-review-overview--actions .btn-primary' => 'color: {{VALUE}}',
				],
			]
		);

		// Header button border color
		$this->add_control( 'ekit_yelp_review_ss_header_button_border_color_normal',
			[
				'label'     => __('Border Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-review-overview--actions .btn-primary' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		// Header button tab hover
        $this->start_controls_tab(
            'ekit_yelp_review_ss_header_button_tab_hover', [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
		);

		// Header button background color hover
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'ekit_yelp_review_ss_header_button_bg_color_hover',
				'label'    => esc_html__('Background', 'elementskit'),
				'types'    => ['classic'],
				'selector' => '{{WRAPPER}} .ekit-review-overview--actions .btn-primary' .':hover',
			]
		);

		// Header button color hover
		$this->add_control( 'ekit_yelp_review_ss_header_button_color_hover',
			[
				'label'     => __('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-review-overview--actions .btn-primary' .':hover' => 'color: {{VALUE}}',
				],
			]
		);

		// Header button border color hover
		$this->add_control( 'ekit_yelp_review_ss_header_button_border_color_hover',
			[
				'label'     => __('Border Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-review-overview--actions .btn-primary' .':hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

	}

	private function controls_section_overview_buttons(){

		// Overviews buttons typography
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'		 => 'ekit_yelp_review_ss_oo_buttons_typography',
			'selector'	 => '{{WRAPPER}} .ekit-review-card--actions .btn',
		]);

		// See all reviews
		$this->add_control( 'ekit_yelp_review_ss_oo_sar_button', [
			'label'     => esc_html__('Sell all reviews', 'elementskit'),
			'type'      => Controls_Manager::HEADING
		]);

		// See all reviews button tabs
		$this->start_controls_tabs( 'ekit_yelp_review_ss_oo_sar_buttons_tabs' );

		// See all reviews tab normal
        $this->start_controls_tab(
            'ekit_yelp_review_ss_oo_sar_button_tab_normal', [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
		);

		// See all reviews button color
		$this->add_control( 'ekit_yelp_review_ss_oo_sar_button_color_normal',
			[
				'label'     => __('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card--actions .btn:first-child' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		// See all reviews tab hover
        $this->start_controls_tab(
            'ekit_yelp_review_ss_oo_sar_button_tab_hover', [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
		);

		// See all reviews button color hover
		$this->add_control( 'ekit_yelp_review_ss_oo_sar_button_color_hover',
			[
				'label'     => __('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card--actions .btn:first-child:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		// Write a review
		$this->add_control( 'ekit_yelp_review_ss_oo_rar_button', [
			'label'     => esc_html__('Write a review', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before'
		]);

		// Start write a review button tabs
		$this->start_controls_tabs( 'ekit_yelp_review_ss_oo_rar_buttons_tabs' );

		// Write a review tab normal
        $this->start_controls_tab(
            'ekit_yelp_review_ss_oo_rar_button_tab_normal', [
                'label' => esc_html__( 'Normal', 'elementskit' ),
            ]
		);

		// Write a review button color
		$this->add_control( 'ekit_yelp_review_ss_oo_rar_button_color_normal',
			[
				'label'     => __('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card--actions .btn:last-child' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		// Write a review tab hover
        $this->start_controls_tab(
            'ekit_yelp_review_ss_oo_rar_button_tab_hover', [
                'label' => esc_html__( 'Hover', 'elementskit' ),
            ]
		);

		// Write a review button color hover
		$this->add_control( 'ekit_yelp_review_ss_oo_rar_button_color_hover',
			[
				'label'     => __('Text Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card--actions .btn:last-child:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
	}

	private function controls_section_overview_card(){

		// ekit_yelp_review_overview_card_background
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'      => 'ekit_yelp_review_overview_card_background',
				'label'     => esc_html__('Card Background', 'elementskit'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .ekit-review-card-yelp.ekit-review-card-overview'
			]
		);

        // Box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name' => 'ekit_yelp_review_overview_card_shadow',
				'label' => __( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-review-card-yelp.ekit-review-card-overview'
			]
		);

		// ekit_yelp_review_overview_card_padding
		$this->add_responsive_control( 'ekit_yelp_review_overview_card_padding', [
			'label'      => esc_html__('Padding', 'elementskit'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [ 
				'{{WRAPPER}} .ekit-review-card-yelp.ekit-review-card-overview' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--top-right-logo' => "{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}}"
			],
			'default'	        => $this->get_dimension(2),
			'tablet_default'	=> $this->get_dimension(1),
			'mobile_default'	=> $this->get_dimension(8, 'px')
		]);

		// ekit_yelp_review_overview_card_margin
		$this->add_responsive_control(
			'ekit_yelp_review_overview_card_margin', [
				'label'          => esc_html__('Margin', 'elementskit'),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => ['px', '%', 'em'],
				'default'        => $this->get_dimension(0),
				'tablet_default' => $this->get_dimension(0),
				'mobile_default' => $this->get_dimension(0),
				'selectors'      => [ 
					'{{WRAPPER}} .ekit-review-card-yelp.ekit-review-card-overview' => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				],
			]
		);

		// ekit_yelp_review_overview_card_border
		$this->control_border( 
            'ekit_yelp_review_overview_card_border', [ '.ekit-review-card-yelp.ekit-review-card-overview' ],
            [ 'default' => '0', 'unit' => 'px' ]
        );
    }

    private function controls_section_header_card(){

		// ekit_yelp_review_header_card_background
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'      => 'ekit_yelp_review_header_card_background',
				'label'     => esc_html__('Card Background', 'elementskit'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .ekit-review-overview.ekit-review-overview-yelp'
			]
		);

        // ekit_yelp_review_header_card_box_shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name' => 'ekit_yelp_review_header_card_shadow',
				'label' => __( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-review-overview.ekit-review-overview-yelp'
			]
		);

		// ekit_yelp_review_header_card_padding
		$this->add_responsive_control( 'ekit_yelp_review_header_card_padding', [
			'label'      => esc_html__('Padding', 'elementskit'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [ 
				'{{WRAPPER}} .ekit-review-overview.ekit-review-overview-yelp' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--top-right-logo' => "top:{{TOP}}{{UNIT}};right:{{RIGHT}}{{UNIT}};"
			],
			'default'	=> [
				'top'      => '24', 'right'    => '24',
				'bottom'   => '24', 'left'     => '24',
				'unit'     => 'px', 'isLinked' => true,
			],
			'tablet_default'	=> [
				'top'      => '1', 'right'    => '1',
				'bottom'   => '1', 'left'     => '1',
				'unit'     => 'em', 'isLinked' => true,
			],
			'mobile_default'	=> [
				'top'      => '8', 'right'    => '8',
				'bottom'   => '8', 'left'     => '8',
				'unit'     => 'px', 'isLinked' => true,
			]
		]);

		// ekit_yelp_review_header_card_margin
		$this->add_responsive_control(
			'ekit_yelp_review_header_card_margin', [
				'label'          => esc_html__('Margin', 'elementskit'),
				'type'           => Controls_Manager::DIMENSIONS,
				'size_units'     => ['px', '%', 'em'],
				'default'        => [
					'top'      => '0', 'right'    => '0',
					'bottom'   => '24', 'left'     => '0',
					'unit'     => 'px', 'isLinked' => false,
				],
				'tablet_default' => [
					'top'      => '0', 'right'    => '0',
					'bottom'   => '1', 'left'     => '0',
					'unit'     => 'em', 'isLinked' => false,
				],
				'mobile_default' => [
					'top'      => '0', 'right'    => '0',
					'bottom'   => '8', 'left'     => '0',
					'unit'     => 'px', 'isLinked' => false,
				],
				'selectors'      => [ '{{WRAPPER}} .ekit-review-overview.ekit-review-overview-yelp' => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		// ekit_yelp_review_header_card_border
		$this->control_border( 
            'ekit_yelp_review_header_card_border', [ '.ekit-review-overview.ekit-review-overview-yelp' ], 
            [ 'default' => '0', 'unit' => 'px' ] 
        );

        // Thumbnail heading
		$this->add_control( 'ekit_yelp_review_header_card_thumbnail_heading', [
			'label'     => esc_html__('Thumbnail', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
        ]);

        // ekit_yelp_review_header_card_thumbnail_size
		$this->add_responsive_control(
			'ekit_yelp_review_header_card_thumbnail_size', [
				'label' => __( 'Thumbnail Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [ 'min' => 0, 'max' => 64, 'step' => 1 ],
					'em' => [ 'min' => 0, 'max' => 4, 'step' => 0.1 ],
				],
				'default' => [ 'unit' => 'px', 'size' => 40 ],
				'tablet_default' => [ 'unit' => 'px', 'size' => 40 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 40 ],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-overview.ekit-review-overview-yelp .ekit-review-overview--thumbnail thumbnail' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
			]
        );

        // ekit_yelp_review_header_card_thumbnail_margin_right
		$this->add_responsive_control(
			'ekit_yelp_review_header_card_thumbnail_margin_right', [
				'label' => __( 'Margin Right', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [ 'min' => 0, 'max' => 64, 'step' => 1 ],
					'em' => [ 'min' => 0, 'max' => 2, 'step' => 0.1 ],
				],
				'default' => [ 'unit' => 'px', 'size' => 16 ],
				'tablet_default' => [ 'unit' => 'px', 'size' => 16 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-overview.ekit-review-overview-yelp .ekit-review-overview--thumbnail' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
        );

        // Page name heading
		$this->add_control( 'ekit_yelp_review_header_card_pagename_heading', [
			'label'     => esc_html__('Page Name', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
        ]);

        $this->control_text(
			'ekit_yelp_review_header_card_page_name', 
			'.ekit-review-overview.ekit-review-overview-yelp .ekit-review-overview--title h4 > span', 
			['margin', 'shadow']
		);
        
        // Avg rating heading
		$this->add_control( 'ekit_yelp_review_header_card_rating_avg_heading', [
			'label'     => esc_html__('Rating Average', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
        ]);

        $this->control_text(
			'ekit_yelp_review_header_card_avg_rating', 
			'.ekit-review-overview.ekit-review-overview-yelp .rating-average', 
			['margin', 'shadow']
		);
        
        // Stars heading
		$this->add_control( 'ekit_yelp_review_header_card_stars_heading', [
			'label'     => esc_html__('Stars', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
        ]);

        $this->control_text(
			'ekit_yelp_review_header_card_stars', 
			'.ekit-review-overview.ekit-review-overview-yelp .ekit-review-overview--stars', 
			['margin', 'shadow', 'typography']
		);
        
        // ekit_yelp_review_header_card_stars_margin
		$this->add_responsive_control(
			'ekit_yelp_review_header_card_stars_margin', [
				'label' => __( 'Margin Left Right', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [ 'min' => 0, 'max' => 32, 'step' => 1 ],
					'em' => [ 'min' => 0, 'max' => 2, 'step' => 0.1 ],
				],
				'default' => [ 'unit' => 'px', 'size' => 10 ],
				'tablet_default' => [ 'unit' => 'px', 'size' => 10 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 10 ],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-overview.ekit-review-overview-yelp .ekit-review-overview--stars' => 'margin-right: {{SIZE}}{{UNIT}};margin-left: {{SIZE}}{{UNIT}};',
				],
			]
        );

        // Desc heading
		$this->add_control( 'ekit_yelp_review_header_card_desc_heading', [
			'label'     => esc_html__('Reviews Count', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
        ]);

        $this->control_text(
			'ekit_yelp_review_header_card_desc',
			'.ekit-review-overview.ekit-review-overview-yelp .rating-text', 
			['margin', 'shadow']
		);
    }

    private function controls_section_review_card(){

		// ekit_yelp_review_review_card_background
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'      => 'ekit_yelp_review_review_card_background',
				'label'     => esc_html__('Card Background', 'elementskit'),
				'types'     => ['classic', 'gradient'],
				'selector'  => '{{WRAPPER}} .ekit-review-card.ekit-review-card-yelp',
				'condition' => [
					'ekit_review_card_type' => 'default'
				]
			]
		);

		// ekit_yelp_review_review_card_background_bubble
		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'      => 'ekit_yelp_review_review_card_background_bubble',
				'label'     => esc_html__('Card Background', 'elementskit'),
				'types'     => ['classic', 'gradient'],
				'selector'  => 
					'{{WRAPPER}} .ekit-review-card-yelp.ekit-review-card-bubble:before,
					{{WRAPPER}} .ekit-review-card-yelp.ekit-review-card-bubble:after',
				'condition' => [
					'ekit_review_card_type' => 'bubble'
				]
			]
		);

        // Box shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name' => 'ekit_yelp_review_review_card_shadow',
				'label' => __( 'Box Shadow', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-review-card.ekit-review-card-yelp'
			]
		);

		// ekit_yelp_review_review_card_padding
		$this->add_responsive_control( 'ekit_yelp_review_review_card_padding', [
			'label'      => esc_html__('Padding', 'elementskit'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [ 
				'{{WRAPPER}} .ekit-review-card.ekit-review-card-yelp' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .ekit-review-card.ekit-review-card-yelp .ekit-review-card--top-right-logo' => "top:{{TOP}}{{UNIT}};right:{{RIGHT}}{{UNIT}};"
			],
			'default'           => $this->get_dimension( 2, 'em' ),
			'tablet_default'	=> $this->get_dimension( 1, 'em' ),
            'mobile_default'	=> $this->get_dimension( 8, 'px' ),
            'condition'         => [ 'ekit_review_card_type' => 'default' ]
        ]);
        //.ekit-wid-con .ekit-review-card-bubble:after

        // ekit_yelp_review_review_card_padding_bubble
		$this->add_responsive_control( 'ekit_yelp_review_review_card_padding_bubble', [
			'label'      => esc_html__('Padding', 'elementskit'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px'],
			'selectors'  => [ 
				'{{WRAPPER}} .ekit-review-card.ekit-review-card-yelp' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .ekit-review-card.ekit-review-card-yelp .ekit-review-card--top-right-logo' => "top:{{TOP}}{{UNIT}}; right: {{RIGHT}}{{UNIT}};",
                '{{WRAPPER}} .ekit-review-card-yelp.ekit-review-card-bubble:after' => "left:calc({{LEFT}}{{UNIT}} + 10px);",
				//'{{WRAPPER}} ' . $comment => "margin-bottom: calc($b - 1rem)",
			],
			'default'           => $this->get_dimension( 24, 'px' ),
			'tablet_default'	=> $this->get_dimension( 16, 'px' ),
            'mobile_default'	=> $this->get_dimension( 8, 'px' ),
            'condition'         => [ 'ekit_review_card_type' => 'bubble' ]
        ]);

		// ekit_yelp_review_review_card_margin
		$this->add_responsive_control( 'ekit_yelp_review_review_card_margin', [
			'label'      => esc_html__('Margin', 'elementskit'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [ '{{WRAPPER}} .ekit-review-card.ekit-review-card-yelp' => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			'default'           => $this->get_dimension( [0, 0, 24, 0], 'px', false ),
			'tablet_default'	=> $this->get_dimension( [0, 0, 16, 0], 'px', false ),
			'mobile_default'	=> $this->get_dimension( [0, 0, 8, 0], 'px', false ),
            'condition' => [ 'ekit_review_card_appearance' => 'list' ]
		]);

		// ekit_yelp_review_review_card_border
        $this->control_border(
			'ekit_yelp_review_review_card_border', 
			[ '.ekit-review-card.ekit-review-card-yelp' ],
			[ 'default' => '0', 'unit' => 'px' ], 
			[ 'ekit_review_card_type' => 'default' ],
		);

		$this->control_border(
			'ekit_yelp_review_bubble_card_border', 
			[ '.ekit-review-card.ekit-review-card-bubble::before' ],
			[ 'default' => '0', 'unit' => 'px' ] ,
			[ 'ekit_review_card_type' => 'bubble' ],
		);

    }

    private function controls_section_oc_page_pro_pic(){

        // Container heading
		$this->add_control( 'ekit_yelp_review_ss_oc_page_pro_pic_con_heading', [
			'label'     => esc_html__('Container', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
        ]);

        // ekit_yelp_review_ss_oc_page_pro_pic_con_size
        $this->add_responsive_control( 'ekit_yelp_review_ss_oc_page_pro_pic_con_size', [
            'label' => __( 'Container Size', 'elementskit' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', 'em' ],
            'range' => [
                'px' => [ 'min' => 40, 'max' => 128, 'step' => 4 ],
                'em' => [ 'min' => 2, 'max' => 8, 'step' => 0.2 ],
            ],
            'default' => [ 'unit' => 'px', 'size' => 60 ],
            'tablet_default' => [ 'unit' => 'px', 'size' => 60 ],
            'mobile_default' => [ 'unit' => 'px', 'size' => 60 ],
            'selectors' => [
                '{{WRAPPER}} .ekit-review-card-yelp.ekit-review-card-overview .ekit-review-card--image' => "height:{{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};min-height:{{SIZE}}{{UNIT}};min-width:{{SIZE}}{{UNIT}};",
            ],
        ]);

        // ekit_yelp_review_ss_oc_page_pro_pic_con_padding
		$this->add_responsive_control( 'ekit_yelp_review_ss_oc_page_pro_pic_con_padding', [
			'label'      => esc_html__('Padding', 'elementskit'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [ '{{WRAPPER}} .ekit-review-card-yelp.ekit-review-card-overview .ekit-review-card--image' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			'default'           => $this->get_dimension( 16, 'px' ),
			'tablet_default'	=> $this->get_dimension( 12, 'px' ),
            'mobile_default'	=> $this->get_dimension( 8, 'px' )
        ]);

        // ekit_yelp_review_ss_oc_page_pro_pic_con_margin_right
        $this->add_responsive_control(
			'ekit_yelp_review_ss_oc_page_pro_pic_con_margin_right', [
				'label' => __( 'Margin Right', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [ 'min' => 0, 'max' => 32, 'step' => 1 ],
					'em' => [ 'min' => 0, 'max' => 2, 'step' => 0.1 ],
				],
				'default' => [ 'unit' => 'px', 'size' => 16 ],
				'tablet_default' => [ 'unit' => 'px', 'size' => 16 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card-yelp.ekit-review-card-overview .ekit-review-card--image' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
        );

        // ekit_yelp_review_ss_oc_page_pro_pic_con_border_radius
		$this->add_control( 'ekit_yelp_review_ss_oc_page_pro_pic_con_border_radius', [
			'label'			=> esc_html__('Border Radius', 'elementskit'),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> ['px', '%', 'em'],
			'selectors'		=> [ 
                '{{WRAPPER}} .ekit-review-card-yelp.ekit-review-card-overview .ekit-review-card--image' => 
                    'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
			'default'    => [
				'top'      => '50', 'right'	=> '50',
				'bottom'   => '50', 'left'	=> '50',
				'unit'     => '%', 'isLinked' => true,
            ]
        ]);

        // Image heading
		$this->add_control( 'ekit_yelp_review_ss_oc_page_pro_pic_image_heading', [
			'label'     => esc_html__('Profile Picture', 'elementskit'),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before'
        ]);

        // ekit_yelp_review_ss_oc_page_pro_pic_image_border_radius
		$this->add_control( 'ekit_yelp_review_ss_oc_page_pro_pic_image_border_radius', [
			'label'			=> esc_html__('Border Radius', 'elementskit'),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> ['px', '%', 'em'],
			'selectors'		=> [ 
                '{{WRAPPER}} .ekit-review-card-yelp.ekit-review-card-overview .ekit-review-card--image .thumbnail' => 
                    'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
			'default'    => [
				'top'      => '50', 'right'	=> '50',
				'bottom'   => '50', 'left'	=> '50',
				'unit'     => '%', 'isLinked' => true,
			]
		]);
    }

    private function controls_section_reviewer_thumbnail(){

        // ekit_yelp_review_reviewer_thumbnail_size
        $this->add_responsive_control(
			'ekit_yelp_review_reviewer_thumbnail_size', [
				'label' => __( 'Thumbnail Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [ 'min' => 0, 'max' => 96, 'step' => 4 ],
					'em' => [ 'min' => 0, 'max' => 6, 'step' => 0.2 ],
				],
				'default' => [ 'unit' => 'px', 'size' => 40 ],
				'tablet_default' => [ 'unit' => 'px', 'size' => 40 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 40 ],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card.ekit-review-card-yelp .ekit-review-card--thumbnail .thumbnail' => "height:{{SIZE}}{{UNIT}};width:{{SIZE}}{{UNIT}};min-height:{{SIZE}}{{UNIT}};min-width:{{SIZE}}{{UNIT}};",
				],
			]
        );

        // ekit_yelp_review_reviewer_thumbnail_margin_right
        $this->add_responsive_control(
			'ekit_yelp_review_reviewer_thumbnail_margin_right', [
				'label' => __( 'Margin Right', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [ 'min' => 0, 'max' => 32, 'step' => 1 ],
					'em' => [ 'min' => 0, 'max' => 2, 'step' => 0.1 ],
				],
				'default' => [ 'unit' => 'px', 'size' => 16 ],
				'tablet_default' => [ 'unit' => 'px', 'size' => 16 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 16 ],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card.ekit-review-card-yelp .ekit-review-card--thumbnail' => 'padding-right: {{SIZE}}{{UNIT}};',
				],
			]
        );

        // ekit_yelp_review_reviewer_thumbnail_border_radius
		$this->add_control( 'ekit_yelp_review_reviewer_thumbnail_border_radius', [
			'label'			=> esc_html__('Border Radius', 'elementskit'),
			'type'			=> Controls_Manager::DIMENSIONS,
			'size_units'	=> ['px', '%', 'em'],
			'selectors'		=> [ 
                '{{WRAPPER}} .ekit-review-card.ekit-review-card-yelp .ekit-review-card--thumbnail .thumbnail' => 
                    'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
            ],
			'default'    => [
				'top'      => '50', 'right'	=> '50',
				'bottom'   => '50', 'left'	=> '50',
				'unit'     => '%', 'isLinked' => true,
			]
		]);

		 // Thumbnail Badge
		 $this->add_control( 'ekit_yelp_review_thumbnail_bag_heading', [
			'label'     => esc_html__('Thumbnail Badge', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [
				'ekit_review_card_thumbnail_badge' => 'yes'
			]
        ]);

		$this->add_control(
			'ekit_yelp_review_header_card_thumbnail_badge_color',
			[
				'label' => esc_html__( 'Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--thumbnail-badge .badge i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'ekit_review_card_thumbnail_badge' => 'yes'
				]
			]
		);

		$this->add_control(
			'ekit_yelp_review_header_card_thumbnail_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#AF0606',
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--thumbnail-badge .badge' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'ekit_review_card_thumbnail_badge' => 'yes'
				]
			]
		);
    }

    private function controls_section_reviewer_name(){
		$this->control_text(
			'controls_section_reviewer_name', 
			'.ekit-review-card.ekit-review-card-yelp .ekit-review-card--name', [], 
			[ "def_margin" => [ 'bottom' => '8', 'unit' => 'px', 'isLinked' => false ]
        ]);
    }

    private function controls_section_reviewer_card_date(){
		$this->control_text(
			'controls_section_reviewer_card_date', 
			'.ekit-review-card.ekit-review-card-yelp .ekit-review-card--date', 
			[ 'shadow', 'margin' ]
		);
    }

    private function controls_section_reviewer_card_stars(){

		$this->add_responsive_control(
			'reviewer_card_stars_gap', [
				'label' => __( 'Icon Gap', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [ 'min' => 0, 'max' => 24, 'step' => 1 ],
				],
				'default' => [ 'unit' => 'px', 'size' => 4 ],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--stars > span' => 
						'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->control_text(
			'controls_section_reviewer_card_stars', 
			'.ekit-review-card.ekit-review-card-yelp .ekit-review-card--stars i', 
			[ 'shadow', 'margin', 'typography' ]
		);
    }

    private function controls_section_reviewer_card_review(){

		$this->control_text(
			'controls_section_reviewer_card_review', 
			'.ekit-review-card.ekit-review-card-yelp .ekit-review-card--comment', [], 
			[
				"def_margin" => [ 'bottom' => '1', 'unit' => 'em', 'isLinked' => false 
			]
        ]);

        // controls_section_reviewer_card_review_padding
		$this->add_responsive_control( 'controls_section_reviewer_card_review_padding', [
			'label'      => esc_html__('Padding', 'elementskit'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [ '{{WRAPPER}} .ekit-review-card.ekit-review-card-yelp .ekit-review-card--comment' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			'default'           => [ 'top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '0', 'unit' => 'em', 'isLinked' => false, ],
			'tablet_default'	=> [ 'top' => '12', 'right' => '12', 'bottom' => '12', 'left' => '0', 'unit' => 'px', 'isLinked' => false, ],
			'mobile_default'	=> [ 'top' => '8', 'right' => '8', 'bottom' => '8', 'left' => '0', 'unit' => 'px', 'isLinked' => false, ]
		]);

		 // Thumbnail Badge
		$this->add_control( 'controls_section_reviewer_card_more_heading', [
			'label'     => esc_html__('More Option', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
        ]);

		$this->add_control(
			'controls_section_reviewer_card_more_color',
			[
				'label' => esc_html__( 'Text Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#AF0606',
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--comment .more' => 'color: {{VALUE}}',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'controls_section_reviewer_card_more_typography',
				'selector' => '{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--comment .more',
			]
		);
    }

	private function controls_section_overview_page_name(){
		$this->control_text(
			'controls_section_overview_page_name', 
			'.ekit-review-card-yelp.ekit-review-card-overview .ekit-review-card--name'
		);
	}

	private function controls_section_overview_desc(){
		$this->control_text(
			'controls_section_overview_desc', 
			'.ekit-review-card-yelp.ekit-review-card-overview .ekit-review-card--desc', 
			['margin']
		);
	}

	private function controls_section_overview_stars(){

		// Overview stars
		$this->add_control(
			'controls_section_overview_average_rating_heading', [
				'label'     => esc_html__('Average Rating', 'elementskit'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		// Overview average rating text
		$this->control_text(
			'controls_section_overview_average_rating', 
			'.ekit-review-card-yelp.ekit-review-card-overview .ekit-review-card--average', 
			['margin','shadow']
		);

		// Overview stars
		$this->add_control(
			'controls_section_overview_stars_heading', [
				'label'     => esc_html__('Stars', 'elementskit'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		// Overview average rating text
		$this->control_text(
			'ekit_yelp_review_overview_rating_stars', 
			'.ekit-review-card-yelp.ekit-review-card-overview .ekit-review-card--stars i', 
			['margin','shadow','typography']
		);
	}

	private function controls_section_top_right_logo(){

        // Top right brand icon
        $this->add_control(
            'controls_section_top_right_logo_icons', [
                'label' => esc_html__( 'Header Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'controls_section_top_right_logo_icon',
                'default' => [
                    'value' => 'fab fa-yelp',
                    'library' => 'fa-brands',
                ],
                'label_block' => true
            ]
        );

        // Top right brand icon size
		$this->add_responsive_control(
			'controls_section_top_right_logo_size', [
				'label' => __( 'Logo Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [ 'min' => 0, 'max' => 96, 'step' => 4 ],
					'em' => [ 'min' => 0, 'max' => 6, 'step' => 0.2 ],
				],
				'default' => [ 'unit' => 'px', 'size' => 20 ],
				'tablet_default' => [ 'unit' => 'px', 'size' => 20 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 20 ],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--top-right-logo i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
        );

        // Top right brand icon color
        $this->add_control(
			'controls_section_top_right_logo_color', [
				'label'     => __('Logo Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--top-right-logo i' => 'color: {{VALUE}}',
				],
			]
		);
    }

	private function controls_section_bottom_posted_on(){

        // Icon heading
		$this->add_control( 'ekit_yelp_review_card_bottom_posted_on_icon_heading', [
			'label'     => esc_html__('Icon', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
        ]);

        // ekit_yelp_review_posted_on_icons
        $this->add_control(
            'ekit_yelp_review_posted_on_icons', [
                'label' => esc_html__( 'Posted On Icon', 'elementskit' ),
                'type' => Controls_Manager::ICONS,
                'fa4compatibility' => 'ekit_yelp_review_posted_on_icon',
                'default' => [
                    'value' => 'fab fa-yelp',
                    'library' => 'fa-brands',
                ],
                'label_block' => true
            ]
        );

        // ekit_yelp_review_posted_on_icon_size
		$this->add_responsive_control(
			'ekit_yelp_review_posted_on_icon_size', [
				'label' => __( 'Icon Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [ 'min' => 0, 'max' => 96, 'step' => 4 ],
					'em' => [ 'min' => 0, 'max' => 6, 'step' => 0.2 ],
				],
				'default' => [ 'unit' => 'px', 'size' => 32 ],
				'tablet_default' => [ 'unit' => 'px', 'size' => 32 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 32 ],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--posted-on i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--posted-on svg' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
        );

        // ekit_yelp_review_posted_on_icon_color
        $this->add_control(
			'ekit_yelp_review_posted_on_icon_color', [
				'label'     => __('Icon Color', 'elementskit'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--posted-on i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--posted-on svg' => 'fill: {{VALUE}};',
					'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--posted-on svg path' => 'fill: {{VALUE}};',
				],
			]
        );
        
        // ekit_yelp_review_posted_on_icon_margin_right
		$this->add_responsive_control(
			'ekit_yelp_review_posted_on_icon_margin_right', [
				'label' => __( 'Margin Right', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [ 'min' => 0, 'max' => 32, 'step' => 1 ],
					'em' => [ 'min' => 0, 'max' => 2, 'step' => 0.1 ],
				],
				'default' => [ 'unit' => 'px', 'size' => 12 ],
				'tablet_default' => [ 'unit' => 'px', 'size' => 12 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 12 ],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--posted-on i' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ekit-review-card-yelp .ekit-review-card--posted-on svg' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
        );

        // Icon heading
		$this->add_control( 'ekit_yelp_review_card_bottom_posted_on_heading', [
			'label'     => esc_html__('Posted On', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
        ]);

        $this->control_text(
			'ekit_yelp_review_card_bottom_posted_on',
			'.ekit-review-card-yelp .ekit-review-card--posted-on p', 
			['margin', 'shadow']
		);

        // Yelp heading
		$this->add_control( 'ekit_yelp_review_card_bottom_posted_on_fb_heading', [
			'label'     => esc_html__('Yelp', 'elementskit'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
        ]);

		$this->control_text(
			'ekit_yelp_review_card_bottom_posted_on_yelp',
			'.ekit-review-card-yelp .ekit-review-card--posted-on h5', 
			['margin', 'shadow']
		);
	}

	private function controls_section_arrow(){
		$this->start_controls_section(
			'ekit_yelp_review_arrow_section',
			[
				'label' => esc_html__( 'Arrows', 'elementskit' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_review_slideshow_show_arrow' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'ekit_yelp_review_arrow_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper .swiper-navigation-button' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_padding',
			[
				'label' => esc_html__( 'Padding', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper .swiper-navigation-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'arrow_left_pos',
			[
				'label' => esc_html__( 'Left Arrow Position', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -10,
						'max' => 100,
					],
				],

				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper .swiper-button-prev' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_right_pos',
			[
				'label' => esc_html__( 'Right Arrow Position', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => -10,
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper .swiper-button-next' => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		// Arrow Normal
		$this->start_controls_tabs('ekit_arrow_style_tabs');

		$this->start_controls_tab(
			'ekit_arrow_arrow_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'elementskit' ),
			]
		);

		$this->add_control(
			'ekit_yelp_review_arrow_color',
			[
				'label' => esc_html__( 'Arrow Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FFFFFF',
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper .swiper-navigation-button i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ekit_yelp_review_arrow_bg_color',
			[
				'label' => esc_html__( 'Arrow Background', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#AF0606',
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper .swiper-navigation-button' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'arrow_border_group',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-review-slider-wrapper .swiper-navigation-button',
			]
		);

		$this->add_responsive_control(
			'arrow_border_radious',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper .swiper-navigation-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name'      => 'arrow_shadow',
				'selector'  => '{{WRAPPER}} .ekit-review-slider-wrapper .swiper-navigation-button',
			]
		);

		$this->end_controls_tab();

		//  Arrow hover tab
		$this->start_controls_tab(
			'arrow_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'elementskit' ),
			]
		);

		$this->add_control(
			'arrow_hv_color',
			[
				'label' => esc_html__( 'Arrow Color', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper .swiper-navigation-button:hover i' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'arrow_hover_background',
			[
				'label' => esc_html__( 'Arrow Background', 'elementskit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper .swiper-navigation-button:hover' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'arrow_hvr_border_group',
				'label' => esc_html__( 'Border', 'elementskit' ),
				'selector' => '{{WRAPPER}} .ekit-review-slider-wrapper .swiper-navigation-button:hover',
			]
		);

		$this->add_responsive_control(
			'arrow_hvr_border_radious',
			[
				'label' => esc_html__( 'Border Radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper .swiper-navigation-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name'      => 'arrow_hvr_shadow',
				'selector'  => '{{WRAPPER}} .ekit-review-slider-wrapper .swiper-navigation-button:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function controls_section_dots(){

		$this->add_responsive_control(
			'dots_top_spacing',
			[
				'label' => esc_html__( 'Dots Top Spacing', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => -30,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper-yelp .swiper-pagination' => 
						'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dot_width',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper-yelp .swiper-pagination span' => 'width: {{SIZE}}{{UNIT}};'
				],
			]
		);

		$this->add_responsive_control(
			'dot_height',
			[
				'label' => esc_html__( 'Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper-yelp .swiper-pagination span' =>  'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dot_border_radius',
			[
				'label' => esc_html__( 'Border radius', 'elementskit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper-yelp .swiper-pagination span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dots_spacing',
			[
				'label' => esc_html__( 'Space between dots', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 12,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper-yelp .swiper-pagination span:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'dots_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-review-slider-wrapper-yelp .swiper-pagination span',
			]
		);

		$this->add_control(
			'dot_active_heading',
			[
				'label' => esc_html__( 'Active', 'elementskit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'dot_active_background',
				'label' => esc_html__( 'Background', 'elementskit' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .ekit-review-slider-wrapper-yelp .swiper-pagination span.swiper-pagination-bullet-active',
			]
		);

		$this->add_responsive_control(
			'dot_active_width',
			[
				'label' => esc_html__( 'Width', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper-yelp .swiper-pagination span.swiper-pagination-bullet-active' => 
						'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dot_active_height',
			[
				'label' => esc_html__( 'Height', 'elementskit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 8,
				],
				'selectors' => [
					'{{WRAPPER}} .ekit-review-slider-wrapper-yelp .swiper-pagination span.swiper-pagination-bullet-active' => 
						'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
	}

	protected function register_controls() {

		// Layout section
		$this->controls_section(
			[ 
				'label' => esc_html__('Layout', 'elementskit'),
				'key' => 'ekit_yelp_review_cs_layout' 
			],
			esc_html__('controls_section_layout', 'elementskit')
		);

        // Contents section
		$this->controls_section(
			[
				'label' => esc_html__('Contents', 'elementskit'),
				'key' => 'ekit_yelp_review_cs_contents'
			],
			esc_html__('controls_section_contents', 'elementskit')
		);

        // Slideshow section
		$this->controls_section(
			[
				'label' => esc_html__('Slideshow', 'elementskit'),
				'key' => 'ekit_yelp_review_cs_slideshow',
				'condition' => [
					'ekit_review_styles' => 'slideshow'
				]
			],
			esc_html__('controls_section_slideshow', 'elementskit')
		);

        // Widget section
		$this->controls_section(
			[
				'label' => esc_html__('Widget', 'elementskit'),
				'key' => 'ekit_yelp_review_ss_widget',
				'tab' => Controls_Manager::TAB_STYLE 
			], 
			esc_html__('controls_section_widget', 'elementskit')
		);

        // Overview card section
		$this->controls_section(
			[
				'label' => esc_html__('Card', 'elementskit'),
				'key' => 'ekit_yelp_review_ss_overview_card',
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'ekit_review_styles' => 'default'
				]
			], 
			esc_html__('controls_section_overview_card', 'elementskit')
		);

        // Reviewer Name
        $this->controls_section(
			[ 
				'label' => esc_html__('Page Profile Picture', 'elementskit'), 
				'key' => 'ekit_yelp_review_ss_oc_page_pro_pic', 
				'tab' => Controls_Manager::TAB_STYLE, 
				'condition' => [ 
					'ekit_review_styles' => 'default' 
				]
			], 
			esc_html__('controls_section_oc_page_pro_pic', 'elementskit')
		);

		// Overview page name section
		$this->controls_section(
			[
				'label' => esc_html__('Page Name', 'elementskit'),       
				'key' => 'ekit_yelp_review_ss_overview_page_name', 
				'tab' => Controls_Manager::TAB_STYLE, 
				'condition' => [ 
					'ekit_review_styles' => 'default' 
				]
			], 
			esc_html__('controls_section_overview_page_name', 'elementskit')
		);

		// Overview stars section
		$this->controls_section([ 
			'label' => esc_html__('Rating and Stars', 'elementskit'), 
			'key' => 'ekit_yelp_review_ss_overview_stars',   
			'tab' => Controls_Manager::TAB_STYLE, 
			'condition' => [ 
					'ekit_review_styles' => 'default'
				]
			], 
			esc_html__('controls_section_overview_stars', 'elementskit')
		);

		// Overview description section
		$this->controls_section(
			[ 
				'label' => esc_html__('Description', 'elementskit'),     
				'key' => 'ekit_yelp_review_ss_overview_desc',     
				'tab' => Controls_Manager::TAB_STYLE, 
				'condition' => [ 
					'ekit_review_styles' => 'default' 
				]
			], 
			esc_html__('controls_section_overview_desc', 'elementskit')
		);

		// Overview Buttons section
		$this->controls_section(
			[ 
				'label' => esc_html__('Buttons', 'elementskit'),         
				'key' => 'ekit_yelp_review_ss_oo_buttons',        
				'tab' => Controls_Manager::TAB_STYLE, 
				'condition' => [ 
					'ekit_review_styles' => 'default' 
				]
			], 
			esc_html__('controls_section_overview_buttons', 'elementskit')
		);
        
        // Header card section
		$this->controls_section(
			[ 
				'label' => esc_html__('Header Card', 'elementskit'),     
				'key' => 'ekit_yelp_review_ss_header_card',       
				'tab' => Controls_Manager::TAB_STYLE, 
				'condition' => [ 
					'ekit_review_styles!' => 'default', 
					'ekit_review_overview_card' => 'yes' 
				]
			], 
			esc_html__('controls_section_header_card', 'elementskit')
		);
        
        // Header button section
		$this->controls_section(
			[
				'label' => esc_html__('Header Button', 'elementskit'),   
				'key' => 'ekit_yelp_review_ss_header_button',     
				'tab' => Controls_Manager::TAB_STYLE, 
				'condition' => [
					'ekit_review_styles!' => 'default',
					'ekit_review_overview_card' => 'yes'
				]
			],
			esc_html__('controls_section_header_button', 'elementskit')
		);

        // Review card section
		$this->controls_section(
			[ 
				'label' 	=> esc_html__('Review Card', 'elementskit'),   
				'key' => 'ekit_yelp_review_ss_review_card',       
				'tab' => Controls_Manager::TAB_STYLE, 
				'condition' => [ 
					'ekit_review_styles!' => 'default' 
				]
			],
			esc_html__('controls_section_review_card', 'elementskit')
		);

        // Reviewer Name
        $this->controls_section(
			[ 
				'label' => esc_html__('Reviewer Thumbnail', 'elementskit'), 
				'key' => 'ekit_yelp_review_ss_reviewer_thumbnail', 
				'tab' => Controls_Manager::TAB_STYLE, 
				'condition' => [ 
					'ekit_review_styles!' => 'default' 
				]
			], 
			esc_html__('controls_section_reviewer_thumbnail', 'elementskit')
		);
        
        // Reviewer Name
		$this->controls_section(
			[ 
				'label' => esc_html__('Reviewer Name', 'elementskit'),   
				'key' => 'ekit_yelp_review_ss_reviewer_name',     
				'tab' => Controls_Manager::TAB_STYLE, 
				'condition' => [ 
					'ekit_review_styles!' => 'default' 
				]
			], 
			esc_html__('controls_section_reviewer_name', 'elementskit')
		);

        // Review card date
		$this->controls_section(
			[ 
				'label' => esc_html__('Review Date', 'elementskit'),     
				'key' => 'ekit_yelp_review_ss_review_card_date',  
				'tab' => Controls_Manager::TAB_STYLE, 
				'condition' => [ 
					'ekit_review_styles!' => 'default' 
				]
			], 
			esc_html__('controls_section_reviewer_card_date', 'elementskit')
		);

        // Review card stars
		$this->controls_section(
			[ 
				'label' => esc_html__('Review Stars', 'elementskit'),    
				'key' => 'ekit_yelp_review_ss_review_card_stars', 
				'tab' => Controls_Manager::TAB_STYLE, 
				'condition' => [ 
					'ekit_review_styles!' => 'default' 
				]
			], 
			esc_html__('controls_section_reviewer_card_stars', 'elementskit')
		);

        // Review card review
		$this->controls_section(
			[ 
				'label' => esc_html__('Review Feedback', 'elementskit'), 
				'key' => 'ekit_yelp_review_ss_review_card_review', 
				'tab' => Controls_Manager::TAB_STYLE, 
				'condition' => [ 
					'ekit_review_styles!' => 'default' 
				]
			], 
			esc_html__('controls_section_reviewer_card_review', 'elementskit')
		);

		// Top right brand logo
        $this->controls_section(
			[ 
				'label' => esc_html__('Top Right Logo', 'elementskit'),  
				'key' => 'ekit_yelp_review_top_right_logo',       
				'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 
					'ekit_review_card_top_right_logo' => 'yes' 
				]
			], 
			esc_html__('controls_section_top_right_logo', 'elementskit')
		);
        
        // Bottom posted on logo
		$this->controls_section(
			[ 
				'label' => esc_html__('Posted On', 'elementskit'),       
				'key' => 'ekit_yelp_review_bottom_posted_on_section', 
				'tab' => Controls_Manager::TAB_STYLE, 
				'condition' => [ 
					'ekit_review_card_posted_on' => 'yes', 
					'ekit_review_styles!' => 'default' 
				]
			], 
			esc_html__('controls_section_bottom_posted_on', 'elementskit')
		);
		
		// Arrow
		$this->controls_section_arrow();

		// Bottom posted on logo
		$this->controls_section(
			[ 
				'label' => esc_html__('Dots', 'elementskit'),       
				'key' => esc_html__('dots_section', 'elementskit'), 
				'tab' => Controls_Manager::TAB_STYLE, 
				'condition' => [ 
					'ekit_review_slideshow_show_dot' => 'yes'
				]
			], 
			esc_html__('controls_section_dots', 'elementskit')
		);

	}

    protected function render( ) {
        echo '<div class="ekit-wid-con" >';
            $this->render_raw();
        echo '</div>';
	}

    public function render_raw(){

        $settings = $this->get_settings_for_display();
		extract($settings);

        $handler_url = Handler::get_url();

        //=================================

        $overview             = isset($ekit_review_overview_card) && $ekit_review_overview_card == 'yes';
        $allOverviews         = isset($ekit_review_show_all_overviews) && $ekit_review_show_all_overviews == 'yes';
        $thumbnail_badge      = isset($ekit_review_card_thumbnail_badge) && $ekit_review_card_thumbnail_badge == 'yes';
        $border               = isset($ekit_review_card_border_type_border) && $ekit_review_card_border_type_border;
        $align_content_center = $ekit_review_card_align_center == 'yes' && $ekit_review_card_type != 'bubble' && $ekit_review_styles != 'default';
        $format_comment       = $ekit_review_card_appearance == 'grid' || $ekit_review_styles == 'slideshow';

        // Start Joining Card Classes
        $card_classes = 'ekit-review-card ekit-review-card-yelp';
        if($align_content_center)                       $card_classes .= ' ekit-review-card-align-center';
        if($ekit_review_styles == 'overviews')          $card_classes .= ' ekit-review-card-overview';
        if($ekit_review_card_stars_inline == 'yes')     $card_classes .= ' ekit_review_card_stars_inline';
        if($ekit_review_card_thumbnail_left == 'yes')   $card_classes .= ' ekit-review-card-thumbnail-left';
        if($ekit_review_card_name_at_bottom == 'yes')   $card_classes .= ' ekit-review-card-name-bottom ekit-review-card-thumbnail-left';
        if($ekit_review_card_type == 'bubble' && $ekit_review_styles != 'overviews') $card_classes .= ' ekit-review-card-bubble';

        // End Joining Card Classes

        $data = Handler::get_data();
		$page_Info   = null;

        if (!isset($data->error)): ?>

            <!-- Start Markup -->
            <div class="ekit-review-wrapper ekit-review-wrapper-yelp">

				<?php if($ekit_review_styles == 'default') :
					require Handler::get_dir() . 'markup/only-overviews.php';
				else:

					if($overview && $page_Info) require Handler::get_dir() . 'markup/overview-card.php';
					if(!empty($data->reviews)):
						if($ekit_yelp_review_only_positive == 'positive') {
							$data->reviews = array_filter($data->reviews, function($item) {
								return $item->rating >= 4 ;
							});
						}
						?>
						<div class="ekit-review-cards <?php echo "ekit-review-cards-" . $ekit_review_card_appearance ?>">
							<?php
							if(isset($ekit_review_styles) && $ekit_review_styles == 'slideshow') :

								$config = [
									'rtl'				=> is_rtl(),
									'arrows'			=> $ekit_review_slideshow_show_arrow == 'yes' ? true : false,
									'dots'				=> $ekit_review_slideshow_show_dot == 'yes' ? true : false,
									'autoplay'			=> $ekit_review_slideshow_autoplay == 'yes' ? true : false,
									'speed'				=> !empty($ekit_review_slideshow_speed) ? $ekit_review_slideshow_speed : 1000,
									'infinite'			=> $ekit_review_slideshow_autoplay == 'yes' ? true : false,
									'slidesPerView'		=> !empty($ekit_review_slideshow_slides_to_show) ? $ekit_review_slideshow_slides_to_show : 3,
									'slidesPerGroup'	=> !empty($ekit_review_slideshow_slides_to_scroll) ? $ekit_review_slideshow_slides_to_scroll : 1,
									'pauseOnMouseEnter'	=> $ekit_review_slideshow_pause_on_hover == 'yes' ? true : false,
									'loop'				=> !empty($carousel_loop) ? true : false,
									'breakpointsInverse'	=> true,
									'breakpoints'		=> [
										360 => [
											'slidesPerView'     => !empty($ekit_review_slideshow_slides_to_show_mobile) ? $ekit_review_slideshow_slides_to_show_mobile : 1,
											'slidesPerGroup'    => !empty($ekit_review_slideshow_slides_to_scroll_mobile) ? $ekit_review_slideshow_slides_to_scroll_mobile : 1,
											'spaceBetween'      => !empty($ekit_review_slideshow_left_right_spacing_mobile['size']) ? $ekit_review_slideshow_left_right_spacing_mobile['size'] : 10,
										],
										768 => [
											'slidesPerView'     => !empty($ekit_review_slideshow_slides_to_show_tablet) ? $ekit_review_slideshow_slides_to_show_tablet : 2,
											'slidesPerGroup'    => !empty($ekit_review_slideshow_slides_to_scroll_tablet) ? $ekit_review_slideshow_slides_to_scroll_tablet : 1,
											'spaceBetween'      => !empty($ekit_review_slideshow_left_right_spacing_tablet['size']) ? $ekit_review_slideshow_left_right_spacing_tablet['size'] : 10,
										],
										1024 => [
											'slidesPerView'     => !empty($ekit_review_slideshow_slides_to_show) ? $ekit_review_slideshow_slides_to_show : 3,
											'slidesPerGroup'    => !empty($ekit_review_slideshow_slides_to_scroll) ? $ekit_review_slideshow_slides_to_scroll : 1,
											'spaceBetween'      => !empty($ekit_review_slideshow_left_right_spacing['size']) ? $ekit_review_slideshow_left_right_spacing['size'] : 15,
										]
									],
								];

								// Main wrapper
								$this->add_render_attribute('wrapper', [
									'class' => ['ekit-review-slider-wrapper', 'ekit-review-slider-wrapper-yelp', 'arrow_inside'],
									'data-config' => wp_json_encode($config),
								]);

								// Swiper container
								$this->add_render_attribute(
									'swiper-container',
									[
										'class'	=> method_exists('\ElementsKit_Lite\Utils', 'swiper_class') ? \ElementsKit_Lite\Utils::swiper_class() : 'swiper',
									]
								);
								?>

								<!-- Start slideshow -->
								<div <?php $this->print_render_attribute_string('wrapper'); ?>>
									<div <?php $this->print_render_attribute_string('swiper-container'); ?>>
										<div class="swiper-wrapper">
											<?php foreach($data->reviews as $item) :
												$time      = strtotime($item->time_created);
												$star_icon = $item->rating == 5 ? 'icon-star-1' : 'icon-star1';
												?>
												<div class="swiper-slide">
													<div class="swiper-slide-inner">
														<?php require Handler::get_dir() . 'markup/review-card.php'; ?>
													</div>
												</div>
											<?php endforeach;?>
										</div>
									
										<?php if(!empty($ekit_review_slideshow_show_dot)) : ?>
											<div class="swiper-pagination"></div>
										<?php endif; ?>

										<?php if(!empty($ekit_review_slideshow_show_arrow)) : ?>
											<div class="swiper-navigation-button swiper-button-prev"><?php Icons_Manager::render_icon($slideshow_left_arrows, [ 'aria-hidden' => 'true' ]); ?></div>
											<div class="swiper-navigation-button swiper-button-next"><?php Icons_Manager::render_icon($slideshow_right_arrows, [ 'aria-hidden' => 'true' ]); ?></div>
										<?php endif; ?>
									</div>
                                </div>
                                <!-- End slideshow -->
								<?php

							elseif($ekit_review_card_appearance == 'grid'): 
								$grid_column_count = $this->format_column($settings, 'ekit_review_responsive_column');
							?>
                                <!-- Start review cards -->
                                <div class="row ekit-fb-row ekit-layout-grid"> <?php
									foreach($data->reviews as $item) :
										$time = strtotime($item->time_created);
										$star_icon = $item->rating == 5 ? 'icon-star-1' : 'icon-star1'; ?>
                                        <div class='<?php echo esc_attr($grid_column_count); ?>'>
											<?php require Handler::get_dir() . 'markup/review-card.php'; ?>
                                        </div> <?php
									endforeach; ?>
                                </div>
                                <!-- End review cards -->
								<?php

							elseif($ekit_review_card_appearance == 'masonry'):
								$masonry_column_count = $this->format_column($settings, 'ekit_review_responsive_column');
							?>
                                <div class="masonry ekit-fb-row ekit-layout-masonary <?php echo esc_attr( $masonry_column_count ); ?>">
									<?php
									foreach($data->reviews as  $item) :
										$time      = strtotime($item->time_created);
										$star_icon = $item->rating == 5 ? 'icon-star-1' : 'icon-star1';
										require Handler::get_dir() . 'markup/review-card.php';
									endforeach;
									?>
                                </div>
								<?php

							else:
								foreach($data->reviews as $key => $item) :
									$time      = strtotime($item->time_created);
									$star_icon = $item->rating == 5 ? 'icon-star-1' : 'icon-star1';

									require Handler::get_dir() . 'markup/review-card.php';
								endforeach;
							endif; ?>
                        </div>
						<?php

					endif;
				endif ?>

            </div>
            <!-- End Markup -->
        <?php endif;
	}
}
