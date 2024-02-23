<?php
/**
 * Class Vendors_Settings: Manage the settings for vendors.
 */

/**
 * Class Vendors_Settings
 *
 * @since 2.4.8
 * @version 2.4.8
 */
class Vendors_Settings {

    /**
     * Vendor ID
     *
     * @var int
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    protected $vendor_id;

    /**
     * Vendor Settings
     *
     * @var array
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    protected $settings = array();

    /**
     * Limit keys to get
     *
     * @var array
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    protected $limit_keys = array();


    /**
     * Data keys
     *
     * @var array
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    protected $data_keys = array(
        'wp'   => array(
            'display_name' => 'display_name',
            'first_name'   => 'first_name',
            'last_name'    => 'last_name',
            'email'        => 'user_email',
            'registered'   => 'user_registered',
            'roles'        => 'roles',
            'id'           => 'ID',
        ),
        'wcv'  => array(
            'shop_name'             => 'pv_shop_name',
            'seller_info'           => 'pv_seller_info',
            'shop_description'      => 'pv_shop_description',
            'html_enabled'          => 'pv_shop_html_enabled',
            'paypal_email'          => 'pv_paypal',
            'payout_method'         => 'wcv_commission_payout_method',
            'paypal_masspay'        => 'wcv_paypal_masspay_wallet',
            'venmo_id'              => 'wcv_paypal_masspay_venmo_id',
            'bank_account_name'     => 'wcv_bank_account_name',
            'bank_account_number'   => 'wcv_bank_account_number',
            'bank_name'             => 'wcv_bank_name',
            'bank_iban'             => 'wcv_bank_iban',
            'bank_routing_number'   => 'wcv_bank_routing_number',
            'bank_bic'              => 'wcv_bank_bic_swift',
            'stripe_connect_id'     => '_stripe_connect_user_id',
            'store_commission_rate' => 'pv_custom_commission_rate',
            'give_tax'              => 'wcv_give_vendor_tax',
            'give_shipping'         => 'wcv_give_vendor_shipping',
            'vendor_status'         => '_wcv_vendor_status',
        ),
        'wcvp' => array(
            'commission_type'            => '_wcv_commission_type',
            'commission_percent'         => '_wcv_commission_percent',
            'commission_amount'          => '_wcv_commission_amount',
            'commission_fee'             => '_wcv_commission_fee',
            'commission_tiers'           => 'wcv_vendor_commission_tiers',
            'store_url'                  => '_wcv_company_url',
            'store_phone'                => '_wcv_store_phone',
            'store_address'              => '_wcv_store_address1',
            'store_address2'             => '_wcv_store_address2',
            'store_city'                 => '_wcv_store_city',
            'store_postcode'             => '_wcv_store_postcode',
            'store_country'              => '_wcv_store_country',
            'store_state'                => '_wcv_store_state',
            'store_lat'                  => 'wcv_address_latitude',
            'store_lng'                  => 'wcv_address_longitude',
            'verified_vendor'            => '_wcv_verified_vendor',
            'trusted_vendor'             => '_wcv_trusted_vendor',
            'untrusted_vendor'           => '_wcv_untrusted_vendor',
            'lock_new_products'          => '_wcv_lock_new_products_vendor',
            'lock_new_products_msg'      => '_wcv_lock_new_products_vendor_msg',
            'lock_edit_products'         => '_wcv_lock_edit_products_vendor',
            'lock_edit_products_msg'     => '_wcv_lock_edit_products_vendor_msg',
            'show_total_sales'           => '_wcv_show_product_total_sales',
            'vacation_mode'              => '_wcv_vacation_mode',
            'vacation_mode_msg'          => '_wcv_vacation_mode_msg',
            'disable_cart'               => '_wcv_vacation_disable_cart',
            'enabled_store_notice'       => '_wcv_vendor_enable_store_notice',
            'store_notice'               => '_wcv_vendor_store_notice',
            'enable_store_opening_hours' => '_wcv_enable_opening_hours',
            'store_opening_hours'        => 'wcv_store_opening_hours',
            'enable_google_analytics'    => '_wcv_settings_enable_ga_code',
            'google_analytics_id'        => '_wcv_settings_ga_tracking_id',
            'disk_usage'                 => '_wcv_vendor_disk_usage_limit',
            'file_count'                 => '_wcv_vendor_file_count_limit',
            'limit_include_thumbs'       => '_wcv_vendor_upload_limits_include_thumbnails',
            'seo_title'                  => 'wcv_seo_title',
            'seo_meta_description'       => 'wcv_seo_meta_description',
            'seo_meta_keywords'          => 'wcv_seo_meta_keywords',
            'facebook_title'             => 'wcv_seo_fb_title',
            'facebook_description'       => 'wcv_seo_fb_description',
            'facebook_image'             => 'wcv_seo_fb_image_id',
            'twitter_title'              => 'wcv_seo_twitter_title',
            'twitter_description'        => 'wcv_seo_twitter_description',
            'twitter_image'              => 'wcv_seo_twitter_image_id',
            'store_banner'               => '_wcv_store_banner_id',
            'store_icon'                 => '_wcv_store_icon_id',
            'twitter_username'           => '_wcv_twitter_username',
            'facebook_url'               => '_wcv_facebook_url',
            'instagram_username'         => '_wcv_instagram_username',
            'youtube_url'                => '_wcv_youtube_url',
            'pinterest_url'              => '_wcv_pinterest_url',
            'linkedin_url'               => '_wcv_linkedin_url',
            'telegram_username'          => '_wcv_telegram_username',
            'snapchat_username'          => '_wcv_snapchat_username',
            'shipping_flat_rate'         => '_wcv_shipping',
            'shipping_table_rates'       => '_wcv_shipping_rates',
            'privacy_policy'             => 'wcv_policy_privacy',
            'terms_and_conditions'       => 'wcv_policy_terms',
            'shipping_type'              => '_wcv_shipping_type',
        ),
    );

    /**
     * Changes
     *
     * @var array $changes The changes to be made.
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    public $changes = array();

    /**
     * Constructor
     *
     * @param int     $vendor_id (default: false) The vendor ID to load.
     * @param boolean $view (default: true) Whether to load the settings.
     * @access public
     * @return void
     * @throws Exception If the vendor ID is invalid.
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    public function __construct( $vendor_id = false, $view = true ) {
        if ( $vendor_id && ( WCV_Vendors::is_vendor( $vendor_id ) || WCV_Vendors::is_pending( $vendor_id ) ) ) {
            $this->vendor_id = (int) $vendor_id;
            if ( $view ) {
                $this->settings = $this->get_settings();
            }
        } else {
            throw new Exception( esc_html__( 'Invalid vendor ID', 'wc-vendors' ) );
        }
    }

    /**
     * Get magic method
     *
     * @param string $key     The key to get.
     *
     * @access public
     *
     * @return string|null
     * @since 2.4.8
     * @version 2.4.8
     */
    public function __get( $key ) {
        $isset = $this->find_key( $key );
        if ( $isset ) {
            return $this->get_prop( $isset );
        }
        return null;
    }

