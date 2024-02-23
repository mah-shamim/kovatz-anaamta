<?php
/**
 * Role capabilities editor class
 *
 * @package    User-Role-Editor
 * @subpackage Editor
 * @author     Vladimir Garagulya <support@role-editor.com>
 * @copyright  Copyright (c) 2010 - 2021, Vladimir Garagulia
 **/
class URE_Editor {

    private static $instance = null;
    
    protected $lib = null;

    protected $role_additional_options = null;    
    protected $apply_to_all = 0;
    protected $capabilities_to_save = null;
    protected $caps_columns_quant = 1;
    protected $caps_readable = false;
    protected $current_role = '';
    protected $current_role_name = '';
    protected $full_capabilities = false;
    protected $notification = '';   // notification message to show on page    
    protected $roles = null;
    protected $show_deprecated_caps = false;    
    protected $ure_object = 'role';  // what to process, 'role' or 'user'      
    protected $user_to_edit = null;
    protected $wp_default_role = '';
    
    
    public static function get_instance() {
        
        if (self::$instance === null) {            
            // new static() will work too
            self::$instance = new URE_Editor();
        }

        return self::$instance;
    }
    // end of get_instance()
    
    
    private function __construct() {
        
        $this->lib = URE_Lib::get_instance();
    }
    // end of __construct()
    
    
    public function get($property_name) {
        
        if (!property_exists($this, $property_name)) {
            syslog(LOG_ERR, 'URE_Editor class does not have such property '. $property_name);
            return null;
        }
        
        return $this->$property_name;
    }
    // end of get_property()

    
    public function get_edit_user_caps_mode() {
        
        $multisite = $this->lib->get('multisite');
        if ($multisite && $this->lib->is_super_admin()) {
            return 1;
        }
        
        $edit_user_caps = $this->lib->get_option('edit_user_caps', 1);
        
        return $edit_user_caps;
    }
    // end of get_edit_user_caps_mode()
    
    
    
    // Validate information about user we intend to edit
    protected function check_user_to_edit() {

        if ( $this->ure_object !=='user' ) {
            return true;
        }
        
        if ( isset( $_REQUEST['user_id'] ) ) {
            $user_id = filter_var( $_REQUEST['user_id'], FILTER_VALIDATE_INT );
        } elseif ( isset( $_POST['values']['user_id'] ) ) {
            $user_id = filter_var( $_POST['values']['user_id'], FILTER_VALIDATE_INT );
        } else {
            return false;    // user_id value is missed
        }
        if ( empty( $user_id ) ) {
            return false;
        }

        $this->user_to_edit = get_user_to_edit( $user_id );
        if ( empty( $this->user_to_edit ) ) {
            return false;
        }
                            
        return true;
    }
    // end of check_user_to_edit()
    
    
    protected function get_caps_columns_quant() {
        
        if ( isset( $_POST['caps_columns_quant'] ) && in_array( $_POST['caps_columns_quant'], array(1,2,3) ) ) {
            $value = (int) filter_var( $_POST['caps_columns_quant'], FILTER_VALIDATE_INT );
            set_site_transient( 'ure_caps_columns_quant', $value, URE_Lib::TRANSIENT_EXPIRATION );
        } else {
            $value = get_site_transient( 'ure_caps_columns_quant' );
            if ( $value===false ) {
                $value = $this->lib->get_option( 'caps_columns_quant', 1 );
            }
        }
        
        return $value;        
    }
    // end of get_caps_columns_quant()

    
    protected function _init0() {
        // could be sent as via POST, as via GET
        if ( isset( $_REQUEST['object'] ) ) {
            $this->ure_object = $_REQUEST['object'];
        } elseif ( isset( $_POST['values']['object'] ) ) {  // AJAX POST
            $this->ure_object = $_POST['values']['object'];
        } else {
            $this->ure_object = 'role';
        }
        
        if ( $this->ure_object=='user') {
            if ( !$this->check_user_to_edit() ) {
                return false;
            }
        }
        
        $this->apply_to_all = isset( $_POST['values']['ure_apply_to_all']) ? true : false;
        if ( empty( $this->apply_to_all ) && isset( $_POST['ure_apply_to_all'] ) ) {
            $this->apply_to_all = true;
        }
        
        return true;
    }
    // end of _init0()
        
    
    protected function init0() {
        
        if ( !$this->_init0() ) {
            return false;
        }

        $this->caps_readable = get_site_transient( 'ure_caps_readable' );
        if ( false === $this->caps_readable ) {
            $this->caps_readable = $this->lib->get_option( 'ure_caps_readable' );
            set_site_transient( 'ure_caps_readable', $this->caps_readable, URE_Lib::TRANSIENT_EXPIRATION );
        }
        $this->show_deprecated_caps = get_site_transient( 'ure_show_deprecated_caps' );
        if ( false === $this->show_deprecated_caps ) {
            $this->show_deprecated_caps = $this->lib->get_option( 'ure_show_deprecated_caps' );
            set_site_transient( 'ure_show_deprecated_caps', $this->show_deprecated_caps, URE_Lib::TRANSIENT_EXPIRATION );
        }

        $this->wp_default_role = get_option( 'default_role' );        
        $this->caps_columns_quant = $this->get_caps_columns_quant();
        
        return true;
    }
    // end of init0() 


