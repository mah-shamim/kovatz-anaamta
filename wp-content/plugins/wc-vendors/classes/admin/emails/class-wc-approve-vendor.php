<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Approve_Vendor Class.
 *
 * Notifies the vendor that their application has been approved, denied, or is pending.
 *
 * @class    WC_Email_Approve_Vendor
 * @version  2.4.8
 * @since   2.4.8
 */
class WC_Email_Approve_Vendor extends WC_Email {

	/**
	 * User
	 *
	 * @var $object wp_user
	 */
	public $user;

	/**
	 * Status
	 *
	 * @var string
	 */
	public $status;

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->id    = 'vendor_application';
		$this->title = sprintf(
			/* translators: %s: vendor name */
            __( '%s Application - deprecated', 'wc-vendors' ),
            wcv_get_vendor_name()
        );
		$deprecated_message = __( 'This email has been deprecated.', 'wc-vendors' );
		$this->description  = sprintf(
			/* translators: %1$s: vendor name, %2$s deprecated */
            __( '%1$s application will either be approved, denied, or pending. %2$s', 'wc-vendors' ),
            wcv_get_vendor_name(),
			'<strong>' . $deprecated_message . '</strong>'
        );

		$this->heading = __( 'Application {status}', 'wc-vendors' );
		$this->subject = sprintf(
			/* translators: %s: vendor name */
            __( '[{blogname}] Your %s application has been {status}', 'wc-vendors' ),
            wcv_get_vendor_name( true, false )
        );

		$this->template_base  = dirname( dirname( dirname( __DIR__ ) ) ) . '/templates/emails/';
		$this->template_html  = 'application-status.php';
		$this->template_plain = 'application-status.php';

		// Call parent constuctor.
		parent::__construct();

		// Other settings.
		$this->recipient = $this->get_option( 'recipient' );

		if ( ! $this->recipient ) {
			$this->recipient = get_option( 'admin_email' );
		}
	}

	/**
	 * Trigger function.
	 *
	 * @access public
	 * @return void
	 *
	 * @param int    $user_id The User ID.
	 * @param string $status  The status.
	 */
	public function trigger( $user_id, $status ) {

		if ( ! $this->is_enabled() ) {
			return;
		}

		$this->find[]    = '{status}';
		$this->replace[] = $status;

		$this->status = $status;

		$this->user = get_userdata( $user_id );
		$user_email = $this->user->user_email;

		$this->send( $user_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

		if ( 'pending' === $status ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}
	}

	/**
	 * Get html content.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {

		ob_start();
		wc_get_template(
			$this->template_html,
            array(
				'status'        => $this->status,
				'user'          => $this->user,
				'email_heading' => $this->get_heading(),
            ),
            'woocommerce',
            $this->template_base
		);

		return ob_get_clean();
	}


	/**
	 * Get plain content.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_plain() {

		ob_start();
		wc_get_template(
			$this->template_plain,
            array(
				'status'        => $this->status,
				'user'          => $this->user,
				'email_heading' => $this->get_heading(),
            ),
            'woocommerce',
            $this->template_base
		);

		return ob_get_clean();
	}


	/**
	 * Initialise Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', 'wc-vendors' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'wc-vendors' ),
				'default' => 'no',
			),
			'recipient'  => array(
				'title'       => __( 'Recipient(s)', 'woocommerce' ),
				'type'        => 'text',
				'description' => sprintf(
					/* translators: %s: admin email */
                    __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wc-vendors' ),
                    '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>'
                ),
				'placeholder' => '',
				'default'     => '',
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'wc-vendors' ),
				'type'        => 'text',
				'description' => sprintf(
					/* translators: %s email subject */
                    __( 'This controls the email subject line. Leave blank to use the default subject: %s.', 'wc-vendors' ),
                    '<code>' . $this->subject . '</code>'
                ),
				'placeholder' => '',
				'default'     => '',
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'wc-vendors' ),
				'type'        => 'text',
				'description' => sprintf(
					/* translators: %s email heading */
                    __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: %s.', 'wc-vendors' ),
                    '<code>' . $this->heading . '</code>'
                ),
				'placeholder' => '',
				'default'     => '',
			),
			'email_type' => array(
				'title'       => __( 'Email type', 'wc-vendors' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'wc-vendors' ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => __( 'Plain text', 'wc-vendors' ),
					'html'      => __( 'HTML', 'wc-vendors' ),
					'multipart' => __( 'Multipart', 'wc-vendors' ),
				),
			),
		);
	}
}
