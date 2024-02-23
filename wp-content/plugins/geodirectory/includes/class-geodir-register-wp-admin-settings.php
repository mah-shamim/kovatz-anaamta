<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Take settings registered for WP-Admin and hooks them up to the REST API.
 *
 * @since    2.0.0
 * @package  GeoDirectory/Classes
 * @category Class
 */
class GeoDir_Register_WP_Admin_Settings {

	/** @var class Contains the current class to pull settings from. Either a admin page object. */
	protected $object;

	/**
	 * Hooks into the settings API and starts registering our settings.
	 *
	 * @since 2.0.0
	 * @param GeoDir_Settings_Page $object The object that contains the settings to register.
	 * @param string                    $type   Type of settings to register page.
	 */
	public function __construct( $object, $type ) {
		if ( ! is_object( $object ) ) {
			return;
		}

		$this->object = $object;

		if ( 'page' === $type ) {
			add_filter( 'geodir_settings_groups', array( $this, 'register_page_group' ) );
			add_filter( 'geodir_settings-' . $this->object->get_id(),  array( $this, 'register_page_settings' ) );
		}
	}

	/**
	 * Registers a setting group, based on admin page ID & label as parent group.
	 *
	 * @since  2.0.0
	 * @param  array $groups Array of previously registered groups.
	 * @return array
	 */
	public function register_page_group( $groups ) {
		$groups[] = array(
			'id'    => $this->object->get_id(),
			'label' => $this->object->get_label(),
		);
		return $groups;
	}

	/**
	 * Registers settings to a specific group.
	 *
	 * @since  2.0.0
	 * @param  array $settings Existing registered settings
	 * @return array
	 */
	public function register_page_settings( $settings ) {
		/**
		 * wp-admin settings can be broken down into separate sections from
		 * a UI standpoint. This will grab all the sections associated with
		 * a particular setting group (like 'products') and register them
		 * to the REST API.
		 */
		$sections = $this->object->get_sections();
		if ( empty( $sections ) ) {
			// Default section is just an empty string, per admin page classes
			$sections = array( '' );
		}

		foreach ( $sections as $section => $section_label ) {
			$settings_from_section = $this->object->get_settings( $section );
			foreach ( $settings_from_section as $setting ) {
				if ( ! isset( $setting['id'] ) ) {
					continue;
				}
				$setting['option_key'] = $setting['id'];
				$new_setting           = $this->register_setting( $setting );
				if ( $new_setting ) {
					$settings[] = $new_setting;
				}
			}
		}
		return $settings;
	}

	/**
	 * Register a setting into the format expected for the Settings REST API.
	 *
	 * @since 2.0.0
	 * @param  array $setting
	 * @return array|bool
	 */
	public function register_setting( $setting ) {
		if ( ! isset( $setting['id'] ) ) {
			return false;
		}

		$description = '';
		if ( ! empty( $setting['desc'] ) ) {
			$description = $setting['desc'];
		} elseif ( ! empty( $setting['description'] ) ) {
			$description = $setting['description'];
		}

		$new_setting = array(
			'id'          => $setting['id'],
			'label'       => ( ! empty( $setting['name'] ) ? $setting['name'] : '' ),
			'description' => $description,
			'type'        => $setting['type'],
			'option_key'  => $setting['option_key'],
		);

		if ( isset( $setting['default'] ) ) {
			$new_setting['default'] = $setting['default'];
		}
		if ( isset( $setting['options'] ) ) {
			$new_setting['options'] = $setting['options'];
		}
		if ( isset( $setting['desc_tip'] ) ) {
			if ( true === $setting['desc_tip'] ) {
				$new_setting['tip'] = $description;
			} elseif ( ! empty( $setting['desc_tip'] ) ) {
				$new_setting['tip'] = $setting['desc_tip'];
			}
		}

		return $new_setting;
	}

}