    protected function valid_nonce() {
        
        if ( empty( $_POST['ure_nonce'] ) || !wp_verify_nonce( $_POST['ure_nonce'], 'user-role-editor' ) ) {
            echo '<h3>Wrong or older request (invalid nonce value). Action prohibited.</h3>';
            return false;
        }
        
        return true;
    }
    // end of check_nonce()
    
    
    protected function set_caps_readable() {
        
        if ($this->caps_readable) {
            $this->caps_readable = 0;					
        } else {
            $this->caps_readable = 1;
        }
        set_site_transient( 'ure_caps_readable', $this->caps_readable, URE_Lib::TRANSIENT_EXPIRATION );        
        
    }
    // end of caps_readable()
    
    
    protected function set_show_deprecated_caps() {
        if ($this->show_deprecated_caps) {
            $this->show_deprecated_caps = 0;
        } else {
            $this->show_deprecated_caps = 1;
        }
        set_site_transient( 'ure_show_deprecated_caps', $this->show_deprecated_caps, URE_Lib::TRANSIENT_EXPIRATION );
    }
    // end of set_show_deprecated_caps()
            
    
    private function get_role_id() {
        
        if ( isset( $_POST['values']['user_role'] ) ) {
            $role_id = $_POST['values']['user_role'];
        } elseif ( isset( $_POST['user_role'] ) ) {
            $role_id = $_POST['user_role'];
        } else {
            $role_id = false;
        }
        
        return $role_id;
        
    }
    // end of get_role_id()
    
    
    public function init_current_role_name() {

        $role_id = $this->get_role_id();
        $this->current_role = '';
        $this->current_role_name = '';
        if ( empty( $role_id ) ) {
            $mess = esc_html__('Error: ', 'user-role-editor') . esc_html__('Wrong request!', 'user-role-editor');
        } else if ( !isset( $this->roles[$role_id] ) ) {
            $mess = esc_html__('Error: ', 'user-role-editor') . esc_html__('Role', 'user-role-editor') . ' <em>' . esc_html( $role_id ) . '</em> ' . 
                    esc_html__('does not exist', 'user-role-editor');
        } else {
            $this->current_role = $role_id;
            $roles = $this->lib->get_user_roles();
            $this->current_role_name = $roles[$this->current_role]['name'];
            $mess = '';
        }
        
        return $mess;        
    }
    // end of init_current_role_name()
    
    
    // Add existing WPBakery Visial Composer () plugin capabilities from this role to the list of capabilities for save with this role update -
    // Visual Composer capabilities are excluded from a role update as they may store not boolean values.
    protected function restore_visual_composer_caps() {
        
        if ( !isset( $this->roles[$this->current_role] ) || 
             !is_array( $this->roles[$this->current_role]['capabilities'] ) ) {
            return false;
        }
        
        foreach($this->roles[$this->current_role]['capabilities'] as $cap=>$value) {
            if (strpos($cap, 'vc_access_rules_')!==false) {
                $this->capabilities_to_save[$cap] = $value;
            }
        }
        
        return true;
    }
    // end of restore_visual_composer_caps()
    

    /**
     *  prepare capabilities from user input to save at the database
     */
    protected function prepare_capabilities_to_save() {
        
        $this->capabilities_to_save = array();
        if (empty($this->full_capabilities)) {
            return; // There is no valid initialization
        }
                
        foreach ( $this->full_capabilities as $cap ) {
            $cap_id_esc = URE_Capability::escape( $cap['inner'] );
            if ( isset( $_POST['values'][$cap_id_esc] ) || isset( $_POST[$cap_id_esc] ) ) {
                $this->capabilities_to_save[ $cap['inner'] ] = true;
            }
        }
        
        $this->restore_visual_composer_caps();
    }
    // end of prepare_capabilities_to_save()

    
    /**
     * Make full synchronization of roles for all sites with roles from the main site directly updating database records
     * 
     * @return boolean
     */
    protected function is_full_network_synch() {
        
        /* is_network_admin() returns wrong result for the AJAX calls from the network admin
         * so have to use this way
         */
        $network_admin = $this->lib->get_request_var('network_admin', 'post', 'int');
        if ( $network_admin==1 ) {   // for Pro version only
            $result = true;
        } else {
            $result = defined('URE_MULTISITE_DIRECT_UPDATE') && URE_MULTISITE_DIRECT_UPDATE == 1;
        }
        
        return $result;
    }
    // end of is_full_network_synch()    
    

    protected function last_check_before_update() {        
        
        if ( empty($this->roles ) || !is_array( $this->roles ) || count( $this->roles )===0 ) { 
            // Nothing to save - something goes wrong - stop execution...            
            return false;
        }
        
        $key_capability = URE_Own_Capabilities::get_key_capability();
        if ( current_user_can( $key_capability ) ) {    
            // current user is an URE admin
            return true;
        }
        
        if ( !current_user_can( 'ure_edit_roles' ) ) {
            // Not enough permissions
            return false;
        }
        
        $current_user = wp_get_current_user();
        if ( in_array( $this->current_role, $current_user->roles ) ) {
            // Do not allow to non-admin user without full access to URE update his own role
            return false;
        }
        
        return true;
    }
    // end of last_check_before_update()
            

    /**
     * Return true if $capability is included to the list of capabilities allowed for the single site administrator
     * @param string $capability - capability ID
     * @param boolean $ignore_super_admin - if 
     * @return boolean
     */
    public function block_cap_for_single_admin($capability, $ignore_super_admin=false) {
        
        if (!$this->lib->is_pro()) {    
            // this functionality is for the Pro version only.
            return false;
        }        
        $multisite = $this->lib->get('multisite');
        if ( !$multisite ) {    // work for multisite only
            return false;
        }
        if ( !$ignore_super_admin && $this->lib->is_super_admin() ) { 
            // Do not block superadmin
            return false;
        }
        $caps_access_restrict_for_simple_admin = $this->lib->get_option( 'caps_access_restrict_for_simple_admin', 0 );
        if ( !$caps_access_restrict_for_simple_admin ) {
            return false;
        }
        
        $allowed_caps = $this->lib->get_option( 'caps_allowed_for_single_admin', array() );
        if (in_array( $capability, $allowed_caps ) ) {
            $block_this_cap = false;
        } else {
            $block_this_cap = true;
        }
        
        return $block_this_cap;
    }
    // end of block_cap_for_single_admin()


