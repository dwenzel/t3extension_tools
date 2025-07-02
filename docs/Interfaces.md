# Interfaces and Traits Documentation

This document provides a comprehensive overview of all interfaces and their corresponding trait implementations in the TYPO3 Extension Tools package.

## Overview

The extension follows a strict **Interface-Trait Pattern** where each major feature provides both an interface contract and a reusable trait implementation. This design allows for:

- **Flexible Integration**: Use interfaces for strict contracts, traits for quick implementation
- **Modular Architecture**: Components can be used independently or combined
- **Type Safety**: Strong typing through interface contracts
- **Code Reusability**: Traits provide common implementations

## Command System

### Core Command Interfaces
*Namespace: `DWenzel\T3extensionTools\Command\`*

| Interface | Implementation | Purpose | Example |
|-----------|----------------|---------|---------|
| [`ArgumentAwareInterface`][ArgumentAwareInterface] | [`ArgumentAwareTrait`][ArgumentAwareTrait] | Commands that accept arguments | [`ExampleCommand`][ExampleCommand] |
| [`OptionAwareInterface`][OptionAwareInterface] | [`OptionAwareTrait`][OptionAwareTrait] | Commands that accept options | [`ExampleCommand`][ExampleCommand] |
| [`StatusAwareInterface`][StatusAwareInterface] | - | Commands with status reporting | [`Status`][Status] |

### Command Arguments
*Namespace: `DWenzel\T3extensionTools\Command\Argument\`*

| Interface | Implementation | Purpose | Examples |
|-----------|----------------|---------|----------|
| [`InputArgumentInterface`][InputArgumentInterface] | [`InputArgumentTrait`][InputArgumentTrait] | Command argument definitions | [`AgeArgument`][AgeArgument], [`DirectoryArgument`][DirectoryArgument] |

### Command Options
*Namespace: `DWenzel\T3extensionTools\Command\Option\`*

| Interface | Implementation | Purpose | Examples |
|-----------|----------------|---------|----------|
| [`InputOptionInterface`][InputOptionInterface] | [`InputOptionTrait`][InputOptionTrait] | Command option definitions | [`ExampleOption`][ExampleOption], [`ConnectionOption`][ConnectionOption] |

## Configuration System

### Plugin Management
*Namespace: `DWenzel\T3extensionTools\Configuration\`*

| Interface | Implementation | Purpose | Service |
|-----------|----------------|---------|---------|
| [`PluginConfigurationInterface`][PluginConfigurationInterface] | [`PluginConfigurationTrait`][PluginConfigurationTrait] | Plugin configuration contracts | [`PluginConfigurationService`][PluginConfigurationService] |
| [`PluginRegistrationInterface`][PluginRegistrationInterface] | [`PluginRegistrationTrait`][PluginRegistrationTrait] | Plugin registration logic | [`PluginRegistrationService`][PluginRegistrationService] |
| [`SettingsInterface`][SettingsInterface] | - | Extension settings contract | - |

## Utility Traits

### Command Utilities
*Namespace: `DWenzel\T3extensionTools\Traits\Command\`*

| Trait | Purpose | Used By |
|-------|---------|---------|
| [`ConfigureTrait`][ConfigureTrait] | Command configuration helpers | [`ExampleCommand`][ExampleCommand] |
| [`ExecuteSqlTrait`][ExecuteSqlTrait] | SQL execution utilities | [`DeleteLogs`][DeleteLogs] |
| [`InitializeTrait`][InitializeTrait] | Command initialization | Various commands |
| [`InputPropertiesTrait`][InputPropertiesTrait] | Input property management | Command classes |

### Upgrade Wizards
*Namespace: `DWenzel\T3extensionTools\Traits\Upgrade\`*

| Trait | Purpose | Interface Pattern |
|-------|---------|-------------------|
| [`DescriptionTrait`][DescriptionTrait] | Wizard descriptions | Upgrade wizards |
| [`IdentifierTrait`][IdentifierTrait] | Wizard identifiers | Upgrade wizards |
| [`PrerequisitesTrait`][PrerequisitesTrait] | Prerequisites checking | Upgrade wizards |
| [`TitleTrait`][TitleTrait] | Wizard titles | Upgrade wizards |
| [`UpgradeWizardTrait`][UpgradeWizardTrait] | Complete wizard implementation | Upgrade wizards |

### Update/Migration Utilities
*Namespace: `DWenzel\T3extensionTools\Update\`*

| Trait | Purpose | Context |
|-------|---------|---------|
| [`DatabaseConnectionAwareTrait`][DatabaseConnectionAwareTrait] | Database connection management | Migration tasks |
| [`DescribableTrait`][DescribableTrait] | Descriptive update tasks | Update wizards |
| [`MarkingDoneTrait`][MarkingDoneTrait] | Task completion tracking | Update processes |

## Service Components

### Processing Traits
*Namespace: `DWenzel\T3extensionTools\Service\`*

| Trait | Purpose | Used By |
|-------|---------|---------|
| [`PluginProcessorTrait`][PluginProcessorTrait] | Plugin processing logic | Service classes |

## Usage Examples

### Basic Plugin Configuration

```php
<?php
use DWenzel\T3extensionTools\Configuration\PluginConfigurationInterface;
use DWenzel\T3extensionTools\Configuration\PluginConfigurationTrait;

