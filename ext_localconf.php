<?php

defined('TYPO3') or die();

// Configure plugins from PHP classes
$extensionConfigurationService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \DWenzel\T3extensionTools\Service\PluginConfigurationService::class
);
$extensionConfigurationService->configurePlugins();

// Configure plugins from YAML files
$yamlPluginService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \DWenzel\T3extensionTools\Service\YamlPluginRegistrationService::class
);
$yamlPluginService->configurePlugins();
