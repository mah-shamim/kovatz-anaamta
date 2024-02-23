<?php

use \Elementor\Core\DynamicTags\Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Jet_Listing_Dynamic_Widget extends \Elementor\Widget_Base {

	use \Jet_Engine\Modules\Performance\Traits\Prevent_Wrap;

	private $jet_active_settings  = null;
	private $jet_dynamic_settings = null;
	private $jet_settings         = null;

	/**
	 * Get settings for display.
	 *
	 * Retrieve all the settings or, when requested, a specific setting for display.
	 *
	 * Unlike `get_settings()` method, this method retrieves only active settings
	 * that passed all the conditions, rendered all the shortcodes and all the dynamic
	 * tags.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param string $setting_key Optional. The key of the requested setting.
	 *                            Default is null.
	 *
	 * @return mixed The settings.
	 */
	public function get_settings_for_display( $setting_key = null ) {

		if ( ! $this->jet_active_settings ) {
			$this->jet_active_settings = $this->get_parsed_dynamic_settings();
		}

		return self::get_items( $this->jet_active_settings, $setting_key );
	}

	/**
	 * @since 2.0.14
	 * @access public
	 */
	public function get_parsed_dynamic_settings( $setting = null, $settings = null ) {

		if ( null === $settings ) {
			$settings = $this->jet_settings();
		}

		if ( null === $this->jet_dynamic_settings ) {
			$this->jet_dynamic_settings = $this->parse_dynamic_settings( $settings, null, $settings );
		}

		return self::get_items( $this->jet_dynamic_settings, $setting );

	}

	/**
	 * Parse dynamic settings.
	 *
	 * Retrieve the settings with rendered dynamic tags.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array $settings     Optional. The requested setting. Default is null.
	 * @param array $controls     Optional. The controls array. Default is null.
	 * @param array $all_settings Optional. All the settings. Default is null.
	 *
	 * @return array The settings with rendered dynamic tags.
	 */
	public function parse_dynamic_settings( $settings, $controls = null, $all_settings = null ) {
		
		if ( null === $all_settings ) {
			$all_settings = $this->jet_settings();
		}

		if ( null === $controls ) {
			$controls = Jet_Elementor_Widgets_Storage::instance()->get_dynamic_widget_controls( $this );
		}

		foreach ( $controls as $control ) {
		
			$control_name = $control['name'];
			$control_obj = Elementor\Plugin::$instance->controls_manager->get_control( $control['type'] );

			if ( ! $control_obj instanceof \Elementor\Base_Data_Control ) {
				continue;
			}

			if ( $control_obj instanceof \Elementor\Control_Repeater ) {

				if ( ! isset( $settings[ $control_name ] ) ) {
					continue;
				}

				foreach ( $settings[ $control_name ] as & $field ) {
					$field = $this->parse_dynamic_settings( $field, $control['fields'], $field );
				}

				continue;
			}

			$dynamic_settings = $control_obj->get_settings( 'dynamic' );

			if ( ! $dynamic_settings ) {
				$dynamic_settings = [];
			}

			if ( ! empty( $control['dynamic'] ) ) {
				$dynamic_settings = array_merge( $dynamic_settings, $control['dynamic'] );
			}

			if ( empty( $dynamic_settings ) || ! isset( $all_settings[ Manager::DYNAMIC_SETTING_KEY ][ $control_name ] ) ) {
				continue;
			}

			if ( ! empty( $dynamic_settings['active'] ) && ! empty( $all_settings[ Manager::DYNAMIC_SETTING_KEY ][ $control_name ] ) ) {
				$parsed_value = $control_obj->parse_tags( $all_settings[ Manager::DYNAMIC_SETTING_KEY ][ $control_name ], $dynamic_settings );

				$dynamic_property = ! empty( $dynamic_settings['property'] ) ? $dynamic_settings['property'] : null;

				if ( $dynamic_property ) {
					$settings[ $control_name ][ $dynamic_property ] = $parsed_value;
				} else {
					$settings[ $control_name ] = $parsed_value;
				}
			}
		}

		return $settings;
	}

	/**
	 * Get frontend settings.
	 *
	 * Retrieve the settings for all frontend controls.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @return array Frontend settings.
	 */
	public function get_frontend_settings() {
		
		$keys   = Jet_Elementor_Widgets_Storage::instance()->get_widget_frontend_settings_keys( $this );
		$result = [];

		$settings = $this->get_settings_for_display();

		if ( ! empty( $keys ) ) {
			foreach ( $keys as $key ) {

				$value = isset( $settings[ $key ] ) ? $settings[ $key ] : null;

				if ( in_array( $value, array( null, '' ), true ) ) {
					continue;
				}

				if ( is_array( $value ) && empty( $value ) ) {
					continue;
				}

				$is_slider_control = is_array( $value ) && isset( $value['size'] ) && isset( $value['sizes'] );

				if ( $is_slider_control && \Jet_Engine_Tools::is_empty( $value['size'] ) && empty( $value['sizes'] ) ) {
					continue;
				}

				if ( ! $this->is_control_visible( $this->get_controls( $key ), $settings ) ) {
					continue;
				}

				$result[ $key ] = $value;
			}
		}

		return $result;

	}

	public function jet_with_defaults( $settings = array() ) {
		return array_merge( Jet_Elementor_Widgets_Storage::instance()->get_widget_defaults( $this ), $settings );
	}

	public function jet_settings( $setting = null ) {

		if ( null === $this->jet_settings ) {
			$this->jet_settings = $this->jet_with_defaults( $this->get_data( 'settings' ) );
		}

		return self::get_items( $this->jet_settings, $setting );

	}

}