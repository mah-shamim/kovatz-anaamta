<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Skins_Presets' ) ) {

	/**
	 * Define Jet_Engine_Skins_Presets class
	 */
	class Jet_Engine_Skins_Presets {

		private $data = array();
		private $log  = array();

		/**
		 * Process skin export
		 */
		public function __construct() {

			add_action( 'admin_footer', array( $this, 'print_templates' ) );
			add_action( 'jet-engine/dashboard/assets', array( $this, 'presets_config' ) );

			add_action( 'wp_ajax_jet_engine_import_preset', array( $this, 'import_preset' ) );

		}

		/**
		 * Process preset importing
		 *
		 * @return [type] [description]
		 */
		public function import_preset() {

			$nonce_action = jet_engine()->dashboard->get_nonce_action();

			if ( empty( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], $nonce_action ) ) {
				wp_send_json_error();
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error();
			}

			$preset = $_REQUEST['preset'] ? esc_attr( $_REQUEST['preset'] ) : null;

			if ( ! $preset ) {
				wp_send_json_error();
			}

			$this->data = $this->get_remote_preset( $preset );

			if ( ! $this->data ) {
				wp_send_json_error();
			}

			$process_stack = array(
				'forms',
				'listings',
				'pages',
				'templates',
			);

			foreach ( $process_stack as $stack_group ) {

				if ( empty( $this->data[ $stack_group ] ) ) {
					continue;
				} else {
					$this->import_items( $this->data[ $stack_group ], $stack_group );
				}

			}

			if ( ! empty( $this->data['options_to_export'] ) ) {
				foreach ( $this->data['options_to_export'] as $option_key => $option_value ) {
					update_option( $option_key, $option_value, false );
				}
			}

			if ( ! empty( $this->data['meta_boxes'] ) ) {

				foreach ( $this->data['meta_boxes'] as $meta_box ) {

					if ( isset( $meta_box['id'] ) ) {
						unset( $meta_box['id'] );
					}

					jet_engine()->meta_boxes->data->update_item_in_db( $meta_box );

					if ( empty( $this->log['meta_boxes'] ) ) {
						$this->log['meta_boxes'] = array( 'items' => array() );
					}

					$this->log['meta_boxes']['items'][] = $meta_box['args']['name'];
				}

				if ( ! empty( $this->log['meta_boxes'] ) ) {
					$this->log['meta_boxes']['label'] = __( 'Meta Boxes', 'jet-engine' );
				}

			}

			$handler = $this->get_preset_handler( $preset );

			if ( $handler ) {
				call_user_func( $handler );
			}

			wp_send_json_success();

		}

		/**
		 * Import items
		 *
		 * @param  array  $items [description]
		 * @param  string $group [description]
		 * @return [type]        [description]
		 */
		public function import_items( $items = array(), $group = 'global' ) {

			if ( empty( $items ) ) {
				return;
			}

			if ( empty( $this->log[ $group ] ) ) {
				$this->log[ $group ] = array();
			}

			foreach ( $items as $item ) {

				$item['post_status'] = 'publish';
				$content = ! empty( $item['meta_input']['_elementor_data'] ) ? $item['meta_input']['_elementor_data'] : '';
				$old_post_id = isset( $item['ID'] ) ? $item['ID'] : false;

				if ( $old_post_id ) {
					unset( $item['ID'] );
				}

				if ( ! empty( $item['meta_input']['_elementor_page_settings']['jet_popup_use_ajax'] ) ) {
					$item['meta_input']['_elementor_page_settings']['jet_popup_use_ajax'] = '';
				}

				$form_data          = false;
				$preset             = false;
				$notifications_data = false;

				if ( ! empty( $item['meta_input']['_form_data'] ) ) {
					$form_data = wp_slash( $item['meta_input']['_form_data'] );
					unset( $item['meta_input']['_form_data'] );
				}

				if ( ! empty( $item['meta_input']['_preset'] ) ) {
					$preset = $item['meta_input']['_preset'];
					unset( $item['meta_input']['_preset'] );
				}

				if ( ! empty( $item['meta_input']['_notifications_data'] ) ) {
					$notifications_data = wp_slash( $item['meta_input']['_notifications_data'] );
					unset( $item['meta_input']['_notifications_data'] );
				}

				$new_post_id = wp_insert_post( $item );

				if ( ! $new_post_id ) {
					continue;
				}

				if ( $form_data ) {
					update_post_meta( $new_post_id, '_form_data', $form_data );
				}

				if ( $preset ) {
					update_post_meta( $new_post_id, '_preset', $preset );
				}

				if ( $notifications_data ) {
					update_post_meta( $new_post_id, '_notifications_data', $notifications_data );
				}

				$this->log[ $group ][ $old_post_id ] = $new_post_id;

				if ( $content && class_exists( '\Elementor\Plugin' ) ) {

					$content  = json_decode( $content, true );
					$content  = $this->process_import_content( $content );
					$post_id  = $new_post_id;
					$document = \Elementor\Plugin::$instance->documents->get( $post_id );

					if ( $document ) {
						$content = $document->get_elements_raw_data( $content, true );
					}

					update_post_meta( $post_id, '_elementor_data', wp_slash( json_encode( $content ) ) );

				}

			}

		}

		/**
		 * Process content for export/import.
		 *
		 * @param array  $content A set of elements.
		 *
		 * @return mixed Processed content data.
		 */
		protected function process_import_content( $content ) {
			return \Elementor\Plugin::$instance->db->iterate_data(
				$content,
				function( $element_data ) {

					$element = \Elementor\Plugin::$instance->elements_manager->create_element_instance( $element_data );

					// If the widget/element isn't exist, like a plugin that creates a widget but deactivated
					if ( ! $element ) {
						return null;
					}

					return $this->process_element_import_content( $element );
				}
			);
		}

		/**
		 * Process single element content for export/import.
		 *
		 * @param Controls_Stack $element
		 *
		 * @return array Processed element data.
		 */
		protected function process_element_import_content( $element ) {

			$element_data = $element->get_data();

			if ( method_exists( $element, 'on_import' ) ) {
				// TODO: Use the internal element data without parameters.
				$element_data = $element->on_import( $element_data );
			}

			foreach ( $element->get_controls() as $control ) {

				$control_class = \Elementor\Plugin::$instance->controls_manager->get_control( $control['type'] );

				// If the control isn't exist, like a plugin that creates the control but deactivated.
				if ( ! $control_class ) {
					return $element_data;
				}

				if ( method_exists( $control_class, 'on_import' ) ) {
					$element_data['settings'][ $control['name'] ] = $control_class->on_import(
						$element->get_settings( $control['name'] ),
						$control
					);
				}

			}

			return $element_data;

		}

		public function remap_forms_ids( $elementor_data ) {

			return preg_replace_callback(
				'/(_form_id)[\'\"]:[\'\"](\d+)/',
				function( $matches ) {

					if ( empty( $matches[2] ) ) {
						return $matches[0];
					}

					$log    = ! empty( $this->log['forms'] ) ? $this->log['forms'] : array();
					$new_id = ! empty( $log[ $matches[2] ] ) ? $log[ $matches[2] ] : false;

					if ( ! $new_id ) {
						return $matches[0];
					} else {
						return str_replace( $matches[2], $new_id, $matches[0] );
					}

				},
				$elementor_data
			);

		}

		public function remap_listing_ids( $elementor_data ) {

			return preg_replace_callback(
				'/(lisitng_id)[\'\"]:[\'\"](\d+)/',
				function( $matches ) {

					if ( empty( $matches[2] ) ) {
						return $matches[0];
					}

					$log    = ! empty( $this->log['listings'] ) ? $this->log['listings'] : array();
					$new_id = ! empty( $log[ $matches[2] ] ) ? $log[ $matches[2] ] : false;

					if ( ! $new_id ) {
						return $matches[0];
					} else {
						return str_replace( $matches[2], $new_id, $matches[0] );
					}

				},
				$elementor_data
			);

		}

		public function profile_user_preset() {

			$forms      = ! empty( $this->log['forms'] ) ? $this->log['forms'] : array();
			$templates  = ! empty( $this->log['templates'] ) ? $this->log['templates'] : array();
			$subpage_id = null;

			foreach ( $templates as $old_id => $new_id ) {

				$elementor_data = get_post_meta( $new_id, '_elementor_data', true );

				if ( ! empty( $elementor_data ) ) {

					if ( ! empty( $forms ) ) {
						$elementor_data = $this->remap_forms_ids( $elementor_data );
					}

					update_post_meta( $new_id, '_elementor_data', wp_slash( $elementor_data ) );

				}

				$subpage_id = $new_id;

			}

			$options     = get_option( 'profile-builder' );
			$new_subpage = array(
				'title'     => __( 'User Settings', 'jet-engine' ),
				'slug'      => 'user-settings',
				'template'  => array( $subpage_id ),
				'collapsed' => true,
			);

			if ( ! empty( $options ) ) {

				if ( ! empty( $options['account_page_structure'] ) ) {
					$options['account_page_structure'][] = $new_subpage;

				} else {
					$options['account_page_structure'] = array( $new_subpage );
				}

			} else {
				$options = array(
					'account_page_structure' => array( $new_subpage ),
				);
			}

			update_option( 'profile-builder', $options, false );

			flush_rewrite_rules( true );

		}

		/**
		 * Handle profile related presets import
		 * @return [type] [description]
		 */
		public function profile_preset() {

			$forms     = ! empty( $this->log['forms'] ) ? $this->log['forms'] : array();
			$templates = ! empty( $this->log['templates'] ) ? $this->log['templates'] : array();
			$listings  = ! empty( $this->log['listings'] ) ? $this->log['listings'] : array();
			$pages     = ! empty( $this->log['pages'] ) ? $this->log['pages'] : array();

			foreach ( $templates as $old_id => $new_id ) {

				$elementor_data = get_post_meta( $new_id, '_elementor_data', true );

				if ( ! empty( $elementor_data ) ) {

					if ( ! empty( $forms ) ) {
						$elementor_data = $this->remap_forms_ids( $elementor_data );
					}

					if ( ! empty( $listings ) ) {
						$elementor_data = $this->remap_listing_ids( $elementor_data );
					}

					update_post_meta( $new_id, '_elementor_data', wp_slash( $elementor_data ) );

				}

			}

			foreach ( $forms as $old_id => $new_id ) {

				$notifications_data = get_post_meta( $new_id, '_notifications_data', true );
				$notifications_data = wp_unslash( $notifications_data );

				if ( ! empty( $pages ) ) {
					$notifications_data = preg_replace_callback(
						'/(redirect_page)[\'\"]:[\'\"](\d+)/',
						function( $matches ) {

							if ( empty( $matches[2] ) ) {
								return $matches[0];
							}

							$log    = ! empty( $this->log['pages'] ) ? $this->log['pages'] : array();
							$new_id = ! empty( $log[ $matches[2] ] ) ? $log[ $matches[2] ] : false;

							if ( ! $new_id ) {
								return $matches[0];
							} else {
								return str_replace( $matches[2], $new_id, $matches[0] );
							}

						},
						$notifications_data
					);
				}

				update_post_meta( $new_id, '_notifications_data', wp_slash( $notifications_data ) );

			}

			$options    = get_option( 'profile-builder' );
			$pages_keys = array(
				'account_page',
				'users_page',
				'single_user_page'
			);

			$templates_keys = array(
				'account_page_structure',
				'user_page_structure',
			);

			if ( ! empty( $options ) ) {

				foreach ( $options as $option => $value ) {

					if ( in_array( $option, $pages_keys ) && ! empty( $value ) ) {
						if ( isset( $pages[ $value ] ) ) {
							$options[ $option ] = $pages[ $value ];
						}

					} elseif ( in_array( $option, $templates_keys ) ) {

						if ( is_array( $value ) ) {

							$new_value = array();

							foreach ( $value as $subpage ) {

								if ( ! empty( $subpage['template'] ) ) {
									$tid = $subpage['template'][0];
									$subpage['template'] = ! empty( $templates[ $tid ] ) ? array( $templates[ $tid ] ) : $subpage['template'];
								}

								$new_value[] = $subpage;

							}

							$options[ $option ] = $new_value;

						}

					}
				}

				update_option( 'profile-builder', $options, false );

			}

			flush_rewrite_rules( true );

		}

		/**
		 * Returns remote preset content
		 *
		 * @return [type] [description]
		 */
		public function get_remote_preset( $preset ) {

			$presets     = $this->get_presets();
			$preset_data = ! empty( $presets[ $preset ] ) ? $presets[ $preset ] : null;

			if ( ! $preset_data ) {
				return;
			}

			$url = $preset_data['url'];

			if ( ! $url ) {
				return false;
			}

			$response = wp_remote_get( $url, array(
				'timeout'   => 60,
				'sslverify' => false
			) );

			if ( is_wp_error( $response ) ) {
				return false;
			}

			$body = wp_remote_retrieve_body( $response );
			return json_decode( $body, true );

		}

		/**
		 * Returns remote preset content
		 *
		 * @return [type] [description]
		 */
		public function get_preset_handler( $preset ) {

			$presets     = $this->get_presets();
			$preset_data = ! empty( $presets[ $preset ] ) ? $presets[ $preset ] : null;

			if ( ! $preset_data ) {
				return false;
			}

			$handler = ! empty( $preset_data['handler'] ) ? $preset_data['handler'] : false;

			if ( ! $handler || ! is_callable( $handler ) ) {
				return false;
			}

			return $handler;

		}

		/**
		 * Register presets configuration
		 *
		 * @return [type] [description]
		 */
		public function presets_config() {
			wp_localize_script(
				'jet-engine-dashboard-skins',
				'JetEnginePresetsConfig',
				$this->get_presets( true )
			);
		}

		/**
		 * Get presets
		 *
		 * @return [type] [description]
		 */
		public function get_presets( $for_js = false ) {

			return apply_filters( 'jet-engine/dashborad/presets', array(
				'profile-posts-edit' => array(
					'url'         => 'https://account.crocoblock.com/free-download/presets/profile-posts-edit.json',
					'title'       => __( 'User profile with editable content', 'jet-engine' ),
					'desc'        => __( 'Configure user profile module to allow registered users publish and edit posts', 'jet engine' ),
					'handler'     => ( ! $for_js ) ? array( $this, 'profile_preset' ) : '',
					'deps'        => array( 'booking-forms', 'profile-builder' ),
					'success_msg' => sprintf( __( '<p>This preset is set Account page and created 3 subpages for account page:</p><ol style="margin-top: -8px;"><li>Main (with posts created by current user),</li><li>Edit post</li><li>New post.</li></ol><p>To finalize the process you need to go to <a href="%1$s">Permalink Settings</a> and re-save permalinks structure (just click on "Save Changes" button).', 'jet-engine' ), admin_url( 'options-permalink.php' ) ),
				),
				'profile-user-edit' => array(
					'url'         => 'https://account.crocoblock.com/free-download/presets/profile-user-edit.json',
					'title'       => __( 'Editable user settings page for user profile', 'jet-engine' ),
					'desc'        => __( 'Adds user settings subpage to profile builder with edit user form', 'jet engine' ),
					'handler'     => ( ! $for_js ) ? array( $this, 'profile_user_preset' ) : '',
					'deps'        => array( 'booking-forms', 'profile-builder' ),
					'success_msg' => __( '<p>This preset added User Settings subpage for account page and Edit User form with apropriate notification.', 'jet-engine' ),
				),
			) );

		}

		/**
		 * Export component template
		 *
		 * @return void
		 */
		public function print_templates() {

			ob_start();
			include jet_engine()->get_template( 'admin/pages/dashboard/presets.php' );
			$content = ob_get_clean();

			printf( '<script type="text/x-template" id="jet_engine_skins_presets">%s</script>', $content );

		}

	}

}
