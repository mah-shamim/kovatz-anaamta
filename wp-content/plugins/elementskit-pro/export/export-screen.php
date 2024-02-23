<?php

namespace ElementsKit\Export;


use ElementsKit\Traits\Singleton;

class Export_Screen {

	use Singleton;


	public function init() {

		/**
		 * Add another action in bulk option dropdown
		 */
		add_filter('bulk_actions-edit-elementskit_widget', [$this, 'add_option_in_bulk']);

		/**
		 * Handler for new bulk option
		 */
		add_filter('handle_bulk_actions-edit-elementskit_widget', [$this, 'bulk_response_export'], 10, 3);

	}


	public function add_option_in_bulk($bulk_actions) {

		$bulk_actions['export-in-json'] = __('Export', 'elementskit');

		return $bulk_actions;
	}


	public function bulk_response_export($redirect_url, $action, $post_ids) {

		if($action == 'export-in-json') {

			if(!is_user_logged_in() || !current_user_can('manage_options')) {

				return [
					'success' => false,
					'message' => [
						esc_html__("Not enough permission.", 'elementskit'),
					],
				];
			}

			$exported = [];

			foreach($post_ids as $post_id) {

				$metas = get_post_meta($post_id);

				$each['_md_hash']                       = md5('ekit_wb_' . $post_id);
				$each['_elementor_edit_mode']           = empty($metas['_elementor_edit_mode'][0]) ? 'builder' : $metas['_elementor_edit_mode'][0];
				$each['_wp_page_template']              = empty($metas['_wp_page_template'][0]) ? 'elementor_canvas' : $metas['_wp_page_template'][0];
				$each['elementskit_custom_widget_data'] = empty($metas['elementskit_custom_widget_data'][0]) ? '' : $metas['elementskit_custom_widget_data'][0];

				$exported[] = $each;
			}


			header('Content-disposition: attachment; filename=widget_export.' . date('Y-m-d') . '.json');
			header("Content-type: application/json; charset=utf-8");
			echo json_encode($exported);
			exit();

		}

		return $redirect_url;
	}

}