    /**
     * Set magic method
     *
     * @param string $key     The key to set.
     * @param string $value   The value to set.
     *
     * @access public
     * @return void
     * @since 2.4.8
     * @version 2.4.8
     */
    public function __set( $key, $value ) {
        $isset = $this->find_key( $key );
        if ( $isset ) {
            $this->set_prop( $isset, $value );
        }
    }

    /**
     * Find key in $data_keys
     *
     * @param string $key The key to find.
     *
     * @return string|bool
     * @since 2.4.8
     * @version 2.4.8
     */
    protected function find_key( $key ) {
        $keys = $this->get_keys();
        foreach ( $keys as $section => $fields ) {
            if ( isset( $fields[ $key ] ) ) {
                return $key;
            }
        }
        return false;
    }

    /**
     * Get settings keys
     *
     * @access public
     * @return array
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    public function get_keys() {
        $setting_keys = apply_filters( 'wcvendors_vendor_settings_keys', $this->data_keys );
        return $setting_keys;
    }

    /**
     * Get media fields
     *
     * @access public
     * @return array
     */
    public function get_media_fields() {
        $media_fields = array(
            'store_banner',
            'store_icon',
            'facebook_image',
            'twitter_image',
        );

        return apply_filters( 'wcvendors_vendor_settings_media_fields', $media_fields );
    }

