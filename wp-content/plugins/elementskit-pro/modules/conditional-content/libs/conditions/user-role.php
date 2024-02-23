<?php

namespace ElementsKit\Modules\Conditional_Content\Libs\Conditions;
class User_Role extends Condition_Base
{
    public function set_data($settings, $logical_operator, $config)
    {
        $current_user = wp_get_current_user();
        $this->result = $this->compare(
            (is_user_logged_in() && in_array(
                    $settings[$this->prefix . 'condition_user_role'], $current_user->roles
                )), true, $logical_operator
        );

    }
}