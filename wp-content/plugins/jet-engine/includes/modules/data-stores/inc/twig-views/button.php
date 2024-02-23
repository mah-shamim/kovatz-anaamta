<?php

namespace Jet_Engine\Modules\Data_Stores\Twig_Views;

use Jet_Engine\Modules\Data_Stores\Module as Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Button_Function extends \Jet_Engine\Timber_Views\View\Functions\Base {
	
	public function get_name() {
		return 'jet_engine_data_store';
	}

	public function get_label() {
		return __( 'Data Store Button', 'jet-engine' );
	}

	public function get_result( $args ) {

		$options_map = [
			'store'         => 'store',
			'label'         => 'label',
			'icon'          => 'icon',
			'synch_grid'    => 'synch_grid',
			'synch_grid_id' => 'synch_grid_id',
			'trigger_popup' => 'trigger_popup',
			'action'        => 'action_after_added',
			'added_label'   => 'added_to_store_label',
			'added_icon'    => 'added_to_store_icon',
			'added_url'     => 'added_to_store_url',
			'context'       => 'object_context',
		];

		$mapped_args = [
			'wrapper_css' => 'jet-data-store-link-in-template',
		];

		foreach ( $args as $key => $value ) {
			if ( isset( $options_map[ $key ] ) ) {
				$mapped_args[ $options_map[ $key ] ] = $value;
			}
		}

		$render = jet_engine()->listings->get_render_instance( 'data-store-button', $mapped_args );

		ob_start();
		$render->render_content();
		return ob_get_clean();

	}

	public function get_args() {

		$args = [];

		$args['store'] = [
			'label'       => __( 'Store', 'jet-engine' ),
			'type'        => 'select',
			'default'     => '',
			'options'     => Module::instance()->blocks_integration->get_store_options(),
			'description' => __( '<b>Warning!</b> Do not put Data Store button inside &lt;a&gt;&lt;/a&gt; tag', 'jet-engine' ),
		];

		$args['label'] = [
			'label'     => __( 'Label', 'jet-engine' ),
			'type'      => 'text',
			'default'   => '',
		];

		$args['icon'] = [
			'label'       => __( 'Icon', 'jet-engine' ),
			'type'        => 'textarea',
			'default'     => '',
			'description' => __( 'SVG or font icon', 'jet-engine' ),
		];

		$args['synch_grid'] = [
			'label'       => __( 'Reload listing grid on success', 'jet-engine' ),
			'type'        => 'switcher',
			'description' => __( 'You can use this option to reload listing grid with current Store posts on success', 'jet-engine' ),
		];

		$args['synch_grid_id'] = [
			'label'       => __( 'Listing grid ID', 'jet-engine' ),
			'type'        => 'text',
			'description' => __( 'Here you need to set listing ID to reload. The same ID must be set in the Advanced settings of selected listing', 'jet-engine' ),
			'condition'   => array(
				'synch_grid' => 'true',
			),
		];

		$args['action'] = [
			'label'     => __( 'Action after an item added to store', 'jet-engine' ),
			'type'      => 'select',
			'default'   => '',
			'options'   => [
				[
					'value' => 'remove_from_store',
					'label' => __( 'Remove from store button', 'jet-engine' ),
				],
				[
					'value' => 'switch_status',
					'label' => __( 'Switch button status', 'jet-engine' ),
				],
				[
					'value' => 'hide',
					'label' => __( 'Hide button', 'jet-engine' ),
				],
			],
		];

		$args['added_label'] = [
			'label'       => __( 'Label after added to store', 'jet-engine' ),
			'label_block' => true,
			'type'        => 'text',
			'default'     => '',
			'condition'   => array(
				'action' => [ 'switch_status', 'remove_from_store' ],
			),
		];

		$args['added_icon'] = [
			'label'       => __( 'Icon after added to store', 'jet-engine' ),
			'type'        => 'textarea',
			'default'     => '',
			'label_block' => true,
			'description' => __( 'SVG or font icon', 'jet-engine' ),
			'condition'   => array(
				'action' => [ 'switch_status', 'remove_from_store' ],
			),
		];

		$args['added_url'] = [
			'label'       => __( 'URL after added to store', 'jet-engine' ),
			'type'        => 'text',
			'default'     => '',
			'condition'   => array(
				'action' => [ 'switch_status' ],
			),
		];

		$args['context'] = [
			'label'     => __( 'Context', 'jet-engine' ),
			'type'      => 'select',
			'default'   => 'default_object',
			'options'   => jet_engine()->listings->allowed_context_list( 'blocks' ),
		];

		return $args;

	}

}