    /**
     * Get the vendor settings
     *
     * @access public
     * @return array
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    public function get_settings() {
        $setting_keys   = $this->get_keys();
        $settings       = array();
        $user_data      = get_userdata( $this->vendor_id );
        $user_meta_data = get_user_meta( $this->vendor_id );

        foreach ( $setting_keys as $section => $fields ) {
            if ( 'wp' === $section ) {
                foreach ( $fields as $field => $meta_key ) {
                    $settings[ $field ] = $user_data->{$meta_key};
                }
            } else {
                foreach ( $fields as $field => $meta_key ) {
                    $meta_value = array_key_exists( $meta_key, $user_meta_data ) ? maybe_unserialize( $user_meta_data[ $meta_key ][0] ) : '';
                    // If pro is not active, set the pro yes/no fields to no.
                    if ( ! is_wcv_pro_active() && 'yes' === $meta_value && 'wcvp' === $section ) {
                        $meta_value = 'no';
                    }
                    $settings[ $field ] = $meta_value;
                }
            }
        }

        $images_fields = $this->get_media_fields();

        foreach ( $images_fields as $field ) {
            if ( isset( $settings[ $field ] ) && ! empty( $settings[ $field ] ) ) {
                $settings[ $field ] = array(
                    'id'  => $settings[ $field ],
                    'url' => wp_get_attachment_url( $settings[ $field ] ),
                );
            } else {
                $settings[ $field ] = array(
                    'id'  => '',
                    'url' => '',
                );
            }
        }

        if ( isset( $settings['shipping_table_rates'] ) && empty( $settings['shipping_table_rates'] ) ) {
            $settings['shipping_table_rates'] = array(
                array(
					'country'      => '',
					'fee'          => '',
					'postcode'     => '',
					'qty_override' => '',
					'region'       => '',
					'state'        => '',
                ),
            );
        }

        if ( isset( $settings['shipping_flat_rate'] ) && empty( $settings['shipping_flat_rate'] ) ) {
            $settings['shipping_flat_rate'] = array(
                'national'                          => '',
                'national_max_charge'               => '',
                'national_min_charge'               => '',
                'national_free_shipping_order'      => '',
                'national_qty_override'             => '',
                'national_free'                     => '',
                'national_disable'                  => '',
                'international'                     => '',
                'international_max_charge'          => '',
                'international_min_charge'          => '',
                'international_free_shipping_order' => '',
                'international_qty_override'        => '',
                'international_free'                => '',
                'international_disable'             => '',
                'product_handling_fee'              => '',
                'shipping_policy'                   => '',
                'return_policy'                     => '',
                'shipping_from'                     => '',
                'shipping_address'                  => array(
                    'address1' => '',
                    'address2' => '',
                    'city'     => '',
                    'state'    => '',
                    'country'  => '',
                    'postcode' => '',
                ),
            );
        }

        if ( isset( $settings['commission_tiers'] ) && empty( $settings['commission_tiers'] ) ) {
            $settings['commission_tiers'] = array(
                'vendor_sales'  => array(),
                'product_sales' => array(),
                'product_price' => array(),
            );
        }

        $this->settings = $settings;
        return $this->settings;
    }

    /**
     * Get single setting
     *
     * @param string $field  Field to get the setting from.
     * @return mixed
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    public function get_prop( $field ) {
        $settings = $this->settings;
        return isset( $settings[ $field ] ) ? $settings[ $field ] : false;
    }

    /**
     * Set single setting
     *
     * @param string $field  Field to set the setting for.
     * @param mixed  $value  The value to set.
     * @return void
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    public function set_prop( $field, $value ) {
        $isset = $this->find_key( $field );
        if ( $isset ) {
            $this->changes[ $field ] = $value;
        }
    }

    /**
     * Get setting changes
     *
     * @access public
     * @return array The settings that have changed.
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    public function get_changes() {
        return $this->changes;
    }

    /**
     * Save settings
     *
     * @access public
     * @return array The settings that have changed.
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    public function save() {

        $changes = $this->get_changes();
        if ( empty( $changes ) ) {
            return false;
        }

        $media_fields = $this->get_media_fields();

        foreach ( $changes as $field => $value ) {
            $section = $this->get_section( $field );
            if ( 'wp' === $section ) {
                continue;
            }

            if ( ! is_wcv_pro_active() && 'wcvp' === $section ) {
                continue;
            }

            if ( in_array( $field, $media_fields, true ) ) {
                $value = (string) $value['id'];
            }
            if ( ! isset( $this->data_keys[ $section ][ $field ] ) ) {
                continue;
            }
            $result = update_user_meta( $this->vendor_id, $this->data_keys[ $section ][ $field ], $value );
            if ( $result ) {
                $this->change_shop_slug( $field, $value );
                do_action( 'wcvendors_vendor_settings_save', $this->vendor_id, $field, $value );
            }
        }
        return true;
    }

    /**
     * Get section
     *
     * @param string $field The field to get the section for.
     * @return string|bool
     * @since 2.4.8
     * @version 2.4.8
     */
    public function get_section( $field ) {
        $keys = $this->get_keys();
        foreach ( $keys as $section => $fields ) {
            if ( isset( $fields[ $field ] ) ) {
                return $section;
            }
        }
        return false;
    }

