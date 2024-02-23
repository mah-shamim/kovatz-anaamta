<?php

/*
 * admin_notices action support for User Role Editor plugin
 *
 * Author: Vladimir Garagulya
 * Author email: support@role-editor.com
 * Author URI: https://role-editor.com
 */
 
class URE_Admin_Notice {
    
    // Message class: update, success, warning
    private $message_class;
    
    private $message;
    
    
    function __construct( $message_class, $message ) {
        
        $this->message = $message;
        $this->message_class = $message_class;
        
        add_action('admin_notices', array($this, 'render') );
    }
    // end of __construct()
    
    
    public function render() {
        
        printf('<div class="notice notice-%s is-dismissible"><p>%s</p></div>', $this->message_class, $this->message );
        
    }
    // end of render()
        
}
// end of class URE_Admin_Notice
