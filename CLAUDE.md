# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Information
- TYPO3 CMS extension targeting TYPO3 13.4 and PHP 8+
- Extension key: `t3extension_tools`
- Composer package: `dwenzel/t3extension-tools`
- License: GPL-2.0-or-later
- Provides reusable tools and patterns for TYPO3 extension development

## Build Commands
- Run tests: `composer test` or `phpunit -c Tests/Build/UnitTests.xml`
- Run single test: `phpunit -c Tests/Build/UnitTests.xml --filter TestName`
- Install dependencies: `composer install`
- Autoload generation: `composer dump-autoload`

## Code Quality Commands
- Lint all: `composer lint` (includes composer, editorconfig, PHP, rector, fractor)
- Fix all: `composer fix` (fixes composer, editorconfig, PHP, rector, fractor)
- Static Analysis: `composer sca:php` (PHPStan level 6)
- Code Quality: `composer lint:fractor` and `composer lint:rector`

### Individual Commands
- Lint PHP: `composer lint:php`
- Fix PHP: `composer fix:php`
- Lint Composer: `composer lint:composer`
- Fix Composer: `composer fix:composer`
- Lint EditorConfig: `composer lint:editorconfig`
- Fix EditorConfig: `composer fix:editorconfig`
- Lint Rector: `composer lint:rector`
- Fix Rector: `composer fix:rector`
- Lint Fractor: `composer lint:fractor`
- Fix Fractor: `composer fix:fractor`

## Project Architecture
The extension follows a modular, trait-based architecture providing reusable components:

### Core Features
- **Plugin Registration**: Services and traits for registering TYPO3 plugins
- **Console Commands**: Extensible command framework with argument and option handling
- **Configuration Management**: Interfaces and services for extension configuration
- **Upgrade Wizards**: Traits for TYPO3 upgrade wizard implementation

### Directory Structure
- `Classes/Command/` - Console command implementations and interfaces
  - `Argument/` - Command argument definitions
  - `Option/` - Command option definitions
- `Classes/Configuration/` - Plugin and extension configuration services
- `Classes/Service/` - Core services (PluginConfigurationService, PluginRegistrationService)
- `Classes/Traits/` - Reusable traits organized by functionality
  - `Command/` - Command-related traits
  - `Upgrade/` - Upgrade wizard traits
  - `UnitTests/` - Testing utilities
- `Classes/Update/` - Update/migration related traits
- `Tests/Unit/` - PHPUnit test classes

### Key Architectural Patterns
- **Interface-Trait Pattern**: Each major feature has both an interface and corresponding trait implementation
- **Service Layer**: Core functionality exposed through DI services
- **Command Pattern**: Extensible console commands with modular argument/option handling
- **Configuration as Code**: Plugin registration and configuration through PHP services

### Console Commands
- `t3extension-tools:example` - Example command showing usage patterns
- `t3extension-tools:delete-logs` - Schedulable log cleanup command

## Usage Patterns
This extension is designed to be used as a dependency in other TYPO3 extensions:

1. **Plugin Registration**: Use `PluginConfigurationInterface` and `PluginRegistrationInterface` with corresponding traits
2. **Console Commands**: Extend command classes and use argument/option traits
3. **Configuration**: Implement `SettingsInterface` for extension-specific settings
4. **Upgrade Wizards**: Use upgrade wizard traits for TYPO3 version migrations

## Testing
- PHPUnit configuration: `Tests/Build/UnitTests.xml`
- Test namespace: `DWenzel\T3extensionTools\Tests\Unit`
- Coverage reporting configured for `Classes/` directory
- Includes utilities for testing like `ResetSingletonInstancesMacro`

## Dependency Injection
- Services configured in `Configuration/Services.yaml`
- Uses TYPO3's autowiring and autoconfiguration
- Tagged services for plugin configuration and registration
- Console commands registered with appropriate tags

## Development Tools Configuration
- **PHP-CS-Fixer**: `.php-cs-fixer.dist.php` - TYPO3 coding standards configuration
- **PHPStan**: `phpstan.neon` - Static analysis at level 6
- **Rector**: `rector.php` - PHP and TYPO3 code modernization and migration
- **Fractor**: `fractor.php` - TypoScript code modernization
- **EditorConfig**: `.editorconfig` - Consistent code formatting across editors

### Tool Versions
- PHP-CS-Fixer: Uses TYPO3 coding standards package
- PHPStan: Level 6 analysis with memory limit of 1GB
- Rector: TYPO3-specific rules for code quality and migration to TYPO3 v13
- Fractor: TypoScript modernization with proper indentation (2 spaces)
- EditorConfig: Multi-format support (PHP, TypoScript, YAML, JSON, etc.)

## Code Style
- Follow TYPO3 coding standards enforced by PHP-CS-Fixer
- PSR-4 autoloading: `DWenzel\T3extensionTools\`
- Extensive use of interfaces and traits for modularity
- GPL-2.0-or-later license headers required on all files
- PHP 8.3+ features encouraged (strict types, return types, etc.)
- 4-space indentation for PHP, 2-space for TypoScript/YAML
