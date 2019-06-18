<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function () {
    $extensionKey = \DWenzel\T3extensionTools\Configuration\SettingsInterface::EXTENSION_KEY;
    $emConfiguration = \DWenzel\T3extensionTools\Utility\EmConfigurationUtility::getSettings();

    if ($emConfiguration->extensionServiceEnabled()) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $extensionKey . '/Resources/Private/TypoScript/ExtensionService.typoscript">');
    }
});
