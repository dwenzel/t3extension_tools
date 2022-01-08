Interfaces
==========

## Commands 
namespace `DWenzel\T3extensionTools\Command\`

| interface                                                            | implementation                         | example  |
|----------------------------------------------------------------------|----------------------------------------|-------------|
| [`OptionAwareInterface`][OptionAwareInterface]                       |[`OptionAwareTrait`][OptionAwareTrait]  | [`ExampleCommand`][ExampleCommand]            |
| [`Option\InputOptionInterface`][InputOptionInterface]            |[`Option\InputOptionTrait`][InputOptionTrait] | [`ExampleOption`][ExampleOption], [`ConnectionOption][ConnectionOption] |


## Configuration
namespace `DWenzel\T3extensionTools\Configuration\`

| interface                                                            | implementation                                        |
|----------------------------------------------------------------------|-------------------------------------------------------|
|[`ControllerRegistrationInterface`][ControllerRegistrationInterface]  | [`ControllerActionsTrait`][ControllerActionsTrait]    |
|[`ModuleRegistrationInterface`][ModuleRegistrationInterface]          | [`ModuleRegistrationTrait`][ModuleRegistrationTrait]  |
|[`PluginConfigurationInterface`][PluginConfigurationInterface]        | [`PluginRegistrationTrait`][PluginConfigurationTrait] |

[OptionAwareInterface]:../Classes/Command/OptionAwareInterface.php
[OptionAwareTrait]: ../Classes/Traits/Command/OptionAwareTrait.php
[InputOptionInterface]: ../Classes/Command/Option/InputOptionInterface.php
[InputOptionTrait]: ../Classes/Traits/Command/Option/InputOptionTrait.php`
[ConnectionOption]: ../Classes/Command/Option/ConnectionOption.php
[ControllerRegistrationInterface]: ../Classes/Configuration/ControllerRegistrationInterface.php
[ControllerActionsTrait]: ../Classes/Configuration/ControllerActionsTrait.php
[ExampleCommand]: ../Classes/Command/ExampleCommand.php
[ExampleOption]: ../Classes/Command/Option/ExampleOption.php
[ModuleRegistrationInterface]: ../Classes/Configuration/ModuleRegistrationInterface.php
[ModuleRegistrationTrait]: ../Classes/Configuration/ModuleRegistrationTrait.php
[PluginConfigurationInterface]: ../Classes/Configuration/PluginConfigurationInterface.php
[PluginConfigurationTrait]: ../Classes/Configuration/PluginConfigurationTrait.php
