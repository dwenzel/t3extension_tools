# Extension Tools
[![Tests](https://github.com/dwenzel/t3extension-tools/actions/workflows/tests.yml/badge.svg)](https://github.com/dwenzel/t3extension-tools/actions/workflows/tests.yml)

Provide tools for extensions of the TYPO3 CMS

## Installation
```
composer require dwenzel/t3extension-tools
```
**Note**: This extension will be updated in TYPO3 Extension Repository sporadically only.  
Use the latest composer version instead.

## Features

* register plugins
  * [YAML-based plugin configuration](docs/PluginConfiguration.md) for simplified setup
  * [Class-based plugin configuration](docs/ClassBasedPluginConfiguration.md) for advanced use cases
* register modules
* configure tables
  * allow tables on standard pages
  * add localized description for tables (context sensitive help)
* register bitmap and SVG icons
* load TypoScript and PageTS config
* use interfaces and traits for console commands

## Caveats

Make sure, no other extension is extending `TYPO3\CMS\Extbase\Service\ExtensionService`.  
If an extending class is registered via TypoScript it will be found at 
`config.tx_extbase.objects.TYPO3\CMS\Extbase\Service\ExtensionService.className`.

## Development

### Testing

This extension comes with a comprehensive test suite. You can run the tests with the following commands:

```bash
# Run unit tests
composer test:unit

# Run static analysis
composer test:phpstan

# Run all tests
composer test
```

### GitHub Workflows

The following GitHub workflows are configured for this extension:

1. **Tests**: Runs unit tests on both TYPO3 v12 and v13
2. **Static Analysis**: Performs static analysis using PHPStan
