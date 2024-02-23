<?php

namespace ElementsKit\Modules\Conditional_Content\Libs\Conditions;
class Day extends Condition_Base
{
    public function set_data($settings, $logical_operator, $config)
    {
        $today = $this->get_server_time('l');
        $this->result = $this->compare(strtolower($today), $settings[$this->prefix . 'condition_day'], $logical_operator);
    }
}