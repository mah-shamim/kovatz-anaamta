<?php

namespace ElementsKit\Modules\Conditional_Content\Libs\Controls;

use Elementor\Controls_Manager;
use Elementor\Repeater;

class Day extends Repeater_Control_Base
{

    function get_control(Repeater $repeater, $condition)
    {
        $repeater->add_control($this->PREFIX . 'condition_day', [
            'type' => Controls_Manager::SELECT,
            'default' => 'monday',
            'label_block' => true,
            'options' => [
                'monday' => __('Monday', 'elementskit'),
                'tuesday' => __('Tuesday', 'elementskit'),
                'wednesday' => __('Wednesday', 'elementskit'),
                'thursday' => __('Thursday', 'elementskit'),
                'friday' => __('Friday', 'elementskit'),
                'saturday' => __('Saturday', 'elementskit'),
                'sunday' => __('Sunday', 'elementskit'),
            ],
            'condition' => [
                $this->PREFIX . 'conditions_list' => $condition
            ]

        ]);
    }
}