<?php
namespace ElementsKit\Modules\Conditional_Content\Libs\Controls;
use Elementor\Element_Base;
use Elementor\Repeater;

class All_Conditions_List extends Control_Base
{
    function get_control(Element_Base $element)
    {
        $this->add_repeater_controls($element, new Repeater());
    }
}