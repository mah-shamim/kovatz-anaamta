<?php

namespace ElementskitVendor;

// For older (pre-2.7.2) verions of google/apiclient
if (\file_exists(__DIR__ . '/../apiclient/src/Google/Client.php') && !\class_exists('ElementskitVendor\\ElementskitVendor_Google_Client', \false)) {
    require_once __DIR__ . '/../apiclient/src/Google/Client.php';
    if (\defined('ElementskitVendor_Google_Client::LIBVER') && \version_compare(ElementskitVendor_Google_Client::LIBVER, '2.7.2', '<=')) {
        $servicesClassMap = ['ElementskitVendor\\Google\\Client' => 'ElementskitVendor_Google_Client', 'ElementskitVendor\\Google\\Service' => 'Google_Service', 'ElementskitVendor\\Google\\Service\\Resource' => 'ElementskitVendor_Google_Service_Resource', 'ElementskitVendor\\Google\\Model' => 'ElementskitVendor_Google_Model', 'ElementskitVendor\\Google\\Collection' => 'ElementskitVendor_Google_Collection'];
        foreach ($servicesClassMap as $alias => $class) {
            \class_alias($class, $alias);
        }
    }
}
\spl_autoload_register(function ($class) {
    if (0 === \strpos($class, 'Google_Service_')) {
        // Autoload the new class, which will also create an alias for the
        // old class by changing underscores to namespaces:
        //     Google_Service_Speech_Resource_Operations
        //      => Google\Service\Speech\Resource\Operations
        $classExists = \class_exists($newClass = \str_replace('_', '\\', $class));
        if ($classExists) {
            return \true;
        }
    }
}, \true, \true);
