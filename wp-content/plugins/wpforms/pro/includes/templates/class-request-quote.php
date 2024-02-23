<?php

/**
 * Request A Quote form template.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
class WPForms_Template_Request_Quote extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name        = esc_html__( 'Request A Quote Form', 'wpforms' );
		$this->slug        = 'request-quote';
		$this->description = esc_html__( 'Start collecting leads with this pre-made Request a quote form. You can add and remove fields as needed.', 'wpforms' );
		$this->includes    = '';
		$this->icon        = '';
		$this->modal       = '';
		$this->core        = true;
		$this->data        = array(
			'field_id' => '5',
			'fields'   => array(
				'0' => array(
					'id'       => '0',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms' ),
					'required' => '1',
					'size'     => 'medium',
				),
				'1' => array(
					'id'       => '1',
					'type'     => 'text',
					'label'    => esc_html__( 'Business / Organization', 'wpforms' ),
					'required' => '1',
					'size'     => 'medium',
				),
				'2' => array(
					'id'       => '2',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms' ),
					'required' => '1',
					'size'     => 'medium',
				),
				'3' => array(
					'id'       => '3',
					'type'     => 'phone',
					'label'    => esc_html__( 'Phone', 'wpforms' ),
					'format'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				'4' => array(
					'id'       => '4',
					'type'     => 'textarea',
					'label'    => esc_html__( 'Request', 'wpforms' ),
					'required' => '1',
					'size'     => 'medium',
				),
			),
			'settings' => array(
				'honeypot'                    => '1',
				'confirmation_message_scroll' => '1',
				'submit_text_processing'      => esc_html__( 'Sending...', 'wpforms' ),
			),
			'meta'     => array(
				'template' => $this->slug,
			),
		);
	}
}

new WPForms_Template_Request_Quote;
