<?php

/**
 * Donation form template.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
class WPForms_Template_Donation extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name        = esc_html__( 'Donation Form', 'wpforms' );
		$this->slug        = 'donation';
		$this->description = esc_html__( 'Start collecting donation payments on your website with this ready-made Donation form. You can add and remove fields as needed.', 'wpforms' );
		$this->includes    = '';
		$this->icon        = '';
		$this->core        = true;
		$this->modal       = array(
			'title'   => esc_html__( 'Don&#39;t Forget', 'wpforms' ),
			'message' => esc_html__( 'Click the Payments tab to configure your payment provider', 'wpforms' ),
		);
		$this->data        = array(
			'field_id' => '4',
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
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms' ),
					'required' => '1',
					'size'     => 'medium',
				),
				'2' => array(
					'id'       => '2',
					'type'     => 'payment-single',
					'label'    => esc_html__( 'Donation Amount', 'wpforms' ),
					'format'   => 'user',
					'required' => '1',
					'size'     => 'medium',
				),
				'3' => array(
					'id'       => '3',
					'type'     => 'textarea',
					'label'    => esc_html__( 'Comment or Message', 'wpforms' ),
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

	/**
	 * Conditional to determine if the template informational modal screens
	 * should display.
	 *
	 * @since 1.0.0
	 *
	 * @param array $form_data Form data and settings.
	 *
	 * @return boolean
	 */
	public function template_modal_conditional( $form_data ) {

		// If we do not have payment data, then we can assume a payment
		// method has not yet been configured, so we display the modal to
		// remind the user they need to set it up for the form to work
		// correctly.
		if ( empty( $form_data['payments'] ) ) {
			return true;
		} else {
			return false;
		}
	}
}

new WPForms_Template_Donation;