    /**
     * Returns array without capabilities blocked for single site administrators
     * @param array $capabilities
     * @return array
     */
    protected function remove_caps_not_allowed_for_single_admin( $capabilities ) {
        if (!$this->lib->is_pro()) {    
            // this functionality is for the Pro version only.
            return $capabilities;
        }
        
        foreach( array_keys( $capabilities ) as $cap ) {
            if ( $this->block_cap_for_single_admin( $cap ) ) {
                unset( $capabilities[$cap] );
            }
        }
        
        return $capabilities;
    }
    // end of remove_caps_not_allowed_for_single_admin()

    
    protected function role_contains_caps_not_allowed_for_simple_admin( $role_id ) {
        
        $result = false;
        if (!$this->lib->is_pro()) {    
            // this functionality is for the Pro version only.
            return $result;
        }

        if ( !isset( $this->roles[$role_id]) ) {
            return false;
        }
        
        $role = $this->roles[$role_id];
        if ( !is_array( $role['capabilities'] ) ) {
            return false;
        }
        foreach ( array_keys( $role['capabilities'] ) as $cap ) {
            if ( $this->block_cap_for_single_admin( $cap ) ) {
                $result = true;
                break;
            }
        }
        
        return $result;
    } 
    // end of role_contains_caps_not_allowed_for_simple_admin()                
    
    
    // Save Roles to database
    protected function save_roles() {
        global $wpdb;

        if ( !$this->last_check_before_update() ) {
            return false;
        }
        
        if ( !isset($this->roles[$this->current_role]) ) {
            return false;
        }
        
        $this->capabilities_to_save = $this->remove_caps_not_allowed_for_single_admin( $this->capabilities_to_save );                
        $this->roles[$this->current_role]['name'] = $this->current_role_name;
        $this->roles[$this->current_role]['capabilities'] = $this->capabilities_to_save;
        $option_name = $wpdb->prefix . 'user_roles';

        update_option($option_name, $this->roles);

        // save additional options for the current role
        if (empty($this->role_additional_options)) {
            $this->role_additional_options = URE_Role_Additional_Options::get_instance($this->lib);
        }
        $this->role_additional_options->save($this->current_role);
        
        return true;
    }
    // end of save_roles()    


    protected function leave_roles_for_blog( $blog_id, $leave_roles ) {
        global $wpdb;
        
        $prefix = $wpdb->get_blog_prefix( $blog_id );
        $options_table_name = $prefix . 'options';
        $option_name = $prefix . 'user_roles';
        
        $query = "SELECT option_value
                    FROM {$options_table_name}
                    WHERE option_name='$option_name'
                    LIMIT 1";
        $value = $wpdb->get_var( $query );
        if ( empty( $value ) ) {
            return $this->roles;
        }
        $blog_roles = unserialize( $value );
        
        $roles = $this->roles;
        foreach( $leave_roles as $role_id ) {
            $roles[$role_id] = $blog_roles[$role_id];
        }
        $serialized_roles = serialize( $roles );
        
        return $serialized_roles;
    }
    // end of leave_roles_for_blog()
    
    
    /**
     * Update roles for all network using direct database access - quicker in several times
     * Execution speed is critical for large multi-site networks.
     * @global wpdb $wpdb
     * @return boolean
     */
    protected function direct_network_roles_update() {
        global $wpdb;

        $multisite = $this->lib->get( 'multisite' );
        if (!$multisite) {
            return false;
        }

        if ( !$this->last_check_before_update() ) {
            return false;
        }
        
        if ( !empty( $this->current_role ) ) {
            $this->roles[$this->current_role]['name'] = $this->current_role_name;
            $this->roles[$this->current_role]['capabilities'] = $this->capabilities_to_save;
        }

        $serialized_roles = serialize( $this->roles );
        $blog_ids = $this->lib->get_blog_ids();
        /*
         * Roles to leave unchanged during network update which overwrites all roles by default
         * $leave_roles = array( blog_id => array('role_id1', 'role_id2', ... );
         */
        $leave_roles = apply_filters('ure_network_update_leave_roles', array(), 10, 1 );
        if ( empty( $leave_roles ) || !is_array( $leave_roles ) ) {
            $leave_roles = array();
        }
        foreach ( $blog_ids as $blog_id ) {
            $prefix = $wpdb->get_blog_prefix( $blog_id );
            $options_table_name = $prefix . 'options';
            $option_name = $prefix . 'user_roles';
            if ( !isset( $leave_roles[$blog_id] ) ) {
                // overwrites all roles
                $roles = $serialized_roles;
            } else {
                // overwrite except roles you have to leave untouched for this blog
                $roles = $this->leave_roles_for_blog( $blog_id, $leave_roles[$blog_id] );
            }
            $query = "UPDATE {$options_table_name}
                SET option_value='$roles'
                WHERE option_name='$option_name'
                LIMIT 1";
            $wpdb->query( $query );
            if ( $wpdb->last_error ) {
                return false;
            }
            // @TODO: save role additional options     
                        
        }
        
        do_action( 'ure_direct_network_roles_update' );
        
        return true;
    }
    // end of direct_network_roles_update()        
    
    
    protected function wp_api_network_roles_update() {
        global $wpdb;
                
        $old_blog = $wpdb->blogid;
        $blog_ids = $this->lib->get_blog_ids();
        if (empty( $blog_ids ) ) {
            return false;
        }
        
        $result = true;
        foreach ( $blog_ids as $blog_id ) {
            switch_to_blog( $blog_id );
            $this->roles = $this->lib->get_user_roles();
            if ( !isset( $this->roles[$this->current_role] ) ) { // add new role to this blog
                $this->roles[$this->current_role] = array('name' => $this->current_role_name, 'capabilities' => array('read' => true));
            }
            if ( !$this->save_roles() ) {
                $result = false;
                break;
            }
        }
        $this->lib->restore_after_blog_switching( $old_blog );
        $this->roles = $this->lib->get_user_roles();
        
        return $result;
    }
    // end of wp_api_network_roles_update()    
    
    
    /**
     * Update role for all network using WordPress API
     * 
     * @return boolean
     */
    protected function multisite_update_roles() {
        
        $multisite = $this->lib->get('multisite');
        if (!$multisite) {
            return false;
        }
        
        $debug = $this->lib->get('debug');
        if ( $debug ) {
            $time_shot = microtime();
        }
        
        if ( $this->is_full_network_synch() ) {
            $result = $this->direct_network_roles_update();
        } else {
            $result = $this->wp_api_network_roles_update();            
        }
        
        // Use this action to hook a code to execute after roles were updated at all subsites of the multisite network
        do_action('ure_after_network_roles_update');

        if ($debug) {
            echo '<div class="updated fade below-h2">Roles updated for ' . ( microtime() - $time_shot ) . ' milliseconds</div>';
        }

        return $result;
    }
    // end of multisite_update_roles()    
    
    
    /**
     * Process user request on update roles
     * 
     * @global WP_Roles $wp_roles
     * @return boolean
     */
    protected function update_roles() {
        global $wp_roles;
        
        $multisite = $this->lib->get( 'multisite' );
        if ( $multisite && $this->lib->is_super_admin() && $this->apply_to_all ) {  
            // update Role for all blogs/sites in the network (permitted to superadmin only)
            if (!$this->multisite_update_roles()) {
                return false;
            }
        } else {
            if (!$this->save_roles()) {
                return false;
            }
        }

        // refresh global $wp_roles
        $wp_roles = new WP_Roles();
        
        return true;
    }
    // end of update_roles()    

   
    /**
     * if existing user was not added to the current blog - add him
     * @global type $blog_id
     * @param type $user
     * @return bool
     */
    protected function check_blog_user( $user ) {
        global $blog_id;
        
        $result = true;
        if ( is_network_admin() ) {
            if (!array_key_exists( $blog_id, get_blogs_of_user( $user->ID ) ) ) {
                $result = add_existing_user_to_blog( array( 'user_id' => $user->ID, 'role' => 'subscriber' ) );
            }
        }

        return $result;
    }
    // end of check_blog_user()

    
    private function get_user_primary_role( $user ) {
        
        $role = ( is_array( $user->roles ) && count( $user->roles )>0 ) ? $user->roles[0] : '';
        
        return $role;
    }
    
    
    private function get_new_primary_role( $user ) {
        $wp_roles = wp_roles();
        
        $select_primary_role = apply_filters( 'ure_users_select_primary_role', true );
        if ( $select_primary_role  || $this->lib->is_super_admin()) {
            $role = isset( $_POST['values']['primary_role'] ) ? URE_Base_Lib::filter_string_var( $_POST['values']['primary_role'] ) : false;  
            if ( empty( $role ) || !isset( $wp_roles->roles[$role] ) ) {
                $role = '';
            }
        } else {
            $role = $this->get_user_primary_role( $user );
        }
        
        return $role;
    }
    // end of get_new_primary_role()
    
    
    private function get_bbpress_role( $user ) {
        
        $bbpress = $this->lib->get_bbpress();        
        if ( $bbpress->is_active() ) {
            $role = bbp_get_user_role( $user->ID );
        } else {
            $role = '';
        }
        
        return $role;
    }
    // end of get_bbpress_role()
    
