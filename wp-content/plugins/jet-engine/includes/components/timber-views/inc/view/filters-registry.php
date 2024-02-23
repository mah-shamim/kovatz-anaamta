<?php
/**
 * Timber editor render class
 */
namespace Jet_Engine\Timber_Views\View;

use Jet_Engine\Timber_Views\Package;
use Twig\TwigFilter;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Filters_Registry {

	private $_filters = [];
	
	public function __construct() {

		$this->register_filters();

		add_filter( 'timber/twig', [ $this, 'add_filters' ] );
	}

	public function register_filters() {
		$this->_filters = [
			'jet_engine_callback' => [
				'name' => 'jet_engine_callback',
				'label' => __( 'JetEngine Callback', 'jet-engine' ),
				'description' => __( 'Apply one of JetEngine callbacks (same callbacks as for Dynamic Field widget)', 'jet-engine' ),
				'variadic' => true,
				'args' => array_merge( [
					'cb' => [
						'label'   => __( 'Callback function', 'jet-engine' ),
						'type'    => 'select',
						'default' => '',
						'options' => \Jet_Engine_Tools::prepare_list_for_js( 
							jet_engine()->listings->get_allowed_callbacks(), 
							ARRAY_A 
						),
					]
				], $this->get_prepared_callback_args() )
			],
			'resize' => [
				'name' => 'resize',
				'note' => __( 'Works only with image URLs, not IDs', 'jet-engine' ),
				'label' => __( 'Resize Image', 'jet-engine' ),
				'description' => __( 'Allow to resize given image by its URL', 'jet-engine' ),
				'args' => [
					'width' => [
						'label'   => __( 'Image width', 'jet-engine' ),
						'type'    => 'text',
						'default' => '',
						'description' => __( 'Number or pre-defined WP image size', 'jet-engine' ),
					],
					'height' => [
						'label'   => __( 'Image height', 'jet-engine' ),
						'type'    => 'text',
						'default' => '',
						'description' => __( 'Ignored if width is WP image size', 'jet-engine' ),
					]
				],
			],
			'excerpt' => [
				'name' => 'excerpt',
				'label' => __( 'Trim text by words', 'jet-engine' ),
				'description' => __( 'Allow to trim text to a certain number of words', 'jet-engine' ),
				'args' => [
					'num_words' => [
						'label'   => __( 'Number of words', 'jet-engine' ),
						'type'    => 'text',
						'default' => '',
					],
					'more' => [
						'label'   => __( 'More string', 'jet-engine' ),
						'type'    => 'text',
						'default' => '',
						'description' => __( 'What to append if text needs to be trimmed', 'jet-engine' ),
					],
				],
			],
			'excerpt_chars' => [
				'name' => 'excerpt_chars',
				'label' => __( 'Trim text by chars', 'jet-engine' ),
				'description' => __( 'Allow to trim text to a certain number of characters', 'jet-engine' ),
				'args' => [
					'num_chars' => [
						'label'   => __( 'Number of characters', 'jet-engine' ),
						'type'    => 'text',
						'default' => '',
					],
					'more' => [
						'label'   => __( 'More string', 'jet-engine' ),
						'type'    => 'text',
						'default' => '',
						'description' => __( 'What to append if text needs to be trimmed', 'jet-engine' ),
					],
				],
			],
			'list' => [
				'name' => 'list',
				'label' => __( 'Array to list', 'jet-engine' ),
				'description' => __( 'Converts array into a string lis with given delimiter', 'jet-engine' ),
				'args' => [
					'first_delimiter' => [
						'label'       => __( 'First delimiter', 'jet-engine' ),
						'type'        => 'text',
						'default'     => '',
						'description' => __( 'Applied for the all array items except last', 'jet-engine' ),
					],
					'second_delimiter' => [
						'label'   => __( 'Second delimiter', 'jet-engine' ),
						'type'    => 'text',
						'default' => '',
						'description' => __( 'Applied before the last item', 'jet-engine' ),
					],
				],
			],
			'time_ago' => [
				'name'  => 'time_ago',
				'label' => __( 'Time Ago', 'jet-engine' ),
				'description' => __( 'Returns the difference between two times in a human readable format', 'jet-engine' ),
				'args' => [
					'to_date' => [
						'label'       => __( 'Date to calculate difference to', 'jet-engine' ),
						'type'        => 'text',
						'default'     => '',
						'description' => __( 'Date string in strtotime()-readable format', 'jet-engine' ),
					],
					'format_past' => [
						'label'   => __( 'Format of the past dates', 'jet-engine' ),
						'type'    => 'text',
						'default' => '',
						'description' => __( 'Default: `%s ago`. %s with be reaplced with actual value.', 'jet-engine' ),
					],
					'format_future' => [
						'label'   => __( 'Format of the future dates', 'jet-engine' ),
						'type'    => 'text',
						'default' => '',
						'description' => __( 'Default: `%s from now`. %s with be reaplced with actual value.', 'jet-engine' ),
					],
				],
			],
		];
	}

	public function get_filters_for_js() {
		$filters = $this->_filters;

		foreach ( $filters['jet_engine_callback']['args'] as $key => $data ) {
			if ( ! empty( $data['options_callback'] ) && is_callable( $data['options_callback'] ) ) {
				$filters['jet_engine_callback']['args'][ $key ]['options'] = call_user_func( 
					$data['options_callback']
				);
			}
		}

		return $filters;
	}

	public function get_prepared_callback_args() {

		$args = jet_engine()->listings->get_callbacks_args( 'blocks' );

		// Related items compatibility
		// @todo Move into more suitable place
		$args['related_items_prop'] = array(
			'label'   => __( 'Related items to process<br><small>We need to select this again for the Timber/Twig compatibility reasons</small>', 'jet-engine' ),
			'type'    => 'select',
			'default' => '',
			'options_callback' => function() {
				return \Jet_Engine_Tools::prepare_list_for_js( 
					jet_engine()->relations->listing->get_prefixed_relations_sources(),
					ARRAY_A 
				);
			},
			'condition'   => array(
				'dynamic_field_filter' => 'yes',
				'filter_callback'      => array( 'jet_related_items_list' ),
			),
		);

		foreach ( $args as $key => $data ) {
			unset( $data['condition']['dynamic_field_filter'] );
			$data['condition']['cb'] = $data['condition']['filter_callback'];
			unset( $data['condition']['filter_callback'] );
			$args[ $key ] = $data;
		}

		return $args;
	}

	public function add_filters( $twig ) {
		
		$twig->addFilter( new TwigFilter( 
			'jet_engine_callback',
			[ $this, 'apply_callback' ],
			[ 'is_variadic' => true ]
		) );

		remove_filter( 'timber/twig', [ $this, 'add_filters' ] );

		return $twig;
	}

	public function apply_callback( $value = '', array $options = [] ) {

		$args = isset( $options['args'] ) ? $options['args'] : [];

		if ( ! empty( $args['cb'] ) ) {
			$value = $this->myabe_wrap_result(
				jet_engine()->listings->apply_callback( $value, $args['cb'], $args ),
				$args['cb']
			);
		}

		return $value;
	}

	public function myabe_wrap_result( $result, $cb ) {

		$print_with_wrap = [ 'jet_engine_img_gallery_slider' ];
		
		if ( ! in_array( $cb, $print_with_wrap ) ) {
			return $result;
		}

		return sprintf( 
			'<div class="jet-engine-dynamic-field" style="max-width: 100%%;" data-is-block="jet-engine/dynamic-field">%1$s</div>',
			$result
		);

	}

}
