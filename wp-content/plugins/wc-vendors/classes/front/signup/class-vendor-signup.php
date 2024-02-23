<?php

/**
 * Signup form for applying as a vendor
 *
 * @author  Matt Gates <http://mgates.me>, WC Vendors <http://wcvendors.com>
 * @package WCVendors
 */
class WCV_Vendor_Signup {
    /**
     * The terms page id.
     *
     * @var int
     */
    public $terms_page;

    /**
     * __construct()
     */
    public function __construct() {
        if ( ! wc_string_to_bool( get_option( 'wcvendors_vendor_allow_registration', 'no' ) ) ) {
            return;
        }

        $this->terms_page = get_option( 'wcvendors_vendor_terms_page_id' );

        add_action( 'woocommerce_register_form', array( $this, 'vendor_option' ) );

        add_action( 'woocommerce_created_customer', array( $this, 'save_pending' ), 10, 2 );

        add_action( 'template_redirect', array( $this, 'apply_form_dashboard' ), 10 );
        add_action( 'woocommerce_register_post', array( $this, 'validate_vendor_registration' ), 10, 3 );

        if ( $this->terms_page ) {
            add_action( 'login_enqueue_scripts', array( $this, 'load_scripts' ), 1 );
            add_filter( 'registration_errors', array( $this, 'vendor_registration_errors' ), 10, 1 );
        }
    }

    /**
     * Display vendor signup option.
     *
     * @return void
     */
    public function vendor_option() {
        $apply_label_css_classes = apply_filters( 'wcvendors_vendor_registration_apply_label_css_classes', 'apply_for_vendor_label ' );
        $term_label_css_classes  = apply_filters( 'wcvendors_vendor_registration_term_label_css_classes', 'agree_to_terms_label ' );
        $default_label           = __( 'Become a ', 'wc-vendors' );
        $become_a_vendor_label   = strtolower( get_option( 'wcvendors_label_become_a_vendor', $default_label ) );

        apply_filters( 'wcvendors_vendor_signup_path', include_once 'views/html-vendor-signup.php' );
    }


    /**
     * WILL BE COMPLETELY REMOVED
     *
     * Show apply to be vendor on the wp-login screen
     *
     * @since   1.9.0
     * @version 1.0.0
     */
    public function login_apply_vendor_option() {
        include_once 'views/html-vendor-signup.php';
    } // login_apply_vendor_option


    /**
     * Load the javascript for the terms page
     *
     * @since   1.9.0
     * @version 1.0.0
     */
    public function load_scripts() {
        wp_enqueue_script( 'wcv-admin-login', WCV_ASSETS_URL . 'js/wcv-admin-login.js', array( 'jquery' ), WCV_VERSION, true );
    }


    /**
     * Add error to the registration form if the terms are not agreed to.
     *
     * @param object $errors The list of current errors.
     * @return object|null
     */
    public function vendor_registration_errors( $errors ) {
        if ( ! isset( $_POST['apply_for_vendor'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['apply_for_vendor_nonce'] ) ), 'apply_for_vendor' ) ) {
            return;
        }

        if ( empty( $_POST['agree_to_terms'] ) || ! empty( $_POST['agree_to_terms'] ) && '' === trim( $_POST['agree_to_terms'] ) ) {
            $errors->add( 'terms_errors', sprintf( '<strong>%s</strong>: %s', __( 'ERROR', 'wc-vendors' ), __( 'Please agree to the terms and conditions', 'wc-vendors' ) ) );
        }

