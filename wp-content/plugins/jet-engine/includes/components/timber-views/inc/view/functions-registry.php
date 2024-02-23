<?php
/**
 * Timber editor render class
 */
namespace Jet_Engine\Timber_Views\View;

use Jet_Engine\Timber_Views\Package;
use Twig\TwigFunction;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Functions_Registry {

	private $_functions = [];
	
	public function __construct() {
		add_filter( 'timber/twig', [ $this, 'add_functions' ] );
	}

	public function add_functions( $twig ) {
		
		require_once Package::instance()->package_path( 'view/functions/base.php' );
		require_once Package::instance()->package_path( 'view/functions/dynamic-data.php' );
		require_once Package::instance()->package_path( 'view/functions/dynamic-url.php' );

		$this->register_function( new Functions\Dynamic_Data() );
		$this->register_function( new Functions\Dynamic_URL() );
		
		do_action( 'jet-engine/timber-views/register-functions', $this );

		foreach ( $this->get_functions() as $function ) {
			$twig->addFunction( new TwigFunction(
				$function->get_name(),
				[ $function, 'get_result' ]
			) );
		}

		remove_filter( 'timber/twig', [ $this, 'add_functions' ] );

		return $twig;
	}

	public function get_functions() {
		return $this->_functions;
	}

	public function get_functions_for_js() {
		
		$result = [];

		foreach ( $this->get_functions() as $function ) {
			$result[ $function->get_name() ] = [
				'name' => $function->get_name(),
				'source' => 'jet-engine',
				'label' => $function->get_label(),
				'args' => $function->get_args(),
			];
		}

		// add default funcitons data
		$result['post'] = [
			'name' => 'post',
			'source' => 'default',
			'chained' => true,
			'label' => __( 'Post', 'jet-engine' ),
			'children' => [
				'ID' => [
					'label' => __( 'ID', 'jet-engine' ),
				],
				'title' => [
					'label' => __( 'Post Title', 'jet-engine' ),
				],
				'post_author' => [
					'label' => __( 'Post Author ID', 'jet-engine' ),
				],
				'post_date' => [
					'label' => __( 'Post Date', 'jet-engine' ),
				],
				'post_type' => [
					'label' => __( 'Post Type', 'jet-engine' ),
				],
				'slug' => [
					'label' => __( 'Post Slug', 'jet-engine' ),
				],
				'slug' => [
					'label' => __( 'Post Slug', 'jet-engine' ),
				],
				'css_class' => [
					'label' => __( 'Post CSS Classes String', 'jet-engine' ),
				],
				'author' => [
					'label' => __( 'Post Author Data', 'jet-engine' ),
					'children' => 'user',
				],
				'content' => [
					'label' => __( 'Post Content', 'jet-engine' ),
				],
				'edit_link' => [
					'label' => __( 'Post Edit Link', 'jet-engine' ),
				],
				'format' => [
					'label' => __( 'Post Format', 'jet-engine' ),
				],
				'link' => [
					'label' => __( 'Post URL', 'jet-engine' ),
				],
				'meta' => [
					'label' => __( 'Meta', 'jet-engine' ),
					'args' => [
						'field' => [
							'label'   => __( 'Field name', 'jet-engine' ),
							'type'    => 'text',
							'default' => '',
						],
					],
				],
				'next' => [
					'label' => __( 'Next Post Data', 'jet-engine' ),
					'children' => 'post',
				],
				'parent' => [
					'label' => __( 'Parent Post Data', 'jet-engine' ),
					'children' => 'post',
				],
				'prev' => [
					'label' => __( 'Previous Post Data', 'jet-engine' ),
					'children' => 'post',
				],
				'terms' => [
					'label' => __( 'Post Terms', 'jet-engine' ),
				],
				'thumbnail' => [
					'label' => __( 'Post Thumbnail', 'jet-engine' ),
					'children' => [
						'src' => [
							'label' => __( 'Thumbnail URL', 'jet-engine' ),
							'args' => [
								'size' => [
									'label'   => __( 'Size', 'jet-engine' ),
									'type'    => 'select',
									'default' => 'full',
									'options' => jet_engine()->listings->get_image_sizes( 'blocks' ),
								],
							],
						],
						'width' => [
							'label' => __( 'Width', 'jet-engine' ),
						],
						'height' => [
							'label' => __( 'Height', 'jet-engine' ),
						],
						'alt' => [
							'label' => __( 'Alt text', 'jet-engine' ),
						],
						'srcset' => [
							'label' => __( 'Srcset attribute', 'jet-engine' ),
							'args' => [
								'size' => [
									'label'   => __( 'Size', 'jet-engine' ),
									'type'    => 'select',
									'default' => 'full',
									'options' => jet_engine()->listings->get_image_sizes( 'blocks' ),
								],
							],
						],
						'srcset' => [
							'label' => __( 'Sizes attribute', 'jet-engine' ),
							'args' => [
								'size' => [
									'label'   => __( 'Size', 'jet-engine' ),
									'type'    => 'select',
									'default' => 'full',
									'options' => jet_engine()->listings->get_image_sizes( 'blocks' ),
								],
							],
						],
					],
				],
			],
		];

		$result['user'] = [
			'name' => 'user',
			'source' => 'default',
			'chained' => true,
			'label' => __( 'User', 'jet-engine' ),
			'children' => [
				'id' => [
					'label' => __( 'ID', 'jet-engine' ),
				],
				'name' => [
					'label' => __( 'Display Name', 'jet-engine' ),
				],
				'user_email' => [
					'label' => __( 'Email', 'jet-engine' ),
				],
				'slug' => [
					'label' => __( 'Slug (nicename)', 'jet-engine' ),
				],
				'avatar' => [
					'label' => __( 'Avatar', 'jet-engine' ),
				],
				'meta' => [
					'label' => __( 'Meta', 'jet-engine' ),
					'args' => [
						'field' => [
							'label'   => __( 'Field name', 'jet-engine' ),
							'type'    => 'text',
							'default' => '',
						],
					],
				],
			],
		];

		return $result;

	}

	public function register_function( $function_instance ) {
		$this->_functions[] = $function_instance;
	}

}
