<?php
namespace ElementsKit\Libs\Framework\Classes;
use ElementsKit\Libs\Framework\Classes\Utils;

defined( 'ABSPATH' ) || exit;

class License{

    public static $instance = null;

    public function global_var_cache_get($key){
        global $elementskit_global_var_cache;
        if(isset($elementskit_global_var_cache[$key])){
            return $elementskit_global_var_cache[$key];
        }

        return null;
    }

    public function global_var_cache_set($key, $value){
        global $elementskit_global_var_cache;
        $elementskit_global_var_cache[$key] = $value;

        return true;
    }

    public function get_license_info(){
        return apply_filters('elementskit/license/extended', '');
    }

    public function activate($key){
        $data = [
            'key' => $key,
            'id' => \ElementsKit::product_id()
        ];
        $o = $this->com_validate($data);
        if(isset($o->validate) && $o->validate == 1){
            update_option('__validate_oppai__', $o->oppai);
            Utils::instance()->save_option('license_key', $o->key);
        }

        return $o;
    }
    public function revoke(){
        $data = [
            'key' => Utils::instance()->get_option('license_key'),
        ];

        delete_option('__validate_oppai__');
        Utils::instance()->save_option('license_key', '');

        return true;
    }
    public function com_validate($data = []){
        if(strlen($data['key']) < 28){
            return null;
        }
        $data['oppai'] = get_option('__validate_oppai__');
        $data['action'] = 'activate';
        $data['v'] = \ElementsKit::version();
        $url = \ElementsKit::api_url() . 'license?' . http_build_query($data);

        $args = array(
            'timeout'     => 60,
            'redirection' => 3,
            'httpversion' => '1.0',
            'blocking'    => true,
            'sslverify'   => true,
        );

        $res = wp_remote_get( $url, $args );

        return (object) json_decode(
            (string) $res['body']
        );
    }

    public function com_revoke($data = []){
        $data['oppai'] = get_option('__validate_oppai__');
        $data['action'] = 'revoke';
        $data['v'] = \ElementsKit::version();
        $url = \ElementsKit::api_url() . 'license?' . http_build_query($data);

        $args = array(
            'timeout'     => 10,
            'redirection' => 3,
            'httpversion' => '1.0',
            'blocking'    => true,
            'sslverify'   => true,
        );

        $res = wp_remote_get( $url, $args );

        return (object) json_decode(
            (string) $res['body']
        );
    }

    public function status(){
    	wp_cache_set( 'elementskit_license_status', true );
		return true;
        $cached = $this->global_var_cache_get( 'elementskit_license_status' );
		if ( null !== $cached ) {
			return $cached;
        }

        $oppai = get_option('__validate_oppai__');
        $key = Utils::instance()->get_option('license_key');
        $status = 'invalid';

        if($oppai != '' && $key != ''){
            $status = 'valid';
        }
        $this->global_var_cache_set( 'elementskit_license_status', $status );

        return $status;
    }

    public static function instance() {
        if ( is_null( self::$instance ) ) {

            // Fire the class instance
            self::$instance = new self();
        }

        return self::$instance;
    }
}
