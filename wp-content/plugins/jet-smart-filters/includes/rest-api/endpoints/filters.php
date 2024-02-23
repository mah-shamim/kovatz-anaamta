<?php
namespace Jet_Smart_Filters\Endpoints;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Filters extends Base {

	public function get_name() {

		return 'filters';
	}

	public function get_args() {

		return array(
			'status' => array(
				'default'  => 'publish',
				'required' => false,
			),
			'page' => array(
				'default'  => 1,
				'required' => false,
			),
			'per_page' => array(
				'default'  => 20,
				'required' => false,
			),
			'orderby' => array(
				'default'  => 'date',
				'required' => false,
			),
			'order' => array(
				'default'  => 'DESC',
				'required' => false,
			),
			'search' => array(
				'default'  => '',
				'required' => false,
			),
			'date' => array(
				'default'  => array(),
				'required' => false,
			),
			'restore' => array(
				'default'  => false,
				'required' => false,
			),
			'move_to_trash' => array(
				'default'  => false,
				'required' => false,
			),
			'delete' => array(
				'default'  => false,
				'required' => false,
			)
		);
	}

	public function callback( $request ) {

		$args = $request->get_params();

		// Actions
		$restore = $args['restore'];
		if ( $restore ) {
			unset( $args['restore'] );
			jet_smart_filters()->services->filters->restore( $restore );
		}

		$move_to_trash = $args['move_to_trash'];
		if ( $move_to_trash ) {
			unset( $args['move_to_trash'] );
			jet_smart_filters()->services->filters->move_to_trash( $move_to_trash );
		}

		$delete = $args['delete'];
		if ( $delete ) {
			unset( $args['delete'] );
			jet_smart_filters()->services->filters->delete( $delete );
		}

		$update = $args['update'];
		if ( $update ) {
			unset( $args['update'] );
			if ( isset( $update['id'] ) && isset( $update['data'] ) ) {
				jet_smart_filters()->services->filter->update( $update['id'], $update['data'] );
			}
		}

		return rest_ensure_response( jet_smart_filters()->services->filters->get( $args ) );
	}
}
