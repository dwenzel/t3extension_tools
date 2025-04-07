<?php /** @noinspection StaticClosureCanBeUsedInspection */

use DWenzel\T3extensionTools\Service\PluginConfigurationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

(function ($extKey = 't3extension_tools', $table = 'tt_content'): void {
    /** @var PluginConfigurationService $pluginConfigurationService */
    $pluginConfigurationService = GeneralUtility::makeInstance(
        PluginConfigurationService::class
    );

    $pluginConfigurationService->registerPlugins();

})();