    /**
     * Get current comission rates
     *
     * @return array
     * @since 2.4.8
     * @version 2.4.8
     */
    public function get_commission_rates() {
        $value     = array();
        $formatted = '';
        if ( ! is_wcv_pro_active() ) {
            $value     = get_option( 'wcvendors_vendor_commission_rate', 0 );
            $formatted = __( 'Percent: ', 'wc-vendors' ) . $value . '%';

            return array(
                'value'     => $value,
                'formatted' => $formatted,
                'type'      => 'percent',
                'level'     => __( 'Default', 'wc-vendors' ),
            );
        }

        $global_rate = array(
            'type'    => get_option( 'wcvendors_commission_type', 'percent' ),
            'percent' => get_option( 'wcvendors_vendor_commission_rate' ),
            'fee'     => get_option( 'wcvendors_commission_fee' ),
            'amount'  => get_option( 'wcvendors_commission_amount' ),
        );

        $store_rate = array(
            'type'    => $this->get_prop( 'commission_type' ),
            'percent' => $this->get_prop( 'commission_percent' ),
            'fee'     => $this->get_prop( 'commission_fee' ),
            'amount'  => $this->get_prop( 'commission_amount' ),
        );

        $store_tiers_rate = $this->get_prop( 'commission_tiers' );
        $global_tier_rate = get_option( 'wcv_global_commission_tiers', array() );

        $rate  = ! empty( $store_rate['type'] ) ? $store_rate : $global_rate;
        $level = ! empty( $store_rate['type'] ) ? __( 'Store', 'wc-vendors' ) : __( 'Default', 'wc-vendors' );
        $tiers = ! empty( $store_tiers_rate ) ? $store_tiers_rate : $global_tier_rate;

        switch ( $rate['type'] ) {
            case 'percent':
                $value     = $rate['percent'];
                $formatted = __( 'Percent: ', 'wc-vendors' ) . $value . '%';
                break;
            case 'fixed':
                $value     = $rate['amount'];
                $formatted = __( 'Fixed: ', 'wc-vendors' ) . wc_price( $value );
                break;
            case 'percent_fee':
                $value     = array(
                    'percent' => $rate['percent'],
                    'fee'     => $rate['fee'],
                );
                $formatted = __( 'Percent: ', 'wc-vendors' ) . $value['percent'] . '% + ' . __( 'Fee: ', 'wc-vendors' ) . wc_price( $value['fee'] );
                break;
            case 'fixed_fee':
                $value     = array(
                    'amount' => $rate['amount'],
                    'fee'    => $rate['fee'],
                );
                $formatted = __( 'Fixed: ', 'wc-vendors' ) . wc_price( $value['amount'] ) . ' + ' . __( 'Fee: ', 'wc-vendors' ) . wc_price( $value['fee'] );
                break;
            case 'vendor_sales':
                $value     = $tiers;
                $formatted = __( 'Vendor Sales', 'wc-vendors' );
                break;
            case 'product_sales':
                $value     = $tiers;
                $formatted = __( 'Product Sales', 'wc-vendors' );
                break;
            case 'product_price':
                $value     = $tiers;
                $formatted = __( 'Product Price', 'wc-vendors' );
                break;
            default:
                $value     = $rate['percent'];
                $formatted = __( 'Percent: ', 'wc-vendors' ) . $value . '%';
                break;
        }

        return array(
            'value'     => $value,
            'formatted' => $formatted,
            'level'     => $level,
            'type'      => $rate['type'],
        );
    }

