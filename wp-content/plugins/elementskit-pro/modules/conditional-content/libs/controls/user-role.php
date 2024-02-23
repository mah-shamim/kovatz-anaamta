<?php
namespace ElementsKit\Modules\Conditional_Content\Libs\Controls;
use Elementor\Repeater;

class User_Role extends Repeater_Control_Base {

    function get_control(Repeater $repeater, $condition)
    {
        $repeater->add_control($this->PREFIX . 'condition_user_role', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'administrator' => 'Administrator',
                'author' => 'Author',
                'editor' => 'Editor',
                'contributor' => 'Contributor',
                'subscriber' => 'Subscriber',
            ],
            'default' => __('subscriber', 'elementskit'),
            'label_block' => true,
            'condition' => [
                $this->PREFIX . 'conditions_list' => $condition
            ]
        ]);
    }
}