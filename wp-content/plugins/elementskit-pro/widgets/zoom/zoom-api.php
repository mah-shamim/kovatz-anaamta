<?php

namespace ElementsKit\Widgets\Zoom;

use ElementsKit_Lite\Core\Handler_Api;
use Elementor\ElementsKit_Widget_Zoom_Handler;

class Zoom_Api extends Handler_Api {


	private $widget_id;

	private $widget_type;

	private $widget_arr;


	public function __construct() {

		parent::__construct();
	}


	public function config() {
		$this->prefix = 'zoom-meeting';
		$this->param  = "";
	}


	public function post_create() {

		if(!is_user_logged_in() || !current_user_can('manage_options')) {

			return [
				'success' => false,
				'message' => [
					esc_html__("Not enough permission.", 'elementskit'),
				],
			];
		}

		$data = $this->request->get_params();

		$meeting = ElementsKit_Widget_Zoom_Handler::create_meeting($data);

		return $meeting;
	}


	public function post_hosts() {

		if(!is_user_logged_in() || !current_user_can('manage_options')) {

			return [
				'success' => false,
				'message' => [
					esc_html__("Not enough permission.", 'elementskit'),
				],
			];
		}

		//$data = $this->request->get_params();

		$meeting = ElementsKit_Widget_Zoom_Handler::get_hosts();

		return $meeting;
	}


	public function post_password_verify() {

		$data = $this->request->get_params();

		$post_id  = intval($data['post_id']);
		$password = $data['password'];

		$this->widget_id   = $data['widget_id'];
		$this->widget_type = ElementsKit_Widget_Zoom_Handler::get_name();

		$sett = \Elementor\Plugin::$instance->documents->get($post_id)->get_elements_data();

		$this->parse_options($sett);

		$widget = \Elementor\Plugin::$instance->elements_manager->create_element_instance($this->widget_arr);

		$fnl = $widget->get_settings_for_display();

		unset($sett, $widget);

		$m_cache = $fnl['meeting_cache'];
		$pass = $fnl['password'];

		$flag = false;
		$msg = __('Wrong information.', 'elementskit');

		if($password == $pass) {
			$flag = true;
			$msg = __('Password successfully matched.', 'elementskit');
		}

		return [
			'success' => $flag,
			'message' => $msg,
			//'meeting_cache' => $m_cache,
		];
	}


	private function parse_options($data) {

		if(!is_array($data) || empty($data)) {
			return;
		}

		foreach($data as $item) {

			if(empty($item)) {
				continue;
			}

			if('section' === $item['elType'] || 'column' === $item['elType']) {

				$this->parse_options($item['elements']);

			} else {

				$this->parse_options_simple($item);
			}
		}
	}


	private function parse_options_simple($item) {

		if(
			$item['id'] === $this->widget_id &&
			$item['widgetType'] === $this->widget_type
		) {
			$this->widget_arr = $item;
		}
	}
}
