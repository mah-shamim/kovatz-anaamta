<?php

class Ekit_facebook_settings {

	public $ekit_fb_account_id = '';
	/**
	 * Access token app id
	 */
	public $ekit_fb_app_id = '';

	/**
	 * Access token secret key
	 */
	public $ekit_fb_app_secret = '';

	/**
	 * Access token secret key
	 */

	protected static $fb_graph_url = 'https://graph.facebook.com/';
	protected static $fb_api_version = 'v8.0';

	public $ekit_fb_app_access_token = '';

	private $user_app_id;
	private $user_app_key;
	private $user_id;
	private $access_key;
	private $page_id;


	private $fileds = [
		'id',
		'message',
		'created_time',
	];


	public function setup(array $config) {

		$this->user_id    = empty($config['pg_id']) ? '' : $config['pg_id'];
		$this->page_id    = empty($config['pg_id']) ? '' : $config['pg_id'];
		$this->access_key = empty($config['pg_tok']) ? '' : $config['pg_tok'];
	}


	public function getToken() {
		return $this->access_key;

	}


	public static function get_me_call_transient_key($token = 'nothing') {

		return 'ekit_fb_feeds_me_' . md5($token);
	}


	public static function get_fb_user_feed_call_transient_key($user_id) {

		return 'ekit_fbf_call_' . $user_id;
	}


	public static function get_fb_pg_post_call_transient_key($page_id, $tok) {

		$md = md5($page_id . $tok);

		return 'ekit_fbf_pg_post_call_' . $md;
	}


	public static function get_fb_pg_call_transient_key($token = 'nothing') {

		return 'ekit_fbf_page_info_' . md5($token);
	}


	public static function get_fb_feed_url($user_id, $ac_token, $limit = 5, $fld = '') {

		$url  = self::$fb_graph_url . self::$fb_api_version . '/' . $user_id . '/feed';
		$args = '?access_token=' . $ac_token . '&limit=' . $limit . '&fields=' . $fld;

		return $url . $args;
	}


	public static function get_fb_page_posts_url($page_id, $pg_token, $fld = '') {

		$url  = self::$fb_graph_url . self::$fb_api_version . '/' . $page_id . '/posts';
		$args = '?access_token=' . $pg_token . '&fields=' . $fld;

		return $url . $args;
	}

	/**
	 * @param $page_id
	 * @param string $type - small, normal, album, large, square
	 * @return string
	 */
	public static function get_fb_page_profile_pic_url($page_id, $type = 'normal') {

		$url  = self::$fb_graph_url . self::$fb_api_version . '/' . $page_id . '/picture';
		$args = '?type='.$type;

		return $url . $args;
	}


	public function get_fb_user_feed($user_id, $limit = 5, $fields = []) {

		$token           = $this->access_key;
		$transient_name  = self::get_fb_user_feed_call_transient_key($user_id);
		$transient_value = get_transient($transient_name);

		if(false !== $transient_value) {

			return [
				'success' => true,
				'msg'     => __('Fetched from transient', 'elementskit'),
				'param'   => $transient_value,
			];
		}

		try {

			if(empty($fields)) {
				$fld = implode(',', $this->fileds);
			} else {
				$fld = implode(',', $fields);
			}

			$url     = self::get_fb_feed_url($user_id, $token, $limit, $fld);
			$request = wp_remote_get($url);

			if(is_wp_error($request)) {

				return [
					'success' => false,
					'msg'     => $request->get_error_message(),
					'param'   => [],
				];
			}

			$body   = wp_remote_retrieve_body($request);
			$result = json_decode($body);

			$expiration_time = 86400;//in second
			set_transient($transient_name, $result, $expiration_time);


		} catch(\Exception $ex) {

			return [
				'success' => false,
				'msg'     => $ex->getMessage(),
				'param'   => [],
			];
		}

		return [
			'success' => true,
			'msg'     => __('Fetched and cached the api call', 'elementskit'),
			'param'   => $result,
		];
	}


	/**
	 * Get user details
	 * mainly id and name
	 *
	 * @return array
	 */
	public function get_fb_me_id() {

		$msg   = __('Successfully retrieved', 'elementskit');
		$token = $this->access_key;

		$transient_name  = self::get_me_call_transient_key($token);
		$transient_value = get_transient($transient_name);

		if(false !== $transient_value) {

			return [
				'success' => true,
				'msg'     => $msg,
				'param'   => $transient_value,
			];
		}


		$url_acc   = self::$fb_graph_url . self::$fb_api_version . '/me';
		$acc_args  = '?access_token=' . $token;
		$extracted = [];

		try {

			$request = wp_remote_get($url_acc . $acc_args);
			$body    = wp_remote_retrieve_body($request);
			$dt      = json_decode($body);

			if(!empty($dt->name)) {
				$extracted['usr_tok']  = $token;
				$extracted['usr_name'] = $dt->name;
				$extracted['usr_id']   = $dt->id;

				$expiration_time = 86400;//in second

				set_transient($transient_name, $extracted, $expiration_time);
			}

		} catch(\Exception $ex) {

			$msg = $ex->getMessage();
			$dt  = 'Nothing....';
		}


		return [
			'success' => true,
			'msg'     => $msg,
			'param'   => $extracted,
		];
	}


