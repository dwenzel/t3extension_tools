<?php

namespace DWenzel\T3extensionTools\Service;

use DWenzel\T3extensionTools\Configuration\PluginRegistrationInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Class PluginRegistrationService
 *
 * Registers plugins for usage as content element.
 */
class PluginRegistrationService
{
    use PluginProcessorTrait;

    /**
     * Constructor for PluginRegistrationService
     *
     * @param iterable<PluginRegistrationInterface> $plugins
     */
    public function __construct(
        protected iterable $plugins,
    ) {}

    /**
     * Register all plugins.
     * This method is used in EXT:t3extension_tools/Configuration/TCA/Overrides/tt_content.php
     * You must not call it in your extension.
     *
     * For each plugin a class implementing the PluginRegistrationInterface must exist
     */
    public function registerPlugins(): void
    {
        $this->processPlugins(
            $this->plugins,
            function ($plugin) {
                $pluginSignature = ExtensionUtility::registerPlugin(
                    $plugin->getExtensionName(),
                    $plugin->getPluginName(),
                    $plugin->getPluginTitle(),
                    $plugin->getPluginIcon(),
                    $plugin->getPluginGroup(),
                    $plugin->getPluginDescription(),
                );

                if (!empty($flexForm = $plugin->getFlexForm())) {
                    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
                    ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, $flexForm);
                }
            },
            PluginRegistrationInterface::class
        );
    }
}