    /**
     * Add other roles to roles array for this user, extracting selected values from the POST
     * 
     * @param array $roles
     */
    private function add_other_roles( $roles ) {
        
        $wp_roles = wp_roles();
        
        $post_values = isset( $_POST['values'] ) && is_array( $_POST['values'] ) ? $_POST['values'] : array();
        foreach ( $post_values as $key => $value ) {
            $result = preg_match( '/^wp_role_(.+)/', $key, $match );
            if ( $result !== 1 ) {
                continue;
            }
            $role = $match[1];
            if ( !isset( $wp_roles->roles[$role] ) ) {
                // Skip role if it does not exist
                continue;
            }
            if ( !in_array( $role, $roles ) ) {
                $roles[] = $role;
            }
        }

        return $roles;
    }
    // end of add_other_roles()
    
    
    /**
     * Provide a valid placement of a primary role - 1st element of roles array
     * 
     * @param WP_User $user
     * @param array $new_roles
     */    
    private function set_primary_role( $user, $new_primary_role ) {

        
        if ( count( $user->roles )<=1 ) {
            // User does not have roles at all or has only single one
            return;
        }
                        
        $current_primary_role = reset( $user->roles );
        if ( $current_primary_role === $new_primary_role ) {
            // Current primary role is equal to a new one - nothing was changed
            return;
        }                
        
        // remove primary role from user capabilities array
        unset( $user->caps[$new_primary_role] );    
        // insert new primary role as the 1st elemnt of user capabilities array
        $user->caps = array($new_primary_role=>true) + $user->caps;
        
        // update user permissions ar WordPress internal data structures - exactly the same way as WordPress itself does at WP_User::add_role()
        update_user_meta( $user->ID, $user->cap_key, $user->caps );
	$user->get_role_caps();
        $user->update_user_level_from_caps();
        
    }
    // end of set_primary_role()
    
    
    private function update_user_roles( $user, $new_roles ) {
        
        foreach( $user->roles as $role ) {
            if ( !in_array( $role, $new_roles ) ) {
                $user->remove_role( $role );
            }
        }
        
        foreach( $new_roles as $role ) {
            if ( !in_array( $role, $user->roles ) ) {
                $user->add_role( $role );
            }
        }               
        
        if ( !empty( $new_roles ) ) {
            $this->set_primary_role( $user, $new_roles[0] );
        }
                
    }
    // end of update_user_roles()

