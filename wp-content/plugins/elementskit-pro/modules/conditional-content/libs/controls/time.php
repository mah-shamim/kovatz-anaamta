<?php

namespace ElementsKit\Modules\Conditional_Content\Libs\Controls;

use Elementor\Controls_Manager;
use Elementor\Repeater;

class Time extends Repeater_Control_Base
{
    function get_control(Repeater $repeater, $condition)
    {
        $repeater->add_control($this->PREFIX . 'condition_time', [
            'type' => Controls_Manager::DATE_TIME,
            'default' => '12:00',
            'label_block' => true,
            'picker_options' => [
                'noCalendar' => true,
                'enableTime' => true,
                'dateFormat' => "H:i",
            ],
            'condition' => [
                $this->PREFIX . 'conditions_list' => $condition
            ]

        ]);
    }
}