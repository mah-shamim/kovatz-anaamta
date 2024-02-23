<?php

/**
 * PHP Default Modular Exponentiation Engine
 *
 * PHP version 5 and 7
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2017 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://pear.php.net/package/Math_BigInteger
 *
 * Modified by woocommerce on 24-October-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Automattic\WooCommerce\Bookings\Vendor\phpseclib3\Math\BigInteger\Engines\PHP;

use Automattic\WooCommerce\Bookings\Vendor\phpseclib3\Math\BigInteger\Engines\PHP\Reductions\EvalBarrett;

/**
 * PHP Default Modular Exponentiation Engine
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
abstract class DefaultEngine extends EvalBarrett
{
}
