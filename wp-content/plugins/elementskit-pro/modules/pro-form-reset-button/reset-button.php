<?php

namespace ElementsKit\Modules\Pro_Form_Reset_Button;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if (!\defined('ABSPATH')) exit;

/**
 * The Main base class for the reset button
 */
class Reset_Button extends \ElementorPro\Modules\Forms\Fields\Field_Base
{

    public function __construct()
    {
        add_action('elementor/element/form/section_button_style/after_section_end', [$this, 'reset_button_add_style']);
        add_action('elementor/widget/print_template', [$this, 'reset_button_print_template'], 10, 2);
        parent::__construct();
    }

    public function get_name()
    {
        return __('Reset Button', 'elementskit');
    }
    public function get_type()
    {
        return 'reset-button';
    }

    public function reset_button_print_template($template, $widget)
    {
        if ('form' === $widget->get_name()) {
            $template = \false;
        }
        return $template;
    }

    public function reset_button_add_style($widget)
    {
        $widget->start_controls_section(
            'elementskit_reset_button_styles',
            [
                'label' => __('Reset Button', 'elementskit'),
                'tab' => Controls_Manager::TAB_STYLE
            ]
        );

        $widget->start_controls_tabs('elementskit_reset_button_style_tabs');
        $widget->start_controls_tab(
            'elementskit_reset_button_normal',
            [
                'label' => __('Normal', 'elementskit')
            ]
        );
        $widget->add_control(
            'elementskit_reset_button_background_color',
            [
                'label' => __('Background Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-reset-button.elementor-button' => 'background-color: {{VALUE}} !important;'
                ]
            ]
        );
        $widget->add_control(
            'elementskit_reset_button_text_color',
            [
                'label' => __('Text Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-reset-button.elementor-button' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-reset-button.elementor-button svg' => 'fill: {{VALUE}};'
                ]
            ]
        );
        $widget->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'elementskit_reset_button_typography',
                'selector' => '{{WRAPPER}} .elementskit-reset-button.elementor-button'
            ]
        );
        $widget->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'elementskit_reset_button_border',
                'selector' => '{{WRAPPER}} .elementskit-reset-button.elementor-button'
            ]
        );
        $widget->add_control(
            'elementskit_reset_button_border_radius',
            [
                'label' => __('Border Radius', 'elementskit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-reset-button.elementor-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;'
                ]
            ]
        );
        $widget->add_control(
            'elementskit_reset_button_text_padding',
            [
                'label' => __('Text Padding', 'elementskit'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .elementskit-reset-button.elementor-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
                ]
            ]
        );
        $widget->end_controls_tab();

        $widget->start_controls_tab(
            'elementskit_reset_button_hover',
            [
                'label' => __('Hover', 'elementskit')
            ]
        );
        $widget->add_control(
            'elementskit_reset_button_hover_background_color',
            [
                'label' => __('Background Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .elementskit-reset-button.elementor-button:hover' => 'background-color: {{VALUE}} !important;'
                ]
            ]
        );
        $widget->add_control(
            'elementskit_reset_button_hover_text_color',
            [
                'label' => __('Text Color', 'elementskit'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .elementskit-reset-button.elementor-button:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .elementskit-reset-button.elementor-button svg:hover' => 'fill: {{VALUE}};'
                ]
            ]
        );
        $widget->end_controls_tab();
        $widget->end_controls_tabs();
        $widget->end_controls_section();
    }

    public function render($field, $field_index, $form)
    {
        $form->add_render_attribute('input' . $field_index, 'reset-field-id', $field['custom_id']);
        $form->add_render_attribute('input' . $field_index, 'class', 'elementskit-reset-button');
        $form->add_render_attribute('input' . $field_index, 'class', 'elementor-button');
?>
        <input type="reset" <?php $form->print_render_attribute_string('input' . $field_index); ?>>
<?php
    }
}
