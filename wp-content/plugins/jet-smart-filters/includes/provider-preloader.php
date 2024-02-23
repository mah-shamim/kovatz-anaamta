<?php
/**
 * Provider Preloader
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Provider_Preloader' ) ) {

	/**
	 * Define Jet_Smart_Filters_Provider_Preloader class
	 */
	class Jet_Smart_Filters_Provider_Preloader {

		// Settings
		public $is_enabled;
		public $fixed_position;
		public $fixed_edge_gap;
		public $type;
		public $styles;
		public $css;

		// Data
		public $type_options;
		private $preloader_structure;

		/**
		 * Constructor for the class
		 */
		public function __construct() {

			// Settings
			$this->is_enabled     = jet_smart_filters()->settings->get( 'use_provider_preloader', false );
			$this->fixed_position = jet_smart_filters()->settings->get( 'provider_preloader_fixed_position', false );
			$this->fixed_edge_gap = jet_smart_filters()->settings->get( 'provider_preloader_fixed_edge_gap', 80 );
			$this->type           = jet_smart_filters()->settings->get( 'provider_preloader_type', 'circle-clip-growing' );
			$this->styles         = jet_smart_filters()->settings->get( 'provider_preloader_styles', $this->get_default_styles() );
			$this->css            = jet_smart_filters()->settings->get( 'provider_preloader_css', '' );

			// Data
			$this->type_options = $this->get_type_options();
			$this->preloader_structure = $this->get_preloader_structure();

			add_action( 'wp_ajax_jet_smart_filters_get_provider_preloader_template', function() {

				if ( $_REQUEST['action'] !== 'jet_smart_filters_get_provider_preloader_template' ) {
					return false;
				}

				wp_send_json_success( array(
					'template' => $this->get_template( $_REQUEST['type'] )
				) );
			} );
		}

		public function get_template( $type = false ) {

			if ( ! $type ) {
				$type = $this->type;
			}

			$structure = isset( $this->preloader_structure[$type] )
				? $this->preloader_structure[$type]
				: false;

			ob_start();
			include jet_smart_filters()->get_template( 'provider-preloader.php' );
			$template = ob_get_clean();

			return $template;
		}

		private function get_default_styles() {
			return array(
				'use_bg'        => '',
				'size'          => 45,
				'color'         => '#007cba',
				'border_radius' => 5,
				'bg_color'      => '#fff',
				'padding'       => array(
					'top'           => 10,
					'right'         => 10,
					'bottom'        => 10,
					'left'          => 10,
					'is_linked'     => true,
				)
			);
		}

		private function get_type_options() {

			return array(
				array(
					'value' => 'circle-clip-growing',
					'label' => __( 'Circle clip growing', 'jet-smart-filters' ),
				),
				array(
					'value' => 'circle-clip',
					'label' => __( 'Circle clip', 'jet-smart-filters' ),
				),
				array(
					'value' => 'circle',
					'label' => __( 'Circle', 'jet-smart-filters' ),
				),
				array(
					'value' => 'lines-wave',
					'label' => __( 'Lines wave', 'jet-smart-filters' ),
				),
				array(
					'value' => 'lines-pulse',
					'label' => __( 'Lines pulse', 'jet-smart-filters' ),
				),
				array(
					'value' => 'lines-pulse-rapid',
					'label' => __( 'Lines pulse rapid', 'jet-smart-filters' ),
				),
				array(
					'value' => 'cube-grid',
					'label' => __( 'Cube grid', 'jet-smart-filters' ),
				),
				array(
					'value' => 'cube-folding',
					'label' => __( 'Cube folding', 'jet-smart-filters' ),
				),
				array(
					'value' => 'wordpress',
					'label' => __( 'Wordpress', 'jet-smart-filters' ),
				),
				array(
					'value' => 'hash',
					'label' => __( 'Hash', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-grid-pulse',
					'label' => __( 'Dots grid pulse', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-grid-beat',
					'label' => __( 'Dots grid beat', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-circle',
					'label' => __( 'Dots circle', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-pulse',
					'label' => __( 'Dots pulse', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-elastic',
					'label' => __( 'Dots elastic', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-carousel',
					'label' => __( 'Dots carousel', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-windmill',
					'label' => __( 'Dots windmill', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-triangle-path',
					'label' => __( 'Dots triangle path', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-bricks',
					'label' => __( 'Dots bricks', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-fire',
					'label' => __( 'Dots fire', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-rotate',
					'label' => __( 'Dots rotate', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-bouncing',
					'label' => __( 'Dots bouncing', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-chasing',
					'label' => __( 'Dots chasing', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-propagate',
					'label' => __( 'Dots propagate', 'jet-smart-filters' ),
				),
				array(
					'value' => 'dots-spin-scale',
					'label' => __( 'Dots spin scale', 'jet-smart-filters' ),
				),
			);
		}

		private function get_preloader_structure() {

			return array(
				'circle-clip'         => 1,
				'circle'              => 2,
				'lines-wave'          => 5,
				'lines-pulse'         => 5,
				'lines-pulse-rapid'   => 5,
				'cube-grid'           => 9,
				'cube-folding'        => 4,
				'wordpress'           => 2,
				'hash'                => 2,
				'dots-grid-pulse'     => 9,
				'dots-grid-beat'      => 9,
				'dots-circle'         => 12,
				'dots-pulse'          => 3,
				'dots-elastic'        => 3,
				'dots-carousel'       => 1,
				'dots-windmill'       => 3,
				'dots-triangle-path'  => 3,
				'dots-bricks'         => 3,
				'dots-fire'           => 3,
				'dots-rotate'         => 1,
				'dots-bouncing'       => 3,
				'dots-chasing'        => 1,
				'dots-propagate'      => 6,
				'dots-spin-scale'     => 2,
			);
		}
	}
}
