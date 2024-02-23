<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Elementor_Widgets_Storage {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	private $cache_group  = 'jet_engine_widget';
	private $dynamic_key  = '_dynamic';
	private $defaults_key = '_defaults';
	private $frontend_key = '_frontend_settings';

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return Jet_Engine
	 */
	public static function instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function setup_storage_data( $widget ) {

		$name              = $widget->get_name();
		$settings          = [];
		$dynamic_contols   = [];
		$frontend_settings = [];

		foreach ( $widget->get_controls() as $key => $data ) {
			
			if ( isset( $data['default'] ) ) {
				$settings[ $key ] = $data['default'];
			}

			if ( in_array( $data['type'], array( 'repeater', 'jet-repeater' ) ) || ! empty( $data['dynamic'] ) ) {
				$dynamic_contols[ $key ] = $data;
			}

			if ( ! empty( $data['frontend_available'] ) ) {
				$frontend_settings[] = $key;
			}
			
		}

		wp_cache_set( $name . $this->dynamic_key, $dynamic_contols, $this->cache_group );
		wp_cache_set( $name . $this->defaults_key, $settings, $this->cache_group );
		wp_cache_set( $name . $this->frontend_key, $frontend_settings, $this->cache_group );

		return [
			$this->defaults_key => $settings,
			$this->dynamic_key  => $dynamic_contols,
			$this->frontend_key => $frontend_settings,
		];
	}

	public function get_widget_frontend_settings_keys( $widget ) {

		$name              = $widget->get_name();
		$frontend_settings = wp_cache_get( $name . $this->frontend_key, $this->cache_group );

		if ( ! $frontend_settings ) {
			$frontend_settings = $this->setup_storage_data( $widget )[ $this->frontend_key ];
		}

		return $frontend_settings;

	}

	public function get_dynamic_widget_controls( $widget ) {

		$name    = $widget->get_name();
		$dynamic = wp_cache_get( $name . $this->dynamic_key, $this->cache_group );

		if ( ! $dynamic ) {
			$dynamic = $this->setup_storage_data( $widget )[ $this->dynamic_key ];
		}

		return $dynamic;

	}

	public function get_widget_defaults( $widget ) {

		$d = array(
			'_transform_scale_popover' => '',
			'_transform_rotate_popover' => '',
			'_transform_flipX_effect' => '',
			'_transform_flipY_effect' => '',
			'_transform_scale_popover_hover' => '',
			'_transform_rotate_popover_hover' => '',
			'_transform_flipX_effect_hover' => '',
			'_transform_flipY_effect_hover' => '',
		);

		$name = $widget->get_name();
		$settings = wp_cache_get( $name . $this->defaults_key, $this->cache_group );

		if ( ! $settings ) {
			$settings = $this->setup_storage_data( $widget )[ $this->defaults_key ];
		}

		return $settings;

	}

}