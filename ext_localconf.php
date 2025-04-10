<?php

defined('TYPO3') or die();

$extensionConfigurationService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \DWenzel\T3extensionTools\Service\PluginConfigurationService::class
);
$extensionConfigurationService->configurePlugins();
