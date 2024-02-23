<?php

namespace ElementsKit\Modules\Conditional_Content\Libs\Controls;

use Elementor\Repeater;

class Browser extends Repeater_Control_Base
{
    function get_control(Repeater $repeater, $condition)
    {
        $repeater->add_control($this->PREFIX . 'condition_browser', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'chrome',
            'label_block' => true,
            'options' => [
                'opera' => __('Opera', 'elementskit'),
                'edge' => __('Edge', 'elementskit'),
                'chrome' => __('Google Chrome', 'elementskit'),
                'safari' => __('Safari', 'elementskit'),
                'firefox' => __('Mozilla Firefox', 'elementskit'),
                'ie' => __('Internet Explorer', 'elementskit'),
                'others' => __('Others', 'elementskit'),
            ],
            'condition' => [
                $this->PREFIX . 'conditions_list' => $condition
            ]

        ]);
    }
}