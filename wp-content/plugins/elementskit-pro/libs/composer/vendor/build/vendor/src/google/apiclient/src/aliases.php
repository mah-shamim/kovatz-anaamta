<?php

namespace ElementskitVendor;

if (\class_exists('ElementskitVendor\\ElementskitVendor_Google_Client', \false)) {
    // Prevent error with preloading in PHP 7.4
    // @see https://github.com/googleapis/google-api-php-client/issues/1976
    return;
}
$classMap = ['ElementskitVendor\\Google\\Client' => 'ElementskitVendor_Google_Client', 'ElementskitVendor\\Google\\Service' => 'Google_Service', 'ElementskitVendor\\Google\\AccessToken\\Revoke' => 'ElementskitVendor_Google_AccessToken_Revoke', 'ElementskitVendor\\Google\\AccessToken\\Verify' => 'ElementskitVendor_Google_AccessToken_Verify', 'ElementskitVendor\\Google\\Model' => 'ElementskitVendor_Google_Model', 'ElementskitVendor\\Google\\Utils\\UriTemplate' => 'ElementskitVendor_Google_Utils_UriTemplate', 'ElementskitVendor\\Google\\AuthHandler\\Guzzle6AuthHandler' => 'ElementskitVendor_Google_AuthHandler_Guzzle6AuthHandler', 'ElementskitVendor\\Google\\AuthHandler\\Guzzle7AuthHandler' => 'ElementskitVendor_Google_AuthHandler_Guzzle7AuthHandler', 'ElementskitVendor\\Google\\AuthHandler\\Guzzle5AuthHandler' => 'ElementskitVendor_Google_AuthHandler_Guzzle5AuthHandler', 'ElementskitVendor\\Google\\AuthHandler\\AuthHandlerFactory' => 'ElementskitVendor_Google_AuthHandler_AuthHandlerFactory', 'ElementskitVendor\\Google\\Http\\Batch' => 'ElementskitVendor_ElementskitVendor_Google_Http_Batch', 'ElementskitVendor\\Google\\Http\\MediaFileUpload' => 'ElementskitVendor_Google_Http_MediaFileUpload', 'ElementskitVendor\\Google\\Http\\REST' => 'ElementskitVendor_Google_Http_REST', 'ElementskitVendor\\Google\\Task\\Retryable' => 'ElementskitVendor_Google_Task_Retryable', 'ElementskitVendor\\Google\\Task\\Exception' => 'ElementskitVendor_Google_Task_Exception', 'ElementskitVendor\\Google\\Task\\Runner' => 'ElementskitVendor_Google_Task_Runner', 'ElementskitVendor\\Google\\Collection' => 'ElementskitVendor_Google_Collection', 'ElementskitVendor\\Google\\Service\\Exception' => 'ElementskitVendor_Google_Service_Exception', 'ElementskitVendor\\Google\\Service\\Resource' => 'ElementskitVendor_Google_Service_Resource', 'ElementskitVendor\\Google\\Exception' => 'ElementskitVendor_Google_Exception'];
foreach ($classMap as $class => $alias) {
    \class_alias($class, $alias);
}
/**
 * This class needs to be defined explicitly as scripts must be recognized by
 * the autoloader.
 */
class ElementskitVendor_Google_Task_Composer extends \ElementskitVendor\Google\Task\Composer
{
}
/**
 * This class needs to be defined explicitly as scripts must be recognized by
 * the autoloader.
 */
\class_alias('ElementskitVendor\\ElementskitVendor_Google_Task_Composer', 'ElementskitVendor_Google_Task_Composer', \false);
if (\false) {
    class ElementskitVendor_Google_AccessToken_Revoke extends \ElementskitVendor\Google\AccessToken\Revoke
    {
    }
    class ElementskitVendor_Google_AccessToken_Verify extends \ElementskitVendor\Google\AccessToken\Verify
    {
    }
    class ElementskitVendor_Google_AuthHandler_AuthHandlerFactory extends \ElementskitVendor\Google\AuthHandler\AuthHandlerFactory
    {
    }
    class ElementskitVendor_Google_AuthHandler_Guzzle5AuthHandler extends \ElementskitVendor\Google\AuthHandler\Guzzle5AuthHandler
    {
    }
    class ElementskitVendor_Google_AuthHandler_Guzzle6AuthHandler extends \ElementskitVendor\Google\AuthHandler\Guzzle6AuthHandler
    {
    }
    class ElementskitVendor_Google_AuthHandler_Guzzle7AuthHandler extends \ElementskitVendor\Google\AuthHandler\Guzzle7AuthHandler
    {
    }
    class ElementskitVendor_Google_Client extends \ElementskitVendor\Google\Client
    {
    }
    class ElementskitVendor_Google_Collection extends \ElementskitVendor\Google\Collection
    {
    }
    class ElementskitVendor_Google_Exception extends \ElementskitVendor\Google\Exception
    {
    }
    class ElementskitVendor_ElementskitVendor_Google_Http_Batch extends \ElementskitVendor\Google\Http\Batch
    {
    }
    class ElementskitVendor_Google_Http_MediaFileUpload extends \ElementskitVendor\Google\Http\MediaFileUpload
    {
    }
    class ElementskitVendor_Google_Http_REST extends \ElementskitVendor\Google\Http\REST
    {
    }
    class ElementskitVendor_Google_Model extends \ElementskitVendor\Google\Model
    {
    }
    class Google_Service extends \ElementskitVendor\Google\Service
    {
    }
    class ElementskitVendor_Google_Service_Exception extends \ElementskitVendor\Google\Service\Exception
    {
    }
    class ElementskitVendor_Google_Service_Resource extends \ElementskitVendor\Google\Service\Resource
    {
    }
    class ElementskitVendor_Google_Task_Exception extends \ElementskitVendor\Google\Task\Exception
    {
    }
    interface ElementskitVendor_Google_Task_Retryable extends \ElementskitVendor\Google\Task\Retryable
    {
    }
    class ElementskitVendor_Google_Task_Runner extends \ElementskitVendor\Google\Task\Runner
    {
    }
    class ElementskitVendor_Google_Utils_UriTemplate extends \ElementskitVendor\Google\Utils\UriTemplate
    {
    }
}
