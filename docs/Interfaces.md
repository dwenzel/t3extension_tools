Interfaces
==========

## Commands 
namespace `DWenzel\T3extensionTools\Command\`

| interface                                                            | implementation                         | example  |
|----------------------------------------------------------------------|----------------------------------------|-------------|
| [`OptionAwareInterface`][OptionAwareInterface]                       |[`OptionAwareTrait`][OptionAwareTrait]  | [`ExampleCommand`][ExampleCommand]            |
| [`Option\CommandOptionInterface`][CommandOptionInterface]            |[`Option\CommandOptionTrait`][CommandOptionTrait] | [`ExampleOption`][ExampleOption], [`ConnectionOption][ConnectionOption] |


## Configuration
namespace `DWenzel\T3extensionTools\Configuration\`

| interface                                                            | implementation                                        |
|----------------------------------------------------------------------|-------------------------------------------------------|
|[`ControllerRegistrationInterface`][ControllerRegistrationInterface]  | [`ControllerActionsTrait`][ControllerActionsTrait]    |
|[`ModuleRegistrationInterface`][ModuleRegistrationInterface]          | [`ModuleRegistrationTrait`][ModuleRegistrationTrait]  |
|[`PluginConfigurationInterface`][PluginConfigurationInterface]        | [`PluginRegistrationTrait`][PluginConfigurationTrait] |

[OptionAwareInterface]:../Classes/Command/OptionAwareInterface.php
[OptionAwareTrait]: ../Classes/Traits/Command/OptionAwareTrait.php
[CommandOptionInterface]: ../Classes/Command/Option/CommandOptionInterface.php
[CommandOptionTrait]: ../Classes/Traits/Command/Option/CommandOptionTrait.php`
[ConnectionOption]: ../Classes/Command/Option/ConnectionOption.php
[ControllerRegistrationInterface]: ../Classes/Configuration/ControllerRegistrationInterface.php
[ControllerActionsTrait]: ../Classes/Configuration/ControllerActionsTrait.php
[ExampleCommand]: ../Classes/Command/ExampleCommand.php
[ExampleOption]: ../Classes/Command/Option/ExampleOption.php
[ModuleRegistrationInterface]: ../Classes/Configuration/ModuleRegistrationInterface.php
[ModuleRegistrationTrait]: ../Classes/Configuration/ModuleRegistrationTrait.php
[PluginConfigurationInterface]: ../Classes/Configuration/PluginConfigurationInterface.php
[PluginConfigurationTrait]: ../Classes/Configuration/PluginConfigurationTrait.php
