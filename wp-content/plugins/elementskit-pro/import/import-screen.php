<?php

namespace ElementsKit\Import;

use ElementsKit\Traits\Singleton;

class Import_Screen {

	use Singleton;


	public function init() {

		add_action('admin_init', [$this, 'process_imported_json']);
		add_action('admin_head-edit.php', [$this, 'button_insert_into_cpt_list_page']);
		

		add_action('admin_notices', function() {
			if(!empty($_REQUEST['import-from-json'])) {
				printf('<div id="message" class="updated notice is-dismissable"><p>' . __('%d posts imported successfully.', 'elementskit') . '</p></div>', intval($_REQUEST['import-from-json']));
			}
		});
	}


	public function process_imported_json() {

		$args = array($_GET);

		if(isset($_GET['handler']) && $_GET['handler'] = 'ekit_wb_import') {

			if(wp_verify_nonce($_GET['nonce'], 'ekit_wb_import_nnc')) {

				$fl = get_attached_file($_GET['media_id']);

				if(!empty($fl)) {

					if(substr($fl, -5) !='.json') {

						wp_die('Only json file is supporter for import.');
					}

					$cont = $this->read_json_file($fl);

					$cont = json_decode($cont);

					foreach($cont as $item) {

						$wb_data = unserialize($item->elementskit_custom_widget_data);
						$p_title = $wb_data->title;

						$wd_id = $this->create_new_widget($p_title);

						$wb_data->push_id = $wd_id;

						update_post_meta($wd_id, '_elementor_edit_mode', $item->_elementor_edit_mode);
						update_post_meta($wd_id, '_wp_page_template', $item->_wp_page_template);
						update_post_meta($wd_id, 'elementskit_custom_widget_data', $wb_data);

						\ElementsKit_Lite\Modules\Widget_Builder\Widget_File::instance()->create($wb_data, $wd_id);
					}


					$args['import-from-json'] = count($cont);

					$redir = admin_url('edit.php');
					$redir = add_query_arg($args, $redir);
					$redir = remove_query_arg(['handler', 'nonce', 'media_id', 'post_status'], $redir);

					wp_redirect($redir);

					exit();
				}
			}
		}
	}


	public function button_insert_into_cpt_list_page() {

		if(!is_user_logged_in() || !current_user_can('manage_options')) {

			return [
				'success' => false,
				'message' => [
					esc_html__("Not enough permission.", 'elementskit'),
				],
			];
		}

		global $current_screen, $wp;

		if('elementskit_widget' == $current_screen->post_type) { 

			$args = array($_GET);

			$args['handler'] = 'ekit_wb_import';
			$args['nonce']   = wp_create_nonce('ekit_wb_import_nnc');
	
			$redir = home_url(add_query_arg($args, $wp->request));
			
			wp_enqueue_media();
			
			?>

			<script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $($(".wrap .page-title-action")[0]).after('<a id="ekit_import_btn" href="#" class="page-title-action">Import</a>');

                    $(document).on('click', '#ekit_import_btn', function (ev) {
                        ev.preventDefault();


                        var button = $(this),
                            aw_uploader = wp.media({
                                title: 'Widget Import',
                                library: {
                                    uploadedTo: wp.media.view.settings.post.id,
                                    type: 'file'
                                },
                                button: {
                                    text: 'Use this file'
                                },
                                multiple: false
                            }).on('select', function () {

                                var attachment = aw_uploader.state().get('selection').first().toJSON();

                                import_wb_widgets(attachment, '<?php echo $redir ?>');

                            }).open();

                    });


                });


                function import_wb_widgets($importer_file, $url) {

                    var append = '&media_id=' + $importer_file.id;


                    window.location = $url + append;
                }


			</script>
			<?php
		}
	}


	private function read_json_file($file_path) {

		ob_start();

		include $file_path;

		$contents = ob_get_clean();

		return $contents;
	}


	private function create_new_widget($title) {

		$widget_data = [
			'post_title'  => $title,
			'post_status' => 'publish',
			'post_type'   => 'elementskit_widget',
		];

		$id = wp_insert_post($widget_data);

		return $id;
	}

}