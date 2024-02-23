<?php
/**
 * Timber editor render class
 */
namespace Jet_Engine\Timber_Views\View;

use Jet_Engine\Timber_Views\Package;
use Timber\Twig_Filter;
use Twig\TwigFunction;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Registry {

	public $functions;
	public $filters;

	private $_functions = [];
	
	public function __construct() {

		require_once Package::instance()->package_path( 'view/functions-registry.php' );
		require_once Package::instance()->package_path( 'view/filters-registry.php' );

		$this->functions = new Functions_Registry();
		$this->filters   = new Filters_Registry();

	}

}
