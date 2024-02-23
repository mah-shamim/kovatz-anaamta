<?php

/**
 * GeneralSubtree
 *
 * PHP version 5
 *
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2016 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 *
 * Modified by woocommerce on 24-October-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Automattic\WooCommerce\Bookings\Vendor\phpseclib3\File\ASN1\Maps;

use Automattic\WooCommerce\Bookings\Vendor\phpseclib3\File\ASN1;

/**
 * GeneralSubtree
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
abstract class GeneralSubtree
{
    const MAP = [
        'type' => ASN1::TYPE_SEQUENCE,
        'children' => [
            'base' => GeneralName::MAP,
            'minimum' => [
                'constant' => 0,
                'optional' => true,
                'implicit' => true,
                'default' => '0'
            ] + BaseDistance::MAP,
            'maximum' => [
                'constant' => 1,
                'optional' => true,
                'implicit' => true,
            ] + BaseDistance::MAP
        ]
    ];
}
