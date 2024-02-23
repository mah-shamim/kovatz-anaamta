<?php

/**
 * SSH2 Signature Handler
 *
 * PHP version 5
 *
 * Handles signatures in the format used by SSH2
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2016 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 *
 * Modified by woocommerce on 24-October-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Automattic\WooCommerce\Bookings\Vendor\phpseclib3\Crypt\DSA\Formats\Signature;

use Automattic\WooCommerce\Bookings\Vendor\phpseclib3\Common\Functions\Strings;
use Automattic\WooCommerce\Bookings\Vendor\phpseclib3\Math\BigInteger;

/**
 * SSH2 Signature Handler
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
abstract class SSH2
{
    /**
     * Loads a signature
     *
     * @param string $sig
     * @return mixed
     */
    public static function load($sig)
    {
        if (!is_string($sig)) {
            return false;
        }

        $result = Strings::unpackSSH2('ss', $sig);
        if ($result === false) {
            return false;
        }
        list($type, $blob) = $result;
        if ($type != 'ssh-dss' || strlen($blob) != 40) {
            return false;
        }

        return [
            'r' => new BigInteger(substr($blob, 0, 20), 256),
            's' => new BigInteger(substr($blob, 20), 256)
        ];
    }

    /**
     * Returns a signature in the appropriate format
     *
     * @param \Automattic\WooCommerce\Bookings\Vendor\phpseclib3\Math\BigInteger $r
     * @param \Automattic\WooCommerce\Bookings\Vendor\phpseclib3\Math\BigInteger $s
     * @return string
     */
    public static function save(BigInteger $r, BigInteger $s)
    {
        if ($r->getLength() > 160 || $s->getLength() > 160) {
            return false;
        }
        return Strings::packSSH2(
            'ss',
            'ssh-dss',
            str_pad($r->toBytes(), 20, "\0", STR_PAD_LEFT) .
            str_pad($s->toBytes(), 20, "\0", STR_PAD_LEFT)
        );
    }
}
