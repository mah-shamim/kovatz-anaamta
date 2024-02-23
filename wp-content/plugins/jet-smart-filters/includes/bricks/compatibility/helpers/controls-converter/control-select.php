<?php

namespace Jet_Engine\Bricks_Views\Helpers\Controls_Converter;

use Jet_Engine\Bricks_Views\Helpers\Options_Converter;

class Control_Select extends Base {

	public function parse_callback_arguments( $args = [] ) {
		$placeholder = '';
		$css         = [];
		$required    = [];

		if ( $args['type'] === 'jet-query' ) {
			$args['type']       = 'select';
			$args['options']    = $this->get_posts_options( $args );
			unset( $args['query_type'], $args['query'] );
		}

		if ( array_key_exists( 'groups', $args ) ) {
			$args['options'] = Options_Converter::convert_select_groups_to_options($args['groups']);
			unset( $args['groups'] );
		}

		if ( ! empty( $args['options'] ) && array_key_exists( '', $args['options'] ) ) {
			$placeholder = $args['options'][''];
			unset( $args['options'][''] );
		}

		if ( array_key_exists( 'selectors', $args ) ) {
			$css = $this->parse_callback_argument_selectors( $args['selectors'] );
			unset( $args['selectors'] );
		}

		if ( array_key_exists( 'condition', $args ) ) {
			$required = $this->parse_callback_argument_condition( $args['condition'] );
			unset( $args['condition'] );
		}

		return array_merge(
			[ 'tab' => 'content' ],
			$args,
			$placeholder ? [ 'placeholder' => esc_html__( $placeholder, 'jet-engine' ) ] : [],
			$css ? [ 'css' => $css ] : [],
			$required ? [ 'required' => $required ] : [],
		);
	}

	public static function get_posts_options( $options ) {
		$query_args = $options['query'];
		$posts      = get_posts( $query_args );
		$posts      = wp_list_pluck( $posts, 'post_title', 'ID' );

		return $posts;

	}
}