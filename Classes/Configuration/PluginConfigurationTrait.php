<?php

namespace DWenzel\T3extensionTools\Configuration;

use DWenzel\T3extensionTools\Traits\PropertyAccess;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

trait PluginConfigurationTrait
{
    use PropertyAccess;
    public static function getExtensionName(): string
    {
        return self::getStaticProperty(PluginConfigurationInterface::EXTENSION_NAME);
    }
    /**
     * @return string
     * @throws InvalidConfigurationException
     */
    public static function getPluginName(): string
    {
        return self::getStaticProperty(PluginConfigurationInterface::PLUGIN_NAME);
    }

    /**
     * @return string
     * @throws InvalidConfigurationException
     */
    public static function getPluginTitle(): string
    {
        return self::getStaticProperty(PluginConfigurationInterface::PLUGIN_TITLE);
    }

    public static function getPluginIcon(): string
    {
        $pluginIcon = self::getStaticProperty(PluginConfigurationInterface::PLUGIN_ICON);
        return (!empty($pluginIcon)) ? $pluginIcon : '';
    }

    public static function getPluginGroup(): string
    {
        return self::getStaticProperty(PluginConfigurationInterface::PLUGIN_GROUP);
    }

    public static function getPluginType(): string
    {
        $pluginType = self::getStaticProperty(PluginConfigurationInterface::PLUGIN_TYPE);
        return (!empty($pluginType)) ? $pluginType : ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT;
    }

    /**
     * @return string
     * @throws InvalidConfigurationException
     */
    public static function getFlexForm(): string
    {
        return self::getStaticProperty(PluginConfigurationInterface::FLEX_FORM);
    }

    /**
     * @return array
     * @throws InvalidConfigurationException
     */
    public static function getNonCacheableControllerActions(): array
    {
        $nonCacheableControllerActions = self::getStaticProperty(PluginConfigurationInterface::NON_CACHEABLE_CONTROLLER_ACTIONS);
        return (!empty($nonCacheableControllerActions)) ? $nonCacheableControllerActions : [];
    }

    /**
     * @return array
     * @throws InvalidConfigurationException
     */
    public static function getControllerActions(): array
    {
        return self::getStaticProperty(PluginConfigurationInterface::CONTROLLER_ACTIONS);
    }
}
