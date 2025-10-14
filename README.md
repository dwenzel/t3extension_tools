# TYPO3 Extension Tools

A comprehensive toolkit for TYPO3 CMS extension development, providing reusable components, interfaces, and development tools to streamline extension creation and maintenance.

## Installation

```bash
composer require dwenzel/t3extension-tools
```

**Note**: This extension is primarily distributed via Composer. TYPO3 Extension Repository updates are sporadic - always use the latest Composer version.

## Features

### Core Components
* **Plugin Registration**: Automated plugin configuration and registration services
* **Console Commands**: Extensible command framework with modular argument/option handling
* **Configuration Management**: Interfaces and services for extension configuration
* **Upgrade Wizards**: Traits for TYPO3 version migration support
* **Icon Registration**: Utilities for bitmap and SVG icon registration

### Development Tools
* **Code Quality**: Comprehensive linting and formatting tools (PHP-CS-Fixer, PHPStan, Rector)
* **Testing**: PHPUnit integration with TYPO3 Testing Framework
* **Documentation**: Automated code analysis and interface documentation
* **Standards Compliance**: TYPO3 coding standards enforcement

## Quick Start

### 1. Development Commands

```bash
# Install dependencies
composer install

# Run all quality checks
composer lint

# Fix all code quality issues
composer fix

# Run tests
composer test

# Static code analysis
composer sca:php
```

### 2. Plugin Registration Example

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
            'MyController' => 'list,show'
        ];
    }
}
```

### 3. Console Command Example

```php
<?php
use DWenzel\T3extensionTools\Command\ArgumentAwareInterface;
use DWenzel\T3extensionTools\Traits\Command\ArgumentAwareTrait;
use Symfony\Component\Console\Command\Command;

class MyCommand extends Command implements ArgumentAwareInterface
{
    use ArgumentAwareTrait;

    protected function configure(): void
    {
        $this->setDescription('My custom command');
        $this->configureArguments();
    }
}
```

## Architecture

### Interface-Trait Pattern
Each major feature provides both an interface and a corresponding trait implementation, allowing for flexible integration while maintaining strict contracts.

### Service Layer
Core functionality is exposed through dependency injection services, making it easy to extend and customize behavior.

### Modular Design
Components are designed to be used independently or together, providing maximum flexibility for different use cases.

## Development Workflow

### Code Quality Assurance

The extension includes comprehensive code quality tools:

| Tool | Purpose | Command |
|------|---------|---------|
| PHP-CS-Fixer | Code formatting | `composer lint:php` / `composer fix:php` |
| PHPStan | Static analysis | `composer sca:php` |
| Rector | Code modernization | `composer lint:rector` / `composer fix:rector` |
| Fractor | TypoScript modernization | `composer lint:fractor` / `composer fix:fractor` |
| EditorConfig | File formatting | `composer lint:editorconfig` / `composer fix:editorconfig` |
| Composer Normalize | JSON formatting | `composer lint:composer` / `composer fix:composer` |

### Testing

```bash
# Run all tests
composer test

# Run specific test
phpunit -c Tests/Build/UnitTests.xml --filter TestName
```

### Continuous Integration

The extension is designed to work seamlessly in CI/CD pipelines:

```bash
composer install --no-dev --optimize-autoloader
composer lint
composer test
composer sca:php
```

## Requirements

* **TYPO3**: 13.4.0 - 13.4.99
* **PHP**: 8.3 - 8.4
* **Composer**: 2.0+

## Documentation

* [Interfaces and Traits](docs/Interfaces.md) - Complete interface documentation
* [CLAUDE.md](CLAUDE.md) - AI assistant development guide

## Contributing

1. Fork the repository
2. Create a feature branch
3. Run quality checks: `composer lint && composer test`
4. Submit a pull request

## License

GPL-2.0-or-later

## Support

For issues and feature requests, please use the project's issue tracker.