    /**
     * Remove from user directly granted capabilities
     * 
     * @param WP_User $user 
     */
    private function remove_user_capabilities( $user, $caps_to_save ) {

        if ( empty( $user->caps ) ) {
            return;
        }
        
        $roles = wp_roles()->roles;
        $roles_id = array_keys( $roles );
        $caps = array_keys( $user->caps );
        foreach( $caps as $cap ) {
            if ( in_array( $cap, $roles_id ) ) {
                // It's a role ID, skip it
                continue;
            }            
            if ( !in_array( $cap, $caps_to_save ) ) {                
                $user->remove_cap( $cap );
            }
        }
        
    }
    // end of remove_user_capabilities()
    
    
    /**
     * Update individual capabilities of the user
     * 
     * @param WP_User $user
     */
    private function update_user_capabilities( $user ) {
        // $edit_user_caps = $this->get_edit_user_caps_mode();
        
        $caps_to_save = array_keys( $this->capabilities_to_save );        
        $this->remove_user_capabilities( $user, $caps_to_save );               
        $user_caps = array_keys( $user->caps );
        
        // process new added capabilities
        foreach ($caps_to_save as $cap ) {
            if ( !in_array( $cap, $user_caps ) ) {
                $user->add_cap( $cap );
            }
        }

    } 
    // end of update_user_capabilities()
    
    
    /**
     * Update user roles and capabilities
     * 
     * @global WP_Roles $wp_roles
     * @param WP_User $user
     * @return boolean
     */
    protected function update_user( $user ) {
           
        if ( !is_a( $user, 'WP_User') ) {
            return false;
        }
        
        do_action( 'ure_before_user_permissions_update', $user->ID );
                
        $multisite = $this->lib->get('multisite');
        if ($multisite) {
            if ( !$this->check_blog_user( $user ) ) {
                return false;
            }
        }
        
        $new_primary_role = $this->get_new_primary_role( $user );
        $bbp_role = $this->get_bbpress_role( $user );                                        
        // Build new roles array for the user
        $new_roles = array();
        
        // restore primary role
        if ( !empty( $new_primary_role ) ) {
            $new_roles[] = $new_primary_role;
        }
        
        // restore bbPress user role if user had one
        if ( !empty( $bbp_role ) ) {
            $new_roles[] = $bbp_role;
        }
        
        $new_roles = $this->add_other_roles( $new_roles );
        $this->update_user_roles( $user, $new_roles );        
        $this->update_user_capabilities( $user );                
        
        do_action('ure_user_permissions_update', $user->ID, $user);  // In order other plugins may hook to the user permissions update
                        
        return true;
    }
    // end of update_user()
        
    
    /**
     *  Save changes to the roles or user
     *  @param string $mess - notification message to the user
     *  @return string - notification message to the user
     */
    protected function permissions_object_update( $mess ) {

        if ( !empty( $mess ) ) {
            $mess .= '<br/>';
        }                
        
        if ( $this->ure_object === 'role' ) {  // save role changes to database
            if ($this->update_roles()) {                
                if (!$this->apply_to_all) {
                    $mess = esc_html__('Role is updated successfully', 'user-role-editor');
                } else {
                    $mess = esc_html__('Roles are updated for all network', 'user-role-editor');
                }
            } else {
                $mess = esc_html__('Error occurred during role(s) update', 'user-role-editor');
            }
        } else {
            if ($this->update_user($this->user_to_edit)) {
                $mess = esc_html__('User capabilities are updated successfully', 'user-role-editor');
            } else {
                $mess = esc_html__('Error occurred during user update', 'user-role-editor');
            }
        }
        
        return $mess;
    }
    // end of permissions_object_update()
    
    
    /**
     * Return WordPress user roles to its initial state, just like after installation
     * @global WP_Roles $wp_roles
     */
    protected function wp_roles_reinit() {
        global $wp_roles, $wp_user_roles;
        
        $wp_user_roles = null;
        $wp_roles->roles = array();
        $wp_roles->role_objects = array();
        $wp_roles->role_names = array();
        $wp_roles->use_db = true;

        require_once(ABSPATH . '/wp-admin/includes/schema.php');
        populate_roles();
        $wp_roles = new WP_Roles();
        
        $this->roles = $this->lib->get_user_roles();
        
    }
    // end of wp_roles_reinit()
    
    /**
     * Reset user roles to WordPress default roles
     */
    public function reset_user_roles() {
        
        if (!current_user_can('ure_reset_roles')) {            
            esc_html_e('Insufficient permissions to work with User Role Editor','user-role-editor');
            $debug = ( defined('WP_PHP_UNIT_TEST') && WP_PHP_UNIT_TEST==true );
            if ( !$debug ) {
                die;
            } else {
                return false;
            }
        }
              
        $this->wp_roles_reinit();
        URE_Own_Capabilities::init_caps();
        
        $multisite = $this->lib->get('multisite');
        if ( !$multisite ) {
            return true;
        }
        
        $this->apply_to_all = $this->lib->get_request_var('ure_apply_to_all', 'post', 'checkbox');
        if ($this->apply_to_all) {
            $this->current_role = '';
            $this->direct_network_roles_update();
        }
        
        return true;
    }
    // end of reset_user_roles()    
    
    
    protected function get_role_id_from_post() {
        
        $result = array('role_id'=>'', 'message'=>'');
        $role_id = $this->lib->get_request_var('user_role_id', 'post' );
        if ( empty( $role_id ) ) {
            $result['message'] = esc_html__('Error: Role ID is empty!', 'user-role-editor' );
            return $result;
        }        
        // $role_id = utf8_decode( $role_id ); // DEPRECATED as of PHP 8.2.0.
        $role_id = mb_convert_encoding( $role_id, 'ISO-8859-1', 'UTF-8');
        // sanitize user input for security
        $match = array();
        $valid_name = preg_match( '/[A-Za-z0-9_\-]*/', $role_id, $match );
        if ( !$valid_name || ( $valid_name && ( $match[0] !== $role_id ) ) ) { 
            // Some non-alphanumeric charactes found!
            $result['message'] = esc_html__( 'Error: Role ID must contain latin characters, digits, hyphens or underscore only!', 'user-role-editor' );
            return $result;
        }
        $numeric_name = preg_match( '/[0-9]*/', $role_id, $match );
        if ( $numeric_name && ( $match[0] === $role_id ) ) { 
            // Numeric name discovered
            $result['message'] = esc_html__( 'Error: WordPress does not support numeric Role name (ID). Add latin characters to it.', 'user-role-editor' );
            return $result;
        }         
                
        $result['role_id'] = strtolower( $role_id );
        
        return $result;
    }
    // end of get_role_id_from_post()
    
    
    /**
     * Process new role creation request
     * 
     * @return array   - operation result data
     * 
     */
    public function add_new_role() {

        $response = array('result'=>'error', 'role_id'=>'', 'role_name'=>'', 'message'=>'');
        if ( !current_user_can( 'ure_create_roles' ) ) {
            $response['message'] = esc_html__( 'Insufficient permissions to work with User Role Editor','user-role-editor' );
            return $response;
        }
        
        $result = $this->get_role_id_from_post();
        if ( !empty( $result['message'] ) ) {
            $response['message'] = $result['message'];
            return $response;
        }
        
        $role_id = $result['role_id']; 
        $wp_roles = wp_roles();        
        if ( isset( $wp_roles->roles[$role_id] ) ) {
            $response['message'] = sprintf( 'Error! ' . esc_html__( 'Role %s exists already', 'user-role-editor' ), $role_id );
            return $response;
        }

        $role_name = $this->lib->get_request_var( 'user_role_name', 'post' );
        if ( empty( $role_name ) ) {
            $role_name = $role_id;  // as user role name is empty, use user role ID instead as a default value
        }
        $this->current_role = $role_id;
        $role_copy_from = $this->lib->get_request_var( 'user_role_copy_from', 'post' );
        if ( !empty( $role_copy_from ) && $role_copy_from!=='none' && $wp_roles->is_role( $role_copy_from ) ) {
            $role = $wp_roles->get_role($role_copy_from);
            $capabilities = $this->remove_caps_not_allowed_for_single_admin( $role->capabilities );
        } else {
            $capabilities = array('read' => true, 'level_0' => true);   // Use subscriber role permissions as a default value
        }        
        // add new role to the roles array      
        $result = add_role( $role_id, $role_name, $capabilities );
        if ( !isset( $result ) || empty( $result ) ) {
            $response['message'] = 'Error! ' . esc_html__('Error is encountered during new role create operation', 'user-role-editor' );
            return $response;
        }
        
        $response['result'] = 'success';
        $response['role_id'] = $role_id;
        $response['role_name'] = $role_name;
        $response['message'] = sprintf(esc_html__('Role %s is created successfully', 'user-role-editor'), $role_name );
                
        
        return $response;
    }
    // end of add_new_role()    
    

