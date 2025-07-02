# Development Guide

This guide provides comprehensive information for developers working with or extending the TYPO3 Extension Tools package.

## Table of Contents

- [Development Setup](#development-setup)
- [Code Quality Workflow](#code-quality-workflow)
- [Testing Strategy](#testing-strategy)
- [Architecture Patterns](#architecture-patterns)
- [Extension Integration](#extension-integration)
- [Contributing Guidelines](#contributing-guidelines)

## Development Setup

### Prerequisites

- **PHP**: 8.3 - 8.4
- **TYPO3**: 13.4.0 - 13.4.99
- **Composer**: 2.0+
- **Git**: Latest stable version

### Initial Setup

1. **Clone and Install Dependencies**
   ```bash
   git clone <repository-url>
   cd t3extension-tools
   composer install
   ```

2. **Verify Installation**
   ```bash
   composer test
   composer lint
   ```

3. **IDE Configuration**
   - Import `.editorconfig` settings
   - Configure PHPStan for real-time analysis
   - Set up PHP-CS-Fixer integration

## Code Quality Workflow

### Daily Development Cycle

```bash
# 1. Before starting work
composer lint

# 2. During development (fix issues as you go)
composer fix:php
composer sca:php

# 3. Before committing
composer lint && composer test
```

### Comprehensive Quality Check

```bash
# Full quality assurance pipeline
composer fix              # Fix all auto-fixable issues
composer lint             # Check for remaining issues
composer test             # Run test suite
composer sca:php          # Static analysis
```

### Tool-Specific Usage

#### PHP-CS-Fixer
```bash
# Check specific files
.Build/bin/php-cs-fixer fix Classes/MyClass.php --dry-run

# Fix with custom rules
.Build/bin/php-cs-fixer fix --config=.php-cs-fixer.custom.php
```

#### PHPStan
```bash
# Analyze specific directory
.Build/bin/phpstan analyse Classes/Command --level 8

# Generate baseline for existing issues
.Build/bin/phpstan analyse --generate-baseline
```

#### Rector
```bash
# Preview changes
.Build/bin/rector process --dry-run

# Apply specific rule set
.Build/bin/rector process --config=rector-custom.php
```

## Testing Strategy

### Unit Tests

#### Test Structure
```
Tests/
├── Unit/
│   ├── Command/
│   ├── Configuration/
│   ├── Service/
│   └── Traits/
└── Build/
    └── UnitTests.xml
```

#### Writing Tests

```php
<?php
declare(strict_types=1);

namespace DWenzel\T3extensionTools\Tests\Unit\Command;

use DWenzel\T3extensionTools\Command\ExampleCommand;
use PHPUnit\Framework\TestCase;

class ExampleCommandTest extends TestCase
{
    protected ExampleCommand $subject;

    protected function setUp(): void
    {
        $this->subject = new ExampleCommand();
    }

    public function testCommandCreation(): void
    {
        self::assertInstanceOf(ExampleCommand::class, $this->subject);
    }
}
```

#### Test Execution

```bash
# Run all tests
composer test

# Run specific test class
.Build/bin/phpunit -c Tests/Build/UnitTests.xml --filter ExampleCommandTest

# Run with coverage
XDEBUG_MODE=coverage .Build/bin/phpunit -c Tests/Build/UnitTests.xml --coverage-html coverage/
```

### Test Coverage Goals

- **Minimum Coverage**: 80%
- **Critical Components**: 95%
- **Interfaces**: 100% (through implementation tests)

## Architecture Patterns

### Interface-Trait Pattern

**Purpose**: Provide flexible, reusable components with strong typing.

**Implementation**:
```php
// 1. Define interface contract
interface MyFeatureInterface
{
    public function doSomething(): string;
}

// 2. Implement trait with logic
trait MyFeatureTrait
{
    public function doSomething(): string
    {
        return 'implemented';
    }
}

// 3. Use in consuming class
class MyClass implements MyFeatureInterface
{
    use MyFeatureTrait;
}
```

### Service Layer Pattern

**Purpose**: Centralize business logic and provide dependency injection.

**Implementation**:
```php
// 1. Define service interface
interface MyServiceInterface
{
    public function process(): void;
}

// 2. Implement service
class MyService implements MyServiceInterface
{
    public function process(): void
    {
        // Business logic here
    }
}

// 3. Register in Services.yaml
services:
  DWenzel\T3extensionTools\Service\MyService:
    public: true
```

### Command Pattern

**Purpose**: Encapsulate console commands with modular configuration.

**Implementation**:
```php
class MyCommand extends Command implements ArgumentAwareInterface, OptionAwareInterface
{
    use ArgumentAwareTrait, OptionAwareTrait;

    protected function configure(): void
    {
        $this->setDescription('My command description');
        $this->configureArguments();
        $this->configureOptions();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Command logic here
        return Command::SUCCESS;
    }
}
```

## Extension Integration

### Using as Dependency

1. **Install Package**
   ```bash
   composer require dwenzel/t3extension-tools
   ```

2. **Implement Interfaces**
   ```php
   use DWenzel\T3extensionTools\Configuration\PluginConfigurationInterface;
   use DWenzel\T3extensionTools\Configuration\PluginConfigurationTrait;

   class MyExtensionPluginConfig implements PluginConfigurationInterface
   {
       use PluginConfigurationTrait;
       // Implementation here
   }
   ```

3. **Register Services**
   ```yaml
   # Configuration/Services.yaml
   services:
     MyVendor\MyExtension\Configuration\MyExtensionPluginConfig:
       tags:
         - name: 't3extensionTools.pluginConfiguration'
   ```

### Extending Functionality

#### Custom Command Arguments

```php
use DWenzel\T3extensionTools\Command\Argument\InputArgumentInterface;
use DWenzel\T3extensionTools\Traits\Command\Argument\InputArgumentTrait;

class CustomArgument implements InputArgumentInterface
{
    use InputArgumentTrait;

    public function getName(): string
    {
        return 'custom-arg';
    }

    public function getDescription(): string
    {
        return 'Custom argument description';
    }
}
```

#### Custom Configuration

```php
use DWenzel\T3extensionTools\Configuration\SettingsInterface;

class MySettings implements SettingsInterface
{
    public function getConfiguration(): array
    {
        return [
            'custom' => [
                'setting' => 'value'
            ]
        ];
    }
}
```

## Contributing Guidelines

### Development Workflow

1. **Fork Repository**
   - Create personal fork
   - Clone to local environment

2. **Create Feature Branch**
   ```bash
   git checkout -b feature/my-feature
   ```

3. **Development Process**
   - Write tests first (TDD approach)
   - Implement feature
   - Ensure all quality checks pass
   - Update documentation

4. **Quality Assurance**
   ```bash
   composer lint
   composer test
   composer sca:php
   ```

5. **Submit Pull Request**
   - Clear description of changes
   - Reference related issues
   - Include test coverage information

### Code Standards

#### PHP Standards
- **PSR-12**: Coding style standard
- **PSR-4**: Autoloading standard
- **Strict Types**: Always declare `declare(strict_types=1);`
- **Type Declarations**: Use for all parameters and return values
- **Documentation**: PHPDoc for public methods

#### Commit Messages
```
type(scope): description

[optional body]

[optional footer]
```

**Types**: feat, fix, docs, style, refactor, test, chore

**Example**:
```
feat(command): add support for custom arguments

- Implement InputArgumentInterface extension
- Add trait for common argument functionality
- Include comprehensive test coverage

Closes #123
```

### Testing Requirements

- **Unit Tests**: Required for all new features
- **Interface Tests**: Test interface contracts
- **Coverage**: Minimum 80% for new code
- **Integration**: Test with TYPO3 framework

### Documentation Requirements

- **README**: Update for new features
- **Interface Docs**: Document new interfaces/traits
- **Code Examples**: Provide usage examples
- **CLAUDE.md**: Update AI assistant guidance

## Performance Considerations

### Optimization Guidelines

1. **Lazy Loading**: Use dependency injection for heavy services
2. **Caching**: Implement where appropriate
3. **Memory Usage**: Monitor in long-running commands
4. **Database Queries**: Optimize for large datasets

### Profiling Tools

```bash
# Memory usage analysis
php -d memory_limit=128M .Build/bin/phpunit --coverage-text

# Performance profiling with Xdebug
XDEBUG_MODE=profile php script.php
```

## Debugging

### Common Debug Techniques

```php
// 1. Debug output in commands
$output->writeln('Debug: ' . var_export($variable, true));

// 2. TYPO3 logging
use TYPO3\CMS\Core\Log\LogManager;
$logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
$logger->debug('Debug message', ['context' => $data]);

// 3. Exception handling
try {
    // risky code
} catch (\Exception $e) {
    $output->writeln('<error>' . $e->getMessage() . '</error>');
}
```

### IDE Configuration

#### PhpStorm Settings
- Import `.editorconfig`
- Configure PHP interpreter (8.3+)
- Set up PHPUnit test runner
- Configure PHP-CS-Fixer integration

#### VS Code Extensions
- PHP Intelephense
- PHP CS Fixer
- EditorConfig for VS Code
- GitLens

## Resources

- [TYPO3 Documentation](https://docs.typo3.org/)
- [PSR Standards](https://www.php-fig.org/psr/)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Symfony Console Component](https://symfony.com/doc/current/components/console.html)