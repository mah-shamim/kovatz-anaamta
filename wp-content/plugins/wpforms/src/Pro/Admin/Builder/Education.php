<?php

namespace WPForms\Pro\Admin\Builder;

/**
 * Form Builder changes and enhancements to educate Basic/Plus users on what is
 * available in WPForms Pro.
 *
 * @package    WPForms\Pro\Admin\Builder
 * @author     WPForms
 * @since      1.5.1
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2018, WPForms LLC
 */
class Education {

	/**
	 * Addons data.
	 *
	 * @since 1.5.1
	 *
	 * @var object
	 */
	public $addons;

	/**
	 * License level slug.
	 *
	 * @since 1.5.1
	 *
	 * @var string
	 */
	public $license;

	/**
	 * Constructor.
	 *
	 * @since 1.5.1
	 */
	public function __construct() {

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 * @since 1.5.1
	 */
	public function hooks() {

		// Only proceed for the form builder.
		if ( ! \wpforms_is_admin_page( 'builder' ) ) {
			return;
		}

		if ( ! \apply_filters( 'wpforms_admin_builder_education', '__return_true' ) ) {
			return;
		}

		// Load addon data.
		$this->addons = \wpforms()->license->addons();

		// Load license level.
		$this->license = $this->get_license_type();

		\add_filter( 'wpforms_builder_strings', array( $this, 'js_strings' ) );

		\add_action( 'wpforms_builder_enqueues_before', array( $this, 'enqueues' ) );

		\add_filter( 'wpforms_builder_fields_buttons', array( $this, 'fields' ), 100 );

		\add_filter( 'wpforms_builder_field_button_attributes', array( $this, 'fields_attributes' ), 100, 3 );

		\add_action( 'wpforms_builder_after_panel_sidebar', array( $this, 'settings' ), 100, 2 );

		\add_action( 'wpforms_providers_panel_sidebar', array( $this, 'providers' ), 100 );

		\add_action( 'wpforms_payments_panel_sidebar', array( $this, 'payments' ), 100 );
	}

	/**
	 * Localize needed strings.
	 *
	 * @since 1.5.1
	 *
	 * @param array $strings JS strings.
	 *
	 * @return array
	 */
	public function js_strings( $strings ) {

		$strings['education_activate_prompt']  = '<p>' . \esc_html__( 'The %name% is installed but not activated. Would you like to activate it?', 'wpforms' ) . '</p>';
		$strings['education_activate_confirm'] = \esc_html__( 'Yes, activate', 'wpforms' );
		$strings['education_activated']        = \esc_html__( 'Addon activated', 'wpforms' );
		$strings['education_activating']       = \esc_html__( 'Activating', 'wpforms' );
		$strings['education_install_prompt']   = '<p>' . \esc_html__( 'The %name% is not installed. Would you like to install and activate it?', 'wpforms' ) . '</p>';
		$strings['education_install_confirm']  = \esc_html__( 'Yes, install and activate', 'wpforms' );
		$strings['education_installing']       = \esc_html__( 'Installing', 'wpforms' );
		$strings['education_activated']        = \esc_html__( 'Addon activated', 'wpforms' );
		$strings['education_save_prompt']      = \esc_html__( 'Almost done! Would you like to save and refresh the form builder?', 'wpforms' );
		$strings['education_save_confirm']     = \esc_html__( 'Yes, save and refresh', 'wpforms' );
		$strings['education_upgrade_title']    = \esc_html__( 'is a PRO Feature', 'wpforms' );
		$strings['education_upgrade_message']  = '<p>' . \esc_html__( 'We\'re sorry, the %name% is not available on your plan. Please upgrade to the PRO plan to unlock all these awesome features.', 'wpforms' ) . '</p>';
		$strings['education_upgrade_confirm']  = \esc_html__( 'Upgrade to PRO', 'wpforms' );
		$strings['education_upgrade_url']      = 'https://wpforms.com/pricing/?utm_source=WordPress&utm_medium=builder-modal&utm_campaign=plugin';
		$strings['education_license_prompt']   = \esc_html__( 'To access addons please enter and activate your WPForms license key in the plugin settings.', 'wpforms' );

		$license_key = \wpforms()->license->get();
		if ( ! empty( $license_key ) ) {
			$strings['education_upgrade_url'] = \add_query_arg(
				array(
					'license_key' => \sanitize_text_field( $license_key ),
				),
				$strings['education_upgrade_url']
			);
		}

		return $strings;
	}

	/**
	 * Load enqueues.
	 *
	 * @since 1.5.1
	 */
	public function enqueues() {

		$min = \wpforms_get_min_suffix();

		\wp_enqueue_script(
			'wpforms-builder-education',
			\WPFORMS_PLUGIN_URL . "pro/assets/js/admin/builder-education{$min}.js",
			array( 'jquery', 'jquery-confirm' ),
			\WPFORMS_VERSION,
			false
		);
	}

	/**
	 * Display fields.
	 *
	 * @since 1.5.1
	 *
	 * @param array $fields Form fields.
	 *
	 * @return array
	 */
	public function fields( $fields ) {

		$addons = array(
			array(
				'name'        => 'Captcha',
				'slug'        => 'captcha',
				'plugin'      => 'wpforms-captcha/wpforms-captcha.php',
				'plugin_slug' => 'wpforms-captcha',
				'field'       => array(
					'icon'  => 'fa-question-circle',
					'name'  => \esc_html__( 'Captcha', 'wpforms' ),
					'type'  => 'captcha',
					'order' => '3000',
					'class' => 'education-modal',
				),
			),
			array(
				'name'        => 'Signatures',
				'slug'        => 'signatures',
				'plugin'      => 'wpforms-signatures/wpforms-signatures.php',
				'plugin_slug' => 'wpforms-signatures',
				'field'       => array(
					'icon'  => 'fa-pencil',
					'name'  => \esc_html__( 'Signature', 'wpforms' ),
					'type'  => 'signature',
					'order' => '310',
					'class' => 'education-modal',
				),
			),
			array(
				'name'        => 'Surveys and Polls',
				'slug'        => 'surveys-polls',
				'plugin'      => 'wpforms-surveys-polls/wpforms-surveys-polls.php',
				'plugin_slug' => 'wpforms-surveys-polls',
				'field'       => array(
					'icon'  => 'fa-ellipsis-h',
					'name'  => \esc_html__( 'Likert Scale', 'wpforms' ),
					'type'  => 'likert_scale',
					'order' => '4000',
					'class' => 'education-modal',
				),
			),
			array(
				'name'        => 'Surveys and Polls',
				'slug'        => 'surveys-polls',
				'plugin'      => 'wpforms-surveys-polls/wpforms-surveys-polls.php',
				'plugin_slug' => 'wpforms-surveys-polls',
				'field'       => array(
					'icon'  => 'fa-tachometer',
					'name'  => \esc_html__( 'Net Promoter Score', 'wpforms' ),
					'type'  => 'net_promoter_score',
					'order' => '4100',
					'class' => 'education-modal',
				),
			),
		);

		$addons = $this->get_addons_available( $addons );

		if ( empty( $addons ) ) {
			return $fields;
		}

		// Restructure data.
		foreach ( $addons as $addon ) {
			$addon['field']['plugin']      = $addon['plugin'];
			$addon['field']['plugin_name'] = $addon['name'];
			$addon['field']['action']      = $addon['action'];
			$addon['field']['url']         = isset( $addon['url'] ) ? $addon['url'] : '';
			$addon['field']['nonce']       = \wp_create_nonce( 'wpforms-admin' );
			$fields['fancy']['fields'][]   = $addon['field'];
		}

		return $fields;
	}

	/**
	 * Adjust attributes on field media_buttons.
	 *
	 * @since 1.5.1
	 *
	 * @param string $atts      Button attributes.
	 * @param array  $field     Button properties
	 * @param array  $form_data Form data.
	 *
	 * @return array
	 */
	public function fields_attributes( $atts, $field, $form_data ) {

		if ( empty( $field['action'] ) ) {
			return $atts;
		}

		$atts['data']['action']     = $field['action'];
		$atts['data']['nonce']      = \wp_create_nonce( 'wpforms-admin' );
		/* translators: %s - field name*/
		$field_name                 = sprintf( \esc_html__( '%s field', 'wpforms' ), $field['name'] );
		$atts['data']['field-name'] = $field_name;

		if ( ! empty( $field['plugin_name'] ) ) {
			/* translators: %s - addon name*/
			$modal_name           = sprintf( \esc_html__( '%s addon', 'wpforms' ), $field['plugin_name'] );
			$atts['data']['name'] = $modal_name;
		}

		if ( ! empty( $field['plugin'] ) ) {
			$atts['data']['path'] = $field['plugin'];
		}

		if ( ! empty( $field['url'] ) ) {
			$atts['data']['url'] = $field['url'];
		}

		return $atts;
	}

	/**
	 * Display settings panels.
	 *
	 * @since 1.5.1
	 *
	 * @param object $form Current form.
	 * @param string $slug Panel slug.
	 */
	public function settings( $form, $slug ) {

		if ( 'settings' !== $slug ) {
			return;
		}

		$addons = array(
			array(
				'name'        => 'Conversational Forms',
				'slug'        => 'conversational-forms',
				'plugin'      => 'wpforms-conversational-forms/wpforms-conversational-forms.php',
				'plugin_slug' => 'wpforms-conversational-forms',
			),
			array(
				'name'        => 'Surveys and Polls',
				'slug'        => 'surveys-polls',
				'plugin'      => 'wpforms-surveys-polls/wpforms-surveys-polls.php',
				'plugin_slug' => 'wpforms-surveys-polls',
			),
			array(
				'name'        => 'Form Pages',
				'slug'        => 'form-pages',
				'plugin'      => 'wpforms-form-pages/wpforms-form-pages.php',
				'plugin_slug' => 'wpforms-form-pages',
			),
			array(
				'name'        => 'Form Locker',
				'slug'        => 'form-locker',
				'plugin'      => 'wpforms-form-locker/wpforms-form-locker.php',
				'plugin_slug' => 'wpforms-form-locker',
			),
			array(
				'name'        => 'Form Abandonment',
				'slug'        => 'form-abandonment',
				'plugin'      => 'wpforms-form-abandonment/wpforms-form-abandonment.php',
				'plugin_slug' => 'wpforms-form-abandonment',
			),
			array(
				'name'        => 'Post Submissions',
				'slug'        => 'post-submissions',
				'plugin'      => 'wpforms-post-submissions/wpforms-post-submissions.php',
				'plugin_slug' => 'wpforms-post-submissions',
			),
		);

		$settings = $this->get_addons_available( $addons );

		if ( empty( $settings ) ) {
			return;
		}

		foreach ( $settings as $setting ) {

			/* translators: %s - addon name*/
			$modal_name = sprintf( \esc_html__( '%s addon', 'wpforms' ), $setting['name'] );
			printf(
				'<a href="#" class="wpforms-panel-sidebar-section wpforms-panel-sidebar-section-%s education-modal" data-name="%s" data-action="%s" data-path="%s" data-url="%s" data-nonce="%s">',
				\esc_attr( $setting['slug'] ),
				\esc_attr( $modal_name ),
				\esc_attr( $setting['action'] ),
				\esc_attr( $setting['plugin'] ),
				isset( $setting['url'] ) ? \esc_attr( $setting['url'] ) : '',
				\wp_create_nonce( 'wpforms-admin' ) //phpcs:ignore
			);
				echo \esc_html( $setting['name'] );
				echo '<i class="fa fa-angle-right wpforms-toggle-arrow"></i>';
			echo '</a>';
		}
	}

	/**
	 * Display providers.
	 *
	 * @since 1.5.1
	 */
	public function providers() {

		$addons = array(
			array(
				'name'        => 'AWeber',
				'slug'        => 'aweber',
				'img'         => 'addon-icon-aweber.png',
				'plugin'      => 'wpforms-aweber/wpforms-aweber.php',
				'plugin_slug' => 'wpforms-aweber',
			),
			array(
				'name'        => 'Campaign Monitor',
				'slug'        => 'campaign-monitor',
				'img'         => 'addon-icon-campaign-monitor.png',
				'plugin'      => 'wpforms-campaign-monitor/wpforms-campaign-monitor.php',
				'plugin_slug' => 'wpforms-campaign-monitor',
			),
			array(
				'name'        => 'Drip',
				'slug'        => 'drip',
				'img'         => 'addon-icon-drip.png',
				'plugin'      => 'wpforms-drip/wpforms-drip.php',
				'plugin_slug' => 'wpforms-drip',
			),
			array(
				'name'        => 'GetResponse',
				'slug'        => 'getresponse',
				'img'         => 'addon-icon-getresponse.png',
				'plugin'      => 'wpforms-getresponse/wpforms-getresponse.php',
				'plugin_slug' => 'wpforms-getresponse',
			),
			array(
				'name'        => 'MailChimp',
				'slug'        => 'mailchimp',
				'img'         => 'addon-icon-mailchimp.png',
				'plugin'      => 'wpforms-mailchimp/wpforms-mailchimp.php',
				'plugin_slug' => 'wpforms-mailchimp',
			),
			array(
				'name'        => 'Zapier',
				'slug'        => 'zapier',
				'img'         => 'addon-icon-zapier.png',
				'plugin'      => 'wpforms-zapier/wpforms-zapier.php',
				'plugin_slug' => 'wpforms-zapier',
			),
		);

		$providers = $this->get_addons_available( $addons );

		if ( empty( $providers ) ) {
			return;
		}

		foreach ( $providers as $provider ) {

			/* translators: %s - addon name*/
			$modal_name = sprintf( \esc_html__( '%s addon', 'wpforms' ), $provider['name'] );
			printf(
				'<a href="#" class="wpforms-panel-sidebar-section icon wpforms-panel-sidebar-section-%s education-modal" data-name="%s" data-action="%s" data-path="%s" data-url="%s" data-nonce="%s">',
				\esc_attr( $provider['slug'] ),
				\esc_attr( $modal_name ),
				\esc_attr( $provider['action'] ),
				\esc_attr( $provider['plugin'] ),
				isset( $provider['url'] ) ? \esc_attr( $provider['url'] ) : '',
				\wp_create_nonce( 'wpforms-admin' ) //phpcs:ignore
			);
				echo '<img src="' . \esc_attr( WPFORMS_PLUGIN_URL ) . 'assets/images/' . \esc_attr( $provider['img'] ) . '">';
				echo \esc_html( $provider['name'] );
				echo '<i class="fa fa-angle-right wpforms-toggle-arrow"></i>';
			echo '</a>';
		}
	}

	/**
	 * Display payment.
	 *
	 * @since 1.5.1
	 */
	public function payments() {

		$addons = array(
			array(
				'name'        => 'PayPal Standard',
				'slug'        => 'paypal_standard',
				'img'         => 'addon-icon-paypal.png',
				'plugin'      => 'wpforms-paypal-standard/wpforms-paypal-standard.php',
				'plugin_slug' => 'wpforms-paypal-standard',
			),
			array(
				'name'        => 'Stripe',
				'slug'        => 'stripe',
				'img'         => 'addon-icon-stripe.png',
				'plugin'      => 'wpforms-stripe/wpforms-stripe.php',
				'plugin_slug' => 'wpforms-stripe',
			),
		);

		$payments = $this->get_addons_available( $addons );

		if ( empty( $payments ) ) {
			return;
		}

		foreach ( $payments as $payment ) {

			/* translators: %s - addon name*/
			$modal_name = sprintf( \esc_html__( '%s addon', 'wpforms' ), $payment['name'] );
			printf(
				'<a href="#" class="wpforms-panel-sidebar-section icon wpforms-panel-sidebar-section-%s education-modal" data-name="%s" data-action="%s" data-path="%s" data-url="%s" data-nonce="%s">',
				\esc_attr( $payment['slug'] ),
				\esc_attr( $modal_name ),
				\esc_attr( $payment['action'] ),
				\esc_attr( $payment['plugin'] ),
				isset( $payment['url'] ) ? \esc_attr( $payment['url'] ) : '',
				\wp_create_nonce( 'wpforms-admin' ) //phpcs:ignore
			);
				echo '<img src="' . \esc_attr( WPFORMS_PLUGIN_URL ) . 'assets/images/' . \esc_attr( $payment['img'] ) . '">';
				echo \esc_html( $payment['name'] );
				echo '<i class="fa fa-angle-right wpforms-toggle-arrow"></i>';
			echo '</a>';
		}
	}

	/**
	 * Return status of a addon.
	 *
	 * @since 1.5.1
	 *
	 * @param string $plugin Plugin path.
	 *
	 * @return string
	 */
	public function get_addon_status( $plugin ) {

		if ( \is_plugin_active( $plugin ) ) {
			return 'active';
		}

		$plugins = \get_plugins();

		if ( ! empty( $plugins[ $plugin ] ) ) {
			return 'installed';
		}

		return 'missing';
	}

	/**
	 * Returns array of addons available.
	 *
	 * @since 1.5.1
	 *
	 * @param array $addons Addons to check.
	 *
	 * @return array
	 */
	public function get_addons_available( $addons ) {

		foreach ( $addons as $key => $addon ) {

			$status = $this->get_addon_status( $addon['plugin'] );

			if ( 'active' === $status ) {
				unset( $addons[ $key ] );
				continue;
			}

			if ( 'installed' === $status ) {
				$addons[ $key ]['action'] = 'activate';
			} else {
				if ( ! $this->license ) {
					$addons[ $key ]['action'] = 'license';
				} elseif ( $this->has_addon_access( $addon['plugin_slug'] ) ) {
					$addons[ $key ]['action'] = 'install';
					$addons[ $key ]['url']    = $this->get_addon_download_url( $addon['plugin_slug'] );
				} else {
					$addons[ $key ]['action'] = 'upgrade';
				}
			}
		}

		return $addons;
	}

	/**
	 * Returns download URL for an addon.
	 *
	 * @since 1.5.1
	 *
	 * @param string $slug Addon slug.
	 *
	 * @return string|false
	 */
	public function get_addon_download_url( $slug ) {

		if ( empty( $this->addons ) ) {
			return false;
		}

		foreach ( $this->addons as $addon_data ) {
			if (
				$addon_data->slug === $slug &&
				! empty( $addon_data->url )
			) {
				return $addon_data->url;
			}
		}

		return false;
	}

	/**
	 * Determine if user's license level has access.
	 *
	 * @since 1.5.1
	 *
	 * @param string $slug Addons slug.
	 *
	 * @return bool
	 */
	public function has_addon_access( $slug ) {

		if ( empty( $this->addons ) ) {
			return false;
		}

		foreach ( $this->addons as $addon_data ) {

			$license = ( $this->license === 'elite' ) ? 'agency' : $this->license;

			if (
				$addon_data->slug === $slug &&
				in_array( $license, $addon_data->types, true )
			) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the current installation license type (always lowercase).
	 *
	 * @since 1.5.1
	 *
	 * @return string|false
	 */
	public function get_license_type() {

		$type = \wpforms_setting( 'type', '', 'wpforms_license' );

		if ( empty( $type ) || ! \wpforms()->pro ) {
			return false;
		}

		return strtolower( $type );
	}
}
