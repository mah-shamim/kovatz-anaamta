<?php
namespace ElementsKit\Modules\Conditional_Content\Libs\Conditions;

class Login_Status extends Condition_Base {

    public function set_data($settings, $logical_operator, $config)
    {
        $this->result = $this->compare(is_user_logged_in(), true , $logical_operator);
    }
}