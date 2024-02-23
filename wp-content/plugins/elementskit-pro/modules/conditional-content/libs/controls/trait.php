<?php

namespace ElementsKit\Modules\Conditional_Content\Libs\Controls;

trait Extra
{

    protected function get_control_file($control, $condition = false)
    {
        $file = str_replace('_', '-', $control);
        if ($condition) {
            $file = str_replace('controls', 'conditions', plugin_dir_path(__FILE__) . $file . '.php');
        } else {
            $file = plugin_dir_path(__FILE__) . $file . '.php';

        }
        return $file;
    }

    protected function get_class_name($control, $namespace = 'ElementsKit\Modules\Conditional_Content\Libs\\Controls')
    {
        $class_name = str_replace('_', ' ', $control);
        $class_name = str_replace(' ', '_', ucwords($class_name));
        $class_name = $namespace . '\\' . $class_name;
        return $class_name;
    }

    protected $condition_file_link;

    protected function add_file()
    {
        $this->condition_file_link = $this
            ->get_control_file(
                $this->config['condition_name'], true
            );
        return $this;
    }

    protected $class_name;
    protected $config;
    protected $relation;
    protected $settings;
    protected $condition_data;

    protected function create_class()
    {
        require_once $this->condition_file_link;

        $class_name = $this->get_class_name($this->config['condition_name'], 'ElementsKit\Modules\Conditional_Content\Libs\\Conditions');
        $this->class_name = new $class_name($this->prefix) ;
        return $this;
    }

    protected function set_condition($condition)
    {
        $this->config['data'] = $condition;
        $this->config['operator'] = $condition[$this->prefix . 'condition_operator'];
        $this->config['condition_key'] = $condition[$this->prefix . 'conditions_list'];
        $this->config['condition_name'] = str_replace($this->prefix, '', $this->config['condition_key']);
        $this->config['file_path'] = $this->get_control_file($this->config['condition_name']);
        $this->condition_data = $condition;
        return $this;
    }

    protected function set_settings($settings)
    {
        $this->relation = $settings[$this->prefix . 'condition_relation'];
        $this->settings = $settings;
        return $this;
    }

    protected function compare()
    {

        ($this->class_name)->set_data($this->condition_data,$this->config['operator'],$this->config);
        return ($this->class_name)->result;
    }


}