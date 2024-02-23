<?php
/**
 * Defines the WC_Order_Vendor class
 */

/**
 * Order vendor_order
 *
 * Class responsible for creating and managing vendor_order objects.
 *
 * @class    WC_Order_Vendor
 */
class WC_Order_Vendor extends WC_Order {

    /**
     * The order type
     *
     * @var string
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    const ORDER_TYPE = 'shop_order_vendor';

    /**
     * The parent order object.
     *
     * @var WC_Order
     * @version 2.4.8
     * @since   2.4.8
     */
    protected $parent_order;

    /**
     * The order type
     *
     * @var string
     * @version 2.4.8
     */
    protected $order_type = 'shop_order_vendor';

    /**
     * The order date
     *
     * @var string
     * @version 2.4.8
     */
    public $date;

    /**
     * The date the order was modified.
     *
     * @var string
     * @version 2.4.8
     * @since   2.4.8
     */
    public $modified_date;

    /**
     * The reason
     *
     * @var string
     * @version 2.4.8
     * @since   2.4.8
     */
    public $reason;

    /**
     * Extra data for this object. Name value pairs (name + default value).
     *
     * WC 3.0+ property.
     *
     * @var array
     */
    protected $extra_data = array(
        // Extra data with getters/setters.
        'wcv_vendor_id'   => 0,
        'wcv_commission'  => 0,
        'wcv_product_ids' => array(),
    );

    /**
     * Construct an instance of this class.
     *
     * @param string|int|object|WC_Order_Vendor $vendor_order Vendor Order to init.
     * @param int|WP_User                       $vendor       Vendor ID or WP_User object.
     * @version 2.0.0
     * @since   2.4.8 - Added method params
     */
    public function __construct( $vendor_order = 0, $vendor = null ) {

        if ( is_numeric( $vendor_order ) && $vendor_order > 0 ) {
            $this->set_id( absint( $vendor_order ) );
        } elseif ( $vendor_order instanceof WC_Order_Vendor ) {
            $this->set_id( absint( $vendor_order->get_id() ) );
        } elseif ( isset( $vendor_order->ID ) ) {
            $this->set_id( absint( $vendor_order->ID ) );
        }

        if ( is_numeric( $vendor ) && $vendor > 0 ) {
            $this->set_vendor_id( $vendor );
        } elseif ( is_a( $vendor, 'WP_User' ) ) {
            $this->set_vendor_id( $vendor->ID );
        }

        parent::__construct( $vendor_order );

        if ( $this->get_id() > 0 ) {
            $this->get_parent_order();
            $this->set_vendor_id( $this->get_meta( 'wcv_vendor_id', true ) );
            $this->set_vendor_product_ids( $this->get_meta( 'wcv_product_ids', true ) );
            $this->set_commission( $this->get_meta( 'wcv_commission', true ) );
        }
    }

    /**
     * Populates a vendor_order from the loaded post data
     *
     * @param mixed $result The result from retrieving the post.
     * @since   2.2
     * @version 2.4.8
     */
    public function populate( $result ) {
        // Standard post data.
        if ( ! wcv_hpos_enabled() ) {
            $this->set_id( $result->ID );
            $this->set_prop( 'date', $result->post_date );
            $this->set_modified_date( $result->post_modified );
            $this->reason = $result->post_excerpt;
        } else {
            $this->set_id( $result->get_id() );
            $this->set_date( $result->get_date_created() );
            $this->set_modified_date( $result->get_date_modified() );
            $this->set_reason( $result->get_customer_note() );
        }
    }

    /**
     * Getters
     */

    /**
     * Get internal type.
     *
     * @since   2.4.8 - Added.
     * @version 2.4.8
     *
     * @return string
     */
    public function get_type() {
        return self::ORDER_TYPE;
    }

    /**
     * Get vendor id.
     *
     * @since   2.4.8 - Added.
     * @version 2.4.8
     *
     * @param string $context Context, view or edit.
     *
     * @return mixed
     */
    public function get_vendor_id( $context = 'view' ) {
        return $this->get_prop( 'wcv_vendor_id', $context );
    }