class MyPluginConfiguration implements PluginConfigurationInterface 
{
    use PluginConfigurationTrait;
    
    public function getPluginName(): string 
    {
        return 'MyPlugin';
    }
    
    public function getControllerActions(): array 
    {
        return [
            'MyController' => 'list,show,detail'
        ];
    }
}
```

### Console Command with Arguments and Options

```php
<?php
use DWenzel\T3extensionTools\Command\ArgumentAwareInterface;
use DWenzel\T3extensionTools\Command\OptionAwareInterface;
use DWenzel\T3extensionTools\Traits\Command\ArgumentAwareTrait;
use DWenzel\T3extensionTools\Traits\Command\OptionAwareTrait;
use Symfony\Component\Console\Command\Command;

class MyCommand extends Command implements ArgumentAwareInterface, OptionAwareInterface
{
    use ArgumentAwareTrait, OptionAwareTrait;
    
    protected function configure(): void
    {
        $this->setDescription('My custom command');
        $this->configureArguments();
        $this->configureOptions();
    }
}
```

### Upgrade Wizard

```php
<?php
use DWenzel\T3extensionTools\Traits\Upgrade\UpgradeWizardTrait;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class MyUpgradeWizard implements UpgradeWizardInterface
{
    use UpgradeWizardTrait;
    
    public function getIdentifier(): string
    {
        return 'myExtensionUpgrade';
    }
    
    public function getTitle(): string
    {
        return 'My Extension Upgrade';
    }
}
```

## Development Guidelines

1. **Always implement interfaces** when using the provided traits
2. **Use type declarations** for all methods and properties
3. **Follow PSR-4 namespacing** for custom implementations
4. **Document public methods** with PHPDoc blocks
5. **Test interface contracts** in your unit tests

---

## Reference Links

<!-- Command Interfaces -->
[ArgumentAwareInterface]: ../Classes/Command/ArgumentAwareInterface.php
[OptionAwareInterface]: ../Classes/Command/OptionAwareInterface.php
[StatusAwareInterface]: ../Classes/Command/StatusAwareInterface.php
[InputArgumentInterface]: ../Classes/Command/Argument/InputArgumentInterface.php
[InputOptionInterface]: ../Classes/Command/Option/InputOptionInterface.php

<!-- Command Traits -->
[ArgumentAwareTrait]: ../Classes/Traits/Command/ArgumentAwareTrait.php
[OptionAwareTrait]: ../Classes/Traits/Command/OptionAwareTrait.php
[InputArgumentTrait]: ../Classes/Traits/Command/Argument/InputArgumentTrait.php
[InputOptionTrait]: ../Classes/Traits/Command/Option/InputOptionTrait.php
[ConfigureTrait]: ../Classes/Traits/Command/ConfigureTrait.php
[ExecuteSqlTrait]: ../Classes/Traits/Command/ExecuteSqlTrait.php
[InitializeTrait]: ../Classes/Traits/Command/InitializeTrait.php
[InputPropertiesTrait]: ../Classes/Traits/Command/InputPropertiesTrait.php

<!-- Configuration Interfaces -->
[PluginConfigurationInterface]: ../Classes/Configuration/PluginConfigurationInterface.php
[PluginRegistrationInterface]: ../Classes/Configuration/PluginRegistrationInterface.php
[SettingsInterface]: ../Classes/Configuration/SettingsInterface.php

<!-- Configuration Traits -->
[PluginConfigurationTrait]: ../Classes/Configuration/PluginConfigurationTrait.php
[PluginRegistrationTrait]: ../Classes/Configuration/PluginRegistrationTrait.php

<!-- Service Classes -->
[PluginConfigurationService]: ../Classes/Service/PluginConfigurationService.php
[PluginRegistrationService]: ../Classes/Service/PluginRegistrationService.php
[PluginProcessorTrait]: ../Classes/Service/PluginProcessorTrait.php

<!-- Upgrade Traits -->
[DescriptionTrait]: ../Classes/Traits/Upgrade/DescriptionTrait.php
[IdentifierTrait]: ../Classes/Traits/Upgrade/IdentifierTrait.php
[PrerequisitesTrait]: ../Classes/Traits/Upgrade/PrerequisitesTrait.php
[TitleTrait]: ../Classes/Traits/Upgrade/TitleTrait.php
[UpgradeWizardTrait]: ../Classes/Traits/Upgrade/UpgradeWizardTrait.php

<!-- Update Traits -->
[DatabaseConnectionAwareTrait]: ../Classes/Update/DatabaseConnectionAwareTrait.php
[DescribableTrait]: ../Classes/Update/DescribableTrait.php
[MarkingDoneTrait]: ../Classes/Update/MarkingDoneTrait.php

<!-- Example Classes -->
[ExampleCommand]: ../Classes/Command/ExampleCommand.php
[ExampleOption]: ../Classes/Command/Option/ExampleOption.php
[ConnectionOption]: ../Classes/Command/Option/ConnectionOption.php
[AgeArgument]: ../Classes/Command/Argument/AgeArgument.php
[DirectoryArgument]: ../Classes/Command/Argument/DirectoryArgument.php
[DeleteLogs]: ../Classes/Command/DeleteLogs.php
[Status]: ../Classes/Command/Status.php
