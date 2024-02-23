<?php
namespace ElementsKit\Modules\Conditional_Content\Libs\Controls;
use Elementor\Controls_Manager;
use Elementor\Element_Base;

class Condition_Relation extends Control_Base
{
    function get_control(Element_Base $element)
    {
        $element->add_control(
            $this->PREFIX . 'condition_relation',
            [
                'label' => __('Display on', 'elementskit'),
                'type' => Controls_Manager::SELECT,
                'default' => 'and',
                'options' => [
                    'and' => __('All Conditions Met', 'elementskit'),
                    'or' => __('Any Condition Met', 'elementskit'),
                ],
                'condition' => [
                    $this->PREFIX . 'condition_enable' => 'yes',
                ],
            ]
        );
    }
}