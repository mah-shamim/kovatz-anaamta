<?php
namespace ElementsKit\Modules\Conditional_Content\Libs;

use Elementor\Element_Base;
use Elementor\Repeater;

require_once plugin_dir_path(__FILE__) . '/base.php';
require_once plugin_dir_path(__FILE__) . '/conditions/condition-base.php';

class Control extends Base {

    /**
     * Instance
     *
     * @since 1.0.0
     *
     * @access private
     * @static
     *
     * @var  Control The single instance of the class.
     */
    private static $_instance = null;

    public static function instance() {

        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    /**
     * Add plugin actions
     *
     * @access public
     * @return void
     */
    public function init() {
        $this->add_elementor_actions();
    }

    protected function add_elementor_actions() {
        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'ekit_controls'], 1, 2);
        add_action('elementor/element/column/section_advanced/after_section_end', [$this, 'ekit_controls'], 1, 2);
        add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'ekit_controls'], 1, 2);
		
		// Flexbox Container support
		add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'ekit_controls' ) );
    }

    public function ekit_controls(Element_Base $element) {
        $repeater = new Repeater();
        $this->add_required_file('/controls/control-base.php')
            ->add_section($element)
            ->add_controls($element)
            ->end_section($element);
    }

}