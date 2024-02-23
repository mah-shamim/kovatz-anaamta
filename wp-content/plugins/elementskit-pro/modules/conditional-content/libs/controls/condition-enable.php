<?php
namespace ElementsKit\Modules\Conditional_Content\Libs\Controls;
use Elementor\Controls_Manager;
use Elementor\Element_Base;

class Condition_Enable extends Control_Base
{
    function get_control(Element_Base $element)
    {
        $element->add_control(
            $this->PREFIX . 'condition_enable',
            [
                'label' => __('Enable Condition', 'elementskit'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('On', 'elementskit'),
                'label_off' => __('Off', 'elementskit'),
                'return_value' => 'yes',
                'default' => '',
                'frontend_available' => true,
            ]
        );
    }
}