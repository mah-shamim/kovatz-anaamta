<?php
/**
 * REST API
 */
class WCV_Rest_API {
    /**
     * Constructor
     */
    public function __construct() {
        $this->includes();
    }

    /**
     * Include any classes
     */
    public function includes() {
        include_once 'class-abstract-wcv-api.php';
        include_once 'admin/class-wcv-admin-api.php';
    }
}
new WCV_Rest_API();
