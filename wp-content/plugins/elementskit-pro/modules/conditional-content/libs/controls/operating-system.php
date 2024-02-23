<?php
namespace ElementsKit\Modules\Conditional_Content\Libs\Controls;
use Elementor\Repeater;

class Operating_System extends Repeater_Control_Base {

    function get_control(Repeater $repeater , $condition)
    {
        $repeater->add_control($this->PREFIX . 'condition_operating_system', [
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => [
                'mac_os' => __( 'Mac OS', 'elementskit' ),
                'linux' => __( 'Linux', 'elementskit' ),
                'ubuntu' => __( 'Ubuntu', 'elementskit' ),
                'iphone' => __( 'iPhone', 'elementskit' ),
                'android' => __( 'iPad', 'elementskit' ),
                'windows' => __( 'Windows', 'elementskit' ),
                'ipod' => __( 'iPod', 'elementskit' ),
                'ipad' => __( 'Android', 'elementskit' ),
                'blackberry' => __( 'BlackBerry', 'elementskit' ),
                'open_bsd' => __( 'OpenBSD', 'elementskit' ),
                'sun_os' => __( 'SunOS', 'elementskit' ),
                'safari' => __( 'Safari', 'elementskit' ),
                'qnx' => __( 'QNX', 'elementskit' ),
                'beos' => __( 'BeOS', 'elementskit' ),
                'os2' => __( 'OS/2', 'elementskit' ),
                'search_bot' => __( 'Search Bot', 'elementskit' ),
            ],
            'default' => __('mac_os', 'elementskit'),
            'label_block' => true,
            'condition' => [
                $this->PREFIX . 'conditions_list' => $condition
            ]
        ]);
    }
}