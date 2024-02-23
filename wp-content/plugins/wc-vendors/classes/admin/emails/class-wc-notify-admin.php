<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Email_Notify_Admin Class.
 *
 * Notifies the admin that a new product has been submitted.
 *
 * @class    WC_Email_Notify_Admin
 * @version  2.4.8
 * @since    2.4.8
 */
class WC_Email_Notify_Admin extends WC_Email {

	/**
	 * Product name
	 *
	 * @var string
	 */
	public $product_name;

	/**
	 * Vendor ID.
	 *
	 * @var int
	 */
	public $vendor_id;

	/**
	 * Post ID.
	 *
	 * @var int
	 */
	public $post_id;

	/**
	 * Vendor name.
	 *
	 * @var string
	 */
	public $vendor_name;

	/**
	 * Constructor
	 */
	public function __construct() {

		$this->id           = 'admin_new_vendor_product';
		$this->title        = sprintf( /* translators: %s: vendor name */
            __( 'New %s Product - deprecated', 'wc-vendors' ),
            wcv_get_vendor_name()
        );
		$deprecated_message = __( 'This email has been deprecated.', 'wc-vendors' );
		$this->description  = sprintf( /* translators: %1$s: vendor name, %2$s deprecated */
            __( 'New order emails are sent when a new product is submitted by a %1$s. %2$s', 'wc-vendors' ),
            wcv_get_vendor_name( true, false ),
			'<strong>' . $deprecated_message . '</strong>'
        );

		$this->heading = __( 'New product submitted: {product_name}', 'wc-vendors' );
		$this->subject = __( '[{blogname}] New product submitted by {vendor_name} - {product_name}', 'wc-vendors' );

		$this->template_base  = dirname( dirname( dirname( __DIR__ ) ) ) . '/templates/emails/';
		$this->template_html  = 'new-product.php';
		$this->template_plain = 'new-product.php';

		// Triggers for this email.
		add_action( 'pending_product', array( $this, 'trigger' ), 10, 2 );
		add_action( 'pending_product_variation', array( $this, 'trigger' ), 10, 2 );

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
	 * @param unknown $id .
	 * @param unknown $post .
	 */
	public function trigger( $id, $post ) {

		// Ensure that the post author is a vendor.
		if ( ! WCV_Vendors::is_vendor( $post->post_author ) ) {
			return;
		}

		if ( ! $this->is_enabled() ) {
			return;
		}

		$this->find[]       = '{product_name}';
		$this->product_name = $post->post_title;
		$this->replace[]    = $this->product_name;

		$this->find[]      = '{vendor_name}';
		$this->vendor_name = WCV_Vendors::get_vendor_shop_name( $post->post_author );
		$this->replace[]   = $this->vendor_name;

		$this->post_id = $post->ID;

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
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
				'product_name'  => $this->product_name,
				'vendor_name'   => $this->vendor_name,
				'post_id'       => $this->post_id,
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
				'product_name'  => $this->product_name,
				'vendor_name'   => $this->vendor_name,
				'post_id'       => $this->post_id,
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
				'description' => sprintf( /* translators: %s: admin email */
                    __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'wc-vendors' ),
                    '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>'
                ),
				'placeholder' => '',
				'default'     => '',
			),
			'subject'    => array(
				'title'       => __( 'Subject', 'wc-vendors' ),
				'type'        => 'text',
				'description' => sprintf( /* translators: %s subject */
                    __( 'This controls the email subject line. Leave blank to use the default subject: %s.', 'wc-vendors' ),
                    '<code>' . $this->subject . '</code>'
                ),
				'placeholder' => '',
				'default'     => '',
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'wc-vendors' ),
				'type'        => 'text',
				'description' => sprintf( /* translators: %s email headeing */
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
