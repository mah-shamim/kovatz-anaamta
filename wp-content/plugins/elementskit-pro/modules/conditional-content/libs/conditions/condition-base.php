<?php
namespace ElementsKit\Modules\Conditional_Content\Libs\Conditions;

abstract class Condition_Base
{
    public $result;
    protected $prefix = 'ekit_';
    abstract public function set_data($settings, $logical_operator, $config);


    public function compare($left_value, $right_value, $operator)
    {
        switch ($operator) {
            case 'is':
                return $left_value == $right_value;
            case 'is_not':
                return $left_value != $right_value;
            default:
                return $left_value === $right_value;
        }
    }

    public function get_server_time($format = 'Y-m-d h:i:s A')
    {
        $today = date($format, strtotime("now") + (get_option('gmt_offset') * HOUR_IN_SECONDS));
        return $today;
    }
}