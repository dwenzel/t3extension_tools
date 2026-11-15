<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\TestPlugins\Plugin;

use DWenzel\T3extensionTools\Configuration\PluginConfigurationTrait;
use DWenzel\T3extensionTools\Configuration\PluginRegistrationInterface;
use DWenzel\T3extensionTools\Configuration\PluginRegistrationTrait;
use DWenzel\T3extensionTools\TestPlugins\Controller\ListController;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

/**
 * Plugin defined by a PHP class. The constructor dependency makes sure
 * the instance built by the container is used.
 */
class PhpListPlugin implements PluginRegistrationInterface
{
    use PluginConfigurationTrait;
    use PluginRegistrationTrait;

    protected string $extensionName = 'TestPlugins';
    protected string $pluginName = 'PhpList';
    protected string $pluginType = ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT;
    /** @var array<string, string> */
    protected array $controllerActions = [ListController::class => 'list,show'];
    /** @var array<string, string> */
    protected array $nonCacheableControllerActions = [ListController::class => 'show'];
    protected string $pluginTitle = 'PHP list plugin';
    protected string $pluginDescription = 'Plugin defined by a PHP class';
    protected string $pluginIcon = 'content-plugin';
    protected string $pluginGroup = 'plugins';
    protected string $flexForm = 'FILE:EXT:test_plugins/Configuration/FlexForms/List.xml';

    public function __construct(
        protected readonly PackageManager $packageManager,
    ) {}
}