        return $errors;
    }

    /**
     * Save the pending vendor.
     *
     * @param int $user_id The user id.
     */
    public function save_pending( $user_id ) {
        if ( ! isset( $_POST['apply_for_vendor'] ) ) { // phpcs:ignore
            return;
        }

        wc_clear_notices();

        if ( user_can( $user_id, 'manage_options' ) ) {
            wc_add_notice( apply_filters( 'wcvendors_application_denied_msg', __( 'Application denied. You are an administrator.', 'wc-vendors' ) ), 'error' );
            return;
        }

        wc_add_notice( apply_filters( 'wcvendors_application_submitted_msg', __( 'Your application has been submitted.', 'wc-vendors' ) ), 'notice' );

        $manual = wc_string_to_bool( get_option( 'wcvendors_vendor_approve_registration', 'no' ) );
        $role   = apply_filters( 'wcvendors_pending_role', ( $manual ? 'pending_vendor' : 'vendor' ) );

        $wp_user_object = new WP_User( $user_id );
        $wp_user_object->add_role( $role );
        $status = $manual ? 'inactive' : 'active';
        update_user_meta( $user_id, '_wcv_vendor_status', $status );

        $this->maybe_set_vendor_as_primary_role( $wp_user_object, $role );

        do_action_deprecated(
            'wcvendors_application_submited',
            array( 'user_id' => $user_id ),
            '2.4.7',
            'wcvendors_application_submitted'
        );

        do_action( 'wcvendors_application_submitted', $user_id );

        add_filter( 'woocommerce_registration_redirect', array( $this, 'redirect_to_vendor_dash' ) );
    }


    /**
     * Save the pending vendor from the login screen
     *
     * @param string $user_id The user id.
     *
     * @since   2.4.7 - Added default role.
     * @since   1.9.0
     * @version 1.0.0
     */
    public function login_save_pending( $user_id ) {
        if ( ! isset( $_POST['apply_for_vendor'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['apply_for_vendor_nonce'] ) ), 'apply_for_vendor' ) ) {
            return;
        }

        $manual = wc_string_to_bool( get_option( 'wcvendors_vendor_approve_registration', 'no' ) );
        $role   = apply_filters( 'wcvendors_pending_role', ( $manual ? 'pending_vendor' : 'vendor' ) );

        $wp_user_object = new WP_User( $user_id );
        $wp_user_object->add_role( $role );

        $this->maybe_set_vendor_as_primary_role( $user_id, $role );

        do_action( 'wcvendors_application_submited', $user_id );
    }

    /**
     * Login authentication check code for vendors
     *
     * @param WP_User $user The user object.
     * @return void|WP_Error|WP_User
     */
    public function login_vendor_check( $user ) {
        if ( ! isset( $_POST['apply_for_vendor'] ) || ! wp_verify_nonce( $_POST['apply_for_vendor_nonce'], 'apply_for_vendor' ) ) {
            return;
        }

        if ( $this->terms_page && ! isset( $_POST['agree_to_terms'] ) ) {
            $error = new WP_Error();
            $error->add( 'no_terms', apply_filters( 'wcvendors_agree_to_terms_error', __( 'You must accept the terms and conditions to become a vendor.', 'wc-vendors' ) ) );

            return $error;
        } else {
            return $user;
        }
    }

    /**
     * Get the url to redirect to after signup.
     *
     * @param string $redirect The current redirect link.
     * @return string
     */
    public function redirect_to_vendor_dash( $redirect ) {
        $vendor_dashboard_page = get_option( 'wcvendors_vendor_dashboard_page_id' );

        $user = get_userdata( get_current_user_id() );

        $roles = $user->roles;

        if ( ! in_array( 'vendor', $roles, true ) && ! in_array( 'pending_vendor', $roles, true ) ) {
            return $redirect;
        }

        $redirect = apply_filters( 'wcvendors_signup_redirect', get_permalink( $vendor_dashboard_page ) );

        return $redirect;
    }

    /**
     * Add the apply to be a vendor form to the dashboard.
     *
     * @return false|void
     */
    public function apply_form_dashboard() {
        global $wp_query;

        if ( empty( $_POST ) ) {
            return;
        }

        if ( ! isset( $_POST['apply_for_vendor'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['apply_for_vendor_nonce'] ) ), 'apply_for_vendor' ) ) {
            wc_add_notice(
                __( 'Your application was not submitted. Please refresh the page and then try again.', 'wc-vendors' ),
                'error'
            );
            return;
        }

        $vendor_dashboard_page = (int) get_option( 'wcvendors_vendor_dashboard_page_id' );
        $page_id               = (int) get_queried_object_id();

        if ( $page_id === $vendor_dashboard_page || isset( $wp_query->query['become-a-vendor'] ) ) {
            if ( $this->terms_page ) {
                if ( isset( $_POST['agree_to_terms'] ) ) {
                    self::save_pending( get_current_user_id() );
                } else {
                    wc_add_notice( apply_filters( 'wcvendors_agree_to_terms_error', __( 'You must accept the terms and conditions to become a vendor.', 'wc-vendors' ), 'error' ) );
                }
            } else {
                self::save_pending( get_current_user_id() );
            }
        }
    }

    /**
     * Validate vendor registration, checking for errors.
     *
     * @param string $username The username.
     * @param string $email The email address.
     * @param object $validation_errors The validation errors.
     * @return void
     */
    public function validate_vendor_registration( $username, $email, $validation_errors ) {
        if ( ! isset( $_POST['apply_for_vendor'] ) || ! wp_verify_nonce( $_POST['apply_for_vendor_nonce'], 'apply_for_vendor' ) ) {
            return;
        }

        if ( $this->terms_page && ! isset( $_POST['agree_to_terms'] ) ) {
            $validation_errors->add( 'agree_to_terms_error', apply_filters( 'wcvendors_agree_to_terms_error', __( 'You must accept the terms and conditions to become a vendor.', 'wc-vendors' ) ) );
        }
    }

    /**
     * Maybe set the vendor as the default role for the user.
     *
     * @param int|WP_User $user The ID of the user.
     * @param string      $role The role to set.
     * @return void
     * @version 2.4.5
     * @since   2.4.5 - Added.
     */
    public function maybe_set_vendor_as_primary_role( $user, $role = 'vendor' ) {
        $vendor_is_primary_role = wc_string_to_bool( get_option( 'wcvendors_vendor_primary_role', 'no' ) );

        if ( $vendor_is_primary_role ) {
            wcv_set_primary_vendor_role( $user, $role );
        }
    }
}
