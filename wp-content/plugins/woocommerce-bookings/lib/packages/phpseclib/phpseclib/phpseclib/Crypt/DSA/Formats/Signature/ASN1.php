<?php

/**
 * ASN1 Signature Handler
 *
 * PHP version 5
 *
 * Handles signatures in the format described in
 * https://tools.ietf.org/html/rfc3279#section-2.2.2
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

use Automattic\WooCommerce\Bookings\Vendor\phpseclib3\File\ASN1 as Encoder;
use Automattic\WooCommerce\Bookings\Vendor\phpseclib3\File\ASN1\Maps;
use Automattic\WooCommerce\Bookings\Vendor\phpseclib3\Math\BigInteger;

/**
 * ASN1 Signature Handler
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
abstract class ASN1
{
    /**
     * Loads a signature
     *
     * @param string $sig
     * @return array|bool
     */
    public static function load($sig)
    {
        if (!is_string($sig)) {
            return false;
        }

        $decoded = Encoder::decodeBER($sig);
        if (empty($decoded)) {
            return false;
        }
        $components = Encoder::asn1map($decoded[0], Maps\DssSigValue::MAP);

        return $components;
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
        return Encoder::encodeDER(compact('r', 's'), Maps\DssSigValue::MAP);
    }
}
