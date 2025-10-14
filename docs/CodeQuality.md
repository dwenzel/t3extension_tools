# Code Quality Guide

This document outlines the code quality standards, tools, and processes used in the TYPO3 Extension Tools project.

## Quality Standards Overview

### Code Quality Metrics

| Metric | Target | Tool |
|--------|--------|------|
| **Code Coverage** | ≥ 80% | PHPUnit |
| **PHPStan Level** | 6 | PHPStan |
| **Code Style** | TYPO3 Standards | PHP-CS-Fixer |
| **Complexity** | Low-Medium | PHPStan, Code Review |
| **Duplication** | Minimal | Manual Review |

### Quality Gates

All code must pass these gates before merging:

1. ✅ **Linting**: No code style violations
2. ✅ **Static Analysis**: PHPStan level 6 clean
3. ✅ **Tests**: All tests passing
4. ✅ **Coverage**: Minimum 80% test coverage
5. ✅ **Code Review**: Peer review approval

## Tools Configuration

### PHP-CS-Fixer

**Purpose**: Enforce TYPO3 coding standards and consistent formatting.

**Configuration**: `.php-cs-fixer.dist.php`
```php
<?php
declare(strict_types=1);

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config->getFinder()->in(__DIR__ . '/Classes');
$config->getFinder()->in(__DIR__ . '/Tests');

return $config;
```

**Usage**:
```bash
# Check for violations
composer lint:php

# Fix violations automatically
composer fix:php

# Custom file check
.Build/bin/php-cs-fixer fix path/to/file.php --dry-run
```

**Key Rules Enforced**:
- PSR-12 compliance
- TYPO3-specific formatting
- Consistent array syntax
- Proper use of PHP 8+ features
- Strict type declarations

### PHPStan

**Purpose**: Static code analysis to catch bugs and improve code quality.

**Configuration**: `phpstan.neon`
```neon
parameters:
    level: 6
    paths:
        - Classes
        - Tests
    ignoreErrors:
        # Allow dynamic properties in test classes
        - '#Access to an undefined property.*Test::\$.*#'
```

**Usage**:
```bash
# Run static analysis
composer sca:php

# Analyze specific level
.Build/bin/phpstan analyse --level 8

# Generate baseline for existing issues
.Build/bin/phpstan analyse --generate-baseline
```

**Analysis Levels**:
- **Level 0-3**: Basic checks (syntax, undefined variables)
- **Level 4-6**: Type checking, method existence (current target)
- **Level 7-9**: Advanced analysis (future goals)

### Rector

**Purpose**: Automated code modernization and TYPO3 migration.

**Configuration**: `rector.php`
```php
return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/Classes',
        __DIR__ . '/Tests',
        __DIR__ . '/ext_emconf.php',
    ])
    ->withPhpVersion(PhpVersion::PHP_83)
    ->withSets([
        Typo3SetList::CODE_QUALITY,
        Typo3SetList::GENERAL,
        Typo3LevelSetList::UP_TO_TYPO3_13,
    ]);
```

**Usage**:
```bash
# Preview changes
composer lint:rector

# Apply changes
composer fix:rector

# Custom rule set
.Build/bin/rector process --config=custom-rector.php
```

**Rule Categories**:
- **Code Quality**: Remove dead code, simplify conditions
- **Type Declarations**: Add missing type hints
- **TYPO3 Migration**: Update deprecated APIs
- **PHP Modernization**: Use latest PHP features

### Fractor

**Purpose**: TypoScript code modernization and formatting.

**Configuration**: `fractor.php`
```php
return FractorConfiguration::configure()
    ->withPaths([
        __DIR__ . '/Configuration/',
        __DIR__ . '/Resources/',
    ])
    ->withSets([
        Typo3LevelSetList::UP_TO_TYPO3_13
    ])
    ->withOptions([
        TypoScriptProcessorOption::INDENT_SIZE => 2,
        TypoScriptProcessorOption::INDENT_CHARACTER => PrettyPrinterConfiguration::INDENTATION_STYLE_SPACES,
    ]);
```

**Usage**:
```bash
# Check TypoScript formatting
composer lint:fractor

# Fix TypoScript formatting
composer fix:fractor
```

### EditorConfig

**Purpose**: Consistent file formatting across different editors.

**Configuration**: `.editorconfig`
```ini
root = true

[*]
charset = utf-8
end_of_line = lf
indent_style = space
indent_size = 4
insert_final_newline = true
trim_trailing_whitespace = true

[*.{ts,js}]
indent_size = 2

[*.json]
indent_style = tab
```

**Usage**:
```bash
# Check file formatting
composer lint:editorconfig

# Fix file formatting
composer fix:editorconfig
```

### Composer Normalize

**Purpose**: Standardize composer.json formatting.

**Usage**:
```bash
# Check composer.json formatting
composer lint:composer

# Fix composer.json formatting
composer fix:composer
```

## Quality Workflows

### Pre-Commit Workflow

```bash
#!/bin/bash
# .git/hooks/pre-commit

# Run quality checks
composer lint

if [ $? -ne 0 ]; then
    echo "❌ Code quality checks failed. Please fix issues before committing."
    exit 1
fi

echo "✅ Code quality checks passed."
```

### Continuous Integration Pipeline

```yaml
# Example CI configuration
name: Quality Assurance

on: [push, pull_request]

jobs:
  quality:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.3

      - name: Install Dependencies
        run: composer install --no-progress --no-interaction

      - name: Run Linting
        run: composer lint

      - name: Run Tests
        run: composer test

      - name: Run Static Analysis
        run: composer sca:php
```

