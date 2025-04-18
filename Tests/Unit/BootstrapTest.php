<?php

// Register composer autoloader
require_once '/var/www/html/app/vendor/autoload.php';

// Define TYPO3_MODE and TYPO3_REQUESTTYPE if not defined
if (!defined('TYPO3_MODE')) {
    define('TYPO3_MODE', 'BE');
}
if (!defined('TYPO3_REQUESTTYPE')) {
    define('TYPO3_REQUESTTYPE', 1);
}

// Add extension autoloader path
$classLoader = new \Composer\Autoload\ClassLoader();
$classLoader->addPsr4('DWenzel\\T3extensionTools\\', [__DIR__ . '/../../Classes']);
$classLoader->addPsr4('DWenzel\\T3extensionTools\\Tests\\', [__DIR__ . '/../../Tests']);
$classLoader->register();