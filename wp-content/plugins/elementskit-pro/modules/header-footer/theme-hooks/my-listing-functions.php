<?php

if(!function_exists( 'hfe_render_header' )){
    function hfe_render_header(){
        global $elementskit_template_ids;
        if($elementskit_template_ids[0] == null){
            return;
        }

        do_action('elementskit/template/before_header');
        echo '<div class="ekit-template-content-markup ekit-template-content-header">';
            echo \ElementsKit\Utils::render_elementor_content($elementskit_template_ids[0]);
        echo '</div>';
        do_action('elementskit/template/after_header');
    }
}

if(!function_exists( 'get_hfe_header_id' )){
    function get_hfe_header_id(){
        global $elementskit_template_ids;
        return $elementskit_template_ids[0];
    }
}

if(!function_exists( 'hfe_render_footer' )){
    function hfe_render_footer(){
        global $elementskit_template_ids;
        if($elementskit_template_ids[1] == null){
            return;
        }

        do_action('elementskit/template/before_header');
        echo '<div class="ekit-template-content-markup ekit-template-content-header">';
            echo \ElementsKit\Utils::render_elementor_content($elementskit_template_ids[1]);
        echo '</div>';
        do_action('elementskit/template/after_header');
    }
}

if(!function_exists( 'get_hfe_footer_id' )){
    function get_hfe_footer_id(){
        global $elementskit_template_ids;
        return $elementskit_template_ids[1];
    }
}
