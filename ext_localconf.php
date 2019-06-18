<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

use DWenzel\T3extensionTools\Configuration\SettingsInterface as SI;
call_user_func(function () {
    $emConfiguration = \DWenzel\T3extensionTools\Utility\EmConfigurationUtility::getSettings();

    if ($emConfiguration->extensionServiceEnabled()) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . SI::EXTENSION_KEY . '/Resources/Private/TypoScript/ExtensionService.typoscript">');
    }
});

