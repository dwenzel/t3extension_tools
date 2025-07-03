<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use DWenzel\T3extensionTools\Service\PluginRegistrationService;
use DWenzel\T3extensionTools\Service\YamlPluginRegistrationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

(function (): void {
    /** @var PluginRegistrationService $pluginRegistrationService */
    $pluginRegistrationService = GeneralUtility::makeInstance(PluginRegistrationService::class);
    $pluginRegistrationService->registerPlugins();
    $yamlPluginService = GeneralUtility::makeInstance(YamlPluginRegistrationService::class);
    $yamlPluginService->registerPlugins();
})();