    public function update_role() {
        
        $response = array('result'=>'error', 'role_id'=>'', 'role_name'=>'', 'message'=>'');
        if ( !current_user_can( 'ure_edit_roles' ) ) {
            $response['message'] = esc_html__( 'Insufficient permissions to work with User Role Editor','user-role-editor' );
            return $response;
        }
        
        $this->_init0();                
        if ( $this->ure_object==='role') {
            $result = $this->get_role_id_from_post();
            if ( !empty( $result['message'] ) ) {
                $response['message'] = $result['message'];
                return $response;
            }
        }
        
        $this->roles = $this->lib->get_user_roles();
        $this->full_capabilities = $this->lib->init_full_capabilities( $this->ure_object );
        if ( isset( $_POST['values']['user_role'] ) ) {
            $this->notification = $this->init_current_role_name();
        }
        $this->prepare_capabilities_to_save();
        $this->notification = $this->permissions_object_update( $this->notification );
        
        $response['result'] = 'success';
        if ( $this->ure_object==='role') {
            $response['role_id'] = $result['role_id'];
            $response['role_name'] = $this->current_role_name;
        }
        $response['message'] = $this->notification;
        
        return $response;
    }
    // end of update_role()
    
    
    public function update_network() {
        
        $this->init0();
        $this->roles = $this->lib->get_user_roles();
        $this->full_capabilities = $this->lib->init_full_capabilities( $this->ure_object );
        if ( isset( $_POST['user_role'] ) ) {
            $this->notification = $this->init_current_role_name();
        }
        $this->prepare_capabilities_to_save();
        $this->notification = $this->permissions_object_update( $this->notification );
                
    }
    // end of update_network()
        
    
    /**
     * process rename role request
     * 
     * @global WP_Roles $wp_roles
     * 
     * @return string   - message about operation result
     * 
     */
    public function rename_role() {
        global $wp_roles;

        $response = array('result'=>'error', 'message'=>'', 'role_id'=>'', 'role_name'=>'');
        if ( !current_user_can( 'ure_edit_roles' ) ) {
            $response['message'] = esc_html__( 'Insufficient permissions to work with User Role Editor','user-role-editor' );
            return $response;
        }
        
        $result = $this->get_role_id_from_post();
        if ( !empty( $result['message'] ) ) {
            $response['message'] = $result['message'];
            return $response;
        }        
        
        $new_role_name = $this->lib->get_request_var('user_role_name', 'post' );        
        if ( empty( $new_role_name ) ) {            
            $response['message']  = esc_html__( 'Error: Empty role display name is not allowed.', 'user-role-editor' );
            return $response;
        }        
        
        $role_id = $result['role_id'];
        $wp_roles = wp_roles();
        if ( !isset( $wp_roles->roles[$role_id] ) ) {
            $response['message'] = sprintf('Error! ' . esc_html__('Role %s does not exists', 'user-role-editor'), $role_id);
            return $response;
        }
        
        $new_role_name = sanitize_text_field( $new_role_name );        
        $this->current_role = $role_id;
        $this->current_role_name = $new_role_name;

        $old_role_name = $wp_roles->roles[$role_id]['name'];
        $wp_roles->roles[$role_id]['name'] = $new_role_name;
        update_option( $wp_roles->role_key, $wp_roles->roles );
        
        $response['result'] = 'success';
        $response['message'] = sprintf( esc_html__('Role %s is renamed to %s successfully', 'user-role-editor'), $old_role_name, $new_role_name );
        $response['role_id'] = $role_id;
        $response['role_name'] = $new_role_name;
        
        return $response;
    }
    // end of rename_role()        
    
    
    protected function get_wp_built_in_roles() {
        
        $result = array('subscriber', 'contributor', 'author', 'editor', 'administrator');
        
        return $result;
    }
    // end of get_wp_built_in_roles()
    
    /**
     * return array with roles which we could delete, e.g self-created and not used with any blog user
     * 
     * @return array 
     */
    public function get_roles_can_delete() {

        $default_role = get_option( 'default_role' );
        $wp_built_in_roles = $this->get_wp_built_in_roles();
        $roles_can_delete = array();
        $users = count_users();
        $roles = $this->lib->get_user_roles();
        foreach ($roles as $key => $role) {
            $can_delete = true;
            // check if it is default role for new users
            if ( $key === $default_role ) {
                $can_delete = false;
                continue;
            }
            // Do not allow to delete WordPress built-in role
            if ( in_array( $key, $wp_built_in_roles ) ) {
                continue;
            }
            // check if role has capabilities prohibited for the single site administrator
            if ( $this->role_contains_caps_not_allowed_for_simple_admin( $key ) ) {
                continue;
            }                        
            if ( !isset( $users['avail_roles'][$key] ) ) {
                $roles_can_delete[$key] = $role['name'] . ' (' . $key . ')';
            }
        }
        
        return $roles_can_delete;
    }
    // end of get_roles_can_delete()


