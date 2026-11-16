<?php

namespace DWenzel\T3extensionTools\Service;

use DWenzel\T3extensionTools\Configuration\PluginRegistrationInterface;

/**
 * Class PluginRegistrationService
 *
 * Registers plugins for usage as content element.
 */
class PluginRegistrationService
{
    use PluginProcessorTrait;

    protected PluginRegistrar $registrar;

    /**
     * @param iterable<PluginRegistrationInterface|class-string> $plugins
     */
    public function __construct(
        protected iterable $plugins,
        ?PluginRegistrar $registrar = null,
    ) {
        $this->registrar = $registrar ?? new PluginRegistrar();
    }

    /**
     * Register all plugins.
     * This method is used in EXT:t3extension_tools/Configuration/TCA/Overrides/tt_content.php
     * You must not call it in your extension.
     * For each plugin a class implementing the PluginRegistrationInterface must exist
     */
    public function registerPlugins(): void
    {
        $this->processPlugins(
            $this->plugins,
            function (PluginRegistrationInterface $plugin): void {
                $this->registrar->register($plugin);
            },
            PluginRegistrationInterface::class
        );
    }
}
