<?php

namespace ElementsKit\Modules\Conditional_Content\Libs\Controls;

use Elementor\Repeater;

class Login_Status extends Repeater_Control_Base
{
    function get_control(Repeater $repeater, $condition)
    {
        $repeater->add_control($this->PREFIX . 'condition_login_status', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'logged' => 'Logged in',
            ],
            'default' => __('logged', 'elementskit'),
            'label_block' => true,
            'condition' => [
                $this->PREFIX . 'conditions_list' => $condition
            ]
        ]);
    }
}