	public function get_fb_page_id_by_token($page_token) {

		$msg   = __('Successfully retrieved', 'elementskit');
		$token = $page_token;

		$trans_name  = self::get_fb_pg_call_transient_key($token);
		$trans_value = get_transient($trans_name);

		if(false !== $trans_value) {

			return [
				'success' => true,
				'msg'     => $msg,
				'param'   => $trans_value,
			];
		}


		$url_acc   = self::$fb_graph_url . self::$fb_api_version . '/me';
		$acc_args  = '?access_token=' . $token;
		$extracted = [];

		try {

			$request = wp_remote_get($url_acc . $acc_args);
			$body    = wp_remote_retrieve_body($request);
			$dt      = json_decode($body);

			if(!empty($dt->name)) {
				$extracted['pg_tok']  = $token;
				$extracted['pg_name'] = $dt->name;
				$extracted['pg_id']   = $dt->id;

				$expiration_time = 86400 * 10;//10 days at least

				set_transient($trans_name, $extracted, $expiration_time);
			}

		} catch(\Exception $ex) {

			$msg = $ex->getMessage();
			$dt  = 'Nothing....';
		}


		return [
			'success' => true,
			'msg'     => $msg,
			'param'   => $extracted,
		];
	}


	public function verify_fb_page_and_token() {

		$msg     = __('Successfully retrieved', 'elementskit');
		$token   = $this->access_key;
		$page_id = $this->page_id;

		$hash        = md5($token . $this->page_id);
		$trans_name  = self::get_fb_pg_call_transient_key($hash);
		$trans_value = get_transient($trans_name);

		if(false !== $trans_value) {

			return [
				'success' => true,
				'msg'     => $msg,
				'param'   => $trans_value,
			];
		}


		$url_acc   = self::$fb_graph_url . self::$fb_api_version . '/' . $page_id;
		$acc_args  = '?access_token=' . $token;
		$extracted = [];

		try {

			$request = wp_remote_get($url_acc . $acc_args);
			$body    = wp_remote_retrieve_body($request);
			$dt      = json_decode($body);

			if(!empty($dt->name)) {
				$extracted['pg_tok']  = $token;
				$extracted['pg_name'] = $dt->name;
				$extracted['pg_id']   = $dt->id;

				$expiration_time = 86400 * 10;//10 days at least

				set_transient($trans_name, $extracted, $expiration_time);
			}

		} catch(\Exception $ex) {

			$msg = $ex->getMessage();
			$dt  = 'Nothing....';
		}


		return [
			'success' => true,
			'msg'     => $msg,
			'param'   => $extracted,
		];
	}


	public function get_fb_page_posts($limit = 5, $fields = []) {

		$token       = $this->access_key;
		$page_id     = $this->page_id;
		$trans_name  = self::get_fb_pg_post_call_transient_key($page_id, $token);
		$trans_value = get_transient($trans_name);

		if(false !== $trans_value) {

			return [
				'success' => true,
				'msg'     => __('Fetched from transient', 'elementskit'),
				'param'   => $trans_value,
			];
		}

		try {

			$num = empty($limit) ? 10 : ($limit > 25 ? 25 : $limit  );
			$fld     = 'id,message,attachments{description,title,type,url,media},from,comments{message,like_count,user_likes,permalink_url},likes{username,name},shares,permalink_url,full_picture,created_time&limit='.intval($num);
			$url     = self::get_fb_page_posts_url($page_id, $token, $fld);
			$request = wp_remote_get($url);

			if(is_wp_error($request)) {

				return [
					'success' => false,
					'msg'     => $request->get_error_message(),
					'param'   => [],
				];
			}

			$body   = wp_remote_retrieve_body($request);
			$result = json_decode($body);

			$expiration_time = 86400;//in second
			set_transient($trans_name, $result, $expiration_time);

		} catch(\Exception $ex) {

			return [
				'success' => false,
				'msg'     => $ex->getMessage(),
				'param'   => [],
			];
		}

		return [
			'success' => true,
			'msg'     => __('Fetched and cached the api call', 'elementskit'),
			'param'   => $result,
		];
	}


	/**
	 * @param mixed $access_key
	 */
	public function setAccessKey($access_key) {
		$this->access_key = $access_key;
	}
}
