<?php
function ajax_search_block_add_style() {
    if( ! function_exists('jet_sm_register_style_for_block') ){
        return;
    }

    $css_scheme = apply_filters(
        'jet-search/ajax-search/css-scheme',
        array(
            'form'                           => '.jet-ajax-search__form',
            'form_focus'                     => '.jet-ajax-search__form--focus',
            'fields_holder'                  => '.jet-ajax-search__fields-holder',
            'field_wrapper'                  => '.jet-ajax-search__field-wrapper',
            'field'                          => '.jet-ajax-search__field',
            'field_icon'                     => '.jet-ajax-search__field-icon',
            'categories'                     => '.jet-ajax-search__categories',
            'categories_select'              => '.jet-ajax-search__categories-select',
            'categories_select_icon'         => '.jet-ajax-search__categories-select-icon',
            'submit'                         => '.jet-ajax-search__submit',
            'submit_icon'                    => '.jet-ajax-search__submit-icon',
            'submit_label'                   => '.jet-ajax-search__submit-label',
            'results_area'                   => '.jet-ajax-search__results-area',
            'results_header'                 => '.jet-ajax-search__results-header',
            'results_list'                   => '.jet-ajax-search__results-list',
            'results_slide'                  => '.jet-ajax-search__results-slide',
            'results_footer'                 => '.jet-ajax-search__results-footer',
            'results_item'                   => '.jet-ajax-search__results-item',
            'results_item_link'              => '.jet-ajax-search__item-link',
            'results_item_thumb'             => '.jet-ajax-search__item-thumbnail',
            'results_item_thumb_img'         => '.jet-ajax-search__item-thumbnail-img',
            'results_item_thumb_placeholder' => '.jet-ajax-search__item-thumbnail-placeholder',
            'results_item_title'             => '.jet-ajax-search__item-title',
            'results_item_content'           => '.jet-ajax-search__item-content',
            'results_item_price'             => '.jet-ajax-search__item-price',
            'results_item_rating'            => '.jet-ajax-search__item-rating',
            'results_rating_star'            => '.jet-ajax-search__rating-star',
            'results_counter'                => '.jet-ajax-search__results-count',
            'full_results'                   => '.jet-ajax-search__full-results',
            'bullet_btn'                     => '.jet-ajax-search__bullet-button',
            'number_btn'                     => '.jet-ajax-search__number-button',
            'active_nav_btn'                 => '.jet-ajax-search__active-button',
            'arrow_btn'                      => '.jet-ajax-search__arrow-button',
            'message'                        => '.jet-ajax-search__message',
            'spinner'                        => '.jet-ajax-search__spinner',
        )
    );

    $controls_manager = jet_sm_register_style_for_block( 'jet-search/ajax-search' );

    /**
     * `Search Form` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_search_form_style',
            'initialOpen' => true,
            'title'       => esc_html__( 'Search Form', 'jet-search' ),
        )
    );

    $controls_manager->start_tabs(
        'style_controls',
        array(
            'id'         => 'tabs_search_form',
            'separator'  => 'both',
        )
    );

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_search_form_normal',
            'title' => esc_html__( 'Normal', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_form_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['form'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_search_form_focus',
            'title' => esc_html__( 'Focus', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_form_bg_color_focus',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['form_focus'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_form_border_color_focus',
            'label'        => esc_html__( 'Border Color', 'jet-search' ),
            'help'         => esc_html__( 'Border width must be more then 0', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['form_focus'] => 'border-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_form_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['form'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
            'separator' => 'after',
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_form_border_width',
            'label'        => esc_html__( 'Border', 'jet-search' ),
            'type'         => 'border',
            'units'        => array( 'px', '%', 'em' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['form'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
            ),
        )
    );

    $controls_manager->end_section();

    /**
     * `Input Field and Categories List Wrapper` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_search_fields_holder_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'Input Field and Categories List Wrapper', 'jet-search' ),
            'condition' => array(
                'show_search_category_list' => true,
            ),
        )
    );

    $controls_manager->start_tabs(
        'style_controls',
        array(
            'id'         => 'tabs_search_fields_holder',
            'separator'  => 'both',
        )
    );

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_search_fields_holder_normal',
            'title' => esc_html__( 'Normal', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_fields_holder_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['fields_holder'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    // Not supported
    // $this->add_group_control(
    // 	Group_Control_Box_Shadow::get_type(),
    // 	array(
    // 		'name'      => 'search_fields_holder_box_shadow',
    // 		'selector'  => '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['fields_holder'],
    // 		'condition' => array(
    // 			'show_search_category_list' => 'yes',
    // 		),
    // 	)
    // );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_search_fields_holder_focus',
            'title' => esc_html__( 'Focus', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_fields_holder_bg_color_focus',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['form_focus'] . ' ' . $css_scheme['fields_holder'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_fields_holder_border_color_focus',
            'label'        => esc_html__( 'Border Color', 'jet-search' ),
            'help'         => esc_html__( 'Border width must be more then 0', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['form_focus'] . ' ' . $css_scheme['fields_holder'] => 'border-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_fields_holder_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['fields_holder'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
            'separator' => 'after',
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_fields_holder_border',
            'label'        => esc_html__( 'Border', 'jet-search' ),
            'type'         => 'border',
            'units'        => array( 'px', '%', 'em' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['fields_holder'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
            ),
        )
    );

    $controls_manager->end_section();

    /**
     * `Input Field` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_search_input_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'Input Field', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['field'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_placeholder_typography',
            'label'        => esc_html__( 'Placeholder Typography', 'jet-search' ),
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['field'] . '::placeholder' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    //
    //search_input_icon_style
    //

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_icon_font_size',
            'type'         => 'range',
            'label'        => esc_html__( 'Icon Font Size', 'jet-search' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['field_icon'] => 'font-size: {{VALUE}}{{UNIT}}',
            ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 14,
                        'unit' => 'px',
                    )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 100,
                    )
                ),
                array(
                    'value' => 'em',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 100,
                    )
                ),
                array(
                    'value' => 'rem',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 100,
                    )
                ),
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_icon_gap',
            'type'         => 'range',
            'label'        => esc_html__( 'Icon Gap', 'jet-search' ),
            'css_selector' => array(
                'body:not(.rtl) {{WRAPPER}} .jet-ajax-search ' . $css_scheme['field_icon'] => 'left: {{VALUE}}{{UNIT}}',
                'body.rtl {{WRAPPER}} .jet-ajax-search ' . $css_scheme['field_icon'] => 'right: {{VALUE}}{{UNIT}}'
            ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 14,
                        'unit' => 'px',
                    )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 50,
                    )
                ),
            ),
        )
    );

    $controls_manager->start_tabs(
        'style_controls',
        array(
            'id'         => 'tabs_search_input',
            'separator'  => 'both',
        )
    );

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_search_input_normal',
            'title' => esc_html__( 'Normal', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_color',
            'label'        => esc_html__( 'Text Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['field'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_icon_color',
            'label'        => esc_html__( 'Icon Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['field_icon'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['field'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_border_color',
            'label'        => esc_html__( 'Border Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['field'] => 'border-color: {{VALUE}};',
            ),
        )
    );

    // Not supported
    // $this->add_group_control(
    // 	Group_Control_Box_Shadow::get_type(),
    // 	array(
    // 		'name'     => 'search_input_box_shadow',
    // 		'selector' => '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['field'],
    // 	)
    // );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_search_input_focus',
            'title' => esc_html__( 'Focus', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_color_focus',
            'label'        => esc_html__( 'Text Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['form_focus'] . ' ' . $css_scheme['field'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_icon_color_focus',
            'label'        => esc_html__( 'Icon Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['form_focus'] . ' ' . $css_scheme['field_icon'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_bg_color_focus',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['form_focus'] . ' ' . $css_scheme['field'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_border_color_focus',
            'label'        => esc_html__( 'Border Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['form_focus'] . ' ' . $css_scheme['field'] => 'border-color: {{VALUE}};',
            ),
        )
    );

    // Not supported
    // $this->add_group_control(
    // 	Group_Control_Box_Shadow::get_type(),
    // 	array(
    // 		'name'     => 'search_input_box_shadow_focus',
    // 		'selector' => '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['form_focus'] . ' ' . $css_scheme['field'],
    // 	)
    // );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_input_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['field'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
            'separator' => 'after',
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_input_margin',
            'label'        => esc_html__( 'Margin', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['field'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
            'separator' => 'after',
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_input_border_width',
            'label'        => esc_html__( 'Border Width', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['field'] => 'border-width: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
            'separator' => 'after',
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_input_border_radius',
            'label'        => esc_html__( 'Border Radius', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['field'] => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
            'separator' => 'after',
        )
    );

    $controls_manager->end_section();

    /**
     * `Submit Button` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_search_submit_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'Submit Button', 'jet-search' ),
            'condition' => array(
                'show_search_submit' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_submit_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['submit_label'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_submit_icon_font_size',
            'type'         => 'range',
            'label'        => esc_html__( 'Icon Font Size', 'jet-search' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['submit_icon'] => 'font-size: {{VALUE}}{{UNIT}}',
            ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 14,
                        'unit' => 'px',
                    )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 100,
                    )
                ),
                array(
                    'value' => 'em',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 100,
                    )
                ),
                array(
                    'value' => 'rem',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 100,
                    )
                ),
            ),
        )
    );

    $controls_manager->start_tabs(
        'style_controls',
        array(
            'id'         => 'tabs_search_submit',
            'separator'  => 'both',
        )
    );

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_search_input_normal',
            'title' => esc_html__( 'Normal', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_submit_color',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['submit'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_submit_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['submit'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    // Not supported
    // $this->add_group_control(
    // 	Group_Control_Box_Shadow::get_type(),
    // 	array(
    // 		'name'     => 'search_submit_box_shadow',
    // 		'selector' => '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['submit'],
    // 	)
    // );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_search_submit_hover',
            'title' => esc_html__( 'Hover', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_submit_color_hover',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['submit'] . ':hover' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_submit_bg_color_hover',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['submit'] . ':hover' => 'background-color: {{VALUE}};',
            ),
        )
    );

    // Not supported
    // $this->add_group_control(
    // 	Group_Control_Box_Shadow::get_type(),
    // 	array(
    // 		'name'     => 'search_submit_box_shadow_hover',
    // 		'selector' => '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['submit'] . ':hover',
    // 	)
    // );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_control(
        array(
        'id'        => 'search_submit_vertical_align',
        'type'      => 'choose',
        'label'     => esc_html__( 'Vertical Align', 'jet-search' ),
        'options'   => array(
            '' => array(
                'shortcut' => esc_html__( 'Default', 'jet-search' ),
                'icon'     => 'dashicons-text-page',
            ),
            'flex-start' => array(
                'shortcut' => esc_html__( 'Top', 'jet-search' ),
                'icon'     => 'dashicons-arrow-up-alt',
            ),
            'center' => array(
                'shortcut' => esc_html__( 'Center', 'jet-search' ),
                'icon'     => 'dashicons-align-center',
            ),
            'flex-end' => array(
                'shortcut' => esc_html__( 'Bottom', 'jet-search' ),
                'icon'     => 'dashicons-arrow-down-alt',
            ),
            'stretch' => array(
                'shortcut' => esc_html__( 'Stretch', 'jet-search' ),
                'icon'     => 'dashicons-editor-justify',
            ),
        ),
        'css_selector' => array(
            '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['submit'] => 'align-self: {{VALUE}};',
        ),
    ));

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_submit_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['submit'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_submit_margin',
            'label'        => esc_html__( 'Margin', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['submit'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_submit_border',
            'label'        => esc_html__( 'Border', 'jet-search' ),
            'type'         => 'border',
            'units'        => array( 'px', '%', 'em' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['submit'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
            ),
        )
    );

    $controls_manager->end_section();

    /**
     * `Categories List` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_search_category_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'Categories List', 'jet-search' ),
            'condition' => array(
                'show_search_category_list' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_width',
            'type'         => 'range',
            'label'        => esc_html__( 'Width', 'jet-search' ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 200,
                        'unit' => 'px',
                        )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 500,
                    )
                ),
                array(
                    'value' => '%',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 500,
                    )
                ),
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] => 'width: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select'] . ', {{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_icon_font_size',
            'type'         => 'range',
            'label'        => esc_html__( 'Arrow Font Size', 'jet-search' ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 14,
                        'unit' => 'px',
                        )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 100,
                    )
                ),
                array(
                    'value' => 'em',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 100,
                    )
                ),
                array(
                    'value' => 'rem',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 100,
                    )
                ),
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select_icon'] . ' svg' => 'width: {{VALUE}}{{UNIT}}; height: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->start_tabs(
        'style_controls',
        array(
            'id'         => 'tabs_search_category',
            'separator'  => 'both',
        )
    );

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_search_category_normal',
            'title' => esc_html__( 'Normal', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_color',
            'label'        => esc_html__( 'Text Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select'] => 'color: {{VALUE}};',
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_icon_color',
            'label'        => esc_html__( 'Arrow Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select_icon'] . ' svg > *' => 'fill: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select'] => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_border_color',
            'label'        => esc_html__( 'Border Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select'] => 'border-color: {{VALUE}};',
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single' => 'border-color: {{VALUE}};',
            ),
        )
    );

    // Not supported
    // $this->add_group_control(
    // 	Group_Control_Box_Shadow::get_type(),
    // 	array(
    // 		'name'     => 'search_category_box_shadow',
    // 		'selector' => '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select'] . ', {{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single',
    // 	)
    // );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_search_category_focus',
            'title' => esc_html__( 'Focus', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_color_focus',
            'label'        => esc_html__( 'Text Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select'] . ':focus' => 'color: {{VALUE}};',
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single.chosen-with-drop  .chosen-single' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_icon_color_focus',
            'label'        => esc_html__( 'Arrow Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select'] . ':focus ~ ' . $css_scheme['categories_select_icon'] . ' svg > *' => 'fill: {{VALUE}};',
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-with-drop ~ ' . $css_scheme['categories_select_icon'] . ' svg > *' => 'fill: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_bg_color_focus',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select'] . ':focus' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single.chosen-with-drop  .chosen-single' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_border_color_focus',
            'label'        => esc_html__( 'Border Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select'] . ':focus' => 'border-color: {{VALUE}};',
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single.chosen-with-drop  .chosen-single' => 'border-color: {{VALUE}};',
            ),
        )
    );

    //Not supported
    // $this->add_group_control(
    // 	Group_Control_Box_Shadow::get_type(),
    // 	array(
    // 		'name'     => 'search_category_box_shadow_focus',
    // 		'selector' => '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select'] . ':focus , {{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single.chosen-with-drop  .chosen-single',
    // 	)
    // );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_category_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
                'body:not(.rtl) {{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select_icon'] => 'right: {{RIGHT}};',
                'body.rtl {{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select_icon'] => 'left: {{LEFT}};',
            ),
            'separator' => 'after',
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_category_margin',
            'label'        => esc_html__( 'Margin', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_category_border_width',
            'label'        => esc_html__( 'Border Width', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select'] => 'border-width: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single' => 'border-width: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_category_border_radius',
            'label'        => esc_html__( 'Border Radius', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories_select'] => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'search_category_dropdown_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Dropdown Style', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_max_height',
            'type'         => 'range',
            'label'        => esc_html__( 'Max Height', 'jet-search' ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 240,
                        'unit' => 'px',
                    )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 500,
                    )
                ),
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-results' => 'max-height: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_gap',
            'type'         => 'range',
            'label'        => esc_html__( 'Gap', 'jet-search' ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 10,
                        'unit' => 'px',
                    )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 100,
                    )
                ),
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-drop' => 'margin-top: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-drop' => 'background-color: {{VALUE}};',
            ),
        )
    );

    //Not supported
    // $this->add_group_control(
    // 	Group_Control_Box_Shadow::get_type(),
    // 	array(
    // 		'name'     => 'search_category_dropdown_box_shadow',
    // 		'selector' => '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-drop',
    // 	)
    // );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_category_dropdown_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-drop' => 'padding: {{TOP}} 0 {{BOTTOM}} 0;',
                '{{WRAPPER}} .jet-ajax-search-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results' => 'padding: 0 {{RIGHT}} 0 {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_category_dropdown_border',
            'label'        => esc_html__( 'Border', 'jet-search' ),
            'type'         => 'border',
            'units'        => array( 'px', '%', 'em' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-drop' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_scrollbar_thumb_bg',
            'label'        => esc_html__( 'Scrollbar Thumb Color', 'jet-search' ),
            'type'         => 'color-picker',
            'separator'    => 'before',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-drop ::-webkit-scrollbar-thumb' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'search_category_dropdown_items_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Dropdown Items Style', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_items_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    $controls_manager->start_tabs(
        'style_controls',
        array(
            'id'         => 'tabs_search_category_dropdown_items',
            'separator'  => 'both',
        )
    );

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_search_category_dropdown_items_normal',
            'title' => esc_html__( 'Normal', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_items_color',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_items_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_search_category_dropdown_items_hover',
            'title' => esc_html__( 'Hover', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_items_color_hover',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li.highlighted' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_items_bg_color_hover',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li.highlighted' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_items_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_items_border_radius',
            'label'        => esc_html__( 'Border Radius', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_items_gap',
            'type'         => 'range',
            'label'        => esc_html__( 'Gap', 'jet-search' ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 1,
                        'unit' => 'px',
                    )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 0,
                        'max'  => 50,
                    )
                ),
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li:not(:first-child)' => 'margin-top: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->end_section();

    /**
     * `Results Area` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_results_area_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'Results Area', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'results_area_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Results Area', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_area_gap',
            'type'         => 'range',
            'label'        => esc_html__( 'Gap', 'jet-search' ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 10,
                        'unit' => 'px',
                        )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 100,
                    )
                ),
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_area'] => 'margin-top: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_area_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_area'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    //Not supported
    // $this->add_group_control(
    // 	Group_Control_Box_Shadow::get_type(),
    // 	array(
    // 		'name'     => 'results_area_box_shadow',
    // 		'selector' => '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_area'],
    // 	)
    // );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'results_area_border',
            'label'        => esc_html__( 'Border', 'jet-search' ),
            'type'         => 'border',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_area'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'results_header_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Results Header', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_header_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' .  $css_scheme['results_header'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'results_header_border',
            'label'        => esc_html__( 'Border', 'jet-search' ),
            'type'         => 'border',
            'units'        => array( 'px', '%', 'em' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_header'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'results_list_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Results List', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'         => 'enable_scroll',
            'type'       => 'toggle',
            'label'      => esc_html__( 'Enable Scrolling', 'jet-search' ),
            'return_value' => array(
                'true'  => 'overflow-y: auto;',
                'false' => 'max-height: none !important',
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_slide'] => '{{VALUE}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_list_height',
            'type'         => 'range',
            'label'        => esc_html__( 'Max Height (px)', 'jet-search' ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 500,
                    )
                ),
            ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 500,
                        'unit' => 'px'
                    )
                )
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_slide']  => 'max-height: {{VALUE}}{{UNIT}}',
            ),
            'condition' => array(
                'enable_scroll' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_list_scrollbar_bg',
            'label'        => esc_html__( 'Scrollbar Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_slide'] . '::-webkit-scrollbar' => 'background-color: {{VALUE}};',
            ),
            'condition' => array(
                'enable_scroll' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_list_scrollbar_thumb_bg',
            'label'        => esc_html__( 'Scrollbar Thumb Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_slide'] . '::-webkit-scrollbar-thumb' => 'background-color: {{VALUE}};',
            ),
            'condition' => array(
                'enable_scroll' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'results_footer_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Results Footer', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_footer_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' .  $css_scheme['results_footer'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'results_footer_border',
            'label'        => esc_html__( 'Border', 'jet-search' ),
            'type'         => 'border',
            'units'        => array( 'px', '%', 'em' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_footer'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'        => 'results_highlight_heading',
            'type'      => 'text',
            'content'   => esc_html__( 'Results Highlight', 'jet-search' ),
            'condition' => array(
                'highlight_searched_text' => true,
            ),
            'separator' => 'before'
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_highlight_color',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item'] . ' mark' => 'color: {{VALUE}};',
            ),
            'condition' => array(
                'highlight_searched_text' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_highlight_bg',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item'] . ' mark' => 'background: {{VALUE}};',
            ),
            'condition' => array(
                'highlight_searched_text' => true,
            ),
        )
    );

    $controls_manager->end_section();

    /**
     * `Results Items` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_results_items_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'Results Items', 'jet-search' ),
        )
    );

    $controls_manager->start_tabs(
        'style_controls',
        array(
            'id'         => 'tabs_results_item',
            'separator'  => 'both',
        )
    );

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_results_item_normal',
            'title' => esc_html__( 'Normal', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_link'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_title_color',
            'label'        => esc_html__( 'Title Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_title'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_content_color',
            'label'        => esc_html__( 'Content Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_content'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_rating_color',
            'label'        => esc_html__( 'Product Rating Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_rating_star'] . ':before' => 'color: {{VALUE}};',
            ),
            'condition' => array(
                'show_product_rating' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_rating_unmarked_color',
            'label'        => esc_html__( 'Product Rating Unmarked Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_rating_star'] => 'color: {{VALUE}};',
            ),
            'condition' => array(
                'show_product_price' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_price_color',
            'label'        => esc_html__( 'Product Price Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_price'] . ' .price' => 'color: {{VALUE}};',
            ),
            'condition' => array(
                'show_product_price' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_old_price_color',
            'label'        => esc_html__( 'Product Old Price Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_price'] . ' .price del' => 'color: {{VALUE}};',
            ),
            'condition' => array(
                'show_product_price' => true,
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_results_item_hover',
            'title' => esc_html__( 'Hover', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_bg_color_hover',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_link'] . ':hover' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_title_color_hover',
            'label'        => esc_html__( 'Title Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_link'] . ':hover ' . $css_scheme['results_item_title'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_content_color_hover',
            'label'        => esc_html__( 'Content Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_link'] . ':hover ' . $css_scheme['results_item_content'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_price_color_hover',
            'label'        => esc_html__( 'Product Price Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_link'] . ':hover ' . $css_scheme['results_item_price'] . ' .price' => 'color: {{VALUE}};',
            ),
            'condition' => array(
                'show_product_price' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_old_price_color_hover',
            'label'        => esc_html__( 'Product Old Price Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_link'] . ':hover ' . $css_scheme['results_item_price'] . ' .price del' => 'color: {{VALUE}};',
            ),
            'condition' => array(
                'show_product_price' => true,
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'results_item_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_link'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'        => 'results_item_align',
            'type'      => 'choose',
            'label'     => esc_html__( 'Alignment', 'jet-search' ),
            'options'   => array(
                'left' => array(
                    'shortcut' => esc_html__( 'Left', 'jet-search' ),
                    'icon'     => 'dashicons-editor-alignleft',
                ),
                'center' => array(
                    'shortcut' => esc_html__( 'Center', 'jet-search' ),
                    'icon'     => 'dashicons-editor-aligncenter',
                ),
                'right' => array(
                    'shortcut' => esc_html__( 'Right', 'jet-search' ),
                    'icon'     => 'dashicons-editor-alignright',
                ),
                'justify' => array(
                    'shortcut' => esc_html__( 'Justified', 'jet-search' ),
                    'icon'     => 'dashicons-editor-justify',
                ),
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_link'] => 'text-align: {{VALUE}};',
            ),
            'separator' => 'after'
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_divider',
            'type'         => 'toggle',
            'label'        => esc_html__( 'Divider', 'jet-search' ),
            'return_value' => array(
                'true'  => '1px;',
                'false' => '0 !important;',
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item'] . ':not(:first-child)' => 'border-top-width: {{VALUE}}{{UNIT}};'
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_divider_weight',
            'type'         => 'range',
            'label'        => esc_html__( 'Weight', 'jet-search' ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 1,
                        'unit' => 'px',
                    )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 20,
                    )
                ),
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item'] . ':not(:first-child)' => 'border-top-width: {{VALUE}}{{UNIT}};'
            ),
            'condition' => array(
                'results_item_divider' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_divider_color',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item'] . ':not(:first-child)' => 'border-top-color: {{VALUE}};',
            ),
            'condition' => array(
                'results_item_divider' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'results_item_thumb_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Thumbnail', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_thumb_width',
            'type'         => 'range',
            'label'        => esc_html__( 'Width', 'jet-search' ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 15,
                        'unit' => '%',
                    )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 0,
                        'max'  => 600,
                    )
                ),
                array(
                    'value' => '%',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 0,
                        'max'  => 600,
                    )
                ),
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_thumb'] => 'width: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_thumb_gap',
            'type'         => 'range',
            'label'        => esc_html__( 'Gap', 'jet-search' ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 10,
                        'unit' => 'px',
                    )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 0,
                        'max'  => 100,
                    )
                ),
            ),
            'css_selector' => array(
                'body:not(.rtl) {{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_thumb'] => 'margin-right: {{VALUE}}{{UNIT}}',
                'body.rtl {{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_thumb'] => 'margin-left: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_thumb_border_radius',
            'label'        => esc_html__( 'Border Radius', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_thumb_img'] => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'results_item_title_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Title', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_title_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_title'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_title_margin',
            'label'        => esc_html__( 'Margin', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_title'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'results_item_content_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Content', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_content_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_content'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_content_margin',
            'label'        => esc_html__( 'Margin', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_content'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'results_item_rating_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Product Rating', 'jet-search' ),
            'condition' => array(
                'show_product_rating' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_rating_font_size',
            'type'         => 'range',
            'label'        => esc_html__( 'Font Size', 'jet-search' ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 14,
                        'unit' => 'px',
                    )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 0,
                        'max'  => 100,
                    )
                ),
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_rating_star'] => 'font-size:: {{VALUE}}{{UNIT}}',
            ),
            'condition' => array(
                'show_product_rating' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_rating_margin',
            'label'        => esc_html__( 'Margin', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_rating'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
            'condition' => array(
                'show_product_rating' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'results_item_price_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Product Price', 'jet-search' ),
            'condition' => array(
                'show_product_price' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_price_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_price'] . ' .price' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
            'condition' => array(
                'show_product_price' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'label'        => esc_html__( 'Old Price Typography', 'jet-search' ),
            'id'           => 'results_item_previous_price_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_price'] . ' .price del' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
            'condition' => array(
                'show_product_price' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_item_price_margin',
            'label'        => esc_html__( 'Margin', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_item_price'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
            'condition' => array(
                'show_product_price' => true,
            ),
        )
    );

    $controls_manager->end_section();

    //
    //
    // Custom Fields styling ????
    //
    //
    //

    /**
     * `Results Counter` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_results_counter_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'Results Counter', 'jet-search' ),
            'condition' => array(
                'show_results_counter' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_counter_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_counter'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    $controls_manager->start_tabs(
        'style_controls',
        array(
            'id'         => 'tabs_results_counter',
            'separator'  => 'both',
        )
    );

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_results_counter_normal',
            'title' => esc_html__( 'Normal', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_counter_color',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_counter'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_counter_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_counter'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_results_counter_hover',
            'title' => esc_html__( 'Hover', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_counter_color_hover',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_counter'] . ':hover' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'results_counter_bg_color_hover',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_counter'] . ':hover' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_control(
        array(
            'id'           => 'results_counter_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_counter'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'results_counter_border',
            'label'        => esc_html__( 'Border', 'jet-search' ),
            'type'         => 'border',
            'units'        => array( 'px', '%', 'em' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['results_counter']=> 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
            ),
        )
    );

    $controls_manager->end_section();

    /**
     * `All Results Button` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_full_results_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'All Results Button', 'jet-search' ),
            'condition'   => array(
                'show_full_results' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'full_results_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['full_results'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    $controls_manager->start_tabs(
        'style_controls',
        array(
            'id'         => 'tabs_full_results',
            'separator'  => 'both',
        )
    );

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_full_results_normal',
            'title' => esc_html__( 'Normal', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'full_results_color',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['full_results'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'full_results_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['full_results'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_full_results_hover',
            'title' => esc_html__( 'Hover', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'full_results_color_hover',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['full_results'] . ':hover' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'full_results_bg_color_hover',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['full_results'] . ':hover' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_control(
        array(
            'id'           => 'full_results_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['full_results'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'full_results_border',
            'label'        => esc_html__( 'Border', 'jet-search' ),
            'type'         => 'border',
            'units'        => array( 'px', '%', 'em' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['full_results'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
            ),
        )
    );

    $controls_manager->end_section();

    /**
     * `Bullet Pagination` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_bullet_pagination_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'Bullet Pagination', 'jet-search' ),
            'condition' => array(
                'bullet_pagination' => array( 'in_header', 'in_footer', 'both' ),
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'bullet_size',
            'type'         => 'range',
            'label'        => esc_html__( 'Size', 'jet-search' ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 15,
                        'unit' => 'px'
                    )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 50,
                    )
                ),
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['bullet_btn'] => 'width: {{VALUE}}{{UNIT}}; height: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->start_tabs(
        'style_controls',
        array(
            'id'         => 'tabs_bullet_pagination',
            'separator'  => 'both',
        )
    );

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_bullet_pagination_normal',
            'title' => esc_html__( 'Normal', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'bullet_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['bullet_btn'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_bullet_pagination_hover',
            'title' => esc_html__( 'Hover', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'bullet_bg_color_hover',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['bullet_btn'] . ':hover' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_bullet_pagination_active',
            'title' => esc_html__( 'Active', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'bullet_bg_color_active',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['bullet_btn'] . $css_scheme['active_nav_btn'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'bullet_border',
            'label'        => esc_html__( 'Border', 'jet-search' ),
            'type'         => 'border',
            'units'        => array( 'px', '%', 'em' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['bullet_btn'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
            ),
        )
    );

    $controls_manager->end_section();

    /**
     * `Number Pagination` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_number_pagination_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'Number Pagination', 'jet-search' ),
            'condition' => array(
                'number_pagination' => array( 'in_header', 'in_footer', 'both' ),
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'number_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['number_btn'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    $controls_manager->start_tabs(
        'style_controls',
        array(
            'id'         => 'tabs_number_pagination',
            'separator'  => 'both',
        )
    );

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_number_pagination_normal',
            'title' => esc_html__( 'Normal', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'number_color',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['number_btn'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'number_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['number_btn'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_number_pagination_hover',
            'title' => esc_html__( 'Hover', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'number_color_hover',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['number_btn'] . ':hover' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'number_bg_color_hover',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['number_btn'] . ':hover' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_number_pagination_active',
            'title' => esc_html__( 'Active', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'number_color_active',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['number_btn'] . $css_scheme['active_nav_btn'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'number_bg_color_active',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['number_btn'] . $css_scheme['active_nav_btn'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_control(
        array(
            'id'           => 'number_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['number_btn'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'number_border',
            'label'        => esc_html__( 'Border', 'jet-search' ),
            'type'         => 'border',
            'units'        => array( 'px' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['number_btn'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
            ),
        )
    );

    $controls_manager->end_section();

    /**
     * `Navigation Arrows` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_navigation_arrows_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'Navigation Arrows', 'jet-search' ),
            'condition' => array(
                'navigation_arrows' => array( 'in_header', 'in_footer', 'both' ),
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'arrow_font_size',
            'type'         => 'range',
            'label'        => esc_html__( 'Font Size', 'jet-search' ),
            'attributes' => array(
                'default' => array(
                    'value' => array(
                        'value' => 14,
                        'unit' => 'px'
                    )
                )
            ),
            'units' => array(
                array(
                    'value' => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 100,
                    )
                ),
                array(
                    'value' => 'em',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 100,
                    )
                ),
                array(
                    'value' => 'rem',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 1,
                        'max'  => 100,
                    )
                ),
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['arrow_btn'] . ' svg' => 'width: {{VALUE}}{{UNIT}}; height: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->start_tabs(
        'style_controls',
        array(
            'id'         => 'tabs_navigation_arrows',
            'separator'  => 'both',
        )
    );

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_navigation_arrows_normal',
            'title' => esc_html__( 'Normal', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'arrow_color',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['arrow_btn'] . ' svg > *' => 'fill: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'arrow_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['arrow_btn'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_navigation_arrows_hover',
            'title' => esc_html__( 'Hover', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'arrow_color_hover',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['arrow_btn'] . ':hover svg > *' => 'fill: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'arrow_bg_color_hover',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['arrow_btn'] . ':hover' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'arrow_border_color_hover',
            'label'        => esc_html__( 'Border Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['arrow_btn'] . ':hover' => 'border-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_control(
        array(
            'id'           => 'arrow_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['arrow_btn'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'arrow_border',
            'label'        => esc_html__( 'Border', 'jet-search' ),
            'type'         => 'border',
            'units'        => array( 'px' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['arrow_btn'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
            ),
        )
    );

    $controls_manager->end_section();

    /**
     * `Notifications` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_notifications_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'Notifications', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'notifications_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['message'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'notifications_color',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['message'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'        => 'notifications_align',
            'type'      => 'choose',
            'label'     => esc_html__( 'Alignment', 'jet-search' ),
            'options'   => array(
                'left' => array(
                    'shortcut' => esc_html__( 'Left', 'jet-search' ),
                    'icon'     => 'dashicons-editor-alignleft',
                ),
                'center' => array(
                    'shortcut' => esc_html__( 'Center', 'jet-search' ),
                    'icon'     => 'dashicons-editor-aligncenter',
                ),
                'right' => array(
                    'shortcut' => esc_html__( 'Right', 'jet-search' ),
                    'icon'     => 'dashicons-editor-alignright',
                ),
                'justify' => array(
                    'shortcut' => esc_html__( 'Justified', 'jet-search' ),
                    'icon'     => 'dashicons-editor-justify',
                ),
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['message'] => 'text-align: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'notifications_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['message'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
            'separator' => 'before'
        )
    );

    $controls_manager->end_section();

    /**
     * `Spinner` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_spinner_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'Spinner', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'spinner_color',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-ajax-search ' . $css_scheme['spinner'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_section();
}
?>