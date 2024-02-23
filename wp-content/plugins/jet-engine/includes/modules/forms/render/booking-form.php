<?php
namespace Jet_Engine\Forms\Render;

class Booking_Form extends \Jet_Engine_Render_Base {

	private $is_edit_mode = false;

	public function get_name() {
		return 'jet-engine-booking-form';
	}

	public function default_settings() {
		return array(
			'_form_id'         => '',
			'fields_layout'    => 'column',
			'fields_label_tag' => 'div',
			'submit_type'      => 'reload',
			'cache_form'       => false,
			'rows_divider'     => false,
			'required_mark'    => '*',
		);
	}

	public function set_edit_mode( $edit_mode ) {
		$this->is_edit_mode = $edit_mode;
	}

	public function render() {

		$settings = $this->get_settings();
		$form_id  = isset( $settings['_form_id'] ) ? absint( $settings['_form_id'] ) : false;
		$form_id  = apply_filters( 'jet-engine/forms/render/form-id', $form_id );

		jet_engine()->admin_bar->register_post_item( $form_id );

		$custom_form = apply_filters( 'jet-engine/forms/pre-render-form', false, $settings );

		if ( $custom_form ) {
			echo $custom_form;
			return;
		}

		if ( ! $form_id ) {
			_e( 'Please, select form to show', 'jet-engine' );
			return;
		}

		$fields_layout = isset( $settings['fields_layout'] ) ? esc_attr( $settings['fields_layout'] ) : 'column';
		$label_tag     = isset( $settings['fields_label_tag'] ) ? esc_attr( $settings['fields_label_tag'] ) : 'div';
		$required_mark = isset( $settings['required_mark'] ) ? esc_attr( $settings['required_mark'] ) : '';
		$submit_type   = isset( $settings['submit_type'] ) ? esc_attr( $settings['submit_type'] ) : 'reload';
		$rows_divider  = isset( $settings['rows_divider'] ) ? $settings['rows_divider'] : '';
		$cache         = isset( $settings['cache_form'] ) ? $settings['cache_form'] : '';
		$cache         = filter_var( $cache, FILTER_VALIDATE_BOOLEAN );
		$force_update  = ! $cache;
		$rows_divider  = filter_var( $rows_divider, FILTER_VALIDATE_BOOLEAN );
		$messages      = jet_engine()->forms->get_messages_builder( $form_id );
		$builder       = jet_engine()->forms->get_form_builder( $form_id, false, array(
			'fields_layout' => $fields_layout,
			'label_tag'     => $label_tag,
			'rows_divider'  => $rows_divider,
			'required_mark' => $required_mark,
			'submit_type'   => $submit_type,
			'messages'      => $messages,
		) );

		$builder->render_form( $force_update );

		if ( 'ajax' === $submit_type ) {
			$messages->set_is_ajaxified( true );
		}

		$messages->render_messages();

		if ( $this->is_edit_mode ) {
			$messages->render_messages_samples();
		}
	}
}
