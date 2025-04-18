# Class-Based Plugin Configuration

This document describes how to configure and register TYPO3 plugins using PHP classes.

## Overview

The traditional approach to configure and register TYPO3 plugins is to create PHP classes that implement specific interfaces provided by this extension.

## Basic Plugin Configuration

To configure a plugin, create a class that implements the `PluginConfigurationInterface`:

```php
<?php

namespace MyVendor\MyExtension\Configuration;

use DWenzel\T3extensionTools\Configuration\PluginConfigurationInterface;
use DWenzel\T3extensionTools\Configuration\PluginConfigurationTrait;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

class MyPluginConfiguration implements PluginConfigurationInterface
{
    use PluginConfigurationTrait;

    protected string $extensionName = 'MyExtension';
    protected string $pluginName = 'MyPlugin';
    protected string $pluginType = ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT; // or ExtensionUtility::PLUGIN_TYPE_PLUGIN
    
    protected array $controllerActions = [
        'MyController' => 'list,show,search'
    ];
    
    protected array $nonCacheableControllerActions = [
        'MyController' => 'search'
    ];
}
```

## Plugin Registration for Backend

If you want your plugin to appear in the TYPO3 backend, implement the `PluginRegistrationInterface`:

```php
<?php

namespace MyVendor\MyExtension\Configuration;

use DWenzel\T3extensionTools\Configuration\PluginConfigurationTrait;
use DWenzel\T3extensionTools\Configuration\PluginRegistrationInterface;
use DWenzel\T3extensionTools\Configuration\PluginRegistrationTrait;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

class MyPluginRegistration implements PluginRegistrationInterface
{
    use PluginConfigurationTrait;
    use PluginRegistrationTrait;

    protected string $extensionName = 'MyExtension';
    protected string $pluginName = 'MyPlugin';
    protected string $pluginType = ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT;
    
    protected array $controllerActions = [
        'MyController' => 'list,show,search'
    ];
    
    protected array $nonCacheableControllerActions = [
        'MyController' => 'search'
    ];
    
    // Registration properties
    protected string $pluginTitle = 'My Plugin Title';
    protected string $pluginDescription = 'Description for my plugin';
    protected string $pluginIcon = 'EXT:my_extension/Resources/Public/Icons/Plugin.svg';
    protected string $pluginGroup = 'MyExtension';
    protected string $flexForm = 'FILE:EXT:my_extension/Configuration/FlexForms/MyPlugin.xml';
}
```

## Configuration Properties

### Basic Configuration

| Property | Description |
|----------|-------------|
| extensionName | The extension name (CamelCase) |
| pluginName | The plugin name (CamelCase) |
| pluginType | The plugin type, either ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT or ExtensionUtility::PLUGIN_TYPE_PLUGIN |
| controllerActions | Array mapping controller names to comma-separated action lists |
| nonCacheableControllerActions | Array mapping controller names to comma-separated non-cacheable action lists |

### Registration Configuration

| Property | Description |
|----------|-------------|
| pluginTitle | The title shown in the backend (can be a language reference) |
| pluginDescription | The description shown in the backend (can be a language reference) |
| pluginIcon | Icon identifier or path for the plugin |
| pluginGroup | Plugin group in the content element wizard |
| flexForm | Path to a FlexForm configuration file |

## Using the Configuration Classes

The extension automatically finds and uses classes that implement these interfaces. To make your classes available to the service, tag them in your `Services.yaml` file:

```yaml
# Configuration/Services.yaml
services:
  _defaults:
    autowire: true
    autoconfigure: true
    public: false

  MyVendor\MyExtension\Configuration\MyPluginConfiguration:
    tags:
      - 't3extensionTools.pluginConfiguration'
      
  MyVendor\MyExtension\Configuration\MyPluginRegistration:
    tags:
      - 't3extensionTools.pluginRegistration'
```

The configuration and registration services provided by this extension will automatically process your plugin classes and register them with TYPO3.

## Benefits of Class-Based Configuration

- Full PHP functionality and flexibility
- Type hinting and IDE support
- Ability to inherit and extend configurations
- Integration with Dependency Injection

## See Also

- [YAML-based Plugin Configuration](PluginConfiguration.md) for a simpler approach