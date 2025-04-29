# Plugin Configuration with YAML

This document describes how to configure and register TYPO3 plugins using YAML files instead of PHP classes.

> **Note:** Any parsing errors or issues with YAML configuration files will be logged using TYPO3's standard logging system.
>
> For the traditional class-based approach, see [Class-Based Plugin Configuration](ClassBasedPluginConfiguration.md).

## Overview

Plugin configuration files should be placed in your extension's `Configuration/Plugins` directory.
The `YamlPluginRegistrationService` will automatically discover these files and use them to configure and register your plugins.

## File Structure

Each plugin configuration file should be named descriptively (e.g., `EventList.yaml`) and follow this format:

```yaml
# Basic plugin configuration (required)
plugin:
  # Extension key (required)
  extensionName: 'MyExtension'

  # Plugin name (required) - used as identifier
  name: 'EventList'

  # Plugin type (optional, defaults to content element)
  # Possible values:
  # - 'CType' (TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT)
  # - 'list_type' (TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_PLUGIN)
  type: 'CType'

  # Controller actions (required)
  # Format: fully qualified controller class name => comma-separated list of actions without 'Action' suffix
  controllerActions:
    MyVendor\MyExtensionName\Controller\EventController: 'list,show,search'
    Category: 'list'

  # Non-cacheable controller actions (optional)
  # Format: same as controllerActions
  nonCacheableControllerActions:
      MyVendor\MyExtensionName\Controller\EventController: 'search'

# Plugin registration (optional, only needed if the plugin should appear in the backend)
registration:
  # Plugin title shown in the backend (required for registration)
  title: 'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:plugin.event_list.title'

  # Plugin description shown in the backend (optional)
  description: 'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:plugin.event_list.description'

  # Plugin icon, referring to an icon identifier (required for registration)
  icon: 'ext-myextension-event-list'

  # Plugin group in the New Content Element Wizard (optional)
  # If omitted, the plugin will appear in the "plugins" tab
  group: 'MyExtension'

  # Path to a FlexForm configuration file (optional)
  # If provided, this FlexForm will be used for the plugin configuration
  flexForm: 'FILE:EXT:my_extension/Configuration/FlexForms/EventList.xml'

# Additional configuration (optional)
advanced:
  # Custom wizard items (optional)
  # Allows defining custom wizard items for the plugin
  wizardItems:
    - id: 'myextension-event-list'
      title: 'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:wizard.event_list.title'
      description: 'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:wizard.event_list.description'
      iconIdentifier: 'ext-myextension-event-list'
      tt_content_defValues:
        CType: 'list_type'
        list_type: 'myextension_eventlist'
```

## Configuration Sections

### plugin (required)

The `plugin` section contains the basic configuration for your plugin:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| extensionName | string | Yes | The extension name (usually your extension key in CamelCase) |
| name | string | Yes | The plugin name (used as identifier) |
| type | string | No | Plugin type, either 'CType' (content element) or 'list_type' (plugin) |
| controllerActions | object | Yes | Map of controller names to comma-separated action lists |
| nonCacheableControllerActions | object | No | Map of controller names to comma-separated non-cacheable action lists |

### registration (optional)

The `registration` section is used to register the plugin in the TYPO3 backend:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| title | string | Yes | The title shown in the backend (can be a language reference) |
| description | string | No | The description shown in the backend (can be a language reference) |
| icon | string | Yes | Icon identifier for the plugin |
| group | string | No | Plugin group in the content element wizard |
| flexForm | string | No | Path to a FlexForm configuration file |

### advanced (optional)

The `advanced` section contains additional configuration options:

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| wizardItems | array | No | Custom wizard items for the plugin |

## Usage

1. Create a YAML file in your extension's `Configuration/Plugins` directory
2. Configure your plugin using the format described above
3. The `PluginConfigurationScanner` will automatically discover and process these files

## Benefits

- Declarative configuration instead of imperative PHP classes
- Easier to maintain and understand
- Separation of configuration from code
- No need to create PHP classes for simple plugin configuration
