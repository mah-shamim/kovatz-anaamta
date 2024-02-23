<?php

/**
 * Billing / Order form template.
 *
 * @package    WPForms
 * @author     WPForms
 * @since      1.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, WPForms LLC
 */
class WPForms_Template_Order extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		$this->name        = esc_html__( 'Billing / Order Form', 'wpforms' );
		$this->slug        = 'order';
		$this->description = esc_html__( 'Collect Payments for product and service orders with this ready-made form template. You can add and remove fields as needed.', 'wpforms' );
		$this->includes    = '';
		$this->icon        = '';
		$this->core        = true;
		$this->modal       = array(
			'title'   => esc_html__( 'Don&#39;t Forget', 'wpforms' ),
			'message' => esc_html__( 'Click the Payments tab to configure your payment provider', 'wpforms' ),
		);
		$this->data        = array(
			'field_id' => '7',
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
					'type'     => 'phone',
					'label'    => esc_html__( 'Phone', 'wpforms' ),
					'format'   => 'us',
					'required' => '1',
					'size'     => 'medium',
				),
				'3' => array(
					'id'              => '3',
					'type'            => 'address',
					'label'           => esc_html__( 'Address', 'wpforms' ),
					'required'        => '1',
					'size'            => 'medium',
					'country_default' => 'US',
				),

				'4' => array(
					'id'       => '4',
					'type'     => 'payment-multiple',
					'label'    => esc_html__( 'Available Items', 'wpforms' ),
					'required' => '1',
					'choices'  => array(
						'1' => array(
							'label' => esc_html__( 'First Item', 'wpforms' ),
							'value' => '$10.00',
						),
						'2' => array(
							'label' => esc_html__( 'Second Item', 'wpforms' ),
							'value' => '$20.00',
						),
						'3' => array(
							'label' => esc_html__( 'Third Item', 'wpforms' ),
							'value' => '$30.00',
						),
					),
				),
				'5' => array(
					'id'    => '5',
					'type'  => 'payment-total',
					'label' => esc_html__( 'Total Amount', 'wpforms' ),
				),
				'6' => array(
					'id'       => '6',
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

new WPForms_Template_Order;
