<?php

/** @noinspection StaticClosureCanBeUsedInspection */

use DWenzel\T3extensionTools\Service\PluginRegistrationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

(function ($extKey = 't3extension_tools', $table = 'tt_content'): void {
    /** @var PluginRegistrationService $pluginRegistrationService */
    $pluginRegistrationService = GeneralUtility::makeInstance(
        PluginRegistrationService::class
    );

    $pluginRegistrationService->registerPlugins();

})();