    /**
     * Deletes user role from the WP database
     */
    protected function delete_wp_roles( $roles_to_del ) {
        global $wp_roles;

        $result = array('result'=>false, 'deleted_roles'=>array(), 'message'=>'');        
        $roles_can_delete = $this->get_roles_can_delete();
        $wp_roles = wp_roles();
        foreach($roles_to_del as $role_id) {
            if ( !isset( $wp_roles->roles[$role_id] ) ) {
                $result['message'] = esc_html__('Role does not exist','user-role-editor') .' - '. $role_id;
                return $result;
            }
            if ( !isset( $roles_can_delete[$role_id]) ) {
                $result['message'] = esc_html__( 'You can not delete role', 'user-role-editor' ) .' - '.$role_id;
                return $result;
            }                                                        
            
            unset( $wp_roles->role_objects[$role_id] );
            unset( $wp_roles->role_names[$role_id] );
            unset( $wp_roles->roles[$role_id] );
            $result['deleted_roles'][] = $role_id;
            $result['result'] = true;
        }   // foreach()
        if ( $result['result'] ) {
            update_option( $wp_roles->role_key, $wp_roles->roles );            
        }
        
        return $result;
    }
    // end of delete_wp_roles()    
    
    
    protected function delete_all_unused_roles() {        
        
        $roles_to_del = array_keys( $this->get_roles_can_delete() );  
        if ( empty( $roles_to_del ) || !is_array( $roles_to_del ) ) {
            $result = array(
                'result'=>false, 
                'deleted_roles'=>array(), 
                'message'=>esc_html__( 'There are no roles for deletion','user-role-editor' ));
            return $result;
        }
        
        $result = $this->delete_wp_roles( $roles_to_del );
        
        return $result;        
    }
    // end of delete_all_unused_roles()
    
    
    /**
     * Process user request for user role deletion
     * @return string
     */
    public function delete_role() {        
        
        $response = array('result'=>'error', 'message'=>'', 'deleted_roles'=>array());
        if ( !current_user_can('ure_delete_roles') ) {
            $response['message'] = esc_html__('Insufficient permissions to work with User Role Editor','user-role-editor');
            return $response;
        }

        $role_id = $this->lib->get_request_var( 'user_role_id', 'post');
        if ( $role_id==-1 ) { // delete all unused roles
            $result = $this->delete_all_unused_roles();
        } else {
            if ( empty( $role_id ) ) {
                $response['message'] = esc_html__( 'Wrong role ID','user-role-editor');
                return $response;
            }
            $result = $this->delete_wp_roles( array( $role_id ) );
        }
        if ( $result['result']===true ) {
            $response['result'] = 'success';
            $response['deleted_roles'] = $result['deleted_roles'];
            if ( $role_id==-1 ) {
                $response['message'] = esc_html__( 'Unused roles are deleted successfully', 'user-role-editor' );
            } else {
                $response['message'] = sprintf( esc_html__( 'Role %s is deleted successfully', 'user-role-editor' ), $role_id );
            }
        } else {
            $response['message'] = $result['message'];
        }
        if ( isset( $_POST['user_role_id'] ) ) {            
            unset( $_POST['user_role_id'] );
        }

        return $response;
    }
    // end of delete_role()
    
    
    /**
     * Change default WordPress role
     * This method is only for a single site of WordPress multisite installation.
     * @return string
     */
    protected function change_default_role() {
        
        if ( !current_user_can('ure_delete_roles') ) {
            $mess = esc_html__('Insufficient permissions to work with User Role Editor','user-role-editor');
            return $mess;
        }
        
        $multisite = $this->lib->get('multisite');
        if ( !$multisite || is_network_admin() ) {
            $mess = esc_html__('This method is only for a single site of WordPress multisite installation.', 'user-role-editor');
            return $mess;
        }
        if ( empty( $_POST['user_role_id'] ) ) {
            $mess = esc_html__('Wrong request. Default role can not be empty', 'user-role-editor');
            return $mess;
        }
        
        $mess = '';
        $wp_roles = wp_roles();        
        $role_id = $this->lib->get_request_var('user_role_id', 'post');
        unset( $_POST['user_role_id'] );
        if ( isset( $wp_roles->role_objects[$role_id] ) && $role_id !== 'administrator' ) {
            update_option( 'default_role', $role_id );
            $this->wp_default_role = get_option( 'default_role' );
            if ($this->wp_default_role===$role_id) {
                $mess = sprintf(esc_html__('Default role for new users is set to %s successfully', 'user-role-editor'), $wp_roles->role_names[$role_id]);
            } else {
                $mess = 'Error! ' . esc_html__('Error encountered during default role change operation', 'user-role-editor');
            }
        } elseif ($role_id === 'administrator') {
            $mess = 'Error! ' . esc_html__('Can not set Administrator role as a default one', 'user-role-editor');
        } else {
            $mess = 'Error! ' . esc_html__('This role does not exist - ', 'user-role-editor') . esc_html($role_id);
        }
        

        return $mess;
    }
    // end of change_default_role()
    
    
    /**
     * Process user request
     */
    protected function process_user_request() {

        $this->notification = '';
        if ( !isset( $_POST['action'] ) ) {
            return false;
        }
        if ( !$this->valid_nonce() ) {
            if ( defined('WP_DEBUG') && WP_DEBUG ) {
                return false;
            } else {
                exit;
            }
        }
        
        $action = $this->lib->get_request_var('action', 'post');
        switch ( $action ) {
            case 'reset': {
                $this->reset_user_roles();
                exit;            
            }
            case 'change-default-role': {
                $this->notification = $this->change_default_role();
                break;
            }
            case 'caps-readable': {
                $this->set_caps_readable();                
                break;
            }
            case 'show-deprecated-caps': {
                $this->set_show_deprecated_caps();                
                break;
            }
            case 'roles_restore_note': {
                $this->notification = esc_html__( 'User Roles are restored to WordPress default values. ', 'user-role-editor' );
                break;
            }
            default: {
                do_action( 'ure_process_user_request' );
            }
        }   // switch ( $action ) ....
              
        return true;
    }
    // end of process_user_request()
    
    
    public function init1() {

        $this->roles = $this->lib->get_user_roles();
        $this->full_capabilities = $this->lib->init_full_capabilities( $this->ure_object );
        if ( empty( $this->role_additional_options ) ) {
            $this->role_additional_options = URE_Role_Additional_Options::get_instance( $this->lib );
        }
                
    }
    // end of editor_init1()    
    
    
    /**
     * Return id of role last in the list of sorted roles
     * 
     */
    protected function get_last_role_id() {
        
        // get the key of the last element in roles array
        $keys = array_keys( $this->roles );
        asort( $keys );
        $last_role_id = array_pop( $keys );
        
        return $last_role_id;
    }
    // end of get_last_role_id()

    
    protected function set_current_role() {
        
        if (!isset($this->current_role) || !$this->current_role) {
            if (isset($_REQUEST['user_role']) && $_REQUEST['user_role'] && isset($this->roles[$_REQUEST['user_role']])) {
                $this->current_role = $_REQUEST['user_role'];
            } else {
                $this->current_role = $this->get_last_role_id();
            }
            $roles = $this->lib->get_user_roles();
            $this->current_role_name = $roles[$this->current_role]['name'];
        }
        
    }
    // end of set_current_role()

    
    // returns true if editing user has $capability assigned through the roles or directly
    // returns true if editing user has role with name equal $capability
    public function user_can($capability) {
        
        if (isset($this->user_to_edit->caps[$capability])) {
            return true;
        }
        foreach ($this->user_to_edit->roles as $role) {
            if ($role===$capability) {
                return true;
            }
            if (!empty($this->roles[$role]['capabilities'][$capability])) {
                return true;
            }
        }
                
        return false;        
    }
    // end of user_can()
    
    
    protected function show_editor() {

        $this->lib->show_message( $this->notification );
        if ( $this->ure_object == 'user' ) {
            $view = new URE_User_View();
        } else {
            $this->set_current_role();
            $view = new URE_Role_View();
            $view->role_edit_prepare_html();
        }
        ?>
        <div class="wrap">
            <h1><?php _e('User Role Editor', 'user-role-editor'); ?></h1>
            <div id="ure_container">                
                <div id="user_role_editor" class="ure-table-cell" >
                    <form id="ure_form" method="post" action="<?php echo admin_url() . URE_PARENT . '?page=users-' . URE_PLUGIN_FILE; ?>" >			
                        <div id="ure_form_controls">
        <?php
        $view->display();
        wp_nonce_field( 'user-role-editor', 'ure_nonce' );
        ?>
                            <input type="hidden" name="action" value="update" />
                        </div>      
                    </form>		      
<?php
        if ( !$this->lib->is_pro() ) {
            $view->advertise_pro();
        }
?>
                </div>
<?php
/*
        if (!$this->lib->is_pro()) {
            $view->advertise_commercials();
        } 
 */
        $view->display_edit_dialogs();
        do_action( 'ure_dialogs_html' );
        URE_Role_View::output_confirmation_dialog();
?>            
            </div>
        </div>
        <?php
    }
    // end of show_editor()
    
    
    /**
     *  Show main page according to the context - role or user editor
     */
    public function show() {

        if (!$this->init0()) {
            $message = esc_html__( 'Error: wrong request', 'user-role-editor' );
            $this->lib->show_message( $message );
            return false;
        }                
        
        $this->process_user_request();
        $this->init1();
        $this->show_editor();
        
        return true;
    }
    // end of show()


