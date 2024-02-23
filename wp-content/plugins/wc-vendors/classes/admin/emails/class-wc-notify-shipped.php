<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Email_Notify_Shipped Class
 *
 * Notifies the vendor that their order has been shipped.
 *
 * @class    WC_Email_Notify_Shipped
 * @version  2.4.8
 * @since    2.4.8
 */
class WC_Email_Notify_Shipped extends WC_Email {

	/**
	 * Current Vendor
	 *
	 * @var unknown
	 */
	public $current_vendor;


	/**
	 * Constructor
	 */
	public function __construct() {

		$this->id    = 'vendor_notify_shipped';
		$this->title = sprintf(
			/* translators: %s vendor name */
            __( '%s has shipped - deprecated', 'wc-vendors' ),
            wcv_get_vendor_name()
        );
		$deprecated_message = __( 'This email has been deprecated.', 'wc-vendors' );
		$this->description  = sprintf(
			/* translators: %1$s vendor name, %2$s deprecated */
            __( 'An email is sent when a %1$s has marked one of their orders as shipped. %2$s', 'wc-vendors' ),
            wcv_get_vendor_name( true, false ),
			'<strong>' . $deprecated_message . '</strong>'
        );

		$this->heading = __( 'Your order has been shipped', 'wc-vendors' );
		$this->subject = __( '[{blogname}] Your order has been shipped ({order_number}) - {order_date}', 'wc-vendors' );

		$this->template_html  = 'notify-vendor-shipped.php';
		$this->template_plain = 'notify-vendor-shipped.php';
		$this->template_base  = dirname( dirname( dirname( __DIR__ ) ) ) . '/templates/emails/';
		$this->recipient      = '';

		// Call parent constuctor.
		parent::__construct();
	}


	/**
	 * Trigger function.
	 *
	 * @access public
	 * @return void
	 *
	 * @param unknown $order_id Order ID.
	 * @param unknown $vendor_id Vendor ID.
	 */
	public function trigger( $order_id, $vendor_id ) {

		$this->object         = wc_get_order( $order_id );
		$this->current_vendor = $vendor_id;
		$order_date           = $this->object->get_date_created();

		$this->find[]    = '{order_date}';
		$this->replace[] = date_i18n( wc_date_format(), strtotime( $order_date ) );

		$this->find[]    = '{order_number}';
		$this->replace[] = $this->object->get_order_number();

		$billing_email = $this->object->get_billing_email();

		if ( ! $this->is_enabled() ) {
			return;
		}

		add_filter( 'woocommerce_order_get_items', array( $this, 'check_items' ), 10, 2 );
		add_filter( 'woocommerce_get_order_item_totals', array( $this, 'check_order_totals' ), 10, 2 );
		$this->send( $billing_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		remove_filter( 'woocommerce_get_order_item_totals', array( $this, 'check_order_totals' ), 10, 2 );
		remove_filter( 'woocommerce_order_get_items', array( $this, 'check_items' ), 10, 2 );
	}


	/**
	 * Check items.
	 *
	 * @param unknown $items Items.
	 * @param unknown $order Order.
	 *
	 * @return unknown
	 */
	public function check_items( $items, $order ) {

		foreach ( $items as $key => $product ) {

			if ( empty( $product['product_id'] ) ) {
				unset( $items[ $key ] );
				continue;
			}

			$author = WCV_Vendors::get_vendor_from_product( $product['product_id'] );

			if ( $this->current_vendor != $author ) { //phpcs:ignore
				unset( $items[ $key ] );
				continue;
			}
		}

		return $items;
	}

	/**
	 * Check order total.
	 *
	 * @param unknown $total_rows Total rows.
	 * @param unknown $order Order.
	 *
	 * @return unknown
	 */
	public function check_order_totals( $total_rows, $order ) {

		$return['cart_subtotal']          = $total_rows['cart_subtotal'];
		$return['cart_subtotal']['label'] = __( 'Subtotal:', 'wc-vendors' );

		return $return;
	}

	/**
	 * Get HTMl content.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {

		ob_start();
		wc_get_template(
			$this->template_html,
            array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
            ),
            'woocommerce/emails',
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
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
            ),
            'woocommerce/emails',
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
			'subject'    => array(
				'title'       => __( 'Subject', 'wc-vendors' ),
				'type'        => 'text',
				'description' => sprintf(
					/* translators: %s Email subject */
                    __( 'This controls the email subject line. Leave blank to use the default subject: %s.', 'wc-vendors' ),
                    '<code>' . $this->subject . '</code>'
                ),
				'placeholder' => '',
				'default'     => '',
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', 'wc-vendors' ),
				'type'        => 'text',
				'description' => sprintf( /* translators: %s Email heading */
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
