<?php
/**
 * @license MIT
 *
 * Modified by woocommerce on 24-October-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace Automattic\WooCommerce\Bookings\Vendor\GuzzleHttp\Handler;

use Automattic\WooCommerce\Bookings\Vendor\Psr\Http\Message\RequestInterface;

interface CurlFactoryInterface
{
    /**
     * Creates a cURL handle resource.
     *
     * @param RequestInterface $request Request
     * @param array            $options Transfer options
     *
     * @throws \RuntimeException when an option cannot be applied
     */
    public function create(RequestInterface $request, array $options): EasyHandle;

    /**
     * Release an easy handle, allowing it to be reused or closed.
     *
     * This function must call unset on the easy handle's "handle" property.
     */
    public function release(EasyHandle $easy): void;
}
