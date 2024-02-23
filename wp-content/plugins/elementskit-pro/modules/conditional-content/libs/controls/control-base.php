<?php

namespace ElementsKit\Modules\Conditional_Content\Libs\Controls;

use Elementor\Element_Base;
use Elementor\Repeater;

require_once 'trait.php';

abstract class Control_Base
{
    use Extra;

    protected $PREFIX;
    protected $NAMESPACE = 'ElementsKit\Modules\Conditional_Content\Libs\\Controls';
    protected $conditional_controls_list = [
        'login_status' => 'Login Status',
        'user_role' => 'User Role',
        'operating_system' => 'Operating System',
        'browser' => 'Browser',
        'date' => 'Date',
        'day' => 'Day',
        'time' => 'Time',
    ];

    public function __construct($PREFIX)
    {
        $this->PREFIX = $PREFIX;
    }

    abstract function get_control(Element_Base $element);

    protected function add_repeater_controls(Element_Base $element, Repeater $repeater)
    {
        require_once 'repeater-control-base.php';
        $this
            ->add_condition_list($repeater)
            ->add_conditional_operator($repeater)
            ->add_controls_for_repeater($repeater)
            ->add_repeater($element, $repeater);
    }

    protected function add_condition_list(Repeater $repeater)
    {
        $repeater->add_control(
            $this->PREFIX . 'conditions_list', [
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'login_status',
                'options' => $this->conditional_controls_list,
                'label_block' => true,
            ]
        );
        return $this;
    }

    protected function add_conditional_operator(Repeater $repeater)
    {
        $repeater->add_control($this->PREFIX . 'condition_operator', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'default' => 'is',
            'options' => [
                'is' => 'Is',
                'is_not' => 'Is Not',
            ],
            'label_block' => true,
        ]);
        return $this;
    }

    protected function add_controls_for_repeater(Repeater $repeater)
    {
        foreach ($this->conditional_controls_list as $control_name => $control_label) {

            $control_file = $this
                ->get_control_file($control_name);

            if (file_exists($control_file)) {

                include_once $control_file;

                $class_name = $this
                    ->get_class_name($control_name);
                if (class_exists($class_name))
                    (new $class_name($this->PREFIX))->get_control($repeater, $control_name);

            }
        }
        return $this;
    }

    protected function add_repeater(Element_Base $element, Repeater $repeater)
    {
        $element->add_control(
            $this->PREFIX . 'all_conditions_list',
            [
                'label' => __('Conditions', 'elementskit'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ ekit_conditions_list.replace(/_/i, " ").split(" ").map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(" ") }}}',
                'condition' => [
                    $this->PREFIX . 'condition_enable' => 'yes',
                ],
            ]
        );
        return $this;
    }
}