<?php

namespace ElementsKit\Traits;

/**
 * Trait for making singleton instance
 *
 * @package ElementsKit\Traits
 */
trait Singleton {

	private static $instance;


	public static function instance() {
		if(!self::$instance) {
			self::$instance = new static();
		}

		return self::$instance;
	}
}