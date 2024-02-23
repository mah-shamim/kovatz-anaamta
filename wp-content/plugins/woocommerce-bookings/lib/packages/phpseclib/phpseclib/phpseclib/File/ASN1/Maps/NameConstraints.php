<?php

/**
 * NameConstraints
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
 * NameConstraints
 *
 * @author  Jim Wigginton <terrafrost@php.net>
 */
abstract class NameConstraints
{
    const MAP = [
        'type' => ASN1::TYPE_SEQUENCE,
        'children' => [
            'permittedSubtrees' => [
                'constant' => 0,
                'optional' => true,
                'implicit' => true
            ] + GeneralSubtrees::MAP,
            'excludedSubtrees' => [
                'constant' => 1,
                'optional' => true,
                'implicit' => true
            ] + GeneralSubtrees::MAP
        ]
    ];
}
