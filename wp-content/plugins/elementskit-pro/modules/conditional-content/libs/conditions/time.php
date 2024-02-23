<?php
namespace ElementsKit\Modules\Conditional_Content\Libs\Conditions;
class Time extends Condition_Base
{
    public function set_data($settings, $logical_operator, $config)
    {
        $time = strtotime($settings[$this->prefix . 'condition_time']);
        $local_time = $this->get_server_time('H:i');
        $local_time = strtotime($local_time);
        error_log('Local time ' . $local_time);
        error_log('Saved time ' . $time);
        $result = ($time <= $local_time);
        error_log($result);
        $this->result = $this->compare($result, true, $logical_operator);
    }
}