    /**
     * Get the date the order was created.
     *
     * @param string $context The context. view or edit.
     * @return string
     * @version 2.4.8
     * @since   2.4.8 - Added
     */
    public function get_date( $context = 'view' ) {
        return $this->get_prop( 'date', $context );
    }

    /**
     * Get the date the order was last modified.
     *
     * @param string $context The context. view or edit.
     * @return string
     * @version 2.4.8
     * @since   2.4.8 - Added
     */
    public function get_modified_date( $context = 'view' ) {
        return $this->get_prop( 'modified_date', $context );
    }

    /**
     * Get the customer note
     *
     * @param string $context The context. view or edit.
     * @return string
     * @version 2.4.8
     * @since   2.4.8
     */
    public function get_reason( $context = 'view' ) {
        return $this->get_prop( 'reason', $context );
    }

    /**
     * Get order commission
     *
     * @param string $context The context. view or edit.
     * @return number
     * @version 1.4.8
     * @since   1.4.8 - Added.
     */
    public function get_commission( $context = 'view' ) {
        return $this->get_prop( 'wcv_commission', $context );
    }

    /**
     * Get order item IDs
     *
     * @param string $context The context. view or edit.
     * @return array[int]
     * @version 2.4.8
     * @since   2.4.8
     */
    public function get_vendor_product_ids( $context = 'view' ) {
        return $this->get_prop( 'wcv_product_ids', $context );
    }

    /**
     * Get parent order of vendor order.
     *
     * @since   2.4.8 - Added.
     * @version 2.4.8
     *
     * @return WC_Order
     */
    public function get_parent_order() {
        if ( ! is_object( $this->parent_order ) ) {
            $this->parent_order = wc_get_order( $this->get_parent_id() );
        }

        return $this->parent_order;
    }

    /**
     * Setters
     *
     * Methods to set data in the order object, bypassing the setter methods so data is not escaped or trigger actions.
     */

    /**
     * Set vendor id.
     *
     * @since   2.4.8 - Added.
     * @version 2.4.8
     *
     * @param int $vendor_id Vendor ID.
     */
    public function set_vendor_id( $vendor_id ) {
        $this->set_prop( 'wcv_vendor_id', $vendor_id );
    }

    /**
     * Set the order date.
     *
     * @param string $date The date the order was created.
     * @return void
     * @version 2.4.8
     * @since   2.4.8 - Added
     */
    public function set_date( $date ) {
        $this->set_prop( 'date', $date );
    }

    /**
     * Set the date the order was last modified.
     *
     * @param string $date The date the order was last modified.
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function set_modified_date( $date ) {
        $this->set_prop( 'modified_date', $date );
    }

    /**
     * Set the customer note
     *
     * @param string $reason The customer note.
     * @return void
     * @version 2.4.8
     * @since   2.4.8
     */
    public function set_reason( $reason ) {
        $this->set_prop( 'reason', $reason );
    }

    /**
     * Set order item ids.
     *
     * @param array[int] $product_ids The order product ids.
     * @return void
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public function set_vendor_product_ids( $product_ids ) {
        $this->set_prop( 'wcv_product_ids', $product_ids );
    }

    /**
     * Set order commission
     *
     * @param number $commission The order commission paid|due to vendor.
     * @return void
     * @version 2.4.8
     * @since   2.4.8 - Added.
     */
    public function set_commission( $commission ) {
        $this->set_prop( 'wcv_commission', $commission );
    }

    /**
     * Set parent order of vendor order.
     *
     * @since   2.4.8 - Added.
     * @version 2.4.8
     *
     * @param WC_Order $order WC Order object.
     */
    public function set_parent_order( $order ) {
        $this->parent_order = $order;
    }

    /**
     * Vendor order doesn't need payment.
     *
     * @since   2.4.8 - Added.
     * @version 2.4.8
     *
     * @return bool
     */
    public function needs_payment() {
        return false;
    }

    /**
     * We don't process order item of vendor order.
     *
     * @since   2.4.8 - Added.
     * @version 2.4.8
     *
     * @return bool
     */
    public function needs_processing() {
        return false;
    }

    /**
     * New order email sent
     *
     * Always true because we do not want to send email if sub order
     *
     * @param string $context Context - default is view.
     *
     * @return boolean
     */
    public function get_new_order_email_sent( $context = 'view' ) {
        return true;
    }
}
