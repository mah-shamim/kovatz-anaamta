<?php

namespace ElementsKit\Modules\Conditional_Content\Libs\Controls;
use Elementor\Repeater;

require_once 'trait.php';

abstract class Repeater_Control_Base
{
    use Extra;

    protected $PREFIX;
    protected $NAMESPACE = 'ElementsKit\Modules\Conditional_Content\Libs\\Controls';

    public function __construct($PREFIX)
    {
        $this->PREFIX = $PREFIX;
    }

    abstract function get_control(Repeater $repeater, $condition);
}