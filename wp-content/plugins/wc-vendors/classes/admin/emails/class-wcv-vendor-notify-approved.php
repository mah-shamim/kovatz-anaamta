<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WCVendors_Vendor_Notify_Approved' ) ) :

	/**
	 * Notify vendor application approved.
	 *
	 * An email sent to the vendor when their application has been approved.
	 *
	 * @class       WCV_Notify_Vendor_Application
	 * @version     2.0.0
	 * @package     Classes/Admin/Emails
	 * @author      WC Vendors
	 * @extends     WC_Email
	 */
	class WCVendors_Vendor_Notify_Approved extends WC_Email {

		/**
		 * User
		 *
		 * @var WP_User
		 */
		public $user;

		/**
		 * Status
		 *
		 * @var string
		 */
		public $status;

		/** User email
		 *
		 * @var string
		 */
		public $user_email;

		/**
		 * Content
		 *
		 * @var string
		 */
		public $content;

		/**
		 * Constructor.
		 */
		public function __construct() {

			$this->id    = 'vendor_notify_approved';
			$this->title = sprintf(
				/* translators: %s vendor name */
                __( '%s notify approved', 'wc-vendors' ),
                wcv_get_vendor_name()
            );
			$this->description = sprintf(
				/* translators: %s vendor name */
                __( 'Notification is sent to the %s that their application has been approved', 'wc-vendors' ),
                wcv_get_vendor_name( true, false )
            );
			$this->template_html  = 'emails/vendor-notify-approved.php';
			$this->template_plain = 'emails/plain/vendor-notify-approved.php';
			$this->template_base  = dirname( dirname( dirname( __DIR__ ) ) ) . '/templates/';
			$this->placeholders   = array(
				'{site_title}' => $this->get_blogname(),
			);
			$this->recipient      = '';

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Get email subject.
		 *
		 * @since  2.0.0
		 * @return string
		 */
		public function get_default_subject() {

			return sprintf(
				/* translators: %s vendor name */
                __( '[{site_title}] Your %s application has been approved', 'wc-vendors' ),
                wcv_get_vendor_name( true, false )
            );
		}

		/**
		 * Get email heading.
		 *
		 * @since  2.0.0
		 * @return string
		 */
		public function get_default_heading() {

			return sprintf(
				/* translators: %s vendor name */
                __( '%s Application Approved', 'wc-vendors' ),
                wcv_get_vendor_name()
            );
		}

		/**
		 * Get email content
		 *
		 * @since  2.0.0
		 * @return string
		 */
		public function get_default_content() {

			return sprintf(
				/* translators: %s vendor name */
                __( 'Your application to become a %s has been approved.', 'wc-vendors' ),
                wcv_get_vendor_name( true, false )
            );
		}

		/**
		 * Trigger the sending of this email.
		 *
		 * @param int    $vendor_id The vendor ID.
		 * @param string $status status of the application.
		 */
		public function trigger( $vendor_id, $status = '' ) {

			$this->setup_locale();

			$this->user       = get_userdata( $vendor_id );
			$this->user_email = $this->user->user_email;
			$this->content    = $this->get_option( 'content', $this->get_default_content() );
			$this->status     = $status;

			if ( $this->is_enabled() ) {
				$this->send( $this->user_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Get content html.
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_html() {

			return wc_get_template_html(
				$this->template_html,
                array(
					'order'         => $this->object,
					'email_heading' => $this->get_heading(),
					'sent_to_admin' => true,
					'plain_text'    => false,
					'email'         => $this,
					'user'          => $this->user,
					'content'       => $this->content,
					'status'        => $this->status,
                ),
                'woocommerce',
                $this->template_base
			);
		}

		/**
		 * Get content plain.
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_plain() {

			return wc_get_template_html(
				$this->template_plain,
                array(
					'order'         => $this->object,
					'email_heading' => $this->get_heading(),
					'sent_to_admin' => true,
					'plain_text'    => true,
					'email'         => $this,
					'user'          => $this->user,
					'content'       => $this->content,
					'status'        => $this->status,
				)
			);
		}

		/**
		 * Initialise settings form fields.
		 */
		public function init_form_fields() {

			$this->form_fields = array(
				'enabled'    => array(
					'title'   => __( 'Enable/Disable', 'wc-vendors' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'wc-vendors' ),
					'default' => 'yes',
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'wc-vendors' ),
					'type'        => 'text',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'Available placeholders: %s', 'wc-vendors' ), '<code>{site_title}</code>' ),
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'content'    => array(
					'title'       => __( 'Content', 'wc-vendors' ),
					'type'        => 'textarea',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf(
						/* translators: %s: vendor name */
                        __( 'Email body to be included when sent to the %s.', 'wc-vendors' ),
                        wcv_get_vendor_name( true, false )
                    ),
					'placeholder' => $this->get_default_content(),
					'default'     => '',
				),
				'heading'    => array(
					'title'       => __( 'Email heading', 'wc-vendors' ),
					'type'        => 'text',
					'desc_tip'    => true,
					/* translators: %s: list of placeholders */
					'description' => sprintf( __( 'Available placeholders: %s', 'wc-vendors' ), '<code>{site_title}</code>' ),
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'wc-vendors' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'wc-vendors' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}
	}

endif;