### Development Workflow

#### Daily Development

```bash
# 1. Start development
git checkout -b feature/my-feature

# 2. During development
composer fix:php          # Fix formatting issues
composer sca:php          # Check for type issues

# 3. Before commit
composer lint             # Final quality check
composer test             # Ensure tests pass

# 4. Commit and push
git add .
git commit -m "feat: add new feature"
git push origin feature/my-feature
```

#### Code Review Checklist

**Reviewer Checklist**:
- [ ] All quality tools pass
- [ ] Code follows TYPO3 conventions
- [ ] Tests cover new functionality
- [ ] Documentation updated
- [ ] No security vulnerabilities
- [ ] Performance considerations addressed

## Best Practices

### Code Style

#### PHP Code Standards

```php
<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Example;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Example class demonstrating code standards
 */
final class ExampleClass implements SingletonInterface
{
    private string $property;

    public function __construct(private readonly ConfigurationInterface $config)
    {
    }

    public function processData(array $data): array
    {
        // Use early returns
        if (empty($data)) {
            return [];
        }

        // Type hints everywhere
        return array_map(
            fn(string $item): string => $this->transformItem($item),
            $data
        );
    }

    private function transformItem(string $item): string
    {
        return strtoupper($item);
    }
}
```

#### Documentation Standards

```php
/**
 * Process configuration data and return transformed result
 *
 * This method validates input data, applies transformations,
 * and returns the processed configuration array.
 *
 * @param array<string, mixed> $data Input configuration data
 * @return array<string, mixed> Processed configuration
 * @throws InvalidArgumentException When data validation fails
 */
public function processConfiguration(array $data): array
{
    // Implementation
}
```

### Testing Standards

#### Unit Test Structure

```php
<?php

declare(strict_types=1);

namespace DWenzel\T3extensionTools\Tests\Unit\Example;

use DWenzel\T3extensionTools\Example\ExampleClass;
use PHPUnit\Framework\TestCase;

final class ExampleClassTest extends TestCase
{
    private ExampleClass $subject;

    protected function setUp(): void
    {
        $this->subject = new ExampleClass();
    }

    /**
     * @test
     */
    public function processDataReturnsEmptyArrayForEmptyInput(): void
    {
        $result = $this->subject->processData([]);

        self::assertSame([], $result);
    }

    /**
     * @test
     */
    public function processDataTransformsItems(): void
    {
        $input = ['item1', 'item2'];
        $expected = ['ITEM1', 'ITEM2'];

        $result = $this->subject->processData($input);

        self::assertSame($expected, $result);
    }
}
```

### Performance Guidelines

#### Memory Optimization

```php
// Good: Use generators for large datasets
public function processLargeDataset(): \Generator
{
    foreach ($this->dataSource as $item) {
        yield $this->processItem($item);
    }
}

// Good: Unset large variables when done
$largeArray = $this->fetchLargeData();
$result = $this->process($largeArray);
unset($largeArray);
return $result;
```

#### Database Optimization

```php
// Good: Use QueryBuilder for complex queries
$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
    ->getQueryBuilderForTable('tx_example');

$result = $queryBuilder
    ->select('*')
    ->from('tx_example')
    ->where(
        $queryBuilder->expr()->eq(
            'status',
            $queryBuilder->createNamedParameter(1, \PDO::PARAM_INT)
        )
    )
    ->executeQuery()
    ->fetchAllAssociative();
```

## Quality Metrics Dashboard

### Key Performance Indicators

| Metric | Current | Target | Trend |
|--------|---------|--------|-------|
| Test Coverage | 85% | 90% | ↗️ |
| PHPStan Issues | 5 | 0 | ↘️ |
| Code Duplication | 2% | <1% | ↘️ |
| Technical Debt | Low | Low | ➡️ |

### Monthly Quality Review

**Process**:
1. **Metrics Collection**: Gather automated metrics
2. **Manual Review**: Code quality assessment
3. **Goal Setting**: Update targets for next period
4. **Tool Updates**: Update quality tools and rules

**Deliverables**:
- Quality metrics report
- Technical debt assessment
- Improvement recommendations
- Tool configuration updates

## Troubleshooting

### Common Issues

#### PHP-CS-Fixer Issues

**Problem**: Cache corruption
```bash
# Solution: Clear cache
rm .php-cs-fixer.cache
composer fix:php
```

**Problem**: Memory limit exceeded
```bash
# Solution: Increase memory limit
php -d memory_limit=512M .Build/bin/php-cs-fixer fix
```

#### PHPStan Issues

**Problem**: False positives
```bash
# Solution: Add to ignore list in phpstan.neon
parameters:
    ignoreErrors:
        - '#Specific error pattern#'
```

**Problem**: Baseline conflicts
```bash
# Solution: Regenerate baseline
.Build/bin/phpstan analyse --generate-baseline
```

#### Test Failures

**Problem**: Missing dependencies
```bash
# Solution: Install test dependencies
composer install --dev
```

**Problem**: Database connection issues
```bash
# Solution: Check TYPO3 test configuration
export TYPO3_DATABASE_HOST=localhost
composer test
```

## Resources

- [TYPO3 Coding Guidelines](https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/CodingGuidelines/)
- [PSR-12 Extended Coding Style](https://www.php-fig.org/psr/psr-12/)
- [PHPStan Documentation](https://phpstan.org/user-guide/getting-started)
- [PHP-CS-Fixer Documentation](https://cs.symfony.com/)
- [Rector Documentation](https://getrector.org/documentation)
