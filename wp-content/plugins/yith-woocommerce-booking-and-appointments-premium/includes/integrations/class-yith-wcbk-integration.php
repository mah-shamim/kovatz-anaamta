<?php
/**
 * Class YITH_WCBK_Integration
 *
 * @author  YITH
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Integration
 *
 * @abstract
 * @author  Leanza Francesco <leanzafrancesco@gmail.com>
 * @since   1.0.1
 */
class YITH_WCBK_Integration {

	/**
	 * Plugin data.
	 *
	 * @var array
	 */
	protected $data = array(
		'type'              => 'plugin',
		'key'               => '',
		'name'              => '',
		'title'             => '',
		'icon'              => '',
		'landing_uri'       => '',
		'description'       => '',
		'optional'          => false,
		'constant'          => '',
		'installed_version' => '',
		'min_version'       => '',
		'version_compare'   => '>=',
		'new'               => false,
		'visible'           => true,
	);

	/**
	 * Initialized flag.
	 *
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * Initialization
	 */
	public function init_once() {
		if ( ! $this->initialized ) {
			$this->init();
			$this->initialized = true;
		}
	}

	/**
	 * Initialization
	 */
	protected function init() {

	}

	/**
	 * Set the integration data.
	 *
	 * @param array $integration_data The integration data.
	 */
	public function set_data( array $integration_data ) {
		foreach ( $this->data as $key => $value ) {
			if ( isset( $integration_data[ $key ] ) ) {
				$this->data[ $key ] = $integration_data[ $key ];
			}
		}
	}

	/**
	 * Get property
	 *
	 * @param string $prop The property.
	 *
	 * @return mixed|null
	 */
	public function get_prop( $prop ) {
		return array_key_exists( $prop, $this->data ) ? $this->data[ $prop ] : null;
	}

	/**
	 * Get the type.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->get_prop( 'type' );
	}

	/**
	 * Get the constant.
	 *
	 * @return string
	 */
	public function get_constant() {
		return $this->get_prop( 'constant' );
	}

	/**
	 * Get the key.
	 *
	 * @return string
	 */
	public function get_key() {
		return $this->get_prop( 'key' );
	}

	/**
	 * Get the installed_version.
	 *
	 * @return string
	 */
	public function get_installed_version() {
		return $this->get_prop( 'installed_version' );
	}

	/**
	 * Get the min_version.
	 *
	 * @return string
	 */
	public function get_min_version() {
		return $this->get_prop( 'min_version' );
	}

	/**
	 * Get the name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->get_prop( 'name' );
	}

	/**
	 * Get the title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->get_prop( 'title' );
	}

	/**
	 * Get the icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return $this->get_prop( 'icon' );
	}

	/**
	 * Get the landing_uri.
	 *
	 * @return string
	 */
	public function get_landing_uri() {
		return $this->get_prop( 'landing_uri' );
	}

	/**
	 * Get the landing_uri.
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->get_prop( 'description' );
	}

	/**
	 * Get the version_compare.
	 *
	 * @return string
	 */
	public function get_version_compare() {
		return $this->get_prop( 'version_compare' );
	}

	/**
	 * Get the activation URL.
	 *
	 * @return string
	 */
	public function get_activation_url() {
		return $this->get_status_change_url( 'activate' );
	}

	/**
	 * Get the deactivation URL.
	 *
	 * @return string
	 */
	public function get_deactivation_url() {
		return $this->get_status_change_url( 'deactivate' );
	}

	/**
	 * Get status change URL.
	 *
	 * @param string $action The action.
	 *
	 * @return string
	 */
	private function get_status_change_url( string $action ): string {
		return wp_nonce_url(
			add_query_arg(
				array(
					'yith-wcbk-integration-action' => $action,
					'integration'                  => $this->get_key(),
				)
			),
			'yith-wcbk-integration-status-change'
		);
	}

	/**
	 * Is this optional?
	 *
	 * @return bool
	 */
	public function is_optional(): bool {
		return ! ! $this->get_prop( 'optional' );
	}

	/**
	 * Is this visible?
	 *
	 * @return bool
	 */
	public function is_visible(): bool {
		return ! ! $this->get_prop( 'visible' );
	}

	/**
	 * Is this new?
	 *
	 * @return bool
	 */
	public function is_new(): bool {
		return ! ! $this->get_prop( 'new' );
	}

	/**
	 * Is the component(plugin or theme) active?
	 *
	 * @return bool
	 */
	public function is_component_active() {
		if ( 'theme' === $this->get_type() ) {
			return YITH_WCBK()->theme->is_active( $this->get_key() );
		} else {
			$constant = $this->get_constant();
			if ( $constant && defined( $constant ) && constant( $constant ) ) {
				$installed_version = $this->get_installed_version();
				$min_version       = $this->get_min_version();

				if ( ! $installed_version || ! $min_version ) {
					return true;
				}

				if (
					defined( $installed_version ) && constant( $installed_version )
					&&
					version_compare( constant( $installed_version ), $min_version, $this->get_version_compare() )
				) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Is the integration enabled?
	 *
	 * @return bool
	 */
	public function is_enabled() {
		return $this->is_component_active() && ( ! $this->is_optional() || get_option( 'yith-wcbk-' . $this->get_key() . '-add-on-active', 'no' ) === 'yes' );
	}
}
