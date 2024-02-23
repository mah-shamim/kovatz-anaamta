<?php

namespace ElementsKit\Modules\Conditional_Content\Libs;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use ElementsKit\Modules\Conditional_Content\Libs\Controls\Extra;

require_once 'controls/trait.php';

class Base
{
    use Extra;
    protected $NAMESPACE = 'ElementsKit\Modules\Conditional_Content\Libs\\Controls';
    protected $PREFIX = 'ekit_';
    protected $default_control_list = [
        'condition_enable',
        'condition_relation',
        'all_conditions_list',
    ];

    /**
     * Get the control file path
     *
     * @access protected
     * @param $control
     * @return string
     */
    protected function get_control_file($control)
    {
        $control_file = str_replace('_', '-', $control);
        $control_file = plugin_dir_path(__FILE__) . '/controls/'. $control_file . '.php';
        return $control_file;
    }

    /**
     * Include the required file
     *
     * @access protected
     * @param $file
     * @return $this
     */
    protected function add_required_file($file)
    {
        require_once plugin_dir_path(__FILE__) . $file;
        return $this;
    }

    /**
     * Add control section to the @Elementor
     * @param Element_Base $element
     * @return $this
     */
    protected function add_section(Element_Base $element)
    {
        $element->start_controls_section(
            $this->PREFIX . 'condition_section',
            [
                'label' => __('ElementsKit Conditions', 'elementskit'),
                'tab' => Controls_Manager::TAB_ADVANCED
            ]
        );
        return $this;
    }

    /**
     * Close the section that has been started
     * to add section to the @elementor panel.
     *
     * @param Element_Base $element
     * @return $this
     */
    protected function end_section(Element_Base $element)
    {
        $element->end_controls_section();
        return $this;
    }

    /**
     * Add controls to the advance tab of @elementor
     *
     * @access protected
     *
     * @param Element_Base $element
     * @return $this
     */
    protected function add_controls(Element_Base $element)
    {
        foreach ($this->default_control_list as $control) {
            $control_file = $this->get_control_file($control);
            if (file_exists($control_file)) {
                include_once $control_file;
                $class_name = $this->get_class_name($control);
                if (class_exists($class_name))
                    (new $class_name($this->PREFIX))->get_control($element);
            }
        }
        return $this;
    }
}