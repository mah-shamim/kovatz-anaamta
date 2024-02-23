<?php 
namespace ElementsKit\Libs\Framework\Classes;

use ElementsKit\Libs\Framework\Classes\License;

defined( 'ABSPATH' ) || exit;

class Ajax extends \ElementsKit_Lite\Libs\Framework\Classes\Ajax{

    public function __construct() {
        parent::__construct();
        
        add_action( 'wp_ajax_ekit_admin_license', [$this, 'elementskit_admin_license'] );
    }

    public function elementskit_admin_license() {
        if(!current_user_can('manage_options')){
            return;
        }      
        $key = !isset($_POST['elementkit_pro_license_key']) ? '' : sanitize_text_field($_POST['elementkit_pro_license_key']);
        $type2 = !isset($_GET['type']) ? '' : sanitize_text_field($_GET['type']);
        $type = !isset($_POST['type']) ? '' : sanitize_text_field($_POST['type']);
        $type = ($type == '') ? $type2 : $type;


        $result = [
            'status' => 'danger',
            'message' => esc_html__('Something went wrong', 'elementskit')
        ];

        switch($type){
            case 'activate':
                if($key == ''){
                    $result['message'] = esc_html__('Your key is empty', 'elementskit');
                    echo $this->return_json($result); wp_die();
                }
        
                $o = License::instance()->activate( $key );
                if ( is_object($o) ) {
                    $result = $o;
                }
            
            break;
            case 'revoke':
                License::instance()->revoke();
                wp_redirect('https://account.wpmet.com/?wpmet-screen=products'); exit;
            break;
        }
        
        echo $this->return_json($result);
        wp_die();
    }

    public function return_json($data){
        if(is_array($data) || is_object($data)){
            return  json_encode($data);
        }else{
            return $data;
        }
    }

}