    /**
     * Get commission due
     *
     * @return float The commission due
     *
     * @since 2.4.8
     * @version 2.4.8
     */
    public function get_commission_due() {
        global $wpdb;
        $sql    = $wpdb->prepare( "SELECT SUM( total_due ) FROM `{$wpdb->prefix}pv_commission` WHERE vendor_id = %d AND status = 'due'", $this->vendor_id );
        $result = $wpdb->get_var( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        $result = ! empty( $result ) ? $result : 0;
        return array(
			'total'     => $result,
			'formatted' => wc_price( $result ),
        );
    }

    /**
     * Get vendor status
     */
    public function get_vendor_status() {
        $vendor_status = $this->get_prop( 'vendor_status' );
        $roles         = $this->get_prop( 'roles' );

        $status = 'inactive';

        if ( in_array( 'vendor', $roles, true ) && ! in_array( 'pending_vendor', $roles, true ) && $vendor_status && 'inactive' !== $vendor_status ) {
            $status = 'active';
        } elseif ( in_array( 'pending_vendor', $roles, true ) ) {
            $status = 'pending';
        }

        $statuses = array(
            'pending'  => __( 'Pending', 'wc-vendors' ),
            'inactive' => __( 'Inactive', 'wc-vendors' ),
            'active'   => __( 'Active', 'wc-vendors' ),
        );

        return array(
            'value'     => $status,
            'formatted' => $statuses[ $status ],
        );
    }

    /**
     * Change shop slug after save shop name
     *
     * @param string $field The field key.
     * @param string $value The field value.
     *
     * @version 2.4.8
     * @since 2.4.8
     */
    public function change_shop_slug( $field, $value ) {
        if ( 'shop_name' === $field ) {
            $shop_slug = sanitize_title( $value );

            $check        = new WP_User_Query(
                array(
                    'meta_key'   => 'pv_shop_slug',
                    'meta_value' => $shop_slug,
                )
            );
            $result       = $check->get_results();
            $result_count = $check->get_total();
            $first_result = array_shift( $result );
            if ( 0 === $result_count || $this->vendor_id === $first_result->ID ) {
                update_user_meta( $this->vendor_id, 'pv_shop_slug', $shop_slug );
            } else {
                $shop_slug = $shop_slug . '-' . $this->vendor_id;
                update_user_meta( $this->vendor_id, 'pv_shop_slug', $shop_slug );
            }
        }
    }
}
