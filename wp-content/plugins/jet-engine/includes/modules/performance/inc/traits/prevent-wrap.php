<?php
namespace Jet_Engine\Modules\Performance\Traits;

use Jet_Engine\Modules\Performance\Module;

/**
 * Trait to use optimized DOM ouput performance tweak
 */
trait Prevent_Wrap {

	public static $prevent_wrap = null;

	/**
	 * Check if optimized DOM tweak is enabled,
	 * so we prevent elements from additional wrappers
	 */
	public function prevent_wrap() {

		if ( null === self::$prevent_wrap ) {
			self::$prevent_wrap = Module::instance()->is_tweak_active( 'optimized_dom' );
		}

		return self::$prevent_wrap;

	}

}
