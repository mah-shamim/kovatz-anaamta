<?php
namespace ElementsKit\Modules\Conditional_Content\Libs\Conditions;
class Date extends Condition_Base
{
    public function set_data($settings, $logical_operator, $config)
    {
        $date = strtotime($settings[$this->prefix . 'condition_date']);
        $today = $this->get_server_time('d-m-Y');
        $today = strtotime($today);
        $result = (($today >= $date));
        $this->result = $this->compare($result, true, $logical_operator);
    }


}