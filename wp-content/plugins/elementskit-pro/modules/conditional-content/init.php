<?php

namespace ElementsKit\Modules\Conditional_Content;

use Elementor\Element_Base;
use ElementsKit\Modules\Conditional_Content\Libs\Base;
use ElementsKit\Modules\Conditional_Content\Libs\Control;
use ElementsKit\Modules\Conditional_Content\Libs\Controls\Extra;

defined('ABSPATH') || exit;
require_once 'libs/controls/trait.php';

class Init extends Base
{
    use Extra;

    private $prefix = 'ekit_';

    public function __construct() {

        require_once 'libs/control.php';
        Control::instance()->init();
        $this->apply_elementor_filters();
    }

    public function apply_elementor_filters() {
        add_filter("elementor/frontend/widget/should_render", [$this, 'ekit_content_render'], 10, 2);
        add_filter("elementor/frontend/section/should_render", [$this, 'ekit_content_render'], 10, 2);
        add_filter("elementor/frontend/column/should_render", [$this, 'ekit_content_render'], 10, 2);

        // Flexbox Container support
        add_filter( 'elementor/frontend/container/should_render', [ $this, 'ekit_content_render' ], 10, 2 );
    }

    public function ekit_content_render($should_render, Element_Base $element) {
        $settings = $element->get_settings();

        if (isset($settings[$this->prefix . 'condition_enable'])) {
            if ('yes' === $settings[$this->prefix . 'condition_enable']) {
                $conditions = $settings[$this->prefix . 'all_conditions_list'];
                $relation = $settings[$this->prefix . 'condition_relation'];
                $results = [];
                foreach ($conditions as $condition) {
                    $results[] = $this
                        ->set_condition($condition)
                        ->set_settings($settings)
                        ->add_file()
                        ->create_class()
                        ->compare();
                }

                if ($relation == 'or') {
                    $should_render = false;
                    foreach ($results as $result) {
                        if ($result == true) {
                            $should_render = true;
                        }
                    }
                }

                if ($relation == 'and') {
                    foreach ($results as $result) {
                        if ($result == false) {
                            $should_render = false;
                        }
                    }
                }
            }

        }

        return $should_render;
    }
}