    public function set_notification($value) {
        
        $this->notification = $value;
        
    }
    // end of set_notification()
    

    /**
     * Not really used in the plugin - just storage for the translation strings
     */
    protected function translation_data() {
// for the translation purpose
        if (false) {
// Standard WordPress roles
            __('Editor', 'user-role-editor');
            __('Author', 'user-role-editor');
            __('Contributor', 'user-role-editor');
            __('Subscriber', 'user-role-editor');
// Standard WordPress capabilities
            __('Switch themes', 'user-role-editor');
            __('Edit themes', 'user-role-editor');
            __('Activate plugins', 'user-role-editor');
            __('Edit plugins', 'user-role-editor');
            __('Edit users', 'user-role-editor');
            __('Edit files', 'user-role-editor');
            __('Manage options', 'user-role-editor');
            __('Moderate comments', 'user-role-editor');
            __('Manage categories', 'user-role-editor');
            __('Manage links', 'user-role-editor');
            __('Upload files', 'user-role-editor');
            __('Import', 'user-role-editor');
            __('Unfiltered html', 'user-role-editor');
            __('Edit posts', 'user-role-editor');
            __('Edit others posts', 'user-role-editor');
            __('Edit published posts', 'user-role-editor');
            __('Publish posts', 'user-role-editor');
            __('Edit pages', 'user-role-editor');
            __('Read', 'user-role-editor');
            __('Level 10', 'user-role-editor');
            __('Level 9', 'user-role-editor');
            __('Level 8', 'user-role-editor');
            __('Level 7', 'user-role-editor');
            __('Level 6', 'user-role-editor');
            __('Level 5', 'user-role-editor');
            __('Level 4', 'user-role-editor');
            __('Level 3', 'user-role-editor');
            __('Level 2', 'user-role-editor');
            __('Level 1', 'user-role-editor');
            __('Level 0', 'user-role-editor');
            __('Edit others pages', 'user-role-editor');
            __('Edit published pages', 'user-role-editor');
            __('Publish pages', 'user-role-editor');
            __('Delete pages', 'user-role-editor');
            __('Delete others pages', 'user-role-editor');
            __('Delete published pages', 'user-role-editor');
            __('Delete posts', 'user-role-editor');
            __('Delete others posts', 'user-role-editor');
            __('Delete published posts', 'user-role-editor');
            __('Delete private posts', 'user-role-editor');
            __('Edit private posts', 'user-role-editor');
            __('Read private posts', 'user-role-editor');
            __('Delete private pages', 'user-role-editor');
            __('Edit private pages', 'user-role-editor');
            __('Read private pages', 'user-role-editor');
            __('Delete users', 'user-role-editor');
            __('Create users', 'user-role-editor');
            __('Unfiltered upload', 'user-role-editor');
            __('Edit dashboard', 'user-role-editor');
            __('Update plugins', 'user-role-editor');
            __('Delete plugins', 'user-role-editor');
            __('Install plugins', 'user-role-editor');
            __('Update themes', 'user-role-editor');
            __('Install themes', 'user-role-editor');
            __('Update core', 'user-role-editor');
            __('List users', 'user-role-editor');
            __('Remove users', 'user-role-editor');
            __('Add users', 'user-role-editor');
            __('Promote users', 'user-role-editor');
            __('Edit theme options', 'user-role-editor');
            __('Delete themes', 'user-role-editor');
            __('Export', 'user-role-editor');
        }
    }
    // end of translation_data()    
    
    
    
    /**
     * Prevent cloning of the instance of the *Singleton* instance.
     *
     * @return void
     */
    public function __clone() {
        throw new \Exception('Do not clone a singleton instance.');
    }
    // end of __clone()
    
    /**
     * Prevent unserializing of the *Singleton* instance.
     *
     * @return void
     */
    public function __wakeup() {
        throw new \Exception('Do not unserialize a singleton instance.');
    }
    // end of __wakeup()    
    
}
// end of URE_Editor class
