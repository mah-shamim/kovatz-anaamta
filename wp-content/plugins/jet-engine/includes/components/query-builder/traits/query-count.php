<?php
namespace Jet_Engine\Query_Builder\Traits;

use Jet_Engine\Query_Builder\Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

trait Query_Count_Trait {

	public function get_title() {
		return esc_html__( 'Query Results Count', 'jet-engine' );
	}

	public function get_args() {
		return array(
			'query_id' => array(
				'label'   => esc_html__( 'Query', 'jet-engine' ),
				'type'    => 'select',
				'options' => Manager::instance()->get_queries_for_options(),
			),
			'count_type' => array(
				'label'       => esc_html__( 'Returned Count', 'jet-engine' ),
				'type'        => 'select',
				'label_block' => true,
				'default'     => 'total',
				'options'     => array(
					'total'         => esc_html__( 'Total query results count', 'jet-engine' ),
					'visible'       => esc_html__( 'Currently visible query results count (per page)', 'jet-engine' ),
					'custom_format' => esc_html__( 'Custom format for several counters', 'jet-engine' ),
				),
			),
			'custom_format' => array(
				'label'       => esc_html__( 'Format', 'jet-engine' ),
				'label_block' => true,
				'type'        => 'textarea',
				'default'     => esc_html__( 'Showing %start-item%-%end-item% of %total% items', 'jet-engine' ),
				'description' => $this->get_format_description(),
				'has_html'    => true,
				'condition'   => array(
					'count_type' => array( 'custom_format' ),
				),
			)
		);
	}

	public function get_format_description() {
		$html = '<b>' . esc_html__( 'Available macros:', 'jet-engine' ) . '</b>';
		$html .= '<ul>';
			$html .= '<li><code>%total%</code> - ' . esc_html__( 'Total query results count', 'jet-engine' ) . '</li>';
			$html .= '<li><code>%visible%</code> - ' . esc_html__( 'Currently visible query results count', 'jet-engine' ) . '</li>';
			$html .= '<li><code>%start-item%</code> - ' . esc_html__( 'Start item index on page', 'jet-engine' ) . '</li>';
			$html .= '<li><code>%end-item%</code> - ' . esc_html__( 'End item index on page', 'jet-engine' ) . '</li>';
		$html .= '</ul>';

		return $html;
	}

	public function get_result( $settings = array() ) {
		$result     = null;
		$query_id   = ! empty( $settings['query_id'] ) ? $settings['query_id'] : false;
		$count_type = ! empty( $settings['count_type'] ) ? $settings['count_type'] : false;

		if ( 'custom_format' === $count_type ) {

			$format = ! empty( $settings['custom_format'] ) ? $settings['custom_format'] : false;

			$available_macros = array(
				'total',
				'visible',
				'start-item',
				'end-item',
			);

			if ( ! empty( $format ) ) {
				$result = preg_replace_callback( '/%([a-z_-]+)%/', function ( $matches ) use ( $query_id, $available_macros ) {

					if ( ! in_array( $matches[1], $available_macros ) ) {
						return '';
					}

					return Manager::instance()->get_query_count_html( $query_id, $matches[1] );
				}, $format );
			}

		} else {
			$result = Manager::instance()->get_query_count_html( $query_id, $count_type );
		}

		return wp_kses_post( $result );
	}

}
