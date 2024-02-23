<?php
function search_suggestions_block_add_style() {
    if( ! function_exists('jet_sm_register_style_for_block') ){
        return;
    }

    $css_scheme = apply_filters(
        'jet-search/search-suggestions/css-scheme',
        array(
            'form'                   => '.jet-search-suggestions__form',
            'form_focus'             => '.jet-search-suggestions__form--focus',
            'fields_holder'          => '.jet-search-suggestions__fields-holder',
            'field_wrapper'          => '.jet-search-suggestions__field-wrapper',
            'field'                  => '.jet-search-suggestions__field',
            'categories'             => '.jet-search-suggestions__categories',
            'categories_select'      => '.jet-search-suggestions__categories-select',
            'categories_select_icon' => '.jet-search-suggestions__categories-select-icon',
            'submit'                 => '.jet-search-suggestions__submit',
            'submit_icon'            => '.jet-search-suggestions__submit-icon',
            'submit_label'           => '.jet-search-suggestions__submit-label',
            'focus_area'             => '.jet-search-suggestions__focus-area',
            'focus_area_item'        => '.jet-search-suggestions__focus-area-item',
            'focus_area_item_title'  => '.jet-search-suggestions__focus-area-item-title',
            'inline_area'            => '.jet-search-suggestions__inline-area',
            'inline_area_item'       => '.jet-search-suggestions__inline-area-item',
            'inline_area_item_title' => '.jet-search-suggestions__inline-area-item-title',
            'message'                => '.jet-search-suggestions__message',
            'spinner'                => '.jet-search-suggestions__spinner',
        )
    );

    $controls_manager = jet_sm_register_style_for_block( 'jet-search/search-suggestions' );

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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['form'] => 'background-color: {{VALUE}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['form_focus'] => 'background-color: {{VALUE}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['form_focus'] => 'border-color: {{VALUE}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['form'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['form'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['field'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_placeholder_typography',
            'label'        => esc_html__( 'Placeholder Typography', 'jet-search' ),
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['field'] . '::placeholder' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['field'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['field'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_border_color',
            'label'        => esc_html__( 'Border Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['field'] => 'border-color: {{VALUE}};',
            ),
        )
    );

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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['form_focus'] . ' ' . $css_scheme['field'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_bg_color_focus',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['form_focus'] . ' ' . $css_scheme['field'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_input_border_color_focus',
            'label'        => esc_html__( 'Border Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['form_focus'] . ' ' . $css_scheme['field'] => 'border-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_input_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['field'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['field'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['field'] => 'border-width: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['field'] => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['submit_label'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_submit_icon_font_size',
            'type'         => 'range',
            'label'        => esc_html__( 'Icon Font Size', 'jet-search' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['submit_icon'] => 'font-size: {{VALUE}}{{UNIT}}',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['submit'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_submit_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['submit'] => 'background-color: {{VALUE}};',
            ),
        )
    );

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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['submit'] . ':hover' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_submit_bg_color_hover',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['submit'] . ':hover' => 'background-color: {{VALUE}};',
            ),
        )
    );

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
            '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['submit'] => 'align-self: {{VALUE}};',
        ),
    ));

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_submit_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['submit'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['submit'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['submit'] => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories'] => 'width: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select'] . ', {{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select_icon'] . ' svg' => 'width: {{VALUE}}{{UNIT}}; height: {{VALUE}}{{UNIT}}',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select'] => 'color: {{VALUE}};',
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_icon_color',
            'label'        => esc_html__( 'Arrow Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select_icon'] . ' svg > *' => 'fill: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select'] => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_border_color',
            'label'        => esc_html__( 'Border Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select'] => 'border-color: {{VALUE}};',
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single' => 'border-color: {{VALUE}};',
            ),
        )
    );

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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select'] . ':focus' => 'color: {{VALUE}};',
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories'] . ' .chosen-container-single.chosen-with-drop  .chosen-single' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_icon_color_focus',
            'label'        => esc_html__( 'Arrow Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select'] . ':focus ~ ' . $css_scheme['categories_select_icon'] . ' svg > *' => 'fill: {{VALUE}};',
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories'] . ' .chosen-with-drop ~ ' . $css_scheme['categories_select_icon'] . ' svg > *' => 'fill: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_bg_color_focus',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select'] . ':focus' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories'] . ' .chosen-container-single.chosen-with-drop  .chosen-single' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_border_color_focus',
            'label'        => esc_html__( 'Border Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select'] . ':focus' => 'border-color: {{VALUE}};',
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories'] . ' .chosen-container-single.chosen-with-drop  .chosen-single' => 'border-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_category_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
                'body:not(.rtl) {{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select_icon'] => 'right: {{RIGHT}};',
                'body.rtl {{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select_icon'] => 'left: {{LEFT}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories'] => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select'] => 'border-width: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single' => 'border-width: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories_select'] => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-single' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['categories'] . ' .chosen-results' => 'max-height: {{VALUE}}{{UNIT}}',
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
                '{{WRAPPER}} .jet-search-suggestions-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-drop' => 'margin-top: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-drop' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'           => 'search_category_dropdown_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-drop' => 'padding: {{TOP}} 0 {{BOTTOM}} 0;',
                '{{WRAPPER}} .jet-search-suggestions-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results' => 'padding: 0 {{RIGHT}} 0 {{LEFT}};',
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
                '{{WRAPPER}} .jet-search-suggestions-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-drop' => 'border-style: {{STYLE}}; border-width: {{WIDTH}}; border-radius: {{RADIUS}}; border-color: {{COLOR}}',
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
                '{{WRAPPER}} .jet-search-suggestions-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-drop ::-webkit-scrollbar-thumb' => 'background-color: {{VALUE}};',
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
                '{{WRAPPER}} .jet-search-suggestions-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li' => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
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
                '{{WRAPPER}} .jet-search-suggestions-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_items_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li' => 'background-color: {{VALUE}};',
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
                '{{WRAPPER}} .jet-search-suggestions-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li.highlighted' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_items_bg_color_hover',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li.highlighted' => 'background-color: {{VALUE}};',
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
                '{{WRAPPER}} .jet-search-suggestions-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
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
                '{{WRAPPER}} .jet-search-suggestions-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'search_category_dropdown_items_gap',
            'type'         => 'range',
            'label'        => esc_html__( 'Gap', 'jet-search' ),
            'attributes'   => array(
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
                '{{WRAPPER}} .jet-search-suggestions-block .jet-ajax-search ' . $css_scheme['categories'] . ' .chosen-container-single .chosen-results li:not(:first-child)' => 'margin-top: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->end_section();

    /**
     * `Inline Area` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_inline_area_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'Inline Area', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'inline_area_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Inline Area', 'jet-search' ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'         => 'inline_area_gap',
            'type'       => 'range',
            'label'      => esc_html__( 'Gap', 'jet-search' ),
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['inline_area'] => 'margin-top: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'inline_area_item_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Inline Area Item', 'jet-search' ),
            'separator'  => 'before',
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'inline_area_item_title_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block ' . $css_scheme['inline_area_item_title'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'         => 'inline_area_item_column_gap',
            'type'       => 'range',
            'label'      => esc_html__( 'Column Gap', 'jet-search' ),
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['inline_area'] => 'column-gap: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'         => 'inline_area_item_rows_gap',
            'type'       => 'range',
            'label'      => esc_html__( 'Rows gap', 'jet-search' ),
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
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['inline_area'] => 'row-gap: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->start_tabs(
        'style_controls',
        array(
            'id'         => 'tabs_inline_area_item',
            'separator'  => 'both',
        )
    );

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_inline_area_item_normal',
            'title' => esc_html__( 'Normal', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'inline_area_item_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block ' . $css_scheme['inline_area_item_title'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'inline_area_item_color',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block ' . $css_scheme['inline_area_item_title'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'inline_area_item_border_radius',
            'label'        => esc_html__( 'Border Radius', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['inline_area_item_title'] => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
            'condition' => array(
                'inline_area_item_bg_color!' => '',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_inline_area_item_hover',
            'title' => esc_html__( 'Hover', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'inline_area_item_bg_color_hover',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block ' . $css_scheme['inline_area_item_title'] . ':hover' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'inline_area_item_color_hover',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block ' . $css_scheme['inline_area_item_title'] . ':hover' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'inline_area_item_border_radius_hover',
            'label'        => esc_html__( 'Border Radius', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['inline_area_item_title'] . ':hover' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
            'condition' => array(
                'inline_area_item_bg_color!' => '',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_control(
        array(
            'id'           => 'inline_area_item_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['inline_area_item_title'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
            'separator'  => 'before',
        )
    );

    $controls_manager->end_section();

    /**
     * `Focus Area` Style Section
     */

    $controls_manager->start_section(
        'style_controls',
        array(
            'id'          => 'section_focus_area_style',
            'initialOpen' => false,
            'title'       => esc_html__( 'Focus Area', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'focus_area_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Focus Area', 'jet-search' ),
        )
    );

    $controls_manager->add_responsive_control(
        array(
            'id'    => 'focus_area_gap',
            'type'  => 'range',
            'label' => esc_html__( 'Gap', 'jet-search' ),
            'units' => array(
                array(
                    'value'     => 'px',
                    'intervals' => array(
                        'step' => 1,
                        'min'  => 0,
                        'max'  => 100,
                    )
                ),
            ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['focus_area'] => 'margin-top: {{VALUE}}{{UNIT}}',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'      => 'focus_area_item_heading',
            'type'    => 'text',
            'content' => esc_html__( 'Focus Area Item', 'jet-search' ),
            'separator'  => 'before',
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'focus_area_item_title_typography',
            'type'         => 'typography',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block ' . $css_scheme['focus_area_item_title'] => 'font-family: {{FAMILY}}; font-weight: {{WEIGHT}}; text-transform: {{TRANSFORM}}; font-style: {{STYLE}}; text-decoration: {{DECORATION}}; line-height: {{LINEHEIGHT}}{{LH_UNIT}}; letter-spacing: {{LETTERSPACING}}{{LS_UNIT}}; font-size: {{SIZE}}{{S_UNIT}};',
            ),
        )
    );

    $controls_manager->start_tabs(
        'style_controls',
        array(
            'id'         => 'tabs_focus_area_item',
            'separator'  => 'both',
        )
    );

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_focus_area_item_normal',
            'title' => esc_html__( 'Normal', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'focus_area_item_bg_color',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block ' . $css_scheme['focus_area_item'] => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'focus_area_item_color',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block ' . $css_scheme['focus_area_item_title'] => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->start_tab(
        'style_controls',
        array(
            'id'    => 'tab_focus_area_item_hover',
            'title' => esc_html__( 'Hover', 'jet-search' ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'focus_area_item_bg_color_hover',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block ' . $css_scheme['focus_area_item'] . ':hover' => 'background-color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'focus_area_item_color_hover',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block ' . $css_scheme['focus_area_item_title'] . ':hover' => 'color: {{VALUE}};',
            ),
        )
    );

    $controls_manager->end_tab();

    $controls_manager->end_tabs();

    $controls_manager->add_control(
        array(
            'id'           => 'focus_area_item_padding',
            'label'        => esc_html__( 'Padding', 'jet-search' ),
            'type'         => 'dimensions',
            'units'        => array( 'px', '%' ),
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['focus_area_item_title'] => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
            ),
            'separator' => 'before',
        )
    );

    $controls_manager->add_control(
        array(
            'id'        => 'focus_area_item_highlight',
            'type'      => 'text',
            'content'   => esc_html__( 'Results Highlight', 'jet-search' ),
            'separator' => 'before',
            'condition' => array(
                'highlight_searched_text' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'focus_area_item_highlight_color',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block ' . $css_scheme['focus_area_item_title'] . ' mark' => 'color: {{VALUE}};',
            ),
            'condition' => array(
                'highlight_searched_text' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'focus_area_item_highlight_bg',
            'label'        => esc_html__( 'Background Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions-block ' . $css_scheme['focus_area_item_title'] . ' mark' => 'background: {{VALUE}};',
            ),
            'condition' => array(
                'highlight_searched_text' => true,
            ),
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
            'condition'   => array(
                'show_search_suggestions_list_on_focus_preloader' => true,
            ),
        )
    );

    $controls_manager->add_control(
        array(
            'id'           => 'spinner_color',
            'label'        => esc_html__( 'Color', 'jet-search' ),
            'type'         => 'color-picker',
            'css_selector' => array(
                '{{WRAPPER}} .jet-search-suggestions ' . $css_scheme['spinner'] => 'color: {{VALUE}};',
            ),
            'condition' => array(
                'show_search_suggestions_list_on_focus_preloader' => true,
            ),
        )
    );

    $controls_manager->end_section();
}
?>